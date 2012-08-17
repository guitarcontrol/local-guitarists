<?php
    /*
        view_bb.php
        
        Allows a member to view the posts and replies to all messages posted 
        to and from him/her.
        
    */
    
    // include our needed files
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    require("functions.php");
    require("HTML/BBCodeParser.php");
    
    // parse the BB Code and turn it into HTML
    $parser = new HTML_BBCodeParser(parse_ini_file("../BBCodeParser.ini"));
    
    // make sure they're logged in
    verify_access($_SESSION["AccessLevel"], 1);
    
    // make sure an ID was passed
    if (!isset($_GET["id"])) {
        print"
        <script language=\"JavaScript\">
        alert(\"Please choose a message to view.\");
        location.replace(\"index.php\");
        </script>";
        exit();
    }
    
    // query the main message
    $qryMessage = $dbConn->getRow("
        select      msg_main.ID as msgID,
                    msg_main.strTitle,
                    msg_main.txtContent,
                    msg_main.intMemID,
                    msg_main.intViews,
                    msg_main.dateAdded,
                    msg_main.intRecipient,
                    members.ID,
                    members.intPosts,
                    members.strUsername, 
                    members.strAIM,
                    members.strICQ,
                    members.strMSN,
                    members.strYahoo,
                    members.intAllowChat,
                    members.strPublicEmail,
                    members.intAccess,
                    members.strAccess,
                    members.dateJoined,
                    members.txtSignature,
                    members.intBanned,
                    members.strIP
        from        msg_main,
                    members
        where       msg_main.ID = " . $dbConn->quote($_GET["id"]) . " and
                    msg_main.intMemID = members.ID",
        DB_FETCHMODE_ASSOC);
    
    // make sure we have priveleges here to view this message
    if ($qryMessage["intMemID"] != $_SESSION["MemberID"] && $qryMessage["intRecipient"] != $_SESSION["MemberID"]) {
        print "
        <script language=\"JavaScript\">
        alert(\"You do not have priveleges to view this post.\");
        location.replace(\"index.php\");
        </script>";
        exit();
    }
    
    // get any and all replies from the db
    $qryReplies = $dbConn->query("
        select      msg_replies.ID as replyID,
                    msg_replies.strTitle,
                    msg_replies.txtContent,
                    msg_replies.intMemID,
                    msg_replies.dateAdded,
                    msg_replies.intRecipient,
                    msg_replies.dateAdded,
                    members.ID,
                    members.intPosts,
                    members.strUsername, 
                    members.strAIM,
                    members.strICQ,
                    members.strMSN,
                    members.strYahoo,
                    members.intAllowChat,
                    members.strPublicEmail,
                    members.intAccess,
                    members.strAccess,
                    members.dateJoined,
                    members.txtSignature,
                    members.intBanned,
                    members.strIP
        from        msg_replies,
                    members
        where        msg_replies.intParent = " . $qryMessage["msgID"] . " and
                    msg_replies.intMemID = members.ID
        order by    msg_replies.dateAdded");
    
    // update the view counter
    $intViews = $qryMessage["intViews"] + 1;
    
    // update the database
    $qryViewCount = $dbConn->query("
        update  msg_main
        set     intViews = " . $intViews . "
        where   ID = " . $qryMessage["msgID"]);
    
    // if this is my post, mark it as read
    if ($qryMessage["intRecipient"] == $_SESSION["MemberID"]) {
        $qryViewCount = $dbConn->query("
            update  msg_main
            set     intRead = 1
            where   ID = '" . $qryMessage["msgID"] . "'");
    }
    
    // mark the item as read by you
    $qryUpdate = $dbConn->query("
        update    msg_replies
        set       intRead = 1
        where     intParent = '" . $qryMessage["msgID"] . "' and
                  intRecipient = '" . $_SESSION["MemberID"] . "'");
    
    // set our variables
    $pageTitle = "Guitar Discussion: Messages: " . $qryMessage["strTitle"] . " (" . $qryReplies->numRows() . ")";
    $pageDescription = "";
    $pageKeywords = "";
    $areaName = "forums";
    $crlf = chr(10);
    
    // set our max number to display per page
    $intDisplayNum = 20;
    
    // setup our previous/next links
    if (isset($_GET["page"])) {
        $startRow = $_GET["page"];
        $endRow = $startRow + ($intDisplayNum - 1);
    } else {
        $startRow = 0;
        $endRow = $intDisplayNum - 1;
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
        <td>
        
        <!--- begin layout file --->
        <?php if (empty($_SESSION["GGCIFrame"])) { ?>
        <table width="100%" cellspacing="1" cellpadding="3" border="0">
        <tr>
            <td class="tablehead">&nbsp;&raquo;&nbsp;<a href="/index.php"><b>Home</b></a>&nbsp;&raquo;&nbsp;<a href="/forum_ggc/index.php"><b>Guitar Discussion</b></a>&nbsp;&raquo;&nbsp;<a href="/forum_ggc/msgs/index.php"><b>Private Messages</b></a>&nbsp;&raquo;&nbsp;<?php print $qryMessage["strTitle"]; ?></td>
        </tr>
        </table>
        <?php } ?>
        
        <table width="100%" cellspacing="0" cellpadding="2" border="0">
        <form name="myChoiceTop">
        <tr>
            <td colspan="2" align="right" class="smalltxt">
            <b>Options:</b>
            <select name="option" class="dropdown" onChange="location.href=this.value">
                <option value="/forum_ggc/msgs/reply/index_bb.php?msg=<?php print $qryMessage["msgID"]; ?>">&nbsp;&raquo;&nbsp;Post A Reply</option>
                <option value="/forum_ggc/msgs/post/index_bb.php">&nbsp;&raquo;&nbsp;Post New Message</option>
                <option value="/forum_ggc/msgs/delete.php?id=<?php print $_GET["id"]; ?>">&nbsp;&raquo;&nbsp;Delete Message &amp; Replies</option>
                <option value="/forum_ggc/msgs/index.php">&nbsp;&raquo;&nbsp;View Message List</option>
            </select>
            <input type="Button" value="Go!" class="smbutton" onClick="location.href=document.myChoiceTop.option.options[document.myChoiceTop.option.selectedIndex].value">
            </td>
        </tr>
        </form>
        <!--- output our main post --->
        <?php
            ?>
            <tr>
                <td class="innerhead" colspan="2"><b style="color: #08087C"><?php print $qryMessage["strTitle"]; ?></b></td>
            </tr>
            <tr valign="top">
                <td bgcolor="#f6f6f6" width="130" class="smalltxt">
                <a href="/members/profile.php?user=<?php print $qryMessage["intMemID"]; ?>"><b class="medtxt"><?php print $qryMessage["strUsername"]; ?></b></a><br>
                <b>IP:</b>
                <?php
                // see if they're an admin or not
                if ($_SESSION["AccessLevel"] >= 90) {
                    print $qryMessage["strIP"];
                } else {
                    print "Logged";
                }
                ?><br>
                <b>Status:</b> 
                <?php
                // display their status
                if ($qryMessage["intAccess"] >= 20 && strlen($qryMessage["strAccess"])) {
                    print $qryMessage["strAccess"];
                } else {
                    switch ($qryMessage["intAccess"]) {
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
                        case 95: print "G-Net Editor"; break;
                        case 99: print "Administrator"; break;
                        default: print "Member"; break;
                    }
                }
                ?><br>
                <b>Posts:</b> <?php print number_format($qryMessage["intPosts"]); ?><br>
                <b>Join Date:</b> <?php print date("n/d/Y", strtotime($qryMessage["dateJoined"])); ?><br>
                <?php
                // see if they chose to allow IM's
                if ($qryMessage["intAllowChat"] && $_SESSION["MemberID"]) {
                    // check their IM options
                    if ($qryMessage["intAllowChat"] == 1 || ($qryMessage["intAllowChat"] == 2 && $_SESSION["MemberID"]) || ($qryMessage["intAllowChat"] == 3 && $_SESSION["AccessLevel"] >= 90)) {
                        // check AIM
                        if (strlen($qryMessage["strAIM"])) {
                            print "<a href=\"aim:goim?screenname=" . trim($qryMessage["strAIM"]) . "&message=G-Net+member+chat\"><img src=\"/forum_ggc/images/aim.gif\" width=\"26\" height=\"20\" alt=\"Send AIM\" border=\"0\"></a>";
                        }
                        
                        // check ICQ
                        if (strlen($qryMessage["strICQ"])) {
                            print "<a href=\"http://wwp.icq.com/scripts/search.dll?to=" . trim($qryMessage["strICQ"]) . "\" target=\"_new\"><img src=\"/forum_ggc/images/icq.gif\" width=\"18\" height=\"20\" alt=\"Send ICQ\" border=\"0\"></a>";
                        }
                        
                        // check MSN Messenger
                        if (strlen($qryMessage["strMSN"])) {
                            print "<img src=\"/forum_ggc/images/msn.gif\" width=\"21\" height=\"20\" alt=\"" . trim($qryMessage["strMSN"]) . "\" border=\"0\">";
                        }
                        
                        // check Yahoo! Messenger
                        if (strlen($qryMessage["strYahoo"])) {
                            print "<a href=\"http://edit.yahoo.com/config/send_webmesg?.target=" . trim($qryMessage["strYahoo"]) . "&.src=pg\" target=\"_new\"><img src=\"/forum_ggc/images/yahoo.gif\" width=\"24\" height=\"20\" alt=\"Send Yahoo! Message\" border=\"0\"></a>";
                        }
                    }
                    print "<br>";
                }
                
                // display our editing options
                if ($_SESSION["MemberID"] == $qryMessage["intMemID"]) {
                    print "
                    <p>
                    &raquo; <a href=\"edit/post_bb.php?id=" . $qryMessage["msgID"] . "\"><b>Edit Topic</b></a><br>";
                } else {
                    ?>
                    &raquo; <a href="/members/buddy.php?id=<?php print $qryMessage["intMemID"]; ?>&return=<?php print $strLoginURL; ?>"><b>Add to Buddy List</b></a><br>
                    <?php
                }
                ?>
                &raquo; <a href="delete.php?id=<?php print $qryMessage["msgID"]; ?>"><b>Delete Message</b></a><br>
                </td>
                <td>
                <?php
                // parse our text for BBCode
                $msgText = $parser->qParse(smilies2(str_replace($crlf, "<br>" . chr(10), $qryMessage["txtContent"]), 0));
                
                // display the normal stuff
                print $msgText . "
                <br>";
                
                // display their signature
                if (strlen($qryMessage["txtSignature"])) {
                    print "
                    <br>
                    " . $parser->qParse(smilies2(str_replace($crlf, "<br>" . chr(10), $qryMessage["txtSignature"]), 0)) . "
                    <br>";
                }
                ?>
                <br>
                <table width="100%" cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <td class="smalltxt"><b>Post Date:</b> <?php print date("n/j/Y \@ g:i a", strtotime($qryMessage["dateAdded"])); ?></td>
                    <td class="smalltxt" align="right">
                    &raquo; <a href="reply/index_bb.php?msg=<?php print $qryMessage["msgID"]; ?>&quote=1"><b>Reply With Quote</b></a><br>
                    </td>
                </tr>
                </table>
                </td>
            </tr>
            <tr>
                <td><img src="/images/spacer.gif" width="130" height="1" alt="" border="0"></td>
                <td><br></td>
            </tr>
            <?php
        ?>
        <!--- end our main post --->
            
        <!--- output our replies --->
        <?php
            // loop through our replies
            while ($qryRow = $qryReplies->fetchRow(DB_FETCHMODE_ASSOC)) {
                ?>
                <tr>
                    <td class="innerhead" colspan="2"><b>
                    <a name="post<?php print $qryRow["replyID"]; ?>"></a>
                    <?php
                    // see if the supplied a reply title
                    if (!strlen($qryRow["strTitle"])) {
                        print "&nbsp;";
                    } else {
                        print $qryRow["strTitle"];
                    }
                    ?></b></td>
                </tr>
                <tr valign="top">
                    <td bgcolor="#f6f6f6" class="smalltxt">
                    <a href="/members/profile.php?user=<?php print $qryRow["intMemID"]; ?>"><b class="medtxt"><?php print $qryRow["strUsername"]; ?></b></a><br>
                    <b>IP:</b> 
                    <?php
                    if ($_SESSION["AccessLevel"] >= 90) {
                        print $qryRow["strIP"];
                    } else {
                        print "Logged";
                    }
                    ?><br>
                    <b>Status:</b> 
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
                            case 95: print "G-Net Editor"; break;
                            case 99: print "Administrator"; break;
                            default: print "Member"; break;
                        }
                    }
                    ?><br>
                    <b>Posts:</b> <?php print number_format($qryRow["intPosts"]); ?><br>
                    <?php
                    // see if they chose to allow IM's
                    if ($qryRow["intAllowChat"] && $_SESSION["MemberID"]) {
                        // check their IM options
                        if ($qryRow["intAllowChat"] == 1 || ($qryRow["intAllowChat"] == 2 && $_SESSION["MemberID"]) || ($qryRow["intAllowChat"] == 3 && $_SESSION["AccessLevel"] >= 90)) {
                            // check AIM
                            if (strlen($qryRow["strAIM"])) {
                                print "<a href=\"aim:goim?screenname=" . trim($qryRow["strAIM"]) . "&message=G-Net+member+chat\"><img src=\"/forum_ggc/images/aim.gif\" width=\"26\" height=\"20\" alt=\"Send AIM\" border=\"0\"></a>";
                            }
                            
                            // check ICQ
                            if (strlen($qryRow["strICQ"])) {
                                print "<a href=\"http://wwp.icq.com/scripts/search.dll?to=" . trim($qryRow["strICQ"]) . "\" target=\"_new\"><img src=\"/forum_ggc/images/icq.gif\" width=\"18\" height=\"20\" alt=\"Send ICQ\" border=\"0\"></a>";
                            }
                            
                            // check MSN Messenger
                            if (strlen($qryRow["strMSN"])) {
                                print "<img src=\"/forum_ggc/images/msn.gif\" width=\"21\" height=\"20\" alt=\"" . trim($qryRow["strMSN"]) . "\" border=\"0\">";
                            }
                            
                            // check Yahoo! Messenger
                            if (strlen($qryRow["strYahoo"])) {
                                print "<a href=\"http://edit.yahoo.com/config/send_webmesg?.target=" . trim($qryRow["strYahoo"]) . "&.src=pg\" target=\"_new\"><img src=\"/forum_ggc/images/yahoo.gif\" width=\"24\" height=\"20\" alt=\"Send Yahoo! Message\" border=\"0\"></a>";
                            }
                        }
                        print "<br>";
                    }
                    
                    // see if they can edit the item
                    if ($_SESSION["MemberID"] == $qryRow["intMemID"]) {
                        ?>
                        <p>
                        &raquo; <a href="edit/reply_bb.php?id=<?php print $qryRow["replyID"]; ?>"><b>Edit Reply</b></a><br>
                        &raquo; <a href="delete.php?msg=<?php print $qryMessage["msgID"]; ?>&reply=<?php print $qryRow["replyID"]; ?>"><b>Delete Reply</b></a><br>
                        <?php
                    } else {
                        ?>
                        &raquo; <a href="/members/buddy.php?id=<?php print $qryRow["intMemID"]; ?>&return=<?php print $strLoginURL; ?>"><b>Add to Buddy List</b></a><br>
                        <?php
                    }
                    ?>
                    </td>
                    <td>
                    <?php
                    // print out our reply
                    $msgText = $parser->qParse(smilies2(str_replace($crlf, "<br>", $qryRow["txtContent"]), 0));
                    
                    // swap out our quote text
                    $msgText = str_replace("<q>", "<table align=right border=0 width=95%><tr><td>", $msgText);
                    $msgText = str_replace("</q>", "</td></tr></table><br clear=all />", $msgText);
                    
                    // display the text
                    print $msgText;
                    
                    // display their signature
                    if (strlen($qryRow["txtSignature"])) {
                        print "
                        <p>
                        " . $parser->qParse(smilies2(str_replace($crlf, "<br>" . chr(10), $qryRow["txtSignature"]), 0)) . "
                        <br>";
                    }
                    ?><br><br>
                    <table width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td class="smalltxt"><b>Post Date:</b> <?php print date("n/j/Y \@ g:i a", strtotime($qryRow["dateAdded"])); ?></td>
                        <td class="smalltxt" align="right">&raquo; <a href="reply/index_bb.php?msg=<?php print $qryMessage["msgID"]; ?>&id=<?php print $qryRow["replyID"]; ?>&quote=1"><b>Reply With Quote</b></a></td>
                    </tr>
                    </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><br></td>
                </tr>
                <?php
            }
        ?>
        </td>
    </tr>
    <form name="myChoiceBottom">
    <tr>
        <td colspan="2" align="right" class="smalltxt">
        <b>Options:</b>
        <select name="option" class="dropdown" onChange="location.href=this.value">
            <option value="/forum_ggc/msgs/reply/index_bb.php?msg=<?php print $qryMessage["msgID"]; ?>">&nbsp;&raquo;&nbsp;Post A Reply</option>
            <option value="/forum_ggc/msgs/post/index_bb.php">&nbsp;&raquo;&nbsp;Post New Message</option>
            <option value="/forum_ggc/msgs/delete.php?id=<?php print $_GET["id"]; ?>">&nbsp;&raquo;&nbsp;Delete Message &amp; Replies</option>
            <option value="/forum_ggc/msgs/index.php">&nbsp;&raquo;&nbsp;View Message List</option>
        </select>
        <input type="Button" value="Go!" class="smbutton" onClick="location.href=document.myChoiceBottom.option.options[document.myChoiceBottom.option.selectedIndex].value">
        </td>
    </tr>
    </form>
    </form>
    </table>
    <!--- end layout file --->
    
    </td>
</tr>
</table>

<?php
    // include our footer
    if (empty($_SESSION["GGCIFrame"])) {
        require("footer.php");
    }
?>
