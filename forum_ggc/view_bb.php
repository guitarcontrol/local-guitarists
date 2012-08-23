<?php    
    /*
        view_bb.php
        
        Here we'll view the chosen thread.  We first strip out the forum 
        and thread ID's from the PATH_INFO (if supplied).  Then we get the 
        main thread, and then we query any/all replies.  Then just display 
        all of the info.
    */
    
    // include our needed file(s)
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    require("functions.php");
    require("HTML/BBCodeParser.php");

    // see if old links are being used (
    if (ereg("\.[html]$", $_SERVER["REQUEST_URI"]) || !isset($_GET["thread"])) {
        // redirect to new pages
        print "<script language=\"JavaScript\">location.replace(\"/forum_ggc/index.php\");</script>\n";
        exit;
    }
    
    // our function to display the country flag for this user
    function display_country_flag($number) {
        // build our array of country ID's we have images for
        $arrImages = array(10, 13, 14, 21, 29, 38, 68, 69, 76, 100, 103, 132, 143, 146, 152, 162, 182, 194, 212, 213);
        
        // if the passed country is in our array, display it
        if (in_array($number, $arrImages)) {
            // build our image to display
            print "<br /><img src=\"/images/countries/" . $number . ".gif\" width=\"47\" height=\"28\" border=\"0\" />";
        } else {
            print "";
        }
    }
    
    // get the forum title and topic title from the db/
    $qryMain = $dbConn->getRow("
        select      topics.ID as topicID,
                    topics.strTitle,
                    topics.intViews,
                    topics.datePosted,
                    topics.txtPost,
                    topics.bitReply, 
                    topics.intMemID,
                    topics.txtEdited,
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
                    members.strIP,
                    about.intCountry,
                    forums.ID as forumID,
                    forums.strName,
                    forums.intPrivate,
                    forums.intActive,
                    files.filename as avatar
        from        (topics,
                    members,
                    about,
                    forums)
                    LEFT JOIN files on (members.ID = files.uid and files.filetype = 'avatar')
        where       (topics.ID = " . $dbConn->quote($_GET["thread"]) . " and 
                    topics.intForum = " . $dbConn->quote($_GET["forum"]) . ") and 
                    topics.intMemID = members.ID and
                    members.ID = about.intMemID and
                    forums.ID = " . $dbConn->quote($_GET["forum"]) . "
        order by    topics.intForum,
                    topics.dateLastPost desc",
        DB_FETCHMODE_ASSOC);
    
    // if we didn't find anything, take them back
    if (!count($qryMain)) {
        print "
        <script language=\"JavaScript\">
        alert(\"This topic has since been moved. Please try another discussion.\");
        location.replace(\"/forum_ggc/index.php\");
        </script>";
        exit();
    }
    
    // if only mods can view this thread, stop now
    if (!$qryMain["intActive"] && $_SESSION["AccessLevel"] < 90) {
        print "
        <script language=\"JavaScript\">
        alert(\"This forum can only be viewed by moderators.  You are now being redirected.\");
        location.replace(\"/forum_ggc/index.php\");
        </script>";
        exit();
    }
    
    // see if they chose to sort them descendingly
    if (isset($_GET["sort"])) {
        $sortOrder = $_GET["sort"];
        
        if (isset($_COOKIE["SORT"])) {
            if ($_COOKIE["SORT"] != $_GET["sort"]) {
                setcookie("SORT", $_GET["sort"], time() + 31536000, "/");
            }
        } else {
            // set a cookie to remember
            setcookie("SORT", $_GET["sort"], time() + 31536000, "/");
        }
    } else {
        if (isset($_COOKIE["SORT"])) {
            $sortOrder = $_COOKIE["SORT"];
        } else {
            $sortOrder = "asc";
        }
    }
    
    // get the replies for this topic (if any)
    $qryReplies = $dbConn->query("
        select      replies.ID as replyID,
                    replies.strTitle,
                    replies.datePosted,
                    replies.txtReply,
                    replies.txtQuote,
                    replies.intMemID,
                    replies.intDisplaySig,
                    replies.txtEdited,
                    members.ID,
                    members.intAccess,
                    members.strAccess,
                    members.intPosts, 
                    members.intAccess,
                    members.strUsername,
                    members.strAIM,
                    members.strICQ,
                    members.strMSN,
                    members.strYahoo,
                    members.intAllowChat,
                    members.strPublicEmail,
                    members.dateJoined,
                    members.intBanned,
                    members.strIP,
                    members.txtSignature,
                    about.intCountry,
                    files.filename as avatar
        from        (replies,
                    members,
                    about)
                    LEFT JOIN files on (members.ID = files.uid and files.filetype = 'avatar')
        where       replies.intTopic = " . $dbConn->quote($_GET["thread"]) . " and 
                    replies.intMemID = members.ID and
                    members.ID = about.intMemID
        order by    replies.datePosted " . $sortOrder);
    
    // create our array to store our blocked ID's in
    $arrBlocked = array();
        
    // see if they're logged in
    if ($_SESSION["MemberID"]) {
        // get our blocked members for this user
        $qryBlocked = $dbConn->query("
            select  intBlockID
            from    blocked
            where   intMemID = " . $_SESSION["MemberID"]);
        
        // loop through
        while ($qryRow = $qryBlocked->fetchRow(DB_FETCHMODE_ASSOC)) {
            $arrBlocked[] = $qryRow["intBlockID"];
        }
    }
    
    // query the appropriate forums
    $sqlText = "select ID, strTitle, intSort from categories where intParent = 24";
    
    // if they're not an admin, only pull active forums
    if ($_SESSION["AccessLevel"] < 90) {
        $sqlText .= " and intActive = 1";
    }
    $sqlText .= " order by intSort";
    
    // select our main categories to display our forums
    $qryCats = $dbConn->query($sqlText);
    
    // set our default $savedCount
    $savedCount = 0;
    
    // if they're a logged in user, see if they saved any tabs
    if ($_SESSION["MemberID"]) {
        // query their saved tabs (if any)
        $qrySaved = $dbConn->query("
            select    intItem
            from    saved
            where    intType = 2 and
                    intMemID = " . $_SESSION["MemberID"]);
        
        // create an array of saved items
        $arrSaved = array();
        
        // loop through and add any saved items in our array
        while ($qryRow = $qrySaved->fetchRow(DB_FETCHMODE_ASSOC)) {
            $arrSaved[] = $qryRow["intItem"];
        }
    }
    
    // get our list of banned IP's from the db
    $qryIPList = $dbConn->query("
        select      strIP
        from        banned_ips
        order by    strIP");
    
    // create an array to store our IPs in
    while ($qryRow = $qryIPList->fetchRow(DB_FETCHMODE_ASSOC)) {
        $arrIPList[] = $qryRow["strIP"];
    }
    
    // create our BB Code parser to use later
    $parser = new HTML_BBCodeParser(parse_ini_file("BBCodeParser.ini"));
    
    // set our page variables
    $pageTitle = "Guitar Forums: " . $qryMain["strName"] . ": " . $qryMain["strTitle"];
    $pageDescription = "Take part in one of many guitar related discussions here at the Guitarists Network.";
    $pageKeywords = "guitar, chat, forum, topic, thread, guitarists, guitars";
    $areaName = "forums";
    $crlf = chr(10);
    
    // see the order we're to sort the data
    if (isset($_GET["threads"])) {
        // see if we should display all
        if ($_GET["threads"] == "all") {
            $paginate = 0;
            $intDisplayNum = 1000;
        } else {
            $paginate = 1;
            $intDisplayNum = $_GET["threads"];
        }
        
        // set a cookie to remember
        if (!isset($_COOKIE["THREADS"]) || $_COOKIE["THREADS"] != $_GET["threads"]) {
            setcookie("THREADS", $_GET["threads"], time() + 31536000, "/");
        }
    } else if (isset($_COOKIE["THREADS"])) {
        if ($_COOKIE["THREADS"] == "all") {
            $paginate = 0;
            $intDisplayNum = 1000;
        } else {
            $paginate = 1;
            $intDisplayNum = $_COOKIE["THREADS"];
        }
    } else {
        $paginate = 1;
        $intDisplayNum = 15;
    }
           
    // setup our previous/next links
    if (!empty($_GET["page"])) {
        $startRow = $_GET["page"];
        $endRow = $startRow + ($intDisplayNum - 1);
    } else { 
        $startRow = 0;
        $endRow = $intDisplayNum - 1;
    }                    
    
    // set the Google row to display our ad at

    
    
    if ($qryReplies->numRows() > 6) {
        $googleRow = floor(($endRow - $startRow) / 2);
    } else {
        $googleRow = 0;
    }
    print "<!-- Google: $googleRow Start: $startRow End: $endRow -->\n";
    
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

    <br />
<?php if (empty($_SESSION["GGCIFrame"])) { ?>
    <table width="100%" cellspacing="1" cellpadding="3" border="0">
    <tr>
        <td class="tablehead">&nbsp;&raquo;&nbsp;<a href="/index.php"><b>Home</b></a>&nbsp;&raquo;&nbsp;<a href="/forum_ggc/index.php"><b>Guitar Discussions</b></a>&nbsp;&raquo;&nbsp;<a href="/forum_ggc/topics_bb.php?forum=<?php print $_GET["forum"]; ?>"><b><?php print $qryMain["strName"]; ?></b></a>&nbsp;&raquo;&nbsp;<?php print $qryMain["strTitle"]; ?></td>
    </tr>
    </table>
<?php } ?>

    <table width="100%" cellspacing="1" cellpadding="3" border="0">
    <tr>
        <td class="tablehead">&nbsp;&raquo;&nbsp;<a href="/forum_ggc/index.php"><b>Home</b></a>&nbsp;&raquo;&nbsp;<a href="/forum_ggc/topics_bb.php?forum=<?php print $_GET["forum"]; ?>"><b><?php print $qryMain["strName"]; ?></b></a>&nbsp;&raquo;&nbsp;<?php print $qryMain["strTitle"]; ?></td>
    </tr>
    </table>
    <table width="100%" cellspacing="0" cellpadding="2" border="0">
    <tr valign="top">
        <td>
        
        <!--- begin layout file --->
        <table width="100%" cellspacing="1" cellpadding="3" border="0">
        <form name="myChoiceTop">
        <tr>
            <td colspan="2">
            <!-- begin options table -->
            <table width="100%" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <td class="smalltxt">
                <b>Options:</b>
                <select name="option" class="dropdown" onChange="location.href=this.value">
                <?php
                    // see if they can reply
                    if ($qryMain["bitReply"] && $_SESSION["MemberID"] && empty($_SESSION["GGCIFrame"])) {
                        print "<option value=\"/forum_ggc/reply/index_bb.php?ggc=&thread=" . $_GET["thread"] . "\">&nbsp;&raquo;&nbsp;Post A Reply</option>";
                    }
                    
                    // see if they're logged in
                    if ($_SESSION["MemberID"]) {
                        // allow them to start a new thread
                        print "<option value=\"/forum_ggc/post/index_bb.php?forum=" . $_GET["forum"] . "\">&nbsp;&raquo;&nbsp;Start a New Thread</option>";
                        
                        // see if they have any saved threads
                        if (count($arrSaved)) {
                            // continue, based on the results
                            if (!in_array($_GET["thread"], $arrSaved)) {
                                ?>
                                <option value="javascript:plainWin('/forum_ggc/subscribe.php?forum=<?php print $_GET["forum"]; ?>&thread=<?php print $_GET["thread"]; ?>&status=1','250','100')">&nbsp;&raquo;&nbsp;Subscribe To Thread</option>
                                <?php
                            } else {
                                ?>
                                <option value="javascript:plainWin('/forum_ggc/subscribe.php?forum=<?php print $_GET["forum"]; ?>&thread=<?php print $_GET["thread"]; ?>&status=0','250','100')">&nbsp;&raquo;&nbsp;Unsubscribe To Thread</option>
                                <?php
                            }
                        } else {
                            ?>
                            <option value="javascript:plainWin('/forum_ggc/subscribe.php?forum=<?php print $_GET["forum"]; ?>&thread=<?php print $_GET["thread"]; ?>&status=1','250','100')">&nbsp;&raquo;&nbsp;Subscribe To Thread</option>
                            <?php
                        }
                        ?>
                        <option value="/forum_ggc/deliver.php?thread=<?php print $_GET["thread"]; ?>">&nbsp;&raquo;&nbsp;Email To A Friend</option>
                        <option value="/forum_ggc/myposts.php">&nbsp;&raquo;&nbsp;View Your Posts</option>
                        <?php
                    }
                    ?>
                    <option value="/forum_ggc/recent.php">&nbsp;&raquo;&nbsp;Most Recent Posts</option>
                    <option value="/forum_ggc/topics_bb.php?forum=<?php print $qryMain["forumID"]; ?>">&nbsp;&raquo;&nbsp;More Threads</option>
                    <option value="/forum_ggc/search.php">&nbsp;&raquo;&nbsp;Search Threads</option>
                    <option value="/forum_ggc/index.php">&nbsp;&raquo;&nbsp;Home</option>
                </select>
                <input type="Button" value="Go!" class="smbutton" onClick="location.href=document.myChoiceTop.option.options[document.myChoiceTop.option.selectedIndex].value">
                </td>
                <td align="right" class="smalltxt">
                <b>Forums:</b>
                <!--- display our output --->
                <select name="forum" onchange="location.href='/forum_ggc/topics_bb.php?forum=' + this.value" class="dropdown">
                    <?php
                    // loop through our categories and grab the forums
                    while ($qryRow = $qryCats->fetchRow(DB_FETCHMODE_ASSOC)) {
                        ?>
                        <option value=""><?php print $qryRow["strTitle"]; ?></option>
                        <?php
                        // show our categories
                        show_forums($qryRow["ID"], '&nbsp;&nbsp;&nbsp;&nbsp;', $_GET["forum"], $_SESSION["AccessLevel"], $dbConn);
                    }
                    ?>
                </select>
                <input type="Button" value="Go!" class="smbutton" onClick="location.href='/forum_ggc/topics_bb.php?forum=' + document.myChoiceTop.forum.options[document.myChoiceTop.forum.selectedIndex].value">
                </td>
            </tr>
            </table>
    
            </td>
        </tr>
        </form>
        </table>
        
        <table width="100%" cellspacing="0" cellpadding="2" border="0">
        <?php
            // see if they chose to paginate or not
            if ($paginate) {
                // display our seperate pages (if needed)
                if ($qryReplies->numRows() > $intDisplayNum) {
                    // call our pages function
                    $strURL = "/forum_ggc/view_bb.php?forum=". $_GET["forum"] . "&thread=" . $_GET["thread"] . "&display=$intDisplayNum&sort=$sortOrder&";
                    $pageResults = f_prevnext($qryReplies->numRows(), $intDisplayNum, $startRow, '8', $strURL);
                }
            }
            
            // see if the thread is in "read-only" mode
            if (!$qryMain["bitReply"]) {
                ?>
                <tr>
                    <td colspan="2">
                    <b>Note:</b> This thread is in "read only" mode. No replies are allowed.</td>
                </tr>
                <?php
            }
            
            // see if they chose to paginate or not
            if ($paginate) {
                // include our pagination script
                require("paginate_bb.php");
            } else {
                // include our normal script
                require("normal_bb.php");
            }
        ?>
        <!--- end our replies --->
        <?php
            // if replies are on and the user is logged in, offer the quick reply option
            if ($qryMain["bitReply"] && $_SESSION["MemberID"]) {
                ?>
                <form name="myForm" action="/forum_ggc/reply/submit_bb.php" method="post">
                <input type="hidden" name="intForum" value="<?php print $_GET["forum"]; ?>" />
                <input type="hidden" name="intTopic" value="<?php print $_GET["thread"]; ?>" />
                <input type="hidden" name="intMemID" value="<?php print $_SESSION["MemberID"]; ?>" />
                <input type="hidden" name="strTitle" value="RE: <?php print $qryMain["strTitle"]; ?>" />
                <input type="hidden" name="subscribe" value="0" />
                <input type="hidden" name="intDisplaySig" value="1" />
                <tr valign="top">
                    <td>&nbsp;</td>
                    <td>
                    <b>Quick Reply</b><br />
                    <textarea name="txtPost" cols="70" rows="10" wrap="virtual" class="input"></textarea>
                    <input type="submit" value="Quick Reply" class="smbutton">
                    </td>
                </tr>
                </form>
                <?php
            }
        ?>
        <tr>
            <td colspan="2">
            <!-- begin options table -->
            <table width="100%" cellspacing="0" cellpadding="0" border="0">
            <tr valign="top">
                <form name="myChoiceBottom">
                <td class="smalltxt">
                <b>Options:</b><br />
                <select name="option" class="dropdown" onChange="location.href=this.value">
                <?php
                    // see if they can reply
                    if ($qryMain["bitReply"] && $_SESSION["MemberID"]) {
                        print "<option value=\"/forum_ggc/reply/index_bb.php?ggc=1&thread=" . $_GET["thread"] . "\">&nbsp;&raquo;&nbsp;Post A Reply</option>";
                    }
                    
                    // see if they're logged in
                    if ($_SESSION["MemberID"]) {
                        // allow them to start a new thread
                        print "<option value=\"/forum_ggc/post/index_bb.php?ggc=1&forum=" . $_GET["forum"] . "\">&nbsp;&raquo;&nbsp;Start a New Thread</option>";
                        
                        // see if they have any saved threads
                        if (count($arrSaved)) {
                            // continue, based on the results
                            if (!in_array($_GET["thread"], $arrSaved)) {
                                ?>
                                <option value="javascript:plainWin('/forum_ggc/subscribe.php?forum=<?php print $_GET["forum"]; ?>&thread=<?php print $_GET["thread"]; ?>&status=1','250','100')">&nbsp;&raquo;&nbsp;Subscribe To Thread</option>
                                <?php
                            } else {
                                ?>
                                <option value="javascript:plainWin('/forum_ggc/subscribe.php?forum=<?php print $_GET["forum"]; ?>&thread=<?php print $_GET["thread"]; ?>&status=0','250','100')">&nbsp;&raquo;&nbsp;Unsubscribe To Thread</option>
                                <?php
                            }
                        } else {
                            ?>
                            <option value="javascript:plainWin('/forum_ggc/subscribe.php?forum=<?php print $_GET["forum"]; ?>&thread=<?php print $_GET["thread"]; ?>&status=1','250','100')">&nbsp;&raquo;&nbsp;Subscribe To Thread</option>
                            <?php
                        }
                        ?>
                        <option value="/forum_ggc/deliver.php?thread=<?php print $_GET["thread"]; ?>">&nbsp;&raquo;&nbsp;Email To A Friend</option>
                        <option value="/forum_ggc/myposts.php">&nbsp;&raquo;&nbsp;View Your Posts</option>
                        <?php
                    }
                    ?>
                    <option value="/forum_ggc/recent.php">&nbsp;&raquo;&nbsp;Most Recent Posts</option>
                    <option value="/forum_ggc/topics_bb.php>&forum=<?php print $qryMain["forumID"]; ?>">&nbsp;&raquo;&nbsp;More Threads</option>
                    <option value="/forum_ggc/search.php">&nbsp;&raquo;&nbsp;Search Threads</option>
                    <option value="/forum_ggc/index.php">&nbsp;&raquo;&nbsp;Home</option>
                </select>
                <input type="Button" value="Go!" class="smbutton" onClick="location.href=document.myChoiceBottom.option.options[document.myChoiceBottom.option.selectedIndex].value">
                </td>
                </form>
                <td class="smalltxt">
                <form action="view_bb.php" method="get">
                <input type="Hidden" name="forum" value="<?php print $_GET["forum"]; ?>" />
                <input type="Hidden" name="thread" value="<?php print $_GET["thread"]; ?>" />
                <input type="Hidden" name="sort" value="<?php print $sortOrder; ?>" />
                <input type="hidden" name="ggc" value="" />
                <b>Display: </b><br />
                <select name="threads" class="dropdown">
                    <option value="5"<?php if ($intDisplayNum == 5) { print " selected"; } ?>> 5 replies/page</option>
                    <option value="10"<?php if ($intDisplayNum == 10) { print " selected"; } ?>> 10 replies/page</option>
                    <option value="15"<?php if ($intDisplayNum == 15) { print " selected"; } ?>> 15 replies/page</option>
                    <option value="20"<?php if ($intDisplayNum == 20) { print " selected"; } ?>> 20 replies/page</option>
                    <option value="25"<?php if ($intDisplayNum == 25) { print " selected"; } ?>> 25 replies/page</option>
                    <option value="30"<?php if ($intDisplayNum == 30) { print " selected"; } ?>> 30 replies/page</option>
                    <option value="all"<?php if ($intDisplayNum == 1000) { print " selected"; } ?>> All replies</option>
                </select>
                <input type="Submit" value="Go!" class="button" />
                </td>
                </form>
                <form action="view_bb.php" method="get">
                <input type="Hidden" name="forum" value="<?php print $_GET["forum"]; ?>" />
                <input type="Hidden" name="thread" value="<?php print $_GET["thread"]; ?>" />
                <input type="Hidden" name="threads" value="<?php print $intDisplayNum; ?>" />
                <input type="hidden" name="ggc" value="" />
                <td class="smalltxt">
                <b>Sort: </b><br />
                <select name="sort" class="dropdown">
                    <option value="asc"<?php if ($sortOrder == "asc") { print " selected"; } ?>> Oldest to Newest</option>
                    <option value="desc"<?php if ($sortOrder == "desc") { print " selected"; } ?>> Newest to Oldest</option>
                </select>
                <input type="Submit" value="Go!" class="button" />
                </td>
                </form>
            </tr>
            </table>
            <!--- end options table --->
            </td>
        </tr>
        </table>
        <!--- end layout file --->
        </td>
        <?php //require("fastclick.php"); ?>
    </tr>
    </table>

<?php
    // update the view counter
    $pageviews = $qryMain["intViews"] + 1;
    
    // update the database with our new # of views
    $qryUpCount = $dbConn->query("
        update    topics
        set        intViews = " . $pageviews . "
        where    ID = " . $dbConn->quote($_GET["thread"]));

    // include our footer
    if (empty($_SESSION["GGCIFrame"])) {
        require("footer.php");
    }
?>
