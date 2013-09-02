#!/usr/local/bin/php -q
<?php
require_once 'db_vars.php';
require_once 'functions.php';
require_once 'cw_vars.php';
//Use CRON to run the daily war
$db_server = mysql_connect(DB_HOST, DB_USER, DB_PW);

if(!$db_server) die("Unable to connect to MySQL: " . mysql_error());



mysql_select_db(DB_NAME) or die("Unable to select database: " . mysql_error());

$query = "SELECT id, achievement5 FROM CW_Achievements WHERE achievement5>=0";
$result = mysql_query($query);

$rows = mysql_num_rows($result);

for($j = 0; $j < $rows; $j++)
{
$row = mysql_fetch_row($result);
$users[] = $row[0];
$Dedicated[] = $row[1];
}
$query = "SELECT DISTINCT id FROM CW_Warriors";
$result = mysql_query($query);

$rows = mysql_num_rows($result);

for($j = 0; $j < $rows; $j++)
{
$row = mysql_fetch_row($result);
$distinct[] = $row[0];
}

for($i = 0; $i < count($users); $i++)
{
	$reset = 1;
	$AchDedi = 0;
	for($j = 0; $j < count($distinct); $j++)
	{
		if($users[$i] == $distinct[$j])
		{
			$reset = 0;
			if(++$Dedicated[$i] == 7)
			{
				$Dedicated[$i] = -1;
				$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $users[$i] . "'";
				$result = mysql_query($query);
				$AchDedi = 1;
			}
			$query = "UPDATE CW_Achievements SET achievement5='" . $Dedicated[$i] . "' WHERE id='" . $users[$i] . "'";
			$result = mysql_query($query);
			if($AchDedi == 1)
			{
				$query = "SELECT * FROM CW_Notices WHERE id='" . $users[$i] . "'";
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
				
				$newtime = time();
				$newtime = date("Y-m-d H:i:s", $newtime);
				for ($a = 4; $a > 0; $a--)
				{
					$message[$a] = $message[($a - 1)];
					$messageTime[$a] = $messageTime[($a - 1)];
				}
				$message[0] = "You earned the Dedicated achievement by participating in faction wars for 7 days!  You have been awarded 1 skill point.";
				$messageTime[0] = $newtime;
			
				$query = "UPDATE CW_Notices SET message1='" . $message[0] . "', message2='" . $message[1] . "', message3='" . $message[2] . "', message4='" . $message[3] . "', message5='" . $message[4] . "', messageTime1='" . $messageTime[0] . "', messageTime2='" . $messageTime[1] . "', messageTime3='" . $messageTime[2] . "', messageTime4='" . $messageTime[3] . "', messageTime5='" . $messageTime[4] . "' WHERE id='" . $users[$i] . "'";
				$result = mysql_query($query);
			}
			break;
		}
	}
	if($reset == 1)
	{
		$query = "UPDATE CW_Achievements SET achievement5=0 WHERE id='" . $users[$i] . "'";
		$result = mysql_query($query);
	}
}

for($i = 1; $i <= 5; $i++)
{
$strength[$i] = 0;
$query = "SELECT strength FROM CW_Warriors WHERE god='" . $i . "'";
$result = mysql_query($query);

$rows = mysql_num_rows($result);

for($j = 0; $j < $rows; $j++)
{
$row = mysql_fetch_row($result);
$strength[$i] += $row[0];
}

$query = "SELECT DISTINCT id FROM CW_Warriors WHERE god='" . $i . "'";
$result = mysql_query($query);

$rows = mysql_num_rows($result);

for($j = 0; $j < $rows; $j++)
{
$row = mysql_fetch_row($result);
$chosen[$i][] = $row[0];
}
}

for($i = 1; $i <6; $i++)
{
	$query = "UPDATE CW_WarStats SET support" . $i . "='" . $strength[$i] . "'";
	$result = mysql_query($query);
}
$query = "DELETE FROM CW_Warriors";
$result = mysql_query($query);

$query = "SELECT day FROM CW_WarStats";
$result = mysql_query($query);

$row = mysql_fetch_row($result);
$day = $row[0];

$win1 = 0;
$lose1 = 0;
$win2 = 0;
$lose2 = 0;
$lose3 = 0;

