<?php
    /*
        move.php
        
        This allows a moderator to move a thread to a different forum.
    */
    
    // include our needed files
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    require("functions.php");
    
    // make sure they're logged in
    verify_access($_SESSION["AccessLevel"], 90);
    
    // set our action
    if (isset($_POST["action"])) {
        $process = 1;
    } else {
        $process = 0;
    }
    
    // continue, based on where we are
    if (!$process) {
        // query our list of forums
        $qryForums = $dbConn->query("
            select        ID,
                        strTitle,
                        intSort
            from        categories
            where        intParent = 24
            order by    intSort,
                        strTitle");
        
        // query our thread name
        $qryThread = $dbConn->getRow("
            select  ID,
                    intForum,
                    strTitle
            from    topics
            where   ID = " . $dbConn->quote($_GET["topic"]),
            DB_FETCHMODE_ASSOC);
        ?>
        <html>
        <head>
            <title>Thread Mover</title>
            <link type="text/css" rel="stylesheet" href="/inc/styles.css">
        </head>
        
        <body bgcolor="#ffffff" topmargin="0" leftmargin="0" marginwidth="0" marginheight="0">
        
        <br>
        <div align="center">
        <table width="95%" cellspacing="0" cellpadding="1" border="0">
        <form action="move.php" method="post">
        <input type="Hidden" name="ID" value="<?php print $qryThread["ID"]; ?>">
        <input type="Hidden" name="intForum" value="<?php print $qryThread["intForum"]; ?>">
        <input type="Hidden" name="strTitle" value="<?php print $qryThread["strTitle"]; ?>">
        <tr>
            <td class="tablehead">&nbsp;&raquo;&nbsp;Thread Mover</td>
        </tr>
        </table>
        
        <table width="100%" cellspacing="0" cellpadding="2" border="0">
        <tr>
            <td width="50"><b>To: </b></td>
            <td>
            <select name="forumID" class="dropdown">
            <?php
                // loop through our categories and grab the forums
                while ($qryRow = $qryForums->fetchRow(DB_FETCHMODE_ASSOC)) {
                    ?>
                    <option value=""> <?php print $qryRow["strTitle"];
                    // show our categories
                    show_forums($qryRow["ID"], '&nbsp;&nbsp;&nbsp;&nbsp;', $qryThread["intForum"], $_SESSION["AccessLevel"], $dbConn);
                }
            ?>
            </select>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
            <input type="Submit" name="action" value="Move &raquo;" class="smbutton">
            <input type="Button" value="Cancel" onClick="window.close()" class="smbutton">
            </td>
        </tr>
        </form>
        </table></div>
        
        </body>
        </html>
        <?php
    } else {
        // query the main topic info
        $qryTitle = $dbConn->getRow("
            select  *
            from    topics
            where   ID = '" . $_POST["ID"] . "'",
            DB_FETCHMODE_ASSOC);
        
        // query the counts to move and subtract from
        $qryNumbers = $dbConn->getRow("
            select  intTopics,
                    intPosts
            from    forums
            where   ID = '" . $qryTitle["intForum"] . "'",
            DB_FETCHMODE_ASSOC);
        
        // set our new totals
        $oldTopicCount = $qryNumbers["intTopics"] - 1;
        $oldReplyCount = $qryNumbers["intPosts"] - $qryTitle["intReplies"];
        
        // update the db with the new numbers
        $qryUpdate = $dbConn->query("
            update    forums
            set        intTopics = " . $oldTopicCount . ",
                    intPosts = " . $oldReplyCount . "
            where    ID = " . $_POST["intForum"]);
        
        // query the current totals to add to for the new home of the thread
        $qryNew = $dbConn->getRow("
            select  ID,
                    strName,
                    intTopics,
                    intActive,
                    intPosts
            from    forums
            where   ID = '" . $_POST["forumID"] . "'",
            DB_FETCHMODE_ASSOC);
        
        // set our new totals for the forum
        $newTopicCount = $qryNew["intTopics"] + 1;
        $newReplyCount = $qryNew["intPosts"] + $qryTitle["intReplies"];
        
        // update the db
        $qryUpdate = $dbConn->query("
            update    forums
            set        intTopics = " . $newTopicCount . ",
                    intPosts = " . $newReplyCount . "
            where    ID = " . $_POST["forumID"]);
        
        // update the thread with the new ID
        $qryUpTopic = $dbConn->query("
            update    topics
            set        intForum = " . $_POST["forumID"] . "
            where    ID = " . $_POST["ID"]);
        
        // set the moved text, so users know where it went
        $moveText = "This thread has been moved to <b>" . $qryNew["strName"] . "</b>. You can view and reply to it <a href=\"/forum_ggc/view_bb.php?forum=" . $qryNew["ID"] . "&thread=" . $_POST["ID"] . "\"><b>here</b></a>.";
        
        // add a link to the moved thread in the original forum
        if ($qryNew["intActive"]) {
            $qryAddLink = $dbConn->query("
                insert into topics (
                    intForum,
                    strTitle,
                    intReplies,
                    intMemID,
                    datePosted,
                    txtPost,
                    dateLastPost,
                    intLastID,
                    intSticky,
                    bitReply,
                    intActive
                ) values (
                    " . $qryTitle["intForum"] . ",
                    'MOVED - " . $qryTitle["strTitle"] . "',
                    " . $qryTitle["intReplies"] . ",
                    " . $qryTitle["intMemID"] . ",
                    Now(),
                    '" . $moveText . "',
                    Now(),
                    0,
                    0,
                    0,
                    1
                )");
        }
        
        // redirect them to the new section
        print "
        <script language=\"JavaScript\">
        opener.location.replace(\"/forum_ggc/topics_bb.php?forum=" . $_POST["forumID"] . "&thread=" . $_POST["ID"] . "\");
        self.close();
        </script>";
        exit();
    }
?>
