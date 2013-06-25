<?php

include("geoip/geoipcity.inc.php");
require ("settings.inc.php");
include ("email.inc.php");


function confirmUser($id)
{
	$db = DBConnect();
	$sql = "UPDATE `accounts` SET `emailConfirmed`=1 WHERE `ID`=" . safe($id) . " LIMIT 1";
	$result = mysql_query($sql, $db);
	if ($result)
	{	
		return true;
	}
	else
	{
		return false;
	}
}
function sendConfirmationEmail($id, $email)
{
	// setup a confirmation code;
	// sends the email confirmation with a special link to confirm the signup
	$c = rand(10000,999999);
	// store the code in the database
	// TODO
	$db = DBConnect();
	$sql = "UPDATE `accounts` SET `confirmCode`=$c WHERE `ID`=" . safe($id) . " LIMIT 1";
	$result = mysql_query($sql, $db);
	if ($result)
	{
		$subject = "Confirmation Email";
		$confirmLink = "http://www.missinternetdiva.com/confirm.php?id=$id&c=$c";
		$message = getContent("REGISTER_EMAIL");
		$message = str_replace("<<LINK>>",$confirmLink,$message);
		$altMessage = preg_replace('/<br\\\\s*?\\/??>/i', "\\n", $message);
		return sendEmail($email, $subject, $message, $altMessage);
	}
	else
	{
		return false;
	}
	
}
if (!function_exists("stripos")) {
  function stripos($str,$needle,$offset=0)
  {
     return strpos(strtolower($str),strtolower($needle),$offset);
  }
}
function viewedGallery($id)
{
	// increments a view counter
	$db = DBConnect();
	$sql = "UPDATE `accounts` SET `totalViews`=`totalViews`+1 WHERE `ID`=" . safe($id) . " LIMIT 1";
	$result = mysql_query($sql, $db);
	if ($result)
	{
		return true;
	}
	else
	{
		return false;
	}
}
function getIPNumber($dotted_ip_address)

{

	// override to just return Ip since we are now using the binary database instead of sql
	// update: brian april 28, 2011
	return $dotted_ip_address;
	$ip_parts = explode(".",$dotted_ip_address);
	$ip_number = sprintf("%u", ip2long($dotted_ip_address));
	return $ip_number;
}


function getUsersCountry()

{
	// gets the current visitor's country code via IP lookup
	$ipNum = getIPNumber($_SERVER['REMOTE_ADDR']);	
	$cc = getCountryCode($ipNum);
	return $cc;
}


function getCountryCode($ip, $noOver=false)
{
	// override country
	//return "US"; // everyone is US
	if ($noOver == false)
	{
		// check for override
		if ($_COOKIE["cntover"] <> "")
		{
			$cc = $_COOKIE["cntover"];
			return $cc;
		}
	}
	
	$gi = geoip_open("/home/missi27/public_html/geoip/GeoLiteCity.dat",GEOIP_STANDARD);
	$ret = geoip_country_code_by_addr($gi, $ip);
	geoip_close($gi);	
	return $ret;


}


function getCountryCodeOld($ipNum, $noOver=false)
{
	//errorLog( "session: " . $_SESSION["cntover"]);
	// override country
	//return "US"; // everyone is US
	if ($noOver == false)
	{
		if ($_COOKIE["cntover"] <> "")
		{
			$cc = $_COOKIE["cntover"];
			return $cc;
		}
	}
	$db = DBConnect();
	$sql="SELECT cc FROM geo_ip WHERE " . safe($ipNum) . " >= start AND " . safe($ipNum) . " <= end LIMIT 1";

	
	
	$result = mysql_query($sql, $db);
	$ret = "";
	
	if ($result)
	{
		$row = mysql_fetch_row($result);
		$ret = $row[0];
	}
	return $ret;
	


}
function br2nl($text)
{
    return  str_replace("<br />", chr(13) . chr(10), $text);
}
function getCountryFullName($cc)
{
	$db = DBConnect();
	$sql = "SELECT cn FROM `geo_ip` WHERE `cc`=" . safe($cc) . " LIMIT 1";
	$result = mysql_query($sql, $db);
	$name = "";
	if ($result)
	{
		$row = mysql_fetch_array($result);
		$name = $row["cn"];
	}
	return $name;
}
function printCountrySelectBox($selected="", $allowAny=false, $linkTimeZone=false)
{
	$db = DBConnect();
	$sql = "SELECT cc, cn FROM `geo_ip` GROUP BY cc ORDER BY cn";
	$result = mysql_query($sql, $db);
	if ($result)
	{ 
		?>
	<select name="country" id="country" <? if ($linkTimeZone){?> onChange="countryChanged(this);" <? } ?>>
<? if ($allowAny){?><option value="">All Countries</option><? } ?>
	<? 
		while ($row = mysql_fetch_array($result))
		{
			$cn = $row["cn"];
			$cc = $row["cc"];
		?>	<option value="<?=$cc?>" <? if ($selected == $cc){?>selected="selected"<? } ?>><?=$cn?></option>
				<?
		}
		?>
	</select><?
	}
}
function DBConnect($dbName = SITE_DBNAME)
{		
	$dbx = mysql_connect(SITE_HOST, SITE_LOGIN, SITE_PASSWORD) or die("Could not connect to database");
		mysql_select_db($dbName,$dbx);
		return $dbx;		
		exit;
}