switch($day)
{
	case 0:
		//check the first two gem gods first
		if($strength[SAPPHIROS] > $strength[RUBIA])
		{
			$win1 = SAPPHIROS;
			$lose1 = RUBIA;
		}
		else if($strength[SAPPHIROS] < $strength[RUBIA])
		{
			$lose1 = SAPPHIROS;
			$win1 = RUBIA;
		}
		else//unlikely event of a tie
		{
			if(rand(0,1) == 0)
			{
				$win1 = SAPPHIROS;
				$lose1 = RUBIA;
			}
			else
			{
				$win1 = RUBIA;
				$lose1 = SAPPHIROS;
			}
		}
		
		//now check for the remaining 3
		if($strength[JEDEITE] > $strength[DAMOS])
		{
			if($strength[JEDEITE] > $strength[MACHALITE])
			{
				$win2 = JEDEITE;
				$lose2 = DAMOS;
				$lose3 = MACHALITE;
			}
			else if($strength[JEDEITE] < $strength[MACHALITE])
			{
				$win2 = MACHALITE;
				$lose2 = DAMOS;
				$lose3 = JEDEITE;
			}
			else
			{
				if(rand(0,1) == 0)
				{
					$win2 = JEDEITE;
					$lose2 = MACHALITE;
				}
				else
				{
					$lose2 = JEDEITE;
					$win2 = MACHALITE;
				}
				$lose3 = DAMOS;
			}
		}
		else if($strength[JEDEITE] < $strength[DAMOS])
		{
			if($strength[DAMOS] > $strength[MACHALITE])
			{
				$lose2 = JEDEITE;
				$win2 = DAMOS;
				$lose3 = MACHALITE;
			}
			else if($strength[DAMOS] < $strength[MACHALITE])
			{
				$win2 = MACHALITE;
				$lose2 = DAMOS;
				$lose3 = JEDEITE;
			}
			else
			{
				if(rand(0,1) == 0)
				{
					$win2 = DAMOS;
					$lose2 = MACHALITE;
				}
				else
				{
					$lose2 = DAMOS;
					$win2 = MACHALITE;
				}
				$lose3 = JEDEITE;
			}
		}
		else
		{
			if($strength[JEDEITE] > $strength[MACHALITE])
			{
				if(rand(0,1) == 0)
				{
					$lose2 = JEDEITE;
					$win2 = DAMOS;
				}
				else
				{
					$win2 = JEDEITE;
					$lose2 = DAMOS;
				}
				$lose3 = MACHALITE;
			}
			else if($strength[JEDEITE] < $strength[MACHALITE])
			{
				$win2 = MACHALITE;
				$lose2 = DAMOS;
				$lose3 = JEDEITE;
			}
			else
			{
				$rand = rand(0,2);
				if($rand == 0)
				{
					$win2 = JEDEITE;
					$lose2 = MACHALITE;
					$lose3 = DAMOS;
				}
				else if($rand == 1)
				{
					$lose2 = JEDEITE;
					$win2 = MACHALITE;
					$lose3 = DAMOS;
				}
				else
				{
					$win2 = DAMOS;
					$lose2 = MACHALITE;
					$lose3 = JEDEITE;
				}
				
			}
		}
	break;
	case 1:
	//check the first two gem gods first
		if($strength[JEDEITE] > $strength[DAMOS])
		{
			$win1 = JEDEITE;
			$lose1 = DAMOS;
		}
		else if($strength[JEDEITE] < $strength[DAMOS])
		{
			$lose1 = JEDEITE;
			$win1 = DAMOS;
		}
		else//unlikely event of a tie
		{
			if(rand(0,1) == 0)
			{
				$win1 = JEDEITE;
				$lose1 = DAMOS;
			}
			else
			{
				$win1 = DAMOS;
				$lose1 = JEDEITE;
			}
		}
		
		//now check for the remaining 3
		if($strength[SAPPHIROS] > $strength[RUBIA])
		{
			if($strength[SAPPHIROS] > $strength[MACHALITE])
			{
				$win2 = SAPPHIROS;
				$lose2 = RUBIA;
				$lose3 = MACHALITE;
			}
			else if($strength[SAPPHIROS] < $strength[MACHALITE])
			{
				$win2 = MACHALITE;
				$lose2 = RUBIA;
				$lose3 = SAPPHIROS;
			}
			else
			{
				if(rand(0,1) == 0)
				{
					$win2 = SAPPHIROS;
					$lose2 = MACHALITE;
				}
				else
				{
					$lose2 = SAPPHIROS;
					$win2 = MACHALITE;
				}
				$lose3 = RUBIA;
			}
		}
		else if($strength[SAPPHIROS] < $strength[RUBIA])
		{
			if($strength[RUBIA] > $strength[MACHALITE])
			{
				$lose2 = SAPPHIROS;
				$win2 = RUBIA;
				$lose3 = MACHALITE;
			}
			else if($strength[RUBIA] < $strength[MACHALITE])
			{
				$win2 = MACHALITE;
				$lose2 = RUBIA;
				$lose3 = SAPPHIROS;
			}
			else
			{
				if(rand(0,1) == 0)
				{
					$win2 = RUBIA;
					$lose2 = MACHALITE;
				}
				else
				{
					$lose2 = RUBIA;
					$win2 = MACHALITE;
				}
				$lose3 = SAPPHIROS;
			}
		}
		else
		{
			if($strength[SAPPHIROS] > $strength[MACHALITE])
			{
				if(rand(0,1) == 0)
				{
					$lose2 = SAPPHIROS;
					$win2 = RUBIA;
				}
				else
				{
					$win2 = SAPPHIROS;
					$lose2 = RUBIA;
				}
				$lose3 = MACHALITE;
			}
			else if($strength[SAPPHIROS] < $strength[MACHALITE])
			{
				$win2 = MACHALITE;
				$lose2 = RUBIA;
				$lose3 = SAPPHIROS;
			}
			else
			{
				$rand = rand(0,2);
				if($rand == 0)
				{
					$win2 = SAPPHIROS;
					$lose2 = MACHALITE;
					$lose3 = RUBIA;
				}
				else if($rand == 1)
				{
					$lose2 = SAPPHIROS;
					$win2 = MACHALITE;
					$lose3 = RUBIA;
				}
				else
				{
					$win2 = RUBIA;
					$lose2 = MACHALITE;
					$lose3 = SAPPHIROS;
				}
				
			}
		}
	
	break;
	case 2:
		//check the first two gem gods first
		if($strength[SAPPHIROS] > $strength[MACHALITE])
		{
			$win1 = SAPPHIROS;
			$lose1 = MACHALITE;
		}
		else if($strength[SAPPHIROS] < $strength[MACHALITE])
		{
			$lose1 = SAPPHIROS;
			$win1 = MACHALITE;
		}
		else//unlikely event of a tie
		{
			if(rand(0,1) == 0)
			{
				$win1 = SAPPHIROS;
				$lose1 = MACHALITE;
			}
			else
			{
				$win1 = MACHALITE;
				$lose1 = SAPPHIROS;
			}
		}
		
		//now check for the remaining 3
		if($strength[JEDEITE] > $strength[DAMOS])
		{
			if($strength[JEDEITE] > $strength[RUBIA])
			{
				$win2 = JEDEITE;
				$lose2 = DAMOS;
				$lose3 = RUBIA;
			}
			else if($strength[JEDEITE] < $strength[RUBIA])
			{
				$win2 = RUBIA;
				$lose2 = DAMOS;
				$lose3 = JEDEITE;
			}
			else
			{
				if(rand(0,1) == 0)
				{
					$win2 = JEDEITE;
					$lose2 = RUBIA;
				}
				else
				{
					$lose2 = JEDEITE;
					$win2 = RUBIA;
				}
				$lose3 = DAMOS;
			}
		}
		else if($strength[JEDEITE] < $strength[DAMOS])
		{
			if($strength[DAMOS] > $strength[RUBIA])
			{
				$lose2 = JEDEITE;
				$win2 = DAMOS;
				$lose3 = RUBIA;
			}
			else if($strength[DAMOS] < $strength[RUBIA])
			{
				$win2 =RUBIA;
				$lose2 = DAMOS;
				$lose3 = JEDEITE;
			}
			else
			{
				if(rand(0,1) == 0)
				{
					$win2 = DAMOS;
					$lose2 = RUBIA;
				}
				else
				{
					$lose2 = DAMOS;
					$win2 = RUBIA;
				}
				$lose3 = JEDEITE;
			}
		}
		else
		{
			if($strength[JEDEITE] > $strength[RUBIA])
			{
				if(rand(0,1) == 0)
				{
					$lose2 = JEDEITE;
					$win2 = DAMOS;
				}
				else
				{
					$win2 = JEDEITE;
					$lose2 = DAMOS;
				}
				$lose3 = RUBIA;
			}
			else if($strength[JEDEITE] < $strength[RUBIA])
			{
				$win2 = RUBIA;
				$lose2 = DAMOS;
				$lose3 = JEDEITE;
			}
			else
			{
				$rand = rand(0,2);
				if($rand == 0)
				{
					$win2 = JEDEITE;
					$lose2 = RUBIA;
					$lose3 = DAMOS;
				}
				else if($rand == 1)
				{
					$lose2 = JEDEITE;
					$win2 = RUBIA;
					$lose3 = DAMOS;
				}
				else
				{
					$win2 = DAMOS;
					$lose2 = RUBIA;
					$lose3 = JEDEITE;
				}
				
			}
		}
	break;
	case 3:
		//check the first two gem gods first
		if($strength[JEDEITE] > $strength[RUBIA])
		{
			$win1 = JEDEITE;
			$lose1 = RUBIA;
		}
		else if($strength[JEDEITE] < $strength[RUBIA])
		{
			$lose1 = JEDEITE;
			$win1 = RUBIA;
		}
		else//unlikely event of a tie
		{
			if(rand(0,1) == 0)
			{
				$win1 = JEDEITE;
				$lose1 = RUBIA;
			}
			else
			{
				$win1 = RUBIA;
				$lose1 = JEDEITE;
			}
		}
		
		//now check for the remaining 3
		if($strength[SAPPHIROS] > $strength[DAMOS])
		{
			if($strength[SAPPHIROS] > $strength[MACHALITE])
			{
				$win2 = SAPPHIROS;
				$lose2 = DAMOS;
				$lose3 = MACHALITE;
			}
			else if($strength[SAPPHIROS] < $strength[MACHALITE])
			{
				$win2 = MACHALITE;
				$lose2 = DAMOS;
				$lose3 = SAPPHIROS;
			}
			else
			{
				if(rand(0,1) == 0)
				{
					$win2 = SAPPHIROS;
					$lose2 = MACHALITE;
				}
				else
				{
					$lose2 = SAPPHIROS;
					$win2 = MACHALITE;
				}
				$lose3 = DAMOS;
			}
		}
		else if($strength[SAPPHIROS] < $strength[DAMOS])
		{
			if($strength[DAMOS] > $strength[MACHALITE])
			{
				$lose2 = SAPPHIROS;
				$win2 = DAMOS;
				$lose3 = MACHALITE;
			}
			else if($strength[DAMOS] < $strength[MACHALITE])
			{
				$win2 = MACHALITE;
				$lose2 = DAMOS;
				$lose3 = SAPPHIROS;
			}
			else
			{
				if(rand(0,1) == 0)
				{
					$win2 = DAMOS;
					$lose2 = MACHALITE;
				}
				else
				{
					$lose2 = DAMOS;
					$win2 = MACHALITE;
				}
				$lose3 = SAPPHIROS;
			}
		}
		else
		{
			if($strength[SAPPHIROS] > $strength[MACHALITE])
			{
				if(rand(0,1) == 0)
				{
					$lose2 = SAPPHIROS;
					$win2 = DAMOS;
				}
				else
				{
					$win2 = SAPPHIROS;
					$lose2 = DAMOS;
				}
				$lose3 = MACHALITE;
			}
			else if($strength[SAPPHIROS] < $strength[MACHALITE])
			{
				$win2 = MACHALITE;
				$lose2 = DAMOS;
				$lose3 = SAPPHIROS;
			}
			else
			{
				$rand = rand(0,2);
				if($rand == 0)
				{
					$win2 = SAPPHIROS;
					$lose2 = MACHALITE;
					$lose3 = DAMOS;
				}
				else if($rand == 1)
				{
					$lose2 = SAPPHIROS;
					$win2 = MACHALITE;
					$lose3 = DAMOS;
				}
				else
				{
					$win2 = DAMOS;
					$lose2 = MACHALITE;
					$lose3 = SAPPHIROS;
				}
				
			}
		}
	break;
	case 4:
		//check the first two gem gods first
		if($strength[MACHALITE] > $strength[DAMOS])
		{
			$win1 = MACHALITE;
			$lose1 = DAMOS;
		}
		else if($strength[MACHALITE] < $strength[DAMOS])
		{
			$lose1 = MACHALITE;
			$win1 = DAMOS;
		}
		else//unlikely event of a tie
		{
			if(rand(0,1) == 0)
			{
				$win1 = MACHALITE;
				$lose1 = DAMOS;
			}
			else
			{
				$win1 = DAMOS;
				$lose1 = MACHALITE;
			}
		}
		
		//now check for the remaining 3
		if($strength[SAPPHIROS] > $strength[RUBIA])
		{
			if($strength[SAPPHIROS] > $strength[JEDEITE])
			{
				$win2 = SAPPHIROS;
				$lose2 = RUBIA;
				$lose3 = JEDEITE;
			}
			else if($strength[SAPPHIROS] < $strength[JEDEITE])
			{
				$win2 = JEDEITE;
				$lose2 = RUBIA;
				$lose3 = SAPPHIROS;
			}
			else
			{
				if(rand(0,1) == 0)
				{
					$win2 = SAPPHIROS;
					$lose2 = JEDEITE;
				}
				else
				{
					$lose2 = SAPPHIROS;
					$win2 = JEDEITE;
				}
				$lose3 = RUBIA;
			}
		}
		else if($strength[SAPPHIROS] < $strength[RUBIA])
		{
			if($strength[RUBIA] > $strength[JEDEITE])
			{
				$lose2 = SAPPHIROS;
				$win2 = RUBIA;
				$lose3 = JEDEITE;
			}
			else if($strength[RUBIA] < $strength[JEDEITE])
			{
				$win2 = JEDEITE;
				$lose2 = RUBIA;
				$lose3 = SAPPHIROS;
			}
			else
			{
				if(rand(0,1) == 0)
				{
					$win2 = RUBIA;
					$lose2 = JEDEITE;
				}
				else
				{
					$lose2 = RUBIA;
					$win2 = JEDEITE;
				}
				$lose3 = SAPPHIROS;
			}
		}
		else
		{
			if($strength[SAPPHIROS] > $strength[JEDEITE])
			{
				if(rand(0,1) == 0)
				{
					$lose2 = SAPPHIROS;
					$win2 = RUBIA;
				}
				else
				{
					$win2 = SAPPHIROS;
					$lose2 = RUBIA;
				}
				$lose3 = JEDEITE;
			}
			else if($strength[SAPPHIROS] < $strength[JEDEITE])
			{
				$win2 = JEDEITE;
				$lose2 = RUBIA;
				$lose3 = SAPPHIROS;
			}
			else
			{
				$rand = rand(0,2);
				if($rand == 0)
				{
					$win2 = SAPPHIROS;
					$lose2 = JEDEITE;
					$lose3 = RUBIA;
				}
				else if($rand == 1)
				{
					$lose2 = SAPPHIROS;
					$win2 = JEDEITE;
					$lose3 = RUBIA;
				}
				else
				{
					$win2 = RUBIA;
					$lose2 = JEDEITE;
					$lose3 = SAPPHIROS;
				}
				
			}
		}
	break;
	case 5:
		//check the first two gem gods first
		if($strength[SAPPHIROS] > $strength[JEDEITE])
		{
			$win1 = SAPPHIROS;
			$lose1 = JEDEITE;
		}
		else if($strength[SAPPHIROS] < $strength[JEDEITE])
		{
			$lose1 = SAPPHIROS;
			$win1 = JEDEITE;
		}
		else//unlikely event of a tie
		{
			if(rand(0,1) == 0)
			{
				$win1 = SAPPHIROS;
				$lose1 = JEDEITE;
			}
			else
			{
				$win1 = JEDEITE;
				$lose1 = SAPPHIROS;
			}
		}
		
		//now check for the remaining 3
		if($strength[RUBIA] > $strength[DAMOS])
		{
			if($strength[RUBIA] > $strength[MACHALITE])
			{
				$win2 = RUBIA;
				$lose2 = DAMOS;
				$lose3 = MACHALITE;
			}
			else if($strength[RUBIA] < $strength[MACHALITE])
			{
				$win2 = MACHALITE;
				$lose2 = DAMOS;
				$lose3 = RUBIA;
			}
			else
			{
				if(rand(0,1) == 0)
				{
					$win2 = RUBIA;
					$lose2 = MACHALITE;
				}
				else
				{
					$lose2 = RUBIA;
					$win2 = MACHALITE;
				}
				$lose3 = DAMOS;
			}
		}
		else if($strength[RUBIA] < $strength[DAMOS])
		{
			if($strength[DAMOS] > $strength[MACHALITE])
			{
				$lose2 = RUBIA;
				$win2 = DAMOS;
				$lose3 = MACHALITE;
			}
			else if($strength[DAMOS] < $strength[MACHALITE])
			{
				$win2 = MACHALITE;
				$lose2 = DAMOS;
				$lose3 = RUBIA;
			}
			else
			{
				if(rand(0,1) == 0)
				{
					$win2 = DAMOS;
					$lose2 = MACHALITE;
				}
				else
				{
					$lose2 = DAMOS;
					$win2 = MACHALITE;
				}
				$lose3 = RUBIA;
			}
		}
		else
		{
			if($strength[RUBIA] > $strength[MACHALITE])
			{
				if(rand(0,1) == 0)
				{
					$lose2 = RUBIA;
					$win2 = DAMOS;
				}
				else
				{
					$win2 = RUBIA;
					$lose2 = DAMOS;
				}
				$lose3 = MACHALITE;
			}
			else if($strength[RUBIA] < $strength[MACHALITE])
			{
				$win2 = MACHALITE;
				$lose2 = DAMOS;
				$lose3 = RUBIA;
			}
			else
			{
				$rand = rand(0,2);
				if($rand == 0)
				{
					$win2 = RUBIA;
					$lose2 = MACHALITE;
					$lose3 = DAMOS;
				}
				else if($rand == 1)
				{
					$lose2 = RUBIA;
					$win2 = MACHALITE;
					$lose3 = DAMOS;
				}
				else
				{
					$win2 = DAMOS;
					$lose2 = MACHALITE;
					$lose3 = RUBIA;
				}
				
			}
		}
	break;
	case 6:
		//check the first two gem gods first
		if($strength[MACHALITE] > $strength[RUBIA])
		{
			$win1 = MACHALITE;
			$lose1 = RUBIA;
		}
		else if($strength[MACHALITE] < $strength[RUBIA])
		{
			$lose1 = MACHALITE;
			$win1 = RUBIA;
		}
		else//unlikely event of a tie
		{
			if(rand(0,1) == 0)
			{
				$win1 = MACHALITE;
				$lose1 = RUBIA;
			}
			else
			{
				$win1 = RUBIA;
				$lose1 = MACHALITE;
			}
		}
		
		//now check for the remaining 3
		if($strength[JEDEITE] > $strength[DAMOS])
		{
			if($strength[JEDEITE] > $strength[SAPPHIROS])
			{
				$win2 = JEDEITE;
				$lose2 = DAMOS;
				$lose3 = SAPPHIROS;
			}
			else if($strength[JEDEITE] < $strength[SAPPHIROS])
			{
				$win2 = SAPPHIROS;
				$lose2 = DAMOS;
				$lose3 = JEDEITE;
			}
			else
			{
				if(rand(0,1) == 0)
				{
					$win2 = JEDEITE;
					$lose2 = SAPPHIROS;
				}
				else
				{
					$lose2 = JEDEITE;
					$win2 = SAPPHIROS;
				}
				$lose3 = DAMOS;
			}
		}
		else if($strength[JEDEITE] < $strength[DAMOS])
		{
			if($strength[DAMOS] > $strength[SAPPHIROS])
			{
				$lose2 = JEDEITE;
				$win2 = DAMOS;
				$lose3 = SAPPHIROS;
			}
			else if($strength[DAMOS] < $strength[SAPPHIROS])
			{
				$win2 = SAPPHIROS;
				$lose2 = DAMOS;
				$lose3 = JEDEITE;
			}
			else
			{
				if(rand(0,1) == 0)
				{
					$win2 = DAMOS;
					$lose2 = SAPPHIROS;
				}
				else
				{
					$lose2 = DAMOS;
					$win2 = SAPPHIROS;
				}
				$lose3 = JEDEITE;
			}
		}
		else
		{
			if($strength[JEDEITE] > $strength[SAPPHIROS])
			{
				if(rand(0,1) == 0)
				{
					$lose2 = JEDEITE;
					$win2 = DAMOS;
				}
				else
				{
					$win2 = JEDEITE;
					$lose2 = DAMOS;
				}
				$lose3 = SAPPHIROS;
			}
			else if($strength[JEDEITE] < $strength[SAPPHIROS])
			{
				$win2 = SAPPHIROS;
				$lose2 = DAMOS;
				$lose3 = JEDEITE;
			}
			else
			{
				$rand = rand(0,2);
				if($rand == 0)
				{
					$win2 = JEDEITE;
					$lose2 = SAPPHIROS;
					$lose3 = DAMOS;
				}
				else if($rand == 1)
				{
					$lose2 = JEDEITE;
					$win2 = SAPPHIROS;
					$lose3 = DAMOS;
				}
				else
				{
					$win2 = DAMOS;
					$lose2 = SAPPHIROS;
					$lose3 = JEDEITE;
				}
				
			}
		}
	
	break;
	case 7:
		//check the first two gem gods first
		if($strength[SAPPHIROS] > $strength[DAMOS])
		{
			$win1 = SAPPHIROS;
			$lose1 = DAMOS;
		}
		else if($strength[SAPPHIROS] < $strength[DAMOS])
		{
			$lose1 = SAPPHIROS;
			$win1 = DAMOS;
		}
		else//unlikely event of a tie
		{
			if(rand(0,1) == 0)
			{
				$win1 = SAPPHIROS;
				$lose1 = DAMOS;
			}
			else
			{
				$win1 = DAMOS;
				$lose1 = SAPPHIROS;
			}
		}
		
		//now check for the remaining 3
		if($strength[JEDEITE] > $strength[RUBIA])
		{
			if($strength[JEDEITE] > $strength[MACHALITE])
			{
				$win2 = JEDEITE;
				$lose2 = RUBIA;
				$lose3 = MACHALITE;
			}
			else if($strength[JEDEITE] < $strength[MACHALITE])
			{
				$win2 = MACHALITE;
				$lose2 = RUBIA;
				$lose3 = JEDEITE;
			}
			else
			{
				if(rand(0,1) == 0)
				{
					$win2 = JEDEITE;
					$lose2 = MACHALITE;
				}
				else
				{
					$lose2 = JEDEITE;
					$win2 = MACHALITE;
				}
				$lose3 = RUBIA;
			}
		}
		else if($strength[JEDEITE] < $strength[RUBIA])
		{
			if($strength[RUBIA] > $strength[MACHALITE])
			{
				$lose2 = JEDEITE;
				$win2 = RUBIA;
				$lose3 = MACHALITE;
			}
			else if($strength[RUBIA] < $strength[MACHALITE])
			{
				$win2 = MACHALITE;
				$lose2 = RUBIA;
				$lose3 = JEDEITE;
			}
			else
			{
				if(rand(0,1) == 0)
				{
					$win2 = RUBIA;
					$lose2 = MACHALITE;
				}
				else
				{
					$lose2 = RUBIA;
					$win2 = MACHALITE;
				}
				$lose3 = JEDEITE;
			}
		}
		else
		{
			if($strength[JEDEITE] > $strength[MACHALITE])
			{
				if(rand(0,1) == 0)
				{
					$lose2 = JEDEITE;
					$win2 = RUBIA;
				}
				else
				{
					$win2 = JEDEITE;
					$lose2 = RUBIA;
				}
				$lose3 = MACHALITE;
			}
			else if($strength[JEDEITE] < $strength[MACHALITE])
			{
				$win2 = MACHALITE;
				$lose2 = RUBIA;
				$lose3 = JEDEITE;
			}
			else
			{
				$rand = rand(0,2);
				if($rand == 0)
				{
					$win2 = JEDEITE;
					$lose2 = MACHALITE;
					$lose3 = RUBIA;
				}
				else if($rand == 1)
				{
					$lose2 = JEDEITE;
					$win2 = MACHALITE;
					$lose3 = RUBIA;
				}
				else
				{
					$win2 = RUBIA;
					$lose2 = MACHALITE;
					$lose3 = JEDEITE;
				}
				
			}
		}
	break;
	case 8:
		//check the first two gem gods first
		if($strength[MACHALITE] > $strength[JEDEITE])
		{
			$win1 = MACHALITE;
			$lose1 = JEDEITE;
		}
		else if($strength[MACHALITE] < $strength[JEDEITE])
		{
			$lose1 = MACHALITE;
			$win1 = JEDEITE;
		}
		else//unlikely event of a tie
		{
			if(rand(0,1) == 0)
			{
				$win1 = MACHALITE;
				$lose1 = JEDEITE;
			}
			else
			{
				$win1 = JEDEITE;
				$lose1 = MACHALITE;
			}
		}
		
		//now check for the remaining 3
		if($strength[SAPPHIROS] > $strength[RUBIA])
		{
			if($strength[SAPPHIROS] > $strength[DAMOS])
			{
				$win2 = SAPPHIROS;
				$lose2 = RUBIA;
				$lose3 = DAMOS;
			}
			else if($strength[SAPPHIROS] < $strength[DAMOS])
			{
				$win2 = DAMOS;
				$lose2 = RUBIA;
				$lose3 = SAPPHIROS;
			}
			else
			{
				if(rand(0,1) == 0)
				{
					$win2 = SAPPHIROS;
					$lose2 = DAMOS;
				}
				else
				{
					$lose2 = SAPPHIROS;
					$win2 = DAMOS;
				}
				$lose3 = RUBIA;
			}
		}
		else if($strength[SAPPHIROS] < $strength[RUBIA])
		{
			if($strength[RUBIA] > $strength[DAMOS])
			{
				$lose2 = SAPPHIROS;
				$win2 = RUBIA;
				$lose3 = DAMOS;
			}
			else if($strength[RUBIA] < $strength[DAMOS])
			{
				$win2 = DAMOS;
				$lose2 = RUBIA;
				$lose3 = SAPPHIROS;
			}
			else
			{
				if(rand(0,1) == 0)
				{
					$win2 = RUBIA;
					$lose2 = DAMOS;
				}
				else
				{
					$lose2 = RUBIA;
					$win2 = DAMOS;
				}
				$lose3 = SAPPHIROS;
			}
		}
		else
		{
			if($strength[SAPPHIROS] > $strength[DAMOS])
			{
				if(rand(0,1) == 0)
				{
					$lose2 = SAPPHIROS;
					$win2 = RUBIA;
				}
				else
				{
					$win2 = SAPPHIROS;
					$lose2 = RUBIA;
				}
				$lose3 = DAMOS;
			}
			else if($strength[SAPPHIROS] < $strength[DAMOS])
			{
				$win2 = DAMOS;
				$lose2 = RUBIA;
				$lose3 = SAPPHIROS;
			}
			else
			{
				$rand = rand(0,2);
				if($rand == 0)
				{
					$win2 = SAPPHIROS;
					$lose2 = DAMOS;
					$lose3 = RUBIA;
				}
				else if($rand == 1)
				{
					$lose2 = SAPPHIROS;
					$win2 = DAMOS;
					$lose3 = RUBIA;
				}
				else
				{
					$win2 = RUBIA;
					$lose2 = DAMOS;
					$lose3 = SAPPHIROS;
				}
				
			}
		}
	break;
	case 9:
		//check the first two gem gods first
		if($strength[RUBIA] > $strength[DAMOS])
		{
			$win1 = RUBIA;
			$lose1 = DAMOS;
		}
		else if($strength[RUBIA] < $strength[DAMOS])
		{
			$lose1 = RUBIA;
			$win1 = DAMOS;
		}
		else//unlikely event of a tie
		{
			if(rand(0,1) == 0)
			{
				$win1 = RUBIA;
				$lose1 = DAMOS;
			}
			else
			{
				$win1 = DAMOS;
				$lose1 = RUBIA;
			}
		}
		
		//now check for the remaining 3
		if($strength[SAPPHIROS] > $strength[JEDEITE])
		{
			if($strength[SAPPHIROS] > $strength[MACHALITE])
			{
				$win2 = SAPPHIROS;
				$lose2 = JEDEITE;
				$lose3 = MACHALITE;
			}
			else if($strength[SAPPHIROS] < $strength[MACHALITE])
			{
				$win2 = MACHALITE;
				$lose2 = JEDEITE;
				$lose3 = SAPPHIROS;
			}
			else
			{
				if(rand(0,1) == 0)
				{
					$win2 = SAPPHIROS;
					$lose2 = MACHALITE;
				}
				else
				{
					$lose2 = SAPPHIROS;
					$win2 = MACHALITE;
				}
				$lose3 = RUBIA;
			}
		}
		else if($strength[SAPPHIROS] < $strength[JEDEITE])
		{
			if($strength[JEDEITE] > $strength[MACHALITE])
			{
				$lose2 = SAPPHIROS;
				$win2 = JEDEITE;
				$lose3 = MACHALITE;
			}
			else if($strength[JEDEITE] < $strength[MACHALITE])
			{
				$win2 = MACHALITE;
				$lose2 = JEDEITE;
				$lose3 = SAPPHIROS;
			}
			else
			{
				if(rand(0,1) == 0)
				{
					$win2 = JEDEITE;
					$lose2 = MACHALITE;
				}
				else
				{
					$lose2 = JEDEITE;
					$win2 = MACHALITE;
				}
				$lose3 = SAPPHIROS;
			}
		}
		else
		{
			if($strength[SAPPHIROS] > $strength[MACHALITE])
			{
				if(rand(0,1) == 0)
				{
					$lose2 = SAPPHIROS;
					$win2 = JEDEITE;
				}
				else
				{
					$win2 = SAPPHIROS;
					$lose2 = JEDEITE;
				}
				$lose3 = MACHALITE;
			}
			else if($strength[SAPPHIROS] < $strength[MACHALITE])
			{
				$win2 = MACHALITE;
				$lose2 = JEDEITE;
				$lose3 = SAPPHIROS;
			}
			else
			{
				$rand = rand(0,2);
				if($rand == 0)
				{
					$win2 = SAPPHIROS;
					$lose2 = MACHALITE;
					$lose3 = JEDEITE;
				}
				else if($rand == 1)
				{
					$lose2 = SAPPHIROS;
					$win2 = MACHALITE;
					$lose3 = JEDEITE;
				}
				else
				{
					$win2 = JEDEITE;
					$lose2 = MACHALITE;
					$lose3 = SAPPHIROS;
				}
				
			}
		}
	break;
	case 100:
	case 101:
	case 102:
	case 103:
	case 104:
	case 105:
	case 106:
		$lose1 = 0;
		$lose2 = 0;
		$lose3 = 0;
		if($strength[SAPPHIROS] > $strength[RUBIA])
		{
			$win1 = SAPPHIROS;
			$win2 = SAPPHIROS;
		}
		else
		{
			$win1 = RUBIA;
			$win2 = RUBIA;
		}
		if($strength[JEDEITE] > $strength[$win1])
		{
			$win1 = JEDEITE;
			$win2 = JEDEITE;
		}
		if($strength[DAMOS] > $strength[$win1])
		{
			$win1 = DAMOS;
			$win2 = DAMOS;
		}
		if($strength[MACHALITE] > $strength[$win1])
		{
			$win1 = MACHALITE;
			$win2 = MACHALITE;
		}
		break;
	case 200:
		//Day 1 - Sapphiros vs Rubia, and Mach + Dam vs Jed
		if($strength[SAPPHIROS] > $strength[RUBIA])
		{
			$win1 = SAPPHIROS;
			$lose1 = RUBIA;
		}
		else if($strength[SAPPHIROS] < $strength[RUBIA])
		{
			$lose1 = SAPPHIROS;
			$win1 = RUBIA;
		}
		else//unlikely event of a tie
		{
			if(rand(0,1) == 0)
			{
				$win1 = SAPPHIROS;
				$lose1 = RUBIA;
			}
			else
			{
				$win1 = RUBIA;
				$lose1 = SAPPHIROS;
			}
		}
		//check team fight
		if($strength[JEDEITE] > $strength[DAMOS] + $strength[MACHALITE])
		{
			$win2 = JEDEITE;
			$lose2 = DAMOS;
			$lose3 = MACHALITE;
		}
		else if($strength[JEDEITE] < $strength[DAMOS] + $strength[MACHALITE])
		{
			$lose3 = JEDEITE;
			$win2 = DAMOS;
			$lose2 = MACHALITE;
		}
		else//unlikely event of a tie
		{
			if(rand(0,1) == 0)
			{
				$win2 = JEDEITE;
				$lose2 = DAMOS;
				$lose3 = MACHALITE;
			}
			else
			{
				$win2 = DAMOS;
				$lose2 = MACHALITE;
				$lose3 = JEDEITE;
			}
		}
		break;
	case 201:
		if($strength[MACHALITE] > $strength[DAMOS])
		{
			$win1 = MACHALITE;
			$lose1 = DAMOS;
		}
		else if($strength[MACHALITE] < $strength[DAMOS])
		{
			$lose1 = MACHALITE;
			$win1 = DAMOS;
		}
		else//unlikely event of a tie
		{
			if(rand(0,1) == 0)
			{
				$win1 = MACHALITE;
				$lose1 = DAMOS;
			}
			else
			{
				$win1 = DAMOS;
				$lose1 = MACHALITE;
			}
		}
		//check team fight
		if($strength[JEDEITE] > $strength[SAPPHIROS] + $strength[RUBIA])
		{
			$win2 = JEDEITE;
			$lose2 = SAPPHIROS;
			$lose3 = RUBIA;
		}
		else if($strength[JEDEITE] < $strength[SAPPHIROS] + $strength[RUBIA])
		{
			$lose3 = JEDEITE;
			$win2 = SAPPHIROS;
			$lose2 = RUBIA;
		}
		else//unlikely event of a tie
		{
			if(rand(0,1) == 0)
			{
				$win2 = JEDEITE;
				$lose2 = SAPPHIROS;
				$lose3 = RUBIA;
			}
			else
			{
				$win2 = SAPPHIROS;
				$lose2 = RUBIA;
				$lose3 = JEDEITE;
			}
		}
		break;
	case 202:
	//check the first two gem gods first
		if($strength[SAPPHIROS] > $strength[DAMOS])
		{
			$win1 = SAPPHIROS;
			$lose1 = DAMOS;
		}
		else if($strength[SAPPHIROS] < $strength[DAMOS])
		{
			$lose1 = SAPPHIROS;
			$win1 = DAMOS;
		}
		else//unlikely event of a tie
		{
			if(rand(0,1) == 0)
			{
				$win1 = SAPPHIROS;
				$lose1 = DAMOS;
			}
			else
			{
				$win1 = DAMOS;
				$lose1 = SAPPHIROS;
			}
		}
		//check team fight
		if($strength[JEDEITE] > $strength[MACHALITE] + $strength[RUBIA])
		{
			$win2 = JEDEITE;
			$lose2 = MACHALITE;
			$lose3 = RUBIA;
		}
		else if($strength[JEDEITE] < $strength[MACHALITE] + $strength[RUBIA])
		{
			$lose3 = JEDEITE;
			$win2 = MACHALITE;
			$lose2 = RUBIA;
		}
		else//unlikely event of a tie
		{
			if(rand(0,1) == 0)
			{
				$win2 = JEDEITE;
				$lose2 = MACHALITE;
				$lose3 = RUBIA;
			}
			else
			{
				$win2 = MACHALITE;
				$lose2 = RUBIA;
				$lose3 = JEDEITE;
			}
		}
		break;
	case 203:
	//check the first two gem gods first
		if($strength[MACHALITE] > $strength[RUBIA])
		{
			$win1 = MACHALITE;
			$lose1 = RUBIA;
		}
		else if($strength[MACHALITE] < $strength[RUBIA])
		{
			$lose1 = MACHALITE;
			$win1 = RUBIA;
		}
		else//unlikely event of a tie
		{
			if(rand(0,1) == 0)
			{
				$win1 = MACHALITE;
				$lose1 = RUBIA;
			}
			else
			{
				$win1 = RUBIA;
				$lose1 = MACHALITE;
			}
		}
		//check team fight
		if($strength[JEDEITE] > $strength[SAPPHIROS] + $strength[DAMOS])
		{
			$win2 = JEDEITE;
			$lose2 = SAPPHIROS;
			$lose3 = DAMOS;
		}
		else if($strength[JEDEITE] < $strength[SAPPHIROS] + $strength[DAMOS])
		{
			$lose3 = JEDEITE;
			$win2 = SAPPHIROS;
			$lose2 = DAMOS;
		}
		else//unlikely event of a tie
		{
			if(rand(0,1) == 0)
			{
				$win2 = JEDEITE;
				$lose2 = SAPPHIROS;
				$lose3 = DAMOS;
			}
			else
			{
				$win2 = SAPPHIROS;
				$lose2 = DAMOS;
				$lose3 = JEDEITE;
			}
		}
		break;
	case 204:
	//check the first two gem gods first
		if($strength[SAPPHIROS] > $strength[MACHALITE])
		{
			$win1 = SAPPHIROS;
			$lose1 = MACHALITE;
		}
		else if($strength[SAPPHIROS] < $strength[MACHALITE])
		{
			$lose1 = SAPPHIROS;
			$win1 = MACHALITE;
		}
		else//unlikely event of a tie
		{
			if(rand(0,1) == 0)
			{
				$win1 = SAPPHIROS;
				$lose1 = MACHALITE;
			}
			else
			{
				$win1 = MACHALITE;
				$lose1 = SAPPHIROS;
			}
		}
		//check team fight
		if($strength[JEDEITE] > $strength[RUBIA] + $strength[DAMOS])
		{
			$win2 = JEDEITE;
			$lose2 = RUBIA;
			$lose3 = DAMOS;
		}
		else if($strength[JEDEITE] < $strength[RUBIA] + $strength[DAMOS])
		{
			$lose3 = JEDEITE;
			$win2 = RUBIA;
			$lose2 = DAMOS;
		}
		else//unlikely event of a tie
		{
			if(rand(0,1) == 0)
			{
				$win2 = JEDEITE;
				$lose2 = RUBIA;
				$lose3 = DAMOS;
			}
			else
			{
				$win2 = RUBIA;
				$lose2 = DAMOS;
				$lose3 = JEDEITE;
			}
		}
		break;
	case 205:
	//check the first two gem gods first
		if($strength[RUBIA] > $strength[DAMOS])
		{
			$win1 = RUBIA;
			$lose1 = DAMOS;
		}
		else if($strength[RUBIA] < $strength[DAMOS])
		{
			$lose1 = RUBIA;
			$win1 = DAMOS;
		}
		else//unlikely event of a tie
		{
			if(rand(0,1) == 0)
			{
				$win1 = RUBIA;
				$lose1 = DAMOS;
			}
			else
			{
				$win1 = DAMOS;
				$lose1 = RUBIA;
			}
		}
		//check team fight
		if($strength[JEDEITE] > $strength[SAPPHIROS] + $strength[MACHALITE])
		{
			$win2 = JEDEITE;
			$lose2 = SAPPHIROS;
			$lose3 = MACHALITE;
		}
		else if($strength[JEDEITE] < $strength[SAPPHIROS] + $strength[MACHALITE])
		{
			$lose3 = JEDEITE;
			$win2 = SAPPHIROS;
			$lose2 = MACHALITE;
		}
		else//unlikely event of a tie
		{
			if(rand(0,1) == 0)
			{
				$win2 = JEDEITE;
				$lose2 = SAPPHIROS;
				$lose3 = MACHALITE;
			}
			else
			{
				$win2 = SAPPHIROS;
				$lose2 = MACHALITE;
				$lose3 = JEDEITE;
			}
		}
		break;
	case 206:
	//the final battle against Jedeite!
		if($strength[JEDEITE] > $strength[SAPPHIROS] + $strength[DAMOS] + $strength[RUBIA] + $strength[MACHALITE])
		{
			$win1 = JEDEITE;
			$win2 = RUBIA;
			$lose1 = DAMOS;
			$lose2 = SAPPHIROS;
			$lose3 = MACHALITE;
		}
		else if($strength[JEDEITE] < $strength[SAPPHIROS] + $strength[MACHALITE])
		{
			$win1 = RUBIA;
			$lose1 = RUBIA;
			$lose3 = JEDEITE;
			$win2 = SAPPHIROS;
			$lose2 = MACHALITE;
		}
		else//unlikely event of a tie
		{
			if(rand(0,1) == 0)
			{
				$win1 = JEDEITE;
				$win2 = RUBIA;
				$lose1 = DAMOS;
				$lose2 = SAPPHIROS;
				$lose3 = MACHALITE;
			}
			else
			{
				$win1 = RUBIA;
				$lose1 = RUBIA;
				$lose3 = JEDEITE;
				$win2 = SAPPHIROS;
				$lose2 = MACHALITE;
			}
		}
		break;
}

