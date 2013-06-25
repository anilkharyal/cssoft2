<?php
include_once('ps_pagination.php');

function setzonename()
{
	$conid = getActiveContest();
	$data1 = mysql_query("SELECT * FROM `contest` WHERE `id`='".$conid."'") 
	$info = mysql_fetch_array($data1);
    $reg=$info['timeZone'];

	date_default_timezone_set($reg);

if (date_default_timezone_get()) {

}

if (ini_get('date.timezone')) {
    
}

 
 date_default_timezone_set($reg);
 $date= date('F j, Y, g:i a') ; 


	
	return $date;

}


function getpicture($uID)
{
	$conid = getActiveContest();
	
	$sql = "SELECT * FROM `pictures` where `ownerID` ='$uID' and `contestID` = '$conid'";
	$res = mysql_query($sql);	
	while($row = mysql_fetch_array($res))
{
	return $res1 = $row['picURL']; 	
}
}



function showWinner()
{
	// get the most recent contest
	
	
	$val= getLastContest();
	if ($val >0)
	{
		return true;
	}
	else
	{
		return false;
	}
}
function checkVote($uID)
{
	// get the vote id from the cookie
	$voteID = getVoteID($votingPass);

	// get contest
	$contestID = getActiveContest();

	// maximum protection get user ID 
	$userID = getMemberID();
	
	// now get the count of votes for this user
	if ($userID > 0)
	{
	$sql = "SELECT rating FROM `ratings` WHERE (`cookieID`=" . safe($voteID) . " OR `voterID`=" .safe($userID) . ") AND `voteForID`=" . safe($uID) . " AND `contestID`=" . safe($contestID) . " LIMIT 1";
	}
	else
	{
	$sql = "SELECT rating FROM `ratings` WHERE `cookieID`=" . safe($voteID) . " AND `voteForID`=" . safe($uID) . " AND `contestID`=" . safe($contestID) . " LIMIT 1";
	}

	$db = DBConnect();
	
	$result = mysql_query($sql, $db);
	if ($result)
	{
		if (mysql_num_rows($result) == 0)
		{
			return false;
		}
		else
		{		
			$row = mysql_fetch_row($result);
			return $row[0];
		}
	}
	else
	{
		return false;
	}	 
}
function showFullName($contestID)
{
	$val= getSimpleVal('showFullName', 'contest', 'ID', $contestID);
	if ($val >0)
	{
		return true;
	}
	else
	{
		return false;
	}
}
function getContestTerms($contestID)
{

	return getSimpleVal('terms', 'contest', 'ID', $contestID);
}
function getContestQuestion($contestID)
{

	return getSimpleVal('commentQuestion', 'contest', 'ID', $contestID);
}
function printVoteBox($uID)
{
	// get the member's id
	$memberID = getMemberID();
	$canVote = isVotingOpen($voteStart, $voteClose);
	$votesLeft = getVotesLeft();
	$votedBefore = checkVote($uID);
	if ($votedBefore == false && $votesLeft > 0 && $canVote)
	{
	
?>
<style type="text/css">
<!--
.style1 {font-weight: bold}
-->
</style>				<div id="rateDiv">						  
                          <form id="rateForm" name="rateForm" action="rating.php" onsubmit="return sendVote(<?=$uID?>);">
                            <input type="image" name="submit" src="graphics/vote.jpg" alt="Vote For This Gallery" />  
						  </form></div>
<?
	}
	else if ($canVote == false)
	{
		echo '<img src="graphics/starRed.gif" alt="-" align="absmiddle" />' . "Voting has not yet started.";
	}
	else if ($votesLeft < 1)
	{
		echo '<img src="graphics/starRed.gif" alt="-" align="absmiddle" />' . "You have <strong>0 votes</strong> left.";
		if ($votedBefore >0)
		{
			echo "<br/><br/>You voted for this gallery.";
		}
	}
	else
	{
		// already voted
		echo '<img src="graphics/starRed.gif" alt="-" align="absmiddle" />' . "You voted for this gallery.";
	}
}
function getContestState($contestID)
{
	$sql="SELECT * FROM `contest` WHERE `ID`=" . safe($contestID) . " LIMIT 1";
	$db = DBConnect();

	$result = mysql_query($sql, $db);
	if ($result)
	{	
			$registrationOpens = $row["registrationOpens"];
			$registrationEnds = $row["registrationEnds"];
			$votingEnds = $row["votingEnds"];	
			$votingStarts = $row["votingOpens"];	
		
			return getContestStateHelper($registrationOpens, $votingStarts, $votingEnds, &$reasonString);
	}	
	else
	{
		return 0;
	}
		
	
}
function getContestStateHelper($regStartDate, $votingStartDate, $votingEndDate, &$reasonString)
{
	
	// this function will analyze the various dates of the contest
	
	$regStart = strtotime($regStartDate);
	$voteStart = strtotime($votingStartDate);
	$voteEnd = strtotime($votingEndDate);
	
	$regStartDate = date("M d, Y h:mA", $regStart);
	$regStartDate = date("M d, Y h:mA", $voteStart);
	$votingEndDate = date("M d, Y h:mA", $voteEnd);		
	
	$curTime = time();
	$reasonString = "";
	if ($voteEnd < $curTime)
	{
		
		// contest is now closed, voting is over
		$reasonString = "The voting period has ended and this contest is now closed";
		return 0;
	}
	else if ($regStart < $curTime  && $curTime < $voteStart)
	{
		// contest is now closed, voting is over
		$reasonString = "Accepting registration. Voting begins $votingStartDate";
		return 1;
	}
	else if (  $voteStart < $curTime  && $curTime < $voteEnd)
	{
		// contest is now closed, voting is over
		$reasonString = "Voting is now open and will close $votingEndDate";
		return 2;
	}
	else if ($curTime < $regStart)
	{
		$reasonString = "This contest is not yet open. Registration will begin $regStartDate";
		return 3;
	
		
	}	
}
function isFavorite($ownerID, $favID)
{
	$totalFound = 0;
	$db = DBConnect();
	$memberID = getMemberID();

	$favCookieID = getFavoriteID();
	
	if ($memberID > 0)
	{
		// logged in, so use cookie OR memberID
		$sql="SELECT count(*) FROM `favorites` f WHERE favoriteID=" . safe($favID) . " AND (f.ownerID=" . safe($memberID) . " OR f.cookieID=" . safe($favCookieID) . ")" . " LIMIT 1";
	}
	else
	{
		// not logged in, just use cookie
		$sql="SELECT count(*) FROM `favorites` f WHERE favoriteID=" . safe($favID) . " AND f.cookieID=" . safe($favCookieID) . " LIMIT 1";
	}
	
	
	//$sql="select count(*) from favorites where ownerID=" . safe($ownerID) . " AND favoriteID=" . safe($favID) . " LIMIT 1";

	$result = mysql_query($sql, $db);
	if ($result)
	{
		$row = mysql_fetch_row($result);
		$totalFound = $row[0];		
	}
	
	if ($totalFound > 0)
	{
		return true;
	}
	else
	{
		return false;
	}
}
function getThumbnail($uID)
{
	$picURL = "";
	$db = DBConnect();
	
	$sql="SELECT thumbURL FROM `pictures` p WHERE p.`tableName`='contest' AND p.ownerID=" .safe($uID) . " ORDER BY p.ID ASC LIMIT 1";

	$result = mysql_query($sql, $db);
	if ($result)
	{
		$row = mysql_fetch_row($result);
		$picURL = $row[0];
	}
	return $picURL;
	
}
function printRandomThumbs($contestID, $limit=10)
{
	$db = DBConnect();
	
		
	$sql = "SELECT a.ID,  concat(firstName, ' ', lastName) as fullName FROM `accounts` a LEFT JOIN `ratings` r ON r.voteForID=a.ID WHERE a.`galleryApproved`=1 AND a.`currentContest`=" . safe($contestID) . " ORDER BY RAND() LIMIT $limit" ;

	$result = mysql_query($sql, $db);
	if ($result)
	{
		while ($row = mysql_fetch_array($result))
		{
			$thumbSrc = getThumbnail($row["ID"]);
			$thumbURL = PICTURES_UPLOAD_DIR  . $thumbSrc;
			$id = $row['ID'];
			?><div class="thumb"><a href="viewGallery.php?uID=<?=$id?>"><img src="<?=$thumbURL?>" width="50" /></a></div><?
		}
	}
		
}
function printBrowseList($page=1, $randSeed="")
{
	// Grab the data from the database
	
	// get current contest
	$contestID = getActiveContest();
	$db = DBConnect();
	
	if ($randSeed == "")
	{
		$randSeed = rand(1,9999999);
	}
	$sql = "SELECT a.ID,  concat(firstName, ' ', lastName) as fullName FROM `accounts` a LEFT JOIN `ratings` r ON r.voteForID=a.ID WHERE a.`galleryApproved`=1 AND a.`currentContest`=" . safe($contestID) . " GROUP BY a.ID ORDER BY RAND(" . $randSeed . ")" ;
	//Create a PS_Pagination object
	$maxPerPage = 16;
	$pageResults = 7;
	$pager = new PS_Pagination($db,$sql,$maxPerPage,$pageResults, $randSeed);
	$totalResult = mysql_query($sql, $db);
	$totalGalleries =  mysql_num_rows($totalResult);
	//The paginate() function returns a mysql result set 
	$result = $pager->paginate();
	
	//$result = mysql_query($sql, $db);
	if ($result)
	{
		$totalShowing = mysql_num_rows($result);
		if ($totalGalleries == 0)
		{
			echo "no pictures uploaded";
		}
		else
		{
			// get the starting picture number
			$startNum = $pager->offset + 1;
			
			// get end number
			// previous page rows plus what's on this page
			$endNum = (($pager->page - 1) * $maxPerPage) + $totalShowing;
		?><h2>Total Galleries: <?=$totalGalleries?></h2><h3>Showing <?=$startNum?>-<?=$endNum?></h3><?
		
		if ($pager->max_pages > 1)
		{
			echo '<div class="pageNav">Results Page: ';
		
			//Display the link to first page: First
			//echo $pager->renderFirst();
			
			if ($pager->page > 1)
			{
				//Display the link to previous page: <<
				echo $pager->renderPrev("PREV");
			}
			echo $pager->renderNav();
			if ($pager->page < $pager->max_pages)
			{
				//Display the link to previous page: <<
				echo $pager->renderNext("NEXT");
			}
			//Display page links: 1 2 3
			echo '</div>';
		}
	
	//Display the link to next page: >>
	
	?><?
		}
		while ($row=mysql_fetch_array($result))
		{
			$thumbSrc = getThumbnail($row["ID"]);
			$thumbURL = PICTURES_UPLOAD_DIR  . $thumbSrc;
			
			$rating = $row["avg_rating"];
			$totalVotes = $row["totalVotes"];
			if ($rating == NULL)
			{
				$rating = "<em>no votes</em>";
			}
			// should we show full name or ID
			$showFullName = showFullName($contestID);
			$displayName = '';
			if ($showFullName)
			{
				$displayName = $row["fullName"];
			}
			else
			{
				$displayName =  'Contestant ' . $row["ID"];
			}
			
			if ($thumbSrc == '' || !file_exists($thumbURL) )
			{
				// file not found								
				$thumbURL = 'graphics/noPic.gif';
			}
			//$thumbURL = PICTURES_UPLOAD_DIR . $row["thumbURL"];
			//$caption = wordwrap(stripslashes($row["caption"]), 20,"<BR/>");
			?><div class="pic"><a href="viewGallery.php?uID=<?=$row["ID"]?>&randSeed=<?=$randSeed?>"><img alt="view this gallery" title="view this gallery" class="picBorder" src="<?=$thumbURL?>" /></a>
<div class="rating" align="center"><?=$displayName?></div>
</div>
<?
		}
		
	//Display the full navigation in one go
	//Display the link to last page: Last
	//echo $pager->renderLast();
	}
	if ($pager->max_pages > 1)
		{
			echo '<div class="pageNav">Results Page: ';
		
			//Display the link to first page: First
			//echo $pager->renderFirst();
			
			if ($pager->page > 1)
			{
				//Display the link to previous page: <<
				echo $pager->renderPrev("PREV");
			}$qry7 = mysql_query("SELECT * FROM `contest` WHERE `id`='".$conid."'") 
	  or die(mysql_error()); 
	$info7 = mysql_fetch_array( $qry7);
        echo $regopn = $info7['registrationOpens'];
	echo $regend = $info7['registrationEnds'];
	
			echo $pager->renderNav();
			if ($pager->page < $pager->max_pages)
			{
				//Display the link to previous page: <<
				echo $pager->renderNext("NEXT");
			}
			echo '</div>';
			//Display page links: 1 2 3
			
		}
	
}
function setFavoriteID()
{
	// set a cookie for the favorite list, use a unique id
	$better_token = uniqid(md5(rand()), true);
	setcookie ("cntfavid", $better_token, time() + 31536000, "/", '.missinternetdiva.com'); // 1 year from now
	
	// return new id
	return $better_token;
}
function getFavoriteID()
{
	// returns the ID in the favorite cookie, or 0 if none found
	return session_id();
	$favID = $_COOKIE["cntfavid"];
	
	if ($favID == "")
	{
		return 0;
	}
	else
	{
		return $favID;
	}
}
function printFavoriteList($page=1)
{
	// Grab the data from the database
	
	// get current contest
	$memberID = getMemberID();
	$contestID = getActiveContest();
	$favCookieID = getFavoriteID();
	
	$db = DBConnect();
	
	if ($memberID > 0)
	{
		// logged in, so use cookie OR memberID
		$sql="SELECT f.favoriteID, concat(firstName, ' ', lastName) as fullName FROM `favorites` f, `accounts` a WHERE a.`ID`=f.`favoriteID` AND a.`galleryApproved`=1 AND a.`currentContest`=" . safe($contestID) . " AND (f.ownerID=" . safe($memberID) . " OR f.cookieID=" . safe($favCookieID) . ")";
	}
	else
	{
		// not logged in, just use cookie
		$sql="SELECT f.favoriteID, concat(firstName, ' ', lastName) as fullName FROM `favorites` f, `accounts` a WHERE a.`ID`=f.`favoriteID` AND a.`galleryApproved`=1 AND a.`currentContest`=" . safe($contestID) . " AND f.cookieID=" . safe($favCookieID);
	}



	$sql .= " ORDER BY `favoriteID`";

//Create a PS_Pagination object
	$maxPerPage = 16;
	$pageResults = 7;
	$pager = new PS_Pagination($db,$sql,$maxPerPage,$pageResults);
	$totalResult = mysql_query($sql, $db);
	$totalGalleries =  mysql_num_rows($totalResult);
	//The paginate() function returns a mysql result set 
	$result = $pager->paginate();
	
	//$result = mysql_query($sql, $db);
	if ($result)
	{
		$totalShowing = mysql_num_rows($result);
		if ($totalGalleries == 0)
		{
			echo "You have no favorite galleries.";
		}
		else
		{
			// get the starting picture number
			$startNum = $pager->offset + 1;
			
			// get end number
			// previous page rows plus what's on this page
			$endNum = (($pager->page - 1) * $maxPerPage) + $totalShowing;
		?><h2>Total Galleries: <?=$totalGalleries?></h2><h3>Showing <?=$startNum?>-<?=$endNum?></h3><?
		
		if ($pager->max_pages > 1)
		{
			echo "<br />Results Page: ";
		
			//Display the link to first page: First
			//echo $pager->renderFirst();
			
			if ($pager->page > 1)
			{
				//Display the link to previous page: <<
				echo $pager->renderPrev("PREV");
			}
			echo $pager->renderNav();
			if ($pager->page < $pager->max_pages)
			{
				//Display the link to previous page: <<
				echo $pager->renderNext("NEXT");
			}
			//Display page links: 1 2 3
			
		}
	
	//Display the link to next page: >>
	
	?><hr size="1" noshade="noshade" color="#CC0000"/><?
		}
		while ($row=mysql_fetch_array($result))
		{
			$showFullName = showFullName($contestID);
			$displayName = '';
			if ($showFullName)
			{
				$displayName = $row["fullName"];
			}
			else
			{
				$displayName =  'ID: ' . $row["favoriteID"];
			}
			$thumbURL = PICTURES_UPLOAD_DIR . getThumbnail($row["favoriteID"]);
			//$thumbURL = PICTURES_UPLOAD_DIR . $row["thumbURL"];
			//$caption = wordwrap(stripslashes($row["caption"]), 20,"<BR/>");
			?><div class="pic"><a href="viewGallery.php?uID=<?=$row["favoriteID"]?>&fav=1"><img alt="view this gallery" title="view this gallery" class="picBorder" src="<?=$thumbURL?>" /></a><div align="center"><a href="removeFavorite.php?returnFav=1&favID=<?=$row["favoriteID"]?>">Remove from Favorites</a></div><div class="rating" align="center"><?=$displayName?></div></div>
<?
		}
	
	//Display the link to last page: Last
	//echo $pager->renderLast();
	}
	
	
}
function getNextVotingRound($contestID)
{
	return getSimpleVal('nextVotingStarts', 'contest', 'ID', $contestID);
}
function isVotingOpen(&$votingOpensTime, &$votingEndsTime)
{
	// get the contest
	$contestID = getActiveContest();
	
	// now see the date that the voting begins
	$votingOpens = getSimpleVal('votingOpens', 'contest', 'ID', $contestID);
	$votingEnds = getSimpleVal('votingEnds', 'contest', 'ID', $contestID);
	$nowtym = setzonename();
	$votingOpensTime = strtotime($votingOpens);
	$votingEndsTime = strtotime($votingEnds);
	$nowTime = strtotime($nowtym);	
	//$nowTime = strtotime("now");
	// if the the time now is past the opening time and before the closed time
	if ($votingOpensTime <= $nowTime && $nowTime <= $votingEndsTime)
	{
		return true;
	}
	else
	{
		return false;
	}
}
function getUserTime()
{
	// returns the USERS time
	// not 100% but based on javascript
	// more accurate than IP tracing 
	$userTZ = getTimeZone();

	$serverTZ = SERVER_TIME_ZONE;
	$offsetTZ = $userTZ  - $serverTZ;
		//echo "tz: $userTZ <br>";
	       //echo "offsetTZ: $offsetTZ <br>";
	return strtotime($offsetTZ . " hour", time());
}

