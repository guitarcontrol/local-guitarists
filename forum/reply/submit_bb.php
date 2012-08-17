<?php
    /*
        submit.php
        
        Here we process the form and add the post into the database.
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
    
    // update our sub text
    $_SESSION["SubText"] = post_to_array($_POST);
    
    // set the default preview option to 0
    $preview = 0;
    $error = 0;
    
    // see if they chose to preview the post
    if (!empty($_POST["preview"])) {
        $preview = 1;
    }
    
    // make sure the post doesn't contain foul language
    if (curseFilter($_POST["strTitle"]) || curseFilter($_POST["txtPost"])) {
        $error = 1;
    }
    
    // set our carraige returns
    $crlf = chr(10);
    
    // get the forum title from the db for this topic
    $qryTopic = $dbConn->getRow("
        select  ID,
                intForum,
                intMemID,
                strTitle
        from    topics
        where   ID = '" . $_POST["intTopic"] . "'",
        DB_FETCHMODE_ASSOC);
    
    // see if they chose to preview the code, or they cursed in it
    if ($preview || $error) {
        // get the forum title from the db for this topic
        $qryForum = $dbConn->getRow("
            select  ID,
                    strName
            from    forums
            where   ID = '" . $qryTopic["intForum"] . "'",
            DB_FETCHMODE_ASSOC);
        
        // set our page variables
        $pageTitle = "Guitar Discussions: " . $qryForum["strName"] . ": Reply to " . $qryTopic["strTitle"];
        $areaName = "forums";
        
        // include our header
        require("header.php");
    ?>
        <script language="JavaScript" src="/inc/func.js"></script>
        
        <br>
        <div align="center">
        <form name="myForm" action="submit_bb.php" method="post" onSubmit="return checkPost()">
        <input type="hidden" name="intForum" value="<?php print $_POST["intForum"]; ?>">
        <input type="hidden" name="intTopic" value="<?php print $_POST["intTopic"]; ?>">
        <input type="hidden" name="intMemID" value="<?php print $_POST["intMemID"]; ?>">
        <table width="100%" cellspacing="0" cellpadding="1" border="0">
        <tr valign="top" align="center">
            <td>
            
            <table width="100%" cellspacing="0" cellpadding="2" border="0">
            <tr>
                <td class="tablehead">&nbsp;&raquo;&nbsp;<a href="/index.php"><b>Home</b></a>&nbsp;&raquo;&nbsp;<a href="/forum/index.php"><b>Guitar Discussions</b></a>&nbsp;&raquo;&nbsp;<a href="/forum/topics_bb.php?forum=<?php print $qryForum["ID"]; ?>"><b><?php print $qryForum["strName"]; ?></b></a>&nbsp;&raquo;&nbsp;Reply to "<?php print $qryTopic["strTitle"]; ?>"</td>
            </tr>
            </table>
            
            <table width="720" cellspacing="0" cellpadding="2" border="0">
            <tr>
                <td>Title:</td>
                <td><input type="text" name="strTitle" value="<?php print trim($_POST["strTitle"]); ?>" size="60" maxlength="150" class="input"></td>
                <td width="300" rowspan="10" valign="top">
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
                <textarea name="txtPost" cols="70" rows="25" wrap="virtual" class="input"><?php print trim(stripslashes($_POST["txtPost"])); ?></textarea><br />
                <div class="left" id="js-buttons">
                <input type="button" value="bold" title="subtly (if you have anti-alaising) bolded text" class="small" onclick="boldz(event);return false;" />
                <input type="button" value="ital" title="italic text (slanty)" class="small" onclick="italicz(event);return false;" />
                <input type="button" value="block" title="blocktext" class="small" onclick="simcode(event);return false;" />
                <input type="button" value="img" title="simple image tag" class="small" onclick="doimage(event);return false;" />
                <input type="button" value="url" title="you will be asked to supply a URL and a title for this link" class="small" onclick="linkz(event);return false;" />
                <input type="button" name="undo" id="UndoButt" class="small" value="undo" onclick="UndoThat(event);return false;" title="this button takes you back to just before your last magic edit" />
                <p />
                <a href="javascript:newWin('/forum/bbcode.php', 600, 400)"><b>BB Code Examples</b></a>
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
                <input type="radio" name="intDisplaySig" value="1"<?php if ($_POST["intDisplaySig"] == 1) { print " checked"; } ?> /> Yes
                <input type="radio" name="intDisplaySig" value="0"<?php if ($_POST["intDisplaySig"] == 0) { print " checked"; } ?> /> No
                </td>
            </tr>
            <tr>
                <td>Subscribe: </td>
                <td>
                <input type="radio" name="subscribe" value="1"<?php if ($_POST["subscribe"] == 1) { print " checked"; } ?> /> Yes
                <input type="radio" name="subscribe" value="0"<?php if ($_POST["subscribe"] == 0) { print " checked"; } ?> /> No
                </td>
            </tr>
            <tr>
                <td></td>
                <td><br />
                <input type="Checkbox" name="preview" value="1" /> Check here to preview the post before posting it.
                <p />
                <input type="submit" value="Post Now" class="smbutton">
                <input type="button" value="Cancel" onclick="location.href='/forum/view_bb.php?forum=<?php print $_POST["intForum"]; ?>&thread=<?php print $_POST["intTopic"]; ?>';" class="smbutton">
                </td>
            </tr>
            <?php
                // see if they chose to preview the text
                if ($preview) {
                    // include our BBCode code
                    require("HTML/BBCodeParser.php");
                    
                    // set our carraige returns
                    $crlf = chr(10);
                    
                    // fix our title
                    $postTitle = trim(htmlspecialchars(strip_tags($_POST["strTitle"])));
                    $postTitle = stripslashes($postTitle);
                    
                    // parse the BB Code and turn it into HTML
                    $parser = new HTML_BBCodeParser(parse_ini_file("../BBCodeParser.ini"));
                    $postText = trim($parser->qParse(htmlspecialchars(strip_tags(stripslashes($_POST['txtPost'])))));
                    
                    // replace our smilies
                    $postText = smilies2($postText, 0);
                    ?>
                    <tr>
                        <td colspan="3"><br /></td>
                    </tr>
                    </table>
                    
                    <table width="100%" cellspacing="0" cellpadding="2" border="0">
                    <tr>
                        <td class="tablehead">&nbsp;&raquo;&nbsp;Preview '<?php print $postTitle; ?>'</td>
                    </tr>
                    </table>
                    
                    <table width="100%" cellspacing="0" cellpadding="2" border="0">
                    <tr>
                        <td colspan="3">
                        <?php print str_replace($crlf, "<br>", $postText); ?>
                        </td>
                    </tr>
                    <?php
                }
            ?>
            </table>
            </form>
            
            </td>
        </tr>
        </table>
        </div>
    
        <?php
        // include our footer
        require("footer.php");
        exit();
    } else {
        // remove our bad characters from our text
        $postTitle = htmlspecialchars(strip_tags($_POST["strTitle"]));
        $postTitle = stripslashes($postTitle);
        $postText = htmlspecialchars(strip_tags($_POST["txtPost"]));
        $postText = stripslashes($postText);
        
        // addour reply to the db
        $qryAddReply = $dbConn->query("
            insert into replies ( 
                intTopic, 
                strTitle, 
                txtReply,
                intMemID,
                intDisplaySig,
                datePosted
            ) values ( 
                " . $_POST["intTopic"] . ", 
                '" . addslashes(trim($postTitle)) . "', 
                '" . addslashes(trim($postText)) . "',
                " . $_SESSION["MemberID"] . ",
                " . $_POST["intDisplaySig"] . ",
                Now()
            )");
        
        // get the last added ID from the db
        $qryLast_ID = mysql_insert_id($dbConn->connection);
        
        // see if they chose to subscribe to this thread
        if (!empty($_POST["subscribe"])) {
            // see if they're already 
            $qryExists = $dbConn->getRow("
                select  COUNT(*) as totals
                from    saved
                where   intType = 2 and
                        intMemID = '" . $_SESSION["MemberID"] . "' and
                        intItem = '" . $_POST["intTopic"] . "'",
                DB_FETCHMODE_ASSOC);
            
            // subscribe them
            if (!$qryExists["totals"]) {
                $qrySave = $dbConn->query("
                    insert into saved (
                        intType,
                        intMemID,
                        intItem
                    ) values (
                        2,
                        '" . $_SESSION["MemberID"] . "',
                        '" . $_POST["intTopic"] . "'
                    )");
            }
        }
        
        // get our counting info from various tables
        $qryCount = $dbConn->getRow("
            select  topics.intReplies,
                    topics.intMemID,
                    forums.ID,
                    forums.intPosts,
                    members.intPosts as memPosts,
                    members.strUsername,
                    members.intAccess
            from    forums,
                    topics,
                    members
            where   topics.ID = '" . $_POST["intTopic"] . "' and
                    topics.intForum = forums.ID and 
                    members.ID = '" . $_SESSION["MemberID"] . "'",
            DB_FETCHMODE_ASSOC);
        
        // set our new total
        $postCount = $qryCount["intPosts"] + 1;
        $memPosts = $qryCount["memPosts"] + 1;
        
        // get the # of replies in the db for this topic
        $qryReplyCount = $dbConn->getRow("
            select  count(*) as totals
            from    replies
            where   intTopic = '" . $_POST["intTopic"] . "'",
            DB_FETCHMODE_ASSOC);
        
        $replyCount = $qryReplyCount["totals"];
        
        // update the topics data
        $qryUpdateTopic = $dbConn->query("
            update  topics 
            set     intReplies = " . $replyCount . ", 
                    strLastPost = '" . trim($qryCount["strUsername"]) . "',
                    intLastID = " . $_SESSION["MemberID"] . ",
                    dateLastPost = Now()
            where   ID = " . $_POST["intTopic"]);
        
        // update the forum
        $qryUpdateForum = $dbConn->query("
            update  forums 
            set     intPosts = " . $postCount . ", 
                    strLastName = '" . trim($qryCount["strUsername"]) . "',
                    intLastID = " . $_SESSION["MemberID"] . ",
                    dateChanged = Now()
            where   ID = " . $qryCount["ID"]);
        
        // see if we need to update their settings (profile info)
        $changeAccess = 0;
        
        // specify a new access level (if needed) --->
        if (($memPosts >= 5 && $memPosts < 100) and $qryCount["intAccess"] < 2) {
            $newAccess = 2;
            $changeAccess = 1;
        } else if (($memPosts >= 100 && $memPosts < 250) && $qryCount["intAccess"] < 3) {
            $newAccess = 3;
            $changeAccess = 1;
        } else if (($memPosts >= 250 && $memPosts < 500) && $qryCount["intAccess"] < 4) {
            $newAccess = 4;
            $changeAccess = 1;
        } else if (($memPosts >= 500 && $memPosts < 750) && $qryCount["intAccess"] < 5) {
            $newAccess = 5;
            $changeAccess = 1;
        } else if (($memPosts >= 750 && $memPosts < 1000) && $qryCount["intAccess"] < 6) {
            $newAccess = 6;
            $changeAccess = 1;
        } else if (($memPosts >= 1000 && $memPosts < 1500) && $qryCount["intAccess"] < 14) {
            $newAccess = 14;
            $changeAccess = 1;
        } else if ($memPosts >= 1500 && $qryCount["intAccess"] < 20) {
            $newAccess = 20;
            $changeAccess = 1;
        }
        
        // get their totals from topics
        $qryPosts = $dbConn->getRow("
            select  count(id) as totals
            from    topics
            where   intMemID = '" . $_SESSION["MemberID"] . "'",
            DB_FETCHMODE_ASSOC);
        
        // get their totals from replies
        $qryReplies = $dbConn->getRow("
            select  count(id) as totals
            from    replies
            where   intMemID = '" . $_SESSION["MemberID"] . "'",
            DB_FETCHMODE_ASSOC);
        
        // set the new total
        $newTotal = $qryPosts["totals"] + $qryReplies["totals"];
        
        // update the total in 'members'
        $qryUpdate = $dbConn->query("
            update  members
            set     intPosts = " . $newTotal . "
            where   ID = " . $_SESSION["MemberID"]);
        
        // see if we need to change their access level
        if ($changeAccess) {
            $qryUpdate = $dbConn->query("
                update  members
                set     intAccess = " . $newAccess . "
                where   ID = " . $_SESSION["MemberID"]);
        }
        
        // if we updated their level, see if anything new is avialbale for them
        if ($changeAccess) {
            print "
            <script language=\"JavaScript\">
            alert(\"Your access level has been updated.  You now may have a few\\n\" +
                  \"features at your disposal.  You'll need to logout and back\\n\" +
                  \"in again to see them.  Then go to the Members area.)\");
            </script>";
        }
        
        // include our script to email out updates
        require("email.php");
        
        // set our location to redirect to
        $location = "/forum/view_bb.php?forum=" . $qryCount["ID"] . "&thread=" . $_POST["intTopic"] . "#post" . $qryLast_ID;
        
        // redirect them
        print "
        <script language=\"JavaScript\">
        location.replace(\"/forum/view_bb.php?forum=" . $qryCount["ID"] . "&thread=" . $_POST["intTopic"] . "#post" . $qryLast_ID . "\");
        </script>";
    }
?>
