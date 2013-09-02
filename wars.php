<?php
require_once 'src/facebook.php';
require_once 'db_vars.php';
require_once 'functions.php';
require_once 'cw_vars.php';

$app_id = APP_ID;
    $app_secret = APP_SECRET;
    $my_url = "http://apps.facebook.com/crimsonwinter/";
$displayAchievement = 0;
$achName = '';
    $access_token = $_REQUEST["token"];
	if(!empty($access_token))
	{
	$graph_url = "https://graph.facebook.com/me?" . $access_token;

    $user = json_decode(file_get_contents($graph_url));

    $uid = $user->id;
    }
if(!$uid)
{
$signed_request = $_POST['signed_request'];
$signed_data = parse_signed_request($signed_request, $app_secret);
$uid = $signed_data['user_id'];
//echo $uid;
}
	if(!$uid)
	{
    $code = $_REQUEST["code"];

    if(empty($code)) {
        $dialog_url = "http://www.facebook.com/dialog/oauth?client_id=" 
            . $app_id . "&redirect_uri=" . urlencode($my_url);

        echo("<script> top.location.href='" . $dialog_url . "'</script>");
    }

    $token_url = "https://graph.facebook.com/oauth/access_token?client_id="
        . $app_id . "&redirect_uri=" . urlencode($my_url) . "&client_secret="
        . $app_secret . "&code=" . $code;

    $access_token = file_get_contents($token_url);

    $graph_url = "https://graph.facebook.com/me?" . $access_token;

    $user = json_decode(file_get_contents($graph_url));

    $uid = $user->id;
     $checkFriends = 1;
}
if(!is_numeric($uid))
	$uid = 0;



$db_server = mysql_connect(DB_HOST, DB_USER, DB_PW);

if(!$db_server) die("Unable to connect to MySQL: " . mysql_error());

mysql_select_db(DB_NAME) or die("Unable to select database: " . mysql_error());

if($checkFriends == 1 && $uid != 0)
{
$knights_url = "https://graph.facebook.com/me/friends?" . $access_token;
$all_friends = json_decode(file_get_contents($knights_url));
$num_friends = count($all_friends->data);

for($i = 0; $i < $num_friends; $i++)
{
	$query = "SELECT level FROM CW_Users WHERE id='" . $all_friends->data[$i]->id . "'";
	$result = mysql_query($query);
	if(mysql_num_rows($result) == 1)
	{
		$numKnights++;
	}
}
$query = "SELECT achievement27, achievement28 FROM CW_Achievements WHERE id='" . $uid . "'";
$result = mysql_query($query);
$row = mysql_fetch_row($result);
$Popular = $row[0];
$Famous = $row[1];
if($Popular != -1)
{
	$Popular = $numKnights;
	if($Popular >= 10)
	{
		$Popular = -1;
		$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $uid . "'";
		$result = mysql_query($query);
		$displayAchievement = 27;
		$achName = "Popular";
	}
	$query = "UPDATE CW_Achievements SET achievement27='" . $Popular . "' WHERE id='" . $uid . "'";
	$result = mysql_query($query);
}
if($Famous != -1)
{
	$Famous = $numKnights;
	if($Famous >= 100)
	{
		$Famous = -1;
		$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $uid . "'";
		$result = mysql_query($query);
		$displayAchievement = 28;
		$achName = "Famous";
	}
	$query = "UPDATE CW_Achievements SET achievement28='" . $Famous . "' WHERE id='" . $uid . "'";
	$result = mysql_query($query);
}
$query = "UPDATE CW_Users SET friends='" . $numKnights . "' WHERE id='" . $uid . "'";
$result = mysql_query($query);
$numKnights = 0;
}
$query = "SELECT * FROM CW_Users WHERE id='" . $uid . "'";//for real game, add WHERE to narrow it down to fb uid
$result = mysql_query($query);

if(!$result) die("Database access failed: " . mysql_error());


$rows = mysql_fetch_row($result);
//user details
$c_energy = $rows[1];
$m_energy = $rows[2];
$time = $rows[3];
$level = $rows[4];
$experience = $rows[5];
$krevels = $rows[6];
$gems = $rows[8];
$skills = $rows[9];
$attack = $rows[10];
$defence = $rows[11];
$c_stamina = $rows[12];
$m_stamina = $rows[13];
$c_health = $rows[14];
$m_health = $rows[15];
$hTime = $rows[16];
$sTime = $rows[17];
$faction = $rows[19];
$fac_change = $rows[20];

$levelUp = 0;

$played = $rows[29];


if($played == 0)
{
	$played = 1;
	$query = "UPDATE CW_Users SET played=1 WHERE id='" . $uid . "'";
	$result = mysql_query($query);
	$query = "SELECT achievement21 FROM CW_Achievements WHERE id='" . $uid . "'";
	$result = mysql_query($query);
	$row = mysql_fetch_row($result);
	if($row[0] == 6)
	{
		$skills++;
		$query="UPDATE CW_Users SET skills=skills+1 WHERE id='" . $uid . "'";
		$result = mysql_query($query);
		$query="UPDATE CW_Achievements SET achievement21='-1' WHERE id='" . $uid . "'";
		$result = mysql_query($query);
$displayAchievement = 21;
$achName = "New Home";
	}
}

$style='';
switch ($faction)
{
	case SAPPHIROS:
		$style="sapphiros";
		break;
	case RUBIA:
		$style="rubia";
		break;	
	case JEDEITE:
		$style="jedeite";
		break;
	case DAMOS:
		$style="damos";
		break;
	case MACHALITE:
		$style="machalite";
		break;
}

$query = "SELECT power67 FROM CW_Inventory WHERE id='" . $uid . "'";
$result = mysql_query($query);
$row = mysql_fetch_row($result);
$TempleKey = $row[0];

$query = "SELECT achievement1, achievement33 FROM CW_Achievements WHERE id='" . $uid . "'";
$result = mysql_query($query);
$row = mysql_fetch_row($result);
$WildCard = $row[0];
$TaiyouTourist = $row[1];
//war details
$query = "SELECT * FROM CW_WarStats";
$result = mysql_query($query);
$row = mysql_fetch_row($result);
$day = $row[5];
$win1 = $row[0];
$lose1 = $row[1];
$win2 = $row[2];
$lose2 = $row[3];
$lose3 = $row[4];

$trackSapphiros = "white";
$trackRubia = "white";
$trackJedeite = "white";
$trackMachalite = "white";

$trackBar[SAPPHIROS] = 0;
$trackBar[RUBIA] = 0;
$trackBar[JEDEITE] = 0;
$trackBar[DAMOS] = 0;
$trackBar[MACHALITE] = 0;

for($i = 7; $i < 21; $i++)
{
if($row[$i] < 6)
	$trackBar[$row[$i]]++;
else
{
switch($row[$i])
{
	case 6:
		$trackBar[SAPPHIROS]++;
		$trackBar[RUBIA]++;
	break;
	case 7:
		$trackBar[SAPPHIROS]++;
		$trackBar[DAMOS]++;
	break;
	case 8:
		$trackBar[SAPPHIROS]++;
		$trackBar[MACHALITE]++;
	break;
	case 9:
		$trackBar[DAMOS]++;
		$trackBar[RUBIA]++;
	break;
	case 10:
		$trackBar[MACHALITE]++;
		$trackBar[RUBIA]++;
	break;
	case 11:
		$trackBar[MACHALITE]++;
		$trackBar[DAMOS]++;
	break;
	case 12:
		$trackBar[SAPPHIROS]++;
		$trackBar[RUBIA]++;
		$trackBar[MACHALITE]++;
		$trackBar[DAMOS]++;
	break;
}
}
}
if($trackBar[SAPPHIROS] == 0)
	$trackSapphiros = "black";
if($trackBar[RUBIA] == 0)
	$trackRubia = "black";
if($trackBar[JEDEITE] == 0)
	$trackJedeite = "black";
if($trackBar[MACHALITE] == 0)
	$trackMachalite = "black";
	
$supportSapphiros = "white";
$supportRubia = "white";
$supportJedeite = "white";
$supportMachalite = "white";

$supporters[SAPPHIROS] = $row[21];
$supporters[RUBIA] = $row[22];
$supporters[JEDEITE] = $row[23];
$supporters[DAMOS] = $row[24];
$supporters[MACHALITE] = $row[25];

