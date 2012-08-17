<?php
    /*
        report.php
        
        This allows a member add a topic (or reply) to be seen by the mods easily.  
        We'll query the title of the topic, the text of the topic/reply, and who 
        posted it, as well as who reported it.  We'll simply create a new topic 
        in the mods room, where it can easily be seen, and handled accordingly.
    */
    
    // include our needed file(s)
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    
    // make sure they're logged in
    verify_access($_SESSION["AccessLevel"], 1);
    
    // query the data we need, based on the passed vars
    if (isset($_GET["reply"])) {
        // query the reply info
        $qryData = $dbConn->query("
            select    replies.txtReply as postText,
                    replies.intMemID,
                    replies.datePosted,
                    topics.strTitle,
                    members.strUsername
            from    replies,
                    topics,
                    members
            where    replies.ID = " . $dbConn->quote($_GET["reply"]) . " and
                    replies.intMemID = members.ID and
                    replies.intTopic = topics.ID");
    } else {
        // query the topic info
        $qryData = $dbConn->getRow("
            select  topics.strTitle,
                    topics.intMemID,
                    topics.txtPost as postText,
                    topics.datePosted,
                    members.strUsername
            from    topics,
                    members
            where   topics.ID = " . $dbConn->quote($_GET["thread"]) . " and
                    topics.intMemID = members.ID",
            DB_FETCHMODE_ASSOC);
    }
    
    // stop if nothing was found
    if (PEAR::isError($qryData) || empty($qryData["strTitle"])) {
        ?>
        <script language='JavaScript'>
        alert('Nothing was found to report.  Please try again.');
        history.back();
        </script>
        <?php
    }
    
    // script the text we'll add to the new post
    $msgTitle = "WARNING: " . $qryData["strTitle"];
    $msgText = "<a href=\"/forum_ggc/tools/listthreads.php?memID=" . $_SESSION["MemberID"] . "\"><b>" . $_SESSION["Username"] . "</b></a> has reported the following thread posted by <a href=\"/forum_ggc/tools/listthreads.php?memID=" . $qryData["intMemID"] . "\"><b>" . $qryData["strUsername"] . "</b></a>";
    $msgText .= " on " . date("n/j/Y \@ g:i a") . ":<p>";
    $msgText .= "<i>" . trim($qryData["postText"]) . "</i><p>";
    $msgText .= "The thread can be seen <a href=\"/forum_ggc/view_bb.php?forum=" . $_GET["forum"] . "&thread=" . $_GET["thread"] . "\"><b>here</b></a>.";
    
    // add the topic to the moderator forums
    $qryReport = $dbConn->query("
        insert into topics (
            intForum,
            strTitle,
            intReplies,
            intViews,
            intMemID,
            datePosted,
            txtPost,
            dateLastPost,
            strLastPost,
            intSticky,
            bitReply,
            intActive
        ) values (
            30,
            '" . addslashes($msgTitle) . "',
            0,
            0,
            " . $_SESSION["MemberID"] . ",
            Now(),
            '" . addslashes($msgText) . "',
            Now(),
            '" . $_SESSION["Username"] . "',
            0,
            1,
            1
        )");
    
    // query our number of threads here
    $qryCount = $dbConn->getRow("
        select  count(ID) as totals
        from    topics
        where   intForum = 30",
        DB_FETCHMODE_ASSOC);
    
    // update the totals for the deleted posts room
    $qryUpdate = $dbConn->query("
        update  forums
        set     intTopics = " . $qryCount["totals"] . "
        where   ID = 30");
    
    // all done!
    print "
    <script language=\"JavaScript\">
    alert(\"This thread has been reported to our moderators.\");
    location.replace(\"/forum_ggc/view_bb.php?forum=" . $_GET["forum"] . "&thread=" . $_GET["thread"] . "\");
    </script>";
?>
