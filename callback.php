<?php
// Copyright 2004-2011 Facebook. All Rights Reserved.
// Modified 2011 by Michael C. A. Patoine
// The file for making cash transactions
/**
   * You should reference http://developers.facebook.com/docs/creditsapi as you
   * familiarize yourself with callback.php. In particular, read all the steps
   * under "Callback Flow in Detail".
   *
   * Your application needs the following inputs and outputs
   *
   * @param int order_id             
   * @param string status
   * @param string method
   * @param array order_details (JSON-encoded)
   *
   * @return array A JSON-encoded array with order_id, next_state (optional: error code, comments)
   */

// Enter your app information below 
$app_id = APP_ID;
$api_key = API_KEY;
$secret = APP_SECRET;

require_once 'src/facebook.php';
require_once 'db_vars.php';
require_once 'functions.php';
require_once 'cw_vars.php';

$facebook = new Facebook(array(
  'appId' => APP_ID,
  'secret' => APP_SECRET,
  'cookie' => true,
));

// prepare the return data array
$data = array('content' => array());

$session = $facebook->getSession();

$me = null;
// Session based API call.
if ($session) {
  try {
    $FB_User = $facebook->getUser();
    $me = $facebook->api('/me');
    $uid = $me['id'];
  } catch (FacebookApiException $e) {
    error_log($e);
  }
}

// parse signed data
$request = parse_signed_request($_REQUEST['signed_request'], $secret);

if ($request == null) {
  // handle an unauthenticated request here
}

$payload = $request['credits'];

// retrieve all params passed in
$func = $_REQUEST['method'];
$order_id = $payload['order_id'];