$supportTotal = 0;
for($i = 1; $i < 6; $i++)
	$supportTotal += $supporters[$i];
	
if($supportTotal != 0)
{	
for($i = 1; $i < 6; $i++)
{
	$supportBar[$i] = round($supporters[$i] / $supportTotal * 184) ;
	$supporters[$i] = round($supporters[$i] / $supportTotal * 100);
}
}
if($supportBar[SAPPHIROS] < 6)
	$supportSapphiros = "black";
if($supportBar[RUBIA] < 6)
	$supportRubia = "black";
if($supportBar[JEDEITE] < 6)
	$supportJedeite = "black";
if($supportBar[MACHALITE] < 6)
	$supportMachalite = "black";
$godName[SAPPHIROS] = "Sapphiros";
$godName[RUBIA] = "Rubia";
$godName[JEDEITE] = "Jedeite";
$godName[DAMOS] = "Damos";
$godName[MACHALITE] = "Machalite";
$godName[6] = "Team Sapphiros/Rubia";
$godName[7] = "Team Sapphiros/Damos";
$godName[8] = "Team Sapphiros/Machalite";
$godName[9] = "Team Rubia/Damos";
$godName[10] = "Team Rubia/Machalite";
$godName[11] = "Team Damos/Machalite";

$query = "SELECT jTier4, jTier10 FROM CW_Missions WHERE id='" . $uid . "'";
$result = mysql_query($query);
$rows = mysql_fetch_row($result);
if($rows[0] == 4 && $faction == JEDEITE)
	$jedeiteGold = 1;
else
	$jedeiteGold = 0;
if($rows[1] == 4 && $faction == JEDEITE)
$cost = 9;
else
$cost = 10;


$gemPower[SAPPHIROS] = 0;
$gemPower[RUBIA] = 0;
$gemPower[JEDEITE] = 0;
$gemPower[DAMOS] = 0;
$gemPower[MACHALITE] = 0;

$query = "SELECT strength, god FROM CW_Warriors WHERE id='" . $uid . "' ORDER BY god";
$result = mysql_query($query);
$rows = mysql_num_rows($result);
for($i = 0; $i < $rows; $i++)
{
$row = mysql_fetch_row($result);
if($gemPower[$row[1]] > 0)
{
	$query2 = "DELETE FROM CW_Warriors WHERE id='" . $uid . "' AND god='" . $row[1] . "'";
	$result2 = mysql_query($query2);
	$query2 = "INSERT INTO CW_Warriors VALUES(" . $uid . ", 0, " . $row[1] . ")";
	$result2 = mysql_query($query2);
}
$gemPower[$row[1]] += $row[0];
}

//time for new energy?
if($c_energy < $m_energy)
{
	do
	{
	
	$newtime = time();
	$query = "SELECT UNIX_TIMESTAMP('" . $time. "') as diff";//for real game, add WHERE to narrow it down to fb uid
	//$query = "SELECT TIMEDIFF('" . $time. "', SYSDATE()) as diff";
	$result = mysql_query($query);
	$result = mysql_fetch_assoc($result);
	
	$result['diff'] -= $newtime;

	
	
	
	
	if($result['diff'] < 0)
	{
		
		
		$c_energy++;
		if($faction == SAPPHIROS)
			$query = "UPDATE CW_Users SET time='" . $time. "' + INTERVAL 2 MINUTE + INTERVAL 30 SECOND, c_energy='" . $c_energy . "' WHERE id='" . $uid . "'";
		else
			$query = "UPDATE CW_Users SET time='" . $time. "' + INTERVAL 3 MINUTE, c_energy='" . $c_energy . "' WHERE id='" . $uid . "'";
		$subresult = mysql_query($query);
		$query = "SELECT time FROM CW_Users as time WHERE id='" . $uid . "'";
		$subresult = mysql_query($query);
		$subresult = mysql_fetch_assoc($subresult);
		$time= $subresult['time'];
		
	}
	if($c_energy >= $m_energy)
		$result['diff'] = 1;
	} while ($result['diff'] < 0 );
	
}

if($c_stamina < $m_stamina)
{
	do
	{
	$newtime = time();
	$query = "SELECT UNIX_TIMESTAMP('" . $sTime. "') as sDiff";//for real game, add WHERE to narrow it down to fb uid
	$result = mysql_query($query);
	$result = mysql_fetch_assoc($result);
	
	$result['sDiff'] -= $newtime;

	
	
	
	
	if($result['sDiff'] < 0)
	{
		
		$c_stamina++;
		if($faction == MACHALITE)
			$query = "UPDATE CW_Users SET sTime='" . $sTime. "' + INTERVAL 3 MINUTE, c_stamina='" . $c_stamina . "' WHERE id='" . $uid . "'";
		else
			$query = "UPDATE CW_Users SET sTime='" . $sTime. "' + INTERVAL 4 MINUTE, c_stamina='" . $c_stamina . "' WHERE id='" . $uid . "'";
		$subresult = mysql_query($query);
		$query = "SELECT sTime FROM CW_Users as sTime WHERE id='" . $uid . "'";
		$subresult = mysql_query($query);
		$subresult = mysql_fetch_assoc($subresult);
		$sTime= $subresult['sTime'];
		
	}
	if($c_stamina >= $m_stamina)
		$result['sDiff'] = 1;
	} while ($result['sDiff'] < 0 );
	
}

