<?php
    
    /*
        update_playing.php
        
        This updates the passed info in the db for their playing preferences 
        and abilities.
    */
    
    // include our needed file(s)
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    
    // see if they're logged in
    verify_access($_SESSION["AccessLevel"], 1);
    
    // strip any commas from our influences
    $crlf = chr(10);
    $txtInfluences = str_replace(",", $crlf, $_POST["txtInfluences"]);
    
    // update the data in the db
    if ($_POST["ID"]) {
        // update the db
        $qryUpdate = $dbConn->query("
            update  about
            set     txtGear = '" . trim(addslashes($_POST["txtGear"])) . "',
                    intPlayYears = " . $_POST["intPlayYears"] . ",
                    intExperience = " . $_POST["intExperience"] . ",
                    intSongTypes = " . $_POST["intSongTypes"] . ",
                    intSituation = " . $_POST["intSituation"] . ",
                    txtInfluences = '" . trim(addslashes($txtInfluences)) . "',
                    txtComments = '" . trim(addslashes($_POST["txtComments"])) . "',
                    strURL = '" . trim($_POST["strURL"]) . "'
            where     ID = " . $_POST["ID"]);
    } else {
        // insert the new data into the db
        $qryInsert = $dbConn->query("
            insert into about (
                intMemID,
                txtGear,
                intPlayYears,
                intExperience,
                intSongTypes,
                intSituation,
                txtInfluences,
                txtComments,
                strURL,
                dateEdited
            ) values (
                " . $_SESSION["MemberID"] . ",
                '" . trim(addslashes($_POST["txtGear"])) . "',
                " . $_POST["intPlayYears"] . ",
                " . $_POST["intExperience"] . ",
                " . $_POST["intSongTypes"] . ",
                " . $_POST["intSituation"] . ",
                '" . trim(addslashes($txtInfluences)) . "',
                '" . trim(addslashes($_POST["txtComments"])) . "',
                '" . trim($_POST["strURL"]) . "',
                Now()
            )");
    }
    
    // delete all of their current styles from the db
    $qryDelete = $dbConn->query("
        delete
        from    member_styles
        where   memid = '" . $_SESSION["MemberID"] . "'");
    
    // loop through and add all of the new ones
    if (!empty($_POST["styles"])) {
        foreach ($_POST["styles"] as $style) {
            $qryAdd = $dbConn->query("
                insert into member_styles (
                    memid,
                    styleid
                ) values (
                    '" . $_SESSION["MemberID"] . "',
                    '" . $style . "'
                )");
        }
    }
    
    // all done!
    print "
    <script language=\"javascript\">
    alert(\"Your information was successfully updated. Thanks.\");
    location.replace(\"index.php\");
    </script>";
    exit();
?>
