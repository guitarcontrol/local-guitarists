<?php
    /*
        delete.php
        
        This script deletes either a reply, or an entire thread, based on what was 
        passed to it. It can take 1 of 2 variables:
        
        topic:    the ID of the main thread to delete
        reply:    the ID of the reply to delete
    */
    
    // include our needed files
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    require("functions.php");
    
    // make sure they're logged in
    verify_access($_SESSION["AccessLevel"], 1);
    
    // make sure that something was passed
    if (!isset($_GET["topic"]) && !isset($_GET["reply"])) {
        print "
        <script language=\"javascript\">
        alert(\"Please choose a thread\/topic to delete first. Thanks.\");
        window.close();
        </script>";
        exit();
    }
    
    print "<b style=\"font-family: Tahoma, Verdana, Arial; font-size : 11px; color: #cc3333;\">Processing Request...</b>";
    
    // continue, based on what was passed
    if (isset($_GET["topic"])) {
        // get our forum information
        $qryTopic = $dbConn->getRow("
            select  topics.ID, 
                    topics.intMemID, 
                    topics.strTitle, 
                    forums.ID as forumID, 
                    forums.intTopics, 
                    forums.intPosts
            from    topics,
                    forums
            where   topics.ID = " . $dbConn->quote($_GET["topic"]) . " and 
                    topics.intForum = forums.ID",
            DB_FETCHMODE_ASSOC);
        
        // make sure a record was found
        if (!count($qryTopic)) {
            print "
            <script language=\"JavaScript\">
            alert(\"It appears this thread does not exist.  Please verify and try again.\");
            window.close();
            </script>";
            exit();
        }
        
        // make sure this person is the original poster or a mod
        if ($qryTopic["intMemID"] != $_SESSION["MemberID"] && $_SESSION["AccessLevel"] < 90) {
            print "
            <script language=\"JavaScript\">
            alert(\"It appears you do not have permission to delete this thread.\");
            window.close();
            </script>";
            exit();
        }
        
        // get the number of replies for this thread
        $qryReplies = $dbConn->getRow("
            select  count(ID) as totals
            from    replies
            where   intTopic = " . $dbConn->quote($_GET["topic"]),
            DB_FETCHMODE_ASSOC);
        
        // set our values to update the db with
        $topicCount = $qryTopic["intTopics"] - 1;
        $replyCount = $qryTopic["intPosts"] - $qryReplies["totals"];
        
        // delete replies (if any)
        if ($qryReplies["totals"]) {
            $qryKillReplies = $dbConn->query("
                delete
                from    replies
                where    intTopic = " . $dbConn->quote($_GET["topic"]));
        }
        
        // delete our main thread
        $qryKillTopic = $dbConn->query("
            delete
            from    topics
            where    ID = " . $dbConn->quote($_GET["topic"]));
        
        // update our forums counts for display
        $qryUpdate = $dbConn->query("
            update    forums
            set        intTopics = " . $topicCount . ", 
                    intPosts = " . $replyCount . "
            where    ID = " . $qryTopic["forumID"]);
        
        // delete any subscriptions that people have signed up for
        $qryKillSubs = $dbConn->query("
            delete
            from    saved
            where    intItem = " . $qryTopic["ID"] . " and
                    intType = 2");
        
        // set our URL to redirect to
        $strURL = "/forum_ggc/topics_bb.php?forum=" . $qryTopic["forumID"];
        
        // all done
        print "
        <script language=\"javascript\">
        alert(\"The thread was successfully deleted.\");
        opener.location.href = '" . $strURL . "';
        window.close();
        </script>";
        exit();
    } else if (isset($_GET["reply"])) {
        // get our reply information
        $qryReply = $dbConn->getRow("
            select  intTopic,
                    intMemID,
                    strTitle
            from    replies
            where   ID = " . $dbConn->quote($_GET["reply"]),
            DB_FETCHMODE_ASSOC);
        
        // make sure a record was found
        if (!count($qryReply)) {
            print "
            <script language=\"JavaScript\">
            alert(\"It appears this reply does not exist.  Please verify and try again.\");
            window.close();
            </script>";
            exit();
        }
        
        // make sure this person is the original poster or a mod
        if ($qryReply["intMemID"] != $_SESSION["MemberID"] && $_SESSION["AccessLevel"] < 90) {
            print "
            <script language=\"JavaScript\">
            alert(\"It appears you do not have permission to delete this reply.\");
            window.close();
            </script>";
            exit();
        }
        
        // get our forum information for this reply
        $qryForum = $dbConn->getRow("
            select  topics.ID, 
                    topics.strTitle, 
                    topics.intReplies, 
                    forums.ID as forumID, 
                    forums.strName, 
                    forums.intTopics, 
                    forums.intPosts
            from    topics,
                    forums
            where   topics.ID = '" . $qryReply["intTopic"] . "' and 
                    topics.intForum = forums.ID",
            DB_FETCHMODE_ASSOC);
        
        // set our values to update the db with
        $replyCount = $qryForum["intReplies"] - 1;
        $postCount = $qryForum["intPosts"] - 1;
        
        // delete the reply
        $qryKillReply = $dbConn->query("
            delete
            from   replies
            where    ID = " . $dbConn->quote($_GET["reply"]) . "
            limit 1");
        /*$qryKillReply = $dbConn->query("
            update   replies
            set      txtReply = '*** Deleted Post ***',
                     txtQuote = '',
                     intDisplaySig = 0
            where    ID = " . $dbConn->quote($_GET["reply"]));*/
        
        // update our counter
        $qryUpdate = $dbConn->query("
            update   topics
            set      intReplies = " . $replyCount . "
            where    ID = " . $qryForum["ID"]);
        
        // update our counter for the main forum page
        $qryUpdateForum = $dbConn->query("
            update    forums
            set        intPosts = " . $postCount . "
            where    ID = " . $qryForum["forumID"]);
        
        // all done
        print "
        <script language=\"javascript\">
        alert(\"The reply was successfully deleted.\");
        opener.location.href = '/forum_ggc/view_bb.php?forum=" . $qryForum["forumID"] . "&thread=" . $qryForum["ID"] . "';
        window.close();
        </script>";
    }
?>
