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
    
    // see if they chose to preview the code, or they cursed in it
    if ($preview || $error) {
        // get the forum title from the db for this topic
        $qryForum = $dbConn->getRow("
            select  ID,
                    strName
            from    forums
            where   ID = '" . $_POST["intForum"] . "'",
            DB_FETCHMODE_ASSOC);
        
        // query the appropriate forums
        $sqlText = "select ID, strTitle, intSort from categories where intParent = 24";
        
        // if they're not an admin, only pull active forums
        if ($_SESSION["AccessLevel"] < 90) {
            $sqlText .= " and intActive = 1";
        }
        $sqlText .= " order by intSort";
        
        // select our main categories to display our forums
        $qryCats = $dbConn->query($sqlText);
        
        // set our page variables
        $pageTitle = "Guitar Discussions: " . $qryForum["strName"] . ": Start a new thread";
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
        <script language="JavaScript" type="text/javascript" src="/inc/functions.js"></script>
        
        <br>
        <div align="center">
        <form name="myForm" action="submit_bb.php" method="post" onSubmit="return checkPost()">
        <input type="hidden" name="strName" value="<?php print $qryForum["strName"]; ?>">
        <table width="100%" cellspacing="0" cellpadding="1" border="0">
        <tr valign="top" align="center">
            <td>
            
            <?php if (empty($_SESSION["GGCIFrame"])) { ?>
            <table width="100%" cellspacing="0" cellpadding="2" border="0">
            <tr>
                <td class="tablehead">&nbsp;&raquo;&nbsp;<a href="/index.php"><b>Home</b></a>&nbsp;&raquo;&nbsp;<a href="/forum_ggc/index.php"><b>Guitar Discussions</b></a>&nbsp;&raquo;&nbsp;<a href="/forum_ggc/topics_bb.php?forum=<?php print $_POST["intForum"]; ?>"><b><?php print $qryForum["strName"]; ?></b></a>&nbsp;&raquo;&nbsp;Start a new thread</td>
            </tr>
            </table>
            <?php } ?>
            
            <table width="720" cellspacing="0" cellpadding="2" border="0">
            <tr valign="top">
                <td width="100">Forum</td>
                <td>
                <select name="intForum" class="dropdown">
                    <?php
                        // loop through our categories and grab the forums
                        while ($qryRow = $qryCats->fetchRow(DB_FETCHMODE_ASSOC)) {
                            
                            print "
                            <option value=\"\">" . $qryRow["strTitle"];
                            
                            // show our categories
                            show_forums($qryRow["ID"], '&nbsp;&nbsp;&nbsp;&nbsp;', $_POST["intForum"], $_SESSION["AccessLevel"], $dbConn);
                        }
                    ?>
                </select>
                </td>
                <td width="300" rowspan="10">
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
            <tr>
                <td>Title:</td>
                <td><input type="text" name="strTitle" value="<?php print trim(str_replace('"', "&quot;", stripslashes($_POST["strTitle"]))); ?>" size="60" maxlength="150" class="input"></td>
            </tr>
            <tr valign="top">
                <td>Message:</td>
                <td>
                <textarea name="txtPost" cols="70" rows="25" wrap="virtual" class="input"><?php print trim(str_replace('"', "&quot;", stripslashes($_POST["txtPost"]))); ?></textarea><br />
                <div class="left" id="js-buttons">
                <input type="button" value="bold" title="subtly (if you have anti-alaising) bolded text" class="small" onclick="boldz(event);return false;" />
                <input type="button" value="ital" title="italic text (slanty)" class="small" onclick="italicz(event);return false;" />
                <input type="button" value="block" title="blocktext" class="small" onclick="simcode(event);return false;" />
                <input type="button" value="img" title="simple image tag" class="small" onclick="doimage(event);return false;" />
                <input type="button" value="url" title="you willll be asked to supply a URL and a title for this link" class="small" onclick="linkz(event);return false;" />
                <input type="button" name="undo" id="UndoButt" class="small" value="undo" onclick="UndoThat(event);return false;" title="this button takes you back to just before your last magic edit" />
                </div>
                </td>
            </tr>
        <?php
            // see if they're a mod or not
            if ($_SESSION["AccessLevel"] >= 90) {
                ?>
                <tr>
                    <td>Read Only</td>
                    <td colspan="2">
                    <input type="radio" name="bitReply" value="0"<?php if (!$_POST["bitReply"]) { print " checked"; } ?>> Yes
                    <input type="radio" name="bitReply" value="1"<?php if ($_POST["bitReply"]) { print " checked"; } ?>> No
                    </td>
                </tr>
                <tr>
                    <td>Sticky</td>
                    <td colspan="2">
                    <input type="radio" name="intSticky" value="1"<?php if ($_POST["intSticky"]) { print " checked"; } ?>> Yes
                    <input type="radio" name="intSticky" value="0"<?php if (!$_POST["intSticky"]) { print " checked"; } ?>> No
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
                <input type="Hidden" name="bitReply" value="<?php print $_POST["bitReply"]; ?>">
                <input type="Hidden" name="intSticky" value="<?php print $_POST["intSticky"]; ?>">
                <?php
            }
        ?>
            <tr>
                <td colspan="2"><br /></td>
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
                <input type="button" value="Cancel" onclick="location.href='/forum_ggc/topics_bb.php?forum=<?php print $_POST["intForum"]; ?>';" class="smbutton">
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
        if (empty($_SESSION["GGCIFrame"])) {
            require("footer.php");
        }
        exit();
    } else {
        // remove our bad characters from our text
        $postTitle = htmlspecialchars(strip_tags($_POST["strTitle"]));
        $postTitle = stripslashes($postTitle);
        $postText = htmlspecialchars(strip_tags($_POST["txtPost"]));
        $postText = stripslashes($postText);
        
        // add the post to the database
        $qryAddPost = $dbConn->query("
            insert into topics ( 
                intForum, 
                strTitle, 
                intReplies, 
                intMemID,
                datePosted,
                txtPost,
                dateLastPost,
                bitReply,
                intSticky
            ) values ( 
                " . $_POST["intForum"] . ", 
                '" . addslashes($postTitle) . "', 
                0, 
                " . $_SESSION["MemberID"] . ",
                Now(),
                '" . addslashes($postText) . "',
                Now(),
                " . $_POST["bitReply"] . ",
                " . $_POST["intSticky"] . "
            )");
        
        // reset the session var
        $_SESSION["SubText"] = "";
        
        // see if they chose to subscribe to this thread
        if (!empty($_POST["subscribe"])) {
            // get the last added ID from the db
            $lastID = mysql_insert_id($dbConn->connection);
            
            // subscribe them
            $qrySave = $dbConn->query("
                insert into saved (
                    intType,
                    intMemID,
                    intItem
                ) values (
                    2,
                    '" . $_SESSION["MemberID"] . "',
                    '" . $lastID . "'
                )");
        }
        
        // get our number of posts for this user
        $qryMem = $dbConn->getRow("
            select  strUsername,
                    intPosts,
                    intAccess
            from    members
            where   ID = '" . $_SESSION["MemberID"] . "'",
            DB_FETCHMODE_ASSOC);
        
        // get our number of active threads for this forum
        $qryCount = $dbConn->getRow("
            select  count(ID) as totals
            from    topics
            where   intForum = '" . $_POST["intForum"] . "'",
            DB_FETCHMODE_ASSOC);
        
        // update our forum with the new totals
        $qryUpdate = $dbConn->query("
            update    forums 
            set        intTopics = " . $qryCount["totals"] . ", 
                    intLastID = " . $_SESSION["MemberID"] . ",
                    strLastName = '" . $qryMem["strUsername"] . "',
                    dateChanged = Now()
            where     ID = " . $_POST["intForum"]);
        
        // update our post count
        $intPosts = $qryMem["intPosts"] + 1;
        
        // see if we need to update their settings (profile info)
        $changeAccess = 0;
        
        // specify a new access level (if needed)
        if (($intPosts >= 5 && $intPosts < 100) and $qryMem["intAccess"] < 2) {
            $newAccess = 2;
            $changeAccess = 1;
        } else if (($intPosts >= 100 && $intPosts < 250) && $qryMem["intAccess"] < 3) {
            $newAccess = 3;
            $changeAccess = 1;
        } else if (($intPosts >= 250 && $intPosts < 500) && $qryMem["intAccess"] < 4) {
            $newAccess = 4;
            $changeAccess = 1;
        } else if (($intPosts >= 500 && $intPosts < 750) && $qryMem["intAccess"] < 5) {
            $newAccess = 5;
            $changeAccess = 1;
        } else if (($intPosts >= 750 && $intPosts < 1000) && $qryMem["intAccess"] < 6) {
            $newAccess = 6;
            $changeAccess = 1;
        } else if (($intPosts >= 1000 && $intPosts < 1500) && $qryMem["intAccess"] < 14) {
            $newAccess = 14;
            $changeAccess = 1;
        } else if ($intPosts >= 1500 && $qryMem["intAccess"] < 20) {
            $newAccess = 20;
            $changeAccess = 1;
        }
        
        // create our SQL to update the members info
        $sqlText = "
            update     members 
            set     intPosts = " . $intPosts;
        
        // see if we need to change their access level
        if ($changeAccess) {
            $sqlText .= ",
                    intAccess = " . $newAccess;
        }
        
        $sqlText .= "    where    ID = " . $_SESSION["MemberID"];
        
        // update the db
        $qryUpdateMem = $dbConn->query($sqlText);
        
        // if we updated their level, see if anything new is avialable for them
        if ($changeAccess) {
            print "
            <script language=\"JavaScript\">
            alert(\"Your access level has been updated.  You may now have a few\\n\" +
                  \"features at your disposal.  Go to \/members\/ to view\\n\" +
                  \"them.  And thanks again for supporting Guitarists.net.)\");
            </script>";
        }
        
        // redirect them
        print "
        <script language=\"JavaScript\">
        location.replace(\"/forum_ggc/topics_bb.php?forum=" . $_POST["intForum"] . "\");
        </script>";
    }
?>
