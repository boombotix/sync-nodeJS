<?php

require_once 'Soundcloud.php';

$client = new Services_Soundcloud(
  '7e941308433d4c5a50b07e9c1cdae8e4', '8a50f701351de52b991caa7b03414c7a', 'http://boombotix.clicklabs.in/v1/soundcloud/');

//header("Location: " . $client->getAuthorizeUrl());

$authurl = $client->getAuthorizeUrl();

echo '<pre>';
echo '<a href ="'.$authurl.'">Connect to sand cloud</a> ';


$code = $_GET['code'];


try {
   $access_token = $client->accessToken($code);
   print_r($access_token);
   
} catch (Services_Soundcloud_Invalid_Http_Response_Code_Exception $e) {
    exit($e->getMessage());
}
die();
//
//
$accessToken = $access_token['access_token'];

$client = new Services_Soundcloud('7e941308433d4c5a50b07e9c1cdae8e4');
//
// find all sounds of buskers licensed under 'creative commons share alike'
$tracks = $client->get('tracks', array('q' => 'buskers', 'license' => 'cc-by-sa'));

//echo "<pre>";
//
//// die(json_encode(array(
////                "Data" => $tracks
////                            ), JSON_PRETTY_PRINT));
//
//
//
//print_r($tracks);
//die();

$query = "light";

$Url  = "http://api.soundcloud.com/tracks.json?q=".$query."&client_id=7e941308433d4c5a50b07e9c1cdae8e4";

//$Url  = "http://api.soundcloud.com/playlists/405726.json?client_id=7e941308433d4c5a50b07e9c1cdae8e4";

//$Url = "https://api.soundcloud.com/tracks/101323352/stream?client_id=b45b1aa10f1ac2941910a7f0d10f8e28";   
$ch = curl_init();
 
    // Now set some options (most are optional)
 
    // Set URL to download
    curl_setopt($ch, CURLOPT_URL, $Url);
 
 
    // Include header in result? (0 = yes, 1 = no)
    curl_setopt($ch, CURLOPT_HEADER, true);
 
    // Should cURL return or print out the data? (true = return, false = print)
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
    //CURLOPT_HTTPHEADER => array('Accept: application/json','Authorization: OAuth '.$token));
 
    // Download the given URL, and return output
    $output = curl_exec($ch);
 
    // Close the cURL resource, and free system resources
    curl_close($ch);
 
    echo "<pre>";
    print_r ($output);



?>