function getNowTime()
{

	$contestID = getActiveContest();	
	$userTZ = getTimeZone().'<br>';
        $serverTZ = SERVER_TIME_ZONE;
//	$contestTZ = getSimpleVal('timeZone', 'contest', 'ID', $contestID);
	$contestTZ = getCountryTimeZone($contestID); 
	
	// the now time is the time that the server will compare the current server time to to see if the contest is open
	// it is used instead of using time(); so as to incorporate time zones
	// so for instance the server time zone is -8	
	// and the visitors time zone is -6
	// and the actual contest time zone is set for -5
	
	// so the now time should be 
	
	// get the time now using the contest's time zone
	
	$offsetTZ = $contestTZ - $serverTZ;
	//$localTime = strtotime($offsetTZ . " hour", time());
	$localTime = strtotime($offsetTZ);
	
	/*
		echo "$userTZ - $contestTZ - $serverTZ = $offsetTZ";
	
	echo "TIME ZONE: " . getTimeZone() . "<BR>";
	echo "TIME: " . time() . "<BR>";

	echo "Your Local Time is " . strftime("%I:%M %p", $localTime);*/
	return $offsetTZ;
}
function isRegistrationOpen(&$regOpensTime, &$regEndsTime)
{	
		

	// get the contest
	 $contestID = getActiveContest();
	
	// now see the date that the registration begins
	 $regOpens = getSimpleVal('registrationOpens', 'contest', 'ID', $contestID);
	$regEnds = getSimpleVal('registrationEnds', 'contest', 'ID', $contestID);
	$nowtym = setzonename();
	$regOpensTime = strtotime($regOpens);
	$regEndsTime = strtotime($regEnds);;	
	$nowTime = strtotime($nowtym);
        //echo $nowTime = strtotime("now").'<br>';die();
	//echo $regOpensTime . "<BR>";
		//echo $nowTime . "<BR>" ;
	//echo $regEndsTime  . "<BR>";	
	//echo "Your NOw Time is " . strftime("%I:%M %p", $nowTime);
	
	
	// if the the time now is past the opening time and before the closed time
	if ($regOpensTime <= $nowTime && $nowTime <= $regEndsTime)
	{
		return true;
	}
	else
	{
		return false;
	}

}

