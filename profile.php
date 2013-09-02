<?php
require_once 'src/facebook.php';
require_once 'db_vars.php';
require_once 'functions.php';
require_once 'cw_vars.php';

$app_id = APP_ID;
    $app_secret = APP_SECRET;
    $my_url = "http://apps.facebook.com/crimsonwinter/profile.php";
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

//user details
$rows = mysql_fetch_row($result);

$c_energy = $rows[1];
$m_energy = $rows[2];
$time = $rows[3];
$level = $rows[4];
$experience = $rows[5];
$krevels = $rows[6];
$bank = $rows[7];
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
$name = $rows[18];
$faction = $rows[19];
$fac_change = $rows[20];
$knights = $rows[23];
$fightsWon = $rows[24];
$fightsLost = $rows[25];

if($fightsWon + $fightsLost != 0)
$winPercent = (int)($fightsWon / ($fightsWon + $fightsLost) * 100);
else
$winPercent = 0;

$viewPower = $rows[28];
$played = $rows[29];


$viewKrevels = $krevels + $bank;

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
$knights_url = "https://graph.facebook.com/me/friends?" . $access_token;
$all_friends = json_decode(file_get_contents($knights_url));
$num_friends = count($all_friends->data);
$knights += num_friends;
for($i = 0; $i < $num_friends; $i++)
{
	$query = "SELECT level FROM CW_Users WHERE id='" . $all_friends->data[$i]->id . "'";
	$result = mysql_query($query);
	if(mysql_num_rows($result) == 1)
	{
		$myKnights[] = $all_friends->data[$i]->id;
	}
}
$numKnights = count($myKnights);

$knights += $numKnights;

$query = "SELECT achievement25 FROM CW_Achievements WHERE id='" . $uid . "'";
$result = mysql_query($query);
$rows = mysql_fetch_row($result);
$TeamPlayer = $rows[0];


$style='';

$viewID = $uid;
if(!empty($_REQUEST['view']))
{
	$view = SanitizeString($_REQUEST['view']);
	$query = "SELECT name, level, fightsWon, fightsLost, missionTotal, faction, krevels, bank, powerTotal FROM CW_Users WHERE id='" . $view . "'";
	$result = mysql_query($query);
	if(!$result)
		$error = 1;
	else
	{
		$row = mysql_fetch_row($result);
		$viewID = $view;
		$viewName = $row[0];
		$viewLevel = $row[1];
		$viewWon = $row[2];
		$viewLost = $row[3];
		$viewMission = $row[4];
		$viewFaction = $row[5];
		$viewKrevels = $row[6];
		$viewKrevels += $row[7];
		$viewPower = $row[8];
		
	}
}
if($name == '')
	$name = "Unnamed Chosen";
if($viewName == '')
	$viewName = "Unnamed Chosen";

$query = "SELECT * FROM CW_Inventory WHERE id='" . $viewID . "'";//for real game, add WHERE to narrow it down to fb uid
$result = mysql_query($query);

if(!$result) die("Database access failed: " . mysql_error());
$rows = mysql_fetch_row($result);

for($i = 1; $i < mysql_num_fields($result); $i++)
{
	$powers[] = $rows[$i];
}

$query = "SELECT name, attack, defence, type, id FROM CW_Items ORDER BY type, id";//for real game, add WHERE to narrow it down to fb uid
$result = mysql_query($query);

if(!$result) die("Database access failed: " . mysql_error());


$rows = mysql_num_rows($result);

for($i = 0; $i < $rows; $i++)
{
	$row = mysql_fetch_row($result);
	$powerName[] = $row[0];
	$powerAttack[] = $row[1];
	$powerDefence[] = $row[2];
	$powerType[] = $row[3];
	$powerID[] = $row[4];
}
//necessary to line up inventory with sorted power list
$fillerArray = $powers;

for($i = 0; $i < count($powers); $i++)
{
	for($j = 0; $j < count($powerID); $j++)
	{
		
			if($i + 1 == $powerID[$j])
			{
				
				$powers[$j] = $fillerArray[$i];
				
			}//end if match

	}//end count id
}//end count powers

$query = "SELECT jTier1, jTier2, jTier3, jTier4, jTier5, jTier6, jTier7, jTier8, jTier9, jTier10 FROM CW_Missions WHERE id='" . $viewID . "'";
$result = mysql_query($query);
$rows = mysql_fetch_row($result);
$Gold[SAPPHIROS] = $rows[4];
$Gold[RUBIA] = $rows[0];
$Gold[JEDEITE] = $rows[3];
$Gold[DAMOS] = $rows[2];
$Gold[MACHALITE] = $rows[1];
$Gold2[SAPPHIROS] = $rows[5];
$Gold2[RUBIA] = $rows[8];
$Gold2[JEDEITE] = $rows[9];
$Gold2[DAMOS] = $rows[7];
$Gold2[MACHALITE] = $rows[6];

$query = "SELECT * FROM CW_Achievements WHERE id='" . $viewID . "'";
$result = mysql_query($query);
$rows = mysql_fetch_row($result);
for($i = 1; $i < count($rows); $i++)
{
$achEarned[] = $rows[$i];
}



//Top attack and defence of the current profile
$atkName = '';
$defName = '';
$utilName = '';
$topOFF = 0;
$topDEF = 0;
$topUTIL = 0;
$tempDef = 0;
$topOffID = -1;
$topDefID = -1;
$topUtilID = -1;

	
	for($i = 0; $i < count($powers); $i++)
	{
		
		
			switch($powerType[$i])
			{
				case 0:
					if($powers[$i] > 0)
					{
						if($powerAttack[$i] > $tempTopOFF)
						{
						$tempTopOFF = $powerAttack[$i];
						if($k == 0)//only use first one for the top attack
						$atkName = $powerName[$i];
						$tempDef = $powerDefence[$i];
						$topOffID = $powerID[$i];
						}
						else if($powerAttack[$i] == $tempTopOFF)
						{
							if($powerDefence[$i] > $tempDef)
							{
								$tempTopOFF = $powerAttack[$i];
								if($k == 0)//only use first one for the top attack
								$atkName = $powerName[$i];
								$tempDef = $powerDefence[$i];
								$topOffID = $powerID[$i];
							}
						}
						
						
					}
					
					break;
				case 1:
					if($powerType[($i - 1)] != 1)
						$tempDef = 0;
					if($powers[$i] > 0)
					{
						if($powerAttack[$i] > $tempTopDEF)
						{
						$tempTopDEF = $powerAttack[$i];
						if($k == 0)//only use first one for the top attack
						$defName = $powerName[$i];
						$tempDef = $powerDefence[$i];
						$topDefID = $powerID[$i];
						}
						else if($powerAttack[$i] == $tempTopDEF)
						{
							if($powerDefence[$i] > $tempDef)
							{
								$tempTopDEF = $powerAttack[$i];
								if($k == 0)//only use first one for the top attack
								$defName = $powerName[$i];
								$tempDef = $powerDefence[$i];
								$topDefID = $powerID[$i];
							}
						}
						
					}
					
					break;
				case 2:
					if($powerType[($i - 1)] != 2)
							$tempDef = 0;
					if($powers[$i] > 0)
					{
						if($powerAttack[$i] > $tempTopUTIL)
						{
						$tempTopUTIL = $powerAttack[$i];
						if($k == 0)//only use first one for the top attack
						$utilName = $powerName[$i];
						$tempDef = $powerDefence[$i];
						$topUtilID = $powerID[$i];
						}
						else if($powerAttack[$i] == $tempTopUTIL)
						{
							if($powerDefence[$i] > $tempDef)
							{
								$tempTopUTIL = $powerAttack[$i];
								if($k == 0)//only use first one for the top attack
								$utilName = $powerName[$i];
								$tempDef = $powerDefence[$i];
								$topUtilID = $powerID[$i];
							}
						}
					}
					
					
					
					break;
				default:
					break;
			
			
		}
		
	
	}

		
		$dAtkName = '';
