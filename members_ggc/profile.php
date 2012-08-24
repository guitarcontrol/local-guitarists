<?php
    /*
        profile.php
        
        This script allows a user to view info on the various member who's 
        link they clicked on.
    */
    
    // include our needed file(s)
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    require("functions.php");
    
    // make sure a user ID was passed
    if (!isset($_GET["user"])) {
        print "
        <script language=\"JavaScript\">
        alert(\"Please choose a member to view first.\");
        history.back();
        </script>";
        exit();
    }
    
    // query the data to display for the user
    $qryInfo = $dbConn->getRow("
        select  members.strUsername,
                members.strFName,
                members.strLName,
                members.strEmail,
                members.intPrivate,
                members.dateJoined,
                members.intAccess,
                members.strAccess,
                members.dateLVisit,
                about.strCity,
                about.intState,
                about.strState,
                about.intCountry,
                about.intAge,
                about.intPlayYears,
                about.intExperience,
                about.intSongTypes,
                about.intSituation,
                about.txtGear,
                about.txtComments,
                about.txtInfluences,
                about.strURL,
                files.filename as photo
        from    (members,
                about)
                LEFT JOIN files ON (members.ID = files.uid and files.filetype = 'photo')
        where   members.ID = " . $dbConn->quote($_GET["user"]) . " and 
                members.ID = about.intMemID",
        DB_FETCHMODE_ASSOC);
    
    // make sure a record was found
    if (!count($qryInfo)) {
        print "
        <script language=\"JavaScript\">
        alert(\"This member does not appear to exist.  Please verify the\\n\" +
              \"link and try again.  Thanks.\");
        history.back();
        </script>";
        exit();
    }
    
    // see if they're logged in
    $qrySess = $dbConn->query("
        select  UserID
        from    sessions
        where   UserID = " . $dbConn->quote($_GET["user"]));
    
    // query their styles of music they play
    $qryStyles = $dbConn->query("
        select      style,
                    sort
        from        styles,
                    member_styles
        where       member_styles.memid = " . $dbConn->quote($_GET["user"]) . " and
                    member_styles.styleid = styles.ID
        order by    sort");
    
    // see if they decided how to view the buddies
    if (!empty($_GET["view"]) && in_array($_GET["view"], array("all","offline","online"))) {
        $display = $_GET["view"];
        
        // set our where clause text
        if ($_GET["view"] == "all") {
            $where = "";
        } else if ($_GET["view"] == "offline") {
            $where = " AND (sessions.IPAddress = '' OR sessions.IPAddress IS NULL)";
        } else if ($_GET["view"] == "online") {
            $where = " AND (sessions.IPAddress != '' AND sessions.IPAddress IS NOT NULL)";
        }
    } else {
        $display = "all";
        $where = "";
    }
    
    // query the total # of buddies they have
    $arrBuds = $dbConn->getRow("select COUNT(*) as totals from saved where IntMemID = " . $dbConn->quote($_GET["user"]), DB_FETCHMODE_ASSOC);
    
    // query their online buddies
    $qryBuds = $dbConn->query("
        SELECT      members.ID,
                    members.strUsername,
                    saved.intItem,
                    files.filename as avatar,
                    sessions.IPAddress
        FROM        (members, saved)
                    LEFT JOIN files ON (members.ID = files.uid and files.filetype = 'avatar')
                    LEFT JOIN sessions ON (members.ID = sessions.UserID)
        WHERE       saved.IntMemID = " . $dbConn->quote($_GET["user"]) . " AND
                    saved.intType = '4' AND
                    saved.intItem = members.ID 
                    " . $where . "
        ORDER BY    members.strUsername");
    
    // query the state (if any)
    if ($qryInfo["intState"]) {
        $qryState = $dbConn->getRow("
            select  strAbbr
            from    states
            where   ID = '" . $qryInfo["intState"] . "' limit 1",
            DB_FETCHMODE_ASSOC);
        
        $strState = trim($qryState["strAbbr"]);
    } else {
        $strState = trim($qryInfo["strState"]);
    }
    
    // query the country (if any)
    if ($qryInfo["intCountry"]) {
        $qryCountry = $dbConn->getRow("
            select  strCountry
            from    countries
            where   ID = '" . $qryInfo["intCountry"] . "'",
            DB_FETCHMODE_ASSOC);
        
        $strCountry = trim($qryCountry["strCountry"]);
    } else {
        $strCountry = "N/A";
    }
    
    // set our page defaults
    $pageTitle = "Members Profile: " . $qryInfo["strFName"] . " " . $qryInfo["strLName"] . " (" . $qryInfo["strUsername"] . ")";
    $areaName = "members";
    $crlf = chr(10);
    
    // include our header file
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
        <td class="tablehead">&nbsp;&raquo;&nbsp;<a href="/forum_ggc/index.php"><b>Home</b></a>&nbsp;&raquo;&nbsp;Members Profile: <?php print $qryInfo["strFName"]; ?> <?php print $qryInfo["strLName"]; ?> (<?php print $qryInfo["strUsername"]; ?>)</td>
    </tr>
    </table>
    
    <table width="100%" cellspacing="0" cellpadding="2" border="0">
    <tr valign="top">
        <?php //require("fastclick.php"); ?>
        <td align="center">
    
        <!--- begin layout file --->
        <table width="100%" cellspacing="1" cellpadding="3" border="0">
        <tr valign="top">
            <td colspan="2">
            
            <?php
                // see if we should display the buddies
                if ($arrBuds["totals"]) {
                    ?>
                    <!-- display our buddies block -->
                    <div id="buddies">
                        <table id="buddies-table">
                        <tr valign='top' align='center'>
                        <?php
                            // set our cutoff
                            $cutoff = 1;
                            
                            // loop through our query and display
                            while ($bud = $qryBuds->fetchRow(DB_FETCHMODE_ASSOC)) {
                                // display our cell
                                print "<td class='smalltxt'>\n<a href='profile.php?user=" . $bud["ID"] . "&view=" . $display . "' title='Profile for " . $bud["strUsername"] . "'><b>" . $bud["strUsername"] . "</b></a><br />\n\n";
                                
                                // see if they have an avatar
                                if (!empty($bud["avatar"])) {
                                    // get the file specs of the image
                                    list($width, $height, $type, $attr) = getimagesize("../files/" . $bud["avatar"]);
                                    $width = $width / 2;
                                    $height = $height / 2;
                                    
                                    // display the image
                                    print "<img src='/files/" . $bud["avatar"] . "' width='" . $width . "' height='" . $height . "' alt='" . $bud["strUsername"] . "' class='buddy-icon' /><br />\n";
                                } else {
                                    print "<img src='/images/generic-avatar.gif' width='50' height='50' alt='" . $bud["strUsername"] . "' class='no-buddy-icon' /><br />\n";
                                }
                                
                                // see if they're online or not
                                if ($bud["IPAddress"]) {
                                    print "<b style=\"color: green;\">Online</b>\n";
                                }
                                
                                // end the cell
                                print "</td>\n";
                                
                                // see if we need to start over
                                if ($cutoff == 5) {
                                    print "</tr>\n<tr valign='top' align='center'>\n";
                                    $cutoff = 1;
                                } else {
                                    $cutoff++;
                                }
                            }
                        ?>
                        </tr>
                        <tr>
                            <td colspan="5" align="right">
                            Buddy Display:
                            <?php
                                // see what we need to display
                                if ($display == "all") {
                                    print "All";
                                } else { 
                                    print "<a href='profile.php?user=" . $_GET["user"] . "&view=all' title='Display All Buddies'>All</a>";
                                } 
                            ?> |
                            <?php
                                // see what we need to display
                                if ($display == "online") {
                                    print "Online";
                                } else { 
                                    print "<a href='profile.php?user=" . $_GET["user"] . "&view=online' title='Display Online Buddies'>Online</a>";
                                } 
                            ?> |
                            <?php
                                // see what we need to display
                                if ($display == "offline") {
                                    print "Offline";
                                } else { 
                                    print "<a href='profile.php?user=" . $_GET["user"] . "&view=offline' title='Display Offline Buddies'>Offline</a>";
                                } 
                            ?>
                            </td>
                        </tr>
                        </table>
                    </div>
                    <?php
                }
            ?>
            
            <b>Last Login:</b> 
            <?php
                // display their last login data
                print date("F j, Y \@ g:i A", strtotime($qryInfo["dateLVisit"]));
                
                // see if they're logged in now
                if ($qrySess->numRows()) {
                    print " - <b style=\"color: green;\">Online Now</b>\n";
                } else {
                    print " - <b style=\"color: red;\">Offline</b>\n";
                }
            ?>
            <p>
            <b>A little about <?php print trim($qryInfo["strUsername"]); ?>:</b>
            <p>
            <table width="400" cellspacing="0" cellpadding="2" border="0">
            <?php
                // make sure they're not set to private
                if (!$qryInfo["intPrivate"] && !empty($qryInfo["strFName"])) {
                    ?>
                    <tr>
                        <td><b>Name:</b></td>
                        <td>
                        <?php
                        // see if they're logged in or not
                        if ($_SESSION["MemberID"]) {
                            print "<a href=\"mailto:" . mask_email($qryInfo["strEmail"]) . "\" title=\"Email " . trim($qryInfo["strUsername"]) . "\"><b>" . trim($qryInfo["strFName"]) . " " . trim($qryInfo["strLName"]) . "</b></a>";
                        } else {
                            print trim($qryInfo["strFName"]) . " " . trim($qryInfo["strLName"]);
                        }
                        ?></td>
                    </tr>
                    <?php
                }
                
                // make sure they're not set to private
                if (!$qryInfo["intPrivate"] && !empty($qryInfo["intAge"])) {
                    ?>
                    <tr>
                        <td width="120"><b>Age:</b></td>
                        <td width="280">
                        <?php
                        // display their age
                        if ($qryInfo["intAge"]) {
                            print $qryInfo["intAge"] . " years old";
                        } else {
                            print "N/A";
                        }
                        ?>
                        </td>
                    </tr>
                    <?php
                }
                
                // make sure they're not set to private
                if (!$qryInfo["intPrivate"] && !empty($qryInfo["strCity"])) {
                    ?>
                    <tr>
                        <td><b>Location:</b></td>
                        <td>
                        <?php
                        // display their address info
                        if (strlen($qryInfo["strCity"])) {
                            print trim($qryInfo["strCity"]) . ", ";
                        }
                        if (strlen($strState)) {
                            print $strState . " ";
                        }
                        print $strCountry;
                        ?>
                        </td>
                    </tr>
                    <?php
                }
            ?>
            <tr>
                <td><b>Joined:</b></td>
                <td><?php print date("n/j/Y", strtotime($qryInfo["dateJoined"])); ?></td>
            </tr>
            <tr>
                <td><b>Level:</b></td>
                <td>
                <?php
                // display their status
                if (strlen($qryInfo["strAccess"])) {
                    print $qryInfo["strAccess"];
                } else {
                    switch ($qryInfo["intAccess"]) {
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
                }
                ?></td>
            </tr>
            <?php
                // see if we know how long they've been playing'
                if (!empty($qryInfo["intPlayYears"])) {
                    ?>
                    <tr>
                        <td><b>Playing Guitar For:</b></td>
                        <td>
                        <?php
                        if (strlen($qryInfo["intPlayYears"])) {
                            print $qryInfo["intPlayYears"] . " year(s)";
                        } else {
                            print "N/A";
                        }
                        ?></td>
                    </tr>
                    <?php
                }
                
                // see if we know how long their experience
                if (!empty($qryInfo["intExperience"])) {
                    ?>
                    <tr>
                        <td><b>Experience:</b></td>
                        <td>
                        <?php
                            switch ($qryInfo["intExperience"]) {
                                case 0: print "N/A"; break;
                                case 1: print "Studio professional"; break;
                                case 2: print "Professional player"; break;
                                case 3: print "Semi-professional"; break;
                                case 4: print "Part-time player"; break;
                                case 5: print "Student"; break;
                                case 6: print "Beginner"; break;
                                default: print "N/A"; break;
                            }
                        ?></td>
                    </tr>
                    <?php
                }
                
                // see if we know how long their situation
                if (!empty($qryInfo["intSituation"])) {
                    ?>
                    <tr>
                        <td><b>Situation:</b></td>
                        <td>
                        <?php
                            switch ($qryInfo["intSituation"]) {
                                case 0: print "N/A"; break;
                                case 1: print "I'm in a band that needs a guitarist."; break;
                                case 2: print "I'm looking to join a band in my area."; break;
                                case 3: print "I'm looking to jam with other players in my area."; break;
                                case 4: print "In a band."; break;
                                case 5: print "N/A"; break;
                                default: print "N/A"; break;
                            }
                        ?>
                        </td>
                    </tr>
                    <?php
                }
                
                // see if we know what song types they play
                if (!empty($qryInfo["intSongTypes"])) {
                    ?>
                    <tr>
                        <td><b>Song Types:</b></td>
                        <td>
                        <?php
                            switch ($qryInfo["intSongTypes"]) {
                                case 0: print "N/A"; break;
                                case 1: print "Original songs that I or my band have written"; break;
                                case 2: print "Cover songs"; break;
                                case 3: print "Mixture of both cover songs and originals"; break;
                                default: print "N/A"; break;
                            }
                        ?>
                        </td>
                    </tr>
                    <?php
                }
            ?>
            <tr>
                <td colspan="2"><br />
                <?php
                    // check and see if they listed an image
                    if (!empty($qryInfo["photo"]) && file_exists("../files/" . $qryInfo["photo"])) {
                        ?>
                        &raquo;&nbsp;<a href="/files/<?php print $qryInfo["photo"]; ?>" rel="lightbox" title="Photo for <?php print $qryInfo["strUsername"]; ?>"><b>View Their Photo</b></a><br />
                        <?php
                    }
                    
                    // see if they're themselve or someone else
                    if ($_SESSION["MemberID"] && $_SESSION["MemberID"] != $_GET["user"]) {
                        ?>
                        &raquo;&nbsp;Send <?php print trim($qryInfo["strUsername"]); ?> a <a href="/forum_ggc/msgs/post/index_bb.php?user=<?php print $_GET["user"]; ?>" title="Send Private Message"><b>Private Message</b></a>.<br />
                        &raquo;&nbsp;Add to your "<a href="buddy.php?id=<?php print $_GET["user"]; ?>&return=/members_ggc/profile.php?user=<?php print $_GET["user"]; ?>" title="Add to Buddy List"><b>buddy list</b></a>".
                        <?php
                    }
                ?>
                </td>
            </tr>
            <?php
                // see if we need to display any comments
                if (strlen($qryInfo["txtComments"])) {
                    ?>
                    <tr>
                        <td colspan="2"><br>
                        <?php print trim(str_replace($crlf, "<br>", $qryInfo["txtComments"]));
                        ?>
                        <br><br></td>
                    </tr>
                    <?php
                }
            ?>
            </table>
            <br clear="all" />
            <!-- start influnces and styles table -->
            <table width="100%" cellspacing="0" cellpadding="1" border="0">
            <tr valign="top">
            <?php
            // see if they chose any gear
            if (strlen($qryInfo["txtGear"])) {
                ?>
                <td width="40%">
                <b>Equipment Used:</b>
                <ul>
                <?php
                // create an array with our influence list
                $arrGear = explode(chr(10), $qryInfo["txtGear"]);
                
                // loop through, and display each
                foreach ($arrGear as $strGear) {
                    if (strlen($strGear)) {
                        ?><li><?php print $strGear; ?></li>
                        <?php
                    }
                }
                ?>
                </ul>
                </td>
                <?php
            }
            
            // see if they entered any influences
            if (strlen($qryInfo["txtInfluences"])) {
                ?>
                <td width="40%"><b>Influences:</b>
                <ul>
                <?php
                // create an array with our influence list
                $arrInfluences = explode(chr(10), $qryInfo["txtInfluences"]);
                
                // loop through, and display each
                foreach ($arrInfluences as $strInfluence) {
                    if (strlen(trim($strInfluence))) {
                        ?><li><?php print $strInfluence; ?></li>
                        <?php
                    }
                }
                ?>
                </ul>
                </td>
                <?php
            }
            
            // see if they chose any styles
            if ($qryStyles->numRows()) {
                ?>
                <td width="20%">
                <b>Musical Styles:</b>
                <ul>
                <?php
                // loop through our saved array, and grab the data from $arrTypes
                while ($style = $qryStyles->fetchRow(DB_FETCHMODE_ASSOC)) {
                    ?>
                    <li><?php print $style["style"]; ?></li>
                    <?php
                }
                ?>
                </ul>
                </td>
                <?php
            }
            ?>
            </tr>
            </table>
            <!-- end influnces and styles table -->
            
            <?php
            // see if they entered a URL
            if (strlen($qryInfo["strURL"])) {
                ?>
                <p>
                Check out <a href="
                <?php
                if (substr($qryInfo["strURL"], 0, 7) != "http://") {
                    print "http://";
                }
                print $qryInfo["strURL"] . "\" target=\"_new\"><b>" . $qryInfo["strURL"] . "</b></a>.";
            }
            
            // see if the person viewing the profile is the actual user
            if ($_GET["user"] == $_SESSION["MemberID"]) {
                ?>
                <p>
                <b>&raquo;</b> <a href="/members_ggc/index.php"><b>Edit This Info</b></a>
                <?php
            }
            ?>
            </td>
        </tr>
        </table>
        <!--- end layout file --->
        
        </td>
    </tr>
    </table>

<?php
    // include our header file
    require("footer.php");
?>
