<?php
    /*
        reply_bb.php
        
        This is the main script that allows a member to post a new 
        thread in the forums.
    */
    
    // include our needed files
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    require("functions.php");
    
    // make sure they're logged in
    verify_access($_SESSION["AccessLevel"], 1);
    
    // make sure they're logged in
    verify_frozen($_SESSION["MemberID"], $dbConn);
    
    // make sure a thread was passed
    if (!isset($_GET["id"])) {
        print "
        <script language=\"JavaScript\">
        alert(\"Please choose a reply to edit first.\");
        history.back();
        </script>";
        exit();
    }
    
    // get the forum title from the db for this topic
    $arrReply = $dbConn->getRow("
        select  *
        from    replies
        where   ID =  " . $dbConn->quote($_GET["id"]) . "",
        DB_FETCHMODE_ASSOC);
    
    // make sure they have the privelege to edit this reply
    if ($arrReply["intMemID"] != $_SESSION["MemberID"] && $_SESSION["AccessLevel"] < 90) {
        print "
        <script language=\"javascript\">
        alert(\"It appears you do not have permission to edit this message.\");
        history.back();
        </script>";
        exit();
    }
    
    // get the forum title from the db for this topic
    $qryTopic = $dbConn->getRow("
        select  ID,
                intForum,
                intMemID,
                strTitle
        from    topics
        where   ID = '" . $arrReply["intTopic"] . "'",
        DB_FETCHMODE_ASSOC);
    
    // get the forum title from the db for this topic
    $qryForum = $dbConn->getRow("
        select  ID,
                strName
        from    forums
        where   ID = '" . $qryTopic["intForum"] . "'",
        DB_FETCHMODE_ASSOC);
    
    // set our page variables
    $pageTitle = "Guitar Discussions: Edit '" . $qryTopic["strTitle"] . "'";
    $areaName = "forums";
    
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
    <script language="JavaScript" src="/inc/func.js"></script>
    
    <br>
    <div align="center">
    <form name="myForm" action="update_reply_bb.php" method="post" onSubmit="return checkPost()">
    <input type="hidden" name="ID" value="<?php print $arrReply["ID"]; ?>">
    <input type="hidden" name="intForum" value="<?php print $qryTopic["intForum"]; ?>">
    <input type="hidden" name="intTopic" value="<?php print $qryTopic["ID"]; ?>">
    <input type="hidden" name="intMemID" value="<?php print $arrReply["intMemID"]; ?>">
    <table width="100%" cellspacing="0" cellpadding="1" border="0">
    <tr valign="top" align="center">
        <td>
        
        <?php if (empty($_SESSION["GGCIFrame"])) { ?>
        <table width="100%" cellspacing="0" cellpadding="2" border="0">
        <tr>
            <td class="tablehead">&nbsp;&raquo;&nbsp;<a href="/index.php"><b>Home</b></a>&nbsp;&raquo;&nbsp;<a href="/forum_ggc/index.php"><b>Guitar Discussions</b></a>&nbsp;&raquo;&nbsp;<a href="/forum_ggc/topics_bb.php?forum=<?php print $qryForum["ID"]; ?>"><b><?php print $qryForum["strName"]; ?></b></a>&nbsp;&raquo;&nbsp;<a href="/forum_ggc/view_bb.php?forum=<?php print $qryTopic["intForum"]; ?>&thread=<?php print $qryTopic["ID"]; ?>"><b><?php print $qryTopic["strTitle"]; ?></b></a>&nbsp;&raquo;&nbsp;Edit Reply "<?php print $qryTopic["strTitle"]; ?>"</td>
        </tr>
        </table>
        <?php } ?>
        
        <table width="720" cellspacing="0" cellpadding="2" border="0">
        <tr>
            <td>Title:</td>
            <td><input type="text" name="strTitle" value="<?php print trim($arrReply["strTitle"]); ?>" size="60" maxlength="150" class="input"></td>
            <td width="250" rowspan="4" valign="top">
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
            <textarea name="txtPost" cols="70" rows="25" wrap="virtual" class="input"><?php print trim($arrReply["txtReply"]); ?></textarea><br />
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
            <br /><br />
            </td>
        </tr>
        <tr>
            <td colspan="2"><br /></td>
        </tr>
        <tr>
            <td nowrap>Reason:</td>
            <td><input type="text" name="strEditReason" value="" size="60" maxlength="250" class="input" /></td>
        </tr>
        <tr>
            <td>Display Signature</td>
            <td>
            <input type="radio" name="intDisplaySig" value="1"<?php if ($arrReply["intDisplaySig"] == 1) { print " checked"; } ?> /> Yes
            <input type="radio" name="intDisplaySig" value="0"<?php if ($arrReply["intDisplaySig"] == 0) { print " checked"; } ?> /> No
            </td>
        </tr>
        <tr>
            <td></td>
            <td><br />
            <input type="Checkbox" name="preview" value="1" /> Check here to preview the post before posting it.
            <p />
            <input type="submit" value="Post Now" class="smbutton">
            <input type="button" value="Cancel" onclick="location.href='/forum_ggc/view_bb.php?forum=<?php print $qryTopic["intForum"]; ?>&thread=<?php print $qryTopic["ID"]; ?>#post<?php print $arrReply["ID"]; ?>';" class="smbutton">
            </td>
        </tr>
        </table>
        </form>
        
        </td>
    </tr>
    </table>
    </div>

<?php
    // include our footer
    if (empty($_SESSION["GGCIFrame"])) {
        require("footer.php");
    }
?>