if($c_health < $m_health)
{
	do
	{
	
	$newtime = time();
	$query = "SELECT UNIX_TIMESTAMP('" . $hTime. "') as hDiff";//for real game, add WHERE to narrow it down to fb uid
	$result = mysql_query($query);
	$result = mysql_fetch_assoc($result);
	
	$result['hDiff'] -= $newtime;

	
	
	
	
	if($result['hDiff'] < 0)
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
		$result['hDiff'] = 1;
	} while ($result['hDiff'] < 0 );
	
}
$hasFought = 0;
$hasWon = 0;
$expWon = 0;
$strength = 0;
$showResult = 0;
$error = 0;
$errorMsg = '';
$areaChange = 0;
if($_POST[posted] == "yes")
{
	$_POST[posted] = "no";
	$showResult = 1;
	if($c_stamina >= $cost)
	{
		$setSTime = 0;
		if($c_stamina == $m_stamina)
			$setSTime = 1;
		$c_stamina-= $cost;
		if($faction == RUBIA)
			$attack = (int)($attack * 1.1);
		if($faction == DAMOS)
			$defence = (int)($defence * 1.1);
		
		$hasFought = 1;
		
		if($WildCard > 0)
		{
			$newFaction = 1;
			for($a = 0; $a < strlen($WildCard); $a++)
			{
				if(substr($WildCard, $a, 1) == $faction)
				{
					$newFaction = 0;
					break;
				}
			}
			if($newFaction == 1)
			{
				
				$WildCard .= $faction;
				
				if(strlen($WildCard) == 5)
				{
					$WildCard = -1;
					$skills++;
					$query = "UPDATE CW_Users SET skills='" . $skills . "' WHERE id='" . $uid . "'";
					$result = mysql_query($query);
$displayAchievement = 1;
$achName = "Wild Card";					
				}
				$query = "UPDATE CW_Achievements SET achievement1='" . $WildCard . "' WHERE id='" . $uid . "'";
				$result = mysql_query($query);
			}
		}
		else if($WildCard == 0)
		{
			$WildCard = $faction;
			$query = "UPDATE CW_Achievements SET achievement1='" . $WildCard . "' WHERE id='" . $uid . "'";
			$result = mysql_query($query);
		}
		
		
		$strength = $attack + $defence;
		
		$expWon = 10;
		if($attack > 600)
		{
			$expAttack = $attack - 600;
			$expWon += 10 + 10 + 6 + (int)($expAttack * 0.01);
		}
		else if($attack > 300)
		{
			$expAttack = $attack - 300;
			$expWon += 10 + 10 + (int)($expAttack * 0.02);
		}
		else if($attack > 100)
		{
			$expAttack = $attack - 100;
			$expWon += 10 + (int)($expAttack * 0.05);
		}
		else
		{
			$expWon += (int)($attack * 0.1);
		}
		if($defence > 600)
		{
			$expDefence = $defence - 600;
			$expWon += 10 + 10 + 6 + (int)($expDefence * 0.01);
		}
		else if($defence > 300)
		{
			$expDefence = $defence - 300;
			$expWon += 10 + 10 + (int)($expDefence * 0.02);
		}
		else if($defence > 100)
		{
			$expDefence = $defence - 100;
			$expWon += 10 + (int)($expDefence * 0.05);
		}
		else
		{
			$expWon += (int)($defence * 0.1);
		}
		
		if($jedeiteGold == 1)
		{
			$expWon += 1;
		}
		if($TempleKey > 0)
		{
			$expWon += 5;
			$query = "UPDATE CW_Inventory SET power67=power67 - 1 WHERE id='" . $uid . "'";
			$result = mysql_query($query);
		}
		$experience += $expWon;
		
		$next_exp = GetExpToNextLevel($level);
			while($experience >= $next_exp)
			{
				$level++;
				if($c_energy < $m_energy)
					$c_energy = $m_energy;
				if($c_health < $m_health)
					$c_health = $m_health;
				if($c_stamina < $m_stamina)
					$c_stamina = $m_stamina;
				$skills += 5;
				if($level % 3 == 0)
					$gems++;
				$next_exp = GetExpToNextLevel($level);
				$levelUp = 1;
				if($fac_change == 0)
					$fac_change = 1;
				if($level == 11 || $level == 26 || $level == 41 || $level == 61)
					$areaChange = 1;
			}
		$query = "UPDATE CW_Users SET gems='" . $gems . "', fac_change='" . $fac_change . "', skills='" . $skills . "', experience='" . $experience . "', level='" . $level . "', c_stamina='" . $c_stamina . "', c_health='" . $c_health . "', c_energy='" . $c_energy . "' WHERE id='" . $uid . "'";
		$result = mysql_query($query);
		
		$rand = rand(0, 100);
			if($rand < 5)
			{
				$rand = rand(0, 1);
				
				switch($rand)
				{
					case 0:
						$dropMsg = "<div style='position:absolute; left:564px; top:22px; width:86px;'>You found: <br/>\"Extra Belts\".</div>";
						$dropMsg .= "<div style='position:absolute; top:17px; left:657px;'><img title='Extra Belts' src='assets/power73.jpg'/></div>";
						$dropPower = 73;
						break;
					case 1:
						$dropMsg = "<div style='position:absolute; left:564px; top:22px; width:86px;'>You found: <br/>\"Mount\".</div>";
						$dropMsg .= "<div style='position:absolute; top:17px; left:657px;'><img title='Mount' src='assets/power74.jpg'/></div>";
						$dropPower = 74;
						break;
					
				}
				$query = "UPDATE CW_Inventory SET power" . $dropPower . "=power" . $dropPower . " + 1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
			}
			/*
			else if($rand < 15)
			{
			$rand = rand(0, 1);
				
				switch($rand)
				{
					case 0:
						$dropMsg = "<div style='position:absolute; left:564px; top:22px; width:86px;'>You found: <br/>\"Temple Statue\".</div>";
						$dropMsg .= "<div style='position:absolute; top:17px; left:657px;'><img title='Extra Belts' src='assets/power98.jpg'/></div>";
						$dropPower = 98;
						$numCollection = 1;
if($TaiyouTourist > 0)
				{
					$newCollect = 1;
					for($a = 0; $a < strlen($TaiyouTourist); $a++)
					{
						if(substr($TaiyouTourist, $a, 1) == $numCollection)
						{
							$newCollect = 0;
							break;
						}
					}
					if($newCollect == 1)
					{
						
						$TaiyouTourist .= $numCollection;
						
						if(strlen($TaiyouTourist) == 8)
						{
							$TaiyouTourist = -1;
							$skills++;
							$query = "UPDATE CW_Users SET skills='" . $skills . "' WHERE id='" . $uid . "'";
							$result = mysql_query($query);
							$displayAchievement = 33;
							$achName = "Little Jade City";		
						}
						$query = "UPDATE CW_Achievements SET achievement33='" . $TaiyouTourist . "' WHERE id='" . $uid . "'";
						$result = mysql_query($query);

					}
				}
				else if($TaiyouTourist == 0)
				{
					$TaiyouTourist = $numCollection;
					$query = "UPDATE CW_Achievements SET achievement33='" . $TaiyouTourist . "' WHERE id='" . $uid . "'";
					$result = mysql_query($query);
				}
						break;
					case 1:
						$dropMsg = "<div style='position:absolute; left:564px; top:22px; width:86px;'>You found: <br/>\"Sword Statue\".</div>";
						$dropMsg .= "<div style='position:absolute; top:17px; left:657px;'><img title='Mount' src='assets/power103.jpg'/></div>";
						$dropPower = 103;
						$numCollection = 6;
if($TaiyouTourist > 0)
				{
					$newCollect = 1;
					for($a = 0; $a < strlen($TaiyouTourist); $a++)
					{
						if(substr($TaiyouTourist, $a, 1) == $numCollection)
						{
							$newCollect = 0;
							break;
						}
					}
					if($newCollect == 1)
					{
						
						$TaiyouTourist .= $numCollection;
						
						if(strlen($TaiyouTourist) == 8)
						{
							$TaiyouTourist = -1;
							$skills++;
							$query = "UPDATE CW_Users SET skills='" . $skills . "' WHERE id='" . $uid . "'";
							$result = mysql_query($query);
							$displayAchievement = 33;
							$achName = "Little Jade City";		
						}
						$query = "UPDATE CW_Achievements SET achievement33='" . $TaiyouTourist . "' WHERE id='" . $uid . "'";
						$result = mysql_query($query);

					}
				}
				else if($TaiyouTourist == 0)
				{
					$TaiyouTourist = $numCollection;
					$query = "UPDATE CW_Achievements SET achievement33='" . $TaiyouTourist . "' WHERE id='" . $uid . "'";
					$result = mysql_query($query);
				}
						break;
					
				}
				$query = "UPDATE CW_Inventory SET power" . $dropPower . "=power" . $dropPower . " + 1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
			}
		*/
		if($gemPower[$faction] == 0)
		{
			$query = "INSERT INTO CW_Warriors VALUES(" . $uid . ", " . $strength . ", " . $faction . ")";
			$result = mysql_query($query);
		}
		else
		{
		$strength += $gemPower[$faction];
		$query = "UPDATE CW_Warriors SET strength='" . $strength . "' WHERE id='" . $uid . "' AND god='" . $faction . "'";
		$result = mysql_query($query);
		}
		if($setSTime == 1)
		{
			if($faction == MACHALITE)
				$sTime= time() + 3 * 60;
			else
				$sTime= time() + 4 * 60;
			$sTime= date("Y-m-d H:i:s", $sTime);
			$query = "UPDATE CW_Users SET sTime='" . $sTime . "' WHERE id='" . $uid . "'";
			$result = mysql_query($query);
		}
	}
	else if ($c_stamina <= 9)
	{
		$error = 1;
		$errorMsg = "<div style='position:absolute; width:100%; text-align:center; top:40px;'>Not enough stamina. <a href='market.php?token=$access_token' class='faction'>FILL UP HERE!</a></div>";
	}
		
}


$next_exp = GetExpToNextLevel($level);
$this_exp = GetExpToNextLevel($level - 1);
$expToNext = $next_exp - $experience;
$nextLevel = $next_exp - $this_exp;
$expWidth = (int)(($nextLevel - $expToNext) / $nextLevel * 100);
$sKrevels = number_format($krevels);
$expToThis = $nextLevel - $expToNext;

for($i = 1; $i <= 5; $i++)
{
$trackWidth[$i] = (int)(280 * ($trackBar[$i] / 7));
}
echo <<<_END
<?xml version="1.0" encoding="iso-8859-1" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<script>
var id = $uid;
var faction = $faction;
if(!id || !faction)
{
	window.location = "http://apps.facebook.com/crimsonwinter/setname.php";
	
}

</script>
<link rel="stylesheet" type="text/css" href="style.css" /> 
<title>Wars</title>
</head>
_END;
echo "<body class='" . $style . "'";
if($displayAchievement != 0)
{
echo <<<_END
onload="FB.ui(
   {
    method: 'feed',
    link: 'http://apps.facebook.com/crimsonwinter',
    picture: 'http://www.firstagestudios.com/Ldd49cEsTcwfb/assets/achie$displayAchievement.jpg',
    source: 'http://www.firstagestudios.com/Ldd49cEsTcwfb/assets/achie$displayAchievement.jpg',
     name: 'Achievement',
     caption: 'Crimson Winter',
     description: 'I have obtained the $achName achievement in Crimson Winter. Can you do the same?'

   },
   function(response) {
     if (response && response.post_id) {
       alert('Post was published.');
     } else {
       alert('Post was not published.');
     }

   }
  );"
