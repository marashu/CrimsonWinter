<?php
require_once 'src/facebook.php';
require_once 'db_vars.php';
require_once 'functions.php';
require_once 'cw_vars.php';

$app_id = APP_ID;
    $app_secret = APP_SECRET;
    $my_url = "http://apps.facebook.com/crimsonwinter/boss.php";
    
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

//get friend details
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
$c_stamina = $rows[12];
$m_stamina = $rows[13];
$c_health = $rows[14];
$m_health = $rows[15];
$hTime = $rows[16];
$sTime = $rows[17];
$faction = $rows[19];
$played = $rows[29];
$sandTime = $rows[34];
$sandHealth = $rows[35];
$region = $rows[37];
$oujouHealth = $rows[48];
$oujouTime = $rows[49];


//change regions if necessary
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

$query = "SELECT achievement36, achievement37 FROM CW_Achievements WHERE id='" . $uid . "'";
$result = mysql_query($query);
$row = mysql_fetch_row($result);
$RememberFear = $row[0];
$HitMeAgain = $row[1];


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

//check boss timing
$fightExpire = 0;
$showExpire = 0;
$newtime = time();
//region 0 is Sir Sabien
if($region == 0)
{
	$query = "SELECT UNIX_TIMESTAMP('" . $sandTime. "') as sandDiff";//for real game, add WHERE to narrow it down to fb uid
	$result = mysql_query($query);
	$result = mysql_fetch_assoc($result);
		
	$result['sandDiff'] -= $newtime;
	
	if($result['sandDiff'] < 0)//the fight lasts 24 hours.  longer than that, and it resets
	{
		$fightExpire = 1;
		if($sandHealth < 5000)
		{
			$query = "UPDATE CW_Users SET sandHealth=5000, sandHelp='' WHERE id='$uid'";
			$result = mysql_query($query);
			$showExpire = 1;
		}
	}
	else
	{
		
		$newtime = date("Y-m-d H:i:s", $newtime);
		$query = "SELECT TIMEDIFF('" . $sandTime. "', '" . $newtime . "') as diff";
		$result = mysql_query($query);
		$result = mysql_fetch_assoc($result);
		$diffSandTime = $result['diff'];
	}
}
//region 1 is the Oujou
else if($region == 1)
{
	$query = "SELECT UNIX_TIMESTAMP('" . $oujouTime. "') as sandDiff";//for real game, add WHERE to narrow it down to fb uid
	$result = mysql_query($query);
	$result = mysql_fetch_assoc($result);
		
	$result['sandDiff'] -= $newtime;
	
	if($result['sandDiff'] < 0)
	{
		$fightExpire = 1;
		if($oujouHealth < 14000)
		{
			$query = "UPDATE CW_Users SET oujouHealth=14000, oujouHelp='' WHERE id='$uid'";
			$result = mysql_query($query);
			$showExpire = 1;
		}
	}
	else
	{
		
		$newtime = date("Y-m-d H:i:s", $newtime);
		$query = "SELECT TIMEDIFF('" . $oujouTime. "', '" . $newtime . "') as diff";
		$result = mysql_query($query);
		$result = mysql_fetch_assoc($result);
		$diffSandTime = $result['diff'];
	}
}
		$showDrop = 0;
$error = 0;
$errorMsg = '';

