<?php
    /*
        index.php
        
        This is the main script that allows a member to post a new 
        thread in the forums.
    */
    
    // include our needed file(s)
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    require("functions.php");
    
    // make sure they're logged in
    verify_access($_SESSION["AccessLevel"], 1);
    
    // if they chose a user, get the username here
    if (!empty($_GET["id"])) {
        // query the data from the db
        $qryUser = $dbConn->getRow("
            select  ID,
                    strUsername
            from    members
            where   ID = " . $dbConn->quote($_GET["id"]) . " or
                    strUsername= " . $dbConn->quote(urldecode($_GET["id"])) . "
            limit 1",
            DB_FETCHMODE_ASSOC);
    } else if (!empty($_GET["user"])) {
        // query the data from the db
        $qryUser = $dbConn->getRow("
            select  ID,
                    strUsername
            from    members
            where   ID = " . $dbConn->quote($_GET["user"]) . " or
                    strUsername= " . $dbConn->quote(urldecode($_GET["user"])) . "
            limit 1",
            DB_FETCHMODE_ASSOC);
    } else {
        // set defaults
        $qryUser = array("ID" => 0, "strUsername" => "");
    }
    
    // see if they have any favorite members added in their buddy list
    $qryBuds = $dbConn->query("
        select      saved.intItem,
                    members.ID,
                    members.strUsername
        from        saved,
                    members
        where       saved.intMemID = " . $_SESSION["MemberID"] . " and
                    saved.intType = 4 and
                    saved.intItem = members.ID
        order by    members.strUsername");
    
    // include our header file
    if (empty($_SESSION["GGCIFrame"])) {
        require("header.php");
    } else {
        ?>
        <link type="text/css" rel="stylesheet" href="/inc/styles.css" />
        <style>
        BODY {
            background: none;
        }
        </style>
        <?php
    }
?>
    <script language="JavaScript" type="text/javascript" src="/inc/functions.js"></script>
    <script language="JavaScript" src="/inc/func.js"></script>

    <br>
    <form name="myForm" action="submit_bb.php" method="post" onSubmit="return checkPM()">
    <table width="100%" cellspacing="0" cellpadding="1" border="0">
    <tr valign="top">
        <td align="center">
        
        <?php if (empty($_SESSION["GGCIFrame"])) { ?>
        <table width="100%" cellspacing="0" cellpadding="2" border="0">
        <tr>
            <td class="tablehead">&nbsp;&raquo;&nbsp;<a href="/index.php"><b>Home</b></a>&nbsp;&raquo;&nbsp;<a href="/forum_ggc/index.php"><b>Guitar Discussion</b></a>&nbsp;&raquo;&nbsp;<a href="/forum_ggc/msgs/index.php"><b>Private Messages</b></a>&nbsp;&raquo;&nbsp;Send a New Message<?php if (strlen($qryUser["strUsername"])) { print " to " . $qryUser["strUsername"]; } ?></td>
        </tr>
        </table>
        <?php } ?>
        <table width="100%" cellspacing="0" cellpadding="2" border="0">
        <tr>
            <td class="tablehead">&nbsp;&raquo;&nbsp;<a href="/forum_ggc/index.php"><b>Home</b></a>&nbsp;&raquo;&nbsp;<a href="/forum_ggc/msgs/index.php"><b>Private Messages</b></a>&nbsp;&raquo;&nbsp;Send a New Message<?php if (strlen($qryUser["strUsername"])) { print " to " . $qryUser["strUsername"]; } ?></td>
        </tr>
        </table>
        <table cellspacing="0" cellpadding="2" border="0">
        <tr>
            <td width="100">Recipient:</td>
            <td colspan="2">
            <?php
            // see if they have any buds saved
            if ($qryBuds->numRows()) {
                ?>
                <select name="intRecipient" class="dropdown">
                    <option value=""> [ Choose a Buddy ]</option>
                    <?php
                    // loop through our query
                    while ($qryRow = $qryBuds->fetchRow(DB_FETCHMODE_ASSOC)) {
                        print "
                        <option value=\"" . $qryRow["ID"] . "\"> " . $qryRow["strUsername"] . "</option>";
                    }
                    ?>
                </select>
                <?php
            }
            ?>
            <input type="Text" name="strRecipient" value="<?php print $qryUser["strUsername"]; ?>" class="input">
            <i>Username</i> or <i>Member ID</i>
            </td>
        </tr>
        <tr>
            <td>Title:</td>
            <td><input type="text" name="strTitle" value="" size="60" maxlength="150" class="input"></td>
            <td width="250" rowspan="2" valign="top">
            <img src="/images/spacer.gif" width="250" height="3" /><br />
            <?php
                // loop through and display our smilies
                for ($i = 1; $i <= 74; $i++) {
                    ?>
                    <a href="javascript:smileIt(':sm<?php print $i; ?>:','0')"><img src="/images/smilies/<?php print $i; ?>.gif" alt="" border="0"></a> &nbsp;
                    <?php
                }
            ?>
            </td>
        </tr>
        <tr valign="top">
            <td>Message:</td>
            <td>
            <textarea name="txtPost" cols="60" rows="25" wrap="virtual" class="input"></textarea>
            <div class="left" id="js-buttons">
            <input type="button" value="bold" title="subtly (if you have anti-alaising) bolded text" class="small" onclick="boldz(event);return false;" />
            <input type="button" value="ital" title="italic text (slanty)" class="small" onclick="italicz(event);return false;" />
            <input type="button" value="block" title="blocktext" class="small" onclick="simcode(event);return false;" />
            <input type="button" value="img" title="simple image tag" class="small" onclick="doimage(event);return false;" />
            <input type="button" value="url" title="you willll be asked to supply a URL and a title for this link" class="small" onclick="linkz(event);return false;" />
            <input type="button" name="undo" id="UndoButt" class="small" value="undo" onclick="UndoThat(event);return false;" title="this button takes you back to just before your last magic edit" />
            </div>
            <p />
            <a href="javascript:newWin('/forum_ggc/bbcode.php', 600, 400)"><b>BB Code Examples</b></a>
            <p />
            <input type="Checkbox" name="previewMe" value="1" /> Preview your post.
            </td>
        </tr>
        <tr>
            <td></td>
            <td><br>
            <input type="submit" value="Post Now" class="button">
            <input type="button" value="Cancel" onclick="history.back();" class="button">
            </td>
        </tr>
        </table>
        </form>
        
        </td>
    </tr>
    </table>
    
    <script language="JavaScript">
    function checkPM() {
        // set our variables
        strMessage = "Please provide the following:\n";
        intCount = 0;
        
        // make sure our required variables are set
        <?php
            if (!isset($_GET["user"])) {
                if ($qryBuds->numRows()) {
                    print "if (document.myForm.intRecipient.options[document.myForm.intRecipient.selectedIndex].value == \"\" && document.myForm.strRecipient.value == \"\") { strMessage += \"Recipient\\n\"; intCount++ }";
                } else {
                    print "if (document.myForm.strRecipient.value == \"\") { strMessage += \"Recipient\\n\"; intCount++ }";
                }
            }
        ?>
        if (document.myForm.strTitle.value == "") { strMessage += "Message Title\n"; intCount++ }
        if (document.myForm.txtPost.value == "") { strMessage += "Message Body\n"; intCount++ }
        
        // stop, if we need to
        if (intCount > 0) {
            alert(strMessage);
            return false;
        }
        // all good
        return true;
    }
    </script>

<?php
    // include our footer
    if (empty($_SESSION["GGCIFrame"])) {
        require("footer.php");
    }
?>
