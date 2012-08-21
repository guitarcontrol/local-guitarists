<?php
    
    /*
        update_email.php
        
        This allows a person to have their password emailed to them, in case they 
        have forgotten it.
    */
    
    // include our needed file(s)
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    
    // see if they're logged in
    verify_access($_SESSION["AccessLevel"], 1);
    
    // see if we need to update the public email
    if (trim($_POST["origPublicEmail"]) != trim($_POST["strPublicEmail"])) {
        // update the public email address
        $qryUpdate = $dbConn->query("
            update    members
            set        strPublicEmail = '" . trim($_POST["strPublicEmail"]) . "'
            where     ID = " . $_POST["ID"]);
    }
    
    // see if we need to update the private email
    if (trim($_POST["origEmail"]) != trim($_POST["strEmail"])) {
        // make sure this email doesn't already exist
        $qryCount = $dbConn->query("
            select    ID
            from    members
            where    strEmail = '" . trim($_POST["strEmail"]) . "'");
        
        // if any were found, stop
        if ($qryCount->numRows()) {
            print "
            <script language=\"javascript\">
            alert(\"An account already exists with this email. Please try again.\");
            history.back();
            </script>";
            exit();
        }
        
        // generate a random registration key to confirm the process
        $arrKeyList = array("48","49","50","51","52","53","54","55","56","57","97","98","99","100","101","102","103","104","105","106","107","108","109","110","111","112","113","114","115","116","117","118","119","120","121","122");
        $regKey = "";
        
        // loop through and create our registration key
        for ($i = 1; $i <= 15; $i++) {
            $pos = rand(0,35);
            $regKey .= strtoupper(chr($arrKeyList[$pos]));
        }
        
        // add this key into the db, so we know that they're changing their own email
        $qryUpdate = $dbConn->query("
            update    members
            set        strRegKey = '" . $regKey . "'
            where    ID = " . $_POST["ID"]);
        
        // get the info from the db for this user
        $qryInfo = $dbConn->getRow("
            select  ID, 
                    strFName, 
                    strUsername, 
                    strPassword, 
                    strEmail 
            from    members 
            where   ID = '" . $_POST["ID"] . "'",
            DB_FETCHMODE_ASSOC);
        
        // build our email message to send to them to confirm the change
        $txtEmail = "Dear " . $qryInfo["strFName"] . ":\n\n";
        $txtEmail .= "You (or someone else) requested that we update your email address from \n";
        $txtEmail .= $qryInfo["strEmail"] . " to " . trim($_POST["strEmail"]) . ". In order to \n";
        $txtEmail .= "complete this request, simply click the link below:\n\n";
        $txtEmail .= "http://www.guitarists.net/process/email.php?id=" . $qryInfo["ID"] . "&email=" . trim($_POST["strEmail"]) . "&key=" . $regKey . "\n";
        $txtEmail .= "- or -\n";
        $txtEmail .= "<a href=\"http://www.guitarists.net/process/email.php?id=" . $qryInfo["ID"] . "&email=" . trim($_POST["strEmail"]) . "&key=" . $regKey . "\">Click here</a>\n\n";
        $txtEmail .= "NOTE:  Watch for line wrapping in this message.\n\n";
        $txtEmail .= "If this a mistake, or you did not make this request, simply delete this \n";
        $txtEmail .= "email with our sincere apologies.  No other action has been taken.\n\n";
        $txtEmail .= "Thanks for using the Guitarists Network.\n\n";
        $txtEmail .= "The Guitarists Network Staff";
        
        // send the email to the user
        mail(trim($qryInfo["strEmail"]),
             "Guitarists.net Email Change Request",
             $txtEmail,
             "From: member.support@guitarists.net\r\n" .
             "Reply-To: member.support@guitarists.net");
    }
    
    // display an alert
    print "
    <script language=\"javascript\">
    alert(\"Your request has been submitted and is being processed. If any\\n\" +
          \"changes were made to your public email, they will appear now.\");
    location.replace(\"index.php\");
    </script>";
    exit();
?>