<?php
    /*
        index.php
        
        This is the starting script for the registration process.  From 
        here we grab the pertinent info for the user.  So, no matter 
        if they add no other bio information, we'll have the main stuff.
    */
    
    // include our needed file(s)
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    
    // query our states from the db
    $qryStates = $dbConn->query("
        select      ID,
                    strName
        from        states
        order by    strName");
    
    // query our states from the db
    $qryCountries = $dbConn->query("
        select      *
        from        countries
        order by    intSort,
                    strCountry");
    
    // see if this is there first hit to the page
    if (empty($_GET["error"])) {
        // set our sesion fields to help track submissions
        $_SESSION["SubText"] = array(
                                 "strFName" => "",
                                 "strLName" => "",
                                 "strEmail" => "",
                                 "strEmail2" => "",
                                 "strUsername" => "",
                                 "strPassword" => "",
                                 "strPassword2" => "",
                                 "intAge" => 13,
                                 "intGender" => "",
                                 "strCity" => "",
                                 "intState" => 8,
                                 "strZipCode" => "",
                                 "intCountry" => 213,
                                 "intSendEmail" => 1,
                                 "intPrivate" => 0);
    }
    
    // set our page variables
    $pageTitle = "Guitar Resources: Member Registration: Welcome!";
    $pageDescription = "Fill in our form to become a member of the Guitarists Network. It's simple, easy, and, best of all, FREE!";
    $pageKeywords = "";
    $areaName = "registration";
    
    // include our header
    require("header.php");
?>

    <br>
    <table width="100%" border="0" cellspacing="0" cellpadding="2">
    <tr>
        <td class="tablehead">&nbsp;&raquo;&nbsp;<a href="/index.php">Home</a>&nbsp;&raquo;&nbsp;Register To Be a Member</td>
    </tr>
    </table>
    
    <table width="100%" border="0" cellspacing="0" cellpadding="2">
    <form name="register" action="process.php" method="post" onSubmit="return regForm()">
    <tr valign="top">
        <?php if ($adPlace == 1) { require("fastclick.php"); } ?>
        <td>
        <!--- content table --->
        Welcome to the Guitarists Network, and thank you for your interest in joining 
        our growing community. Please complete the <b>entire</b> form below to continue.  Please 
        feel free to review our <a href="/privacy.php"><b>privacy policy</b></a> if 
        you're concerned about entering this data.
        <p />
        
        <table width="100%" cellspacing="1" cellpadding="1" border="0">
        <tr>
            <td class="tablehead">&nbsp;&raquo;&nbsp;Account Information</td>
        </tr>
        </table>
        
        <table cellspacing="1" cellpadding="1" border="0">
        <tr>
            <td align="right"><b>Name:</b></td>
            <td colspan="3">
            <input type="text" name="strFName" value="<?php print trim($_SESSION["SubText"]["strFName"]); ?>" maxlength="30" size="15" class="input">
            <input type="text" name="strLName" value="<?php print trim($_SESSION["SubText"]["strLName"]); ?>" maxlength="35" size="15" class="input">
            </td>
        </tr>
        <tr>
            <td align="right"><b>Email:</b></td>
            <td><input type="text" name="strEmail" value="<?php print trim($_SESSION["SubText"]["strEmail"]); ?>" maxlength="80" size="25" class="input"></td>
            <td align="right"><b>Confirm:</b></td>
            <td><input type="text" name="strEmail2" value="<?php print trim($_SESSION["SubText"]["strEmail2"]); ?>" maxlength="80" size="25" class="input"></td>
        </tr>
        <tr valign="top">
            <td></td>
            <td colspan="3" class="smalltxt">
            It's important you supply a valid email address. Your registration key will be<br>
            generated and then emailed to you. You'll need this to activate your account.
            </td>
        </tr>
        <tr>
            <td colspan="4"><br></td>
        </tr>
        <tr>
            <td align="right"><b>Username:</b></td>
            <td colspan="3"><input type="text" name="strUsername" value="<?php print trim($_SESSION["SubText"]["strUsername"]); ?>" size="20" maxlength="20" class="input"></td>
        </tr>
        <tr valign="top">
            <td></td>
            <td colspan="3" class="smalltxt">
            This is the name people will refer to you as in the forums, chat, and other<br>
            areas.  Please choose a name you will not want to change later.
            </td>
        </tr>
        <tr>
            <td colspan="4"><br></td>
        </tr>
        <tr>
            <td align="right"><b>Password:</b></td>
            <td><input type="password" name="strPassword" value="<?php print trim($_SESSION["SubText"]["strPassword"]); ?>" size="20" maxlength="20" class="input">
             <td align="right"><b>Confirm:</b></td>
            <td colspan="3"><input type="password" name="strPassword2" value="<?php print trim($_SESSION["SubText"]["strPassword2"]); ?>" size="20" maxlength="20" class="input"></td>
        </tr>
        <tr>
            <td></td>
            <td colspan="3" class="smalltxt">Between 6 and 20 characters in length.</td>
        </tr>
        <tr>
            <td colspan="4"><br></td>
        </tr>
        <tr>
            <td align="right"><b>Age:</b></td>
            <td>
            <select name="intAge" class="dropdown">
            <?php
                // loop through and display our numbers
                for ($i = 13; $i < 100; $i++) {
                    print "
                    <option value=\"" . $i . "\"";
                    if ($i == $_SESSION["SubText"]["intAge"]) {
                        print " selected";
                    }
                    print ">" . $i . "</option>";
                }
            ?>
            </select>
            <td align="right"><b>Gender:</b></td>
            <td>
            <input type="Radio" name="intGender" value="0"<?php if ($_SESSION["SubText"]["intGender"] == 0) { print " checked"; } ?>> Male 
            <input type="Radio" name="intGender" value="1"<?php if ($_SESSION["SubText"]["intGender"] == 1) { print " checked"; } ?>> Female
            </font></td>
        </tr>
        <tr>
            <td align="right"><b>City: </b></td>
            <td><input type="text" name="strCity"  value="<?php print trim($_SESSION["SubText"]["strCity"]); ?>" size="15" maxlength="60" class="input"></td>
            <td align="right"><b>State: </b></td>
            <td>
            <select name="intState" class="dropdown">
                <option value="">[ Choose One ]</option>
                <option value="0"> N/A</option>
            <?php
                // loop through our state query
                while ($qryRow = $qryStates->fetchRow(DB_FETCHMODE_ASSOC)) {
                    print "
                    <option value=\"" . $qryRow["ID"] . "\"";
                    if ($qryRow["ID"] == $_SESSION["SubText"]["intState"]) {
                        print " selected";
                    }
                    print "> " . $qryRow["strName"] . "</option>";
                }
            ?>
            </select>
            </td>
        </tr>
        <tr>
            <td align="right"><b>Zip Code: </b></td>
            <td><input type="text" name="strZipCode"  value="<?php print trim($_SESSION["SubText"]["strZipCode"]); ?>" size="5" maxlength="10" class="input"></td>
            <td align="right"><b>Country:</b></td>
            <td>
            <select name="intCountry" class="dropdown">
                <option value="">[ Choose One ]</option>
                <?php
                    // loop through our countries
                    while ($qryRow = $qryCountries->fetchRow(DB_FETCHMODE_ASSOC)) {
                        print "
                        <option value=\"" . $qryRow["ID"] . "\"";
                        if ($qryRow["ID"] == $_SESSION["SubText"]["intCountry"]) {
                            print " selected";
                        }
                        print "> " . $qryRow["strCountry"] . "</option>";
                    }
                ?>
            </select>
            </td>
        </tr>
        <tr>
            <td align="right"><b>Receive Emails: </b></td>
            <td colspan="3">
            <input type="Radio" name="intSendEmail" value="1"<?php if ($_SESSION["SubText"]["intSendEmail"]) { print " checked"; } ?> /> Yes
            <input type="Radio" name="intSendEmail" value="0"<?php if (!$_SESSION["SubText"]["intSendEmail"]) { print " checked"; } ?> /> No
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td colspan="3">
            Occassionally, we'll send out newsletters to our users.  If you wish to not receive these mailings, 
            check "No" above.  Please remember this site is FREE, and these newsletters help keep it that way.
            </td>
        </tr>
        <tr>
            <td align="right"><b>Keep Info Private: </b></td>
            <td colspan="3">
            <input type="Radio" name="intPrivate" value="1"<?php if ($_SESSION["SubText"]["intPrivate"]) { print " checked"; } ?> /> Yes
            <input type="Radio" name="intPrivate" value="0"<?php if ($_SESSION["SubText"]["intPrivate"]) { print " checked"; } ?> /> No
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td colspan="3">
            If you wish other users not to see your personal information on this site, check "No" above.  We'll 
            display your name, city, state, zip, and age otherwise.  We display this to help other users find 
            you in their areas.
            </td>
        </tr>
        <tr>
            <td></td>
            <td colspan="3"><br><textarea name="tos" cols="75" rows="15" wrap="hard" class="input">Terms of Use and "House" Rules

This web site is provided to you free of charge, "as is," by The Guitarists Network. 

By using the information, services and products available through this web site, you are agreeing to the terms and conditions contained herein. 

Liability Disclaimer
The information, services and products available to you on this web site may contain errors and are subject to periods of interruption. While we do our best to maintain the information, services and products we offer on this site, it cannot be held responsible for any errors, defects, lost profits or other consequential damages arising from the use of this site. 

THE GUITARISTS NETWORK PROVIDES THE INFORMATION ON THIS WEB SITE "AS IS," WITH NO WARRANTIES WHATSOEVER. ALL EXPRESS WARRANTIES AND ALL IMPLIED WARRANTIES, INCLUDING WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE, AND NON-INFRINGEMENT OF PROPRIETARY RIGHTS ARE HEREBY DISCLAIMED TO THE FULLEST EXTENT PERMITTED BY LAW. 

IN NO EVENT SHALL THE "NETWORK" BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, CONSEQUENTIAL, SPECIAL AND EXEMPLARY DAMAGES, OR ANY DAMAGES WHATSOEVER, ARISING FROM THE USE OR PERFORMANCE OF THIS WEB SITE OR FROM ANY INFORMATION, SERVICES OR PRODUCTS PROVIDED THROUGH THIS WEB SITE, EVEN IF WE HAVE BEEN ADVISED OF THE POSSIBILITY OF SUCH DAMAGES. 

IF YOU ARE DISSATISFIED WITH THIS SITE, OR ANY PORTION THEREOF, YOUR EXCLUSIVE REMEDY SHALL BE TO STOP USING THE WEB SITE.

Our Message Board
This web site contains a message board that is provided as a free service to users who agree to abide by the following terms and conditions. We assume no responsibility for the accuracy, currency, completeness or usefulness of information, views, opinions or advice in any material on a message board contained on this site. In addition, it does not endorse any opinions or recommendations posted by others. The information posted here is the responsibility of the person or persons posting the message. Any user who violates the terms and conditions listed herein may be permanently banned from posting messages herein. Further, The Network reserves the right to change these terms and conditions at any time and for any reason. 

You are entirely responsible and liable for any message you post or any message that is posted through your Account. You agree to refrain from the following actions while using a message board on this Web site: 

1. harassing, threatening, embarrassing or causing distress or discomfort upon another individual or entity; 
2. transmitting any information, data, text, files, links, software, chat, communication or other materials that are unlawful, harmful, threatening, abusive, invasive of another's privacy, harassing, defamatory, vulgar, obscene, hateful or racially or otherwise objectionable; 
3. impersonating any person, including but not limited to a "Network" moderator; 4. posting or transmitting any advertising, promotional materials, or any other forms of solicitation, including but not limited to "junk mail," "spam," "chain letters," or unsolicited mass distribution of email; 
5. posting or transmitting third-party copyrighted information or in any way infringing on the intellectual property rights, contractual or fiduciary rights of others; 
6. creating offensive and indecent member names, screen names, or handles;
7. providing false information on your registration form, or impersonating someone else. 
8. violating any applicable local, state, national or international law. 

You agree that The Guitarists Network in its sole discretion may remove posts by you or terminate your account if it believes that you may have in any way violated the terms and conditions contained herein, or any subsequent modifications. Any suspected fraudulent, abusive or illegal activity may be grounds for termination of your account and may be referred to appropriate law enforcement authorities. The Guitarists Network shall not be liable to you or any third party for the termination of your account or any claims related to your termination. The Guitarists Network reserves the right to monitor or remove any information transmitted or received through the message boards. We rely on our users to bring violations to our attention, although we do not guarantee any action based upon such information. Please direct these problems via email to a moderator or to the owner.

Should you object to any of the terms and conditions set forth herein or any subsequent modifications or become dissatisfied with the message boards in any way, your only recourse is to immediately: (1) discontinue use of the message boards; (2) terminate your account; and (3) notify The Guitarists Network of termination by sending an email to members@guitrists.net. Continued use of the message boards is an acceptance of the terms of use and any subsequent changes.

Members who are in violation of these policies may have their membership revoked and their posted information removed without prior warning.

The Guitarists Network reserves the right to modify or add to this terms of use policy at any time with no prior notification to any user or member.</textarea></td>
        </tr>
        <tr>
            <td></td>
            <td colspan="3"><input type="checkbox" name="acceptTerms" value="Yes"> <b>I accept the terms of use.</b></td>
        </tr>
        <tr>
            <td></td>
            <td colspan="3"><br>
            To verify you are a real person filling out this form, please enter the letters you see in the image 
            into the box below:
            <p>
            
            <table width="75%" cellspacing="2" cellpadding="2" border="0">
            <tr>
                <td><input type="Text" name="captcha" value="" size="10" /></td>
                <td><img src="/images/captcha.php" alt="Verification Text" border="0" /></td>
            </tr>
            </table>
            
            </td>
        </tr>
        <tr>
            <td></td>
            <td colspan="3"><br>
            <input type="submit" value="Continue &raquo;" class="button">
            <input type="Button" value="Cancel" onclick="javascript:location.href='../index.php';" class="button">
            </td>
        </tr>
        </table>
        </form>
        <!--- end content table --->
        
        </td>
        <?php if ($adPlace == 2) { require("fastclick.php"); } ?>
    </tr>
    </table>
    
<?php
    // include our footer
    require("footer.php");
?>
