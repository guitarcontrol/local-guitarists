<?php
    
    /*
        demo.php
        
        Allows a member to update their demographic information for other players 
        to be able to find them easily.
    */
    
    // include our needed file(s)
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    
    // see if they're logged in
    verify_access($_SESSION["AccessLevel"], 1);
    
    // query the data from 'about' the db
    $qryInfo = $dbConn->getRow("
        select  ID,
                strCity,
                intState,
                strState,
                strZipCode,
                intCountry
        from    about
        where   intMemID = '" . $_SESSION["MemberID"] . "'",
        DB_FETCHMODE_ASSOC);
    
    // stop, if no records were found
    if (!count($qryInfo)) {
        $qryInfo["ID"] = 0;
        $qryInfo["strCity"] = "";
        $qryInfo["intState"] = 0;
        $qryInfo["strState"] = "";
        $qryInfo["strZipCode"] = "";
        $qryInfo["intCountry"] = 0;
    }
    
    // query our states from the db
    $qryStates = $dbConn->query("
        select        ID,
                    strName
        from        states
        order by    strName");
    
    // query our states from the db
    $qryCountries = $dbConn->query("
        select        *
        from        countries
        order by    intSort,
                    strCountry");
    
    // create our page variables
    $pageTitle = "Members Area: Edit your demographic information";
    $pageDescription = "";
    $pageKeywords = "";
    $areaName = "members";
    
    // include our header file
    require("header.php");
?>
    
    <br>
    <table width="100%" cellspacing="0" cellpadding="2" border="0">
    <tr>
        <td class="tablehead">&nbsp;&raquo;&nbsp;<a href="/index.php"><b>Home</b></a>&nbsp;&raquo;&nbsp;<a href="/members/index.php"><b>Members Area</b></a>&nbsp;&raquo;&nbsp;Edit your Demographic Info</td>
    </tr>
    </table>
    
    <div align="center">
    <table width="600" cellspacing="0" cellpadding="2" border="0">
    <form name="myInfo" action="update_demo.php" method="post" onSubmit="return checkDemoInfo()">
    <input type="Hidden" name="ID" value="<?php print $_SESSION["MemberID"]; ?>">
    <input type="Hidden" name="aboutID" value="<?php print $qryInfo["ID"]; ?>">
    <tr>
        <td align="right"><b>City:</b> </td>
        <td><input type="text" name="strCity" value="<?php print trim($qryInfo["strCity"]); ?>" size="35" maxlength="60" class="input"></td>
    </tr>
    <tr>
        <td align="right"><b>State:</b> </td>
        <td>
        <select name="intState" class="dropdown">
            <option value="0"<?php if ($qryInfo["intState"] == 0) { print " selected"; } ?>> N/A</option>
        <?php
            // loop through our state query
            while ($qryRow = $qryStates->fetchRow(DB_FETCHMODE_ASSOC)) {
                print "
                <option value=\"" . $qryRow["ID"] . "\"";
                if ($qryInfo["intState"] == $qryRow["ID"]) {
                    print " selected";
                }
                print "> " . $qryRow["strName"] . "</option>";
            }
        ?>
        </select>
        </td>
    </tr>
    <tr>
        <td align="right"><b>Other:</b> </td>
        <td><input type="text" name="strState" size="35" value="<?php print trim($qryInfo["strState"]); ?>" maxlength="50" class="input"></td>
    </tr>
    <tr>
        <td></td>
        <td class="smalltxt">If outside the United States.</td>
    </tr>
    <tr>
        <td colspan="2"><br></td>
    </tr>
    <tr>
        <td align="right"><b>Zip Code:</b> </td>
        <td><input type="text" name="strZipCode" value="<?php print $qryInfo["strZipCode"]; ?>" size="5" maxlength="10" class="input"></td>
    </tr>
    <tr>
        <td align="right"><b>Country:</b> </td>
        <td>
        <select name="intCountry" class="dropdown">
        <?php
            // loop through our countries
            while ($qryRow = $qryCountries->fetchRow(DB_FETCHMODE_ASSOC)) {
                print "
                <option value=\"" . $qryRow["ID"] . "\"";
                if ($qryInfo["intCountry"] == $qryRow["ID"]) {
                    print " selected";
                }
                print "> " . $qryRow["strCountry"] . "</option>";
            }
        ?>
        </select>
        </td>
    </tr>
    <tr>
        <td colspan="2"><br></td>
    </tr>
    <tr>
           <td></td>
           <td>
        <input type="submit" name="action" value="Update Information" class="button">
        <input type="Button" value="Cancel" onclick="location.href='index.php'" class="button">
        </td>
    </tr>
    </table>
    </form>
    </div>
    
<?php
    // include our footer
    require("footer.php");
?>