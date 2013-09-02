<?php
require_once 'src/facebook.php';
require_once 'db_vars.php';
require_once 'functions.php';
require_once 'cw_vars.php';

//AJAX callback file for whether an achievement was earned for inviting friends
$db_server = mysql_connect(DB_HOST, DB_USER, DB_PW);

if(!$db_server) die("Unable to connect to MySQL: " . mysql_error());

mysql_select_db(DB_NAME) or die("Unable to select database: " . mysql_error());

$q=$_GET["q"];


$access_token = $_REQUEST["token"];

$requestID = explode(',',$_GET['q']);
for ($j = 0; $j < count($requestID); $j++)
{
	$request_url = "https://graph.facebook.com/" . $requestID[$j] . "?" . $access_token;
	$request_object = json_decode(file_get_contents($request_url), true);
	
	
	

$uid = $request_object['from']['id'];

if($uid)
{
$query = "SELECT achievement26 FROM CW_Achievements WHERE id='" . $uid . "'";
$result = mysql_query($query);
$row = mysql_fetch_row($result);

$Recruiter = $row[0];




if($Recruiter >= 0)
{
$Recruiter++;
	if($Recruiter == 10)
	{
		$Recruiter = -1;
		$query = "UPDATE CW_Users SET skills=skills+1 WHERE id='" . $uid . "'";
		$result = mysql_query($query);
echo <<<_END
<script>
FB.ui(
   {
    method: 'feed',
    link: 'http://apps.facebook.com/crimsonwinter',
    picture: 'http://www.firstagestudios.com/Ldd49cEsTcwfb/assets/achie26.jpg',
    source: 'http://www.firstagestudios.com/Ldd49cEsTcwfb/assets/achie26.jpg',
     name: 'Achievement',
     caption: 'Crimson Winter',
     description: 'I have obtained the Recruiter achievement in Crimson Winter. Can you do the same?'

   },
   function(response) {
     if (response && response.post_id) {
       alert('Post was published.');
     } else {
       alert('Post was not published.');
     }

   }
  );
  </script>
_END;
	}
	$query = "UPDATE CW_Achievements SET achievement26='" . $Recruiter . "' WHERE id='" . $uid . "'";
	$result = mysql_query($query);
}

}
}
mysql_close($db_server);


?>
