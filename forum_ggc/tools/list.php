<?php
    /*
        list.php
        
        This allows mods to view the threads started by this member for the last 
        10 days.
    */
    
    // include our needed files
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    require("functions.php");
    
    // make sure they're logged in
    verify_access($_SESSION["AccessLevel"], 90);
    
    // set our cutoff date
    $cutoff = strtotime("-10 days");
    
    // query the info for this user
    $qryData = $dbConn->query("
        select        topics.ID,
                    topics.intForum,
                    topics.strTitle,
                    topics.txtPost,
                    topics.intReplies,
                    topics.intViews,
                    topics.datePosted,
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
        where        topics.intMemID = " . $dbConn->quote($_GET["memid"]) . " and
                    topics.datePosted >= '" . date("Y-m-d", $cutoff) . " 00:00:00' and
                    topics.intForum = forums.ID and 
                    topics.intMemID = members.ID
        order by    topics.dateLastPost desc");
    
    // if we found no records, get their last 10 posts
    if (!$qryData->numRows()) {
        $qryData = $dbConn->query("
            select        topics.ID,
                        topics.intForum,
                        topics.strTitle,
                        topics.txtPost,
                        topics.intReplies,
                        topics.intViews,
                        topics.datePosted,
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
            where        topics.intMemID = " . $dbConn->quote($_GET["memid"]) . " and
                        topics.intForum = forums.ID and 
                        topics.intMemID = members.ID
            order by    topics.dateLastPost desc
            limit 10");
    }
    
    // query the info for this user
    $qryInfo = $dbConn->getRow("
        select  strUsername
        from    members
        where   ID = " . $dbConn->quote($_GET["memid"]),
        DB_FETCHMODE_ASSOC);
    
    // set our page defaults
    $pageTitle = "Guitar Forums: Mod Tools: Recent Threads by " . $qryInfo["strUsername"];
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

    <br>
    <table width="100%" cellspacing="0" cellpadding="2" border="0">
    <tr valign="top">
        <td align="center">
    
        <!--- begin layout file --->
        <?php if (empty($_SESSION["GGCIFrame"])) { ?>
        <table width="100%" cellspacing="1" cellpadding="2" border="0">
        <tr>
            <td class="tablehead">&nbsp;&raquo;&nbsp;Most Recent Threads by '<?php print $qryInfo["strUsername"]; ?>' (last 10 days)</td>
        </tr>
        </table>
        <?php } ?>
        
        <table width="100%" cellspacing="0" cellpadding="2" border="0">
        <?php
            // set a row counter
            $row = 1;
            
            // see if we found any posts
            if ($qryData->numRows()) {
                // loop through the resultset
                while ($qryRow = $qryData->fetchRow(DB_FETCHMODE_ASSOC)) {
                    // set the bgcolor
                    $bgcolor = "#ffffff";
                    if ($row % 2 == 0) {
                        $bgcolor = "#f6f6f6";
                    }
                    ?>
                    <tr valign="top" bgcolor="<?php print $bgcolor; ?>">
                        <td><b>&raquo;</b> <a href="/forum_ggc/view_bb.php?forum=<?php print $qryRow["intForum"]; ?>&thread=<?php print $qryRow["ID"]; ?>"><b><?php print $qryRow["strTitle"]; ?></b></a></td>
                        <td><a href="/forum_ggc/topics_bb.php?forum=<?php print $qryRow["intForum"]; ?>"><b><?php print $qryRow["strName"]; ?></b></a></td>
                        <td><?php print $qryRow["intReplies"]; ?></td>
                        <td><?php print $qryRow["intViews"]; ?></td>
                    </tr>
                    <tr bgcolor="<?php print $bgcolor; ?>">
                        <td colspan="4">
                        <?php print substr($qryRow["txtPost"], 0, 300); ?>...<br><br>
                        </td>
                    </tr>
                    <?php
                    $row++;
                }
            } else {
                ?>
                <tr>
                    <td colspan="4">
                    This user currently has <b>0</b> new threads in the last 10 days.
                    </td>
                </tr>
                <?php
            }
        ?>
        <tr>
            <td colspan="4"><br>
            &raquo;&nbsp;<a href="panel.php?memid=<?php print $_GET["memid"]; ?>"><b>Return to Control Panel</b></a>
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
