<?php
// post data
$ggc_members_form = array
(  	
  "access_token" => "G!op5mYJ4HtX?Ghl" /*this is the current access_token*/,
  "firstname" => "Tony",
  "lastname" => "Shark",
  "address" => "somewhere there",
  "city" => "Long Island",
  "state" => "New York", 
  "zipcode" => "11727",
  "country" => "United States",
  "email" => "TS@sharkindustries2",
   "password" => "testpwd"
);

$fields = "";
foreach( $ggc_members_form as $key => $value ) $fields .= "$key=" . urlencode( $value ) . "&";
 
$ch = curl_init("http://www.guitarists.net/register/api.php"); 
curl_setopt($ch, CURLOPT_HEADER, 0); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt($ch, CURLOPT_POSTFIELDS, rtrim( $fields, "& " )); // use HTTP POST to send form data

$resp = curl_exec($ch); //execute post and get results
curl_close ($ch);

echo $resp;

/* 
$api_return = json_decode($resp);
 
if($api_return->{"msg"} == "success")
{
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
	
	echo $api_return->{"msg"};
}else
{
	//GGC membership failed
	echo $api_return->{"msg"};	
}
*/
?>