_END;
}
else if($levelUp == 1)
{
echo <<<_END
onload="FB.ui(
   {
    method: 'feed',
    link: 'http://apps.facebook.com/crimsonwinter',
    picture: 'http://www.firstagestudios.com/Ldd49cEsTcwfb/assets/Icon_LevelUp.png',
    source: 'http://www.firstagestudios.com/Ldd49cEsTcwfb/assets/Icon_LevelUp.png',
     name: 'Level Up',
     caption: 'Crimson Winter',
     description: 'I am a level $level Chosen in Crimson Winter, see how much stronger I have become!'

   },
   function(response) {
     if (response && response.post_id) {
       alert('Post was published.');
     } else {
       alert('Post was not published.');
     }

   }
  );"
_END;
}
echo ">";
echo <<<_END
<div id="fb-root"></div>
<script src="http://connect.facebook.net/en_US/all.js"></script>
<script>
  window.fbAsyncInit = function() {
    FB.init({appId: '$app_id', status: true, cookie: true,
             xfbml: true});
    FB.Canvas.setAutoResize();
  };
  (function() {
    var e = document.createElement('script'); e.async = true;
    e.src = document.location.protocol +
      '//connect.facebook.net/en_US/all.js';
    document.getElementById('fb-root').appendChild(e);
  }());

</script>


<div class="hLevel">
<div style='font-size:1.5em; color:yellow;'>$level</div>
level<br/>
<div>
<iframe src="http://www.facebook.com/plugins/like.php?app_id=194805060566095&amp;href=http%3A%2F%2Fapps.facebook.com%2Fcrimsonwinter&amp;send=false&amp;layout=button_count&amp;width=90&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font=trebuchet+ms&amp;height=35" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:450px; height:35px;" allowTransparency="true"></iframe>
</div>
</div>
<div class='experience'>
<div style='position:absolute; width:111px; top:0px; left:0px;'><img src='assets/ExpBarBG.jpg'/></div>
<div style='position:absolute; width:111px; top:0px; left:0px; text-align:left;'><img src='assets/ExpBarFill.jpg' width='$expWidth%' height='18px'/></div>
<div style='position:absolute; width:111px; top:0px; left:0px;'>$expToThis / $nextLevel</div>
<div style='position:absolute; width:111px; top:22px; left:0px; font-variant:small-caps;'>experience</div>
<div style='position:absolute; width:111px; top:44px; left:0px; font-size:0.875em;'>next: $expToNext</div>
</div>

<div class='krevels'>
	<div style='position:absolute; width:130px; top:0px; left:0px; font-size:1.75em; color:yellow;'>$sKrevels</div>
	<div style='position:absolute; width:130px; top:30px; left:0px; font-variant:small-caps;'>krevels<img style='position:relative; top:5px; left:4px;' src='assets/KrevelIcon.png'/></div>
	<div style="position:absolute; width:65px; top:57px; left:30px; font-size:0.875em; background-image:url('assets/BG_box4x4.png');">
		<a href='bank.php?token=$access_token'>visit bank</a>
	</div>
</div>

<table class="stats">
<tr><td style="font-size:0.9375em;">energy</td><td class='header' id='tagEnergy'><a class='header'>$c_energy</a> / $m_energy</td></tr>
<tr><td style="font-size:0.9375em;">health</td><td class='header' id='tagHealth'><a class='header'>$c_health</a> / $m_health</td></tr>
<tr><td style="font-size:0.9375em;">stamina</td><td class='header' id='tagStamina'><a class='header'>$c_stamina</a> / $m_stamina</td></tr>
</table>

<table class="timers">
_END;
echo "<tr class='timers'>";
if($c_energy < $m_energy)
{
	$newtime = time();
	$newtime = date("Y-m-d H:i:s", $newtime);
	$query = "SELECT TIMEDIFF('" . $time. "', '" . $newtime . "') as diff";
	$result = mysql_query($query);
	$result = mysql_fetch_assoc($result);
	$diffETime = $result['diff'];
	echo "<td id='eTime'>more in " . $diffETime . "</td>";
	echo "<td style='width:26px;'><a href='market.php?token=$access_token'><img src='assets/BTN_Gem.jpg'/></a></td>";
	 
}
else
	echo "<td></td>";
echo "</tr><tr class='timers'>";
if($c_health < $m_health)
{
	$newtime = time();
	$newtime = date("Y-m-d H:i:s", $newtime);
	$query = "SELECT TIMEDIFF('" . $hTime. "', '" . $newtime . "') as diff";
	$result = mysql_query($query);
	$result = mysql_fetch_assoc($result);
	$diffHTime = $result['diff'];
	echo "<td id='hTime'>more in " . $diffHTime . "</td>";
	echo "<td style='width:26px;'><a href='flaqqers.php?token=$access_token'><img src='assets/BTN_Health.jpg'/></a></td>";
	 
}
else
	echo "<td></td>";
echo "</tr><tr class='timers'>";
if($c_stamina < $m_stamina)
{
	$newtime = time();
	$newtime = date("Y-m-d H:i:s", $newtime);
	$query = "SELECT TIMEDIFF('" . $sTime. "', '" . $newtime . "') as diff";
	$result = mysql_query($query);
	$result = mysql_fetch_assoc($result);
	$diffSTime = $result['diff'];
	echo "<td id='sTime'>more in " . $diffSTime . "</td>";
	echo "<td style='width:26px;'><a href='market.php?token=$access_token'><img src='assets/BTN_Gem.jpg'/></a></td>";
	 
}
else
	echo "<td></td>";
echo <<<_END
</tr>
</table>
<table class="menu">
_END;
echo "<tr><td class='menu'><a class='" . $style . "' href='index.php?token=$access_token'>Home</a></td>";
echo "<td class='menu'><a class='" . $style . "' href='missions.php?token=$access_token'>Missions</a></td>";
echo "<td class='menu'><a class='" . $style . "' href='duel.php?token=$access_token'>Fight</a></td>";
if ($skills <= 0)
echo "<td class='menu'><a class='" . $style . "' href='profile.php?token=$access_token'>Profile</a></td>";
else
echo "<td class='menu'><a class='" . $style . "' href='profile.php?token=$access_token'>Profile(<img src='assets/ProfileIcon.png'/>" . $skills . ")</a></td>";
echo "<td class='menu'><a class='" . $style . "' href='market.php?token=$access_token'>Market(<img src='assets/GemIcon" . $faction . ".png'/>" . $gems . ")</a></td>";
echo "<td class='menu'><a class='" . $style . "' href='inventory.php?token=$access_token'>Powers</a></td>";
echo "<td class='menu'><a class='" . $style . "' href='help.php?token=$access_token'>Help</a></td><td style='width:45px;'></td>";
echo <<<_END
</tr>
</table><br/><br/>
<div class="fightButtons1" style='top:263px;'>
<table style="top:8px; width:250px; text-align:center; position:absolute; left:245px;">
<tr><td><a href="duel.php?token=$access_token">DUELS</duel></td><td><a class="button" href="wars.php?token=$access_token">WARS</a></td></tr></table>
</div>
<div class='explain' style="color:black; background-image:url('assets/Box_DuelDescription.png'); height:112px;"><div style="color: orange; width: 744px; top:13px; left:0px; position:absolute;">FACTION WARS</div><br/><br/>Combat will help you to grow stronger. Use stamina to lend your strength to your gem god and gain experience. The Chosen of the winning faction will learn powers unique to that gem god. There is an ultimate power for each gem god, can you collect all 5?
</div>
<div style="position: absolute; left: 0px; width: 760px; background-image: url('assets/WhiteStripe.jpg'); top: 253px; height: 4px;" class="whitestripe"> 
</div>
_END;
if($showResult == 1)
{
echo "<div class='warResults' style='top:308px; color:black;'>";
if($levelUp == 1)
{
echo "<div style='position:absolute; left:293px; top:17px;'><img src='assets/Icon_LevelUp.png'/></div>";
echo "<div style='position:absolute; left:370px; top:16px; width:182px;'>Spend your skill points on the <a href='profile.php?token=$access_token' class='faction'>profile page</a> and see how much stronger you have become.";

echo "</div>";
}

if($hasFought == 1)
{
echo "<div style='position:absolute;left:20px; top:24px; width:260px;'>You have done the right thing and supported your gem god.  " . $godName[$faction] . " has rewarded you with " . $expWon . " experience.<br/>";
echo <<<_END
<a class='faction' href='#' onclick="FB.ui(
   {
    method: 'feed',
    link: 'http://apps.facebook.com/crimsonwinter',
    picture: 'http://www.firstagestudios.com/Ldd49cEsTcwfb/assets/FactionIcon$faction.jpg',
    source: 'http://www.firstagestudios.com/Ldd49cEsTcwfb/assets/FactionIcon$faction.jpg',
     name: 'Help turn the tides',
     caption: 'Crimson Winter',
     description: 'I\'ve supported $godName[$faction] today, but we can use all the help we can get!  If we win, we both have a chance for great prizes.'

   }
  );">Ask for backup</a>
</div>
_END;
echo $dropMsg;
}
if($error == 1)
	echo $errorMsg . "<br/><br/>";
