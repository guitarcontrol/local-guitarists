<?php
    /*
        update_post_bb.php
        
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
        $arrForum = $dbConn->getRow("
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
        $pageTitle = "Guitar Discussions: " . $arrForum["strName"] . ": Preview '" . trim($_POST["strTitle"]) . "'";
        $areaName = "forums";
        
        // include our header
        require("header.php");
    ?>
        <script language="JavaScript" src="/inc/func.js"></script>
        
        <br>
        <div align="center">
        <form name="myForm" action="update_post_bb.php" method="post" onSubmit="return checkPostEdit()">
        <input type="Hidden" name="ID" value="<?php print $_POST["ID"]; ?>" />
        <input type="Hidden" name="intMemID" value="<?php print $_POST["intMemID"]; ?>" />
        <input type="Hidden" name="intForum" value="<?php print $arrForum["ID"]; ?>" />
        <table width="100%" cellspacing="0" cellpadding="1" border="0">
        <tr valign="top" align="center">
            <td>
            
            <table width="100%" cellspacing="0" cellpadding="2" border="0">
            <tr>
                <td class="tablehead">&nbsp;&raquo;&nbsp;<a href="/index.php"><b>Home</b></a>&nbsp;&raquo;&nbsp;<a href="/forum/index.php"><b>Guitar Discussions</b></a>&nbsp;&raquo;&nbsp;<a href="/forum/topics_bb.php?forum=<?php print $arrForum["ID"]; ?>"><b><?php print $arrForum["strName"]; ?></b></a>&nbsp;&raquo;&nbsp;Preview '<?php print trim(stripslashes($_POST["strTitle"])); ?>'</td>
            </tr>
            </table>
            
            <table width="720" cellspacing="0" cellpadding="2" border="0">
            <tr>
                <td>Title:</td>
                <td><input type="text" name="strTitle" value="<?php print trim(stripslashes($_POST["strTitle"])); ?>" size="60" maxlength="150" class="input"></td>
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
                <input type="button" value="url" title="you willll be asked to supply a URL and a title for this link" class="small" onclick="linkz(event);return false;" />
                <input type="button" name="undo" id="UndoButt" class="small" value="undo" onclick="UndoThat(event);return false;" title="this button takes you back to just before your last magic edit" />
                </div>
                <p />
                <a href="javascript:newWin('/forum/bbcode.php', 600, 400)"><b>BB Code Examples</b></a>
                <br /><br />
                </td>
            </tr>
            <tr>
                <td colspan="2"><br /></td>
            </tr>
            <tr>
                <td nowrap>Reason:</td>
                <td><input type="text" name="strEditReason" value="<?php print trim(htmlspecialchars(strip_tags(stripslashes($_POST["strEditReason"])))); ?>" size="60" maxlength="250" class="input" /></td>
            </tr>
            <?php
                // see if they're a mod or not
                if ($_SESSION["AccessLevel"] >= 90) {
                    ?>
                    <tr>
                        <td>Read Only: </td>
                        <td colspan="2">
                        <input type="radio" name="bitReply" value="0"<?php if ($_POST["bitReply"] == 0) { print " checked"; } ?> /> Yes
                        <input type="radio" name="bitReply" value="1"<?php if ($_POST["bitReply"] == 1) { print " checked"; } ?> /> No
                        </td>
                    </tr>
                    <tr>
                        <td>Sticky: </td>
                        <td colspan="2">
                        <input type="radio" name="intSticky" value="1"<?php if ($_POST["intSticky"] == 1) { print " checked"; } ?> /> Yes
                        <input type="radio" name="intSticky" value="0"<?php if ($_POST["intSticky"] == 0) { print " checked"; } ?> /> No
                        </td>
                    </tr>
                    <?php
                } else {
                    ?>
                    <input type="Hidden" name="bitReply" value="<?php print $_POST["bitReply"]; ?>" />
                    <input type="Hidden" name="intSticky" value="<?php print $_POST["intSticky"]; ?>" />
                    <?php
                }
            ?>
            <tr>
                <td></td>
                <td><br />
                <input type="Checkbox" name="preview" value="1" /> Check here to preview the post before posting it.
                <p />
                <input type="submit" value="Post Now" class="smbutton">
                <input type="button" value="Cancel" onclick="location.href='/forum/view_bb.php?forum=<?php print $arrForum["ID"]; ?>&thread=<?php print $_POST["ID"]; ?>';" class="smbutton">
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
                        <p>
                        <b style="color: #cc3333;">Edited</b> by <b><?php print $_SESSION["Username"]; ?></b> on <?php print date("n/j/Y \@ g:s A"); ?>
                        <?php
                            // see if they provided a reason
                            if (!empty($_POST["strEditReason"])) {
                                print ": " . $_POST["strEditReason"];
                            }
                        ?>
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
        $postTitle = trim(htmlspecialchars(strip_tags($_POST["strTitle"])));
        $postTitle = stripslashes($postTitle);
        $postText = trim(htmlspecialchars(strip_tags($_POST["txtPost"])));
        $postText = stripslashes($postText);
        
        // update the database with the new text.  first, see if someone else has edited it
        if ($_POST["intMemID"] != $_SESSION["MemberID"] || !empty($_POST["strEditReason"])) {
            $editText = "<b style=\"color: #cc3333;\">Edited</b> by <b>" . $_SESSION["Username"] . "</b> on " . date("n/j/Y \@ g:s A");
            
            // see if they provided a reason
            if (strlen($_POST["strEditReason"])) {
                $editText .= ": " . $_POST["strEditReason"];
            }
        } else {
            $editText = "";
        }
        
        // update the thread info
        $qryUpdate = $dbConn->query("
            update  topics 
            set     strTitle = '" . trim(addslashes($postTitle)) . "',
                    txtPost = '" . trim(addslashes($postText)) . "',
                    txtEdited = '" . trim(addslashes($editText)) . "',
                    bitReply = " . $_POST["bitReply"] . ",
                    intSticky = " . $_POST["intSticky"] . "
            where   ID = " . $_POST["ID"]);
        
        // all done
        print "
        <script language=\"JavaScript\">
        alert(\"This post was successfully updated.\");
        location.replace(\"/forum/view_bb.php?forum=" . $_POST["intForum"] . "&thread=" . $_POST["ID"] . "\");
        </script>";
    }
?>