$dDefName = '';
$dUtilName = '';
		
			$dTopOffID = -1;
			$dTopDefID = -1;
			$dTopUtilID = -1;
			$tempAtk = 0;
			$tempTopOFF = 0;
			$tempTopDEF = 0;
			$tempTopUTIL = 0;					
			for($i = 0; $i < count($powers); $i++)
			{
				
				
					switch($powerType[$i])
					{
						case 0:
							if($powers[$i] > 0)
							{
								if($powerDefence[$i] > $tempTopOFF)
								{
								$tempTopOFF = $powerDefence[$i];
								if($k == 0)
								$dAtkName = $powerName[$i];
								$tempAtk = $powerAttack[$i];
								$dTopOffID = $powerID[$i];
								}
								else if($powerDefence[$i] == $tempTopOFF)
								{
									if($powerAttack[$i] > $tempAtk)
									{
										$tempTopOFF = $powerDefence[$i];
										if($k == 0)
										$dAtkName = $powerName[$i];
										$tempAtk = $powerAttack[$i];
										$dTopOffID = $powerID[$i];
									}
								}
								
								
								
							}
							
							break;
						case 1:
							if($powerType[($i - 1)] != 1)
									$tempAtk = 0;
							if($powers[$i] > 0)
							{
								if($powerDefence[$i] > $tempTopDEF)
								{
								$tempTopDEF = $powerDefence[$i];
								if($k == 0)
								$dDefName = $powerName[$i];
								$tempAtk = $powerAttack[$i];
								$dTopDefID = $powerID[$i];
								}
								else if($powerDefence[$i] == $tempTopDEF)
								{
									if($powerAttack[$i] > $tempAtk)
									{
										$tempTopDEF = $powerDefence[$i];
										if($k == 0)
										$dDefName = $powerName[$i];
										$tempAtk = $powerAttack[$i];
										$dTopDefID = $powerID[$i];
									}
								}
								
							}
							break;
						case 2:
							if($powerType[($i - 1)] != 2)
									$tempAtk = 0;
							if($powers[$i] > 0)
							{
								if($powerDefence[$i] > $tempTopUTIL)
								{
								$tempTopUTIL = $powerDefence[$i];
								if($k == 0)
								$dUtilName = $powerName[$i];
								$tempAtk = $powerAttack[$i];
								$dTopUtilID = $powerID[$i];
								}
								else if($powerDefence[$i] == $tempTopUTIL)
								{
									if($powerAttack[$i] > $tempAtk)
									{
										$tempTopUTIL = $powerDefence[$i];
										if($k == 0)
										$dUtilName = $powerName[$i];
										$tempAtk = $powerAttack[$i];
										$dTopUtilID = $powerID[$i];
									}
								}
							}
							
							
							
							break;
						default:
							break;
					
					
				}
				
			
			}
			
		



if($_POST[posted] == "yes")
{
	$_POST[posted] = "no";
	if($skills > 0)
	{
		$action = $_POST[action];
		switch($action)
		{
		case "energy":
			$c_energy += 1;
			$m_energy += 1;
			$skills -= 1;
			break;
		case "attack":
			$attack += 1;
			$skills -= 1;
			break;
		case "defence":
			$defence += 1;
			$skills -= 1;
			break;
		case "stamina":
			$c_stamina += 1;
			$m_stamina += 1;
			$skills -= 1;
			break;
		case "health":
			$c_health += 5;
			$m_health += 5;
			$skills -= 1;
			break;
		case "energy5":
			if($skills >= 5)
			{
			$c_energy += 5;
			$m_energy += 5;
			$skills -= 5;
			}
			break;
		case "attack5":
			if($skills >= 5)
			{
			$attack += 5;
			$skills -= 5;
			}
			break;
		case "defence5":
			if($skills >= 5)
			{
			$defence += 5;
			$skills -= 5;
			}
			break;
		case "stamina5":
			if($skills >= 5)
			{
			$c_stamina += 5;
			$m_stamina += 5;
			$skills -= 5;
			}
			break;
		case "health5":
			if($skills >= 5)
			{
			$c_health += 25;
			$m_health += 25;
			$skills -= 5;
			}
			break;
			
		default:
			break;
		}
		$query = "UPDATE CW_Users SET skills='" . $skills . "',  c_energy='" . $c_energy . "', m_energy='" . $m_energy . "', attack='" . $attack . "', defence='" . $defence . "', c_stamina='" . $c_stamina . "', m_stamina='" . $m_stamina . "', c_health='" . $c_health . "', m_health='" . $m_health . "' WHERE id='" . $uid . "'";
		$result = mysql_query($query);
	}
	
}
else if($_POST[posted] == "change")
{
	$_POST[posted] = "no";
	if(!$fac_change)
		echo "You may only do that once per level.";
	else if($_POST[action] == $faction)
		echo "While your current Gem God is proud of your faith, you may only faction change to a different faction.";
	else
	{
		$fac_change = 0;
		switch($_POST[action])
		{
			case "sapphiros":
				if($faction == JEDEITE)
				{
					$query = "UPDATE CW_Users SET hTime='" . $hTime. "' + INTERVAL 1 MINUTE WHERE id='" . $uid . "'";
					$result = mysql_query($query);
				}
				else if($faction == MACHALITE)
				{
					$query = "UPDATE CW_Users SET sTime='" . $sTime. "' + INTERVAL 1 MINUTE WHERE id='" . $uid . "'";
					$result = mysql_query($query);
				}
				
				$faction = SAPPHIROS;
				$query = "UPDATE CW_Users SET time='" . $time. "' - INTERVAL 30 SECOND WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$query = "UPDATE CW_Users set fac_change='" . $fac_change . "', faction='" . $faction . "' WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				break;
			case "rubia":
				if($faction == SAPPHIROS)
				{
					$query = "UPDATE CW_Users SET time='" . $time. "' + INTERVAL 30 SECOND WHERE id='" . $uid . "'";
					$result = mysql_query($query);
				}
				else if($faction == JEDEITE)
				{
					$query = "UPDATE CW_Users SET hTime='" . $hTime. "' + INTERVAL 1 MINUTE WHERE id='" . $uid . "'";
					$result = mysql_query($query);
				}
				else if($faction == MACHALITE)
				{
					$query = "UPDATE CW_Users SET sTime='" . $sTime. "' + INTERVAL 1 MINUTE WHERE id='" . $uid . "'";
					$result = mysql_query($query);
				}
				$faction = RUBIA;
				$query = "UPDATE CW_Users set fac_change='" . $fac_change . "', faction='" . $faction . "' WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				break;
			case "jedeite":
				if($faction == SAPPHIROS)
				{
					$query = "UPDATE CW_Users SET time='" . $time. "' + INTERVAL 30 SECOND WHERE id='" . $uid . "'";
					$result = mysql_query($query);
				}
				else if($faction == MACHALITE)
				{
					$query = "UPDATE CW_Users SET sTime='" . $sTime. "' + INTERVAL 1 MINUTE WHERE id='" . $uid . "'";
					$result = mysql_query($query);
				}
				$faction = JEDEITE;
				$query = "UPDATE CW_Users SET hTime='" . $hTime. "' - INTERVAL 1 MINUTE WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$query = "UPDATE CW_Users set fac_change='" . $fac_change . "', faction='" . $faction . "' WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				break;
			case "damos":
				if($faction == SAPPHIROS)
				{
					$query = "UPDATE CW_Users SET time='" . $time. "' + INTERVAL 30 SECOND WHERE id='" . $uid . "'";
					$result = mysql_query($query);
				}
				else if($faction == JEDEITE)
				{
					$query = "UPDATE CW_Users SET hTime='" . $hTime. "' + INTERVAL 1 MINUTE WHERE id='" . $uid . "'";
					$result = mysql_query($query);
				}
				else if($faction == MACHALITE)
				{
					$query = "UPDATE CW_Users SET sTime='" . $sTime. "' + INTERVAL 1 MINUTE WHERE id='" . $uid . "'";
					$result = mysql_query($query);
				}
				$faction = DAMOS;
				$query = "UPDATE CW_Users set fac_change='" . $fac_change . "', faction='" . $faction . "' WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				break;
			case "machalite":
				if($faction == SAPPHIROS)
				{
					$query = "UPDATE CW_Users SET time='" . $time. "' + INTERVAL 30 SECOND WHERE id='" . $uid . "'";
					$result = mysql_query($query);
				}
				else if($faction == JEDEITE)
				{
					$query = "UPDATE CW_Users SET hTime='" . $hTime. "' + INTERVAL 1 MINUTE WHERE id='" . $uid . "'";
					$result = mysql_query($query);
				}
				$faction = MACHALITE;
				$query = "UPDATE CW_Users SET sTime='" . $sTime. "' - INTERVAL 1 MINUTE WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$query = "UPDATE CW_Users set fac_change='" . $fac_change . "', faction='" . $faction . "' WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				break;
			default:
				echo "Error: chosen gem god does not exist.";
				break;
		}
		if($TeamPlayer > 0)
		{
			$newFaction = 1;
			for($a = 0; $a < strlen($TeamPlayer); $a++)
			{
				if(substr($TeamPlayer, $a, 1) == $faction)
				{
					$newFaction = 0;
					break;
				}
			}
			if($newFaction == 1)
			{
				
				$TeamPlayer .= $faction;
				
				if(strlen($TeamPlayer) == 5)
				{
					$TeamPlayer = -1;
					$skills++;
					$query = "UPDATE CW_Users SET skills='" . $skills . "' WHERE id='" . $uid . "'";
					$result = mysql_query($query);
$displayAchievement = 25;
$achName = "Team Player";		
				}
				$query = "UPDATE CW_Achievements SET achievement25='" . $TeamPlayer . "' WHERE id='" . $uid . "'";
				$result = mysql_query($query);
$achEarned[24] = $TeamPlayer;
			}
		}
		else if($TeamPlayer == 0)
		{
			$TeamPlayer = $faction;
			$query = "UPDATE CW_Achievements SET achievement25='" . $TeamPlayer . "' WHERE id='" . $uid . "'";
			$result = mysql_query($query);
$achEarned = $TeamPlayer;
		}
		
	}
}


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