function getTotalRegistrants($contestID="")
{
	if ($contestID == "")
	{
		$contestID = getActiveContest();
	}
	return getSimpleVal("COUNT(*)", "accounts", "currentContest", $contestID);
}
function getMaxRegistrants($contestID="")
{
	if ($contestID == "")
	{
		$contestID = getActiveContest();
	}
	return getSimpleVal("maxAccounts", "contest", "ID", $contestID);
}
function getCountryTimeZone($contestID)
{
	$timezoneID = getSimpleVal('timeZone', 'contest', 'ID', $contestID);
	
	$tz = getSimpleVal('gmt_offset', 'timezone', 'timezoneid', $timezoneID);
	
	return $tz;
}
function printRegistrationDates()
{
	$contestID = getActiveContest();
//	$contestTZ = getSimpleVal('timeZone', 'contest', 'ID', $contestID);
	$contestTZ = getCountryTimeZone($contestID).'<br/>';
	$userTZ = getTimeZone();

	$qry7 = mysql_query("SELECT * FROM `contest` WHERE `id`='".$contestID."'") 
	  or die(mysql_error()); 
	$info7 = mysql_fetch_array( $qry7);
        $regopn = $info7['registrationOpens'];
	$regend = $info7['registrationEnds'];
	
	$old_date_timestamp2 = strtotime($regend);
	$new_date2 = date('F j, Y \a\\t g:i a', $old_date_timestamp2);

	$old_date_timestamp = strtotime($regopn);
	$new_date = date('F j, Y \a\\t g:i a', $old_date_timestamp);

	// get the difference between the two
	// and make the start time as though it was the user's time zone
	$diff = $userTZ - $contestTZ; 
	$regStarts;
	$canRegister = isRegistrationOpen($regStarts, $regEnds);
	$regStartStr = $new_date;
	$regEndStr = $new_date2;
	//$regStartStr = date("F j, Y \a\\t g:i a", strtotime($diff . " HOURS" , $regStarts));
	//$regEndStr = date("F j, Y \a\\t g:i a",  strtotime($diff . " HOURS" , $regEnds));	
//	$curDate = date("F j, Y, g:i a", getUserTime());
	//$curDate = date("F j, Y, g:i a");
	$curDate = setzonename();
	$t=time();
	//$curDate=date("F d Y,H:i");




	$maxAccounts = getMaxRegistrants();
	$contestInfo = getContestInfo();
					?>
<h2>Registration is not  open</h2>
<table width="100%" border="0" cellspacing="1" cellpadding="1">
  <tr>
    <td width="19%"><strong>Registration Opens</strong>:</td>
    <td width="81%"><?=$regStartStr?> (Your Time)</td>
  </tr>
  <tr>
    <td><strong>Registration Closes</strong>:</td>
    <td><?=$regEndStr?>  (Your Time)</td>
  </tr>
  <tr>
    <td><strong>Max. Contestants: </strong></td>
    <td><?=$maxAccounts?></td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td><strong><img src="graphics/starRed.gif" alt="-" width="18" height="19" align="absmiddle" /> Prize: </strong></td>
    <td><?=$contestInfo["prize"];?></td>
  </tr>
  <tr>
    <td colspan="2"><hr size="1" noshade="noshade" color="#CC0000"/></td>
  </tr>
</table>
<br />
<img src="graphics/starRed.gif" alt="-" width="18" height="19" align="absmiddle" /> On that date you will be able to register for the contest and upload your pictures.<br />
<img src="graphics/starRed.gif" alt="-" width="18" height="19" align="absmiddle" /> We are tracking your time as:
<?=$curDate?>. <br />
<br />
<?

}
function getLastContest()
{
	// gets the most recent closed contest for this country
	$ipNum = getIPNumber($_SERVER['REMOTE_ADDR']);		
		
	// get the country code
	$cc = getCountryCode($ipNum);
	$db = DBConnect();
	$sql = "SELECT `ID` from `contest` WHERE `country`=" . safe($cc) . " AND `winnerChosen`=1 ORDER BY `dateClosed` DESC LIMIT 1";
	
	$result = mysql_query($sql, $db);
	if ($result)
	{
		$row=mysql_fetch_array($result);
		$contestID = $row["ID"];
	}
	else
	{
		$contestID = 0;
	}
	return $contestID;
}
function getActiveContest($overrideID=0)
{
	
	// return the contest that this user should see
	// this is first based on the account data
	// if nothing is in there, then go to IP address lookup
	if ($overrideID == 0)
	{
		$memberID = getMemberID();
	}
	else
	{
		$memberID = $overrideID;
	}
	$contestID = 0;
	$contestID = getSimpleVal('currentContest', 'accounts', 'ID', $memberID);

	// check for override
	if ($_COOKIE["cntover"] <> "")
	{
		$contestID = 0;
	}
	

	if ($contestID == 0)
	{
		// then check IP and get the contest for this country code
		
		// get the ip number
		$ipNum = getIPNumber($_SERVER['REMOTE_ADDR']);		
		
		// get the country code
		$cc = getCountryCode($ipNum);

		if ($cc == "")
		{
			// no active contest as the country could not be found
			$contestID = 0;
		}
		else
		{
			// get the contest for this country code that is beyond expired, and closest to this date.
			$db = DBConnect();
			//$sql = "SELECT `ID` from `contest` WHERE `country`=" . safe($cc) . " AND `votingEnds`>=NOW() AND `winnerChosen`=0 ORDER BY `registrationOpens` ASC LIMIT 1";
			$sql = "SELECT `ID` from `contest` WHERE `country`=" . safe($cc) . " AND `nextVotingStarts`>=NOW() AND `winnerChosen`=0 ORDER BY `registrationOpens` ASC LIMIT 1";
		
			$result = mysql_query($sql, $db);
			if ($result)
			{
				$row=mysql_fetch_array($result);
				$contestID = $row["ID"];
			}
			else
			{
				$contestID = 0;
			}

		}
	
		
		
		
	}
	
	return $contestID;
}
function printContestInfo($contestID =0)
{
	$contestInfo = '';
	if ($contestID == 0)
	{
		$contestInfo = getContestInfo();
		$contestID = $contestInfo["ID"];
	}
	else
	{
		$contestInfo = getContestInfo($contestID);
	}
	$details = $contestInfo['details'];
	$prize = $contestInfo['prize'];	
?>
<table width="100%" border="0" cellpadding="2" cellspacing="4">
                      
                      <tr>
                        <td width="54%"><h2>Current Contest </h2></td>
                      </tr>
                      <tr>
                        <td><img src="graphics/starRed.gif" alt="-" width="18" height="19" align="absmiddle" /> <strong>Prize</strong>:
                          <?=$prize?>
                        </td>
  </tr>
                      
                      <tr>
                        <td><hr size="1" noshade="noshade" color="#CC0000"/></td>
                      </tr>
</table>
<?	
}
function getContestInfo($contestID="")
{
	// returns an array of current contest info
	$ret = array();
	
	if ($contestID=="")
	{
		// get the correct contest
		$contestID = getActiveContest();
	}
	$sql="SELECT * FROM `contest`  WHERE `ID`=" . safe($contestID) . " LIMIT 1 ";
	
	
	$db = DBConnect();
	
	$result = mysql_query($sql, $db);
		if ($result)
		{
			$row = mysql_fetch_array($result);
			$ret = $row;
			return $ret;
		}
		else
		{
			return false;
		}
		
}
function getGalleryInfo($userID)
{
	// returns information about this gallery
	$ret = array();
	// Grab the data from the database
	$sql="SELECT concat(firstName, ' ', lastName) as fullName, firstName, lastName, email, dateCreated, galleryApproved, enteredContest, allowUploadOverride, emailConfirmed FROM `accounts`,  WHERE `ID`=" . safe($id) . " LIMIT 1 ";
	$db = DBConnect();
	
	$result = mysql_query($sql, $db);
		if ($result)
		{
			// get the variables
			return mysql_fetch_array($result);			
		}
		else
		{
			return array();
		}
}
?>