echo "</div>";
echo "<div class='factionwars' style='top:418px;'>";
if($win2 <= 5)
{
	if($win1 != $win2)
		echo "<div class='yesterday' style='text-align:center;'><div style='position:absolute; top:5px; width:100%; left:0px; color:orange;'>YESTERDAY</div><div style='position:absolute; top:25px; left:5px; width:120px; text-align:center;'><img src='assets/FactionIcon" . $win1 . ".jpg' /></div><div style='position:absolute; top:94px; left:8px; text-align:center; width:120px;'>" . $godName[$win1] . " defeated " . $godName[$lose1] . ".</div><div style='position:absolute; top:25px; left:132px; width:150px; text-align:center;'><img src='assets/FactionIcon" . $win2 . ".jpg' /></div><div style='position:absolute; top:94px; left:132px; text-align:center; width:150px;'>".   $godName[$win2] . " defeated " . $godName[$lose2] . " and " . $godName[$lose3] . ".</div></div>";
	else
		echo "<div class='yesterday' style='text-align:center;'><div style='position:absolute; top:5px; width:100%; left:0px; color:orange;'>YESTERDAY</div><div style='position:absolute; top:25px; left:5px; width:120px; text-align:center;'><img src='assets/FactionIcon" . $win1 . ".jpg' /></div><div style='position:absolute; top:94px; left:8px; text-align:center; width:120px;'>" . $godName[$win1] . " came out on top.</div></div>";
}
else
{
	if($win1 != $win2 && $day != 206)
	{
		echo "<div class='yesterday' style='text-align:center;'><div style='position:absolute; top:5px; width:100%; left:0px; color:orange;'>YESTERDAY</div><div style='position:absolute; top:25px; left:5px; width:120px; text-align:center;'><img src='assets/FactionIcon" . $win1 . ".jpg' /></div><div style='position:absolute; top:94px; left:8px; text-align:center; width:120px;'>" . $godName[$win1] . " defeated " . $godName[$lose1] . ".</div><div style='position:absolute; top:25px; left:132px; width:150px; text-align:center;'>";
		switch($win2)
		{
		case 6:
		echo "<img src='assets/FactionIcon" . SAPPHIROS . ".jpg' />";
		echo "<img src='assets/FactionIcon" . RUBIA . ".jpg' />";
		break;
		case 7:
		echo "<img src='assets/FactionIcon" . SAPPHIROS . ".jpg' />";
		echo "<img src='assets/FactionIcon" . DAMOS . ".jpg' />";
		break;
		case 8:
		echo "<img src='assets/FactionIcon" . SAPPHIROS . ".jpg' />";
		echo "<img src='assets/FactionIcon" . MACHALITE . ".jpg' />";
		break;
		case 9:
		echo "<img src='assets/FactionIcon" . RUBIA . ".jpg' />";
		echo "<img src='assets/FactionIcon" . DAMOS . ".jpg' />";
		break;
		case 10:
		echo "<img src='assets/FactionIcon" . RUBIA . ".jpg' />";
		echo "<img src='assets/FactionIcon" . MACHALITE . ".jpg' />";
		break;
		case 11:
		echo "<img src='assets/FactionIcon" . DAMOS . ".jpg' />";
		echo "<img src='assets/FactionIcon" . MACHALITE . ".jpg' />";
		break;
		}
		echo "</div><div style='position:absolute; top:94px; left:132px; text-align:center; width:150px;'>".   $godName[$win2] . " defeated " . $godName[$lose3] . ".</div></div>";
	}
	else if($win1 == $win2)
		echo "<div class='yesterday' style='text-align:center;'><div style='position:absolute; top:5px; width:100%; left:0px; color:orange;'>YESTERDAY</div><div style='position:absolute; top:25px; left:5px; width:120px; text-align:center;'><img src='assets/FactionIcon" . $win1 . ".jpg' /></div><div style='position:absolute; top:94px; left:8px; text-align:center; width:120px;'>" . $godName[$win1] . " came out on top.</div></div>";
	else
	{
				echo "<div class='yesterday' style='text-align:center;'><div style='position:absolute; top:5px; width:100%; left:0px; color:orange;'>YESTERDAY</div><div style='position:absolute; top:25px; left:5px; width:120px; text-align:center;'><img src='assets/FactionIcon" . SAPPHIROS . ".jpg' /><img src='assets/FactionIcon" . RUBIA . ".jpg' /><img src='assets/FactionIcon" . DAMOS . ".jpg' /><img src='assets/FactionIcon" . MACHALITE . ".jpg' /></div><div style='position:absolute; top:94px; left:8px; text-align:center; width:120px;'>Sapphiros, Rubia, Damos, and Machalite defeated Jedeite!</div></div>";
	}
	
}
echo "<div class='today' style='width:442px;'>";
switch($day)
{
	case 0:
		$fighter1 = "Sapphiros";
		$fighter2 = "Rubia";
		$fighter3 = "Jedeite";
		$fighter4 = "Damos";
		$fighter5 = "Machalite";
	break;
	case 1:
		$fighter3 = "Sapphiros";
		$fighter4 = "Rubia";
		$fighter1 = "Jedeite";
		$fighter2 = "Damos";
		$fighter5 = "Machalite";
	break;
	case 2:
		$fighter1 = "Sapphiros";
		$fighter5 = "Rubia";
		$fighter3 = "Jedeite";
		$fighter4 = "Damos";
		$fighter2 = "Machalite";
	break;
	case 3:
		$fighter3 = "Sapphiros";
		$fighter2 = "Rubia";
		$fighter1 = "Jedeite";
		$fighter4 = "Damos";
		$fighter5 = "Machalite";
	break;
	case 4:
		$fighter3 = "Sapphiros";
		$fighter4 = "Rubia";
		$fighter5 = "Jedeite";
		$fighter1 = "Damos";
		$fighter2 = "Machalite";
	break;
	case 5:
		$fighter2 = "Sapphiros";
		$fighter3 = "Rubia";
		$fighter1 = "Jedeite";
		$fighter5 = "Damos";
		$fighter4 = "Machalite";
	break;
	case 6:
		$fighter5 = "Sapphiros";
		$fighter2 = "Rubia";
		$fighter3 = "Jedeite";
		$fighter4 = "Damos";
		$fighter1 = "Machalite";
	break;
	case 7:
		$fighter1 = "Sapphiros";
		$fighter4 = "Rubia";
		$fighter3 = "Jedeite";
		$fighter2 = "Damos";
		$fighter5 = "Machalite";
	break;
	case 8:
		$fighter3 = "Sapphiros";
		$fighter5 = "Rubia";
		$fighter1 = "Jedeite";
		$fighter4 = "Damos";
		$fighter2 = "Machalite";
	break;
	case 9:
		$fighter4 = "Sapphiros";
		$fighter1 = "Rubia";
		$fighter3 = "Jedeite";
		$fighter2 = "Damos";
		$fighter5 = "Machalite";
	break;

	case 200:
		$fighter1 = "Sapphiros";
		$fighter2 = "Rubia";
		$fighter3 = "Jedeite";
		$fighter4 = "Damos";
		$fighter5 = "Machalite";
	break;
	case 201:
		$fighter4 = "Sapphiros";
		$fighter5 = "Rubia";
		$fighter3 = "Jedeite";
		$fighter1 = "Damos";
		$fighter2 = "Machalite";
	break;
	case 202:
		$fighter1 = "Sapphiros";
		$fighter5 = "Rubia";
		$fighter3 = "Jedeite";
		$fighter2 = "Damos";
		$fighter4 = "Machalite";
	break;
	case 203:
		$fighter5 = "Sapphiros";
		$fighter1 = "Rubia";
		$fighter3 = "Jedeite";
		$fighter4 = "Damos";
		$fighter2 = "Machalite";
	break;
	case 204:
		$fighter2 = "Sapphiros";
		$fighter5 = "Rubia";
		$fighter3 = "Jedeite";
		$fighter4 = "Damos";
		$fighter1 = "Machalite";
	break;
	case 205:
		$fighter4 = "Sapphiros";
		$fighter2 = "Rubia";
		$fighter3 = "Jedeite";
		$fighter1 = "Damos";
		$fighter5 = "Machalite";
	break;
	case 206:
		$fighter1 = "Sapphiros";
		$fighter2 = "Rubia";
		$fighter3 = "Jedeite";
		$fighter4 = "Damos";
		$fighter5 = "Machalite";
	break;
}


