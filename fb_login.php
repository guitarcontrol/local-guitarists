<?php
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("functions.php");
    //require("fbconfig.php");


    $user = $facebook->getUser();
    if(empty($user)){
    header('Location: http://www.guitarists.net/index.php');
}
    $access_token = $facebook->getAccessToken();
    $me = $facebook->api('/me');
print "<!--\n\n"; print_r($me); print "\n-->\n\n";
    // We have an active session; let's check if we've already registered the user
    $result = $dbConn->getRow("SELECT * FROM members WHERE facebookID = '" . $me['id'] . "' LIMIT 1", DB_FETCHMODE_ASSOC);
print "<!--\n\n"; print_r($result); print "\n-->\n\n";
    // If not, let's add it to the database
    if (empty($result)) {
        list($username, $domain) = explode('@', $me['email']);
        $username = (!empty($username) ? trim($username) : "Facbook Member");
        //$username = @strstr($me['email'], '@', true); //Gets before @
        $results = $dbConn->getRow("SELECT * FROM members WHERE strUsername = " . $user, DB_FETCHMODE_ASSOC);
        if (!empty($results))
        {
            //If the user doesnt exists but the username does, add 4 random digits to the end of username
            $username .= rand(0000,9999);
        }
        $string = md5(rand(0,999999)); //Creates random string
        $password = substr($string,0,15);//uses only part of the string
   	$query = $dbConn->query("INSERT INTO members (facebook,facebookID, strFName, strLName,strUsername,strPassword, strEmail, dateJoined, strIP, dateEdited) VALUES ('1', '{$me['id']}', '{$me['first_name']}', '{$me['last_name']}', '$username', '$password', '{$me['email']}', Now(), '{$_SERVER['REMOTE_ADDR']}', NOW())");
    	$result = $dbConn->getRow("SELECT * FROM members WHERE id = " . mysql_insert_id($dbConn->connection) . " LIMIT 1", DB_FETCHMODE_ASSOC);
    	$about = $dbConn->query("INSERT INTO `about` ( ID, intMemID ) VALUES ( NULL, " . $result['ID'] . " )");

        // pass off to the GGC API
        // post data
        $ggc_members_form = array
        (  	
            "access_token" => "GGCkopa56lz09paf" /*this is the current access_token*/,
            "firstname" => $me['first_name'],
            "lastname" => $me['first_name'],
            "address" => "",
            "city" => "",
            "state" => "", 
            "zipcode" => "",
            "country" => "USA",
            "email" => $me['email'],
            "password" => $password
        );

        $fields = "";
        foreach( $ggc_members_form as $key => $value ) $fields .= "$key=" . urlencode( $value ) . "&";
 
        $ch = curl_init("http://ws1.guitargodclub.com/ggc_api.php"); 
        curl_setopt($ch, CURLOPT_HEADER, 0); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, rtrim( $fields, "& " )); // use HTTP POST to send form data

        $resp = curl_exec($ch); //execute post and get results
        curl_close ($ch);
 
        $api_return = json_decode($resp);
    } else {
        $username = trim($result["strUsername"]);
    }

    //Set the session varibales
    $_SESSION["MemberID"] = $result["ID"];
    $_SESSION["Username"] = trim($result["strUsername"]);
    $_SESSION["HideAds"] = $result["intHideAds"];
    $_SESSION["AccessLevel"] = $result["intAccess"];
    $_SESSION["LastLogin"] = $result["dateLVisit"];
    $_SESSION["Style"] = array($result["FontID"], $qryUser["FontSize"]);
    
    if (isset($_POST["rememberMe"])) {
     // set a cookie to remember them
        setcookie("MEMID", $_SESSION["MemberID"], time() + 31536000, "/");
    }

    // update our visit count
    $visits = $result["intVisits"] + 1;
     
    // process the query
    $query = $dbConn->query("
        update  members
        set     dateLVisit = Now(), 
                intVisits = " . $visits . ",
                strIP = '" . $_SERVER["REMOTE_ADDR"] . "',
                intValidated = 1
        where   ID = " . $result["ID"]);

    // set session variables for login_db
    $session_id = session_id(); //Session ID        
    $login_key = generatePassword(20); //Random string with a length of 20
    $_SESSION["login_key"] = $login_key; //Set the login key session variable

    //Insert session_id, email, login_key, and last_hit for the current user to login_db            
    $qryAdd = $dbConnL->query("
    insert into logins (
        session_id,
        email,
        login_key,
        last_hit,
        ip_address
    ) values (
        '" . $session_id . "',
        '" . $result["strEmail"] . "',
        '" . $login_key . "',
        NOW(),
        '" . $_SERVER["REMOTE_ADDR"] . "')
    ");
?>
       <script language="JavaScript">
        location.href='http://www.guitarists.net/index.php';
       </script>