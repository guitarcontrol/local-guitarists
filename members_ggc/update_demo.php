<?php
    
    /*
        update_demo.php
        
        Allows the member to update their info.
        
    */
    
    // include our needed file(s)
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    
    // see if they're logged in
    verify_access($_SESSION["AccessLevel"], 1);
    
    // update the member with the passed demographic data
    if ($_POST["ID"]) {
        $qryUpdate = $dbConn->query("
            update     about
            set     strCity = '" . trim(addslashes($_POST["strCity"])) . "',
                    intState = " . $_POST["intState"] . ",
                    strState = '" . trim(addslashes($_POST["strState"])) . "',
                    strZipCode = '" . trim(addslashes($_POST["strZipCode"])) . "',
                    intCountry = " . trim($_POST["intCountry"]) . ",
                    dateEdited = Now()
            where     intMemID = " . $_POST["ID"]);
    } else {
        $qryInsert = $dbConn->query("
            insert into about (
                intMemID,
                strCity,
                intState,
                strState,
                strZipCode
                intCountry,
                dateEdited
            ) values (
                " . $_POST["ID"] . ",
                '" . addslashes($_POST["strCity"]) . "',
                " . $_POST["intState"] . ",
                '" . addslashes($_POST["strState"]) . "',
                '" . addslashes(trim($_POST["strZipCode"])) . "',
                " . $_POST["intCountry"] . ",
                Now()
            )");
    }
    
    // based on the results, continue
    if (mysql_affected_rows()) {
        print "<script language=\"JavaScript\">
        alert(\"Your information was successfully updated.  Thanks.\");
        location.replace(\"index.php\");
        </script>";
        exit();
    } else {
        print "<script language=\"JavaScript\">
        alert(\"There was an error updating the data.  Please try again.\");
        location.replace(\"index.php\");
        </script>";
        exit();
    }
?>