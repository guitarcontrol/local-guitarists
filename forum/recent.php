<?php
    /*
        recent.php
        
        This script will display the last 50 added/updated topics going on, and displays 
        them in descending order (newest to oldest).
    */
    
    // include our needed file(s)
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    
    // generate our dynamic SQL statement
    $sqlText = "
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
                    forums.intActive,
                    members.ID as memID, 
                    members.strUsername
        from        topics,
                    forums,
                    members
        where       topics.intForum = forums.ID and 
                    topics.intMemID = members.ID and
                    forums.intPrivate = 0";
        
    // if they're not a mod, show active rooms only
    if ($_SESSION["AccessLevel"] < 90) {
        $sqlText .= " and
                forums.intActive = 1";
    }
    
    $sqlText .= "        order by    topics.dateLastPost desc
        limit 50";
    
    // get the last 50 added topics to the db
    $qryTopics = $dbConn->query($sqlText);
    
    // set our page variables
    $pageTitle = "Guitar Forums: Last 50 Topics";
    $pageDescription = "Browse the last 50 topics posted to the site, broken down by date";
    $pageKeywords = "guitar, chat, forum, topic, thread, guitarists, guitars";
    $areaName = "forums";
    
    // include our header
    require("header.php");
?>

    <br>
    <table width="100%" cellspacing="0" cellpadding="2" border="0">
    <tr valign="top">
        <td>
        <!--- begin layout file --->
        <table width="100%" cellspacing="1" cellpadding="3" border="0">
        <tr>
            <td class="tablehead">&nbsp;&raquo;&nbsp;<a href="/index.php"><b>Home</b></a>&nbsp;&raquo;&nbsp;<a href="/forum/index.php"><b>Guitar Discussions</b></a>&nbsp;&raquo;&nbsp;The Last 50 Topics</td>
        </tr>
        </table>
        
        <table width="100%" cellspacing="0" cellpadding="2" border="0">
        <tr>
            <td align="right" colspan="6">
            <table width="100%" cellpadding="0" cellspacing="1" border="0">
            <form name="myChoiceTop">
            <tr>
                <!--- <td width="150" class="smalltxt">Moderated By: <a href="/members/profile.php?user=#qryForum.intAdmin#"><b>#qryForum.strUsername#</b></a></td> --->
                <td align="right" class="smalltxt">
                <b>Options:</b>
                <select name="option" class="dropdown" onChange="location.href=this.value">
                    <option value="/forum/search.php">&nbsp;&raquo;&nbsp;Search Threads</option>
                    <?php
                        // see if they're logged in or not
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
                    <option value="/forum/index.php">&nbsp;&raquo;&nbsp;Home</option>
                </select>
                <input type="Button" value="Go!" class="smbutton" onClick="location.href=document.myChoiceTop.option.options[document.myChoiceTop.option.selectedIndex].value">
                </td>
            </tr>
            </form>
            </table>
            </td>
        </tr>
        <?php
            // see if we found any records
            if ($qryTopics->numRows()) {
                ?>
                <tr align="center">
                    <td class="innerhead"><b>Title</b></td>
                    <td class="innerhead"><b>Replies</b></td>
                    <td class="innerhead"><b>Views</b></td>
                    <td class="innerhead"><b>Author</b></td>
                    <td class="innerhead"><b>Last Update</b></td>
                </tr>
                <?php
                // create our alternate color var
                $altCounter = 1;
                
                // loop through our results
                while ($qryRow = $qryTopics->fetchRow(DB_FETCHMODE_ASSOC)) {
                    // set the bgcolor
                    $bgcolor = "#ffffff";
                    if ($altCounter % 2 == 0) {
                        $bgcolor = "#f6f6f6";
                    }
                    ?>
                    <tr valign="middle" bgcolor="<?php print $bgcolor; ?>">
                        <td>
                        <b><a href="/forum/view_bb.php?forum=<?php print $qryRow["intForum"]; ?>&thread=<?php print $qryRow["ID"]; ?>"><?php print $qryRow["strTitle"]; ?></a></b><br>
                        &nbsp;&raquo;&nbsp;<?php print $qryRow["strName"]; ?>
                        </td>
                        <td align="center" class="smalltxt"><?php print number_format($qryRow["intReplies"]); ?></td>
                        <td align="center" class="smalltxt"><?php print number_format($qryRow["intViews"]); ?></td>
                        <td align="center" class="smalltxt" nowrap><a href="/members/profile.php?user=<?php print $qryRow["memID"]; ?>"><b><?php print $qryRow["strUsername"]; ?></b></a></td>
                        <td align="center" class="smalltxt" nowrap><?php print date("M\. j \@ g\:i A", strtotime($qryRow["dateLastPost"]));
                        
                        if (strlen($qryRow["strLastPost"])) {
                            print "<br>
                            <a href=\"/members/profile.php?user=" . $qryRow["intLastID"] . "\"><b>" . $qryRow["strLastPost"] . "</b></a>";
                        }
                        ?></td>
                    </tr>
                    <?php
                    // update our counter
                    $altCounter++;
                }
            } else {
                ?>
                <tr>
                    <td colspan="6">
                    There are currently <b>0</b> active topics in the database.
                    Feel free to bookmark this page, so you can easily come back, as new 
                    discussions are taking place all of the time.
                    <p>
                    Thanks.
                    </td>
                </tr>
                <?php
            }
        ?>
        <tr>
            <td colspan="6"><br></td>
        </tr>
        <tr>
            <td align="right" colspan="6">
            <table width="100%" cellpadding="0" cellspacing="1" border="0">
            <form name="myChoiceBottom">
            <tr>
                <!--- <td width="150" class="smalltxt">Moderated By: <a href="/members/profile.php?user=#qryForum.intAdmin#"><b>#qryForum.strUsername#</b></a></td> --->
                <td align="right" class="smalltxt">
                <b>Options:</b>
                <select name="option" class="dropdown" onChange="location.href=this.value">
                    <option value="/forum/search.php">&nbsp;&raquo;&nbsp;Search Threads</option>
                    <?php
                        // see if they're logged in or not
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
                    <option value="/forum/index.php">&nbsp;&raquo;&nbsp;Home</option>
                </select>
                <input type="Button" value="Go!" class="smbutton" onClick="location.href=document.myChoiceTop.option.options[document.myChoiceTop.option.selectedIndex].value">
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
    // include our header
    require("footer.php");
?>