if($day < 100)
{
echo <<<_END
<div style='position:absolute; top:5px; width:100%; left:0px; color:orange;'>TODAY</div><div style='position:absolute; top:35px; left:8px; text-align:left; width:145px;'> $fighter1 vs $fighter2!!<br/><br/> $fighter3 vs $fighter4 vs $fighter5!!</div><div style='position:absolute; top:35px; left:165px;'><img src='assets/ImageGem.png'/></div></div>
_END;
}
else if($day >= 200 && $day < 206)
{
echo <<<_END
<div style='position:absolute; top:5px; width:100%; left:0px; color:orange;'>TODAY</div><div style='position:absolute; top:35px; left:8px; text-align:left; width:145px;'> $fighter1 vs $fighter2!!<br/><br/> $fighter3 vs TEAM $fighter4/$fighter5!!</div><div style='position:absolute; top:35px; left:165px;'><img src='assets/ImageGem.png'/></div></div>
_END;
}
else if($day == 206)
{
echo <<<_END
<div style='position:absolute; top:5px; width:100%; left:0px; color:orange;'>TODAY</div><div style='position:absolute; top:35px; left:8px; text-align:left; width:145px;'> $fighter3 vs TEAM $fighter2/$fighter1/<br/><br/>$fighter4/$fighter5!!</div><div style='position:absolute; top:35px; left:165px;'><img src='assets/ImageGem.png'/></div></div>
_END;
}
else
{
echo <<<_END
<div style='position:absolute; top:5px; width:100%; left:0px; color:orange;'>TODAY</div><div style='position:absolute; top:35px; left:8px; text-align:left; width:145px;'> ALL CIVILITIES SET ASIDE!<br/><br/>Every team for itself!</div><div style='position:absolute; top:35px; left:165px;'><img src='assets/ImageGem.png'/></div></div>
_END;
}
echo <<<_END
<div class='warStats' style='top:176px;'><div style='position:absolute; left:0px; width:100%; text-align:center; color:orange;'>GEM GOD STRENGTH</div>
_END;
echo "<div style='position:absolute; top:40px; left:0px; width:86px;'>SAPPHIROS</div><div style='position:absolute; left:90px; top:40px;'>" . $supporters[SAPPHIROS] . "%</div><div class='sapBar' style='width:" . $supportBar[SAPPHIROS] . "px; color:" . $supportSapphiros . ";'></div>";
echo "<div style='position:absolute; top:74px; left:0px; width:86px;'>RUBIA</div><div style='position:absolute; left:90px; top:74px;'>" . $supporters[RUBIA] . "%</div><div class='rubBar' style='width:" . $supportBar[RUBIA] . "px; color:" . $supportRubia . ";'></div>";
echo "<div style='position:absolute; top:108px; left:0px; width:86px;'>JEDEITE</div><div style='position:absolute; left:90px; top:108px;'>" . $supporters[JEDEITE] . "%</div><div class='jedBar' style='width:" . $supportBar[JEDEITE] . "px; color:" . $supportJedeite . ";'></div>";
echo "<div style='position:absolute; top:142px; left:0px; width:86px;'>DAMOS</div><div style='position:absolute; left:90px; top:142px;'>" . $supporters[DAMOS] . "%</div><div class='damBar' style='width:" . $supportBar[DAMOS] . "px; '></div>";
echo "<div style='position:absolute; top:176px; left:0px; width:86px;'>MACHALITE</div><div style='position:absolute; left:90px; top:176px;'>" . $supporters[MACHALITE] . "%</div><div class='macBar' style='width:" . $supportBar[MACHALITE] . "px; color:" . $supportMachalite . ";'></div>";
echo <<<_END
</div>
<div class='warTrack' style='top:176px;'><div style='position:absolute; left:0px; width:100%; text-align:center; color:orange;'>7 DAY TREND</div>
_END;
echo "<div style='position:absolute; top:40px; left:8px; width:86px;'>SAPPHIROS</div><div style='position:absolute; left:95px; top:40px;'>" . $trackBar[SAPPHIROS] . "</div><div class='sapBar' style='width:" . $trackWidth[SAPPHIROS] . "px; color:" . $trackSapphiros . ";'></div>";
echo "<div style='position:absolute; top:74px; left:8px; width:86px;'>RUBIA</div><div style='position:absolute; left:95px; top:74px;'>" . $trackBar[RUBIA] . "</div><div class='rubBar' style='width:" . $trackWidth[RUBIA] . "px; color:" . $trackRubia . ";'></div>";
echo "<div style='position:absolute; top:108px; left:8px; width:86px;'>JEDEITE</div><div style='position:absolute; left:95px; top:108px;'>" . $trackBar[JEDEITE] . "</div><div class='jedBar' style='width:" . $trackWidth[JEDEITE] . "px; color:" . $trackJedeite . ";'></div>";
echo "<div style='position:absolute; top:142px; left:8px; width:86px;'>DAMOS</div><div style='position:absolute; left:95px; top:142px;'>" . $trackBar[DAMOS] . "</div><div class='damBar' style='width:" . $trackWidth[DAMOS] . "px;'></div>";
echo "<div style='position:absolute; top:176px; left:8px; width:86px;'>MACHALITE</div><div style='position:absolute; left:95px; top:176px;'>" . $trackBar[MACHALITE] . "</div><div class='macBar' style='width:" . $trackWidth[MACHALITE] . "px; color:" . $trackMachalite . ";'></div>";
echo <<<_END
</div>

<div class="support" style='text-align:center;'>
<div style='position:absolute; top:24px; left:0px;'>Support $godName[$faction] <br/>($cost Stamina)</div>  <div style='position:absolute; top:61px; left:22px;'><form method="post" action="wars.php">
	<input type="hidden" name="posted" value="yes"/><input type="hidden" name="token" value="$access_token"/>
	<input type='image' src='assets/BTN_Go.png' value='FIGHT'/></form></div>
</div>
</div>
<div style='position:absolute; top:860px; left:520px; font-size:0.75em;'>
<table style='text-align:center;'>
<tr>
<td style='width:120px;'>
<a 
_END;
if($faction == DAMOS)
	echo " class='button'";
echo <<<_END
 href='termsofservice.html'>Terms of Service</a></td>
<td style='width:120px;'>
<a 
_END;
if($faction == DAMOS)
	echo " class='button'";
echo <<<_END
 href='privacypolicy.html'>Privacy Policy</a></td>