//error handling
if(!empty($_REQUEST['fight']))
{
	if($fightExpire == 1)//start a new fight
	{
		if($region == 0)
		{
			$fightExpire = 0;
			if($c_energy < 50)
			{
				$errorMsg = "You are tired.  Challenge me when you are ready.";
				$error = 1;
			}
			else if($c_health < 10)
			{
				$errorMsg = "You're too badly injured.  Return after you have recovered.<br/><a class='faction' href='flaqqers.php?token=$access_token'>Buy flaqqers with krevels to speed things up!</a>";
				$error = 1;
			}
			else
			{
				$sandTime = time() + (60 * 60 * 24);
				$sandTime = date("Y-m-d H:i:s", $sandTime);
				if($c_energy >= $m_energy && $c_energy - 50 < $m_energy)
				{
					if($faction == SAPPHIROS)
						$time= time() + (2 * 60) + 30;
					else
						$time= time() + 3 * 60;
					$time= date("Y-m-d H:i:s", $time);
				}
				$c_energy -= 50;
				$query = "UPDATE CW_Users SET sandTime='" . $sandTime . "', c_energy='" . $c_energy . "', time='" . $time . "'  WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$newtime = time();
				$newtime = date("Y-m-d H:i:s", $newtime);
				$query = "SELECT TIMEDIFF('" . $sandTime. "', '" . $newtime . "') as diff";
				$result = mysql_query($query);
				$result = mysql_fetch_assoc($result);
				$diffSandTime = $result['diff'];
			}
		}
		else if($region == 1)
		{
			$fightExpire = 0;
			if($c_energy < 50)
			{
				$errorMsg = "You are not prepared for this fight.  Recover energy and try again.";
				$error = 1;
			}
			else if($c_health < 10)
			{
				$errorMsg = "You're too badly injured.  Return after you have recovered.<br/><a class='faction' href='flaqqers.php?token=$access_token'>Buy flaqqers with krevels to speed things up!</a>";
				$error = 1;
			}
			else
			{
				$oujouTime = time() + (60 * 60 * 24);
				$oujouTime = date("Y-m-d H:i:s", $oujouTime);
				if($c_energy >= $m_energy && $c_energy - 50 < $m_energy)
				{
					if($faction == SAPPHIROS)
						$time= time() + (2 * 60) + 30;
					else
						$time= time() + 3 * 60;
					$time= date("Y-m-d H:i:s", $time);
				}
				$c_energy -= 50;
				$query = "UPDATE CW_Users SET oujouTime='" . $oujouTime . "', c_energy='" . $c_energy . "', time='" . $time . "'  WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$newtime = time();
				$newtime = date("Y-m-d H:i:s", $newtime);
				$query = "SELECT TIMEDIFF('" . $oujouTime. "', '" . $newtime . "') as diff";
				$result = mysql_query($query);
				$result = mysql_fetch_assoc($result);
				$diffSandTime = $result['diff'];
			}
		}
	}
	else
	{
		if($region == 0)
		{
		if($c_health < 10)
		{
			$errorMsg = "You're too badly hurt to keep fighting Sir Sabien.  Regroup and keep fighting later.<br/><a class='faction' href='flaqqers.php?token=$access_token'>Buy flaqqers with krevels to speed things up!</a>";
			$error = 1;
		}
		else if($sandHealth <= 0)
		{
			$errorMsg = "You have proven yourself already.  Come back tomorrow.";
			$error = 1;
		}
		}
		else if($region == 1)
		{
		if($c_health < 10)
		{
			$errorMsg = "You're too badly hurt to keep fighting the Oujou.  Regroup and keep fighting later.<br/><a class='faction' href='flaqqers.php?token=$access_token'>Buy flaqqers with krevels to speed things up!</a>";
			$error = 1;
		}
		else if($oujouHealth <= 0)
		{
			$errorMsg = "The halls are silent. The Oujou are nowhere to be seen.";
			$error = 1;
		}
		}
	}
	if(!$error)//pick up your weapons AND FIGHT
	{
		$error = 1;
		$rand1 = rand(0,40) + 70;
		if($region == 0)
		$sandHealth -= $rand1;
		else
		$oujouHealth -= $rand1;
		$rand = rand(0,10) + 10;
		if($c_health >= $m_health && $c_health - $rand < $m_health)
		{
		if($faction == JEDEITE)
				$hTime= time() + 3 * 60;
			else
				$hTime= time() + 4 * 60;
			$hTime= date("Y-m-d H:i:s", $hTime);
			$query = "UPDATE CW_Users SET hTime='" . $hTime . "' WHERE id='" . $uid . "'";
			$result = mysql_query($query);
		}
		$c_health -= $rand;
		if($c_health < 0)
			$c_health = 0;
		//$errorMsg = "A riveting exchange!  You deal $rand1 damage to Sir Sabien, and take $rand damage in return!";
		if($region == 0)
		{
			if($sandHealth <= 0)
			{
				
				$sandHealth = 0;
				$experience += 200;
				$krevels += 50000;
				$errorMsg = "\"I surrender.  Victory is yours.\"  You claim 200 exp and 50 000 krevels!!";
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
					$query = "UPDATE CW_Users SET experience=$experience, fac_change='" . $fac_change . "', level=$level, c_energy=$c_energy, c_stamina=$c_stamina, skills=$skills, gems=$gems WHERE id=$uid";
					$result = mysql_query($query);
					
					
				}
				$query = "UPDATE CW_Users SET experience=$experience, krevels=$krevels WHERE id=$uid";
				$result = mysql_query($query);
	
				if($RememberFear >= 0)
					{
						$RememberFear++;
						if($RememberFear >= 3)
						{
$RememberFear = -1;
						$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $uid . "'";
						$result = mysql_query($query);
						$displayAchievement = 36;
						$achName = "Remember Fear";
						}
						$query = "UPDATE CW_Achievements SET achievement36=$RememberFear WHERE id=$uid";
						$result = mysql_query($query);
					}
					
					/*
					$rand = rand(0,100);
					if($rand < 50)
					{
						$dropCollection = 88;
						$numCollection = 7;
						$dropName = "Map";
					}
					else
					{
						$dropCollection = 89;
						$numCollection = 8;
						$dropName = "Compass";
					}
					$showDrop = $dropCollection;;
					$query = "UPDATE CW_Inventory SET power" . $dropCollection . "=power" . $dropCollection . " + 1 WHERE id=$uid";
					$result = mysql_query($query);
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
	*/
			}
		
		
		$query = "UPDATE CW_Users SET c_health=$c_health, sandHealth=$sandHealth WHERE id=$uid";
		$result = mysql_query($query);
		}
		else if($region == 1)
		{
					if($oujouHealth <= 0)
			{
				
				$oujouHealth = 0;
				$experience += 500;
				$krevels += 110000;
				$errorMsg = "In a band of green mist, the Oujou are no more.  You claim 500 exp and 110 000 krevels!!";
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
					$query = "UPDATE CW_Users SET experience=$experience, fac_change='" . $fac_change . "', level=$level, c_energy=$c_energy, c_stamina=$c_stamina, skills=$skills, gems=$gems WHERE id=$uid";
					$result = mysql_query($query);
					
					
				}
				$query = "UPDATE CW_Users SET experience=$experience, krevels=$krevels WHERE id=$uid";
				$result = mysql_query($query);
	
				if($HitMeAgain >= 0)
					{
						$HitMeAgain++;
						if($HitMeAgain >= 5)
						{
$HitMeAgain = -1;
						$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $uid . "'";
						$result = mysql_query($query);
						$displayAchievement = 37;
						$achName = "Hit Me Again";
						}
						$query = "UPDATE CW_Achievements SET achievement37=$HitMeAgain WHERE id=$uid";
						$result = mysql_query($query);
					}
					
					/*  FROM THE SUMMER EVENT, OBSOLETE
					$rand = rand(0,100);
					if($rand < 50)
					{
						$dropCollection = 88;
						$numCollection = 7;
						$dropName = "Map";
					}
					else
					{
						$dropCollection = 89;
						$numCollection = 8;
						$dropName = "Compass";
					}
					$showDrop = $dropCollection;;
					$query = "UPDATE CW_Inventory SET power" . $dropCollection . "=power" . $dropCollection . " + 1 WHERE id=$uid";
					$result = mysql_query($query);
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
	*/
			}
			$query = "UPDATE CW_Users SET c_health=$c_health, oujouHealth=$oujouHealth WHERE id=$uid";
			$result = mysql_query($query);
		}
	}
	
}



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
<html xmlns="http://www.w3.org/1999/xhtml"
xmlns:fb="http://www.facebook.com/2008/fbml">

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
<title>Boss Fight</title>
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
_END;
if($region == 0)
{
echo "<div class='powerButtons0' style='top:133px;'><div style='position:absolute; left:275px; top:5px;'><a class='button' href='boss.php?region=1&token=$access_token'>SAPPHIRE</a></div>";
if($level < 81)
echo "<div style='position:absolute; left:425px;top:5px;'>JADE<br/>Levels 81+</div>";
else
echo "<div style='position:absolute; left:425px;top:5px;'><a href='boss.php?region=2&token=$access_token'>JADE</a></div>";
echo "</div>";
if($level < 11)
{
echo "<table class='group5' style='top:178px;'><tr>";

echo "<td style='width:125px; color:yellow;'><a class='$style' href='missions.php?group=0&token=$access_token'>Earth<br/>Levels 1-10</a></td>";
echo "<td style='width:125px;'>Sand Lake<br/>Levels 11-25</td>";
echo "<td style='width:125px;'>Outpost<br/>Levels 26-40</td>";
echo "<td style='width:125px;'>Valley of Lilyth<br/>Levels 41-60</td>";
echo "<td style='width:125px;'>Temple of Sapphire<br/>Levels 61+</td>";
echo "<td style='width:125px;'>BOSS<br/>Levels 61+</td>";
echo <<<_END
</tr></table>
_END;
}
else if($level < 26)
{
echo "<table class='group5'  style='top:178px;'><tr>";

echo "<td style='width:125px;'><a class='" . $style . "' href='missions.php?group=0&token=$access_token'>Earth<br/>Levels 1-10</a></td>";

echo "<td style='width:125px;'><a class='" . $style . "' href='missions.php?group=1&token=$access_token'>Sand Lake<br/>Levels 11-25</a></td>";
echo "<td style='width:125px;'>Outpost<br/>Levels 26-40</td>";
echo "<td style='width:125px;'>Valley of Lilyth<br/>Levels 41-60</td>";
echo "<td style='width:125px;'>Temple of Sapphire<br/>Levels 61+</td>";
echo "<td style='width:125px;'>BOSS<br/>Levels 61+</td>";
echo <<<_END
</tr></table>
_END;
}
else if($level < 41)
{
echo "<table class='group5' style='top:178px;'><tr>";

echo "<td style='width:125px;'><a class='" . $style . "' href='missions.php?group=0&token=$access_token'>Earth<br/>Levels 1-10</a></td>";

echo "<td style='width:125px;'><a class='" . $style . "' href='missions.php?group=1&token=$access_token'>Sand Lake<br/>Levels 11-25</a></td>";

echo "<td style='width:125px;'><a class='" . $style . "' href='missions.php?group=2&token=$access_token'>Outpost<br/>Levels 26-40</a></td>";
echo "<td style='width:125px;'>Valley of Lilyth<br/>Levels 41-60</td>";
echo "<td style='width:125px;'>Temple of Sapphire<br/>Levels 61+</td>";
echo "<td style='width:125px;'>BOSS<br/>Levels 61+</td>";
echo <<<_END
</tr></table>
_END;
}
else if($level < 61)
{
echo "<table class='group5' style='top:178px;'><tr>";

echo "<td style='width:125px;'><a class='" . $style . "' href='missions.php?group=0&token=$access_token'>Earth<br/>Levels 1-10</a></td>";

echo "<td style='width:125px;'><a class='" . $style . "' href='missions.php?group=1&token=$access_token'>Sand Lake<br/>Levels 11-25</a></td>";

echo "<td style='width:125px;'><a class='" . $style . "' href='missions.php?group=2&token=$access_token'>Outpost<br/>Levels 26-40</a></td>";

echo "<td style='width:125px;'><a class='" . $style . "' href='missions.php?group=3&token=$access_token'>Valley of Lilyth<br/>Levels 41-60</a></td>";
echo "<td style='width:125px;'>Temple of Sapphire<br/>Levels 61+</td>";
echo "<td style='width:125px;'>BOSS<br/>Levels 61+</td>";
echo <<<_END
</tr></table>
_END;
}
else
{
echo "<table class='group5' style='top:178px;'><tr>";

echo "<td style='width:125px;'><a class='" . $style . "' href='missions.php?group=0&token=$access_token'>Earth<br/>Levels 1-10</a></td>";

echo "<td style='width:125px;'><a class='" . $style . "' href='missions.php?group=1&token=$access_token'>Sand Lake<br/>Levels 11-25</a></td>";

echo "<td style='width:125px;'><a class='" . $style . "' href='missions.php?group=2&token=$access_token'>Outpost<br/>Levels 26-40</a></td>";

echo "<td style='width:125px;'><a class='" . $style . "' href='missions.php?group=3&token=$access_token'>Valley of Lilyth<br/>Levels 41-60</a></td>";

echo "<td style='width:125px;'><a class='" . $style . "' href='missions.php?group=4&token=$access_token'>Temple of Sapphire<br/>Levels 61+</a></td>";
echo "<td style='width:125px;'><a class='button' href='boss.php?token=$access_token'>BOSS<br/>Levels 61+</td>";
echo <<<_END
</tr></table>
_END;
}
}
else
{
echo "<div class='powerButtons1' style='top:133px;'><div style='position:absolute; left:275px; top:5px;'><a href='boss.php?region=1&token=$access_token'>SAPPHIRE</a></div>";

echo "<div style='position:absolute; left:425px;top:5px;'><a class='button' href='boss.php?region=2&token=$access_token'>JADE</a></div>";
echo "</div>";
if($level < 101)
{
echo "<table class='group5' style='top:178px;'><tr>";

echo "<td style='width:125px; color:yellow;'><a class='button' href='missions.php?group=0&token=$access_token'>Long Roads<br/>Levels 81-100</a></td>";
echo "<td style='width:125px;'>Sanctioned Outpost<br/>Levels 101-125</td>";
echo "<td style='width:125px;'>Raman Province<br/>Levels 126-150</td>";
echo "<td style='width:125px;'>City of Taiyou<br/>Levels 151-180</td>";
echo "<td style='width:125px;'>Temple of Sapphire<br/>Levels 61+</td>";
echo "<td style='width:125px;'>BOSS<br/>Levels 61+</td>";
echo <<<_END
</tr></table>
_END;
}
else if($level < 126)
{
echo "<table class='group5' style='top:178px;'><tr>";

echo "<td style='width:125px;'><a class='" . $style . "' href='missions.php?group=0&token=$access_token'>Long Roads<br/>Levels 81-100</a></td>";

echo "<td style='width:125px;'><a class='" . $style . "' href='missions.php?group=1&token=$access_token'>Sanctioned Outpost<br/>Levels 101-125</a></td>";
echo "<td style='width:125px;'>Raman Province<br/>Levels 126-150</td>";
echo "<td style='width:125px;'>City of Taiyou<br/>Levels 151-180</td>";
echo "<td style='width:125px;'>Temple of Jade<br/>Levels 181+</td>";
echo "<td style='width:125px;'>BOSS<br/>Levels 181+</td>";
echo <<<_END
</tr></table>
_END;
}
else if($level < 151)
{
echo "<table class='group5 style='top:178px;''><tr>";

echo "<td style='width:125px;'><a class='" . $style . "' href='missions.php?group=0&token=$access_token'>Long Roads<br/>Levels 81-100</a></td>";

echo "<td style='width:125px;'><a class='" . $style . "' href='missions.php?group=1&token=$access_token'>Sanctioned Outpost<br/>Levels 101-125</a></td>";

echo "<td style='width:125px;'><a class='" . $style . "' href='missions.php?group=2&token=$access_token'>Raman Province<br/>Levels 126-150</a></td>";
echo "<td style='width:125px;'>City of Taiyou<br/>Levels 151-180</td>";
echo "<td style='width:125px;'>Temple of Jade<br/>Levels 181+</td>";
echo "<td style='width:125px;'>BOSS<br/>Levels 181+</td>";
echo <<<_END
</tr></table>
_END;
}
else if($level < 181)
{
echo "<table class='group5' style='top:178px;'><tr>";

echo "<td style='width:125px;'><a class='" . $style . "' href='missions.php?group=0&token=$access_token'>Long Roads<br/>Levels 81-100</a></td>";

echo "<td style='width:125px;'><a class='" . $style . "' href='missions.php?group=1&token=$access_token'>Sanctioned Outpost<br/>Levels 101-125</a></td>";

echo "<td style='width:125px;'><a class='" . $style . "' href='missions.php?group=2&token=$access_token'>Raman Province<br/>Levels 126-150</a></td>";

echo "<td style='width:125px;'><a class='" . $style . "' href='missions.php?group=3&token=$access_token'>City of Taiyou<br/>Levels 151-180</a></td>";
echo "<td style='width:125px;'>Temple of Jade<br/>Levels 181+</td>";
echo "<td style='width:125px;'>BOSS<br/>Levels 181+</td>";
echo <<<_END
</tr></table>
_END;
}
else
{
echo "<table class='group5' style='top:178px;'><tr>";

echo "<td style='width:125px;'><a class='" . $style . "' href='missions.php?group=0&token=$access_token'>Long Roads<br/>Levels 81-100</a></td>";

echo "<td style='width:125px;'><a class='" . $style . "' href='missions.php?group=1&token=$access_token'>Sanctioned Outpost<br/>Levels 101-125</a></td>";

echo "<td style='width:125px;'><a class='" . $style . "' href='missions.php?group=2&token=$access_token'>Raman Province<br/>Levels 126-150</a></td>";

echo "<td style='width:125px;'><a class='" . $style . "' href='missions.php?group=3&token=$access_token'>City of Taiyou<br/>Levels 151-180</a></td>";

echo "<td style='width:125px;'><a class='" . $style . "' href='missions.php?group=4&token=$access_token'>Temple of Jade<br/>Levels 181+</a></td>";
echo "<td style='width:125px;'><a class='button' href='boss.php?token=$access_token'>BOSS<br/>Levels 181+</a></td>";
echo <<<_END
</tr></table>
_END;
}
}
if($region == 0)
{
echo <<<_END
<div style="position:absolute; top:221px; left:8px; width:442px; height:322px; background-image:url('assets/Box_Bank.png'); color:black;">
<div style="position:absolute; top:8px; left:28px; width:360px; text-align:center; color:orange;">DUEL WITH SIR SABIEN</div>
_END;
if($sandHealth > 0)
{
echo <<<_END
<div style="position:absolute; top:33px; left:28px; width:360px; text-align:center;">
"If you are ready to test your strength, you may challenge me.  I shall not easily be subdued."<br/>(Health: $sandHealth / 5000)<br/>

_END;
if(($fightExpire == 0 || $showExpire == 1) && $sandHealth < 5000)
{
echo <<<_END
Fight ends in:
<div id="sandTime">$diffSandTime</div><br/>
HINT: <a class='faction' onclick='AskHelp(); return false;' href='#'>Ask your friends for help!</a>
_END;
}
else
{
	echo "<br/><br/>Spend 50 energy to challenge Sir Sabien to a duel,<br/> which lasts 24 hours.<br/>";
}
echo <<<_END
</div>



<div style='position:absolute; top:190px; width:100%; text-align:center;'>
<form method="post" action="boss.php">
<input type="hidden" name="fight" value="yes"/><input type="hidden" name="token" value="$access_token"/><input type="image" src="assets/BTN_Go.png" value="FIGHT"/></form>



</div>


_END;
}
else
{
	echo <<<_END
	<div style="position:absolute; top:48px; left:28px; width:360px; text-align:center;">
	"Well done," says Sir Sabien.  "I must rest, so if you wish to challenge me again, return tomorrow."
_END;
if($showDrop > 0)
		{
			echo "<br/><img src='assets/power$showDrop.jpg'/><br/>You found a $dropName!";
		}
echo <<<_END
	<br/><br/>
Rematch in:
<div id="sandTime">$diffSandTime</div>
	</div>
_END;
}
if($error == 1 || ($sandHealth == 0 && !empty($_REQUEST['fight'])))
{
	echo "<div style='position:absolute; top:275px; left:6px; width: 430px; text-align:center;'>";
	
		echo $errorMsg;
		
	
	echo "</div>";
}
echo <<<_END
</div>
</div>
<div style="position:absolute; top:228px; left:368px; width:244px; height:300px; background-image:url('assets/Sabien.png');">
</div>
_END;
}
else if($region == 1)
{
echo <<<_END
<div style="position:absolute; top:221px; left:8px; width:442px; height:322px; background-image:url('assets/Box_Bank.png'); color:black;">
<div style="position:absolute; top:8px; left:28px; width:360px; text-align:center; color:orange;">DEFEAT THE OUJOU</div>
_END;
if($oujouHealth > 0)
{
echo <<<_END
<div style="position:absolute; top:33px; left:28px; width:360px; text-align:center;">
"We are Oujou for we are many. What are you when compared to the Oujou?"<br/>(Health: $oujouHealth / 14000)<br/>

_END;
if(($fightExpire == 0 || $showExpire == 1) && $oujouHealth < 14000)
{
echo <<<_END
Fight ends in:
<div id="oujouTime">$diffSandTime</div><br/>
HINT: <a class='faction' onclick='AskHelp2(); return false;' href='#'>Ask your friends for help!</a>
_END;
}
else
{
	echo "<br/><br/>Spend 50 energy to challenge the Oujou to a duel,<br/> which lasts 24 hours.<br/>";
}
echo <<<_END
</div>



<div style='position:absolute; top:190px; width:100%; text-align:center;'>
<form method="post" action="boss.php">
<input type="hidden" name="fight" value="yes"/><input type="hidden" name="token" value="$access_token"/><input type="image" src="assets/BTN_Go.png" value="FIGHT"/></form>



</div>


_END;
}
else
{
	echo <<<_END
	<div style="position:absolute; top:48px; left:28px; width:360px; text-align:center;">
	The Oujou explodes into green mist that covers the floor. For now she is gone, but who knows when she might reform.
_END;
if($showDrop > 0)
		{
			echo "<br/><img src='assets/power$showDrop.jpg'/><br/>You found a $dropName!";
		}
echo <<<_END
	<br/><br/>
Rematch in:
<div id="oujouTime">$diffSandTime</div>
	</div>
_END;
}
if($error == 1 || ($oujouHealth == 0 && !empty($_REQUEST['fight'])))
{
	echo "<div style='position:absolute; top:275px; left:6px; width: 430px; text-align:center;'>";
	
		echo $errorMsg;
		
	
	echo "</div>";
}
echo <<<_END
</div>
</div>
<div style="position:absolute; top:198px; left:338px; width:300px; height:357px; background-image:url('assets/Oujou.png');">
</div>
_END;
}//end region
echo <<<_END
<div style='position:absolute; top:553px; left:520px; font-size:0.75em;'>
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

