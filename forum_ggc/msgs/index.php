<?php
    /*
        index.php
        
        This is the main page users will come to view their private messages 
        from other members, reply to messages, send new messages, or delete 
        exisiting items.
        
    */
    
    // include our needed files
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    require("functions.php");
    
    // make sure they're logged in
    verify_access($_SESSION["AccessLevel"], 1);
    
    // query our data from the db
    $qryTopics = $dbConn->query("
        select      msg_main.*,
                    members.strUsername,
                    SUM(msg_replies.intRead) as totals,
                    COUNT(msg_replies.ID) as replies
        from        (msg_main, members)
                    LEFT JOIN msg_replies ON msg_main.ID = msg_replies.intParent and
                    msg_replies.intRecipient = '" . $_SESSION["MemberID"] . "'
        where       (msg_main.intMemID = '" . $_SESSION["MemberID"] . "' or msg_main.intRecipient = '" . $_SESSION["MemberID"] . "') and
                    msg_main.intMemID = members.ID
        group by    msg_main.dateLastPost desc");
    
    // update our page variables
    $pageTitle = "Guitar Discussion: Your Private Messages (" . $qryTopics->numRows() . ")";
    $pageDescription = "";
    $pageKeywords = "";
    $areaName = "forums";
    $intDisplay = 20;
    
    // setup our previous/next links
    if (isset($_GET["page"])) {
        $startRow = $_GET["page"];
        $endRow = $startRow + ($intDisplay - 1);
    } else {
        $startRow = 0;
        $endRow = $intDisplay - 1;
    }
    
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
        <?php if (empty($_SESSION["GGCIFrame"])) { ?>
        <table width="720" cellspacing="1" cellpadding="3" border="0">
        <tr>
            <td class="tablehead">&nbsp;&raquo;&nbsp;<a href="/index.php"><b>Home</b></a>&nbsp;&raquo;&nbsp;<a href="/forum_ggc/index.php"><b>Guitar Discussion</b></a>&nbsp;&raquo;&nbsp;Your Private Messages</td>
        </tr>
        </table>
        <?php } ?>
        
        <table width="720" cellspacing="0" cellpadding="2" border="0">
        <tr>
            <td><br /></td>
        </tr>
        <?php
            // see if we need to display any messages
            if ($qryTopics->numRows()) {
                ?>
                <form name="myMsgs" action="delete.php" method="post" onSubmit="return valBoxes()">
                <tr align="center">
                    <td colspan="2" class="innertitle">Title</td>
                    <td class="innertitle">From/To</td>
                    <td class="innertitle">Replies</td>
                    <td class="innertitle">Views</td>
                    <td class="innertitle">Last Update</td>
                </tr>
                <?php
                // set a row counter to help us loop
                $intRow = 0;
                $loopCount = 1;
                
                // loop through our results
                while ($qryRow = $qryTopics->fetchRow(DB_FETCHMODE_ASSOC)) {
                    // see if we need to display these items
                    if ($intRow >= $startRow && $intRow <= $endRow) {
                        // build our SQL text
                        $sqlText = "select    ID,
                                            strUsername
                                    from    members
                                    where    ";
                        
                        if ($qryRow["intRecipient"] != $_SESSION["MemberID"]) {
                            $sqlText .= "ID = " . $qryRow["intRecipient"];
                        } else {
                            $sqlText .= "ID = " . $qryRow["intMemID"];
                        }
                        
                        // get this users username (whether in to or from)
                        $qryUser = $dbConn->getRow($sqlText, DB_FETCHMODE_ASSOC);
                        
                        // see if we found any records
                        if (!count($qryUser)) {
                            $qryUser["ID"] = 0;
                            $qryUser["strUsername"] = "---";
                        }
                        
                        // set the background color
                        $bgcolor = "#ffffff";
                        if ($loopCount % 2 == 0) {
                            $bgcolor = "#f6f6f6";
                        }
                        ?>
                        <tr valign="middle" bgcolor="<?php print $bgcolor; ?>">
                            <td valign="top" width="12"><input type="Checkbox" name="delID[]" value="<?php print $qryRow["ID"]; ?>"></td>
                            <td valign="top" width="338"><b><a href="view_bb.php?id=<?php print $qryRow["ID"]; ?>">
                            <?php
                            // see if there's a title or not
                            if (strlen($qryRow["strTitle"])) {
                                print $qryRow["strTitle"];
                            } else {
                                print "No Title Provided";
                            }
                            ?></a></b>
                            <?php
                            //print "<!-- " . $qryRow["intRecipient"] . " - " . $_SESSION["MemberID"] . " - " . $qryRow["intRead"] . " - " .$qryRow["totals"] . " -->\n";
                            // see who sent or received it
                            if ($qryRow["intRecipient"] == $_SESSION["MemberID"] && !$qryRow["intRead"]) {
                                print " <b style=\"color: #cc3333; font-style : italic;\">NEW!</b>";
                            } else if ($qryRow["intRecipient"] != $_SESSION["MemberID"] && $qryRow["replies"] && !$qryRow["totals"]) {
                                print " <b style=\"color: #cc3333; font-style : italic;\">NEW!</b>";
                            }
                            ?></td>
                            <td width="65" align="center" class="smalltxt"><a href="/members_ggc/profile.php?user=<?php print $qryUser["ID"]; ?>"><b><?php print $qryUser["strUsername"]; ?></b></a></td>
                            <td width="30" align="center" class="smalltxt"><?php print $qryRow["intReplies"]; ?></td>
                            <td width="40" align="center" class="smalltxt"><?php print $qryRow["intViews"]; ?></td>
                            <td width="125" align="center" class="smalltxt"><?php print date("M\. j \@ g\:i A", strtotime($qryRow["dateLastPost"])); ?></td>
                        </tr>
                        <?php
                    }
                    
                    // set a row counter to help us loop
                    $intRow++;
                    $loopCount++;
                }
                
                // see if we need to paginate
                if ($qryTopics->numRows() > $intDisplay) {
                    // call our pages function
                    $strURL = "/forum_ggc/msgs/index.php?id=" . $_SESSION["MemberID"] . "&";
                    $pageResults = f_prevnext($qryTopics->numRows(), $intDisplay, $startRow, '6', $strURL);
                }
                
                print "
                </form>";
            } else {
                ?>
                <tr>
                    <td colspan="6">
                    You have <b>0</b> private message either to or from other members at this time.
                    </td>
                </tr>
                <?php
            }
        ?>
        <form>
        <tr>
            <td colspan="6" class="smalltxt"><br>
            <input type="Button" value="Post A Message &raquo;" onClick="location.href='post/index.php'" class="button">
            <?php
                if ($qryTopics->numRows()) {
                    print "
                    <input type=\"Button\" value=\"Delete Checked &raquo;\" onClick=\"valBoxes()\" class=\"button\">";
                }
            ?>
            </td>
        </tr>
        </form>
        </table>
        <!--- end layout file --->
        
        </td>
    </tr>
    </table>
    
    <script language="JavaScript">
    function valBoxes() {
        // make sure something was checked
        var intCount = 0;
        
        for (i = 0; i < document.myMsgs.elements.length; i++) {
            if (document.myMsgs.elements[i].type == "checkbox" && document.myMsgs.elements[i].checked == true) {
                intCount++;
            }
        }
        
        if (intCount == 0) {
            alert("Please choose one message to delete first.");
        } else {
            document.myMsgs.submit();
        }
    }
    </script>

<?php
    // include our footer
    if (empty($_SESSION["GGCIFrame"])) {
        require("footer.php");
    }
?>
