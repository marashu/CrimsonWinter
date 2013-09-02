<?php
require_once 'src/facebook.php';
require_once 'db_vars.php';
require_once 'functions.php';
require_once 'cw_vars.php';

///////NEVER, EVER SAVE THIS FILE USING THE CPANEL CODE EDITOR!!!  VERY IMPORTANT!!!

$app_id = APP_ID;
    $app_secret = APP_SECRET;
    $my_url = "http://apps.facebook.com/crimsonwinter/missions.php";
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
$time= $rows[3];
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
$fac_change = $rows[20];
$location = $rows[21];
$powerTotal = $rows[28];

$diffETime = 0;
$diffSTime = 0;
$diffHTime = 0;

$levelUp = 0;
$played = $rows[29];
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

$query = "SELECT power73, power74 FROM CW_Inventory WHERE id='" . $uid . "'";
$result = mysql_query($query);
$row = mysql_fetch_row($result);
$ExtraBelts = $row[0];
$Mount = $row[1];

$query = "SELECT * FROM CW_Missions WHERE id='" . $uid . "'";//for real game, add WHERE to narrow it down to fb uid
$result = mysql_query($query);

if(!$result) die("Database access failed: " . mysql_error());


$counter = 0;
$rows = mysql_fetch_row($result);

for($i = 1; $i < mysql_num_fields($result); $i++)
{
	if($counter < 10)
	{
		$counter++;
		$jExp[] = $rows[$i];
	}
	else
	{
		$counter = 0;
		$jTier[] = $rows[$i];
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

if($jTier[4] == 4 && $faction == SAPPHIROS)
$sapphirosGold = 1;
else
$sapphirosGold = 0;
if($jTier[5] == 4 && $faction == SAPPHIROS)
$sapphirosGold2 = 1;
else
$sapphirosGold2 = 0;

$query = "SELECT achievement20, achievement16, achievement17, achievement18, achievement31, achievement35, achievement40 FROM CW_Achievements WHERE id='" . $uid . "'";
$result = mysql_query($query);
$row = mysql_fetch_row($result);
$WealthyChosen = $row[0];
$Repertoire = $row[1];
$GoldRush = $row[2];
$GoldMedalist = $row[3];
$AllSetForSand = $row[4];
$TaiyouTourist = $row[5];
$BeenThereDoneThat = $row[6];

//for real game, narrow down inventory to only fields I need
$query = "SELECT power45, power15, power21, power54, power1, power47, power37, power27, power41, power43, power23, power49, power12, power42, power2, power33, power50, power39, power40, power44, power60, power46, power30, power123, power126, power127, power125, power122, power130, power131, power133, power134, power135, power3, power139, power136, power129, power140, power132, power144, power150, power143, power142, power138, power137, power148, power141, power149, power146, power128, power154, power147, power156, power152, power158, power157, power151  FROM CW_Inventory WHERE id='" . $uid . "'";//for real game, add WHERE to narrow it down to fb uid
$result = mysql_query($query);

if(!$result) die("Database access failed: " . mysql_error());
$rows = mysql_fetch_row($result);

$power1 = $rows[0];
$mistOut = $rows[2];
$slowPerson = $rows[1];
$createWater = $rows[3];
$arrow = $rows[4];
$farSight = $rows[5];
$obscureVision = $rows[6];
$gustOfWind = $rows[7];
$ropeArrow = $rows[8];
$lightArrow = $rows[9];
$heal = $rows[10];
$nearSight = $rows[11];
$fireball = $rows[12];
$netArrow = $rows[13];
$penetrationArrow = $rows[14];
$intimidate = $rows[15];
$snow = $rows[16];
$resistHeat = $rows[17];
$resistCold = $rows[18];
$readMemoryPerson = $rows[20];
$messageArrow = $rows[19];
$mistTravel = $rows[21];
$largeShield = $rows[22];
$enhanceVoice = $rows[23];
$increasedIntelligence = $rows[24];
$increasedCharisma = $rows[25];
$immunityToPoison = $rows[26];
$explodingArrow = $rows[27];
$deafeningRoar = $rows[28];
$mistOther = $rows[29];
$wings = $rows[30];
$confusion = $rows[31];
$hover = $rows[32];
$energyDrainingNet = $rows[33];
$xrayVision = $rows[34];
$vision = $rows[35];
$antibodies = $rows[36];
$heat = $rows[37];
$summonMistedPerson = $rows[38];
$bloodhound = $rows[39];
$locatePerson = $rows[40];
$wireFrameVision = $rows[41];
$vanishAndStorePerson = $rows[42];
$increasedSpeed = $rows[43];
$readMemoryLocation = $rows[44];
$bestowPower = $rows[45];
$inspireConfidence = $rows[46];
$auraOfPower = $rows[47];
$inspireFear = $rows[48];
$vanishAndStoreObject = $rows[49];
$readLanguages = $rows[50];
$powerCounter = $rows[51];
$explosion = $rows[52];
$magicSight = $rows[53];
$wallOfEarth = $rows[54];
$memoryTransfer = $rows[55];
$premonition = $rows[56];


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

$missiongroup = $location + (5 * $region);

$group = $_GET[group];
if($group != "")
{
$group += 5 * $region;
if((($group == 9 && $level > 180) || ($group == 8 && $level > 150) || ($group == 7 && $level > 125) || ($group == 6 && $level > 100) || ($group == 5 && $level > 80) ||($group == 4 && $level > 60) || ($group == 3 && $level > 40) || ($group == 2 && $level > 25) || ($group == 1 && $level > 10) || $group == 0))
{
$missiongroup = $group;
$location = $missiongroup % 5;
$query = "UPDATE CW_Users SET location='" . $location . "' WHERE id='" . $uid . "'";//for real game, add WHERE to narrow it down to fb uid
$result = mysql_query($query);
}
else
	echo "Your level is not high enough to access these missions.";
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
//set an array for mission details
for($i = 0; $i < count($jExp); $i++)
{
	switch($i)
	{
		case 0:
			$usedEnergy[$i] = 1;
			$expWon[$i] = 1;
			$krevelsWon[$i] = 3;
			$jExpBase[$i] = 10.5;
			break;
		case 1:
			$usedEnergy[$i] = 2;
			$expWon[$i] = 2;
			$krevelsWon[$i] = 7;
			$jExpBase[$i] = 10.5;
			break;
			
		case 2:
			$usedEnergy[$i] = 2;
			$expWon[$i] = 2;
			$krevelsWon[$i] = 10;
			$jExpBase[$i] = 10.5;
			break;
		case 3:
			$usedEnergy[$i] = 3;
			$expWon[$i] = 3;
			$krevelsWon[$i] = 16;
			$jExpBase[$i] = 10.3;
			break;
			
		case 4:
			$usedEnergy[$i] = 4;
			$expWon[$i] = 4;
			$krevelsWon[$i] = 23;
			$jExpBase[$i] = 10.3;
			break;
		case 5:
			$usedEnergy[$i] = 5;
			$expWon[$i] = 6;
			$krevelsWon[$i] = 30;
			$jExpBase[$i] = 10.3;
			break;
			
		case 6:
			$usedEnergy[$i] = 5;
			$expWon[$i] = 6;
			$krevelsWon[$i] = 36;
			$jExpBase[$i] = 9.5;
			break;
		case 7:
			$usedEnergy[$i] = 6;
			$expWon[$i] = 7;
			$krevelsWon[$i] = 44;
			$jExpBase[$i] = 9.5;
			break;
			
		case 8:
			$usedEnergy[$i] = 7;
			$expWon[$i] = 8;
			$krevelsWon[$i] = 55;
			$jExpBase[$i] = 9;
			break;
		case 9:
			$usedEnergy[$i] = 8;
			$expWon[$i] = 10;
			$krevelsWon[$i] = 67;
			$jExpBase[$i] = 8.8;
			break;
		case 10:
			$usedEnergy[$i] = 10;
			$expWon[$i] = 12;
			$krevelsWon[$i] = 90;
			$jExpBase[$i] = 8;
			break;
		case 11:
			$usedEnergy[$i] = 12;
			$expWon[$i] = 14;
			$krevelsWon[$i] = 100;
			$jExpBase[$i] = 7.5;
			break;
			
		case 12:
			$usedEnergy[$i] = 13;
			$expWon[$i] = 15;
			$krevelsWon[$i] = 130;
			$jExpBase[$i] = 7.5;
			break;
		case 13:
			$usedEnergy[$i] = 15;
			$expWon[$i] = 17;
			$krevelsWon[$i] = 170;
			$jExpBase[$i] = 7.2;
			break;
			
		case 14:
			$usedEnergy[$i] = 15;
			$expWon[$i] = 18;
			$krevelsWon[$i] = 160;
			$jExpBase[$i] = 7.2;
			break;
		case 15:
			$usedEnergy[$i] = 16;
			$expWon[$i] = 18;
			$krevelsWon[$i] = 180;
			$jExpBase[$i] = 6.9;
			break;
			
		case 16:
			$usedEnergy[$i] = 17;
			$expWon[$i] = 20;
			$krevelsWon[$i] = 210;
			$jExpBase[$i] = 6.7;
			break;
		case 17:
			$usedEnergy[$i] = 17;
			$expWon[$i] = 19;
			$krevelsWon[$i] = 240;
			$jExpBase[$i] = 6.4;
			break;
			
		case 18:
			$usedEnergy[$i] = 18;
			$expWon[$i] = 23;
			$krevelsWon[$i] = 260;
			$jExpBase[$i] = 6.4;
			break;
		case 19:
			$usedEnergy[$i] = 20;
			$expWon[$i] = 25;
			$krevelsWon[$i] = 290;
			$jExpBase[$i] = 6;
			break;
		case 20:
			$usedEnergy[$i] = 21;
			$expWon[$i] = 27;
			$krevelsWon[$i] = 500;
			$jExpBase[$i] = 6;
			break;
		case 21:
			$usedEnergy[$i] = 23;
			$expWon[$i] = 30;
			$krevelsWon[$i] = 570;
			$jExpBase[$i] = 6;
			break;
			
		case 22:
			$usedEnergy[$i] = 23;
			$expWon[$i] = 29;
			$krevelsWon[$i] = 600;
			$jExpBase[$i] = 5.7;
			break;
		case 23:
			$usedEnergy[$i] = 25;
			$expWon[$i] = 32;
			$krevelsWon[$i] = 650;
			$jExpBase[$i] = 5.7;
			break;
			
		case 24:
			$usedEnergy[$i] = 26;
			$expWon[$i] = 33;
			$krevelsWon[$i] = 680;
			$jExpBase[$i] = 5.5;
			break;
		case 25:
			$usedEnergy[$i] = 26;
			$expWon[$i] = 32;
			$krevelsWon[$i] = 720;
			$jExpBase[$i] = 5.3;
			break;
			
		case 26:
			$usedEnergy[$i] = 27;
			$expWon[$i] = 35;
			$krevelsWon[$i] = 750;
			$jExpBase[$i] = 5.3;
			break;
		case 27:
			$usedEnergy[$i] = 28;
			$expWon[$i] = 36;
			$krevelsWon[$i] = 780;
			$jExpBase[$i] = 5.3;
			break;
			
		case 28:
			$usedEnergy[$i] = 28;
			$expWon[$i] = 35;
			$krevelsWon[$i] = 820;
			$jExpBase[$i] = 5;
			break;
		case 29:
			$usedEnergy[$i] = 30;
			$expWon[$i] = 40;
			$krevelsWon[$i] = 950;
			$jExpBase[$i] = 5;
			break;
		case 30:
			$usedEnergy[$i] = 31;
			$expWon[$i] = 41;
			$krevelsWon[$i] = 1100;
			$jExpBase[$i] = 5;
			break;
		case 31:
			$usedEnergy[$i] = 32;
			$expWon[$i] = 41;
			$krevelsWon[$i] = 1200;
			$jExpBase[$i] = 4.8;
			break;
			
		case 32:
			$usedEnergy[$i] = 32;
			$expWon[$i] = 42;
			$krevelsWon[$i] = 1320;
			$jExpBase[$i] = 4.8;
			break;
		case 33:
			$usedEnergy[$i] = 34;
			$expWon[$i] = 44;
			$krevelsWon[$i] = 1400;
			$jExpBase[$i] = 4.7;
			break;
			
		case 34:
			$usedEnergy[$i] = 34;
			$expWon[$i] = 43;
			$krevelsWon[$i] = 1450;
			$jExpBase[$i] = 4.4;
			break;
		case 35:
			$usedEnergy[$i] = 35;
			$expWon[$i] = 48;
			$krevelsWon[$i] = 1530;
			$jExpBase[$i] = 4.2;
			break;
			
		case 36:
			$usedEnergy[$i] = 37;
			$expWon[$i] = 50;
			$krevelsWon[$i] = 1800;
			$jExpBase[$i] = 4.2;
			break;
		case 37:
			$usedEnergy[$i] = 38;
			$expWon[$i] = 52;
			$krevelsWon[$i] = 1900;
			$jExpBase[$i] = 3.8;
			break;
			
		case 38:
			$usedEnergy[$i] = 38;
			$expWon[$i] = 50;
			$krevelsWon[$i] = 1980;
			$jExpBase[$i] = 3.8;
			break;
		case 39:
			$usedEnergy[$i] = 40;
			$expWon[$i] = 56;
			$krevelsWon[$i] = 2200;
			$jExpBase[$i] = 3.6;
			break;
		case 40:
			$usedEnergy[$i] = 43;
			$expWon[$i] = 60;
			$krevelsWon[$i] = 3000;
			$jExpBase[$i] = 3.5;
			break;
		case 41:
			$usedEnergy[$i] = 45;
			$expWon[$i] = 64;
			$krevelsWon[$i] = 3400;
			$jExpBase[$i] = 3.3;
			break;
			
		case 42:
			$usedEnergy[$i] = 48;
			$expWon[$i] = 69;
			$krevelsWon[$i] = 3800;
			$jExpBase[$i] = 2.7;
			break;
		case 43:
			$usedEnergy[$i] = 50;
			$expWon[$i] = 72;
			$krevelsWon[$i] = 4000;
			$jExpBase[$i] = 2.5;
			break;
			
		case 44:
			$usedEnergy[$i] = 53;
			$expWon[$i] = 77;
			$krevelsWon[$i] = 4300;
			$jExpBase[$i] = 2.4;
			break;
		case 45:
			$usedEnergy[$i] = 55;
			$expWon[$i] = 81;
			$krevelsWon[$i] = 4600;
			$jExpBase[$i] = 2;
			break;
			
		case 46:
			$usedEnergy[$i] = 58;
			$expWon[$i] = 86;
			$krevelsWon[$i] = 5100;
			$jExpBase[$i] = 1.7;
			break;
		case 47:
			$usedEnergy[$i] = 60;
			$expWon[$i] = 90;
			$krevelsWon[$i] = 5500;
			$jExpBase[$i] = 1.6;
			break;
			
		case 48:
			$usedEnergy[$i] = 62;
			$expWon[$i] = 94;
			$krevelsWon[$i] = 5800;
			$jExpBase[$i] = 1.4;
			break;
		case 49:
			$usedEnergy[$i] = 65;
			$expWon[$i] = 100;
			$krevelsWon[$i] = 6100;
			$jExpBase[$i] = 1;
			break;
		case 50:
			$usedEnergy[$i] = 44;
			$expWon[$i] = 57;
			$krevelsWon[$i] = 6950;
			$jExpBase[$i] = 9;
			break;
		case 51:
			$usedEnergy[$i] = 45;
			$expWon[$i] = 60;
			$krevelsWon[$i] = 7250;
			$jExpBase[$i] = 8.8;
			break;
			
		case 52:
			$usedEnergy[$i] = 46;
			$expWon[$i] = 63;
			$krevelsWon[$i] = 7500;
			$jExpBase[$i] = 8.65;
			break;
		case 53:
			$usedEnergy[$i] = 46;
			$expWon[$i] = 64;
			$krevelsWon[$i] = 7700;
			$jExpBase[$i] = 8.5;
			break;
			
		case 54:
			$usedEnergy[$i] = 48;
			$expWon[$i] = 67;
			$krevelsWon[$i] = 7900;
			$jExpBase[$i] = 8.5;
			break;
		case 55:
			$usedEnergy[$i] = 50;
			$expWon[$i] = 71;
			$krevelsWon[$i] = 8100;
			$jExpBase[$i] = 8.45;
			break;
			
		case 56:
			$usedEnergy[$i] = 52;
			$expWon[$i] = 74;
			$krevelsWon[$i] = 8400;
			$jExpBase[$i] = 8.4;
			break;
		case 57:
			$usedEnergy[$i] = 53;
			$expWon[$i] = 77;
			$krevelsWon[$i] = 9000;
			$jExpBase[$i] = 8.2;
			break;
			
		case 58:
			$usedEnergy[$i] = 54;
			$expWon[$i] = 81;
			$krevelsWon[$i] = 9600;
			$jExpBase[$i] = 8.1;
			break;
		case 59:
			$usedEnergy[$i] = 55;
			$expWon[$i] = 85;
			$krevelsWon[$i] = 10400;
			$jExpBase[$i] = 8;
			break;
		case 60:
			$usedEnergy[$i] = 70;
			$expWon[$i] = 112;
			$krevelsWon[$i] = 11000;
			$jExpBase[$i] = 8;
			break;
		case 61:
			$usedEnergy[$i] = 71;
			$expWon[$i] = 116;
			$krevelsWon[$i] = 11500;
			$jExpBase[$i] = 7.7;
			break;
			
		case 62:
			$usedEnergy[$i] = 73;
			$expWon[$i] = 118;
			$krevelsWon[$i] = 13000;
			$jExpBase[$i] = 7.5;
			break;
		case 63:
			$usedEnergy[$i] = 75;
			$expWon[$i] = 124;
			$krevelsWon[$i] = 13100;
			$jExpBase[$i] = 7.3;
			break;
			
		case 64:
			$usedEnergy[$i] = 78;
			$expWon[$i] = 130;
			$krevelsWon[$i] = 13900;
			$jExpBase[$i] = 7;
			break;
		case 65:
			$usedEnergy[$i] = 80;
			$expWon[$i] = 136;
			$krevelsWon[$i] = 14000;
			$jExpBase[$i] = 6.7;
			break;
			
		case 66:
			$usedEnergy[$i] = 84;
			$expWon[$i] = 144;
			$krevelsWon[$i] = 15000;
			$jExpBase[$i] = 6.4;
			break;
		case 67:
			$usedEnergy[$i] = 88;
			$expWon[$i] = 151;
			$krevelsWon[$i] = 15700;
			$jExpBase[$i] = 6.2;
			break;
			
		case 68:
			$usedEnergy[$i] = 92;
			$expWon[$i] = 160;
			$krevelsWon[$i] = 16500;
			$jExpBase[$i] = 6;
			break;
		case 69:
			$usedEnergy[$i] = 95;
			$expWon[$i] = 166;
			$krevelsWon[$i] = 17400;
			$jExpBase[$i] = 6;
			break;
		case 70:
			$usedEnergy[$i] = 100;
			$expWon[$i] = 175;
			$krevelsWon[$i] = 19500;
			$jExpBase[$i] = 6.2;
			break;
		case 71:
			$usedEnergy[$i] = 103;
			$expWon[$i] = 181;
			$krevelsWon[$i] = 21700;
			$jExpBase[$i] = 6;
			break;
			
		case 72:
			$usedEnergy[$i] = 107;
			$expWon[$i] = 188;
			$krevelsWon[$i] = 22000;
			$jExpBase[$i] = 5.8;
			break;
		case 73:
			$usedEnergy[$i] = 110;
			$expWon[$i] = 195;
			$krevelsWon[$i] = 23500;
			$jExpBase[$i] = 5.6;
			break;
			
		case 74:
			$usedEnergy[$i] = 112;
			$expWon[$i] = 198;
			$krevelsWon[$i] = 24700;
			$jExpBase[$i] = 5.5;
			break;
		case 75:
			$usedEnergy[$i] = 115;
			$expWon[$i] = 207;
			$krevelsWon[$i] = 25600;
			$jExpBase[$i] = 5.5;
			break;
			
		case 76:
			$usedEnergy[$i] = 119;
			$expWon[$i] = 217;
			$krevelsWon[$i] = 26900;
			$jExpBase[$i] = 5.4;
			break;
		case 77:
			$usedEnergy[$i] = 122;
			$expWon[$i] = 226;
			$krevelsWon[$i] = 28400;
			$jExpBase[$i] = 5.3;
			break;
			
		case 78:
			$usedEnergy[$i] = 125;
			$expWon[$i] = 234;
			$krevelsWon[$i] = 29300;
			$jExpBase[$i] = 5.2;
			break;
		case 79:
			$usedEnergy[$i] = 130;
			$expWon[$i] = 247;
			$krevelsWon[$i] = 31000;
			$jExpBase[$i] = 5;
			break;
		case 80:
			$usedEnergy[$i] = 135;
			$expWon[$i] = 257;
			$krevelsWon[$i] = 31200;
			$jExpBase[$i] = 5;
			break;
		case 81:
			$usedEnergy[$i] = 138;
			$expWon[$i] = 261;
			$krevelsWon[$i] = 32300;
			$jExpBase[$i] = 4.8;
			break;
			
		case 82:
			$usedEnergy[$i] = 142;
			$expWon[$i] = 271;
			$krevelsWon[$i] = 34800;
			$jExpBase[$i] = 4.7;
			break;
		case 83:
			$usedEnergy[$i] = 146;
			$expWon[$i] = 280;
			$krevelsWon[$i] = 36100;
			$jExpBase[$i] = 4.6;
			break;
			
		case 84:
			$usedEnergy[$i] = 150;
			$expWon[$i] = 291;
			$krevelsWon[$i] = 37500;
			$jExpBase[$i] = 4.3;
			break;
		case 85:
			$usedEnergy[$i] = 158;
			$expWon[$i] = 308;
			$krevelsWon[$i] = 38000;
			$jExpBase[$i] = 4.2;
			break;
			
		case 86:
			$usedEnergy[$i] = 161;
			$expWon[$i] = 317;
			$krevelsWon[$i] = 39800;
			$jExpBase[$i] = 4;
			break;
		case 87:
			$usedEnergy[$i] = 165;
			$expWon[$i] = 327;
			$krevelsWon[$i] = 41200;
			$jExpBase[$i] = 3.8;
			break;
			
		case 88:
			$usedEnergy[$i] = 168;
			$expWon[$i] = 334;
			$krevelsWon[$i] = 43600;
			$jExpBase[$i] = 3.5;
			break;
		case 89:
			$usedEnergy[$i] = 170;
			$expWon[$i] = 340;
			$krevelsWon[$i] = 45000;
			$jExpBase[$i] = 3.3;
			break;
		case 90:
			$usedEnergy[$i] = 180;
			$expWon[$i] = 360;
			$krevelsWon[$i] = 45000;
			$jExpBase[$i] = 3.3;
			break;
		case 91:
			$usedEnergy[$i] = 187;
			$expWon[$i] = 381;
			$krevelsWon[$i] = 46500;
			$jExpBase[$i] = 3;
			break;
			
		case 92:
			$usedEnergy[$i] = 192;
			$expWon[$i] = 396;
			$krevelsWon[$i] = 48100;
			$jExpBase[$i] = 2.7;
			break;
		case 93:
			$usedEnergy[$i] = 197;
			$expWon[$i] = 407;
			$krevelsWon[$i] = 49300;
			$jExpBase[$i] = 2.4;
			break;
			
		case 94:
			$usedEnergy[$i] = 201;
			$expWon[$i] = 418;
			$krevelsWon[$i] = 52200;
			$jExpBase[$i] = 2.2;
			break;
		case 95:
			$usedEnergy[$i] = 205;
			$expWon[$i] = 431;
			$krevelsWon[$i] = 55100;
			$jExpBase[$i] = 2;
			break;
			
		case 96:
			$usedEnergy[$i] = 211;
			$expWon[$i] = 447;
			$krevelsWon[$i] = 57800;
			$jExpBase[$i] = 1.5;
			break;
		case 97:
			$usedEnergy[$i] = 216;
			$expWon[$i] = 464;
			$krevelsWon[$i] = 60900;
			$jExpBase[$i] = 1.2;
			break;
			
		case 98:
			$usedEnergy[$i] = 223;
			$expWon[$i] = 486;
			$krevelsWon[$i] = 62500;
			$jExpBase[$i] = 1.1;
			break;
		case 99:
			$usedEnergy[$i] = 230;
			$expWon[$i] = 506;
			$krevelsWon[$i] = 65000;
			$jExpBase[$i] = 1;
			break;
	}
	if($sapphirosGold == 1)
		$expWon[$i] += (int)($expWon[$i] * 0.1);
	//$expWon[$i] *= 2;
	//$jExpBase[$i] *= 2;
}

$didMission = 0;
$job = 0;
$errorMsg = '';
$drop = 0;
$dropMsg = '';
$areaChange = 0;
$clearMission = 0;
$clearTier = 0;
if($_POST[posted] == "yes")
{
	$_POST[posted] = "no";
	$job = SanitizeString($_POST[job]);
	
	
	
	if($c_energy < $usedEnergy[$job] && $usedEnergy[$job] != 0)
	{
		$errorMsg = "Need more energy?  <a class='red' href='market.php'>Fill up now</a>";
		$didMission = -1;
	}
	else if($usedEnergy[$job] == 0)
	{
		$errorMsg = "An error occured.  Please try again.";
		$didMission = -1;
	}
//make sure the user has the right powers to do the mission
	else if(($job == 5 && $slowPerson < 1) || ($job == 6 && $mistOut < 1) ||
		($job == 17 && $createWater < 1) || ($job == 18 && $arrow < 1) || ($job == 19 && $farSight < 1) || ($job == 23 && $obscureVision < 5) ||
		($job == 26 && $gustOfWind < 10) || ($job == 27 && $ropeArrow < 10) || ($job == 28 && $lightArrow < 5) ||
		($job == 31 && $heal < 10) || ($job == 32 && $nearSight < 10) || ($job == 33 && $createWater < 20) || ($job == 34 && ($fireball < 10 || $netArrow < 10)) ||
		($job == 36 && $penetrationArrow < 15) || ($job == 37 && $intimidate < 15) || ($job == 39 && $snow < 10) || ($job == 42 && $resistHeat < 5) ||
		($job == 43 && $resistCold < 5) || ($job == 44 && $messageArrow < 20) || ($job == 45 && $readMemoryPerson < 8) || ($job == 49 && ($largeShield < 20 || $mistTravel < 10)) ||
		($job == 51 && $enhanceVoice < 20) || ($job == 55 && $increasedIntelligence < 25)|| ($job == 56 && $increasedCharisma < 25) ||
		($job == 65 && $immunityToPoison < 15) || ($job == 66 && $explodingArrow < 15) || ($job == 67 && $mistOther < 40) || ($job == 69 && $wings < 40) ||
		($job == 71 && ($confusion < 15 || $hover < 45)) || ($job == 72 && $energyDrainingNet < 25) || ($job == 73 && $xrayVision < 40) || ($job == 74 && $vision < 50) || ($job == 77 && $antibodies < 15) ||
		($job == 78 && $heat < 100) || ($job == 79 && $summonMistedPerson < 15) || ($job == 81 && $bloodhound < 50) || ($job == 82 && $locatePerson < 20) || ($job == 83 && $wireFrameVision < 55) || ($job == 84 && ($vanishAndStorePerson < 50 || $increasedSpeed < 25)) ||
		($job == 85 && $readMemoryLocation < 25) || ($job == 86 && $bestowPower < 65) || ($job == 89 && ($inspireConfidence < 25 || $auraOfPower < 350)) || ($job == 90 && $inspireFear < 30) ||
		($job == 94 && ($vanishAndStoreObject < 100 || $readLanguages < 100)) || ($job == 95 && ($powerCounter < 30 || $explosion < 120)) || ($job == 97 && ($magicSight < 30 || $wallOfEarth < 130)) ||
		($job == 98 && $memoryTransfer < 150) || ($job == 99 && $premonition < 40))
	{
		$errorMsg = "Not enough powers.";
		$didMission = -1;
	}
//Make sure the user is part of the right team
	else if(($job == 17 && $faction != SAPPHIROS) || ($job == 20 && $faction != MACHALITE) || ($job == 36 && $faction != DAMOS) || ($job == 37 && $faction != JEDEITE) ||
	($job == 42 && $faction != SAPPHIROS) || ($job == 45 && $faction != RUBIA) || ($job == 61 && $faction != MACHALITE) || ($job == 68 && $faction != DAMOS) || ($job == 72 && $faction != SAPPHIROS)||
	($job == 79 && $faction != JEDEITE) || ($job == 84 && $faction != DAMOS) || ($job == 86 && $faction != SAPPHIROS) || ($job == 89 && $faction != RUBIA) || ($job == 90 && $faction != MACHALITE) ||
	($job == 91 && $faction != JEDEITE)|| ($job == 95 && $faction != DAMOS) || ($job == 99 && $faction != RUBIA))
	{
		$errorMsg = "Infidel!  Only certain Chosen may complete this task. <a class='red' href='market.php'>Change your faction.</a>";
		$didMission = -1;
	}
	else
	{
		if($ExtraBelts)
		{
			$expWon[$job] *= 2;
			$query = "UPDATE CW_Inventory SET power73=power73 - 1 WHERE id='" . $uid . "'";
			$result = mysql_query($query);
		}
		if($Mount)
		{
			$krevelsWon[$job] *= 2;
			$query = "UPDATE CW_Inventory SET power74=power74 - 1 WHERE id='" . $uid . "'";
			$result = mysql_query($query);
		}	
		//this if is for when the player's timer needs to start
		if($c_energy >= $m_energy && $c_energy - $usedEnergy[$job] < $m_energy)
		{
		
			if($jExp[$job] < 100)
			{
				$jExp[$job] += (int)($jExpBase[$job] * (4 - $jTier[(int)($job/10)]));
				if($jExp[$job] >= 100)
				{
					$jExp[$job] = 100;
					$skills += 1;
					$clearMission = 1;
				}
			}
			
			if($jExp[0] == 100 && $jExp[1] == 100 && $jExp[2] == 100 && $jExp[3] == 100 && $jExp[4] == 100 && 
				$jExp[5] == 100 && $jExp[6] == 100 && $jExp[7] == 100 && $jExp[8] == 100 && $jExp[9] == 100 && $jTier[0] < 4)
			{
				if($jTier[0] < 3)
				{
				$jExp[0] = 0;
				$jExp[1] = 0;
				$jExp[2] = 0;
				$jExp[3] = 0;
				$jExp[4] = 0;
				$jExp[5] = 0;
				$jExp[6] = 0;
				$jExp[7] = 0;
				$jExp[8] = 0;
				$jExp[9] = 0;
				}
				$jTier[0]++;
				$skills++;
				$clearTier = 1;
				$query = "UPDATE CW_Users SET skills='" . $skills . "'  WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$query = "UPDATE CW_Missions SET jExp1='" . $jExp[0] . "', jExp2='" . $jExp[1] . "', jExp3='" . $jExp[2] . "', jExp4='" . $jExp[3] . "', jExp5='" . $jExp[4] . "', jExp6='" . $jExp[5] . "', jExp7='" . $jExp[6] . "', jExp8='" . $jExp[7] . "', jExp9='" . $jExp[8] . "', jExp10='" . $jExp[9] . "', jTier1='" . $jTier[0] . "'  WHERE id='" . $uid . "'";
				$result = mysql_query($query);
			}
			if($jExp[10] == 100 && $jExp[11] == 100 && $jExp[12] == 100 && $jExp[13] == 100 && $jExp[14] == 100 && 
				$jExp[15] == 100 && $jExp[16] == 100 && $jExp[17] == 100 && $jExp[18] == 100 && $jExp[19] == 100 && $jTier[1] < 4)
			{
				if($jTier[1] < 3)
				{
				$jExp[10] = 0;
				$jExp[11] = 0;
				$jExp[12] = 0;
				$jExp[13] = 0;
				$jExp[14] = 0;
				$jExp[15] = 0;
				$jExp[16] = 0;
				$jExp[17] = 0;
				$jExp[18] = 0;
				$jExp[19] = 0;
				}
				$clearTier = 1;
				$jTier[1]++;
				$skills++;
				$query = "UPDATE CW_Users SET skills='" . $skills . "'  WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$query = "UPDATE CW_Missions SET jExp11='" . $jExp[10] . "', jExp12='" . $jExp[11] . "', jExp13='" . $jExp[12] . "', jExp14='" . $jExp[13] . "', jExp15='" . $jExp[14] . "', jExp16='" . $jExp[15] . "', jExp17='" . $jExp[16] . "', jExp18='" . $jExp[17] . "', jExp19='" . $jExp[18] . "', jExp20='" . $jExp[19] . "', jTier2='" . $jTier[1] . "'  WHERE id='" . $uid . "'";
				$result = mysql_query($query);
			}
			if($jExp[20] == 100 && $jExp[21] == 100 && $jExp[22] == 100 && $jExp[23] == 100 && $jExp[24] == 100 && 
				$jExp[25] == 100 && $jExp[26] == 100 && $jExp[27] == 100 && $jExp[28] == 100 && $jExp[29] == 100 && $jTier[2] < 4)
			{
				if($jTier[2] < 3)
				{
				$jExp[20] = 0;
				$jExp[21] = 0;
				$jExp[22] = 0;
				$jExp[23] = 0;
				$jExp[24] = 0;
				$jExp[25] = 0;
				$jExp[26] = 0;
				$jExp[27] = 0;
				$jExp[28] = 0;
				$jExp[29] = 0;
				}
				$clearTier = 1;
				$jTier[2]++;
				$skills++;
				$query = "UPDATE CW_Users SET skills='" . $skills . "'  WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$query = "UPDATE CW_Missions SET jExp21='" . $jExp[20] . "', jExp22='" . $jExp[21] . "', jExp23='" . $jExp[22] . "', jExp24='" . $jExp[23] . "', jExp25='" . $jExp[24] . "', jExp26='" . $jExp[25] . "', jExp27='" . $jExp[26] . "', jExp28='" . $jExp[27] . "', jExp29='" . $jExp[28] . "', jExp30='" . $jExp[29] . "', jTier3='" . $jTier[2] . "'  WHERE id='" . $uid . "'";
				$result = mysql_query($query);
			}
			if($jExp[30] == 100 && $jExp[31] == 100 && $jExp[32] == 100 && $jExp[33] == 100 && $jExp[34] == 100 && 
				$jExp[35] == 100 && $jExp[36] == 100 && $jExp[37] == 100 && $jExp[38] == 100 && $jExp[39] == 100 && $jTier[3] < 4)
			{
				if($jTier[3] < 3)
				{
				$jExp[30] = 0;
				$jExp[31] = 0;
				$jExp[32] = 0;
				$jExp[33] = 0;
				$jExp[34] = 0;
				$jExp[35] = 0;
				$jExp[36] = 0;
				$jExp[37] = 0;
				$jExp[38] = 0;
				$jExp[39] = 0;
				}
				$clearTier = 1;
				$jTier[3]++;
				$skills++;
				$query = "UPDATE CW_Users SET skills='" . $skills . "'  WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$query = "UPDATE CW_Missions SET jExp31='" . $jExp[30] . "', jExp32='" . $jExp[31] . "', jExp33='" . $jExp[32] . "', jExp34='" . $jExp[33] . "', jExp35='" . $jExp[34] . "', jExp36='" . $jExp[35] . "', jExp37='" . $jExp[36] . "', jExp38='" . $jExp[37] . "', jExp39='" . $jExp[38] . "', jExp40='" . $jExp[39] . "', jTier4='" . $jTier[3] . "'  WHERE id='" . $uid . "'";
				$result = mysql_query($query);
			}
			if($jExp[40] == 100 && $jExp[41] == 100 && $jExp[42] == 100 && $jExp[43] == 100 && $jExp[44] == 100 && 
				$jExp[45] == 100 && $jExp[46] == 100 && $jExp[47] == 100 && $jExp[48] == 100 && $jExp[49] == 100 && $jTier[4] < 4)
			{
				if($jTier[4] < 3)
				{
				$jExp[40] = 0;
				$jExp[41] = 0;
				$jExp[42] = 0;
				$jExp[43] = 0;
				$jExp[44] = 0;
				$jExp[45] = 0;
				$jExp[46] = 0;
				$jExp[47] = 0;
				$jExp[48] = 0;
				$jExp[49] = 0;
				}
				$clearTier = 1;
				$jTier[4]++;
				$skills++;
				$query = "UPDATE CW_Users SET skills='" . $skills . "'  WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$query = "UPDATE CW_Missions SET jExp41='" . $jExp[40] . "', jExp42='" . $jExp[41] . "', jExp43='" . $jExp[42] . "', jExp44='" . $jExp[43] . "', jExp45='" . $jExp[44] . "', jExp46='" . $jExp[45] . "', jExp47='" . $jExp[46] . "', jExp48='" . $jExp[47] . "', jExp49='" . $jExp[48] . "', jExp50='" . $jExp[49] . "', jTier5='" . $jTier[4] . "'  WHERE id='" . $uid . "'";
				$result = mysql_query($query);
			}
			if($jExp[50] == 100 && $jExp[51] == 100 && $jExp[52] == 100 && $jExp[53] == 100 && $jExp[54] == 100 && 
				$jExp[55] == 100 && $jExp[56] == 100 && $jExp[57] == 100 && $jExp[58] == 100 && $jExp[59] == 100 && $jTier[5] < 4)
			{
				if($jTier[5] < 3)
				{
				$jExp[50] = 0;
				$jExp[51] = 0;
				$jExp[52] = 0;
				$jExp[53] = 0;
				$jExp[54] = 0;
				$jExp[55] = 0;
				$jExp[56] = 0;
				$jExp[57] = 0;
				$jExp[58] = 0;
				$jExp[59] = 0;
				}
				$clearTier = 1;
				$jTier[5]++;
				$skills++;
				$query = "UPDATE CW_Users SET skills='" . $skills . "'  WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$query = "UPDATE CW_Missions SET jExp51='" . $jExp[50] . "', jExp52='" . $jExp[51] . "', jExp53='" . $jExp[52] . "', jExp54='" . $jExp[53] . "', jExp55='" . $jExp[54] . "', jExp56='" . $jExp[55] . "', jExp57='" . $jExp[56] . "', jExp58='" . $jExp[57] . "', jExp59='" . $jExp[58] . "', jExp60='" . $jExp[59] . "', jTier6='" . $jTier[5] . "'  WHERE id='" . $uid . "'";
				$result = mysql_query($query);
			}
			if($jExp[60] == 100 && $jExp[61] == 100 && $jExp[62] == 100 && $jExp[63] == 100 && $jExp[64] == 100 && 
				$jExp[65] == 100 && $jExp[66] == 100 && $jExp[67] == 100 && $jExp[68] == 100 && $jExp[69] == 100 && $jTier[6] < 4)
			{
				if($jTier[6] < 3)
				{
				$jExp[60] = 0;
				$jExp[61] = 0;
				$jExp[62] = 0;
				$jExp[63] = 0;
				$jExp[64] = 0;
				$jExp[65] = 0;
				$jExp[66] = 0;
				$jExp[67] = 0;
				$jExp[68] = 0;
				$jExp[69] = 0;
				}
				$clearTier = 1;
				$jTier[6]++;
				$skills++;
				$query = "UPDATE CW_Users SET skills='" . $skills . "'  WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$query = "UPDATE CW_Missions SET jExp61='" . $jExp[60] . "', jExp62='" . $jExp[61] . "', jExp63='" . $jExp[62] . "', jExp64='" . $jExp[63] . "', jExp65='" . $jExp[64] . "', jExp66='" . $jExp[65] . "', jExp67='" . $jExp[66] . "', jExp68='" . $jExp[67] . "', jExp69='" . $jExp[68] . "', jExp70='" . $jExp[69] . "', jTier7='" . $jTier[6] . "'  WHERE id='" . $uid . "'";
				$result = mysql_query($query);
			}
			if($jExp[70] == 100 && $jExp[71] == 100 && $jExp[72] == 100 && $jExp[73] == 100 && $jExp[74] == 100 && 
				$jExp[75] == 100 && $jExp[76] == 100 && $jExp[77] == 100 && $jExp[78] == 100 && $jExp[79] == 100 && $jTier[7] < 4)
			{
				if($jTier[7] < 3)
				{
				$jExp[70] = 0;
				$jExp[71] = 0;
				$jExp[72] = 0;
				$jExp[73] = 0;
				$jExp[74] = 0;
				$jExp[75] = 0;
				$jExp[76] = 0;
				$jExp[77] = 0;
				$jExp[78] = 0;
				$jExp[79] = 0;
				}
				$clearTier = 1;
				$jTier[7]++;
				$skills++;
				$query = "UPDATE CW_Users SET skills='" . $skills . "'  WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$query = "UPDATE CW_Missions SET jExp71='" . $jExp[70] . "', jExp72='" . $jExp[71] . "', jExp73='" . $jExp[72] . "', jExp74='" . $jExp[73] . "', jExp75='" . $jExp[74] . "', jExp76='" . $jExp[75] . "', jExp77='" . $jExp[76] . "', jExp78='" . $jExp[77] . "', jExp79='" . $jExp[78] . "', jExp80='" . $jExp[79] . "', jTier8='" . $jTier[7] . "'  WHERE id='" . $uid . "'";
				$result = mysql_query($query);
			}
			if($jExp[80] == 100 && $jExp[81] == 100 && $jExp[82] == 100 && $jExp[83] == 100 && $jExp[84] == 100 && 
				$jExp[85] == 100 && $jExp[86] == 100 && $jExp[87] == 100 && $jExp[88] == 100 && $jExp[89] == 100 && $jTier[8] < 4)
			{
				if($jTier[8] < 3)
				{
				$jExp[80] = 0;
				$jExp[81] = 0;
				$jExp[82] = 0;
				$jExp[83] = 0;
				$jExp[84] = 0;
				$jExp[85] = 0;
				$jExp[86] = 0;
				$jExp[87] = 0;
				$jExp[88] = 0;
				$jExp[89] = 0;
				}
				$clearTier = 1;
				$jTier[8]++;
				$skills++;
				$query = "UPDATE CW_Users SET skills='" . $skills . "'  WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$query = "UPDATE CW_Missions SET jExp81='" . $jExp[80] . "', jExp82='" . $jExp[81] . "', jExp83='" . $jExp[82] . "', jExp84='" . $jExp[83] . "', jExp85='" . $jExp[84] . "', jExp86='" . $jExp[85] . "', jExp87='" . $jExp[86] . "', jExp88='" . $jExp[87] . "', jExp89='" . $jExp[88] . "', jExp90='" . $jExp[89] . "', jTier9='" . $jTier[8] . "'  WHERE id='" . $uid . "'";
				$result = mysql_query($query);
			}
			if($jExp[90] == 100 && $jExp[91] == 100 && $jExp[92] == 100 && $jExp[93] == 100 && $jExp[94] == 100 && 
				$jExp[95] == 100 && $jExp[96] == 100 && $jExp[97] == 100 && $jExp[98] == 100 && $jExp[99] == 100 && $jTier[9] < 4)
			{
				if($jTier[9] < 3)
				{
				$jExp[90] = 0;
				$jExp[91] = 0;
				$jExp[92] = 0;
				$jExp[93] = 0;
				$jExp[94] = 0;
				$jExp[95] = 0;
				$jExp[96] = 0;
				$jExp[97] = 0;
				$jExp[98] = 0;
				$jExp[99] = 0;
				}
				$clearTier = 1;
				$jTier[9]++;
				$skills++;
				$query = "UPDATE CW_Users SET skills='" . $skills . "'  WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$query = "UPDATE CW_Missions SET jExp91='" . $jExp[90] . "', jExp92='" . $jExp[91] . "', jExp93='" . $jExp[92] . "', jExp94='" . $jExp[93] . "', jExp95='" . $jExp[94] . "', jExp96='" . $jExp[95] . "', jExp97='" . $jExp[96] . "', jExp98='" . $jExp[97] . "', jExp99='" . $jExp[98] . "', jExp100='" . $jExp[99] . "', jTier10='" . $jTier[9] . "'  WHERE id='" . $uid . "'";
				$result = mysql_query($query);
			}
			if($faction == SAPPHIROS)
				$time= time() + (2 * 60) + 30;
			else
				$time= time() + 3 * 60;
			$time= date("Y-m-d H:i:s", $time);
			$c_energy -= $usedEnergy[$job];
			$experience += $expWon[$job];
			$krevels += $krevelsWon[$job];
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
			if($WealthyChosen >= 0 && ($krevels + $bank >= 1000000))
			{
				$WealthyChosen = -1;
				$skills++;
				$query = "UPDATE CW_Achievements SET achievement20='" . $WealthyChosen . "' WHERE id='" . $uid . "'";
				$result = mysql_query($query);
$displayAchievement = 20;
			}
			$query = "UPDATE CW_Users SET ";
			if($clearMission == 1)
				$query .= "missionCleared=missionCleared + 1, ";
			$query .= "time='" . $time . "', missionTotal=missionTotal + 1, gems='" . $gems . "', fac_change='" . $fac_change . "', krevels='" . $krevels . "', skills='" . $skills . "',  c_energy='" . $c_energy . "', c_stamina='" . $c_stamina . "', c_health='" . $c_health . "', level='" .$level . "', experience='" . $experience . "' WHERE id='" . $uid . "'";
			$result = mysql_query($query);
			$query = "UPDATE CW_Missions SET jExp" . ($job + 1) . "='" . $jExp[$job] . "' WHERE id='" . $uid . "'";
			$result = mysql_query($query);
			$query = "SELECT * FROM CW_Users WHERE id='" . $uid . "'";//for real game, add WHERE to narrow it down to fb uid
			$result = mysql_query($query);
			$time= mysql_result($result, 0, 'time');
			$didMission = 1;
			
			
			
		}
		else
		{
			if($jExp[$job] < 100)
			{
				$jExp[$job] += (int)($jExpBase[$job] * (4 - $jTier[(int)($job/10)]));
				if($jExp[$job] >= 100)
				{
					$jExp[$job] = 100;
					$skills += 1;
					$clearMission = 1;
				}
			}
			
			if($jExp[0] == 100 && $jExp[1] == 100 && $jExp[2] == 100 && $jExp[3] == 100 && $jExp[4] == 100 && 
				$jExp[5] == 100 && $jExp[6] == 100 && $jExp[7] == 100 && $jExp[8] == 100 && $jExp[9] == 100 && $jTier[0] < 4)
			{
				if($jTier[0] < 3)
				{
				$jExp[0] = 0;
				$jExp[1] = 0;
				$jExp[2] = 0;
				$jExp[3] = 0;
				$jExp[4] = 0;
				$jExp[5] = 0;
				$jExp[6] = 0;
				$jExp[7] = 0;
				$jExp[8] = 0;
				$jExp[9] = 0;
				}
				$clearTier = 1;
				$jTier[0]++;
				$skills++;
				$query = "UPDATE CW_Users SET skills='" . $skills . "'  WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$query = "UPDATE CW_Missions SET jExp1='" . $jExp[0] . "', jExp2='" . $jExp[1] . "', jExp3='" . $jExp[2] . "', jExp4='" . $jExp[3] . "', jExp5='" . $jExp[4] . "', jExp6='" . $jExp[5] . "', jExp7='" . $jExp[6] . "', jExp8='" . $jExp[7] . "', jExp9='" . $jExp[8] . "', jExp10='" . $jExp[9] . "', jTier1='" . $jTier[0] . "'  WHERE id='" . $uid . "'";
				$result = mysql_query($query);
			}
			if($jExp[10] == 100 && $jExp[11] == 100 && $jExp[12] == 100 && $jExp[13] == 100 && $jExp[14] == 100 && 
				$jExp[15] == 100 && $jExp[16] == 100 && $jExp[17] == 100 && $jExp[18] == 100 && $jExp[19] == 100 && $jTier[1] < 4)
			{
				if($jTier[1] < 3)
				{
				$jExp[10] = 0;
				$jExp[11] = 0;
				$jExp[12] = 0;
				$jExp[13] = 0;
				$jExp[14] = 0;
				$jExp[15] = 0;
				$jExp[16] = 0;
				$jExp[17] = 0;
				$jExp[18] = 0;
				$jExp[19] = 0;
				}
				$clearTier = 1;
				$jTier[1]++;
				$skills++;
				$query = "UPDATE CW_Users SET skills='" . $skills . "'  WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$query = "UPDATE CW_Missions SET jExp11='" . $jExp[10] . "', jExp12='" . $jExp[11] . "', jExp13='" . $jExp[12] . "', jExp14='" . $jExp[13] . "', jExp15='" . $jExp[14] . "', jExp16='" . $jExp[15] . "', jExp17='" . $jExp[16] . "', jExp18='" . $jExp[17] . "', jExp19='" . $jExp[18] . "', jExp20='" . $jExp[19] . "', jTier2='" . $jTier[1] . "'  WHERE id='" . $uid . "'";
				$result = mysql_query($query);
			}
			if($jExp[20] == 100 && $jExp[21] == 100 && $jExp[22] == 100 && $jExp[23] == 100 && $jExp[24] == 100 && 
				$jExp[25] == 100 && $jExp[26] == 100 && $jExp[27] == 100 && $jExp[28] == 100 && $jExp[29] == 100 && $jTier[2] < 4)
			{
				if($jTier[2] < 3)
				{
				$jExp[20] = 0;
				$jExp[21] = 0;
				$jExp[22] = 0;
				$jExp[23] = 0;
				$jExp[24] = 0;
				$jExp[25] = 0;
				$jExp[26] = 0;
				$jExp[27] = 0;
				$jExp[28] = 0;
				$jExp[29] = 0;
				}
				$clearTier = 1;
				$jTier[2]++;
				$skills++;
				$query = "UPDATE CW_Users SET skills='" . $skills . "'  WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$query = "UPDATE CW_Missions SET jExp21='" . $jExp[20] . "', jExp22='" . $jExp[21] . "', jExp23='" . $jExp[22] . "', jExp24='" . $jExp[23] . "', jExp25='" . $jExp[24] . "', jExp26='" . $jExp[25] . "', jExp27='" . $jExp[26] . "', jExp28='" . $jExp[27] . "', jExp29='" . $jExp[28] . "', jExp30='" . $jExp[29] . "', jTier3='" . $jTier[2] . "'  WHERE id='" . $uid . "'";
				$result = mysql_query($query);
			}
			if($jExp[30] == 100 && $jExp[31] == 100 && $jExp[32] == 100 && $jExp[33] == 100 && $jExp[34] == 100 && 
				$jExp[35] == 100 && $jExp[36] == 100 && $jExp[37] == 100 && $jExp[38] == 100 && $jExp[39] == 100 && $jTier[3] < 4)
			{
				if($jTier[3] < 3)
				{
				$jExp[30] = 0;
				$jExp[31] = 0;
				$jExp[32] = 0;
				$jExp[33] = 0;
				$jExp[34] = 0;
				$jExp[35] = 0;
				$jExp[36] = 0;
				$jExp[37] = 0;
				$jExp[38] = 0;
				$jExp[39] = 0;
				}
				$clearTier = 1;
				$jTier[3]++;
				$skills++;
				$query = "UPDATE CW_Users SET skills='" . $skills . "'  WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$query = "UPDATE CW_Missions SET jExp31='" . $jExp[30] . "', jExp32='" . $jExp[31] . "', jExp33='" . $jExp[32] . "', jExp34='" . $jExp[33] . "', jExp35='" . $jExp[34] . "', jExp36='" . $jExp[35] . "', jExp37='" . $jExp[36] . "', jExp38='" . $jExp[37] . "', jExp39='" . $jExp[38] . "', jExp40='" . $jExp[39] . "', jTier4='" . $jTier[3] . "'  WHERE id='" . $uid . "'";
				$result = mysql_query($query);
			}
			if($jExp[40] == 100 && $jExp[41] == 100 && $jExp[42] == 100 && $jExp[43] == 100 && $jExp[44] == 100 && 
				$jExp[45] == 100 && $jExp[46] == 100 && $jExp[47] == 100 && $jExp[48] == 100 && $jExp[49] == 100 && $jTier[4] < 4)
			{
				if($jTier[4] < 3)
				{
				$jExp[40] = 0;
				$jExp[41] = 0;
				$jExp[42] = 0;
				$jExp[43] = 0;
				$jExp[44] = 0;
				$jExp[45] = 0;
				$jExp[46] = 0;
				$jExp[47] = 0;
				$jExp[48] = 0;
				$jExp[49] = 0;
				}
				$clearTier = 1;
				$jTier[4]++;
				$skills++;
				$query = "UPDATE CW_Users SET skills='" . $skills . "'  WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$query = "UPDATE CW_Missions SET jExp41='" . $jExp[40] . "', jExp42='" . $jExp[41] . "', jExp43='" . $jExp[42] . "', jExp44='" . $jExp[43] . "', jExp45='" . $jExp[44] . "', jExp46='" . $jExp[45] . "', jExp47='" . $jExp[46] . "', jExp48='" . $jExp[47] . "', jExp49='" . $jExp[48] . "', jExp50='" . $jExp[49] . "', jTier5='" . $jTier[4] . "'  WHERE id='" . $uid . "'";
				$result = mysql_query($query);
			}
			if($jExp[50] == 100 && $jExp[51] == 100 && $jExp[52] == 100 && $jExp[53] == 100 && $jExp[54] == 100 && 
				$jExp[55] == 100 && $jExp[56] == 100 && $jExp[57] == 100 && $jExp[58] == 100 && $jExp[59] == 100 && $jTier[5] < 4)
			{
				if($jTier[5] < 3)
				{
				$jExp[50] = 0;
				$jExp[51] = 0;
				$jExp[52] = 0;
				$jExp[53] = 0;
				$jExp[54] = 0;
				$jExp[55] = 0;
				$jExp[56] = 0;
				$jExp[57] = 0;
				$jExp[58] = 0;
				$jExp[59] = 0;
				}
				$clearTier = 1;
				$jTier[5]++;
				$skills++;
				$query = "UPDATE CW_Users SET skills='" . $skills . "'  WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$query = "UPDATE CW_Missions SET jExp51='" . $jExp[50] . "', jExp52='" . $jExp[51] . "', jExp53='" . $jExp[52] . "', jExp54='" . $jExp[53] . "', jExp55='" . $jExp[54] . "', jExp56='" . $jExp[55] . "', jExp57='" . $jExp[56] . "', jExp58='" . $jExp[57] . "', jExp59='" . $jExp[58] . "', jExp60='" . $jExp[59] . "', jTier6='" . $jTier[5] . "'  WHERE id='" . $uid . "'";
				$result = mysql_query($query);
			}
			if($jExp[60] == 100 && $jExp[61] == 100 && $jExp[62] == 100 && $jExp[63] == 100 && $jExp[64] == 100 && 
				$jExp[65] == 100 && $jExp[66] == 100 && $jExp[67] == 100 && $jExp[68] == 100 && $jExp[69] == 100 && $jTier[6] < 4)
			{
				if($jTier[6] < 3)
				{
				$jExp[60] = 0;
				$jExp[61] = 0;
				$jExp[62] = 0;
				$jExp[63] = 0;
				$jExp[64] = 0;
				$jExp[65] = 0;
				$jExp[66] = 0;
				$jExp[67] = 0;
				$jExp[68] = 0;
				$jExp[69] = 0;
				}
				$clearTier = 1;
				$jTier[6]++;
				$skills++;
				$query = "UPDATE CW_Users SET skills='" . $skills . "'  WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$query = "UPDATE CW_Missions SET jExp61='" . $jExp[60] . "', jExp62='" . $jExp[61] . "', jExp63='" . $jExp[62] . "', jExp64='" . $jExp[63] . "', jExp65='" . $jExp[64] . "', jExp66='" . $jExp[65] . "', jExp67='" . $jExp[66] . "', jExp68='" . $jExp[67] . "', jExp69='" . $jExp[68] . "', jExp70='" . $jExp[69] . "', jTier7='" . $jTier[6] . "'  WHERE id='" . $uid . "'";
				$result = mysql_query($query);
			}
			if($jExp[70] == 100 && $jExp[71] == 100 && $jExp[72] == 100 && $jExp[73] == 100 && $jExp[74] == 100 && 
				$jExp[75] == 100 && $jExp[76] == 100 && $jExp[77] == 100 && $jExp[78] == 100 && $jExp[79] == 100 && $jTier[7] < 4)
			{
				if($jTier[7] < 3)
				{
				$jExp[70] = 0;
				$jExp[71] = 0;
				$jExp[72] = 0;
				$jExp[73] = 0;
				$jExp[74] = 0;
				$jExp[75] = 0;
				$jExp[76] = 0;
				$jExp[77] = 0;
				$jExp[78] = 0;
				$jExp[79] = 0;
				}
				$clearTier = 1;
				$jTier[7]++;
				$skills++;
				$query = "UPDATE CW_Users SET skills='" . $skills . "'  WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$query = "UPDATE CW_Missions SET jExp71='" . $jExp[70] . "', jExp72='" . $jExp[71] . "', jExp73='" . $jExp[72] . "', jExp74='" . $jExp[73] . "', jExp75='" . $jExp[74] . "', jExp76='" . $jExp[75] . "', jExp77='" . $jExp[76] . "', jExp78='" . $jExp[77] . "', jExp79='" . $jExp[78] . "', jExp80='" . $jExp[79] . "', jTier8='" . $jTier[7] . "'  WHERE id='" . $uid . "'";
				$result = mysql_query($query);
			}
			if($jExp[80] == 100 && $jExp[81] == 100 && $jExp[82] == 100 && $jExp[83] == 100 && $jExp[84] == 100 && 
				$jExp[85] == 100 && $jExp[86] == 100 && $jExp[87] == 100 && $jExp[88] == 100 && $jExp[89] == 100 && $jTier[8] < 4)
			{
				if($jTier[8] < 3)
				{
				$jExp[80] = 0;
				$jExp[81] = 0;
				$jExp[82] = 0;
				$jExp[83] = 0;
				$jExp[84] = 0;
				$jExp[85] = 0;
				$jExp[86] = 0;
				$jExp[87] = 0;
				$jExp[88] = 0;
				$jExp[89] = 0;
				}
				$clearTier = 1;
				$jTier[8]++;
				$skills++;
				$query = "UPDATE CW_Users SET skills='" . $skills . "'  WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$query = "UPDATE CW_Missions SET jExp81='" . $jExp[80] . "', jExp82='" . $jExp[81] . "', jExp83='" . $jExp[82] . "', jExp84='" . $jExp[83] . "', jExp85='" . $jExp[84] . "', jExp86='" . $jExp[85] . "', jExp87='" . $jExp[86] . "', jExp88='" . $jExp[87] . "', jExp89='" . $jExp[88] . "', jExp90='" . $jExp[89] . "', jTier9='" . $jTier[8] . "'  WHERE id='" . $uid . "'";
				$result = mysql_query($query);
			}
			if($jExp[90] == 100 && $jExp[91] == 100 && $jExp[92] == 100 && $jExp[93] == 100 && $jExp[94] == 100 && 
				$jExp[95] == 100 && $jExp[96] == 100 && $jExp[97] == 100 && $jExp[98] == 100 && $jExp[99] == 100 && $jTier[9] < 4)
			{
				if($jTier[9] < 3)
				{
				$jExp[90] = 0;
				$jExp[91] = 0;
				$jExp[92] = 0;
				$jExp[93] = 0;
				$jExp[94] = 0;
				$jExp[95] = 0;
				$jExp[96] = 0;
				$jExp[97] = 0;
				$jExp[98] = 0;
				$jExp[99] = 0;
				}
				$clearTier = 1;
				$jTier[9]++;
				$skills++;
				$query = "UPDATE CW_Users SET skills='" . $skills . "'  WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$query = "UPDATE CW_Missions SET jExp91='" . $jExp[90] . "', jExp92='" . $jExp[91] . "', jExp93='" . $jExp[92] . "', jExp94='" . $jExp[93] . "', jExp95='" . $jExp[94] . "', jExp96='" . $jExp[95] . "', jExp97='" . $jExp[96] . "', jExp98='" . $jExp[97] . "', jExp99='" . $jExp[98] . "', jExp100='" . $jExp[99] . "', jTier10='" . $jTier[9] . "'  WHERE id='" . $uid . "'";
				$result = mysql_query($query);
			}
			
			$c_energy -= $usedEnergy[$job];
			$experience += $expWon[$job];
			$krevels += $krevelsWon[$job];
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
				{
					$areaChange = 1;
				}
			}
                        if($WealthyChosen >= 0 && ($krevels + $bank >= 1000000))
			{
				$WealthyChosen = -1;
				$skills++;
				$query = "UPDATE CW_Achievements SET achievement20='" . $WealthyChosen . "' WHERE id='" . $uid . "'";
				$result = mysql_query($query);
$displayAchievement = 20;
$achName = "One Krevel...  Two Krevels...";
			}
			$query = "UPDATE CW_Users SET ";
			if($clearMission == 1)
				$query .= "missionCleared=missionCleared + 1, ";
			$query .= "missionTotal=missionTotal + 1, gems='" . $gems . "', fac_change='" . $fac_change . "', krevels='" . $krevels . "', skills='" . $skills . "',  c_energy='" . $c_energy . "', c_stamina='" . $c_stamina . "', c_health='" . $c_health . "', level='" .$level . "', experience='" . $experience . "' WHERE id='" . $uid . "'";
			$result = mysql_query($query);
			$query = "UPDATE CW_Missions SET jExp" . ($job + 1) . "='" . $jExp[$job] . "' WHERE id='" . $uid . "'";
			$result = mysql_query($query);
			$didMission = 1;
		}
		if($GoldRush >= 0 && ($jTier[0] == 4 || $jTier[1] == 4 || $jTier[2] == 4 || $jTier[3] == 4 || $jTier[4] == 4 || $jTier[5] == 4 || $jTier[5] == 4 || $jTier[5] == 4 || $jTier[5] == 4 || $jTier[5] == 4))
		{
			$GoldRush = -1;
			$skills++;
			$query = "UPDATE CW_Users SET skills='" . $skills . "'  WHERE id='" . $uid . "'";
			$result = mysql_query($query);
			$query = "UPDATE CW_Achievements SET achievement17='" . $GoldRush . "' WHERE id='" . $uid . "'";
			$result = mysql_query($query);
$displayAchievement = 17;
$achName = "Gold Rush";
		}
		if($GoldMedalist >= 0 && ($jTier[0] == 4 && $jTier[1] == 4 && $jTier[2] == 4 && $jTier[3] == 4 && $jTier[4] == 4))
		{
			$GoldMedalist = -1;
			$skills++;
			$query = "UPDATE CW_Users SET skills='" . $skills . "'  WHERE id='" . $uid . "'";
			$result = mysql_query($query);
			$query = "UPDATE CW_Achievements SET achievement18='" . $GoldMedalist . "' WHERE id='" . $uid . "'";
			$result = mysql_query($query);
$displayAchievement = 18;
$achName = "Gold Medalist";
		}
		if($BeenThereDoneThat >= 0 && ($jTier[5] == 4 && $jTier[6] == 4 && $jTier[7] == 4 && $jTier[8] == 4 && $jTier[9] == 4))
		{
			$BeenThereDoneThat = -1;
			$skills++;
			$query = "UPDATE CW_Users SET skills='" . $skills . "'  WHERE id='" . $uid . "'";
			$result = mysql_query($query);
			$query = "UPDATE CW_Achievements SET achievement40='" . $BeenThereDoneThat . "' WHERE id='" . $uid . "'";
			$result = mysql_query($query);
$displayAchievement = 40;
$achName = "Been There, Done That";
		}
		if($job == 4)
		{
			$rand = rand(0, 100);
			$odds = 40;
			if($sapphirosGold2)
				$odds *= 1.1;
			if($rand < $odds)
			{
				$query = "SELECT power37 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$rows = mysql_fetch_row($result);
				if($rows[0] == 0)
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
				$query = "UPDATE CW_Inventory SET power37=power37 + 1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$drop = 1;
				$dropMsg = "<div style='position:absolute; left:96px; top:0px;'><img title='Obscure Vision' src='assets/power37.jpg'/></div>";
			}
		}
		else if($job == 16)
		{
			$rand = rand(0, 100);
			$odds = 40;
			if($sapphirosGold2)
				$odds *= 1.1;
			if($rand < $odds)
			{
				$query = "SELECT power50 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$rows = mysql_fetch_row($result);
				if($rows[0] == 0)
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
				$query = "UPDATE CW_Inventory SET power50=power50 + 1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$drop = 1;
				$dropMsg = "<div style='position:absolute; left:96px; top:0px;'><img  title='Snow' src='assets/power50.jpg'/></div>";
			}
		}
		else if($job == 18)
		{
			$rand = rand(0, 100);
			$odds = 36;
			if($sapphirosGold2)
				$odds *= 1.1;
			if($rand < $odds)
			{
				$query = "SELECT power43 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$rows = mysql_fetch_row($result);
				if($rows[0] == 0)
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
				$query = "UPDATE CW_Inventory SET power43=power43 + 1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$drop = 1;
				$dropMsg = "You learned \"Light Arrow\".";
				$dropMsg = "<div style='position:absolute; left:96px; top:0px;'><img   title='Light Arrow' src='assets/power43.jpg'/></div>";
			}
		}
		else if($job == 20)
		{
			$rand = rand(0, 100);
			$odds = 30;
			if($sapphirosGold2)
				$odds *= 1.1;
			if($rand < $odds)
			{
				$query = "SELECT power24 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$rows = mysql_fetch_row($result);
				if($rows[0] == 0)
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
				$query = "UPDATE CW_Inventory SET power24=power24 + 1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$drop = 1;
				$dropMsg = "<div style='position:absolute; left:96px; top:0px;'><img  title='Vanish Weapon' src='assets/power24.jpg'/></div>";
			}
		}
		else if($job == 27)
		{
			$rand = rand(0, 100);
			$odds = 30;
			if($sapphirosGold2)
				$odds *= 1.1;
			if($rand < $odds)
			{
				$query = "SELECT power42 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$rows = mysql_fetch_row($result);
				if($rows[0] == 0)
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
				$query = "UPDATE CW_Inventory SET power42=power42 + 1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$drop = 1;
				$dropMsg = "<div style='position:absolute; left:96px; top:0px;'><img  title='Net Arrow' src='assets/power42.jpg'/></div>";
			}
		}
		else if($job == 30)
		{
			$rand = rand(0, 100);
			$odds = 25;
			if($sapphirosGold2)
				$odds *= 1.1;
			if($rand < $odds)
			{
				$query = "SELECT power8 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$rows = mysql_fetch_row($result);
				if($rows[0] == 0)
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
				$query = "UPDATE CW_Inventory SET power8=power8 + 1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$drop = 1;
				$dropMsg = "<div style='position:absolute; left:96px; top:0px;'><img  title='Seeker Arrow' src='assets/power8.jpg'/></div>";
			}
		}
		else if($job == 33)
		{
			$rand = rand(0, 100);
			$odds = 25;
			if($sapphirosGold2)
				$odds *= 1.1;
			if($rand < $odds)
			{
				$query = "SELECT power46 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$rows = mysql_fetch_row($result);
				if($rows[0] == 0)
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
				$query = "UPDATE CW_Inventory SET power46=power46 + 1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$drop = 1;
				$dropMsg = "<div style='position:absolute; left:96px; top:0px;'><img  title='Mist Travel' src='assets/power46.jpg'/></div>";
			}
		}
		else if($job == 49)
		{
			$rand = rand(0, 100);
			$odds = 20;
			if($sapphirosGold2)
				$odds *= 1.1;
			if($rand < $odds)
			{
				$query = "SELECT power3 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$rows = mysql_fetch_row($result);
				if($rows[0] == 0)
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
				$query = "UPDATE CW_Inventory SET power3=power3 + 1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$drop = 1;
				$dropMsg = "<div style='position:absolute; left:96px; top:0px;'><img   title='Energy Draining Net' src='assets/power3.jpg'/></div>";
			}
		}
		else if($job == 50)
		{
			$rand = rand(0, 100);
			$odds = 30;
			if($sapphirosGold2)
				$odds *= 1.1;
			if($rand < $odds)
			{
				$query = "SELECT power122 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$rows = mysql_fetch_row($result);
				if($rows[0] == 0)
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
				$query = "UPDATE CW_Inventory SET power122=power122 + 1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$drop = 1;
				$dropMsg = "<div style='position:absolute; left:96px; top:0px;'><img   title='Exploding Arrow' src='assets/power122.jpg'/></div>";
			}
		}
		else if($job == 52)
		{
			$rand = rand(0, 100);
			$odds = 30;
			if($sapphirosGold2)
				$odds *= 1.1;
			if($rand < $odds)
			{
				$query = "SELECT power125 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$rows = mysql_fetch_row($result);
				if($rows[0] == 0)
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
				$query = "UPDATE CW_Inventory SET power125=power125 + 1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$drop = 1;
				$dropMsg = "<div style='position:absolute; left:96px; top:0px;'><img   title='Immunity to Poison' src='assets/power125.jpg'/></div>";
			}
		}
		else if($job == 64)
		{
			$rand = rand(0, 100);
			$odds = 27;
			if($sapphirosGold2)
				$odds *= 1.1;
			if($rand < $odds)
			{
				$query = "SELECT power134 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$rows = mysql_fetch_row($result);
				if($rows[0] == 0)
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
				$query = "UPDATE CW_Inventory SET power134=power134 + 1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$drop = 1;
				$dropMsg = "<div style='position:absolute; left:96px; top:0px;'><img   title='Confusion' src='assets/power134.jpg'/></div>";
			}
		}
		else if($job == 65)
		{
			$rand = rand(0, 100);
			$odds = 27;
			if($sapphirosGold2)
				$odds *= 1.1;
			if($rand < $odds)
			{
				$query = "SELECT power129 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$rows = mysql_fetch_row($result);
				if($rows[0] == 0)
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
				$query = "UPDATE CW_Inventory SET power129=power129 + 1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$drop = 1;
				$dropMsg = "<div style='position:absolute; left:96px; top:0px;'><img   title='Antibodies' src='assets/power129.jpg'/></div>";
			}
		}
		else if($job == 67)
		{
			$rand = rand(0, 100);
			$odds = 27;
			if($sapphirosGold2)
				$odds *= 1.1;
			if($rand < $odds)
			{
				$query = "SELECT power132 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$rows = mysql_fetch_row($result);
				if($rows[0] == 0)
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
				$query = "UPDATE CW_Inventory SET power132=power132 + 1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$drop = 1;
				$dropMsg = "<div style='position:absolute; left:96px; top:0px;'><img   title='Summon Misted Person' src='assets/power132.jpg'/></div>";
			}
		}
		else if($job == 74)
		{
			$rand = rand(0, 100);
			$odds = 20;
			if($sapphirosGold2)
				$odds *= 1.1;
			if($rand < $odds)
			{
				$query = "SELECT power137 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$rows = mysql_fetch_row($result);
				if($rows[0] == 0)
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
				$query = "UPDATE CW_Inventory SET power137=power137 + 1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$drop = 1;
				$dropMsg = "<div style='position:absolute; left:96px; top:0px;'><img   title='Read Memory Location' src='assets/power137.jpg'/></div>";
			}
		}
		else if($job == 75)
		{
			$rand = rand(0, 100);
			$odds = 20;
			if($sapphirosGold2)
				$odds *= 1.1;
			if($rand < $odds)
			{
				$query = "SELECT power150 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$rows = mysql_fetch_row($result);
				if($rows[0] == 0)
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
				$query = "UPDATE CW_Inventory SET power150=power150 + 1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$drop = 1;
				$dropMsg = "<div style='position:absolute; left:96px; top:0px;'><img   title='Locate Person' src='assets/power150.jpg'/></div>";
			}
		}
		else if($job == 76)
		{
			$rand = rand(0, 100);
			$odds = 20;
			if($sapphirosGold2)
				$odds *= 1.1;
			if($rand < $odds)
			{
				$query = "SELECT power141 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$rows = mysql_fetch_row($result);
				if($rows[0] == 0)
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
				$query = "UPDATE CW_Inventory SET power141=power141 + 1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$drop = 1;
				$dropMsg = "<div style='position:absolute; left:96px; top:0px;'><img   title='Inspire Confidence' src='assets/power141.jpg'/></div>";
			}
		}
		else if($job == 78)
		{
			$rand = rand(0, 100);
			$odds = 20;
			if($sapphirosGold2)
				$odds *= 1.1;
			if($rand < $odds)
			{
				$query = "SELECT power138 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$rows = mysql_fetch_row($result);
				if($rows[0] == 0)
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
				$query = "UPDATE CW_Inventory SET power138=power138 + 1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$drop = 1;
				$dropMsg = "<div style='position:absolute; left:96px; top:0px;'><img   title='Increased Speed' src='assets/power138.jpg'/></div>";
			}
		}
		else if($job == 80)
		{
			$rand = rand(0, 100);
			$odds = 35;
			if($sapphirosGold2)
				$odds *= 1.1;
			if($rand < $odds)
			{
				$query = "SELECT power95 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$rows = mysql_fetch_row($result);
				if($rows[0] == 0)
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
				$query = "UPDATE CW_Inventory SET power95=power95 + 1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$drop = 1;
				$dropMsg = "<div style='position:absolute; left:96px; top:0px;'><img   title='Sense Life Force' src='assets/power95.jpg'/></div>";
			}
		}
		else if($job == 83)
		{
			$rand = rand(0, 100);
			$odds = 23;
			if($sapphirosGold2)
				$odds *= 1.1;
			if($rand < $odds)
			{
				$query = "SELECT power152 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$rows = mysql_fetch_row($result);
				if($rows[0] == 0)
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
				$query = "UPDATE CW_Inventory SET power152=power152 + 1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$drop = 1;
				$dropMsg = "<div style='position:absolute; left:96px; top:0px;'><img   title='Magic Sight' src='assets/power152.jpg'/></div>";
			}
		}
		else if($job == 87)
		{
			$rand = rand(0, 100);
			$odds = 40;
			if($sapphirosGold2)
				$odds *= 1.1;
			if($rand < $odds)
			{
				$rand = rand(146, 147);
				$query = "SELECT power" . $rand . " FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$rows = mysql_fetch_row($result);
				if($rows[0] == 0)
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
				$query = "UPDATE CW_Inventory SET power" . $rand . "=power" . $rand . " + 1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$drop = 1;
				$dropMsg = "<div style='position:absolute; left:96px; top:0px;'><img   title='";
				if($rand == 146)
				$dropMsg .= "Inspire Fear";
				else
				$dropMsg .= "Counter";
				$dropMsg .= "' src='assets/power" . $rand . ".jpg'/></div>";
			}
		}
		else if($job == 88)
		{
			$rand = rand(0, 100);
			$odds = 20;
			if($sapphirosGold2)
				$odds *= 1.1;
			if($rand < $odds)
			{
				$query = "SELECT power151 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$rows = mysql_fetch_row($result);
				if($rows[0] == 0)
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
				$query = "UPDATE CW_Inventory SET power151=power151 + 1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$drop = 1;
				$dropMsg = "<div style='position:absolute; left:96px; top:0px;'><img   title='Premonition' src='assets/power151.jpg'/></div>";
			}
		}
		else if($job == 92)
		{
			$rand = rand(0, 100);
			$odds = 20;
			if($sapphirosGold2)
				$odds *= 1.1;
			if($rand < $odds)
			{
				$query = "SELECT power153 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$rows = mysql_fetch_row($result);
				if($rows[0] == 0)
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
				$query = "UPDATE CW_Inventory SET power153=power153 + 1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$drop = 1;
				$dropMsg = "<div style='position:absolute; left:96px; top:0px;'><img   title='Read Memory Object' src='assets/power153.jpg'/></div>";
			}
		}
		else if($job == 95)
		{
			$rand = rand(0, 100);
			$odds = 10;
			if($sapphirosGold2)
				$odds *= 1.1;
			if($rand < $odds)
			{
				$query = "SELECT power155 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$rows = mysql_fetch_row($result);
				if($rows[0] == 0)
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
				$query = "UPDATE CW_Inventory SET power155=power155 + 1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$drop = 1;
				$dropMsg = "<div style='position:absolute; left:96px; top:0px;'><img   title='Spirit Punch' src='assets/power155.jpg'/></div>";
			}
		}
		if(!$drop)
		{
			$rand = rand(0, 100);
			$odds = 3;
			if($sapphirosGold2)
				$odds = 5;
			if($rand < $odds)
			{
//make this rand 7 when adding in the collection
				$rand = rand(0, 5);
				$drop = 1;
				switch($rand)
				{
					case 0:
						$dropMsg = "<div style='position:absolute; left:96px; top:0px;'><img  title='Temple Key' src='assets/power67.jpg'/></div>";
						$dropPower = 67;
						break;
					case 1:
						$dropMsg = "<div style='position:absolute; left:96px; top:0px;'><img  title='Katana' src='assets/power68.jpg'/></div>";
						$dropPower = 68;
						break;
					case 2:
						$dropMsg = "<div style='position:absolute; left:96px; top:0px;'><img  title='Leather Vest' src='assets/power69.jpg'/></div>";
						$dropPower = 69;
						break;
					case 3:
						$dropMsg = "<div style='position:absolute; left:96px; top:0px;'><img  title='Bolt Thrower' src='assets/power70.jpg'/></div>";
						$dropPower = 70;
						break;
					case 4:
						$dropMsg = "<div style='position:absolute; left:96px; top:0px;'><img  title='Quarterstaff' src='assets/power71.jpg'/></div>";
						$dropPower = 71;
						break;
					case 5:
						$dropMsg = "<div style='position:absolute; left:96px; top:0px;'><img  title='Gauntlet' src='assets/power72.jpg'/></div>";
						$dropPower = 72;
						break;
				}
				$query = "UPDATE CW_Inventory SET power" . $dropPower . "=power" . $dropPower . " + 1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
			}

			else if($rand < $odds + 3 && $region == 1)
			{
			$rand = rand(0,7);
			$drop = 1;
			switch($rand)
			{
case 0:
						$dropMsg = "<div style='position:absolute; left:96px; top:0px;'><img  title='Ferry Token' src='assets/power106.jpg'/></div>";
						$dropPower = 106;
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
							$achName = "Taiyou Tourist";		
						}
						$query = "UPDATE CW_Achievements SET achievement35='" . $TaiyouTourist . "' WHERE id='" . $uid . "'";
						$result = mysql_query($query);

					}
				}
				else if($TaiyouTourist == 0)
				{
					$TaiyouTourist = $numCollection;
					$query = "UPDATE CW_Achievements SET achievement35='" . $TaiyouTourist . "' WHERE id='" . $uid . "'";
					$result = mysql_query($query);
				}
						break;
case 1:
						$dropMsg = "<div style='position:absolute; left:96px; top:0px;'><img  title='Medic Kit' src='assets/power107.jpg'/></div>";
						$dropPower = 107;
$numCollection = 2;
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
							$achName = "Taiyou Tourist";		
						}
						$query = "UPDATE CW_Achievements SET achievement35='" . $TaiyouTourist . "' WHERE id='" . $uid . "'";
						$result = mysql_query($query);

					}
				}
				else if($TaiyouTourist == 0)
				{
					$TaiyouTourist = $numCollection;
					$query = "UPDATE CW_Achievements SET achievement35='" . $TaiyouTourist . "' WHERE id='" . $uid . "'";
					$result = mysql_query($query);
				}
						break;
case 2:
						$dropMsg = "<div style='position:absolute; left:96px; top:0px;'><img  title='Note' src='assets/power108.jpg'/></div>";
						$dropPower = 108;
$numCollection = 3;
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
							$achName = "Taiyou Tourist";		
						}
						$query = "UPDATE CW_Achievements SET achievement35='" . $TaiyouTourist . "' WHERE id='" . $uid . "'";
						$result = mysql_query($query);

					}
				}
				else if($TaiyouTourist == 0)
				{
					$TaiyouTourist = $numCollection;
					$query = "UPDATE CW_Achievements SET achievement35='" . $TaiyouTourist . "' WHERE id='" . $uid . "'";
					$result = mysql_query($query);
				}
						break;
