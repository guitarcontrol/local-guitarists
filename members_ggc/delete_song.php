<?php
    // include our main app file
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    
    // make sure they're logged in
    verify_access($_SESSION["AccessLevel"], 1);
    
    // make sure this user is the one who posted the song
    $qryUser = $dbConn->query("
        select    ID
        from    music
        where    ID = " . $dbConn->quote($_GET["id"]) . " and
                MemberID = " . $_SESSION["MemberID"]);
    
    // make sure we found results
    if (!$qryUser->numRows()) {
        print "
        <script language=\"JavaScript\">
        alert(\"You did not post this song.  Please try again.\");
        history.back();
        </script>\n";
        exit();
    }
    
    // delete the song from the db
    $qryDelete = $dbConn->query("
        delete
        from    music
        where    ID = " . $dbConn->quote($_GET["id"]));
    
    // set the return page
    if (isset($_GET["callPage"])) {
        $return = $_GET["callPage"];
    } else {
        $return = "index.php";
    }
    
    // alert them of the status
    if (mysql_affected_rows()) {
        print "
        <script language=\"JavaScript\">
        alert(\"Your song was successfully removed.\");
        location.replace(\"" . $return . "\");
        </script>";
    } else {
        print "
        <script language=\"JavaScript\">
        alert(\"There was an error deleting the song.\");
        location.replace(\"" . $return . "\");
        </script>";
    }
?>