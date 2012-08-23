<?php
    
    /*
        update_password.php
        
        Simply enter the new password in the db for this user.
    */
    
    // include our needed file(s)
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    
    // see if they're logged in
    verify_access($_SESSION["AccessLevel"], 1);
    
    // make sure the current password matches the db exactly
    $qryCurrent = $dbConn->getRow("
        select  strPassword
        from    members
        where   ID = " . $dbConn->quote($_POST["ID"]),
        DB_FETCHMODE_ASSOC);
    
    // proceed, based on the value
    if (trim($qryCurrent["strPassword"]) != trim(md5($_POST["password"]))) {
        print "
        <script language=\"javascript\">
        alert(\"Your password does not match the one currently listed in\\n\" +
              \"our database. Your password is case sensitive, so make\\n\" +
              \"sure you do not have Caps Lock on and try again.\");
        history.back();
        </script>";
        exit();
    } else {
        // update the database
        $qryUpdate = $dbConn->query("
            update  members
            set     strPassword = '" . trim(addslashes(md5($_POST["newpassword"]))) . "'
            where  ID = '" . $_POST["ID"] . "'");

        // get the user's data from the system
        $qryMember = $dbConn->getRow("
        SELECT      m.ID,
                    m.strFName,
                    m.strLName,
                    m.strUsername,
                    m.strEmail,
                    m.intHideAds,
                    m.intSendEmail,
                    m.intAccess,
                    m.intValidated,
                    m.dateLVisit,
                    m.strPlainText,
                    a.strAddress,
                    a.strCity,
                    a.strZipCode,
                    s.strName AS state,
                    c.strCountry AS country
        FROM        members m
        LEFT JOIN   about a ON a.intMemID = m.ID
        LEFT JOIN   states s ON s.ID = a.intState AND a.intMemID = m.ID
        LEFT JOIN   countries c ON c.ID = a.intCountry AND a.intMemID = m.ID
        WHERE       m.ID = "  . trim($dbConn->quote($_POST["ID"])) . "
        LIMIT 1", DB_FETCHMODE_ASSOC);

        // update the user in GGC
        if (!PEAR::isError($qryMember) && !empty($qryMember)) {
            $ggc_members_form = array(
                "access_token" => "GGCkopa56lz09paf" /*this is the current access_token*/,
                "firstname"    => $qryMember["strFName"],
                "lastname"     => $qryMember["strLName"],
                "address"      => $qryMember["strAddress"],
                "city"         => $qryMember["strCity"],
                "state"        => $qryMember["strSate"], 
                "zipcode"      => $qryMember["strZipCode"],
                "country"      => $country,
                "email"        => $qryMember["strEmail"],
                "password"     => $qryMember["strPlainText"]
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

            // process here, as needed
        }
    }
    
    // all done!
    print "
    <script language=\"javascript\">
    alert(\"Your password was successfully updated.  Thanks.\");
    location.replace(\"index.php\");
    </script>";
    exit();
?>