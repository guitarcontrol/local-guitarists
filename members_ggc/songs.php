<?php
    /*
        songs.php
        
        Here we give the user a list of the songs they have posted, so they 
        can edit or delete them.
    */
    
    // include our needed file(s)
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    require("functions.php");
    
    // specify the order to sort by (asc or desc)
    if (isset($_GET["sort"])) {
        $sort = $_GET["sort"];
        if ($sort == "asc") {
            $oppsort = "desc";
        } else {
            $oppsort = "asc";
        }
    } else {
        $sort = "asc";
        $oppsort = "desc";
    }
    
    // specify how to sort the data
    switch($_GET["col"]) {
        case 1:
            $column = "music.Title";
            $colnum = 1;
            break;
        case 2:
            $column = "categories.strTitle";
            $colnum = 2;
            break;
        case 3:
            $column = "music.Active";
            $colnum = 3;
            break;
        case 4:
            $column = "music.DateAdded";
            $colnum = 4;
            $sort = "desc";
            break;
        default:
            $column = "music.DateAdded";
            $colnum = 4;
            $sort = "desc";
            break;
    }
    
    // get our 3 newest lessons
    $qryData = $dbConn->query("
        select        music.ID,
                    music.Title,
                    music.DateAdded,
                    music.Active,
                    categories.ID as catID,
                    categories.strTitle as catName,
                    members.ID as memID,
                    members.strUsername
        from         music,
                    categories,
                    members
        where        music.MemberID = " . $_SESSION["MemberID"] . " and
                    music.CategoryID = categories.ID and
                    music.MemberID = members.ID
        order by     " . $column . " " . $sort);
    
    // set our page variables
    $pageTitle = "Guitar Resources: Members Area: Your Music";
    $pageDescription = "";
    $pageKeywords = "";
    $areaName = "members";
    
    // specify the # of rows to display per page
    $display = 25;
    
    // setup our previous/next links
    if (isset($_GET["page"])) {
        $startRow = $_GET["page"];
        $endRow = $startRow + ($display - 1);
    } else {
        $startRow = 0;
        $endRow = $display - 1;
    }
    
    // include our header
    require("header.php");
?>

    <br>
    <table width="100%" cellspacing="0" cellpadding="2" border="0">
    <tr>
        <td class="tablehead">&nbsp;&raquo;&nbsp;<a href="/index.php"><b>Home</b></a>&nbsp;&raquo;&nbsp;<a href="index.php"><b>Members Area</b></a>&nbsp;&raquo;&nbsp;Your Songs</td>
    </tr>
    </table>
    
    <table width="100%" cellspacing="0" cellpadding="2" border="0">
    <tr valign="top">
        <?php if ($adPlace == 1) { require("fastclick.php"); } ?>
        <td>
        
        <table width="100%" cellspacing="0" cellpadding="1" border="0">
        <tr>
            <td>
            Welcome to "Our Music," the home for songs written and recorded by the Guitarists.net
            community. Simply browse below to find the type of music that interests you.  Click 
            on the field name to sort by that field.
            <br><br>
            </td>
        </tr>
        <tr>
            <td>
            
            <!--- begin display table --->
            <table width="100%" cellspacing="0" cellpadding="2" border="0">
            <tr>
                <td class="innertitle">&nbsp;</td>
                <td class="innertitle">&nbsp;&raquo;&nbsp;<a href="songs.php?col=1&sort=<?php if ($colnum == 1) { print $oppsort; } else { print "asc"; } ?>&page=<?php print $startRow; ?>" class="listitem">Song Title</a></td>
                <td class="innertitle">&nbsp;&raquo;&nbsp;<a href="songs.php?col=2&sort=<?php if ($colnum == 2) { print $oppsort; } else { print "asc"; } ?>&page=<?php print $startRow; ?>" class="listitem">Category</a></td>
                <td class="innertitle">&nbsp;&raquo;&nbsp;<a href="songs.php?col=3&sort=<?php if ($colnum == 3) { print $oppsort; } else { print "asc"; } ?>&page=<?php print $startRow; ?>" class="listitem">Active</a></td>
                <td class="innertitle">&nbsp;&raquo;&nbsp;<a href="songs.php?col=4&sort=<?php if ($colnum == 4) { print $oppsort; } else { print "asc"; } ?>&page=<?php print $startRow; ?>" class="listitem">Date</a></td>
            </tr>
            <?php
                // set our row counter
                $row = 0;
                
                // loop through our query results
                while ($qryRow = $qryData->fetchRow(DB_FETCHMODE_ASSOC)) {
                    // see if we need to displaythis data
                    if ($row >= $startRow && $row <= $endRow) {
                        // set our bgcolor
                        $bgcolor = "#f6f6f6";
                        if ($row % 2 == 0) {
                            $bgcolor = "#ffffff";
                        }
                        ?>
                        <tr bgcolor="<?php print $bgcolor; ?>">
                            <td nowrap>
                            <a href="edit_song.php?id=<?php print $qryRow["ID"]; ?>"><b>E</b></a> |
                            <a href="delete_song.php?id=<?php print $qryRow["ID"]; ?>" style="color: red;" onClick="return confirm('Are you sure you want to remove this song?');"><b>X</b></a>
                            </td>
                            <td><b><?php print $qryRow["Title"]; ?></a></td>
                            <td nowrap><?php print $qryRow["catName"]; ?></td>
                            <td nowrap><?php if ($qryRow["Active"]) { print "Active"; } else { print "<u style=\"text-decoration: line-through;\">Inactive</u>"; } ?></td>
                            <td nowrap><?php print date("M j, Y", strtotime($qryRow["DateAdded"])); ?></td>
                        </tr>
                        <?php
                    }
                    
                    // update our row counter
                    $row++;
                }
                
                // see if we need to display prev/next links
                if ($qryData->numRows() > $display) {
                    $strURL = "index.php?col=" . $colnum . "&sort=" . $sort . "&";
                    f_prevnext($qryData->numRows(), $display, $startRow, '4', $strURL);
                }
            ?>
            </table>
            <!--- end display table --->
            
            </td>
        </tr>
        </table>
        
        </td>
        <?php if ($adPlace == 2) { require("fastclick.php"); } ?>
    </tr>
    </table>

<?php
    // include our header
    require("footer.php");
?>
