<?php
require_once 'src/facebook.php';
require_once 'db_vars.php';
require_once 'functions.php';
require_once 'cw_vars.php';



$app_id = APP_ID;
    $app_secret = APP_SECRET;
    $my_url = "http://apps.facebook.com/crimsonwinter/setname.php";
session_start();
//login
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
 $_SESSION['state'] = md5(uniqid(rand(), TRUE)); //CSRF protection
        $dialog_url = "http://www.facebook.com/dialog/oauth?client_id=" 
            . $app_id . "&redirect_uri=" . urlencode($my_url) . "&state="
       . $_SESSION['state'];

        echo("<script> top.location.href='" . $dialog_url . "'</script>");
    }

    if($_REQUEST['state'] == $_SESSION['state']) {
    $token_url = "https://graph.facebook.com/oauth/access_token?client_id="
        . $app_id . "&redirect_uri=" . urlencode($my_url) . "&client_secret="
        . $app_secret . "&code=" . $code;

    $response = file_get_contents($token_url);
     $params = null;
     parse_str($response, $params);

     $graph_url = "https://graph.facebook.com/me?access_token=" 
       . $params['access_token'];

    $user = json_decode(file_get_contents($graph_url));

    $uid = $user->id; 

}
}
if(!is_numeric($uid))
	$uid = 0;





$db_server = mysql_connect(DB_HOST, DB_USER, DB_PW);

if(!$db_server) die("Unable to connect to MySQL: " . mysql_error());

mysql_select_db(DB_NAME) or die("Unable to select database: " . mysql_error());

$query = "SELECT * FROM CW_Users WHERE id='" . $uid . "'";//for real game, add WHERE to narrow it down to fb uid
$result = mysql_query($query);

$numRows = mysql_num_rows($result);
//if the user does not exist, create the user
if($numRows == 0)
{
$query = "INSERT INTO CW_Users SET id='" . $uid . "'";
$result = mysql_query($query);
if(!$result) die("Database access failed at user details: " . mysql_error());
$query = "INSERT INTO CW_Achievements SET id='" . $uid . "'";
$result = mysql_query($query);
if(!$result) die("Database access failed  at achievement details: " . mysql_error());
$query = "INSERT INTO CW_Inventory SET id='" . $uid . "'";
$result = mysql_query($query);
if(!$result) die("Database access failed  at inventory details: " . mysql_error());
$query = "INSERT INTO CW_Notices SET id='" . $uid . "'";
$result = mysql_query($query);
if(!$result) die("Database access failed  at notices details: " . mysql_error());
$query = "INSERT INTO CW_Missions SET id='" . $uid . "'";
$result = mysql_query($query);
if(!$result) die("Database access failed  at mission details: " . mysql_error());
$faction = 0;
}
else
{
$rows = mysql_fetch_row($result);
$faction = $rows[19];
}
if ($faction != 0)
	header('Location: http://www.firstagestudios.com/Ldd49cEsTcwfb/');

$error = 0;
$errorMsg = '';
//check name and faction
if(isset($_POST[faction]))
{
	$newMessage = "Checking your name";
	$name = SanitizeString($_POST[name]);
	$tempID = SanitizeString($_POST[id]);
	$badWords = @file("assets/badwords.txt");
	for ($i=0; $i<count($badWords); $i++) {
        if (preg_match("/" . substr($badWords[$i], 0, -1) . "/i",  $name) > 0)
        {
        	$error = 1;
        	$errorMsg = "Cannot have a word containing " . $badWords[$i] . ".  Please try a new name.";
        	break;
        }
   	} 
   	if($error == 0)
   	{
		$tempFaction = $_POST[faction];
		
		if($tempFaction == SAPPHIROS || $tempFaction == RUBIA || $tempFaction == JEDEITE || $tempFaction == DAMOS || $tempFaction == MACHALITE)
		{
			$faction = $tempFaction;
			$query = "UPDATE CW_Users SET name='" . $name . "', faction='" . $faction . "' WHERE id='" . $tempID . "'";//for real game, add WHERE to narrow it down to fb uid
			$result = mysql_query($query);
			$newtime = time();
			$newtime = date("Y-m-d H:i:s", $newtime);
			$newMessage = "Welcome to Crimson Winter Facebook! Your gem god welcomes you to the game, and encourages you to do missions and increase your power.";
			$query = "UPDATE CW_Notices SET message1='" . $newMessage . "', messageTime1='" . $newtime . "' WHERE id='" . $tempID . "'";
			$result = mysql_query($query);
			$query = "UPDATE CW_Achievements SET achievement25='" . $faction . "' WHERE id='" . $tempID . "'";
			$result = mysql_query($query);
		}
	}
}



