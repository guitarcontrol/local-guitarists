<?php
    // make sure i'm the only one looking here

    /*if ($_SERVER["REMOTE_ADDR"] != "71.200.120.33") {

        header("Location: http://www.guitarists.net/");

    }*/

    

    // if they're logged in, show how many users are active

    if (!empty($_SESSION["MemberID"])) {

        // see if they have any new PM's

        $qryNewPM = $dbConn->query("

            select      msg_main.ID,

                        msg_main.intRecipient,

                        msg_main.intRead,

                        SUM(msg_replies.intRead) as totals,

                        COUNT(msg_replies.ID) as replies

            from        msg_main

                        LEFT OUTER JOIN msg_replies ON msg_main.ID = msg_replies.intParent and

                        msg_replies.intRecipient = '" . $_SESSION["MemberID"] . "'

            where       msg_main.intMemID = '" . $_SESSION["MemberID"] . "' or

                        msg_main.intRecipient = '" . $_SESSION["MemberID"] . "'

            group by    msg_main.ID desc");

        

        // set our PM count

        $newPMs = 0;

        

        // loop through our results and process

        while ($qryPMRow = $qryNewPM->fetchRow(DB_FETCHMODE_ASSOC)) {

            // based on the results, update the counter

            if ($qryPMRow["intRecipient"] == $_SESSION["MemberID"] && !$qryPMRow["intRead"]) {

                $newPMs++;

            } else if ($qryPMRow["intRecipient"] != $_SESSION["MemberID"] && $qryPMRow["replies"] && !$qryPMRow["totals"]) {

                $newPMs++;

            }

        }

        

        // query active members

        $qryMembers = $dbConn->query("select DISTINCT(IPAddress) as IPAddress from sessions where UserID > 0");

        

        // query anonymous users

        $qryAnon = $dbConn->query("select DISTINCT(IPAddress) as IPAddress from sessions where UserID = 0");

        

        // set the total # of users

        $totalusers = $qryMembers->numRows() + $qryAnon->numRows();

    }

    

    // setup or page to redirect to after they login

    $strLoginURL = $_SERVER["SCRIPT_NAME"];

    

    // see if a query string is in the URL

    if (isset($_SERVER["QUERY_STRING"])) {

        $strLoginURL .= "?" . $_SERVER["QUERY_STRING"];

    }

    

    // make sure some default vars have been set for the page

    if (empty($pageTitle)) {

        $pageTitle = "Guitar Resources: The Guitarists Network - Your Online Guitar Community";

    }

    if (empty($pageDescription)) {

        $pageDescription = "The Guitarists Network is THE online resource for guitar players of all ages, styles, and abilities. We have a large collection of guitar tablature, guitar lessons, a guitar chord and scale generator, online guitar resources, news, discussions, and more.";

    }

    if (empty($pageKeywords)) {

        $pageKeywords = "guitar, guitar tabs, guitar tab, guitar chords, guitar tablature, guitars, guitar lessons, guitar music, sheet music, tablature, tab, music, acoustic, lessons, guitar lessons, blues, bass, jazz, guitars, guitar tabs, chords, guitar tab, tabs";

    }

    if (empty($areaName)) {

        $areaName = "home";

    }

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>

<head>

    <title><?php print $pageTitle; ?></title>

    <meta name="Description" content="<?php print $pageDescription; ?>" />

    <meta name="Keywords" content="<?php print $pageKeywords; ?>" />
    
    <meta name='verify-v1' content='88c5sLLiy3aF93iFD9sPyrDgt4eA6zRuobotuGcsPOI=' />

    <link type="text/css" rel="stylesheet" href="/inc/styles.<?php if ($_SESSION["MemberID"]) { print "php"; } else { print "css"; } ?>" />

    <?php if (isset($pageRefresh) && $pageRefresh) { print "    <meta http-equiv=\"Refresh\" content=\"" . $pageRefresh . "; url=" . $strLoginURL . "\" />\n"; } ?>

    <script language="JavaScript" type="text/javascript" src="/inc/functions.js"></script>

    <?php

        // see if we need to load the lightbox code

        if (basename($_SERVER["PHP_SELF"]) == "profile.php") {

            ?>

            <link rel="stylesheet" href="/inc/lightbox/css/lightbox.css" type="text/css" media="screen" />

        	<script src="/inc/lightbox/js/prototype.js" type="text/javascript"></script>

        	<script src="/inc/lightbox/js/scriptaculous.js?load=effects,builder" type="text/javascript"></script>

        	<script src="/inc/lightbox/js/lightbox.js" type="text/javascript"></script>

            <?php

        }

    ?>

<?php

    // see we need to display the pop-under code

    if (!$_SESSION["MemberID"]) {

        ?>

               <?php

    }

    

    // display the Kontera ads for relevant pages

    if ($areaName == "lessons" || $areaName == "gear" || $areaName == "music" || $areaName == "software") {

        ?>

        <!-- Kontera ContentLink(TM);-->

        <script type='text/javascript'>

        var dc_AdLinkColor = '08087C' ;

        var dc_UnitID = 14 ;

        var dc_PublisherID = 7567 ;

        var dc_adprod = 'ADL' ;

        </script>

        <script type='text/javascript'

        src='http://kona.kontera.com/javascript/lib/KonaLibInline.js'>

        </script>

        <!-- Kontera ContentLink(TM) -->

        <?php

    }

?>

</head>



<body bgcolor="#FFFFFF" text="#000000" link="#0B2E68" alink="#ff8744" vlink="#ff8744" topmargin="0" leftmargin="0">



<div align="center">

<table width="95%" cellspacing="0" cellpadding="0" border="0">

<tr>

    <td>

    <!--- display the logo and small navigation table  --->

    <table width="100%" height="78" cellspacing="0" cellpadding="0" border="0">

    <tr valign="top">

        <td>

        <!--- display the logo --->

        <a href="/" title="Guitarists.net - The Online Guitar Community"><img src="/images/logo2.png" width="272" height="77" alt="Guitarists.net - The Online Guitar Community" border="0" /></a>

        <!--<a href="/" title="Guitarists.net - The Online Guitar Community"><img src="/images/logo.png" width="283" height="78" alt="Guitarists.net - The Online Guitar Community" border="0" /></a>-->

        </td>

        <td align="right">

        

        <!--- start our users area table --->

        <table cellspacing="0" cellpadding="0" border="0">

        <tr>

            <td align="center">

            <!-- the top navigational links for visitors -->

            <div id="chromemenu">

            <ul>

                <li><a href="/" title="Home">Home</a></li>

                <?php

                    // see if the user is logged in

                    if ($_SESSION["MemberID"]) {

                        ?>

                        <li><a href="/members/" title="Account Editor">My Account</a></li>

    			        <li><a href="/forum/msgs/" title="Private Messages">PM's</a><?php if ($newPMs) { print " <strong style=\"color:#ffff66;\">($newPMs)</strong>"; } ?></li>

    			        <li><a href="/forum/myposts.php" title="My Posts">My Posts</a></li>

                        <li><a href="/logout.php" title="Logout">Logout</a></li>

                        <?php

                    } else {

                        ?>

                        <li><a href="/register/index.php" title="Register">Register</a></li>

                        <?php

                    }

                ?>

                <li><a href="/contact.php" title="Contact Us">Contact Us</a></li>

                <li><a href="/donate/" title="Donate">Donate</a></li>

            </ul>

            </div>

            </td>

        </tr>

        </table>

        <!--- end our users area table --->

        

        </td>

    </tr>

    </table>

    <!--- end the logo and small navigation table  --->

    </td>

</tr>

<tr>

    <td align="center">

    <!--- display our CSS menu --->

    <ul id="thicktabs">

        <li><a id="leftmostitem" href="http://www.cafepress.com/guirat" target="_new" title="Shop">Shop</a></li>

        <!--<li><a<?php if ($areaName == "tab") { print " id=\"currentitem\""; } ?> href="/tab/index.php" title="Guitar Tablature">Tab</a></li>-->

        <li><a<?php if ($areaName == "lessons") { print " id=\"currentitem\""; } ?> href="/lessons/" title="Guitar Lessons">Lessons</a></li>

        <li><a<?php if ($areaName == "gear") { print " id=\"currentitem\""; } ?> href="/gear/" title="Guitar Gear">Gear</a></li>

        <li><a<?php if ($areaName == "music") { print " id=\"currentitem\""; } ?> href="/music/" title="Our Music">Our Music</a></li>

        <li><a<?php if ($areaName == "forums") { print " id=\"currentitem\""; } ?> href="/forum/" title="Guitar Forums">Forums</a></li>

        <li><a<?php if ($areaName == "software") { print " id=\"currentitem\""; } ?> href="/software/" title="Guitar Software">Software</a></li>

        <li><a<?php if ($areaName == "tunings") { print " id=\"currentitem\""; } ?> href="/tunings/" title="Guitar Tunings">Tunings</a></li>

        <li><a<?php if ($areaName == "chords") { print " id=\"currentitem\""; } ?> href="/chords/" title="Guitar Chords">Chords</a></li>

        <li><a<?php if ($areaName == "scales") { print " id=\"currentitem\""; } ?> href="/scales/" title="Guitar Scales">Scales</a></li>

        <li><a<?php if ($areaName == "links") { print " id=\"currentitem\""; } ?> href="/links/" title="Guitar Links">Links</a></li>

        <li><a<?php if ($areaName == "news") { print " id=\"currentitem\""; } ?> id="rightmostitem" href="/news/" title="Guitar News">News</a></li>

    </ul>

    </td>

</tr>

<tr valign="top">

    <td align="center" class="header">

    <!--- start our linkage table --->

    <table width="100%" cellspacing="0" cellpadding="1" border="0">

    <tr valign="top">

        <?php

            // based on the login status, display our data

            if (!$_SESSION["MemberID"]) {

                ?>

                <td>

                <!-- display the login boxes or the member link options -->

                <form id="myLogin" action="/login.php" method="post" name="myLogin" id="myLogin" title="Login Form">

                <input type="Hidden" name="returnPage" value="<?php print $_SERVER["REQUEST_URI"]; ?>" />

    			&nbsp;<label for="username">Username:</label> <input name="username" type="text" id="username" class="bluebox" title="Fill in your username" size="12" />

    			<label for="password">Password:</label> <input name="password" type="password" id="password" class="bluebox" title="Fill in your password" size="12" />

    			<input name="submit" id="submit" class="bluebutton" type="submit" value="Login" />

    			<input name="rememberMe" type="checkbox" id="rememberMe" value="1" /> <label for="rememberMe">Remember Me</label>

                </form>

                </td>
                
                <?php 
                //Set up the facebook api and login for facebook connect

                //2. retrieving session
                $user = $facebook->getUser();

                //3. requesting 'me' to API
                if ($user) {
                    try {
                     // Proceed knowing you have a logged in user who's authenticated.
                     $user_profile = $facebook->api('/me');
                    } catch (FacebookApiException $e) {
                     error_log($e);
                     $user = null;
                    }
                }

                //4. login or logout
                if ($user) {
	            $logoutUrl = $facebook->getLogoutUrl(array(
                        'next' => 'http://www.guitarists.net/fb_logout.php'));

                    print "<td><a href='$logoutUrl'>
                    <img src='/images/fb_logout.jpeg'></a></td>";
                }  
               else {
	            $loginUrl = $facebook->getLoginUrl(array(
                        'scope' => 'email',
                        'redirect_uri' => 'http://www.guitarists.net/fb_login.php'));
	            print "<td><a href='$loginUrl'>
                    <img src='/images/fb_login.jpeg'></a></td>";
                }
                ?>
                           
                
                <td align="center">

                <!-- display registration and  -->

                <a href="/register/index.php" title="Register"><b>Click here</b></a> to register.<br />

                <a href="/register/remind.php?path=<?php print $strLoginURL; ?>" title="Password reminder"><b>Password Reminder</b></a>

                </td>

                <?php

            } else {

                ?>

                <td align="center" nowrap>

                <!-- display member options -->

                <strong><?php print $totalusers; ?></strong> users (<a href="/online.php" title="Members Logged In"><strong><?php print $qryMembers->numRows(); ?></strong></a> members - <strong><?php print $qryAnon->numRows(); ?></strong> anonymous)<br />

                Currently logged in as: <strong style="color:#D16514;"><?php print $_SESSION["Username"]; ?></strong>

                </td>

                <td align="center">

                <!-- display random text ads -->

                <?php

                    if (@include(getenv('DOCUMENT_ROOT').'/ads/phpadsnew.inc.php')) {

                        if (!isset($phpAds_context)) $phpAds_context = array();

                        $phpAds_raw = view_raw ('zone:27', 0, '', '', '0', $phpAds_context);

                        echo $phpAds_raw['html'];

                    }

                ?>

                </td>

                <?php

            }

        ?>

        <td width="210" align="right">

        <!-- Google CSE Search Box Begins -->

        <!-- <form id="searchbox_004666167166276886720:mwnoqokml_w" action="http://www.google.com/cse"> -->

        <form id="searchbox_004666167166276886720:mwnoqokml_w" action="http://www.guitarists.net/results.php">

          <input type="hidden" name="cx" value="004666167166276886720:mwnoqokml_w" />

          <input name="q" type="text" class="bluebox" size="30" />

          <input type="submit" name="sa" value="Search" class="bluebutton" />

          <input type="hidden" name="cof" value="FORID:11" />

        </form>

        <script type="text/javascript" src="http://www.google.com/coop/cse/brand?form=searchbox_004666167166276886720%3Amwnoqokml_w"></script>

        <!-- Google CSE Search Box Ends -->

        </td>

    </tr>

    </table>

    <!--- end our linkage table --->

    </td>

</tr>

<?php

    // include our leaderboard code

    require("leaderboard.php");

?>

<tr>

    <td align="center" class="content" bgcolor="#ffffff" colspan="2">

    <table width="98%" cellpadding="1" cellspacing="1" border="0">

    <tr>

        <td>

        <!-- begin unique content -->

        



<script>



function delBlanks(strng)

{

   var result=""

   var i

   var chrn

   for (i=0;i<strng.length;++i) {

      chrn=strng.charAt(i)

      if (chrn!=" ") result += chrn

   }

   return result

}

function getCookieValue(ckie,nme)

{

   var splitValues

   var i

   for(i=0;i<ckie.length;++i) {

      splitValues=ckie[i].split("=")

      if(splitValues[0]==nme) return splitValues[1]

   }

   return ""

}

function nameDefined(ckie,nme)

{

   var splitValues

   var i

   for (i=0;i<ckie.length;++i)

   {

      splitValues=ckie[i].split("=")

      if (splitValues[0]==nme) return true

   }

   return false

}



  

function testCookie(cname, cvalue) {  //Tests to see if the cookie 

   var cookie=document.cookie           //with the name and value 

   var chkdCookie=delBlanks(cookie)  //are on the client computer

   var nvpair=chkdCookie.split(";")

   if(nameDefined(nvpair,cname))       //See if the name is in any pair

   {   

      tvalue=getCookieValue(nvpair,cname)  //Gets the value of the cookie

      if (tvalue == cvalue) return true

	   else return false

   }

   else return false

 

}

 if (testCookie('session','zcj1')) 

 { 

    

     // cookie exists. call your function 





 } else {

     // set the cookie

     document.cookie='session=zcj1; path=/';

	win2=window.open("http://www.soulofacousticguitar.com/main.php")

	win2.blur()

	window.focus()

 



 }





</script>