if ($func == 'payments_status_update') {
  $status = $payload['status'];

  // write your logic here, determine the state you wanna move to
  if ($status == 'placed') {
    $next_state = 'settled';
    $data['content']['status'] = $next_state;
    }
    else if($status == 'settled')
    {
    $details = stripcslashes($payload['order_details']);
    $details = json_decode($details, true);
//a 20 gem sale
   if($details['items'][0]['item_id'] == 1)
{
$db_server = mysql_connect(DB_HOST, DB_USER, DB_PW);

if(!$db_server) die("Unable to connect to MySQL: " . mysql_error());

mysql_select_db(DB_NAME) or die("Unable to select database: " . mysql_error());

//$query = "UPDATE CW_Users SET gems=gems+20 WHERE id='" . $uid . "'";
//sale
$query = "UPDATE CW_Users SET gems=gems+24 WHERE id='" . $uid . "'";
$result = mysql_query($query);

//send an email notifying us that a sale was made
$subject="You made a $5 sale in Crimson Winter";
$headers= "From: MoneyFairy@firstagestudios.com \n";
 $headers.='Content-type: text/html; charset=iso-8859-1';
mail("info@firstagestudios.com", $subject,  "
<html>
<head>
 <title>Gem Purchase</title>
</head>
<body>
Somebody bought $5 worth of gems in Crimson Winter.  Congrats!
</body>
</html>" , $headers);
$isOpen = 1;
}
//a 120 gem sale
   else if($details['items'][0]['item_id'] == 2)
{
$db_server = mysql_connect(DB_HOST, DB_USER, DB_PW);

if(!$db_server) die("Unable to connect to MySQL: " . mysql_error());

mysql_select_db(DB_NAME) or die("Unable to select database: " . mysql_error());

//$query = "UPDATE CW_Users SET gems=gems+120 WHERE id='" . $uid . "'";
//sale
$query = "UPDATE CW_Users SET gems=gems+144 WHERE id='" . $uid . "'";
$result = mysql_query($query);

//email to notify us
$subject="You made a $25 sale in Crimson Winter";
$headers= "From: MoneyFairy@firstagestudios.com \n";
 $headers.='Content-type: text/html; charset=iso-8859-1';
mail("info@firstagestudios.com", $subject,  "
<html>
<head>
 <title>Gem Purchase</title>
</head>
<body>
Somebody bought $25 worth of gems in Crimson Winter.  Congrats!
</body>
</html>" , $headers);

$isOpen = 1;
}

	  $data['content']['order_details'] = $details;
  }

  // compose returning data array_change_key_case
  $data['content']['order_id'] = $order_id;

} else if ($func == 'payments_get_items') {

  // remove escape characters  
  $order_info = stripcslashes($payload['order_info']);
  

    // In the sample credits application we allow the developer to enter the
    // information for easy testing. Please note that this information can be
    // modified by the user if not verified by your callback. When using
    // credits in a production environment be sure to pass an order ID and 
    // contruct item information in the callback, rather than passing it
    // from the parent call in order_info.

    $item_info = json_decode($order_info);
if($item_info == "11a")
{
/*
     $item['item_id'] = 1;
     $item['title'] = '20 gems';
     $item['price'] = 50;
     $item['description'] = '20 gems to spend on Crimson Winter';
     $item['image_url'] = 'http://www.firstagestudios.com/Ldd49cEsTcwfb/assets/GemIcon1.png';
     $item['product_url'] = 'http://www.firstagestudios.com/Ldd49cEsTcwfb/assets/GemIcon1.png';
     */
     //sale info
     $item['item_id'] = 1;
     $item['title'] = '24 gems';
     $item['price'] = 50;
     $item['description'] = '24 gems to spend on Crimson Winter';
     $item['image_url'] = 'http://www.firstagestudios.com/Ldd49cEsTcwfb/assets/GemIcon1.png';
     $item['product_url'] = 'http://www.firstagestudios.com/Ldd49cEsTcwfb/assets/GemIcon1.png';
}
     else if($item_info == "22b")
{
/*
     $item['item_id'] = 2;
     $item['title'] = '120 gems';
     $item['price'] = 250;
     $item['description'] = '120 gems to spend on Crimson Winter for ' . $uid;
     $item['image_url'] = 'http://www.firstagestudios.com/Ldd49cEsTcwfb/assets/GemIcon1.png';
     $item['product_url'] = 'http://www.firstagestudios.com/Ldd49cEsTcwfb/assets/GemIcon1.png';
     */
     $item['item_id'] = 2;
     $item['title'] = '144 gems';
     $item['price'] = 250;
     $item['description'] = '144 gems to spend on Crimson Winter for ' . $uid;
     $item['image_url'] = 'http://www.firstagestudios.com/Ldd49cEsTcwfb/assets/GemIcon1.png';
     $item['product_url'] = 'http://www.firstagestudios.com/Ldd49cEsTcwfb/assets/GemIcon1.png';
}
    
    else 
$item = 0;

    // for url fields, if not prefixed by http://, prefix them
    $url_key = array('product_url', 'image_url');  
    foreach ($url_key as $key) {
      if (substr($item[$key], 0, 7) != 'http://') {
        $item[$key] = 'http://'.$item[$key];
      }
    }

    // prefix test-mode
    if (isset($payload['test_mode'])) {
       $update_keys = array('title', 'description');
       foreach ($update_keys as $key) {
         $item[$key] = '[Test Mode] '.$item[$key];
       }
     }
 


  // Put the associate array of item details in an array, and return in the
  // 'content' portion of the callback payload.
  $data['content'] = array($item);
}

// required by api_fetch_response()
$data['method'] = $func;

// send data back
echo json_encode($data);

// you can find the following functions and more details
// on http://developers.facebook.com/docs/authentication/canvas
function parse_signed_request($signed_request, $secret) {
  list($encoded_sig, $payload) = explode('.', $signed_request, 2);

  // decode the data
  $sig = base64_url_decode($encoded_sig);
  $data = json_decode(base64_url_decode($payload), true);

  if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
    error_log('Unknown algorithm. Expected HMAC-SHA256');
    return null;
  }

  // check signature
  $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
  if ($sig !== $expected_sig) {
    error_log('Bad Signed JSON signature!');
    return null;
  }

  return $data;
}
if($isOpen == 1)
mysql_close($db_server);
function base64_url_decode($input) {
  return base64_decode(strtr($input, '-_', '+/'));
}
