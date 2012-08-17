<?php
    /*
        delete.php
        
        Simply delete child/parent threads, based on the ID's passed.
        
    */
    
    // include our needed files
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    require("functions.php");
    
    // make sure they're logged in
    verify_access($_SESSION["AccessLevel"], 1);
    
    // make sure an ID was passed some way
    if (!isset($_POST["delID"]) && !isset($_GET["id"]) && !isset($_GET["reply"])) {
        print "
        <script language=\"JavaScript\">
        alert(\"Please choose an item to delete first.\");
        history.back();
        </script>";
        exit();
    }
    
    // continue, based on where we are
    if (isset($_POST["delID"])) {
        // set a list of ID's from our array
        $strIDs = implode(",", $_POST["delID"]);
        
        // delete all of our replies
        $qryReplies = $dbConn->query("
            delete
            from    msg_replies
            where   intParent IN ( " . $strIDs . " )");
        
        // delete our main topics
        $qryMain = $dbConn->query("
            delete
            from    msg_main
            where    ID IN ( " . $strIDs . " )");
        
        // set our redirect URL
        $strURL = "/forum/msgs/index.php";
    }
    
    if (isset($_GET["id"])) {
        // delete the child threads
        $qryReplies = $dbConn->query("
            delete
            from    msg_replies
            where    intParent = " . $dbConn->quote($_GET["id"]));
        
        // delete our main thread
        $qryMain = $dbConn->query("
            delete
            from    msg_main
            where    ID = " . $dbConn->quote($_GET["id"]));
        
        // set our redirect URL
        $strURL = "/forum/msgs/index.php";
    }
    
    if (isset($_GET["reply"])) {
        // delete the child threads
        $qryReplies = $dbConn->query("
            delete
            from    msg_replies
            where   ID = " . $dbConn->quote($_GET["reply"]));
        
        // set our redirect URL
        $strURL = "/forum/msgs/view.php?id=" . $_GET["msg"];
    }
    
    // all done!
    print "
    <script language=\"JavaScript\">
    alert(\"The item was successfully deleted.\");
    location.replace(\"" . $strURL . "\");
    </script>";
?>