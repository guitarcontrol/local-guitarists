<?php
    
    /*
        results.php
        
        Here we'll query the db for all members matching the data provided, and 
        then return the listings to the member.
    */
    
    // include our main app file
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    require("functions.php");
    
    // make sure they're logged in
    verify_access($_SESSION["AccessLevel"], 1);
    
    // make sure they passed something to search by
    if (!isset($_GET["state"]) || !isset($_GET["country"]) || !isset($_GET["city"])) {
        print "
        <script language=\"JavaScript\">
        alert(\"Please choose something to search by.\");
        location.replace(\"/members/index.php\");
        </script>";
        exit();
    }
    
    // create a list from our styles
    if (is_array($_GET["city"])) {
        $txtCities = "'" . implode("','", $_GET["city"]) . "'";
    } else {
        $txtCities = $_GET["city"];
    }
    
    // build our dynamic SQL statement
    $sqlText = "
        select        members.ID,
                    members.strUsername,
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
        where        members.intBanned = 0 and
                    members.ID = about.intMemID and
                    about.strCity IN ( " . $txtCities . " )";
    
    // if they entered a username, try and match it
    if (strlen($_GET["username"])) {
        $sqlText .= " and members.strUsername LIKE '%" . trim(mysql_escape_string($_GET["username"])) . "%'";
    }
    
    // set a row counter
    $rowCount = 1;
    
    // add our style search.  loop through our list.
    if (strlen($_GET["styles"])) {
        $sqlText .= " and (";
        
        foreach ($_GET["styles"] as $city) {
            $sqlText .= "about.txtStyles LIKE '%," . $city . ",%'";
            
            // see if we need to add an 'or' statement
            if ($rowCount < count($_GET["styles"])) {
                $sqlText .= " or ";
            }
            
            $rowCount++;
        }
        
        $sqlText .= " )";
    }
    
    $sqlText .= " and about.intCountry = " . $dbConn->quote($_GET["country"]) . " ";
    
    // see if they chose a valid state
    if ($_GET["state"]) {
        $sqlText .= " and about.intState = " . $dbConn->quote($_GET["state"]) . " ";
    }
    
    // see if they chose a certain experience level
    if ($_GET["exp"]) {
        $sqlText .= " and about.intExperience = " . $dbConn->quote($_GET["exp"]) . " ";
    }
    
    // see if they chose a certain song creation type
    if ($_GET["songs"]) {
        $sqlText .= " and about.intSongTypes = " . $dbConn->quote($_GET["songs"]) . " ";
    }
    
    // see if they chose a certain situation
    if ($_GET["situation"]) {
        $sqlText .= " and about.intSituation = " . $dbConn->quote($_GET["situation"]) . " ";
    }
    
    // continue
    $sqlText .= " and
                    about.intCountry = countries.ID
        order by    members.strUsername,
                    about.strCity";
    
    // process our results
    $qryMemList = $dbConn->query($sqlText);
    
    // query our passed state
    $qryState = $dbConn->query("
        select  strAbbr
        from    states
        where   ID = " . $dbConn->quote($_GET["state"]) . "
        limit 1",
        DB_FETCHMODE_ASSOC);
    
    // create our page variables
    $pageTitle = "Members Area: Search Results (" . number_format($qryMemList->numRows()) . ")";
    $areaName = "players";
    $display = 35;
    
    // setup our previous/next links
    if (isset($_GET["page"])) {
        $startRow = $_GET["page"];
        $endRow = $startRow + ($display - 1);
    } else {
        $startRow = 0;
        $endRow = $display - 1;
    }
    
    // include our header file
    require("header.php");
?>
    
    <br>
    <table width="100%" cellspacing="0" cellpadding="2" border="0">
    <tr valign="top">
        <?php if ($adPlace == 1) { require("fastclick.php"); } ?>
        <td align="right">
        
        <table width="100%" cellspacing="0" cellpadding="1" border="0">
        <tr>
            <td colspan="5" class="medtxt"><a href="/index.php"><b>Home</b></a>&nbsp;&raquo;&nbsp;<a href="index.php"><b>Members Area</b></a>&nbsp;&raquo;&nbsp;Member Search Results</td>
        </tr>
        <tr>
              <td colspan="5"><br>
            Your search matched in <b><?php print number_format($qryMemList->numRows()); ?></b> member(s) in the community.  
            Below are the results:<br><br>
            </td>
        </tr>
        <?php
            // continue, based on the number of records found
            if ($qryMemList->numRows()) {
                // create a row counter
                $rowCount = 0;
                ?>
                <tr bgcolor="#08087C">
                    <td width="25%"><b style="color: #ffffff;">&nbsp;Name</b></td>
                    <td width="30%"><b style="color: #ffffff;">Location</b></td>
                    <td width="15%" align="center"><b style="color: #ffffff;">Years</b></td>
                    <td width="15%"><b style="color: #ffffff;">Status</b></td>
                    <td width="15%"><b style="color: #ffffff;">Last Login</b></td>
                </tr>
                <?php
                // loop through our results
                while ($qryRow = $qryMemList->fetchRow(DB_FETCHMODE_ASSOC)) {
                    // see if it's in our page of results
                    if ($rowCount >= $startRow && $rowCount <= $endRow) {
                        // set our background color
                        $bgcolor = "#ffffff";
                        if ($rowCount % 2 == 0) {
                            $bgcolor = "#f6f6f6";
                        }
                        ?>
                        <tr bgcolor="<?php print $bgcolor; ?>">
                            <td><b>&raquo;&nbsp;<a href="profile.php?user=<?php print $qryRow["ID"]; ?>" target="_player"><?php print $qryRow["strUsername"]; ?></a></b></td>
                            <td><?php print $qryRow["strCity"]; ?>, <?php print $qryRow["strState"]; ?> <?php print $qryRow["strCountry"]; ?></td>
                            <td align="center"><?php print $qryRow["intPlayYears"]; ?></td>
                            <td>
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
                                    case 99: print "Administrator"; break;
                                    default: print "Member"; break;
                                }
                            }
                            ?></td>
                            <td><?php print date("M\. j, Y", strtotime($qryRow["dateLVisit"])); ?></td>
                        </tr>
                        <?php
                    }
                    
                    // update our row counter
                    $rowCount++;
                }
                
                // see if we need to display our page through links
                if ($qryMemList->numRows() > $display) {
                    // call our pages function
                    $strURL = "results.php?country=" . $_GET["country"] . "&state=" . $_GET["state"] . "&city=" . $txtCities . "&exp=" . $_GET["exp"] . "&songs=" . $_GET["songs"] . "&situation=" . $_GET["situation"] . "&";
                    f_prevnext($qryMemList->numRows(), $display, $startRow, '5', $strURL);
                }
                ?>
                <tr>
                    <td colspan="5"><br>
                    <b>&raquo;&nbsp;<a href="search.php?state=<?php print $qryState["strAbbr"]; ?>">Search Again</a></b>
                    </td>
                </tr>
                <?php
            } else {
                ?>
                <tr>
                    <td colspan="5">
                    We did not find any active members matching your search parameters.  
                    Feel free to <a href="search.php?state=<?php print $qryState["strAbbr"]; ?>"><b>broaden your requirements</b></a>, 
                    and try again.
                    <p>
                    Thanks.
                    </td>
                </tr>
                <?php
            }
        ?>
        </table>
        
        </td>
        <?php if ($adPlace == 2) { require("fastclick.php"); } ?>
    </tr>
    </table>

<?php
    // include our footer file
    require("footer.php");
?>