case 3:
						$dropMsg = "<div style='position:absolute; left:96px; top:0px;'><img  title='Note' src='assets/power109.jpg'/></div>";
						$dropPower = 109;
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
							$achName = "Taiyou Tourist";		
						}
						$query = "UPDATE CW_Achievements SET achievement35='" . $TaiyouTourist . "' WHERE id='" . $uid . "'";
						$result = mysql_query($query);

					}
				}
				else if($TaiyouTourist == 0)
				{
					$TaiyouTourist = $numCollection;
					$query = "UPDATE CW_Achievements SET achievement35='" . $TaiyouTourist . "' WHERE id='" . $uid . "'";
					$result = mysql_query($query);
				}
						break;
case 4:
						$dropMsg = "<div style='position:absolute; left:96px; top:0px;'><img  title='Taiyoun Robes' src='assets/power110.jpg'/></div>";
						$dropPower = 110;
$numCollection = 5;
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
							$achName = "Taiyou Tourist";		
						}
						$query = "UPDATE CW_Achievements SET achievement35='" . $TaiyouTourist . "' WHERE id='" . $uid . "'";
						$result = mysql_query($query);

					}
				}
				else if($TaiyouTourist == 0)
				{
					$TaiyouTourist = $numCollection;
					$query = "UPDATE CW_Achievements SET achievement35='" . $TaiyouTourist . "' WHERE id='" . $uid . "'";
					$result = mysql_query($query);
				}
						break;