i = '$diffSandTime';
var sandHealth = $sandHealth;
var sandHour = i.charAt(0) + i.charAt(1);
var sandMin = i.charAt(3) + i.charAt(4);
var sandSec = i.substr(6);

var oujouHealth = $oujouHealth;
var oujouHour = i.charAt(0) + i.charAt(1);
var oujouMin = i.charAt(3) + i.charAt(4);
var oujouSec = i.substr(6);

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
if(sandHealth < 5000)
{
var sSandSec = PadDigits(sandSec, 2);
var sSandMin = PadDigits(sandMin, 2);
var sandTime = sandHour + ':' + sSandMin + ':' + sSandSec;
document.getElementById('sandTime').innerHTML = sandTime;
sandSec--;

if(sandSec < 0)
{
	sandSec = 59;
	sandMin--;
	if(sandMin < 0)
	{
		sandMin = 59;
		sandHour--;
		if(sandHour < 0)
		{
			sandHealth = 5000;
			
			document.getElementById('sandTime').innerHTML = 'DONE!(RELOAD PAGE)';
		}
	}
}



}
if(oujouHealth < 14000)
{
var sOujouSec = PadDigits(oujouSec, 2);
var sOujouMin = PadDigits(oujouMin, 2);
var oujouTime = oujouHour + ':' + sOujouMin + ':' + sOujouSec;
document.getElementById('oujouTime').innerHTML = oujouTime;
oujouSec--;

if(oujouSec < 0)
{
	oujouSec = 59;
	oujouMin--;
	if(oujouMin < 0)
	{
		oujouMin = 59;
		oujouHour--;
		if(oujouHour < 0)
		{
			oujouHealth = 14000;
			
			document.getElementById('oujouTime').innerHTML = 'DONE!(RELOAD PAGE)';
		}
	}
}
}
};

