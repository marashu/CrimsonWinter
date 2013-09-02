<?php
require_once 'src/facebook.php';
require_once 'db_vars.php';
require_once 'functions.php';
require_once 'cw_vars.php';

//this file is the callback for AJAX for sending gift requests.
$q=$_GET["q"];
$db_server = mysql_connect(DB_HOST, DB_USER, DB_PW);

if(!$db_server) die("Unable to connect to MySQL: " . mysql_error());

mysql_select_db(DB_NAME) or die("Unable to select database: " . mysql_error());


$access_token = $_REQUEST["token"];
$requestID = explode(',',$_GET['q']);
for ($j = 0; $j < count($requestID); $j++)
{
	$request_url = "https://graph.facebook.com/" . $requestID[$j] . "?" . $access_token;
	$request_object = json_decode(file_get_contents($request_url), true);
	$myRequest = $request_object['to']['id'];
	
	

$uid = $request_object['from']['id'];


$query = "SELECT achievement29, achievement30 FROM CW_Achievements WHERE id='" . $uid . "'";
$result = mysql_query($query);
$row = mysql_fetch_row($result);

$Sponsor = $row[0];
$MistMe = $row[1];



if($Sponsor >= 0)
{
$Sponsor++;
	if($Sponsor == 10)
	{
		$Sponsor = -1;
		$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $uid . "'";
		$result = mysql_query($query);
echo "1111";
	}
	$query = "UPDATE CW_Achievements SET achievement29='" . $Sponsor . "' WHERE id='" . $uid . "'";
	$result = mysql_query($query);
}
if($MistMe >= 0 && $request_object['data'] == 22)
{
$MistMe++;
	if($MistMe == 5)
	{
		$MistMe = -1;
		$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $uid . "'";
		$result = mysql_query($query);
echo "2222";
	}
	$query = "UPDATE CW_Achievements SET achievement30='" . $MistMe . "' WHERE id='" . $uid . "'";
	$result = mysql_query($query);
}

}
mysql_close($db_server);


?>
