<?php
    /*
        process_invite.php
        
        This page takes the form input from invite.php, saves the valid emails in the db, and then
        emails each user an invitation.
    */
    
    // include our needed file(s)
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    require("functions.php");
    require("classes/class.phpmailer.php");
    
    // see if they're logged in
    verify_access($_SESSION["AccessLevel"], 1);
    
    // create our array of emails to save
    $arrValid = array();
    
    // create an array of our email addresses
    $arrEmails = explode("\n", trim($_POST["emails"]));
    
    // if we have emails in our array, process them
    if (count($arrEmails)) {
        foreach ($arrEmails as $email) {
            // if it's valid, add it to our final array
            if (validate_email(trim($email))) {
                $arrValid[] = trim($email);
            }
        }
    }
    
    // if we have valid emails, process them
    if (count($arrValid)) {
        // set our date for 30 days ago and our default text
        $myDate = time() - (60 * 60 * 24 * 30);
        $htmlText = "";
        $plainText = "";
        
        // query our members that match the date of their last login
        $qryMembers = $dbConn->query("
            select  strUsername,
                    strEmail,
                    dateLVisit
            from    members
            where   dateLVisit >= '" . date("Y-m-d", $myDate) . " 00:00:00' and
                    dateLVisit <= '" . date("Y-m-d", $myDate) . " 23:59:59' and
                    intBanned = 0
            order by strUsername");
        
        // query the # of new songs posted
        $arrMusic = $dbConn->getRow("
            select  COUNT(*) as totals
            from    music
            where   DateAdded > '" . date("Y-m-d", $myDate) . " 23:59:59'",
            DB_FETCHMODE_ASSOC);
        
        // query the # of new songs posted
        $arrFree = $dbConn->getRow("
            select  COUNT(*) as totals
            from    topics
            where   datePosted > '" . date("Y-m-d", $myDate) . " 23:59:59' and
                    intForum = 32",
            DB_FETCHMODE_ASSOC);
        
        // query the # of new songs posted
        $arrOpen = $dbConn->getRow("
            select  COUNT(*) as totals
            from    topics
            where   datePosted > '" . date("Y-m-d", $myDate) . " 23:59:59' and
                    intForum = 11",
            DB_FETCHMODE_ASSOC);
        
        // query the # of new songs posted
        $arrGear = $dbConn->getRow("
            select  COUNT(*) as totals
            from    ratings
            where   dateAdded > '" . date("Y-m-d", $myDate) . " 23:59:59' and
                    intArea = 1",
            DB_FETCHMODE_ASSOC);
        
        // query the # of new songs posted
        $arrLessons = $dbConn->getRow("
            select  COUNT(*) as totals
            from    lessons
            where   dateAdded > '" . date("Y-m-d", $myDate) . " 23:59:59'",
            DB_FETCHMODE_ASSOC);
        
        // query the # of new songs posted
        $arrTabs = $dbConn->getRow("
            select  COUNT(*) as totals
            from    tablature
            where   dateAdded > '" . date("Y-m-d", $myDate) . " 23:59:59'",
            DB_FETCHMODE_ASSOC);
        
        // query the # of members active on the site
        $arrMemCnt = $dbConn->getRow("
            select  COUNT(*) as totals
            from    members
            where   intValidated = 1",
            DB_FETCHMODE_ASSOC);
        
        // append our areas of text if we found new items
        if ($arrMusic["totals"]) {
            $plainText .= "  - " . number_format($arrMusic["totals"]) . " new pieces in \"Our Music\" (http://www.guitarists.net/music/)\n";
            $htmlText .= "<li>" . number_format($arrMusic["totals"]) . " new pieces in \"Our Music\" (http://www.guitarists.net/music/)</li>\n";
        }
        if ($arrFree["totals"]) {
            $plainText .= "  - " . number_format($arrFree["totals"]) . " topics in \"Free For All\" (http://www.guitarists.net/forum/topics.php?forum=32)\n";
            $htmlText .= "<li>" . number_format($arrFree["totals"]) . " topics in \"Free For All\" (http://www.guitarists.net/forum/topics.php?forum=32)</li>\n";
        }
        if ($arrOpen["totals"]) {
            $plainText .= "  - " . number_format($arrOpen["totals"]) . " topics in \"Open Chat\" (http://www.guitarists.net/forum/topics.php?forum=11)\n";
            $htmlText .= "<li>" . number_format($arrOpen["totals"]) . " topics in \"Open Chat\" (http://www.guitarists.net/forum/topics.php?forum=11)</li>\n";
        }
        if ($arrGear["totals"]) {
            $plainText .= "  - " . number_format($arrGear["totals"]) . " new gear ratings (http://www.guitarists.net/gear/)\n";
            $htmlText .= "<li>" . number_format($arrGear["totals"]) . " new gear ratings (http://www.guitarists.net/gear/)</li>\n";
        }
        if ($arrLessons["totals"]) {
            $plainText .= "  - " . number_format($arrLessons["totals"]) . " new lessons (http://www.guitarists.net/lessons/)\n";
            $htmlText .= "<li>" . number_format($arrLessons["totals"]) . " new lessons (http://www.guitarists.net/lessons/)</li>\n";
        }
        if ($arrTabs["totals"]) {
            $plainText .= "  - " . number_format($arrTabs["totals"]) . " new tablature files (http://www.guitarists.net/tab/)\n";
            $htmlText .= "<li>" . number_format($arrTabs["totals"]) . " new tablature files (http://www.guitarists.net/tab/)</li>\n";
        }
        
        // set our mailer options
        $mail = new PHPMailer();
        
        $mail->From     = "member.support@guitarists.net";
        $mail->FromName = "The Guitarists Network";
        $mail->Subject  = "A Personal Invitation To Visit Guitarists.net";
        $mail->Host     = "localhost";
        $mail->Mailer   = "smtp";
        
        // loop through our emails
        foreach ($arrValid as $email) {
            // set our complete text
            $plain = "Dear Friend:\n\nA member here at Guitarists.net (known to us as " . $_POST["strUsername"] . ") thought enough of you (and our site here) to invite you to take a look to see what we have available.  Below is a sample of the items added to our site just in the last 30 days:\n\n" . $plainText . "\nOur membership is growing and we remain committed to bringing the guitar community together by providing the latest in guitar and music news and the opportunity to exchange ideas, techniques and opinions with over " . number_format(round($arrMemCnt["totals"], -3)) . " members world-wide.\n\nWe hope to see you soon!\n\nClaude J. - Owner\nThe Guitarists Network\nhttp://www.guitarists.net/";
            $html = "<p>Dear Friend:</p>\n<p>A member here at Guitarists.net (known to us as " . $_POST["strUsername"] . ") thought enough of you (and our site here) to invite you to take a look to see what we have available.  Below is a sample of the items added to our site just in the last 30 days:</p>\n</ul>\n" . $htmlText . "</ul>\n<p>Our membership is growing and we remain committed to bringing the guitar community together by providing the latest in guitar and music news and the opportunity to exchange ideas, techniques and opinions with over <b>" . number_format(round($arrMemCnt["totals"], -3)) . "</b> members world-wide!</p>\n<p>We hope to see you soon!</p>\n<p>Claude J. - Owner<br />\nThe Guitarists Network<br />\nhttp://www.guitarists.net/</p>";
            
            // set our mail values
            $mail->Body    = $html;
            $mail->AltBody = $plain;
            $mail->AddAddress($email, "Guitarists.net Invitee");
            
            // send the email and print out any error
            if(!$mail->Send()) {
                print "There has been a mail error sending to " . $email . ".<br />\n";
            }
            
            // Clear all addresses for next loop  
            $mail->ClearAddresses();
            
            // add the address to our invitations table
            $qryAdd = $dbConn->query("INSERT INTO `invitations` ( email, uid, invitedate ) VALUES ( '" . $email . "', '" . $_SESSION["MemberID"] . "', NOW() )");
        }
        
        // all done!
        print "
        <script language=\"JavaScript\">
        alert(\"An invitation was emailed to " . count($arrValid) . " user\(s\).  Thanks!\");
        location.replace(\"index.php\");
        </script>";
    } else {
        // all done!
        print "
        <script language=\"JavaScript\">
        alert(\"No valid emails were found.  Please try again.\");
        location.replace(\"invite.php\");
        </script>";
    }
?>