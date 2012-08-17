<?php
    /*
        banned_ip_ranges.php
        
        Here we simply list all of the IP ranges in the database that have been 
        banned.
        
         @author aloise@aloise.name
    */
    
    // include our needed files
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    require("functions.php");
    
    // make sure they're logged in
    verify_access($_SESSION["AccessLevel"], 90);
    
    // set our IP (if any)
    if (isset($_GET["ip"])) {
        $addy = $_GET["ip"];
    } else {
        $addy = "0.0.0.0";
    }
    
    
    if( !empty( $_REQUEST['delete'] ))
    {
    	$dbConn->query("DELETE FROM banned_ip_ranges WHERE id = ".(int) $_REQUEST['delete'] );
    }
    else if( !empty($_REQUEST['add']))
    {
    	$dbConn->query("INSERT INTO banned_ip_ranges( ipRangeStart, ipRangeEnd ) 
    						VALUES (". sprintf("%u", ip2long($_REQUEST['ipRangeStart'])) .", ". sprintf("%u", ip2long($_REQUEST['ipRangeEnd'])) .")" );
    }
    
    
    // get our IP's from the db
    $qryIPs = $dbConn->query("
        select      *
        from        banned_ip_ranges
        order by    ipRangeStart");
    
    // set the # to display per row
    $display = ceil($qryIPs->numRows() / 5);
    
    // set our total number of items found
    $totalcount = $qryIPs->numRows();
    
    // set our page defaults
    $pageTitle = "Guitar Forums: Mod Tools: Display Banned IP ranges";
    $pageDescription = "";
    $pageKeywords = "";
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
?>

    <br>
    <table width="100%" cellspacing="0" cellpadding="2" border="0">
    <tr valign="top">
        <td align="center">
    
        <!--- begin layout file --->
        <?php if (empty($_SESSION["GGCIFrame"])) { ?>
        <table width="100%" cellspacing="1" cellpadding="1" border="0">
        <tr>
            <td class="tablehead">&nbsp;&raquo;&nbsp;Banned&nbsp;IP ranges (<?php print $qryIPs->numRows(); ?> found)</td>
        </tr>
        </table>
        <?php } ?>
        
        <table width="100%" cellspacing="0" cellpadding="2" border="0">
        <tr valign="top">
            <td>
            <?php
                // set a row #
                $intRow = 1;
                
                // loop through our results
                while ($qryRow = $qryIPs->fetchRow(DB_FETCHMODE_ASSOC)) {
                    // see if it matches our passed IP
                       ?> 
                       <b>
                       	<?php echo long2ip ( $qryRow['ipRangeStart'] ). ' - '. long2ip ( $qryRow['ipRangeEnd'] ) ?>
                       	<a href="?delete=<?php echo $qryRow['id']?>" onclick="return confirm('Are you sure to delete a range ?')" >delete?</a> 
                       </b>
                       <br>
                    <?php 
                    // see if we need to start a new row
                    if ($intRow == $display) {
                        print "</td><td>\n";
                        $intRow = 1;
                    } else {
                        $intRow++;
                    }
                }
            ?><br>
            </td>
        </tr>
        <tr>
            <td colspan="5"><br></td>
        </tr>
        </table>
        
        
 
        <!--- end layout file --->
        
        </td>
        
    </tr>
    </table>
    
    <hr />
    
    <form method="post" action="?add">
    	<h1>Add a range</h1>
    	
    	<label>Start IP</label>
    	<input type="text" name="ipRangeStart" />
    	
    	
    	<label>End IP</label>
    	<input type="text" name="ipRangeEnd" />
    	 
    	
    	<input type="submit" name="add" value="Add" />
    </form>

<?php
    // include our footer
    if (empty($_SESSION["GGCIFrame"])) {
        require("footer.php");
    }
?>
