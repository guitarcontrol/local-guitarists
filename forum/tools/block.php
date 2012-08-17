<?php
    /*
        block.php
        
        This script allows a user to block various members posts from being viewed. We'll 
        make sure the id was passed, and the activity (block or unblock).
    */
    
    // include our needed files
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    require("functions.php");
    
    // make sure they're logged in
    verify_access($_SESSION["AccessLevel"], 1);
    
    // make sure our variables exist
    if (!isset($_GET["id"]) || !isset($_GET["ban"])) {
        print "
        <script language=\"JavaScript\">
        alert(\"Please choose a member to block first.\");
        window.close();
        </script>";
        exit();
    }
    
    print "<b style=\"font-family: Tahoma, Verdana, Arial; font-size : 11px; color: #cc3333;\">Processing Request...</b>";
    
    // build our URL to redirect to
    $strURL = "/forum/view_bb.php?forum=" . $_GET["forum"] . "&thread=" . $_GET["topic"];
    
    // proceed, based on the action chosen
    if ($_GET["ban"]) {
        // make sure we haven't already blocked them yet
        $qryExists = $dbConn->getRow("
            select  count(intBlockID) as totals
            from    blocked
            where   intBlockID = " . $dbConn->quote($_GET["id"]) . " and
                    intMemID = '" . $_SESSION["MemberID"] . "'",
            DB_FETCHMODE_ASSOC);
        
        // see what was found
        if ($qryExists["totals"]) {
            print "
            <script language=\"JavaScript\">
            alert(\"You already have this member blocked.  Try refreshing the page.\");
            window.close();
            </script>";
            exit();
        } else {
            // block them
            $qryAdd = $dbConn->query("
                insert into blocked (
                    intMemID,
                    intBlockID
                ) values (
                    " . $_SESSION["MemberID"] . ",
                    " . $dbConn->quote($_GET["id"]) . "
                )");
            
            // all done!
            print "
            <script language=\"JavaScript\">
            alert(\"The user was successfully blocked.\");
            opener.location.href = '" . $strURL . "';
            window.close();
            </script>";
            exit();
        }
    } else {
        // unblock them
        $qryAdd = $dbConn->query("
            delete
            from    blocked
            where   intBlockID = " . $dbConn->quote($_GET["id"]) . " and
                    intMemID = " . $_SESSION["MemberID"]);
        
        // all done!
        print "
        <script language=\"JavaScript\">
        alert(\"The user was successfully unblocked.\");
        opener.location.href = '" . $strURL . "';
        window.close();
        </script>";
        exit();
    }
?>