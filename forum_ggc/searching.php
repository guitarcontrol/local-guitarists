<?php
    /*
        results.php
        
        Here we display the results of our search, based on the terms and options chosen.
    */
    
    // include our needed file(s)
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    require("functions.php");



    // make sure our vars were passed
    if (!isset($_GET["terms"])) {
        print "<script language=\"JavaScript\">location.replace(\"search.php\");</script>\n";
        exit();
    }
    
    // set our redirect page
    $redirect = "results.php";
    
    // loop through our GET fields to build our page to redirect to
    foreach ($_GET as $key => $value) {
        $redirect .= $key . "=" . $value . "&";
    }
  
    // set our variables
    $pageTitle = "Guitar Forums: Searching for '" . $_GET["terms"] . "'";
    $areaName = "forums";
    
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

    if (empty($_SESSION["GGCIFrame"])) {
?>

    <br />
    <table width="100%" cellspacing="0" cellpadding="2" border="0">
    <tr valign="top">
        <td>
        <!--- begin layout file --->
        <table width="600" cellspacing="1" cellpadding="3" border="0">
        <tr>
            <td colspan="5" class="medtxt"><a href="/index.php"><b>Home</b></a>&nbsp;&raquo;&nbsp;<a href="index.php"><b>Guitar Discussions</b></a>&nbsp;&raquo;&nbsp;Searching for "<?php print $_GET["terms"]; ?>"</td>
        </tr>
<?php } else { ?>
    <table width="100%" cellspacing="0" cellpadding="2" border="0">
<?php } ?>
        <tr>
            <td colspan="5"><br />
            Your search for <b><?php print $_GET["terms"]; ?></b> is currently being processed.  Please wait, as it may take 
            a few seconds for the results to be displayed.
            <p />
            Thanks.
            </td>
        </tr>
        </table>
        <!--- end layout file --->
        </td>
    </tr>
    </table>

    <script language="JavaScript">
    // redirect to the results page
    location.href='<?php print $redirect; ?>';
    </script>
    
<?php
    // include our footer
    if (empty($_SESSION["GGCIFrame"])) {
        require("footer.php");
    }
?>
