<?php
    /*
        normal.php
        
        This is included if they chose to break the pages up (view.php) 
        into multiple pages.
    */
    
    // output our main post
    if ($startRow == 0) {
        ?>
        <tr>
            <td bgcolor="#e5e5e5" colspan="2"><b style="color: #08087C"><?php print $qryMain["strTitle"]; ?></b></td>
        </tr>
        <tr valign="top">
            <td bgcolor="#f6f6f6" width="130" class="smalltxt" nowrap>
            <a href="/members_ggc/profile.php?user=<?php print $qryMain["intMemID"]; ?>"><b style="font-size: 12px;"><?php print $qryMain["strUsername"]; ?></b></a><br>
            <b>IP:</b> 
            <?php
            // show the IP to admins
            if ($_SESSION["AccessLevel"] >= 90) {
                print "<a href=\"tools/banned_ips.php?ip=" . $qryMain["strIP"] . "\" target=\"_new\">";
                if (in_array($qryMain["strIP"], $arrIPList)) {
                    print "<b style=\"color: #cc3333;\">" . $qryMain["strIP"] . "</b>";
                } else {
                    print $qryMain["strIP"];
                }
                print "</a>";
            } else {
                print "Logged";
            }
            ?><br>
            <b>Status:</b> <?php get_member_level($qryMain["intAccess"], $qryMain["strAccess"], $qryMain["intBanned"]); ?><br>
            <b>Posts:</b> <?php print number_format($qryMain["intPosts"]); ?><br>
            <b>Join Date:</b> <?php print date("n/d/Y", strtotime($qryMain["dateJoined"])); ?><br>
            <?php
            // see if they chose to allow IM's
            if ($qryMain["intAllowChat"] && $_SESSION["MemberID"]) {
                // check their IM options
                if ($qryMain["intAllowChat"] == 1 || ($qryMain["intAllowChat"] == 2 && $_SESSION["MemberID"]) || ($qryMain["intAllowChat"] == 3 && $_SESSION["AccessLevel"] >= 90)) {
                    // check AIM
                    if (strlen($qryMain["strAIM"])) {
                        print "<a href=\"aim:goim?screenname=" . trim($qryMain["strAIM"]) . "&message=G-Net+member+chat\"><img src=\"/forum_ggc/images/aim.gif\" width=\"26\" height=\"20\" alt=\"Send AIM\" border=\"0\"></a>";
                    }
                    
                    // check ICQ
                    if (strlen($qryMain["strICQ"])) {
                        print "<a href=\"http://wwp.icq.com/scripts/search.dll?to=" . trim($qryMain["strICQ"]) . "\" target=\"_new\"><img src=\"/forum_ggc/images/icq.gif\" width=\"18\" height=\"20\" alt=\"Send ICQ\" border=\"0\"></a>";
                    }
                    
                    // check MSN Messenger
                    if (strlen($qryMain["strMSN"])) {
                        print "<img src=\"/forum_ggc/images/msn.gif\" width=\"21\" height=\"20\" alt=\"" . trim($qryMain["strMSN"]) . "\" border=\"0\">";
                    }
                    
                    // check Yahoo! Messenger
                    if (strlen($qryMain["strYahoo"])) {
                        print "<a href=\"http://edit.yahoo.com/config/send_webmesg?.target=" . trim($qryMain["strYahoo"]) . "&.src=pg\" target=\"_new\"><img src=\"/forum_ggc/images/yahoo.gif\" width=\"24\" height=\"20\" alt=\"Send Yahoo! Message\" border=\"0\"></a>";
                    }
                }
                print "<br>";
            }
            
            // see if the user is the poster or a moderator
            if ($_SESSION["MemberID"] == $qryMain["intMemID"] || $_SESSION["AccessLevel"] >= 90) {
                ?>
                <br>
                &raquo; <a href="/forum_ggc/edit/post.php?id=<?php print $_GET["thread"]; ?>"><b>Edit Topic</b></a><br>
                &raquo; <a href="javascript:plainWin('/forum_ggc/tools/delete.php?topic=<?php print $_GET["thread"]; ?>','100','20')" onClick="return confirm('Are you sure you want to delete this entire thread?');"><b>Delete Thread</b></a><br>
                <?php
            }
            
            // display options if they're a mod
            if ($_SESSION["AccessLevel"] >= 90) {
                // display the close or opn link
                if ($qryMain["bitReply"]) {
                    ?>
                    &raquo; <a href="javascript:plainWin('/forum_ggc/tools/close.php?forum=<?php print $_GET["forum"]; ?>&topic=<?php print $_GET["thread"]; ?>&active=0','150','20')"><b>Close Topic</b></a><br>
                    <?php
                } else {
                    ?>
                    &raquo; <a href="javascript:plainWin('/forum_ggc/tools/close.php?forum=<?php print $_GET["forum"]; ?>&topic=<?php print $_GET["thread"]; ?>&active=1','150','20')"><b>Open Topic</b></a><br>
                    <?php
                }
                ?>
                &raquo; <a href="javascript:plainWin('/forum_ggc/tools/move.php?topic=<?php print $_GET["thread"]; ?>','250','100')"><b>Move Thread</b></a><br>
                <?php
            }
            
            // display standard user options
            if ($_SESSION["MemberID"] && $qryMain["ID"] != $_SESSION["MemberID"]) {
                // see if they have blocked anyone at all
                if (count($arrBlocked)) {
                    // see if they've blocked this user from being displayed
                    if (in_array($qryMain["intMemID"], $arrBlocked)) {
                        print "&raquo; <a href=\"javascript:plainWin('/forum_ggc/tools/block.php?id=" . $qryMain["intMemID"] . "&forum=" . $_GET["forum"] . "&topic=" . $_GET["thread"] . "&ban=0','100','20')\"><b>Unblock Member</b></a><br>";
                    } else {
                        print "&raquo; <a href=\"javascript:plainWin('/forum_ggc/tools/block.php?id=" . $qryMain["intMemID"] . "&forum=" . $_GET["forum"] . "&topic=" . $_GET["thread"] . "&ban=1','100','20')\" onClick=\"return confirm('Are you sure you want to block all posts by \'" . addslashes($qryMain["strUsername"]) . "\'?')\"><b>Block Member</b></a><br>";
                    }
                } else {
                    print "
                    &raquo; <a href=\"javascript:plainWin('/forum_ggc/tools/block.php?id=" . $qryMain["intMemID"] . "&forum=" . $_GET["forum"] . "&topic=" . $_GET["thread"] . "&ban=1','100','20')\" onClick=\"return confirm('Are you sure you want to block all posts by \'" . addslashes($qryMain["strUsername"]) . "\'?')\"><b>Block Member</b></a><br>";
                }
                
                // see if they're not banned
                if (!$qryMain["intBanned"]) {
                    ?>
                    &raquo; <a href="/members_ggc/msgs/post/index_bb.php?user=<?php print $qryMain["intMemID"]; ?>"><b>Send Private Message</b></a><br>
                    &raquo; <a href="/members_ggc/buddy.php?id=<?php print $qryMain["intMemID"]; ?>&return=/forum_ggc/view.php?<?php print $_SERVER["QUERY_STRING"]; ?>"><b>Add to "Buddy List"</b></a><br>
                    <?php
                }
            }
            
            // see if they're a mod
            if ($_SESSION["AccessLevel"] >= 90) {
                ?>
                &raquo; <a href="tools/panel.php?memid=<?php print $qryMain["intMemID"]; ?>" target="_new"><b>Control Panel</b></a><br>
                <?php
            }
            ?>
            </td>
            <td>
            <?php
            // see if we found any blocked members for this user
            if (count($arrBlocked)) {
                // if they've been blocked, display nothing
                if (in_array($qryMain["intMemID"], $arrBlocked) && $qryMain["ID"] != $_SESSION["MemberID"]) {
                    // display the blocked text message
                    print ":: Blocked Content ::";
                } else {
                    // see if we need to mark search terms
                    if (!empty($_GET["mark"])) {
                        $msgText = str_replace($_GET["mark"], "<b style=\"color: red;\">" . $_GET["mark"] . "</b>", $qryMain["txtPost"]);
                    } else {
                        $msgText = $qryMain["txtPost"];
                    }
                    
                    // display the normal stuff
                    print str_replace($crlf, "<br>" . chr(10), $msgText) . "
                    <br>";
                    
                    // display their signature
                    if (strlen($qryMain["txtSignature"])) {
                        print "
                        <br>
                        " . str_replace($crlf, "<br>" . chr(10), $qryMain["txtSignature"]) . "
                        <br>";
                    }
                }
            } else {
                // see if we need to mark search terms
                if (!empty($_GET["mark"])) {
                    $msgText = str_replace($_GET["mark"], "<b style=\"color: red;\">" . $_GET["mark"] . "</b>", $qryMain["txtPost"]);
                } else {
                    $msgText = $qryMain["txtPost"];
                }
                
                // display the normal stuff
                print str_replace($crlf, "<br>" . chr(10), $msgText) . "
                <br>";
                
                // display their signature
                if (strlen($qryMain["txtSignature"])) {
                    print "
                    <br>
                    " . str_replace($crlf, "<br>" . chr(10), $qryMain["txtSignature"]) . "
                    <br>";
                }
            }
            ?>
            <br>
            <table width="100%" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <td class="smalltxt"><b>Post Date:</b> <?php print date("n/j/Y \@ g:i a", strtotime($qryMain["datePosted"]));
                
                // see it has been edited by a mod
                if (strlen($qryMain["txtEdited"])) {
                    print "<br>
                    " . str_replace($crlf, "<br>" . chr(10), $qryMain["txtEdited"]);
                }
                ?></td>
                <td class="smalltxt" align="right" nowrap>
                <?php
                // see if they can reply or quote it
                if ($_SESSION["MemberID"] && $qryMain["bitReply"]) {
                    print "
                    &raquo; <a href=\"/forum_ggc/reply/index.php?thread=" . $_GET["thread"] . "&quote=1\"><b>Reply With Quote</b></a><br>";
                }
                
                // see if it can be reported
                if ($_SESSION["MemberID"]) {
                    print "
                    &raquo; <a href=\"/forum_ggc/report.php?thread=" . $_GET["thread"] . "&forum=" . $_GET["forum"] . "&topic=" . $_GET["thread"] . "\" onClick=\"return confirm('Are you sure you want to report this thread?')\"><b>Report To A Moderator</b></a>";
                }
                ?>
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
    }
    
    // display replies, if any were found
    if ($qryReplies->numRows()) {
    
    // output our replies, based on the page chosen
    while ($qryRow = $qryReplies->fetchRow(DB_FETCHMODE_ASSOC)) {
        ?>
        <tr>
            <td bgcolor="#e5e5e5" colspan="2"><b style="color: #08087C">
            <a name="post<?php print $qryRow["replyID"]; ?>"></a>
            <?php
            // see if a title was entered (or not
            if (!strlen($qryRow["strTitle"])) {
                print "...";
            } else {
                print $qryRow["strTitle"];
            }
            ?></b></td>
            </tr></a>
            <tr valign="top">
                <td bgcolor="#f6f6f6" class="smalltxt" nowrap>
                <a href="/members_ggc/profile.php?user=<?php print $qryRow["intMemID"]; ?>"><b style="font-size: 12px;"><?php print $qryRow["strUsername"]; ?></b></a><br>
                IP: 
                <?php
                // if they're a mod, show the IP
                if ($_SESSION["AccessLevel"] >= 90) {
                    print "<a href=\"tools/banned_ips.php?ip=" . $qryRow["strIP"] . "\" target=\"_new\">";
                    if (in_array($qryRow["strIP"], $arrIPList)) {
                        print "<b style=\"color: #cc3333;\">" . $qryRow["strIP"] . "</b>";
                    } else {
                        print $qryRow["strIP"];
                    }
                    print "</a>";
                } else {
                    print "Logged";
                }
                ?><br>
                <b>Status:</b> <?php get_member_level($qryRow["intAccess"], $qryRow["strAccess"], $qryRow["intBanned"]); ?><br>
                <b>Posts:</b> <?php print number_format($qryRow["intPosts"]); ?><br>
                <b>Join Date:</b> <?php print date("n/d/Y", strtotime($qryRow["dateJoined"])) . "<br>";
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
            
            // see if they can delete this item
            if ($_SESSION["MemberID"] == $qryRow["intMemID"] || $_SESSION["AccessLevel"] >= 90) {
                ?>
                <br>
                &raquo; <a href="/forum_ggc/edit/reply.php?id=<?php print $qryRow["replyID"]; ?>"><b>Edit Reply</b></a><br>
                &raquo; <a href="javascript:plainWin('/forum_ggc/tools/delete.php?reply=<?php print $qryRow["replyID"]; ?>','100','20')" onclick="return confirm('Are you sure you want to delete this reply?');"><b>Delete Reply</b></a><br>
                <?php
            }
            
            // display standard user options
            if ($_SESSION["MemberID"] && $qryRow["intMemID"] != $_SESSION["MemberID"]) {
                // see if they have blocked anyone at all
                if (count($arrBlocked)) {
                    // see if they've blocked this user from being displayed
                    if (in_array($qryRow["intMemID"], $arrBlocked)) {
                        print "&raquo; <a href=\"javascript:plainWin('/forum_ggc/tools/block.php?id=" . $qryRow["intMemID"] . "&forum=" . $_GET["forum"] . "&topic=" . $_GET["thread"] . "&ban=0','100','20')\"><b>Unblock Member</b></a><br>";
                    } else {
                        print "&raquo; <a href=\"javascript:plainWin('/forum_ggc/tools/block.php?id=" . $qryRow["intMemID"] . "&forum=" . $_GET["forum"] . "&topic=" . $_GET["thread"] . "&ban=1','100','20')\" onClick=\"return confirm('Are you sure you want to block all posts by \'" . addslashes($qryRow["strUsername"]) . "\'?')\"><b>Block Member</b></a><br>";
                    }
                } else {
                    print "
                    &raquo; <a href=\"javascript:plainWin('/forum_ggc/tools/block.php?id=" . $qryRow["intMemID"] . "&forum=" . $_GET["forum"] . "&topic=" . $_GET["thread"] . "&ban=1','100','20')\" onClick=\"return confirm('Are you sure you want to block all posts by \'" . addslashes($qryRow["strUsername"]) . "\'?')\"><b>Block Member</b></a><br>";
                }
                
                print "
                &raquo; <a href=\"/members_ggc/msgs/post/index_bb.php?user=" . $qryRow["intMemID"] . "\"><b>Send Private Message</b></a><br>
                &raquo; <a href=\"/members_ggc/buddy.php?id=" . $qryRow["intMemID"] . "&return=/forum_ggc/view.php?" . $_SERVER["QUERY_STRING"] . "\"><b>Add to \"Buddy List\"</b></a><br>\n";
            }
            
            // see if they're a mod
            if ($_SESSION["AccessLevel"] >= 90) {
                // display the control panel link
                print "
                &raquo; <a href=\"tools/panel.php?memid=" . $qryRow["intMemID"] . "\" target=\"_new\"><b>Control Panel</b></a><br>";
            }
            ?></td>
            <td>
            <?php
            // see if we found any blocked members for this user
            if (count($arrBlocked)) {
                // if they've been blocked, display nothing
                if (in_array($qryRow["intMemID"], $arrBlocked) && $qryRow["intMemID"] != $_SESSION["MemberID"]) {
                    // display the blocked text message
                    print ":: Blocked Content ::";
                } else {
                    // see if they have a quote
                    if (strlen($qryRow["txtQuote"])) {
                        ?>
                        <div align="right"><table width="98%" cellspacing="0" cellpadding="0" border="0">
                        <tr>
                            <td><?php print str_replace($crlf, "<br>" . chr(10), trim($qryRow["txtQuote"])); ?></td>
                        </tr>
                        </table></div>
                        <p>
                        <?php
                    }
                    
                    // see if we need to mark search terms
                    if (!empty($_GET["mark"])) {
                        $msgText = str_replace($_GET["mark"], "<b style=\"color: red;\">" . $_GET["mark"] . "</b>", $qryRow["txtReply"]);
                    } else {
                        $msgText = $qryRow["txtReply"];
                    }
                    
                    // display their signature
                    print str_replace($crlf, "<br>" . chr(10), $msgText) . "<br>";
                    
                    // see if they chose to add their sig to this post
                    if (strlen($qryRow["txtSignature"]) && $qryRow["intDisplaySig"]) {
                        print "<br>" . 
                        str_replace($crlf, "<br>" . chr(10), $qryRow["txtSignature"]) . "<br>";
                    }
                }
            } else {
                // see if they have a quote
                if (strlen($qryRow["txtQuote"])) {
                    ?>
                    <div align="right"><table width="98%" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td><?php print str_replace($crlf, "<br>" . chr(10), trim($qryRow["txtQuote"])); ?></td>
                    </tr>
                    </table></div>
                    <p>
                    <?php
                }
                
                // see if we need to mark search terms
                if (!empty($_GET["mark"])) {
                    $msgText = str_replace($_GET["mark"], "<b style=\"color: red;\">" . $_GET["mark"] . "</b>", $qryRow["txtReply"]);
                } else {
                    $msgText = $qryRow["txtReply"];
                }
                
                // display the normal stuff
                print str_replace($crlf, "<br>" . chr(10), $msgText) . "
                <br>";
                
                // see if they chose to add their sig to this post
                if (strlen($qryRow["txtSignature"]) && $qryRow["intDisplaySig"]) {
                    print "<br>" . 
                    str_replace($crlf, "<br>" . chr(10), $qryRow["txtSignature"]) . "<br>";
                }
            }
            ?><br>
            <table width="100%" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <td class="smalltxt"><b>Post Date:</b> <?php print date("n/j/Y \@ g:i a", strtotime($qryRow["datePosted"]));
                // see if it has been edited
                if (strlen($qryRow["txtEdited"])) {
                    ?><br>
                    <?php print $qryRow["txtEdited"];
                }
                ?>
                </td>
                <td class="smalltxt" align="right" nowrap>
                <?php
                // make sure they're logged in
                if ($_SESSION["MemberID"] && $qryMain["bitReply"]) {
                    print "&raquo; <a href=\"/forum_ggc/reply/index.php?thread=" . $_GET["thread"] . "&reply=" . $qryRow["replyID"] . "&quote=1\"><b>Reply With Quote</b></a><br>";
                }
                // create a link to report the thread
                if ($_SESSION["MemberID"]) {
                    print "&raquo; <a href=\"/forum_ggc/report.php?thread=" . $_GET["thread"] . "&forum=" . $_GET["forum"] . "&reply=" . $qryRow["replyID"] . "\" onClick=\"return confirm('Are you sure you want to report this thread?')\"><b>Report To A Moderator</b></a>";
                }
                ?>
                </td>
            </tr>
            </table>
            </td>
        </tr>
        <tr>
            <td colspan="2"><br></td>
        </tr>
        <?php
        }
    }
?>
