<?php
require_once 'src/facebook.php';
require_once 'db_vars.php';
require_once 'functions.php';
require_once 'cw_vars.php';

$app_id = APP_ID;
    $app_secret = APP_SECRET;
    $my_url = "http://apps.facebook.com/crimsonwinter/allies.php";
    
   $displayAchievement = 0;
$achName = '';

//login code same as for index.php
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

//allies are groups the player invests in, who pay the character at regular intervals
$ally[] = $rows[38];
$ally[] = $rows[40];
$ally[] = $rows[42];
$ally[] = $rows[44];
$ally[] = $rows[46];

$allyTime[] = $rows[39];
$allyTime[] = $rows[41];
$allyTime[] = $rows[43];
$allyTime[] = $rows[45];
$allyTime[] = $rows[47];

//SET TIME LIMITS AND OTHER VARIABLES HERE FOR EASY ACCESS
for($i = 0; $i < count($ally); $i++)
{
	$allyPayOut[] = 0;
	$diffAllyTime[] = 0;
}
$allyBase[] = 200;
$allyBase[] = 800;
$allyBase[] = 5000;
$allyBase[] = 10000;
$allyBase[] = 50000;

$allyIncrement[] = 25;
$allyIncrement[] = 90;
$allyIncrement[] = 450;
$allyIncrement[] = 981;
$allyIncrement[] = 5600;

$allyTimeSpan[] = 60*60*2;
//$allyTimeSpan[] = 20;//TEMP FOR TESTING
$allyTimeSpan[] = 60*60*6;
$allyTimeSpan[] = 60*60*8;
$allyTimeSpan[] = 60*60*12;
$allyTimeSpan[] = 60*60*24;

//how much it costs to buy an ally
$allyCost[] = 1000;
$allyCost[] = 4100;
$allyCost[] = 20000;
$allyCost[] = 45000;
$allyCost[] = 250000;

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
$query = "SELECT achievement20, achievement38, achievement39 FROM CW_Achievements WHERE id='" . $uid . "'";
$result = mysql_query($query);
$row = mysql_fetch_row($result);
$WealthyChosen = $row[0];
$AlliedForces = $row[1];
$SwornBrothers = $row[2];

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


for($i = 0; $i < count($ally); $i++)
{ 
	$newtime = time();
	$query = "SELECT UNIX_TIMESTAMP('" . $allyTime[$i]. "') as allyDiff";//for real game, add WHERE to narrow it down to fb uid
	$result = mysql_query($query);
	$result = mysql_fetch_assoc($result);
		
	$result['allyDiff'] -= $newtime;
	
	if($result['allyDiff'] < 0)
	{
		$allyPayOut[$i] = 1;
	}
	else
	{
		$newtime = date("Y-m-d H:i:s", $newtime);
		$query = "SELECT TIMEDIFF('" . $allyTime[$i]. "', '" . $newtime . "') as diff";
		$result = mysql_query($query);
		$result = mysql_fetch_assoc($result);
		$diffAllyTime[$i] = $result['diff'];
		$allyPayOut[$i] = 0;
	}
	
}

