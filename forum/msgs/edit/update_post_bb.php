<?php
    /*
        submit.php
        
        Here we process the form and add the post into the database.
    */
    
    // include our needed file(s)
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    require("functions.php");
    
    // set the default preview option to 0
    $preview = 0;
    $error = 0;
    
    // make sure the post doesn't contain foul language
    if (curseFilter($_POST["strTitle"]) || curseFilter($_POST["txtPost"])) {
        $error = 1;
    }
    
    // see if they chose to preview the post
    if (!empty($_POST["previewMe"])) {
        $preview = 1;
    }
    
    // if any words were found, stop here
    if ($preview || $error) {
        // set our page variables
        $pageTitle = "Guitar Discussion: Private Message: Preview update";
        $areaName = "forums";
        
        // include our header
        require("header.php");
    ?>
        <script language="JavaScript" src="/inc/func.js"></script>

        <br>
        <form name="myForm" action="update_post_bb.php" method="post" onSubmit="return checkPM()">
        <input type="Hidden" name="ID" value="<?php print $_POST["ID"]; ?>">
        <table width="100%" cellspacing="0" cellpadding="1" border="0">
        <tr valign="top">
            <td align="center">
            
            <table width="100%" cellspacing="0" cellpadding="2" border="0">
            <tr>
                <td class="tablehead">&nbsp;&raquo;&nbsp;<a href="/index.php"><b>Home</b></a>&nbsp;&raquo;&nbsp;<a href="/forum/index.php"><b>Guitar Discussion</b></a>&nbsp;&raquo;&nbsp;<a href="/forum/msgs/index.php"><b>Private Messages</b></a>&nbsp;&raquo;&nbsp;Edit Post '<?php print trim(str_replace('"', "&quot;", $_POST["strTitle"])); ?>'</td>
            </tr>
            </table>
            
            <table width="720" cellspacing="0" cellpadding="2" border="0">
            <tr>
                <td>Title:</td>
                <td><input type="text" name="strTitle" value="<?php print trim(str_replace('"', "&quot;", $_POST["strTitle"])); ?>" size="60" maxlength="150" class="input"></td>
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
                <textarea name="txtPost" cols="80" rows="25" wrap="virtual" class="input"><?php print trim(str_replace('"', "&quot;", $_POST["txtPost"])); ?></textarea>
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
                    $parser = new HTML_BBCodeParser(parse_ini_file("../../BBCodeParser.ini"));
                    $postText = trim($parser->qParse(htmlspecialchars(strip_tags($_POST['txtPost']))));
                    
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
        
        <script language="JavaScript">
        function checkPM() {
            // set our variables
            strMessage = "Please provide the following:\n";
            intCount = 0;
            
            // make sure our required variables are set
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
        require("footer.php");
        exit();
    }
    
    // set our carraige returns
    $crlf = chr(10);
    
    // make sure the title was specified
    if (empty($_POST["strTitle"])) {
        $postTitle = "Untitled PM to " . $qryUser["strUsername"];
    } else {
        $postTitle = strip_tags($_POST["strTitle"]);
    }
    
    // strip any tags and slashes from our data
    $postTitle = stripslashes($postTitle);
    $postText = strip_tags($_POST["txtPost"]);
    $postText = stripslashes($postText);
    
    // update the database with the new text
    $qryUpdate = $dbConn->query("
        update  msg_main
        set     strTitle = '" . addslashes(trim($postTitle)) . "',
                txtContent = '" . addslashes(trim($postText)) . "'
        where   ID = " . $_POST["ID"]);
    
    // update our redirect
    $strURL = "/forum/msgs/view_bb.php?id=" . $_POST["ID"];
    
    // all done
    print "
    <script language=\"JavaScript\">
    alert(\"This post was successfully updated.\");
    location.replace(\"" . $strURL . "\");
    </script>";
?>
