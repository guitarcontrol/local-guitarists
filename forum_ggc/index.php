<?php
    /*
        index.php

        This is the start page for our forums.  Here we simply display our categories
        organized by parents.  We also offer direct links to various sections they
        have access to.
    */

    // include our needed file(s)
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");

    // create our conditional SQL statement
    $sqlText = "
        select      categories.ID as catID,
                    categories.strTitle,
                    categories.intSort as catSort,
                    forums.ID,
                    forums.strName,
                    forums.txtDescription,
                    forums.intAdmin,
                    forums.intTopics,
                    forums.intPosts,
                    forums.intLastID,
                    forums.intPrivate,
                    forums.strLastName,
                    forums.intSort,
                    forums.dateChanged
        from        categories,
                    forums
        where       categories.intParent = 24 and ";

    // if they're a mod, let them see everything
    if ($_SESSION["AccessLevel"] < 90) {
        $sqlText .= " categories.intActive = 1 and";
    }

    $sqlText .= " categories.ID = forums.intParent";

    // if they're a mod, let them see everything
    if ($_SESSION["AccessLevel"] < 90) {
        $sqlText .= " and forums.intActive = 1";
    }

    // continue
    $sqlText .= "
        order by    categories.intSort,
                    categories.strTitle,
                    forums.intSort,
                    forums.strName";

    // query the data to display for the user
    $qryForums = $dbConn->query($sqlText);

    // set our page defaults
    $pageTitle = "Guitar Forums: Discuss guitar related topics with other community members";
    $pageDescription = "Take part in a wide variety of guitar and guitar related discussions covering equipment, recording, tablature, and more.";
    $pageKeywords = "guitar, chat, forum, topic, thread, guitarists, guitars";
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

    <br />
