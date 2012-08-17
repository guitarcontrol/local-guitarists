<?php
    /*
        close.php
        
        Simply closes a forum so no one else can post replies or edit it. 
        It simply takes 1 parameter:
        
        url.topic
    */
    
    // include our needed files
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    require("functions.php");
    
    // make sure they're logged in
    verify_access($_SESSION["AccessLevel"], 90);
    
    // make sure the topic id was passed
    if (!isset($_GET["topic"])) {
        // stop here
        print "
        <script language=\"JavaScript\">
        alert(\"Please choose a thread to close first. Thanks.\");
        window.close();
        </script>";
        exit();
    }
    
    print "<b style=\"font-family: Tahoma, Verdana, Arial; font-size : 11px; color: #cc3333;\">Updating Thread...</b>";
    
    // update the thread
    $qryClose = $dbConn->query("
        update  topics
        set     bitReply = " . $dbConn->quote($_GET["active"]) . "
        where   ID = " . $dbConn->quote($_GET["topic"]));
    
    // redirect them back
    print "
    <script language=\"JavaScript\">
    alert(\"The thread was successfully updated.\");
    opener.location.href = \"/forum_ggc/view_bb.php?forum=" . $_GET["forum"] . "&thread=" . $_GET["topic"] . "\";
    window.close();
    </script>";
    exit();
?>
