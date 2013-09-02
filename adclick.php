<?php
require_once 'src/facebook.php';
require_once 'db_vars.php';
require_once 'functions.php';
require_once 'cw_vars.php';

//this file is for the AJAX call for when a player clicks on the novel ad

$db_server = mysql_connect(DB_HOST, DB_USER, DB_PW);

if(!$db_server) die("Unable to connect to MySQL: " . mysql_error());

mysql_select_db(DB_NAME) or die("Unable to select database: " . mysql_error());


$access_token = $_REQUEST["token"];


if(!empty($access_token))
	{
	$graph_url = "https://graph.facebook.com/me?" . $access_token;

    $user = json_decode(file_get_contents($graph_url));

    $uid = $user->id;
    }



if($uid)
{
$query = "SELECT achievement23 FROM CW_Achievements WHERE id='" . $uid . "'";
$result = mysql_query($query);
$row = mysql_fetch_row($result);

$Research = $row[0];




if($Research >= 0)
{
$Research = -1;
	
		
		$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $uid . "'";
		$result = mysql_query($query);

	$query = "UPDATE CW_Achievements SET achievement23='" . $Research . "' WHERE id='" . $uid . "'";
	$result = mysql_query($query);
	echo "1111";
}
}

mysql_close($db_server);


?>
