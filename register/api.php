<?php
/*
    api.php
    
    Allow users to register via the GGC website.
*/

// include our needed file(s)
require("/home/gnet/includes/guitarists.net/global_vars.php");
require("gnet_db.php");
require("functions.php");

// set the API key
$apikey = "G!op5mYJ4HtX?Ghl";
$response = array();

// build a local array from the POST array
foreach ($_POST as $key => $value) {
    $response[$key] = $value;
}

// see if any values are missing
if (empty($response["access_token"]) || empty($response["firstname"]) || empty($response["lastname"]) || empty($response["address"]) || empty($response["city"]) || empty($response["state"]) || empty($response["zipcode"]) || empty($response["country"]) || empty($response["email"]) || empty($response["password"])) {
    $response["msg"] = "missing data";
    print trim(json_encode($response));
    exit();
}

// check what we're doing (if any)
if (!empty($response["process"])) {
    $process = $response["process"];
} else {
    $process = "create";
}

// check the access code
if (trim($response['access_token']) != $apikey) {
    $response["msg"] = "authentication failed (" . trim($response['access_token']) . " => " . $apikey . ")";
    print trim(json_encode($response));
    exit();
}

// check the email address
if ($process == "create") {
    $check = $dbConn->getRow("SELECT ID, intFrozen, intBanned FROM `members` WHERE strEmail = " . $dbConn->quote($response['email']) . " LIMIT 1", DB_FETCHMODE_ASSOC);
    if (!empty($check["intFrozen"]) || !empty($check["intBanned"])) {
        $response["msg"] = "cancelled";
        print trim(json_encode($response));
        exit();
    }

    // if they already have an ID, stop
    if (!empty($check["ID"])) {
        $response["msg"] = "active";
        print trim(json_encode($response));
        exit();
    }

    // set the password
    while (!$valid) {
        $username = "guitarist" . rand(1001,99999);
        $check = $dbConn->getRow("SELECT ID FROM `members` WHERE strUsername = " . $dbConn->quote($username) . " LIMIT 1", DB_FETCHMODE_ASSOC);
        if (empty($check["username"])) {
            $response["username"] = $username;
            $valid = 1;
        }
    }

    // add the user into the system
    $add_member = $dbConn->query("
        INSERT INTO `members` (
            ID,
            strFName,
            strLName,
            strUsername,
            strPassword,
            intValidated,
            dateJoined,
            strEmail,
            strPublicEmail
        ) VALUES (
            NULL,
            " . $dbConn->quote($response["firstname"]) . ",
            " . $dbConn->quote($response["lastname"]) . ",
            " . $dbConn->quote($username) . ",
            " . $dbConn->quote(md5($response["password"])) . ",
            1,
            NOW(),
            " . $dbConn->quote($response["email"]) . ",
            " . $dbConn->quote(mask_email($response["email"])) . "
        )");

    // make sure it took
    if (PEAR::isError($add_member)) {
        $response["msg"] = "error";
        print trim(json_encode($response));
        exit();
    }
	
	$intNewId = $dbConn->getRow("SELECT ID FROM `members` WHERE strEmail = " . $dbConn->quote($response["email"]), DB_FETCHMODE_ASSOC);		
	
    // get the last added ID
    $userid = $intNewId["ID"];

    // get the state and country for the user
    $state = $dbConn->getRow("SELECT ID FROM `states` WHERE strAbbr = " . $dbConn->quote($response["state"]) . " LIMIT 1", DB_FETCHMODE_ASSOC);	
	
    if ($response["country"] == "US") {
        $countryid = "213";		
    } else {
        $country = $dbConn->getRow("SELECT ID FROM `countries` WHERE strCountry = " . $dbConn->quote($response["country"]) . " LIMIT 1", DB_FETCHMODE_ASSOC);		
		$countryid = $country["ID"];
    }

    // add the about info
    $about = $dbConn->query("
        INSERT INTO `about` (
            intMemID,
            strCity,
            intState,
            intCountry,
            intGender,
            intAge
        ) values (
            '" . $userid . "',
            '" . trim(ucfirst(strtolower($response["city"]))) . "',
            " . trim($state["ID"]) . ",
            " . trim($countryid) . ",
            1,
            0
        )");
		
    if (PEAR::isError($about)) {
       $response["msg"] = "error";
        print trim(json_encode($response));
        exit();
    }
} else if ($process == "update") {
    // check the password
    if (empty($response["password"]) && empty($response["md5password"])) {
        $response["msg"] = "no password sent";
        print trim(json_encode($response));
        exit();
    }

    // update the users password
    $newpass = (!empty($response["md5password"]) ? trim($response["md5password"]) : md5(trim($response["password"])));
    $sql = "UPDATE `members` SET strPassword = " . $dbConn->quote($newpass) . " WHERE strEmail = " . $dbConn->quote($response["email"]) . " LIMIT 1";
    $update = $dbConn->query($sql);

    // check the result
    if (PEAR::isError($update)) {
        $response["msg"] = "error updating password";
        print trim(json_encode($response));
        exit();
    } else if (!$dbConn->affectedRows()) {
        $response["msg"] = "same password";
        print trim(json_encode($response));
        exit();
    }
} else if ($process == "delete") {
    // update the user as inactive
    $sql = "UPDATE `members` SET intFrozen = 1 WHERE strEmail = " . $dbConn->quote($response["email"]) . " LIMIT 1";
    $update = $dbConn->query($sql);

    // check the result
    if (PEAR::isError($update)) {
        $response["msg"] = "error freezing account";
        print trim(json_encode($response));
        exit();
    }
}

// all set
$response["msg"] = "success";
print trim(json_encode($response));
?>