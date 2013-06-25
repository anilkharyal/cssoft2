<?php
	session_start();
	include ("../DBFunctions.inc.php");
	
	include ("../navBar.inc.php");
	include ("../footer.inc.php");
	include ("../accounts.inc.php");
	include_once ("../contest.inc.php");
	include ("admin.inc.php");

	$a = $_REQUEST["a"];
	if ($a == "INSERT")
	{
		$prize = $_REQUEST["prize"];
		$details = $_REQUEST["details"];
		
		// get start and end times
		 $voteStartDate = $_REQUEST["voteStartDate"];
		 $voteStartTime = $_REQUEST["voteStartTime"];
		 
		 $voteEndDate = $_REQUEST["voteEndDate"];
		 $voteEndTime = $_REQUEST["voteEndTime"];	
		 
		// $nextVotingStarts = $_REQUEST["nextVotingStarts"];
		// $nextVoteEndTime = $_REQUEST["nextVoteStartTime"];	 

         $nextVotingStarts = $_REQUEST["nextVotingStarts"];
		 $nextVoteStartTime = $_REQUEST["nextVoteStartTime"];
	
		 	 
		 $regStartDate = $_REQUEST["regStartDate"];		 		 		  
		 $regStartTime = $_REQUEST["regStartTime"];
		 
		 $regEndDate = $_REQUEST["regEndDate"];		 		 		  
		 $regEndTime = $_REQUEST["regEndTime"];	
		 
		 $voteStartAMPM = $_REQUEST["voteStartAMPM"];
		 $voteEndAMPM = $_REQUEST["voteEndAMPM"];		
		 //$nextVoteEndAMPM = $_REQUEST["nextVoteStartAMPM"]; 
                 $nextVoteStartAMPM = $_REQUEST["nextVoteStartAMPM"]; 
		 $regStartAMPM = $_REQUEST["regStartAMPM"];
		 $regEndAMPM = $_REQUEST["regEndAMPM"];
		 $showFullName = $_REQUEST["showFullName"];
		 $commentQuestion = $_REQUEST["commentQuestion"];
		 $isLastRound = $_REQUEST["isLastRound"];
		 
		 $winnerCaption = stripslashes($_REQUEST["winnerCaption"]);
		 
		 
		 $timeZone = $_REQUEST["timeZone"];
		 
		 $maxAccounts = str_replace(",","",$_REQUEST["maxAccounts"]);
		 $tempStart = explode("/",$voteStartDate);
		 // get voteStart
		 if ($voteStartAMPM == "PM")
		 {
		 	if (substr($voteStartTime,0,2) <> "12")
			{				
				$tempTime = explode(":", $voteStartTime);
				$voteStartTime = ($tempTime[0] + 12)%24 . ":" . $tempTime[1] ;
				if (strlen($voteStartTime) == 4)
				{
					$voteStartTime = "0" . $voteStartTime;
				}
			}
		 }
		 else
		 {
		 	// AM
			// only change if we are at 12
			if (substr($voteStartTime,0,2) == "12")
			{
				$tempTime = explode(":", $voteStartTime);
				$voteStartTime = ($tempTime[0] + 12)%24 . ":" . $tempTime[1] ;
				if (strlen($voteStartTime) == 4)
				{
					$voteStartTime = "0" . $voteStartTime;
				}
			}
		 }
		 $voteStartDate = $tempStart[2] . "-" . $tempStart[0] . "-" . $tempStart[1] . " " . $voteStartTime . ":00";
		 
		 
		 $tempStart = explode("/",$nextVotingStarts);
		 // get next voting round
		 if ($nextVoteStartAMPM == "PM")
		 {
		 	if (substr($nextVoteStartTime,0,2) <> "12")
			{				
				$tempTime = explode(":", $nextVoteStartTime);
				$nextVoteStartTime = ($tempTime[0] + 12)%24 . ":" . $tempTime[1] ;
				if (strlen($nextVoteStartTime) == 4)
				{
					$nextVoteStartTime = "0" . $nextVoteStartTime;
				}
			}
		 }
		 else
		 {
		 	// AM
			// only change if we are at 12
			if (substr($nextVoteStartTime,0,2) == "12")
			{
				$tempTime = explode(":", $nextVoteStartTime);
				$nextVoteStartTime = ($tempTime[0] + 12)%24 . ":" . $tempTime[1] ;
				if (strlen($nextVoteStartTime) == 4)
				{
					$nextVoteStartTime = "0" . $nextVoteStartTime;
				}
			}
		 }
		 $nextVoteStartDate = $tempStart[2] . "-" . $tempStart[0] . "-" . $tempStart[1] . " " . $nextVoteStartTime . ":00";


 		// get vote end
	     $tempStart = explode("/",$voteEndDate);
		 if ($voteEndAMPM == "PM")
		 {
			if (substr($voteEndTime,0,2) <> "12")
			{
				$tempTime = explode(":", $voteEndTime);
				$voteEndTime = ($tempTime[0] + 12)%24 . ":" . $tempTime[1] ;
				if (strlen($voteEndTime) == 4)
				{
					$voteEndTime = "0" . $voteEndTime;
				}
			}
		 }
		 else
		 {
		 	// AM
			// only change if we are at 12
			if (substr($voteEndTime,0,2) == "12")
			{
				$tempTime = explode(":", $voteEndTime);
				$voteEndTime = ($tempTime[0] + 12)%24 . ":" . $tempTime[1] ;
				if (strlen($voteEndTime) == 4)
				{
					$voteEndTime = "0" . $voteEndTime;
				}
			}
		 }
		 $voteEndDate = $tempStart[2] . "-" . $tempStart[0] . "-" . $tempStart[1] . " " . $voteEndTime . ":00";

		 // get reg Start
	     $tempStart = explode("/",$regStartDate);
		 
		 if ($regStartAMPM == "PM")
		 {
		 	if (substr($regStartTime,0,2) <> "12")
			{
				$tempTime = explode(":", $regStartTime);
				$regStartTime = ($tempTime[0] + 12)%24 . ":" . $tempTime[1] ;
				if (strlen($regStartTime) == 4)
				{
					$regStartTime = "0" . $regStartTime;
				}
			}
		 }
		 else
		 {
		 	// AM
			// only change if we are at 12
			if (substr($regStartTime,0,2) == "12")
			{
				$tempTime = explode(":", $regStartTime);
				$regStartTime = ($tempTime[0] + 12)%24 . ":" . $tempTime[1] ;
				if (strlen($regStartTime) == 4)
				{
					$regStartTime = "0" . $regStartTime;
				}
			}
		 }
		 
		 $regStartDate = $tempStart[2] . "-" . $tempStart[0] . "-" . $tempStart[1] . " " . $regStartTime . ":00";
		
		// get reg End
	     $tempStart = explode("/",$regEndDate);
		 if ($regEndAMPM == "PM")
		 {
		 	if (substr($regEndTime,0,2) <> "12")
			{
				$tempTime = explode(":", $regEndTime);
				$regEndTime = ($tempTime[0] + 12)%24 . ":" . $tempTime[1] ;
				if (strlen($regEndTime) == 4)
				{
					$regEndTime = "0" . $regEndTime;
				}
			}
		
		 }
		 else
		 {
		 	// AM
			// only change if we are at 12
			if (substr($regEndTime,0,2) == "12")
			{
				$tempTime = explode(":", $regEndTime);
				$regEndTime = ($tempTime[0] + 12)%24 . ":" . $tempTime[1] ;
				if (strlen($regEndTime) == 4)
				{
					$regEndTime = "0" . $regEndTime;
				}
			}
		 }
		 $regEndDate = $tempStart[2] . "-" . $tempStart[0] . "-" . $tempStart[1] . " " . $regEndTime . ":00";
		 $country = $_REQUEST["country"];
		
		$terms = $_REQUEST["terms"];
		$contestHeader = $_REQUEST["contestHeader"];
		$watermark_text = $_REQUEST['watermark_text'];
		
		$db = DBConnect();
		 $sql="INSERT INTO `contest` (`prize`,`details`,`registrationOpens`,`votingOpens`,`country`,`votingEnds`,`terms`,
		 `maxAccounts`,`registrationEnds`,`commentQuestion`,`showFullName`,`nextVotingStarts`,`timeZone`,`winnerCaption`,
		 `banner`,`isLastRound`,`contestHeader`,`watermark_text`) VALUES 
		 (" . safe($prize) . "," . safe($details) . "," . safe($regStartDate) . "," . safe($voteStartDate) . ",
		 " . safe($country) . "," . safe($voteEndDate) . "," . safe($terms) . "," . safe($maxAccounts) . ",
		 " . safe($regEndDate) . "," . safe($commentQuestion) . "," . safe($showFullName) . "," . safe($nextVoteStartDate) . ", 
		 " . safe($timeZone) . ", " . safe($winnerCaption) . "," . safe($filename) . " ," . safe($isLastRound) . ", 
		 " . safe($contestHeader) . ", " . safe($watermark_text) . ")";
			
			$result = mysql_query($sql, $db);		
			 $id = mysql_insert_id(); 
			$_SESSION['cid'] = $id;
			$bid = $_SESSION['banner_id'];
			
			if ($result)
			{

				$timerclock = $_POST['timer'];
				if ($timerclock == "Registration") 
				{
				$timer = "Registration";
				$db = DBConnect();
               			$qry = "UPDATE `contest` SET `timerclock`='$timer' where `id` = '$id'"; 
				$res = mysql_query($qry,$db);
				
				}
				elseif($timerclock == "Voting")	
				{
				$timer = "Voting";
				$db = DBConnect();
				$qry = "UPDATE `contest` SET `timerclock`='$timer' where `id` = '$id'";
				$res = mysql_query($qry,$db);
				}				
				else
				{
				$timer = "None";
				$db = DBConnect();
				$qry = "UPDATE `contest` SET `timerclock`='$timer' where `id` = '$id'";
				$res = mysql_query($qry,$db);
				}				

				$goodString = "Successfully added contest to the database";
				header("location: secondstep.php");
				exit;
			}

			else
			{
				$errorString = "An error occurred, please try again";
				header("location: secondstep.php?errorString=$errorString");
				exit;
			}
	}
	$curYear = date("Y");
	

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Administration - Create contest</title>
<script src="js/jquery.js"></script>
<script src="js/jquerymaskedinput.js"></script>
<script type="text/javascript" src="js/datepicker.js"></script>
<script src="js/startup.js"></script>
<script>
function showTerms()
{
	 window.open ("managelogo.php", "termswindow","menubar=1,resizable=1,width=630,height=550, scrollbars=1"); 
}
</script>
<script>
function showTerms1()
{
	 window.open ("managebanner.php", "termswindow","menubar=1,resizable=1,width=630,height=550, scrollbars=1"); 
}
</script>
<script>
function checkForm(theForm)
{
	if (theForm.timeZone.value == "")
	{
		alert("Please enter a Time Zone");
		return false;
	}
	else if (theForm.regStartDate.value == "")
	{
		alert("Please enter a Registration Start Date");
		return false;
	}
	else if (theForm.regEndDate.value == "")
	{
		alert("Please enter a Registration End Date");
		return false;
	}
	else if (theForm.voteStartDate.value == "")
	{
		alert("Please enter a Vote Start Date");
		return false;
	}
	else if (theForm.voteEndDate.value == "")
	{
		alert("Please enter a Vote End Date");
		return false;
	}
	else if (theForm.regStartTime.value == "")
	{
		alert("Please enter a Vote End time");
		return false;
	}
	else if (theForm.regEndTime.value == "")
	{
		alert("Please enter a Vote End time");
		return false;
	}
	else if (theForm.voteStartTime.value == "")
	{
		alert("Please enter a Vote End time");
		return false;
	}
	else if (theForm.voteEndTime.value == "")
	{
		alert("Please enter a Vote End time");
		return false;
	}
	else if (theForm.nextVotingStarts.value == "")
	{
		alert("Please enter a Next Voting Start Date");
		return false;
	}
	else if (theForm.nextVoteStartTime.value == "")
	{
		alert("Please enter a Next Voting Start Time");
		return false;
	}
	
}
</script>
<link rel="stylesheet" type="text/css" href="js/style.css" title="default" media="screen" />
<link href="../css/style.css" rel="stylesheet" type="text/css" />
</head>
<body onload="countdown(year,month,day,hour,minute)">
<table width="100%" border="0" cellpadding="5" cellspacing="1">
  <tr>
    <td align="center" valign="top"><table width="800" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" valign="top"><? printAdminHeader(false, false, true);?></td>
      </tr>
	  <tr>
        <td align="left" valign="top"><table width="100%" cellpadding="2" cellspacing="1" border="0">
              
              <tr>
                <td width="100%" align="left" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
                  <tr>
                    <td><h2>Administration Section</h2>
						<? if ($errorString <> ""){?>                                        
                      <div id="errorString">
                          <?=$errorString?>
                        </div>
                        <? }?>
						<? if ($goodString <> ""){?>                                        
                      <div id="goodString">
                          <?=$goodString?>
                        </div>
                        <? }?>
			    <br /></td>
                    </tr>
			<tr>
			<td>


		</td>
		</tr>                 
   
                  <tr>
                    <td align="left" valign="top"><table width="100%" border="0" cellpadding="2" cellspacing="4">
                        <tr>
                        <td width="31%"><form action="createContest.php" enctype="multipart/form-data" method="post" onsubmit="return checkForm(this);" >
                          <h2>Create a Contest </h2>
                          Fill out the following form to create a contest. You can edit this information at a later time.<br />
                          <br />
                          <table width="100%" cellpadding="5" cellspacing="2" border="0">

			      <tr class="row2">
                              <td width="32%" align="left" valign="top" class="row1">Country:</td>
                              <td class="row1"><? printCountrySelectBox($country);?></td>
                            </tr>
                            <tr class="row2">
                              <td align="left" valign="top" class="row2">Picture Watermark Text</td>
                              <td class="row2"><span class="row1">
                                <input name="watermark_text" type="text" id="watermark_text" value="<?=htmlentities($watermark_text, ENT_QUOTES)?>" size="50" maxlength="255" />
                              </span></td>
                            </tr>
                            <tr class="row2">
                              <td align="left" valign="top" class="row1">Prize:</td>
                              <td width="68%" class="row1"><input type="text" size="51" name="prize" value="" maxlength="255" /></td>
                            </tr>
                            <tr class="row1">
                              <td align="left" valign="top" class="row2">Details:</td>
                              <td width="68%" class="row2"><textarea cols="50" rows="6" name="details"></textarea></td>
                            </tr>
                            <tr class="row2">
                              <td align="left" valign="top" class="row1">Choose a Time Zone: </td>
                              <td class="row1"><select name="timeZone" id="timeZone">
			        <option value="">Select a Time Zone</option>
					<option value="Pacific/Midway">(GMT-11:00) Midway Island, Samoa</option>
					<option value="America/Adak">(GMT-10:00) Hawaii-Aleutian</option>
					<option value="Etc/GMT+10">(GMT-10:00) Hawaii</option>
					<option value="Pacific/Marquesas">(GMT-09:30) Marquesas Islands</option>
					<option value="Pacific/Gambier">(GMT-09:00) Gambier Islands</option>
					<option value="America/Anchorage">(GMT-09:00) Alaska</option>
					<option value="America/Ensenada">(GMT-08:00) Tijuana, Baja California</option>
					<option value="Etc/GMT+8">(GMT-08:00) Pitcairn Islands</option>
					<option value="America/Los_Angeles">(GMT-08:00) Pacific Time (US & Canada)</option>
					<option value="America/Denver">(GMT-07:00) Mountain Time (US & Canada)</option>
					<option value="America/Chihuahua">(GMT-07:00) Chihuahua, La Paz, Mazatlan</option>
					<option value="America/Dawson_Creek">(GMT-07:00) Arizona</option>
					<option value="America/Belize">(GMT-06:00) Saskatchewan, Central America</option>
					<option value="America/Cancun">(GMT-06:00) Guadalajara, Mexico City, Monterrey</option>
					<option value="Chile/EasterIsland">(GMT-06:00) Easter Island</option>
					<option value="America/Chicago">(GMT-06:00) Central Time (US & Canada)</option>
					<option value="America/New_York">(GMT-05:00) Eastern Time (US & Canada)</option>
					<option value="America/Havana">(GMT-05:00) Cuba</option>
					<option value="America/Bogota">(GMT-05:00) Bogota, Lima, Quito, Rio Branco</option>
					<option value="America/Caracas">(GMT-04:30) Caracas</option>
					<option value="America/Santiago">(GMT-04:00) Santiago</option>
					<option value="America/La_Paz">(GMT-04:00) La Paz</option>
					<option value="Atlantic/Stanley">(GMT-04:00) Faukland Islands</option>
					<option value="America/Campo_Grande">(GMT-04:00) Brazil</option>
					<option value="America/Goose_Bay">(GMT-04:00) Atlantic Time (Goose Bay)</option>
					<option value="America/Glace_Bay">(GMT-04:00) Atlantic Time (Canada)</option>
					<option value="America/St_Johns">(GMT-03:30) Newfoundland</option>
					<option value="America/Araguaina">(GMT-03:00) UTC-3</option>
					<option value="America/Montevideo">(GMT-03:00) Montevideo</option>
					<option value="America/Miquelon">(GMT-03:00) Miquelon, St. Pierre</option>
					<option value="America/Godthab">(GMT-03:00) Greenland</option>
					<option value="America/Argentina/Buenos_Aires">(GMT-03:00) Buenos Aires</option>
					<option value="America/Sao_Paulo">(GMT-03:00) Brasilia</option>
					<option value="America/Noronha">(GMT-02:00) Mid-Atlantic</option>
					<option value="Atlantic/Cape_Verde">(GMT-01:00) Cape Verde Is.</option>
					<option value="Atlantic/Azores">(GMT-01:00) Azores</option>
					<option value="Europe/Belfast">(GMT) Greenwich Mean Time : Belfast</option>
					<option value="Europe/Dublin">(GMT) Greenwich Mean Time : Dublin</option>
					<option value="Europe/Lisbon">(GMT) Greenwich Mean Time : Lisbon</option>
					<option value="Europe/London">(GMT) Greenwich Mean Time : London</option>
					<option value="Africa/Abidjan">(GMT) Monrovia, Reykjavik</option>
					<option value="Europe/Amsterdam">(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna</option>
					<option value="Europe/Belgrade">(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague</option>
					<option value="Europe/Brussels">(GMT+01:00) Brussels, Copenhagen, Madrid, Paris</option>
					<option value="Africa/Algiers">(GMT+01:00) West Central Africa</option>
					<option value="Africa/Windhoek">(GMT+01:00) Windhoek</option>
					<option value="Asia/Beirut">(GMT+02:00) Beirut</option>
					<option value="Africa/Cairo">(GMT+02:00) Cairo</option>
					<option value="Asia/Gaza">(GMT+02:00) Gaza</option>
					<option value="Africa/Blantyre">(GMT+02:00) Harare, Pretoria</option>
					<option value="Asia/Jerusalem">(GMT+02:00) Jerusalem</option>
					<option value="Europe/Minsk">(GMT+02:00) Minsk</option>
					<option value="Asia/Damascus">(GMT+02:00) Syria</option>
					<option value="Europe/Moscow">(GMT+03:00) Moscow, St. Petersburg, Volgograd</option>
					<option value="Africa/Addis_Ababa">(GMT+03:00) Nairobi</option>
					<option value="Asia/Tehran">(GMT+03:30) Tehran</option>
					<option value="Asia/Dubai">(GMT+04:00) Abu Dhabi, Muscat</option>
					<option value="Asia/Yerevan">(GMT+04:00) Yerevan</option>
					<option value="Asia/Kabul">(GMT+04:30) Kabul</option>
					<option value="Asia/Yekaterinburg">(GMT+05:00) Ekaterinburg</option>
					<option value="Asia/Tashkent">(GMT+05:00) Tashkent</option>
					<option value="Asia/Kolkata">(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi</option>
					<option value="Asia/Katmandu">(GMT+05:45) Kathmandu</option>
					<option value="Asia/Dhaka">(GMT+06:00) Astana, Dhaka</option>
					<option value="Asia/Novosibirsk">(GMT+06:00) Novosibirsk</option>
					<option value="Asia/Rangoon">(GMT+06:30) Yangon (Rangoon)</option>
					<option value="Asia/Bangkok">(GMT+07:00) Bangkok, Hanoi, Jakarta</option>
					<option value="Asia/Krasnoyarsk">(GMT+07:00) Krasnoyarsk</option>
					<option value="Asia/Hong_Kong">(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi</option>
					<option value="Asia/Irkutsk">(GMT+08:00) Irkutsk, Ulaan Bataar</option>
					<option value="Australia/Perth">(GMT+08:00) Perth</option>
					<option value="Australia/Eucla">(GMT+08:45) Eucla</option>
					<option value="Asia/Tokyo">(GMT+09:00) Osaka, Sapporo, Tokyo</option>
					<option value="Asia/Seoul">(GMT+09:00) Seoul</option>
					<option value="Asia/Yakutsk">(GMT+09:00) Yakutsk</option>
					<option value="Australia/Adelaide">(GMT+09:30) Adelaide</option>
					<option value="Australia/Darwin">(GMT+09:30) Darwin</option>
					<option value="Australia/Brisbane">(GMT+10:00) Brisbane</option>
					<option value="Australia/Hobart">(GMT+10:00) Hobart</option>
					<option value="Asia/Vladivostok">(GMT+10:00) Vladivostok</option>
					<option value="Australia/Lord_Howe">(GMT+10:30) Lord Howe Island</option>
					<option value="Etc/GMT-11">(GMT+11:00) Solomon Is., New Caledonia</option>
					<option value="Asia/Magadan">(GMT+11:00) Magadan</option>
					<option value="Pacific/Norfolk">(GMT+11:30) Norfolk Island</option>
					<option value="Asia/Anadyr">(GMT+12:00) Anadyr, Kamchatka</option>
					<option value="Pacific/Auckland">(GMT+12:00) Auckland, Wellington</option>
					<option value="Etc/GMT-12">(GMT+12:00) Fiji, Kamchatka, Marshall Is.</option>
					<option value="Pacific/Chatham">(GMT+12:45) Chatham Islands</option>
					<option value="Pacific/Tongatapu">(GMT+13:00) Nuku'alofa</option>
					<option value="Pacific/Kiritimati">(GMT+14:00) Kiritimati</option>


</select>                          </td>
                            </tr>
                            <tr class="row1">
                              <td colspan="2" align="left" valign="top"><em><strong>NOTE: All times will be in the time zone you selected.</strong> </em></td>
                              </tr>
                            <tr class="row2">
                              <td align="left" valign="top">Registration Opens:<br />
                                  <em>(mm/dd/yyyy)</em></td>
                              <td><input name="regStartDate" type="text" id="regStartDate" class="datePicker" value="<?=$regStartDate?>" size="10" maxlength="10" />
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>time:</strong>
                                <input name="regStartTime" type="text" id="regStartTime" value="<?=$regStartTime?>" size="5" maxlength="5" />
                                <select name="regStartAMPM" id="regStartAMPM">
                                  <option value="AM" <? if ($regStartAMPM == "AM"){ echo "selected";}?>>AM</option>
                                  <option value="PM" <? if ($regStartAMPM == "PM"){ echo "selected";}?>>PM</option>
                                </select>
                                HH:MM (<a href="http://www.timeanddate.com/worldclock/converter.html" target="_blank">Open the Time Zone Converter</a>) </td>
                            </tr>
                            <tr class="row1">
                              <td align="left" valign="top">Registration Ends:</td>
                              <td><input name="regEndDate" type="text" id="regEndDate" class="datePicker" value="<?=$regEndDate?>" size="10" maxlength="10" />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>time:</strong>
<input name="regEndTime" type="text" id="regEndTime" value="<?=$regEndTime?>" size="5" maxlength="5" />
<select name="regEndAMPM" id="regEndAMPM">
  <option value="AM" <? if ($regEndAMPM == "AM"){ echo "selected";}?>>AM</option>
  <option value="PM" <? if ($regEndAMPM == "PM"){ echo "selected";}?>>PM</option>
</select>
HH:MM </td>
                            </tr>
                            <tr class="row1">
                              <td align="left" valign="top" class="row2">Voting Opens:</td>
                              <td class="row2"><p>
                                  <input name="voteStartDate" type="text" id="voteStartDate" class="datePicker" value="<?=$voteStartDate?>" size="10" maxlength="10" />
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>time:</strong>
                                <input name="voteStartTime" type="text" id="voteStartTime" value="<?=$voteStartTime?>" size="5" maxlength="5" />
                                <select name="voteStartAMPM" id="voteStartAMPM">
                                  <option value="AM" <? if ($voteStartAMPM == "AM"){ echo "selected";}?>>AM</option>
                                  <option value="PM" <? if ($voteStartAMPM == "PM"){ echo "selected";}?>>PM</option>
                                </select>
                                HH:MM</p></td>
                            </tr>
                            <tr class="row2">
                              <td align="left" valign="top" class="row1">Voting Ends:</td>
                              <td class="row1"><input name="voteEndDate" type="text" id="voteEndDate" class="datePicker" value="<?=$voteEndDate?>" size="10" maxlength="10" />
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>time:</strong>
                                <input name="voteEndTime" type="text" id="voteEndTime" value="<?=$voteEndTime?>" size="5" maxlength="5" />
                                <select name="voteEndAMPM" id="voteEndAMPM">
                                  <option value="AM" <? if ($voteEndAMPM == "AM"){ echo "selected";}?>>AM</option>
                                  <option value="PM" <? if ($voteEndAMPM == "PM"){ echo "selected";}?>>PM</option>
                                </select>
                                HH:MM</td>
                            </tr>
                            
                            <tr class="row1">
                              <td align="left" valign="top" class="row2"><strong>Next Voting Round Opens:
           
                                </strong><br />
                                <em>(This is only text shown after the voting round ends. You will need to come back here and change the Voting Opens and Voting Ends time to officially start the next rounds.) </em></td>
                              <td align="left" valign="middle" class="row2"><input name="nextVotingStarts" type="text" id="nextVotingStarts" class="datePicker" value="<?=$nextVotingStarts?>" size="10" maxlength="10" />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>time:</strong>
<input name="nextVoteStartTime" type="text" id="nextVoteStartTime" value="<?=$nextVoteStartTime?>" size="5" maxlength="5" />
<select name="nextVoteStartAMPM" id="nextVoteStartAMPM">
  <option value="AM" <? if ($nextVoteStartAMPM == "AM"){ echo "selected";}?>>AM</option>
  <option value="PM" <? if ($nextVoteStartAMPM == "PM"){ echo "selected";}?>>PM</option>
</select>
HH:MM</td>
                            </tr>


				<tr bgcolor="#5C1010" class="row1">
                              <td align="left" valign="top" class="row2">Display timer for: </td>
                              <td class="row2">
				<input type="radio" name="timer" value="Registration" size="17">Registration
				<input type="radio" name="timer" value="Voting" size="17">Voting		
				<input type="radio" name="timer" value="None" size="17">None
				
				</td>
                            </tr>




                            <tr class="row1">
                              <td align="left" valign="top" class="row1">Contest Header: </td>
                              <td class="row1"><textarea name="contestHeader" cols="50" rows="6" id="contestHeader"><?=$contestHeader?></textarea></td>
                            </tr>
                            <tr class="row1">
                              <td align="left" valign="top" class="row2">Contest Rules: </td>
                              <td class="row2"><textarea name="terms" cols="50" rows="6" id="terms"><?=$terms?></textarea></td>
                            </tr>
                            <tr class="row1">
                              <td align="left" valign="top" class="row1">Maximum Signups: </td>
                              <td class="row1"><input name="maxAccounts" type="text" id="maxAccounts" size="25" maxlength="20" /> 
                                (no commas)</td>
                            </tr>
                            <tr class="row2">
                              <td align="left" valign="top" class="row2">Contest Question: </td>
                              <td class="row2"><textarea name="commentQuestion" cols="50" rows="4" id="commentQuestion"><?=htmlentities($commentQuestion, ENT_QUOTES)?></textarea></td>
                            </tr>
                            <tr class="row2">
                              <td align="left" valign="top" class="row1">Show User's Full Name: </td>
                              <td class="row1"><select name="showFullName" id="showFullName">                          
                                <option value="0" <? if ($showFullName == 0){ echo "selected";}?>>no</option>
							     <option value="1" <? if ($showFullName == 1){ echo "selected";}?>>yes</option>
                              </select>&nbsp;</td>
                            </tr>
                            
				

				<tr class="row2">
                              <td align="left" valign="top" class="row2">Winner's Caption</td>
                              <td class="row2"><span class="row1">
                                <input name="winnerCaption" type="text" id="winnerCaption" value="<?=$winnerCaption?>" size="50" maxlength="255" />
                              </span></td>
                            </tr>
					

                            <tr class="row1">
                              <td align="left" valign="top" class="row1">Is this the last round?</td>
                              <td class="row1"><input name="isLastRound" type="checkbox" id="isLastRound" value="1" />
                                <label for="isLastRound">Tick if yes</label></td>
                            </tr>
                          </table>
                          <p>
                            <input type="submit" name="Submit" value="Create Contest" />
                            <input type="hidden" value="INSERT" name="a" />
                          </p>
                        </form></td>
			</tr>

                    </table>
                      <br /></td>
                  </tr>
                  
                </table>                  </td>
              </tr>
            </table>
	      </td>
      </tr>
	  <tr>
	    <td align="left" valign="top">&nbsp;</td>
	    </tr>
    </table></td>
  </tr>
</table>
</body>
</html>
