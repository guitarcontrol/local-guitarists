<?php
    /*
        update_personal.php
        
        Simply update the database with the passed info from personal.php.
    */
    
    // include our needed file(s)
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    require("functions.php");
    
    // see if they're logged in
    verify_access($_SESSION["AccessLevel"], 1);
    
    // make sure they didn't curse in their display type or signature
    if (curseFilter($_POST["strAccess"]) || curseFilter($_POST["txtSignature"])) {
        print "
        <script language=\"JavaScript\">
        alert(\"Please refrain from using adult language in your info.\");
        history.back();
        </script>";
        exit();
    }
    
    // create our compliant signature
    if (strlen($_POST["txtSignature"])) {
        // strip out bad chars
        $txtSig = stripslashes(htmlspecialchars(strip_tags($_POST["txtSignature"])));
    } else {
        $txtSig = "";
    }
    
    // update 'members' with the passed data
    $qryUpdate = $dbConn->query("
        update  members
        set     strAIM = '" . trim(addslashes($_POST["strAIM"])) . "',
                strICQ = '" . trim(addslashes($_POST["strICQ"])) . "',
                strMSN = '" . trim(addslashes($_POST["strMSN"])) . "',
                strYahoo = '" . trim(addslashes($_POST["strYahoo"])) . "',
                strAccess = '" . trim(addslashes($_POST["strAccess"])) . "',
                txtSignature = '" . trim(addslashes($txtSig)) . "',
                intAllowChat = '" . $_POST["intAllowChat"] . "',
                intPrivate = '" . $_POST["intPrivate"] . "',
                dateEdited = Now()
        where  ID = '" . $_POST["ID"] . "'");
    
    // update their timezone
    /*$qryUpdate = $dbConn->query("
        update  about
        set     timezone = '" . trim($_POST["timezone"]) . "',
                dateEdited = Now()
        where   intMemID = '" . $_POST["ID"] . "'
        limit 1");*/
    
    // all done!
    print "
    <script language=\"JavaScript\">
    alert(\"Your information was successfully updated.  Thanks.\");
    location.replace(\"index.php\");
    </script>";
?>