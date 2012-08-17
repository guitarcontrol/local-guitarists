<?php
    /*
        validate.php
        
        This is the page a new member will link to to activate their account.  It takes
        the ID and validation code (key) and checks it against the database.
    */
    
    // include our needed file(s)
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    require("functions.php");
    
    // make sure the data was passed in the URL
    if (!isset($_GET["id"]) || !isset($_GET["key"])) {
        print "
        <script language=\"JavaScript\">
        alert(\"Please verify your validation link and try again.\");
        location.replace(\"/index.php\");
        </script>";
        exit();
    }
    
    // query the db, based on this data
    $qryMember = $dbConn->getRow("
        select  m.ID,
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
       a.strCity,
       a.strZipCode,
       s.strName AS state,
       c.strCountry AS country
             FROM members m
      LEFT JOIN about a ON a.intMemID = m.ID
      LEFT JOIN states s ON s.ID = a.intState AND a.intMemID = m.ID
      LEFT JOIN countries c ON c.ID = a.intCountry AND a.intMemID = m.ID
             where   m.ID = "  . trim($dbConn->quote($_GET["id"])) . "
      LIMIT 1", DB_FETCHMODE_ASSOC);
 
    // make sure a record was found
    if (!count($qryMember)) {
        // see if they're not passing the entire validation code
        if (strlen($_GET["key"]) < 15) {
            // the line wrapped, and the entire key isn't being passed
            print "
            <script language=\"JavaScript\">
            alert(\"You\\'re not passing the entire validation key to our server.  This is normally caused\\n\" +
                  \"by your email program \'wrapping\' the lines.  You may need to copy\/paste each line\\n\" +
                  \"of the URL into the address bar of your browser.  Try that, and if you still\\n\" +
                  \"experience problems, let us know.  Thanks.\");
            location.replace(\"/index.php\");
            </script>";
            exit();
        } else {
            // it's simply not there
            print "
            <script language=\"JavaScript\">
            alert(\"You are attempting to validate an account that does not\\n\" +
                  \"exist, or that has already been validated.   Please check \\n\" +
                  \"your email, and try again. If you are pasting the\\n\" +
                  \"URL from an email message, watch out for line wrapping.\");
            location.replace(\"/index.php\");
            </script>";
            exit();
        }
    } else {
        // see if it has already been validated
        if ($qryMember["intValidated"]) {
            print "
            <script language=\"JavaScript\">
            alert(\"You are attempting to validate an account that has already been\\n\" +
                  \"validated. Please feel free to login to your account now.\");
            location.replace(\"/index.php\");
            </script>";
            exit();
        } else {
            // create our SQL text to validate them
            $sqlValText = "
                update   members
                set      intValidated = 1
                where    ID = " . $qryMember["ID"];
            
            // update the db
            $qryUpdate = $dbConn->query($sqlValText);
                      
            //BEGIN API TESTER

            // post data
        $ggc_members_form = array
        (  	
            "access_token" => "GGCkopa56lz09paf" /*this is the current access_token*/,
            "firstname" => $qryMember["strFName"],
            "lastname" => $qryMember["strLName"],
            "address" => "",
            "city" => $qryMember["strCity"],
            "state" => $qryMember["strSate"], 
            "zipcode" => $qryMember["strZipCode"],
            "country" => $country,
            "email" => $qryMember["strEmail"],
            "password" => $qryMember["strPlainText"]
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

        if($api_return->{"msg"} == "success")
        {
	    //GGC membership was created successfully
	    //add code for successfully entry
	    //return message is on json format

        }else
        {
	//GGC membership failed
	    //echo $api_return->{"msg"}; //returns failed message
            //could add stuff here later
        }
        //END API TESTER
        
        //Set plainTextPassword as nul;
        $query = $dbConn->query("
             update  members
             set     strPlainText = NULL,
                    intValidated = 0
             where   ID = " . $qryMember["ID"]);  
        
        
            // if they chose to receive emails, add them to the list
            if ($qryMember["intSendEmail"]) {
                // include our 1-2-all code
                require("12all_db.php");
                
                // add them to the database
                $db12All->query("
                    INSERT INTO `12all_listmembers` (
                        `sip`,
                        `comp`,
                        `sdate`,
                        `email`,
                        `name`,
                        `bounced`,
                        `soft_bounced`,
                        `bounced_d`,
                        `active` ,
                        `nl`,
                        `stime`,
                        `respond`,
                        `last_send`,
                        `no_autoresponders`
                    ) VALUES (
                        '',
                        '',
                        '" . date("Y-m-d") . "',
                        '" . trim(addslashes($qryMember["strEmail"])) . "',
                        '" . trim(addslashes($qryMember["strUsername"])) . "',
                        '0',
                        '0',
                        '0000-00-00',
                        '0',
                        '1',
                        '" . date("H:i:s") . "',
                        '',
                        '0',
                        '0'
                    )");
            }
            
            // set our session variables
            $_SESSION["MemberID"] = $qryMember["ID"];
            $_SESSION["Username"] = trim($qryMember["strUsername"]);
            $_SESSION["HideAds"] = $qryMember["intHideAds"];
            $_SESSION["AccessLevel"] = $qryMember["intAccess"];
            $_SESSION["LastLogin"] = $qryMember["dateLVisit"];
            
            // all good
            print "
            <script language=\"JavaScript\">
            alert(\"Congratulations!  Your account has been validated, and you are\\n\" +
                  \"now logged in.  Thanks for joining!\");
            location.href(\"/index.php\");
            </script>";
        }
    }
    
?>