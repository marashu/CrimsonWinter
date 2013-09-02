#!/usr/local/bin/php -q
<?php
require_once 'db_vars.php';
require_once 'functions.php';
require_once 'cw_vars.php';

$db_server = mysql_connect(DB_HOST, DB_USER, DB_PW);

if(!$db_server) die("Unable to connect to MySQL: " . mysql_error());



mysql_select_db(DB_NAME) or die("Unable to select database: " . mysql_error());

$query = "SELECT achievement21 FROM CW_Achievements ORDER BY id";
	$result2 = mysql_query($query);
	$row2 = mysql_num_rows($result2);
for($i = 0; $i < $row2; $i++)
{
$rows = mysql_fetch_row($result2);
$Achieve[] = $rows[0];
}

$query = "SELECT id, faction, played FROM CW_Users ORDER BY id";
$result = mysql_query($query);
$rows = mysql_num_rows($result);

$supporters[SAPPHIROS] = 0;
$supporters[RUBIA] = 0;
$supporters[JEDEITE] = 0;
$supporters[DAMOS] = 0;
$supporters[MACHALITE] = 0;

for($i = 0; $i < $rows; $i++)
{
	$row = mysql_fetch_row($result);
	$id = $row[0];
	$faction = $row[1];
	$played = $row[2];
	
	$supporters[$faction]++;
	
	
	
	if($Achieve[$i] >= 0 && $played == 1)
	{
		
		$query = "UPDATE CW_Achievements SET achievement21=achievement21 + 1 WHERE id='" . $id . "'";
		$result2 = mysql_query($query);
	}
	else if($Achieve[$i] > 0 && $played == 0)
	{
		$query = "UPDATE CW_Achievements SET achievement21='0' WHERE id='" . $id . "'";
		$result2 = mysql_query($query);
	}
}

$query = "UPDATE CW_Users SET played=0, boostEnergy=0, boostStamina=0, boostHealth=0";
$result = mysql_query($query);


?>
