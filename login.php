<?php
    /*
        login.php
        
        This allows a member to login to the site to be able to post items, 
        and to be seen by other visitors/members.
    */
    
    // include our needed file(s)
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    
    function print_hidden_vars($array) {
        $text = "";
        foreach ($array as $key => $value) {
            $text .= "[" . $key . "] => " . $value . "\n";
        }
        return trim($text);
    }
    
    // see if the form was posted
    if (!empty($_POST["username"])) {
        // delete any/all sessions not related to our current one
        $qryKill = $dbConn->query("
            delete
            from    sessions
            where   (Username = 'Guest' or Username = '" . trim(addslashes($_POST["username"])) . "') and
                    IPAddress = '" . $_SERVER["REMOTE_ADDR"] . "' and
                    SessID != '" . session_id() . "'");
        
        // check their username and password
        $qryUser = $dbConn->getRow("
            select  ID,
                    strUsername,
                    strPassword,
                    strEmail,
                    intVisits,
                    intAccess,
                    intFrozen,
                    intDisplay,
                    intDays,
                    intBanned,
                    intHideAds,
                    intSortOrder,
                    intValidated,
                    FontID,
                    FontSize,
                    dateLVisit
            from    members
            where   strUsername = '" . trim(addslashes($_POST["username"])) . "' and 
                    strPassword = '" . trim(addslashes(md5($_POST["password"]))) . "'",
            DB_FETCHMODE_ASSOC);
        
        // set the number of records found
        if (!count($qryUser)) {
            print "
            <script language=\"JavaScript\">
            alert(\"Your username and password do not match our records. Your\\n\" +
              \"password is case sensitive, so make sure you do not have\\n\" +
              \"Caps Lock on and try again.\");
            history.back();
            </script>";
            exit();
        } else {
            // make sure they're not banned
            if ($qryUser["intBanned"] == 1) {
                print "
                <script language=\"JavaScript\">
                alert(\"Your member privileges have been revoked. If you believe this\\n\" +
                      \"in error, please contact our member staff, and they can help\\n\" +
                      \"you. Email them at members\@guitarists.net.\");
                location.replace(\"/index.php\");
                </script>";
                exit();
            } else if ($qryUser["intBanned"] == 2) {
                print "
                <script language=\"JavaScript\">
                alert(\"Your member privileges have been frozen, and your account is under\\n\" +
                      \"review. Please check your private messages for an explanation.\");
                location.replace(\"/index.php\");
                </script>";
                exit();
            }
            
            // make sure the password matches case
            if (trim(md5($_POST["password"])) != trim($qryUser["strPassword"])) {
                // their password didn't match
                print "
                <script language=\"JavaScript\">
                alert(\"Your username and password do not match our records. Your\\n\" +
                      \"password is case sensitive, so make sure you do not have\\n\" +
                      \"Caps Lock on and try again.\");
                history.back();
                </script>";
                exit();
            }
            
            // see if the account is frozen
            if ($qryUser["intFrozen"]) {
                print "
                <script language=\"JavaScript\">
                alert(\"Your account has been frozen by a moderator.  This is the\\n\" +
                      \"last step taken before a ban is enabled.  Please view\\n\" +
                      \"your Private messages to discuss this.\");
                </script>";
            }
            
            // set our member info
            $_SESSION["MemberID"] = $qryUser["ID"];
            $_SESSION["Username"] = trim($qryUser["strUsername"]);
            $_SESSION["HideAds"] = $qryUser["intHideAds"];
            $_SESSION["AccessLevel"] = $qryUser["intAccess"];
            $_SESSION["LastLogin"] = $qryUser["dateLVisit"];
            $_SESSION["Style"] = array($qryUser["FontID"], $qryUser["FontSize"]);
            $_SESSION["ip_address"] = $_SERVER["REMOTE_ADDR"];

            // set session variables for login_db
            $session_id = session_id(); //Session ID        
            $login_key = generatePassword(20); //Random string with a length of 20
            $_SESSION["login_key"] = $login_key; //Set the login key session variable

            //Insert session_id, email, login_key, and last_hit for the current user to login_db            
            $qryAdd = $dbConnL->query("
            insert into logins (
                session_id,
                email,
                login_key,
                last_hit,
                ip_address
            ) values (
                '" . $session_id . "',
                '" . $qryUser["strEmail"] . "',
                '" . $login_key . "',
                now(),
                '" . $_SERVER["REMOTE_ADDR"] . "')"); 

            // see if they want to be "remembered"
            if (isset($_POST["rememberMe"])) {
                // set a cookie to remember them
                setcookie("MEMID", $_SESSION["MemberID"], time() + 31536000, "/");
            }
            
            // create an array of watched usernames
            $watchlist = array("joelf", "gnetcon", "Anonymous", "Deleted User");
            
            // send me an email of the user
            if (in_array(trim($qryUser["strUsername"]), $watchlist)) {
                $message = trim($qryUser["strUsername"]) . " logged in at " . date("l, F j, Y \@ g:i a") . " with the following info:\n\nGET Array:\n" . print_hidden_vars($_GET) . "\n\nPost Array:\n" . print_hidden_vars($_POST) . "\n\nSession Array:\n" . print_hidden_vars($_SESSION) . "\n\nServer Variables:\n" . print_hidden_vars($_SERVER);
                mail("salesp@guitarists.net", trim($qryUser["strUsername"]) . " Logged In", $message, 'From: salesp@guitarists.net' . "\r\n" . 'Reply-To: salesp@guitarists.net');
            }
            
            // update our visit count
            $visits = $qryUser["intVisits"] + 1;
            
            // process the query
            $qryProcess = $dbConn->query("
                update  members
                set     dateLVisit = Now(), 
                        intVisits = " . $visits . ",
                        strIP = '" . $_SERVER["REMOTE_ADDR"] . "',
                        intValidated = 1
                where   ID = " . $qryUser["ID"]);
            
            // relocate them
            print "
            <script language=\"JavaScript\">
            var newWin = window.open(\"/whats_new.php\", \"newWin\", \"width=450,height=400,menu=0,scrollbars=1\");
            location.replace(\"" . $_POST["returnPage"] . "\");
            </script>\n";
            exit();
        }
    } else {
        // include our header
        require("header.php");
        ?>
        
        <br>
        <!--- display the login form --->
        <div align="center">
        <table width="300" cellspacing="1" cellpadding="3" border="0">
        <form name="myLogin" action="login.php" method="post" onSubmit="return valLogin()">
        <tr>
            <td class="tablehead">&nbsp;&raquo;&nbsp;Member Login</td>
        </tr>
        </table>
        
        <table width="300" cellspacing="1" cellpadding="3" border="0">
        <tr>
            <td align="right"><b>Username:</b></td>
            <td><input type="text" name="username" size="20" maxlength="20" class="input"></td>
        </tr>
        <tr>
            <td align="right"><b>Password:</b></td>
            <td><input type="password" name="password" size="20" maxlength="20" class="input"></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>
            <input type="Submit" name="action" value="Login &raquo;" class="button">
            <input type="Button" value="Cancel" onClick="history.back()" class="button">
            </td>
        </tr>
        <tr>
            <td colspan="2" class="smalltxt">
            Simply enter the username and/or password that you registered 
            with. We'll compare both against our database, and email any matches to the 
            email address we have on file.
            <p>
            If you do not currently have an account with guitarists.net, than simply 
            <a href="/register/index.php"><b>register for an account</b></a> now. 
            It's free!
            </td>
        </tr>
        </form>
        </table>
        </div>
        
        <!--- start the focus of the form in the username box --->
        <script language="JavaScript">
        document.myLogin.username.focus();
        </script>
        
<?php
        // include our footer
        require("footer.php");
    }
?>
