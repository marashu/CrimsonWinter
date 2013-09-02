<?php
require_once 'src/facebook.php';
require_once 'db_vars.php';
require_once 'functions.php';
require_once 'cw_vars.php';
$app_id = APP_ID;
    $app_secret = APP_SECRET;
    $my_url = "http://apps.facebook.com/crimsonwinter/missionrequest.php";
//echo "test";
$q=$_GET["q"];
$t=$_GET["t"];
$uid = $_GET["uid"];
//echo $t;
$db_server = mysql_connect(DB_HOST, DB_USER, DB_PW);

if(!$db_server) die("Unable to connect to MySQL: " . mysql_error());

mysql_select_db(DB_NAME) or die("Unable to select database: " . mysql_error());

//echo "UID: " . $uid;
//$uid = $request_object['from']['id'];
if($t == "move")
{
//mission areas will be digits, changing actual regions will be 100's
switch($q)
{
case 0:
case 1:
case 2:
case 3:
case 4:
//echo "here";
$query = "SELECT region, level FROM CW_Users WHERE id='" . $uid . "'";
$result = mysql_query($query);
$row = mysql_fetch_row($result);
$region = $row[0];
$level = $row[1];
$group = $q + 5 * $region;
if((($group == 9 && $level > 180) || ($group == 8 && $level > 150) || ($group == 7 && $level > 125) || ($group == 6 && $level > 100) || ($group == 5 && $level > 80) ||($group == 4 && $level > 60) || ($group == 3 && $level > 40) || ($group == 2 && $level > 25) || ($group == 1 && $level > 10) || $group == 0))
{
$missiongroup = $group;
$location = $missiongroup % 5;
$query = "UPDATE CW_Users SET location='" . $location . "' WHERE id='" . $uid . "'";//for real game, add WHERE to narrow it down to fb uid
$result = mysql_query($query);
echo "1111";
}

break;
case 100:
case 200:
$query = "SELECT level FROM CW_Users WHERE id='" . $uid . "'";
$result = mysql_query($query);
$row = mysql_fetch_row($result);
$level = $row[0];
$newRegion = $q / 100 - 1;
if($newRegion == 1 && $level > 80 || $newRegion == 0)
{
$region = $newRegion;

$query = "UPDATE CW_Users SET region='" . $region . "', location='0' WHERE id='" . $uid . "'";//for real game, add WHERE to narrow it down to fb uid
$result = mysql_query($query);
echo "1111";
}
break;
}
}
else
{


}

mysql_close($db_server);


?>
