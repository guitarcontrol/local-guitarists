<?php
    /*
        sessions_update.php
        
        Here we'll update the sessions table, based on the passed info.
    */
    
    // include our needed files
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    require("functions.php");
    
    // make sure they're logged in
    verify_access($_SESSION["AccessLevel"], 90);
    
    // depending on the choice made, continue
    if ($_POST["submit"] == "Ban User(s)") {
        // loop through the passed user(s) and ban them
        foreach ($_POST["UserID"] as $userID) {
            // get their IP address from the sessions table to add to 'banned_ips'
            $qryIP = $dbConn->query("
                select  IPAddress
                from    sessions
                where   UserID = " . trim($userID));
            
            // loop through our results, and add it to 'banned_ips' and 'bans' table
            while ($qryRow = $qryIP->fetchRow(DB_FETCHMODE_ASSOC)) {
                // ban the IP address
                $qryBanIP = $dbConn->query("
                    insert into banned_ips (
                        strIP,
                        dateAdded
                    ) values (
                        '" . trim($qryRow["IPAddress"]) . "',
                        Now()
                    )");
                
                // ban the user
                $qryBanIP = $dbConn->query("
                    insert into bans (
                        intMemID,
                        intType,
                        strIP,
                        dateAdded
                    ) values (
                        " . $userID . ",
                        0,
                        '" . trim($qryRow["IPAddress"]) . "',
                        Now()
                    )");
            }
            
            // ban them in 'members' so they can't login again
            $qryBan = $dbConn->query("
                update  members
                set     intBanned = 1
                where   ID = " . trim($userID));
            
            // delete the session from the db
            $qryDelete = $dbConn->query("
                delete
                from    sessions
                where   UserID = " . trim($userID));
        }
    } else if ($_POST["submit"] == "Freeze User(s)") {
        // loop through the passed user(s) and freeze them
        foreach ($_POST["UserID"] as $userID) {
            // freeze them in the db
            $qryFreeze = $dbConn->query("
                update  members
                set     intFrozen = 1
                where   ID = " . trim($userID));
            
            // delete the session from the db
            $qryDelete = $dbConn->query("
                delete
                from    sessions
                where   UserID = " . trim($userID));
        }
    } else if ($_POST["submit"] == "Reset User(s)") {
        // loop through the passed user(s) and freeze them
        foreach ($_POST["UserID"] as $userID) {
            // freeze them in the db
            $qryFreeze = $dbConn->query("
                update  members
                set     intFrozen = 0,
                        intBanned = 0
                where   ID = " . trim($userID));
        }
    } else if ($_POST["submit"] == "Close Sessions") {
        // simply delete their session info
        foreach ($_POST["UserID"] as $userID) {
            // delete the session from the db
            $qryDelete = $dbConn->query("
                delete
                from    sessions
                where   UserID = " . trim($userID));
        }
    }
    
    // redirect them
    print "
    <script language=\"JavaScript\">
    alert(\"The sessions were successfully updated.\");
    location.replace(\"panel.php?memid=" . $_POST["memID"] . "\");
    </script>";
?>
