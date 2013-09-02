<?php
require_once 'src/facebook.php';
require_once 'db_vars.php';
require_once 'functions.php';
require_once 'cw_vars.php';

$displayAchievement = 0;

$achName = '';
$app_id = APP_ID;
    $app_secret = APP_SECRET;
    $my_url = "http://apps.facebook.com/crimsonwinter/duel.php";

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

//player details
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
$powerTotal = $rows[28];


if($name == '')
	$name = "Unnamed Chosen";

$levelUp = 0;
$rawATK = $attack;
if($faction == RUBIA)
	$rawATK += (int)($attack * 0.1);

$played = $rows[29];
$knights += $rows[30];

if($knights > 500)
	$knights = 500;
	
$region = $rows[37];

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

//check for rubia gold bonus
$query = "SELECT jTier1, jTier2, jTier8, jTier9 FROM CW_Missions WHERE id='" . $uid . "'";
$result = mysql_query($query);
$rows = mysql_fetch_row($result);
if($rows[0] == 4 && $faction == RUBIA)
$rubiaGold = 1;
else
$rubiaGold = 0;
if($rows[1] == 4 && $faction == MACHALITE)
{
$threshold = 15;
}
else
$threshold = 20;
if($rows[2] == 4 && $faction == DAMOS)
$damosGold2 = 1;
else
$damosGold2 = 0;
if($rows[4] == 4 && $faction == RUBIA)
$rubiaGold2 = 1;
else
$rubiaGold2 = 0;



if($rubiaGold)
	$knights++;

$query = "SELECT * FROM CW_Inventory WHERE id='" . $uid . "'";//for real game, add WHERE to narrow it down to fb uid
$result = mysql_query($query);

if(!$result) die("Database access failed: " . mysql_error());
$rows = mysql_fetch_row($result);

for($i = 1; $i < mysql_num_fields($result); $i++)
{
	$powers[] = $rows[$i];
}

$query = "SELECT power68, power70, power71 FROM CW_Inventory WHERE id='" . $uid . "'";//for real game, add WHERE to narrow it down to fb uid
$result = mysql_query($query);
$row = mysql_fetch_row($result);
$Katana = $row[0];
$BoltThrower = $row[1];
$Quarterstaff = $row[2];