echo <<<_END
<?xml version="1.0" encoding="iso-8859-1" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<script>
var faction = $faction;
if(faction != 0)
	window.location = "http://apps.facebook.com/crimsonwinter/";
</script>
<link rel="stylesheet" type="text/css" href="style.css" /> 
<title>Set profile</title></head>
<body class='start'>

<div id="fb-root"></div>
<script src="http://connect.facebook.net/en_US/all.js"></script>
<script>
  window.fbAsyncInit = function() {
    FB.init({appId: '$app_id', status: true, cookie: true,
             xfbml: true});
    FB.Canvas.setSize({width:720, height:1000});
  };
  (function() {
    var e = document.createElement('script'); e.async = true;
    e.src = document.location.protocol +
      '//connect.facebook.net/en_US/all.js';
    document.getElementById('fb-root').appendChild(e);
  }());

</script>

<div style="width:500px; left:235px; top:105px; position:absolute;">
$newMessage<br/>
Welcome to Crimson Winter.<br/><br/>
There are five gem gods of unimaginable power, but by mutual agreement they can only access the mortal realm through their Chosen. The gem gods have Chosen you; decide which one you will support.<br/><br/>
</div>
<form action="setname.php" method="post">
<div class='setname'>
Please enter a name: <input type="text" name="name" size="16" maxlength="16"/><br/>
_END;
if($error == 1)
	echo $errorMsg;
echo <<<_END
</div>
<table class='setfaction'><tr>
<td class='setfaction'><img src='assets/FactionIcon1.jpg'/></td>
<td class='setfaction'><img src='assets/FactionIcon2.jpg'/></td>
<td class='setfaction'><img src='assets/FactionIcon3.jpg'/></td>
<td class='setfaction'><img src='assets/FactionIcon4.jpg'/></td>
<td class='setfaction'><img src='assets/FactionIcon5.jpg'/></td>
</tr>
<tr>
<td class='setfaction'>Sapphiros<input type="radio" name="faction" value="1" checked="checked" /></td>
<td class='setfaction'>Rubia<input type="radio" name="faction" value="2" /></td>
<td class='setfaction'>Jedeite<input type="radio" name="faction" value="3" /></td>
<td class='setfaction'>Damos<input type="radio" name="faction" value="4" /></td>
<td class='setfaction'>Machalite<input type="radio" name="faction" value="5" /></td>
</tr>
</div>
<tr>
<td class='setfaction'>Balance and Harmony</td>
<td class='setfaction'>Passion and Power</td>
<td class='setfaction'>Civilized Savagery</td>
<td class='setfaction'>Honour and Duty</td>
<td class='setfaction'>Order through Chaos</td>
</tr>
<tr>
<td class='setfaction'>Energy Recovery</td>
<td class='setfaction'>Attack Boost</td>
<td class='setfaction'>Health Recovery</td>
<td class='setfaction'>Defence Boost</td>
<td class='setfaction'>Stamina Recovery</td>
</tr>
</table>
<div style="position:absolute; width:50px; top:460px; left:355px; text-align:center;">
<input type="hidden" name="id" value='$uid'/>
<input type="submit" value="SUBMIT"/>
</div>
</form>
<!--
<div style="width:744px; position:relative; left:8px;">
<form action="setname.php" method="post">
NAME: <input type="text" name="name" size="12" maxlength="12"/>
<br/><br/>FACTION<br/>
There are five gem gods of unimaginable power, but by mutual agreement they can only access the mortal realm through their Chosen. The gem gods have Chosen you; which faction will you choose?  You may change faction once per level for free.<br/><br/>
 Sapphiros: The Sapphire god of balance and harmony, Sapphiros exists to promote peace and unity.  Sapphiros' Chosen earn increased energy recovery. <br/><br/>
 Rubia: The Ruby god of passion and power, Rubia's followers fight for causes they are passionate about. Chosen of Rubia gain increased attack strength. <br/><br/>
 Jedeite: The Jade god of civilized savagery, Jedeite's followers fight to protect their homeland and their freedom. Jedeite's Chosen gain increased health recovery.<br/><br/>
 Damos: The Diamond god of honor and duty, Damos' followers fight to protect others. Damos' Chosen gain increased defense.<br/><br/>
 Machalite: The black stone god stands for order through chaos. Machalite's followers strive for perfection. Machalite's Chosen benefit from powers that increase stamina recovery. <br/><br/>



</form>
</div>
-->
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
</body></html>
_END;
mysql_close($db_server);
?>
