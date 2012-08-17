<?php
    
    /*
        warn.php
        
        This simply updates the warning total in the db.  If it's the 3rd 
        strike, then they're banned.
    */
    
    // include our needed files
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    require("functions.php");
    
    // make sure they're logged in
    verify_access($_SESSION["AccessLevel"], 90);
    
    // set the new total
    $intWarnings = $_POST["intWarnings"] + 1;
    
    // if it totals 3, ban them
    if ($intWarnings >= 3) {
        // go ahead and ban them
        $qryUpdate = $dbConn->query("
            update    members
            set        intBanned = 1,
                    intWarnings = 0
            where    ID = " . $_POST["ID"]);
        
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
        
        // all done!
        print "
        <script language=\"JavaScript\">
        alert(\"The member has been banned.\");
        location.replace(\"panel.php?memid=" . $_POST["ID"] . "\");
        </script>";
        exit();
    } else {
        // update the members totals
        $qryUpdate = $dbConn->query("
            update    members
            set        intWarnings = " . $intWarnings . "
            where    ID = " . $_POST["ID"]);
        
        // send them a private message that they have been warned
        if (strlen($_POST["reason"])) {
            $qryAddPost = $dbConn->query("
                insert into messages ( 
                    intParent,
                    strTitle,
                    txtContent,
                    intReplies,
                    intViews,
                    intMemID,
                    intRecipient,
                    intLastMem,
                    intRead,
                    dateLastPost,
                    dateAdded
                ) values ( 
                    0,
                    'WARNING #" . $intWarnings . "',
                    'You now have " . $intWarnings . " warning(s).  When you reach 3, you will be permanently banned from the site.\n\nReason:\n" . addslashes(trim($_POST["reason"])) . "',
                    0,
                    0,
                    " . $_SESSION["MemberID"] . ",
                    " . $_POST["ID"] . ",
                    " . $_SESSION["MemberID"] . ",
                    0,
                    Now(),
                    Now()
                )");
        }
        
        // all done!
        print "
        <script language=\"JavaScript\">
        alert(\"The member has been warned.\");
        location.replace(\"panel.php?memid=" . $_POST["ID"] . "\");
        </script>";
        exit();
    }
?>