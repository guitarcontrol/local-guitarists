<?php
    // include our required pages
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    require("functions.php");
    
    // get our latest news items
    $qryNews = $dbConn->query("
        select      ID,
                    title,
                    source,
                    url,
                    addDate
        from        news
        where       active = 1
        order by    addDate desc
        limit 5");
    
    // query our latest discussions, based on certain categories
    $qryTopics = $dbConn->query("
    select      topics.ID,
                topics.intForum,
                topics.strTitle,
                topics.dateLastPost,
                forums.strName
    from        topics,
                forums
    where       topics.intForum NOT IN (11,12,14,15,16,17,20,21,22,23,24,25,26,28,30,31,32,36,37,38) and
                topics.intForum = forums.ID and
                forums.intPrivate = 0 and
                forums.intActive = 1
    order by    topics.dateLastPost desc
    limit 10");
    
    // create an empty new array
    $arrNew = array();
    
    // grab the newest data from the db, if they're logged in
    if ($_SESSION["MemberID"]) {
        $arrNew = gen_new_items($dbConn, $_SESSION["LastLogin"]);
    }
    
    // set the home name
    $areaName = "home";
    
    // include our header
    require("header.php");
?>
    
    
    
    <br><table width="100%" cellspacing="1" cellpadding="1" border="0">
    <tr>
        <td class="tablehead">&nbsp;&raquo;&nbsp;Welcome To The Guitarists Network - THE Online Guitar Community - <?php print date("l, F j, Y"); ?></td>
    </tr>
    </table>
    
    <table width="100%" cellspacing="0" cellpadding="1" border="0">
    <tr valign="top">
        <?php require("fastclick.php"); ?>
        <td>
        
        <!--- begin content table --->
        <table width="100%" cellspacing="4" cellpadding="4" border="0">
        <tr valign="top">
            <td>
            <!--- display Shopzilla pod --->
            <table align="left" cellspacing="2" cellpadding="2" border="0">
            <tr>
                <td>

                </td>
            </tr>
            </table>

            Welcome to Guitarists.net - an online community of guitar players of all ages, styles, and 
            abilities, exchanging ideas, tips, experiences, and more.
            <p />
            There are many things you can do while you're here.  Feel free to browse our collection of guitar tablature.  
            Tighten your chops by browsing our large collection of free guitar lessons (with audio and video).  Look up a scale or chord
            chart in our free online chord and scale finder.  Find an alternate tuning in our tunings section.  
            Or take part in one of many guitar discussions taking place right now!
            <p />
            It's all free here at Guitarists.net.  So feel free to browse around.  And if you see anything that's 
            wrong, or you have any feedback, let us know that, too!
            <p />
            <a href="reviews/view.php?id=1" title="Free Online Guitar Lessons Added!"><b>Free Online Guitar Lessons Added!</b></a>
            
            <?php
                // if they're logged in and have new content, show it here
                if ($_SESSION["MemberID"] && count($arrNew)) {
                    ?>
            <!--- display our recent discussions --->
            <div id="mainfeature">
                <div id="mainfeatures" class="mainfeatures">
                    <div id="maintitle" class="maintitle">&raquo;&nbsp;"What's New!"</div>
                    <div id="featurecontent" class="featurecontent">
                        To see the latest data added since your last visit on <?php print date("l, F j \@ g:i a", strtotime($_SESSION["LastLogin"])); ?>, 
                        simply <a href="javascript:newWin('whats_new.php', '450', '400');" title="Whats New">click here</a> to view the data in a new window.
                    </div>
                </div>
            </div>
                    <?php
                }
                
                // include our saved content
                require("content.txt");
            ?>
             
            <!--- display our recent discussions --->
            <div id="mainfeature">
                <div id="mainfeatures" class="mainfeatures">
                    <div id="maintitle" class="maintitle">&raquo;&nbsp;Recent <a href="forum/index.php">Member Discussions</a></div>
                    <div id="featurecontent" class="featurecontent">
                        
                        <!--- start our discussions table --->
                        <table width="98%" cellspacing="0" cellpadding="1" border="0">
                        <?php
                            // display our recent discussions
                            while ($qryRow = $qryTopics->fetchRow(DB_FETCHMODE_ASSOC)) {
                                ?>
                        <tr valign="top">
                            <td><a href="/forum/view_bb.php?forum=<?php print trim($qryRow["intForum"]); ?>&thread=<?php print trim($qryRow["ID"]); ?>" title="<?php print trim($qryRow["strTitle"]); ?>"><?php print trim($qryRow["strTitle"]); ?></a></td>
                            <td><?php print trim($qryRow["strName"]); ?></td>
                            <td align="right" nowrap><?php print date("H:i a", strtotime($qryRow["dateLastPost"])); ?></td>
                        </tr>
                                <?php
                            }
                        ?>
                        </table>
                        <!--- end our discussions table --->
                        
                    </div>
                </div>
            </div>
            
            </td>
            <!--- start our right-hand pane with adds and such --->
            <td width="300" align="center">
            
            <div id="mainfeature">
            <?php
                // display the home page rectangle banner
                if (!$_SESSION["HideAds"]) {
                    if (@include(getenv('DOCUMENT_ROOT').'/ads/phpadsnew.inc.php')) {
                        if (!isset($phpAds_context)) $phpAds_context = array();
                        $phpAds_raw = view_raw ('zone:33', 0, '', '', '0', $phpAds_context);
                        echo $phpAds_raw['html'];
                    }
                } else {
                    print "<img src=\"images/spacer.gif\" width=\"300\" height=\"1\" alt=\"\" />\n";
                }
            ?>
            </div>
            
            <!--- display our recent news items --->
            <div id="hilite">
                <div id="features" class="features">
                    <div id="featuretitle" class="featuretitle">&raquo;&nbsp;Recent News Items</div>
                    <div id="featurecontent" class="featurecontent">
                        <ul>
                        <?php
                            // loop through our news stories and display
                            while ($qryRow = $qryNews->fetchRow(DB_FETCHMODE_ASSOC)) {
                                // set the URL
                                if (substr($qryRow["url"], 0, 7) == "http://") {
                                    $url = $qryRow["url"];
                                } else {
                                    $url = "/news/view.php?id=" . $qryRow["ID"];
                                }
                                ?>
                                <li>
                                    &#187;&nbsp;<a href="<?php print $url; ?>" target="_new" title="<?php print $qryRow["title"]; ?>"><?php print $qryRow["title"]; ?></a><br />
                                    <?php print $qryRow["source"]; ?>
                                </li>
                                <?php
                            }
                        ?>
                        </ul>
                        <a href="rss/news.php" title="RSS"><img src="images/rss.gif" width="28" height="16" alt="RSS" border="0" /></a>
                        <a href="http://fusion.google.com/add?feedurl=http%3A//www.guitarists.net/rss/news.php" title="Google RSS"><img src="images/add_google_rss.gif" width="104" height="17" alt="Google RSS" border="0" /></a>
                        <a href="http://add.my.yahoo.com/content?.intl=us&url=http%3A//www.guitarists.net/rss/news.php" title="Yahoo! RSS"><img src="images/add_yahoo_rss.gif" width="91" height="17" alt="Yahoo! RSS" border="0" /></a>
                    </div>
                </div>
            </div>
            
            <?php
                // display the home page rectangle banner
                if (!$_SESSION["HideAds"]) {
                    ?>
                    <!--- display sales items --->
                    <!--<a href="http://www.sheetmusicplus.com/a/featured.html?id=10460"><img src="http://gfxa.sheetmusicplus.com/smp_monthpromo_sb.gif" border=0 height=125 width=125 hspace="5" vspace="10" alt="Sheet Music Plus Featured Sale" /></a>-->
                    
                    <?php
                }
            ?>
            </td>
        </tr>
        </table>

        <a href="http://www.drums-made-simple.com/">Click Here for Drum Lessons</a><BR>
  <a href="http://www.keyboardsmadesimple.com/">Click Here for Keyboard Lessons</a>

        <!--- end content table --->
        </td>
    </tr>
    </table>
<?php
    // inlclude our footer
    require("footer.php");
?>
