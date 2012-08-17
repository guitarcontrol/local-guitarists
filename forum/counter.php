<?php
    /*
        counter.php
        
        This allows for the topic and post counts to be updated dynamically.  
        As threads are deleted and moved, the counter can get out of whack.  
        This script fixes the bugs.
        
    */
    
    // include our needed file(s)
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    
    // get our list of ID's for all of the forums
    $qryIDs = $dbConn->query("
        select    ID
        from    forums");
    
    // loop through the results, and process accordingly
    while ($qryRow = $qryIDs->fetchRow(DB_FETCHMODE_ASSOC)) {
        // select our number of replies
        $qryReplies = $dbConn->getRow("
            select  sum(intReplies) as replies
            from    topics
            where   intForum = '" . $qryRow["ID"] . "'",
            DB_FETCHMODE_ASSOC);
        
        // select the number of posts
        $qryPosts = $dbConn->getRow("
            select  count(ID) as totals
            from    topics
            where   intForum = '" . $qryRow["ID"] . "'",
            DB_FETCHMODE_ASSOC);
        
        // update the forums
        $qryUpdate = $dbConn->query("
            update  forums
            set     intTopics = '" . $qryPosts["totals"] . "',
                    intPosts = '" . $qryReplies["replies"] . "'
            where   ID = '" . $qryRow["ID"] . "'");
    }
?>