if($day != 206)
{
$cPrize = 0;
$cPrize2 = 0;
$uPrize = 0;
$uPrize2 = 0;
switch ($win1)
{
	case SAPPHIROS:
		$cPrize = 28;
		$cPrize2 = 116;
		$uPrize = 6;
		$uPrize2 = 117;
		$rPrize = 62;
		$win1Name = "Sapphiros";
		break;
	case RUBIA:
		$cPrize = 9;
		$cPrize2 = 114;
		$uPrize = 14;
		$uPrize2 = 115;
		$rPrize = 63;
		$win1Name = "Rubia";
		break;
	case JEDEITE:
		$cPrize = 26;
		$cPrize2 = 93;
		$uPrize = 36;
		$uPrize2 = 94;
		$rPrize = 64;
		$win1Name = "Jedeite";
		break;
	case DAMOS:
		$cPrize = 48;
		$cPrize2 = 118;
		$uPrize = 38;
		$uPrize2 = 119;
		$rPrize = 65;
		$win1Name = "Damos";
		break;
	case MACHALITE:
		$cPrize = 4;
		$cPrize2 = 120;
		$uPrize = 52;
		$uPrize2 = 121;
		$rPrize = 66;
		$win1Name = "Machalite";
		break;
}

for($i = 0; $i < count($chosen[$win1]); $i++)
{
	$query = "SELECT achievement2, achievement3, achievement4, achievement13 FROM CW_Achievements WHERE id='" . $chosen[$win1][$i] . "'";
	$result = mysql_query($query);
	
	$rows = mysql_fetch_row($result);
	$TipTheScales = $rows[0];
	$OneChosenArmy = $rows[1];
	$Monotheist = $rows[2];
	$ImpressiveCollection = $rows[3];
	
	$AchTip = 0;
	$AchOne = 0;
	$AchMono = 0;
	$AchRep = 0;
	$AchImp = 0;
	$AchCollect = 0;
	
	if($TipTheScales >= 0)
	{
		if(++$TipTheScales == 10)
		{
			$TipTheScales = -1;
			$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $chosen[$win1][$i] . "'";
			$result = mysql_query($query);
			$AchTip = 1;
		}
		$query = "UPDATE CW_Achievements SET achievement2='" . $TipTheScales . "' WHERE id='" . $chosen[$win1][$i] . "'";
		$result = mysql_query($query);
	}
	if($OneChosenArmy >= 0)
	{
		if(++$OneChosenArmy == 100)
		{
			$OneChosenArmy = -1;
			$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $chosen[$win1][$i] . "'";
			$result = mysql_query($query);
			$AchOne = 1;
		}
		$query = "UPDATE CW_Achievements SET achievement3='" . $OneChosenArmy . "' WHERE id='" . $chosen[$win1][$i] . "'";
		$result = mysql_query($query);
	}
	
	if($Monotheist >= 0)
	{
		$doubleDip = 0;
		for ($j = 1; $j < 6; $j++)
		{
			if($j == $win1 || $doubleDip == 1)
				continue;
			for($k = 0; $k < count($chosen[$j]); $k++)
			{
				if($chosen[$j][$k] == $chosen[$win1][$i])
				{
					$doubleDip = 1;
					break;
				}
			}
		}
		if($doubleDip == 0)
		{
			if(++$Monotheist == 5)
			{
				$Monotheist = -1;
				$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $chosen[$win1][$i] . "'";
				$result = mysql_query($query);
				$AchMono = 1;
			}
			$query = "UPDATE CW_Achievements SET achievement4='" . $Monotheist . "' WHERE id='" . $chosen[$win1][$i] . "'";
			$result = mysql_query($query);
		}
	}

	$query = "SELECT * FROM CW_Notices WHERE id='" . $chosen[$win1][$i] . "'";
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
		
	
	$rand = rand(0, 100);
	if($rand < 75)
	{
		$prize = $cPrize;
		$rand = rand(0,1);
		if($rand == 1)
			$prize = $cPrize2;
	}
	else if($rand <= 95)
	{
		$prize = $uPrize;
		$rand = rand(0,1);
		if($rand == 1)
			$prize = $uPrize2;
	}
	else
	{
		$prize = $rPrize;
		//Prize for every occasion!
		if($ImpressiveCollection > 0)
		{
			$newFaction = 1;
			for($a = 0; $a < strlen($ImpressiveCollection); $a++)
			{
				if(substr($ImpressiveCollection, $a, 1) == $win1)
				{
					$newFaction = 0;
					break;
				}
			}
			if($newFaction == 1)
			{
				
				$ImpressiveCollection .= $win1;
				
				if(strlen($ImpressiveCollection) == 5)
				{
					$ImpressiveCollection = -1;
					$skills++;
					$query = "UPDATE CW_Users SET skills='" . $skills . "' WHERE id='" . $chosen[$win1][$i] . "'";
					$result = mysql_query($query);
					$AchImp = 1;
				}
				$query = "UPDATE CW_Achievements SET achievement13='" . $ImpressiveCollection . "' WHERE id='" . $chosen[$win1][$i] . "'";
				$result = mysql_query($query);
			}
		}
		else if($ImpressiveCollection == 0)
		{
			$ImpressiveCollection = $win1;
			$query = "UPDATE CW_Achievements SET achievement13='" . $ImpressiveCollection . "' WHERE id='" . $chosen[$win1][$i] . "'";
			$result = mysql_query($query);
		}
	}
	$query = "SELECT power" . $prize . " FROM CW_Inventory WHERE id='" . $chosen[$win1][$i] . "'";
	$result = mysql_query($query);
	$rows = mysql_fetch_row($result);
	if($rows[0] == 0)
	{
		$query = "SELECT achievement16, achievement14 FROM CW_Achievements WHERE id='" . $chosen[$win1][$i] . "'";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		$Repertoire = $row[0];
		$CollectThemAll = $row[1];
		$query = "SELECT powerTotal FROM CW_Users WHERE id='" . $chosen[$win1][$i] . "'";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		if($Repertoire >= 0 && ++$row[0] >= 50)
		{
			$Repertoire = -1;
			$skills++;
			$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $chosen[$win1][$i] . "'";
			$result = mysql_query($query);
			$query = "UPDATE CW_Achievements SET achievement16='" . $Repertoire . "' WHERE id='" . $chosen[$win1][$i] . "'";
			$result = mysql_query($query);
			$AchRep = 1;
		}
		if($CollectThemAll >= 0)
		{
			if(++$CollectThemAll >= 15)
			{
				$CollectThemAll = -1;
				$skills++;
				$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $chosen[$win1][$i] . "'";
				$result = mysql_query($query);
				$AchCollect = 1;
			}
			$query = "UPDATE CW_Achievements SET achievement14='" . $CollectThemAll . "' WHERE id='" . $chosen[$win1][$i] . "'";
			$result = mysql_query($query);
			
		}
		$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id='" . $chosen[$win1][$i] . "'";
		$result = mysql_query($query);
			
	}
	$query = "SELECT name FROM CW_Items WHERE id='" . $prize . "'";
	$result = mysql_query($query);
	$rows = mysql_fetch_row($result);
	$pName = $rows[0];
	$newMessage = "You assisted " . $win1Name . " at winning a faction war!<br/>In recognition of your service, you have been awarded " . $pName . ".";
	$newtime = time();
	$newtime = date("Y-m-d H:i:s", $newtime);
	for ($j = 4; $j > 0; $j--)
	{
		$message[$j] = $message[($j - 1)];
		$messageTime[$j] = $messageTime[($j - 1)];
	}
	$message[0] = $newMessage;
	$messageTime[0] = $newtime;
	if($AchTip == 1)
	{
		$newtime = time();
		$newtime = date("Y-m-d H:i:s", $newtime);
		for ($j = 4; $j > 0; $j--)
		{
			$message[$j] = $message[($j - 1)];
			$messageTime[$j] = $messageTime[($j - 1)];
		}
		$message[0] = "You earned the Tip The Scales achievement by winning 10 faction wars!  You have been awarded 1 skill point.";
		$messageTime[0] = $newtime;
	}
	if($AchOne == 1)
	{
		$newtime = time();
		$newtime = date("Y-m-d H:i:s", $newtime);
		for ($j = 4; $j > 0; $j--)
		{
			$message[$j] = $message[($j - 1)];
			$messageTime[$j] = $messageTime[($j - 1)];
		}
		$message[0] = "You earned the One Chosen Army achievement by winning 100 faction wars!  You have been awarded 1 skill point.";
		$messageTime[0] = $newtime;
	}
	if($AchMono == 1)
	{
		$newtime = time();
		$newtime = date("Y-m-d H:i:s", $newtime);
		for ($j = 4; $j > 0; $j--)
		{
			$message[$j] = $message[($j - 1)];
			$messageTime[$j] = $messageTime[($j - 1)];
		}
		$message[0] = "You earned the Monotheist achievement by winning 5 faction wars while supporting only 1 god!  You have been awarded 1 skill point.";
		$messageTime[0] = $newtime;
	}
	if($AchRep == 1)
	{
		$newtime = time();
		$newtime = date("Y-m-d H:i:s", $newtime);
		for ($j = 4; $j > 0; $j--)
		{
			$message[$j] = $message[($j - 1)];
			$messageTime[$j] = $messageTime[($j - 1)];
		}
		$message[0] = "You earned the Repertoire achievement by possessing 50 powers!  You have been awarded 1 skill point.";
		$messageTime[0] = $newtime;
	}
	if($AchCollect == 1)
	{
		$newtime = time();
		$newtime = date("Y-m-d H:i:s", $newtime);
		for ($j = 4; $j > 0; $j--)
		{
			$message[$j] = $message[($j - 1)];
			$messageTime[$j] = $messageTime[($j - 1)];
		}
		$message[0] = "You earned the Collect Them All achievement by earning 15 gem god powers!  You have been awarded 1 skill point.";
		$messageTime[0] = $newtime;
	}
	$query = "UPDATE CW_Notices SET message1='" . $message[0] . "', message2='" . $message[1] . "', message3='" . $message[2] . "', message4='" . $message[3] . "', message5='" . $message[4] . "', messageTime1='" . $messageTime[0] . "', messageTime2='" . $messageTime[1] . "', messageTime3='" . $messageTime[2] . "', messageTime4='" . $messageTime[3] . "', messageTime5='" . $messageTime[4] . "' WHERE id='" . $chosen[$win1][$i] . "'";
	$result = mysql_query($query);
	$query = "UPDATE CW_Inventory SET power" . $prize . "=power" . $prize . " + 1 WHERE id='" . $chosen[$win1][$i] . "'";
	$result = mysql_query($query);
}
$cPrize = 0;
$uPrize = 0;
$rPrize = 0;
$cPrize2 = 0;
$uPrize2 = 0;
$message = array();

switch ($win2)
{
	case SAPPHIROS:
		$cPrize = 28;
		$cPrize2 = 116;
		$uPrize = 6;
		$uPrize2 = 117;
		$rPrize = 62;
		$win2Name = "Sapphiros";
		break;
	case RUBIA:
		$cPrize = 9;
		$cPrize2 = 114;
		$uPrize = 14;
		$uPrize2 = 115;
		$rPrize = 63;
		$win2Name = "Rubia";
		break;
	case JEDEITE:
		$cPrize = 26;
		$cPrize2 = 93;
		$uPrize = 36;
		$uPrize2 = 94;
		$rPrize = 64;
		$win2Name = "Jedeite";
		break;
	case DAMOS:
		$cPrize = 48;
		$cPrize2 = 118;
		$uPrize = 38;
		$uPrize2 = 119;
		$rPrize = 65;
		$win2Name = "Damos";
		break;
	case MACHALITE:
		$cPrize = 4;
		$cPrize2 = 120;
		$uPrize = 52;
		$uPrize2 = 121;
		$rPrize = 66;
		$win2Name = "Machalite";
		break;
}
//$cPrize2 = 0;
//$uPrize2 = 0;
//$rPrize2 = 0;
if($day >= 200 && $day < 206 && $win2 != JEDEITE)
{
	switch ($lose2)
	{
		case SAPPHIROS:
			$cPrize22 = 28;
			$uPrize22 = 6;
			$rPrize22 = 62;
			$win2Name .= " and Sapphiros";
			break;
		case RUBIA:
			$cPrize22 = 9;
			$uPrize22 = 14;
			$rPrize22 = 63;
			$win2Name .= " and Rubia";
			break;
		case JEDEITE:
			$cPrize = 26;
		$cPrize2 = 93;
		$uPrize = 36;
		$uPrize2 = 94;
			$rPrize2 = 64;
			$win2Name .= " and Jedeite";
			break;
		case DAMOS:
			$cPrize22 = 48;
			$uPrize22 = 38;
			$rPrize22 = 65;
			$win2Name .= " and Damos";
			break;
		case MACHALITE:
			$cPrize22 = 4;
			$uPrize22 = 52;
			$rPrize22= 66;
			$win2Name .= " and Machalite";
			break;
	}
}

for($i = 0; $i < count($chosen[$win2]); $i++)
{
	$query = "SELECT achievement2, achievement3, achievement4, achievement13, achievement34 FROM CW_Achievements WHERE id='" . $chosen[$win2][$i] . "'";
	$result = mysql_query($query);
	
	$rows = mysql_fetch_row($result);
	$TipTheScales = $rows[0];
	$OneChosenArmy = $rows[1];
	$Monotheist = $rows[2];
	$ImpressiveCollection = $rows[3];
$EnemyOfMyEnemy = $rows[4];
	
	$AchTip = 0;
	$AchOne = 0;
	$AchMono = 0;
	$AchRep = 0;
	$AchImp = 0;
	$AchCollect = 0;
$achEnemy = 0;
	
	if($TipTheScales >= 0)
	{
		if(++$TipTheScales == 10)
		{
			$TipTheScales = -1;
			$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $chosen[$win2][$i] . "'";
			$result = mysql_query($query);
			$AchTip = 1;
		}
		$query = "UPDATE CW_Achievements SET achievement2='" . $TipTheScales . "' WHERE id='" . $chosen[$win2][$i] . "'";
		$result = mysql_query($query);
	}
	if($OneChosenArmy >= 0)
	{
		if(++$OneChosenArmy == 100)
		{
			$OneChosenArmy = -1;
			$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $chosen[$win2][$i] . "'";
			$result = mysql_query($query);
			$AchOne = 1;
		}
		$query = "UPDATE CW_Achievements SET achievement3='" . $OneChosenArmy . "' WHERE id='" . $chosen[$win2][$i] . "'";
		$result = mysql_query($query);
	}
	
	if($Monotheist >= 0)
	{
		$doubleDip = 0;
		for ($j = 1; $j < 6; $j++)
		{
			if($j == $win2 || $doubleDip == 1)
				continue;
			for($k = 0; $k < count($chosen[$j]); $k++)
			{
				if($chosen[$j][$k] == $chosen[$win2][$i])
				{
					$doubleDip = 1;
					break;
				}
			}
		}
		if($doubleDip == 0)
		{
			if(++$Monotheist == 5)
			{
				$Monotheist = -1;
				$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $chosen[$win2][$i] . "'";
				$result = mysql_query($query);
				$AchMono = 1;
			}
			$query = "UPDATE CW_Achievements SET achievement4='" . $Monotheist . "' WHERE id='" . $chosen[$win2][$i] . "'";
			$result = mysql_query($query);
		}
	}
	$query = "SELECT * FROM CW_Notices WHERE id='" . $chosen[$win2][$i] . "'";
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
	
	if($day >= 200 && $win2 == JEDEITE)
	{
		for($a = 0; $a < 3; $a++)
		{
		echo "in Jedeite win : prize ";
			//do this 3 times if Jedeite wins the special event
			$rand = rand(0, 100);
			if($rand < 75)
			{
				$prize = $cPrize;
				$rand = rand(0,1);
				if($rand == 1 && $win2 == JEDEITE)
					$prize = 93;
			}
			else if($rand <= 95)
			{
				$prize = $uPrize;
				$rand = rand(0,1);
				if($rand == 1 && $win2 == JEDEITE)
					$prize = 94;
			}
			else
			{
				$prize = $rPrize;
				//Prize for every occasion!
				if($ImpressiveCollection > 0)
				{
					$newFaction = 1;
					for($a = 0; $a < strlen($ImpressiveCollection); $a++)
					{
						if(substr($ImpressiveCollection, $a, 1) == $win2)
						{
							$newFaction = 0;
							break;
						}
					}
					if($newFaction == 1)
					{
						
						$ImpressiveCollection .= $win2;
						
						if(strlen($ImpressiveCollection) == 5)
						{
							$ImpressiveCollection = -1;
							$skills++;
							$query = "UPDATE CW_Users SET skills='" . $skills . "' WHERE id='" . $chosen[$win2][$i] . "'";
							$result = mysql_query($query);
							$AchImp = 1;
						}
						$query = "UPDATE CW_Achievements SET achievement13='" . $ImpressiveCollection . "' WHERE id='" . $chosen[$win2][$i] . "'";
						$result = mysql_query($query);
					}
				}
				else if($ImpressiveCollection == 0)
				{
					$ImpressiveCollection = $win2;
					$query = "UPDATE CW_Achievements SET achievement13='" . $ImpressiveCollection . "' WHERE id='" . $chosen[$win2][$i] . "'";
					$result = mysql_query($query);
				}
			}
			
			//echo $prize;
			$query = "SELECT power" . $prize . " FROM CW_Inventory WHERE id='" . $chosen[$win2][$i] . "'";
			$result = mysql_query($query);
			$rows = mysql_fetch_row($result);
			if($rows[0] == 0)
			{
				$query = "SELECT achievement16, achievement14 FROM CW_Achievements WHERE id='" . $chosen[$win2][$i] . "'";
				$result = mysql_query($query);
				$row = mysql_fetch_row($result);
				$Repertoire = $row[0];
				$CollectThemAll = $row[1];
				$query = "SELECT powerTotal FROM CW_Users WHERE id='" . $chosen[$win2][$i] . "'";
				$result = mysql_query($query);
				$row = mysql_fetch_row($result);
				if($Repertoire >= 0 && ++$row[0] >= 50)
				{
					$Repertoire = -1;
					$skills++;
					$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $chosen[$win2][$i] . "'";
					$result = mysql_query($query);
					$query = "UPDATE CW_Achievements SET achievement16='" . $Repertoire . "' WHERE id='" . $chosen[$win2][$i] . "'";
					$result = mysql_query($query);
					$AchRep = 1;
				}
				if($CollectThemAll >= 0)
				{
					if(++$CollectThemAll >= 15)
					{
						$CollectThemAll = -1;
						$skills++;
						$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $chosen[$win2][$i] . "'";
						$result = mysql_query($query);
						$AchCollect = 1;
					}
					$query = "UPDATE CW_Achievements SET achievement14='" . $CollectThemAll . "' WHERE id='" . $chosen[$win2][$i] . "'";
					$result = mysql_query($query);
					
				}
				$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id='" . $chosen[$win2][$i] . "'";
				$result = mysql_query($query);
					
			}
			$query = "SELECT name FROM CW_Items WHERE id='" . $prize . "'";
			$result = mysql_query($query);
			$rows = mysql_fetch_row($result);
			$pName = $rows[0];
			$newMessage = "You assisted " . $win2Name . " at winning a faction war!<br/>In recognition of your service, you have been awarded " . $pName . ".";
			$newtime = time();
			$newtime = date("Y-m-d H:i:s", $newtime);
			for ($j = 4; $j > 0; $j--)
			{
				$message[$j] = $message[($j - 1)];
				$messageTime[$j] = $messageTime[($j - 1)];
			}
			$message[0] = $newMessage;
			$messageTime[0] = $newtime;
		}
	}
	else
	{
		$rand = rand(0, 100);
		if($rand < 75)
		{
			$prize = $cPrize;
			$rand = rand(0,1);
			if($rand == 1)
				$prize = $cPrize2;
		}
		else if($rand <= 95)
		{
			$prize = $uPrize;
			$rand = rand(0,1);
			if($rand)
				$prize = $uPrize2;
		}
		else
		{
			$prize = $rPrize;
			//Prize for every occasion!
			if($ImpressiveCollection > 0)
			{
				$newFaction = 1;
				for($a = 0; $a < strlen($ImpressiveCollection); $a++)
				{
					if(substr($ImpressiveCollection, $a, 1) == $win2)
					{
						$newFaction = 0;
						break;
					}
				}
				if($newFaction == 1)
				{
					
					$ImpressiveCollection .= $win2;
					
					if(strlen($ImpressiveCollection) == 5)
					{
						$ImpressiveCollection = -1;
						$skills++;
						$query = "UPDATE CW_Users SET skills='" . $skills . "' WHERE id='" . $chosen[$win2][$i] . "'";
						$result = mysql_query($query);
						$AchImp = 1;
					}
					$query = "UPDATE CW_Achievements SET achievement13='" . $ImpressiveCollection . "' WHERE id='" . $chosen[$win2][$i] . "'";
					$result = mysql_query($query);
				}
			}
			else if($ImpressiveCollection == 0)
			{
				$ImpressiveCollection = $win2;
				$query = "UPDATE CW_Achievements SET achievement13='" . $ImpressiveCollection . "' WHERE id='" . $chosen[$win2][$i] . "'";
				$result = mysql_query($query);
			}
		}
		$query = "SELECT power" . $prize . " FROM CW_Inventory WHERE id='" . $chosen[$win2][$i] . "'";
		$result = mysql_query($query);
		$rows = mysql_fetch_row($result);
		if($rows[0] == 0)
		{
			$query = "SELECT achievement16, achievement14 FROM CW_Achievements WHERE id='" . $chosen[$win2][$i] . "'";
			$result = mysql_query($query);
			$row = mysql_fetch_row($result);
			$Repertoire = $row[0];
			$CollectThemAll = $row[1];
			$query = "SELECT powerTotal FROM CW_Users WHERE id='" . $chosen[$win2][$i] . "'";
			$result = mysql_query($query);
			$row = mysql_fetch_row($result);
			if($Repertoire >= 0 && ++$row[0] >= 50)
			{
				$Repertoire = -1;
				$skills++;
				$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $chosen[$win2][$i] . "'";
				$result = mysql_query($query);
				$query = "UPDATE CW_Achievements SET achievement16='" . $Repertoire . "' WHERE id='" . $chosen[$win2][$i] . "'";
				$result = mysql_query($query);
				$AchRep = 1;
			}
			if($CollectThemAll >= 0)
			{
				if(++$CollectThemAll >= 15)
				{
					$CollectThemAll = -1;
					$skills++;
					$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $chosen[$win2][$i] . "'";
					$result = mysql_query($query);
					$AchCollect = 1;
				}
				$query = "UPDATE CW_Achievements SET achievement14='" . $CollectThemAll . "' WHERE id='" . $chosen[$win2][$i] . "'";
				$result = mysql_query($query);
				
			}
			$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id='" . $chosen[$win2][$i] . "'";
			$result = mysql_query($query);
				
		}
		$query = "SELECT name FROM CW_Items WHERE id='" . $prize . "'";
		$result = mysql_query($query);
		$rows = mysql_fetch_row($result);
		$pName = $rows[0];
		$newMessage = "You assisted " . $win2Name . " at winning a faction war!<br/>In recognition of your service, you have been awarded " . $pName . ".";
		$newtime = time();
		$newtime = date("Y-m-d H:i:s", $newtime);
		for ($j = 4; $j > 0; $j--)
		{
			$message[$j] = $message[($j - 1)];
			$messageTime[$j] = $messageTime[($j - 1)];
		}
		$message[0] = $newMessage;
		$messageTime[0] = $newtime;
		if($day >= 200 && $day < 206 && $win2 != JEDEITE)//get the other teammate's prize
		{


if($EnemyOfMyEnemy >= 0)
	{
		
			$EnemyOfMyEnemy = -1;
			$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $chosen[$win2][$i] . "'";
			$result = mysql_query($query);
			$AchEnemy = 1;
		
		$query = "UPDATE CW_Achievements SET achievement34='" . $EnemyOfMyEnemy . "' WHERE id='" . $chosen[$win2][$i] . "'";
		$result = mysql_query($query);
	}







			$rand = rand(0, 100);
			if($rand < 75)
	{
		$prize = $cPrize22;
			
	}
	else if($rand <= 95)
	{
		$prize = $uPrize22;
	}
			else
			{
				$prize = $rPrize22;
				//Prize for every occasion!
				if($ImpressiveCollection > 0)
				{
					$newFaction = 1;
					for($a = 0; $a < strlen($ImpressiveCollection); $a++)
					{
						if(substr($ImpressiveCollection, $a, 1) == $lose2)
						{
							$newFaction = 0;
							break;
						}
					}
					if($newFaction == 1)
					{
						
						$ImpressiveCollection .= $lose2;
						
						if(strlen($ImpressiveCollection) == 5)
						{
							$ImpressiveCollection = -1;
							$skills++;
							$query = "UPDATE CW_Users SET skills='" . $skills . "' WHERE id='" . $chosen[$win2][$i] . "'";
							$result = mysql_query($query);
							$AchImp = 1;
						}
						$query = "UPDATE CW_Achievements SET achievement13='" . $ImpressiveCollection . "' WHERE id='" . $chosen[$win2][$i] . "'";
						$result = mysql_query($query);
					}
				}
				else if($ImpressiveCollection == 0)
				{
					$ImpressiveCollection = $lose2;
					$query = "UPDATE CW_Achievements SET achievement13='" . $ImpressiveCollection . "' WHERE id='" . $chosen[$win2][$i] . "'";
					$result = mysql_query($query);
				}
			}
			$query = "SELECT power" . $prize . " FROM CW_Inventory WHERE id='" . $chosen[$win2][$i] . "'";
			$result = mysql_query($query);
			$rows = mysql_fetch_row($result);
			if($rows[0] == 0)
			{
				$query = "SELECT achievement16, achievement14 FROM CW_Achievements WHERE id='" . $chosen[$win2][$i] . "'";
				$result = mysql_query($query);
				$row = mysql_fetch_row($result);
				$Repertoire = $row[0];
				$CollectThemAll = $row[1];
				$query = "SELECT powerTotal FROM CW_Users WHERE id='" . $chosen[$win2][$i] . "'";
				$result = mysql_query($query);
				$row = mysql_fetch_row($result);
				if($Repertoire >= 0 && ++$row[0] >= 50)
				{
					$Repertoire = -1;
					$skills++;
					$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $chosen[$win2][$i] . "'";
					$result = mysql_query($query);
					$query = "UPDATE CW_Achievements SET achievement16='" . $Repertoire . "' WHERE id='" . $chosen[$win2][$i] . "'";
					$result = mysql_query($query);
					$AchRep = 1;
				}
				if($CollectThemAll >= 0)
				{
					if(++$CollectThemAll >= 15)
					{
						$CollectThemAll = -1;
						$skills++;
						$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $chosen[$win2][$i] . "'";
						$result = mysql_query($query);
						$AchCollect = 1;
					}
					$query = "UPDATE CW_Achievements SET achievement14='" . $CollectThemAll . "' WHERE id='" . $chosen[$win2][$i] . "'";
					$result = mysql_query($query);
					
				}
				$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id='" . $chosen[$win2][$i] . "'";
				$result = mysql_query($query);
					
			}
			$query = "SELECT name FROM CW_Items WHERE id='" . $prize . "'";
			$result = mysql_query($query);
			$rows = mysql_fetch_row($result);
			$pName = $rows[0];
			$newMessage = "You assisted " . $win2Name . " at winning a faction war!<br/>In recognition of your service, you have been awarded " . $pName . ".";
			$newtime = time();
			$newtime = date("Y-m-d H:i:s", $newtime);
			for ($j = 4; $j > 0; $j--)
			{
				$message[$j] = $message[($j - 1)];
				$messageTime[$j] = $messageTime[($j - 1)];
			}
			$message[0] = $newMessage;
			$messageTime[0] = $newtime;
		}
	}
	if($AchTip == 1)
	{
		$newtime = time();
		$newtime = date("Y-m-d H:i:s", $newtime);
		for ($j = 4; $j > 0; $j--)
		{
			$message[$j] = $message[($j - 1)];
			$messageTime[$j] = $messageTime[($j - 1)];
		}
		$message[0] = "You earned the Tip The Scales achievement by winning 10 faction wars!  You have been awarded 1 skill point.";
		$messageTime[0] = $newtime;
	}
	if($AchOne == 1)
	{
		$newtime = time();
		$newtime = date("Y-m-d H:i:s", $newtime);
		for ($j = 4; $j > 0; $j--)
		{
			$message[$j] = $message[($j - 1)];
			$messageTime[$j] = $messageTime[($j - 1)];
		}
		$message[0] = "You earned the One Chosen Army achievement by winning 100 faction wars!  You have been awarded 1 skill point.";
		$messageTime[0] = $newtime;
	}
	if($AchMono == 1)
	{
		$newtime = time();
		$newtime = date("Y-m-d H:i:s", $newtime);
		for ($j = 4; $j > 0; $j--)
		{
			$message[$j] = $message[($j - 1)];
			$messageTime[$j] = $messageTime[($j - 1)];
		}
		$message[0] = "You earned the Monotheist achievement by winning 5 faction wars while supporting only 1 god!  You have been awarded 1 skill point.";
		$messageTime[0] = $newtime;
	}
	if($AchRep == 1)
	{
		$newtime = time();
		$newtime = date("Y-m-d H:i:s", $newtime);
		for ($j = 4; $j > 0; $j--)
		{
			$message[$j] = $message[($j - 1)];
			$messageTime[$j] = $messageTime[($j - 1)];
		}
		$message[0] = "You earned the Repertoire achievement by possessing 50 powers!  You have been awarded 1 skill point.";
		$messageTime[0] = $newtime;
	}
	if($AchCollect == 1)
	{
		$newtime = time();
		$newtime = date("Y-m-d H:i:s", $newtime);
		for ($j = 4; $j > 0; $j--)
		{
			$message[$j] = $message[($j - 1)];
			$messageTime[$j] = $messageTime[($j - 1)];
		}
		$message[0] = "You earned the Collect Them All achievement by earning 15 gem god powers!  You have been awarded 1 skill point.";
		$messageTime[0] = $newtime;
	}
if($AchEnemy == 1)
	{
		$newtime = time();
		$newtime = date("Y-m-d H:i:s", $newtime);
		for ($j = 4; $j > 0; $j--)
		{
			$message[$j] = $message[($j - 1)];
			$messageTime[$j] = $messageTime[($j - 1)];
		}
		$message[0] = "You earned the Enemy of my Enemy achievement by defeating Jedeite during the Seven Days In Jade event!  You have been awarded 1 skill point.";
		$messageTime[0] = $newtime;
	}
	$query = "UPDATE CW_Notices SET message1='" . $message[0] . "', message2='" . $message[1] . "', message3='" . $message[2] . "', message4='" . $message[3] . "', message5='" . $message[4] . "', messageTime1='" . $messageTime[0] . "', messageTime2='" . $messageTime[1] . "', messageTime3='" . $messageTime[2] . "', messageTime4='" . $messageTime[3] . "', messageTime5='" . $messageTime[4] . "' WHERE id='" . $chosen[$win2][$i] . "'";
	$result = mysql_query($query);
	$query = "UPDATE CW_Inventory SET power" . $prize . "=power" . $prize . " + 1 WHERE id='" . $chosen[$win2][$i] . "'";
	$result = mysql_query($query);
}
if($day >= 200 && $day < 206 && $win2 != JEDEITE)
{
//the other teammate wins, too!
for($i = 0; $i < count($chosen[$lose2]); $i++)
{
$doubleDip = 0;
	for($k = 0; $k < count($chosen[$win2]); $k++)
			{
				if($chosen[$win2][$k] == $chosen[$lose2][$i])
				{
					$doubleDip = 1;
					break;
				}
			}
	if($doubleDip > 0)
		continue;
	
	$query = "SELECT achievement2, achievement3, achievement4, achievement13, achievement34 FROM CW_Achievements WHERE id='" . $chosen[$lose2][$i] . "'";
	$result = mysql_query($query);
	
	$rows = mysql_fetch_row($result);
	$TipTheScales = $rows[0];
	$OneChosenArmy = $rows[1];
	$Monotheist = $rows[2];
	$ImpressiveCollection = $rows[3];
$EnemyOfMyEnemy = $rows[4];
	
	$AchTip = 0;
	$AchOne = 0;
	$AchMono = 0;
	$AchRep = 0;
	$AchImp = 0;
	$AchCollect = 0;
$achEnemy = 0;
	if($EnemyOfMyEnemy >= 0)
	{
		
			$EnemyOfMyEnemy = -1;
			$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $chosen[$lose2][$i] . "'";
			$result = mysql_query($query);
			$AchEnemy = 1;
		
		$query = "UPDATE CW_Achievements SET achievement34='" . $EnemyOfMyEnemy . "' WHERE id='" . $chosen[$lose2][$i] . "'";
		$result = mysql_query($query);
	}
	if($TipTheScales >= 0)
	{
		if(++$TipTheScales == 10)
		{
			$TipTheScales = -1;
			$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $chosen[$lose2][$i] . "'";
			$result = mysql_query($query);
			$AchTip = 1;
		}
		$query = "UPDATE CW_Achievements SET achievement2='" . $TipTheScales . "' WHERE id='" . $chosen[$lose2][$i] . "'";
		$result = mysql_query($query);
	}
	if($OneChosenArmy >= 0)
	{
		if(++$OneChosenArmy == 100)
		{
			$OneChosenArmy = -1;
			$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $chosen[$lose2][$i] . "'";
			$result = mysql_query($query);
			$AchOne = 1;
		}
		$query = "UPDATE CW_Achievements SET achievement3='" . $OneChosenArmy . "' WHERE id='" . $chosen[$lose2][$i] . "'";
		$result = mysql_query($query);
	}
	
	if($Monotheist >= 0)
	{
		$doubleDip = 0;
		for ($j = 1; $j < 6; $j++)
		{
			if($j == $lose2 || $doubleDip == 1)
				continue;
			for($k = 0; $k < count($chosen[$j]); $k++)
			{
				if($chosen[$j][$k] == $chosen[$lose2][$i])
				{
					$doubleDip = 1;
					break;
				}
			}
		}
		if($doubleDip == 0)
		{
			if(++$Monotheist == 5)
			{
				$Monotheist = -1;
				$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $chosen[$lose2][$i] . "'";
				$result = mysql_query($query);
				$AchMono = 1;
			}
			$query = "UPDATE CW_Achievements SET achievement4='" . $Monotheist . "' WHERE id='" . $chosen[$lose2][$i] . "'";
			$result = mysql_query($query);
		}
	}
	$query = "SELECT * FROM CW_Notices WHERE id='" . $chosen[$lose2][$i] . "'";
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
	
		$rand = rand(0, 100);
		if($rand < 75)
			{
				$prize = $cPrize;
				$rand = rand(0,1);
				if($rand == 1 && $win2 == JEDEITE)
					$prize = $cPrize2;
			}
			else if($rand <= 95)
			{
				$prize = $uPrize;
				$rand = rand(0,1);
				if($rand == 1 && $win2 == JEDEITE)
					$prize = $uPrize2;
			}
		else
		{
			$prize = $rPrize;
			//Prize for every occasion!
			if($ImpressiveCollection > 0)
			{
				$newFaction = 1;
				for($a = 0; $a < strlen($ImpressiveCollection); $a++)
				{
					if(substr($ImpressiveCollection, $a, 1) == $win2)
					{
						$newFaction = 0;
						break;
					}
				}
				if($newFaction == 1)
				{
					
					$ImpressiveCollection .= $win2;
					
					if(strlen($ImpressiveCollection) == 5)
					{
						$ImpressiveCollection = -1;
						$skills++;
						$query = "UPDATE CW_Users SET skills='" . $skills . "' WHERE id='" . $chosen[$lose2][$i] . "'";
						$result = mysql_query($query);
						$AchImp = 1;
					}
					$query = "UPDATE CW_Achievements SET achievement13='" . $ImpressiveCollection . "' WHERE id='" . $chosen[$lose2][$i] . "'";
					$result = mysql_query($query);
				}
			}
			else if($ImpressiveCollection == 0)
			{
				$ImpressiveCollection = $win2;
				$query = "UPDATE CW_Achievements SET achievement13='" . $ImpressiveCollection . "' WHERE id='" . $chosen[$lose2][$i] . "'";
				$result = mysql_query($query);
			}
		}
		$query = "SELECT power" . $prize . " FROM CW_Inventory WHERE id='" . $chosen[$lose2][$i] . "'";
		$result = mysql_query($query);
		$rows = mysql_fetch_row($result);
		if($rows[0] == 0)
		{
			$query = "SELECT achievement16, achievement14 FROM CW_Achievements WHERE id='" . $chosen[$lose2][$i] . "'";
			$result = mysql_query($query);
			$row = mysql_fetch_row($result);
			$Repertoire = $row[0];
			$CollectThemAll = $row[1];
			$query = "SELECT powerTotal FROM CW_Users WHERE id='" . $chosen[$lose2][$i] . "'";
			$result = mysql_query($query);
			$row = mysql_fetch_row($result);
			if($Repertoire >= 0 && ++$row[0] >= 50)
			{
				$Repertoire = -1;
				$skills++;
				$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $chosen[$lose2][$i] . "'";
				$result = mysql_query($query);
				$query = "UPDATE CW_Achievements SET achievement16='" . $Repertoire . "' WHERE id='" . $chosen[$lose2][$i] . "'";
				$result = mysql_query($query);
				$AchRep = 1;
			}
			if($CollectThemAll >= 0)
			{
				if(++$CollectThemAll >= 15)
				{
					$CollectThemAll = -1;
					$skills++;
					$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $chosen[$lose2][$i] . "'";
					$result = mysql_query($query);
					$AchCollect = 1;
				}
				$query = "UPDATE CW_Achievements SET achievement14='" . $CollectThemAll . "' WHERE id='" . $chosen[$lose2][$i] . "'";
				$result = mysql_query($query);
				
			}
			$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id='" . $chosen[$lose2][$i] . "'";
			$result = mysql_query($query);
				
		}
		$query = "SELECT name FROM CW_Items WHERE id='" . $prize . "'";
		$result = mysql_query($query);
		$rows = mysql_fetch_row($result);
		$pName = $rows[0];
		$newMessage = "You assisted " . $win2Name . " at winning a faction war!<br/>In recognition of your service, you have been awarded " . $pName . ".";
		$newtime = time();
		$newtime = date("Y-m-d H:i:s", $newtime);
		for ($j = 4; $j > 0; $j--)
		{
			$message[$j] = $message[($j - 1)];
			$messageTime[$j] = $messageTime[($j - 1)];
		}
		$message[0] = $newMessage;
		$messageTime[0] = $newtime;
		if($day >= 200 && $day < 206 && $win2 != JEDEITE)//get the other teammate's prize
		{
			$rand = rand(0, 100);
			if($rand < 75)
				$prize = $cPrize22;
			else if($rand <= 95)
			{
				$prize = $uPrize22;
			}
			else
			{
				$prize = $rPrize22;
				//Prize for every occasion!
				if($ImpressiveCollection > 0)
				{
					$newFaction = 1;
					for($a = 0; $a < strlen($ImpressiveCollection); $a++)
					{
						if(substr($ImpressiveCollection, $a, 1) == $lose2)
						{
							$newFaction = 0;
							break;
						}
					}
					if($newFaction == 1)
					{
						
						$ImpressiveCollection .= $lose2;
						
						if(strlen($ImpressiveCollection) == 5)
						{
							$ImpressiveCollection = -1;
							$skills++;
							$query = "UPDATE CW_Users SET skills='" . $skills . "' WHERE id='" . $chosen[$lose2][$i] . "'";
							$result = mysql_query($query);
							$AchImp = 1;
						}
						$query = "UPDATE CW_Achievements SET achievement13='" . $ImpressiveCollection . "' WHERE id='" . $chosen[$lose2][$i] . "'";
						$result = mysql_query($query);
					}
				}
				else if($ImpressiveCollection == 0)
				{
					$ImpressiveCollection = $lose2;
					$query = "UPDATE CW_Achievements SET achievement13='" . $ImpressiveCollection . "' WHERE id='" . $chosen[$lose2][$i] . "'";
					$result = mysql_query($query);
				}
			}
			$query = "SELECT power" . $prize . " FROM CW_Inventory WHERE id='" . $chosen[$lose2][$i] . "'";
			$result = mysql_query($query);
			$rows = mysql_fetch_row($result);
			if($rows[0] == 0)
			{
				$query = "SELECT achievement16, achievement14 FROM CW_Achievements WHERE id='" . $chosen[$lose2][$i] . "'";
				$result = mysql_query($query);
				$row = mysql_fetch_row($result);
				$Repertoire = $row[0];
				$CollectThemAll = $row[1];
				$query = "SELECT powerTotal FROM CW_Users WHERE id='" . $chosen[$lose2][$i] . "'";
				$result = mysql_query($query);
				$row = mysql_fetch_row($result);
				if($Repertoire >= 0 && ++$row[0] >= 50)
				{
					$Repertoire = -1;
					$skills++;
					$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $chosen[$lose2][$i] . "'";
					$result = mysql_query($query);
					$query = "UPDATE CW_Achievements SET achievement16='" . $Repertoire . "' WHERE id='" . $chosen[$lose2][$i] . "'";
					$result = mysql_query($query);
					$AchRep = 1;
				}
				if($CollectThemAll >= 0)
				{
					if(++$CollectThemAll >= 15)
					{
						$CollectThemAll = -1;
						$skills++;
						$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $chosen[$lose2][$i] . "'";
						$result = mysql_query($query);
						$AchCollect = 1;
					}
					$query = "UPDATE CW_Achievements SET achievement14='" . $CollectThemAll . "' WHERE id='" . $chosen[$lose2][$i] . "'";
					$result = mysql_query($query);
					
				}
				$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id='" . $chosen[$lose2][$i] . "'";
				$result = mysql_query($query);
					
			}
			$query = "SELECT name FROM CW_Items WHERE id='" . $prize . "'";
			$result = mysql_query($query);
			$rows = mysql_fetch_row($result);
			$pName = $rows[0];
			$newMessage = "You assisted " . $win2Name . " at winning a faction war!<br/>In recognition of your service, you have been awarded " . $pName . ".";
			$newtime = time();
			$newtime = date("Y-m-d H:i:s", $newtime);
			for ($j = 4; $j > 0; $j--)
			{
				$message[$j] = $message[($j - 1)];
				$messageTime[$j] = $messageTime[($j - 1)];
			}
			$message[0] = $newMessage;
			$messageTime[0] = $newtime;
		}
	
	if($AchTip == 1)
	{
		$newtime = time();
		$newtime = date("Y-m-d H:i:s", $newtime);
		for ($j = 4; $j > 0; $j--)
		{
			$message[$j] = $message[($j - 1)];
			$messageTime[$j] = $messageTime[($j - 1)];
		}
		$message[0] = "You earned the Tip The Scales achievement by winning 10 faction wars!  You have been awarded 1 skill point.";
		$messageTime[0] = $newtime;
	}
	if($AchOne == 1)
	{
		$newtime = time();
		$newtime = date("Y-m-d H:i:s", $newtime);
		for ($j = 4; $j > 0; $j--)
		{
			$message[$j] = $message[($j - 1)];
			$messageTime[$j] = $messageTime[($j - 1)];
		}
		$message[0] = "You earned the One Chosen Army achievement by winning 100 faction wars!  You have been awarded 1 skill point.";
		$messageTime[0] = $newtime;
	}
	if($AchMono == 1)
	{
		$newtime = time();
		$newtime = date("Y-m-d H:i:s", $newtime);
		for ($j = 4; $j > 0; $j--)
		{
			$message[$j] = $message[($j - 1)];
			$messageTime[$j] = $messageTime[($j - 1)];
		}
		$message[0] = "You earned the Monotheist achievement by winning 5 faction wars while supporting only 1 god!  You have been awarded 1 skill point.";
		$messageTime[0] = $newtime;
	}
	if($AchRep == 1)
	{
		$newtime = time();
		$newtime = date("Y-m-d H:i:s", $newtime);
		for ($j = 4; $j > 0; $j--)
		{
			$message[$j] = $message[($j - 1)];
			$messageTime[$j] = $messageTime[($j - 1)];
		}
		$message[0] = "You earned the Repertoire achievement by possessing 50 powers!  You have been awarded 1 skill point.";
		$messageTime[0] = $newtime;
	}
	if($AchCollect == 1)
	{
		$newtime = time();
		$newtime = date("Y-m-d H:i:s", $newtime);
		for ($j = 4; $j > 0; $j--)
		{
			$message[$j] = $message[($j - 1)];
			$messageTime[$j] = $messageTime[($j - 1)];
		}
		$message[0] = "You earned the Collect Them All achievement by earning 15 gem god powers!  You have been awarded 1 skill point.";
		$messageTime[0] = $newtime;
	}
if($AchEnemy == 1)
	{
		$newtime = time();
		$newtime = date("Y-m-d H:i:s", $newtime);
		for ($j = 4; $j > 0; $j--)
		{
			$message[$j] = $message[($j - 1)];
			$messageTime[$j] = $messageTime[($j - 1)];
		}
		$message[0] = "You earned the Enemy of my Enemy achievement by defeating Jedeite during the Seven Days In Jade event!  You have been awarded 1 skill point.";
		$messageTime[0] = $newtime;
	}
	$query = "UPDATE CW_Notices SET message1='" . $message[0] . "', message2='" . $message[1] . "', message3='" . $message[2] . "', message4='" . $message[3] . "', message5='" . $message[4] . "', messageTime1='" . $messageTime[0] . "', messageTime2='" . $messageTime[1] . "', messageTime3='" . $messageTime[2] . "', messageTime4='" . $messageTime[3] . "', messageTime5='" . $messageTime[4] . "' WHERE id='" . $chosen[$lose2][$i] . "'";
	$result = mysql_query($query);
	$query = "UPDATE CW_Inventory SET power" . $prize . "=power" . $prize . " + 1 WHERE id='" . $chosen[$lose2][$i] . "'";
	$result = mysql_query($query);
}//end for
}//end if it's 2vs1
}//end if it's not day 206
else
{
if($win1 == JEDEITE)
{
$cPrize = 0;
$uPrize = 0;
switch ($win1)
{
	case SAPPHIROS:
		$cPrize = 28;
		$uPrize = 6;
		$rPrize = 62;
		$win1Name = "Sapphiros";
		break;
	case RUBIA:
		$cPrize = 9;
		$uPrize = 14;
		$rPrize = 63;
		$win1Name = "Rubia";
		break;
	case JEDEITE:
		$cPrize = 26;
		$cPrize2 = 93;
		$uPrize = 36;
		$uPrize2 = 94;
		$rPrize = 64;
		$win1Name = "Jedeite";
		break;
	case DAMOS:
		$cPrize = 48;
		$uPrize = 38;
		$rPrize = 65;
		$win1Name = "Damos";
		break;
	case MACHALITE:
		$cPrize = 4;
		$uPrize = 52;
		$rPrize = 66;
		$win1Name = "Machalite";
		break;
}

for($i = 0; $i < count($chosen[$win1]); $i++)
{
	$query = "SELECT achievement2, achievement3, achievement4, achievement13 FROM CW_Achievements WHERE id='" . $chosen[$win1][$i] . "'";
	$result = mysql_query($query);
	
	$rows = mysql_fetch_row($result);
	$TipTheScales = $rows[0];
	$OneChosenArmy = $rows[1];
	$Monotheist = $rows[2];
	$ImpressiveCollection = $rows[3];
	
	$AchTip = 0;
	$AchOne = 0;
	$AchMono = 0;
	$AchRep = 0;
	$AchImp = 0;
	$AchCollect = 0;
	
	if($TipTheScales >= 0)
	{
		if(++$TipTheScales == 10)
		{
			$TipTheScales = -1;
			$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $chosen[$win1][$i] . "'";
			$result = mysql_query($query);
			$AchTip = 1;
		}
		$query = "UPDATE CW_Achievements SET achievement2='" . $TipTheScales . "' WHERE id='" . $chosen[$win1][$i] . "'";
		$result = mysql_query($query);
	}
	if($OneChosenArmy >= 0)
	{
		if(++$OneChosenArmy == 100)
		{
			$OneChosenArmy = -1;
			$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $chosen[$win1][$i] . "'";
			$result = mysql_query($query);
			$AchOne = 1;
		}
		$query = "UPDATE CW_Achievements SET achievement3='" . $OneChosenArmy . "' WHERE id='" . $chosen[$win1][$i] . "'";
		$result = mysql_query($query);
	}
	
	if($Monotheist >= 0)
	{
		$doubleDip = 0;
		for ($j = 1; $j < 6; $j++)
		{
			if($j == $win1 || $doubleDip == 1)
				continue;
			for($k = 0; $k < count($chosen[$j]); $k++)
			{
				if($chosen[$j][$k] == $chosen[$win1][$i])
				{
					$doubleDip = 1;
					break;
				}
			}
		}
		if($doubleDip == 0)
		{
			if(++$Monotheist == 5)
			{
				$Monotheist = -1;
				$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $chosen[$win1][$i] . "'";
				$result = mysql_query($query);
				$AchMono = 1;
			}
			$query = "UPDATE CW_Achievements SET achievement4='" . $Monotheist . "' WHERE id='" . $chosen[$win1][$i] . "'";
			$result = mysql_query($query);
		}
	}

	$query = "SELECT * FROM CW_Notices WHERE id='" . $chosen[$win1][$i] . "'";
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
		
	for($a = 0; $a < 3; $a++)
	{
	$rand = rand(0, 100);
	if($rand < 75)
	{
		$prize = $cPrize;
		$rand = rand(0,1);
		if($rand == 1 && $win1 == JEDEITE)
			$prize = $cPrize2;
	}
	else if($rand <= 95)
	{
		$prize = $uPrize;
		$rand = rand(0,1);
		if($rand == 1 && $win1 == JEDEITE)
			$prize = $uPrize2;
	}
	else
	{
		$prize = $rPrize;
		//Prize for every occasion!
		if($ImpressiveCollection > 0)
		{
			$newFaction = 1;
			for($a = 0; $a < strlen($ImpressiveCollection); $a++)
			{
				if(substr($ImpressiveCollection, $a, 1) == $win1)
				{
					$newFaction = 0;
					break;
				}
			}
			if($newFaction == 1)
			{
				
				$ImpressiveCollection .= $win1;
				
				if(strlen($ImpressiveCollection) == 5)
				{
					$ImpressiveCollection = -1;
					$skills++;
					$query = "UPDATE CW_Users SET skills='" . $skills . "' WHERE id='" . $chosen[$win1][$i] . "'";
					$result = mysql_query($query);
					$AchImp = 1;
				}
				$query = "UPDATE CW_Achievements SET achievement13='" . $ImpressiveCollection . "' WHERE id='" . $chosen[$win1][$i] . "'";
				$result = mysql_query($query);
			}
		}
		else if($ImpressiveCollection == 0)
		{
			$ImpressiveCollection = $win1;
			$query = "UPDATE CW_Achievements SET achievement13='" . $ImpressiveCollection . "' WHERE id='" . $chosen[$win1][$i] . "'";
			$result = mysql_query($query);
		}
	}
	$query = "SELECT power" . $prize . " FROM CW_Inventory WHERE id='" . $chosen[$win1][$i] . "'";
	$result = mysql_query($query);
	$rows = mysql_fetch_row($result);
	if($rows[0] == 0)
	{
		$query = "SELECT achievement16, achievement14 FROM CW_Achievements WHERE id='" . $chosen[$win1][$i] . "'";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		$Repertoire = $row[0];
		$CollectThemAll = $row[1];
		$query = "SELECT powerTotal FROM CW_Users WHERE id='" . $chosen[$win1][$i] . "'";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		if($Repertoire >= 0 && ++$row[0] >= 50)
		{
			$Repertoire = -1;
			$skills++;
			$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $chosen[$win1][$i] . "'";
			$result = mysql_query($query);
			$query = "UPDATE CW_Achievements SET achievement16='" . $Repertoire . "' WHERE id='" . $chosen[$win1][$i] . "'";
			$result = mysql_query($query);
			$AchRep = 1;
		}
		if($CollectThemAll >= 0)
		{
			if(++$CollectThemAll >= 15)
			{
				$CollectThemAll = -1;
				$skills++;
				$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $chosen[$win1][$i] . "'";
				$result = mysql_query($query);
				$AchCollect = 1;
			}
			$query = "UPDATE CW_Achievements SET achievement14='" . $CollectThemAll . "' WHERE id='" . $chosen[$win1][$i] . "'";
			$result = mysql_query($query);
			
		}
		$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id='" . $chosen[$win1][$i] . "'";
		$result = mysql_query($query);
			
	}
	$query = "SELECT name FROM CW_Items WHERE id='" . $prize . "'";
	$result = mysql_query($query);
	$rows = mysql_fetch_row($result);
	$pName = $rows[0];
	$newMessage = "You assisted " . $win1Name . " at winning a faction war!<br/>In recognition of your service, you have been awarded " . $pName . ".";
	$newtime = time();
	$newtime = date("Y-m-d H:i:s", $newtime);
	for ($j = 4; $j > 0; $j--)
	{
		$message[$j] = $message[($j - 1)];
		$messageTime[$j] = $messageTime[($j - 1)];
	}
	$message[0] = $newMessage;
	$messageTime[0] = $newtime;
	}//end for loop
	if($AchTip == 1)
	{
		$newtime = time();
		$newtime = date("Y-m-d H:i:s", $newtime);
		for ($j = 4; $j > 0; $j--)
		{
			$message[$j] = $message[($j - 1)];
			$messageTime[$j] = $messageTime[($j - 1)];
		}
		$message[0] = "You earned the Tip The Scales achievement by winning 10 faction wars!  You have been awarded 1 skill point.";
		$messageTime[0] = $newtime;
	}
	if($AchOne == 1)
	{
		$newtime = time();
		$newtime = date("Y-m-d H:i:s", $newtime);
		for ($j = 4; $j > 0; $j--)
		{
			$message[$j] = $message[($j - 1)];
			$messageTime[$j] = $messageTime[($j - 1)];
		}
		$message[0] = "You earned the One Chosen Army achievement by winning 100 faction wars!  You have been awarded 1 skill point.";
		$messageTime[0] = $newtime;
	}
	if($AchMono == 1)
	{
		$newtime = time();
		$newtime = date("Y-m-d H:i:s", $newtime);
		for ($j = 4; $j > 0; $j--)
		{
			$message[$j] = $message[($j - 1)];
			$messageTime[$j] = $messageTime[($j - 1)];
		}
		$message[0] = "You earned the Monotheist achievement by winning 5 faction wars while supporting only 1 god!  You have been awarded 1 skill point.";
		$messageTime[0] = $newtime;
	}
	if($AchRep == 1)
	{
		$newtime = time();
		$newtime = date("Y-m-d H:i:s", $newtime);
		for ($j = 4; $j > 0; $j--)
		{
			$message[$j] = $message[($j - 1)];
			$messageTime[$j] = $messageTime[($j - 1)];
		}
		$message[0] = "You earned the Repertoire achievement by possessing 50 powers!  You have been awarded 1 skill point.";
		$messageTime[0] = $newtime;
	}
	if($AchCollect == 1)
	{
		$newtime = time();
		$newtime = date("Y-m-d H:i:s", $newtime);
		for ($j = 4; $j > 0; $j--)
		{
			$message[$j] = $message[($j - 1)];
			$messageTime[$j] = $messageTime[($j - 1)];
		}
		$message[0] = "You earned the Collect Them All achievement by earning 15 gem god powers!  You have been awarded 1 skill point.";
		$messageTime[0] = $newtime;
	}
	$query = "UPDATE CW_Notices SET message1='" . $message[0] . "', message2='" . $message[1] . "', message3='" . $message[2] . "', message4='" . $message[3] . "', message5='" . $message[4] . "', messageTime1='" . $messageTime[0] . "', messageTime2='" . $messageTime[1] . "', messageTime3='" . $messageTime[2] . "', messageTime4='" . $messageTime[3] . "', messageTime5='" . $messageTime[4] . "' WHERE id='" . $chosen[$win1][$i] . "'";
	$result = mysql_query($query);
	$query = "UPDATE CW_Inventory SET power" . $prize . "=power" . $prize . " + 1 WHERE id='" . $chosen[$win1][$i] . "'";
	$result = mysql_query($query);
}
}
else
{
$cPrize = 0;
$uPrize = 0;

		$cPrize = 28;
		$uPrize = 6;
		$rPrize = 62;
		$win1Name = "Sapphiros";


		$cPrize2 = 9;
		$uPrize2 = 14;
		$rPrize2 = 63;
		$win1Name .= ", Rubia";
		
		$cPrize3 = 48;
		$uPrize3 = 38;
		$rPrize3 = 65;
		$win1Name .= ", Damos";
		
		$cPrize4 = 4;
		$uPrize4 = 52;
		$rPrize4 = 66;
		$win1Name .= ", and Machalite";
		

for($t = 1; $t < 6; $t++)
{
if($t == JEDEITE)
	continue;
for($i = 0; $i < count($chosen[$t]); $i++)
{
	$doubleDip = 0;
	for($u = $t - 1; $u > 0; $u--)
	{
		if($u == JEDEITE || $doubleDip == 1)
			continue;
		for($k = 0; $k < count($chosen[$u]); $k++)
		{
			if($chosen[$u][$k] == $chosen[$t][$i])
			{
				$doubleDip = 1;
				break;
			}
		}
	}
	if($doubleDip == 1)
		continue;
	$query = "SELECT achievement2, achievement3, achievement4, achievement13, achievement34 FROM CW_Achievements WHERE id='" . $chosen[$t][$i] . "'";
	$result = mysql_query($query);
	
	$rows = mysql_fetch_row($result);
	$TipTheScales = $rows[0];
	$OneChosenArmy = $rows[1];
	$Monotheist = $rows[2];
	$ImpressiveCollection = $rows[3];
$EnemyOfMyEnemy = $rows[4];
	
	$AchTip = 0;
	$AchOne = 0;
	$AchMono = 0;
	$AchRep = 0;
	$AchImp = 0;
	$AchCollect = 0;
$AchEnemy = 0;


if($EnemyOfMyEnemy >= 0)
	{
		
			$EnemyOfMyEnemy = -1;
			$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $chosen[$t][$i] . "'";
			$result = mysql_query($query);
			$AchEnemy = 1;
		
		$query = "UPDATE CW_Achievements SET achievement34='" . $EnemyOfMyEnemy . "' WHERE id='" . $chosen[$t][$i] . "'";
		$result = mysql_query($query);
	}





	
	if($TipTheScales >= 0)
	{
		if(++$TipTheScales == 10)
		{
			$TipTheScales = -1;
			$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $chosen[$t][$i] . "'";
			$result = mysql_query($query);
			$AchTip = 1;
		}
		$query = "UPDATE CW_Achievements SET achievement2='" . $TipTheScales . "' WHERE id='" . $chosen[$t][$i] . "'";
		$result = mysql_query($query);
	}
	if($OneChosenArmy >= 0)
	{
		if(++$OneChosenArmy == 100)
		{
			$OneChosenArmy = -1;
			$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $chosen[$t][$i] . "'";
			$result = mysql_query($query);
			$AchOne = 1;
		}
		$query = "UPDATE CW_Achievements SET achievement3='" . $OneChosenArmy . "' WHERE id='" . $chosen[$t][$i] . "'";
		$result = mysql_query($query);
	}
	
	if($Monotheist >= 0)
	{
		$doubleDip = 0;
		for ($j = 1; $j < 6; $j++)
		{
			if($j == $t || $doubleDip == 1)
				continue;
			for($k = 0; $k < count($chosen[$j]); $k++)
			{
				if($chosen[$j][$k] == $chosen[$t][$i])
				{
					$doubleDip = 1;
					break;
				}
			}
		}
		if($doubleDip == 0)
		{
			if(++$Monotheist == 5)
			{
				$Monotheist = -1;
				$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $chosen[$t][$i] . "'";
				$result = mysql_query($query);
				$AchMono = 1;
			}
			$query = "UPDATE CW_Achievements SET achievement4='" . $Monotheist . "' WHERE id='" . $chosen[$t][$i] . "'";
			$result = mysql_query($query);
		}
	}

	$query = "SELECT * FROM CW_Notices WHERE id='" . $chosen[$t][$i] . "'";
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
		
	//hope you like this part, cuz we're repeating it 4 times
	$rand = rand(0, 100);
	if($rand < 75)
		$prize = $cPrize;
	else if($rand <= 95)
	{
		$prize = $uPrize;
	}
	else
	{
		$prize = $rPrize;
		//Prize for every occasion!
		if($ImpressiveCollection > 0)
		{
			$newFaction = 1;
			for($a = 0; $a < strlen($ImpressiveCollection); $a++)
			{
				if(substr($ImpressiveCollection, $a, 1) == SAPPHIROS)
				{
					$newFaction = 0;
					break;
				}
			}
			if($newFaction == 1)
			{
				
				$ImpressiveCollection .= SAPPHIROS;
				
				if(strlen($ImpressiveCollection) == 5)
				{
					$ImpressiveCollection = -1;
					$skills++;
					$query = "UPDATE CW_Users SET skills='" . $skills . "' WHERE id='" . $chosen[$t][$i] . "'";
					$result = mysql_query($query);
					$AchImp = 1;
				}
				$query = "UPDATE CW_Achievements SET achievement13='" . $ImpressiveCollection . "' WHERE id='" . $chosen[$t][$i] . "'";
				$result = mysql_query($query);
			}
		}
		else if($ImpressiveCollection == 0)
		{
			$ImpressiveCollection = SAPPHIROS;
			$query = "UPDATE CW_Achievements SET achievement13='" . $ImpressiveCollection . "' WHERE id='" . $chosen[$t][$i] . "'";
			$result = mysql_query($query);
		}
	}
	$query = "SELECT power" . $prize . " FROM CW_Inventory WHERE id='" . $chosen[$t][$i] . "'";
	$result = mysql_query($query);
	$rows = mysql_fetch_row($result);
	if($rows[0] == 0)
	{
		$query = "SELECT achievement16, achievement14 FROM CW_Achievements WHERE id='" . $chosen[$t][$i] . "'";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		$Repertoire = $row[0];
		$CollectThemAll = $row[1];
		$query = "SELECT powerTotal FROM CW_Users WHERE id='" . $chosen[$t][$i] . "'";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		if($Repertoire >= 0 && ++$row[0] >= 50)
		{
			$Repertoire = -1;
			$skills++;
			$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $chosen[$t][$i] . "'";
			$result = mysql_query($query);
			$query = "UPDATE CW_Achievements SET achievement16='" . $Repertoire . "' WHERE id='" . $chosen[$t][$i] . "'";
			$result = mysql_query($query);
			$AchRep = 1;
		}
		if($CollectThemAll >= 0)
		{
			if(++$CollectThemAll >= 15)
			{
				$CollectThemAll = -1;
				$skills++;
				$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $chosen[$t][$i] . "'";
				$result = mysql_query($query);
				$AchCollect = 1;
			}
			$query = "UPDATE CW_Achievements SET achievement14='" . $CollectThemAll . "' WHERE id='" . $chosen[$t][$i] . "'";
			$result = mysql_query($query);
			
		}
		$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id='" . $chosen[$t][$i] . "'";
		$result = mysql_query($query);
			
	}
	$query = "SELECT name FROM CW_Items WHERE id='" . $prize . "'";
	$result = mysql_query($query);
	$rows = mysql_fetch_row($result);
	$pName = $rows[0];
	$newMessage = "You assisted " . $win1Name . " at winning a faction war!<br/>In recognition of your service, you have been awarded " . $pName . ".";
	$newtime = time();
	$newtime = date("Y-m-d H:i:s", $newtime);
	for ($j = 4; $j > 0; $j--)
	{
		$message[$j] = $message[($j - 1)];
		$messageTime[$j] = $messageTime[($j - 1)];
	}
	$message[0] = $newMessage;
	$messageTime[0] = $newtime;
	
	//2nd pass
	$rand = rand(0, 100);
	if($rand < 75)
		$prize = $cPrize2;
	else if($rand <= 95)
	{
		$prize = $uPrize2;
	}
	else
	{
		$prize = $rPrize2;
		//Prize for every occasion!
		if($ImpressiveCollection > 0)
		{
			$newFaction = 1;
			for($a = 0; $a < strlen($ImpressiveCollection); $a++)
			{
				if(substr($ImpressiveCollection, $a, 1) == RUBIA)
				{
					$newFaction = 0;
					break;
				}
			}
			if($newFaction == 1)
			{
				
				$ImpressiveCollection .= RUBIA;
				
				if(strlen($ImpressiveCollection) == 5)
				{
					$ImpressiveCollection = -1;
					$skills++;
					$query = "UPDATE CW_Users SET skills='" . $skills . "' WHERE id='" . $chosen[$t][$i] . "'";
					$result = mysql_query($query);
					$AchImp = 1;
				}
				$query = "UPDATE CW_Achievements SET achievement13='" . $ImpressiveCollection . "' WHERE id='" . $chosen[$t][$i] . "'";
				$result = mysql_query($query);
			}
		}
		else if($ImpressiveCollection == 0)
		{
			$ImpressiveCollection = RUBIA;
			$query = "UPDATE CW_Achievements SET achievement13='" . $ImpressiveCollection . "' WHERE id='" . $chosen[$t][$i] . "'";
			$result = mysql_query($query);
		}
	}
	$query = "SELECT power" . $prize . " FROM CW_Inventory WHERE id='" . $chosen[$t][$i] . "'";
	$result = mysql_query($query);
	$rows = mysql_fetch_row($result);
	if($rows[0] == 0)
	{
		$query = "SELECT achievement16, achievement14 FROM CW_Achievements WHERE id='" . $chosen[$t][$i] . "'";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		$Repertoire = $row[0];
		$CollectThemAll = $row[1];
		$query = "SELECT powerTotal FROM CW_Users WHERE id='" . $chosen[$t][$i] . "'";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		if($Repertoire >= 0 && ++$row[0] >= 50)
		{
			$Repertoire = -1;
			$skills++;
			$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $chosen[$t][$i] . "'";
			$result = mysql_query($query);
			$query = "UPDATE CW_Achievements SET achievement16='" . $Repertoire . "' WHERE id='" . $chosen[$t][$i] . "'";
			$result = mysql_query($query);
			$AchRep = 1;
		}
		if($CollectThemAll >= 0)
		{
			if(++$CollectThemAll >= 15)
			{
				$CollectThemAll = -1;
				$skills++;
				$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $chosen[$t][$i] . "'";
				$result = mysql_query($query);
				$AchCollect = 1;
			}
			$query = "UPDATE CW_Achievements SET achievement14='" . $CollectThemAll . "' WHERE id='" . $chosen[$t][$i] . "'";
			$result = mysql_query($query);
			
		}
		$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id='" . $chosen[$t][$i] . "'";
		$result = mysql_query($query);
			
	}
	$query = "SELECT name FROM CW_Items WHERE id='" . $prize . "'";
	$result = mysql_query($query);
	$rows = mysql_fetch_row($result);
	$pName = $rows[0];
	$newMessage = "You assisted " . $win1Name . " at winning a faction war!<br/>In recognition of your service, you have been awarded " . $pName . ".";
	$newtime = time();
	$newtime = date("Y-m-d H:i:s", $newtime);
	for ($j = 4; $j > 0; $j--)
	{
		$message[$j] = $message[($j - 1)];
		$messageTime[$j] = $messageTime[($j - 1)];
	}
	$message[0] = $newMessage;
	$messageTime[0] = $newtime;
	
	//third pass
	$rand = rand(0, 100);
	if($rand < 75)
		$prize = $cPrize3;
	else if($rand <= 95)
	{
		$prize = $uPrize3;
	}
	else
	{
		$prize = $rPrize3;
		//Prize for every occasion!
		if($ImpressiveCollection > 0)
		{
			$newFaction = 1;
			for($a = 0; $a < strlen($ImpressiveCollection); $a++)
			{
				if(substr($ImpressiveCollection, $a, 1) == DAMOS)
				{
					$newFaction = 0;
					break;
				}
			}
			if($newFaction == 1)
			{
				
				$ImpressiveCollection .= DAMOS;
				
				if(strlen($ImpressiveCollection) == 5)
				{
					$ImpressiveCollection = -1;
					$skills++;
					$query = "UPDATE CW_Users SET skills='" . $skills . "' WHERE id='" . $chosen[$t][$i] . "'";
					$result = mysql_query($query);
					$AchImp = 1;
				}
				$query = "UPDATE CW_Achievements SET achievement13='" . $ImpressiveCollection . "' WHERE id='" . $chosen[$t][$i] . "'";
				$result = mysql_query($query);
			}
		}
		else if($ImpressiveCollection == 0)
		{
			$ImpressiveCollection = DAMOS;
			$query = "UPDATE CW_Achievements SET achievement13='" . $ImpressiveCollection . "' WHERE id='" . $chosen[$t][$i] . "'";
			$result = mysql_query($query);
		}
	}
	$query = "SELECT power" . $prize . " FROM CW_Inventory WHERE id='" . $chosen[$t][$i] . "'";
	$result = mysql_query($query);
	$rows = mysql_fetch_row($result);
	if($rows[0] == 0)
	{
		$query = "SELECT achievement16, achievement14 FROM CW_Achievements WHERE id='" . $chosen[$t][$i] . "'";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		$Repertoire = $row[0];
		$CollectThemAll = $row[1];
		$query = "SELECT powerTotal FROM CW_Users WHERE id='" . $chosen[$t][$i] . "'";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		if($Repertoire >= 0 && ++$row[0] >= 50)
		{
			$Repertoire = -1;
			$skills++;
			$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $chosen[$t][$i] . "'";
			$result = mysql_query($query);
			$query = "UPDATE CW_Achievements SET achievement16='" . $Repertoire . "' WHERE id='" . $chosen[$t][$i] . "'";
			$result = mysql_query($query);
			$AchRep = 1;
		}
		if($CollectThemAll >= 0)
		{
			if(++$CollectThemAll >= 15)
			{
				$CollectThemAll = -1;
				$skills++;
				$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $chosen[$t][$i] . "'";
				$result = mysql_query($query);
				$AchCollect = 1;
			}
			$query = "UPDATE CW_Achievements SET achievement14='" . $CollectThemAll . "' WHERE id='" . $chosen[$t][$i] . "'";
			$result = mysql_query($query);
			
		}
		$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id='" . $chosen[$t][$i] . "'";
		$result = mysql_query($query);
			
	}
	$query = "SELECT name FROM CW_Items WHERE id='" . $prize . "'";
	$result = mysql_query($query);
	$rows = mysql_fetch_row($result);
	$pName = $rows[0];
	$newMessage = "You assisted " . $win1Name . " at winning a faction war!<br/>In recognition of your service, you have been awarded " . $pName . ".";
	$newtime = time();
	$newtime = date("Y-m-d H:i:s", $newtime);
	for ($j = 4; $j > 0; $j--)
	{
		$message[$j] = $message[($j - 1)];
		$messageTime[$j] = $messageTime[($j - 1)];
	}
	$message[0] = $newMessage;
	$messageTime[0] = $newtime;
	
	//final pass
	$rand = rand(0, 100);
	if($rand < 75)
		$prize = $cPrize4;
	else if($rand <= 95)
	{
		$prize = $uPrize4;
	}
	else
	{
		$prize = $rPrize;
		//Prize for every occasion!
		if($ImpressiveCollection > 0)
		{
			$newFaction = 1;
			for($a = 0; $a < strlen($ImpressiveCollection); $a++)
			{
				if(substr($ImpressiveCollection, $a, 1) == MACHALITE)
				{
					$newFaction = 0;
					break;
				}
			}
			if($newFaction == 1)
			{
				
				$ImpressiveCollection .= MACHALITE;
				
				if(strlen($ImpressiveCollection) == 5)
				{
					$ImpressiveCollection = -1;
					$skills++;
					$query = "UPDATE CW_Users SET skills='" . $skills . "' WHERE id='" . $chosen[$t][$i] . "'";
					$result = mysql_query($query);
					$AchImp = 1;
				}
				$query = "UPDATE CW_Achievements SET achievement13='" . $ImpressiveCollection . "' WHERE id='" . $chosen[$t][$i] . "'";
				$result = mysql_query($query);
			}
		}
		else if($ImpressiveCollection == 0)
		{
			$ImpressiveCollection = MACHALITE;
			$query = "UPDATE CW_Achievements SET achievement13='" . $ImpressiveCollection . "' WHERE id='" . $chosen[$t][$i] . "'";
			$result = mysql_query($query);
		}
	}
	$query = "SELECT power" . $prize . " FROM CW_Inventory WHERE id='" . $chosen[$t][$i] . "'";
	$result = mysql_query($query);
	$rows = mysql_fetch_row($result);
	if($rows[0] == 0)
	{
		$query = "SELECT achievement16, achievement14 FROM CW_Achievements WHERE id='" . $chosen[$t][$i] . "'";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		$Repertoire = $row[0];
		$CollectThemAll = $row[1];
		$query = "SELECT powerTotal FROM CW_Users WHERE id='" . $chosen[$t][$i] . "'";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		if($Repertoire >= 0 && ++$row[0] >= 50)
		{
			$Repertoire = -1;
			$skills++;
			$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $chosen[$t][$i] . "'";
			$result = mysql_query($query);
			$query = "UPDATE CW_Achievements SET achievement16='" . $Repertoire . "' WHERE id='" . $chosen[$t][$i] . "'";
			$result = mysql_query($query);
			$AchRep = 1;
		}
		if($CollectThemAll >= 0)
		{
			if(++$CollectThemAll >= 15)
			{
				$CollectThemAll = -1;
				$skills++;
				$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $chosen[$t][$i] . "'";
				$result = mysql_query($query);
				$AchCollect = 1;
			}
			$query = "UPDATE CW_Achievements SET achievement14='" . $CollectThemAll . "' WHERE id='" . $chosen[$t][$i] . "'";
			$result = mysql_query($query);
			
		}
		$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id='" . $chosen[$t][$i] . "'";
		$result = mysql_query($query);
			
	}
	$query = "SELECT name FROM CW_Items WHERE id='" . $prize . "'";
	$result = mysql_query($query);
	$rows = mysql_fetch_row($result);
	$pName = $rows[0];
	$newMessage = "You assisted " . $win1Name . " at winning a faction war!<br/>In recognition of your service, you have been awarded " . $pName . ".";
	$newtime = time();
	$newtime = date("Y-m-d H:i:s", $newtime);
	for ($j = 4; $j > 0; $j--)
	{
		$message[$j] = $message[($j - 1)];
		$messageTime[$j] = $messageTime[($j - 1)];
	}
	$message[0] = $newMessage;
	$messageTime[0] = $newtime;
	
	
	if($AchTip == 1)
	{
		$newtime = time();
		$newtime = date("Y-m-d H:i:s", $newtime);
		for ($j = 4; $j > 0; $j--)
		{
			$message[$j] = $message[($j - 1)];
			$messageTime[$j] = $messageTime[($j - 1)];
		}
		$message[0] = "You earned the Tip The Scales achievement by winning 10 faction wars!  You have been awarded 1 skill point.";
		$messageTime[0] = $newtime;
	}
	if($AchOne == 1)
	{
		$newtime = time();
		$newtime = date("Y-m-d H:i:s", $newtime);
		for ($j = 4; $j > 0; $j--)
		{
			$message[$j] = $message[($j - 1)];
			$messageTime[$j] = $messageTime[($j - 1)];
		}
		$message[0] = "You earned the One Chosen Army achievement by winning 100 faction wars!  You have been awarded 1 skill point.";
		$messageTime[0] = $newtime;
	}
	if($AchMono == 1)
	{
		$newtime = time();
		$newtime = date("Y-m-d H:i:s", $newtime);
		for ($j = 4; $j > 0; $j--)
		{
			$message[$j] = $message[($j - 1)];
			$messageTime[$j] = $messageTime[($j - 1)];
		}
		$message[0] = "You earned the Monotheist achievement by winning 5 faction wars while supporting only 1 god!  You have been awarded 1 skill point.";
		$messageTime[0] = $newtime;
	}
	if($AchRep == 1)
	{
		$newtime = time();
		$newtime = date("Y-m-d H:i:s", $newtime);
		for ($j = 4; $j > 0; $j--)
		{
			$message[$j] = $message[($j - 1)];
			$messageTime[$j] = $messageTime[($j - 1)];
		}
		$message[0] = "You earned the Repertoire achievement by possessing 50 powers!  You have been awarded 1 skill point.";
		$messageTime[0] = $newtime;
	}
	if($AchCollect == 1)
	{
		$newtime = time();
		$newtime = date("Y-m-d H:i:s", $newtime);
		for ($j = 4; $j > 0; $j--)
		{
			$message[$j] = $message[($j - 1)];
			$messageTime[$j] = $messageTime[($j - 1)];
		}
		$message[0] = "You earned the Collect Them All achievement by earning 15 gem god powers!  You have been awarded 1 skill point.";
		$messageTime[0] = $newtime;
	}
if($AchEnemy == 1)
	{
		$newtime = time();
		$newtime = date("Y-m-d H:i:s", $newtime);
		for ($j = 4; $j > 0; $j--)
		{
			$message[$j] = $message[($j - 1)];
			$messageTime[$j] = $messageTime[($j - 1)];
		}
		$message[0] = "You earned the Enemy Of My Enemy achievement by defeating Jedeite during the Seven Days In Jade event!  You have been awarded 1 skill point.";
		$messageTime[0] = $newtime;
	}
	$query = "UPDATE CW_Notices SET message1='" . $message[0] . "', message2='" . $message[1] . "', message3='" . $message[2] . "', message4='" . $message[3] . "', message5='" . $message[4] . "', messageTime1='" . $messageTime[0] . "', messageTime2='" . $messageTime[1] . "', messageTime3='" . $messageTime[2] . "', messageTime4='" . $messageTime[3] . "', messageTime5='" . $messageTime[4] . "' WHERE id='" . $chosen[$t][$i] . "'";
	$result = mysql_query($query);
	$query = "UPDATE CW_Inventory SET power" . $prize . "=power" . $prize . " + 1 WHERE id='" . $chosen[$t][$i] . "'";
	$result = mysql_query($query);
}
}//end t
}
}
$day++;
if(($day >= 10 && $day < 90) || ($day > 106 && $day < 200) || $day > 206)
	$day = 0;




