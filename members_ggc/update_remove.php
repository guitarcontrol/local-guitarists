<?php
    
    /*
        update_remove.php
        
        This updates the passed info in the db for their playing preferences 
        and abilities.
    */
    
    // include our needed file(s)
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    require("12all_db.php");
    
    // see if they're logged in
    verify_access($_SESSION["AccessLevel"], 1);
    
    // make sure they checked the delete option to verify
    if (empty($_POST["deleteMe"])) {
        header("Location: /members/remove.php?status=2");
        exit();
    }
    
    // select the data from the db for this user to verify they are them
    $arrUser = $dbConn->getRow("
        select  ID,
                strUsername,
                strEmail,
                strRegKey
        from    members
        where   ID = '" . $_SESSION["MemberID"] . "'
        limit 1",
        DB_FETCHMODE_ASSOC);
    
    // if they don't match, stop here
    if ($_POST["ID"] != $arrUser["ID"] && $_POST["strRegKey"] != $arrUser["strRegKey"]) {
        header("Location: /members/remove.php?status=1");
        exit();
    }
    
    // begin the process of deleteing their account
    $qryUpdate = $dbConn->query("delete from bans where intMemID = " . $_POST["ID"]);
    $qryUpdate = $dbConn->query("delete from blocked where intMemID = " . $_POST["ID"] . " or intBlockID = " . $_POST["ID"]);
    $qryUpdate = $dbConn->query("delete from cdsignup where intMemID = " . $_POST["ID"]);
    $qryUpdate = $dbConn->query("delete from saved where intMemID = " . $_POST["ID"]);
    $qryUpdate = $dbConn->query("update forums set intLastID = 4 where intLastID = " . $_POST["ID"]);
    $qryUpdate = $dbConn->query("update gear set intMemID = 4 where intMemID = " . $_POST["ID"]);
    $qryUpdate = $dbConn->query("update lessons set intMemID = 4 where intMemID = " . $_POST["ID"]);
    $qryUpdate = $dbConn->query("delete from member_blocks where intMemID = " . $_POST["ID"] . " or intBlockID = " . $_POST["ID"]);
    $qryUpdate = $dbConn->query("update messages set intMemID = 4 where intMemID = " . $_POST["ID"]);
    $qryUpdate = $dbConn->query("update messages set intRecipient = 4 where intRecipient = " . $_POST["ID"]);
    $qryUpdate = $dbConn->query("update msg_main set intMemID = 4 where intMemID = " . $_POST["ID"]);
    $qryUpdate = $dbConn->query("update msg_main set intRecipient = 4 where intRecipient = " . $_POST["ID"]);
    $qryUpdate = $dbConn->query("update msg_replies set intMemID = 4 where intMemID = " . $_POST["ID"]);
    $qryUpdate = $dbConn->query("update msg_replies set intRecipient = 4 where intRecipient = " . $_POST["ID"]);
    $qryUpdate = $dbConn->query("delete from news where memberID = " . $_POST["ID"]);
    $qryUpdate = $dbConn->query("update newtab set intMemID = 2 where intMemID = " . $_POST["ID"]);
    $qryUpdate = $dbConn->query("update tablature set intMemID = 2 where intMemID = " . $_POST["ID"]);
    $qryUpdate = $dbConn->query("update ratings set intMemID = 4 where intMemID = " . $_POST["ID"]);
    $qryUpdate = $dbConn->query("update replies set intMemID = 4 where intMemID = " . $_POST["ID"]);
    $qryUpdate = $dbConn->query("update resources set intMemID = 4 where intMemID = " . $_POST["ID"]);
    $qryUpdate = $dbConn->query("delete from saved where intMemID = " . $_POST["ID"] . " or (intType = 4 and intItem = " . $_POST["ID"] . ")");
    $qryUpdate = $dbConn->query("update software set intMemID = 4 where intMemID = " . $_POST["ID"]);
    $qryUpdate = $dbConn->query("update topics set intMemID = 4 where intMemID = " . $_POST["ID"]);
    $qryUpdate = $dbConn->query("update tunings set intMemID = 4 where intMemID = " . $_POST["ID"]);
    $qryUpdate = $dbConn->query("delete from about where intMemID = " . $_POST["ID"]);
    $qryUpdate = $dbConn->query("delete from members where ID = " . $_POST["ID"]);
    
    // add the ID back into available
    $qryAdd = $dbConn->query("insert into available ( intID ) values ( '" . $_POST["ID"] . "' )");
    
    // remove their email from the 12all db
    $qryDelete = $db12All->query("
        delete
        from    12all_listmembers
        where   email = '" . trim($arrUser["strEmail"]) . "' and
                nl = 1");
    
    // remove their cookie
    setcookie("MEMID", "", time() - 3600, "/");
    
    // unset and remove their session
    session_unset();
    session_destroy();
    
    // all done!
    print "
    <script language=\"javascript\">
    alert(\"Your account was successfully removed. Thanks.\");
    location.replace(\"/logout.php?path=/index.php\");
    </script>";
    exit();
?>
