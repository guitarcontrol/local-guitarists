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
    
    // create our variables
    $pageTitle = "Members Area: Change Your Password";
    $pageDescription = "";
    $pageKeywords = "";
    $areaName = "members";
    
    // include our header file
    // require("header.php");
?>
    <link type="text/css" rel="stylesheet" href="/inc/styles.css" />
	<style>
    BODY {
        background: none;
    }
    </style>
    <br>
    <table width="100%" cellspacing="0" cellpadding="2" border="0">
    <tr>
        <td class="tablehead">&nbsp;&raquo;&nbsp;<a href="/index.php"><b>Home</b></a>&nbsp;&raquo;&nbsp;<a href="/members_ggc/index.php"><b>Members Area</b></a>&nbsp;&raquo;&nbsp;Change your password</td>
    </tr>
    </table>
    
    <div align="center">
    <table cellspacing="0" cellpadding="1" border="0">
    <form name="myPass" action="update_password.php" method="post" onSubmit="return checkPassInfo()">
    <input type="Hidden" name="ID" value="<?php print $_SESSION["MemberID"]; ?>">
    <tr>
        <td align="right"><b>Current:</b> </td>
        <td><input type="password" name="password" size="20" maxlength="20" class="input"></td>
    </tr>
    <tr>
        <td></td>
        <td class="smalltxt">
        Enter the password you currently use to access this site.
        <br><br>
        </td>
    </tr>
    <tr>
        <td align="right"><b>New:</b> </td>
        <td><input type="password" name="newpassword" size="20" maxlength="20" class="input"></td>
    </tr>
    <tr>
        <td></td>
        <td class="smalltxt">
        Enter the new password you would like to use.
        <br><br>
        </td>
    </tr>
    <tr>
        <td align="right"><b>Confirm:</b> </td>
        <td><input type="password" name="confirmpassword" size="20" maxlength="20" class="input"></td>
    </tr>
    <tr>
        <td></td>
        <td class="smalltxt">
        Confirm the new password by entering it <b>exactly</b> as you just did.
        </td>
    </tr>
    <tr>
           <td></td>
           <td><br>
        <input type="submit" value="Update &raquo;" class="button">
        <input type="Button" value="Cancel" onclick="location.href='index.php'" class="button">
        </td>
    </tr>
    <tr>
        <td colspan="2"><br>
        <b>&raquo;</b>&nbsp;<a href="/register/remind.php"><b>Click here</b></a> if you have 
        forgotten your password, and would like us to <a href="/register/remind.php"><b>email 
        it you</b></a>.
        </td>
    </tr>
    </form>
    </table>
    </div>

<?php
    // include our footer file
    require("footer.php");
?>
