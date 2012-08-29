<?php

    /*

        topics_bb.php

        

        Here we'll display our active threads, based on the forum chosen.

    */

    

    // include our needed file(s)

    require("/home/gnet/includes/guitarists.net/global_vars.php");

    require("gnet_db.php");

    require("sessions.php");

    require("ads.php");

    require("functions.php");

    

    // see if old links are being used (

    if (ereg("\.[html]$", $_SERVER["REQUEST_URI"]) || !isset($_GET["forum"])) {

        // redirect to new pages

        print "<script language=\"JavaScript\">location.replace(\"/forum/index.php\");</script>\n";

        exit;

    }

    

    // make sure it's not PM's.  If so, redirect.

    if ($_GET["forum"] == 24) {

        print "

        <script language=\"JavaScript\">

        alert(\"Private Messages have moved to their own area.  Please update your bookmarks.\");

        location.replace(\"/members/msgs/index.php\");

        </script>";

        exit();

    }

    

    // query the db based on the forum ID passed

    $qryForum = $dbConn->getRow("

        select  forums.ID,

                forums.intParent,

                forums.strName,

                forums.intPrivate,

                forums.intActive,

                forums.intAdmin,

                forums.ExternalURL,

                categories.strTitle,

                members.strUsername

        from    forums,

                categories,

                members

        where   forums.ID = " . $dbConn->quote($_GET["forum"]) . " and 

                forums.intParent = categories.ID and

                forums.intAdmin = members.ID",

        DB_FETCHMODE_ASSOC);

    

    // if no records were found, stop here

    if (PEAR::isError($qryForum) || !count($qryForum)) {

        print "

        <script language=\"JavaScript\">

        alert(\"This forum has been moved or does not exist.  Please try again.\");

        location.replace(\"/forum/index.php\");

        </script>";

        exit();

    }

    

    // if the forum isn't active, take them out of here

    if (!$qryForum["intActive"] && $_SESSION["AccessLevel"] < 90) {

        print "

        <script language=\"JavaScript\">

        alert(\"This forum is currently not active.  You are now being redirected.\");

        location.replace(\"/forum/index.php\");

        </script>";

        exit();

    }

    

    // if the forum is private, make sure they're logged in

    if ($qryForum["intPrivate"] && !$_SESSION["MemberID"]) {

        print "

        <script language=\"JavaScript\">

        alert(\"You must be logged in to view this forum.\");

        location.replace(\"/forum/index.php\");

        </script>";

        exit();

    }

    

    // if we've specified an external URL, redirect them now

    if ($qryForum["ExternalURL"]) {

        print "

        <script language=\"JavaScript\">

        location.replace(\"" . $qryForum["ExternalURL"] . "\");

        </script>";

        exit();

    }

    

    // set the # of days to pull in

    if (isset($_GET["days"])) {

        // see what the value is

        if ($_GET["days"] == "all") {

            $cutOff = strtotime("1990-01-01 00:00:00");

            $myDays = "all";

        } else {

            $myDays = $_GET["days"];

            $cutOff = strtotime("-" . $_GET["days"] . " days");

        }

        setcookie("DAYS", $_GET["days"], time() + 31536000, "/");

    } else if (isset($_COOKIE["DAYS"])) {

        if ($_COOKIE["DAYS"] == "all") {

            $cutOff = strtotime("1990-01-01 00:00:00");

            $myDays = "all";

        } else {

            $myDays = $_COOKIE["DAYS"];

            $cutOff = strtotime("-" . $_COOKIE["DAYS"] . " days");

        }

    } else {

        $myDays = 30;

        $cutOff = strtotime("-30 days");

    }

    

    // set the # of topics to list per page

    if (isset($_GET["display"])) {

        $intDisplayNum = $_GET["display"];

        setcookie("DISPLAY", $_GET["display"], time() + 31536000, "/");

    } else if (isset($_COOKIE["DISPLAY"])) {

        $intDisplayNum = $_COOKIE["DISPLAY"];

    } else {

        $intDisplayNum = 25;

    }

    

    // query the db for our titles

    $qryTopics = $dbConn->query("

        select        topics.ID,

                    topics.strTitle,

                    topics.intReplies,

                    topics.intViews,

                    topics.dateLastPost, 

                    topics.strLastPost,

                    topics.intLastID,

                    topics.intSticky,

                    topics.bitReply,

                    members.ID as memID, 

                    members.strUsername

        from        topics,

                    members

        where        topics.intForum = " . $qryForum["ID"] . " and 

                    (topics.dateLastPost >= '" . date("Y-m-d", $cutOff) . " 00:00:00' or intSticky = 1) and

                    topics.intMemID = members.ID

        order by    topics.intSticky desc,

                    topics.dateLastPost desc");

    

    // get our admins for this forum

    $qryMods = $dbConn->query("

        select  forum_mods.*,

                members.strUsername

        from    forum_mods,

                members

        where   forum_mods.ForumID = " . $qryForum["ID"] . " and

                forum_mods.MemberID = members.ID");


    

    // query the appropriate forums

    $sqlText = "select ID, strTitle, intSort from categories where intParent = 24";

    

    // if they're not an admin, only pull active forums

    if ($_SESSION["AccessLevel"] < 90) {

        $sqlText .= " and intActive = 1";

    }

    $sqlText .= " order by intSort";

    

    // select our main categories to display our forums

    $qryCats = $dbConn->query($sqlText);

    

    // create an array of categories

    while ($qryRow = $qryCats->fetchRow(DB_FETCHMODE_ASSOC)) {

        $arrCats[] = array($qryRow["ID"],

                           $qryRow["strTitle"]);

    }

    

    // set our variables

    $pageTitle = "Guitar Forums: " . $qryForum["strTitle"] . ": " . $qryForum["strName"];

    $pageRefresh = 300;

    $areaName = "forums";

    

    if (isset($_GET["page"])) {

        $startRow = $_GET["page"];

        $endRow = $startRow + ($intDisplayNum - 1);

    } else {

        $startRow = 0;

        $endRow = $intDisplayNum - 1;

    }

    

    // include our header

    require("header.php");

?>



    <br>

    <table width="100%" cellspacing="0" cellpadding="2" border="0">

    <tr>

        <td class="tablehead">&nbsp;&raquo;&nbsp;<a href="/index.php"><b>Home</b></a>&nbsp;&raquo;&nbsp;<a href="/forum/index.php"><b>Guitar Discussions</b></a>&nbsp;&raquo;&nbsp;<a href="/forum/forums.php?id=<?php print $qryForum["intParent"]; ?>"><b><?php print $qryForum["strTitle"]; ?></b></a>&nbsp;&raquo;&nbsp;<?php print $qryForum["strName"]; ?></td>

    </tr>

    </table>

    

    <table width="100%" cellspacing="0" cellpadding="2" border="0">

    <tr valign="top">

        <td>

        <!--- begin layout file --->

        <table width="100%" cellspacing="1" cellpadding="3" border="0">

        <tr>

            <td colspan="6">

            <table width="100%" cellpadding="0" cellspacing="1" border="0">

            <form name="myChoiceTop">

            <tr>

                <td class="smalltxt">

                <b>Options:</b>

                <select name="option" class="dropdown" onChange="location.href=this.value">

                <?php

                    // see if it's private or not

                    if (!$qryForum["intPrivate"] && $_SESSION["MemberID"]) {

                        print "<option value=\"/forum/post/index.php?forum=" . $qryForum["ID"] . "\">&nbsp;&raquo;&nbsp;Start A Thread</option>";

                    }

                    ?>

                    <option value="/forum/search.php">&nbsp;&raquo;&nbsp;Search Threads</option>

                    <?php

                    // see if they're logged in or not

                    if ($_SESSION["MemberID"]) {

                        ?>

                        <option value="/forum/myposts.php">&nbsp;&raquo;&nbsp;View Your Posts</option>

                        <option value="/members/msgs/index.php">&nbsp;&raquo;&nbsp;View Private Messages</option>

                        <?php

                    } else {

                        ?>

                        <option value="/register/index.php">&nbsp;&raquo;&nbsp;Register To Post</option>

                        <option value="/login.php">&nbsp;&raquo;&nbsp;Login To Post</option>

                        <?php

                    }

                    ?>

                    <option value="/forum/recent.php">&nbsp;&raquo;&nbsp;Most Recent Posts</option>

                    <option value="/forum/posters.php">&nbsp;&raquo;&nbsp;Top 50 Posters</option>

                    <option value="/forum/index.php">&nbsp;&raquo;&nbsp;Home</option>

                </select>

                <input type="Button" value="Go!" class="smbutton" onClick="location.href=document.myChoiceTop.option.options[document.myChoiceTop.option.selectedIndex].value">

                </td>

                <td align="right" class="smalltxt">

                <b>Forums:</b>

                <!--- display our output --->

                <select name="forum" onchange="location.href='/forum/topics_bb.php?forum=' + this.value" class="dropdown">

                    <?php

                        // loop through our categories and grab the forums

                        for ($i = 0; $i < count($arrCats); $i++) {

                            ?>

                            <option value="<?php print $arrCats[$i][0]; ?>"><?php print $arrCats[$i][1]; ?></option>

                            <?php

                            // show our categories

                            show_forums($arrCats[$i][0], '&nbsp;&nbsp;&nbsp;&nbsp;', $qryForum["ID"], $_SESSION["AccessLevel"], $dbConn);

                        }

                    ?>

                </select>

                <input type="Button" value="Go!" class="smbutton" onClick="location.href='/forum/topics_bb.php?forum=' + document.myChoiceTop.forum.options[document.myChoiceTop.forum.selectedIndex].value">

                </td>

            </tr>

            </form>

            </table>

            </td>

        </tr>

        

        <?php

            // see if we need to paginate
            
        

            if ($qryTopics->numRows() > $intDisplayNum) {

                // call our pages function

                $strURL = "/forum/topics_bb.php?forum=" . $qryForum["ID"] . "&days=$myDays&display=$intDisplayNum&";

                f_prevnext($qryTopics->numRows(), $intDisplayNum, $startRow, '8', $strURL);

            }

            

            // see if any records were found

            if ($qryTopics->numRows()) {

                // if we found mods, display them

                if ($qryMods->numRows()) {

                    ?>

                    <tr>

                        <td colspan="6" class="smalltxt">

                        Your moderator(s) for today:

                        <?php

                        // set our row counter

                        $rowCount = 0;

                        

                        // loop through our mods

                        while ($qryRow = $qryMods->fetchRow(DB_FETCHMODE_ASSOC)) {

                            ?>

                            <a href="/members/profile.php?user=<?php print $qryRow["MemberID"]; ?>"><b><?php print $qryRow["strUsername"]; ?></b></a><?php

                            // update thr row counter

                            $rowCount++;

                            

                            // if it's not the last, display a comma

                            if ($rowCount < $qryMods->numRows()) {

                                print ", ";

                            }

                        }

                        ?>

                        </td>

                    </tr>

                    <?php

                ?>

                <tr align="center">

                    <td class="innertitle">&nbsp;</td>

                    <td width="50%" class="innertitle"><b>Topics</b></td>

                    <td width="10%" class="innertitle"><b>Replies</b></td>

                    <td width="10%" class="innertitle"><b>Views</b></td>

                    <td width="15%" class="innertitle"><b>Author</b></td>

                    <td width="15%" class="innertitle"><b>Last Update</b></td>

                </tr>

                <?php

                }

                

                // set our alternate row color counter

                $altCounter = 1;

                $rowCount = 0;

                

                // loop through our results

                while ($qryRow = $qryTopics->fetchRow(DB_FETCHMODE_ASSOC)) {

                    // see if it's in our list to be displayed

                    if ($rowCount >= $startRow && $rowCount <= $endRow) {

                        // set the row counter

                        $bgcolor = "#ffffff";

                        if ($altCounter % 2 == 0) {

                            $bgcolor = "#f6f6f6";

                        }

                        ?>

                        <tr valign="middle" bgcolor="<?php print $bgcolor; ?>">

                            <td nowrap>

                            <?php

                            // see if it's a new thread

                            if ($_SESSION["MemberID"]) {

                                // set a time for the last update of this post

                                if (strtotime($qryRow["dateLastPost"]) > strtotime($_SESSION["LastLogin"])) {

                                    ?>

                                    <img src="/forum/images/new.gif" width="11" height="12" alt="New!">

                                    <?php

                                }

                            }

                            

                            // see if it's a sticky

                            if ($qryRow["intSticky"]) {

                                ?>

                                <img src="/forum/images/sticky.gif" align="absmiddle" width="11" height="12" alt="Sticky">

                                <?php

                            }

                            

                            // see if it's a read only

                            if (!$qryRow["bitReply"]) {

                                ?>

                                <img src="/forum/images/locked.gif" align="absmiddle" width="11" height="12" alt="Sticky">

                                <?php

                            }

                            ?></td>

                                <td>

                                <b><a href="/forum/view_bb.php?forum=<?php print $qryForum["ID"]; ?>&thread=<?php print $qryRow["ID"]; ?>">

                            <?php

                            // make sure a title exists

                            if (strlen($qryRow["strTitle"])) {

                                print $qryRow["strTitle"];

                            } else {

                                print "No Title Provided";

                            }

                            ?></a></b></td>

                                <td align="center" class="smalltxt"><?php print number_format($qryRow["intReplies"]); ?></td>

                                <td align="center" class="smalltxt"><?php print number_format($qryRow["intViews"]); ?></td>

                                <td align="center" class="smalltxt"><a href="/members/profile.php?user=<?php print $qryRow["memID"]; ?>"><b><?php print $qryRow["strUsername"]; ?></b></a></td>

                                <td align="center" class="smalltxt"><?php print date("M\. j \@ g\:i A", strtotime($qryRow["dateLastPost"]));

                                // see if we have a valid date

                                if (strlen($qryRow["strLastPost"])) {

                                    ?><br>

                                    <a href="/members/profile.php?user=<?php print $qryRow["intLastID"]; ?>"><b><?php print trim($qryRow["strLastPost"]); ?></b></a>

                                    <?php

                                }

                            ?>

                            </td>

                        </tr>

                        <?php

                    }

                    

                    // update our alternate row color counter

                    $altCounter++;

                    $rowCount++;

                }

                ?>

                <tr>

                    <td colspan="5">

                    <img src="/forum/images/new.gif" width="11" height="12" alt="New" border="0"> 

                    indicates a new post&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                    <img src="/forum/images/locked.gif" width="11" height="12" alt="Read Only" border="0"> 

                    indicates a "read only" post&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                    <img src="/forum/images/sticky.gif" width="12" height="12" alt="Sticky" border="0"> 

                    indicates a "sticky" post&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                    </td>

                </tr>

                

                <tr>

                    <td colspan="5"><br />

                    <a href="/rss/forum.php?forum=<?php print $_GET["forum"]; ?>" title="RSS" target="_new"><img src="/images/rss.gif" width="28" height="16" alt="RSS" border="0" /></a>

                    <a href="http://fusion.google.com/add?feedurl=http%3A//www.guitarists.net/rss/forum.php?forum=<?php print $_GET["forum"]; ?>" title="Google RSS" target="_new"><img src="/images/add_google_rss.gif" width="104" height="17" alt="Google RSS" border="0" /></a>

                    <a href="http://add.my.yahoo.com/content?.intl=us&url=http%3A//www.guitarists.net/rss/forum.php?forum=<?php print $_GET["forum"]; ?>" title="Yahoo! RSS" target="_new"><img src="/images/add_yahoo_rss.gif" width="91" height="17" alt="Yahoo! RSS" border="0" /></a>

                    </td>

                </tr>

                <?php

            } else {

                ?>

                <tr>

                    <td colspan="5">

                    There are currently <b>0</b> topics listed under <b>"<?php print $qryForum["strName"]; ?>"</b>
                    within the number of days specified.
                    Feel free to <a href="/forum_ggc/post/index.php?thread=<?php print $qryForum["ID"]; ?>"><b>start a new 
                    thread</b></a>, or bookmark this page, so you can easily come back, or choose a "All Posts" in the dropdown below.

                    <p>

                    Thanks.

                    </td>

                </tr>

                <?php

            }

            

            // display our Google ad if ads are on

            if (!$_SESSION["HideAds"]) {

                ?>

                <tr>

                    <td colspan="5"><br />

                    <script type="text/javascript"><!--

                    google_ad_client = "ca-pub-3777083047736569";

                    google_ad_width = 300;

                    google_ad_height = 250;

                    google_ad_format = "300x250_as";

                    google_ad_type = "text_image";

                    //2007-02-28: Guitarists Forum (300x250)

                    google_ad_channel = "3551436182";

                    google_color_border = "FFFFFF";

                    google_color_bg = "FFFFFF";

                    google_color_link = "0A3D6B";

                    google_color_text = "000000";

                    google_color_url = "004A80";

                    //--></script>

                    <script type="text/javascript"

                      src="http://pagead2.googlesyndication.com/pagead/show_ads.js">

                    </script>

                    </td>

                </tr>

                <?php

            }

            

            // see if we need to paginate

            if ($qryTopics->numRows() > $intDisplayNum) {

                // call our pages function

                $strURL = "/forum/topics_bb.php?forum=" . $qryForum["ID"] . "&days=$myDays&display=$intDisplayNum&";

                f_prevnext($qryTopics->numRows(), $intDisplayNum, $startRow, '8', $strURL);

            }

            ?>

            <tr>

                <td colspan="8"><br />

                <a href="/forum/post/index.php?forum=<?php print $qryForum["ID"]; ?>" title="Start a New Thread"><img src="/images/submit_thread.png" width="160" height="38" alt="Start a New Thread" border="0" /></a>

                <p />

                <!-- begin options table -->

                <table width="100%" cellspacing="0" cellpadding="0" border="0">

                <tr>

                    <form action="topics_bb.php" method="get">

                    <input type="Hidden" name="forum" value="<?php print $_GET["forum"]; ?>" />

                    <td class="smalltxt">

                    <b>Days:</b><br>

                    <select name="days" class="dropdown">

                        <option value="1"<?php if ($myDays == 1) { print " selected"; } ?>> Yesterday</option>

                        <option value="2"<?php if ($myDays == 2) { print " selected"; } ?>> 2 Days</option>

                        <option value="3"<?php if ($myDays == 3) { print " selected"; } ?>> 3 Days</option>

                        <option value="4"<?php if ($myDays == 4) { print " selected"; } ?>> 4 Days</option>

                        <option value="5"<?php if ($myDays == 5) { print " selected"; } ?>> 5 Days</option>

                        <option value="10"<?php if ($myDays == 10) { print " selected"; } ?>> 10 Days</option>

                        <option value="15"<?php if ($myDays == 15) { print " selected"; } ?>> 15 Days</option>

                        <option value="20"<?php if ($myDays == 20) { print " selected"; } ?>> 20 Days</option>

                        <option value="30"<?php if ($myDays == 30) { print " selected"; } ?>> 30 Days</option>

                        <option value="60"<?php if ($myDays == 60) { print " selected"; } ?>> 60 Days</option>

                        <option value="90"<?php if ($myDays == 90) { print " selected"; } ?>> 90 Days</option>

                        <option value="all"<?php if ($myDays == "all") { print " selected"; } ?>> All Posts</option>

                    </select>

                    <input type="Submit" value="Go!" class="smbutton">

                    </td>

                    <input type="Hidden" name="page" value="<?php print $startRow; ?>" />

                    <input type="Hidden" name="display" value="<?php print $intDisplayNum; ?>" />

                    </form>

                    <form action="topics_bb.php" method="get">

                    <input type="Hidden" name="forum" value="<?php print $_GET["forum"]; ?>" />

                    <input type="Hidden" name="days" value="<?php print $myDays; ?>" />

                    <input type="Hidden" name="page" value="<?php print $startRow; ?>" />

                    <td class="smalltxt">

                    <b>Display:</b><br>

                    <select name="display" class="dropdown">

                        <option value="15"<?php if ($intDisplayNum == 15) { print " selected"; } ?>> 15 per page</option>

                        <option value="20"<?php if ($intDisplayNum == 20) { print " selected"; } ?>> 20 per page</option>

                        <option value="25"<?php if ($intDisplayNum == 25) { print " selected"; } ?>> 25 per page</option>

                        <option value="50"<?php if ($intDisplayNum == 50) { print " selected"; } ?>> 50 per page</option>

                        <option value="75"<?php if ($intDisplayNum == 75) { print " selected"; } ?>> 75 per page</option>

                        <option value="100"<?php if ($intDisplayNum == 100) { print " selected"; } ?>> 100 per page</option>

                    </select>

                    <input type="Submit" value="Go!" class="smbutton">

                    </td>

                    </form>

                    <form name="myChoiceBottom">

                    <td class="smalltxt">

                    <b>Forums:</b><br>

                    <!--- display our output --->

                    <select name="forum" onchange="location.href='/forum/topics_bb.php?forum=' + this.value" class="dropdown">";

                    <?php

                    // loop through our categories and grab the forums

                    for ($i = 0; $i < count($arrCats); $i++) {

                        ?>

                        <option value="<?php print $arrCats[$i][0]; ?>"><?php print $arrCats[$i][1]; ?></option>

                        <?php

                        // show our categories

                        show_forums($arrCats[$i][0], '&nbsp;&nbsp;&nbsp;&nbsp;', $qryForum["ID"], $_SESSION["AccessLevel"], $dbConn);

                    }

                    ?>

                    </select>

                    <input type="Button" value="Go!" class="smbutton" onClick="location.href='/forum/topics_bb.php?forum=' + document.myChoiceTop.forum.options[document.myChoiceTop.forum.selectedIndex].value">

                    </td>

                                </form>

                </tr>

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

    // if an extrernal URL is set, open a new window

    if (strlen($qryForum["ExternalURL"])) {

        print "

        <script language=\"JavaScript\">

        window.open(\"" . $qryForum["ExternalURL"] . "\");

        </script>\n";

    }

    

    // include our footer

    require("footer.php");

?>

