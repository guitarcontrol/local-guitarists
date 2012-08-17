<?php
    /*
        subscribe.php
        
        This allows a member to subscribe to a topic from the discussion forum. We'll 
        save it into the 'saved' table. Then, we can simply query that table to see 
        if/when it has been updated. If it has, we'll email everyone that has asked 
        to be notified. It takes 2 variables:
        
        url.thread: the ID of the topic to subscribe to
        url.status: wether to add it (1) or remove it (0) from our saved list
    */
    
    // include our needed file(s)
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    require("functions.php");
    
    // make sure the user is logged in
    verify_access($_SESSION["AccessLevel"], 1);
    
    // make sure the topic id was passed
    if (!isset($_GET["thread"]) || !isset($_GET["status"])) {
        // stop here
        print "
        <script language=\"JavaScript\">
        alert(\"Your link does not appear to be valid.  Please try again.\");
        window.close();
        </script>";
        exit();
    }
    
    print "<b style=\"font-family: Tahoma, Verdana, Arial; font-size : 11px; color: #cc3333;\">Processing your request...</b>";
    
    // set our return URL
    $strURL = "/forum/view_bb.php?forum=" . $_GET["forum"] . "&thread=" . $_GET["thread"];
    
    // process the request
    saveItem('2', $_GET["thread"], $_SESSION["MemberID"], $_GET["status"], $dbConn);
    
    // all done
    print "
    <script language=\"JavaScript\">
    opener.location.href=\"" . $strURL . "\";
    window.close();
    </script>";
?>