case 5:
						$dropMsg = "<div style='position:absolute; left:96px; top:0px;'><img  title='Lion Head Badge' src='assets/power111.jpg'/></div>";
						$dropPower = 111;
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
							$achName = "Taiyou Tourist";		
						}
						$query = "UPDATE CW_Achievements SET achievement35='" . $TaiyouTourist . "' WHERE id='" . $uid . "'";
						$result = mysql_query($query);

					}
				}
				else if($TaiyouTourist == 0)
				{
					$TaiyouTourist = $numCollection;
					$query = "UPDATE CW_Achievements SET achievement35='" . $TaiyouTourist . "' WHERE id='" . $uid . "'";
					$result = mysql_query($query);
				}
						break;
case 6:
						$dropMsg = "<div style='position:absolute; left:96px; top:0px;'><img  title='Crown' src='assets/power112.jpg'/></div>";
						$dropPower = 112;
$numCollection = 7;
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
							$achName = "Taiyou Tourist";		
						}
						$query = "UPDATE CW_Achievements SET achievement35='" . $TaiyouTourist . "' WHERE id='" . $uid . "'";
						$result = mysql_query($query);

					}
				}
				else if($TaiyouTourist == 0)
				{
					$TaiyouTourist = $numCollection;
					$query = "UPDATE CW_Achievements SET achievement35='" . $TaiyouTourist . "' WHERE id='" . $uid . "'";
					$result = mysql_query($query);
				}
						break;
