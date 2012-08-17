<?php
    /*
        manual.php
        
        This allows a member to validate their account manually, if their 
        link isn't working from their email.  We'll ask for the member ID 
        number, password, and validation code.  If they match, we'll 
        validate them so they can login.
    */
    
    // include our needed file(s)
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    
    // continue, based on where we are
    if (isset($_POST["password"])) {
        // check their username and password
        $qryPass = $dbConn->getRow("
            select  ID,
                    strUsername,
                    strEmail,
                    strPassword,
                    dateLVisit,
                    intVisits,
                    intSendEmail,
                    intAccess,
                    intDisplay,
                    intDays,
                    intBanned,
                    intHideAds,
                    intSortOrder,
                    intValidated
            from    members
            where   ID = " . trim($_POST["ID"]) . " and 
                    strPassword = '" . trim($_POST["password"]) . "' and
                    strRegKey = '" . trim($_POST["regKey"]) . "'",
            DB_FETCHMODE_ASSOC);
        
        // if nor records were found
        if (!count($qryPass)) {
            print "
            <script language=\"javascript\">
            alert(\"Your information does not match our records. Your password is\\n\" +
                  \"case sensitive, so make sure you do not have Caps Lock\\n\" +
                  \"on and try again.\");
            history.back();
            </script>";
            exit();
        } else {
            // make sure they're not banned
            if ($qryPass["intBanned"]) {
                print "
                <script language=\"javascript\">
                alert(\"Your member priveleges have been revoked. If you believe this\\n\" +
                      \"in error, please contact our member staff, and they can help\\n\" +
                      \"you. Email them at members\@guitarists.net.\");
                location.replace(\"/index.php\");
                </script>";
                exit();
            }
            
            // make sure they're not already validated
            if ($qryPass["intValidated"]) {
                print "
                <script language=\"javascript\">
                alert(\"This account has already been validated.  Please feel free to login now.\");
                location.replace(\"login.php\");
                </script>";
                exit();
            }
            
            // make sure the password matches case
            if (trim($_POST["password"]) != trim($qryPass["strPassword"])) {
                // their password didn't match
                print "
                <script language=\"javascript\">
                alert(\"Your username and password do not match our records. Your\\n\" +
                      \"password is case sensitive, so make sure you do not have\\n\" +
                      \"Caps Lock on and try again.\");
                history.back();
                </script>";
                exit();
            } else {
                // set our member info
                $_SESSION["MemberID"] = $qryPass["ID"];
                $_SESSION["Username"] = trim($qryPass["strUsername"]);
                $_SESSION["HideAds"] = $qryPass["intHideAds"];
                $_SESSION["AccessLevel"] = $qryPass["intAccess"];
                $_SESSION["LastLogin"] = $qryPass["dateLVisit"];
                
                // see if they want to be "remembered"
                if (isset($_POST["rememberMe"])) {
                    // set a cookie to remember them
                    setcookie("MEMID", $_SESSION["MemberID"], time() + 31536000, "/");
                }
                
                // update our visit count
                $visits = $qryPass["intVisits"] + 1;
                
                // process the query
                $qryProcess = $dbConn->query("
                    update  members
                    set     dateLVisit = Now(), 
                            intVisits = " . $visits . ",
                            strIP = '" . $_SERVER["REMOTE_ADDR"] . "',
                            intValidated = 1
                    where   ID = " . $qryPass["ID"]);
                
                // if they chose to receive emails, add them to the list
                if ($qryPass["intSendEmail"]) {
                    // include our 1-2-all code
                    require("12all_db.php");
                    
                    // add them to the database
                    $db12All->query("
                        INSERT INTO `12all_listmembers` (
                            `sip`,
                            `comp`,
                            `sdate`,
                            `email`,
                            `name`,
                            `bounced`,
                            `soft_bounced`,
                            `bounced_d`,
                            `active` ,
                            `nl`,
                            `stime`,
                            `respond`,
                            `last_send`,
                            `no_autoresponders`
                        ) VALUES (
                            '',
                            '',
                            '" . date("Y-m-d") . "',
                            '" . trim(addslashes($qryPass["strEmail"])) . "',
                            '" . trim(addslashes($qryPass["strUsername"])) . "',
                            '0',
                            '0',
                            '0000-00-00',
                            '0',
                            '1',
                            '" . date("H:i:s") . "',
                            '',
                            '0',
                            '0'
                        )");
                }
                
                // relocate them
                print "
                <script language=\"javascript\">
                alert(\"Congratulations!  Your account has been validated, and you have\\n\" +
                      \"been logged in.  Thanks for joining Guitarists.net.\");
                location.replace(\"/index.php\");
                </script>";
                exit();
            }
        }
    } else {
        $pageTitle = "Guitar Resources: Member Login";
        $pageDescription = "";
        $pageKeywords = "";
        $areaName = "register";
        
        // include our header
        require("header.php");
    ?>
    
    <br>
    <!--- display the login form --->
    <div align="center">
    <table width="350" cellspacing="1" cellpadding="3" border="0">
    <tr>
        <td class="tablehead">&nbsp;&raquo;&nbsp;Member Account Validation</td>
    </tr>
    </table>
    
    <table width="350" cellspacing="1" cellpadding="3" border="0">
    <form name="myCode" action="manual.php" method="post" onSubmit="return valRegCode()">
    <tr>
        <td align="right"><b>User ID:</b></td>
        <td><input type="text" name="ID" size="20" maxlength="20" class="input"></td>
    </tr>
    <tr>
        <td align="right"><b>Validation Code:</b></td>
        <td><input type="text" name="regKey" size="20" maxlength="15" class="input"></td>
    </tr>
    <tr>
        <td align="right"><b>Password:</b></td>
        <td><input type="password" name="password" size="20" maxlength="20" class="input"></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td><input type="Checkbox" name="rememberMe" value="1"> Remember my login.</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>
        <input type="Submit" value="Validate &raquo;" class="button">
        <input type="Button" value="Cancel" onClick="history.back()" class="button">
        </td>
    </tr>
    </form>
    </table>
    </div>
    
    <script language="JavaScript">
    document.myCode.ID.focus();
    </script>
        
<?php
        // include our footer
        require("footer.php");
    }
?>
