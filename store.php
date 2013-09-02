<?php
require_once 'src/facebook.php';
require_once 'db_vars.php';
require_once 'functions.php';
require_once 'cw_vars.php';

$app_id = APP_ID;
    $app_secret = APP_SECRET;
    $my_url = "http://apps.facebook.com/crimsonwinter/store.php";
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
$c_stamina = $rows[12];
$m_stamina = $rows[13];
$c_health = $rows[14];
$m_health = $rows[15];
$hTime = $rows[16];
$sTime = $rows[17];
$faction = $rows[19];
$powerTotal = $rows[28];
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

$query = "SELECT achievement15, achievement16 FROM CW_Achievements WHERE id='" . $uid . "'";
$result = mysql_query($query);
$row = mysql_fetch_row($result);
$StormOfArrows = $row[0];
$Repertoire = $row[1];

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

$query = "SELECT * FROM CW_Inventory WHERE id='" . $uid . "'";//for real game, add WHERE to narrow it down to fb uid
$result = mysql_query($query);

if(!$result) die("Database access failed: " . mysql_error());
$rows = mysql_fetch_row($result);

for($i = 1; $i < mysql_num_fields($result); $i++)
{
	$powers[] = $rows[$i];
}
if($region == 0)
$query = "SELECT name, attack, defence, cost, type, id, earned, faction FROM CW_Items WHERE earned='5' AND id<100 ORDER BY type, cost";//for real game, add WHERE to narrow it down to fb uid
else if($region == 1)//new region = new powers
$query = "SELECT name, attack, defence, cost, type, id, earned, faction FROM CW_Items WHERE earned='5' AND id>100 ORDER BY type, cost";
$result = mysql_query($query);

if(!$result) die("Database access failed: " . mysql_error());


$rows = mysql_num_rows($result);

