<?php
	session_start();
	include ("../DBFunctions.inc.php");
	include ("../navBar.inc.php");
	include ("../footer.inc.php");
	include ("../accounts.inc.php");
	include_once ("../contest.inc.php");
	include ("admin.inc.php");	
	
	$country = $_REQUEST["country"];
	$override = $_REQUEST["override"];
	$clear = $_REQUEST["clear"];
	if ($clear == 1)
	{
		unset($_SESSION['tmzon']);
		setcookie("cntover","",time() - 92000,'/');		
		header('location: overrideCountry.php?clear=0&override=0');
		exit;
	}
	if ($country <> "" && $override == 1)
	{
		// remove the previous cookie
		// set a cookie to override the country
		setcookie("cntover",$country,time()+9012412,'/');		
		header('location: overrideCountry.php?override=0');
		exit;
	}
	
	
	$ipNum = getIPNumber($_SERVER['REMOTE_ADDR']);		
	$ccReal = getCountryCode($ipNum, true);
	$cc = getCountryCode($ipNum);
	if ($_COOKIE["cntover"] <> "")
	{
		$cc = $_COOKIE["cntover"];
		
	}
	
	
	

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Administration</title>
<link href="../css/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<table width="100%" border="0" cellpadding="5" cellspacing="1">
  <tr>
    <td align="center" valign="top"><table width="800" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" valign="top"><? printAdminHeader(false);?></td>
      </tr>
	  <tr>
        <td align="left" valign="top"><table width="100%" cellpadding="7" cellspacing="1" border="0">
              
              <tr>
                <td width="100%" align="left" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
                  <tr>
                    <td><h2>Country Override </h2>
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
                    <td align="left" valign="top"><table width="100%" border="0" cellpadding="2" cellspacing="2">
                      <tr>
                        <td width="31%"><h2>Your Real Country is: <b>
                          <?=$ccReal?>
                          <?=getCountryFlag($ccReal);


						?>
                        </b></h2>
                          <? if ($cc <> $ccReal){?>                             <strong> <br />
                          <img src="../graphics/starRed.gif" alt="-" width="18" height="19" align="absmiddle" />                          OVERRIDE ENABLED</strong>: But, your country is currently being overridden and tracked as: <b>
                            <?= $cc?>
                            <?=getCountryFlag($cc);?>

						<?
						$con = mysql_connect("localhost","missi27_dbuser","ha87902ke");
						if (!$con)
						  {
						  die('Could not connect: ' . mysql_error());
						  }

						mysql_select_db("missi27_contestbase", $con);



						$qrytm = "SELECT `zone_name` FROM `zone1` WHERE `country_code` LIKE '$cc'";
						$restm = mysql_query($qrytm);
						if($restm)
						{
						$rowtm = mysql_fetch_array($restm);
						$zon = $rowtm['zone_name'];
						}

						date_default_timezone_set($zon);

						if (date_default_timezone_get()) {
							//echo 'date_default_timezone_set: ' . date_default_timezone_get() . '<br />';
						}

						if (ini_get('date.timezone')) {
							//echo 'date.timezone: ' . ini_get('date.timezone');
						}

						 
						 date_default_timezone_set($zon);
						 $curDate= date('F j, Y, g:i a'); 
						 $_SESSION['tmzon'] = $curDate;





						?>

                          </b>
                          [<a href="overrideCountry.php?clear=1">turn off override</a>]
                          <? } ?>
<b><br />
                          <br />
                          </b>
                          <hr size="1" noshade="noshade" color="#CC0000"/>
                          <b>                          You can override this by choosing a country here:<br />
                          </b>
                          <form id="form1" name="form1" method="post" action="">
                            <b><span class="row1"><? printCountrySelectBox($cc);?></span></b>
                            <input type="submit" name="submit" id="submit" value="Override" />
                            <input name="override" type="hidden" id="override" value="1" />
                          </form>
                          <b><br /> 
                          <br />
                          <br />
                          <br />
                          <br />
                          </b></td>
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
