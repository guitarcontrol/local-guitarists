<?php
    /*
        unsub.php
        
        This script allows a user to unsubscribe from our mailing list by clicking 
        on a link in the email they receive.  They can also do it by going to their 
        member section, but this is easier.
        
    */
    print "This feature is disabled.\n"; exit();
    // include our functions script
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    require("functions.php");
    
    // select our pertinent data for our users
    $arrUser = $dbConn->getRow("
        select  ID,
                strUsername,
                strEmail,
                strRegKey
        from    members
        where   ID = '" . $_SESSION["MemberID"] . "'
        limit 1",
        DB_FETCHMODE_ASSOC);
    
    // set the page title for this page
    $pageTitle = "Guitarists.net Account Removal";
    
    // all good!  display the page
    require("header.php");
?>
    
    <br />
    <table width="100%" cellspacing="0" cellpadding="2" border="0">
    <tr>
        <td class="tablehead">&nbsp;&raquo;&nbsp;<a href="/index.php" class="tablehead"><b>Home</b></a>&nbsp;&raquo;&nbsp;Guitarists.net Account Removal</td>
    </tr>
    </table>
    
    <!--- display the login form --->
    <div align="center">
    <table width="720" cellspacing="1" cellpadding="3" border="0">
    <form name="verify" action="update_remove.php" method="post" onSubmit="return verifyIt()">
    <input type="Hidden" name="ID" value="<?php print $_SESSION["MemberID"]; ?>">
    <input type="Hidden" name="strUsername" value="<?php print $arrUser["strUsername"]; ?>">
    <input type="Hidden" name="strEmail" value="<?php print $arrUser["strEmail"]; ?>">
    <input type="Hidden" name="strRegKey" value="<?php print $arrUser["strRegKey"]; ?>">
    <tr>
        <td><br>
        <?php
            // see if an error message was passed
            if (!empty($_GET["status"])) {
                // display our error message
                switch ($_GET["status"]) {
                    case 1: print "<b style=\"color: red; font-size: 14px;\">You do not appear to be deleting an account you have access to.  Please try again.</b><p />\n"; break;
                    case 2: print "<b style=\"color: red; font-size: 14px;\">Please verify that you do indeed want to delete this account permanently.</b><p />\n"; break;
                }
            }
        ?>
        This will allow you to remove your account from the Guitarists.net database.  All data 
        associated with you will be lost.  <b>This cannot be undone.</b>  Please verify that you wish to 
        be removed below.
        <p>
        Thanks for using Guitarists.net!
        <p>
        <input type="Checkbox" name="deleteMe" value="1"> Yes, I want to be removed.
        <p>
        <input type="Submit" value="Process &raquo;" class="button">
        <input type="Button" value="Cancel" onClick="location.href='/index.php'" class="button">
        </td>
    </tr>
    </form>
    </table>
    </div>
    
    <script language="JavaScript">
    function verifyIt() {
        // make sure they agreed to continue
        if (document.verify.deleteMe.checked == false) {
            alert("You must verify that you want to be removed.");
            return false;
        }
        return true;
    }
    </script>
    
<?php
    // include our footer
    require("footer.php");
?>
