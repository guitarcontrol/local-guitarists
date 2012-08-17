<?php
    /*
        index.php
        
        This is our main reply script that allows a member to reply 
        to a given topic.
    */
    
    // include our needed file(s)
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    require("functions.php");
    
    // make sure they're logged in
    verify_access($_SESSION["AccessLevel"], 1);
    
    // make sure a thread was passed
    if (!isset($_GET["msg"])) {
        print "
        <script language=\"JavaScript\">
        alert(\"Please choose a message to reply to first.\");
        location.replace(\"/forum/msgs/index.php\");
        </script>";
        exit();
    }
    
    // get the forum title from the db for this topic
    $qryTopic = $dbConn->getRow("
        select  ID,
                intMemID,
                strTitle,
                intRecipient
        from    msg_main
        where   ID = " . $dbConn->quote($_GET["msg"]),
        DB_FETCHMODE_ASSOC);
    
    // make sure they should be able to view/reply to this thread
    if ($qryTopic["intMemID"] != $_SESSION["MemberID"] && $qryTopic["intRecipient"] != $_SESSION["MemberID"]) {
        print "
        <script language=\"JavaScript\">
        alert(\"You do not have permission to reply to other member's messages.\");
        location.replace(\"/forum/msgs/index.php\");
        </script>";
        exit();
    }
    
    // if we're quoting, let's get that data here
    if (isset($_GET["quote"])) {
        // generate our SQL
        if (!empty($_GET["id"])) {
            $sqlText = "select msg_replies.txtContent, members.strUsername from msg_replies, members where msg_replies.ID = " . $dbConn->quote($_GET["id"]) . " and msg_replies.intMemID = members.ID";
        } else {
            $sqlText = "select msg_main.txtContent, members.strUsername from msg_main, members where msg_main.ID = " . $dbConn->quote($_GET["msg"]) . " and msg_main.intMemID = members.ID";
        }
        
        // query the text for us to quote
        $qryQuote = $dbConn->getRow($sqlText, DB_FETCHMODE_ASSOC);
        
        // replace our smilies
        $quoteText = "[quote][b][i]" . $qryQuote["strUsername"] . " said:[/i][/b]\n\n[i]" . smilies($qryQuote["txtContent"],'1') . "[/i][/quote]\n\n";;
        
        // strip out any HTML
        $quoteText = strip_tags($quoteText);
        $quoteName = $qryQuote["strUsername"];
    } else {
        // set empty variables
        $quoteText = "";
        $quoteName = "";
    }
    
    // set our reply title
    if (substr($qryTopic["strTitle"], 0, 4) != "RE: ") {
        $strTitle = "RE: " . $qryTopic["strTitle"];
    } else {
        $strTitle = $qryTopic["strTitle"];
    }
    
    // include our header
    require("header.php");
?>
    <script language="JavaScript" src="/inc/func.js"></script>

    <br>
    <form name="myForm" action="submit_bb.php" method="post">
    <input type="hidden" name="intParent" value="<?php print $_GET["msg"]; ?>">
    <input type="Hidden" name="poster" value="<?php print $quoteName; ?>">
    <input type="Hidden" name="intRecipient" value="<?php
        if ($qryTopic["intMemID"] != $_SESSION["MemberID"]) {
            print $qryTopic["intMemID"];
        } else {
            print $qryTopic["intRecipient"];
        }
    ?>">
    <table width="100%" cellspacing="0" cellpadding="1" border="0">
    <tr valign="top">
        <td align="center">
    
        <table width="100%" cellspacing="1" cellpadding="3" border="0">
        <tr>
            <td class="tablehead">&nbsp;&raquo;&nbsp;<a href="/index.php"><b>Home</b></a>&nbsp;&raquo;&nbsp;<a href="/forum/index.php"><b>Guitar Discussion</b></a>&nbsp;&raquo;&nbsp;<a href="/forum/msgs/index.php"><b>Private Messages</b></a>&nbsp;&raquo;&nbsp;Reply to "<?php print $qryTopic["strTitle"]; ?>"</td>
        </tr>
        </table>
        
        <table width="720" cellspacing="0" cellpadding="2" border="0">
        <tr>
            <td>Title:</td>
            <td><input type="text" name="strTitle" value="<?php print $strTitle; ?>" size="60" maxlength="100" class="input"></td>
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
            <textarea name="txtPost" cols="80" rows="25" wrap="virtual" class="input"><?php print $quoteText; ?></textarea>
            <div class="left" id="js-buttons">
            <input type="button" value="bold" title="subtly (if you have anti-alaising) bolded text" class="small" onclick="boldz(event);return false;" />
            <input type="button" value="ital" title="italic text (slanty)" class="small" onclick="italicz(event);return false;" />
            <input type="button" value="block" title="blocktext" class="small" onclick="simcode(event);return false;" />
            <input type="button" value="img" title="simple image tag" class="small" onclick="doimage(event);return false;" />
            <input type="button" value="url" title="you willll be asked to supply a URL and a title for this link" class="small" onclick="linkz(event);return false;" />
            <input type="button" name="undo" id="UndoButt" class="small" value="undo" onclick="UndoThat(event);return false;" title="this button takes you back to just before your last magic edit" />
            </div>
            <p />
            <a href="javascript:newWin('/forum/bbcode.php', 600, 400)"><b>BB Code Examples</b></a>
            <p />
            <input type="Checkbox" name="previewMe" value="1" /> Preview your reply.
            </td>
        </tr>
        <tr>
            <td></td>
            <td><br>
            <input type="submit" value="Post Now" class="button">
            <input type="button" value="Cancel" onclick="history.back();" class="button">
            </td>
        </tr>
        </form>
        </table>
        
        </td>
    </tr>
    </table>
    
    <script language="JavaScript">
    function checkPM() {
        // set our variables
        strMessage = "Please provide the following:\n";
        intCount = 0;
        
        // make sure our required variables are set
        if (document.myForm.strTitle.value == "") { strMessage += "Message Title\n"; intCount++ }
        if (document.myForm.txtContent.value == "") { strMessage += "Message Body\n"; intCount++ }
        
        // stop, if we need to
        if (intCount > 0) {
            alert(strMessage);
            return false;
        }
        // all good
        return true;
    }
    function smiley(face,page) {
        // specify our value
        if (page == 1) {
            document.myForm.txtContent.value += ' ' + face;
            document.myForm.txtContent.focus();
        } else {
            document.myForm.txtContent.value += ' ' + face;
        }
    }
    </script>
    
<?php
    // include our footer
    require("footer.php");
?>
    