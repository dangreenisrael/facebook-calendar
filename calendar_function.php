
<?php function calendar($page_id){?>

  <?php
	//we have to set timezone to Tel Aviv
	date_default_timezone_set('Asia/Tel_Aviv');
	
	//requiring FB PHP SDK
	$path = $_SERVER['DOCUMENT_ROOT'];
	$path .='/common/facebook/src/facebook.php';
	include ($path);
	
	//initializing keys
	$facebook = new Facebook(array(
	    'appId'  => 'YOUR_APP_ID',
	    'secret' => 'YOUR_APP_SECRET',
	    'cookie' => true,
	))
	$facebook->setAccessToken('YOUR_APP_ACCESS_TOKEN');
// make SURE that the uid (Even ID) is a STRING not an INT
	$fql = "SELECT 
	            name, pic, start_time, end_time, location, description 
	        FROM 
	            event 
	        WHERE 
	            eid IN ( SELECT eid FROM event_member WHERE uid = $page_id ) 
	        ORDER BY 
	            start_time desc";
	
	$param  =   array(
	    'method'    => 'fql.query',
	    'query'     => $fql,
	    'callback'  => ''
	);
	//add this: AND start_time >= now()
	
	
	$fqlResult   =   $facebook->api($param);
	
	//looping through retrieved data
	foreach( $fqlResult as $keys => $values ){
	    $month = date( 'n', $values['start_time'] );
	    $day = date('d', $values['start_time']);
	    $title = $values['name'];
		$i++;
	
		
		$pubcrawl[$i]['day']	= $day;
		$pubcrawl[$i]['month']	= $month;
		$pubcrawl[$i]['title']	= $title;
	
	
	}
	
	// demo purposes only
	/*
	$pubcrawl[0]['day']		= 12;
	$pubcrawl[0]['month']	= 05;
	$pubcrawl[0]['title']	= "Pubcrawl 1";
	
	$pubcrawl[1]['day']		= 12;
	$pubcrawl[1]['month']	= 05;
	$pubcrawl[1]['title']	= "Pubcrawl 2";
	
	$pubcrawl[2]['day']		= 26;
	$pubcrawl[2]['month']	= 05;
	$pubcrawl[2]['title']	= "Pubcrawl 3";
	
	$pubcrawl[3]['day']		= 02;
	$pubcrawl[3]['month']	= 06;
	$pubcrawl[3]['title']	= "Pubcrawl 4";
	*/
	//
	?>
	
	<?php
	 
	$monthNames = Array("January", "February", "March", "April", "May", "June", "July",
	"August", "September", "October", "November", "December");
	 
	if (!isset($_GET["m"])) $_GET["m"] = date("n");
	 
	$currentMonth = $_GET["m"];
	 
	$p_month = $currentMonth-1;
	$n_month = $currentMonth+1;
	 
	if ($p_month == 0 ) {
	    $p_month = 12;
	}
	 
	if ($n_month == 13 ) {
	    $n_month = 1;
	}
	$days=array('1'=>"Sunday",'2'=>"Monday",'3'=>"Tuesday",'4'=>"Wednesday",'5'=>"Thurs",'6'=>"Friday",'7'=>"Saturday");
	 
	?>
	 
	<table width="960">
		<tr align="center">
			<td>
				<table width="100%">
					<tr>
						<td width="50%" align="left"> <h3> <a href="<?php echo $_SERVER["PHP_SELF"] . "?m=". $p_month?>"><?php echo $monthNames[$p_month-1];?></a></h3></td>
						<td width="50%" align="right"><h3><a href="<?php echo $_SERVER["PHP_SELF"] . "?m=". $n_month?>"><?php echo  $monthNames[$n_month-1];?></a>  </h3></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td align="center">
				<table id = "main_calendar" width="100%">
					<tr align="center">
						<td colspan="7" class="headings" ><h1><?php echo $monthNames[$currentMonth-1]?></h1></td>
					</tr>
					<tr >
					<?php for($i=1;$i<=7;$i++){ ?>
					 
						<td align="center" class="headings" ><h3><?php echo $days[$i]; ?></h3></td>
					 
					<?php } ?>
					</tr>
					<?php
					$timestamp = mktime(0,0,0,$currentMonth,1,$currentYear);
					$maxday = date("t",$timestamp);
					$thismonth = getdate ($timestamp);
					$startday = $thismonth['wday'];
					$end_here = -1;
					for ($i=0; $i<50; $i++) {
						if ($i == $end_here) break;
					    //the date being processed
					    $date = $i-$startday+1;
						$lastday = $maxday+$startday;
					    
					    
					    if(($i % 7) == 0 ) {
					    	echo "<tr>";
					    	$week_tracker++;
					    }
					    if ($i==$lastday) $end_here = $week_tracker*7;
					    
					  	//last month
					    if($i < $startday){ 
					    	echo "<td class='not_current_month'> </td>";
					    }
					    elseif ($i <$lastday) {
					    	echo "<td class = 'current_month' align='center' valign='middle'>"; 
					    	// Check if there is an event on a given day & echo it
					    	foreach ($pubcrawl as $each_event){
						    	if (($date == $each_event['day'])&&($currentMonth==$each_event['month'])){
						    		echo $each_event['title'];
						    	}
					    	}
					    	echo "<span class = 'day_number'>$date</span>";
					    
					    	echo "</td>";
					    }
					    else{
					    	
					    	echo "<td class='not_current_month'> </td>";
					    }
					    	
					    
					    if(($i % 7) == 6 ) echo "</tr>";
					}
					?>
				</table>
			</td>
		</tr>
	</table>
<?php } ?>