<?php if (empty($_SESSION["GGCIFrame"])) { ?>
    <table width="100%" cellspacing="1" cellpadding="3" border="0">
    <tr>
        <td class="tablehead">&nbsp;&raquo;&nbsp;<a href="/index.php"><b>Home</b></a>&nbsp;&raquo;&nbsp;Guitar Discussions</td>
    </tr>
    </table>
<?php } ?>

    <table width="100%" cellpadding="0" cellspacing="2" border="0">
    <tr>
        <td class="tablehead">&nbsp;&raquo;&nbsp;Home</td>
    </tr>
    </table>

    <table width="100%" cellspacing="0" cellpadding="2" border="0">
    <tr valign="top">
        <td align="center">

        <!--- begin layout file --->
        <table width="100%" cellspacing="1" cellpadding="3" border="0">
        <tr>
            <td colspan="2">
            <a href="/rss/forum.php" title="RSS" target="_new"><img src="/images/rss.gif" width="28" height="16" alt="RSS" border="0" /></a>
            <a href="http://fusion.google.com/add?feedurl=http%3A//www.guitarists.net/rss/forum.php" title="Google RSS" target="_new"><img src="/images/add_google_rss.gif" width="104" height="17" alt="Google RSS" border="0" /></a>
            <a href="http://add.my.yahoo.com/content?.intl=us&url=http%3A//www.guitarists.net/rss/forum.php" title="Yahoo! RSS" target="_new"><img src="/images/add_yahoo_rss.gif" width="91" height="17" alt="Yahoo! RSS" border="0" /></a>
            </td>
            <td align="right" colspan="3">
            <table cellpadding="0" cellspacing="1" border="0">
            <form name="myChoiceTop">
            <tr>
                <td class="smalltxt" nowrap>
                <b>Options:</b>
                <select name="option" class="dropdown" onChange="location.href=this.value">
                    <option value="/forum_ggc/search.php">&nbsp;&raquo;&nbsp;Search Threads</option>
                    <?php
                        if ($_SESSION["MemberID"]) {
                            ?>
                            <option value="/forum_ggc/myposts.php">&nbsp;&raquo;&nbsp;View Your Posts</option>
                            <option value="/members_ggc/msgs/index.php">&nbsp;&raquo;&nbsp;View Private Messages</option>
                            <?php
                        } else {
                            ?>
                            <option value="/register/index.php">&nbsp;&raquo;&nbsp;Register To Post</option>
                            <option value="/login.php">&nbsp;&raquo;&nbsp;Login To Post</option>
                            <?php
                        }
                    ?>
                    <option value="/forum_ggc/recent.php">&nbsp;&raquo;&nbsp;Most Recent Posts</option>
                    <option value="/forum_ggc/posters.php">&nbsp;&raquo;&nbsp;Top 50 Posters</option>
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
            // set our default rowID
            $catID = 0;
            $totalPosts = 0;
            $totalReplies = 0;

            // display our output
            while ($qryRow = $qryForums->fetchRow(DB_FETCHMODE_ASSOC)) {
                // display our header (if needed)
                if ($catID != $qryRow["catID"]) {
                    ?>
                    <tr>
                        <td colspan="6" class="innerhead"><b><?php print $qryRow["strTitle"]; ?></b></td>
                    </tr>
                    <tr>
                        <td bgcolor="#f6f6f6"></td>
                        <td bgcolor="#f6f6f6"><img src="/images/pointer.gif" width="11" height="11" alt="" border="0">&nbsp;<a href="topics_bb.php?forum=<?php print $qryRow["ID"]; ?>"><b><?php print $qryRow["strName"]; ?></b></a><br>
                        <?php print $qryRow["txtDescription"]; ?></td>
                        <td align="center" valign="middle" class="innerhead"><?php print number_format($qryRow["intTopics"]); ?></td>
                        <td align="center" valign="middle" class="innerhead"><?php print number_format($qryRow["intPosts"]); ?></td>
                        <td align="center" valign="middle" class="innerhead">
                        <?php
                        // see if we need to display the date/time and user of the last post
                        if (!$qryRow["intTopics"] && !$qryRow["intPosts"]) {
                            print "---";
                        } else {
                            print date("M\. j g\:i A", strtotime($qryRow["dateChanged"])) . "<br><b><a href=\"/members_ggc/profile.php?user=" . $qryRow["intLastID"] . "\">" . $qryRow["strLastName"] . "</a></b>";
                        }
                        ?>
                        </td>
                    </tr>
                    <?php
                    // update our category ID
                    $catID = $qryRow["catID"];
                } else {
                    ?>
                    <tr>
                        <td bgcolor="#f6f6f6"></td>
                        <td bgcolor="#f6f6f6"><img src="/images/pointer.gif" width="11" height="11" alt="" border="0">&nbsp;<a href="topics_bb.php?forum=<?php print $qryRow["ID"]; ?>"><b><?php print $qryRow["strName"]; ?></b></a><br>
                        <?php print $qryRow["txtDescription"]; ?></td>
                        <td align="center" valign="middle" class="innerhead"><?php print number_format($qryRow["intTopics"]); ?></td>
                        <td align="center" valign="middle" class="innerhead"><?php print number_format($qryRow["intPosts"]); ?></td>
                        <td align="center" valign="middle" class="innerhead">
                        <?php
                        // see if we need to display the date/time and user of the last post
                        if (!$qryRow["intTopics"] && !$qryRow["intPosts"]) {
                            print "---";
                        } else {
                            print date("M\. j g\:i A", strtotime($qryRow["dateChanged"])) . "<br><b><a href=\"/members_ggc/profile.php?user=" . $qryRow["intLastID"] . "\">" . $qryRow["strLastName"] . "</a></b>";
                        }
                        ?>
                        </td>
                    </tr>
                    <?php

                    $totalPosts = $totalPosts + $qryRow["intTopics"];
                    $totalReplies = $totalReplies + $qryRow["intPosts"];
                }
            }
        ?>
        <tr>
            <td colspan="2" bgcolor="#f6f6f6" align="right" class="smalltxt"><b>Totals:</b>&nbsp;</td>
            <td align="center" valign="middle" class="innerhead" nowrap><?php print number_format($totalPosts); ?></td>
            <td align="center" valign="middle" class="innerhead" nowrap><?php print number_format($totalReplies); ?></td>
            <td align="center" valign="middle" class="innerhead">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="2">
            <a href="/rss/forum.php" title="RSS" target="_new"><img src="/images/rss.gif" width="28" height="16" alt="RSS" border="0" /></a>
            <a href="http://fusion.google.com/add?feedurl=http%3A//www.guitarists.net/rss/forum.php" title="Google RSS" target="_new"><img src="/images/add_google_rss.gif" width="104" height="17" alt="Google RSS" border="0" /></a>
            <a href="http://add.my.yahoo.com/content?.intl=us&url=http%3A//www.guitarists.net/rss/forum.php" title="Yahoo! RSS" target="_new"><img src="/images/add_yahoo_rss.gif" width="91" height="17" alt="Yahoo! RSS" border="0" /></a>
            </td>
            <td align="right" colspan="3">
            <table cellpadding="0" cellspacing="1" border="0">
            <form name="myChoiceBottom">
            <tr>
                <td class="smalltxt">
                <b>Options:</b>
                <select name="option" class="dropdown" onChange="location.href=this.value">
                    <option value="/forum_ggc/search.php">&nbsp;&raquo;&nbsp;Search Threads</option>
                    <?php
                        if ($_SESSION["MemberID"]) {
                            ?>
                            <option value="/forum_ggc/myposts.php">&nbsp;&raquo;&nbsp;View Your Posts</option>
                            <option value="/members_ggc/msgs/index.php">&nbsp;&raquo;&nbsp;View Private Messages</option>
                            <?php
                        } else {
                            ?>
                            <option value="/register/index.php">&nbsp;&raquo;&nbsp;Register To Post</option>
                            <option value="/login.php">&nbsp;&raquo;&nbsp;Login To Post</option>
                            <?php
                        }
                    ?>
                    <option value="/forum_ggc/recent.php">&nbsp;&raquo;&nbsp;Most Recent Posts</option>
                </select>
                <input type="Button" value="Go!" class="smbutton" onClick="location.href=document.myChoiceBottom.option.options[document.myChoiceBottom.option.selectedIndex].value">
                </td>
            </tr>
            </form>
            </table>
            </td>
        </tr>
        </table>
        <!--- end layout file --->

        </td>
        <?php //require("fastclick.php"); ?>
    </tr>
    </table>

<?php
// include our footer
if (empty($_SESSION["GGCIFrame"])) {
    require("footer.php");
}

?>
