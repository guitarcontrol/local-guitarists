<?php
    /*
        personal.php
        
        Allows the member to update their personal info that appears on the site.
        
    */
    
    // include our needed file(s)
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    require("functions.php");
    
    // see if they're logged in
    verify_access($_SESSION["AccessLevel"], 1);
    
    // get our member info we need to edit
    $qryInfo = $dbConn->getRow("
        select  members.ID,
                members.strUsername,
                members.strFName,
                members.strLName,
                members.strEmail,
                members.intSendEmail,
                members.txtSignature,
                members.intHideAds,
                members.intPrivate,
                members.FontID,
                members.FontSize,
                about.intGender,
                about.intAge
        from    members,
                about
        where   members.ID = '" . $_SESSION["MemberID"] . "' and
                members.ID = about.intMemID",
        DB_FETCHMODE_ASSOC);
    
    // stop, if no records were found
    if (!count($qryInfo)) {
        print "
        <script language=\"JavaScript\">
        alert(\"Your data appears to be corrupted.  Please try again, or report\\n\" +
              \"the problem if it continues.\");
        history.back();
        </script>";
        exit();
    }
    
    // get any currently saved photo
    $arrPhoto = $dbConn->getRow("
        select  filename,
                filesize
        from    files
        where   filetype = 'photo' and
                uid = '" . $_SESSION["MemberID"] . "'
        limit 1",
        DB_FETCHMODE_ASSOC);
    
    // get any currently saved avatar
    $arrAvatar = $dbConn->getRow("
        select  filename,
                filesize
        from    files
        where   filetype = 'avatar' and
                uid = '" . $_SESSION["MemberID"] . "'
        limit 1",
        DB_FETCHMODE_ASSOC);
    
    // create our page variables
    $pageTitle = "Members Area: Edit your account information";
    $pageDescription = "";
    $pageKeywords = "";
    $areaName = "members";
    
    // set an array of the ID's that are allowed to turn banners off
    $arrLevels = array("6","11","13","14","20","90","99");
    $arrPrefered = array("20","90","99");
    
    // include our header
    //require("header.php");
?>
    <link type="text/css" rel="stylesheet" href="/inc/styles.css" />
	<style>
    BODY {
        background: none;
    }
    </style>
    <script language="JavaScript" src="/inc/func.js"></script>
    
    <br>
    <table width="100%" cellspacing="0" cellpadding="2" border="0">
    <tr>
        <td class="tablehead">&nbsp;&raquo;&nbsp;<a href="/forum_ggc/index.php"><b>Home</b></a>&nbsp;&raquo;&nbsp;<a href="/members_ggc/index.php"><b>Members Area</b></a>&nbsp;&raquo;&nbsp;Edit your Account Info</td>
    </tr>
    </table>
    
    <div align="center">
    <table width="600" cellspacing="0" cellpadding="1" border="0">
    <form name="myForm" action="update_personal_new.php" method="post" enctype="multipart/form-data" onSubmit="return checkPersonalInfo()">
    <input type="Hidden" name="ID" value="<?php print $_SESSION["MemberID"]; ?>" />    
    <input type="Hidden" name="strEmail" value="<?php print trim($qryInfo["strEmail"]); ?>" />
    <tr>
           <td width="110"><b>Username:</b> </td>
           <td width="490">       
        <input type="text" name="strUsername" value="<?php print trim($qryInfo["strUsername"]); ?>" maxlength="30" size="20" class="input">
        </td>
    </tr>
    <tr>
        <td colspan="2"><br></td>
    </tr>    
    <tr>
           <td width="110"><b>Name:</b> </td>
           <td width="490">
        <input type="text" name="strFName" value="<?php print trim($qryInfo["strFName"]); ?>" maxlength="30" size="20" class="input">
        <input type="text" name="strLName" value="<?php print trim($qryInfo["strLName"]); ?>" maxlength="35" size="25" class="input"></td>
    </tr>
    <tr>
        <td colspan="2"><br></td>
    </tr>
    <tr>
           <td><b>Gender:</b> </td>
           <td>
        <input type="Radio" name="intGender" value="0"<?php if ($qryInfo["intGender"] == 0) { print " checked"; } ?>> Male 
        <input type="Radio" name="intGender" value="1"<?php if ($qryInfo["intGender"] == 1) { print " checked"; } ?>> Female
        </td>
    </tr>
    <tr>
        <td colspan="2"><br></td>
    </tr>
    <tr>
           <td><b>Age:</b> </td>
           <td>
        <select name="intAge" class="dropdown">
        <?php
            // loop through and display our numbers
            for ($i = 13; $i < 100; $i++) {
                print "
                <option value=\"" . $i . "\"";
                if ($qryInfo["intAge"] == $i) {
                    print " selected";
                }
                print ">" . $i . "</option>";
            }
        ?>
        </select></td>
    </tr>
    <tr>
        <td colspan="2"><br></td>
    </tr>
    <tr>
           <td><b>Photo:</b> </td>
           <td><input type="File" name="img_photo" value="" size="45" class="input"></td>
    </tr>
    <tr>
        <td></td>
        <td class="smalltxt">
        <?php
            // see if they have a photo uploaded
            if (count($arrPhoto)) {
                print "<a href='http://www.guitarists.net/files/" . $arrPhoto["filename"] . "' title='Preview " . $arrPhoto["filename"] . "' target='_new'>http://www.guitarists.net/files/" . $arrPhoto["filename"] . "</a> (" . $arrPhoto["filesize"] . "k)<br />";
                print "<input type='checkbox' name='del_img_photo' value='1' /> Delete current file";
            } else {
                print "Upload your photo here (maximum of 500 pixels wide - bigger images will be resized and the aspect ratio will be preserved).";
            }
        ?><br><br>
        </td>
    </tr>
    <tr>
           <td><b>Avatar:</b> </td>
           <td><input type="File" name="img_avatar" value="" size="45" class="input"></td>
    </tr>
    <tr>
        <td></td>
        <td class="smalltxt">
        <?php
            // see if they have a photo uploaded
            if (count($arrAvatar)) {
                print "<a href='http://www.guitarists.net/files/" . $arrAvatar["filename"] . "' title='Preview " . $arrAvatar["filename"] . "' target='_new'>http://www.guitarists.net/files/" . $arrAvatar["filename"] . "</a> (" . $arrAvatar["filesize"] . "k)<br />";
                print "<input type='checkbox' name='del_img_avatar' value='1' /> Delete current file";
            } else {
                print "Upload an avatar here for the forum and buddy lists (maximum of 100x100 pixels - bigger images will be resized and may be distorted, as the aspect ratio will not be preserved).";
            }
        ?><br><br>
        </td>
    </tr>
    <tr>
        <td colspan="2"><br></td>
    </tr>
    <tr>
        <td><b>Receive Email:</b> </td>
        <td>
        <input type="radio" name="intSendEmail" value="1"<?php if ($qryInfo["intSendEmail"] == 1) { print " checked"; } ?>> Yes
        <input type="radio" name="intSendEmail" value="0"<?php if ($qryInfo["intSendEmail"] == 0) { print " checked"; } ?>> No
        <input type="Hidden" name="orig_intSendEmail" value="<?php print $qryInfo["intSendEmail"]; ?>" />
        </td>
    </tr>
    <tr>
        <td></td>
        <td class="smalltxt">
        This will allow you to receive our free newsletter, which is full of the<br>
        most recent    changes to our site, as well as the guitar world at large.<br><br>
        </td>
    </tr>
    <tr>
        <td><b>Private Info:</b> </td>
        <td>
        <input type="radio" name="intPrivate" value="1"<?php if ($qryInfo["intPrivate"] == 1) { print " checked"; } ?>> Yes
        <input type="radio" name="intPrivate" value="0"<?php if ($qryInfo["intPrivate"] == 0) { print " checked"; } ?>> No
        </td>
    </tr>
    <tr>
           <td></td>
           <td class="smalltxt">
           This will keep your real name, address, and age hidden from other user<br>
           on your profile page.<br><br>
        </td>
    </tr>
    <?php
        // see if this users access is allowed to remove banners
        if ($_SESSION["AccessLevel"] >= 20) {
            print "
            <tr>
                <td><b>Display Banners:</b> </td>
                <td>
                <input type=\"radio\" name=\"intHideAds\" value=\"0\"";
                
            if ($qryInfo["intHideAds"] == 0) {
                print " checked";
            }
            print "> Yes
                <input type=\"radio\" name=\"intHideAds\" value=\"1\"";
                
            if ($qryInfo["intHideAds"] == 1) {
                print " checked";
            }
            print "> No
                </td>
            </tr>
            <tr>
                   <td></td>
                   <td class=\"smalltxt\">
                Because of your support of this site, you can choose whether to see<br>
                ads on our site. Please remember that, even though they may be annoying,<br>
                they keep the site <b>free</b>.<br><br>
                </td>
            </tr>";
        } else {
            print "
            <input type=\"Hidden\" name=\"intHideAds\" value=\"" . $qryInfo["intHideAds"] . "\">";
        }
    ?>
    <tr>
        <td colspan="2"><br></td>
    </tr>
    <tr>
        <td colspan="2" class="innertitle">&nbsp;&raquo;&nbsp;Display Preferences</td>
    </tr>
    <tr>
        <td><b>Display Font:</b> </td>
        <td>
        <select name="FontID" class="dropdown">
            <option value="1"<?php if ($qryInfo["FontID"] == 1) { print " selected"; } ?>> Tahoma, Verdana, Arial</option>
            <option value="2"<?php if ($qryInfo["FontID"] == 2) { print " selected"; } ?>> Arial, Helvetica, sans-serif</option>
            <option value="3"<?php if ($qryInfo["FontID"] == 3) { print " selected"; } ?>> "Times New Roman", Times, serif</option>
            <option value="4"<?php if ($qryInfo["FontID"] == 4) { print " selected"; } ?>> "Courier New", Courier, monospace</option>
            <option value="5"<?php if ($qryInfo["FontID"] == 5) { print " selected"; } ?>> Verdana, Geneva, Arial, Helvetica, sans-serif</option>
            <option value="6"<?php if ($qryInfo["FontID"] == 6) { print " selected"; } ?>> "MS Serif", "New York", serif</option>
            <option value="7"<?php if ($qryInfo["FontID"] == 7) { print " selected"; } ?>> "MS Sans Serif", Geneva, sans-serif</option>
        </select>
        </td>
    </tr>
    <tr>
           <td></td>
           <td class="smalltxt">
        This will be the font used from your system.  If you do not have any of the<br>
        fonts selected, the default will be displayed (i.e. Times New Roman).
        </td>
    </tr>
    <tr>
        <td><b>Font Size:</b> </td>
        <td>
        <select name="FontSize" class="dropdown">
            <option value="9"<?php if ($qryInfo["FontSize"] == 9) { print " selected"; } ?>> 9</option>
            <option value="9.5"<?php if ($qryInfo["FontSize"] == 9.5) { print " selected"; } ?>> 9.5</option>
            <option value="10"<?php if ($qryInfo["FontSize"] == 10) { print " selected"; } ?>> 10</option>
            <option value="10.5"<?php if ($qryInfo["FontSize"] == 10.5) { print " selected"; } ?>> 10.5</option>
            <option value="11"<?php if ($qryInfo["FontSize"] == 11) { print " selected"; } ?>> 11</option>
            <option value="11.5"<?php if ($qryInfo["FontSize"] == 11.5) { print " selected"; } ?>> 11.5</option>
            <option value="12"<?php if ($qryInfo["FontSize"] == 12) { print " selected"; } ?>> 12</option>
            <option value="12.5"<?php if ($qryInfo["FontSize"] == 12.5) { print " selected"; } ?>> 12.5</option>
            <option value="13"<?php if ($qryInfo["FontSize"] == 13) { print " selected"; } ?>> 13</option>
            <option value="13.5"<?php if ($qryInfo["FontSize"] == 13.5) { print " selected"; } ?>> 13.5</option>
            <option value="14"<?php if ($qryInfo["FontSize"] == 14) { print " selected"; } ?>> 14</option>
            <option value="14.5"<?php if ($qryInfo["FontSize"] == 14.5) { print " selected"; } ?>> 14.5</option>
        </select> pixels
        </td>
    </tr>
    <tr>
           <td></td>
           <td class="smalltxt">
        This size (in pixels) of the font chosen above.<br><br>
        </td>
    </tr>
    <tr>
           <td></td>
           <td>
        <input type="submit" name="action" value="Update Information" class="button">
        <input type="Button" value="Cancel" onclick="location.href='index.php'" class="button">
        </td>
    </tr>
    </form>
    </table>
    </div>
    
    <script language="JavaScript">
    function smileys(face,page) {
        // specify our value
        if (page == 1) {
            document.myForm.txtPost.value += ' ' + face;
            document.myForm.txtPost.focus();
        } else {
            document.myForm.txtPost.value += ' ' + face;
        }
    }
    </script>
    
<?php
    // include our footer
    require("footer.php");
?>

