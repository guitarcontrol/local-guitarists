<?php
    
    /*
        myposts.php
        
        Here we'll display our active threads, based on the forum chosen.
    
    */
    
    // include our needed file(s)
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    require("functions.php");
    
    // make sure they're logged in
    verify_access($_SESSION["AccessLevel"], 1);
    
    // set the # of days to pull in
    if (isset($_GET["days"])) {
        // see what the value is
        if ($_GET["days"] == "all") {
            $myDays = "9999999";
        } else {
            $myDays = $_GET["days"];
        }
    } else {
        $myDays = 30;
    }
    
    // set the # of topics to list per page
    if (isset($_GET["display"])) {
        $intDisplayNum = $_GET["display"];
    } else {
        $intDisplayNum = 25;
    }
    
    // query the db for our titles
    $qryTopics = $dbConn->query("
        select      topics.ID,
                    topics.intForum,
                    topics.strTitle,
                    topics.intReplies,
                    topics.intViews,
                    topics.dateLastPost,
                    topics.strLastPost,
                    topics.intLastID,
                    topics.intSticky,
                    topics.bitReply,
                    forums.strName,
                    members.ID as memID,
                    members.strUsername
        from        topics,
                    forums,
                    members
        where       topics.intMemID = " . $_SESSION["MemberID"] . " and
                    topics.intForum NOT IN ( 30, 36 ) and
                    topics.dateLastPost >= DATE_SUB(NOW(), INTERVAL " . $myDays . " DAY) and
                    topics.intForum = forums.ID and
                    topics.intMemID = members.ID
        order by    topics.dateLastPost desc
        limit 15");
    
    // query topics they the user has replied to
    $qryReplies = $dbConn->query("
        select      DISTINCT(topics.ID) as ID,
                    topics.intForum,
                    topics.strTitle,
                    topics.intMemID,
                    topics.intReplies,
                    topics.intViews,
                    topics.intLastID,
                    topics.strLastPost,
                    topics.dateLastPost,
                    forums.strName,
                    members.strUsername
        from        topics,
                    replies,
                    forums,
                    members
        where       replies.intMemID = '" . $_SESSION["MemberID"] . "' and
                    replies.intTopic = topics.ID and
                    topics.dateLastPost >= DATE_SUB(NOW(), INTERVAL " . $myDays . " DAY) and
                    topics.intForum = forums.ID and
                    topics.intMemID = members.ID
        order by    topics.dateLastPost desc
        limit 25");
    
    // query the appropriate forums
    $sqlText = "select ID, strTitle, intSort from categories where intParent = 24";
    
    // if they're not an admin, only pull active forums
    if ($_SESSION["AccessLevel"] < 90) {
        $sqlText .= " and intActive = 1";
    }
    $sqlText .= " order by intSort";
    
    // select our main categories to display our forums
    $qryCats = $dbConn->query($sqlText);
    
    // set our variables
    $pageTitle = "Guitar Forums: My Threads";
    $pageDescription = "";
    $pageKeywords = "";
    $areaName = "forums";
    $arrSeen = array("0");
    
    // setup our previous/next links
    if (isset($_GET["page"])) {
        $startRow = $_GET["page"];
        $endRow = $startRow + ($intDisplayNum - 1);
    } else {
        $startRow = 0;
        $endRow = $intDisplayNum - 1;
    }
    
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

    <br>
    <table width="100%" cellspacing="0" cellpadding="2" border="0">
    <tr valign="top">
        <td>
        <!--- begin layout file --->
        <?php if (empty($_SESSION["GGCIFrame"])) { ?>
        <table width="100%" cellspacing="1" cellpadding="3" border="0">
        <tr>
            <td class="tablehead">&nbsp;&raquo;&nbsp;<a href="/index.php"><b>Home</b></a>&nbsp;&raquo;&nbsp;<a href="/forum_ggc/index.php"><b>Guitar Discussions</b></a>&nbsp;&raquo;&nbsp;My Topics</td>
        </tr>
        </table>
        <?php } ?>
        
        <table width="100%" cellspacing="0" cellpadding="2" border="0">
        <tr>
            <td colspan="6">
            <table width="100%" cellpadding="0" cellspacing="1" border="0">
            <form name="myChoiceTop">
            <tr>
                <td class="smalltxt">
                <b>Options:</b>
                <select name="option" class="dropdown" onChange="location.href=this.value">
                    <option value="search.php">&nbsp;&raquo;&nbsp;Search Threads</option>
                <?php
                    // see if they're logged in or not
                    if ($_SESSION["MemberID"]) {
                        ?>
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
                    <option value="/forum_ggc/index.php">&nbsp;&raquo;&nbsp;Home</option>
                </select>
                <input type="Button" value="Go!" class="smbutton" onClick="location.href=document.myChoiceTop.option.options[document.myChoiceTop.option.selectedIndex].value">
                </td>
                <td align="right" class="smalltxt">
                <b>Forums:</b>
                <!--- display our output --->
                <select name="forum" onchange="location.href='/forum_ggc/topics_bb.php?forum=' + this.value" class="dropdown">
                    <?php
                        // loop through our categories and grab the forums
                        while ($qryRow = $qryCats->fetchRow(DB_FETCHMODE_ASSOC)) {
                            ?>
                            <option value=""><?php print $qryRow["strTitle"]; ?></option>
                            <?php
                            // show our categories
                            show_forums($qryRow["ID"], '&nbsp;&nbsp;&nbsp;&nbsp;', 0, $_SESSION["AccessLevel"], $dbConn);
                        }
                    ?>
                </select>
                <input type="Button" value="Go!" class="smbutton" onClick="location.href='/forum_ggc/topics_bb.php?forum=' + document.myChoiceTop.forum.options[document.myChoiceTop.forum.selectedIndex].value">
                </td>
            </tr>
            </form>
            </table>
            </td>
        </tr>
        </table>
        
        <table width="100%" cellspacing="0" cellpadding="1" border="0">
        <tr>
            <td class="tablehead">&nbsp;&raquo;&nbsp;My Topics</td>
        </tr>
        </table>
        
        <table width="100%" cellspacing="0" cellpadding="2" border="0">
        <?php
            
            // see if any records were found
            if ($qryTopics->numRows()) {
                ?>
                <tr align="center">
                    <td colspan="2" class="innerhead"><b>Topics</b></td>
                    <td class="innerhead"><b>Replies</b></td>
                    <td class="innerhead"><b>Views</b></td>
                    <td class="innerhead"><b>Last Update</b></td>
                </tr>
                <?php
                // set our alternate row color counter
                $altCounter = 1;
                
                // loop through our results
                while ($qryRow = $qryTopics->fetchRow(DB_FETCHMODE_ASSOC)) {
                    $bgcolor = "#ffffff";
                    if ($altCounter % 2 == 0) {
                        $bgcolor = "#f6f6f6";
                    }
                    ?>
                    <tr valign="middle" bgcolor="<?php print $bgcolor; ?>">
                        <td valign="top"><!-- new posts tracker here --></td>
                        <td valign="top">
                        <b><a href="/forum_ggc/view_bb.php?forum=<?php print $qryRow["intForum"]; ?>&thread=<?php print $qryRow["ID"]; ?>">
                        <?php
                        // make sure a title exists
                        if (strlen($qryRow["strTitle"])) {
                            print $qryRow["strTitle"];
                        } else {
                            print "No Title Provided";
                        }
                        ?></a></b><br />
                        &nbsp;&raquo;&nbsp;<?php print $qryRow["strName"]; ?>
                        </td>
                        <td align="center" class="smalltxt"><?php print $qryRow["intReplies"]; ?></td>
                        <td align="center" class="smalltxt"><?php print $qryRow["intViews"]; ?></td>
                        <td align="center" class="smalltxt"><?php print date("M\. j \@ g\:i A", strtotime($qryRow["dateLastPost"]));
                        // see if we have a valid date
                        if (strlen($qryRow["strLastPost"])) {
                            print "<br>
                            <a href=\"/members_ggc/profile.php?user=" . $qryRow["intLastID"] . "\"><b>" . trim($qryRow["strLastPost"]) . "</b></a>";
                        }
                        ?></td>
                    </tr>
                    <?php
                    
                    // update our alternate row color counter
                    $altCounter++;
                    $arrSeen[] = $qryRow["ID"];
                }
                ?>
                <tr>
                    <td colspan="6">
                    <img src="/forum_ggc/images/new.gif" width="11" height="12" alt="New" border="0"> 
                    indicates a new post.
                    </td>
                </tr>
                <?php
            } else {
                ?>
                <tr>
                    <td colspan="6">
                    You have <b>0</b> topics listed that you have started recently.  Adjust the search below 
                    and try again.
                    </td>
                </tr>
                <?php
            }
        ?>
        <tr>
            <td colspan="6"><br /></td>
        </tr>
        </table>
        
        <table width="100%" cellspacing="0" cellpadding="1" border="0">
        <tr>
            <td class="tablehead">&nbsp;&raquo;&nbsp;Topics I Have Replied To</td>
        </tr>
        </table>
        
        <table width="100%" cellspacing="0" cellpadding="2" border="0">
        <?php
            // query topics they the user has replied to
            $qryReplies = $dbConn->query("
                select      DISTINCT(topics.ID) as ID,
                            topics.intForum,
                            topics.strTitle,
                            topics.intMemID,
                            topics.intReplies,
                            topics.intViews,
                            topics.intLastID,
                            topics.strLastPost,
                            topics.dateLastPost,
                            forums.strName,
                            members.strUsername
                from        topics,
                            replies,
                            forums,
                            members
                where       replies.intMemID = '" . $_SESSION["MemberID"] . "' and
                            replies.intTopic = topics.ID and
                            topics.ID NOT IN ( " . implode(",", $arrSeen) . " ) and
                            topics.dateLastPost >= DATE_SUB(NOW(), INTERVAL " . $myDays . " DAY) and
                            topics.intForum = forums.ID and
                            topics.intMemID = members.ID
                order by    topics.dateLastPost desc
                limit 25");
            
            // see if any records were found
            if ($qryReplies->numRows()) {
                ?>
                <tr align="center">
                    <td colspan="2" class="innerhead"><b>Topics</b></td>
                    <td class="innerhead"><b>Replies</b></td>
                    <td class="innerhead"><b>Views</b></td>
                    <td class="innerhead"><b>Last Update</b></td>
                </tr>
                <?php
                // set our alternate row color counter
                $altCounter = 1;
                
                // loop through our results
                while ($qryRow = $qryReplies->fetchRow(DB_FETCHMODE_ASSOC)) {
                    $bgcolor = "#ffffff";
                    if ($altCounter % 2 == 0) {
                        $bgcolor = "#f6f6f6";
                    }
                    ?>
                    <tr valign="middle" bgcolor="<?php print $bgcolor; ?>">
                        <td valign="top"><!-- new posts tracker here --></td>
                        <td valign="top">
                        <b><a href="/forum_ggc/view_bb.php?forum=<?php print $qryRow["intForum"]; ?>&thread=<?php print $qryRow["ID"]; ?>">
                        <?php
                        // make sure a title exists
                        if (strlen($qryRow["strTitle"])) {
                            print $qryRow["strTitle"];
                        } else {
                            print "No Title Provided";
                        }
                        ?></a></b><br />
                        &nbsp;&raquo;&nbsp;<?php print $qryRow["strName"]; ?>
                        </td>
                        <td align="center" class="smalltxt"><?php print $qryRow["intReplies"]; ?></td>
                        <td align="center" class="smalltxt"><?php print $qryRow["intViews"]; ?></td>
                        <td align="center" class="smalltxt"><?php print date("M\. j \@ g\:i A", strtotime($qryRow["dateLastPost"]));
                        // see if we have a valid date
                        if (strlen($qryRow["strLastPost"])) {
                            print "<br>
                            <a href=\"/members_ggc/profile.php?user=" . $qryRow["intLastID"] . "\"><b>" . trim($qryRow["strLastPost"]) . "</b></a>";
                        }
                        ?></td>
                    </tr>
                    <?php
                    
                    // update our alternate row color counter
                    $altCounter++;
                }
                ?>
                <tr>
                    <td colspan="6">
                    <img src="/forum_ggc/images/new.gif" width="11" height="12" alt="New" border="0"> 
                    indicates a new post.
                    </td>
                </tr>
                <?php
            } else {
                ?>
                <tr>
                    <td colspan="6">
                    You have <b>0</b> topics listed that you have replied to recently.  Adjust the search below 
                    and try again.
                    </td>
                </tr>
                <?php
            }
        ?>
        <tr>
            <td colspan="6"><br />
            <!-- begin options table -->
            <table cellspacing="0" cellpadding="0" border="0">
            <tr>
                <form action="myposts.php" method="get">
                <td class="smalltxt">
                <b>Days:</b>
                <select name="days" class="dropdown">
                    <option value="1"<?php if ($myDays == 1) { print " selected"; } ?>> Yesterday</option>
                    <option value="2"<?php if ($myDays == 2) { print " selected"; } ?>> 2 Days</option>
                    <option value="3"<?php if ($myDays == 3) { print " selected"; } ?>> 3 Days</option>
                    <option value="4"<?php if ($myDays == 4) { print " selected"; } ?>> 4 Days</option>
                    <option value="5"<?php if ($myDays == 5) { print " selected"; } ?>> 5 Days</option>
                    <option value="10"<?php if ($myDays == 10) { print " selected"; } ?>> 10 Days</option>
                    <option value="15"<?php if ($myDays == 15) { print " selected"; } ?>> 15 Days</option>
                    <option value="20"<?php if ($myDays == 20) { print " selected"; } ?>> 20 Days</option>
                    <option value="30"<?php if ($myDays == 30) { print " selected"; } ?>> 30 Days</option>
                    <option value="60"<?php if ($myDays == 60) { print " selected"; } ?>> 60 Days</option>
                    <option value="90"<?php if ($myDays == 90) { print " selected"; } ?>> 90 Days</option>
                </select>
                <input type="Submit" value="Go!" class="smbutton">
                </td>
                </form>
            </tr>
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