</tr>
</table>
</div>
_END;
}
else
{
echo "<div class='factionwars' style='top:308px;'>";
if($win2 <= 5)
{
	if($win1 != $win2)
		echo "<div class='yesterday' style='text-align:center;'><div style='position:absolute; top:5px; width:100%; left:0px; color:orange;'>YESTERDAY</div><div style='position:absolute; top:25px; left:5px; width:120px; text-align:center;'><img src='assets/FactionIcon" . $win1 . ".jpg' /></div><div style='position:absolute; top:94px; left:8px; text-align:center; width:120px;'>" . $godName[$win1] . " defeated " . $godName[$lose1] . ".</div><div style='position:absolute; top:25px; left:132px; width:150px; text-align:center;'><img src='assets/FactionIcon" . $win2 . ".jpg' /></div><div style='position:absolute; top:94px; left:132px; text-align:center; width:150px;'>".   $godName[$win2] . " defeated " . $godName[$lose2] . " and " . $godName[$lose3] . ".</div></div>";
	else
		echo "<div class='yesterday' style='text-align:center;'><div style='position:absolute; top:5px; width:100%; left:0px; color:orange;'>YESTERDAY</div><div style='position:absolute; top:25px; left:5px; width:120px; text-align:center;'><img src='assets/FactionIcon" . $win1 . ".jpg' /></div><div style='position:absolute; top:94px; left:8px; text-align:center; width:120px;'>" . $godName[$win1] . " came out on top.</div></div>";
}
else
{
	if($win1 != $win2 && $day != 206)
	{
		echo "<div class='yesterday' style='text-align:center;'><div style='position:absolute; top:5px; width:100%; left:0px; color:orange;'>YESTERDAY</div><div style='position:absolute; top:25px; left:5px; width:120px; text-align:center;'><img src='assets/FactionIcon" . $win1 . ".jpg' /></div><div style='position:absolute; top:94px; left:8px; text-align:center; width:120px;'>" . $godName[$win1] . " defeated " . $godName[$lose1] . ".</div><div style='position:absolute; top:25px; left:132px; width:150px; text-align:center;'>";
		switch($win2)
		{
		case 6:
		echo "<img src='assets/FactionIcon" . SAPPHIROS . ".jpg' />";
		echo "<img src='assets/FactionIcon" . RUBIA . ".jpg' />";
		break;
		case 7:
		echo "<img src='assets/FactionIcon" . SAPPHIROS . ".jpg' />";
		echo "<img src='assets/FactionIcon" . DAMOS . ".jpg' />";
		break;
		case 8:
		echo "<img src='assets/FactionIcon" . SAPPHIROS . ".jpg' />";
		echo "<img src='assets/FactionIcon" . MACHALITE . ".jpg' />";
		break;
		case 9:
		echo "<img src='assets/FactionIcon" . RUBIA . ".jpg' />";
		echo "<img src='assets/FactionIcon" . DAMOS . ".jpg' />";
		break;
		case 10:
		echo "<img src='assets/FactionIcon" . RUBIA . ".jpg' />";
		echo "<img src='assets/FactionIcon" . MACHALITE . ".jpg' />";
		break;
		case 11:
		echo "<img src='assets/FactionIcon" . DAMOS . ".jpg' />";
		echo "<img src='assets/FactionIcon" . MACHALITE . ".jpg' />";
		break;
		}
		echo "</div><div style='position:absolute; top:94px; left:132px; text-align:center; width:150px;'>".   $godName[$win2] . " defeated " . $godName[$lose3] . ".</div></div>";
	}
	else if($win1 == $win2)
		echo "<div class='yesterday' style='text-align:center;'><div style='position:absolute; top:5px; width:100%; left:0px; color:orange;'>YESTERDAY</div><div style='position:absolute; top:25px; left:5px; width:120px; text-align:center;'><img src='assets/FactionIcon" . $win1 . ".jpg' /></div><div style='position:absolute; top:94px; left:8px; text-align:center; width:120px;'>" . $godName[$win1] . " came out on top.</div></div>";
	else
	{
				echo "<div class='yesterday' style='text-align:center;'><div style='position:absolute; top:5px; width:100%; left:0px; color:orange;'>YESTERDAY</div><div style='position:absolute; top:25px; left:5px; width:120px; text-align:center;'><img src='assets/FactionIcon" . SAPPHIROS . ".jpg' /><img src='assets/FactionIcon" . RUBIA . ".jpg' /><img src='assets/FactionIcon" . DAMOS . ".jpg' /><img src='assets/FactionIcon" . MACHALITE . ".jpg' /></div><div style='position:absolute; top:94px; left:8px; text-align:center; width:120px;'>Sapphiros, Rubia, Damos, and Machalite defeated Jedeite!</div></div>";
	}
	
}
echo "<div class='today' style='width:442px;'>";
switch($day)
{
	case 0:
		$fighter1 = "Sapphiros";
		$fighter2 = "Rubia";
		$fighter3 = "Jedeite";
		$fighter4 = "Damos";
		$fighter5 = "Machalite";
	break;
	case 1:
		$fighter3 = "Sapphiros";
		$fighter4 = "Rubia";
		$fighter1 = "Jedeite";
		$fighter2 = "Damos";
		$fighter5 = "Machalite";
	break;
	case 2:
		$fighter1 = "Sapphiros";
		$fighter5 = "Rubia";
		$fighter3 = "Jedeite";
		$fighter4 = "Damos";
		$fighter2 = "Machalite";
	break;
	case 3:
		$fighter3 = "Sapphiros";
		$fighter2 = "Rubia";
		$fighter1 = "Jedeite";
		$fighter4 = "Damos";
		$fighter5 = "Machalite";
	break;
	case 4:
		$fighter3 = "Sapphiros";
		$fighter4 = "Rubia";
		$fighter5 = "Jedeite";
		$fighter1 = "Damos";
		$fighter2 = "Machalite";
	break;
	case 5:
		$fighter2 = "Sapphiros";
		$fighter3 = "Rubia";
		$fighter1 = "Jedeite";
		$fighter5 = "Damos";
		$fighter4 = "Machalite";
	break;
	case 6:
		$fighter5 = "Sapphiros";
		$fighter2 = "Rubia";
		$fighter3 = "Jedeite";
		$fighter4 = "Damos";
		$fighter1 = "Machalite";
	break;
	case 7:
		$fighter1 = "Sapphiros";
		$fighter4 = "Rubia";
		$fighter3 = "Jedeite";
		$fighter2 = "Damos";
		$fighter5 = "Machalite";
	break;
	case 8:
		$fighter3 = "Sapphiros";
		$fighter5 = "Rubia";
		$fighter1 = "Jedeite";
		$fighter4 = "Damos";
		$fighter2 = "Machalite";
	break;
	case 9:
		$fighter4 = "Sapphiros";
		$fighter1 = "Rubia";
		$fighter3 = "Jedeite";
		$fighter2 = "Damos";
		$fighter5 = "Machalite";
	break;
	case 200:
		$fighter1 = "Sapphiros";
		$fighter2 = "Rubia";
		$fighter3 = "Jedeite";
		$fighter4 = "Damos";
		$fighter5 = "Machalite";
	break;
	case 201:
		$fighter4 = "Sapphiros";
		$fighter5 = "Rubia";
		$fighter3 = "Jedeite";
		$fighter1 = "Damos";
		$fighter2 = "Machalite";
	break;
	case 202:
		$fighter1 = "Sapphiros";
		$fighter5 = "Rubia";
		$fighter3 = "Jedeite";
		$fighter2 = "Damos";
		$fighter4 = "Machalite";
	break;
	case 203:
		$fighter5 = "Sapphiros";
		$fighter1 = "Rubia";
		$fighter3 = "Jedeite";
		$fighter4 = "Damos";
		$fighter2 = "Machalite";
	break;
	case 204:
		$fighter2 = "Sapphiros";
		$fighter5 = "Rubia";
		$fighter3 = "Jedeite";
		$fighter4 = "Damos";
		$fighter1 = "Machalite";
	break;
	case 205:
		$fighter4 = "Sapphiros";
		$fighter2 = "Rubia";
		$fighter3 = "Jedeite";
		$fighter1 = "Damos";
		$fighter5 = "Machalite";
	break;
	case 206:
		$fighter1 = "Sapphiros";
		$fighter2 = "Rubia";
		$fighter3 = "Jedeite";
		$fighter4 = "Damos";
		$fighter5 = "Machalite";
	break;
}



if($day < 100)
{
echo <<<_END
<div style='position:absolute; top:5px; width:100%; left:0px; color:orange;'>TODAY</div><div style='position:absolute; top:35px; left:8px; text-align:left; width:145px;'> $fighter1 vs $fighter2!!<br/><br/> $fighter3 vs $fighter4 vs $fighter5!!</div><div style='position:absolute; top:35px; left:165px;'><img src='assets/ImageGem.png'/></div></div>
_END;
}
else if($day >= 200 && $day < 206)
{
echo <<<_END
<div style='position:absolute; top:5px; width:100%; left:0px; color:orange;'>TODAY</div><div style='position:absolute; top:35px; left:8px; text-align:left; width:145px;'> $fighter1 vs $fighter2!!<br/><br/> $fighter3 vs TEAM $fighter4/$fighter5!!</div><div style='position:absolute; top:35px; left:165px;'><img src='assets/ImageGem.png'/></div></div>
_END;
}
else if($day == 206)
{
echo <<<_END
<div style='position:absolute; top:5px; width:100%; left:0px; color:orange;'>TODAY</div><div style='position:absolute; top:35px; left:8px; text-align:left; width:145px;'> $fighter3 vs TEAM $fighter2/$fighter1/<br/><br/>$fighter4/$fighter5!!</div><div style='position:absolute; top:35px; left:165px;'><img src='assets/ImageGem.png'/></div></div>
_END;
}
else
{
echo <<<_END
<div style='position:absolute; top:5px; width:100%; left:0px; color:orange;'>TODAY</div><div style='position:absolute; top:35px; left:8px; text-align:left; width:145px;'> ALL CIVILITIES SET ASIDE!<br/><br/>Every team for itself!</div><div style='position:absolute; top:35px; left:165px;'><img src='assets/ImageGem.png'/></div></div>
_END;
}
echo <<<_END
<div class='warStats' style='top:176px;'><div style='position:absolute; left:0px; width:100%; text-align:center; color:orange;'>GEM GOD STRENGTH</div>
_END;
echo "<div style='position:absolute; top:40px; left:0px; width:86px;'>SAPPHIROS</div><div style='position:absolute; left:90px; top:40px;'>" . $supporters[SAPPHIROS] . "%</div><div class='sapBar' style='width:" . $supportBar[SAPPHIROS] . "px; color:" . $supportSapphiros . ";'></div>";
echo "<div style='position:absolute; top:74px; left:0px; width:86px;'>RUBIA</div><div style='position:absolute; left:90px; top:74px;'>" . $supporters[RUBIA] . "%</div><div class='rubBar' style='width:" . $supportBar[RUBIA] . "px; color:" . $supportRubia . ";'></div>";
echo "<div style='position:absolute; top:108px; left:0px; width:86px;'>JEDEITE</div><div style='position:absolute; left:90px; top:108px;'>" . $supporters[JEDEITE] . "%</div><div class='jedBar' style='width:" . $supportBar[JEDEITE] . "px; color:" . $supportJedeite . ";'></div>";
echo "<div style='position:absolute; top:142px; left:0px; width:86px;'>DAMOS</div><div style='position:absolute; left:90px; top:142px;'>" . $supporters[DAMOS] . "%</div><div class='damBar' style='width:" . $supportBar[DAMOS] . "px; '></div>";
echo "<div style='position:absolute; top:176px; left:0px; width:86px;'>MACHALITE</div><div style='position:absolute; left:90px; top:176px;'>" . $supporters[MACHALITE] . "%</div><div class='macBar' style='width:" . $supportBar[MACHALITE] . "px; color:" . $supportMachalite . ";'></div>";
echo <<<_END
</div>
<div class='warTrack' style='top:176px;'><div style='position:absolute; left:0px; width:100%; text-align:center; color:orange;'>7 DAY TREND</div>
_END;
echo "<div style='position:absolute; top:40px; left:8px; width:86px;'>SAPPHIROS</div><div style='position:absolute; left:95px; top:40px;'>" . $trackBar[SAPPHIROS] . "</div><div class='sapBar' style='width:" . $trackWidth[SAPPHIROS] . "px; color:" . $trackSapphiros . ";'></div>";
echo "<div style='position:absolute; top:74px; left:8px; width:86px;'>RUBIA</div><div style='position:absolute; left:95px; top:74px;'>" . $trackBar[RUBIA] . "</div><div class='rubBar' style='width:" . $trackWidth[RUBIA] . "px; color:" . $trackRubia . ";'></div>";
echo "<div style='position:absolute; top:108px; left:8px; width:86px;'>JEDEITE</div><div style='position:absolute; left:95px; top:108px;'>" . $trackBar[JEDEITE] . "</div><div class='jedBar' style='width:" . $trackWidth[JEDEITE] . "px; color:" . $trackJedeite . ";'></div>";
echo "<div style='position:absolute; top:142px; left:8px; width:86px;'>DAMOS</div><div style='position:absolute; left:95px; top:142px;'>" . $trackBar[DAMOS] . "</div><div class='damBar' style='width:" . $trackWidth[DAMOS] . "px;'></div>";
echo "<div style='position:absolute; top:176px; left:8px; width:86px;'>MACHALITE</div><div style='position:absolute; left:95px; top:176px;'>" . $trackBar[MACHALITE] . "</div><div class='macBar' style='width:" . $trackWidth[MACHALITE] . "px; color:" . $trackMachalite . ";'></div>";
echo <<<_END
</div>

<div class="support" style='text-align:center;'>
<div style='position:absolute; top:24px; left:0px;'>Support $godName[$faction] <br/>($cost Stamina)</div>  <div style='position:absolute; top:61px; left:22px;'><form method="post" action="wars.php">
	<input type="hidden" name="posted" value="yes"/><input type="hidden" name="token" value="$access_token"/>
	<input type='image' src='assets/BTN_Go.png' value='FIGHT'/></form></div>
</div>
</div>
<div style='position:absolute; top:750px; left:520px; font-size:0.75em;'>
<table style='text-align:center;'>
<tr>
<td style='width:120px;'>
<a 
_END;
if($faction == DAMOS)
	echo " class='button'";
echo <<<_END
 href='termsofservice.html'>Terms of Service</a></td>
<td style='width:120px;'>
<a 
_END;
if($faction == DAMOS)
	echo " class='button'";
echo <<<_END
 href='privacypolicy.html'>Privacy Policy</a></td>
</tr>
</table>
</div>
_END;
}
echo <<<_END

