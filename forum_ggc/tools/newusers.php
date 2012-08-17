<?php
    /*
        newusers.php
        
        Displays the newest members that have posted to the forums in the 
        last 30 days, and allows a mod to ban 1 (or many) of them.
    */
    
    // include our needed code
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    
    // make sure they're logged in
    verify_access($_SESSION["AccessLevel"], 90);
    
    // set our cutoff time
    $cutoff = strtotime("-60 days");
    
    // query our members that have posted
    $qryPosts = $dbConn->query("
        select      ID,
                    strUsername,
                    intAccess,
                    dateLVisit,
                    strIP,
                    intPosts,
                    intFrozen,
                    intBanned
        from        members
        where       intPosts > 0 and
                    dateJoined >= '" . date("Y-m-d", $cutoff) . " 00:00:00'
        order by    dateLVisit desc");
    
    // set our page defaults
    $pageTitle = "Guitar Forums: Newest Members w/ Active Posts (" . $qryPosts->numRows() . " found)";
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
?>
    
    <br>
    <table width="100%" cellspacing="0" cellpadding="2" border="0">
    <tr valign="top">
        <td align="center">
    
        <!--- begin layout file --->
        <table width="100%" cellspacing="0" cellpadding="2" border="0">
        <form name="myPeeps" action="sessions_update.php" method="post" onSubmit="return valBans()">
        <input type="Hidden" name="memID" value="<?php print $_GET["memid"]; ?>" />
        <?php if (empty($_SESSION["GGCIFrame"])) { ?>
        <tr>
            <td class="tablehead">&nbsp;&raquo;&nbsp;Newest Members w/ Active Posts (<?php print $qryPosts->numRows(); ?> found)</td>
        </tr>
        <?php } ?>
        </table>
        
        <table width="100%" cellspacing="0" cellpadding="2" border="0">
        <?php
            // see if any records were found
            if ($qryPosts->numRows()) {
                ?>
                <tr bgcolor="#e5e5e5">
                    <td>&nbsp;</td>
                    <td class="smalltxt"><b>Username</b></td>
                    <td class="smalltxt"><b>IP Address</b></td>
                    <td class="smalltxt"><b>Posts</b></td>
                    <td class="smalltxt"><b>Acct Type</b></td>
                    <td class="smalltxt"><b>Status</b></td>
                    <td class="smalltxt"><b>Last Login</b></td>
                </tr>
                <?php
                $row = 1;
                
                // loop through our results
                while ($qryRow = $qryPosts->fetchRow(DB_FETCHMODE_ASSOC)) {
                    $bgcolor = "#ffffff";
                    if ($row % 2 == 0) {
                        $bgcolor = "#f6f6f6";
                    }
                    ?>
                    <tr bgcolor="<?php print $bgcolor; ?>">
                        <td><input type="Checkbox" name="UserID[]" value="<?php print $qryRow["ID"]; ?>" /></td>
                        <td><a href="list.php?memid=<?php print $qryRow["ID"]; ?>"><b><?php print $qryRow["strUsername"]; ?></b></a></td>
                        <td><a href="banned_ips.php?ip=<?php print $qryRow["strIP"]; ?>"><b><?php print $qryRow["strIP"]; ?></b></a></td>
                        <td><?php print $qryRow["intPosts"]; ?> post(s)</td>
                        <td>
                        <?php
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
                                case 95: print "Official Editor"; break;
                                case 99: print "Administrator"; break;
                                default: print "Member"; break;
                            }
                        ?>
                        </td>
                        <td>
                        <?php
                            if ($qryRow["intFrozen"]) {
                                print "Frozen";
                            } else if ($qryRow["intBanned"]) {
                                print "Banned";
                            } else {
                                print "Normal";
                            }
                        ?>
                        </td>
                        <td><?php print date("M\. j \@ g:i a", strtotime($qryRow["dateLVisit"])); ?></td>
                    </tr>
                    <?php
                    $row++;
                }
                ?>
                <tr>
                    <td colspan="7">
                    <input type="Submit" name="submit" value="Ban User(s)" class="smbutton" />
                    <input type="Submit" name="submit" value="Freeze User(s)" class="smbutton" />
                    <input type="Submit" name="submit" value="Reset User(s)" class="smbutton" />
                    </td>
                </tr>
                <?php
            } else {
                ?>
                <tr>
                    <td colspan="7">
                    There are <b>0</b> members that have registered in the past 30 days that have posted
                    in the forums.
                    </td>
                </tr>
                <?php
            }
        ?>
        </form>
        </table>
        <!--- end layout file --->
        
        </td>
        <?php //require("fastclick.php"); ?>
    </tr>
    </table>
    
    <script language="JavaScript">
    function valBans() {
        // set our default
        var intChecks = 0;
        
        // loop through all of our check boxes
        for (i = 0; i < document.myPeeps.elements.length; i++) {
            if (document.myPeeps.elements[i].type == "checkbox") {
                if (document.myPeeps.elements[i].checked == true) {
                    intChecks++;
                }
            }
        }
        
        // if it's still 0, than nothing was checked
        if (intChecks == 0) {
            alert("Please choose atleast 1 user to ban/freeze.");
            return false;
        }
        
        // all good
        return true;
    }
    </script>
    
<?php
    // include our footer
    if (empty($_SESSION["GGCIFrame"])) {
        require("footer.php");
    }
?>
