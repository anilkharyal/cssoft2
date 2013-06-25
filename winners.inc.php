<?php
function getThumbnailWinner($userID, $contestID)
{
	$db = DBConnect();
	$sql="SELECT thumbURL FROM `pictures` p WHERE p.`ownerID`=" . safe($userID) . " AND p.`contestID`=" . safe($contestID) . " AND p.`tableName`='contest' GROUP BY p.ownerID LIMIT 1";
	$result = mysql_query($sql, $db);
	if ($result)
	{	
		$row = mysql_fetch_array($result);
		$retURL = 	PICTURES_UPLOAD_DIR . $row["thumbURL"];
	}
	return $retURL;

}
function getLastWinner()
{
	$contestID = getLastContest();
	return getSimpleVal('originalID','winners','contestID',$contestID);
	 
}
function printLastWinners($link="viewGalleryArchive.php")
{
	$db = DBConnect();
	$contestID = getLastContest();
	$sql = "SELECT originalID, concat(firstName, ' ', lastName) as name from winners WHERE contestID=" . safe($contestID);
	$result = mysql_query($sql, $db);
	if ($result)
	{	
		$contestInfo = getContestInfo($contestID);
		if (mysql_num_rows($result) > 0)
		{
	



	
		}		
		
		while ($row = mysql_fetch_array($result))
			{
				$userID = $row["originalID"];
				$thumbURL = getThumbnailWinner($userID, $contestID);
				$name = $row["name"];
			?><table width="100%" border="0" cellspacing="1" cellpadding="2">
  <tr>
    <td width="12%" align="left" valign="middle"><a href="<?=$link?>?uID=<?=$userID?>"><img src="<?=$thumbURL?>" alt="winner" width="100" height="67" border="0" class="picBorder" /></a></td>
    <td width="88%"><table width="100%" border="0" cellspacing="1" cellpadding="1">
      <tr>
        <td colspan="2"><span class="style1">Previous Winner</span> </td>
      </tr>
      <tr>
        <td width="11%" align="left" valign="top"><strong>Winner:</strong></td>
        <td width="89%" align="left" valign="top"><?=$name?></td>
      </tr>
      <tr>
        <td align="left" valign="top"><strong>Prize:</strong></td>
        <td align="left" valign="top"><?=$contestInfo["prize"];?></td>
      </tr>
    </table></td>
  </tr>
</table><?
		}
	}
}
?>
