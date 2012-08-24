<?php
    /*
        posters.php
        
        Here we'll simply display the top 50 posters in the forums and their info.
    */
    
    // include our main app file
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    
    // process our results
    $qryMemList = $dbConn->query("
        select      members.ID,
                    members.strUsername,
                    members.intPosts,
                    members.intAccess,
                    members.strAccess,
                    members.dateLVisit,
                    about.strCity,
                    about.intState,
                    about.strState,
                    about.intCountry,
                    about.intPlayYears,
                    countries.strCountry
        from        members,
                    about,
                    countries
        where       members.intBanned = 0 and
                    members.ID = about.intMemID and
                    about.intCountry = countries.ID
        order by    members.intPosts desc
        limit 50");
    
    // create our page variables
    $pageTitle = "Guitar Discussion: Our Top 50 Posters";
    $areaName = "forums";
    
    // include our header file
    if (empty($_SESSION["GGCIFrame"])) {
        require("header.php");
    } else {
        ?>
        <link type="text/css" rel="stylesheet" href="/inc/styles.css" />
        <style>
        BODY {
            background: none;
        }
        </style>
        <?php
    }

    if (empty($_SESSION["GGCIFrame"])) {
?>
    
    <br />
    <table width="100%" cellspacing="0" cellpadding="2" border="0">
    <tr valign="top">
        <td align="right">
        
        <table width="100%" cellspacing="0" cellpadding="1" border="0">
        <tr>
            <td class="tablehead">&nbsp;&raquo;&nbsp;<a href="/index.php"><b>Home</b></a>&nbsp;&raquo;&nbsp;<a href="index.php"><b>Guitar Discussion</b></a>&nbsp;&raquo;&nbsp;Our Top 50 Posters</td>
        </tr>
        </table>
<?php } else {
    ?>
    <p><a href="index.php?ggc=<?php print $_SESSION["GGCIFrame"]; ?>"><b>Guitar Discussion</b></a>&nbsp;&raquo;&nbsp;Our Top 50 Posters</p>
    <?php } ?>
        
        <table width="100%" cellspacing="0" cellpadding="2" border="0">
        <?php
            // continue, based on the number of records found
            if ($qryMemList->numRows()) {
                // create a row counter
                $row = 1;
                ?>
                <tr bgcolor="#08087C">
                    <td width="25%"><b style="color: #ffffff;">&nbsp;Name</b></td>
                    <td width="30%"><b style="color: #ffffff;">Location</b></td>
                    <td width="15%" align="center"><b style="color: #ffffff;">Posts</b></td>
                    <td width="15%"><b style="color: #ffffff;">Status</b></td>
                    <td width="15%"><b style="color: #ffffff;">Last Login</b></td>
                </tr>
                <?php
                // loop through our results
                while ($qryRow = $qryMemList->fetchRow(DB_FETCHMODE_ASSOC)) {
                    // set our background color
                    $bgcolor = "#ffffff";
                    if ($row % 2 == 0) {
                        $bgcolor = "#f6f6f6";
                    }
                    ?>
                    <tr bgcolor="<?php print $bgcolor; ?>">
                        <td nowrap><b>&raquo;&nbsp;
                        <?php if (empty($_SESSION["GGCIFrame"])) {
                            ?><a href="/members_ggc/profile.php?user=<?php print $qryRow["ID"]; ?>"><?php print $qryRow["strUsername"]; ?></a><?php
                        } else {
                            ?><?php print $qryRow["strUsername"]; ?><?php
                        } ?></b></td>
                        <td><?php if (strlen($qryRow["strCity"])) { print $qryRow["strCity"] . ","; } ?> <?php print $qryRow["strState"]; ?> <?php print $qryRow["strCountry"]; ?></td>
                        <td align="center" nowrap><?php print number_format($qryRow["intPosts"]); ?></td>
                        <td nowrap>
                        <?php
                        // display their status
                        if ($qryRow["intAccess"] >= 20 && strlen($qryRow["strAccess"])) {
                            print $qryRow["strAccess"];
                        } else {
                            switch ($qryRow["intAccess"]) {
                                case 1: print "New Member"; break;
                                case 2: print "Member"; break;
                                case 3: print "Junior Member"; break;
                                case 4: print "Intermediate Member"; break;
                                case 5: print "Advanced Member"; break;
                                case 6: print "Power User"; break;
                                case 10: print "Affiliate"; break;
                                case 11: print "Supporter"; break;
                                case 12: print "Teacher"; break;
                                case 13: print "Advertiser"; break;
                                case 14: print "Preferred Member"; break;
                                case 20: print "Preferred Member"; break;
                                case 90: print "Moderator"; break;
                                case 95: print "Editor"; break;
                                case 99: print "Administrator"; break;
                                default: print "Member"; break;
                            }
                        }
                        ?></td>
                        <td><?php print date("M\. j, Y", strtotime($qryRow["dateLVisit"])); ?></td>
                    </tr>
                    <?php
                    $row++;
                }
            } else {
                ?>
                <tr>
                    <td colspan="5">
                    We do not currently have any top posters.
                    <p>
                    Thanks.
                    </td>
                </tr>
                <?php
            }
        ?>
        </table>
        
        </td>
        <?php //require("fastclick.php"); ?>
    </tr>
    </table>

<?php
    // include our footer
    if (empty($_SESSION["GGCIFrame"])) {
        require("footer.php");
    }
?>
