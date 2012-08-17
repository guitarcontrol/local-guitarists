<?php
    
    /*
        index_bb.php
        
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
    if (!isset($_GET["thread"])) {
        print "
        <script language=\"JavaScript\">
        alert(\"Please choose a topic to reply to first.\");
        //location.replace(\"/forum_ggc/index.php\");
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
        where   ID = " . $dbConn->quote($_GET["thread"]),
        DB_FETCHMODE_ASSOC);
    
    // set our vars from our form fields submitted
    $strTitle = $qryTopic["strTitle"];
    
    // set our reply title
    if (substr($strTitle, 0, 4) != "RE: ") {
        $strTitle = "RE: " . $strTitle;
    }
    
    // get the forum title from the db for this topic
    $qryForum = $dbConn->getRow("
        select  ID,
                strName
        from    forums
        where   ID = '" . $qryTopic["intForum"] . "'",
        DB_FETCHMODE_ASSOC);
    
    // if we're quoting, let's get that data here
    if (!empty($_GET["quote"])) {
        // see if they're quoting the main thread or a reply
        if (isset($_GET["reply"])) {
            // get the text to quote for this reply
            $qryQuote = $dbConn->getRow("
                select  replies.txtReply,
                        members.strUsername
                from    replies,
                        members
                where   replies.ID = " . $dbConn->quote($_GET["reply"]) . " and 
                        replies.intMemID = members.ID",
                DB_FETCHMODE_ASSOC);
            
            // replace our smilies
            $txtMessage = "[quote][b][i]" . $qryQuote["strUsername"] . " said:[/i][/b]\n\n[i]" . smilies2($qryQuote["txtReply"],'1') . "[/i][/quote]\n\n";
        } else {
            // get the text to quote for the main post
            $qryQuote = $dbConn->getRow("
                select  topics.txtPost,
                        members.strUsername
                from    topics,
                        members
                where   topics.ID = " . $dbConn->quote($_GET["thread"]) . " and 
                        topics.intMemID = members.ID",
                DB_FETCHMODE_ASSOC);
            
            // replace our smilies
            $txtMessage = "[quote][b][i]" . $qryQuote["strUsername"] . " said:[/i][/b]\n\n[i]" . smilies($qryQuote["txtPost"],'1') . "[/i][/quote]\n\n";
        }
    } else {
        $txtMessage = "";
    }
    
    // see if they chose to qoute them
    if (!empty($_GET["quote"])) {
        $intQuote = 1;
    } else {
        $intQuote = 0;
    }
    
    // set our page variables
    $pageTitle = "Guitar Discussions: Reply to " . $strTitle;
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
    <form name="myForm" action="submit_bb.php" method="post" onSubmit="return checkPost()">
    <input type="hidden" name="intForum" value="<?php print $qryTopic["intForum"]; ?>">
    <input type="hidden" name="intTopic" value="<?php print $qryTopic["ID"]; ?>">
    <input type="hidden" name="intMemID" value="<?php print $qryTopic["intMemID"]; ?>">
    <table width="100%" cellspacing="0" cellpadding="1" border="0">
    <tr valign="top" align="center">
        <td>
        
        <?php if (empty($_SESSION["GGCIFrame"])) { ?>
        <table width="100%" cellspacing="0" cellpadding="2" border="0">
        <tr>
            <td class="tablehead">&nbsp;&raquo;&nbsp;<a href="/index.php"><b>Home</b></a>&nbsp;&raquo;&nbsp;<a href="/forum_ggc/index.php"><b>Guitar Discussions</b></a>&nbsp;&raquo;&nbsp;<a href="/forum_ggc/topics_bb.php?forum=<?php print $qryForum["ID"]; ?>"><b><?php print $qryForum["strName"]; ?></b></a>&nbsp;&raquo;&nbsp;Reply to "<?php print $qryTopic["strTitle"]; ?>"</td>
        </tr>
        </table>
        <?php } ?>
        
        <table width="720" cellspacing="0" cellpadding="2" border="0">
        <tr>
            <td width="100">Title:</td>
            <td width="320"><input type="text" name="strTitle" value="<?php print trim($strTitle); ?>" size="60" maxlength="150" class="input"></td>
            <td width="200" rowspan="6" valign="top">
            <img src="/images/spacer.gif" width="200" height="3" alt="Spacer" /><br />
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
            <textarea name="txtPost" cols="80" rows="25" wrap="virtual" class="input"><?php print trim($txtMessage); ?></textarea><br />
            <div class="left" id="js-buttons">
            <input type="button" value="bold" title="subtly (if you have anti-alaising) bolded text" class="small" onclick="boldz(event);return false;" />
            <input type="button" value="ital" title="italic text (slanty)" class="small" onclick="italicz(event);return false;" />
            <input type="button" value="block" title="blocktext" class="small" onclick="simcode(event);return false;" />
            <input type="button" value="img" title="simple image tag" class="small" onclick="doimage(event);return false;" />
            <input type="button" value="url" title="you willll be asked to supply a URL and a title for this link" class="small" onclick="linkz(event);return false;" />
            <input type="button" name="undo" id="UndoButt" class="small" value="undo" onclick="UndoThat(event);return false;" title="this button takes you back to just before your last magic edit" />
            <p />
            <a href="javascript:newWin('/forum_ggc/bbcode.php', 600, 400)"><b>BB Code Examples</b></a>
            <br /><br />
            </div>
            </td>
        </tr>
        <tr>
            <td colspan="2"><br /></td>
        </tr>
        <tr>
            <td>Display Signature</td>
            <td>
            <input type="radio" name="intDisplaySig" value="1" checked /> Yes
            <input type="radio" name="intDisplaySig" value="0" /> No
            </td>
        </tr>
        <tr>
            <td>Subscribe: </td>
            <td>
            <input type="radio" name="subscribe" value="1" /> Yes
            <input type="radio" name="subscribe" value="0" checked /> No
            </td>
        </tr>
        <tr>
            <td></td>
            <td><br />
            <input type="Checkbox" name="preview" value="1" /> Check here to preview the post before posting it.
            <p />
            <input type="submit" value="Post Now" class="smbutton">
            <input type="button" value="Cancel" onclick="location.href='/forum_ggc/view_bb.php?forum=<?php print $qryTopic["intForum"]; ?>&thread=<?php print $qryTopic["ID"]; ?>';" class="smbutton">
            </td>
        </tr>
        <tr>
            <td colspan="3"><br />
            <b>Previous Discussions:</b><br />
            <iframe src="/forum_ggc/reply/list.php?forum=<?php print $qryTopic["intForum"]; ?>&thread=<?php print $qryTopic["ID"]; ?>" frameborder="0" scrolling="auto" name="list" width="100%" height="300" marginwidth="0" marginheight="0">
            <p>This page uses iframes. If you see this message, you cannot view the previous posts and replies here.
            </iframe>
            </td>
        </tr>
        <!-- <tr>
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
            [code]if ($code) { <br />
             &nbsp; &nbsp;echo &quot;Code in fixed-width font&quot;; <br />
            }[/code] <br />
            
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
    if (empty($_SESSION["GGCIFrame"])) {
        require("footer.php");
    }
?>
