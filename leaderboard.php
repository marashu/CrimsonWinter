<?php
require_once 'src/facebook.php';
require_once 'db_vars.php';
require_once 'functions.php';
require_once 'cw_vars.php';
$displayAchievement = 0;
$achName = '';
$app_id = APP_ID;
$app_secret = APP_SECRET;
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
//player data
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
$fTime = $rows[22];

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
//10 highest levels
$query = "SELECT name, experience, id FROM CW_Users WHERE id!=0 ORDER BY experience DESC LIMIT 0,10";
$result = mysql_query($query);

$rows = mysql_num_rows($result);
for($i = 0; $i < $rows; $i++)
{
$row = mysql_fetch_row($result);
if($row[0] == '')
	$row[0] = "Unnamed Chosen";
$leadExpName[] = $row[0];
$leadExp[] = $row[1];
$leadExpID[] = $row[2];
}
//most fights won
$query = "SELECT name, fightsWon, id FROM CW_Users WHERE id!=0 ORDER BY fightsWon DESC LIMIT 0,10";
$result = mysql_query($query);

$rows = mysql_num_rows($result);
for($i = 0; $i < $rows; $i++)
{
$row = mysql_fetch_row($result);
if($row[0] == '')
	$row[0] = "Unnamed Chosen";
$leadFightsName[] = $row[0];
$leadFights[] = $row[1];
$leadFightsID[] = $row[2];
}

//most krevels
$query = "SELECT name, krevels + bank, id FROM CW_Users WHERE id!=0 ORDER BY krevels + bank DESC LIMIT 0,10";
$result = mysql_query($query);

$rows = mysql_num_rows($result);
for($i = 0; $i < $rows; $i++)
{
$row = mysql_fetch_row($result);
if($row[0] == '')
	$row[0] = "Unnamed Chosen";
$leadKrevelsName[] = $row[0];
$leadKrevels[] = $row[1];
$leadKrevelsID[] = $row[2];
}

//most missions done
$query = "SELECT name, missionTotal, id FROM CW_Users WHERE id!=0 ORDER BY missionTotal DESC LIMIT 0,10";
$result = mysql_query($query);

$rows = mysql_num_rows($result);
for($i = 0; $i < $rows; $i++)
{
$row = mysql_fetch_row($result);
if($row[0] == '')
	$row[0] = "Unnamed Chosen";
$leadMissionsName[] = $row[0];
$leadMissions[] = $row[1];
$leadMissionsID[] = $row[2];
}

//most missions cleared
$query = "SELECT name, missionCleared, id FROM CW_Users WHERE id!=0 ORDER BY missionCleared DESC LIMIT 0,10";
$result = mysql_query($query);

$rows = mysql_num_rows($result);
for($i = 0; $i < $rows; $i++)
{
$row = mysql_fetch_row($result);
if($row[0] == '')
	$row[0] = "Unnamed Chosen";
$leadClearedName[] = $row[0];
$leadCleared[] = $row[1];
$leadClearedID[] = $row[2];
}

//most powers
$query = "SELECT name, powerTotal, id FROM CW_Users WHERE id!=0 ORDER BY powerTotal DESC LIMIT 0,10";
$result = mysql_query($query);