$next_exp = GetExpToNextLevel($level);
$this_exp = GetExpToNextLevel($level - 1);
$expToNext = $next_exp - $experience;
$nextLevel = $next_exp - $this_exp;
$expWidth = (int)(($nextLevel - $expToNext) / $nextLevel * 100);
$sKrevels = number_format($krevels);
$expToThis = $nextLevel - $expToNext;
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
<title>Profile</title>
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

echo ">";
echo <<<_END
<div id="fb-root"></div>
<script src="http://connect.facebook.net/en_US/all.js"></script>
<script>
  window.fbAsyncInit = function() {
    FB.init({appId: '$app_id', status: true, cookie: true,
             xfbml: true});
    FB.Canvas.setSize({width:720, height:3500});
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
<div class="explain" style="background-image:url('assets/Box_DuelDescription.png'); height:112px; width:744px; top:134px; color:black;">
<div style="width:100%; left:0px; top:8px; position:relative; color:orange; text-align:center;">CHOSEN</div><br/>
<div style="width:728px; left:8px; position:relative;">Spend your skill points here and see how much stronger you have become. Energy allows you to do missions, attack and defence count in Faction Wars and dueling, stamina allows you to participate in combat and you can no longer duel when your health drops below 20. </div>
</div>
<div class='whitestripe' style="position:absolute; left:0px; width:760px; background-image:url('assets/WhiteStripe.jpg'); top:253px; height:4px;"> 
</div>
<div class="btn1of4" style="top:266px;">
<table style="top:9px; width:600px; text-align:center; position:absolute; left:99px;">
<tr><td style='width:145px; border:0px; padding:0px;'><a class='button' href="profile.php?token=$access_token">CHOSEN</a></td><td style='width:105px; border:0px; padding:0px; text-align:left;'><a href="knights.php?token=$access_token">KNIGHTS</a></td><td style='width:105px; border:0px; padding:0px;text-align:left;'><a href='leaderboard.php?token=$access_token'>RANKINGS</a></td><td style='width:135px;border:0px; padding:0px;text-align:left;'><a href='allies.php?token=$access_token'>ALLIES</a></td></tr></table>
</div>
<div class='profile' style="background-image:url('assets/Box_Profile3.png'); height:236px; color:black; top:311px;">
_END;
if(!$error)
{
if($uid == $viewID)
{
echo "<div style='width:744px; text-align:center; color:orange; position:relative; top:8px;'>$name's profile ($knights knights)</div><br/>";
if($skills >= 5)
{
echo <<<_END
<table style='position:relative; left:8px;'>
<tr style='height:20px;'><td style='width:70px;'>ENERGY: </td><td style='width:48px;'>$m_energy</td>
<td style='width:26px;'>
<form method="post" action="profile.php">
<input type="hidden" name="posted" value="yes"/>
<input type="hidden" name="action" value="energy"/>
<input type="hidden" name="token" value="$access_token"/>
<input type="image" src="assets/BTN_plus1.png" value="+"/>
</form>
</td>
<td style='width:26px;'>
<form method="post" action="profile.php">
<input type="hidden" name="posted" value="yes"/>
<input type="hidden" name="action" value="energy5"/>
<input type="hidden" name="token" value="$access_token"/>
<input type="image" src="assets/BTN_plus5.png" value="+5"/>
</form>
</td><td style='width:55px;'></td><td>Fights Won: $fightsWon</td></tr>
<tr style='height:20px;'><td style='width:70px;'>STAMINA: </td><td style='width:48px;'>$m_stamina</td>
<td style='width:26px;'>
<form method="post" action="profile.php">
<input type="hidden" name="posted" value="yes"/>
<input type="hidden" name="action" value="stamina"/>
<input type="hidden" name="token" value="$access_token"/>
<input type="image" src="assets/BTN_plus1.png" value="+"/>
</form>
</td>
<td style='width:26px;'>
<form method="post" action="profile.php">
<input type="hidden" name="posted" value="yes"/>
<input type="hidden" name="action" value="stamina5"/>
<input type="hidden" name="token" value="$access_token"/>
<input type="image" src="assets/BTN_plus5.png" value="+5"/>
</form>
</td><td style='width:55px;'></td><td>Fights Lost: $fightsLost</td></tr>
<tr style='height:20px;'><td style='width:70px;'>HEALTH: </td><td style='width:48px;'>$m_health</td>
<td style='width:26px;'>
<form method="post" action="profile.php">
<input type="hidden" name="posted" value="yes"/>
<input type="hidden" name="action" value="health"/>
<input type="hidden" name="token" value="$access_token"/>
<input type="image" src="assets/BTN_plus1.png" value="+"/>
</form>
</td>
<td style='width:26px;'>
<form method="post" action="profile.php">
<input type="hidden" name="posted" value="yes"/>
<input type="hidden" name="action" value="health5"/>
<input type="hidden" name="token" value="$access_token"/>
<input type="image" src="assets/BTN_plus5.png" value="+5"/>
</form>
</td><td style='width:55px;'></td><td>Won: $winPercent%</td></tr>
<tr>
<td style='width:70px;'>ATTACK: </td><td style='width:48px;'>$attack</td>
<td style='width:26px;'>
<form method="post" action="profile.php">
<input type="hidden" name="posted" value="yes"/>
<input type="hidden" name="token" value="$access_token"/>
<input type="hidden" name="action" value="attack"/>
<input type="image" src="assets/BTN_plus1.png" value="+"/>
</form>
</td>
<td style='width:26px;'>
<form method="post" action="profile.php">
<input type="hidden" name="posted" value="yes"/>
<input type="hidden" name="action" value="attack5"/>
<input type="hidden" name="token" value="$access_token"/>
<input type="image" src="assets/BTN_plus5.png" value="+5"/>
</form>
</td>
</tr>
<tr>
<td style='width:70px;'>DEFENCE: </td><td style='width:48px;'>$defence</td>
<td style='width:26px;'>
<form method="post" action="profile.php">
<input type="hidden" name="posted" value="yes"/>
<input type="hidden" name="action" value="defence"/>
<input type="hidden" name="token" value="$access_token"/>
<input type="image" src="assets/BTN_plus1.png" value="+"/>
</form>
</td>
<td style='width:26px;'>
<form method="post" action="profile.php">
<input type="hidden" name="posted" value="yes"/>
<input type="hidden" name="action" value="defence5"/>
<input type="hidden" name="token" value="$access_token"/>
<input type="image" src="assets/BTN_plus5.png" value="+5"/>
</form>
</td>
</tr>
</table>
_END;
}
else if($skills > 0)
{
echo <<<_END
<table style='position:relative; left:8px;'>
<tr style='height:20px;'><td style='width:70px;'>ENERGY: </td><td style='width:48px;'>$m_energy</td>
<td style='width:26px;'>
<form method="post" action="profile.php">
<input type="hidden" name="posted" value="yes"/>
<input type="hidden" name="action" value="energy"/>
<input type="hidden" name="token" value="$access_token"/>
<input type="image" src="assets/BTN_plus1.png" value="+"/>
</form>
</td><td style='width:26px;'></td><td style='width:55px;'></td><td>Fights Won: $fightsWon</td></tr>
<tr style='height:20px;'><td style='width:70px;'>STAMINA: </td><td style='width:48px;'>$m_stamina</td>
<td style='width:26px;'>
<form method="post" action="profile.php">
<input type="hidden" name="posted" value="yes"/>
<input type="hidden" name="action" value="stamina"/>
<input type="hidden" name="token" value="$access_token"/>
<input type="image" src="assets/BTN_plus1.png" value="+"/>
</form>
</td><td style='width:26px;'></td><td style='width:55px;'></td><td>Fights Lost: $fightsLost</td></tr>
<tr style='height:20px;'><td style='width:70px;'>HEALTH: </td><td style='width:48px;'>$m_health</td>
<td style='width:26px;'>
<form method="post" action="profile.php">
<input type="hidden" name="posted" value="yes"/>
<input type="hidden" name="action" value="health"/>
<input type="hidden" name="token" value="$access_token"/>
<input type="image" src="assets/BTN_plus1.png" value="+"/>
</form>
</td><td style='width:26px;'></td><td style='width:55px;'></td><td>Won: $winPercent%</td></tr>
<tr>
<td style='width:70px;'>ATTACK: </td><td style='width:48px;'>$attack</td>
<td style='width:26px;'>
<form method="post" action="profile.php">
<input type="hidden" name="posted" value="yes"/>
<input type="hidden" name="action" value="attack"/>
<input type="hidden" name="token" value="$access_token"/>
<input type="image" src="assets/BTN_plus1.png" value="+"/>
</form>
</td>
</tr>
<tr>
<td style='width:70px;'>DEFENCE: </td><td style='width:48px;'>$defence</td>
<td style='width:26px;'>
<form method="post" action="profile.php">
<input type="hidden" name="posted" value="yes"/>
<input type="hidden" name="action" value="defence"/>
<input type="hidden" name="token" value="$access_token"/>
<input type="image" src="assets/BTN_plus1.png" value="+"/>
</form>
</td>
</tr>
</table>
_END;
}
else
{
echo <<<_END
<table style='position:relative; left:8px;'>
<tr style='height:20px;'><td style='width:70px;'>ENERGY: </td><td style='width:48px;'>$m_energy</td><td style='width:26px;'></td><td style='width:26px;'></td><td style='width:55px;'></td><td>Fights Won: $fightsWon</td></tr>
<tr style='height:20px;'><td style='width:70px;'>STAMINA: </td><td style='width:48px;'>$m_stamina</td><td style='width:26px;'></td><td style='width:26px;'></td><td style='width:55px;'></td><td>Fights Lost: $fightsLost</td></tr>

<tr style='height:20px;'><td style='width:70px;'>HEALTH: </td><td style='width:48px;'>$m_health</td><td style='width:26px;'></td><td style='width:26px;'></td><td style='width:55px;'></td><td>Won: $winPercent%</td></tr>
<tr>
<td style='width:70px;'>ATTACK: </td><td style='width:48px;'>$attack</td>
</tr>
<tr>
<td style='width:70px;'>DEFENCE: </td><td style='width:48px;'>$defence</td>
</tr></table>
_END;


}

echo "<div style='position:absolute; top:110px; left:240px; text-align:center;'>";
echo "<b>Top attack:</b><br/>";
echo "<table style='text-align:center;'><tr>";
if($atkName == '')
echo "<td style='width:70px;'</td>";
else
echo "<td style='width:70px;'><img src='assets/power$topOffID.jpg'/></td>";

if($defName == '')
echo "<td style='width:70px;'</td>";
else
echo "<td style='width:70px;'><img src='assets/power$topDefID.jpg'/></td>";

if($utilName == '')
echo "<td style='width:70px;'</td></tr>";
else
echo "<td style='width:70px;'><img src='assets/power$topUtilID.jpg'/></td></tr>";
if($atkName == '')
echo "<td style='width:70px;'</td>";
else
echo "<td style='width:70px;'>$atkName</td>";

if($defName == '')
echo "<td style='width:70px;'</td>";
else
echo "<td style='width:70px;'>$defName</td>";

if($utilName == '')
echo "<td style='width:70px;'</td></tr>";
else
echo "<td style='width:70px;'>$utilName</td></tr>";
echo "</table>";

echo "</div>";

echo "<div style='position:absolute; top:110px; left:490px; text-align:center;'>";
echo "<b>Top defence:</b><br/>";
echo "<table style='text-align:center;'><tr>";
if($dAtkName == '')
echo "<td style='width:70px;'</td>";
else
echo "<td style='width:70px;'><img src='assets/power$dTopOffID.jpg'/></td>";

if($dDefName == '')
echo "<td style='width:70px;'</td>";
else
echo "<td style='width:70px;'><img src='assets/power$dTopDefID.jpg'/></td>";

if($dUtilName == '')
echo "<td style='width:70px;'</td></tr>";
else
echo "<td style='width:70px;'><img src='assets/power$dTopUtilID.jpg'/></td></tr>";
if($dAtkName == '')
echo "<td style='width:70px;'</td>";
else
echo "<td style='width:70px;'>$dAtkName</td>";

if($dDefName == '')
echo "<td style='width:70px;'</td>";
else
echo "<td style='width:70px;'>$dDefName</td>";

if($dUtilName == '')
echo "<td style='width:70px;'</td></tr>";
else
echo "<td style='width:70px;'>$dUtilName</td></tr>";
echo "</table>";

echo "</div>";

}
else
{


	echo "<div style='width:744px; text-align:center; color:orange; position:relative; top:8px;'>$viewName's profile</div><br/><br/>";
	echo "<div style='position:absolute; top:28px; left:16px; text-align:center;'><img src='assets/FactionIcon" . $viewFaction . ".jpg'/></div><div style='position:absolute; top:50px; left:98px; text-align:center;'>Level $viewLevel</div><div style='position:absolute; top:14px; left:650px; text-align:center;'>Duel?<br/>";
	echo "<form method='post' action='duel.php'>";
	echo "<input type='hidden' name='posted' value='yes'/>";

	echo "<input type='hidden' name='defender' value=" . $viewID ."/><input type='hidden' name='token' value='" . $access_token . "'/>";
	echo "<input type='image' value='DUEL' src='assets/BTN_Go.png'/></form>";

	echo "</div>";
	if($viewWon + $viewLost != 0)
	$winPercent = (int)(($viewWon / ($viewWon + $viewLost) * 100));
	else
	$winPercent = 0;
echo <<<_END
	<div style=" width: 180px; position:absolute; left:280px; top:35px;">
	Fights Won: $viewWon<br/>
	Fights Lost: $viewLost<br/>
	Won: $winPercent%
	</div>



_END;

echo "<div style='position:absolute; top:110px; left:240px; text-align:center;'>";
echo "<b>Top attack:</b><br/>";
echo "<table style='text-align:center;'><tr>";
if($atkName == '')
echo "<td style='width:70px;'</td>";
else
echo "<td style='width:70px;'><img src='assets/power$topOffID.jpg'/></td>";

if($defName == '')
echo "<td style='width:70px;'</td>";
else
echo "<td style='width:70px;'><img src='assets/power$topDefID.jpg'/></td>";

if($utilName == '')
echo "<td style='width:70px;'</td></tr>";
else
echo "<td style='width:70px;'><img src='assets/power$topUtilID.jpg'/></td></tr>";
if($atkName == '')
echo "<td style='width:70px;'</td>";
else
echo "<td style='width:70px;'>$atkName</td>";

if($defName == '')
echo "<td style='width:70px;'</td>";
else
echo "<td style='width:70px;'>$defName</td>";

if($utilName == '')
echo "<td style='width:70px;'</td></tr>";
else
echo "<td style='width:70px;'>$utilName</td></tr>";
echo "</table>";

echo "</div>";

echo "<div style='position:absolute; top:110px; left:490px; text-align:center;'>";
echo "<b>Top defence:</b><br/>";
echo "<table style='text-align:center;'><tr>";
if($dAtkName == '')
echo "<td style='width:70px;'</td>";
else
echo "<td style='width:70px;'><img src='assets/power$dTopOffID.jpg'/></td>";

if($dDefName == '')
echo "<td style='width:70px;'</td>";
else
echo "<td style='width:70px;'><img src='assets/power$dTopDefID.jpg'/></td>";

if($dUtilName == '')
echo "<td style='width:70px;'</td></tr>";
else
echo "<td style='width:70px;'><img src='assets/power$dTopUtilID.jpg'/></td></tr>";
if($dAtkName == '')
echo "<td style='width:70px;'</td>";
else
echo "<td style='width:70px;'>$dAtkName</td>";

if($dDefName == '')
echo "<td style='width:70px;'</td>";
else
echo "<td style='width:70px;'>$dDefName</td>";

if($dUtilName == '')
echo "<td style='width:70px;'</td></tr>";
else
echo "<td style='width:70px;'>$dUtilName</td></tr>";
echo "</table>";

echo "</div>";



}


}
else
{
	echo "<div style='width:744px; text-align:center; color:yellow;'>That user does not exist.</div><br/><br/>";
}
echo "</div>";
if(!$error && $uid == $viewID)
{
echo <<<_END



<div class='godpowers' style='top:555px;'><div style='color:orange; position:relative; top:8px;'>
GEM GOD POWERS</div>
<table style='position:relative; left:8px; top:8px; text-align:center;'>
<tr><th style='width:149px; border:0px; padding-left:4px; padding-right:4px; padding-top:0px; padding-bottom:0px; margin:0px;'><img src="assets/FactionIcon1.jpg"/><br/>SAPPHIROS</th><th style='width:149px;border:0px; padding-left:4px; padding-right:4px; padding-top:0px; padding-bottom:0px; margin:0px;'><img src="assets/FactionIcon2.jpg"/><br/>RUBIA</th><th style='width:149px;border:0px; padding-left:4px; padding-right:4px; padding-top:0px; padding-bottom:0px; margin:0px;'><img src="assets/FactionIcon3.jpg"/><br/>JEDEITE</th><th style='width:149px;border:0px; padding-left:4px; padding-right:4px; padding-top:0px; padding-bottom:0px; margin:0px;'><img src="assets/FactionIcon4.jpg"/><br/>DAMOS</th><th style='width:149px;border:0px; padding-left:4px; padding-right:4px; padding-top:0px; padding-bottom:0px; margin:0px;'><img src="assets/FactionIcon5.jpg"/><br/>MACHALITE</th></tr>

_END;
if($fac_change)
{
echo "<tr>";
if($faction != SAPPHIROS)
{

echo <<<_END
<td style='border:0px; padding-left:4px; padding-right:4px; padding-top:0px; padding-bottom:0px; margin:0px;'>

<form method="post" action="profile.php">
<input type="hidden" name="posted" value="change"/>
<input type="hidden" name="action" value="sapphiros"/>
<input type="hidden" name="token" value="$access_token"/>
<input type="image"  value="CHANGE?" src="assets/BTN_Change.png"/>
</form>
</td>
_END;
}
else
	echo "<td style='border:0px; padding-left:4px; padding-right:4px; padding-top:0px; padding-bottom:0px; margin:0px;'></td>";

if($faction != RUBIA)
{

echo <<<_END
<td style='border:0px; padding-left:4px; padding-right:4px; padding-top:0px; padding-bottom:0px; margin:0px;'>

<form method="post" action="profile.php">
<input type="hidden" name="posted" value="change"/>
<input type="hidden" name="token" value="$access_token"/>
<input type="hidden" name="action" value="rubia"/>
<input type="image"  value="CHANGE?" src="assets/BTN_Change.png"/>
</form>
</td>
_END;
}
else
	echo "<td style='border:0px; padding-left:4px; padding-right:4px; padding-top:0px; padding-bottom:0px; margin:0px;'></td>";

if($faction != JEDEITE)
{

echo <<<_END
<td style='border:0px; padding-left:4px; padding-right:4px; padding-top:0px; padding-bottom:0px; margin:0px;'>

<form method="post" action="profile.php">
<input type="hidden" name="posted" value="change"/>
<input type="hidden" name="token" value="$access_token"/>
<input type="hidden" name="action" value="jedeite"/>
<input type="image"  value="CHANGE?" src="assets/BTN_Change.png"/>
</form>
</td>
_END;
}
else
	echo "<td style='border:0px; padding-left:4px; padding-right:4px; padding-top:0px; padding-bottom:0px; margin:0px;'></td>";

if($faction != DAMOS)
{

echo <<<_END
<td style='border:0px; padding-left:4px; padding-right:4px; padding-top:0px; padding-bottom:0px; margin:0px;'>

<form method="post" action="profile.php">
<input type="hidden" name="posted" value="change"/>
<input type="hidden" name="token" value="$access_token"/>
<input type="hidden" name="action" value="damos"/>
<input type="image"  value="CHANGE?" src="assets/BTN_Change.png"/>
</form>
</td>
_END;
}
else
	echo "<td style='border:0px; padding-left:4px; padding-right:4px; padding-top:0px; padding-bottom:0px; margin:0px;'></td>";

if($faction != MACHALITE)
{
echo <<<_END
<td style='border:0px; padding-left:4px; padding-right:4px; padding-top:0px; padding-bottom:0px; margin:0px;'>

<form method="post" action="profile.php">
<input type="hidden" name="posted" value="change"/>
<input type="hidden" name="token" value="$access_token"/>
<input type="hidden" name="action" value="machalite"/>
<input type="image"  value="CHANGE?" src="assets/BTN_Change.png"/>
</form>
</td>
_END;
}
else
	echo "<td style='border:0px; padding-left:4px; padding-right:4px; padding-top:0px; padding-bottom:0px; margin:0px;'></td>";
echo "</tr>";
}
echo <<<_END
<tr ><td style='border:0px; padding-left:4px; padding-right:4px; padding-top:0px; padding-bottom:0px; margin:0px;'>-30 sec on Energy timer</td><td style='border:0px; padding-left:4px; padding-right:4px; padding-top:0px; padding-bottom:0px; margin:0px;'>+10% Attack</td><td style='border:0px; padding-left:4px; padding-right:4px; padding-top:0px; padding-bottom:0px; margin:0px;'>-1 min on Health timer</td><td style='border:0px; padding-left:4px; padding-right:4px; padding-top:0px; padding-bottom:0px; margin:0px;'>+10% Defence</td><td style='border:0px; padding-left:4px; padding-right:4px; padding-top:0px; padding-bottom:0px; margin:0px;'>-1 min on Stamina timer</td></tr>
<tr>
_END;
if($Gold[SAPPHIROS] == 4)
	echo "<td style='border:0px; padding-left:4px; padding-right:4px; padding-top:0px; padding-bottom:0px; margin:0px;'>+10% EXP on missions</td>";
else
	echo "<td style='border:0px; padding-left:4px; padding-right:4px; padding-top:0px; padding-bottom:0px; margin:0px;'></td>";
if($Gold[RUBIA] == 4)
	echo "<td style='border:0px; padding-left:4px; padding-right:4px; padding-top:0px; padding-bottom:0px; margin:0px;'>+2 damage when attacking</td>";
else
	echo "<td style='border:0px; padding-left:4px; padding-right:4px; padding-top:0px; padding-bottom:0px; margin:0px;'></td>";
if($Gold[JEDEITE] == 4)
	echo "<td style='border:0px; padding-left:4px; padding-right:4px; padding-top:0px; padding-bottom:0px; margin:0px;'>+1 EXP for faction wars</td>";
else
	echo "<td style='border:0px; padding-left:4px; padding-right:4px; padding-top:0px; padding-bottom:0px; margin:0px;'></td>";
if($Gold[DAMOS] == 4)
	echo "<td style='border:0px; padding-left:4px; padding-right:4px; padding-top:0px; padding-bottom:0px; margin:0px;'>1 bonus knight while defending</td>";
else
	echo "<td style='border:0px; padding-left:4px; padding-right:4px; padding-top:0px; padding-bottom:0px; margin:0px;'></td>";
if($Gold[MACHALITE] == 4)
	echo "<td style='border:0px; padding-left:4px; padding-right:4px; padding-top:0px; padding-bottom:0px; margin:0px;'>Attack with 15+ Health</td>";
else
	echo "<td></td>";
echo <<<_END
</tr>
<tr>
_END;
if($Gold2[SAPPHIROS] == 4)
	echo "<td style='border:0px; padding-left:4px; padding-right:4px; padding-top:0px; padding-bottom:0px; margin:0px;'>Increased drop rate for missions</td>";
else
	echo "<td style='border:0px; padding-left:4px; padding-right:4px; padding-top:0px; padding-bottom:0px; margin:0px;'></td>";
if($Gold2[RUBIA] == 4)
	echo "<td style='border:0px; padding-left:4px; padding-right:4px; padding-top:0px; padding-bottom:0px; margin:0px;'>1 bonus knight while attacking</td>";
else
	echo "<td style='border:0px; padding-left:4px; padding-right:4px; padding-top:0px; padding-bottom:0px; margin:0px;'></td>";
if($Gold2[JEDEITE] == 4)
	echo "<td style='border:0px; padding-left:4px; padding-right:4px; padding-top:0px; padding-bottom:0px; margin:0px;'>-1 Stamina for faction wars</td>";
else
	echo "<td style='border:0px; padding-left:4px; padding-right:4px; padding-top:0px; padding-bottom:0px; margin:0px;'></td>";
if($Gold2[DAMOS] == 4)
	echo "<td style='border:0px; padding-left:4px; padding-right:4px; padding-top:0px; padding-bottom:0px; margin:0px;'>Take -2 damage when attacking</td>";
else
	echo "<td style='border:0px; padding-left:4px; padding-right:4px; padding-top:0px; padding-bottom:0px; margin:0px;'></td>";
if($Gold2[MACHALITE] == 4)
	echo "<td style='border:0px; padding-left:4px; padding-right:4px; padding-top:0px; padding-bottom:0px; margin:0px;'>Reduced flaqqers cost</td>";
else
	echo "<td></td>";
echo <<<_END
</tr>
</table>
</div>
_END;
}
echo <<<_END
<div class='achievements'
_END;
if($viewID != $uid)
{
	echo " style='top:555px;'";
}
echo <<<_END
>
<div style='color:orange; text-align:center; position:relative; top:8px;'>ACHIEVEMENTS</div>
<table style='position:absolute; top:25px; left:8px; width:728px;'>
<tr style='height:108px;'>
<td style='width:364px;'>
<div style='width:364px; height:100px;'>
<img style='width:100px; height:100px; position:absolute;' src='assets/achie1.jpg'/>
_END;
if($achEarned[0] >= 0)
	echo "<img style='width:100px; height:100px; position:absolute;' src='assets/BG_box4x4.png'/>";
echo <<<_END


<div style='position:relative; left:108px; top:8px; width:256px; font-size:0.75em;'>
WILD CARD<br/>
Support each gem god once.<br/>
_END;
if($achEarned[0] == -1)
	echo "5/5";
else if($achEarned[0] == 0)
	echo $achEarned[0] . "/5";
else
	echo strlen($achEarned[0]) . "/5";
echo <<<_END
</div>
</div>
</td>
<td style='width:364px;'>
<div style='width:364px; height:100px;'>
<img style='width:100px; height:100px; position:absolute;' src='assets/achie2.jpg'/>
_END;
if($achEarned[1] >= 0)
	echo "<img style='width:100px; height:100px; position:absolute;' src='assets/BG_box4x4.png'/>";
echo <<<_END


<div style='position:relative; left:108px; top:8px; width:256px; font-size:0.75em;'>
TIP THE SCALES<br/>
Win 10 faction wars<br/>
_END;
if($achEarned[1] == -1)
	echo "10/10";
else
	echo $achEarned[1] . "/10";
echo <<<_END
</div>
</div>
</td>
</tr>

<tr style='height:108px;'>
<td style='width:364px;'>
<div style='width:364px; height:100px;'>
<img style='width:100px; height:100px; position:absolute;' src='assets/achie3.jpg'/>
_END;
if($achEarned[2] >= 0)
	echo "<img style='width:100px; height:100px; position:absolute;' src='assets/BG_box4x4.png'/>";
echo <<<_END


<div style='position:relative; left:108px; top:8px; width:256px; font-size:0.75em;'>
ONE-CHOSEN ARMY<br/>
Win 100 faction wars.<br/>
_END;
if($achEarned[2] == -1)
	echo "100/100";
else
	echo $achEarned[2] . "/100";
echo <<<_END
</div>
</div>
</td>
<td style='width:364px;'>
<div style='width:364px; height:100px;'>
<img style='width:100px; height:100px; position:absolute;' src='assets/achie4.jpg'/>
_END;
if($achEarned[3] >= 0)
	echo "<img style='width:100px; height:100px; position:absolute;' src='assets/BG_box4x4.png'/>";
echo <<<_END


<div style='position:relative; left:108px; top:8px; width:256px; font-size:0.75em;'>
MONOTHEIST<br/>
Win 5 faction wars while supporting only one gem god.<br/>
_END;
if($achEarned[3] == -1)
	echo "5/5";
else
	echo $achEarned[3] . "/5";
echo <<<_END
</div>
</div>
</td>
</tr>

<tr style='height:108px;'>
<td style='width:364px;'>
<div style='width:364px; height:100px;'>
<img style='width:100px; height:100px; position:absolute;' src='assets/achie5.jpg'/>
_END;
if($achEarned[4] >= 0)
	echo "<img style='width:100px; height:100px; position:absolute;' src='assets/BG_box4x4.png'/>";
echo <<<_END


<div style='position:relative; left:108px; top:8px; width:256px; font-size:0.75em;'>
DEDICATED<br/>
Participate in faction wars 7 days in a row.<br/>
_END;
if($achEarned[4] == -1)
	echo "7/7";
else
	echo $achEarned[4] . "/7";
echo <<<_END
</div>
</div>
</td>
<td style='width:364px;'>
<div style='width:364px; height:100px;'>
<img style='width:100px; height:100px; position:absolute;' src='assets/achie6.jpg'/>
_END;
if($achEarned[5] >= 0)
	echo "<img style='width:100px; height:100px; position:absolute;' src='assets/BG_box4x4.png'/>";
echo <<<_END


<div style='position:relative; left:108px; top:8px; width:256px; font-size:0.75em;'>
SURVIVAL OF THE FITTEST<br/>
Attack 10 Chosen of your own faction.<br/>
_END;
if($achEarned[5] == -1)
	echo "10/10";
else
	echo $achEarned[5] . "/10";
echo <<<_END
</div>
</div>
</td>
</tr>

<tr style='height:108px;'>
<td style='width:364px;'>
<div style='width:364px; height:100px;'>
<img style='width:100px; height:100px; position:absolute;' src='assets/achie7.jpg'/>
_END;
if($achEarned[6] >= 0)
	echo "<img style='width:100px; height:100px; position:absolute;' src='assets/BG_box4x4.png'/>";
echo <<<_END


<div style='position:relative; left:108px; top:8px; width:256px; font-size:0.75em;'>
DOWN BUT NOT OUT<br/>
Lose 10 duels in a row.<br/>
_END;
if($achEarned[6] == -1)
	echo "10/10";
else 
	echo $achEarned[6] . "/10";
echo <<<_END
</div>
</div>
</td>
<td style='width:364px;'>
<div style='width:364px; height:100px;'>
<img style='width:100px; height:100px; position:absolute;' src='assets/achie8.jpg'/>
_END;
if($achEarned[7] >= 0)
	echo "<img style='width:100px; height:100px; position:absolute;' src='assets/BG_box4x4.png'/>";
echo <<<_END


<div style='position:relative; left:108px; top:8px; width:256px; font-size:0.75em;'>
PROVE YOUR STRENGTH<br/>
Win 100 duels in a row.<br/>
_END;
if($achEarned[7] == -1)
	echo "100/100";
else
	echo $achEarned[7] . "/100";
echo <<<_END
</div>
</div>
</td>
</tr>

<tr style='height:108px;'>
<td style='width:364px;'>
<div style='width:364px; height:100px;'>
<img style='width:100px; height:100px; position:absolute;' src='assets/achie9.jpg'/>
_END;
if($achEarned[8] >= 0)
	echo "<img style='width:100px; height:100px; position:absolute;' src='assets/BG_box4x4.png'/>";
echo <<<_END


<div style='position:relative; left:108px; top:8px; width:256px; font-size:0.75em;'>
EQUAL OPPORTUNIST<br/>
Attack a Chosen of each gem god.<br/>
_END;
if($achEarned[8] == -1)
	echo "5/5";
else if($achEarned[8] == 0)
	echo $achEarned[8] . "/5";
else
	echo strlen($achEarned[8]) . "/5";
echo <<<_END
</div>
</div>
</td>
<td style='width:364px;'>
<div style='width:364px; height:100px;'>
<img style='width:100px; height:100px; position:absolute;' src='assets/achie10.jpg'/>
_END;
if($achEarned[9] >= 0)
	echo "<img style='width:100px; height:100px; position:absolute;' src='assets/BG_box4x4.png'/>";
echo <<<_END


<div style='position:relative; left:108px; top:8px; width:256px; font-size:0.75em;'>
ELIMINATION ROUND<br/>
Bring another Chosen to 0 health.<br/>
_END;
if($achEarned[9] == -1)
	echo "1/1";
else
	echo $achEarned[9] . "/1";
echo <<<_END
</div>
</div>
</td>
</tr>

<tr style='height:108px;'>
<td style='width:364px;'>
<div style='width:364px; height:100px;'>
<img style='width:100px; height:100px; position:absolute;' src='assets/achie11.jpg'/>
_END;
if($achEarned[10] >= 0)
	echo "<img style='width:100px; height:100px; position:absolute;' src='assets/BG_box4x4.png'/>";
echo <<<_END


<div style='position:relative; left:108px; top:8px; width:256px; font-size:0.75em;'>
LAST CHOSEN STANDING<br/>
Bring down 20 Chosen.<br/>
_END;
if($achEarned[10] == -1)
	echo "20/20";
else
	echo $achEarned[10] . "/20";
echo <<<_END
</div>
</div>
</td>
<td style='width:364px;'>
<div style='width:364px; height:100px;'>
<img style='width:100px; height:100px; position:absolute;' src='assets/achie12.jpg'/>
_END;
if($achEarned[11] >= 0)
	echo "<img style='width:100px; height:100px; position:absolute;' src='assets/BG_box4x4.png'/>";
echo <<<_END


<div style='position:relative; left:108px; top:8px; width:256px; font-size:0.75em;'>
WALKING SHIELD<br/>
Successfully defend 50 times.<br/>
_END;
if($achEarned[11] == -1)
	echo "50/50";
else
	echo $achEarned[11] . "/50";
echo <<<_END
</div>
</div>
</td>
</tr>

<tr style='height:108px;'>
<td style='width:364px;'>
<div style='width:364px; height:100px;'>
<img style='width:100px; height:100px; position:absolute;' src='assets/achie13.jpg'/>
_END;
if($achEarned[12] >= 0)
	echo "<img style='width:100px; height:100px; position:absolute;' src='assets/BG_box4x4.png'/>";
echo <<<_END


<div style='position:relative; left:108px; top:8px; width:256px; font-size:0.75em;'>
IMPRESSIVE COLLECTION<br/>
Collect all 5 ultimate faction wars powers.<br/>
_END;
if($achEarned[12] == -1)
	echo "5/5";
else if($achEarned[12] == 0)
	echo $achEarned[12] . "/5";
else
	echo strlen($achEarned[12]) . "/5";
echo <<<_END
</div>
</div>
</td>
<td style='width:364px;'>
<div style='width:364px; height:100px;'>
<img style='width:100px; height:100px; position:absolute;' src='assets/achie14.jpg'/>
_END;
if($achEarned[13] >= 0)
	echo "<img style='width:100px; height:100px; position:absolute;' src='assets/BG_box4x4.png'/>";
echo <<<_END


<div style='position:relative; left:108px; top:8px; width:256px; font-size:0.75em;'>
COLLECT THEM ALL<br/>
Collect 15 faction wars powers.<br/>
_END;
if($achEarned[13] == -1)
	echo "15/15";
else
	echo $achEarned[13] . "/15";
echo <<<_END
</div>
</div>
</td>
</tr>

<tr style='height:108px;'>
<td style='width:364px;'>
<div style='width:364px; height:100px;'>
<img style='width:100px; height:100px; position:absolute;' src='assets/achie15.jpg'/>
_END;
if($achEarned[14] >= 0)
	echo "<img style='width:100px; height:100px; position:absolute;' src='assets/BG_box4x4.png'/>";
echo <<<_END


<div style='position:relative; left:108px; top:8px; width:256px; font-size:0.75em;'>
STORM OF ARROWS<br/>
Own 100 "Arrow".<br/>
_END;
if($achEarned[14] == -1)
	echo "100/100";
else
	echo $achEarned[14] . "/100";
echo <<<_END
</div>
</div>
</td>
<td style='width:364px;'>
<div style='width:364px; height:100px;'>
<img style='width:100px; height:100px; position:absolute;' src='assets/achie16.jpg'/>
_END;
if($achEarned[15] >= 0)
	echo "<img style='width:100px; height:100px; position:absolute;' src='assets/BG_box4x4.png'/>";
echo <<<_END


<div style='position:relative; left:108px; top:8px; width:256px; font-size:0.75em;'>
REPERTOIRE<br/>
Own 50 different powers.<br/>
_END;
if($achEarned[15] == -1)
	echo "50/50";
else
	echo $viewPower . "/50";
echo <<<_END
</div>
</div>
</td>
</tr>

<tr style='height:108px;'>
<td style='width:364px;'>
<div style='width:364px; height:100px;'>
<img style='width:100px; height:100px; position:absolute;' src='assets/achie17.jpg'/>
_END;
if($achEarned[16] >= 0)
	echo "<img style='width:100px; height:100px; position:absolute;' src='assets/BG_box4x4.png'/>";
echo <<<_END


<div style='position:relative; left:108px; top:8px; width:256px; font-size:0.75em;'>
GOLD RUSH<br/>
Clear gold rank for any mission area.<br/>
_END;
if($achEarned[16] == -1)
	echo "1/1";
else
	echo $achEarned[16] . "/1";
echo <<<_END
</div>
</div>
</td>
<td style='width:364px;'>
<div style='width:364px; height:100px;'>
<img style='width:100px; height:100px; position:absolute;' src='assets/achie18.jpg'/>
_END;
if($achEarned[17] >= 0)
	echo "<img style='width:100px; height:100px; position:absolute;' src='assets/BG_box4x4.png'/>";
echo <<<_END


<div style='position:relative; left:108px; top:8px; width:256px; font-size:0.75em;'>
GOLD MEDALIST<br/>
Clear gold rank for all 5 Sapphire mission areas.<br/>
_END;
if($achEarned[17] == -1)
	echo "5/5";
else
{
$Medalist = 0;
	for ($i = 1; $i < 6; $i++)
	{
		if($Gold[$i] == 4)
			$Medalist++;
	}
	echo $Medalist . "/5";
}
echo <<<_END
</div>
</div>
</td>
</tr>

<tr style='height:108px;'>
<td style='width:364px;'>
<div style='width:364px; height:100px;'>
<img style='width:100px; height:100px; position:absolute;' src='assets/achie19.jpg'/>
_END;
if($achEarned[18] >= 0)
	echo "<img style='width:100px; height:100px; position:absolute;' src='assets/BG_box4x4.png'/>";
echo <<<_END


<div style='position:relative; left:108px; top:8px; width:256px; font-size:0.75em;'>
KREVEL HOARD<br/>
Deposit 500,000 krevels at once (after Ticket tax).<br/>
_END;
if($achEarned[18] == -1)
	echo "1/1";
else
	echo $achEarned[18] . "/1";
echo <<<_END
</div>
</div>
</td>
<td style='width:364px;'>
<div style='width:364px; height:100px;'>
<img style='width:100px; height:100px; position:absolute;' src='assets/achie20.jpg'/>
_END;
if($achEarned[19] >= 0)
	echo "<img style='width:100px; height:100px; position:absolute;' src='assets/BG_box4x4.png'/>";
echo <<<_END


<div style='position:relative; left:108px; top:8px; width:256px; font-size:0.75em;'>
ONE KREVEL... TWO KREVELS...<br/>
Have 1,000,000 krevels<br/>
_END;
if($achEarned[19] == -1)
	echo "1000000/1000000";
else
	echo ($viewKrevels) . "/1000000";
echo <<<_END
</div>
</div>
</td>
</tr>

<tr style='height:108px;'>
<td style='width:364px;'>
<div style='width:364px; height:100px;'>
<img style='width:100px; height:100px; position:absolute;' src='assets/achie21.jpg'/>
_END;
if($achEarned[20] >= 0)
	echo "<img style='width:100px; height:100px; position:absolute;' src='assets/BG_box4x4.png'/>";
echo <<<_END


<div style='position:relative; left:108px; top:8px; width:256px; font-size:0.75em;'>
NEW HOME<br/>
Play Crimson Winter 7 days in a row.<br/>
_END;
if($achEarned[20] == -1)
	echo "7/7";
else
	echo ($achEarned[20] + 1) . "/7";

echo <<<_END
</div>
</div>
</td>
<td style='width:364px;'>
<div style='width:364px; height:100px;'>
<img style='width:100px; height:100px; position:absolute;' src='assets/achie22.jpg'/>
_END;
if($achEarned[21] >= 0)
	echo "<img style='width:100px; height:100px; position:absolute;' src='assets/BG_box4x4.png'/>";
echo <<<_END


<div style='position:relative; left:108px; top:8px; width:256px; font-size:0.75em;'>
SAVVY SHOPPER<br/>
Purchase anything using gems.<br/>
_END;
if($achEarned[21] == -1)
	echo "1/1";
else
	echo $achEarned[21] . "/1";
echo <<<_END
</div>
</div>
</td>
</tr>

<tr style='height:108px;'>
<td style='width:364px;'>
<div style='width:364px; height:100px;'>
<img style='width:100px; height:100px; position:absolute;' src='assets/achie23.jpg'/>
_END;
if($achEarned[22] >= 0)
	echo "<img style='width:100px; height:100px; position:absolute;' src='assets/BG_box4x4.png'/>";
echo <<<_END


<div style='position:relative; left:108px; top:8px; width:256px; font-size:0.75em;'>
RESEARCH<br/>
Click on the ad for the Crimson Winter novels.<br/>
_END;
if($achEarned[22] == -1)
	echo "1/1";
else
	echo $achEarned[22] . "/1";
echo <<<_END
</div>
</div>
</td>
<td style='width:364px;'>
<div style='width:364px; height:100px;'>
<img style='width:100px; height:100px; position:absolute;' src='assets/achie24.jpg'/>
_END;
if($achEarned[23] >= 0)
	echo "<img style='width:100px; height:100px; position:absolute;' src='assets/BG_box4x4.png'/>";
echo <<<_END


<div style='position:relative; left:108px; top:8px; width:256px; font-size:0.75em;'>
PLAY THE ODDS<br/>
Purchase a raffle ticket.<br/>
_END;
if($achEarned[23] == -1)
	echo "1/1";
else
	echo $achEarned[23] . "/1";
echo <<<_END
</div>
</div>
</td>
</tr>

<tr style='height:108px;'>
<td style='width:364px;'>
<div style='width:364px; height:100px;'>
<img style='width:100px; height:100px; position:absolute;' src='assets/achie25.jpg'/>
_END;
if($achEarned[24] >= 0)
	echo "<img style='width:100px; height:100px; position:absolute;' src='assets/BG_box4x4.png'/>";
echo <<<_END


<div style='position:relative; left:108px; top:8px; width:256px; font-size:0.75em;'>
TEAM PLAYER<br/>
Be Chosen of each gem god.<br/>
_END;
if($achEarned[24] == -1)
	echo "5/5";
else if($achEarned[24] == 0)
	echo $achEarned[24] . "/5";
else
	echo strlen($achEarned[24]) . "/5";
echo <<<_END
</div>
</div>
</td>
<td style='width:364px;'>
<div style='width:364px; height:100px;'>
<img style='width:100px; height:100px; position:absolute;' src='assets/achie26.jpg'/>
_END;
if($achEarned[25] >= 0)
	echo "<img style='width:100px; height:100px; position:absolute;' src='assets/BG_box4x4.png'/>";
echo <<<_END


<div style='position:relative; left:108px; top:8px; width:256px; font-size:0.75em;'>
RECRUITER<br/>
Send 10 invites.<br/>
_END;
if($achEarned[25] == -1)
	echo "10/10";
else
	echo $achEarned[25] . "/10";
echo <<<_END
</div>
</div>
</td>
</tr>

<tr style='height:108px;'>
<td style='width:364px;'>
<div style='width:364px; height:100px;'>
<img style='width:100px; height:100px; position:absolute;' src='assets/achie27.jpg'/>
_END;
if($achEarned[26] >= 0)
	echo "<img style='width:100px; height:100px; position:absolute;' src='assets/BG_box4x4.png'/>";
echo <<<_END


<div style='position:relative; left:108px; top:8px; width:256px; font-size:0.75em;'>
POPULAR<br/>
Have 10 friends playing the game.<br/>
_END;
if($achEarned[26] == -1)
	echo "10/10";
else
	echo $achEarned[26] . "/10";

echo <<<_END
</div>
</div>
</td>
<td style='width:364px;'>
<div style='width:364px; height:100px;'>
<img style='width:100px; height:100px; position:absolute;' src='assets/achie28.jpg'/>
_END;
if($achEarned[27] >= 0)
	echo "<img style='width:100px; height:100px; position:absolute;' src='assets/BG_box4x4.png'/>";
echo <<<_END


<div style='position:relative; left:108px; top:8px; width:256px; font-size:0.75em;'>
FAMOUS<br/>
Have 100 friends playing the game.<br/>
_END;
if($achEarned[27] == -1)
	echo "100/100";
else
	echo $achEarned[27] . "/100";
echo <<<_END
</div>
</div>
</td>
</tr>

<tr style='height:108px;'>
<td style='width:364px;'>
<div style='width:364px; height:100px;'>
<img style='width:100px; height:100px; position:absolute;' src='assets/achie29.jpg'/>
_END;
if($achEarned[28] >= 0)
	echo "<img style='width:100px; height:100px; position:absolute;' src='assets/BG_box4x4.png'/>";
echo <<<_END


<div style='position:relative; left:108px; top:8px; width:256px; font-size:0.75em;'>
SPONSOR<br/>
Send 10 gifts.<br/>
_END;
if($achEarned[28] == -1)
	echo "10/10";
else
	echo $achEarned[28] . "/10";

echo <<<_END
</div>
</div>
</td>
<td style='width:364px;'>
<div style='width:364px; height:100px;'>
<img style='width:100px; height:100px; position:absolute;' src='assets/achie30.jpg'/>
_END;
if($achEarned[29] >= 0)
	echo "<img style='width:100px; height:100px; position:absolute;' src='assets/BG_box4x4.png'/>";
echo <<<_END


<div style='position:relative; left:108px; top:8px; width:256px; font-size:0.75em;'>
MIST ME!<br/>
"Mist Chance" 5 friends.<br/>
_END;
if($achEarned[29] == -1)
	echo "5/5";
else
	echo $achEarned[29] . "/5";
echo <<<_END
</div>
</div>
</td>
</tr>

<tr style='height:108px;'>
<td style='width:364px;'>
<div style='width:364px; height:100px;'>
<img style='width:100px; height:100px; position:absolute;' src='assets/achie31.jpg'/>
_END;
if($achEarned[30] >= 0)
	echo "<img style='width:100px; height:100px; position:absolute;' src='assets/BG_box4x4.png'/>";
echo <<<_END


<div style='position:relative; left:108px; top:8px; width:256px; font-size:0.75em;'>
ALL SET FOR SAND<br/>
Collect all 8 Sand And Sun Event collection items.<br/>
_END;
if($achEarned[30] == -1)
	echo "8/8";
else if($achEarned[30] == 0)
	echo $achEarned[30] . "/8";
else
	echo strlen($achEarned[30]) . "/8";
echo <<<_END
</div>
</div>
</td>
<td style='width:364px;'>
<div style='width:364px; height:100px;'>
<img style='width:100px; height:100px; position:absolute;' src='assets/achie32.jpg'/>
_END;
if($achEarned[31] >= 0)
	echo "<img style='width:100px; height:100px; position:absolute;' src='assets/BG_box4x4.png'/>";
echo <<<_END


<div style='position:relative; left:108px; top:8px; width:256px; font-size:0.75em;'>
CRAWL BEFORE YOU WALK<br/>
Defeat the Sand Crawler.<br/>
_END;
if($achEarned[31] == -1)
	echo "1/1";
else
	echo $achEarned[31] . "/1";
echo <<<_END
</div>
</div>
</td>
</tr>

<tr style='height:108px;'>
<td style='width:364px;'>
<div style='width:364px; height:100px;'>
<img style='width:100px; height:100px; position:absolute;' src='assets/achie33.jpg'/>
_END;
if($achEarned[32] >= 0)
	echo "<img style='width:100px; height:100px; position:absolute;' src='assets/BG_box4x4.png'/>";
echo <<<_END


<div style='position:relative; left:108px; top:8px; width:256px; font-size:0.75em;'>
LITTLE JADE CITY<br/>
Collect all 8 Seven Days In Jade Event collection items.<br/>
_END;
if($achEarned[32] == -1)
	echo "8/8";
else if($achEarned[32] == 0)
	echo $achEarned[32] . "/8";
else
	echo strlen($achEarned[32]) . "/8";
echo <<<_END
</div>
</div>
</td>
<td style='width:364px;'>
<div style='width:364px; height:100px;'>
<img style='width:100px; height:100px; position:absolute;' src='assets/achie34.jpg'/>
_END;
if($achEarned[33] >= 0)
	echo "<img style='width:100px; height:100px; position:absolute;' src='assets/BG_box4x4.png'/>";
echo <<<_END


<div style='position:relative; left:108px; top:8px; width:256px; font-size:0.75em;'>
ENEMY OF MY ENEMY<br/>
Defeat Jedeite's team during the Seven Days In Jade event.<br/>
_END;
if($achEarned[33] == -1)
	echo "1/1";
else
	echo $achEarned[33] . "/1";
echo <<<_END
</div>
</div>
</td>
</tr>

<tr style='height:108px;'>
<td style='width:364px;'>
<div style='width:364px; height:100px;'>
<img style='width:100px; height:100px; position:absolute;' src='assets/achie35.jpg'/>
_END;
if($achEarned[34] >= 0)
	echo "<img style='width:100px; height:100px; position:absolute;' src='assets/BG_box4x4.png'/>";
echo <<<_END


<div style='position:relative; left:108px; top:8px; width:256px; font-size:0.75em;'>
TAIYOU TOURIST<br/>
Collect all 8 collection items in the lands of Jade.<br/>
_END;
if($achEarned[34] == -1)
	echo "8/8";
else if($achEarned[34] == 0)
	echo $achEarned[34] . "/8";
else
	echo strlen($achEarned[34]) . "/8";
echo <<<_END
</div>
</div>
</td>
<td style='width:364px;'>
<div style='width:364px; height:100px;'>
<img style='width:100px; height:100px; position:absolute;' src='assets/achie36.jpg'/>
_END;
if($achEarned[35] >= 0)
	echo "<img style='width:100px; height:100px; position:absolute;' src='assets/BG_box4x4.png'/>";
echo <<<_END


<div style='position:relative; left:108px; top:8px; width:256px; font-size:0.75em;'>
REMEMBER FEAR<br/>
Defeat Sir Sabien three times<br/>
_END;
if($achEarned[35] == -1)
	echo "3/3";
else
	echo $achEarned[35] . "/3";
echo <<<_END
</div>
</div>
</td>
</tr>
<tr style='height:108px;'>
<td style='width:364px;'>
<div style='width:364px; height:100px;'>
<img style='width:100px; height:100px; position:absolute;' src='assets/achie37.jpg'/>
_END;
if($achEarned[36] >= 0)
	echo "<img style='width:100px; height:100px; position:absolute;' src='assets/BG_box4x4.png'/>";
echo <<<_END


<div style='position:relative; left:108px; top:8px; width:256px; font-size:0.75em;'>
HIT ME AGAIN<br/>
Defeat the Oujou five times.<br/>
_END;
if($achEarned[36] == -1)
	echo "5/5";
else
	echo $achEarned[36] . "/5";

echo <<<_END
</div>
</div>
</td>
<td style='width:364px;'>
<div style='width:364px; height:100px;'>
<img style='width:100px; height:100px; position:absolute;' src='assets/achie38.jpg'/>
_END;
if($achEarned[37] >= 0)
	echo "<img style='width:100px; height:100px; position:absolute;' src='assets/BG_box4x4.png'/>";
echo <<<_END


<div style='position:relative; left:108px; top:8px; width:256px; font-size:0.75em;'>
ALLIED FORCES<br/>
Invest in an ally.<br/>
_END;
if($achEarned[37] == -1)
	echo "1/1";
else
	echo $achEarned[37] . "/1";
echo <<<_END
</div>
</div>
</td>
</tr>
<tr style='height:108px;'>
<td style='width:364px;'>
<div style='width:364px; height:100px;'>
<img style='width:100px; height:100px; position:absolute;' src='assets/achie39.jpg'/>
_END;
if($achEarned[38] >= 0)
	echo "<img style='width:100px; height:100px; position:absolute;' src='assets/BG_box4x4.png'/>";
echo <<<_END


<div style='position:relative; left:108px; top:8px; width:256px; font-size:0.75em;'>
SWORN BROTHER<br/>
Upgrade the Knights of Sapphiros to level 50.<br/>
_END;
if($achEarned[38] == -1)
	echo "8/8";
else
	echo $achEarned[38] . "/50";

echo <<<_END
</div>
</div>
</td>
<td style='width:364px;'>
<div style='width:364px; height:100px;'>
<img style='width:100px; height:100px; position:absolute;' src='assets/achie40.jpg'/>
_END;
if($achEarned[39] >= 0)
	echo "<img style='width:100px; height:100px; position:absolute;' src='assets/BG_box4x4.png'/>";
echo <<<_END


<div style='position:relative; left:108px; top:8px; width:256px; font-size:0.75em;'>
BEEN THERE, DONE THAT<br/>
Earn gold rank in all Jade mission areas.<br/>
_END;
if($achEarned[39] == -1)
	echo "1/1";
else
	$Medalist = 0;
	for ($i = 1; $i < 6; $i++)
	{
		if($Gold2[$i] == 4)
			$Medalist++;
	}
	echo $Medalist . "/5";
echo <<<_END
</div>
</div>
</td>
</tr>

</table>
</div>
<br/>
<div style='position:absolute; bottom:0px; left:520px; font-size:0.75em;'>
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
