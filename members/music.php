<?php
    /*
        index.php
        
        Here we give the user a list of the main categories to choose from.  
        We also supply links to the 3 newest lessons that have been added, 
        as well as a link to submit a new lesson for admission.
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
                    categories.ID as catID,
                    categories.strTitle as catName
        from         music,
                    categories
        where        music.MemberID = " . $_SESSION["MemberID"] . " and
                    music.CategoryID = categories.ID
        order by     " . $column . " " . $sort);
    
    // set our page variables
    $pageTitle = "Members Area: Your Music";
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
    <tr valign="top">
        <?php if ($adPlace == 1) { require("fastclick.php"); } ?>
        <td>
        
        <table width="100%" cellspacing="0" cellpadding="2" border="0">
        <tr>
            <td class="tablehead">&nbsp;&raquo;&nbsp;<a href="/index.php"><b>Home</b></a>&nbsp;&raquo;&nbsp;<a href="index.php"><b>Members Area</b></a>&nbsp;&raquo;&nbsp;Your Music</td>
        </tr>
        <tr>
            <td>
            
            <!--- begin display table --->
            <table width="100%" cellspacing="0" cellpadding="2" border="0">
            <tr>
                <td class="innertitle">&nbsp;</td>
                <td class="innertitle">&nbsp;&raquo;&nbsp;<a href="index.php?col=1&sort=<?php if ($colnum == 1) { print $oppsort; } else { print "asc"; } ?>&page=<?php print $startRow; ?>" class="listitem">Song Title</a></td>
                <td class="innertitle">&nbsp;&raquo;&nbsp;<a href="index.php?col=2&sort=<?php if ($colnum == 2) { print $oppsort; } else { print "asc"; } ?>&page=<?php print $startRow; ?>" class="listitem">Category</a></td>
                <td class="innertitle">&nbsp;&raquo;&nbsp;<a href="index.php?col=4&sort=<?php if ($colnum == 4) { print $oppsort; } else { print "asc"; } ?>&page=<?php print $startRow; ?>" class="listitem">Date</a></td>
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
                            <a href="delete_song.php?id=<?php print $qryRow["ID"]; ?>&callPage=music.php" style="color: red;" onClick="return confirm('Are you sure you want to remove this song?');"><b>X</b></a>
                            </td>
                            <td><a href="/music/view.php?id=<?php print $qryRow["ID"]; ?>"><b><?php print $qryRow["Title"]; ?></b></a></td>
                            <td nowrap><?php print $qryRow["catName"]; ?></td>
                            <td nowrap><?php print date("M j, Y", strtotime($qryRow["DateAdded"])); ?></td>
                        </tr>
                        <?php
                    }
                    
                    // update our row counter
                    $row++;
                }
                
                // see if we need to display prev/next links
                if ($qryData->numRows() > $display) {
                    $strURL = "music.php?col=" . $colnum . "&sort=" . $sort . "&";
                    f_prevnext($qryData->numRows(), $display, $startRow, '4', $strURL);
                }
            ?>
            </table>
            <!--- end display table --->
            
            </td>
        </tr>
        <tr>
            <td><br>
            You can discuss these songs in <a href="/forum/topics.php?forum=23"><b>Our Music</b></a> forum.<br> 
            Feel free to <a href="submit.php"><b>add your own songs</b></a> to be listed here 
            (membership required).
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
