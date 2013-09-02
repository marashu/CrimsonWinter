<?php
require_once 'src/facebook.php';
require_once 'db_vars.php';
require_once 'functions.php';
require_once 'cw_vars.php';

//initialize achievement variables
$displayAchievement = 0;
$achName = '';

//Get the player's facebook data
//NOTE: THIS IS OUT OF DATE
$app_id = APP_ID;
    $app_secret = APP_SECRET;
    //$my_url = "http://www.firstagestudios.com/Ldd49cEsTcwfb/";
    $my_url = "http://apps.facebook.com/crimsonwinter/";
    session_start();
    //echo("debug 2");

//get any requests from friends
    if(isset($_GET["request_ids"]))//powers, gifts, etc
    {
    		$request_array = explode(',', $_REQUEST["request_ids"]);
    		$req_id = $request_array[0];
    	        //setcookie('request_ids', $_GET["request_ids"], time() + 60 * 5, '/');
    	        $my_url .= "index.php?request_ids=" . $req_id;
    }
    else if(isset($_GET["sandhelp"]))//Sand crawler helping out
    {
    	$help_id = $_GET["sandhelp"];
    	if(!is_numeric($help_id))
    		$help_id = 0;
    	else
    	{
    		$my_url .= "index.php?sandhelp=" . $help_id;
    	}
    }
    else if(isset($_GET["oujouhelp"]))//Oujou helping out
    {
    	$help_id = $_GET["oujouhelp"];
    	if(!is_numeric($help_id))
    		$help_id = 0;
    	else
    	{
    		$my_url .= "index.php?oujouhelp=" . $help_id;
    	}
    }
    

//access the user id
$access_token = $_REQUEST["token"];
	if(!empty($access_token))
	{
	$graph_url = "https://graph.facebook.com/me?" . $access_token;

    $user = json_decode(file_get_contents($graph_url));

    $uid = $user->id;
    echo $uid;
    }
//If that way didn't work, try this way
if(!$uid)
{
$signed_request = $_POST['signed_request'];
$signed_data = parse_signed_request($signed_request, $app_secret);
$uid = $signed_data['user_id'];
//echo $uid;
}
//and if that way also didn't work, try this way
if(!$uid)
{
    if(!isset( $_REQUEST["code"] ) ) {
    $_SESSION['state'] = md5(uniqid(rand(), TRUE)); //CSRF protection
    $dialog_url = "http://www.facebook.com/dialog/oauth?client_id=" 
        . $app_id . "&redirect_uri=" . urlencode($my_url);

    echo("<script> top.location.href='" . $dialog_url . "'</script>");
} else {
    $code = $_REQUEST["code"];
}

    if(empty($code)) {
        $_SESSION['state'] = md5(uniqid(rand(), TRUE)); //CSRF protection
        $dialog_url = "http://www.facebook.com/dialog/oauth?client_id=" 
            . $app_id . "&redirect_uri=" . urlencode($my_url) . "&state="
       . $_SESSION['state'];
//echo "Debug " . $dialog_url;
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
    
    $checkFriends = 1;
    }
    else
    {
     echo("The state does not match. You may be a victim of <a href='http://en.wikipedia.org/wiki/Cross-site_request_forgery'CSRF</a>.");
    }
}

if(!is_numeric($uid))
	$uid = 0;
	
//connect to the database
$db_server = mysql_connect(DB_HOST, DB_USER, DB_PW);

if(!$db_server) die("Unable to connect to MySQL: " . mysql_error());

mysql_select_db(DB_NAME) or die("Unable to select database: " . mysql_error());

//initialize gift variables
$hadGift = 0;
$gift_notice = '';