<script>
var i = '$diffETime';
var eMin = i.charAt(4);
var eSec = i.substr(6);
var energy = $c_energy;

i = '$diffHTime';
var hMin = i.charAt(4);
var hSec = i.substr(6);
var health = $c_health;

i = '$diffSTime';
var sMin = i.charAt(4);
var sSec = i.substr(6);
var stamina = $c_stamina;
function PadDigits(n, totalDigits) 
    { 
        var s = n.toString(); 
        var pd = '';
        if (totalDigits > s.length) 
        { 
            for (i=0; i < (totalDigits - s.length); i++) 
            { 
                pd += '0'; 
            } 
        } 
        return pd + s.toString(); 
    };
function updateTimer()
{
if(energy < $m_energy)
{

sESec = PadDigits(eSec, 2);
var eTime = eMin + ':' + sESec;
document.getElementById('eTime').innerHTML = "more in " + eTime;
eSec--;

if(eSec < 0)
{
	eSec = 59;
	eMin--;
	if(eMin < 0)
	{
		energy++;
		document.getElementById('tagEnergy').innerHTML = "<a class='header'>" + energy + "</a> / " + $m_energy;
		if($faction == 1)
		{
			eMin = 2;
			eSec = 29;
		}
		else
		{
			eMin = 2;
			eSec = 59;
		}
		if(energy == $m_energy)
			document.getElementById('eTime').innerHTML = '';
	}
}

}

if(health < $m_health)
{
sHSec = PadDigits(hSec, 2);
var hTime = hMin + ':' + sHSec;
document.getElementById('hTime').innerHTML = "more in " + hTime;
hSec--;

if(hSec < 0)
{
	hSec = 59;
	hMin--;
	if(hMin < 0)
	{
		health++;
		document.getElementById('tagHealth').innerHTML = "<a class='header'>" + health + "</a> / " + $m_health;
		if($faction == 3)
		{
			hMin = 2;
		}
		else
		{
			hMin = 3;
		}
		if(health == $m_health)
			document.getElementById('hTime').innerHTML = '';
	}
}

}

if(stamina < $m_stamina)
{
sSSec = PadDigits(sSec, 2);
var sTime = sMin + ':' + sSSec;
document.getElementById('sTime').innerHTML = "more in " + sTime;
sSec--;

if(sSec < 0)
{
	sSec = 59;
	sMin--;
	if(sMin < 0)
	{
		stamina++;
		document.getElementById('tagStamina').innerHTML = "<a class='header'>" + stamina + "</a> / " + $m_stamina;
		if($faction == 5)
		{
			sMin = 2;
		}
		else
		{
			sMin = 3;
		}
		if(stamina == $m_stamina)
			document.getElementById('sTime').innerHTML = '';
	}
}

}
};

setInterval("updateTimer()", 1000);
updateTimer();
</script>
</body>
</html>
_END;
mysql_close($db_server);
?>
