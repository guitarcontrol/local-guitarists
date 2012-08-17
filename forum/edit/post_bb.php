<?php
    /*
        post_bb.php
        
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
        alert(\"Please choose a thread to edit first.\");
        window.close();
        </script>";
        exit();
    }
    
    // get the forum title from the db for this topic
    $arrPost = $dbConn->getRow("
        select  *
        from    topics
        where   ID = " . $dbConn->quote($_GET["id"]) . "",
        DB_FETCHMODE_ASSOC);
    
    // continue, based on how many records were found
    if (!count($arrPost)) {
        print "
        <script language=\"JavaScript\">
        alert(\"Please choose a valid reply to edit first.\");
        history.back();
        </script>";
        exit();
    }
    
    // make sure they have the privelege to edit this reply
    if ($arrPost["intMemID"] != $_SESSION["MemberID"] && $_SESSION["AccessLevel"] < 90) {
        print "
        <script language=\"javascript\">
        alert(\"It appears you do not have permission to edit this message.\");
        history.back();
        </script>";
        exit();
    }
    
    // get the forum title from the db for this topic
    $arrForum = $dbConn->getRow("
        select  ID,
                strName
        from    forums
        where   ID = '" . $arrPost["intForum"] . "'",
        DB_FETCHMODE_ASSOC);
    
    // set our page variables
    $pageTitle = "Guitar Discussions: Edit " . $arrPost["strTitle"];
    $areaName = "forums";
    
    // include our header
    require("header.php");
?>
    <script language="JavaScript" src="/inc/func.js"></script>
    
    <br>
    <div align="center">
    <form name="myForm" action="update_post_bb.php" method="post" onSubmit="return checkPostEdit()">
    <input type="Hidden" name="ID" value="<?php print $arrPost["ID"]; ?>" />
    <input type="Hidden" name="intMemID" value="<?php print $arrPost["intMemID"]; ?>" />
    <input type="Hidden" name="intForum" value="<?php print $arrForum["ID"]; ?>" />
    <table width="100%" cellspacing="0" cellpadding="1" border="0">
    <tr valign="top" align="center">
        <td>
        
		<table width="100%" cellspacing="0" cellpadding="2" border="0">
        <tr>
            <td class="tablehead">&nbsp;&raquo;&nbsp;<a href="/index.php"><b>Home</b></a>&nbsp;&raquo;&nbsp;<a href="/forum/index.php"><b>Guitar Discussions</b></a>&nbsp;&raquo;&nbsp;<a href="/forum/topics_bb.php?forum=<?php print $arrPost["intForum"]; ?>"><b><?php print $arrForum["strName"]; ?></b></a>&nbsp;&raquo;&nbsp;Edit '<?php print $arrPost["strTitle"]; ?>'</td>
        </tr>
        </table>
        
        <table width="720" cellspacing="0" cellpadding="2" border="0">
        <tr>
            <td width="100">Title:</td>
            <td width="420"><input type="text" name="strTitle" value="<?php print trim($arrPost["strTitle"]); ?>" size="60" maxlength="150" class="input"></td>
            <td width="200" rowspan="5" valign="top">
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
            <textarea name="txtPost" cols="70" rows="25" wrap="virtual" class="input"><?php print trim($arrPost["txtPost"]); ?></textarea><br />
            <div class="left" id="js-buttons">
            <input type="button" value="bold" title="subtly (if you have anti-alaising) bolded text" class="small" onclick="boldz(event);return false;" />
            <input type="button" value="ital" title="italic text (slanty)" class="small" onclick="italicz(event);return false;" />
            <input type="button" value="block" title="blocktext" class="small" onclick="simcode(event);return false;" />
            <input type="button" value="img" title="simple image tag" class="small" onclick="doimage(event);return false;" />
            <input type="button" value="url" title="you willll be asked to supply a URL and a title for this link" class="small" onclick="linkz(event);return false;" />
            <input type="button" name="undo" id="UndoButt" class="small" value="undo" onclick="UndoThat(event);return false;" title="this button takes you back to just before your last magic edit" />
            <p />
            <a href="javascript:newWin('/forum/bbcode.php', 600, 400)"><b>BB Code Examples</b></a>
            <br /><br />
            </div>
            </td>
        </tr>
        <tr>
            <td nowrap>Reason:</td>
            <td><input type="text" name="strEditReason" value="" size="60" maxlength="250" class="input" /></td>
        </tr>
        <?php
            // see if they're a mod or not
            if ($_SESSION["AccessLevel"] >= 90) {
                ?>
                <tr>
                    <td>Read Only: </td>
                    <td colspan="2">
                    <input type="radio" name="bitReply" value="0"<?php if ($arrPost["bitReply"] == 0) { print " checked"; } ?> /> Yes
                    <input type="radio" name="bitReply" value="1"<?php if ($arrPost["bitReply"] == 1) { print " checked"; } ?> /> No
                    </td>
                </tr>
                <tr>
                    <td>Sticky: </td>
                    <td colspan="2">
                    <input type="radio" name="intSticky" value="1"<?php if ($arrPost["intSticky"] == 1) { print " checked"; } ?> /> Yes
                    <input type="radio" name="intSticky" value="0"<?php if ($arrPost["intSticky"] == 0) { print " checked"; } ?> /> No
                    </td>
                </tr>
                <tr>
                    <td><br></td>
                    <td class="smalltxt">This create a topic that will always appear at the top 
                    of the list of topics. Use this for user notices, important updates, etc.
                    </td>
                </tr>
                <?php
            } else {
                ?>
                <input type="Hidden" name="bitReply" value="<?php print $arrPost["bitReply"]; ?>" />
                <input type="Hidden" name="intSticky" value="<?php print $arrPost["intSticky"]; ?>" />
                <?php
            }
        ?>
        <tr>
            <td></td>
            <td><br />
            <input type="Checkbox" name="preview" value="1" /> Check here to preview the post before posting it.
            <p />
            <input type="submit" value="Post Now" class="smbutton">
            <input type="button" value="Cancel" onclick="location.href='/forum/view_bb.php?forum=<?php print $arrForum["ID"]; ?>&thread=<?php print $arrPost["ID"]; ?>';" class="smbutton">
            </td>
        </tr>
        <!--<tr>
            <td><br></td>
            <td class="smalltxt"><br />
            To add HTML to your posts, use the following BBCode examples:
            <p>
            <code style="font-size: 14px; ">[color=red]I'm red and I'm hot[/color] <br />
            [size=16]16pt sized text. That's big.[/size] <br />
            [font=Verdana]I can use all kinds of fonts![/font] <br />
            [align=right]This chunk is aligned to the right[/align] <br />
            [align=center]I'm centered[/align] <br />
            Hey, [quote=http://www.h2g2.com]Don't panic![/quote] <br />
             <br />
            [img]http://www.guitarists.net/images/smilies/25.gif[/img] <br />
            [img w=26 h=24]http://www.guitarists.net/images/smilies/49.gif[/img] <br />
             <br />
            http://www.guitarists.net/<br />
            [url]http://www.guitarists.net/[/url] <br />
            [url=http://www.guitarists.net/]G-Net[/url] <br />
            [url=http://www.guitarists.net/ t=_blank]G-Net in a new window[/url] <br />
             <br />
            [url=http://www.guitarists.net/ t=_blank][img w=26 h=24]http://www.guitarists.net/images/smilies/1.gif[/img][/url] <br />
             <br />
            moi@example.org <br />
            [email]moi@example.org[/email] <br />
            [email=we@example.org]drop us an email[/email] <br />
             <br />
            [ulist] <br />
             &nbsp; &nbsp;[*]one <br />
             &nbsp; &nbsp;[*]two <br />
            [/ulist] <br />
             <br />
            [list] <br />
             &nbsp; &nbsp;[*]first &nbsp;<br />
             &nbsp; &nbsp;[*]second <br />
            [/list]</code>
            </td>
        </tr> -->
        </table>
        </form>
        
        </td>
    </tr>
    </table>
    </div>

<?php
    // include our footer
    require("footer.php");
?>