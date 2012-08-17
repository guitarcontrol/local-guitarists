<?php
    /*
        remind.php
        
        This allows a person to have their password emailed to them, in case they 
        have forgotten it.
    */
    
    // include our needed file(s)
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("functions.php");
    require("sessions.php");
    require("ads.php");
    
    // continue, based on what was passed
    if (isset($_POST["username"])) {
        // based on what was passed, set the where statement
        if (strlen($_POST["username"]) && !strlen($_POST["email"])) {
            $where = "strUsername = '" . trim($_POST["username"]) . "'";
        } else if (strlen($_POST["email"]) && !strlen($_POST["username"])) {
            $where = "strEmail = '" . trim($_POST["email"]) . "'";
        } else if (strlen($_POST["email"]) && strlen($_POST["username"])) {
            $where = "strUsername = '" . trim($_POST["username"]) . "' or strEmail = '" . trim($_POST["email"]) . "'";
        }
        
        // select the passed personal info into the database
        $qryInfo = $dbConn->getRow("
            select  ID, 
                    strFName, 
                    strUsername,
                    strEmail 
            from    members 
            where   " . $where,
            DB_FETCHMODE_ASSOC);
        
        // make sure some records were found
        if (count($qryInfo)) {
            // create a new password
            $newpass = createNewPassword();
            
            // update the password in the db
            $qryUpdate = $dbConn->query("UPDATE members SET strPassword = '" . md5($newpass) . "' WHERE ID = '" . $qryInfo["ID"] . "' LIMIT 1");
            
            // build our email text
            $emailText = "Dear " . $qryInfo["strFName"] . ":\n\n";
            $emailText .= "You recently asked us to send your password information. Because of a recent\n";
            $emailText .= "hack of the site, we have reset your password.  Here's what you\n";
            $emailText .= "need to access the site:\n\n";
            $emailText .= "Member ID: " . $qryInfo["ID"] . "\n";
            $emailText .= "Username: " . $qryInfo["strUsername"] . "\n";
            $emailText .= "Password: " . $newpass . "\n";
            $emailText .= "Email: " . $qryInfo["strEmail"] . "\n";
            $emailText .= "IP Address: " . $_SERVER["REMOTE_ADDR"] . "\n\n";
            $emailText .= "If you ever need to edit this information, simply go to:\n";
            $emailText .= "http://www.guitarists.net/members/\n";
            $emailText .= "- or -\n";
            $emailText .= "<a href=\"http://www.guitarists.net/members/\">http://www.guitarists.net/members/</a>\n\n";
            $emailText .= "And if you continue to have problems logging in, please feel free to email\n";
            $emailText .= "member.support@guitarists.net. We'll be happy to help you anyway we can.\n\n";
            $emailText .= "Thanks again!\n\n";
            $emailText .= "The Guitarists Network Staff";
            
            // send the email
            mail($qryInfo["strEmail"],
                 "Password for Guitarists.net",
                 $emailText, "From: member.support@guitarists.net\r\n" .
                 "Reply-To: member.support@guitarists.net");
            
            // let them know we found it
            print "
            <script language=\"javascript\">
            alert(\"Your password has been emailed to " . $qryInfo["strEmail"] . ".\\n\" + 
                  \"You should receive it shortly. Thanks.\");
            location.replace(\"" . $_POST["redirURL"] . "\");
            </script>";
            exit();
        } else {
            // let them know we didn't find found it
            print "
            <script language=\"javascript\">
            alert(\"No accounts were found using the username and email you\\n\" +
                  \"submitted. Please try again.\");
            history.back();
            </script>";
            exit();
        }
    } else {
        // include our header
        require("header.php");
        ?>
        
        <script language="JavaScript">
        function valForm() {
            if (document.lookup.username.value == "" && document.lookup.email.value == "") {
                alert("Please enter either a username or email address for us to look for.");
                return false;
            }
            return true;
        }
        </script>
        
        <br>
        <!--- display the login form --->
        <div align="center">
        <table width="300" cellspacing="1" cellpadding="3" border="0">
        <tr>
            <td class="tablehead">&nbsp;&raquo;&nbsp;Password Reminder</td>
        </tr>
        </table>
        
        <table width="300" cellspacing="1" cellpadding="3" border="0">
        <form name="lookup" action="remind.php" method="post" onSubmit="return valForm()">
        <input type="Hidden" name="redirURL" value="<?php print $_GET["path"]; ?>" />
        <tr>
            <td align="right"><b>Username:</b></td>
            <td><input type="text" name="username" size="20" maxlength="20" class="input"></td>
        </tr>
        <tr>
            <td align="right"><b>Email:</b></td>
            <td><input type="text" name="email" size="20" maxlength="150" class="input"></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>
            <input type="Submit" name="action" value="Lookup" class="button">
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
        document.lookup.username.focus();
        </script>
        
<?php
        // include our footer
        require("footer.php");
    }
?>
