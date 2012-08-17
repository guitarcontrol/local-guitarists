<?php
    /*
        post.php
        
        This is the main script that allows a member to edit their post they 
        made to another member.
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
    if (!isset($_GET["id"])) {
        print "
        <script language=\"JavaScript\">
        alert(\"Please choose a thread to edit first.\");
        window.close();
        </script>";
        exit();
    }
    
    // get the forum title from the db for this topic
    $qryPost = $dbConn->getRow("
        select  ID,
                strTitle,
                txtContent,
                intMemID
        from    msg_main
        where   ID = " . $dbConn->quote($_GET["id"]),
        DB_FETCHMODE_ASSOC);
    
    // continue, based on how many records were found
    if (!count($qryPost)) {
        print "
        <script language=\"JavaScript\">
        alert(\"Please choose a valid reply to edit first.\");
        window.close();
        </script>";
        exit();
    }
    
    // make sure they have the privelege to edit this reply
    if ($qryPost["intMemID"] != $_SESSION["MemberID"] && $_SESSION["AccessLevel"] < 90) {
        print "
        <script language=\"javascript\">
        alert(\"It appears you do not have permission to edit this message.\");
        window.close();
        </script>";
        exit();
    }
    
    // swap out our smilies
    $postText = smilies($qryPost["txtContent"], '1');
    
    // fix our title for display
    $postTitle = str_replace('"', "&quot;", stripslashes(strip_tags($qryPost["strTitle"])));
    
    // change our HTML back to GML
    $postText = str_replace('"', "&quot;", stripslashes(strip_tags($qryPost["txtContent"])));
    
    // update our page variables
    $pageTitle = "Guitar Discussion: Edit Private Message";
    $pageDescription = "";
    $pageKeywords = "";
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
    <form name="myForm" action="update_post_bb.php" method="post" onSubmit="return checkPM()">
    <input type="hidden" name="ID" value="<?php print $qryPost["ID"]; ?>">
    <table width="100%" cellspacing="0" cellpadding="1" border="0">
    <tr valign="top">
        <td align="center">
        
        <?php if (empty($_SESSION["GGCIFrame"])) { ?>
        <table width="100%" cellspacing="2" cellpadding="2" border="0">
        <tr>
            <td class="tablehead">&nbsp;&raquo;&nbsp;<a href="/index.php"><b>Home</b></a>&nbsp;&raquo;&nbsp;<a href="/forum_ggc/index.php"><b>Guitar Discussion</b></a>&nbsp;&raquo;&nbsp;<a href="/forum_ggc/msgs/index.php"><b>Private Messages</b></a>&nbsp;&raquo;&nbsp;Edit '<?php print $postTitle; ?>'</td>
        </tr>
        </table>
        <?php } ?>
        
        <table width="720" cellspacing="0" cellpadding="2" border="0">
        <tr valign="top">
        <tr>
            <td>Title:</td>
            <td><input type="text" name="strTitle" value="<?php print $postTitle; ?>" size="60" maxlength="150" class="input"></td>
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
            <textarea name="txtPost" cols="80" rows="25" wrap="virtual" class="input"><?php print $postText; ?></textarea>
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
            <input type="submit" value="Update Post" class="button">
            <input type="button" value="Cancel" onclick="location.href='../view_bb.php?id=<?php print $_GET["id"]; ?>';" class="smbutton">
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