if($uid != 0)
{
//check requests
if(isset($_REQUEST["request_ids"]))
{
$cookie = $_REQUEST["request_ids"];

	$requestID = explode(',', $_REQUEST["request_ids"]);
	//setcookie("request_ids", $_COOKIE["request_ids"], time() - 500000);
	//echo "<script>alert('$cookie');</script>";
	for($i = 0; $i < count($requestID); $i++)
	{
		$request_url = "https://graph.facebook.com/" . $requestID[$i] . "?" . $access_token;
		$request_object = json_decode(file_get_contents($request_url), true);
		$myRequest = $request_object['data'];
		//has a request
		if($myRequest)
		{
			$hadGift = 1;
			//echo "<script>alert('$i');</script>";
		$delete_url = "https://graph.facebook.com/" . $requestID[$i] . "?" . $access_token . "&method=delete";
		$result = file_get_contents($delete_url);
		//get achievement details for the player from the achievements table
		$query = "SELECT achievement16, achievement31, achievement33 FROM CW_Achievements WHERE id=$uid";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		$Repertoire = $row[0];
$AllSetForSand = $row[1];
$TaiyouTourist = $row[2];
    //get the total powers from the users table, for checking achievements
		$query = "SELECT powerTotal FROM CW_Users WHERE id=$uid";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		$powerTotal = $row[0];
		
		//Handle logic for the different gifts, including checking for achievements
		switch($myRequest)
		{
case 13:
				$query = "SELECT power13 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$row = mysql_fetch_row($result);
				if($row[0] == 0)
				{
					
					$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id='" . $uid . "'";
					$result = mysql_query($query);
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
				}
				$query = "UPDATE CW_Inventory SET power13=power13+1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$gift_notice .= "You have learned Ice Spike under the tutorship of your friend.";
				break;
			case 16:
				$query = "SELECT power16 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$row = mysql_fetch_row($result);
				if($row[0] == 0)
				{
					
					$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id='" . $uid . "'";
					$result = mysql_query($query);
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
				}
				$query = "UPDATE CW_Inventory SET power16=power16+1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$gift_notice .= "You have learned Invisible Arrow under the tutorship of your friend.";
				break;
			case 17:
				$query = "SELECT power17 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$row = mysql_fetch_row($result);
				if($row[0] == 0)
				{
					$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id='" . $uid . "'";
					$result = mysql_query($query);
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
				}
				$query = "UPDATE CW_Inventory SET power17=power17+1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$gift_notice .= "You have learned Blind under the tutorship of your friend.";
				break;
			case 18:
				$query = "SELECT power18 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$row = mysql_fetch_row($result);
				if($row[0] == 0)
				{
					$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id='" . $uid . "'";
					$result = mysql_query($query);
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
				}
				$query = "UPDATE CW_Inventory SET power18=power18+1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$gift_notice .= "You have learned Freeze Person under the tutorship of your friend.";
				break;
			case 22:
				$query = "SELECT power22 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$row = mysql_fetch_row($result);
				if($row[0] == 0)
				{
					$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id='" . $uid . "'";
					$result = mysql_query($query);
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
				}
				$query = "UPDATE CW_Inventory SET power22=power22+1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$gift_notice .= "You have learned Mist Chance under the tutorship of your friend.";
				break;
			case 25:
				$query = "SELECT power25 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$row = mysql_fetch_row($result);
				if($row[0] == 0)
				{
					$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id='" . $uid . "'";
					$result = mysql_query($query);
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
				}
				$query = "UPDATE CW_Inventory SET power25=power25+1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$gift_notice .= "You have learned Enemy Targeting under the tutorship of your friend.";
				break;
case 29:
				$query = "SELECT power29 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$row = mysql_fetch_row($result);
				if($row[0] == 0)
				{
					$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id='" . $uid . "'";
					$result = mysql_query($query);
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
				}
				$query = "UPDATE CW_Inventory SET power29=power29+1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$gift_notice .= "You have learned Small Shield under the tutorship of your friend.";
				break;
			case 31:
				$query = "SELECT power31 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$row = mysql_fetch_row($result);
				if($row[0] == 0)
				{
					$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id='" . $uid . "'";
					$result = mysql_query($query);
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
				}
				$query = "UPDATE CW_Inventory SET power31=power31+1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$gift_notice .= "You have learned Wall of Fire under the tutorship of your friend.";
				break;
			case 45:
				$query = "SELECT power45 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$row = mysql_fetch_row($result);
				if($row[0] == 0)
				{
					$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id='" . $uid . "'";
					$result = mysql_query($query);
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
				}
				$query = "UPDATE CW_Inventory SET power45=power45+1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$gift_notice .= "You have learned Ventriloquism under the tutorship of your friend.";
				break;
			case 55:
				$query = "SELECT power55 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$row = mysql_fetch_row($result);
				if($row[0] == 0)
				{
					$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id='" . $uid . "'";
					$result = mysql_query($query);
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
				}
				$query = "UPDATE CW_Inventory SET power55=power55+1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$gift_notice .= "You have learned Water Breathing under the tutorship of your friend.";
				break;
			case 56:
				$query = "SELECT power56 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$row = mysql_fetch_row($result);
				if($row[0] == 0)
				{
					
					$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id='" . $uid . "'";
					$result = mysql_query($query);
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
				}
				$query = "UPDATE CW_Inventory SET power56=power56+1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$gift_notice .= "You have learned Invisibility under the tutorship of your friend.";
				break;
			case 59:
				$query = "SELECT power59 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$row = mysql_fetch_row($result);
				if($row[0] == 0)
				{
					$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id='" . $uid . "'";
					$result = mysql_query($query);
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
				}
				$query = "UPDATE CW_Inventory SET power59=power59+1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$gift_notice .= "You have learned Levitate Other under the tutorship of your friend.";
				break;
			case 79:
				$query = "SELECT power79 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$row = mysql_fetch_row($result);
				if($row[0] == 0)
				{
					$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id='" . $uid . "'";
					$result = mysql_query($query);
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
				}
				$query = "UPDATE CW_Inventory SET power79=power79+1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$gift_notice .= "You have learned Sunburn under the tutorship of your friend.";
				break;
			case 80:
				$query = "SELECT power80 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$row = mysql_fetch_row($result);
				if($row[0] == 0)
				{
					$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id='" . $uid . "'";
					$result = mysql_query($query);
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
				}
				$query = "UPDATE CW_Inventory SET power80=power80+1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$gift_notice .= "You have learned Chill under the tutorship of your friend.";
				break;
			case 86:
$numCollection = 5;
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
				$query = "UPDATE CW_Inventory SET power86=power86+1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$gift_notice .= "Your friend has given you a pair of Goggles for your collection.";
				break;
			case 87:
$numCollection = 6;
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
				$query = "UPDATE CW_Inventory SET power87=power87+1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$gift_notice .= "Your friend has given you a Hammerscope for your collection.";
				break;
				case 95:
				$query = "SELECT power95 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$row = mysql_fetch_row($result);
				if($row[0] == 0)
				{
					$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id='" . $uid . "'";
					$result = mysql_query($query);
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
				}
				$query = "UPDATE CW_Inventory SET power95=power95+1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$gift_notice .= "You have learned Sense Life Force under the tutorship of your friend.";
				break;
				case 102:
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
							$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $uid . "'";
							$result = mysql_query($query);
							$displayAchievement = 31;
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
				$query = "UPDATE CW_Inventory SET power102=power102+1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$gift_notice .= "Your friend has given you a Tree Statue for your collection.";
				break;
				case 104:
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
							$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $uid . "'";
							$result = mysql_query($query);
							$displayAchievement = 31;
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
				$query = "UPDATE CW_Inventory SET power104=power104+1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$gift_notice .= "Your friend has given you a Tree Statue for your collection.";
				break;
			case 159:
				$query = "SELECT power159 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$row = mysql_fetch_row($result);
				if($row[0] == 0)
				{
					$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id='" . $uid . "'";
					$result = mysql_query($query);
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
				}
				$query = "UPDATE CW_Inventory SET power159=power159+1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$gift_notice .= "You have learned Gentle Rain under the tutorship of your friend.";
				break;
			case 160:
				$query = "SELECT power160 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$row = mysql_fetch_row($result);
				if($row[0] == 0)
				{
					$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id='" . $uid . "'";
					$result = mysql_query($query);
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
				}
				$query = "UPDATE CW_Inventory SET power160=power160+1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$gift_notice .= "You have learned Gentle Rain under the tutorship of your friend.";
				break;
			case 161:
				$query = "SELECT power161 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$row = mysql_fetch_row($result);
				if($row[0] == 0)
				{
					$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id='" . $uid . "'";
					$result = mysql_query($query);
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
				}
				$query = "UPDATE CW_Inventory SET power161=power161+1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$gift_notice .= "You have learned Ice Wall under the tutorship of your friend.";
				break;
case 162:
				$query = "SELECT power162 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$row = mysql_fetch_row($result);
				if($row[0] == 0)
				{
					$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id='" . $uid . "'";
					$result = mysql_query($query);
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
				}
				$query = "UPDATE CW_Inventory SET power162=power162+1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$gift_notice .= "You have learned Wind Wall under the tutorship of your friend.";
				break;
case 163:
				$query = "SELECT power163 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$row = mysql_fetch_row($result);
				if($row[0] == 0)
				{
					$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id='" . $uid . "'";
					$result = mysql_query($query);
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
				}
				$query = "UPDATE CW_Inventory SET power163=power163+1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$gift_notice .= "You have learned Metal Armour under the tutorship of your friend.";
				break;
			case 164:
				$query = "SELECT power164 FROM CW_Inventory WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$row = mysql_fetch_row($result);
				if($row[0] == 0)
				{
					$query = "UPDATE CW_Users SET powerTotal=powerTotal + 1 WHERE id='" . $uid . "'";
					$result = mysql_query($query);
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
				}
				$query = "UPDATE CW_Inventory SET power164=power164+1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$gift_notice .= "You have learned Locate Object under the tutorship of your friend.";
				break;
			case 1111:
				$query = "SELECT m_energy, boostEnergy FROM CW_Users WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$row = mysql_fetch_row($result);
				if($row[1] == 0)
				{
				$newValue = (int)($row[0] * 0.4);
				$query = "UPDATE CW_Users SET c_energy=c_energy + " . $newValue . ", boostEnergy=1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$gift_notice .= "Your friend has given your energy a boost!";
				}
				else
					$gift_notice .= "Cannot receive that boost more than once per day.  Please try again tomorrow.";
				break;
			case 3333:
				$query = "SELECT m_health, boostHealth FROM CW_Users WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$row = mysql_fetch_row($result);
				if($row[1] == 0)
				{
				$newValue = (int)($row[0] * 0.4);
				$query = "UPDATE CW_Users SET c_health=c_health + " . $newValue . ", boostHealth=1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$gift_notice .= "Your friend has given your health a boost!";
				}
				else
					$gift_notice .= "Cannot receive that boost more than once per day.  Please try again tomorrow.";
				break;
			case 5555:
				$query = "SELECT m_stamina, boostStamina FROM CW_Users WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$row = mysql_fetch_row($result);
				if($row[1] == 0)
				{
				$newValue = (int)($row[0] * 0.4);
				$query = "UPDATE CW_Users SET c_stamina=c_stamina + " . $newValue . ", boostStamina=1 WHERE id='" . $uid . "'";
				$result = mysql_query($query);
				$gift_notice .= "Your friend has given your stamina a boost!";
				}
				else
					$gift_notice .= "Cannot receive that boost more than once per day.  Please try again tomorrow.";
				break;
			default:
				$gift_notice .= "Something went wrong with your gift request.";
		}
		}
		else
		{
			$hadGift = 1;
			$gift_notice .= "This gift request has expired.";
		}
		//$gift_notice .= "<br/><br/><a onclick='GiftRedirect(); return false;' href='#' class='header'>Accept more gifts?</a>";
		$gift_notice .= "<a href='http://www.facebook.com/reqs.php#confirm_" . $app_id . "_0' target='_top' class='faction'>  Accept more gifts?</a>";
	}
}
//help request for the area 1 boss fight
else if(isset($_GET["sandhelp"]))
{
//$gift_notice .= "This event has ended.";

	$hadGift = 1;
	if($help_id == 0)
	{
		$gift_notice .= "That user does not exist.";
	}
	else if($help_id == $uid)
	{
		$gift_notice .= "You cannot support your own flank.";
	}
	else
	{
		$query = "SELECT name, sandTime, sandHealth, sandHelp, level, experience, c_energy, m_energy, c_health, m_health, c_stamina, m_stamina FROM CW_Users WHERE id=$help_id";
		$result = mysql_query($query);
		if(!$result)
		{
			$gift_notice .= "That user does not exist.";
		}
		else
		{
			$row = mysql_fetch_row($result);
			$sandName = $row[0];
			$sandTime = $row[1];
			$sandHealth = $row[2];
			$sandHelp = $row[3];
$helpLevel = $row[4];
$helpExp = $row[5];
$c_energy = $row[6];
$m_energy = $row[7];
$c_health = $row[8];
$m_health = $row[9];
$c_stamina = $row[10];
$m_stamina = $row[11];
			
			//don't let friends have blank names
			if($sandName == '')
				$sandName = "Unnamed Chosen";
			
			$query = "SELECT name FROM CW_Users WHERE id=$uid";
			$result = mysql_query($query);
			$row = mysql_fetch_row($result);
			$name = $row[0];
if($name == '')
$name = "Unnamed Chosen";
			//First, we make sure the player isn't too late
			$newtime = time();
			$query = "SELECT UNIX_TIMESTAMP('" . $sandTime. "') as sandDiff";
			$result = mysql_query($query);
			$result = mysql_fetch_assoc($result);
				
			$result['sandDiff'] -= $newtime;
			
			if($result['sandDiff'] < 0 || $sandHealth <= 0)
			{
				$gift_notice .= "$sandName's fight has already ended.";
			}
			else//check if the player has already helped with this fight
			{
				$didHelp = 0;
				if($sandHelp != '')
				{
					$sand_array = explode(',', $sandHelp);
					
					for ($i = 0; $i < count($sand_array); $i++)
					{
						if($sand_array[$i] == $uid)
						{
							$didHelp = 1;
							$gift_notice .= "You have already helped fight this person";
							break;
						}
					}
				}
				if($didHelp == 0)//actually help the player here.
				{
					if($sandHelp == '')
						$sandHelp = $uid;
					else
						$sandHelp .= "," . $uid;
					
					$query = "SELECT * FROM CW_Notices WHERE id='$help_id'";
					$result = mysql_query($query);
						
						
					$rows = mysql_fetch_row($result);
						
					$message[0] = '';
					$message[1] = $rows[1];
					$message[2] = $rows[2];
					$message[3] = $rows[3];
					$message[4] = $rows[4];
					
					$messageTime[0] = time();
					$messageTime[0] = date("Y-m-d H:i:s", $messageTime[0]);
					$messageTime[1] = $rows[6];
					$messageTime[2] = $rows[7];
					$messageTime[3] = $rows[8];
					$messageTime[4] = $rows[9];
					
					
					
					$sandHealth -= 600;
					if($sandHealth < 0)
					{
						$sandHealth = 0;
						$sandKrevels = 20000;
$helpExp += 200;

$next_exp = GetExpToNextLevel($helpLevel);
			while($helpExp >= $next_exp)
			{

				$helpLevel++;
				if($c_energy < $m_energy)
					$c_energy = $m_energy;
				if($c_health < $m_health)
					$c_health = $m_health;
				if($c_stamina < $m_stamina)
					$c_stamina = $m_stamina;
				//$skills += 5;
				if($level % 3 == 0)
					$gems = 1;
else
$gems = 0;
				$next_exp = GetExpToNextLevel($helpLevel);
				//$levelUp = 1;
				if($fac_change == 0)
					$fac_change = 1;
				if($level == 11 || $level == 26 || $level == 41 || $level == 61)
					$areaChange = 1;
				$query = "UPDATE CW_Users SET fac_change=1, level=$helpLevel, c_energy=$c_energy, c_stamina=$c_stamina, skills=skills+5, gems=gems+$gems WHERE id=$help_id";
				$result = mysql_query($query);
				
				
			}

						$gift_notice = "You brought down Sir Sabien!  Collect your share of 20 000 Krevels!";
						$message[0] = "$name brought down Sir Sabien!  You earned 200 experience points.";
						$query = "SELECT achievement36 FROM CW_Achievements WHERE id='$help_id'";
						$result = mysql_query($query);
						$row = mysql_fetch_row($result);
						
$RememberFear = $row[0];

						if($RememberFear >= 0)
						{
							$RememberFear++;
							if($RememberFear >= 3)
							{
							$RememberFear = -1;
							$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='$help_id'";
							$result = mysql_query($query);
							}
							$query = "UPDATE CW_Achievements SET achievement36 = $RememberFear WHERE id='$help_id'";
							$result = mysql_query($query);
							
						}
						/*From the summer event.
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
				$query = "UPDATE CW_Inventory SET power" . $dropCollection . "=power" . $dropCollection . " + 1 WHERE id=$help_id";
				$result = mysql_query($query);
				*/
				/*Also from the summer event
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
							$query = "UPDATE CW_Users SET skills='" . $skills . "' WHERE id='" . $help_id . "'";
							$result = mysql_query($query);
							$displayAchievement = 31;
							$achName = "All Set For Sand";		
						}
						$query = "UPDATE CW_Achievements SET achievement31='" . $AllSetForSand . "' WHERE id='" . $help_id . "'";
						$result = mysql_query($query);

					}
				}
				else if($AllSetForSand == 0)
				{
					$AllSetForSand = $numCollection;
					$query = "UPDATE CW_Achievements SET achievement31='" . $AllSetForSand . "' WHERE id='" . $help_id . "'";
					$result = mysql_query($query);
				}
					*/	
					}
					else
					{
						$sandKrevels = 20000;
						$gift_notice = "You dealt 600 damage to Sir Sabien ($sandHealth remains)!  Collect your share of 20 000 Krevels!";
						$message[0] = "$name dealt 600 damage to Sir Sabien.";
					}
					
					$query = "UPDATE CW_Users SET sandHealth=$sandHealth, sandHelp='" . $sandHelp . "'";
					if($sandHealth <= 0)
						$query .= ", experience=experience + 200, krevels=krevels+50000";
					$query .= " WHERE id=$help_id";
					$result = mysql_query($query);
					$query = "UPDATE CW_Users SET krevels=krevels+$sandKrevels WHERE id=$uid";
					$result = mysql_query($query);
					$query = "UPDATE CW_Notices SET message1='" . $message[0] . "', message2='" . $message[1] . "', message3='" . $message[2] . "', message4='" . $message[3] . "', message5='" . $message[4] . "', messageTime1='" . $messageTime[0] . "', messageTime2='" . $messageTime[1] . "', messageTime3='" . $messageTime[2] . "', messageTime4='" . $messageTime[3] . "', messageTime5='" . $messageTime[4] . "' WHERE id='" . $help_id . "'";
					$result = mysql_query($query);
				}
			}
		}
	}
}
else if(isset($_GET["oujouhelp"]))//Area 2 boss, much like the Area 1 boss
{
//$gift_notice .= "This event has ended.";

	$hadGift = 1;
	if($help_id == 0)
	{
		$gift_notice .= "That user does not exist.";
	}
	else if($help_id == $uid)
	{
		$gift_notice .= "You cannot support your own flank.";
	}
	else
	{
		$query = "SELECT name, oujouTime, oujouHealth, oujouHelp, level, experience, c_energy, m_energy, c_health, m_health, c_stamina, m_stamina FROM CW_Users WHERE id=$help_id";
		$result = mysql_query($query);
		if(!$result)
		{
			$gift_notice .= "That user does not exist.";
		}
		else
		{
			$row = mysql_fetch_row($result);
			$sandName = $row[0];
			$sandTime = $row[1];
			$sandHealth = $row[2];
			$sandHelp = $row[3];
$helpLevel = $row[4];
$helpExp = $row[5];
$c_energy = $row[6];
$m_energy = $row[7];
$c_health = $row[8];
$m_health = $row[9];
$c_stamina = $row[10];
$m_stamina = $row[11];
			
			if($sandName == '')
				$sandName = "Unnamed Chosen";
			
			$query = "SELECT name FROM CW_Users WHERE id=$uid";
			$result = mysql_query($query);
			$row = mysql_fetch_row($result);
			$name = $row[0];
if($name == '')
$name = "Unnamed Chosen";
			//First, we make sure the player isn't too late
			$newtime = time();
			$query = "SELECT UNIX_TIMESTAMP('" . $sandTime. "') as sandDiff";//for real game, add WHERE to narrow it down to fb uid
			$result = mysql_query($query);
			$result = mysql_fetch_assoc($result);
				
			$result['sandDiff'] -= $newtime;
			
			if($result['sandDiff'] < 0 || $sandHealth <= 0)
			{
				$gift_notice .= "$sandName's fight has already ended.";
			}
			else//check if the player has already helped with this fight
			{
				$didHelp = 0;
				if($sandHelp != '')
				{
					$sand_array = explode(',', $sandHelp);
					
					for ($i = 0; $i < count($sand_array); $i++)
					{
						if($sand_array[$i] == $uid)
						{
							$didHelp = 1;
							$gift_notice .= "You have already helped fight this person";
							break;
						}
					}
				}
				if($didHelp == 0)//actually help the player here.
				{
					if($sandHelp == '')
						$sandHelp = $uid;
					else
						$sandHelp .= "," . $uid;
					
					$query = "SELECT * FROM CW_Notices WHERE id='$help_id'";
					$result = mysql_query($query);
						
						
					$rows = mysql_fetch_row($result);
						
					$message[0] = '';
					$message[1] = $rows[1];
					$message[2] = $rows[2];
					$message[3] = $rows[3];
					$message[4] = $rows[4];
					
					$messageTime[0] = time();
					$messageTime[0] = date("Y-m-d H:i:s", $messageTime[0]);
					$messageTime[1] = $rows[6];
					$messageTime[2] = $rows[7];
					$messageTime[3] = $rows[8];
					$messageTime[4] = $rows[9];
					
					
					
					$sandHealth -= 600;
					if($sandHealth < 0)
					{
						$sandHealth = 0;
						$sandKrevels = 35000;
$helpExp += 500;

$next_exp = GetExpToNextLevel($helpLevel);
			while($helpExp >= $next_exp)
			{

				$helpLevel++;
				if($c_energy < $m_energy)
					$c_energy = $m_energy;
				if($c_health < $m_health)
					$c_health = $m_health;
				if($c_stamina < $m_stamina)
					$c_stamina = $m_stamina;
				//$skills += 5;
				if($level % 3 == 0)
					$gems = 1;
else
$gems = 0;
				$next_exp = GetExpToNextLevel($helpLevel);
				//$levelUp = 1;
				if($fac_change == 0)
					$fac_change = 1;
				if($level == 11 || $level == 26 || $level == 41 || $level == 61)
					$areaChange = 1;
				$query = "UPDATE CW_Users SET fac_change=1, level=$helpLevel, c_energy=$c_energy, c_stamina=$c_stamina, skills=skills+5, gems=gems+$gems WHERE id=$help_id";
				$result = mysql_query($query);
				
				
			}

						$gift_notice = "You brought down the Oujou!  Collect your share of 35 000 Krevels!";
						$message[0] = "$name brought down the Oujou!  You earned 500 experience points.";
						$query = "SELECT achievement37 FROM CW_Achievements WHERE id='$help_id'";
						$result = mysql_query($query);
						$row = mysql_fetch_row($result);
						
$RememberFear = $row[0];

						if($RememberFear >= 0)
						{
							$RememberFear++;
							if($RememberFear >= 5)
							{
							$RememberFear = -1;
							$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='$help_id'";
							$result = mysql_query($query);
							}
							$query = "UPDATE CW_Achievements SET achievement37 = $RememberFear WHERE id='$help_id'";
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
				$query = "UPDATE CW_Inventory SET power" . $dropCollection . "=power" . $dropCollection . " + 1 WHERE id=$help_id";
				$result = mysql_query($query);
				*/
				/*
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
							$query = "UPDATE CW_Users SET skills='" . $skills . "' WHERE id='" . $help_id . "'";
							$result = mysql_query($query);
							$displayAchievement = 31;
							$achName = "All Set For Sand";		
						}
						$query = "UPDATE CW_Achievements SET achievement31='" . $AllSetForSand . "' WHERE id='" . $help_id . "'";
						$result = mysql_query($query);

					}
				}
				else if($AllSetForSand == 0)
				{
					$AllSetForSand = $numCollection;
					$query = "UPDATE CW_Achievements SET achievement31='" . $AllSetForSand . "' WHERE id='" . $help_id . "'";
					$result = mysql_query($query);
				}
					*/	
					}
					else
					{
						$sandKrevels = 35000;
						$gift_notice = "You dealt 600 damage to the Oujou ($sandHealth remains)!  Collect your share of 35 000 Krevels!";
						$message[0] = "$name dealt 600 damage to the Oujou.";
					}
					
					$query = "UPDATE CW_Users SET oujouHealth=$sandHealth, oujouHelp='" . $sandHelp . "'";
					if($sandHealth <= 0)
						$query .= ", experience=experience + 500, krevels=krevels+110000";
					$query .= " WHERE id=$help_id";
					$result = mysql_query($query);
					$query = "UPDATE CW_Users SET krevels=krevels+$sandKrevels WHERE id=$uid";
					$result = mysql_query($query);
					$query = "UPDATE CW_Notices SET message1='" . $message[0] . "', message2='" . $message[1] . "', message3='" . $message[2] . "', message4='" . $message[3] . "', message5='" . $message[4] . "', messageTime1='" . $messageTime[0] . "', messageTime2='" . $messageTime[1] . "', messageTime3='" . $messageTime[2] . "', messageTime4='" . $messageTime[3] . "', messageTime5='" . $messageTime[4] . "' WHERE id='" . $help_id . "'";
					$result = mysql_query($query);
				}
			}
		}
	}
}//end oujou help
}



//get friend details here
if($checkFriends == 1)
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

$boostError = 0;
if($myRequest)
{
	//was something here.
}

//get player details
$query = "SELECT * FROM CW_Users WHERE id='" . $uid . "'";
$result = mysql_query($query);
$numRows = mysql_num_rows($result);

if($numRows > 0)
{

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

$played = $rows[29];



if(!empty($myRequest) && $uid != 0)
{
	switch($myRequest)
	{
		case 11:
			$query = "UPDATE CW_Inventory SET power11=power11 + 1 WHERE id='" . $uid . "'";
			$result = mysql_query($query);
			$requestMsg = "You have claimed 1 ice blast.";
			break;
		default:
			$requestMsg = "This gift request has expired or no longer exists.";
			break;
	}
}
//Has the player logged in today?
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

$godName[SAPPHIROS] = "Sapphiros";
$godName[RUBIA] = "Rubia";
$godName[JEDEITE] = "Jedeite";
$godName[DAMOS] = "Damos";
$godName[MACHALITE] = "Machalite";

//Get results from the daily war
$query = "SELECT * FROM CW_WarStats";
$result = mysql_query($query);
$row = mysql_fetch_row($result);
$win1 = $row[0];
$lose1 = $row[1];
$win2 = $row[2];
$lose2 = $row[3];
$lose3 = $row[4];
$raffleWin = $row[6];

//Show the winner of the weekly raffle
$query = "SELECT name FROM CW_Users WHERE id='" . $raffleWin . "'";
$result = mysql_query($query);
$row = mysql_fetch_row($result);
$raffleWin = $row[0];
if($raffleWin == '')
$raffleWin = "Unnamed Chosen";

//messages and timestamps for the user's notice board.
$query = "SELECT * FROM CW_Notices WHERE id='" . $uid . "'";
$result = mysql_query($query);

if(!$result) die("Database access failed: " . mysql_error());



$rows = mysql_fetch_row($result);

$message1 = $rows[1];
$message2 = $rows[2];
$message3 = $rows[3];
$message4 = $rows[4];
$message5 = $rows[5];
$messageTime[] = $rows[6];
$messageTime[] = $rows[7];
$messageTime[] = $rows[8];
$messageTime[] = $rows[9];
$messageTime[] = $rows[10];

for($i = 0; $i < count($messageTime); $i++)
{
	$newtime = time();
	$newtime = date("Y-m-d H:i:s", $newtime);
	$query = "SELECT TIMEDIFF('" . $newtime. "', '" . $messageTime[$i] . "') as diff";
	$result = mysql_query($query);
	$result = mysql_fetch_assoc($result);
	$diffMessTime = $result['diff'];
	$query = "SELECT HOUR('" . $diffMessTime . "') as myHour";
	$result = mysql_query($query);
	$result = mysql_fetch_assoc($result);
	$hours = $result['myHour'];
	if($hours >= 24)
	{
		$hours = (int)($hours / 24);
		$messageStamp[] = $hours . " days ago.";
	}
	else if($hours > 0)
	{
		$messageStamp[] = $hours . " hours ago.";
	}
	else
	{
		$query = "SELECT MINUTE('" . $diffMessTime . "') as myMinute";
		$result = mysql_query($query);
		$result = mysql_fetch_assoc($result);
		$minutes = $result['myMinute'];
		if($minutes > 0)
		{
			$messageStamp[] = $minutes . " minutes ago.";
		}
		else
		{
			$query = "SELECT SECOND('" . $diffMessTime . "') as mySeconds";
			$result = mysql_query($query);
			$result = mysql_fetch_assoc($result);
			$seconds = $result['mySeconds'];
			$messageStamp[] = $seconds . " seconds ago.";
		}
	}
}

//handle energy, stamina, and health recovery from time spent away from the game
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

//level stuff
$next_exp = GetExpToNextLevel($level);
$this_exp = GetExpToNextLevel($level - 1);
$expToNext = $next_exp - $experience;
$nextLevel = $next_exp - $this_exp;
$expWidth = (int)(($nextLevel - $expToNext) / $nextLevel * 100);
$expToThis = $nextLevel - $expToNext;
$sKrevels = number_format($krevels);

//each faction has its own background and style in the .css file.  Get the user's style here.
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
}
else
{
	$faction = 0;
	$uid = 0;
}
//begin echoing the html
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
//if the player has no id or no faction, send them to the intro page
	window.location = "http://apps.facebook.com/crimsonwinter/setname.php";
	
}

