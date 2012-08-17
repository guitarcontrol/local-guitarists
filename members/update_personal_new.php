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
    require("12all_db.php");
    require("classes/class_input_filter.php");
    
    // create our input filter object
    $filter = new InputFilter();
    
    // see if we need to upload any files
    if (!empty($_FILES["img_photo"]) || !empty($_FILES["img_avatar"])) {
        require("classes/class_upload.php");
    }
    
    // see if they're logged in
    verify_access($_SESSION["AccessLevel"], 1);
    
    // make sure they didn't curse in their name
    // if they cursed at all, stop
    if (curseFilter($_POST["strFName"]) || curseFilter($_POST["strLName"])) {
        print "
        <script language=\"JavaScript\">
        alert(\"Please refrain from using adult language in your info.\");
        history.back();
        </script>";
        exit();
    }
    
    // see if they have updated the email option
    if ($_POST["intSendEmail"]) {
        // see if they're in the 12all db
        $qryEmail = $db12All->query("
            select  ID
            from    12all_listmembers
            where   email = '" . trim($_POST["strEmail"]) . "' and
                    nl = 1");
        
        // if no record was found, add them into the db
        if (!$qryEmail->numRows()) {
            // add them to the db
            $qryAddEmail = $db12All->query("
                insert into 12all_listmembers (
                    sip,
                    comp,
                    sdate,
                    email,
                    name,
                    nl,
                    stime
                ) values (
                    'Imported',
                    'Imported',
                    '" . date("Y-m-d") . "',
                    '" . trim($filter->process($_POST["strEmail"])) . "',
                    '" . trim($filter->process($_POST["strUsername"])) . "',
                    '1',
                    '" . date("H:i:s") . "'
                )");
        }
    } else {
        // delete all records from the db for this email
        $qryDelete = $db12All->query("
            delete
            from    12all_listmembers
            where   email = '" . trim($filter->process($_POST["strEmail"])) . "' and
                    nl = 1");
    }
    
    // update 'members' with the passed data
    $qryUpdate = $dbConn->query("
        update  members
        set     strFName = '" . trim($filter->process($_POST["strFName"])) . "',
                strLName = '" . trim($filter->process($_POST["strLName"])) . "',
                intSendEmail = '" . $filter->process($_POST["intSendEmail"]) . "',
                FontID = '" . $filter->process($_POST["FontID"]) . "',
                FontSize = '" . $filter->process($_POST["FontSize"]) . "',
                intHideAds = '" . $filter->process($_POST["intHideAds"]) . "',
                intPrivate = '" . $filter->process($_POST["intPrivate"]) . "',
                dateEdited = Now()
        where  ID = '" . $filter->process($_POST["ID"]) . "'");
    
    // update the 'about' table with the passed info
    $qryAbout = $dbConn->query("
        update  about
        set     intGender = '" . $filter->process($_POST["intGender"]) . "',
                intAge = '" . $filter->process($_POST["intAge"]) . "',
                dateEdited = Now()
        where  intMemID = '" . $filter->process($_POST["ID"]) . "'");
    
    // see if we need to upload any files
    if (!empty($_FILES["img_photo"]["name"])) {
        // grab all images in the db for this user
        $qryFiles = $dbConn->query("select id, filename from `files` where uid = '" . $_SESSION["MemberID"] . "' and filetype = 'photo'");
        
        // loop through our results and delete the file(s)
        while ($qryRow = $qryFiles->fetchRow(DB_FETCHMODE_ASSOC)) {
            // delete the file from the db
            $qryDelete = $dbConn->query("delete from files where id = '" . $qryRow["id"] . "' limit 1");
            
            // delete the file from the server
            unlink("/home/gnet/public_html/files/" . $qryRow["filename"]);
        }
        
        // create our upload instance
        $myPhoto = new Upload($_FILES['img_photo']);
        
        // save uploaded image with a new name, resized to 100px wide
        $myPhoto->file_new_name_body = 'member_photo_' . $_SESSION["MemberID"];
        $myPhoto->allowed = array('image/*');
        
        // see if we need to resize
        if ($myPhoto->image_src_x > 500) {
            // resize the image
            $myPhoto->image_resize = true;
            $myPhoto->image_x = 500;
            $myPhoto->image_ratio_y = true;
        }
        
        // process the file
        $myPhoto->Process('/home/gnet/public_html/files/');
        
        // if it processed, proceed
        if ($myPhoto->processed) {
            // clean up our file
            $myPhoto->Clean();
            
            // add the file to our 'files' table
            $statement = $dbConn->prepare("INSERT INTO `files` ( filetype, filename, filesize, mimetype, uid, adddate ) VALUES (?, ?, ?, ?, ?, ?)");
            $data = array('photo', $myPhoto->file_dst_name, $myPhoto->file_src_size, $myPhoto->file_src_mime, $_SESSION["MemberID"], date("Y-m-d H:i:s"));
            $dbConn->execute($statement, $data);
            $dbConn->freePrepared($statement);
        }
    } else {
        // see if they want to remove the current image
        if (!empty($_POST["del_img_photo"])) {
            // grab all images in the db for this user
            $qryFiles = $dbConn->query("select id, filename from `files` where uid = '" . $_SESSION["MemberID"] . "' and filetype = 'photo'");
            
            // loop through our results and delete the file(s)
            while ($qryRow = $qryFiles->fetchRow(DB_FETCHMODE_ASSOC)) {
                // delete the file from the db
                $qryDelete = $dbConn->query("delete from files where id = '" . $qryRow["id"] . "' limit 1");
                
                // delete the file from the server
                unlink("/home/gnet/public_html/files/" . $qryRow["filename"]);
            }
        }
    }
    
    // see if we need to upload any files
    if (!empty($_FILES["img_avatar"]["name"])) {
        // grab all images in the db for this user
        $qryFiles = $dbConn->query("select id, filename from `files` where uid = '" . $_SESSION["MemberID"] . "' and filetype = 'avatar'");
        
        // loop through our results and delete the file(s)
        while ($qryRow = $qryFiles->fetchRow(DB_FETCHMODE_ASSOC)) {
            // delete the file from the db
            $qryDelete = $dbConn->query("delete from files where id = '" . $qryRow["id"] . "' limit 1");
            
            // delete the file from the server
            unlink("/home/gnet/public_html/files/" . $qryRow["filename"]);
        }
        
        // create our upload instance
        $myAvatar = new Upload($_FILES['img_avatar']);
        $myAvatar->allowed = array('image/*');
        
        // save uploaded image with a new name, resized to 100px wide
        $myAvatar->file_new_name_body = 'member_avatar_' . $_SESSION["MemberID"];
        
        // see if we need to resize
        if ($myAvatar->image_src_x > 100) {
            // resize the image
            $myAvatar->image_resize = true;
            $myAvatar->image_x = 100;
            $myAvatar->image_y = 100;
        }
        
        // process the file
        $myAvatar->Process('/home/gnet/public_html/files/');
        
        // if it processed, proceed
        if ($myAvatar->processed) {
            // clean up our file
            $myAvatar->Clean();
            
            // add the file to our 'files' table
            $statement = $dbConn->prepare("INSERT INTO `files` ( filetype, filename, filesize, mimetype, uid, adddate ) VALUES (?, ?, ?, ?, ?, ?)");
            $data = array('avatar', $myAvatar->file_dst_name, $myAvatar->file_src_size, $myAvatar->file_src_mime, $_SESSION["MemberID"], date("Y-m-d H:i:s"));
            $dbConn->execute($statement, $data);
            $dbConn->freePrepared($statement);
        }
    } else {
        // see if they want to remove the current image
        if (!empty($_POST["del_img_avatar"])) {
            // grab all images in the db for this user
            $qryFiles = $dbConn->query("select id, filename from `files` where uid = '" . $_SESSION["MemberID"] . "' and filetype = 'avatar'");
            
            // loop through our results and delete the file(s)
            while ($qryRow = $qryFiles->fetchRow(DB_FETCHMODE_ASSOC)) {
                // delete the file from the db
                $qryDelete = $dbConn->query("delete from files where id = '" . $qryRow["id"] . "' limit 1");
                
                // delete the file from the server
                unlink("/home/gnet/public_html/files/" . $qryRow["filename"]);
            }
        }
    }
    
    // update our session variables
    if ($_POST["intHideAds"] != $_SESSION["HideAds"]) {
        $_SESSION["HideAds"] = $_POST["intHideAds"];
    }
    
    // update our session style layout
    $_SESSION["Style"] = array($_POST["FontID"], $_POST["FontSize"]);
    
    // all done!
    print "
    <script language=\"JavaScript\">
    alert(\"Your information was successfully updated.  Thanks.\");
    location.replace(\"personal_new.php\");
    </script>";
?>
