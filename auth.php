<?php
$code = $_REQUEST["code"];

    if(empty($code)) {
        header('Location:http://www.facebook.com');
    }
	else
		header('Location:http://apps.facebook.com/crimsonwinter');
    

?>
