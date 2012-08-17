<?php
    /*
        reset_posts.php
        
        This resets the post count for a member, if it has been reset to 0.
    */
    
    // include our needed files
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    require("functions.php");
    
    // make sure they're logged in
    verify_access($_SESSION["AccessLevel"], 90);
    
    // make sure a member ID was passed
    if (!isset($_GET["memid"])) {
        print "
        <script language=\"JavaScript\">
        alert(\"Please choose a member to reset first.  Thanks.\");
        window.close();
        </script>";
        exit();
    }
    
    // get their totals from topics
    $qryPosts = $dbConn->getRow("
        select  count(id) as totals
        from    topics
        where   intMemID = " . $dbConn->quote($_GET["memid"]),
        DB_FETCHMODE_ASSOC);
    
    // get their totals from replies
    $qryReplies = $dbConn->getRow("
        select  count(id) as totals
        from    replies
        where   intMemID = " . $dbConn->quote($_GET["memid"]),
        DB_FETCHMODE_ASSOC);
    
    // set the new total
    $newTotal = $qryPosts["totals"] + $qryReplies["totals"];
    
    // update the total in 'members'
    $qryUpdate = $dbConn->query("
        update  members
        set     intPosts = #variables.total#
        where   ID = " . $dbConn->quote($_GET["memid"]));
    
    // all done!
    print "
    <script language=\"JavaScript\">
    alert(\"The total was successfully updated!\");
    window.close();
    </script>";
?>