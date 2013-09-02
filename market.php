<?php
require_once 'src/facebook.php';
require_once 'db_vars.php';
require_once 'functions.php';
require_once 'cw_vars.php';
$app_id = APP_ID;
    $app_secret = APP_SECRET;
    $my_url = "http://apps.facebook.com/crimsonwinter/market.php";
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
$name = $rows[18];
$faction = $rows[19];
$knights = $rows[23];
$powerTotal = $rows[28];
$played = $rows[29];
$numKnights = $rows[30];






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
	
$query = "SELECT achievement22, achievement24, achievement16, achievement31 FROM CW_Achievements WHERE id='" . $uid . "'";
$result = mysql_query($query);
$rows = mysql_fetch_row($result);
$SavvyShopper = $rows[0];
$PlayTheOdds = $rows[1];
$Repertoire = $rows[2];
$AllSetForSand = $rows[3];

$query = "SELECT * FROM CW_Inventory WHERE id='" . $uid . "'";//for real game, add WHERE to narrow it down to fb uid
$result = mysql_query($query);

if(!$result) die("Database access failed: " . mysql_error());
$rows = mysql_fetch_row($result);

for($i = 1; $i < mysql_num_fields($result); $i++)
{
	$powers[] = $rows[$i];
}

$query = "SELECT name, attack, defence, cost, type, id, earned, faction FROM CW_Items WHERE earned='6' ORDER BY id, type";//for real game, add WHERE to narrow it down to fb uid
$result = mysql_query($query);

if(!$result) die("Database access failed: " . mysql_error());


$rows = mysql_num_rows($result);

