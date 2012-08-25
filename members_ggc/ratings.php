<?php
    /*
        ratings.php
        
        This allows a member to edit ratings posted to the site.
    */
    
    // include our main app file
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    
    // make sure they're logged in
    verify_access($_SESSION["AccessLevel"], 1);
    
    // query our gear ratings
    $qryGear = $dbConn->query("
        select    ratings.ID,
                ratings.intActive,
                ratings.dateAdded,
                gear.strModelName,
                makers.strCompany
        from    ratings,
                gear,
                makers
        where    ratings.intMemID = '" . $_SESSION["MemberID"] . "' and
                ratings.intArea = 1 and
                ratings.intItemID = gear.ID and
                gear.intCompany = makers.ID");
    
    // query our lesson ratings
    $qryLessons = $dbConn->query("
        select    ratings.ID,
                ratings.intActive,
                ratings.dateAdded,
                lessons.strTitle,
                categories.strTitle as catTitle
        from    ratings,
                lessons,
                categories
        where    ratings.intMemID = '" . $_SESSION["MemberID"] . "' and
                ratings.intArea = 2 and
                ratings.intItemID = lessons.ID and
                lessons.intCatID = categories.ID");
    
    // query our software ratings
    $qryApps = $dbConn->query("
        select    ratings.ID,
                ratings.intActive,
                ratings.dateAdded,
                software.strName,
                categories.strTitle as catTitle
        from    ratings,
                software,
                categories
        where    ratings.intMemID = '" . $_SESSION["MemberID"] . "' and
                ratings.intArea = 3 and
                ratings.intItemID = software.ID and
                software.intCatID = categories.ID");
    
    // query our software ratings
    $qryTabs = $dbConn->query("
        select    ratings.ID,
                ratings.intActive,
                ratings.dateAdded,
                tablature.strSongName,
                tab_dirs.strCatID,
                tab_dirs.strBandName
        from    ratings,
                tablature,
                tab_dirs
        where    ratings.intMemID = '" . $_SESSION["MemberID"] . "' and
                ratings.intArea = 4 and
                ratings.intItemID = tablature.ID and
                tablature.intBand = tab_dirs.ID");
    
    // query our music ratings
    $qryMusic = $dbConn->query("
        select    ratings.ID,
                ratings.intActive,
                ratings.dateAdded,
                music.Title,
                categories.strTitle as catTitle
        from    ratings,
                music,
                categories
        where    ratings.intMemID = '" . $_SESSION["MemberID"] . "' and
                ratings.intArea = 5 and
                ratings.intItemID = music.ID and
                music.CategoryID = categories.ID");
    
    // create our page variables
    $pageTitle = "Members Area: Ratings Editor";
    $pageDescription = "";
    $pageKeywords = "";
    $areaName = "members";
    
    // include our header
    // require("header.php");
?>
    <link type="text/css" rel="stylesheet" href="/inc/styles.css" />
	<style>
    BODY {
        background: none;
    }
    </style>
    <table width="100%" cellspacing="0" cellpadding="1" border="0">
    <tr valign="top">
        <?php //if ($adPlace == 1) { require("fastclick.php"); } ?>
        <td>
        
        <table width="100%" cellspacing="0" cellpadding="1" border="0">
        <tr>
            <td colspan="4" class="innertitle">&nbsp;&raquo;&nbsp;Gear Reviews (<?php print $qryGear->numRows(); ?>)</td>
        </tr>
        <tr>
        <?php
            // see if we found any reviews
            if ($qryGear->numRows()) {
                // sett the row counter
                $row = 1;
                
                // loop through our results
                while ($qryRow = $qryGear->fetchRow(DB_FETCHMODE_ASSOC)) {
                    // set the bgcolor
                    $bgcolor = "#ffffff";
                    if ($row % 2 == 0) {
                        $bgcolor = "#f6f6f6";
                    }
                    ?>
                    <tr bgcolor="<?php print $bgcolor; ?>">
                        <td>
                        <a href="/rate/edit.php?id=<?php print $qryRow["ID"]; ?>"><b>E</b></a> |
                        <a href="/rate/delete.php?id=<?php print $qryRow["ID"]; ?>" onClick="return confirm('Are you sure you want to permanently REMOVE this review?');"><b style="color: red;">X</b></a>
                        </td>
                        <td><?php print $qryRow["strCompany"] . "&nbsp;&raquo;&nbsp;" . $qryRow["strModelName"]; ?></td>
                        <td>
                        <?php
                            // see if it's active or not
                            if ($qryRow["intActive"]) {
                                print "Active";
                            } else {
                                print "<i style=\"color: #c0c0c0;\">Pending</i>";
                            }
                        ?>
                        </td>
                        <td><?php print date("M j, Y", strtotime($qryRow["dateAdded"])); ?></td>
                    </tr>
                    <?php
                    $row++;
                }
            } else {
                ?>
                <td colspan="4">
                You currently have <b>0</b> gear reviews posted at this time.  Why not consider 
                <a href="/gear/index.php"><b>adding some</b></a> now.
                </td>
                <?php
            }
        ?>
        </tr>
        <tr>
            <td colspan="4"><br></td>
        </tr>
        <tr>
            <td colspan="4" class="innertitle">&nbsp;&raquo;&nbsp;Music Reviews (<?php print $qryMusic->numRows(); ?>)</td>
        </tr>
        <tr>
        <?php
            // see if we found any reviews
            if ($qryMusic->numRows()) {
                // sett the row counter
                $row = 1;
                
                // loop through our results
                while ($qryRow = $qryMusic->fetchRow(DB_FETCHMODE_ASSOC)) {
                    // set the bgcolor
                    $bgcolor = "#ffffff";
                    if ($row % 2 == 0) {
                        $bgcolor = "#f6f6f6";
                    }
                    ?>
                    <tr bgcolor="<?php print $bgcolor; ?>">
                        <td>
                        <a href="/rate/edit.php?id=<?php print $qryRow["ID"]; ?>"><b>E</b></a> |
                        <a href="/rate/delete.php?id=<?php print $qryRow["ID"]; ?>" onClick="return confirm('Are you sure you want to permanently REMOVE this review?');"><b style="color: red;">X</b></a>
                        </td>
                        <td><?php print $qryRow["catTitle"] . "&nbsp;&raquo;&nbsp;" . $qryRow["Title"]; ?></td>
                        <td>
                        <?php
                            // see if it's active or not
                            if ($qryRow["intActive"]) {
                                print "Active";
                            } else {
                                print "<i style=\"color: #c0c0c0;\">Pending</i>";
                            }
                        ?>
                        </td>
                        <td><?php print date("M j, Y", strtotime($qryRow["dateAdded"])); ?></td>
                    </tr>
                    <?php
                    $row++;
                }
            } else {
                ?>
                <td colspan="4">
                You currently have <b>0</b> music reviews posted at this time.  Why not 
                <a href="/music/index.php"><b>listen to some of the songs posted</b></a> and let the members 
                know what you think of them.
                </td>
                <?php
            }
        ?>
        </tr>
        <tr>
            <td colspan="4"><br></td>
        </tr>
        <tr>
            <td colspan="4" class="innertitle">&nbsp;&raquo;&nbsp;Lesson Reviews (<?php print $qryLessons->numRows(); ?>)</td>
        </tr>
        <tr>
        <?php
            // see if we found any reviews
            if ($qryLessons->numRows()) {
                // sett the row counter
                $row = 1;
                
                // loop through our results
                while ($qryRow = $qryLessons->fetchRow(DB_FETCHMODE_ASSOC)) {
                    // set the bgcolor
                    $bgcolor = "#ffffff";
                    if ($row % 2 == 0) {
                        $bgcolor = "#f6f6f6";
                    }
                    ?>
                    <tr bgcolor="<?php print $bgcolor; ?>">
                        <td>
                        <a href="/rate/edit.php?id=<?php print $qryRow["ID"]; ?>"><b>E</b></a> |
                        <a href="/rate/delete.php?id=<?php print $qryRow["ID"]; ?>" onClick="return confirm('Are you sure you want to permanently REMOVE this review?');"><b style="color: red;">X</b></a>
                        </td>
                        <td><?php print $qryRow["catTitle"] . "&nbsp;&raquo;&nbsp;" . $qryRow["strTitle"]; ?></td>
                        <td>
                        <?php
                            // see if it's active or not
                            if ($qryRow["intActive"]) {
                                print "Active";
                            } else {
                                print "<i style=\"color: #c0c0c0;\">Pending</i>";
                            }
                        ?>
                        </td>
                        <td><?php print date("M j, Y", strtotime($qryRow["dateAdded"])); ?></td>
                    </tr>
                    <?php
                    $row++;
                }
            } else {
                ?>
                <td colspan="4">
                You currently have <b>0</b> lesson reviews posted at this time.  Why not 
                <a href="/lessons/index.php"><b>read a few lessons</b></a> and let the members 
                know what you think of them.
                </td>
                <?php
            }
        ?>
        </tr>
        <!--- <tr>
            <td colspan="4"><br></td>
        </tr>
        <tr>
            <td colspan="4" class="innertitle">&nbsp;&raquo;&nbsp;Software Reviews (<?php print $qryApps->numRows(); ?>)</td>
        </tr>
        <tr>
        <?php
            // see if we found any reviews
            if ($qryApps->numRows()) {
                // sett the row counter
                $row = 1;
                
                // loop through our results
                while ($qryRow = $qryApps->fetchRow(DB_FETCHMODE_ASSOC)) {
                    // set the bgcolor
                    $bgcolor = "#ffffff";
                    if ($row % 2 == 0) {
                        $bgcolor = "#f6f6f6";
                    }
                    ?>
                    <tr bgcolor="<?php print $bgcolor; ?>">
                        <td>
                        <a href="/rate/edit.php?id=<?php print $qryRow["ID"]; ?>"><b>E</b></a> |
                        <a href="/rate/delete.php?id=<?php print $qryRow["ID"]; ?>" onClick="return confirm('Are you sure you want to permanently REMOVE this review?');"><b style="color: red;">X</b></a>
                        </td>
                        <td><?php print $qryRow["catTitle"] . "&nbsp;&raquo;&nbsp;" . $qryRow["strName"]; ?></td>
                        <td>
                        <?php
                            // see if it's active or not
                            if ($qryRow["intActive"]) {
                                print "Active";
                            } else {
                                print "<i style=\"color: #c0c0c0;\">Pending</i>";
                            }
                        ?>
                        </td>
                        <td><?php print date("M j, Y", strtotime($qryRow["dateAdded"])); ?></td>
                    </tr>
                    <?php
                    $row++;
                }
            } else {
                ?>
                <td colspan="4">
                You currently have <b>0</b> software reviews posted at this time.  Why not 
                <a href="/software/index.php"><b>download a few</b></a> and let us know what you think of them.
                </td>
                <?php
            }
        ?>
        </tr>
        <tr>
            <td colspan="4"><br></td>
        </tr>
        <tr>
            <td colspan="4" class="innertitle">&nbsp;&raquo;&nbsp;Tablature Reviews (<?php print $qryTabs->numRows(); ?>)</td>
        </tr>
        <tr>
        <?php
            // see if we found any reviews
            if ($qryTabs->numRows()) {
                // sett the row counter
                $row = 1;
                
                // loop through our results
                while ($qryRow = $qryTabs->fetchRow(DB_FETCHMODE_ASSOC)) {
                    // set the bgcolor
                    $bgcolor = "#ffffff";
                    if ($row % 2 == 0) {
                        $bgcolor = "#f6f6f6";
                    }
                    ?>
                    <tr bgcolor="<?php print $bgcolor; ?>">
                        <td>
                        <a href="/rate/edit.php?id=<?php print $qryRow["ID"]; ?>"><b>E</b></a> |
                        <a href="/rate/delete.php?id=<?php print $qryRow["ID"]; ?>" onClick="return confirm('Are you sure you want to permanently REMOVE this review?');"><b style="color: red;">X</b></a>
                        </td>
                        <td><?php print $qryRow["strCatID"] . "&nbsp;&raquo;&nbsp;" . $qryRow["strBandName"] . "&nbsp;&raquo;&nbsp;" . $qryRow["strSongName"]; ?></td>
                        <td>
                        <?php
                            // see if it's active or not
                            if ($qryRow["intActive"]) {
                                print "Active";
                            } else {
                                print "<i style=\"color: #c0c0c0;\">Pending</i>";
                            }
                        ?>
                        </td>
                        <td><?php print date("M j, Y", strtotime($qryRow["dateAdded"])); ?></td>
                    </tr>
                    <?php
                    $row++;
                }
            } else {
                ?>
                <td colspan="4">
                You currently have <b>0</b> tab reviews posted at this time.  Why not 
                <a href="/tab/index.php"><b>rate some</b></a> to help other members out.
                </td>
                <?php
            }
        ?>
        </tr> --->
        </table>
        
        </td>
        <?php if ($adPlace == 2) { require("fastclick.php"); } ?>
    </tr>
    </table>
    
<?php
    // include our footer
    require("footer.php");
?>
