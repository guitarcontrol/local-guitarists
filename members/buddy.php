<?php
    
    /*
        buddy.php
        
        This script allows a member to add certain people to their buddy list.  This 
        makes it easier for people to send IM's to other members.
    */
    
    // include our needed file(s)
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    
    // make sure they're logged in
    verify_access($_SESSION["AccessLevel"], 1);
    
    // see what was passed in the URL
    if (!isset($_GET["id"])) {
        print "
        <script language=\"JavaScript\">
        alert(\"Please choose a member to add to the list first.\");
        history.back();
        </script>";
        exit();
    }
    
    // make sure it's not already saved
    $qryExists = $dbConn->query("
        select    intItem
        from    saved
        where    intItem = " . $dbConn->quote($_GET["id"]) . " and
                intMemID = " . $_SESSION["MemberID"] . " and
                intType = 4");
    
    // continue, based on the results
    if (!$qryExists->numRows()) {
        // add this info into the db and save it for them
        $qrySave = $dbConn->query("
            insert into saved (
                intType,
                intMemID,
                intItem
            ) values (
                4,
                " . $_SESSION["MemberID"] . ",
                " . $dbConn->quote($_GET["id"]) . "
            )");
        
        // all done
        print "
        <script language=\"JavaScript\">
        alert(\"The user was added to your buddy list.\");
        location.replace(\"" . $_GET["return"] . "\");
        </script>";
        exit();
    } else {
        print "
        <script language=\"JavaScript\">
        alert(\"You already have this user in your buddy list.\");
        location.replace(\"" . $_GET["return"] . "\");
        </script>";
        exit();
    }
?>