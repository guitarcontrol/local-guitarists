<?php
    
    /*
        search.php
        
        Here we'll give the members a choice of cities for the chosen state/country, 
        along with the various options to search by.  Once we have everything, we'll 
        find any/all members matching the criteria chosen.
    */
    
    // include our main app file
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    
    // make sure they're logged in
    verify_access($_SESSION["AccessLevel"], 1);
    
    // make sure they passed something to search by
    if (!isset($_GET["state"]) && !isset($_GET["country"])) {
        print "
        <script language=\"JavaScript\">
        alert(\"Please choose something to search by.\");
        location.replace(\"/members/index.php\");
        </script>";
        exit();
    }
    
    // based on what was passed, search accordingly
    if (isset($_GET["state"])) {
        // query the state ID and name
        $qryState = $dbConn->getRow("
            select  ID,
                    strName
            from    states
            where   strAbbr = " . $dbConn->quote($_GET["state"]),
            DB_FETCHMODE_ASSOC);
        
        // query their cities for this state
        $qryCities = $dbConn->query("
            select      distinct(strCity) as strCity
            from        about
            where       intState = '" . $qryState["ID"] . "' or
                        strState = " . $dbConn->quote($_GET["state"]) . "
            order by    strCity");
        
        // set our vars we'll pass to the results script
        $intState = $qryState["ID"];
        $intCountry = 213;
    } else if (isset($_GET["country"])) {
        // query their cities for this country
        $qryCities = $dbConn->query("
            select      distinct(strCity) as strCity
            from        about
            where       intCountry = " . $dbConn->quote($_GET["country"]) . "
            order by    strCity");
        
        // set our vars we'll pass to the results script
        $intState = 0;
        $intCountry = $_GET["country"];
    }
    
    // set our page variables
    $pageTitle = "Member Search: Refine your search criteria";
    $areaName = "members";
    
    // include our header
    require("header.php");
?>

    <table width="100%" cellspacing="0" cellpadding="2" border="0">
    <tr valign="top">
        <?php if ($adPlace == 1) { require("fastclick.php"); } ?>
        <td align="right">
        
        <table width="100%" cellspacing="1" cellpadding="2" border="0">
        <form name="mySearch" action="results.php" method="get" onSubmit="return valQuery()">
        <input type="Hidden" name="country" value="<?php print $intCountry; ?>">
        <input type="Hidden" name="state" value="<?php print $intState; ?>">
        <tr>
            <td colspan="2" class="medtxt"><a href="/index.php"><b>Home</b></a>&nbsp;&raquo;&nbsp;<a href="index.php"><b>Members Area</b></a>&nbsp;&raquo;&nbsp;Player Search</td>
        </tr>
        <tr>
            <td colspan="2"><br></td>
        </tr>
        <tr valign="top">
            <td width="150">
            <b>Cities:</b><br>
            <select name="city[]" multiple size="15" class="input" style="width: 150;">
            <?php
                // loop through our city results
                while ($qryRow = $qryCities->fetchRow(DB_FETCHMODE_ASSOC)) {
                    if (strlen($qryRow["strCity"])) {
                        ?>
                        <option value="<?php print trim($qryRow["strCity"]); ?>"> <?php print trim($qryRow["strCity"]); ?></option>
                        <?php
                    }
                }
            ?>
            </select>
            <p>
            <table width="150" cellspacing="2" cellpadding="2" border="0">
            <tr>
                <td><b>Username: </b></td>
                <td><input type="Text" name="username" value="" size="10" class="input" /></td>
            </tr>
            <tr>
                <td colspan="2" class="smalltxt">
                Enter a partial name match.
                </td>
            </tr>
            </table>

            </td>
            <td>
            <b>Styles:</b><br>
            <table width="100%" cellspacing="0" cellpadding="1" border="0">
            <tr valign="top">
                   <td width="33%">
                <input type="Checkbox" name="styles[]" value="1">Acid<br>
                <input type="Checkbox" name="styles[]" value="2">Acoustic<br>
                <input type="Checkbox" name="styles[]" value="3">Alternative<br>
                <input type="Checkbox" name="styles[]" value="4">Big Band<br>
                <input type="Checkbox" name="styles[]" value="5">Blue Grass<br>
                <input type="Checkbox" name="styles[]" value="6">Blues<br>
                <input type="Checkbox" name="styles[]" value="7">Christian
                </td>
                   <td width="34%">
                <input type="Checkbox" name="styles[]" value="8">Classic Rock<br>
                <input type="Checkbox" name="styles[]" value="9">Classical<br>
                <input type="Checkbox" name="styles[]" value="10">Country<br>
                <input type="Checkbox" name="styles[]" value="11">Folk<br>
                <input type="Checkbox" name="styles[]" value="12">Funk<br>
                <input type="Checkbox" name="styles[]" value="13">Fusion
                </td>
                   <td width="33%">
                <input type="Checkbox" name="styles[]" value="14">Hard Rock<br>
                <input type="Checkbox" name="styles[]" value="15">Instrumental<br>
                <input type="Checkbox" name="styles[]" value="16">Jazz<br>
                <input type="Checkbox" name="styles[]" value="17">Metal<br>
                <input type="Checkbox" name="styles[]" value="18">Punk<br>
                <input type="Checkbox" name="styles[]" value="19">Undefined
                </td>
            </tr>
            <tr>
                <td colspan="3" class="smalltxt">Check off any/all that apply.<br><br></td>
            </tr>
            </table>
            
            <p>
            <b>Skill:</b><br>
            <table width="100%" cellspacing="0" cellpadding="2" border="0">
            <tr valign="top">
                <td width="33%">
                <input type="Radio" name="exp" value="1"> Studio Professional<br>
                <input type="Radio" name="exp" value="2"> Professional<br>
                <input type="Radio" name="exp" value="3"> Semi-Professional
                </td>
                <td width="34%">
                <input type="Radio" name="exp" value="4"> Part-time Player<br>
                <input type="Radio" name="exp" value="5"> Student
                </td>
                <td width="33%">
                <input type="Radio" name="exp" value="6"> Beginner<br>
                <input type="Radio" name="exp" value="0" checked> Doesn't Matter
                </td>
            </tr>
            </table>
            
            <p>
            <b>Songs:</b><br>
            <table width="100%" cellspacing="0" cellpadding="2" border="0">
            <tr valign="top">
                <td width="33%">
                <input type="Radio" name="songs" value="1"> Originals<br>
                <input type="Radio" name="songs" value="2"> Covers<br>
                </td>
                <td width="34%">
                <input type="Radio" name="songs" value="3"> Mixture of both<br>
                <input type="Radio" name="songs" value="0" checked> Doesn't Matter
                </td>
                <td width="33%"><br></td>
            </tr>
            </table>
            
            <p>
            <b>Situation:</b><br>
            <table width="100%" cellspacing="0" cellpadding="2" border="0">
            <tr valign="top">
                <td width="33%">
                <input type="Radio" name="situation" value="1"> Band needs guitarist<br>
                <input type="Radio" name="situation" value="2"> Looking to join a band
                </td>
                <td width="34%">
                <input type="Radio" name="situation" value="3"> Looking to jam<br>
                <input type="Radio" name="situation" value="4"> In a band
                </td>
                <td width="33%">
                <input type="Radio" name="situation" value="5"> None of the above<br>
                <input type="Radio" name="situation" value="0" checked> Doesn't Matter
                </td>
            </tr>
            </table>
            
            <p>
            <input type="Submit" value="Search" class="button">
            <input type="Button" value="Cancel" onClick="location.href='index.php'" class="button">
            </td>
        </tr>
        </form>
        </table>
        
        </td>
        <?php if ($adPlace == 2) { require("fastclick.php"); } ?>
    </tr>
    </table>

<?php
    // include our footer
    require("footer.php");
?>