case 7:
						$dropMsg = "<div style='position:absolute; left:96px; top:0px;'><img  title='Turtle Shell Scroll' src='assets/power113.jpg'/></div>";
						$dropPower = 113;
$numCollection = 8;
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
							$achName = "Taiyou Tourist";		
						}
						$query = "UPDATE CW_Achievements SET achievement35='" . $TaiyouTourist . "' WHERE id='" . $uid . "'";
						$result = mysql_query($query);

					}
				}
				else if($TaiyouTourist == 0)
				{
					$TaiyouTourist = $numCollection;
					$query = "UPDATE CW_Achievements SET achievement35='" . $TaiyouTourist . "' WHERE id='" . $uid . "'";
					$result = mysql_query($query);
				}
						break;
				}//end switch
				$query = "UPDATE CW_Inventory SET power" . $dropPower . "=power" . $dropPower . " + 1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
			}
			
		}
	}
	
}

switch($jTier[$missiongroup])
{
case 1:
	$sTier = "Bronze";
	break;
case 2:
	$sTier = "Silver";
	$oldSTier = "Bronze";
	break;
case 3:
	$sTier = "Gold";
	$oldSTier = "Silver";
	break;
case 4:
	$oldSTier = "Gold";
	$sTier = "Gold";
	break;
default:
	$sTier = "Unobtainium";
		break;
}
/*
$levelUp = 1;

if(!$drop)
{
	$dropMsg = "<div style='position:absolute; left:96px; top:0px;'><img  title='Gauntlet' src='assets/power72.jpg'/></div>";
	$drop = 1;
}
$clearTier = 1;
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
<title>Missions</title>
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
    FB.init({appId: '205670806110909', status: true, cookie: true,
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

<div class="explain" style="height:131px; background-image:url('assets/Box_MissionsExplanation.png'); color:black;">
<div style='width:100%; position:absolute; top:8px; left:0px;'>
<div style="color:orange;">MISSIONS</div><br/>
Follow the story and use energy to complete missions and earn experience and krevels. For every mission you complete you will unlock more of the story and gain skill points to spend on the profile page. Complete an entire area and the gem gods will lend you more of their power.
_END;
if($sTier == "Bronze")
	$sTierStars = "<img src='assets/Star_Yellow.png'/>";
else if($sTier == "Silver")
	$sTierStars = "<img src='assets/Star_Yellow.png'/><img src='assets/Star_Yellow.png'/>";
else if($sTier == "Gold")
	$sTierStars = "<img src='assets/Star_Yellow.png'/><img src='assets/Star_Yellow.png'/><img src='assets/Star_Yellow.png'/>";
echo <<<_END
<br/><div style='width:1px; height:6px;'></div>$sTierStars
</div>
</div>
_END;
if($region == 0)
{
echo "<div class='powerButtons0' style='top:275px;'><div style='position:absolute; left:275px; top:5px;'><a class='button' href='javascript:void(0)'  onClick='ChangeArea(100);'>SAPPHIRE</a></div>";
if($level < 81)
echo "<div style='position:absolute; left:425px;top:5px;'>JADE<br/>Levels 81+</div>";
else
echo "<div style='position:absolute; left:425px;top:5px;'><a href='javascript:void(0)' onClick='ChangeArea(200);'>JADE</a></div>";
echo "</div>";
if($level < 11)
{
echo "<table class='group" . $missiongroup ."' style='top:320px;'><tr>";

echo "<td style='width:125px; color:yellow;'><a class='button' href='javascript:void(0)' onClick='ChangeArea(0);'>Earth<br/>Levels 1-10</a></td>";
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
echo "<table class='group" . $missiongroup ."' style='top:320px;'><tr>";
if($missiongroup == 0)
echo "<td style='width:125px; color:yellow;'><a class='button' href='javascript:void(0)'  onClick='ChangeArea(0);'>Earth<br/>Levels 1-10</a></td>";
else
echo "<td style='width:125px;'><a class='" . $style . "' href='javascript:void(0)' onClick='ChangeArea(0);'>Earth<br/>Levels 1-10</a></td>";
if($missiongroup == 1)
echo "<td style='width:125px; color:yellow;'><a class='button' href='javascript:void(0)' onClick='ChangeArea(1);'>Sand Lake<br/>Levels 11-25</a></td>";
else
echo "<td style='width:125px;'><a class='" . $style . "' href='javascript:void(0)' onClick='ChangeArea(1);'>Sand Lake<br/>Levels 11-25</a></td>";
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
echo "<table class='group" . $missiongroup ."' style='top:320px;'><tr>";
if($missiongroup == 0)
echo "<td style='width:125px; color:yellow;'><a class='button' href='javascript:void(0)' onClick='ChangeArea(0);'>Earth<br/>Levels 1-10</a></td>";
else
echo "<td style='width:125px;'><a class='" . $style . "' href='javascript:void(0)' onClick='ChangeArea(0);'>Earth<br/>Levels 1-10</a></td>";
if($missiongroup == 1)
echo "<td style='width:125px; color:yellow;'><a class='button' href='javascript:void(0)' onClick='ChangeArea(1);'>Sand Lake<br/>Levels 11-25</a></td>";
else
echo "<td style='width:125px;'><a class='" . $style . "' href='javascript:void(0)' onClick='ChangeArea(1);'>Sand Lake<br/>Levels 11-25</a></td>";
if($missiongroup == 2)
echo "<td style='width:125px; color:yellow;'><a class='button' href='javascript:void(0)' onClick='ChangeArea(2);'>Outpost<br/>Levels 26-40</a></td>";
else
echo "<td style='width:125px;'><a class='" . $style . "' href='javascript:void(0)' onClick='ChangeArea(2);'>Outpost<br/>Levels 26-40</a></td>";
echo "<td style='width:125px;'>Valley of Lilyth<br/>Levels 41-60</td>";
echo "<td style='width:125px;'>Temple of Sapphire<br/>Levels 61+</td>";
echo "<td style='width:125px;'>BOSS<br/>Levels 61+</td>";
echo <<<_END
</tr></table>
_END;
}
else if($level < 61)
{
echo "<table class='group" . $missiongroup ."' style='top:320px;'><tr>";
if($missiongroup == 0)
echo "<td style='width:125px; color:yellow;'><a class='button' href='javascript:void(0)' onClick='ChangeArea(0);'>Earth<br/>Levels 1-10</a></td>";
else
echo "<td style='width:125px;'><a class='" . $style . "' href='javascript:void(0)' onClick='ChangeArea(0);'>Earth<br/>Levels 1-10</a></td>";
if($missiongroup == 1)
echo "<td style='width:125px; color:yellow;'><a class='button' href='javascript:void(0)' onClick='ChangeArea(1);'>Sand Lake<br/>Levels 11-25</a></td>";
else
echo "<td style='width:125px;'><a class='" . $style . "' href='javascript:void(0)' onClick='ChangeArea(1);'>Sand Lake<br/>Levels 11-25</a></td>";
if($missiongroup == 2)
echo "<td style='width:125px; color:yellow;'><a class='button' href='javascript:void(0)' onClick='ChangeArea(2);'>Outpost<br/>Levels 26-40</a></td>";
else
echo "<td style='width:125px;'><a class='" . $style . "' href='javascript:void(0)' onClick='ChangeArea(2);'>Outpost<br/>Levels 26-40</a></td>";
if($missiongroup == 3)
echo "<td style='width:125px; color:yellow;'><a class='button' href='javascript:void(0)' onClick='ChangeArea(3);'>Valley of Lilyth<br/>Levels 41-60</a></td>";
else
echo "<td style='width:125px;'><a class='" . $style . "' href='javascript:void(0)' onClick='ChangeArea(3);'>Valley of Lilyth<br/>Levels 41-60</a></td>";
echo "<td style='width:125px;'>Temple of Sapphire<br/>Levels 61+</td>";
echo "<td style='width:125px;'>BOSS<br/>Levels 61+</td>";
echo <<<_END
</tr></table>
_END;
}
else
{
echo "<table class='group" . $missiongroup ."' style='top:320px;'><tr>";
if($missiongroup == 0)
echo "<td style='width:125px; color:yellow;'><a class='button' href='javascript:void(0)' onClick='ChangeArea(0);'>Earth<br/>Levels 1-10</a></td>";
else
echo "<td style='width:125px;'><a class='" . $style . "' href='javascript:void(0)' onClick='ChangeArea(0);'>Earth<br/>Levels 1-10</a></td>";
if($missiongroup == 1)
echo "<td style='width:125px; color:yellow;'><a class='button' href='javascript:void(0)' onClick='ChangeArea(1);'>Sand Lake<br/>Levels 11-25</a></td>";
else
echo "<td style='width:125px;'><a class='" . $style . "' href='javascript:void(0)' onClick='ChangeArea(1);'>Sand Lake<br/>Levels 11-25</a></td>";
if($missiongroup == 2)
echo "<td style='width:125px; color:yellow;'><a class='button' href='javascript:void(0)' onClick='ChangeArea(2);'>Outpost<br/>Levels 26-40</a></td>";
else
echo "<td style='width:125px;'><a class='" . $style . "' href='javascript:void(0)' onClick='ChangeArea(2);'>Outpost<br/>Levels 26-40</a></td>";
if($missiongroup == 3)
echo "<td style='width:125px; color:yellow;'><a class='button' href='javascript:void(0)' onClick='ChangeArea(3);'>Valley of Lilyth<br/>Levels 41-60</a></td>";
else
echo "<td style='width:125px;'><a class='" . $style . "' href='javascript:void(0)' onClick='ChangeArea(3);'>Valley of Lilyth<br/>Levels 41-60</a></td>";
if($missiongroup == 4)
echo "<td style='width:125px; color:yellow;'><a class='button' href='javascript:void(0)' onClick='ChangeArea(4);'>Temple of Sapphire<br/>Levels 61+</a></td>";
else
echo "<td style='width:125px;'><a class='" . $style . "' href='javascript:void(0)' onClick='ChangeArea(4);'>Temple of Sapphire<br/>Levels 61+</a></td>";
echo "<td style='width:125px;'><a class='" . $style . "' href='boss.php?token=$access_token'>BOSS<br/>Levels 61+</a></td>";
echo <<<_END
</tr></table>
_END;
}
}//end region 1
else
{
echo "<div class='powerButtons1' style='top:275px;'><div style='position:absolute; left:275px; top:5px;'><a href='javascript:void(0)' onClick='ChangeArea(100);'>SAPPHIRE</a></div>";

echo "<div style='position:absolute; left:425px;top:5px;'><a class='button' href='javascript:void(0)' onClick='ChangeArea(200);'>JADE</a></div>";
echo "</div>";
if($level < 101)
{
echo "<table class='group" . $missiongroup % 5 ."' style='top:320px;'><tr>";

echo "<td style='width:125px; color:yellow;'><a class='button' href='javascript:void(0)' onClick='ChangeArea(0);'>Long Roads<br/>Levels 81-100</a></td>";
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
echo "<table class='group" . $missiongroup % 5 ."' style='top:320px;'><tr>";
if($missiongroup == 5)
echo "<td style='width:125px; color:yellow;'><a class='button' href='javascript:void(0)' onClick='ChangeArea(0);'>Long Roads<br/>Levels 81-100</a></td>";
else
echo "<td style='width:125px;'><a class='" . $style . "' href='javascript:void(0)' onClick='ChangeArea(0);'>Long Roads<br/>Levels 81-100</a></td>";
if($missiongroup == 6)
echo "<td style='width:125px; color:yellow;'><a class='button' href='javascript:void(0)' onClick='ChangeArea(1);'>Sanctioned Outpost<br/>Levels 101-125</a></td>";
else
echo "<td style='width:125px;'><a class='" . $style . "' href='javascript:void(0)' onClick='ChangeArea(1);'>Sanctioned Outpost<br/>Levels 101-125</a></td>";
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
echo "<table class='group" . $missiongroup % 5 ."' style='top:320px;'><tr>";
if($missiongroup == 5)
echo "<td style='width:125px; color:yellow;'><a class='button' href='javascript:void(0)' onClick='ChangeArea(0);'>Long Roads<br/>Levels 81-100</a></td>";
else
echo "<td style='width:125px;'><a class='" . $style . "' href='javascript:void(0)' onClick='ChangeArea(0);'>Long Roads<br/>Levels 81-100</a></td>";
if($missiongroup == 6)
echo "<td style='width:125px; color:yellow;'><a class='button' href='javascript:void(0)' onClick='ChangeArea(1);'>Sanctioned Outpost<br/>Levels 101-125</a></td>";
else
echo "<td style='width:125px;'><a class='" . $style . "' href='javascript:void(0)' onClick='ChangeArea(1);'>Sanctioned Outpost<br/>Levels 101-125</a></td>";
if($missiongroup == 7)
echo "<td style='width:125px; color:yellow;'><a class='button' href='javascript:void(0)' onClick='ChangeArea(2);'>Raman Province<br/>Levels 126-150</a></td>";
else
echo "<td style='width:125px;'><a class='" . $style . "' href='javascript:void(0)' onClick='ChangeArea(2);'>Raman Province<br/>Levels 126-150</a></td>";
echo "<td style='width:125px;'>City of Taiyou<br/>Levels 151-180</td>";
echo "<td style='width:125px;'>Temple of Jade<br/>Levels 181+</td>";
echo "<td style='width:125px;'>BOSS<br/>Levels 181+</td>";
echo <<<_END
</tr></table>
_END;
}
else if($level < 181)
{
echo "<table class='group" . $missiongroup % 5 ."' style='top:320px;'><tr>";
if($missiongroup == 5)
echo "<td style='width:125px; color:yellow;'><a class='button' href='javascript:void(0)' onClick='ChangeArea(0);'>Long Roads<br/>Levels 81-100</a></td>";
else
echo "<td style='width:125px;'><a class='" . $style . "' href='javascript:void(0)' onClick='ChangeArea(0);'>Long Roads<br/>Levels 81-100</a></td>";
if($missiongroup == 6)
echo "<td style='width:125px; color:yellow;'><a class='button' href='javascript:void(0)' onClick='ChangeArea(1);'>Sanctioned Outpost<br/>Levels 101-125</a></td>";
else
echo "<td style='width:125px;'><a class='" . $style . "' href='javascript:void(0)' onClick='ChangeArea(1);'>Sanctioned Outpost<br/>Levels 101-125</a></td>";
if($missiongroup == 7)
echo "<td style='width:125px; color:yellow;'><a class='button' href='javascript:void(0)' onClick='ChangeArea(2);'>ORaman Province<br/>Levels 126-150</a></td>";
else
echo "<td style='width:125px;'><a class='" . $style . "' href='javascript:void(0)' onClick='ChangeArea(2);'>Raman Province<br/>Levels 126-150</a></td>";
if($missiongroup == 8)
echo "<td style='width:125px; color:yellow;'><a class='button' href='javascript:void(0)' onClick='ChangeArea(3);'>City of Taiyou<br/>Levels 151-180</a></td>";
else
echo "<td style='width:125px;'><a class='" . $style . "' href='javascript:void(0)' onClick='ChangeArea(3);'>City of Taiyou<br/>Levels 151-180</a></td>";
echo "<td style='width:125px;'>Temple of Jade<br/>Levels 181+</td>";
echo "<td style='width:125px;'>BOSS<br/>Levels 181+</td>";
echo <<<_END
</tr></table>
_END;
}
else
{
echo "<table class='group" . $missiongroup % 5 ."' style='top:320px;'><tr>";
if($missiongroup == 5)
echo "<td style='width:125px; color:yellow;'><a class='button' href='javascript:void(0)' onClick='ChangeArea(0);'>Long Roads<br/>Levels 81-100</a></td>";
else
echo "<td style='width:125px;'><a class='" . $style . "' href='javascript:void(0)' onClick='ChangeArea(0);'>Long Roads<br/>Levels 81-100</a></td>";
if($missiongroup == 6)
echo "<td style='width:125px; color:yellow;'><a class='button' href='javascript:void(0)' onClick='ChangeArea(1);'>Sanctioned Outpost<br/>Levels 101-125</a></td>";
else
echo "<td style='width:125px;'><a class='" . $style . "' href='javascript:void(0)' onClick='ChangeArea(1);'>Sanctioned Outpost<br/>Levels 101-125</a></td>";
if($missiongroup == 7)
echo "<td style='width:125px; color:yellow;'><a class='button' href='javascript:void(0)' onClick='ChangeArea(2);'>Raman Province<br/>Levels 126-150</a></td>";
else
echo "<td style='width:125px;'><a class='" . $style . "' href='javascript:void(0)' onClick='ChangeArea(2);'>Raman Province<br/>Levels 126-150</a></td>";
if($missiongroup == 8)
echo "<td style='width:125px; color:yellow;'><a class='button' href='javascript:void(0)' onClick='ChangeArea(3);'>City of Taiyou<br/>Levels 151-180</a></td>";
else
echo "<td style='width:125px;'><a class='" . $style . "' href='javascript:void(0)' onClick='ChangeArea(3);'>City of Taiyou<br/>Levels 151-180</a></td>";
if($missiongroup == 9)
echo "<td style='width:125px; color:yellow;'><a class='button' href='javascript:void(0)' onClick='ChangeArea(4);'>Temple of Jade<br/>Levels 181+</a></td>";
else
echo "<td style='width:125px;'><a class='" . $style . "' href='javascript:void(0)' onClick='ChangeArea(4);'>Temple of Jade<br/>Levels 181+</a></td>";
echo "<td style='width:125px;'><a class='" . $style . "' href='boss.php?token=$access_token'>BOSS<br/>Levels 181+</a></td>";
echo <<<_END
</tr></table>
_END;
}
}//end region 2


switch($missiongroup % 5)
{
case 0:
	$groupText = "";
	break;
case 1:
	$groupText = "";
	break;
case 2:
	$groupText = "";
	break;
case 3:
	$groupText = "";
	break;
case 4:
	$groupText = "";
	break;
default:
	$groupText = " under the cover of daylight.";
	break;
}

if($didMission != 0)
{
	echo "<div class='results' style='color:black;'>";
	if($didMission > 0)
	{
		echo "<div style='font-size:0.9375em; position:absolute; top:8px; color:orange; width:100%; text-align:center;'>" . $missionTitle[$job] . $groupText . " complete.</div>";
		echo "<div style='position:absolute; top:35px; width:100px; left:200px;'>Earned:<br/>" . $expWon[$job] . " exp<br/>" . $krevelsWon[$job] . " krevels";
		
		echo "</div>";
		if($drop == 1)
		{
			echo "<div style='position:absolute; top:39px; left:300px;'>";
			echo $dropMsg;
			echo "</div>";
		}
		echo "<div style='position:absolute; top:35px; left:20px; width:150px; font-size:0.75em;'>";
		if($clearTier != 1)
		{
			if($sTier == "Bronze")
			{
				if($jExp[$job] == 100)
				{
					echo "<img src='assets/Star_Yellow.png'/>";
				}
				else
				{
					echo "<img src='assets/Star_White.png'/>";
				}
			}
			else if($sTier == "Silver")
			{
				if($jExp[$job] == 100)
				{
					echo "<img src='assets/Star_Yellow.png'/><img src='assets/Star_Yellow.png'/>";
				}
				else
				{
					echo "<img src='assets/Star_Yellow.png'/><img src='assets/Star_White.png'/>";
				}
			}
			else
			{
				if($jExp[$job] == 100)
				{
					echo "<img src='assets/Star_Yellow.png'/><img src='assets/Star_Yellow.png'/><img src='assets/Star_Yellow.png'/>";
				}
				else
				{
					echo "<img src='assets/Star_Yellow.png'/><img src='assets/Star_Yellow.png'/><img src='assets/Star_White.png'/>";
				}
			}
			echo "<br/>" . $sTier . " Mastery: " . $jExp[$job] . "%<br/>";
			echo "<div style='position:absolute; top:35px; width:111px;'><img style='position:absolute;' src='assets/ExpBarBG.jpg'/><img style='position:absolute;' src='assets/ExpBarFill.jpg' width='$jExp[$job]%' height='18'/></div>";
			
		}
		else
		{
			if($oldSTier == "Bronze")
			{
				echo "<img src='assets/Star_Yellow.png'/>";
			}
			else if($oldSTier == "Silver")
			{
				echo "<img src='assets/Star_Yellow.png'/><img src='assets/Star_Yellow.png'/>";
			}
			else
			{
				echo "<img src='assets/Star_Yellow.png'/><img src='assets/Star_Yellow.png'/><img src='assets/Star_Yellow.png'/>";
			}
			echo "<br/>" . $oldSTier . " Mastery: 100%";
			echo "<div style='position:absolute; top:35px; width:111px;'><img style='position:absolute;' src='assets/ExpBarBG.jpg'/><img style='position:absolute;' src='assets/ExpBarFill.jpg'/></div>";
		}
		echo "</div>";
		
		echo "<div style='position:absolute; top:55px; left:650px;'><form method='post' action='missions.php'>$usedEnergy[$job] Energy<br/>";
		echo "<input type='hidden' name='posted' value='yes'/><input type='hidden' name='job' value='" . $job . "'/><input type='hidden' name='token' value='$access_token'/><input type='image' src='assets/BTN_Again.png' value='AGAIN'/></form></div>";
		
		if($clearTier == 1)
			echo "<div style='position:absolute; top:107px; left:8px; width:542px;'>You have been awarded 1 skill point for mastering $missionTitle[$job] and 1 skill point for mastering $oldSTier rank!</div>";
		else if($clearMission == 1)
			echo "<div style='position:absolute; top:107px; left:8px; width:542px;'>You have been awarded 1 skill point for mastering $missionTitle[$job]!</div>";
	}
	else if($didMission < 0)
	{
		echo "<div style='position:absolute; top:10px; left:15px;'>" . $errorMsg . "</div>";
	}
	if($levelUp == 1)
	{
echo <<<_END
<div  style='position:absolute; top:35px; left:550px;'><img src='assets/Icon_LevelUp.png' width='64px' height='64px'/></div>

_END;

                //$feed_url = "http://www.facebook.com/dialog/feed?app_id=205670806110909&redirect_uri=" . urlencode('http://www.firstagestudios.com/Ldd49cEsTcwf/missions.php') . "&picture=http://www.firstagestudios.com/Ldd49cEsTcwf/assets/power36.jpg&name=Level%20Up&caption=Crimson%20Winter&description=I%20just%20reached%20level%20" . $level . "%20in%20Crimson%20Winter.%20Try%20it%20out%20today!";

                //echo("<script> top.location.href='" . $feed_url . "'</script>");
	}
	echo "</div>";
}

if($didMission != 0)
echo "<div class='missions' style='top:528px;'>";
else
echo "<div class='missions' style='top:365px;'>";


for($i = 0 + (10 * $missiongroup); $i < 10 + (10 * $missiongroup); $i++)
{
$missionNumber = $i;
echo "<div class='missionbox' style='top:" . (283 * ($i % 10)) . "px; color:black;'>";
echo <<<_END

<form method="post" action="missions.php">
_END;
echo "<div style='position:absolute; left:9px; top:5px;'>" . GetMissionTitle($missionNumber) . $groupText . "</div>";
if($i == $job && $Mount && $didMission != 0)
	$krevelsWon[$i] /= 2;
echo "<div style='position:absolute; left:472px; top:5px;'>Krevels Earned: " . GetMissionKrevels($i) . "</div>";
if($i == $job && $ExtraBelts && $didMission != 0)
	$expWon[$i] /= 2;
echo "<div style='position:absolute; left:618px;  top: 5px; width:120px; text-align:right;'>Experience: " . GetMissionExp($i) . "</div><br/><br/>";
if($jTier[$missiongroup] > 1 || $jExp[$i] == 100)
{
echo "<img style='position:absolute; top:112px; left:15px;' src='assets/Star_Yellow.png'/>";
echo "<div style='position:absolute; top:112px; left:35px; width:695px;'>" . GetBronzeText($i) . "</div>";
}
else
echo "<img style='position:absolute; top:112px; left:15px;' src='assets/Star_White.png'/>";
if($jTier[$missiongroup] > 2 || ($jTier[$missiongroup] == 2 && $jExp[$i] == 100))
{
echo "<img style='position:absolute; top:147px; left:15px;' src='assets/Star_Yellow.png'/>";
echo "<div style='position:absolute; top:147px; left:35px; width:695px;'>" . GetSilverText($i) . "</div>";
}
else if($jTier[$missiongroup] == 2)
echo "<img style='position:absolute; top:147px; left:15px;' src='assets/Star_White.png'/>";
echo <<<_END
<input type="hidden" name="posted" value="yes"/>
<input type="hidden" name="job" value="$i"/>
<input type="hidden" name="token" value="$access_token"/>
<div style="position:absolute; top:36px; left:10px; width:720px; height:80px;"> 
_END;
echo GetMissionText($missionNumber);
echo <<<_END
 $groupText</div>
<img src='assets/ExpBarBG.jpg' style='position:absolute; left:12px; top:242px;'/>
<div style="position:absolute; left:12px; top:242px; width:111px;"><img src='assets/ExpBarFill.jpg' width='$jExp[$i]%' height='18px'/></div>
<div style="position:absolute; top:245px; left:52px; color:white;">$jExp[$i]%</div>
<div style="position:absolute; top:204px; left:12px;">
_END;
if($missionNumber == 4)
{
echo " Chance for: <br/>Obscure Vision.";
}
else if($missionNumber == 16)
{
echo " Chance for: <br/>Snow.";
}
else if($missionNumber == 18)
{
echo " Chance for: <br/>Light Arrow.";
}
else if($missionNumber == 20)
{
echo " Chance for: <br/>Vanish Weapon.";
}
else if($missionNumber == 27)
{
echo " Chance for: <br/>Net Arrow.";
}
else if($missionNumber == 30)
{
echo " Chance for: <br/>Seeker Arrow.";
}
else if($missionNumber == 33)
{
echo " Chance for: <br/>Mist Travel.";
}
else if($missionNumber == 49)
{
echo " Chance for: <br/>Energy Draining Net.";
}
echo <<<_END
</div>

_END;

if($missionNumber == 5)
{
if($slowPerson < 1)
echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px; vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><a class='faction' href='store.php?token=$access_token'><img  title='Slow Person' src='assets/power15.jpg'/><br/>X1</a></div></div>";
else
echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><img  title='Slow Person' src='assets/power15.jpg'/><br/>X1</div></div>";
}
else if($missionNumber == 6)
{
if($mistOut < 1)
echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><a class='faction' href='store.php?token=$access_token'><img  title='Mist Out' src='assets/power21.jpg'/><br/>X1</a></div></div>";
else
echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'> Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><img  title='Mist Out' src='assets/power21.jpg'/><br/>X1</div></div>";
}
else if($missionNumber == 17)
{
if($createWater < 1)
echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><a class='faction' href='store.php?token=$access_token'><img  title='Create Water' src='assets/power54.jpg'/><br/>X1</a></div></div>";
else
echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><img  title='Create Water' src='assets/power54.jpg'/><br/>X1</div></div>";
}
else if($missionNumber == 18)
{
if($arrow < 1)
echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><a class='faction' href='store.php?token=$access_token'><img  title='Arrow' src='assets/power1.jpg'/><br/>1X</a></div></div>";
else
echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><img  title='Arrow' src='assets/power1.jpg'/>X1</div></div>";
}
else if($missionNumber == 19)
{
if($farSight < 1)
echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><a class='faction' href='store.php?token=$access_token'><img  title='Far Sight' src='assets/power47.jpg'/><br/>X1</a></div></div>";
else
echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><img  title='Far Sight' src='assets/power47.jpg'/><br/>X1</div></div>";
}
else if($missionNumber == 23)
echo "<div style='position:absolute; height:64px; width:128px; text-align:left; top:188px; left:127px;vertical-align:text-top;'>Requires:  <div style='position:absolute; top:0px; left:75px; text-align:center;'><img  title='Obscure Vision' src='assets/power37.jpg'/><br/>X5</div></div>";
else if($missionNumber == 26)
{
if($gustOfWind < 10)
echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><a class='faction' href='store.php?token=$access_token'><img  title='Gust of Wind' src='assets/power27.jpg'/><br/>X10</a></div></div>";
else
echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><img  title='Gust of Wind' src='assets/power27.jpg'/><br/>X10</div></div>";
}
else if($missionNumber == 27)
{
if($ropeArrow < 10)
echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'> Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><a class='faction' href='store.php?token=$access_token'><img  title='Rope Arrow' src='assets/power41.jpg'/><br/>X10</a></div></div>";
else
echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><img  title='Rope Arrow' src='assets/power41.jpg'/><br/>X10</div></div>";
}
else if($missionNumber == 28)
echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><img  title='Light Arrow' src='assets/power43.jpg'/><br/>X5</div></div>";
else if($missionNumber == 31)
{
if($heal < 10)
echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>  Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><a class='faction' href='store.php?token=$access_token'><img  title='Heal' src='assets/power23.jpg'/><br/>X10</a></div></div>";
else
echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><img  title='Heal' src='assets/power23.jpg'/><br/>X10</div></div>";
}
else if($missionNumber == 32)
{
if($nearSight < 10)
echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'> Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><a class='faction' href='store.php?token=$access_token'><img  title='Near Sight' src='assets/power49.jpg'/><br/>X10</a></div></div>";
else
echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><img  title='Near Sight' src='assets/power49.jpg'/><br/>X10</div></div>";
}
else if($missionNumber == 33)
{
if($createWater < 20)
echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><a class='faction' href='store.php?token=$access_token'><img  title='Create Water' src='assets/power54.jpg'/><br/>X20</a></div></div>";
else
echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><img  title='Create Water' src='assets/power54.jpg'/><br/>X20</div></div>";
}
else if($missionNumber == 34)
{
if($fireball < 10)
echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'> Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'> <a class='faction' href='store.php?token=$access_token'><img  title='Fireball' src='assets/power12.jpg'/><br/>X10</a></div> <div style='position:absolute; top:0px; left:147px; text-align:center;'><img  title='Net Arrow' src='assets/power42.jpg'/><br/>X10</div></div>";
else
echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><img  title='Fireball' src='assets/power12.jpg'/><br/>X10</div><div style='position:absolute; top:0px; left:147px; text-align:center;'><img  title='Net Arrow' src='assets/power42.jpg'/><br/>X10</div></div>";
}
else if($missionNumber == 36)
{
if($penetrationArrow < 15)
echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'> Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><a class='faction' href='store.php?token=$access_token'><img  title='Penetration Arrow' src='assets/power2.jpg'/><br/>X15</a></div></div>";
else
echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><img  title='Penetration Arrow' src='assets/power2.jpg'/><br/>X15</div></div>";
}
else if($missionNumber == 37)
{
if($intimidate < 15)
echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'> Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><a class='faction' href='store.php?token=$access_token'><img  title='Intimidate' src='assets/power33.jpg'/><br/>X15</a></div></div>";
else
echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><img  title='Intimidate' src='assets/power33.jpg'/><br/>X15</div></div>";
}
else if($missionNumber == 39)
{
echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><img  title='Snow' src='assets/power50.jpg'/><br/>X10</div></div>";
}
else if($missionNumber == 42)
{
if($resistHeat < 5)
echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'> Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><a class='faction' href='store.php?token=$access_token'><img  title='Resist Heat' src='assets/power39.jpg'/><br/>X5</a></div></div>";
else
echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><img  title='Resist Heat' src='assets/power39.jpg'/><br/>X5</div></div>";
}
else if($missionNumber == 43)
{
if($resistCold < 5)
echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'> Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><a class='faction' href='store.php?token=$access_token'><img  title='Resist Cold' src='assets/power40.jpg'/><br/>X5</a></div></div>";
else
echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><img  title='Resist Cold' src='assets/power40.jpg'/><br/>X5</div></div>";
}
else if($missionNumber == 44)
{
if($messageArrow < 20)
echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'> Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><a class='faction' href='store.php?token=$access_token'><img  title='Message Arrow' src='assets/power44.jpg'/><br/>X20</a></div></div>";
else
echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><img  title='Message Arrow' src='assets/power44.jpg'/><br/>X20</div></div>";
}
else if($missionNumber == 45)
{
if($readMemoryPerson < 8)
echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'> Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><a class='faction' href='store.php?token=$access_token'><img  title='Read Memory Person' src='assets/power60.jpg'/><br/>X8</a></div></div>";
else
echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><img  title='Read Memory Person' src='assets/power60.jpg'/><br/>X8</div></div>";
}
else if($missionNumber == 49)
{
if($largeShield < 20)
echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'> Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><a class='faction' href='store.php?token=$access_token'><img  title='Mist Travel' src='assets/power46.jpg'/><br/>X10</a></div> <div style='position:absolute; top:0px; left:147px; text-align:center;'><img  title='Large Shield' src='assets/power30.jpg'/><br/>X20</div></div>";
else
echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'> <img  title='Mist Travel' src='assets/power46.jpg'/><br/>X10</div><div style='position:absolute; top:0px; left:147px; text-align:center;'><img  title='Large Shield' src='assets/power30.jpg'/><br/>X20</div></div>";
}
if($didMission != 0 && $job == $i)
{
echo "<div style='position:absolute; top: 188px; left:346px;width:160px; height:84px;'>";
if($didMission > 0)
{
if($Mount)
	$krevelsWon[$job] *= 2;
if($ExtraBelts)
	$expWon[$job] *= 2;
echo <<<_END
<div style='width:94px;'>
You earned:<br/>
$expWon[$job] exp<br/>
$krevelsWon[$job] krevels
</div>
_END;
if($drop == 1)
	echo $dropMsg;
echo <<<_END
<div style='position:absolute; bottom:0px;'>
MISSION COMPLETE
</div>
_END;
}
else
{
echo "<div style='position:absolute; top:10px; left:15px;'>" . $errorMsg . "</div>";
}
echo "</div>";
}
if($job == $i && $levelUp && $didMission != 0)
echo  "<div style='position:absolute; top:190px; left:520px;'><img src='assets/Icon_LevelUp.png' width='64px' height='64px'/></div>";
echo <<<_END

<div style="position:absolute; left:595px; top:188px; width:200px; text-align:center;"> 
_END;
if($i == $job && $didMission != 0)
	echo "$usedEnergy[$i] Energy<br/><input type='image' value='GO' src='assets/BTN_Again.png'/>";
else
	echo "$usedEnergy[$i] Energy<br/><input type='image' value='GO' src='assets/BTN_Go.png'/>";
echo <<<_END
</div>

_END;
if($i == 17 && !($i == $job && $levelUp == 1 && $didMission != 0))
echo "<div style='position:absolute; top:190px; left:520px;'>MUST BE <br/>CHOSEN OF <br/>SAPPHIROS</div>";
else if ($i == 20 && !($i == $job && $levelUp == 1 && $didMission != 0))
echo "<div style='position:absolute; top:190px; left:520px;'>MUST BE <br/>CHOSEN OF <br/>MACHALITE</div>";
else if($i == 36 && !($i == $job && $levelUp == 1 && $didMission != 0))
echo "<div style='position:absolute; top:190px; left:520px;'>MUST BE <br/>CHOSEN OF <br/>DAMOS</div>";
else if($i == 37 && !($i == $job && $levelUp == 1 && $didMission != 0))
echo "<div style='position:absolute; top:190px; left:520px;'>MUST BE <br/>CHOSEN OF <br/>JEDEITE</div>";
else if($i == 42 && !($i == $job && $levelUp == 1 && $didMission != 0))
echo "<div style='position:absolute; top:190px; left:520px;'>MUST BE <br/>CHOSEN OF <br/>SAPPHIROS</div>";
else if($i == 45 && !($i == $job && $levelUp == 1 && $didMission != 0))
echo "<div style='position:absolute; top:190px; left:520px;'>MUST BE <br/>CHOSEN OF <br/>RUBIA</div>";
echo <<<_END

</form>
</div>
<div style='border:0px; padding:0px; margin:0px; width:0px; height:8px; position:relative;'></div>
_END;

}

echo <<<_END
<br/>
<br/>
<div style='position:relative; top: 2830px; left:520px; font-size:0.75em;'>
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
function ChangeArea(area)
{
//alert("test");
//AJAX for changing areas
	if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  
		  xmlhttp=new XMLHttpRequest();
		  
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		  xmlhttp.onreadystatechange=function()
		  {
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
		    {
		   //alert(xmlhttp.responseText); 
		  if(xmlhttp.responseText == "1111")
		  {
		  	window.location.reload(true);
		    //document.getElementById("txtHint").innerHTML=xmlhttp.responseText;
		    }
		  }
		  }
		 
		
			
			
		
			
				xmlhttp.open("GET","missionrequest.php?&uid=$uid&q="+area+"&t=move",true);
				xmlhttp.send();
                                //alert("Sorry, this alert is making sure each request counts for your achievements.  Doesn't work without it for some reason; I'll be looking into it tomorrow.");
	
}
function DoMission(missionNo)
{
	if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  
		  xmlhttp=new XMLHttpRequest();
		  
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		  xmlhttp.onreadystatechange=function()
		  {
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
		    {
		    
		  if(xmlhttp.responseText == "1111")
		  {
		  	window.location.reload(true);
		    //document.getElementById("txtHint").innerHTML=xmlhttp.responseText;
		    }
		  }
		  }
		 
		
			
			
		
			
				xmlhttp.open("GET","missionrequest.php?token=$access_token&code=$code&q="+myRequests,true);
				xmlhttp.send();
                                //alert("Sorry, this alert is making sure each request counts for your achievements.  Doesn't work without it for some reason; I'll be looking into it tomorrow.");
			
		
}
</script>
</body>
</html>
_END;
mysql_close($db_server);

?>