$rows = mysql_num_rows($result);
for($i = 0; $i < $rows; $i++)
{
$row = mysql_fetch_row($result);
if($row[0] == '')
	$row[0] = "Unnamed Chosen";
$leadPowersName[] = $row[0];
$leadPowers[] = $row[1];
$leadPowersID[] = $row[2];
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
<title>Leaderboard</title>
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
echo "<td class='menu'><a class='" . $style . "' href='profile.php?token=$access_token'>Profile(<img src='assets/ProfileIcon.png'/>" . $skills . ")</a></td>";
echo "<td class='menu'><a class='" . $style . "' href='market.php?token=$access_token'>Market(<img src='assets/GemIcon" . $faction . ".png'/>" . $gems . ")</a></td>";
echo "<td class='menu'><a class='" . $style . "' href='inventory.php?token=$access_token'>Powers</a></td>";
echo "<td class='menu'><a class='" . $style . "' href='help.php?token=$access_token'>Help</a></td><td style='width:45px;'></td>";
echo <<<_END
</tr>
</table><br/><br/>

<div class="btn3of4">
<table style="top:9px; width:600px; text-align:center; position:absolute; left:99px;">
<tr><td style='width:145px; border:0px; padding:0px;'><a href="profile.php?token=$access_token">CHOSEN</a></td><td style='width:105px; border:0px; padding:0px; text-align:left;'><a href="knights.php?token=$access_token">KNIGHTS</a></td><td style='width:105px; border:0px; padding:0px;text-align:left;'><a  class='button' href='leaderboard.php?token=$access_token'>RANKINGS</a></td><td style='width:135px;border:0px; padding:0px;text-align:left;'><a href='allies.php?token=$access_token'>ALLIES</a></td></tr></table>
</div>
<div class='leaderboard' style='color:black;'>

<!--Highest level-->
<table style='position:absolute; top:0px; left:8px; width:368px;'>
<tr style='height:23px; padding:0px; border:0px;'><th style='width:100%;'>Most Experience</th></tr></table>
<table style='position:absolute; top:23px; left:8px; width:368px;'>
_END;
for ($i = 0; $i < 10; $i++)
{
echo "<tr style='height:23px; padding:0px; border:0px;'>";
echo "<td style='width:184px; padding-left:8px;'>" . ($i + 1) . ". <a href='profile.php?token=$access_token&view=$leadExpID[$i]' class='leaderboard'>" . $leadExpName[$i] . "</a></td>";
echo "<td style='width:184px; text-align:right; padding-right:8px;'>" . $leadExp[$i] . " Experience</td>";
echo "</tr>";
}
echo <<<_END
</table>

<!--Most fights won-->
<table style='position:absolute; top:0px; left:386px; width:368px;'>
<tr style='height:23px; padding:0px; border:0px;'><th style='width:100%;'>Most Fights Won</th></tr></table>
<table style='position:absolute; top:23px; left:386px; width:368px;'>
_END;
for ($i = 0; $i < 10; $i++)
{
echo "<tr style='height:23px; padding:0px; border:0px;'>";
echo "<td style='width:184px; padding-left:8px;'>" . ($i + 1) . ". <a href='profile.php?token=$access_token&view=$leadFightsID[$i]' class='leaderboard'>" . $leadFightsName[$i] . "</a></td>";
echo "<td style='width:184px; text-align:right; padding-right:8px;'>" . $leadFights[$i] . " Fights Won</td>";
echo "</tr>";
}
echo <<<_END
</table>

<!--Most Krevels-->
<table style='position:absolute; top:282px; left:8px; width:368px;'>
<tr style='height:23px; padding:0px; border:0px;'><th style='width:100%;'>Most Krevels</th></tr></table>
<table style='position:absolute; top:305px; left:8px; width:368px;'>
_END;
for ($i = 0; $i < 10; $i++)
{
echo "<tr style='height:23px; padding:0px; border:0px;'>";
echo "<td style='width:184px; padding-left:8px;'>" . ($i + 1) . ". <a href='profile.php?token=$access_token&view=$leadKrevelsID[$i]' class='leaderboard'>" . $leadKrevelsName[$i] . "</a></td>";
echo "<td style='width:184px; text-align:right; padding-right:8px;'>" . $leadKrevels[$i] . " Krevels</td>";
echo "</tr>";
}
echo <<<_END
</table>

<!--Most Powers-->
<table style='position:absolute; top:282px; left:386px; width:368px;'>
<tr style='height:23px; padding:0px; border:0px;'><th style='width:100%;'>Most Powers</th></tr>
</table>
<table style='position:absolute; top:305px; left:386px; width:368px;'>
_END;
for ($i = 0; $i < 10; $i++)
{
echo "<tr style='height:23px; padding:0px; border:0px;'>";
echo "<td style='width:184px; padding-left:8px;'>" . ($i + 1) . ". <a href='profile.php?token=$access_token&view=$leadPowersID[$i]' class='leaderboard'>" . $leadPowersName[$i] . "</a></td>";
echo "<td style='width:184px; text-align:right; padding-right:8px;'>" . $leadPowers[$i] . " Powers</td>";
echo "</tr>";
}
echo <<<_END
</table>

<!--Most Missions Completed-->
<table style='position:absolute; top:564px; left:8px; width:368px;'>
<tr style='height:23px; padding:0px; border:0px;'><th style='width:100%;'>Most Missions Completed</th></tr></table>
<table style='position:absolute; top:587px; left:8px; width:368px;'>
_END;
for ($i = 0; $i < 10; $i++)
{
echo "<tr style='height:23px; padding:0px; border:0px;'>";
echo "<td style='width:184px; padding-left:8px;'>" . ($i + 1) . ". <a href='profile.php?token=$access_token&view=$leadMissionsID[$i]' class='leaderboard'>" . $leadMissionsName[$i] . "</a></td>";
echo "<td style='width:184px; text-align:right; padding-right:8px;'>" . $leadMissions[$i] . " Completed</td>";
echo "</tr>";
}
echo <<<_END
</table>

<!--Most Missions Mastered-->
<table style='position:absolute; top:564px; left:386px; width:368px;'>
<tr style='height:23px; padding:0px; border:0px;'><th style='width:100%;'>Most Missions Mastered</th></tr></table>
<table style='position:absolute; top:587px; left:386px; width:368px;'>
_END;
for ($i = 0; $i < 10; $i++)
{
echo "<tr style='height:23px; padding:0px; border:0px;'>";
echo "<td style='width:184px; padding-left:8px;'>" . ($i + 1) . ". <a href='profile.php?token=$access_token&view=$leadClearedID[$i]' class='leaderboard'>" . $leadClearedName[$i] . "</a></td>";
echo "<td style='width:184px; text-align:right; padding-right:8px;'>" . $leadCleared[$i] . " Mastered</td>";
echo "</tr>";
}
echo <<<_END
</table>

</div>
<div style='position:absolute; top:1020px; left:520px; font-size:0.75em;'>
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
