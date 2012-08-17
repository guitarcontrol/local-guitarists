<?php
    
    /*
        status.php
        
        Update the members status, based on what was chosen by the mod.
    */
    
    // include our needed files
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    require("functions.php");
    
    // make sure they're logged in
    verify_access($_SESSION["AccessLevel"], 90);
    
    // continue, based on the choice made
    if ($_POST["intStatus"] == 0) {
        // reset them to a normal user
        $qryUpdate = $dbConn->query("
            update    members
            set        intFrozen = 0,
                    intBanned = 0
            where    ID = " . $_POST["ID"]);
        
        // delete any records from 'bans'
        $qryBans = $dbConn->query("
            delete
            from    bans
            where    intMemID = " . $_POST["ID"]);
        
        // send them a message, letting them know it has been reset
        $qryAddPost = $dbConn->query("
        insert into msg_main ( 
            strTitle,
            txtContent,
            intReplies,
            intViews,
            intMemID,
            intRecipient,
            intRead,
            dateLastPost,
            dateAdded
        ) values ( 
            'Account Reactivated',
            'Your account has been restored to active status.  You can logout and log back in again to be restored.\n\nThanks.',
            0,
            0,
            '" . $_SESSION["MemberID"] . "',
            '" . $_POST["ID"] . "',
            0,
            Now(),
            Now()
        )");
        
        // all done!
        print "
        <script language=\"JavaScript\">
        alert(\"The member is now back to active status.\");
        location.replace(\"panel.php?memid=" . $_POST["ID"] . "\");
        </script>";
        exit();
    } else if ($_POST["intStatus"] == 1) {
        // the mod has chosen to freeze the account
        $qryUpdate = $dbConn->query("
            update    members
            set        intFrozen = 1
            where    ID = " . $_POST["ID"]);
        
        // add the new item in 'bans'
        $qryBans = $dbConn->query("
            insert into bans (
                intMemID,
                intType,
                dateAdded
            ) values (
                " . $_POST["ID"] . ",
                1,
                Now()
            )");
        
        // add a message in their PM's (if one was supplied
        if (strlen($_POST["reason"])) {
            $qryAddPost = $dbConn->query("
                insert into msg_main ( 
                    strTitle,
                    txtContent,
                    intReplies,
                    intViews,
                    intMemID,
                    intRecipient,
                    intRead,
                    dateLastPost,
                    dateAdded
                ) values ( 
                    'Account Frozen',
                    'Your account has been frozen.\n\nReason:\n" . addslashes(trim($_POST["reason"])) . "',
                    0,
                    0,
                    '" . $_SESSION["MemberID"] . "',
                    '" . $_POST["ID"] . "',
                    0,
                    Now(),
                    Now()
                )");
        }
        
        // all done!
        print "
        <script language=\"JavaScript\">
        alert(\"The member has been marked as frozen.\");
        location.replace(\"panel.php?memid=" . $_POST["ID"] . "\");
        </script>";
        exit();
    } else if ($_POST["intStatus"] == 2) {
        // the mod has chosen to ban the account
        $qryUpdate = $dbConn->query("
            update  members
            set     intBanned = 1
            where   ID = " . $_POST["ID"]);
        
        // get their IP from the db to add it to 'banned_ips'
        $qryIP = $dbConn->getRow("
            select  strIP
            from    members
            where   ID = '" . $_POST["ID"] . "'",
            DB_FETCHMODE_ASSOC);
        
        // add the IP to 'banned_ips'
        $qryBanIP = $dbConn->query("
            insert into banned_ips (
                strIP,
                dateAdded
            ) values (
                '" . $qryIP["strIP"] . "',
                Now()
            )");
        
        // add the new item in 'bans'
        $qryBans = $dbConn->query("
            insert into bans (
                intMemID,
                intType,
                dateAdded
            ) values (
                " . $_POST["ID"] . ",
                0,
                Now()
            )");
        
        // set a date for the last 10 days
        $cutoff = strtotime("-10 days");
        
        // move all of their recent posts to the 'deleted' forum
        $qryUpdate = $dbConn->query("
            update    topics
            set        intForum = 26
            where    intMemID = " . $_POST["ID"] . " and
                    datePosted >= '" . date("Y-m-d", $cutoff) . " 00:00:00'");
        
        // all done!
        print "
        <script language=\"JavaScript\">
        alert(\"The member has been banned.\");
        location.replace(\"panel.php?memid=" . $_POST["ID"] . "\");
        </script>";
        exit();
    }
?>
