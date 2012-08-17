<?php
    /*
        email.php
        
        This script queries the db and emails the chosen users an email that tells 
        them that a discussion that they have subscribed to has an update. We include 
        it so we can easily turn it off.
        
    */
    
    // query the db for anyone that has subscribed to this thread
    $qryPeeps = $dbConn->query("
        select  saved.intMemID,
                members.strEmail
        from    saved,
                members
        where   saved.intType = 2 and
                saved.intItem = " . $_POST["intTopic"] . " and
                saved.intMemID != " . $_SESSION["MemberID"] . " and
                saved.intMemID = members.ID");
    
    // if any records were found, continue
    if ($qryPeeps->numRows()) {
        // create our email title
        $emailTitle = "Guitarists.net Forum Update - '" . stripslashes($qryTopic["strTitle"]) . "'";
        
        // build the email message
        $emailTxt = "Dear G-Net Member:\n\n";
        $emailTxt .= "RE: " . $qryTopic["strTitle"] . "\n\n";
        $emailTxt .= "The topic listed above that you have subscribed to has been updated as\n";
        $emailTxt .= "of " . date("n/j/Y \@ g:i A") . ". You can follow the link below to view it:\n\n";
        $emailTxt .= "http://www.guitarists.net/forum_ggc/view_bb.php?forum=" . $qryCount["ID"] . "&thread=" . $_POST["intTopic"] . "\n\n";
        $emailTxt .= "If you wish to unsubscribe from this thread, simply choose the selection for\n";
        $emailTxt .= "the options menu at the top or bottom of the thread view.\n\n";
        $emailTxt .= "Please do NOT reply to this email.\n\n";
        $emailTxt .= "Thanks!\n\n";
        $emailTxt .= "The Guitarists Network\n";
        $emailTxt .= "http://www.guitarists.net/\n";
        
        // loop through our query results, and send the email
        while ($qryMail = $qryPeeps->fetchRow(DB_FETCHMODE_ASSOC)) {
            // send the email to our subscribers
            @mail($qryMail["strEmail"],
                  $emailTitle,
                  $emailTxt,
                  "From: no-reply@guitarists.net\r\n" .
                  "Reply-To: no-reply@guitarists.net");
        }
    }
    
?>
