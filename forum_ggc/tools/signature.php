<?php
    
    /*
        signature.php
        
        This simply resets the users signature to nothing, and sends them an PM 
        telling them.
    */
    
    // include our needed files
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    require("functions.php");
    
    // make sure they're logged in
    verify_access($_SESSION["AccessLevel"], 90);
    
    // create our compliant signature
    if (strlen($_POST["txtSignature"])) {
        // strip out bad chars
        $txtSig = strip_tags($_POST["txtSignature"]);
        $txtSig = str_replace("{~}", "<li>", $txtSig);
        $txtSig = str_replace("{", "<",$txtSig);
        $txtSig = str_replace("}", ">", $txtSig);
        $txtSig = str_replace("color=", "font color=", $txtSig);
        $txtSig = str_replace("/color", "/font", $txtSig);
        $txtSig = str_replace("link=", "a target=\"_new\" href=", $txtSig);
        
        // convert smilies back
        $txtSig = smilies($txtSig,'0');
    } else {
        $txtSig = "";
    }
    
    // reset the signature
    $qryUpdate = $dbConn->query("
        update    members
        set        txtSignature = '" . addslashes(trim($txtSig)) . "'
        where    ID = " . $_POST["ID"]);
    
    // send them a message (if the mod entered a reason)
    if ($_POST["reason"]) {
        $qryAddPost = $dbConn->query("
            insert into messages ( 
                intParent,
                strTitle,
                txtContent,
                intReplies,
                intViews,
                intMemID,
                intRecipient,
                intLastMem,
                intRead,
                dateLastPost,
                dateAdded
            ) values ( 
                0,
                'Signature Update',
                '" . trim(addslashes($_POST["reason"])) . "',
                0,
                0,
                " . $_SESSION["MemberID"] . ",
                " . $_POST["ID"] . ",
                " . $_SESSION["MemberID"] . ",
                0,
                Now(),
                Now()
            )");
    }
    
    // all done!
    print "
    <script language=\"JavaScript\">
    alert(\"The signature was successfully updated.\");
    location.replace(\"panel.php?memid=" . $_POST["ID"] . "\");
    </script>";
    exit();
?>
