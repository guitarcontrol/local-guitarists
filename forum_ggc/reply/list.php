<?php
    /*
        list.php
        
        This script will allow a user to view previous replies to a thread so they can easily quote items
        that they may have forgotten.
    */
    ini_set("display_errors", "on");
    error_reporting(E_ALL);
    // include our needed file(s)
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("functions.php");
    require("HTML/BBCodeParser.php");
    
    // get the forum title and topic title from the db/
    $qryMain = $dbConn->getRow("
        select      topics.ID as topicID,
                    topics.strTitle,
                    topics.datePosted,
                    topics.txtPost,
                    topics.bitReply, 
                    topics.intMemID,
                    topics.txtEdited,
                    members.ID,
                    members.strUsername,
                    members.txtSignature,
                    members.intBanned,
                    members.strIP,
                    about.intCountry,
                    forums.ID as forumID,
                    forums.strName,
                    forums.intPrivate,
                    forums.intActive
        from        topics,
                    members,
                    about,
                    forums
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
        print "Topic cannot be found.\n";
        exit();
    }
    
    // if only mods can view this thread, stop now
    if (!$qryMain["intActive"] && $_SESSION["AccessLevel"] < 90) {
        print "This topic can only be viewed by moderators.\n";
        exit();
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
                    members.strUsername,
                    members.strIP,
                    members.txtSignature,
                    about.intCountry
        from        replies,
                    members,
                    about
        where       replies.intTopic = " . $dbConn->quote($_GET["thread"]) . " and 
                    replies.intMemID = members.ID and
                    members.ID = about.intMemID
        order by    replies.datePosted desc");
    
    // set our line break
    $crlf = chr(10);
    
    // create our BB Code parser to use later
    $parser = new HTML_BBCodeParser(parse_ini_file("../BBCodeParser.ini"));
?><html>
<head>
	<title>Thread Viewer</title>
    <link type="text/css" rel="stylesheet" href="/inc/styles.php">
</head>

<body bgcolor="#ffffff" text="#000000" link="#08087C" alink="#cc3333" vlink="#1A5C8D" background="/images/bground.gif" topmargin="0" leftmargin="0">

<!--- begin layout file --->
<table width="100%" cellspacing="1" cellpadding="3" border="0" bgcolor="#ffffff">
<?php
    // output our replies, based on the page chosen
    while ($qryRow = $qryReplies->fetchRow(DB_FETCHMODE_ASSOC)) {
        ?>
        <tr>
            <td bgcolor="#e5e5e5" colspan="2"><b style="color: #08087C">
            <?php
            // see if a title was entered (or not
            if (!strlen($qryRow["strTitle"])) {
                print "...";
            } else {
                print $qryRow["strTitle"];
            }
            ?></b></td>
        </tr>
        <tr valign="top">
            <td bgcolor="#f6f6f6" class="smalltxt" nowrap>
            <b style="font-size: 12px;"><?php print $qryRow["strUsername"]; ?></b>
            </td>
            <td>
            <?php
                // see if they have an old quote
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
                
                // parse the BB Code in our text
                $msgText = $parser->qParse($qryRow["txtReply"]);
                
                // swap out our quote text
                $msgText = str_replace("<q>", "<table align=right border=0 width=95%><tr><td>", $msgText);
                $msgText = str_replace("</q>", "</td></tr></table><br clear=all />", $msgText);
                
                // parse our smilies
                $msgText = smilies2($msgText,'0');
                
                // see if we need to mark search terms
                if (!empty($_GET["mark"])) {
                    $msgText = str_replace($_GET["mark"], "<b style=\"color: red;\">" . $_GET["mark"] . "</b>", $msgText);
                }
                
                // display the normal stuff
                print str_replace($crlf, "<br>" . chr(10), $msgText) . "
                <br>";
            ?></td>
        </tr>
        <tr>
            <td colspan="2"><br></td>
        </tr>
        <?php
    }
?>
<tr>
    <td bgcolor="#e5e5e5" colspan="2"><b style="color: #08087C">
    Original Post <?php print $qryMain["strTitle"]; ?>
    </b></td>
</tr>
<tr valign="top">
    <td bgcolor="#f6f6f6" width="130" class="smalltxt" nowrap>
    <b style="font-size: 12px;"><?php print $qryMain["strUsername"]; ?></b>
    </td>
    <td>
    <?php
        // parse the BB Code in our text
        $msgText = $parser->qParse($qryMain["txtPost"]);
        
        // parse our smilies
        $msgText = smilies2($msgText,'0');
        
        // display the normal stuff
        print str_replace($crlf, "<br>" . chr(10), $msgText) . "
        <br>";
    ?>
    </td>
</tr>
</table>

</body>
</html>