</script>
<link rel="stylesheet" type="text/css" href="style.css" /> 
<title>Crimson Winter</title>
</head>
_END;
echo "<body class='" . $style . "'";
if($displayAchievement != 0)
{
//let players share achievements on facebook
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
    FB.Canvas.setSize({width:720, height:1200});
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
<!-- HUD ELEMENTS -->
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
<!-- for navigating between different game areas -->
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
<!-- The novel that inspired the game -->
<div class='novel'>
<a href="http://www.amazon.com/gp/product/0557821118/ref=as_li_tf_tl?ie=UTF8&tag=firagestu-20&linkCode=as2&camp=1789&creative=9325&creativeASIN=0557821118" onclick="ClickNovel(); return false;">
<img style='border:0px; padding:0px; margin:0px;' src='assets/BookBanner.jpg'/>
</a>
</div>

<div class="specials">
<!-- show latest developer updates here.  Updated weekly. -->
<div style="position:relative; top:8px; font-size:1.125em; text-align:center; color:orange;">WHAT'S NEW</div>
<div id="Box1" style="text-align:center; position:absolute; top:35px; left:0px; width:187px;"><a href='gifts.php?token=$access_token' class='new'><img src='assets/power164.jpg'/><br/><br/>THIS WEEK's GIFT!<br/>Locate Object</a></div>
<div id="Box2" style="text-align:center; position:absolute; top:35px; left:187px; width:187px;"><a href='allies.php?token=$access_token' class='new'><img src='assets/Ally4.png' width="64px" height="64px"/><br/><br/>NEW FEATURE<br/>Allies!</a></div>
<div id="Box3" style="text-align:center; position:absolute; top:35px; left:374px; width:187px;"><a href='market.php?token=$access_token' class='new'><img src='assets/Icon_GemSale.jpg'/><br/><br/>GEM SALE!<br/>20% more gems</a></div>
<div id="Box4" style="text-align:center; position:absolute; top:35px; left:561px; width:187px;"><a href='market.php?token=$access_token' class='new'><img src='assets/power167.jpg' width="64px" height="64px"/><br/><br/>NEW POWER!<br/>Far Slice</a></div>

_END;
if($hadGift)
{
echo <<<_END
	<div id="Box5" style="position:absolute; top:184px; left:8px; width:728px; text-align:center; color:black;">
	$gift_notice
	</div>
_END;
}
echo <<<_END
</div>
<!-- Details about the daily wars -->
<div class="homeWar">
<div style='top:8px; left:11px; position:absolute; width:238px;'>
<div style="font-size:1.125em; text-align:center; color:orange;">FACTION WARS</div><br/>
_END;
if($win1 != $win2) //If they're the same, it's from the free for all events
{
echo <<<_END
YESTERDAY: $godName[$win1] defeated $godName[$lose1].  $godName[$win2] defeated $godName[$lose2] and $godName[$lose3].<br/><br/>
_END;
}
else
{
echo <<<_END
YESTERDAY: $godName[$win1] defeated all other competitors!<br/><br/>
_END;
}
echo <<<_END
<div style='width:50%;'>
Want great prizes?  <br/><a class='faction' href='wars.php?token=$access_token'>Help your gem god win today!</a>
</a>
</div>
</div>
</div>
<div class="raffleWin">

<div style="position:absolute; top:8px; left:11px; width:238px;">
<div style="font-size:1.125em; text-align:center; color:orange;">WEEKLY RAFFLE</div><br/>
This week's raffle winner was $raffleWin!<br/><br/>
<div style="width:55%;">
This week's raffle is for a Damos Power Set (Ice Armour, De-Possess, Far Slice)! <a class='faction' href='market.php?token=$access_token'>Buy your ticket now!</a>
</div>
</div>
</div>
<div class="messages" style="background-image:none;">
<div style="background-image:url('assets/Box_Updates.png'); width:485px; height:420px;">
<div style="position:relative; top:8px; left:8px; width:466px;">
<!-- Display player's notices -->
<div style="font-size:1.125em; text-align:center; color:orange;">MESSAGES</div><br/>
_END;
echo $message1;
if($message1 != '')
{
echo "<br/>";
echo $messageStamp[0];
}
echo "<br/><br/>";
echo $message2;
if($message2 != '')
{
echo "<br/>";
echo $messageStamp[1];
}
echo "<br/><br/>";
echo $message3;
if($message3 != '')
{
echo "<br/>";
echo $messageStamp[2];
}
echo "<br/><br/>";
echo $message4;
if($message4 != '')
{
echo "<br/>";
echo $messageStamp[3];
}
echo "<br/><br/>";
echo $message5;
if($message5 != '')
{
echo "<br/>";
echo $messageStamp[4];
}
echo <<<_END
</div>
</div>
<div style='position:absolute; bottom:0px; left:220px; font-size:0.75em;'>
<table style='text-align:center;'>
<tr>
<td style='width:120px;'>
<a 
_END;
if($faction == DAMOS)//because Damos has a light background
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
//countdown timers
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
function GiftRedirect()
{
window.location="http://www.facebook.com/reqs.php#confirm_205670806110909_0";
};
</script>
<script>
//some AJAX for if the player clicked on the novel ad
function ClickNovel()
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
		    FB.ui(
		   {
		    method: 'feed',
		    link: 'http://apps.facebook.com/crimsonwinter',
		    picture: 'http://www.firstagestudios.com/Ldd49cEsTcwfb/assets/achie23.jpg',
		    source: 'http://www.firstagestudios.com/Ldd49cEsTcwfb/assets/achie23.jpg',
		     name: 'Achievement',
		     caption: 'Crimson Winter',
		     description: 'I have obtained the Research achievement in Crimson Winter. Can you do the same?'
		
		   }
		   
		
		   
		  );
		  }
		  
		  }
		  };
		  
		  xmlhttp.open("GET","adclick.php?token=$access_token",true);
		xmlhttp.send();
	window.open('http://www.amazon.com/gp/product/0557821118/ref=as_li_tf_tl?ie=UTF8&tag=firagestu-20&linkCode=as2&camp=1789&creative=9325&creativeASIN=0557821118');
	return false;
}
</script>
</body>
</html>
_END;

mysql_close($db_server);
?>