function checkEmail($email, &$retReason)
{
	// checks if the email is in the database
	// then checks if it is valid email
	$emailID = getUserIDFromEmail($email);
	if ($emailID > 0)
	{
		$retReason = "That email address is already in the system. Try logging in";
		return false;
	}
	else
	{
		// see if valid email
		if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email))
		{
            $retReason =  "The e-mail was not valid";
			return false;
        }
		else
		{         
			return true;
		}

	}
	
}
function safe( $string )
{

	if (is_numeric($string))
	{
		return $string;
	}
	else
	{
		return "'" . mysql_real_escape_string($string) . "'";
	}

}
function isGalleryApproved($id)
{
	// returns true if this gallery is approved
    $retVal = getSimpleVal('galleryApproved', 'accounts', 'ID', $id);
	if ($retVal == 1)
	{
		return true;
	}
	else
	{
		return false;
	}
}


function getMaxPicsAllowed()
{
	return getSimpleVal('value', 'settings', 'name', 'picturesAllowed');
}
function getUnapprovedPictures($contestID)
{
	$db = DBConnect();
	
	$sql = "SELECT COUNT(*) FROM `accounts` WHERE `galleryApproved`=0 AND `currentContest`=" . safe($contestID);

	$result = mysql_query($sql, $db);
	$retVal = 0;
	
	if($result)
	{
		// check if this exists
		$row = mysql_fetch_row($result);
		$retVal = $row[0];		 	  	
	 }	
	return $retVal;
}
function getSetting($whichSetting)
{
	 return getSimpleVal('value', 'settings', 'name', $whichSetting);
}
function isUserConfirmed($id)
{
	// returns 0 if does not exist
	// otherwise returns the ID
	$retVal = getSimpleVal('emailConfirmed', 'accounts', 'ID', $id);
	if ($retVal == 1)
	{
		return true;
	}
	else
	{
		return false;
	}
	 
}
function getGalleryComments($id, $nonl=false)
{
	 if ($nonl)
	 {
	 	return trim(getSimpleVal('galleryComments', 'accounts', 'ID', $id));	
	 }
	 else
	 {
	 	return nl2br(trim(getSimpleVal('galleryComments', 'accounts', 'ID', $id)));	
	 }
}
function getConfirmCode($id)
{
    return getSimpleVal('confirmCode', 'accounts', 'ID', $id);	
}
function getUserPassword($email)
{
	return getSimpleVal('password', 'accounts', 'email', $email);
	 
}
function getSimpleVal($retField, $tableName, $checkField, $checkVal)
{
	// Get access to the database
	$db = DBConnect();
	$checkVal = safe($checkVal);
	
	if (strtoupper($retField) == "COUNT(*)")
	{
		if ($checkField == "")
		{
			$sql = "SELECT $retField FROM `$tableName` WHERE 1";
		}
		else
		{
			$sql = "SELECT $retField FROM `$tableName` WHERE `$checkField` = $checkVal LIMIT 1";
		}
	}	
	else
	{
    	$sql = "SELECT `$retField` FROM `$tableName` WHERE `$checkField` = $checkVal LIMIT 1";
	}
	 	
    /* Run the query */
    $result = mysql_query($sql, $db);
	$retVal = 0;
	
	if($result)
	{
		// check if this exists
		if (mysql_num_rows($result) == 0)
		{
			$retVal = 0;
		}
		else
		{
			$row = mysql_fetch_row($result);
			$retVal = $row[0];
		}    	  	
	 }	
	return $retVal;
}