//regions are important because players can only attack others in their region
//switch regions here
if(!empty($_GET[region]))
{

$newRegion = $_GET[region];
$newRegion--;
if($newRegion == 1 && $level > 80 || $newRegion == 0)
{
$region = $newRegion;
$query = "UPDATE CW_Users SET region='" . $region . "' WHERE id='" . $uid . "'";//for real game, add WHERE to narrow it down to fb uid
$result = mysql_query($query);
}
else
echo "Your level is not high enough to access this area.";
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
//all the pvp achievements
$query = "SELECT achievement6, achievement7, achievement8, achievement9, achievement10, achievement11, achievement20, achievement16, achievement31, achievement33 FROM CW_Achievements WHERE id='" . $uid . "'";
$result = mysql_query($query);

$row = mysql_fetch_row($result);
$SurvivalOfTheFittest = $row[0];
$DownButNotOut = $row[1];
$ProveYourStrength = $row[2];
$EqualOpportunist = $row[3];
$EliminationRound = $row[4];
$LastChosenStanding = $row[5];
$WealthyChosen = $row[6];
$Repertoire = $row[7];
$AllSetForSand = $row[8];
$TaiyouTourist = $row[9];

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
for($k = 0; $k < $knights; $k++)
{
	$topOffID = -1;
	$topDefID = -1;
	$topUtilID = -1;
	$tempDef = 0;
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
						if($powerAttack[$i] > $tempTopOFF)
						{
						$tempTopOFF = $powerAttack[$i];
						if($k == 0)//only use first one for the top attack
						$atkName = $powerName[$i];
						$tempDef = $powerDefence[$i];
						$topOffID = $i;
						}
						else if($powerAttack[$i] == $tempTopOFF)
						{
							if($powerDefence[$i] > $tempDef)
							{
								$tempTopOFF = $powerAttack[$i];
								if($k == 0)//only use first one for the top attack
								$atkName = $powerName[$i];
								$tempDef = $powerDefence[$i];
								$topOffID = $i;
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
						$topDefID = $i;
						}
						else if($powerAttack[$i] == $tempTopDEF)
						{
							if($powerDefence[$i] > $tempDef)
							{
								$tempTopDEF = $powerAttack[$i];
								if($k == 0)//only use first one for the top attack
								$defName = $powerName[$i];
								$tempDef = $powerDefence[$i];
								$topDefID = $i;
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
						$topUtilID = $i;
						}
						else if($powerAttack[$i] == $tempTopUTIL)
						{
							if($powerDefence[$i] > $tempDef)
							{
								$tempTopUTIL = $powerAttack[$i];
								if($k == 0)//only use first one for the top attack
								$utilName = $powerName[$i];
								$tempDef = $powerDefence[$i];
								$topUtilID = $i;
							}
						}
					}
					
					
					
					break;
				default:
					break;
			
			
		}
		
	
	}
	if($topOffID == -1 && $topDefID == -1 && $topUtilID == -1)
	{
		
		break;
		
	}
	if($topOffID != -1)
	{
		$powers[$topOffID]--;
		$topOFF += $tempTopOFF;
	}
	if($topDefID != -1)
	{
		$powers[$topDefID]--;
		$topDEF += $tempTopDEF;
	}
	if($topUtilID != -1)
	{
		$powers[$topUtilID]--;
		$topUTIL += $tempTopUTIL;
	}
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
$atkDamage = 0;
$defDamage = 0;
$krevelsWon = 0;
$dName = "DefaultName";
$dHealth = 0;
$dAtkName = '';
$dDefName = '';
$dUtilName = '';
$error = 0;
$errorMsg = '';
$itemDrop = 0;
$itemDropMsg = '';
$areaChange = 0;
if($_POST[posted] == "yes" && $uid != 0)
{
	$_POST[posted] = "no";
	
	if($c_stamina > 0 && $c_health >= $threshold)
	{
		$setHTime = 0;
		$setSTime = 0;
		if($c_stamina == $m_stamina)
			$setSTime = 1;
		if($c_health == $m_health)
			$setHTime = 1;
		$c_stamina--;
		$defender = $_POST['defender'];
		$defender = SanitizeString($defender);
		if($defender != 0)
		{
//get the details for the defender
		$query = "SELECT defence, krevels, name, faction, c_health, m_health, knights, friends, level FROM CW_Users WHERE id='" . $defender . "'";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		$defence = $row[0];
		$dKrevels = $row[1];
		$dName = $row[2];
		$dFaction = $row[3];
		$dHealth = $row[4];
		$dMHealth = $row[5];
		$dKnights = $row[6];
			if($dName == '')
				$dName = "Unnamed Chosen";
		
$dKnights += $row[7];
		if($dKnights > 500)
			$dKnights = 500;
		
		$dLevel = $row[8];
		$query = "SELECT jTier3 FROM CW_Missions WHERE id='" . $defender . "'";
		$result = mysql_query($query);
		$rows = mysql_fetch_row($result);
		if($rows[0] == 4 && $dFaction == DAMOS)
			$dKnights++;
			
		$query = "SELECT achievement7, achievement8, achievement12, achievement20 FROM CW_Achievements WHERE id='" . $defender . "'";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		$dDownButNotOut = $row[0];
		$dProveYourStrength = $row[1];
		$WalkingShield = $row[2];
		$dWealthyChosen = $row[3];
		
		$query = "SELECT power69, power70, power72 FROM CW_Inventory WHERE id='" . $defender . "'";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		$LeatherVest = $row[0];
		$dBoltThrower = $row[1];
		$Gauntlet = $row[2];
		
		//attacking one of your own?  Tsk tsk
		if($SurvivalOfTheFittest >= 0 && $faction == $dFaction)
		{
			if(++$SurvivalOfTheFittest == 10)
			{
				$SurvivalOfTheFittest = -1;
				$skills++;
				$query = "UPDATE CW_Users SET skills='" . $skills . "' WHERE id='" . $uid . "'";
				$result = mysql_query($query);
$displayAchievement = 6;
$achName = "Survival of the Fittest";
			}
			$query = "UPDATE CW_Achievements SET achievement6='" . $SurvivalOfTheFittest . "' WHERE id='" . $uid . "'";
			$result = mysql_query($query);

		}
		
		//don't be picky, attack EVERYBODY!
		if($EqualOpportunist > 0)
		{
			$newFaction = 1;
			for($a = 0; $a < strlen($EqualOpportunist); $a++)
			{
				if(substr($EqualOpportunist, $a, 1) == $dFaction)
				{
					$newFaction = 0;
					break;
				}
			}
			if($newFaction == 1)
			{
				
				$EqualOpportunist .= $dFaction;
				
				if(strlen($EqualOpportunist) == 5)
				{
					$EqualOpportunist = -1;
					$skills++;
					$query = "UPDATE CW_Users SET skills='" . $skills . "' WHERE id='" . $uid . "'";
					$result = mysql_query($query);
$displayAchievement = 9;
$achName = "Equal Opportunist";		
				}
				$query = "UPDATE CW_Achievements SET achievement9='" . $EqualOpportunist . "' WHERE id='" . $uid . "'";
				$result = mysql_query($query);

			}
		}
		else if($EqualOpportunist == 0)
		{
			$EqualOpportunist = $dFaction;
			$query = "UPDATE CW_Achievements SET achievement9='" . $EqualOpportunist . "' WHERE id='" . $uid . "'";
			$result = mysql_query($query);
		}
		
		//only do the fight if the defender has more than 20 health
		if($dHealth < 20)
		{
			$error = 1;
			$errorMsg = $dName . " does not have enough health.  Please attack someone else.<br/><br/>";
		}
		else
		{
		//make this next part a function
		
		$query = "SELECT * FROM CW_Inventory WHERE id='" . $defender . "'";//for real game, add WHERE to narrow it down to fb uid
		$result = mysql_query($query);
		
		if(!$result) die("Database access failed: " . mysql_error());
		$rows = mysql_fetch_row($result);
		
		for($i = 1; $i < mysql_num_fields($result); $i++)
		{
			$dPowers[] = $rows[$i];
		}
		
		$fillerArray = $dPowers;

		for($i = 0; $i < count($dPowers); $i++)
		{
			for($j = 0; $j < count($powerID); $j++)
			{
				
					if($i + 1 == $powerID[$j])
					{
						
						$dPowers[$j] = $fillerArray[$i];
						
					}//end if match
		
			}//end count id
		}//end count powers
		
		$query = "SELECT * FROM CW_Notices WHERE id='" . $defender . "'";//for real game, add WHERE to narrow it down to fb uid
		$result = mysql_query($query);
		
		if(!$result) die("Database access failed: " . mysql_error());
		$rows = mysql_fetch_row($result);
		$count = ((mysql_num_fields($result) - 1) / 2);
		for($i = 1; $i < ($count + 1); $i++)
		{
			$message[] = $rows[$i];
			$messageTime[] = $rows[$i + $count];
		}
		
		$dTopOFF = 0;
		$dTopDEF = 0;
		$dTopUTIL = 0;
		
		$newMessage = '';
		$tempAtk = 0;
		
		for($k = 0; $k < $dKnights; $k++)
		{
			$topOffID = -1;
			$topDefID = -1;
			$topUtilID = -1;
			$tempAtk = 0;
			$tempTopOFF = 0;
			$tempTopDEF = 0;
			$tempTopUTIL = 0;					
			for($i = 0; $i < count($dPowers); $i++)
			{
				
				
					switch($powerType[$i])
					{
						case 0:
							if($dPowers[$i] > 0)
							{
								if($powerDefence[$i] > $tempTopOFF)
								{
								$tempTopOFF = $powerDefence[$i];
								if($k == 0)
								$dAtkName = $powerName[$i];
								$tempAtk = $powerAttack[$i];
								$topOffID = $i;
								}
								else if($powerDefence[$i] == $tempTopOFF)
								{
									if($powerAttack[$i] > $tempAtk)
									{
										$tempTopOFF = $powerDefence[$i];
										if($k == 0)
										$dAtkName = $powerName[$i];
										$tempAtk = $powerAttack[$i];
										$topOffID = $i;
									}
								}
								
								
								
							}
							
							break;
						case 1:
							if($powerType[($i - 1)] != 1)
									$tempAtk = 0;
							if($dPowers[$i] > 0)
							{
								if($powerDefence[$i] > $tempTopDEF)
								{
								$tempTopDEF = $powerDefence[$i];
								if($k == 0)
								$dDefName = $powerName[$i];
								$tempAtk = $powerAttack[$i];
								$topDefID = $i;
								}
								else if($powerDefence[$i] == $tempTopDEF)
								{
									if($powerAttack[$i] > $tempAtk)
									{
										$tempTopDEF = $powerDefence[$i];
										if($k == 0)
										$dDefName = $powerName[$i];
										$tempAtk = $powerAttack[$i];
										$topDefID = $i;
									}
								}
								
							}
							break;
						case 2:
							if($powerType[($i - 1)] != 2)
									$tempAtk = 0;
							if($dPowers[$i] > 0)
							{
								if($powerDefence[$i] > $tempTopUTIL)
								{
								$tempTopUTIL = $powerDefence[$i];
								if($k == 0)
								$dUtilName = $powerName[$i];
								$tempAtk = $powerAttack[$i];
								$topUtilID = $i;
								}
								else if($powerDefence[$i] == $tempTopUTIL)
								{
									if($powerAttack[$i] > $tempAtk)
									{
										$tempTopUTIL = $powerDefence[$i];
										if($k == 0)
										$dUtilName = $powerName[$i];
										$tempAtk = $powerAttack[$i];
										$topUtilID = $i;
									}
								}
							}
							
							
							
							break;
						default:
							break;
					
					
				}
				
			
			}
			if($topOffID == -1 && $topDefID == -1 && $topUtilID == -1)
				{
		
		break;
		
	}
			if($topOffID != -1)
			{
				
				$dPowers[$topOffID]--;
				$dTopOFF += $tempTopOFF;
			}
			if($topDefID != -1)
			{
				
				$dPowers[$topDefID]--;
				$dTopDEF += $tempTopDEF;
			}
			if($topUtilID != -1)
			{
				
				$dPowers[$topUtilID]--;
				$dTopUTIL += $tempTopUTIL;
			}
			
		}
		$changeDHTime = 0;
		if($dHealth >= $dMHealth)
		{
			$changeDHTime = 1;
			
		}	
		//calculate attack and defence
		if($faction == RUBIA)
			$attack = (int)($attack * 1.1);
		if($dFaction == DAMOS)
			$defence = (int)($defence * 1.1);
		$topATK = $topOFF + $topDEF + $topUTIL;
		$topPRO = $dTopOFF + $dTopDEF + $dTopUTIL;
		$attack += $topOFF;
		$attack += $topDEF;
		$attack += $topUTIL;
		$defence += $dTopOFF;
		$defence += $dTopDEF;
		$defence += $dTopUTIL;
		$hasFought = 1;
		
		if($Katana)
		{
			$attack += 10;
			$query = "UPDATE CW_Inventory SET power68=power68 - 1 WHERE id='" . $uid . "'";
			$result = mysql_query($query);
		}
		if($LeatherVest)
		{
			$defence += 20;
			$query = "UPDATE CW_Inventory SET power69=power69 - 1 WHERE id='" . $defender . "'";
			$result = mysql_query($query);
		}
		
		
		$AchWalking = 0;
		$AchProve = 0;
		$AchDown = 0;
		$AchWealthy = 0;
		
		
		$difference = $attack - $defence;
		if($difference > 0)//this means the attacker wins
		{
			
			if($difference < 50)//close fight
			{
				
				$rand = rand(5, 25);
				$totalDamage = $rand;
				if($totalDamage % 2 == 0)
					$totalDamage++;
				$rand = 0.1 * rand(4,8);
				//damage the attacker receives
				$atkDamage = $totalDamage - (int)($rand * $totalDamage);
				$defDamage = $totalDamage - $atkDamage;
				if($rubiaGold == 1)
					$defDamage += 2;
				if($damosGold2 == 1)
					$atkDamage -= 2;
				if($BoltThrower)
				{
					$defDamage += 5;
					$query = "UPDATE CW_Inventory SET power70=power70 - 1 WHERE id='" . $uid . "'";
					$result = mysql_query($query);
				}
				if($dBoltThrower)
				{
					$atkDamage += 5;
					$query = "UPDATE CW_Inventory SET power70=power70 - 1 WHERE id='" . $defender . "'";
					$result = mysql_query($query);
				}
				if($Quarterstaff)
				{
					$atkDamage -= 5;
					if($atkDamage < 0)
						$atkDamage = 0;
					$query = "UPDATE CW_Inventory SET power71=power71 - 1 WHERE id='" . $uid . "'";
					$result = mysql_query($query);
				}
				if($Gauntlet)
				{
					$defDamage -= 5;
					if($defDamage < 0)
						$defDamage = 0;
					$query = "UPDATE CW_Inventory SET power72=power72 - 1 WHERE id='" . $defender . "'";
					$result = mysql_query($query);
				}
				$c_health -= $atkDamage;
				if($c_health < 0)
					$c_health = 0;
				
				$dHealth -= $defDamage;
				if($dHealth < 0)
					$dHealth = 0;
				
				if($atkDamage <= $defDamage)
				{
					$hasWon = 1;
					if($level > $dLevel + 20)
					$expWon = 1;
					else if($level > $dLevel + 5)
					$expWon = 2;
					else if($dLevel >= $level + 50)
					$expWon = 4;
					else if($dLevel >= $level + 100)
					$expWon = 5;
					else
					$expWon = 3;
				}
				else
				{
					$expWon = 1;
				}
			}
			else //solid victory
			{
				
				$rand = rand(5, 25);
				$totalDamage = $rand;
				if($totalDamage % 2 == 0)
					$totalDamage++;
				$rand = 0.1 * rand(6,10);
				//damage the attacker receives
				$atkDamage = $totalDamage - (int)($rand * $totalDamage);
				$defDamage = $totalDamage - $atkDamage;
				if($rubiaGold == 1)
					$defDamage += 2;
				if($damosGold2 == 1)
					$atkDamage -= 2;
				if($BoltThrower)
				{
					$defDamage += 5;
					$query = "UPDATE CW_Inventory SET power70=power70 - 1 WHERE id='" . $uid . "'";
					$result = mysql_query($query);
				}
				if($dBoltThrower)
				{
					$atkDamage += 5;
					$query = "UPDATE CW_Inventory SET power70=power70 - 1 WHERE id='" . $defender . "'";
					$result = mysql_query($query);
				}
				if($Quarterstaff)
				{
					$atkDamage -= 5;
					if($atkDamage < 0)
						$atkDamage = 0;
					$query = "UPDATE CW_Inventory SET power71=power71 - 1 WHERE id='" . $uid . "'";
					$result = mysql_query($query);
				}
				if($Gauntlet)
				{
					$defDamage -= 5;
					if($defDamage < 0)
						$defDamage = 0;
					$query = "UPDATE CW_Inventory SET power72=power72 - 1 WHERE id='" . $defender . "'";
					$result = mysql_query($query);
				}
				$c_health -= $atkDamage;
				if($c_health < 0)
					$c_health = 0;
				$dHealth -= $defDamage;
				if($dHealth < 0)
					$dHealth = 0;
				
				if($atkDamage <= $defDamage)
				{
					$hasWon = 1;
					if($level > $dLevel + 20)
					$expWon = 1;
					else if($level > $dLevel + 5)
					$expWon = 2;
					else if($dLevel >= $level + 50)
					$expWon = 4;
					else if($dLevel >= $level + 100)
					$expWon = 5;
					else
					$expWon = 3;
				}
				else
					$expWon = 1;
			}
		}
		else if($difference < 0)//this means the defender wins
		{
			
			if($difference > -50)//close one
			{
				
				$rand = rand(5, 25);
				$totalDamage = $rand;
				if($totalDamage % 2 == 0)
					$totalDamage++;
				$rand = 0.1 * rand(2,6);
				//damage the attacker receives
				$atkDamage = $totalDamage - (int)($rand * $totalDamage);
				$defDamage = $totalDamage - $atkDamage;
				if($rubiaGold == 1)
					$defDamage += 2;
				if($damosGold2 == 1)
					$atkDamage -= 2;
				if($BoltThrower)
				{
					$defDamage += 5;
					$query = "UPDATE CW_Inventory SET power70=power70 - 1 WHERE id='" . $uid . "'";
					$result = mysql_query($query);
				}
				if($dBoltThrower)
				{
					$atkDamage += 5;
					$query = "UPDATE CW_Inventory SET power70=power70 - 1 WHERE id='" . $defender . "'";
					$result = mysql_query($query);
				}
				if($Quarterstaff)
				{
					$atkDamage -= 5;
					if($atkDamage < 0)
						$atkDamage = 0;
					$query = "UPDATE CW_Inventory SET power71=power71 - 1 WHERE id='" . $uid . "'";
					$result = mysql_query($query);
				}
				if($Gauntlet)
				{
					$defDamage -= 5;
					if($defDamage < 0)
						$defDamage = 0;
					$query = "UPDATE CW_Inventory SET power72=power72 - 1 WHERE id='" . $defender . "'";
					$result = mysql_query($query);
				}
				$c_health -= $atkDamage;
				if($c_health < 0)
					$c_health = 0;
				$dHealth -= $defDamage;
				if($dHealth < 0)
					$dHealth = 0;
				
				if($atkDamage <= $defDamage)
				{
					$hasWon = 1;
					if($level > $dLevel + 20)
					$expWon = 1;
					else if($level > $dLevel + 5)
					$expWon = 2;
					else if($dLevel >= $level + 50)
					$expWon = 4;
					else if($dLevel >= $level + 100)
					$expWon = 5;
					else
					$expWon = 3;
				}
				else
					$expWon = 1;
			}
			else
			{
				
				$rand = rand(5, 25);
				$totalDamage = $rand;
				if($totalDamage % 2 == 0)
					$totalDamage++;
				$rand = 0.1 * rand(0,4);
				//damage the attacker receives
				$atkDamage = $totalDamage - (int)($rand * $totalDamage);
				$defDamage = $totalDamage - $atkDamage;
				if($rubiaGold == 1)
					$defDamage += 2;
				if($damosGold2 == 1)
					$atkDamage -= 2;
				if($BoltThrower)
				{
					$defDamage += 5;
					$query = "UPDATE CW_Inventory SET power70=power70 - 1 WHERE id='" . $uid . "'";
					$result = mysql_query($query);
				}
				if($dBoltThrower)
				{
					$atkDamage += 5;
					$query = "UPDATE CW_Inventory SET power70=power70 - 1 WHERE id='" . $defender . "'";
					$result = mysql_query($query);
				}
				if($Quarterstaff)
				{
					$atkDamage -= 5;
					if($atkDamage < 0)
						$atkDamage = 0;
					$query = "UPDATE CW_Inventory SET power71=power71 - 1 WHERE id='" . $uid . "'";
					$result = mysql_query($query);
				}
				if($Gauntlet)
				{
					$defDamage -= 5;
					if($defDamage < 0)
						$defDamage = 0;
					$query = "UPDATE CW_Inventory SET power72=power72 - 1 WHERE id='" . $defender . "'";
					$result = mysql_query($query);
				}
				$c_health -= $atkDamage;
				if($c_health < 0)
					$c_health = 0;
				$dHealth -= $defDamage;
				if($dHealth < 0)
					$dHealth = 0;
				
				if($atkDamage <= $defDamage)
				{
					$hasWon = 1;
					if($level > $dLevel + 20)
					$expWon = 1;
					else if($level > $dLevel + 5)
					$expWon = 2;
					else if($dLevel >= $level + 50)
					$expWon = 4;
					else if($dLevel >= $level + 100)
					$expWon = 5;
					else
					$expWon = 3;
				}
				else
					$expWon = 1;
			}
		}
		else//this means there was a tie
		{
			
			$rand = rand(5, 25);
				$totalDamage = $rand;
				if($totalDamage % 2 == 0)
					$totalDamage++;
				$rand = 0.1 * rand(3,7);
				//damage the attacker receives
				$atkDamage = $totalDamage - (int)($rand * $totalDamage);
				$defDamage = $totalDamage - $atkDamage;
				if($rubiaGold == 1)
					$defDamage += 2;
				if($damosGold2 == 1)
					$atkDamage -= 2;
				if($BoltThrower)
				{
					$defDamage += 5;
					$query = "UPDATE CW_Inventory SET power70=power70 - 1 WHERE id='" . $uid . "'";
					$result = mysql_query($query);
				}
				if($dBoltThrower)
				{
					$atkDamage += 5;
					$query = "UPDATE CW_Inventory SET power70=power70 - 1 WHERE id='" . $defender . "'";
					$result = mysql_query($query);
				}
				if($Quarterstaff)
				{
					$atkDamage -= 5;
					if($atkDamage < 0)
						$atkDamage = 0;
					$query = "UPDATE CW_Inventory SET power71=power71 - 1 WHERE id='" . $uid . "'";
					$result = mysql_query($query);
				}
				if($Gauntlet)
				{
					$defDamage -= 5;
					if($defDamage < 0)
						$defDamage = 0;
					$query = "UPDATE CW_Inventory SET power72=power72 - 1 WHERE id='" . $defender . "'";
					$result = mysql_query($query);
				}
			$c_health -= $atkDamage;
			if($c_health < 0)
					$c_health = 0;
			$dHealth -= $defDamage;
			if($dHealth < 0)
				$dHealth = 0;
			//winner based on who did more damage.  tie goes to attacker
			if($atkDamage <= $defDamage)
				{
					$hasWon = 1;
					if($level > $dLevel + 20)
					$expWon = 1;
					else if($level > $dLevel + 5)
					$expWon = 2;
					else if($dLevel >= $level + 50)
					$expWon = 4;
					else if($dLevel >= $level + 100)
					$expWon = 5;
					else
					$expWon = 3;
				}
				else
					$expWon = 1;
			
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
		
		$query = "UPDATE CW_Users SET c_health='" . $dHealth . "' WHERE id='" . $defender . "'";
		$result = mysql_query($query);
		
		if($EliminationRound >= 0 && $dHealth == 0)
		{
			$skills++;
			$query = "UPDATE CW_Users SET skills='" . $skills . "' WHERE id='" . $uid . "'";
			$result = mysql_query($query);
			$EliminationRound = -1;
			$query = "UPDATE CW_Achievements SET achievement10='-1' WHERE id='" . $uid . "'";
			$result = mysql_query($query);
$displayAchievement = 10;
$achName = "Elimination Round";
		}
		
		if($LastChosenStanding >= 0 && $dHealth < 20)
		{
			if(++$LastChosenStanding == 20)
			{
				$skills++;
				$query = "UPDATE CW_Users SET skills='" . $skills . "' WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$LastChosenStanding = -1;
$displayAchievement = 11;
$achName = "Last Chosen Standing";
			}
			$query = "UPDATE CW_Achievements SET achievement11='" . $LastChosenStanding . "' WHERE id='" . $uid . "'";
			$result = mysql_query($query);
		}
		
		if($hasWon == 1)
		{
			$krevelsWon = (int)($dKrevels/10);
			
			//Wanna win?  Gotta win lots!
			if($ProveYourStrength >= 0)
			{
				if(++$ProveYourStrength == 100)
				{
					$ProveYourStrength = -1;
					$skills++;
					$query = "UPDATE CW_Users SET skills='" . $skills . "' WHERE id='" . $uid . "'";
					$result = mysql_query($query);
$displayAchievement = 8;
$achName = "Prove Your Strength";
				}
				
				$query = "UPDATE CW_Achievements SET achievement8='" . $ProveYourStrength . "' WHERE id='" . $uid . "'";
				$result = mysql_query($query);

			}
			
			//too good for this
			if($DownButNotOut >= 0)
			{
				$DownButNotOut = 0;
				$query = "UPDATE CW_Achievements SET achievement7='0' WHERE id='" . $uid . "'";
				$result = mysql_query($query);
			}
			
			//Even an achievement for losing.  How nice of us!  (For the defender)
			if($dDownButNotOut >= 0)
			{
				if(++$dDownButNotOut == 10)
				{
					$dDownButNotOut = -1;
					$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $defender . "'";
					$result = mysql_query($query);
					$AchDown = 1;
				}
				$query = "UPDATE CW_Achievements SET achievement7='" . $dDownButNotOut . "' WHERE id='" . $defender . "'";
				$result = mysql_query($query);
			}
			
			//too good for this
			if($dProveYourStrength >= 0)
			{
				$dProveYourStrength = 0;
				$query = "UPDATE CW_Achievements SET achievement8='0' WHERE id='" . $defender . "'";
				$result = mysql_query($query);
			}
			
			if($krevelsWon > 0)
			{
				if($krevelsWon > 65000)
					$krevelsWon = 65000;
				$dKrevels -= $krevelsWon;
				$krevels += $krevelsWon;
				
			}
			if($WealthyChosen >= 0 && ($krevels + $bank >= 1000000))
			{
				$WealthyChosen = -1;
				$skills++;
				$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$query = "UPDATE CW_Achievements SET achievement20='" . $WealthyChosen . "' WHERE id='" . $uid . "'";
				$result = mysql_query($query);
$displayAchievement = 20;
$achName = "One Krevel... Two Krevels...";
			}
			$query = "UPDATE CW_Users SET krevels='" . $krevels . "', fightsWon=fightsWon + 1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$query = "UPDATE CW_Users SET krevels='" . $dKrevels . "', fightsLost=fightsLost + 1 WHERE id='" . $defender . "'";
				$result = mysql_query($query);
				$newMessage = "You were attacked by " . $name . ".<br/>You lost the fight, and " . $name . " stole " . $krevelsWon . " krevels.";
				if(strncmp($newMessage, $message[0], 26) != 0)
				{
					$newtime = time();
					$newtime = date("Y-m-d H:i:s", $newtime);
					
					for ($i = count($message) - 1; $i > 0; $i--)
					{
						$message[$i] = $message[$i - 1];
					}
					for ($i = count($messageTime) - 1; $i > 0; $i--)
					{
						$messageTime[$i] = $messageTime[$i - 1];
					}
					$message[0] = $newMessage;
					$messageTime[0] = $newtime;
					$query = "UPDATE CW_Notices SET message1='" . $message[0] . "', message2='" . $message[1] . "', message3='" . $message[2] . "', message4='" . $message[3] . "', message5='" . $message[4] . "', messageTime1='" . $messageTime[0] . "', messageTime2='" . $messageTime[1] . "', messageTime3='" . $messageTime[2] . "', messageTime4='" . $messageTime[3] . "', messageTime5='" . $messageTime[4] . "' WHERE id='" . $defender . "'";
					$result = mysql_query($query);
				}
				if($AchDown == 1)
				{
					$newtime = time();
					$newtime = date("Y-m-d H:i:s", $newtime);
					
					for ($i = count($message) - 1; $i > 0; $i--)
					{
						$message[$i] = $message[$i - 1];
					}
					for ($i = count($messageTime) - 1; $i > 0; $i--)
					{
						$messageTime[$i] = $messageTime[$i - 1];
					}
					$message[0] = "You have earned the Down But Not Out achievement for losing 10 duels in a row!  You earned 1 skill point.";
					$messageTime[0] = $newtime;
					$query = "UPDATE CW_Notices SET message1='" . $message[0] . "', message2='" . $message[1] . "', message3='" . $message[2] . "', message4='" . $message[3] . "', message5='" . $message[4] . "', messageTime1='" . $messageTime[0] . "', messageTime2='" . $messageTime[1] . "', messageTime3='" . $messageTime[2] . "', messageTime4='" . $messageTime[3] . "', messageTime5='" . $messageTime[4] . "' WHERE id='" . $defender . "'";
					$result = mysql_query($query);
				}
			
		}
		else
		{
			$krevelsWon = (int)($krevels/10);
			
			//Can't touch this *NA NA NA NA*
			if($WalkingShield >= 0)
			{
				$WalkingShield++;
				if($WalkingShield == 50)
				{
					$WalkingShield = -1;
					$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $defender . "'";
					$result = mysql_query($query);
					$AchWalking = 1;
				}
				$query = "UPDATE CW_Achievements SET achievement12='" . $WalkingShield . "' WHERE id='" . $defender . "'";
				$result = mysql_query($query);
			}
			
			//Even an achievement for losing.  How nice of us!
			if($DownButNotOut >= 0)
			{
				if(++$DownButNotOut == 10)
				{
					$DownButNotOut = -1;
					$skills++;
					$query = "UPDATE CW_Users SET skills='" . $skills . "' WHERE id='" . $uid . "'";
					$result = mysql_query($query);
$displayAchievement = 7;
$achName = "Down But Not Out";
				}
				
				$query = "UPDATE CW_Achievements SET achievement7='" . $DownButNotOut . "' WHERE id='" . $uid . "'";
				$result = mysql_query($query);
			}
			//Your best isn't good enough
			if($ProveYourStrength >= 0)
			{
				$ProveYourStrength = 0;
				$query = "UPDATE CW_Achievements SET achievement8='0' WHERE id='" . $uid . "'";
				$result = mysql_query($query);
			}
			
			if($dProveYourStrength >= 0)
			{
				if(++$dProveYourStrength == 100)
				{
					$dProveYourStrength = -1;
					$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $defender . "'";
					$result = mysql_query($query);
					$AchProve = 1;
				}
				
				$query = "UPDATE CW_Achievements SET achievement8='" . $dProveYourStrength . "' WHERE id='" . $defender . "'";
				$result = mysql_query($query);
			}
			
			//too good for this
			if($dDownButNotOut >= 0)
			{
				$dDownButNotOut = 0;
				$query = "UPDATE CW_Achievements SET achievement7='0' WHERE id='" . $defender . "'";
				$result = mysql_query($query);
			}
			
			
			if($krevelsWon > 0)
			{
				if($krevelsWon > 65000)
					$krevelsWon = 65000;
				$dKrevels += $krevelsWon;
				$krevels -= $krevelsWon;
				
			}
			//if I had a million krevels...
			if($dWealthyChosen >= 0 && $dKrevels >= 1000000)
			{
				
				$dWealthyChosen = -1;
				$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $defender . "'";
				$result = mysql_query($query);
				$query = "UPDATE CW_Achievements SET achievement20='" . $dWealthyChosen . "' WHERE id='" . $defender . "'";
				$result = mysql_query($query);
				$AchWealthy = 1;
			}
			$query = "UPDATE CW_Users SET krevels='" . $krevels . "', fightsLost=fightsLost + 1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
//let the defender know
				$query = "UPDATE CW_Users SET krevels='" . $dKrevels . "', fightsWon=fightsWon + 1 WHERE id='" . $defender . "'";
				$result = mysql_query($query);
				$newMessage = "You were attacked by " . $name . ".<br/>You won the fight, and claimed " . $krevelsWon . " krevels from " . $name . ".";
				if(strncmp($newMessage, $message[0], 26) != 0)
				{
					$newtime = time();
					$newtime = date("Y-m-d H:i:s", $newtime);
					
					for ($i = count($message) - 1; $i > 0; $i--)
					{
						$message[$i] = $message[$i - 1];
					}
					for ($i = count($messageTime) - 1; $i > 0; $i--)
					{
						$messageTime[$i] = $messageTime[$i - 1];
					}
					$message[0] = $newMessage;
					$messageTime[0] = $newtime;
					$query = "UPDATE CW_Notices SET message1='" . $message[0] . "', message2='" . $message[1] . "', message3='" . $message[2] . "', message4='" . $message[3] . "', message5='" . $message[4] . "', messageTime1='" . $messageTime[0] . "', messageTime2='" . $messageTime[1] . "', messageTime3='" . $messageTime[2] . "', messageTime4='" . $messageTime[3] . "', messageTime5='" . $messageTime[4] . "' WHERE id='" . $defender . "'";
					$result = mysql_query($query);
				}
			if($AchWalking == 1)
				{
					$newtime = time();
					$newtime = date("Y-m-d H:i:s", $newtime);
					
					for ($i = count($message) - 1; $i > 0; $i--)
					{
						$message[$i] = $message[$i - 1];
					}
					for ($i = count($messageTime) - 1; $i > 0; $i--)
					{
						$messageTime[$i] = $messageTime[$i - 1];
					}
					$message[0] = "You have earned the Walking Shield achievement for successfully defending 50 times!  You earned 1 skill point.";
					$messageTime[0] = $newtime;
					$query = "UPDATE CW_Notices SET message1='" . $message[0] . "', message2='" . $message[1] . "', message3='" . $message[2] . "', message4='" . $message[3] . "', message5='" . $message[4] . "', messageTime1='" . $messageTime[0] . "', messageTime2='" . $messageTime[1] . "', messageTime3='" . $messageTime[2] . "', messageTime4='" . $messageTime[3] . "', messageTime5='" . $messageTime[4] . "' WHERE id='" . $defender . "'";
					$result = mysql_query($query);
				}
			if($AchProve == 1)
				{
					$newtime = time();
					$newtime = date("Y-m-d H:i:s", $newtime);
					
					for ($i = count($message) - 1; $i > 0; $i--)
					{
						$message[$i] = $message[$i - 1];
					}
					for ($i = count($messageTime) - 1; $i > 0; $i--)
					{
						$messageTime[$i] = $messageTime[$i - 1];
					}
					$message[0] = "You have earned the Prove Your Strength achievement for winning 100 duels in a row!  You earned 1 skill point.";
					$messageTime[0] = $newtime;
					$query = "UPDATE CW_Notices SET message1='" . $message[0] . "', message2='" . $message[1] . "', message3='" . $message[2] . "', message4='" . $message[3] . "', message5='" . $message[4] . "', messageTime1='" . $messageTime[0] . "', messageTime2='" . $messageTime[1] . "', messageTime3='" . $messageTime[2] . "', messageTime4='" . $messageTime[3] . "', messageTime5='" . $messageTime[4] . "' WHERE id='" . $defender . "'";
					$result = mysql_query($query);
				}
			if($AchWealthy == 1)
				{
					$newtime = time();
					$newtime = date("Y-m-d H:i:s", $newtime);
					
					for ($i = count($message) - 1; $i > 0; $i--)
					{
						$message[$i] = $message[$i - 1];
					}
					for ($i = count($messageTime) - 1; $i > 0; $i--)
					{
						$messageTime[$i] = $messageTime[$i - 1];
					}
					$message[0] = "You have earned the Wealthy Chosen achievement for having 1 million krevels!  You earned 1 skill point.";
					$messageTime[0] = $newtime;
					$query = "UPDATE CW_Notices SET message1='" . $message[0] . "', message2='" . $message[1] . "', message3='" . $message[2] . "', message4='" . $message[3] . "', message5='" . $message[4] . "', messageTime1='" . $messageTime[0] . "', messageTime2='" . $messageTime[1] . "', messageTime3='" . $messageTime[2] . "', messageTime4='" . $messageTime[3] . "', messageTime5='" . $messageTime[4] . "' WHERE id='" . $defender . "'";
					$result = mysql_query($query);
				}
			
		}
		
		
		$rand = rand(0,100);
		if($rand < 15)
		{
			$rarity = 0;
			$rand = rand(0,99);
			if($rand < 30)
				$rarity = 1;
			else if($rand < 40)
				$rarity = 2;
			$query = "SELECT id, name FROM CW_Items WHERE earned='2' AND rarity='" . $rarity . "'";
			$result = mysql_query($query);
			$rows = mysql_num_rows($result);
			
			if($rows > 0)
			{
				$rand = rand(0,--$rows);
				$id = mysql_result($result, $rand, 'id');
				$itemDrop = 1;
				$tempMsg = mysql_result($result, $rand, 'name');
				$query = "SELECT power" . $id . " FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$rows = mysql_fetch_row($result);
				if($rows[0] == 0)
				{
					if($Repertoire >= 0 && ++$powerTotal >= 50 && $id != 84 && $id != 85)
					{
						$Repertoire = -1;
						$skills++;
						$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $uid . "'";
						$result = mysql_query($query);
						$query = "UPDATE CW_Achievements SET achievement16='" . $Repertoire . "' WHERE id='" . $uid . "'";
						$result = mysql_query($query);
$displayAchievement = 16;
$achName = "Repertoire";
					}
if($id == 84 || $id == 85)
{
if($id == 84)
$numCollection = 3;
else
$numCollection = 4;
if($AllSetForSand > 0)
				{
					$newCollect = 1;
					for($a = 0; $a < strlen($AllSetForSand); $a++)
					{
						if(substr($AllSetForSand, $a, 1) == $numCollection)
						{
							$newCollect = 0;
							break;
						}
					}
					if($newCollect == 1)
					{
						
						$AllSetForSand .= $numCollection;
						
						if(strlen($AllSetForSand) == 8)
						{
							$AllSetForSand = -1;
							$skills++;
							$query = "UPDATE CW_Users SET skills='" . $skills . "' WHERE id='" . $uid . "'";
							$result = mysql_query($query);
							$displayAchievement = 31;
							$achName = "All Set For Sand";		
						}
						$query = "UPDATE CW_Achievements SET achievement31='" . $AllSetForSand . "' WHERE id='" . $uid . "'";
						$result = mysql_query($query);

					}
				}
				else if($AllSetForSand == 0)
				{
					$AllSetForSand = $numCollection;
					$query = "UPDATE CW_Achievements SET achievement31='" . $AllSetForSand . "' WHERE id='" . $uid . "'";
					$result = mysql_query($query);
				}
}
else if($id == 99 || $id == 101)
{
if($id == 99)
$numCollection = 2;
else
$numCollection = 4;
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
}

else
{
					$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id='" . $uid . "'";
					$result = mysql_query($query);
}
				}
				$query = "UPDATE CW_Inventory SET power" . $id . "=power" . $id . " + 1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				
				$itemDropMsg = "<div style='position:absolute; width:100%; top:4px; text-align:center; left:0px;'><b>You learned</b> </div><div style='width:100%; text-align:center; position:absolute; top:18px; left:0px;'><img src='assets/power" . $id . ".jpg'/></div><div style='width:100%; text-align:center; position:absolute; top:80px; left:0px;'>" . $tempMsg . "</div>";
				
			}
		}
		
		if($setHTime == 1)
		{
			if($faction == JEDEITE)
				$hTime= time() + 3 * 60;
			else
				$hTime= time() + 4 * 60;
			$hTime= date("Y-m-d H:i:s", $hTime);
			$query = "UPDATE CW_Users SET hTime='" . $hTime . "' WHERE id='" . $uid . "'";
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
			if($changeDHTime)
			{
				if($dFaction == JEDEITE)
					$dhTime= time() + 3 * 60;
				else
					$dhTime= time() + 4 * 60;
				$dhTime= date("Y-m-d H:i:s", $dhTime);
				$query = "UPDATE CW_Users SET hTime='" . $dhTime . "' WHERE id='" . $defender . "'";
				$result = mysql_query($query);
			}
		}//end if defender's health is >= 20
		}
	}
	else if ($c_stamina <= 0)
	{
		$error = 1;
		$errorMsg = "Not enough stamina.";
	}
	else
	{
		$error = 1;
		$errorMsg = "Not enough health.";
	}
	
		
}

$query = "SELECT id, name, defence, faction, level, knights, friends FROM CW_Users WHERE id!='" . $uid . "' AND c_health>='20' AND faction>'0' AND region='" . $region . "'";//for real game, add WHERE to narrow it down to fb uid
$result = mysql_query($query);

$numFighters = 10;
$numrows = mysql_num_rows($result);
if($numrows < $numFighters)
	$numFighters = $numrows;
	
for($i = 0; $i < $numFighters; $i++)
{
	$rand = rand(1, $numrows);
	$rand--;
	
	$break = 0;
	for($j = 0; $j < count($fighter, 0); $j++)
	{

		if($fighter[$j][0] == mysql_result($result, $rand, 'id'))
		{
			$break = 1;
			break;
		}
	}
	if($break == 1)
	{
		$i--;
		continue;
	}
	
	$fighter[$i][0] = mysql_result($result, $rand, 'id');
	$fighter[$i][1] = mysql_result($result, $rand, 'name');
	$fighter[$i][2] = mysql_result($result, $rand, 'defence');
	$fighter[$i][3] = mysql_result($result, $rand, 'faction');
	$fighter[$i][4] = mysql_result($result, $rand, 'level');
	$fighter[$i][5] = mysql_result($result, $rand, 'knights');
        $fighter[$i][5] += mysql_result($result, $rand, 'friends');
	
	
	
}
/*
if(!$itemDrop)
{
$itemDrop = 1;
$itemDropMsg = "<div style='position:absolute; width:100%; top:4px; left:0px; text-align:center;'><b>You learned</b> </div><div style='width:100%; text-align:center; position:absolute; top:18px; left:0px;'><img src='assets/power1.jpg'/></div><div style='width:100%; text-align:center; position:absolute; top:80px;left:0px;'>Arrow</div>";
}
*/
$next_exp = GetExpToNextLevel($level);
$this_exp = GetExpToNextLevel($level - 1);
$expToNext = $next_exp - $experience;
$nextLevel = $next_exp - $this_exp;
$expWidth = (int)(($nextLevel - $expToNext) / $nextLevel * 100);
$sKrevels = number_format($krevels);
$expToThis = $nextLevel - $expToNext;
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
<title>Duel</title>
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
    FB.Canvas.setSize({width:720, height:1600});
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

_END;
if($region == 0)
{
echo "<div class='powerButtons0' style='top:263px;'><div style='position:absolute; left:275px; top:5px;'><a class='button' href='duel.php?region=1&token=$access_token'>SAPPHIRE</a></div>";
if($level < 81)
echo "<div style='position:absolute; left:425px;top:5px;'>JADE<br/>Levels 81+</div>";
else
echo "<div style='position:absolute; left:425px;top:5px;'><a href='duel.php?region=2&token=$access_token'>JADE</a></div>";
echo "</div>";
}
else if($region == 1)
{
echo "<div class='powerButtons1' style='top:263px;'><div style='position:absolute; left:275px; top:5px;'><a href='duel.php?region=1&token=$access_token'>SAPPHIRE</a></div>";

echo "<div style='position:absolute; left:425px;top:5px;'><a class='button' href='duel.php?region=2&token=$access_token'>JADE</a></div>";
echo "</div>";
}
echo <<<_END
<div class="fightButtons0" style="top:308px;">
<table style="top:8px; width:250px; text-align:center; position:absolute; left:245px;">
<tr><td><a class='button' href="duel.php?token=$access_token">DUELS</duel></td><td><a href="wars.php?token=$access_token">WARS</a></td></tr></table>
</div>
<div class="explain" style="color:black; height:112px; background-image:url('assets/Box_DuelDescription.png');">
<div style="color:orange; width:744px; position:relative; top:13px;">DUELS</div><br/>
<div style="position:relative; left:8px; width:718px;">Combat will help you to grow stronger. Use stamina to attack other Chosen and their Knights to gain experience, steal krevels and occasionally learn new powers. For every Knight you have (limit 500) you will use an additional offensive, defensive, and utility power while dueling. <br/>NOTE:  YOU CAN ONLY FIGHT CHOSEN IN THE SAME AREA AS YOU
</div>
</div>
<div class='whitestripe' style="position:absolute; left:0px; width:760px; background-image:url('assets/WhiteStripe.jpg'); top:253px; height:4px;"> 
</div>
_END;
if($hasFought == 1 || $error == 1)
{
echo "<div class='fightresults' style='color:black; top:353px;'>";
if($hasFought == 1)
{
echo "<div style='position:absolute; top: 10px; left:10px; width:300px;'>";

if($hasWon == 1)
{
	$winOrLose = "won";
	echo "<b>You " . $winOrLose . " the fight against <a href='profile.php?token=$access_token&view=$defender' class='duel'>" . $dName . "</a>.</b><br/> You dealt " . $defDamage . " damage and took " . $atkDamage . " damage.  You won " . $krevelsWon . " krevels and earned " . $expWon . " EXP. ";
}
else
{
	$winOrLose = "lost";
	echo "<b>You " . $winOrLose . " the fight against <a href='profile.php?token=$access_token&view=$defender' class='duel'>" . $dName . "</a>.</b><br/> You dealt " . $defDamage . " damage and took " . $atkDamage . " damage.  You lost " . $krevelsWon . " krevels and earned " . $expWon . " EXP. ";
}



echo "<br/>Your attack: " . $rawATK . " skill + " . $topATK . " powers = " . ($rawATK + $topATK) . ".</div>";
echo "<div style='position:absolute; top:10px; left:320px; width:100px;'>";
echo "<b>Top attack:</b><br/>";
if($atkName == '' && $defName == '' && $utilName == '')
	echo "Nothing. ";
else if($defName == '' && $utilName == '')
	echo $atkName . ". ";
else if($atkName == '' && $defName == '')
	echo $utilName . ". ";
else if($atkName == '' && $utilName == '')
	echo  $defName . ". ";
else if($utilName == '')
	echo $atkName . ", ". $defName . ". ";
else if($atkName == '')
	echo $defName . ", " . $utilName . ". ";
else if($defName == '')
	echo $atkName . ", " . $utilName . ". ";
else
	echo $atkName . ", " . $defName . ", " . $utilName . ". ";
echo "</div>";



if($c_health >= $threshold && $c_stamina > 0 && $dHealth >= 20)
{
echo "<div style='position:absolute; top:22px; left:652px;'><form method='post' action='duel.php'><input type='hidden'  name='defender' value=" . $defender ."/>";
echo "<input type='hidden' name='posted' value='yes'/><input type='hidden' name='token' value='" . $access_token . "'/>";
echo "<input type='image' src='assets/BTN_Again.png' value='Again'/></form></div>";
}
else if($dHealth < 20)
{
	echo "<div style='position:absolute; top:15px; left:615px; width:126px;'><img src='assets/Icon_BringDownOpponent.png'/></div>";
}

}
if($error == 1)
{
	echo "<div style='position:absolute; top:10px;'>" . $errorMsg . "</div>";
}
if($levelUp == 1)
{
echo "<div style='position:absolute; top:16px; left:430px; width:100px;'><img src='assets/Icon_LevelUp.png'/>";
echo "</div>";
}
if($itemDrop == 1)
{
	echo "<div style='position:absolute; top:4px; left:475px; width:180px; text-align:center;'>" . $itemDropMsg . "</div>";
}
echo "</div>";
}



echo "<div class='fighters'";
if($error == 1 || $hasFought == 1)
	echo " style='top:463px;'>";
else
	echo " style='top:353px;'>";



for($i = 0; $i < $numFighters; $i++)
{	
	$defID = $fighter[$i][0];
	$defName = $fighter[$i][1];
	$defDEF = $fighter[$i][2];
	$defFac = $fighter[$i][3];
	$defLevel = $fighter[$i][4];
	$defKnights = $fighter[$i][5];
	if($defKnights > 500)
		$defKnights = 500;
	if($defName == '')
		$defName = "Unnamed Chosen";
	
echo <<<_END
<div class='fighterbox' style='color:black;'>
<div style="position:absolute; top:4px; left:9px;">
<a class='leaderboard' href='profile.php?token=$access_token&view=$defID'>$defName</a>
</div>
<div style="position:absolute; top:32px; left:8px;">
_END;
echo "<img src='assets/FactionIcon" . $defFac . ".jpg'/>";
echo <<<_END
</div>

<table style="position:absolute; top:30px; left:100px;">
<tr style="height:20px;">
<td style="width:50px;">LEVEL:</td><td style="width:50px; color:red;">$defLevel</td>
</tr>
<tr style="height:20px;">
<td style="width:50px;">KNIGHTS:</td><td style="width:50px; color:red;">$defKnights</td>
</tr>
</table>
<div style="position:absolute; top:30px; left:655px;">
<form method="post" action="duel.php">
	<input type="hidden" name="posted" value="yes"/>
_END;
	echo "<input type='hidden' name='defender' value=" . $defID ."/><input type='hidden' name='token' value='" . $access_token . "'/>";
	echo "<input type='image' src='assets/BTN_Go.png' value='Duel'/></form>";
echo <<<_END
</div>
</div>
<div style='width:1px; height:8px; position:relative;'></div>
_END;
}

echo <<<_END

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
