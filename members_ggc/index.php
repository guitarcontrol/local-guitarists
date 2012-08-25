<?php
    /*
        index.php
        
        This is the main page for the members to use to view all of their 
        pertinent info, as well as see the status of any submissions they 
        have.
        
    */
    
    // include our main app file
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    
    // make sure they're logged in
    verify_access($_SESSION["AccessLevel"], 1);
    
    // set the default count of items for this user
    $intItems = 0;
    $intTab = 0;
    $intLessons = 0;
    $intReviews = 0;
    $intThreads = 0;
    
    // see how many gear reviews they have
    $qryMusic = $dbConn->query("
        select        ID,
                    Title,
                    DateAdded
        from        music
        where        MemberID = " . $_SESSION["MemberID"] . "
        order by    DateAdded desc
        limit 5");
    
    // if any were found, allow them to be displayed
    if ($qryMusic->numRows()) {
        $intItems++;
        $intReviews = 1;
    }
    
    // see how many gear reviews they have
    $qryMusicCount = $dbConn->getRow("
        select  count(ID) as totals
        from    music
        where   MemberID = '" . $_SESSION["MemberID"] . "'",
        DB_FETCHMODE_ASSOC);
    
    // query the number of saved tabs for this user
    $qrySaved = $dbConn->query("
        select  intItem
        from    saved
        where   intMemID = " . $_SESSION["MemberID"] . " and
                intType IN ( 1,2,3,7,8 )");
    
    // query the db for our titles
    $qryTopics = $dbConn->query("
        select      ID,
                    intForum,
                    strTitle,
                    intReplies,
                    dateLastPost
        from        topics
        where       intMemID = " . $_SESSION["MemberID"] . "
        order by    dateLastPost desc
        limit 5");
    
    // specify the number of records found
    $intThreads = $qryTopics->numRows();
    
    // query the number of private messages to or from this member
    $qryPMs = $dbConn->query("
        select        ID
        from        messages
        where        (intMemID = " . $_SESSION["MemberID"] . " or 
                    intRecipient = " . $_SESSION["MemberID"] . ") and
                    intParent = 0");
    
    // create our page variables variables
    $pageTitle = "Members Area: Edit your member profile and communicate with other members";
    $pageDescription = "";
    $pageKeywords = "";
    $areaName = "members";
    
    // include our header
    //require("header.php");
	?>
    
    <link type="text/css" rel="stylesheet" href="/inc/styles.css" />
	<style>
    BODY {
        background: none;
    }
    </style>

    <br>
    <table width="100%" cellspacing="0" cellpadding="2" border="0">
    <tr>
        <td class="tablehead">&nbsp;&raquo;&nbsp;<a href="/forum_ggc/index.php"><b>Home</b></a>&nbsp;&raquo;&nbsp;Members Area&nbsp;&raquo;&nbsp;Welcome!</td>
    </tr>
    </table>
    
    <table width="100%" cellspacing="0" cellpadding="2" border="0">
    <tr valign="top">
        <?php //require("fastclick.php"); ?>
        <td align="right">
        
        <table width="100%" cellspacing="0" cellpadding="2" border="0">
        <tr>
            <td align="center" colspan="2">
            
            <table width="100%" cellpadding="1" cellspacing="2" border="0">
            <tr valign="top">
                <td width="33%"><img src="/images/pointer.gif" width="11" height="11" alt="" border="0">&nbsp;<a href="personal.php"><b>Account Info</b></a><br>
                Use this section to edit your name and signature file, as it
                appears on the web site. Also upload your photo, so people can
                take a look at you.</td>
                <td width="33%"><img src="/images/pointer.gif" width="11" height="11" alt="" border="0">&nbsp;<a href="forum.php"><b>Forum Preferences</b></a><br>
                Here you can specify the preferences for the forum pages,
                including IM settings, signature, and more.</td>
                <td width="33%"><img src="/images/pointer.gif" width="11" height="11" alt="" border="0">&nbsp;<a href="demo.php"><b>Demographic Info</b></a><br>
                If you have recently moved, or previously never setup your
                demographic information, then click here to change it.</td>
            </tr>
            <tr>
                <td colspan="3"><br></td>
            </tr>
            <tr valign="top">
                <td width="34%"><img src="/images/pointer.gif" width="11" height="11" alt="" border="0">&nbsp;<a href="playing.php"><b>Playing Info</b></a><br>
                Use this area to edit your information as it appears to other
                players in the community. This allows other guitar players of
                the same styles, abilities, etc. to find you.</td>
                <td><img src="/images/pointer.gif" width="11" height="11" alt="" border="0">&nbsp;<a href="email.php"><b>Edit Your Email</b></a><br>
                Here you can change the email that people see on this site,
                as well as the one we use to contact you (when needed).</td>
                <td><img src="/images/pointer.gif" width="11" height="11" alt="" border="0">&nbsp;<a href="password.php"><b>Edit Your Password</b></a><br>
                Change your password here that you use to log into the site.</td>
            </tr>
            <tr>
                <td colspan="3"><br></td>
            </tr>
            <tr valign="top">
                <td><img src="/images/pointer.gif" width="11" height="11" alt="" border="0">&nbsp;<a href="ratings.php"><b>Edit Your Ratings</b></a><br>
                You can now modify the ratings you have submiited for various items on the site (gear reviews, lessons, music, etc).</td>
                <td><!--<img src="/images/pointer.gif" width="11" height="11" alt="" border="0">&nbsp;<a href="remove.php"><b>Remove Your Acccount</b></a><br>
                If you wish to no longer have an account here with Guitarists.net, 
                then click here to have it permanently removed.  <b>This CANNOT be undone.</b>--></td>
                <td>&nbsp;</td>
            </tr>
            </table>
            
            </td>
        </tr>
        <tr>
              <td align="center"><br>
            
            <!--- begin main links table --->
            <table width="100%" cellspacing="3" cellpadding="1" border="0">
            <tr>
                <td class="innertitle">&nbsp;<img src="/images/pointer.gif" align="absmiddle" width="11" height="11" alt="" border="0">&nbsp;My Music</td>
                <td class="innertitle">&nbsp;<img src="/images/pointer.gif" align="absmiddle" width="11" height="11" alt="" border="0">&nbsp;My Threads</td>
            </tr>
            <tr valign="top">
                <td>
                <?php
                    // see if any were found
                    if ($qryMusic->numRows()) {
                        // set a row counter
                        $intRow = 1;
                        ?>
                        <table width="100%" cellspacing="0" cellpadding="1" border="0">
                        <?php
                        // loop through our results
                        while ($qryRow = $qryMusic->fetchRow(DB_FETCHMODE_ASSOC)) {
                            // set the class
                            $bgColor = "#ffffff";
                            if ($intRow % 2 == 0) {
                                $bgColor = "#f6f6f6";
                            }
                            ?>
                            <tr valign="top" bgcolor="<?php print $bgColor; ?>">
                                <td nowrap>
                                <a href="edit_song.php?id=<?php print $qryRow["ID"]; ?>"><b>E</b></a> |
                                <a href="delete_song.php?id=<?php print $qryRow["ID"]; ?>" style="color: red;" onClick="return confirm('Are you sure you want to remove this song?');"><b>X</b></a> 
                                </td>
                                <td><a href="/music/view.php?id=<?php print $qryRow["ID"]; ?>"><b><?php print $qryRow["Title"]; ?></b></a></td>
                                <td nowrap><?php print date("M\. j", strtotime($qryRow["DateAdded"])); ?></td>
                            </tr>
                            <?php
                            // update the row counter
                            $intRow++;
                        }
                        ?>
                        <tr>
                            <td align="right" colspan="4">
                            <b>&raquo;&nbsp;<a href="/music/submit.php"><b>Post a New Song</b></a>
                            <?php
                                if ($qryMusicCount["totals"] > 5) {
                                    ?><br>
                                    <b>&raquo;&nbsp;<a href="music.php"><b>Edit Songs</b></a>
                                    <?php
                                }
                            ?>
                            </td>
                        </tr>
                        </table>
                        <?php
                    } else {
                        ?>
                        You currently have <b>0</b> songs posted in our music section.  To add a 
                        new song, <a href="/music/submit.php"><b>click here</b></a>.
                        <?php
                    }
                ?>
                </td>
                <td>
                <?php
                    // see if they have any tab uploaded
                    if ($intThreads) {
                        // set a row counter
                        $intRow = 1;
                        ?>
                        <table width="100%" cellspacing="0" cellpadding="1" border="0">
                        <?php
                        // loop through our results
                        while ($qryRow = $qryTopics->fetchRow(DB_FETCHMODE_ASSOC)) {
                            $bgcolor = "#ffffff";
                            if ($intRow % 2 == 0) {
                                $bgcolor = "#f6f6f6";
                            }
                            ?>
                            <tr valign="top" bgcolor="<?php print $bgcolor; ?>">
                                <td><a href="/forum_ggc/view.php?forum=<?php print $qryRow["intForum"]; ?>&thread=<?php print $qryRow["ID"]; ?>"><b><?php print $qryRow["strTitle"]; ?></b></a> (<?php print $qryRow["intReplies"]; ?>)</td>
                                <td nowrap><?php print date("M\. j g\:i A", strtotime($qryRow["dateLastPost"])); ?></td>
                            </tr>
                            <?php
                            // update the row counter
                            $intRow++;
                        }
                        ?>
                        <tr>
                            <td align="right" colspan="2">
                            <b>&raquo;&nbsp;<a href="/forum_ggc/index.php"><b>View the forums</b></a>
                            </td>
                        </tr>
                        </table>
                        <?php
                    } else {
                        ?>
                        You currently do not have any active threads in our forums.  To start a new 
                        thread, or reply to a number of others, <a href="/forum_ggc/index.php"><b>click here</b></a>.
                        <?php
                    }
                ?>
                </td>
            </tr>
            <tr>
                <td colspan="2"><br></td>
            </tr>
            <tr>
                <td class="innertitle">&nbsp;<img src="/images/pointer.gif" align="absmiddle" width="11" height="11" alt="" border="0">&nbsp;<a href="saved.php">Saved Items</a> (<?php print $qrySaved->numRows(); ?>)</td>
                <td class="innertitle">&nbsp;<img src="/images/pointer.gif" align="absmiddle" width="11" height="11" alt="" border="0">&nbsp;<a href="/forum_ggc/msgs/index.php">Private Messages</a> (<?php print $qryPMs->numRows(); ?>)</td>
            </tr>
            <tr valign="top">
                <td>
                To easily view all of the you have saved here on the site (including
                subscribed threads, buddies, tablature, and more, 
                <a href="saved.php"><b>click here</b></a>.
                </td>
                <td>
                Send <a href="/forum_ggc/msgs/index.php"><b>private messages</b></a> to other users, or read 
                messages sent to you from other members of the community.
                </td>
            </tr>
            </table>
            <!--- end main links table --->
            
            </td>
        </tr>
        </table>
        
        </td>
        <?php if ($adPlace == 2) { require("fastclick.php"); } ?>
    </tr>
    </table>

<?php
    // include our footer
    require("footer.php");
?>