for($i = 0; $i < $rows; $i++)
{
	$row = mysql_fetch_row($result);
	$powerName[] = $row[0];
	$powerAttack[] = $row[1];
	$powerDefence[] = $row[2];
	$powerCost[] = $row[3];
	$powerType[] = $row[4];
	$powerID[] = $row[5];
	$powerEarned[] = $row[6];
	$powerFaction[] = $row[7];
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
$CanAfford = 0;
$boughtMsg = '';
if($_POST[posted] == "yes")
{
	$_POST[posted] = "no";
	$action = $_POST[action];
	
	$action = SanitizeString($action);
	//check to see if the chosen action can be purchased
	switch($action)
	{
		case 0: //buy energy
			if($gems >= 10)
			{
				$CanAfford = 1;
				$gems -= 10;
				$c_energy = $m_energy;
				$boughtMsg = "<div style='position:absolute; text-align:center; top:30px; left:165px; border:0px; padding:0px; margin:0px;'>You<br/>purchased</div>";
				$boughtMsg .= "<div style='position:absolute; top:16px; left:350px; border:0px; padding:0px; margin:0px;'><img src='assets/Buy_Energy.jpg'/></div>";
				$boughtMsg .= "<div style='position:absolute; text-align:center; top:20px; left:465px; width:150px; border:0px; padding:0px; margin:0px;'><br/>Energy Refill</div>";
					
			}
			break;
		case 1: //buy stamina normally 10 gems
			if($gems >= 10)
			{
				$CanAfford = 1;
				$gems -= 10;
				$c_stamina = $m_stamina;
				$boughtMsg = "<div style='position:absolute; text-align:center; top:30px; left:165px; border:0px; padding:0px; margin:0px;'>You<br/>purchased</div>";
				$boughtMsg .= "<div style='position:absolute; top:16px; left:350px; border:0px; padding:0px; margin:0px;'><img src='assets/Buy_Stamina.jpg'/></div>";
				$boughtMsg .= "<div style='position:absolute; text-align:center; top:20px; left:465px; width:150px; border:0px; padding:0px; margin:0px;'><br/>Stamina Refill</div>";
			}
			break;
		case 2: //buy health
			if($gems >= 10)//normaly 10
			{
				$CanAfford = 1;
				$gems -= 3;
				$c_health = $m_health;
				$boughtMsg = "<div style='position:absolute; text-align:center; top:30px; left:165px; border:0px; padding:0px; margin:0px;'>You<br/>purchased</div>";
				$boughtMsg .= "<div style='position:absolute; top:16px; left:350px; border:0px; padding:0px; margin:0px;'><img src='assets/Buy_Health.jpg'/></div>";
				$boughtMsg .= "<div style='position:absolute; text-align:center; top:20px; left:465px; width:150px; border:0px; padding:0px; margin:0px;'><br/>Health Refill</div>";
			}
			break;
		case 3: //buy skills
			if($gems >= 12)
			{
				$CanAfford = 1;
				$gems -= 12;
				$skills += 10;
				$boughtMsg = "<div style='position:absolute; text-align:center; top:30px; left:46px; border:0px; padding:0px; margin:0px;'>You<br/>purchased</div>";
					$boughtMsg .= "<div style='position:absolute; top:16px; left:181px; border:0px; padding:0px; margin:0px;'><img src='assets/Buy_10SP.jpg'/></div>";
					$boughtMsg .= "<div style='position:absolute; text-align:center; top:20px; left:257px; width:150px; border:0px; padding:0px; margin:0px;'>Ten Skill Points</div>";
					$boughtMsg .= "<div style='position:absolute; text-align:center; top:30px; left:433px; width:150px; border:0px; padding:0px; margin:0px;'>Would you<br/>like another?</div>";
					$boughtMsg .= "<div style='position:absolute; text-align:center; top:30px; left:590px; border:0px; padding:0px; margin:0px;'>12<br/>Gem</div>";
					$boughtMsg .= "<div style='position:absolute; text-align:center; top:12px; left:635px; border:0px; padding:0px; margin:0px;'>";
					$boughtMsg .= "<form method='post' action='market.php'>";
					$boughtMsg .= "<input type='hidden' name='posted' value='yes'/>";
					$boughtMsg .= "<input type='hidden' name='action' value='3'/><input type='hidden' name='token' value='" . $access_token . "'/>";
					$boughtMsg .= "<input type='image' src='assets/BTN_Buy.png' value='Buy'/>";
					$boughtMsg .= "</form>";
					$boughtMsg .= "</div>";
			}
			break;
		case 4: //buy faction
			if($gems >= 10)
			{
				//check to make sure the new faction is different
				
				$CanAfford = 1;
				if($faction == $_POST[faction])
				{
					$boughtMsg = "You are already a chosen of this gem god.";
				}
				else if($_POST[faction] == SAPPHIROS || $_POST[faction] == RUBIA || $_POST[faction] == JEDEITE ||
					$_POST[faction] == DAMOS || $_POST[faction] == MACHALITE)
				{
					$boughtMsg = "<div style='position:absolute; text-align:center; top:10px; left:165px; width:170px; border:0px; padding:0px; margin:0px;'>Welcome to<br/>the winning team!</div>";
				$boughtMsg .= "<div style='position:absolute; top:16px; left:350px; border:0px; padding:0px; margin:0px;'><img src='assets/FactionIcon" . $_POST[faction] . ".jpg'/></div>";
				$boughtMsg .= "<div style='position:absolute; text-align:center; top:20px; left:465px; width:150px; border:0px; padding:0px; margin:0px;'>You have joined<br/>a new faction.</div>";
					$gems -= 10;
					switch($_POST[faction])
		{
			case SAPPHIROS:
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
				$query = "UPDATE CW_Users SET faction='" . $faction . "' WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				break;
			case RUBIA:
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
				$query = "UPDATE CW_Users SET faction='" . $faction . "' WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				break;
			case JEDEITE:
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
				$query = "UPDATE CW_Users SET faction='" . $faction . "' WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				break;
			case DAMOS:
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
				$query = "UPDATE CW_Users SET faction='" . $faction . "' WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				break;
			case MACHALITE:
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
				$query = "UPDATE CW_Users SET faction='" . $faction . "' WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				break;
			default:
				echo "Error: chosen gem god does not exist.";
				break;
		}
				}
				else
				{
					echo "Not a valid faction.<br/>";
				}
			}
			break;
		case 5:
			$purchase = $_POST[purchase];
			$purchase = SanitizeString($purchase);
			if($powerEarned[$purchase] == "6")
			{
			
				if($gems >= $powerCost[$purchase])
				{
					$CanAfford = 1;
					$gems -= $powerCost[$purchase];
					$pID = $powerID[$purchase] - 1;
					if($powers[$pID] == 0)
					{
						if($Repertoire >= 0 && ++$powerTotal >= 50)
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
						$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id='" . $uid . "'";
						$result = mysql_query($query);
					}
					$powers[$pID]++;
					$newPower = $powers[$pID];
					
					$boughtMsg = "<div style='position:absolute; text-align:center; top:30px; left:46px; border:0px; padding:0px; margin:0px;'>You<br/>purchased</div>";
					$boughtMsg .= "<div style='position:absolute; top:16px; left:181px; border:0px; padding:0px; margin:0px;'><img src='assets/power$powerID[$purchase].jpg'/></div>";
					$boughtMsg .= "<div style='position:absolute; text-align:center; top:20px; left:257px; width:150px; border:0px; padding:0px; margin:0px;'>$powerName[$purchase]<br/>Atk:$powerAttack[$purchase] Def:$powerDefence[$purchase]<br/>Owned:$powers[$pID]</div>";
					$boughtMsg .= "<div style='position:absolute; text-align:center; top:30px; left:433px; width:150px; border:0px; padding:0px; margin:0px;'>Would you<br/>like another?</div>";
					$boughtMsg .= "<div style='position:absolute; text-align:center; top:30px; left:590px; border:0px; padding:0px; margin:0px;'>$powerCost[$purchase]<br/>Gems</div>";
					$boughtMsg .= "<div style='position:absolute; text-align:center; top:12px; left:635px; border:0px; padding:0px; margin:0px;'>";
					$boughtMsg .= "<form method='post' action='market.php'>";
					$boughtMsg .= "<input type='hidden' name='posted' value='yes'/>";
					$boughtMsg .= "<input type='hidden' name='action' value='5'/><input type='hidden' name='token' value='" . $access_token . "'/>";
					$boughtMsg .= "<input type='hidden' name='purchase' value='$purchase'><input type='image' src='assets/BTN_Buy.png' value='Buy'/>";
					$boughtMsg .= "</form>";
					$boughtMsg .= "</div>";
					$pID++;
					$bought = "power" . $pID;
					$query = "UPDATE CW_Inventory SET " . $bought . "='" . $newPower . "' WHERE id='" . $uid . "'";
					$result = mysql_query($query);
				}
			}
			else
			{
				$error = 1;
				$errorMsg = "That is not a gem power.  That is power # $purchase (" . $powerName[56] . ")";
			}
			break;
		case 6:
			if($gems >= 1)
			{
				$CanAfford = 1;
				$gems -= 1;
				$query = "INSERT INTO CW_Raffle VALUES(" . $uid . ")";
				$result = mysql_query($query);
				$boughtMsg = "<div style='position:absolute; text-align:center; top:30px; left:46px; border:0px; padding:0px; margin:0px;'>You<br/>purchased</div>";
					$boughtMsg .= "<div style='position:absolute; top:16px; left:181px; border:0px; padding:0px; margin:0px;'><img src='assets/Buy_Raffle.jpg'/></div>";
					$boughtMsg .= "<div style='position:absolute; text-align:center; top:20px; left:257px; width:150px; border:0px; padding:0px; margin:0px;'>One Raffle Ticket<br/>for this week's draw<br/>Monday 12:00AM EDT</div>";
					$boughtMsg .= "<div style='position:absolute; text-align:center; top:30px; left:433px; width:150px; border:0px; padding:0px; margin:0px;'>Would you<br/>like another?</div>";
					$boughtMsg .= "<div style='position:absolute; text-align:center; top:30px; left:590px; border:0px; padding:0px; margin:0px;'>1<br/>Gem</div>";
					$boughtMsg .= "<div style='position:absolute; text-align:center; top:12px; left:635px; border:0px; padding:0px; margin:0px;'>";
					$boughtMsg .= "<form method='post' action='market.php'>";
					$boughtMsg .= "<input type='hidden' name='posted' value='yes'/>";
					$boughtMsg .= "<input type='hidden' name='action' value='6'/><input type='hidden' name='token' value='" . $access_token . "'/>";
					$boughtMsg .= "<input type='image' src='assets/BTN_Buy.png' value='Buy'/>";
					$boughtMsg .= "</form>";
					$boughtMsg .= "</div>";
				
				if($PlayTheOdds >= 0)
				{
					$skills++;
					$PlayTheOdds = -1;
					$query = "UPDATE CW_Achievements SET achievement24='" . $PlayTheOdds . "' WHERE id='" . $uid . "'";
					$result = mysql_query($query);
$displayAchievement = 24;
$achName = "Play the Odds";
				}
			}
			break;
		case 7:
			if($gems >= 23)
			{
				$CanAfford = 1;
				$gems -= 23;
				$skills += (($m_health - 100) / 5);
				$skills += ($m_energy - 15);
				$skills += ($m_stamina - 10);
				$skills += ($attack - 10);
				$skills += ($defence - 10);
				
				$m_health = 100;
				$m_energy = 15;
				$m_stamina = 10;
				$attack = 10;
				$defence = 10;
				if ($c_health > $m_health)
					$c_health = $m_health;
				if ($c_energy > $m_energy)
					$c_energy = $m_energy;
				if ($c_stamina > $m_stamina)
					$c_stamina = $m_stamina;
					
				$query = "UPDATE CW_Users SET c_energy='" . $c_energy . "', m_energy='" . $m_energy . "', c_health='" . $c_health . "', m_health='" . $m_health . "', c_stamina='" . $c_stamina . "', m_stamina='" . $m_stamina . "', attack='" . $attack . "', defence='" . $defence . "', skills='" . $skills . "' WHERE  id='" . $uid . "'";
				$result = mysql_query($query);
				$boughtMsg = "<div style='position:absolute; text-align:center; top:30px; left:165px; border:0px; padding:0px; margin:0px;'>You<br/>purchased</div>";
				$boughtMsg .= "<div style='position:absolute; top:16px; left:350px; border:0px; padding:0px; margin:0px;'><img src='assets/Buy_ResetSP.jpg'/></div>";
				$boughtMsg .= "<div style='position:absolute; text-align:center; top:20px; left:465px; width:150px; border:0px; padding:0px; margin:0px;'><br/>Stat Reset!</div>";
				
			}
			break;
		case 8: //buy stamina
			if($gems >= 1)
			{
				$CanAfford = 1;
				$gems -= 1;
				$knights++;
				$boughtMsg = "<div style='position:absolute; text-align:center; top:30px; left:46px; border:0px; padding:0px; margin:0px;'>You<br/>purchased</div>";
					$boughtMsg .= "<div style='position:absolute; top:16px; left:181px; border:0px; padding:0px; margin:0px;'><img src='assets/Buy_One.jpg'/></div>";
					$boughtMsg .= "<div style='position:absolute; text-align:center; top:20px; left:257px; width:150px; border:0px; padding:0px; margin:0px;'>One Bonus Knight<br/>(You now have<br/>" . ($knights + $numKnights) . " knights)</div>";
					$boughtMsg .= "<div style='position:absolute; text-align:center; top:30px; left:433px; width:150px; border:0px; padding:0px; margin:0px;'>Would you<br/>like another?</div>";
					$boughtMsg .= "<div style='position:absolute; text-align:center; top:30px; left:590px; border:0px; padding:0px; margin:0px;'>1<br/>Gem</div>";
					$boughtMsg .= "<div style='position:absolute; text-align:center; top:12px; left:635px; border:0px; padding:0px; margin:0px;'>";
					$boughtMsg .= "<form method='post' action='market.php'>";
					$boughtMsg .= "<input type='hidden' name='posted' value='yes'/>";
					$boughtMsg .= "<input type='hidden' name='action' value='8'/><input type='hidden' name='token' value='" . $access_token . "'/>";
					$boughtMsg .= "<input type='image' src='assets/BTN_Buy.png' value='Buy'/>";
					$boughtMsg .= "</form>";
					$boughtMsg .= "</div>";
				
			}
			break;
		case 9: //buy stamina
			if($gems >= 9)
			{
				$CanAfford = 1;
				$gems -= 9;
				$knights += 10;
				$boughtMsg = "Bought 10 knights! ";
				$boughtMsg = "<div style='position:absolute; text-align:center; top:30px; left:46px; border:0px; padding:0px; margin:0px;'>You<br/>purchased</div>";
					$boughtMsg .= "<div style='position:absolute; top:16px; left:181px; border:0px; padding:0px; margin:0px;'><img src='assets/Buy_Ten.jpg'/></div>";
					$boughtMsg .= "<div style='position:absolute; text-align:center; top:20px; left:257px; width:150px; border:0px; padding:0px; margin:0px;'>Ten Bonus Knights<br/>(You now have<br/>" . ($knights + $numKnights) . " knights)</div>";
					$boughtMsg .= "<div style='position:absolute; text-align:center; top:30px; left:433px; width:150px; border:0px; padding:0px; margin:0px;'>Would you<br/>like another?</div>";
					$boughtMsg .= "<div style='position:absolute; text-align:center; top:30px; left:590px; border:0px; padding:0px; margin:0px;'>10<br/>Gems</div>";
					$boughtMsg .= "<div style='position:absolute; text-align:center; top:12px; left:635px; border:0px; padding:0px; margin:0px;'>";
					$boughtMsg .= "<form method='post' action='market.php'>";
					$boughtMsg .= "<input type='hidden' name='posted' value='yes'/>";
					$boughtMsg .= "<input type='hidden' name='action' value='9'/><input type='hidden' name='token' value='" . $access_token . "'/>";
					$boughtMsg .= "<input type='image' src='assets/BTN_Buy.png' value='Buy'/>";
					$boughtMsg .= "</form>";
					$boughtMsg .= "</div>";
			}
			break;
		case 10:
			if($gems >= 20)//normally 20
			{
				$newName = SanitizeString($_POST[name]);
				
				$badWords = @file("assets/badwords.txt");
				for ($i=0; $i<count($badWords); $i++) {
			        if (preg_match("/" . substr($badWords[$i], 0, -1) . "/i",  $newName) > 0)
			        {
			        	$error = 1;
			        	$errorMsg = "Cannot have a word containing " . $badWords[$i] . ".  Please try a new name.";
			        }
			        }
			        if(!$error)
			        {
				$CanAfford = 1;
				$gems -= 20;
					
			        $name = $newName;
			        
				$boughtMsg = "<div style='position:absolute; text-align:center; top:30px; left:165px; border:0px; padding:0px; margin:0px;'>You<br/>purchased</div>";
				$boughtMsg .= "<div style='position:absolute; top:16px; left:300px; border:0px; padding:0px; margin:0px;'><img src='assets/Buy_NewName.jpg'/></div>";
				$boughtMsg .= "<div style='position:absolute; text-align:center; top:20px; left:465px; width:150px; border:0px; padding:0px; margin:0px;'>Name Change.<br/>Let everyone call you<br/>$name</div>";
				
				}
			}
			break;
		case 11:
			$purchase = $_POST[purchase];
			$purchase = SanitizeString($purchase);
			if($powerEarned[$purchase] == "6")
			{
			
				if($gems >= $powerCost[$purchase])
				{
					$CanAfford = 1;
					$gems -= $powerCost[$purchase];
					$pID = $powerID[$purchase] - 1;
					if($AllSetForSand > 0)
					{
						$newCollect = 1;
						$numCollection = $powerID[$purchase] - 81;
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
$numCollection = $powerID[$purchase] - 81;
						$AllSetForSand = $numCollection;
						$query = "UPDATE CW_Achievements SET achievement31='" . $AllSetForSand . "' WHERE id='" . $uid . "'";
						$result = mysql_query($query);
					}

					$powers[$pID]++;
					$newPower = $powers[$pID];
					
					$boughtMsg = "<div style='position:absolute; text-align:center; top:30px; left:46px; border:0px; padding:0px; margin:0px;'>You<br/>purchased</div>";
					$boughtMsg .= "<div style='position:absolute; top:16px; left:181px; border:0px; padding:0px; margin:0px;'><img src='assets/power$powerID[$purchase].jpg'/></div>";
					$boughtMsg .= "<div style='position:absolute; text-align:center; top:20px; left:257px; width:150px; border:0px; padding:0px; margin:0px;'>$powerName[$purchase]<br/></div>";
					$boughtMsg .= "<div style='position:absolute; text-align:center; top:30px; left:433px; width:150px; border:0px; padding:0px; margin:0px;'>Would you<br/>like another?</div>";
					$boughtMsg .= "<div style='position:absolute; text-align:center; top:30px; left:590px; border:0px; padding:0px; margin:0px;'>$powerCost[$purchase]<br/>Gems</div>";
					$boughtMsg .= "<div style='position:absolute; text-align:center; top:12px; left:635px; border:0px; padding:0px; margin:0px;'>";
					$boughtMsg .= "<form method='post' action='market.php'>";
					$boughtMsg .= "<input type='hidden' name='posted' value='yes'/>";
					$boughtMsg .= "<input type='hidden' name='action' value='5'/><input type='hidden' name='token' value='" . $access_token . "'/>";
					$boughtMsg .= "<input type='hidden' name='purchase' value='$purchase'><input type='image' src='assets/BTN_Buy.png' value='Buy'/>";
					$boughtMsg .= "</form>";
					$boughtMsg .= "</div>";
					$pID++;
					$bought = "power" . $pID;
					$query = "UPDATE CW_Inventory SET " . $bought . "='" . $newPower . "' WHERE id='" . $uid . "'";
					$result = mysql_query($query);
				}
			}
			else
			{
				$error = 1;
				$errorMsg = "That is not a gem power.  That is power # $purchase (" . $powerName[56] . ")";
			}
			break;
		default:
			break;
			
	}
	if($CanAfford == 1)
	{
		if($SavvyShopper >= 0)
		{
			$skills++;
			$SavvyShopper = -1;
			$query = "UPDATE CW_Achievements SET achievement22='" . $SavvyShopper . "' WHERE id='" . $uid . "'";
			$result = mysql_query($query);
$displayAchievement = 22;
$achName = "Savvy Shopper";
		}
		$query = "UPDATE CW_Users SET gems='" . $gems . "', name='" . $name . "', knights='" . $knights . "', c_energy='" . $c_energy . "', c_stamina='" . $c_stamina . "', c_health='" . $c_health . "', faction='" . $faction . "', skills='" . $skills . "' WHERE id='" . $uid . "'";
		$result = mysql_query($query);
		
		$buyTime = time();
		$buyTime= date("Y-m-d H:i:s", $buyTime);
		$query = "INSERT INTO CW_Purchases (userID, purchaseInfo, time) VALUES (" . $uid . ", " . $action . ", '" . $buyTime . "')";
		$result = mysql_query($query);
		
	}
	else if($error == 1)
	{
		$CanAfford = 1;
		$boughtMsg = $errorMsg;
	}
	else
	{
		$CanAfford = 1;
		$boughtMsg = "<div style='position:absolute; width:150px; top:42px; left:220px;'>You need more gems.</div>";
		$boughtMsg .= "<div style='position:absolute; width:150px; top:30px; left:400px;'>Get more<br/>here</div>";
		$boughtMsg .= "<img style='position:absolute; top:8px; left:540px;' src='assets/Buy_CantAffordArrow.png'/>";
	}
	
}

$next_exp = GetExpToNextLevel($level);
$this_exp = GetExpToNextLevel($level - 1);
$expToNext = $next_exp - $experience;
$nextLevel = $next_exp - $this_exp;
$expWidth = (int)(($nextLevel - $expToNext) / $nextLevel * 100);
$sKrevels = number_format($krevels);
$expToThis = $nextLevel - $expToNext;
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
<title>Market</title>
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
    FB.Canvas.setSize({width:720, height:2000});
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
echo "<td class='menu' id='hGems'><a class='" . $style . "' href='market.php?token=$access_token'>Market(<img src='assets/GemIcon" . $faction . ".png'/>" . $gems . ")</a></td>";
echo "<td class='menu'><a class='" . $style . "' href='inventory.php?token=$access_token'>Powers</a></td>";
echo "<td class='menu'><a class='" . $style . "' href='help.php?token=$access_token'>Help</a></td><td style='width:45px;'></td>";
echo <<<_END
</tr>
</table><br/><br/>


<div class='explain' style="height:112px; width:744px; border:0px; margin:0px; padding:0px; background-image:url('assets/Buy_Gems.jpg');">
<div style='position:absolute; top:40px; left:440px;'>
<form name ="place_order" id="order_form" action="#">
<a onclick="placeOrder('11a'); return false;" href="#">
<img src="assets/BTN_Buy.png"/>
</a>
</form>
</div>
<div style='position:absolute; top:40px; left:660px;'>
<form name ="place_order" id="order_form" action="#">
<a onclick="placeOrder('22b'); return false;" href="#">
<img src="assets/BTN_Buy.png"/>
</a>
</form>
</div>
</div>
_END;
if($CanAfford == 1)
{
echo <<<_END
	<div class='marketresults' style="text-align:center;color:black; top:253px; width:744px; height:92px; border:0px; padding:0px; margin:0px; background-image:url('assets/Buy_ResultsBox.png');">
_END;
	echo "<div style='position:absolute; '>" . $boughtMsg . "</div></div>";
	echo "<div class='market' style='top:350px; background-image:none; width:760px; left:0px; border:0px; padding:0px; margin:0px;'>";
}
else
	echo "<div class='market' style='top:250px; background-image:none; width:760px; left:0px; border:0px; padding:0px; margin:0px;'>";
echo <<<_END
<table style='position:relative; top:0px; left:8px; width:750px; text-align:center; color:black;'>
<tr style='height:223px;'>
<td><div style="width:142px; height:215px; background-image:url('assets/Buy_BG.png'); margin:0px; padding:0px; border:0px;">
<img style='position:relative; top:11px;' src='assets/Buy_One.jpg'/><br/><br/>1 knight
<div style='position:relative; top:58px; left:-31px;'>1<br/>Gem</div><div style='position:relative; top:6px; left:29px;'>
<form class='market' method="post" action="market.php">
<input type="hidden" name="posted" value="yes"/>
<input type="hidden" name="action" value="8"/>
<input type="hidden" name="token" value="$access_token"/>
<input type='image' src='assets/BTN_Buy.png' value='Buy'/>
</form>
</div>
</div></td>
<td><div style="width:142px; height:215px; background-image:url('assets/Buy_BG.png'); margin:0px; padding:0px; border:0px;">
<img style='position:relative; top:11px;' src='assets/Buy_Ten.jpg'/><br/><br/>10 knights
<div style='position:relative; top:58px; left:-31px;'>9<br/>Gems</div><div style='position:relative; top:6px; left:29px;'>
 <form class='market' method="post" action="market.php">
<input type="hidden" name="posted" value="yes"/>
<input type="hidden" name="action" value="9"/>
<input type="hidden" name="token" value="$access_token"/>
<input type='image' src='assets/BTN_Buy.png' value='Buy'/>
</form>
</div>
</div></td>
<td><div style="width:142px; height:215px; background-image:url('assets/Buy_BG.png'); margin:0px; padding:0px; border:0px;">
<img style='position:relative; top:11px;' src='assets/Buy_Health.jpg'/><br/><br/>Refill your<br/>Health
<div style='position:relative; top:41px; left:-31px;'>10<br/>Gems</div><div style='position:relative; top:-10px; left:29px;'>
<form class='market' method="post" action="market.php">
<input type="hidden" name="posted" value="yes"/>
<input type="hidden" name="action" value="2"/>
<input type="hidden" name="token" value="$access_token"/>
<input type='image' src='assets/BTN_Buy.png' value='Buy'/>
</form>
</div>
</div></td>
<td><div style="width:142px; height:215px; background-image:url('assets/Buy_BG.png'); margin:0px; padding:0px; border:0px;">
<img style='position:relative; top:11px;' src='assets/Buy_Energy.jpg'/><br/><br/>Refill your<br/>Energy
<div style='position:relative; top:41px; left:-31px;'>10<br/>Gems</div><div style='position:relative; top:-10px; left:29px;'>
<form class='market' method="post" action="market.php">
<input type="hidden" name="posted" value="yes"/>
<input type="hidden" name="action" value="0"/>
<input type="hidden" name="token" value="$access_token"/>
<input type='image' src='assets/BTN_Buy.png' value='Buy'/>
</form>
</div>
</div></td>
<td><div style="width:142px; height:215px; background-image:url('assets/Buy_BG.png'); margin:0px; padding:0px; border:0px;">
<img style='position:relative; top:11px;' src='assets/Buy_Stamina.jpg'/><br/><br/>Refill your<br/>Stamina
<div style='position:relative; top:41px; left:-31px;'>10<br/>Gems</div><div style='position:relative; top:-10px; left:29px;'>
<form class='market' method="post" action="market.php">
<input type="hidden" name="posted" value="yes"/>
<input type="hidden" name="action" value="1"/>
<input type="hidden" name="token" value="$access_token"/>
<input type='image' src='assets/BTN_Buy.png' value='Buy'/>
</form>
</div>
</div></td>
</tr>
<tr style='height:223px;'>
<td><div style="width:142px; height:215px; background-image:url('assets/Buy_BG.png'); margin:0px; padding:0px; border:0px;">
<img style='position:relative; top:11px;' src='assets/Buy_Raffle.jpg'/><br/><br/>Weekly Raffle<br/>for <b>Damos Set</b>!<br/>(1 ticket)
<div style='position:relative; top:25px; left:-31px;'>1<br/>Gem</div><div style='position:relative; top:-26px; left:29px;'>
<form class='market' method="post" action="market.php">
<input type="hidden" name="posted" value="yes"/>
<input type="hidden" name="action" value="6"/>
<input type="hidden" name="token" value="$access_token"/>
<input type='image' src='assets/BTN_Buy.png' value='Buy'/>
</form>
</div>
</div></td>
<td><div style="width:142px; height:215px; background-image:url('assets/Buy_BG.png'); margin:0px; padding:0px; border:0px;">
<img style='position:relative; top:11px;' src='assets/power5.jpg'/><br/><br/>Multi-Arrow<br/>Atk 35 Def 20<br/>Owned:$powers[4]
<div style='position:relative; top:25px; left:-31px;'>8<br/>Gems</div><div style='position:relative; top:-26px; left:29px;'>
<form method='post' action='market.php'>
<input type='hidden' name='token' value='$access_token'/>
<input type='hidden' name='posted' value='yes'/>
<input type='hidden' name='action' value='5'/>
<input type='hidden' name='purchase' value='0'/>
<input type='image' src='assets/BTN_Buy.png' value='Buy'/>
</form>
</div>
</div></td>
<td><div style="width:142px; height:215px; background-image:url('assets/Buy_BG.png'); margin:0px; padding:0px; border:0px;">
<img style='position:relative; top:11px;' src='assets/power32.jpg'/><br/><br/>Ice Armour<br/>Atk 10 Def 45<br/>Owned:$powers[31]
<div style='position:relative; top:25px; left:-31px;'>10<br/>Gems</div><div style='position:relative; top:-26px; left:29px;'>
<form method='post' action='market.php'>
<input type='hidden' name='token' value='$access_token'/>
<input type='hidden' name='posted' value='yes'/>
<input type='hidden' name='action' value='5'/>
<input type='hidden' name='purchase' value='1'/>
<input type='image' src='assets/BTN_Buy.png' value='Buy'/>
</form>
</div>
</div></td>
<td><div style="width:142px; height:215px; background-image:url('assets/Buy_BG.png'); margin:0px; padding:0px; border:0px;">
<img style='position:relative; top:11px;' src='assets/power53.jpg'/><br/><br/>Vanish Object<br/>Atk 30 Def 30<br/>Owned:$powers[52]
<div style='position:relative; top:25px; left:-31px;'>12<br/>Gems</div><div style='position:relative; top:-26px; left:29px;'>
<form method='post' action='market.php'>
<input type='hidden' name='token' value='$access_token'/>
<input type='hidden' name='posted' value='yes'/>
<input type='hidden' name='action' value='5'/>
<input type='hidden' name='purchase' value='2'/>
<input type='image' src='assets/BTN_Buy.png' value='Buy'/>
</form>
</div>
</div></td>
<td><div style="width:142px; height:215px; background-image:url('assets/Buy_BG.png'); margin:0px; padding:0px; border:0px;">
<img style='position:relative; top:11px;' src='assets/power57.jpg'/><br/><br/>Clones<br/>Atk 40 Def 40<br/>Owned:$powers[56]
<div style='position:relative; top:25px; left:-31px;'>16<br/>Gems</div><div style='position:relative; top:-26px; left:29px;'>
<form method='post' action='market.php'>
<input type='hidden' name='token' value='$access_token'/>
<input type='hidden' name='posted' value='yes'/>
<input type='hidden' name='action' value='5'/>
<input type='hidden' name='purchase' value='3'/>
<input type='image' src='assets/BTN_Buy.png' value='Buy'/>
</form>
</div>
</div></td>
</tr>
<tr style='height:223px;'>
<td><div style="width:142px; height:215px; background-image:url('assets/Buy_BG.png'); margin:0px; padding:0px; border:0px;">
<img style='position:relative; top:11px; width:64px; height:64px;' src='assets/FactionIcon1.jpg'/><br/><br/>Pledge to<br/>Sapphiros
_END;
if($faction == SAPPHIROS)
	echo "<br/><br/>You are already<br/>Chosen of<br/>Sapphiros";
else
{
echo <<<_END
<div style='position:relative; top:41px; left:-31px;'>10<br/>Gems</div><div style='position:relative; top:-10px; left:29px;'>
<form class='market' method="post" action="market.php">
<input type="hidden" name="posted" value="yes"/>
<input type="hidden" name="action" value="4"/>
<input type="hidden" name="faction" value="1"/>
<input type="hidden" name="token" value="$access_token"/>
<input type='image' src='assets/BTN_Buy.png' value='Buy'/>
</form>
</div>
_END;
}
echo <<<_END
</div></td>
<td><div style="width:142px; height:215px; background-image:url('assets/Buy_BG.png'); margin:0px; padding:0px; border:0px;">
<img style='position:relative; top:11px; width:64px; height:64px;' src='assets/FactionIcon2.jpg'/><br/><br/>Pledge to<br/>Rubia
_END;
if($faction == RUBIA)
	echo "<br/><br/>You are already<br/>Chosen of<br/>Rubia";
else
{
echo <<<_END
<div style='position:relative; top:41px; left:-31px;'>10<br/>Gems</div><div style='position:relative; top:-10px; left:29px;'>
 <form class='market' method="post" action="market.php">
<input type="hidden" name="posted" value="yes"/>
<input type="hidden" name="action" value="4"/>
<input type="hidden" name="faction" value="2"/>
<input type="hidden" name="token" value="$access_token"/>
<input type='image' src='assets/BTN_Buy.png' value='Buy'/>
</form>
</div>
_END;
}
echo <<<_END
</div></td>
<td><div style="width:142px; height:215px; background-image:url('assets/Buy_BG.png'); margin:0px; padding:0px; border:0px;">
<img style='position:relative; top:11px; width:64px; height:64px;' src='assets/FactionIcon3.jpg'/><br/><br/>Pledge to<br/>Jedeite
_END;
if($faction == JEDEITE)
	echo "<br/><br/>You are already<br/>Chosen of<br/>Jedeite";
else
{
echo <<<_END
<div style='position:relative; top:41px; left:-31px;'>10<br/>Gems</div><div style='position:relative; top:-10px; left:29px;'>
<form class='market' method="post" action="market.php">
<input type="hidden" name="posted" value="yes"/>
<input type="hidden" name="action" value="4"/>
<input type="hidden" name="faction" value="3"/>
<input type="hidden" name="token" value="$access_token"/>
<input type='image' src='assets/BTN_Buy.png' value='Buy'/>
</form>
</div>
_END;
}
echo <<<_END
</div></td>
<td><div style="width:142px; height:215px; background-image:url('assets/Buy_BG.png'); margin:0px; padding:0px; border:0px;">
<img style='position:relative; top:11px; width:64px; height:64px;' src='assets/FactionIcon4.jpg'/><br/><br/>Pledge to<br/>Damos
_END;
if($faction == DAMOS)
	echo "<br/><br/>You are already<br/>Chosen of<br/>Damos";
else
{
echo <<<_END
<div style='position:relative; top:41px; left:-31px;'>10<br/>Gems</div><div style='position:relative; top:-10px; left:29px;'>
<form class='market' method="post" action="market.php">
<input type="hidden" name="posted" value="yes"/>
<input type="hidden" name="action" value="4"/>
<input type="hidden" name="faction" value="4"/>
<input type="hidden" name="token" value="$access_token"/>
<input type='image' src='assets/BTN_Buy.png' value='Buy'/>
</form>
</div>
_END;
}
echo <<<_END
</div></td>
<td><div style="width:142px; height:215px; background-image:url('assets/Buy_BG.png'); margin:0px; padding:0px; border:0px;">
<img style='position:relative; top:11px; width:64px; height:64px;' src='assets/FactionIcon5.jpg'/><br/><br/>Pledge to<br/>Machalite
_END;
if($faction == MACHALITE)
	echo "<br/><br/>You are already<br/>Chosen of<br/>Machalite";
else
{
echo <<<_END
<div style='position:relative; top:41px; left:-31px;'>10<br/>Gems</div><div style='position:relative; top:-10px; left:29px;'>
<form class='market' method="post" action="market.php">
<input type="hidden" name="posted" value="yes"/>
<input type="hidden" name="action" value="4"/>
<input type="hidden" name="faction" value="5"/>
<input type="hidden" name="token" value="$access_token"/>
<input type='image' src='assets/BTN_Buy.png' value='Buy'/>
</form>
</div>
_END;
}
echo <<<_END
</div></td>
</tr>
<tr style='height:223px;'>
<td><div style="width:142px; height:215px; background-image:url('assets/Buy_BG.png'); margin:0px; padding:0px; border:0px;">
<img style='position:relative; top:11px;' src='assets/Buy_10SP.jpg'/><br/><br/>10<br/>Skill Points
<div style='position:relative; top:41px; left:-31px;'>12<br/>Gem</div><div style='position:relative; top:-10px; left:29px;'>
<form class='market' method="post" action="market.php">
<input type="hidden" name="posted" value="yes"/>
<input type="hidden" name="action" value="3"/>
<input type="hidden" name="token" value="$access_token"/>
<input type='image' src='assets/BTN_Buy.png' value='Buy'/>
</form>
</div>
</div></td>
<td><div style="width:142px; height:215px; background-image:url('assets/Buy_BG.png'); margin:0px; padding:0px; border:0px;">
<img style='position:relative; top:11px;' src='assets/Buy_ResetSP.jpg'/><br/><br/>Reset<br/>Skill Points
<div style='position:relative; top:41px; left:-31px;'>23<br/>Gems</div><div style='position:relative; top:-10px; left:29px;'>
 <form class='market' method="post" action="market.php">
<input type="hidden" name="posted" value="yes"/>
<input type="hidden" name="action" value="7"/>
<input type="hidden" name="token" value="$access_token"/>
<input type='image' src='assets/BTN_Buy.png' value='Buy'/>
</form>
</div>
</div></td>
<td><div style="width:142px; height:215px; background-image:url('assets/Buy_BG.png'); margin:0px; padding:0px; border:0px;">
<img style='position:relative; top:26px;' src='assets/Buy_NewName.jpg'/><br/><br/><br/><br/>New Name<br/>
<form class='market' method="post" action="market.php">
<input type="text" name="name" value="$name" size="16" maxlength="16"/>
<div style='position:relative; top:38px; left:-31px;'>20<br/>Gems</div><div style='position:relative; top:-13px; left:29px;'>

<input type="hidden" name="posted" value="yes"/>
<input type="hidden" name="action" value="10"/>
<input type="hidden" name="token" value="$access_token"/>
<input type='image' src='assets/BTN_Buy.png' value='Buy'/>
</form>
</div>
</div></td>
<td><div style="width:142px; height:215px; background-image:url('assets/Buy_BG.png'); margin:0px; padding:0px; border:0px;">
<img style='position:relative; top:11px;' src='assets/power75.jpg'/><br/><br/>Poison Touch<br/>Atk 43 Def 22<br/>Owned:$powers[74]
<div style='position:relative; top:25px; left:-31px;'>13<br/>Gems</div><div style='position:relative; top:-26px; left:29px;'><!--Normally 13-->
<form method='post' action='market.php'>
<input type='hidden' name='token' value='$access_token'/>
<input type='hidden' name='posted' value='yes'/>
<input type='hidden' name='action' value='5'/>
<input type='hidden' name='purchase' value='4'/>
<input type='image' src='assets/BTN_Buy.png' value='Buy'/>
</form>
</div>
</div></td>
<td><div style="width:142px; height:215px; background-image:url('assets/Buy_BG.png'); margin:0px; padding:0px; border:0px;">
<img style='position:relative; top:11px;' src='assets/power76.jpg'/><br/><br/>De-Possess<br/>Atk 10 Def 50<br/>Owned:$powers[75]
<div style='position:relative; top:25px; left:-31px;'>14<br/>Gems</div><div style='position:relative; top:-26px; left:29px;'>
<form method='post' action='market.php'>
<input type='hidden' name='token' value='$access_token'/>
<input type='hidden' name='posted' value='yes'/>
<input type='hidden' name='action' value='5'/>
<input type='hidden' name='purchase' value='5'/>
<input type='image' src='assets/BTN_Buy.png' value='Buy'/>
</form>
</div>
</div></td>
</tr>
<tr style='height:223px;'>
<td><div style="width:142px; height:215px; background-image:url('assets/Buy_BG.png'); margin:0px; padding:0px; border:0px;">
<img style='position:relative; top:11px;' src='assets/power90.jpg'/><br/><br/>Air Platform<br/>Atk 38 Def 21<br/>Owned:$powers[89]
<div style='position:relative; top:25px; left:-31px;'>12<br/>Gems</div><div style='position:relative; top:-26px; left:29px;'>
<form method='post' action='market.php'>
<input type='hidden' name='token' value='$access_token'/>
<input type='hidden' name='posted' value='yes'/>
<input type='hidden' name='action' value='5'/>
<input type='hidden' name='purchase' value='8'/>
<input type='image' src='assets/BTN_Buy.png' value='Buy'/>
</form>
</div>
</div></td>
<td><div style="width:142px; height:215px; background-image:url('assets/Buy_BG.png'); margin:0px; padding:0px; border:0px;">
<img style='position:relative; top:11px;' src='assets/power91.jpg'/><br/><br/>Lightning<br/>Atk 48 Def 16<br/>Owned:$powers[90]
<div style='position:relative; top:25px; left:-31px;'>13<br/>Gems</div><div style='position:relative; top:-26px; left:29px;'>
<form method='post' action='market.php'>
<input type='hidden' name='token' value='$access_token'/>
<input type='hidden' name='posted' value='yes'/>
<input type='hidden' name='action' value='5'/>
<input type='hidden' name='purchase' value='9'/>
<input type='image' src='assets/BTN_Buy.png' value='Buy'/>
</form>
</div>
</div></td>
<td><div style="width:142px; height:215px; background-image:url('assets/Buy_BG.png'); margin:0px; padding:0px; border:0px;">
<img style='position:relative; top:11px;' src='assets/power92.jpg'/><br/><br/>Earth Armour<br/>Atk 25 Def 48<br/>Owned:$powers[91]
<div style='position:relative; top:25px; left:-31px;'>14<br/>Gems</div><div style='position:relative; top:-26px; left:29px;'>
<form method='post' action='market.php'>
<input type='hidden' name='token' value='$access_token'/>
<input type='hidden' name='posted' value='yes'/>
<input type='hidden' name='action' value='5'/>
<input type='hidden' name='purchase' value='10'/>
<input type='image' src='assets/BTN_Buy.png' value='Buy'/>
</form>
</div>
</div></td>
<td><div style="width:142px; height:215px; background-image:url('assets/Buy_BG.png'); margin:0px; padding:0px; border:0px;">
<img style='position:relative; top:11px;' src='assets/power96.jpg'/><br/><br/>Create Food<br/>Atk 24 Def 40<br/>Owned:$powers[95]
<div style='position:relative; top:25px; left:-31px;'>11<br/>Gems</div><div style='position:relative; top:-26px; left:29px;'><!-normally 11-->
<form method='post' action='market.php'>
<input type='hidden' name='token' value='$access_token'/>
<input type='hidden' name='posted' value='yes'/>
<input type='hidden' name='action' value='5'/>
<input type='hidden' name='purchase' value='11'/>
<input type='image' src='assets/BTN_Buy.png' value='Buy'/>
</form>
</div>
</div></td>
<!--
<td><div style="width:142px; height:215px; background-image:url('assets/Buy_BG.png'); margin:0px; padding:0px; border:0px;">
<img style='position:relative; top:11px;' src='assets/power165.jpg'/><br/><br/>Transform Self<br/>Atk 60 Def 30<br/>Owned:$powers[164]
<div style='position:relative; top:25px; left:-31px;'>17<br/>Gems</div><div style='position:relative; top:-26px; left:29px;'>
<form method='post' action='market.php'>
<input type='hidden' name='token' value='$access_token'/>
<input type='hidden' name='posted' value='yes'/>
<input type='hidden' name='action' value='5'/>
<input type='hidden' name='purchase' value='12'/>
<input type='image' src='assets/BTN_Buy.png' value='Buy'/>
</form>
</div>
</div></td>
-->
<td><div style="width:142px; height:215px; background-image:url('assets/Buy_BG.png'); margin:0px; padding:0px; border:0px;">
<img style='position:relative; top:11px;' src='assets/power167.jpg'/><br/><br/>Far Slice<br/>Atk 50 Def 20<br/>Owned:$powers[166]
<div style='position:relative; top:25px; left:-31px;'>14<br/>Gems</div><div style='position:relative; top:-26px; left:29px;'>
<form method='post' action='market.php'>
<input type='hidden' name='token' value='$access_token'/>
<input type='hidden' name='posted' value='yes'/>
<input type='hidden' name='action' value='5'/>
<input type='hidden' name='purchase' value='12'/>
<input type='image' src='assets/BTN_Buy.png' value='Buy'/>
</form>
</div>
</div></td>
</tr>
</table>
<div style='height:16px; width:1px;'></div>
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
var gems = $gems;
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
<script>

var addGems = 0;
    function placeOrder(order) {
    //cash transaction
      if(order=="11a")
      	addGems = 24;
      else if(order=="22b")
      	addGems = 144;

      // calling the API ...
      var obj = {
	    method: 'pay',
	    order_info: order,
	    purchase_type: 'item'
      };

      FB.ui(obj, callback);
    }
    
    var callback = function(data) {
      if (data['order_id']) {
        alert("Transaction Completed! Order ID: " + data['order_id']);
        gems += addGems;
       document.getElementById('hGems').innerHTML = "<a class='$style' href='market.php?token=$access_token'>Market(<img src='assets/GemIcon$faction.png'/>" + gems + ")</a>";
      } else if (data['error_code']) {
        alert("Transaction Failed! "
        + "Error message returned from Facebook: "
        + data['error_message'])+" ";
      } else {
        alert("Transaction failed!");
      }
      addGems = 0;
    };

    


    

  </script>
</body>
</html>
_END;
mysql_close($db_server);
?>
