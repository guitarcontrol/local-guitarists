<?php
    /*
        saved.php
        
        Allows a member to easily browse all of the tabs they have saved on the site.  
        This way they don't have to go back through all of the directories to view 
        them.  They're all right here.
    */
    
    // include our needed file(s)
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    
    // make sure they're logged in
    verify_access($_SESSION["AccessLevel"], 1);
    
    // query our saved tab files
    $qryThreads = $dbConn->query("
        select        saved.intItem,
                    topics.ID,
                    topics.intForum,
                    topics.strTitle,
                    topics.intReplies,
                    topics.intLastID,
                    topics.dateLastPost,
                    forums.strName,
                    members.strUsername
        from        saved,
                    topics,
                    forums,
                    members
        where        saved.intType = 2 and
                    saved.intMemID = " . $_SESSION["MemberID"] . " and
                    saved.intItem = topics.ID and
                    topics.intForum = forums.ID and
                    topics.intLastID = members.ID
        order by    topics.dateLastPost desc");
    
    // set the number of record found
    $intThreads = $qryThreads->numRows();
    
    // query our saved tab files
    $qryTab = $dbConn->query("
        select        saved.intItem,
                    tablature.ID,
                    tablature.strFileName,
                    tablature.strSongName,
                    tab_dirs.ID as bandID,
                    tab_dirs.strCatID,
                    tab_dirs.strDirName,
                    tab_dirs.strBandName
        from        saved,
                    tablature,
                    tab_dirs
        where        saved.intType = 1 and
                    saved.intMemID = " . $_SESSION["MemberID"] . " and
                    saved.intItem = tablature.ID and
                    tablature.intBand = tab_dirs.ID
        order by    tab_dirs.strBandName,
                    tablature.strSongName");
    
    // set the number of record found
    $intTabs = $qryTab->numRows();
    
    // query our saved tab files
    $qryLessons = $dbConn->query("
        select        saved.intItem,
                    lessons.strTitle,
                    categories.strTitle as catTitle
        from        saved,
                    lessons,
                    categories
        where        saved.intType = 3 and
                    saved.intMemID = " . $_SESSION["MemberID"] . " and
                    saved.intItem = lessons.ID and
                    lessons.intCatID = categories.ID
        order by    categories.strTitle,
                    lessons.strTitle");
    
    // set the number of record found
    $intLessons = $qryLessons->numRows();
    
    // query our saved tab files
    $qryApps = $dbConn->query("
        select        saved.intItem,
                    software.strName,
                    categories.strTitle
        from        saved,
                    software,
                    categories
        where        saved.intType = 7 and
                    saved.intMemID = " . $_SESSION["MemberID"] . " and
                    saved.intItem = software.ID and
                    software.intCatID = categories.ID
        order by    categories.strTitle,
                    software.strName");
    
    // set the number of record found
    $intApps = $qryApps->numRows();
    
    // query our saved tab files
    $qrySongs = $dbConn->query("
        select        saved.intItem,
                    music.ID,
                    music.Title,
                    music.MemberID,
                    categories.strTitle,
                    members.strUsername
        from        saved,
                    music,
                    categories,
                    members
        where        saved.intType = 8 and
                    saved.intMemID = " . $_SESSION["MemberID"] . " and
                    saved.intItem = music.ID and
                    music.CategoryID = categories.ID and
                    music.MemberID = members.ID
        order by    categories.strTitle,
                    music.Title");
    
    // query our saved tab files
    $qryBuds = $dbConn->query("
        SELECT      members.ID,
                    members.strUsername,
                    saved.intItem
        FROM        members,
                    saved
        WHERE       saved.IntMemID = '" . $_SESSION["MemberID"] . "' AND
                    saved.intType = '4' AND
                    saved.intItem = members.ID
        ORDER BY    members.strUsername");
    
    // create our variables
    $pageTitle = "Members Area: Guitarists.net Favorite Items";
    $pageDescription = "";
    $pageKeywords = "";
    $areaName = "members";
    
    // include our header file
    require("header.php");
?>

    <br>
    <table width="100%" cellspacing="0" cellpadding="2" border="0">
    <tr>
        <td class="tablehead">&nbsp;&raquo;&nbsp;<a href="/index.php"><b>Home</b></a>&nbsp;&raquo;&nbsp;<a href="/members_ggc/index.php"><b>Members Area</b></a>&nbsp;&raquo;&nbsp;Your Favorite Items</td>
    </tr>
    </table>
    
    <table width="100%" cellspacing="0" cellpadding="2" border="0">
    <tr valign="top">
        <?php if ($adPlace == 1) { require("fastclick.php"); } ?>
        <td><br />
        
        <table width="100%" cellspacing="0" cellpadding="1" border="0">
        <tr valign="top">
            <td>
            
            <!--- start buddies table --->
            <table width="100%" cellspacing="0" cellpadding="2" border="0">
            <tr>
                <td class="tablehead">&nbsp;&raquo;&nbsp;Buddies</td>
            </tr>
            </table>
            
            <table width="100%" cellspacing="0" cellpadding="1" border="0">
            <tr valign="top">
                <td>
                <?php
                    // continue, based on thea mount of records pulled.
                    if ($qryBuds->numRows()) {
                        // set our row counter
                        $rowCount = 1;
                        
                        // set our cutoff
                        $cutoff = ceil($qryBuds->numRows() / 5);
                        
                        print "\n\n<!-- CUTOFF: " . $cutoff . " -->\n\n";
                        
                        // loop through our query results
                        while ($buddy = $qryBuds->fetchRow(DB_FETCHMODE_ASSOC)) {
                            // display our cell
                            print "<a href='delete.php?id=" . $buddy["intItem"] . "&type=4' title='Delete'><img src='images/delete.gif' width='16' height='16' alt='Delete' border='0' /></a> <a href='profile.php?user=" . $buddy["ID"] . "'><b>" . $buddy["strUsername"] . "</b></a><br />\n";
                            
                            // see if we need to start a new cell
                            if ($rowCount == $cutoff) {
                                print "</td><td>\n";
                                $rowCount = 1;
                            } else {
                                $rowCount++;
                            }
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="5">
                            You do not currently have any buddies saved in the database.  But fret not!  Browse the 
                            <a href="/forum" title="Guitar forums here at Guitarists.net"><b>forums</b></a>, get to know people, 
                            and the list will appear here, once you add them!  So get to chating now!
                            </td>
                        </tr>
                        <?php
                    }
                ?>
                </td>
            </tr>
            </table>
            <!--- end tab table --->
            
            <p>
            
            <!--- start tab table --->
            <table width="100%" cellspacing="0" cellpadding="2" border="0">
            <tr>
                <td class="tablehead">&nbsp;&raquo;&nbsp;Subscribed Threads</td>
            </tr>
            </table>
            
            <table width="100%" cellspacing="0" cellpadding="1" border="0">
            <?php
                // continue, based on thea mount of records pulled.
                if ($intThreads) {
                    // set our row counter
                    $rowCount = 0;
                    $innerCount = 1;
                    
                    // loop through our query results
                    while ($qryRow = $qryThreads->fetchRow(DB_FETCHMODE_ASSOC)) {
                        // see if it's in our page of results
                        // if ($rowCount >= $startRow && $rowCount <= $endRow) {
                            // set our background color
                            $bgcolor = "#ffffff";
                            if ($innerCount % 2 == 0) {
                                $bgcolor = "#f6f6f6";
                            }
                            ?>
                            <tr bgcolor="<?php print $bgcolor; ?>">
                                <td width="20"><a href="delete.php?id=<?php print $qryRow["intItem"]; ?>&type=2"><img src="images/delete.gif" width="16" height="16" alt="Delete" border="0"></a></td>
                                <td class="medtxt"><a href="/forum_ggc/view.php?forum=<?php print $qryRow["intForum"]; ?>&thread=<?php print $qryRow["ID"]; ?>"><b><?php print $qryRow["strTitle"]; ?></b></a> (<?php print $qryRow["intReplies"]; ?>)</td>
                                <td class="medtxt"><?php print $qryRow["strName"]; ?></td>
                                <td class="medtxt"><?php print date("n/j/Y \@ g:i a", strtotime($qryRow["dateLastPost"])); ?></td>
                                <td class="medtxt"><a href="/members_ggc/profile.php?user=<?php print $qryRow["intLastID"]; ?>"><b><?php print $qryRow["strUsername"]; ?></b></a></td>
                            </tr>
                            <?php
                        /* } */
                        
                        // update our row counter
                        $rowCount++;
                        $innerCount++;
                    }
                    ?>
                    <tr>
                        <td colspan="5">&raquo;&nbsp;<a href="/forum_ggc/index.php"><b>View Our Forums</b></a></td>
                    </tr>
                    <?php
                } else {
                    ?>
                    <tr>
                        <td>
                        You are not currently subscribed to any threads at this time.  Feel 
                        free to browse our <a href="/forum_ggc/index.php"><b>forums</b></a>, 
                        and subscribe to any threads that you find interesting.
                        </td>
                    </tr>
                    <?php
                }
            ?>
            </table>
            <!--- end tab table --->
            
            <p>
            
            <!--- start tab table --->
            <table width="100%" cellspacing="0" cellpadding="2" border="0">
            <tr>
                <td class="tablehead">&nbsp;&raquo;&nbsp;Tablature Files</td>
            </tr>
            </table>
            
            <table width="100%" cellspacing="0" cellpadding="1" border="0">
            <?php
                // continue, based on thea mount of records pulled.
                if ($intTabs) {
                    $rowCount = 0;
                    $innerCount = 1;
                    
                    // loop through our query results
                    while ($qryRow = $qryTab->fetchRow(DB_FETCHMODE_ASSOC)) {
                        // see if it's in our page of results
                        // if ($rowCount >= $startRow && $rowCount <= $endRow) {
                            // set our background color
                            $bgcolor = "#ffffff";
                            if ($innerCount % 2 == 0) {
                                $bgcolor = "#f6f6f6";
                            }
                            ?>
                            <tr bgcolor="<?php print $bgcolor; ?>">
                                <td width="20"><a href="delete.php?id=<?php print $qryRow["intItem"]; ?>&type=1"><img src="images/delete.gif" width="16" height="16" alt="Delete" border="0"></a></td>
                                <td class="medtxt"><a href="/tab/view.php?id=<?php print $qryRow["ID"]; ?>"><b><?php print $qryRow["strSongName"]; ?></b></a></td>
                                <td class="medtxt"><a href="/tab/bands.php?id=<?php print $qryRow["bandID"]; ?>"><b><?Php print $qryRow["strBandName"]; ?></b></a></td>
                                <td class="medtxt">
                                <?php
                                if (substr($qryRow["strFileName"], -4) == ".tab") {
                                    print "Guitar Tab";
                                } else if (substr($qryRow["strFileName"], -4) == ".crd") {
                                    print "Guitar Chords";
                                } else if (substr($qryRow["strFileName"], -5) == ".bass") {
                                    print "Bass Tab";
                                } else if (substr($qryRow["strFileName"], -5) == ".btab") {
                                    print "Bass Tab";
                                } else if (substr($qryRow["strFileName"], -4) == ".lyr") {
                                    print "Lyrics";
                                } else {
                                    print "Guitar Tab";
                                }
                                ?>
                                </td>
                            </tr>
                            <?php
                        /* } */
                        
                        // update our row counter
                        $rowCount++;
                        $innerCount++;
                    }
                    ?>
                    <tr>
                        <td colspan="4">&raquo;&nbsp;<a href="/tab/index.php"><b>Add More Tab</b></a></td>
                    </tr>
                    <?php
                } else {
                    ?>
                    <tr>
                        <td>
                        You do not currently have any tab files saved.  To do so, simply visit 
                        our <a href="/tab/index.php"><b>tablature section</b></a>, browse for 
                        any files that you would like to save, and then save them.
                        </td>
                    </tr>
                    <?php
                }
            ?>
            </table>
            <!--- end tab table --->
            
            <p>
            
            <!--- start lessons table --->
            <table width="100%" cellspacing="0" cellpadding="2" border="0">
            <tr>
                <td class="tablehead">&nbsp;&raquo;&nbsp;Saved Lessons</td>
            </tr>
            </table>
            
            <table width="100%" cellspacing="0" cellpadding="2" border="0">
            <?php
                // continue, based on thea mount of records pulled.
                if ($intLessons) {
                    $rowCount = 0;
                    $innerCount = 1;
                    
                    // loop through our query results
                    while ($qryRow = $qryLessons->fetchRow(DB_FETCHMODE_ASSOC)) {
                        
                        // see if it's in our page of results
                        // if ($rowCount >= $startRow && $rowCount <= $endRow) {
                            // set our background color
                            $bgcolor = "#ffffff";
                            if ($innerCount % 2 == 0) {
                                $bgcolor = "#f6f6f6";
                            }
                            ?>
                            <tr bgcolor="<?php print $bgcolor; ?>">
                                <td width="20"><a href="delete.php?id=<?php print $qryRow["intItem"]; ?>&type=3"><img src="images/delete.gif" width="16" height="16" alt="Delete" border="0"></a></td>
                                <td class="medtxt"><a href="/lessons/view.php?id=<?php print $qryRow["intItem"]; ?>"><b><?php print $qryRow["strTitle"]; ?></b></a></td>
                                <td class="medtxt"><?php print $qryRow["catTitle"]; ?></td>
                            </tr>
                            <?php
                        /* } */
                        
                        // update our row counter
                        $rowCount++;
                        $innerCount++;
                    }
                    ?>
                    <tr>
                        <td colspan="3">&raquo;&nbsp;<a href="/lessons/index.php"><b>Add More Lessons</b></a></td>
                    </tr>
                    <?php
                } else {
                    ?>
                    <tr>
                        <td>
                        You do not currently have any lessons saved.  To do so, simply visit 
                        our <a href="/lessons/index.php"><b>lessons section</b></a>, browse 
                        through our collection, and save any you would like to read again.
                        </td>
                    </tr>
                    <?php
                }
            ?>
            </table>
            <!--- end lessons table --->
            
            <p>
            
            <!--- start software table --->
            <table width="100%" cellspacing="0" cellpadding="2" border="0">
            <tr>
                <td class="tablehead">&nbsp;&raquo;&nbsp;Saved Software Title</td>
            </tr>
            </table>
            
            <table width="100%" cellspacing="0" cellpadding="1" border="0">
            <?php
                // continue, based on thea mount of records pulled.
                if ($intApps) {
                    $rowCount = 0;
                    $innerCount = 0;
                    
                    // loop through our query results
                    while ($qryRow = $qryApps->fetchRow(DB_FETCHMODE_ASSOC)) {
                        // see if it's in our page of results
                        // if ($rowCount >= $startRow && $rowCount <= $endRow) {
                            // set our background color
                            $bgcolor = "#ffffff";
                            if ($innerCount % 2 == 0) {
                                $bgcolor = "#f6f6f6";
                            }
                            ?>
                            <tr bgcolor="<?php print $bgcolor; ?>">
                                <td width="20"><a href="delete.php?id=<?php print $qryRow["intItem"]; ?>&type=7"><img src="images/delete.gif" width="16" height="16" alt="Delete" border="0"></a></td>
                                <td class="medtxt"><a href="/software/view.php?id=<?php print $qryRow["intItem"]; ?>"><b><?php print $qryRow["strName"]; ?></b></a></td>
                                <td class="medtxt"><?php print $qryRow["strTitle"]; ?></td>
                            </tr>
                            <?php
                        /* } */
                        
                        // update our row counter
                        $rowCount++;
                        $innerCount++;
                    }
                    ?>
                    <tr>
                        <td colspan="3">&raquo;&nbsp;<a href="/software/index.php"><b>Add More Titles</b></a></td>
                    </tr>
                    <?php
                } else {
                    ?>
                    <tr>
                        <td>
                        You do not currently have any applications saved.  To do so, simply visit 
                        our <a href="/software/index.php"><b>software section</b></a>, browse 
                        through our collection, and save any you would like to find again.
                        </td>
                    </tr>
                    <?php
                }
            ?>
            </table>
            <!--- end software table --->
            
            <p>
            
            <!--- start software table --->
            <table width="100%" cellspacing="0" cellpadding="2" border="0">
            <tr>
                <td class="tablehead">&nbsp;&raquo;&nbsp;Saved Songs</td>
            </tr>
            </table>
            
            <table width="100%" cellspacing="0" cellpadding="2" border="0">
            <?php
                // continue, based on thea mount of records pulled.
                if ($qrySongs->numRows()) {
                    // set a row counter
                    $rowCount = 0;
                    $innerCount = 1;
                    
                    // loop through our query results
                    while ($qryRow = $qrySongs->fetchRow(DB_FETCHMODE_ASSOC)) {
                        // set our bgcolor
                        $bgcolor = "#ffffff";
                        if ($innerCount % 2 == 0) {
                            $bgcolor = "#f6f6f6";
                        }
                        ?>
                        <tr bgcolor="<?php print $bgcolor; ?>">
                            <td width="20"><a href="delete.php?id=<?php print $qryRow["intItem"]; ?>&type=8"><img src="images/delete.gif" width="16" height="16" alt="Delete" border="0"></a></td>
                            <td class="medtxt"><a href="/music/view.php?id=<?php print $qryRow["intItem"]; ?>"><b><?php print $qryRow["Title"]; ?></b></a></td>
                            <td class="medtxt"><?php print $qryRow["strTitle"]; ?></td>
                            <td class="medtxt"><a href="/music/members.php?id=<?php print $qryRow["MemberID"]; ?>"><b><?php print $qryRow["strUsername"]; ?></b></a></td>
                        </tr>
                        <?php
                        // update our row counter
                        $rowCount++;
                        $innerCount++;
                    }
                    ?>
                    <tr>
                        <td colspan="3">&raquo;&nbsp;<a href="/music/index.php"><b>Browse All Songs</b></a></td>
                    </tr>
                    <?php
                } else {
                    ?>
                    <tr>
                        <td>
                        You do not currently have any members songs saved.  To do so, simply visit 
                        our <a href="/music/index.php"><b>music section</b></a>, browse 
                        through our collection, and save any you would like to listen to again.
                        </td>
                    </tr>
                    <?php
                }
            ?>
            </table>
            <!--- end music table --->
            
            </td>
        </tr>
        </table>
        
        </td>
    </tr>
    </table>

<?php
    // include our footer file
    require("footer.php");
?>
