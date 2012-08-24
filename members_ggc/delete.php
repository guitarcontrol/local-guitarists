<?php
    
    /*
        delete.php
        
        Here we simply delete the saved item for this member.
    */
    
    // include our needed file(s)
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    require("functions.php");
    
    // make sure they're logged in
    verify_access($_SESSION["AccessLevel"], 1);
    
    // make sure an item was passed
    if (!isset($_GET["id"]) || !isset($_GET["type"])) {
        print "
        <script language=\"JavaScript\">
        alert(\"Please choose an item to remove first.\");
        history.back();
        </script>";
        exit();
    }
    
    // process the request
    saveItem($_GET["type"], $_GET["id"], $_SESSION["MemberID"], '0', $dbConn);
    
    // all done!
    print "
    <script language=\"JavaScript\">
    location.replace(\"/members_ggc/saved.php\");
    </script>";
?>