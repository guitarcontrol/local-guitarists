<?php
    /*
        invite.php
        
        This script allows a user to send invitations to come to Guitarists.net to other people across the net. 
        They simply enter email addresses (1 per line) and our code will send them out.
    */
    
    // include our functions script
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    require("functions.php");
    
    // see if they're logged in
    verify_access($_SESSION["AccessLevel"], 1);
    
    // select our pertinent data for our users
    $arrUser = $dbConn->getRow("
        select  ID,
                strUsername,
                strEmail
        from    members
        where   ID = '" . $_SESSION["MemberID"] . "'
        limit 1",
        DB_FETCHMODE_ASSOC);
    
    // set the page title for this page
    $pageTitle = "Guitarists.net Invitation";
    
    // all good!  display the page
    require("header.php");
?>
    
    <br />
    <table width="100%" cellspacing="0" cellpadding="2" border="0">
    <tr>
        <td class="tablehead">&nbsp;&raquo;&nbsp;<a href="/" title="Guitar Resources Home"><b>Home</b></a>&nbsp;&raquo;&nbsp;<a href="/members_ggc/" title="Members Area"><b>Members Area</b></a>&nbsp;&raquo;&nbsp;Invite A Friend To Join Us</td>
    </tr>
    </table>
    
    <!--- display the login form --->
    <div align="center">
    <table width="720" cellspacing="1" cellpadding="3" border="0">
    <form name="verify" action="process_invite.php" method="post">
    <input type="Hidden" name="ID" value="<?php print $_SESSION["MemberID"]; ?>" />
    <input type="Hidden" name="strUsername" value="<?php print $arrUser["strUsername"]; ?>" />
    <input type="Hidden" name="strEmail" value="<?php print $arrUser["strEmail"]; ?>" />
    <tr>
        <td><br />
        <p>
        We think Guitarists.net is a great place for guitar players to hang out!  And you must feel somewhat the same, seeing that you're a member here.  So feel free to use the form below to let your friends, family, and other guitar players know about our great community!  Simply enter a valid email (one per line) in the box below, and we'll take care of the rest.
        </p>
        Thanks for using Guitarists.net!
        <p>
        <textarea name="emails" cols="50" rows="5" class="input"></textarea>
        <p>
        <input type="Submit" value="Process &raquo;" class="button" />
        <input type="Button" value="Cancel" onClick="location.href='/index.php'" class="button" />
        </td>
    </tr>
    </form>
    </table>
    </div>
    
<?php
    // include our footer
    require("footer.php");
?>