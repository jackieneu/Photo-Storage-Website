<?php
ini_set('display_errors', 'On');
$mysql_handle = mysql_connect('oniddb.cws.oregonstate.edu','lambja-db','5tGW34Y2vYr1Gy5T')
	or die("Error  connecting to database server");
mysql_select_db('lambja-db', $mysql_handle) 
	or die("Error selecting database: $dbname");
	
$photo_id = array_key_exists("id", $_REQUEST) ? $_REQUEST["id"] : 0;
if ($photo_id <= 0)
	echo ""; 
else if (!preg_match('/^[0-9]+$/',$photo_id))
	echo "Invalid id";
else { 
	$rs = mysql_query("select image from photo where photo_id = ".$photo_id);
	header('Content-type: image/jpeg');
if (mysql_numrows($rs) == 1) 
	echo mysql_result($rs,0,"image");
}
mysql_close($mysql_handle);
?>