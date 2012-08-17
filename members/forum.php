<?php
    /*
        forum.php
        
        Allows the member to update their forum preferences.
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
                members.intPrivate,
                members.txtSignature,
                members.intSendEmail,
                members.intPrivate,
                members.txtSignature,
                members.intAccess,
                members.strAccess,
                members.intHideAds,
                members.FontID,
                members.FontSize
        from    members
        where   members.ID = '" . $_SESSION["MemberID"] . "'
        limit 1",
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
    $txtSig = htmlspecialchars(strip_tags($qryInfo["txtSignature"], 1));
    
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
    <table width="100%" cellspacing="0" cellpadding="2" border="0">
    <tr>
        <td class="tablehead">&nbsp;&raquo;&nbsp;<a href="/index.php"><b>Home</b></a>&nbsp;&raquo;&nbsp;<a href="/members/index.php"><b>Members Area</b></a>&nbsp;&raquo;&nbsp;Forum Options</td>
    </tr>
    </table>
    
    <div align="center">
    <table width="600" cellspacing="0" cellpadding="1" border="0">
    <form name="myForm" action="update_forum.php" method="post">
    <input type="Hidden" name="ID" value="<?php print $_SESSION["MemberID"]; ?>">
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
        // see if they can set their user access info
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
        <td colspan="2" class="innertitle">&nbsp;&raquo;&nbsp;Forum Signature</td>
    </tr>
    <tr valign="top">
        <td colspan="2">
        <textarea name="txtSignature" cols="80" rows="8" class="input"><?php print trim($txtSig); ?></textarea><br />
        Use the same <a href="javascript:newWin('/forum/bbcode.php', 600, 400)"><b>BBCode tags</b></a> you use in the forums.
        <p />
        <?php
            // loop through and display our smilies
            for ($i = 1; $i <= 74; $i++) {
                ?>
                <a href="javascript:smileys(':sm<?php print $i; ?>:','1')"><img src="/images/smilies/<?php print $i; ?>.gif" alt="" border="0"></a>
                <?php
            }
        ?>
        </td>
    </tr>
    <tr>
        <td colspan="2"><br></td>
    </tr>
    <tr>
        <td colspan="2" class="innertitle">&nbsp;&raquo;&nbsp;Live Chat Preferences</td>
    </tr>
    <tr valign="middle">
        <td><b>Networks:</b></td>
        <td>
        <!--- begin network table --->
        <table cellspacing="0" cellpadding="2" border="0">
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
            document.myForm.txtSignature.value += ' ' + face;
            document.myForm.txtSignature.focus();
        } else {
            document.myForm.txtSignature.value += ' ' + face;
        }
    }
    </script>
    
<?php
    // include our footer
    require("footer.php");
?>
