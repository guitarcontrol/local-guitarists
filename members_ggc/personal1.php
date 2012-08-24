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
                members.strFName,
                members.strLName,
                members.strAIM,
                members.strICQ,
                members.strMSN,
                members.strYahoo,
                members.intAllowChat,
                members.txtSignature,
                members.intSendEmail,
                members.intPrivate,
                members.txtSignature,
                members.intAccess,
                members.strAccess,
                members.intHideAds,
                members.FontID,
                members.FontSize,
                about.intGender,
                about.intAge,
                about.strPhoto
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
    
    // swap out our smilies
    $txtSig = smilies2(htmlspecialchars(strip_tags($qryInfo["txtSignature"])), 1);
    
    // create our page variables
    $pageTitle = "Members Area: Edit your account information";
    $pageDescription = "";
    $pageKeywords = "";
    $areaName = "members";
    
    // set an array of the ID's that are allowed to turn banners off
    $arrLevels = array("6","11","13","14","20","90","99");
    $arrPrefered = array("20","90","99");
    
    // include our header
    require("header.php");
?>
    <script language="JavaScript" src="/inc/func.js"></script>
    
    <br>
    <div align="center">
    <table width="600" cellspacing="0" cellpadding="1" border="0">
    <form name="myForm" action="update_personal.php" method="post" onSubmit="return checkPersonalInfo()">
    <input type="Hidden" name="ID" value="<?php print $_SESSION["MemberID"]; ?>">
    <tr>
        <td colspan="2" class="tablehead">&nbsp;&raquo;&nbsp;<a href="/index.php" class="tablehead"><b>Home</b></a>&nbsp;&raquo;&nbsp;<a href="/members_ggc/index.php" class="tablehead"><b>Members Area</b></a>&nbsp;&raquo;&nbsp;Edit your Account Info</td>
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
           <td><input type="Text" name="strPhoto" value="<?php print trim($qryInfo["strPhoto"]); ?>" size="45" maxlength="250" class="input"></td>
    </tr>
    <tr>
        <td></td>
        <td class="smalltxt">
        Example:  http://www.guitarists.net/images/logo.gif
        <br><br>
        </td>
    </tr>
    <tr>
        <td><b>Receive Email:</b> </td>
        <td>
        <input type="radio" name="intSendEmail" value="1"<?php if ($qryInfo["intSendEmail"] == 1) { print " checked"; } ?>> Yes
        <input type="radio" name="intSendEmail" value="0"<?php if ($qryInfo["intSendEmail"] == 0) { print " checked"; } ?>> No
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
        
        if ($_SESSION["AccessLevel"] >= 20) {
            print "
            <tr>
                <td><b>Display Title:</b> </td>
                <td>
                <input type=\"Text\" name=\"strAccess\" value=\"" . trim($qryInfo["strAccess"]) . "\" maxlength=\"20\" class=\"input\">
                </td>
            </tr>
            <tr>
                   <td></td>
                   <td class=\"smalltxt\">
                Because of your status on the site, you can create your own account 
                description.<br><br>
                </td>
            </tr>";
        } else {
            print "
            <input type=\"Hidden\" name=\"strAccess\" value=\"\">";
        }
    ?>
    <tr>
        <td colspan="2"><br></td>
    </tr>
    <tr>
        <td colspan="2" class="tablehead"><b style="color: #ffffff;">&nbsp;&raquo;&nbsp;Forum Signature</b></td>
    </tr>
    <tr valign="top">
        <td></td>
        <td>
        <textarea name="txtPost" cols="70" rows="8" class="input"><?php print trim($txtSig); ?></textarea><br />
        <div class="left" id="js-buttons">
        <input type="button" value="bold" title="subtly (if you have anti-alaising) bolded text" class="small" onclick="boldz(event);return false;" />
        <input type="button" value="ital" title="italic text (slanty)" class="small" onclick="italicz(event);return false;" />
        <input type="button" value="block" title="blocktext" class="small" onclick="simcode(event);return false;" />
        <input type="button" value="img" title="simple image tag" class="small" onclick="doimage(event);return false;" />
        <input type="button" value="url" title="you willll be asked to supply a URL and a title for this link" class="small" onclick="linkz(event);return false;" />
        <input type="button" name="undo" id="UndoButt" class="small" value="undo" onclick="UndoThat(event);return false;" title="this button takes you back to just before your last magic edit" />
        </div>
        <?php
            // loop through and display our smilies
            for ($i = 1; $i <= 79; $i++) {
                ?>
                <a href="javascript:smileIt(':sm<?php print $i; ?>:','0')"><img src="/images/smilies/<?php print $i; ?>.gif" alt="" border="0"></a> &nbsp;
                <?php
            }
        ?>
        </td>
    </tr>
    <tr>
        <td colspan="2"><br></td>
    </tr>
    <tr>
        <td colspan="2" class="tablehead"><b style="color: #ffffff;">&nbsp;&raquo;&nbsp;Live Chat Preferences</b></td>
    </tr>
    <tr valign="middle">
        <td><b>Networks:</b></td>
        <td>
        <!--- begin network table --->
        <table cellspacing="0" cellpadding="0" border="0">
        <tr valign="middle">
            <td width="70">AIM: </td>
            <td><input type="text" name="strAIM" value="<?php print trim($qryInfo["strAIM"]); ?>" size="25" maxlength="35" class="input"></td>
        </tr>
        <tr valign="middle">
            <td>ICQ #: </td>
            <td><input type="text" name="strICQ" value="<?php print trim($qryInfo["strICQ"]); ?>" size="25" maxlength="35" class="input"></td>
        </tr>
        <tr valign="middle">
            <td>MSN: </td>
            <td><input type="text" name="strMSN" value="<?php print trim($qryInfo["strMSN"]); ?>" size="40" maxlength="85" class="input"></td>
        </tr>
        <tr valign="middle">
            <td>Yahoo!: </td>
            <td><input type="text" name="strYahoo" value="<?php print trim($qryInfo["strYahoo"]); ?>" size="40" maxlength="85" class="input"></td>
        </tr>
        </table>
        <!--- end network table --->
        </td>
    </tr>
    <tr valign="top">
        <td><b>Allow Chat:</b></td>
        <td>
        <input type="Radio" name="intAllowChat" value="1"<?php if ($qryInfo["intAllowChat"] == 1) { print " checked"; } ?>> All Visitors<br>
        <input type="Radio" name="intAllowChat" value="2"<?php if ($qryInfo["intAllowChat"] == 2) { print " checked"; } ?>> G-Net Members<br>
        <input type="Radio" name="intAllowChat" value="3"<?php if ($qryInfo["intAllowChat"] == 3) { print " checked"; } ?>> Administrators<br>
        <input type="Radio" name="intAllowChat" value="0"<?php if ($qryInfo["intAllowChat"] == 0) { print " checked"; } ?>> No One
        </td>
    </tr>
    <tr>
        <td></td>
        <td class="smalltxt">
        If you have listed any programs above, you can choose to allow all visitors to guitarists.net 
        to chat with you, members only, administrators only, or no one at all.<br><br>
        </td>
    </tr>
    <tr>
        <td colspan="2"><br></td>
    </tr>
    <tr>
        <td colspan="2" class="tablehead"><b style="color: #ffffff;">&nbsp;&raquo;&nbsp;Display Preferences</b></td>
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