function isBanned($id)
{
	 $val = getSimpleVal('banned', 'accounts', 'ID', $id);

	 if ($val > 0)
	 {
	 	return true;
	 }
	 else
	 {
	 	return false;
	 }
}
function isConfirmed($id)
{
	 $val =  getSimpleVal('emailConfirmed', 'accounts', 'ID', $id);
	 if ($val == 1)
	 {
	 	return true;
	 }
	 else
	 {
	 	return false;
	 }
}
function getUserPasswordFromID($id)
{
	return getSimpleVal('password', 'accounts', 'ID', $id);
}
function getWinnerNameFromID($id)
{
   $firstName = getSimpleVal('firstName', 'winners', 'originalID', $id);
   $lastName = getSimpleVal('lastName', 'winners', 'originalID', $id);   
   
   return $firstName . " " . $lastName;
}
function getUserNameFromID($id)
{
   $firstName = getSimpleVal('firstName', 'accounts', 'ID', $id);
   $lastName = getSimpleVal('lastName', 'accounts', 'ID', $id);   
   
   return $firstName . " " . $lastName;
}
function getContestName($id)
{
   $name = getSimpleVal('details', 'contest', 'ID', $id); 
     
   return $name;
}
function getContestCountry($id)
{
   $name = getSimpleVal('country', 'contest', 'ID', $id); 
     
   return $name;
}
function getUserPasswordFromEmail($email)
{
    return getSimpleVal('password', 'accounts', 'email', $email);
}
function doesUserExist($userID)
{

	// checks if the userID is even in the database
	$retVal = getSimpleVal('count(*)', 'accounts', 'ID', $userID);
	if ($retVal > 0)
	{
		return true;
	}
	else
	{
		return false;
	}
}
function getUserIDFromEmail($email)
{
    return getSimpleVal('ID', 'accounts', 'email', $email);
}
function getUserEmailFromID($id)
{
    return getSimpleVal('email', 'accounts', 'ID', $id);
}
function getMemberID()
{
	$cookieVal = $_COOKIE['coverlogin'];

	$ret = 0;
	if ($cookieVal <> "")
	{
		$tempExp = explode(":", $cookieVal);
		$ret = $tempExp[0];
	}
	return $ret;
}

function protectPage($follow_url)
{

	$cookieVal = $_COOKIE['coverlogin'];

	// if the cookie exists we are good to go
	if ($cookieVal <> "")
	{
		$tempExp = explode(":", $cookieVal);
		$myID = $tempExp[0];
		$myPass = $tempExp[1];
		// check if ID is in database
		if (doesUserExist($myID) == false)
		{
			// log user out
			header("location: logout.php");
			exit;
		}
		
		$dbPass = md5(getUserPasswordFromID($myID));
		
		if ($myPass == $dbPass)
		{
			// passes match, so we are authenticated
			// are we banned?
			$isBanned = isBanned($myID);
			$isConfirmed = isConfirmed($myID);
			if ($isBanned == "yes")
			{
				// expired	
				 header("Location: banned.php");
				 exit;
			}
			else if ($isConfirmed == false)
			{
				// expired	
				 header("Location: unconfirmed.php");
				 exit;
			}
			else
			{
				return $myID;
			}
		}		
		else
		{
			$errorString = "You need to login to view that page. Please login below:";
			 header("Location: login-member.php?fflag=1&errorString=" . $errorString . "&follow_url=" . $follow_url . "&PHPSESSID=" . session_id());
			 exit;
		}
	}
	else
	{
		
			$errorString = "You need to login to view that page. Please login below:";
			 header("Location: login-member.php?fflag=1&errorString=" . $errorString . "&follow_url=" . $follow_url . "&PHPSESSID=" . session_id());
			 exit;
	}			
}
include_once("timeZone.inc.php");
?>
