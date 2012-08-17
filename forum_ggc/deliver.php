<?php
    /*
        deliver.php
        
        This script allows someone to email an entire discussion to any 
        email address.
        
    */
    
    // include our needed file(s)
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    
    // make sure they're logged in
    verify_access($_SESSION["AccessLevel"], 1);
    
    // make sure a thread was passed
    if (!isset($_GET["thread"])) {
        print "
        <script language=\"JavaScript\">
        alert(\"Please choose a thread to send first.\");
        history.back();
        </script>";
        exit();
    }
    
    // set our variables
    $pageTitle = "Guitar Forums: Send a thread to a friend";
    $pageDescription = "";
    $pageKeywords = "";
    $areaName = "forums";
    
    // include our header
  // include our header file
   if (empty($_SESSION["GGCIFrame"])) {
        require("header.php");
   } else {
        ?>
        <link type="text/css" rel="stylesheet" href="/inc/styles.css" />
        <style>
        BODY {
            background: none;
        }
        </style>
        <?php
    }
?>
   
    <br>
<?php if (empty($_SESSION["GGCIFrame"])) { ?>
    <table width="100%" cellspacing="0" cellpadding="2" border="0">
    <tr>
        <td class="tablehead">&nbsp;&raquo;&nbsp;<a href="/index.php" class="tablehead"><b>Home</b></a>&nbsp;&raquo;&nbsp;<a href="/forum_ggc/index.php" class="tablehead"><b>Guitar Discussions</b></a>&nbsp;&raquo;&nbsp;Send this thread to a friend</td>
    </tr>
    </table>
<?php } ?>
    
    <table width="100%" cellspacing="0" cellpadding="2" border="0">
    <tr valign="top">
        <td>
        
        <!--- begin layout file --->
        <table width="100%" cellspacing="1" cellpadding="3" border="0">
        <form name="emailIt" action="deliver_email.php" method="post" onSubmit="return deliverIt()">
        <input type="Hidden" name="ID" value="<?php print $_GET["thread"]; ?>">
        <tr>
            <td><b>Friend's Name: </b></td>
            <td><input type="Text" name="name" size="55" class="input"></td>
        </tr>
        <tr>
            <td><b>Friend's Email: </b></td>
            <td><input type="Text" name="email" size="55" class="input"></td>
        </tr>
        <tr>
            <td></td>
            <td>
            <input type="Submit" name="action" value="Send Thread" class="button">
            <input type="Button" value="Cancel" onClick="history.back()" class="button">
            </td>
        </tr>
        </form>
        </table>
        <!--- end form table --->
        
        </td>
        <?php //require("fastclick.php"); ?>
    </tr>
    </table>
    
<?php
    // include our footer
    if (empty($_SESSION["GGCIFrame"])) {
        require("footer.php");
    }
?>
