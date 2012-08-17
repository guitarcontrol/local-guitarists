<?php
    /*
        panel.php
        
        This is the main screen mods will use to keep control of users on the 
        site.  From here they can (un)ban members, freeze member accounts, 
        edit signatures, and add/remove warnings.
    */
    
    // include our needed files
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    require("functions.php");
    
    // make sure they're logged in
    verify_access($_SESSION["AccessLevel"], 90);
    
    // get our IP's from the db
    $qryActiveUsers = $dbConn->query("
        select        *
        from        sessions
        where       UserID > 0
        order by    SessTime desc");
    
    // query the info for this user
    $qryInfo = $dbConn->getRow("
        select  ID,
                strUsername,
                intWarnings,
                intAccess,
                strAccess,
                intFrozen,
                txtSignature,
                intBanned
        from    members
        where   ID = " . $dbConn->quote($_GET["memid"]),
        DB_FETCHMODE_ASSOC);
    
    // swap out our smilies
    $txtSig = smilies($qryInfo["txtSignature"], '1');
    
    // set the data here for our signature
    $txtSig = str_replace("<li>", "{~}", $txtSig);
    $txtSig = str_replace("<", "{", $txtSig);
    $txtSig = str_replace(">", "}", $txtSig);
    $txtSig = str_replace("font color=", "color=", $txtSig);
    $txtSig = str_replace("/font", "/color", $txtSig);
    $txtSig = str_replace("a target=\"_new\" href=", "link=", $txtSig);
    
    // set our page defaults
    $pageTitle = "Guitar Forums: Mod Tools: Member Control Panel";
    $pageDescription = "";
    $pageKeywords = "";
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
        <td>
    
        <!--- begin layout file --->
        <?php if (empty($_SESSION["GGCIFrame"])) { ?>
        <table width="100%" cellspacing="0" cellpadding="2" border="0">
        <tr>
            <td class="tablehead">&nbsp;&raquo;&nbsp;Active Members (<?php print $qryActiveUsers->numRows(); ?> found)</td>
        </tr>
        </table>
        <?php } ?>
        
        <table cellspacing="0" cellpadding="2" border="0">
        <form name="myPeeps" action="sessions_update.php" method="post" onSubmit="return valSessions()">
        <input type="Hidden" name="memID" value="<?php print $_GET["memid"]; ?>" />
        <?php
            // set a row #
            $intRow = 1;
            
            // loop through our results
            while ($qryRow = $qryActiveUsers->fetchRow(DB_FETCHMODE_ASSOC)) {
                // decipher how long the session has been active
                $totalTime = ceil((time() - $qryRow["SessStart"]) / 60);
                $lastActive = ceil((time() - $qryRow["SessTime"]) / 60);
                ?>
                <tr>
                    <td><input type="Checkbox" name="UserID[]" value="<?php print $qryRow["UserID"]; ?>" /></td>
                    <td><a href="list.php?memid=<?php print $qryRow["UserID"]; ?>"><b><?php print $qryRow["Username"]; ?></b></a></td>
                    <td><a href="banned_ips.php?ip=<?php print $qryRow["IPAddress"]; ?>"><b><?php print $qryRow["IPAddress"]; ?></b></a></td>
                    <td><?php print date("D, M j \@ g:i a", $qryRow["SessStart"]); ?></td>
                    <td>Last action: <?php print $lastActive; ?> min(s)</td>
                    <td>Logged in for: <?php print $totalTime; ?> min(s)</td>
                </tr>
                <?php
            }
        ?>
        <tr>
            <td colspan="6">
            <!--
            <input type="Submit" name="submit" value="Ban User(s)" class="smbutton" />
            <input type="Submit" name="submit" value="Freeze User(s)" class="smbutton" />
            <input type="Submit" name="submit" value="Close Sessions" class="smbutton" />
            -->
            </td>
        </tr>
        </form>
        <?php
            // display certain sections
            if (isset($_GET["display"]) && $_GET["display"] == "status") {
                ?>
                <tr>
                    <td colspan="6"><br></td>
                </tr>
                </table>
                
                <table width="100%" cellspacing="0" cellpadding="2" border="0">
                <tr>
                    <td class="tablehead">&nbsp;&raquo;&nbsp;Member Status</td>
                </tr>
                </table>
                
                <table width="100%" cellspacing="0" cellpadding="2" border="0">
                <form method="post" action="status.php">
                <input type="Hidden" name="ID" value="<?php print $qryInfo["ID"]; ?>">
                <tr>
                    <td colspan="6">
                    Update <?php print $qryInfo["strUsername"]; ?>'s status :
                    
                    <table width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td><input type="Radio" name="intStatus" value="0"<?php if (!$qryInfo["intBanned"] && !$qryInfo["intFrozen"]) { print " checked"; } ?>> Normal User</td>
                        <td><input type="Radio" name="intStatus" value="1"<?php if ($qryInfo["intFrozen"]) { print " checked"; } ?>> Frozen</td>
                        <td><input type="Radio" name="intStatus" value="2"<?php if ($qryInfo["intBanned"]) { print " checked"; } ?>> Banned</td>
                        <td><input type="Submit" value="Update &raquo;" class="smbutton"></td>
                    </tr>
                    </table><br>
                    <b>Comment:</b><br>
                    <textarea name="reason" cols="70" rows="6" class="input"></textarea>
                    </td>
                </tr>
                </form>
                <?php
            } else if (isset($_GET["display"]) && $_GET["display"] == "warnings") {
                ?>
                <tr>
                    <td colspan="6"><br></td>
                </tr>
                </table>
                
                <table width="100%" cellspacing="0" cellpadding="2" border="0">
                <tr>
                    <td class="tablehead">&nbsp;&raquo;&nbsp;Warnings</td>
                </tr>
                </table>
                
                <table width="100%" cellspacing="0" cellpadding="2" border="0">
                <form method="post" action="warn.php">
                <input type="Hidden" name="ID" value="<?php print $qryInfo["ID"]; ?>">
                <input type="Hidden" name="intWarnings" value="<?php print $qryInfo["intWarnings"]; ?>">
                <tr>
                    <td colspan="6">
                    Currently <?php print $qryInfo["strUsername"]; ?> has <?php print $qryInfo["intWarnings"]; ?> warning(s).  Update
                    here accordingly.
                    <p>
                    <b>Reason:</b><br>
                    <textarea name="reason" cols="70" rows="6" class="input"></textarea><br>
                    <input type="Submit" value="Add Warning &raquo;" class="smbutton">
                    </td>
                </tr>
                <tr>
                    <td colspan="6" class="smalltxt">
                    <b>NOTE:</b> 3 warnings = frozen account and/or ban.
                    </td>
                </tr>
                </form>
                <?php
            } else if (isset($_GET["display"]) && $_GET["display"] == "sig") {
                ?>
                <tr>
                    <td colspan="6"><br></td>
                </tr>
                </table>
                
                <table width="100%" cellspacing="0" cellpadding="2" border="0">
                <tr>
                    <td class="tablehead">&nbsp;&raquo;&nbsp;Reset Members Signature</td>
                </tr>
                </table>
                
                <table width="100%" cellspacing="0" cellpadding="2" border="0">
                <form method="post" action="signature.php">
                <input type="Hidden" name="ID" value="<?php print $qryInfo["ID"]; ?>">
                <tr>
                    <td colspan="6">
                    If a member has posted inappropriate material in their signature, or they simply need help in 
                    creating links (or other HTML objects), than change it below:
                    <p>
                    <b>Signature:</b><br>
                    <textarea name="txtSignature" cols="70" rows="6" class="input"><?php print trim($txtSig); ?></textarea>
                    <p>
                    <b>Reason For Change:</b><br>
                    <textarea name="reason" cols="70" rows="3" class="input"></textarea><br>
                    <div class="smalltxt">If a reason is entered, it will be PM'd to the user.</div>
                    <p>
                    <input type="Submit" value="Edit Signature &raquo;" class="smbutton">
                    </td>
                </tr>
                </form>
                <?php
            }
        ?>
        <tr>
            <td colspan="6"><br></td>
        </tr>
        </table>
        
        <table width="100%" cellspacing="0" cellpadding="2" border="0">
        <tr>
            <td class="tablehead">&nbsp;&raquo;&nbsp;Administrative Options</td>
        </tr>
        <tr>
            <td colspan="6">
            &raquo;&nbsp;View the <a href="list.php?memid=<?php print $qryInfo["ID"]; ?>"><b>last 10 days of threads</b></a> started by this user.<br>
            &raquo;&nbsp;View the <a href="newusers.php?memid=<?php print $qryInfo["ID"]; ?>"><b>newest posters</b></a> to the site.<br>
            <!--&raquo;&nbsp;View all members that have been <a href="bans.php?memid=<?php print $qryInfo["ID"]; ?>"><b>banned or frozen</b></a> from the site.<br>
            &raquo;&nbsp;Modify the user's <a href="panel.php?memid=<?php print $qryInfo["ID"]; ?>&display=status"><b>status</b></a>.<br>
            &raquo;&nbsp;Administer <a href="panel.php?memid=<?php print $qryInfo["ID"]; ?>&display=warnings"><b>warnings</b></a>.<br>
            &raquo;&nbsp;Edit the member's <a href="panel.php?memid=<?php print $qryInfo["ID"]; ?>&display=sig"><b>signature</b></a>.<br>
            &raquo;&nbsp;Reset the members <a href="post_count.php?memid=<?php print $qryInfo["ID"]; ?>"><b>post count</b></a>.-->
            </td>
        </tr>
        </table>
        <!--- end layout file --->
        
        </td>
    </tr>
    </table>
    
    <script language="JavaScript">
    function valSessions() {
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
            alert("Please choose atleast 1 user to update.");
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
