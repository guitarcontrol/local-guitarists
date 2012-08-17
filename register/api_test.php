<?php

// post data
$gnet_members_form = array(  	
  "access_token" => "G!op5mYJ4HtX?Ghl", // G!op5mYJ4HtX?Ghl
  "firstname"    => "Joel",
  "lastname"     => "Firestone",
  "address"      => "123 Main St.",
  "city"         => "Ocean City",
  "state"        => "Maryland",
  "zipcode"      => "11727",
  "country"      => "United States",
  "email"        => "joel.firestone@gnetconsulting.com",
  "password"     => "TestPass123"
);

$ch = curl_init("http://www.guitarists.net/register/api.php"); 
curl_setopt($ch, CURLOPT_HEADER, 0); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt($ch, CURLOPT_POSTFIELDS, $gnet_members_form); // use HTTP POST to send form data

$resp = curl_exec($ch); //execute post and get results
curl_close ($ch);
print "<pre>\n"; print_r($resp); print "</pre>\n\n"; exit();
$api_return = json_decode($resp);
 
if($api_return->{"msg"} == "success") {
	//GGC membership was created successfully
	//add code for successfully entry
	//return message is on json format
	echo $api_return->{"firstname"};
	echo $api_return->{"lastname"};
	echo $api_return->{"address"};
	echo $api_return->{"city"};
	echo $api_return->{"state"};
	echo $api_return->{"zipcode"};
	echo $api_return->{"country;"};
	echo $api_return->{"email"};
} else {
	//GGC membership failed
	echo $api_return->{"msg"};	
}
?>