$query = "SELECT day1_1, day1_2, day2_1, day2_2, day3_1, day3_2, day4_1, day4_2, day5_1, day5_2, day6_1, day6_2, day7_1, day7_2 FROM CW_WarStats";
$result = mysql_query($query);
$row = mysql_fetch_row($result);
for($i = 13; $i > 1; $i--)
{
	$row[$i] = $row[$i - 2];
}

$query = "UPDATE CW_WarStats SET win1='" . $win1 . "', win2='" . $win2 . "', lose1='" . $lose1 . "', lose2='" . $lose2 . "', lose3='" . $lose3 . "', day='" . $day . "', ";
//for the funky team stuff
if($day >= 200 && $day < 206 && $win2 != JEDEITE)
{
    switch($day)
{
case 200:
$win2 = 11;
break;
case 201:
$win2 = 6;
break;
case 202:
$win2 = 10;
break;
case 203:
$win2 = 7;
break;
case 204:
$win2 = 9;
break;
case 205:
$win2 = 8;
break;
}

}
else if($day == 206)
{
if($win1 == JEDEITE)
$win2 = JEDEITE;
else
{
$win1 = 6;
$win2 = 11;
}
}
$query .= "day1_1 ='" . $win1 . "', day1_2='" . $win2 . "', ";
$query .= "day2_1='" . $row[2] . "', day2_2='" . $row[3] . "', day3_1='" . $row[4] . "', day3_2='" . $row[5] . "', day4_1='" . $row[6] . "', day4_2='" . $row[7] . "', day5_1='" . $row[8] . "', day5_2='" . $row[9] . "', day6_1='" . $row[10] . "', day6_2='" . $row[11] . "', day7_1='" . $row[12] . "', day7_2='" . $row[13] . "'";
$result = mysql_query($query);

mysql_close($db_server);
?>
