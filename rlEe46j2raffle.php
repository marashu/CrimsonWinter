#!/usr/local/bin/php -q
<?php
require_once 'db_vars.php';
require_once 'functions.php';
require_once 'cw_vars.php';
//use CRON to handle the weekly raffle
$db_server = mysql_connect(DB_HOST, DB_USER, DB_PW);

if(!$db_server) die("Unable to connect to MySQL: " . mysql_error());



mysql_select_db(DB_NAME) or die("Unable to select database: " . mysql_error());

$query = "SELECT * FROM CW_Raffle";
$result = mysql_query($query);

$rows = mysql_num_rows($result);

if($rows > 0)
{
	$rand = rand(0,--$rows);
	$id = mysql_result($result, $rand, 'id');
//STANDARD DRAW
/*
$query = "UPDATE CW_Users SET gems=gems+40 WHERE id=$id";
$result = mysql_query($query);
*/

/*
//ATTACK SET
$query = "UPDATE CW_Users SET skills=skills + 5 WHERE id=$id";
$result = mysql_query($query);

$query = "SELECT power5, power75, power78 FROM CW_Inventory WHERE id=$id";
	$result = mysql_query($query);
	$row = mysql_fetch_row($result);
	$power5 = $row[0];
$power75 = $row[1];
$power78 = $row[2];
if($power5 <= 0)
	{
	
		$query = "SELECT achievement16 FROM CW_Achievements WHERE id=$id";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		$Repertoire = $row[0];
		$query = "SELECT powerTotal FROM CW_Users WHERE id=$id";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		$powerTotal = $row[0];
		
		if($Repertoire >= 0 && ++$powerTotal >= 50)
		{
			$query = "UPDATE CW_Users SET skills=skills + 1 WHERE id=$id";
			$result = mysql_query($query);
			$query = "$UPDATE CW_Achievements SET achievement16=-1 WHERE id=$id";
			$result = mysql_query($query);
		}
		$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id=$id";
		$result = mysql_query($query);
	
	}
if($power75 <= 0)
	{
	
		$query = "SELECT achievement16 FROM CW_Achievements WHERE id=$id";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		$Repertoire = $row[0];
		$query = "SELECT powerTotal FROM CW_Users WHERE id=$id";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		$powerTotal = $row[0];
		
		if($Repertoire >= 0 && ++$powerTotal >= 50)
		{
			$query = "UPDATE CW_Users SET skills=skills + 1 WHERE id=$id";
			$result = mysql_query($query);
			$query = "$UPDATE CW_Achievements SET achievement16=-1 WHERE id=$id";
			$result = mysql_query($query);
		}
		$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id=$id";
		$result = mysql_query($query);
	
	}
if($power78 <= 0)
	{
	
		$query = "SELECT achievement16 FROM CW_Achievements WHERE id=$id";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		$Repertoire = $row[0];
		$query = "SELECT powerTotal FROM CW_Users WHERE id=$id";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		$powerTotal = $row[0];
		
		if($Repertoire >= 0 && ++$powerTotal >= 50)
		{
			$query = "UPDATE CW_Users SET skills=skills + 1 WHERE id=$id";
			$result = mysql_query($query);
			$query = "$UPDATE CW_Achievements SET achievement16=-1 WHERE id=$id";
			$result = mysql_query($query);
		}
		$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id=$id";
		$result = mysql_query($query);
	
	}
$query = "UPDATE CW_Inventory SET power5=power5 + 1, power75=power75 + 1, power78=power78 + 1 WHERE id=$id";
	$result = mysql_query($query);
*/
//UTILITY SET
/*
$query = "SELECT power53, power57, power77, power90, power96 FROM CW_Inventory WHERE id=$id";
	$result = mysql_query($query);
	$row = mysql_fetch_row($result);
	$power53 = $row[0];
$power57 = $row[1];
$power77 = $row[2];
$power90 = $row[3];
$power96 = $row[4];

if($power53 <= 0)
	{
	
		$query = "SELECT achievement16 FROM CW_Achievements WHERE id=$id";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		$Repertoire = $row[0];
		$query = "SELECT powerTotal FROM CW_Users WHERE id=$id";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		$powerTotal = $row[0];
		
		if($Repertoire >= 0 && ++$powerTotal >= 50)
		{
			$query = "UPDATE CW_Users SET skills=skills + 1 WHERE id=$id";
			$result = mysql_query($query);
			$query = "$UPDATE CW_Achievements SET achievement16=-1 WHERE id=$id";
			$result = mysql_query($query);
		}
		$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id=$id";
		$result = mysql_query($query);
	
	}
if($power57 <= 0)
	{
	
		$query = "SELECT achievement16 FROM CW_Achievements WHERE id=$id";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		$Repertoire = $row[0];
		$query = "SELECT powerTotal FROM CW_Users WHERE id=$id";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		$powerTotal = $row[0];
		
		if($Repertoire >= 0 && ++$powerTotal >= 50)
		{
			$query = "UPDATE CW_Users SET skills=skills + 1 WHERE id=$id";
			$result = mysql_query($query);
			$query = "$UPDATE CW_Achievements SET achievement16=-1 WHERE id=$id";
			$result = mysql_query($query);
		}
		$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id=$id";
		$result = mysql_query($query);
	
	}
if($power77 <= 0)
	{
	
		$query = "SELECT achievement16 FROM CW_Achievements WHERE id=$id";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		$Repertoire = $row[0];
		$query = "SELECT powerTotal FROM CW_Users WHERE id=$id";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		$powerTotal = $row[0];
		
		if($Repertoire >= 0 && ++$powerTotal >= 50)
		{
			$query = "UPDATE CW_Users SET skills=skills + 1 WHERE id=$id";
			$result = mysql_query($query);
			$query = "$UPDATE CW_Achievements SET achievement16=-1 WHERE id=$id";
			$result = mysql_query($query);
		}
		$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id=$id";
		$result = mysql_query($query);
	
	}
if($power90 <= 0)
	{
	
		$query = "SELECT achievement16 FROM CW_Achievements WHERE id=$id";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		$Repertoire = $row[0];
		$query = "SELECT powerTotal FROM CW_Users WHERE id=$id";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		$powerTotal = $row[0];
		
		if($Repertoire >= 0 && ++$powerTotal >= 50)
		{
			$query = "UPDATE CW_Users SET skills=skills + 1 WHERE id=$id";
			$result = mysql_query($query);
			$query = "$UPDATE CW_Achievements SET achievement16=-1 WHERE id=$id";
			$result = mysql_query($query);
		}
		$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id=$id";
		$result = mysql_query($query);
	
	}
if($power96 <= 0)
	{
	
		$query = "SELECT achievement16 FROM CW_Achievements WHERE id=$id";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		$Repertoire = $row[0];
		$query = "SELECT powerTotal FROM CW_Users WHERE id=$id";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		$powerTotal = $row[0];
		
		if($Repertoire >= 0 && ++$powerTotal >= 50)
		{
			$query = "UPDATE CW_Users SET skills=skills + 1 WHERE id=$id";
			$result = mysql_query($query);
			$query = "$UPDATE CW_Achievements SET achievement16=-1 WHERE id=$id";
			$result = mysql_query($query);
		}
		$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id=$id";
		$result = mysql_query($query);
	
	}
$query = "UPDATE CW_Inventory SET power53=power53 + 1, power57=power57 + 1, power77=power77 + 1, power90=power90 + 1, power96=power96 + 1 WHERE id=$id";
	$result = mysql_query($query);
	*/
//Miracle Boost

/*
$query = "UPDATE CW_Users SET c_energy=m_energy * 5, c_health=m_health * 5, c_stamina=m_stamina * 5 WHERE id=$id";
$result = mysql_query($query);
*/
//MACHALITE SET
$query = "SELECT power53, power91, power90 FROM CW_Inventory WHERE id=$id";
	$result = mysql_query($query);
	$row = mysql_fetch_row($result);
	$power53 = $row[0];
$power57 = $row[1];
$power90 = $row[3];


if($power53 <= 0)
	{
	
		$query = "SELECT achievement16 FROM CW_Achievements WHERE id=$id";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		$Repertoire = $row[0];
		$query = "SELECT powerTotal FROM CW_Users WHERE id=$id";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		$powerTotal = $row[0];
		
		if($Repertoire >= 0 && ++$powerTotal >= 50)
		{
			$query = "UPDATE CW_Users SET skills=skills + 1 WHERE id=$id";
			$result = mysql_query($query);
			$query = "$UPDATE CW_Achievements SET achievement16=-1 WHERE id=$id";
			$result = mysql_query($query);
		}
		$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id=$id";
		$result = mysql_query($query);
	
	}
if($power57 <= 0)
	{
	
		$query = "SELECT achievement16 FROM CW_Achievements WHERE id=$id";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		$Repertoire = $row[0];
		$query = "SELECT powerTotal FROM CW_Users WHERE id=$id";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		$powerTotal = $row[0];
		
		if($Repertoire >= 0 && ++$powerTotal >= 50)
		{
			$query = "UPDATE CW_Users SET skills=skills + 1 WHERE id=$id";
			$result = mysql_query($query);
			$query = "$UPDATE CW_Achievements SET achievement16=-1 WHERE id=$id";
			$result = mysql_query($query);
		}
		$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id=$id";
		$result = mysql_query($query);
	
	}

if($power90 <= 0)
	{
	
		$query = "SELECT achievement16 FROM CW_Achievements WHERE id=$id";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		$Repertoire = $row[0];
		$query = "SELECT powerTotal FROM CW_Users WHERE id=$id";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		$powerTotal = $row[0];
		
		if($Repertoire >= 0 && ++$powerTotal >= 50)
		{
			$query = "UPDATE CW_Users SET skills=skills + 1 WHERE id=$id";
			$result = mysql_query($query);
			$query = "$UPDATE CW_Achievements SET achievement16=-1 WHERE id=$id";
			$result = mysql_query($query);
		}
		$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id=$id";
		$result = mysql_query($query);
	
	}
$query = "UPDATE CW_Inventory SET power53=power53 + 1, power90=power90 + 1, power91=power91 + 1 WHERE id=$id";
	$result = mysql_query($query);
/* PERSONAL ARMY
	$query = "UPDATE CW_Users SET knights=knights + 10, skills=skills + 10 WHERE id='" . $id . "'";
	$result = mysql_query($query);
	
	$query = "SELECT power57 FROM CW_Inventory WHERE id=$id";
	$result = mysql_query($query);
	$row = mysql_fetch_row($result);
	$power57 = $row[0];
	if($power57 <= 0)
	{
	
		$query = "SELECT achievement16 FROM CW_Achievements WHERE id=$id";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		$Repertoire = $row[0];
		$query = "SELECT powerTotal FROM CW_Users WHERE id=$id";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		$powerTotal = $row[0];
		
		if($Repertoire >= 0 && ++$powerTotal >= 50)
		{
			$query = "UPDATE CW_Users SET skills=skills + 1 WHERE id=$id";
			$result = mysql_query($query);
			$query = "$UPDATE CW_Achievements SET achievement16=-1 WHERE id=$id";
			$result = mysql_query($query);
		}
		$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id=$id";
		$result = mysql_query($query);
	
	}
	$query = "UPDATE CW_Inventory SET power57=power57 + 1 WHERE id=$id";
	$result = mysql_query($query);
	*/
	$query = "SELECT * FROM CW_Notices WHERE id='" . $id . "'";
        $result = mysql_query($query);
	$rows = mysql_fetch_row($result);
	$message[0] = $rows[1];
	$message[1] = $rows[2];
	$message[2] = $rows[3];
	$message[3] = $rows[4];
	$message[4] = $rows[5];
	
	$messageTime[0] = $rows[6];
	$messageTime[1] = $rows[7];
	$messageTime[2] = $rows[8];
	$messageTime[3] = $rows[9];
	$messageTime[4] = $rows[10];
	
		
	
		
	for ($j = 4; $j > 0; $j--)
	{
		$message[$j] = $message[($j - 1)];
		$messageTime[$j] = $messageTime[($j - 1)];
	}
	
	$newMessage = "Congratulations!  You won the Taiyou Raffle!  Enjoy your Machalite Set.";
	$newtime = time();
	$newtime = date("Y-m-d H:i:s", $newtime);
	
	$message[0] = $newMessage;
	$messageTime[0] = $newtime;
	$query = "UPDATE CW_Notices SET message1='" . $message[0] . "', message2='" . $message[1] . "', message3='" . $message[2] . "', message4='" . $message[3] . "', message5='" . $message[4] . "', messageTime1='" . $messageTime[0] . "', messageTime2='" . $messageTime[1] . "', messageTime3='" . $messageTime[2] . "', messageTime4='" . $messageTime[3] . "', messageTime5='" . $messageTime[4] . "' WHERE id='" . $id . "'";
	$result = mysql_query($query);
	
	$query = "UPDATE CW_WarStats SET raffle='" . $id . "'";
	$result = mysql_query($query);
}

$query = "DELETE FROM CW_Raffle";
$result = mysql_query($query);

?>
