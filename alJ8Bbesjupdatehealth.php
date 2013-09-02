#!/usr/local/bin/php -q
<?php
//use CRON to update health every minute, for pvp reasons
require_once 'db_vars.php';
require_once 'functions.php';
require_once 'cw_vars.php';

$db_server = mysql_connect(DB_HOST, DB_USER, DB_PW);

if(!$db_server) die("Unable to connect to MySQL: " . mysql_error());

mysql_select_db(DB_NAME) or die("Unable to select database: " . mysql_error());

$query = "SELECT c_health, m_health, hTime, faction, id FROM CW_Users";
$result = mysql_query($query);
if(!$result) die("Database access failed: " . mysql_error());

$rows = mysql_num_rows($result);

for($i = 0; $i < $rows; $i++)
{
	$row = mysql_fetch_row($result);
	$c_health = $row[0];
	$m_health = $row[1];
	$hTime = $row[2];
	$faction = $row[3];
	$uid = $row[4];
	if($c_health < $m_health)
	{
		do
		{
		
		$newtime = time();
		$query = "SELECT UNIX_TIMESTAMP('" . $hTime. "') as hDiff";//for real game, add WHERE to narrow it down to fb uid
		$results = mysql_query($query);
		$results = mysql_fetch_assoc($results);
		
		$results['hDiff'] -= $newtime;
	
		
		
		
		
		if($results['hDiff'] < 0)
		{
			
			
			$c_health++;
			if($faction == JEDEITE)
				$query = "UPDATE CW_Users SET hTime='" . $hTime. "' + INTERVAL 3 MINUTE, c_health='" . $c_health . "' WHERE id='" . $uid . "'";
			else
				$query = "UPDATE CW_Users SET hTime='" . $hTime. "' + INTERVAL 4 MINUTE, c_health='" . $c_health . "' WHERE id='" . $uid . "'";
			$subresult = mysql_query($query);
			$query = "SELECT hTime FROM CW_Users as hTime WHERE id='" . $uid . "'";
			$subresult = mysql_query($query);
			$subresult = mysql_fetch_assoc($subresult);
			$hTime= $subresult['hTime'];
			
		}
		if($c_health >= $m_health)
			$results['hDiff'] = 1;
		} while ($results['hDiff'] < 0 );
		
	}
}
?>