setInterval("updateTimer()", 1000);

//send facebook request asking for help
function AskHelp()
{
var obj = {
	name: 'Fight',
	link: 'http://apps.facebook.com/crimsonwinter?sandhelp=$uid'
};

FB.ui(
   {
    method: 'feed',
    link: 'http://apps.facebook.com/crimsonwinter?sandhelp=$uid',
    picture: 'http://www.firstagestudios.com/Ldd49cEsTcwfb/assets/SabienIcon.jpg',
    source: 'http://www.firstagestudios.com/Ldd49cEsTcwfb/assets/SabienIcon.jpg',
     name: 'Duel with Sabien',
     caption: 'Crimson Winter',
     description: 'I challenged Sir Sabien to a duel!  Help me bring him down, and claim a reward.',
	actions: obj
   },
   function(response) {
     if (response && response.post_id) {
       alert('Post was published.');
     } else {
       alert('Post was not published.');
     }

   }
  );
};
function AskHelp2()
{
var obj = {
	name: 'Fight',
	link: 'http://apps.facebook.com/crimsonwinter?oujouhelp=$uid'
};

FB.ui(
   {
    method: 'feed',
    link: 'http://apps.facebook.com/crimsonwinter?oujouhelp=$uid',
    picture: 'http://www.firstagestudios.com/Ldd49cEsTcwfb/assets/achie37.jpg',
    source: 'http://www.firstagestudios.com/Ldd49cEsTcwfb/assets/achie37.jpg',
     name: 'Defeat the Oujou',
     caption: 'Crimson Winter',
     description: 'I must defeat the Oujou and save Taiyou!  Help me bring them down, and claim a reward.',
	actions: obj
   },
   function(response) {
     if (response && response.post_id) {
       alert('Post was published.');
     } else {
       alert('Post was not published.');
     }

   }
  );
};
updateTimer();
</script>
</body>
</html>
_END;

mysql_close($db_server);
?>
