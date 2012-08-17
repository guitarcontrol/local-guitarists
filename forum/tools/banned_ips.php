<?php
    /*
        banned_ips.php
        
        Here we simply list all of the IPs in the database that have been 
        banned.
    */
    
    // include our needed files
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    require("functions.php");
    
    // make sure they're logged in
    verify_access($_SESSION["AccessLevel"], 90);
    
    // set our IP (if any)
    if (isset($_GET["ip"])) {
        $addy = $_GET["ip"];
    } else {
        $addy = "0.0.0.0";
    }
    
    // get our IP's from the db
    $qryIPs = $dbConn->query("
        select        distinct(strIP) as strIP
        from        banned_ips
        order by    strIP");
    
    // set the # to display per row
    $display = ceil($qryIPs->numRows() / 5);
    
    // get all usernames and IP's that have been banned previously
    $qryUsers = $dbConn->query("
        select        strUsername,
                    strIP
        from        members
        where        intBanned = 1
        order by    strUsername");
    
    // set our total number of items found
    $totalcount = $qryIPs->numRows() + $qryUsers->numRows();
    
    // set our page defaults
    $pageTitle = "Guitar Forums: Mod Tools: Display Banned IP's";
    $pageDescription = "";
    $pageKeywords = "";
    $areaName = "forums";
    
    // include our header file
    require("header.php");
?>

    <br>
    <table width="100%" cellspacing="0" cellpadding="2" border="0">
    <tr valign="top">
        <td align="center">
    
        <!--- begin layout file --->
        <table width="100%" cellspacing="1" cellpadding="1" border="0">
        <tr>
            <td class="tablehead">&nbsp;&raquo;&nbsp;Banned IP's (<?php print $qryIPs->numRows(); ?> found)</td>
        </tr>
        </table>
        
        <table width="100%" cellspacing="0" cellpadding="2" border="0">
        <tr valign="top">
            <td>
            <?php
                // set a row #
                $intRow = 1;
                
                // loop through our results
                while ($qryRow = $qryIPs->fetchRow(DB_FETCHMODE_ASSOC)) {
                    // see if it matches our passed IP
                    if ($qryRow["strIP"] == $addy) {
                        print "<b>" . $qryRow["strIP"] . "</b><br>\n";
                    } else {
                        print $qryRow["strIP"] . "<br>\n";
                    }
                    
                    // see if we need to start a new row
                    if ($intRow == $display) {
                        print "</td><td>\n";
                        $intRow = 1;
                    } else {
                        $intRow++;
                    }
                }
            ?><br>
            </td>
        </tr>
        <tr>
            <td colspan="5"><br></td>
        </tr>
        </table>
        
        <table width="100%" cellspacing="0" cellpadding="2" border="0">
        <tr>
            <td class="tablehead">&nbsp;&raquo;&nbsp;Banned Users (<?php print $qryUsers->numRows(); ?> found)</td>
        </tr>
        </table>
        
        <table width="100%" cellspacing="0" cellpadding="2" border="0">
        <tr valign="top">
            <td colspan="5">
            
            <table width="100%" cellspacing="1" cellpadding="0" border="0">
            <?php
                while ($qryRow = $qryUsers->fetchRow(DB_FETCHMODE_ASSOC)) {
                    ?>
                    <tr>
                        <td width="50%"><b><?php print $qryRow["strUsername"]; ?></b></td>
                        <td width="50%"><?php print $qryRow["strIP"]; ?></td>
                    </tr>
                    <?php
                    // get any other users with an IP like this
                    if (strlen($qryRow["strIP"])) {
                        $arrIP = explode(".", $qryRow["strIP"]);
                    } else {
                        $arrIP = array("0", "0", "0");
                    }
                    
                    $thisIP = $arrIP[0] . "." . $arrIP[1] . "." . $arrIP[2];
                    
                    $qryLikeIP = $dbConn->query("
                        select         strUsername,
                                    strIP
                        from        members
                        where        strIP LIKE '" . $thisIP . "%' and
                                    strUsername != '" . $qryRow["strUsername"] . "'
                        order by    strUsername");
                    
                    if ($qryLikeIP->numRows()) {
                        while ($qrySubRow = $qryLikeIP->fetchRow(DB_FETCHMODE_ASSOC)) {
                            ?>
                            <tr bgcolor="#f6f6f6" valign="top">
                                <td align="right"><?php print $qrySubRow["strUsername"]; ?></td>
                                <td><?php print $qrySubRow["strIP"]; ?></td>
                            </tr>
                            <?php
                        }
                    }
                }
            ?>
            </table>
            </td>
        </tr>
        </table>
        <!--- end layout file --->
        
        </td>
        <?php //require("fastclick.php"); ?>
    </tr>
    </table>

<?php
    // include our header
    require("footer.php");
?>
