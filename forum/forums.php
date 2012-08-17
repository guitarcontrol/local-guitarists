<?php
    /*
        forums.php
        
        Here we'll display any sub-categories for the chosen forum.  This 
        way there's no error if they happen to click on a wrong link.
    */
    
    // include our needed file(s)
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    
    // see if old links are being used (
    if (ereg("\.[html]$", $_SERVER["REQUEST_URI"]) || !isset($_GET["id"])) {
        // redirect to new pages
        print "<script language=\"JavaScript\">location.replace(\"/forum/index.php\");</script>\n";
        exit;
    }
    
    // query the db based on the forum ID passed
    $qryCat = $dbConn->getRow("
        select  categories.ID as catID,
                categories.strTitle,
                forums.intPrivate,
                forums.intActive
        from    categories,
                forums
        where   categories.ID = " . $_GET["id"] . " and 
                categories.ID = forums.intParent
        limit 1",
        DB_FETCHMODE_ASSOC);
    
    // if no records were found, stop here
    if (!count($qryCat)) {
        print "
        <script language=\"JavaScript\">
        alert(\"This forum has been moved or does not exist.  Please try again.\");
        location.replace(\"/forum/index.php\");
        </script>";
        exit();
    }
    
    // if the forum isn't active, take them out of here
    if (!$qryCat["intActive"] && $_SESSION["AccessLevel"] < 90) {
        print "
        <script language=\"JavaScript\">
        alert(\"This forum is currently not active.  You are now being redirected.\");
        location.replace(\"/forum/index.php\");
        </script>";
        exit();
    }
    
    // query the db for our info
    $qryForums = $dbConn->query("
        select      categories.ID as catID,
                    categories.strTitle,
                    forums.ID,
                    forums.strName, 
                    forums.txtDescription,
                    forums.intAdmin,
                    forums.intTopics,
                    forums.intPosts, 
                    forums.intLastID,
                    forums.intPrivate,
                    forums.intSort,
                    forums.strLastName,
                    forums.dateChanged
        from        categories,
                    forums
        where       categories.ID = " . $_GET["id"] . " and 
                    categories.ID = forums.intParent
        order by    categories.ID,
                    forums.intSort,
                    forums.strName");
    
    // set our page defaults
    $pageTitle = "Guitar Forums: Discuss guitar related topics with other community members";
    $pageDescription = "Take part in a wide variety of guitar and guitar related discussions covering equipment, recording, tablature, and more.";
    $pageKeywords = "guitar, chat, forum, topic, thread, guitarists, guitars";
    $areaName = "forums";
    
    // include our header
    require("header.php");
?>

    <br>
    <table width="100%" cellpadding="0" cellspacing="2" border="0">
    <tr>
        <td class="tablehead">&nbsp;&raquo;&nbsp;<a href="/index.php"><b>Home</b></a>&nbsp;&raquo;&nbsp;<a href="/forum/index.php"><b>Guitar Discussions</b></a></td>
    </tr>
    </table>
            
    <table width="100%" cellspacing="0" cellpadding="2" border="0">
    <tr valign="top">
        <td align="center">
    
        <!--- begin layout file --->
        <table width="100%" cellspacing="1" cellpadding="3" border="0">
        <tr>
            <td colspan="6">
            <table width="100%" cellpadding="0" cellspacing="1" border="0">
            <form name="myChoiceTop">
            <tr>
                <td align="right" class="smalltxt">
                <b>Options:</b>
                <select name="option" class="dropdown" onChange="location.href=this.value">
                    <option value="/forum/search.php">&nbsp;&raquo;&nbsp;Search Threads</option>
                    <?php
                        // see if they're logged in
                        if ($_SESSION["MemberID"]) {
                            ?>
                            <option value="/forum/myposts.php">&nbsp;&raquo;&nbsp;View Your Posts</option>
                            <option value="/members/msgs/index.php">&nbsp;&raquo;&nbsp;View Private Messages</option>
                            <?php
                        } else {
                            ?>
                            <option value="/register/index.php">&nbsp;&raquo;&nbsp;Register To Post</option>
                            <option value="/login.php">&nbsp;&raquo;&nbsp;Login To Post</option>
                            <?php
                        }
                    ?>
                    <option value="/forum/recent.php">&nbsp;&raquo;&nbsp;Most Recent Posts</option>
                    <option value="/forum/posters.php">&nbsp;&raquo;&nbsp;Top 50 Posters</option>
                    <option value="/forum/index.php">&nbsp;&raquo;&nbsp;Home</option>
                </select>
                <input type="Button" value="Go!" class="smbutton" onClick="location.href=document.myChoiceTop.option.options[document.myChoiceTop.option.selectedIndex].value">
                </td>
            </tr>
            </form>
            </table>
            </td>
        </tr>
        <tr>
            <td align="center" class="innertitle" colspan="2">Forum</td>
            <td align="center" class="innertitle">Topics</td>
            <td align="center" class="innertitle">Replies</td>
            <td align="center" class="innertitle">Last Post</td>
        </tr>
        <?php
            // set wether we've shown the title
            $intDisplayTitle = 0;
            
            // loop through our query results
            while ($qryRow = $qryForums->fetchRow(DB_FETCHMODE_ASSOC)) {
                // see if we should display the title
                if (!$intDisplayTitle) {
                    ?>
                    <tr>
                        <td colspan="6" class="innerhead"><b><?php print $qryRow["strTitle"]; ?></b></td>
                    </tr>
                    <?php
                    // mark it so we don't display it again
                    $intDisplayTitle = 1;
                }
                ?>
                <tr>
                    <td bgcolor="#f6f6f6"></td>
                    <td bgcolor="#f6f6f6"><img src="/images/pointer.gif" width="11" height="11" alt="" border="0">&nbsp;<a href="/forum/topics_bb.php?forum=<?php print $qryRow["ID"]; ?>"><b><?php print $qryRow["strName"]; ?></b></a><br>
                    <?php print $qryRow["txtDescription"]; ?></td>
                    <td align="center" valign="middle" class="innerhead"><?php print number_format($qryRow["intTopics"]); ?></td>
                    <td align="center" valign="middle" class="innerhead"><?php print number_format($qryRow["intPosts"]); ?></td>
                    <td align="center" valign="middle" class="innerhead" nowrap>
                    <?php
                    // see if we need to display the date/time and user of the last post
                    if (!$qryRow["intTopics"] && !$qryRow["intPosts"]) {
                        print "---";
                    } else {
                        print date("M\. j g\:i A", strtotime($qryRow["dateChanged"])) . "<br><b><a href=\"/members/profile.php?user=" . $qryRow["intLastID"] . "\">" . $qryRow["strLastName"] . "</a></b>";
                    }
                    ?>
                    </td>
                </tr>
                <?php
            }
        ?>
        </table>
        <!--- end layout file --->
        </td>
        <?php //require("fastclick.php"); ?>
    </tr>
    </table>

<?php
    // include our footer
    require("footer.php");
?>
