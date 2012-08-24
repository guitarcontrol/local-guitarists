<?php
    /*
        process.php
        
        Here we take the submission, add it to the database, and then email 
        the admins that it has been added.
    */
    
    // include our needed file(s)
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    require("functions.php");
    
    // make sure they don't use foul language
    $curseName = curseFilter($_POST["Title"]);
    $curseDesc = curseFilter($_POST["Blurb"]);
    $curseDesc = curseFilter($_POST["Description"]);
    
    // add this new lesson to the database
    $qryAdd = $dbConn->query("
        update    music
        set        CategoryID = " . $_POST["CategoryID"] . ",
                Title = '" . trim(addslashes($_POST["Title"])) . "',
                Blurb = '" . trim(addslashes($_POST["Blurb"])) . "',
                Description = '" . trim(addslashes(strip_tags($_POST["Description"]))) . "',
                GearUsed = '" . trim(addslashes(strip_tags($_POST["GearUsed"]))) . "',
                FileSize = '" . trim($_POST["FileSize"]) . "',
                BitRate = " . $_POST["BitRate"] . ",
                SongURL = '" . trim($_POST["SongURL"]) . "',
                ImageURL = '" . trim($_POST["ImageURL"]) . "',
                Active = " . $_POST["Active"] . "
        where    ID = " . $_POST["ID"]);
    
    // alert them of the status
    if (mysql_affected_rows()) {
        print "
        <script language=\"JavaScript\">
        alert(\"Your song was successfully updated.\");
        location.replace(\"/members_ggc/index.php\");
        </script>";
    } else {
        print "
        <script language=\"JavaScript\">
        alert(\"Your song was not successfully updated.  This could be\\n\" +
              \"because of an error, or no data was changed.\");
        location.replace(\"/members_ggc/index.php\");
        </script>";
    }
?>