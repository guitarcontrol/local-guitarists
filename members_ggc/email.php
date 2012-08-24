<?php
    
    /*
        forum.php
        
        This allows a user to edit their preferences in displaying data 
        on the forums.
    */
    
    // include our needed file(s)
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    
    // see if they're logged in
    verify_access($_SESSION["AccessLevel"], 1);
    
    // query the data from the db
    $qryInfo = $dbConn->getRow("
        select  strEmail,
                strPublicEmail
        from    members
        where   ID = '" . $_SESSION["MemberID"] . "'",
        DB_FETCHMODE_ASSOC);
    
    // create our variables
    $pageTitle = "Members Area: Email Preferences";
    $pageDescription = "";
    $pageKeywords = "";
    $areaName = "members";
    
    // include our header file
    require("header.php");
?>
    
    <br>
    <table width="100%" cellspacing="0" cellpadding="2" border="0">
    <tr>
        <td class="tablehead">&nbsp;&raquo;&nbsp;<a href="/index.php"><b>Home</b></a>&nbsp;&raquo;&nbsp;<a href="/members_ggc/index.php"><b>Members Area</b></a>&nbsp;&raquo;&nbsp;Edit your Email Addresses</td>
    </tr>
    </table>
    
    <div align="center">
    <table width="600" cellspacing="0" cellpadding="1" border="0">
    <form name="myEmail" action="update_email.php" method="post" onSubmit="return checkEmailInfo()">
    <input type="Hidden" name="ID" value="<?php print $_SESSION["MemberID"]; ?>">
    <input type="Hidden" name="origPublicEmail" value="<?php print $qryInfo["strPublicEmail"]; ?>">
    <input type="Hidden" name="origEmail" value="<?php print $qryInfo["strEmail"]; ?>">
    <tr>
        <td align="right"><b>Public:</b> </td>
        <td><input type="text" name="strPublicEmail" value="<?php print $qryInfo["strPublicEmail"]; ?>" size="35" maxlength="80" class="input"></td>
    </tr>
    <tr>
        <td></td>
        <td class="smalltxt">This is the email address that everyone sees on the web site.
        We recommend not using your real email in order to prevent spam trolls from 
        indexing it.<br><br></td>
    </tr>
    <tr>
        <td align="right"><b>Private:</b> </td>
        <td><input type="text" name="strEmail" value="<?php print $qryInfo["strEmail"]; ?>" size="35" maxlength="80" class="input"></td>
    </tr>
    <tr>
        <td></td>
        <td class="smalltxt">This must be a valid email. Your change must be verified, 
        and we will email the verification to this new email address.</td>
    </tr>
    <tr>
           <td></td>
           <td><br>
        <input type="submit" value="Update &raquo;" class="button">
        <input type="Button" value="Cancel" onclick="location.href='index.php'" class="button">
        </td>
    </tr>
    </form>
    </table>
    </div>

<?php
    // include our footer file
    require("footer.php");
?>