for($i = 0; $i < $rows; $i++)
{
	$row = mysql_fetch_row($result);
	$powerName[] = $row[0];
	$powerAttack[] = $row[1];
	$powerDefence[] = $row[2];
	//if($row[7] == JEDEITE)
	//	$row[3] *= 0.8;
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

$hasResult = 0;
$error = 0;
$errorMsg = '';
$quantity = 0;
if($_POST[posted] == "yes")
{
	$hasResult = 1;
	$_POST[posted] = "no";
	$action = $_POST[action];
	$CanAfford = 0;
	$bought = "";
	$newPower = 0;
	$quantity = $_POST[quantity];
	$action = SanitizeString($action);
	//check to see if the chosen action can be purchased
	if($powerEarned[$action] == "5")
	{
	
		if($krevels >= ($powerCost[$action] * $quantity))
		{
			if($StormOfArrows >= 0 && $powerName[$action] == "Arrow")
			{
				$StormOfArrows += $quantity;
				if($StormOfArrows >= 100)
				{
					$StormOfArrows = -1;
					$skills++;
					$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $uid . "'";
					$result = mysql_query($query);
$displayAchievement = 15;
$achName = "Storm of Arrows";
				}
				$query = "UPDATE CW_Achievements SET achievement15='" . $StormOfArrows . "' WHERE id='" . $uid . "'";
				$result = mysql_query($query);
			}
			$CanAfford = 1;
			
			$krevels -= ($powerCost[$action] * $quantity);
			$boughtID = $powerID[$action];
			$pID = $powerID[$action] - 1;
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
			$powers[$pID] += $quantity;
			$newPower = $powers[$pID];
			$boughtName = $powerName[$action];
			$pID++;
			$bought = "power" . $pID;
		}
	}
	if($CanAfford == 1)
	{
		$query = "UPDATE CW_Users SET krevels='" . $krevels . "' WHERE id='" . $uid . "'";
		$result = mysql_query($query);
		
	
		$query = "UPDATE CW_Inventory SET " . $bought . "='" . $newPower . "' WHERE id='" . $uid . "'";
		$result = mysql_query($query);
		
	}
	else
	{
		$error = 1;
		$errorMsg = "You need more krevels.";
	}
	
}

$next_exp = GetExpToNextLevel($level);
$this_exp = GetExpToNextLevel($level - 1);
$expToNext = $next_exp - $experience;
$nextLevel = $next_exp - $this_exp;
$expWidth = (int)(($nextLevel - $expToNext) / $nextLevel * 100);
$expToThis = $nextLevel - $expToNext;
$sKrevels = number_format($krevels);

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
<title>Store</title>
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
echo "<td class='menu'><a class='" . $style . "' href='profile.php?token=$access_token'>Profile(<img alt='' src='assets/ProfileIcon.png'/>" . $skills . ")</a></td>";
echo "<td class='menu'><a class='" . $style . "' href='market.php?token=$access_token'>Market(<img alt='' src='assets/GemIcon" . $faction . ".png'/>" . $gems . ")</a></td>";
echo "<td class='menu'><a class='" . $style . "' href='inventory.php?token=$access_token'>Powers</a></td>";
echo "<td class='menu'><a class='" . $style . "' href='help.php?token=$access_token'>Help</a></td><td style='width:45px;'></td>";
echo <<<_END
</tr>
</table><br/><br/>
_END;
if($region == 0)
{
echo "<div class='powerButtons0' style='top:134px;'><div style='position:absolute; left:275px; top:5px;'><a class='button' href='store.php?region=1&token=$access_token'>SAPPHIRE</a></div>";
if($level < 81)
echo "<div style='position:absolute; left:425px;top:5px;'>JADE<br/>Levels 81+</div>";
else
echo "<div style='position:absolute; left:425px;top:5px;'><a href='store.php?region=2&token=$access_token'>JADE</a></div>";
echo "</div>";
}
else if($region == 1)
{
echo "<div class='powerButtons1' style='top:134px;'><div style='position:absolute; left:275px; top:5px;'><a href='store.php?region=1&token=$access_token'>SAPPHIRE</a></div>";

echo "<div style='position:absolute; left:425px;top:5px;'><a class='button' href='store.php?region=2&token=$access_token'>JADE</a></div>";
echo "</div>";
}
echo <<<_END
<div class="profileButtons1" style='top:179px;'>
<table style="top:9px; width:400px; text-align:center; position:absolute; left:199px;">
<tr><td style='width:105px; border:0px; padding:0px;'><a href="inventory.php?token=$access_token">MY POWERS</a></td><td style='width:145px; border:0px; padding:0px;'><a class='button' href="store.php?token=$access_token">TRAINING</a></td><td style='width:105px; border:0px; padding:0px;'><a  href='collection.php?token=$access_token'>COLLECTION</a></td><td style='width:55px;'></td></tr></table>
</div>
_END;

if ($hasResult == 1)
{
echo "<div class='storeresults' style='color:black; top:224px;'>";
if($CanAfford == 1)
{
echo "<div style='position:absolute; top:40px; left:23px;'>You learned</div>";
echo "<div style='position:absolute; top:30px; width:50px; text-align:center; font-size:1.875em; left:100px;'>$quantity</div>";
echo "<div style='position:absolute; top:8px; left:160px;'><img alt='' src='assets/power$boughtID.jpg'/></div>";
echo "<div style='position:absolute; top:30px; left:232px;'>$boughtName<br/>(You now have $newPower)</div>";
echo "<div style='position:absolute; top:30px; left:450px;'>Would you like to<br/>buy $quantity more?</div>";
echo "<div style='position:absolute; top:8px; left:580px;'><form method='post' action='store.php'><input type='hidden' name='token' value='$access_token'/><input type='hidden' name='posted' value='yes'/><input type='hidden' name='quantity' value='$quantity'/><input type='hidden' name='action' value='" . $action . "'/>";
echo "<input type='image' src='assets/BTN_Buy.png' value='Buy'/></form></div>";
}
if($error == 1)
	echo $errorMsg;
echo "</div>";
echo <<<_END
<table class='offence' style='top:327px;'><tr style="background-image:url('assets/BG_box4x4.png');"><th style='width:147px;'>OFFENCE POWERS</th></tr><tr>
_END;
}
else
{
echo <<<_END
<table class='offence' style='top:224px;'><tr style="background-image:url('assets/BG_box4x4.png');"><th style='width:147px;'>OFFENCE POWERS</th></tr><tr>
_END;
}
$counter = 1;
for($i = 0; $i < count($powerID); $i++)
{


	switch($powerType[$i])
		{
			case 0:
					
					$thisPower = $powerName[$i];
					$thisAtk = $powerAttack[$i];
					$thisDef = $powerDefence[$i];
					$pID = $powerID[$i] - 1;
echo <<<_END
					<td style="width:182px; height:158px; background-image:url('assets/Box_TrainingItem.png'); text-align:left; color:black;">
_END;
					echo "<div style='position:relative; top:-12px; left:8px;'><img alt=''  src='assets/power" . $powerID[$i] . ".jpg'/></div>";
					echo "<form method='post' style=height:1px;action='store.php'><input type='hidden' name='token' value='$access_token'/><input type='hidden' name='posted' value='yes'/><input type='hidden' name='action' value='" . $i . "'/>";
					echo "<div style='position:relative; top:-5px; height:50px; left:8px; width:50px; text-align:center;'>";

//if($powerFaction[$i] == JEDEITE)
//	echo "<b>";

echo $powerCost[$i] . "<br/>krevels";

//if($powerFaction[$i] == JEDEITE)
//	echo "</b>";

echo "<br/><select name='quantity' size='1'>";
					echo "<option value='1'>1</option><option value='5'>5</option><option value='10'>10</option><option value='25'>25</option><option value='50'>50</option><option value='100'>100</option></select></div>";
					
						echo "<div style='position:relative; top:-55px; width:71px;left:90px;'><input type='image' src='assets/BTN_Buy.png' value='Buy'/></div></form>";
					echo "<div style='position:relative; height:50px; width:100px; top:-75px; left:72px; text-align:center;'>$thisPower<br/>Atk: $thisAtk Def: $thisDef <br/>Owned: " .$powers[$pID]. "</div> </td>";
					
					$counter++;
					
				
				if($i + 1 < count($powerID) && $powerType[$i] != $powerType[$i + 1])
				{
echo <<<_END
					</tr><tr style='height:5px;'><td></td></tr><tr style="background-image:url('assets/BG_box4x4.png');"><th>DEFENCE POWERS</th></tr><tr>
_END;
					$counter = 1;
				}
				else if($counter > 4)
				{
					echo "</tr><tr></tr>";
					$counter = 1;
				}
				
				break;
			case 1:
				
					$thisPower = $powerName[$i];
					$thisAtk = $powerAttack[$i];
					$thisDef = $powerDefence[$i];
					$pID = $powerID[$i] - 1;
echo <<<_END
					<td style="width:182px; height:158px; background-image:url('assets/Box_TrainingItem.png'); text-align:left; color:black;">
_END;
					echo "<div style='position:relative; top:-12px; left:8px;'><img alt=''  src='assets/power" . $powerID[$i] . ".jpg'/></div>";
					echo "<form method='post' style=height:1px;action='store.php'><input type='hidden' name='token' value='$access_token'/><input type='hidden' name='posted' value='yes'/><input type='hidden' name='action' value='" . $i . "'/>";
					echo "<div style='position:relative; top:-5px; height:50px; left:8px; width:50px; text-align:center;'>";

//if($powerFaction[$i] == JEDEITE)
//	echo "<b>";

echo $powerCost[$i] . "<br/>krevels";

//if($powerFaction[$i] == JEDEITE)
//	echo "</b>";

echo "<br/><select name='quantity' size='1'>";
					echo "<option value='1'>1</option><option value='5'>5</option><option value='10'>10</option><option value='25'>25</option><option value='50'>50</option><option value='100'>100</option></select></div>";
					
						echo "<div style='position:relative; top:-55px; width:71px;left:90px;'><input type='image' src='assets/BTN_Buy.png' value='Buy'/></div></form>";
					echo "<div style='position:relative; height:50px; width:100px; top:-75px; left:72px; text-align:center;'>$thisPower<br/>Atk: $thisAtk Def: $thisDef <br/>Owned: " .$powers[$pID]. "</div> </td>";
					
					$counter++;
					
				
				if($i + 1 < count($powerID) && $powerType[$i] != $powerType[$i + 1])
				{
echo <<<_END
					</tr><tr style='height:5px;'><td></td></tr><tr style="background-image:url('assets/BG_box4x4.png');"><th>UTILITY POWERS</th></tr><tr>
_END;
					$counter = 1;
				}
				else if($counter > 4)
				{
					echo "</tr><tr></tr>";
					$counter = 1;
				}
				
				break;
			case 2:
				
					$thisPower = $powerName[$i];
					$thisAtk = $powerAttack[$i];
					$thisDef = $powerDefence[$i];
					$pID = $powerID[$i] - 1;
echo <<<_END
					<td style="width:182px; height:158px; background-image:url('assets/Box_TrainingItem.png'); text-align:left; color:black;">
_END;
					echo "<div style='position:relative; top:-12px; left:8px;'><img alt=''  src='assets/power" . $powerID[$i] . ".jpg'/></div>";
					echo "<form method='post' style=height:1px;action='store.php'><input type='hidden' name='token' value='$access_token'/><input type='hidden' name='posted' value='yes'/><input type='hidden' name='action' value='" . $i . "'/>";
					echo "<div style='position:relative; top:-5px; height:50px; left:8px; width:50px; text-align:center;'>";

//if($powerFaction[$i] == JEDEITE)
//	echo "<b>";

echo $powerCost[$i] . "<br/>krevels";

//if($powerFaction[$i] == JEDEITE)
//	echo "</b>";

echo "<br/><select name='quantity' size='1'>";
					echo "<option value='1'>1</option><option value='5'>5</option><option value='10'>10</option><option value='25'>25</option><option value='50'>50</option><option value='100'>100</option></select></div>";
					
						echo "<div style='position:relative; top:-55px; width:71px;left:90px;'><input type='image' src='assets/BTN_Buy.png' value='Buy'/></div></form>";
					echo "<div style='position:relative; height:50px; width:100px; top:-75px; left:72px; text-align:center;'>$thisPower<br/>Atk: $thisAtk Def: $thisDef <br/>Owned: " .$powers[$pID]. "</div> </td>";
					
					$counter++;
				
				if($i + 1 >= count($powerID))
				{
					echo "</tr></table><br/>";
					$counter = 1;
				}
				else if($counter > 4)
				{
					echo "</tr><tr></tr>";
					$counter = 1;
				}
				
				
				break;
			default:
				break;
		
		
	}
	
}

echo <<<_END
<div style='position:absolute; top:1680px; left:520px; font-size:0.75em;'>
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
