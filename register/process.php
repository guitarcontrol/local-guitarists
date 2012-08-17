<?php
    /*
        process.php
        
        This adds the main info into the database and creates the users 
        account.  First, we'll make sure the name isn't already taken.
        If not, we'll add them to 'members' and 'about'.  Then we'll 
        generate their registration key and send them an email.
    */
    
    // include our needed file(s)
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("12all_db.php");
    require("sessions.php");
    require("ads.php");
    require("functions.php");
    require("captcha/php-captcha.inc.php");
    
    // update our session text array with the submitted data
    $subText = array(
                 "strFName"       => trim($_POST["strFName"]),
                 "strLName"       => trim($_POST["strLName"]),
                 "strEmail"       => trim($_POST["strEmail"]),
                 "strEmail2"      => trim($_POST["strEmail2"]),
                 "strUsername"    => trim($_POST["strUsername"]),
                 "strPassword"    => trim($_POST["strPassword"]),
                 "strPassword2"   => trim($_POST["strPassword2"]),
                 "intAge"         => trim($_POST["intAge"]),
                 "intGender"      => trim($_POST["intGender"]),
                 "strCity"        => trim($_POST["strCity"]),
                 "intState"       => trim($_POST["intState"]),
                 "strZipCode"     => trim($_POST["strZipCode"]),
                 "intCountry"     => trim($_POST["intCountry"]),
                 "intSendEmail"   => trim($_POST["intSendEmail"]),
                 "intPrivate"     => trim($_POST["intPrivate"])
                    );
    
    // update our session text to remember it
    $_SESSION["SubText"] = $subText;
    
    // verify the passed code is correct
    if (!PhpCaptcha::Validate($_POST["captcha"])) {
        print "
        <script language=\"JavaScript\">
        alert(\"Please verify the validation text you entered and try again.  It does not appear to be valid.\");
        location.replace(\"index.php?error=1\");
        </script>";
        exit();
    }
    
    // if the CAPTCHA #'s don't match, go back
    /*if ($_SESSION["captcha"] != $_POST["captcha"]) {
        print "
        <script language=\"JavaScript\">
        alert(\"Please verify the number you entered.  It does not appear to be valid.\");
        location.replace(\"index.php?error=1\");
        </script>";
        exit();
    }*/
    
    // make sure there are no curse words in their username
    $result = curseFilter($_POST["strUsername"]);
    
    if ($result) {
        print "
        <script language=\"JavaScript\">
        alert(\"Please refrain from using curse words in your username.\");
        location.replace(\"index.php?error=2\");
        </script>";
        //header("Location: /register/index.php");
        exit();
    }
    
    // verify that the username isn't taken
    $qryName = $dbConn->query("
        select    ID
        from    members
        where    strUsername = '" . trim($_POST["strUsername"]) . "'");
    
    // if a record was found, stop now
    if ($qryName->numRows()) {
        print "
        <script language=\"JavaScript\">
        alert(\"The username '" . trim($_POST["strUsername"]) . "' is already taken by another member.\\n\" +
              \"Please choose another name and try again.\");
        location.replace(\"index.php?error=3\");
        </script>";
        exit();
    }
    
    // process the query
    $qryEmail = $dbConn->query("
        select    ID
        from    members
        where    strEmail = '" . trim($_POST["strEmail"]) . "'");
    
    // if a record was found, stop now
    if ($qryEmail->numRows()) {
        print "
        <script language=\"JavaScript\">
        alert(\"The email address '" . trim($_POST["strEmail"]) . "' has already been registered\\n\" +
              \"for an account. Please try again.\");
        location.replace(\"index.php?error=4\");
        </script>";
        exit();
    }
    
    $regKey = regkey();
    $qryAvail["intID"] = 0;
    
    // get the first available ID from available (if any)
    $qryAvail = $dbConn->getRow("
        select  min(intID) as intID
        from    available",
        DB_FETCHMODE_ASSOC);
    
    // start our SQL statement
    $sqlText = "insert into members (";
    
    // create our SQL, based on the results of an available ID
    if ($qryAvail["intID"]) {
        $sqlText .= "
            ID,";
    }
    
    // continue the SQL statement
    $sqlText .= "
            strFName,
            strLName,
            strUsername,
            strPassword,
            strEmail,
            strIP,
            dateJoined,
            dateLVisit,
            strRegKey,
            intSendEmail,
            intPrivate,
            strPlainText
        ) values ( ";
    
    // create our SQL, based on the results of an available ID
    if ($qryAvail["intID"]) {
        $sqlText .= $qryAvail["intID"] . ",";
    }
    
    // continue the SQL statement
    $sqlText .= "
            '" . trim(addslashes($_POST["strFName"])) . "',
            '" . trim(addslashes($_POST["strLName"])) . "',
            '" . trim(addslashes($_POST["strUsername"])) . "',
            '" . trim(addslashes(md5($_POST["strPassword"]))) . "',
            '" . trim($_POST["strEmail"]) . "',
            '" . $_SERVER["REMOTE_ADDR"] . "',
            Now(),
            Now(),
            '" . $regKey . "',
            '" . trim($_POST["intSendEmail"]) . "',
            '" . trim($_POST["intPrivate"]) . "',
            '" . trim(addslashes($_POST["strPassword"])) . "'
        )";
    
    // build our main addition sql code, to add them to 'members'
    $qryAdd = $dbConn->query($sqlText);
    
    // build our sql to get the last added ID
    if ($qryAvail["intID"]) {
        // set the ID we've already aquired
        $intNewID = $qryAvail["intID"];
    } else {
        // set the ID for use later
        $intNewID = mysql_insert_id($dbConn->connection);
    }
    
    // creat the sql to add their basic info to 'about'
    $qryAboutResults = $dbConn->query("
        insert into about (
            intMemID,
            strCity,
            intState,
            intCountry,
            intGender,
            intAge
        ) values (
            '" . $intNewID . "',
            '" . trim(ucfirst(strtolower($_POST["strCity"]))) . "',
            '" . trim($_POST["intState"]) . "',
            '" . trim($_POST["intCountry"]) . "',
            '" . $_POST["intGender"] . "',
            '" . $_POST["intAge"] . "'
        )");
    
    // delete the ID from available
    if (strlen($qryAvail["intID"])) {
        $qryKillAvail = $dbConn->query("
            delete
            from    available
            where    intID = " . $qryAvail["intID"]);
    }
    
    // alert mods about any new users who have similar IP's of banned users.  first, break their IP down
    $arrIP = explode(".", $_SERVER["REMOTE_ADDR"]);
    
    // specify the IP to check for
    $IPAddress = $arrIP[0] . "." . $arrIP[1] . "." . $arrIP[2];
    
    // check for any banned users with the similar IP
    $qryLikeIP = $dbConn->query("
        select      strUsername,
                    strIP
        from        members
        where       strIP LIKE '" . $IPAddress . "%' and
                    intBanned = 1
        order by    strUsername");
    
    // if we found any, write them to the db
    if ($qryLikeIP->numRows()) {
        // set our title
        $banTitle = "New User Watch - " . trim($_POST["strUsername"]);
        
        // set the body of our message
        $banText = "The user <b>" . trim($_POST["strUsername"]) . "</b> recently registered with the IP of <b>" . $_SERVER["REMOTE_ADDR"] . "</b>.  This IP matches the following banned users:\n\n";
        
        // add all of the IP's and users we need
        while ($qryIPRow = $qryLikeIP->fetchRow(DB_FETCHMODE_ASSOC)) {
            $banText .= $qryIPRow["strUsername"] . ": " . $qryIPRow["strIP"] . "\n";
        }
        
        // add the post to the database
        $qryAddPost = $dbConn->query("
            insert into topics ( 
                intForum, 
                strTitle, 
                intReplies, 
                intMemID,
                datePosted,
                txtPost,
                dateLastPost,
                bitReply,
                intSticky
            ) values ( 
                30, 
                '" . $banTitle . "', 
                0, 
                1,
                Now(),
                '" . addslashes($banText) . "',
                Now(),
                0,
                0
            )");
    }
    
    // build our email text to send to the user
    $emailText = "Hello, " . trim(ucfirst(strtolower($_POST["strFName"]))) . ", and welcome!\n\n";
    $emailText .= "Welcome to The Guitarists Network, an online community for guitar players\n";
    $emailText .= "from around the world. We would just like to take this opportunity to\n";
    $emailText .= "welcome you to our small community (though it's growing daily).\n\n";
    $emailText .= "For your records, here's what you supplied to us:\n\n";
    $emailText .= "Name: " . trim(ucfirst(strtolower($_POST["strFName"]))) . " " . trim(ucfirst(strtolower($_POST["strLName"]))) . " (". trim($_POST["strEmail"]) . ")\n\n";
    $emailText .= "User ID: #" . $intNewID . "\n";
    $emailText .= "Username: " . trim($_POST["strUsername"]) . "\n";
    $emailText .= "Password: " . trim($_POST["strPassword"]) . "\n";
    $emailText .= "Validation Code: " . trim($regKey) . "\n\n";
    $emailText .= "Your account has been created, but you still need to validate it. Click\n";
    $emailText .= "on the following link to validate your account:\n\n";
    $emailText .= "http://www.guitarists.net/register/validate.php?id=" . $intNewID . "&key=" . trim($regKey) . "\n\n";
    $emailText .= "- or -\n";
    $emailText .= "<a href=\"http://www.guitarists.net/register/validate.php?id=" . $intNewID . "&key=" . trim($regKey) . "\">http://www.guitarists.net/process/validate.php?id=" . $intNewID . "&actkey=" . trim($regKey) . "</a>\n\n";
    $emailText .= "If neither of these work, go here:\n\n";
    $emailText .= "http://www.guitarists.net/register/manual.php\n\n";
    $emailText .= "And enter your ID, validation code, and password in this email (listed above).\n\n";
    $emailText .= "Please note: ALL accounts not validated within 48 hours are automatically\n";
    $emailText .= "deleted from the database. If this happens, simply re-register.\n\n";
    $emailText .= "If you ever need to change any of the above information, you can simply go to:\n\n";
    $emailText .= "http://www.guitarists.net/members/\n\n";
    $emailText .= "Here you can change any of your information online, including your password.\n";
    $emailText .= "And if you're like me and forget your password alot, we can also email it\n";
    $emailText .= "to you from there.\n\n";
    $emailText .= "To easily get acquainted with other members of the site, we highly recommend\n";
    $emailText .= "you join the new members forum and introduce yourself.  This will allow other\n";
    $emailText .= "members to see that you joined, and help you out, as needed.  You can visit\n";
    $emailText .= "new members forum here:\n\n";
    $emailText .= "http://www.guitarists.net/forum/topics.php?forum=37\n\n";
    $emailText .= "We would also like to take this opportunity to ask you for help. Why not go\n";
    $emailText .= "to the \"Gear\" section and post reviews of all of your equipment - even when\n";
    $emailText .= "it may already be listed. You may be able to help someone in the future.\n\n";
    $emailText .= "http://www.guitarists.net/gear/index.php\n\n";
    $emailText .= "Also, if you find any good web sites, post them in our \"Resources\" section. And\n";
    $emailText .= "don't forget to join in a discussion in our \"Forums\" section.\n\n";
    $emailText .= "So again, welcome to our community. We look forward to your participation.\n\n";
    $emailText .= "The Guitarists Network Staff\n";
    $emailText .= "http://www.guitarists.net/\n";
    $emailText .= date("l, F j, Y \@ g:i A");
    
    // send the email to the new member
    mail(trim($_POST["strEmail"]),
         "Guitarists.net Registration Info",
         $emailText,
         "From: member.support@guitarists.net\r\n" .
         "Reply-To: member.support@guitarists.net");
    
    // clear out the subtext array
    $_SESSION["SubText"] = "";
    
    // clear out the CAPTCHA data
    unset($_SESSION["CAPTCHATXT"]);
    $_SESSION["CAPTCHATXT"] = "";
    
    // include a header file
    require("header.php");
?>
    
    <br>
    <div align="center">
    <table width="100%" border="0" cellspacing="0" cellpadding="2">
    <tr>
        <td class="tablehead">&nbsp;&raquo;&nbsp;<a href="/index.php">Home</a>&nbsp;&raquo;&nbsp;Registration Successful</td>
    </tr>
    </table>
    
    <table width="100%" border="0" cellspacing="0" cellpadding="2">
    <tr valign="top">
        <td align="center">
        
        <!--- content table --->
        <table width="720" cellspacing="1" cellpadding="2" border="0">
        <tr>
            <td>
            <b style="font-size: 16px;">Congratulations!</b>
            <p>
            Your account has been created with the information provided, and an email 
            has been sent to <b><?php print $_POST["strEmail"]; ?></b> to confirm the data 
            submitted. Inside that email is a link with your activation key. It will look 
            similar to the following:
            <p>
            .../validate.php?id=123456&amp;key=1A2B3C4D5E6F7G8
            <p>
            <b>Important!</b><br>
            You    will need to click on that link to validate your account. Once you 
            validate the account, you will be ready to go. You will not be able to 
            login to the account until it has been validated. If your account has not 
            be validated in 48 hours, the account will be deleted automatically.
            <p>
            Once you have validated your account, you can change all of your 
            account information by visiting our <a href="/members/index.php"><b>Members 
            Area</b></a>. There you have access to all of your data, plus new features, 
            like favorites, member search, and more!
            <p>
            <b>Your Bio Information</b><br>
            Once you have validated your account, go to our <a href="/members/index.php"><b>Members Area</b></a> 
            and update your playing information, influences, and more.  From there you can also 
            post new lessons, tab, tunings, and more.
            <p>
            <b>Guitarists.net Apparel</b><br>
            Our <a href="/shop/index.php"><b>online store</b></a> is now open.  And once you 
            validate your account, you can get 10% off of your order!
            <p>
            So, again, welcome to the network! And thank you for your support. And if you 
            feel generous, and would like to donate a few dollars to our cause, we would 
            be happy to accept it.  And a donation of $10.00 or more will allow you 
            the choice whether you see our banners or not.
            <p>
            <table width="480" cellspacing="2" cellpadding="2" border="0">
            <tr valign="top">
                <td width="80"><a href="https://www.paypal.com/xclick/business=donate%40guitarists.net&item_name=%245+Donation&item_number=DON5&amount=5.00&no_shipping=1&return=http%3A//www.guitarists.net/donate/thanks.php&cancel_return=http%3A//www.guitarists.net/donate/index.php&no_note=0&tax=0&currency_code=USD"><img src="/donate/images/paypal5.gif" width="62" height="31" border="0"></a> </td>
                <td width="80"><a href="https://www.paypal.com/xclick/business=donate%40guitarists.net&item_name=%2410+Donation&item_number=DON10&amount=10.00&no_shipping=1&return=http%3A//www.guitarists.net/donate/thanks.php&cancel_return=http%3A//www.guitarists.net/donate/index.php&no_note=0&tax=0&currency_code=USD"><img src="/donate/images/paypal10.gif" width="62" height="31" border="0"></a> </td>
                <td width="80"><a href="https://www.paypal.com/xclick/business=donate%40guitarists.net&item_name=%2420+Donation&item_number=DON20&amount=20.00&no_shipping=1&return=http%3A//www.guitarists.net/donate/thanks.php&cancel_return=http%3A//www.guitarists.net/donate/index.php&no_note=0&tax=0&currency_code=USD"><img src="/donate/images/paypal20.gif" width="62" height="31" border="0"></a> </td>
                <td width="80"><a href="https://www.paypal.com/xclick/business=donate%40guitarists.net&item_name=%2450+Donation&item_number=DON50&amount=50.00&no_shipping=1&return=http%3A//www.guitarists.net/donate/thanks.php&cancel_return=http%3A//www.guitarists.net/donate/index.php&no_note=0&tax=0&currency_code=USD"><img src="/donate/images/paypal50.gif" width="62" height="31" border="0"></a> </td>
                <td width="80"><a href="https://www.paypal.com/xclick/business=donate%40guitarists.net&item_name=Donation&item_number=DONANY&no_shipping=1&return=http%3A//www.guitarists.net/donate/thanks.php&cancel_return=http%3A//www.guitarists.net/donate/index.php&no_note=0&tax=0&currency_code=USD"><img src="/donate/images/paypalany.gif" width="62" height="31" border="0"></a></td>
            </tr>
            </table>
            <p>
            We also take contributions of other types, like helping other players in our 
            <a href="/forum/index.php"><b>forums</b></a>, by submitting <a href="/gear/index.php"><b>gear reviews</b></a>, 
            <a href="/lessons/index.php"><b>lessons</b></a>, <a href="/tunings/index.php"><b>tunings</b></a>, 
            and more.  We can use all the help we can get!
            </td>
        </tr>
        </table>
        
        </td>
    </tr>
    </table>
    </div>
    
<?php
    // include the footer
    require("footer.php");
?>
