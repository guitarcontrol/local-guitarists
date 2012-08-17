<?php
    /*
        results.php
        
        Here we display the results of our search, based on the terms and options chosen.
    */
    
    // include our needed file(s)
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    require("functions.php");
    
    // make sure our vars were passed
    if (empty($_GET["terms"]) || !ereg("^(1|2|3)$", $_GET["searchIn"]) || !ereg("^(1|2|3|4)$", $_GET["sort"])) {
        print "<script language=\"JavaScript\">location.replace(\"search.php\");</script>\n";
        exit();
    }
    
    // set the deault user ID to search for
    $userID = 0;
    
    // see if they chose a username to search on
    if (!empty($_GET["username"])) {
        // query the db for the ID os this user
        $arrUser = $dbConn->getRow("
            select  ID
            from    members
            where   strUsername = " . $dbConn->quote(trim($_GET["username"])) . "
            limit 1",
            DB_FETCHMODE_ASSOC);
        
        // if we found a record, update our var
        if (count($arrUser)) {
            $userID = $arrUser["ID"];
        }
    }
    
    // based on the option they chose
    if ($_GET["searchOption"] == 1) {
        // based on what they chose to match, query the db
        if ($_GET["searchIn"] == 1) {
            $whereTxt = "MATCH(topics.strTitle) AGAINST(" . $dbConn->quote(trim($_GET["terms"])) . ") and ";
        } else if ($_GET["searchIn"] == 2) {
            $whereTxt = "MATCH(topics.txtPost) AGAINST(" . $dbConn->quote(trim($_GET["terms"])) . ") and ";
        } else if ($_GET["searchIn"] == 3) {
            $whereTxt = "(MATCH(topics.strTitle) AGAINST(" . $dbConn->quote(trim($_GET["terms"])) . ") or MATCH(topics.txtPost) AGAINST(" . $dbConn->quote(trim($_GET["terms"])) . ")) and ";
        }
        
        // if they chose a user, include him here
        if ($userID) {
            $whereTxt .= "topics.intMemID = " . $dbConn->quote($userID) . " and ";
        }
        
        // see if we need to cutoff the time to query
        if ($_GET["days"]) {
            $whereTxt .= "topics.dateLastPost >= DATE_SUB(NOW(), INTERVAL " . $dbConn->quote($_GET["days"]) . " DAY) and ";
        }
        
        // set our forums to search in
        if ($_GET["forumID"]) {
            $whereTxt .= "topics.intForum = " . $dbConn->quote($_GET["forumID"]) . " and ";
        }
        
        // set how to sort
        switch($_GET["sort"]) {
            case 1: $column = "topics.dateLastPost"; break;
            case 2: $column = "topics.strTitle"; break;
            case 3: $column = "topics.intViews"; break;
            case 4: $column = "topics.intReplies"; break;
            default: $column = "topics.dateLastPost"; break;
        }
        
        // query the db for our data
        $qrySearch = $dbConn->query("
            select      topics.ID,
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
                        forums,
                        members
            where       " . $whereTxt . "
                        topics.intForum = forums.ID and
                        topics.intMemID = members.ID
            order by    " . $dbConn->quote($column) . " " . $dbConn->quote($_GET["order"]));
    } else if ($_GET["searchOption"] == 2) {
        // based on what they chose to match, query the db
        if ($_GET["searchIn"] == 1) {
            $whereTxt = "MATCH(replies.strTitle) AGAINST(" . $dbConn->quote(trim($_GET["terms"])) . ") and ";
        } else if ($_GET["searchIn"] == 2) {
            $whereTxt = "MATCH(replies.txtReply) AGAINST(" . $dbConn->quote(trim($_GET["terms"])) . ") and ";
        } else if ($_GET["searchIn"] == 3) {
            $whereTxt = "(MATCH(replies.strTitle) AGAINST(" . $dbConn->quote(trim($_GET["terms"])) . ") or MATCH(replies.txtReply) AGAINST(" . $dbConn->quote(trim($_GET["terms"])) . ")) and ";
        }
        
        // if they chose a user, include him here
        if ($userID) {
            $whereTxt .= "replies.intMemID = " . $dbConn->quote($userID) . " and ";
        }
        
        // see if we need to cutoff the time to query
        if ($_GET["days"]) {
            $whereTxt .= "topics.dateLastPost >= DATE_SUB(NOW(), INTERVAL " . $dbConn->quote($_GET["days"]) . " DAY) and ";
        }
        
        // tie the replies to the topics
        $whereTxt .= "replies.intTopic = topics.ID and ";
        
        // set our forums to search in
        if ($_GET["forumID"]) {
            $whereTxt .= "topics.intForum = " . $dbConn->quote($_GET["forumID"]) . " and ";
        }
        
        // set how to sort
        switch($_GET["sort"]) {
            case 1: $column = "topics.dateLastPost"; break;
            case 2: $column = "topics.strTitle"; break;
            case 3: $column = "topics.intViews"; break;
            case 4: $column = "topics.intReplies"; break;
            default: $column = "topics.dateLastPost"; break;
        }
        
        // query the db for our data
        $qrySearch = $dbConn->query("
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
            where       " . $whereTxt . "
                        topics.intForum = forums.ID and
                        topics.intMemID = members.ID
            order by    " . $dbConn->quote($column) . " " . $dbConn->quote($_GET["order"]));
    }
    
    // set our variables
    $pageTitle = "Guitar Forums: Search Results: '" . $_GET["terms"] . "' (" . number_format($qrySearch->numRows()) . " found)";
    $areaName = "forums";
    $intDisplayNum = 25;
    
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
        
    <br />
    <table width="100%" cellspacing="0" cellpadding="2" border="0">
    <tr valign="top">
        <td>
        <!--- begin layout file --->
        <?php if (empty($_SESSION["GGCIFrame"])) { ?>
        <table width="100%" cellspacing="1" cellpadding="3" border="0">
        <tr>
            <td class="tablehead"><a href="/index.php"><b>Home</b></a>&nbsp;&raquo;&nbsp;<a href="index.php"><b>Guitar Discussions</b></a>&nbsp;&raquo;&nbsp;Search Results for "<?php print $_GET["terms"]; ?>"  (<?php print number_format($qrySearch->numRows()); ?> found)</td>
        </tr>
        </table>
        <?php } ?>

        <table width="100%" cellspacing="0" cellpadding="2" border="0">
        <?php
            // make sure something was found
            if ($qrySearch->numRows()) {
                ?>
                <tr bgcolor="#e5e5e5">
                    <td class="smalltxt"><b>Title</b></td>
                    <td class="smalltxt" align="center"><b>Forum</b></td>
                    <td class="smalltxt" align="center"><b>Replies</b></td>
                    <td class="smalltxt" align="center"><b>Views</b></td>
                    <td class="smalltxt" align="center"><b>Author</b></td>
                    <td class="smalltxt" align="center"><b>Last Update</b></td>
                </tr>
                <?php
                // set our rowCounter
                $rowCount = 1;
                $row = 0;
                
                // loop through our query results
                while ($qryRow = $qrySearch->fetchRow(DB_FETCHMODE_ASSOC)) {
                    // see if we need to display this row
                    if ($row >= $startRow && $row <= $endRow) {
                        // set our bgcolor
                        $bgcolor = "#ffffff";
                        if ($row % 2 == 0) {
                            $bgcolor = "#f6f6f6";
                        }
                        ?>
                        <tr valign="middle" bgcolor="<?php print $bgcolor; ?>">
                            <td><b><a href="view_bb.php?ggc=<?php print $ggclink; ?>&forum=<?php print $qryRow["intForum"]; ?>&thread=<?php print $qryRow["ID"]; ?>&mark=<?php print $_GET["terms"]; ?>"><?php print $qryRow["strTitle"]; ?></a></b></td>
                            <td class="smalltxt" align="center"><?php print trim($qryRow["strName"]); ?></td>
                            <td class="smalltxt" align="center"><?php print number_format($qryRow["intReplies"]); ?></td>
                            <td class="smalltxt" align="center"><?php print number_format($qryRow["intViews"]); ?></td>
                            <td class="smalltxt" align="center">
                            <?php if (!$ggclink) { ?>
                                <a href="/members/profile.php?user=<?php print $qryRow["intMemID"]; ?>"><b><?php print $qryRow["strUsername"]; ?></b></a>
                            <?php } else { ?>
                                <b><?php print $qryRow["strUsername"]; ?></b>
                            <?php } ?>
                            </td>
                            <td class="smalltxt" align="center" nowrap><?php print date("M, j \@ g\:i A", strtotime($qryRow["dateLastPost"]));
                            // see if there is a last poster or not
                            if (strlen($qryRow["strLastPost"])) {
                                ?><br />
                                <?php if (!$ggclink) { ?>
                                    <a href="/members/profile.php?user=<?php print $qryRow["intLastID"]; ?>"><b><?php print trim($qryRow["strLastPost"]); ?></b></a>
                                <?php } else { ?>
                                    <b><?php print trim($qryRow["strLastPost"]); ?></b>
                                <?php }
                            }
                            ?></td>
                        </tr>
                        <?php
                    }
                    
                    // update our counters
                    $rowCount++;
                    $row++;
                }
                
                // see if we need to provide links to the next items
                if ($qrySearch->numRows() > $intDisplayNum) {
                    // call our pages function
                    $strURL = "results.php?ggc=$ggclink&terms=" . $_GET["terms"] . "&username=" . $_GET["username"] . "&searchOption=" . $_GET["searchOption"] . "&searchIn=" . $_GET["searchIn"] . "&days=" . $_GET["days"] . "&forumID=" . $_GET["forumID"] . "&sort=" . $_GET["sort"] . "&order=" . $_GET["order"] . "&";
                    f_prevnext($qrySearch->numRows(), $intDisplayNum, $startRow, '6', $strURL);
                }
            } else {
                // nothing was found
                ?>
                <tr>
                    <td colspan="6">
                    There are currently <b>0</b> active topics in the database that have text containing the 
                    phrase <b><?php print trim($_GET["terms"]); ?>"</b>.  Please feel free to refine your search and 
                    <a href="search.php?ggc=<?php print $ggclink; ?>"><b>try again</b></a>.
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
    // include our 
    if (empty($_SESSION["GGCIFrame"])) {
        require("footer.php");
    }
?>