$error = 0;
$errorMsg = '';
$amount = 0;
if(isset($_POST[check]))
{
	if($_POST[action] == "collect")
	{
		switch($_POST[ally])//use switch as a sanitizer
		{
			case 0:
				if($ally[0] > 0 && $allyPayOut[0] > 0)
				{
					$amount += $allyBase[0] + GetAllyIncrement($ally[0], $allyIncrement[0]);
					$thisTime = time() + $allyTimeSpan[0];
					$thisTime = date("Y-m-d H:i:s", $thisTime);
					$query = "UPDATE CW_Users SET allyTime1='" . $thisTime . "' WHERE id=$uid";
					$result = mysql_query($query);
					$allyPayOut[0] = 0;
					
					$newtime = time();
					$query = "SELECT UNIX_TIMESTAMP('" . $thisTime . "') as allyDiff";//for real game, add WHERE to narrow it down to fb uid
					$result = mysql_query($query);
					$result = mysql_fetch_assoc($result);
						
					$result['allyDiff'] -= $newtime;
					
					if($result['allyDiff'] < 0)
					{
						$allyPayOut[0] = 1;
					}
					else
					{
						$newtime = date("Y-m-d H:i:s", $newtime);
						$query = "SELECT TIMEDIFF('" . $thisTime. "', '" . $newtime . "') as diff";
						$result = mysql_query($query);
						$result = mysql_fetch_assoc($result);
						$diffAllyTime[0] = $result['diff'];
						$allyPayOut[0] = 0;
					}
				}
				break;
			case 1:
				if($ally[1] > 0 && $allyPayOut[1] > 0)
				{
					$amount += $allyBase[1] + GetAllyIncrement($ally[1], $allyIncrement[1]);
					$thisTime = time() + $allyTimeSpan[1];
					$thisTime = date("Y-m-d H:i:s", $thisTime);
					$query = "UPDATE CW_Users SET allyTime2='" . $thisTime . "' WHERE id=$uid";
					$result = mysql_query($query);
					
					$newtime = time();
					$query = "SELECT UNIX_TIMESTAMP('" . $thisTime. "') as allyDiff";//for real game, add WHERE to narrow it down to fb uid
					$result = mysql_query($query);
					$result = mysql_fetch_assoc($result);
						
					$result['allyDiff'] -= $newtime;
					
					if($result['allyDiff'] < 0)
					{
						$allyPayOut[1] = 1;
					}
					else
					{
						$newtime = date("Y-m-d H:i:s", $newtime);
						$query = "SELECT TIMEDIFF('" . $thisTime. "', '" . $newtime . "') as diff";
						$result = mysql_query($query);
						$result = mysql_fetch_assoc($result);
						$diffAllyTime[1] = $result['diff'];
						$allyPayOut[1] = 0;
					}
				}
				break;
			case 2:
				if($ally[2] > 0 && $allyPayOut[2] > 0)
				{
					$amount += $allyBase[2] + GetAllyIncrement($ally[2], $allyIncrement[2]);
					$thisTime = time() + $allyTimeSpan[2];
					$thisTime = date("Y-m-d H:i:s", $thisTime);
					$query = "UPDATE CW_Users SET allyTime3='" . $thisTime . "' WHERE id=$uid";
					$result = mysql_query($query);
					
					$newtime = time();
					$query = "SELECT UNIX_TIMESTAMP('" . $thisTime. "') as allyDiff";//for real game, add WHERE to narrow it down to fb uid
					$result = mysql_query($query);
					$result = mysql_fetch_assoc($result);
						
					$result['allyDiff'] -= $newtime;
					
					if($result['allyDiff'] < 0)
					{
						$allyPayOut[2] = 1;
					}
					else
					{
						$newtime = date("Y-m-d H:i:s", $newtime);
						$query = "SELECT TIMEDIFF('" . $thisTime. "', '" . $newtime . "') as diff";
						$result = mysql_query($query);
						$result = mysql_fetch_assoc($result);
						$diffAllyTime[2] = $result['diff'];
						$allyPayOut[2] = 0;
					}
				}
				break;
			
			case 3:
				if($ally[3] > 0 && $allyPayOut[3] > 0)
				{
					$amount += $allyBase[3] + GetAllyIncrement($ally[3], $allyIncrement[3]);
					$thisTime = time() + $allyTimeSpan[3];
					$thisTime = date("Y-m-d H:i:s", $thisTime);
					$query = "UPDATE CW_Users SET allyTime4='" . $thisTime . "' WHERE id=$uid";
					$result = mysql_query($query);
					
					$newtime = time();
					$query = "SELECT UNIX_TIMESTAMP('" . $thisTime. "') as allyDiff";//for real game, add WHERE to narrow it down to fb uid
					$result = mysql_query($query);
					$result = mysql_fetch_assoc($result);
						
					$result['allyDiff'] -= $newtime;
					
					if($result['allyDiff'] < 0)
					{
						$allyPayOut[3] = 1;
					}
					else
					{
						$newtime = date("Y-m-d H:i:s", $newtime);
						$query = "SELECT TIMEDIFF('" . $thisTime. "', '" . $newtime . "') as diff";
						$result = mysql_query($query);
						$result = mysql_fetch_assoc($result);
						$diffAllyTime[3] = $result['diff'];
						$allyPayOut[3] = 0;
					}
				}
				break;
			case 4:
				if($ally[4] > 0 && $allyPayOut[4] > 0)
				{
					$amount += $allyBase[4] + GetAllyIncrement($ally[4], $allyIncrement[4]);
					$thisTime = time() + $allyTimeSpan[4];
					$thisTime = date("Y-m-d H:i:s", $thisTime);
					$query = "UPDATE CW_Users SET allyTime5='" . $thisTime . "' WHERE id=$uid";
					$result = mysql_query($query);
					
					
					$newtime = time();
					$query = "SELECT UNIX_TIMESTAMP('" . $thisTime. "') as allyDiff";//for real game, add WHERE to narrow it down to fb uid
					$result = mysql_query($query);
					$result = mysql_fetch_assoc($result);
						
					$result['allyDiff'] -= $newtime;
					
					if($result['allyDiff'] < 0)
					{
						$allyPayOut[4] = 1;
					}
					else
					{
						$newtime = date("Y-m-d H:i:s", $newtime);
						$query = "SELECT TIMEDIFF('" . $thisTime. "', '" . $newtime . "') as diff";
						$result = mysql_query($query);
						$result = mysql_fetch_assoc($result);
						$diffAllyTime[4] = $result['diff'];
						$allyPayOut[4] = 0;
					}
				}
				break;
			default://collect from everyone
				for($i = 0; $i < count($ally); $i++)
				{
					if($ally[$i] > 0 && $allyPayOut[$i] > 0)
					{
						$amount += $allyBase[$i] + GetAllyIncrement($ally[$i], $allyIncrement[$i]);
						$thisTime = time() + $allyTimeSpan[$i];
						$thisTime = date("Y-m-d H:i:s", $thisTime);
						$query = "UPDATE CW_Users SET allyTime" . $i + 1 . "='" . $thisTime . "' WHERE id=$uid";
						$result = mysql_query($query);
						
						
						$newtime = time();
						$query = "SELECT UNIX_TIMESTAMP('" . $thisTime. "') as allyDiff";//for real game, add WHERE to narrow it down to fb uid
						$result = mysql_query($query);
						$result = mysql_fetch_assoc($result);
							
						$result['allyDiff'] -= $newtime;
						
						if($result['allyDiff'] < 0)
						{
							$allyPayOut[$i] = 1;
						}
						else
						{
							$newtime = date("Y-m-d H:i:s", $newtime);
							$query = "SELECT TIMEDIFF('" . $thisTime . "', '" . $newtime . "') as diff";
							$result = mysql_query($query);
							$result = mysql_fetch_assoc($result);
							$diffAllyTime[$i] = $result['diff'];
							$allyPayOut[$i] = 0;
						}
					}
				}
				break;
				
				
		}
		if($amount > 0)
				{
					$krevels += $amount;
					$query = "UPDATE CW_Users SET krevels=krevels+" . $amount . " WHERE id=$uid";
					$result = mysql_query($query);
					if($WealthyChosen >= 0 && ($krevels + $bank >= 1000000))
					{
						$WealthyChosen = -1;
						$skills++;
						$query = "UPDATE CW_Users SET skills=skills+1 WHERE id=$uid";
						$result = mysql_query($query);
						$query = "UPDATE CW_Achievements SET achievement20='" . $WealthyChosen . "' WHERE id='" . $uid . "'";
						$result = mysql_query($query);
						$displayAchievement = 20;
						$achName = "Wealthy Chosen";
					}
				}
	}//end if collecting
	else if($_POST[action] == "upgrade")//investing money in an ally
	{
		switch($_POST[ally])//use switch as a sanitizer
		{
			case 0:
				if($ally[0] < 100)
				{
					if($krevels >= ($allyCost[0] + (($ally[0]) * $allyCost[0] / 2)))
					{
						$krevels -= ($allyCost[0] + (($ally[0]) * $allyCost[0] / 2));
						$ally[0]++;
						$query = "UPDATE CW_Users SET krevels=$krevels, ally1=$ally[0] WHERE id=$uid";
						$result = mysql_query($query);
						if($AlliedForces >= 0)
						{
							$AlliedForces = -1;
							$skills++;
							$query = "UPDATE CW_Users SET skills=skills+1 WHERE id=$uid";
							$result = mysql_query($query);
							$query = "UPDATE CW_Achievements SET achievement38='" . $AlliedForces . "' WHERE id='" . $uid . "'";
							$result = mysql_query($query);
							$displayAchievement = 38;
							$achName = "Allied Forces";
						}
						
					}
				}
				
				break;
			case 1:
				if($ally[1] < 100)
				{
					if($krevels >= ($allyCost[1] + (($ally[1]) * $allyCost[1] / 2)))
					{
						$krevels -= ($allyCost[1] + (($ally[1]) * $allyCost[1] / 2));
						$ally[1]++;
						$query = "UPDATE CW_Users SET krevels=$krevels, ally2=$ally[1] WHERE id=$uid";
						$result = mysql_query($query);
						if($AlliedForces >= 0)
						{
							$AlliedForces = -1;
							$skills++;
							$query = "UPDATE CW_Users SET skills=skills+1 WHERE id=$uid";
							$result = mysql_query($query);
							$query = "UPDATE CW_Achievements SET achievement38='" . $AlliedForces . "' WHERE id='" . $uid . "'";
							$result = mysql_query($query);
							$displayAchievement = 38;
							$achName = "Allied Forces";
						}
					}
				}
				break;
			case 2:
				if($ally[2] < 100)
				{
					if($krevels >= ($allyCost[2] + (($ally[2]) * $allyCost[2] / 2)))
					{
						$krevels -= ($allyCost[2] + (($ally[2]) * $allyCost[2] / 2));
						$ally[2]++;
						$query = "UPDATE CW_Users SET krevels=$krevels, ally3=$ally[2] WHERE id=$uid";
						$result = mysql_query($query);
						if($AlliedForces >= 0)
						{
							$AlliedForces = -1;
							$skills++;
							$query = "UPDATE CW_Users SET skills=skills+1 WHERE id=$uid";
							$result = mysql_query($query);
							$query = "UPDATE CW_Achievements SET achievement38='" . $AlliedForces . "' WHERE id='" . $uid . "'";
							$result = mysql_query($query);
							$displayAchievement = 38;
							$achName = "Allied Forces";
						}
					}
				}
				break;
			case 3:
				if($krevels >= ($allyCost[3] + (($ally[3]) * $allyCost[3] / 2)))
					{
						$krevels -= ($allyCost[3] + (($ally[3]) * $allyCost[3] / 2));
						$ally[3]++;
						$query = "UPDATE CW_Users SET krevels=$krevels, ally4=$ally[3] WHERE id=$uid";
						$result = mysql_query($query);
						if($AlliedForces >= 0)
						{
							$AlliedForces = -1;
							$skills++;
							$query = "UPDATE CW_Users SET skills=skills+1 WHERE id=$uid";
							$result = mysql_query($query);
							$query = "UPDATE CW_Achievements SET achievement38='" . $AlliedForces . "' WHERE id='" . $uid . "'";
							$result = mysql_query($query);
							$displayAchievement = 38;
							$achName = "Allied Forces";
						}
						if($SwornBrothers >= 0)
						{
							$SwornBrothers++;
							if($SwornBrothers >= 50)
							{
								$SwornBrothers = -1;
								$skills++;
								$query = "UPDATE CW_Users SET skills=skills+1 WHERE id=$uid";
								$result = mysql_query($query);
								$query = "UPDATE CW_Achievements SET achievement39='" . $SwornBrothers . "' WHERE id='" . $uid . "'";
								$result = mysql_query($query);
								$displayAchievement = 39;
								$achName = "Sworn Brothers";
							}
							else
							{
								$query = "UPDATE CW_Achievements SET achievement39='" . $SwornBrothers . "' WHERE id='" . $uid . "'";
								$result = mysql_query($query);
							}
						}
					}
				break;
			case 4:
				if($krevels >= ($allyCost[4] + (($ally[4]) * $allyCost[4] / 2)))
					{
						$krevels -= ($allyCost[4] + (($ally[4]) * $allyCost[4] / 2));
						$ally[4]++;
						$query = "UPDATE CW_Users SET krevels=$krevels, ally5=$ally[4] WHERE id=$uid";
						$result = mysql_query($query);
						if($AlliedForces >= 0)
						{
							$AlliedForces = -1;
							$skills++;
							$query = "UPDATE CW_Users SET skills=skills+1 WHERE id=$uid";
							$result = mysql_query($query);
							$query = "UPDATE CW_Achievements SET achievement38='" . $AlliedForces . "' WHERE id='" . $uid . "'";
							$result = mysql_query($query);
							$displayAchievement = 38;
							$achName = "Allied Forces";
						}
					}
				break;
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
<title>Allies</title>
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
//HUD stuff
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
<div class="btn4of4" style="top:133px;">
<table style="top:9px; width:600px; text-align:center; position:absolute; left:99px;">
<tr><td style='width:145px; border:0px; padding:0px;'><a href="profile.php?token=$access_token">CHOSEN</a></td><td style='width:105px; border:0px; padding:0px; text-align:left;'><a href="knights.php?token=$access_token">KNIGHTS</a></td><td style='width:105px; border:0px; padding:0px;text-align:left;'><a href='leaderboard.php?token=$access_token'>RANKINGS</a></td><td style='width:135px;border:0px; padding:0px;text-align:left;'><a class='button' href='allies.php?token=$access_token'>ALLIES</a></td></tr></table>
</div>
<div style="position:absolute; top:178px; left:8px; width:744px; height:470px; background-image:url('assets/Box_Allies.png'); color:black;">
<div style="position:absolute; top:8px; left:8px; width:728px; text-align:center; color:orange;">ALLIES</div><br/><br/>
<div style="position:absolute; top:25px; left:8px; width:728px; text-align:center;">Find allies here!  Level them up by giving them krevels, and they will be sure to return the favour later!</div>
_END;
for($i = 0; $i < count($ally); $i++)
{
echo <<<_END
<div style="position:absolute; width:100px; height:100px; top:55px; left:
_END;
echo (135 * $i + 18);
echo <<<_END
px;"><img src="assets/Ally
_END;
echo $i + 1;
echo <<<_END
.png"/></div>
_END;
}
echo <<<_END
<div style="position:absolute; width:100px; height:100px; top:163px; left:18px; text-align:center;">
Roughlanders<br/><br/>Level $ally[0]<br/>
_END;
if($ally[0] < 100 && $ally[0] > 0)
echo "<form method='post' action='allies.php'><input type='hidden' name='check' value='yes'/><input type='hidden' name='action' value='upgrade'/><input type='hidden' name='token' value='$access_token'/><input type='hidden' name='ally' value='0';/><input type='image' src='assets/BTN_Upgrade.png' value='UPGRADE'/></form><br/><br/>(" . ($allyCost[0] + (($ally[0]) * $allyCost[0] / 2)) . " krevels)<br/><br/>";
else if($ally[0] == 0)
echo "<form method='post' action='allies.php'><input type='hidden' name='check' value='yes'/><input type='hidden' name='action' value='upgrade'/><input type='hidden' name='token' value='$access_token'/><input type='hidden' name='ally' value='0';/><input type='image' src='assets/BTN_Buy.png' value='BUY'/></form><br/>(" . ($allyCost[0]) . " krevels)<br/><br/>";
else
echo "<br/>";
if($ally[0] > 0)
{
	if($allyPayOut[0] > 0)
	{
		echo "<div id='allyBox0'><form method='post' action='allies.php'><input type='hidden' name='check' value='yes'/><input type='hidden' name='action' value='collect'/><input type='hidden' name='token' value='$access_token'/><input type='hidden' name='ally' value='0';/><input type='image' src='assets/BTN_Collect.png' value='COLLECT'/></form>(" . ($allyBase[0] + GetAllyIncrement($ally[0], $allyIncrement[0])) . ")";
		echo "</div>";
	}
	else
	{
		echo "<div id='allyBox0'>More in: " . $diffAllyTime[0] . "</div>";
	}
}
echo <<<_END
</div>

<div style="position:absolute; width:100px; height:100px; top:163px; left:143px; text-align:center;">
Croatins<br/><br/>Level $ally[1]<br/>
_END;
if($ally[1] < 100 && $ally[1] > 0)
echo "<form method='post' action='allies.php'><input type='hidden' name='check' value='yes'/><input type='hidden' name='action' value='upgrade'/><input type='hidden' name='token' value='$access_token'/><input type='hidden' name='ally' value='1';/><input type='image' src='assets/BTN_Upgrade.png' value='UPGRADE'/></form><br/><br/>(" . ($allyCost[1] + (($ally[1]) * $allyCost[1] / 2)) . " krevels)<br/><br/>";
else if($ally[1] == 0)
echo "<form method='post' action='allies.php'><input type='hidden' name='check' value='yes'/><input type='hidden' name='action' value='upgrade'/><input type='hidden' name='token' value='$access_token'/><input type='hidden' name='ally' value='1';/><input type='image' src='assets/BTN_Buy.png' value='BUY'/></form><br/>(" . ($allyCost[1]) . " krevels)<br/><br/>";
else
echo "<br/>";
if($ally[1] > 0)
{
	if($allyPayOut[1] > 0)
	{
		echo "<div id='allyBox1'><form method='post' action='allies.php'><input type='hidden' name='check' value='yes'/><input type='hidden' name='action' value='collect'/><input type='hidden' name='token' value='$access_token'/><input type='hidden' name='ally' value='1';/><input type='image' src='assets/BTN_Collect.png' value='COLLECT'/></form>(" . ($allyBase[1] + GetAllyIncrement($ally[1], $allyIncrement[1])) . ")";
		echo "</div>";
	}
	else
	{
		echo "<div id='allyBox1'>More in: " . $diffAllyTime[1] . "</div>";
	}
}
echo <<<_END
</div>

<div style="position:absolute; width:100px; height:100px; top:163px; left:278px; text-align:center;">
Legionnaires<br/><br/>Level $ally[2]<br/>
_END;
if($ally[2] < 100 && $ally[2] > 0)
echo "<form method='post' action='allies.php'><input type='hidden' name='check' value='yes'/><input type='hidden' name='action' value='upgrade'/><input type='hidden' name='token' value='$access_token'/><input type='hidden' name='ally' value='2';/><input type='image' src='assets/BTN_Upgrade.png' value='UPGRADE'/></form><br/><br/>(" . ($allyCost[2] + (($ally[2]) * $allyCost[2] / 2)) . " krevels)<br/><br/>";
else if($ally[2] == 0)
echo "<form method='post' action='allies.php'><input type='hidden' name='check' value='yes'/><input type='hidden' name='action' value='upgrade'/><input type='hidden' name='token' value='$access_token'/><input type='hidden' name='ally' value='2';/><input type='image' src='assets/BTN_Buy.png' value='BUY'/></form><br/>(" . ($allyCost[2]) . " krevels)<br/><br/>";
else
echo "<br/>";
if($ally[2] > 0)
{
	if($allyPayOut[2] > 0)
	{
		echo "<div id='allyBox2'><form method='post' action='allies.php'><input type='hidden' name='check' value='yes'/><input type='hidden' name='action' value='collect'/><input type='hidden' name='token' value='$access_token'/><input type='hidden' name='ally' value='2';/><input type='image' src='assets/BTN_Collect.png' value='COLLECT'/></form>(" . ($allyBase[2] + GetAllyIncrement($ally[2], $allyIncrement[2])) . ")";
		echo "</div>";
	}
	else
	{
		echo "<div id='allyBox2'>More in: " . $diffAllyTime[2] . "</div>";
	}
}
echo <<<_END
</div>

<div style="position:absolute; width:100px; height:100px; top:163px; left:413px; text-align:center;">
Knights of Sapphiros<br/>Level $ally[3]<br/>
_END;
if($ally[3] < 100 && $ally[3] > 0)
echo "<form method='post' action='allies.php'><input type='hidden' name='check' value='yes'/><input type='hidden' name='action' value='upgrade'/><input type='hidden' name='token' value='$access_token'/><input type='hidden' name='ally' value='3';/><input type='image' src='assets/BTN_Upgrade.png' value='UPGRADE'/></form><br/><br/>(" . ($allyCost[3] + (($ally[3]) * $allyCost[3] / 2)) . " krevels)<br/><br/>";
else if($ally[3] == 0)
echo "<form method='post' action='allies.php'><input type='hidden' name='check' value='yes'/><input type='hidden' name='action' value='upgrade'/><input type='hidden' name='token' value='$access_token'/><input type='hidden' name='ally' value='3';/><input type='image' src='assets/BTN_Buy.png' value='BUY'/></form><br/>(" . ($allyCost[3]) . " krevels)<br/><br/>";
else
echo "<br/>";
if($ally[3] > 0)
{
	if($allyPayOut[3] > 0)
	{
		echo "<div id='allyBox3'><form method='post' action='allies.php'><input type='hidden' name='check' value='yes'/><input type='hidden' name='action' value='collect'/><input type='hidden' name='token' value='$access_token'/><input type='hidden' name='ally' value='3';/><input type='image' src='assets/BTN_Collect.png' value='COLLECT'/></form>(" . ($allyBase[3] + GetAllyIncrement($ally[3], $allyIncrement[3])) . ")";
		echo "</div>";
	}
	else
	{
		echo "<div id='allyBox3'>More in: " . $diffAllyTime[3] . "</div>";
	}
}
echo <<<_END
</div>

<div style="position:absolute; width:100px; height:100px; top:163px; left:558px; text-align:center;">
Deathsquad<br/><br/>Level $ally[4]<br/>
_END;
if($ally[4] < 100 && $ally[4] > 0)
echo "<form method='post' action='allies.php'><input type='hidden' name='check' value='yes'/><input type='hidden' name='action' value='upgrade'/><input type='hidden' name='token' value='$access_token'/><input type='hidden' name='ally' value='4';/><input type='image' src='assets/BTN_Upgrade.png' value='UPGRADE'/></form><br/><br/>(" . ($allyCost[4] + (($ally[4]) * $allyCost[4] / 2)) . " krevels)<br/><br/>";
else if($ally[4] == 0)
echo "<form method='post' action='allies.php'><input type='hidden' name='check' value='yes'/><input type='hidden' name='action' value='upgrade'/><input type='hidden' name='token' value='$access_token'/><input type='hidden' name='ally' value='4';/><input type='image' src='assets/BTN_Buy.png' value='BUY'/></form><br/>(" . ($allyCost[4]) . " krevels)<br/><br/>";
else
echo "<br/>";
if($ally[4] > 0)
{
	if($allyPayOut[4] > 0)
	{
		echo "<div id='allyBox4'><form method='post' action='allies.php'><input type='hidden' name='check' value='yes'/><input type='hidden' name='action' value='collect'/><input type='hidden' name='token' value='$access_token'/><input type='hidden' name='ally' value='4';/><input type='image' src='assets/BTN_Collect.png' value='COLLECT'/></form>(" . ($allyBase[4] + GetAllyIncrement($ally[4], $allyIncrement[4])) . ")";
		echo "</div>";
	}
	else
	{
		echo "<div id='allyBox4'>More in: " . $diffAllyTime[4] . "</div>";
	}
}
echo <<<_END
</div>
</div>


<div style='position:absolute; top:630px; left:520px; font-size:0.75em;'>
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
//for padding all the timers
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

i = '$diffAllyTime[0]';
var a0Hour = i.charAt(0) + i.charAt(1);
var a0Min = i.charAt(3) + i.charAt(4);
var a0Sec = i.substr(6);
var a0Pay = $allyPayOut[0];

i = '$diffAllyTime[1]';
var a1Hour = i.charAt(0) + i.charAt(1);
var a1Min = i.charAt(3) + i.charAt(4);
var a1Sec = i.substr(6);
var a1Pay = $allyPayOut[1];

i = '$diffAllyTime[2]';
var a2Hour = i.charAt(0) + i.charAt(1);
var a2Min = i.charAt(3) + i.charAt(4);
var a2Sec = i.substr(6);
var a2Pay = $allyPayOut[2];

i = '$diffAllyTime[3]';
var a3Hour = i.charAt(0) + i.charAt(1);
var a3Min = i.charAt(3) + i.charAt(4);
var a3Sec = i.substr(6);
var a3Pay = $allyPayOut[3];

i = '$diffAllyTime[4]';
var a4Hour = i.charAt(0) + i.charAt(1);
var a4Min = i.charAt(3) + i.charAt(4);
var a4Sec = i.substr(6);
var a4Pay = $allyPayOut[4];
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


if(a0Pay <= 0)
{
var sA0Sec = PadDigits(a0Sec, 2);
var sA0Min = PadDigits(a0Min, 2);
var a0Time = a0Hour + ':' + sA0Min + ':' + sA0Sec;
document.getElementById('allyBox0').innerHTML = "More in: " + a0Time;
a0Sec--;

if(a0Sec < 0)
{
	a0Sec = 59;
	a0Min--;
	if(a0Min < 0)
	{
		a0Min = 59;
		a0Hour--;
		if(a0Hour < 0)
		{
			a0Pay = 1;
_END;
$payout = $allyBase[0] + GetAllyIncrement($ally[0], $allyIncrement[0]);
echo <<<_END
			var payout = $payout;
			document.getElementById('allyBox0').innerHTML = "<form method='post' action='allies.php'><input type='hidden' name='check' value='yes'/><input type='hidden' name='action' value='collect'/><input type='hidden' name='token' value='$access_token'/><input type='hidden' name='ally' value='0';/><input type='image' src='assets/BTN_Collect.png' value='COLLECT'/></form>("+ payout + ")";
		}	//document.getElementById('allyBox0').innerHTML = 'DONE';
	}
}

}

if(a1Pay <= 0)
{
var sA1Sec = PadDigits(a1Sec, 2);
var sA1Min = PadDigits(a1Min, 2);
var a1Time = a1Hour + ':' + sA1Min + ':' + sA1Sec;
document.getElementById('allyBox1').innerHTML = "More in: " + a1Time;
a1Sec--;

if(a1Sec < 0)
{
	a1Sec = 59;
	a1Min--;
	if(a1Min < 0)
	{
		a1Min = 59;
		a1Hour--;
		if(a1Hour < 0)
		{
			a1Pay = 1;
_END;
$payout = $allyBase[1] + GetAllyIncrement($ally[1], $allyIncrement[1]);
echo <<<_END
			var payout = $payout;
			document.getElementById('allyBox1').innerHTML = "<form method='post' action='allies.php'><input type='hidden' name='check' value='yes'/><input type='hidden' name='action' value='collect'/><input type='hidden' name='token' value='$access_token'/><input type='hidden' name='ally' value='1';/><input type='image' src='assets/BTN_Collect.png' value='COLLECT'/></form>("+ payout + ")";
		}	//document.getElementById('allyBox0').innerHTML = 'DONE';
	}
}

}

if(a2Pay <= 0)
{
var sA2Sec = PadDigits(a2Sec, 2);
var sA2Min = PadDigits(a2Min, 2);
var a2Time = a2Hour + ':' + sA2Min + ':' + sA2Sec;
document.getElementById('allyBox2').innerHTML = "More in: " + a2Time;
a2Sec--;

if(a2Sec < 0)
{
	a2Sec = 59;
	a2Min--;
	if(a2Min < 0)
	{
		a2Min = 59;
		a2Hour--;
		if(a2Hour < 0)
		{
			a2Pay = 1;
_END;
$payout = $allyBase[2] + GetAllyIncrement($ally[2], $allyIncrement[2]);
echo <<<_END
			var payout = $payout;
			document.getElementById('allyBox2').innerHTML = "<form method='post' action='allies.php'><input type='hidden' name='check' value='yes'/><input type='hidden' name='action' value='collect'/><input type='hidden' name='token' value='$access_token'/><input type='hidden' name='ally' value='2';/><input type='image' src='assets/BTN_Collect.png' value='COLLECT'/></form>("+ payout + ")";
		}	//document.getElementById('allyBox0').innerHTML = 'DONE';
	}
}

}

if(a3Pay <= 0)
{
var sA3Sec = PadDigits(a3Sec, 2);
var sA3Min = PadDigits(a3Min, 2);
var a3Time = a3Hour + ':' + sA3Min + ':' + sA3Sec;
document.getElementById('allyBox3').innerHTML = "More in: " + a3Time;
a3Sec--;

if(a3Sec < 0)
{
	a3Sec = 59;
	a3Min--;
	if(a3Min < 0)
	{
		a3Min = 59;
		a3Hour--;
		if(a3Hour < 0)
		{
			a3Pay = 1;
_END;
$payout = $allyBase[3] + GetAllyIncrement($ally[3], $allyIncrement[3]);
echo <<<_END
			var payout = $payout;
			document.getElementById('allyBox3').innerHTML = "<form method='post' action='allies.php'><input type='hidden' name='check' value='yes'/><input type='hidden' name='action' value='collect'/><input type='hidden' name='token' value='$access_token'/><input type='hidden' name='ally' value='3';/><input type='image' src='assets/BTN_Collect.png' value='COLLECT'/></form>("+ payout + ")";
		}	//document.getElementById('allyBox0').innerHTML = 'DONE';
	}
}

}

if(a4Pay <= 0)
{
var sA4Sec = PadDigits(a4Sec, 2);
var sA4Min = PadDigits(a4Min, 2);
var a4Time = a4Hour + ':' + sA4Min + ':' + sA4Sec;
document.getElementById('allyBox4').innerHTML = "More in: " + a4Time;
a4Sec--;

if(a4Sec < 0)
{
	a4Sec = 59;
	a4Min--;
	if(a4Min < 0)
	{
		a4Min = 59;
		a4Hour--;
		if(a4Hour < 0)
		{
			a4Pay = 1;
_END;
$payout = $allyBase[4] + GetAllyIncrement($ally[4], $allyIncrement[4]);
echo <<<_END
			var payout = $payout;
			document.getElementById('allyBox4').innerHTML = "<form method='post' action='allies.php'><input type='hidden' name='check' value='yes'/><input type='hidden' name='action' value='collect'/><input type='hidden' name='token' value='$access_token'/><input type='hidden' name='ally' value='4';/><input type='image' src='assets/BTN_Collect.png' value='COLLECT'/></form>("+ payout + ")";
		}	//document.getElementById('allyBox0').innerHTML = 'DONE';
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
