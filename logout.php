<?php
    /*
        logout.php
        
        Simply ends a users session.  We'll destroy the session and it's data, 
        and then redirect the user to login again.
    */
    
    // include our session code
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    
    // unset the session and destroy it
    setcookie("MEMID", "", time() - 3600, "/");
    session_unset();
    session_destroy();
    
    // based on the path passed, redirect them
    if (isset($_GET["path"]) && $_GET["path"]) {
        $redirURL = $_GET["path"];
    } else {
        $redirURL = "/index.php";
    }
    
    // redirect them
    print "
    <script language=\"JavaScript\">location.replace(\"" . $redirURL . "\");</script>\n";
    exit();
?>