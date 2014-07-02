<?php


if(isset($_POST['myaddress'])){
$myaddress = urlencode($_POST['myaddress']);
//here is the google api url
$url = "http://maps.googleapis.com/maps/api/geocode/json?address=$myaddress&sensor=false";
//get the content from the api using file_get_contents
$getmap = file_get_contents($url); 
//the result is in json format. To decode it use json_decode
$googlemap = json_decode($getmap);
//get the latitute, longitude from the json result by doing a for loop
foreach($googlemap->results as $res){
	 $address = $res->geometry;
	 $latlng = $address->location;
	 $formattedaddress = $res->formatted_address;
}

$latitude = $latlng->lat;
$longitude =  $latlng->lng;
$formattedaddress = urlencode($formattedaddress);
$response = array("address"=>$myaddress,"latitude"=>$latitude,"longitude"=>$longitude,"formattedaddress"=>$formattedaddress);
     
     echo json_encode($response);


}

?>