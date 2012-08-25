<?php
    /*
        playing.php
        
        This allows a member to change the list of gear they use, their influences,
        pertinent info about their style, and more.
        
    */
    
    // include our needed file(s)
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    
    // see if they're logged in
    verify_access($_SESSION["AccessLevel"], 1);
    
    // get our member info from 'about'
    $qryInfo = $dbConn->getRow("
        select  ID,
                txtGear,
                txtStyles,
                intPlayYears,
                intExperience,
                intSongTypes,
                intSituation,
                txtInfluences,
                txtComments,
                strURL
        from    about
        where   intMemID = '" . $_SESSION["MemberID"] . "'",
        DB_FETCHMODE_ASSOC);
    
    // set the number of records were found
    $crlf = chr(10);
    
    // stop, if no records were found
    if (count($qryInfo)) {
        // create an array from our style choices
        $arrFavs = explode(",", $qryInfo["txtInfluences"]);
    } else {
        // create empty vars
        $qryInfo["ID"] = 0;
        $qryInfo["txtGear"] = "";
        $qryInfo["txtStyles"] = "";
        $qryInfo["intPlayYears"] = 0;
        $qryInfo["intExperience"] = 0;
        $qryInfo["intSongTypes"] = 0;
        $qryInfo["intSituation"] = 0;
        $qryInfo["txtInfluences"] = "";
        $qryInfo["txtComments"] = "";
        $qryInfo["strURL"] = "";
    }
    
    // get all of the styles they have setup
    $qryMemStyles = $dbConn->query("
        select  styleid
        from    member_styles
        where   memid = '" . $_SESSION["MemberID"] . "'");
    
    // loop through and add to our styles array
    while ($qryRow = $qryMemStyles->fetchRow(DB_FETCHMODE_ASSOC)) {
        $arrStyles[] = $qryRow["styleid"];
    }
    
    // get the styles from the db to choose from
    $qryStyles = $dbConn->query("
        select      *
        from        styles
        order by    sort");
    
    // set the cutoff for each column to display
    $cutoff = ceil($qryStyles->numRows() / 3);
    
    // create our variables
    $pageTitle = "Members Area: Edit your playing information";
    $pageDescription = "";
    $pageKeywords = "";
    $areaName = "members";
    
    // include our header
    //require("header.php");
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
        <td class="tablehead">&nbsp;&raquo;&nbsp;<a href="/forum_ggc/index.php"><b>Home</b></a>&nbsp;&raquo;&nbsp;<a href="/members_ggc/index.php"><b>Members Area</b></a>&nbsp;&raquo;&nbsp;Edit your Playing Info</td>
    </tr>
    </table>
    
    <div align="center">
    <table width="600" cellspacing="0" cellpadding="1" border="0">
    <form name="myInfo" action="update_playing.php" method="post" onSubmit="return checkPlayInfo()">
    <input type="Hidden" name="ID" value="<?php print $qryInfo["ID"]; ?>">
    <input type="Hidden" name="memID" value="<?php print $_SESSION["MemberID"]; ?>">
    <tr valign="top">
        <td align="right"><b>Equipment:</b> </td>
        <td colspan="3"><textarea name="txtGear" cols="55" rows="8" wrap="off" class="input"><?php print trim($qryInfo["txtGear"]); ?></textarea></td>
    </tr>
    <tr valign="top">
        <td></td>
        <td colspan="3" class="smalltxt">One item per line. Also, feel free to <a href="/gear/submit.php"><b>add reviews</b></a> 
        of all your gear.<br><br></td>
    </tr>
    <tr valign="top">
        <td align="right"><b>Style(s):</b> </td>
        <td colspan="3">
    
        <table width="100%" cellspacing="0" cellpadding="1" border="0">
        <tr valign="top">
            <td width="33%">
                <?php
                    // set a counter
                    $counter = 1;
                    
                    // loop through our query results, breaking them up into columns
                    while ($qryRow = $qryStyles->fetchRow(DB_FETCHMODE_ASSOC)) {
                        ?>
                        <input type="Checkbox" name="styles[]" value="<?php print $qryRow["ID"]; ?>"<?php if (count($arrStyles) && in_array($qryRow["ID"], $arrStyles)) { print " checked"; } ?>> <?php print $qryRow["style"]; ?><br />
                        <?php
                        // see if we need to start a new column
                        if ($counter == $cutoff) {
                            ?>
                            </td>
                            <td width="33%">
                            <?php
                            // reset the counter
                            $counter = 1;
                        }
                        
                        // update the counter
                        $counter++;
                    }
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="3" class="smalltxt"><br />Check off any/all that apply.<br><br></td>
        </tr>
        </table>

        </td>
    </tr>
    <tr>
        <td align="right"><b>Years:</b></td>
        <td>
        <select name="intPlayYears" class="dropdown">
            <option value="0"<?php if ($qryInfo["intPlayYears"] == 0) { print " selected"; } ?>> Beginner
            <?php
                // loop through our years
                for ($i = 1; $i <= 50; $i++) {
                    print "
                    <option value=\"" . $i . "\"";
                    if ($qryInfo["intPlayYears"] == $i) {
                        print " selected";
                    }
                    print "> " . $i . " year";
                    if ($i != 1) {
                        print "s";
                    }
                    print "</option>";
                }
            ?>
        </select></td>
        <td align="right"><b>Skill:</b></td>
        <td><select name="intExperience" class="dropdown">
            <option value="0"<?php if ($qryInfo["intExperience"] == 0) { print " selected"; } ?>> [ Choose One ]
            <option value="1"<?php if ($qryInfo["intExperience"] == 1) { print " selected"; } ?>> Studio Professional
            <option value="2"<?php if ($qryInfo["intExperience"] == 2) { print " selected"; } ?>> Professional
            <option value="3"<?php if ($qryInfo["intExperience"] == 3) { print " selected"; } ?>> Semi-Professional
            <option value="4"<?php if ($qryInfo["intExperience"] == 4) { print " selected"; } ?>> Part-time Player
            <option value="5"<?php if ($qryInfo["intExperience"] == 5) { print " selected"; } ?>> Student
            <option value="6"<?php if ($qryInfo["intExperience"] == 6) { print " selected"; } ?>> Beginner
        </select></td>
    </tr>
    <tr>
        <td align="right"><b>Songs:</b></td>
        <td><select name="intSongTypes" class="dropdown">
            <option value="0"<?php if ($qryInfo["intSongTypes"] == 0) { print " selected"; } ?>> [ Choose A Type ]
            <option value="1"<?php if ($qryInfo["intSongTypes"] == 1) { print " selected"; } ?>> Originals
            <option value="2"<?php if ($qryInfo["intSongTypes"] == 2) { print " selected"; } ?>> Covers
            <option value="3"<?php if ($qryInfo["intSongTypes"] == 3) { print " selected"; } ?>> Mixture of both
        </select></td>
        <td align="right"><b>Situation:</b></td>
        <td><select name="intSituation" class="dropdown">
            <option value="0"<?php if ($qryInfo["intSituation"] == 0) { print " selected"; } ?>> [ Choose One ]
            <option value="1"<?php if ($qryInfo["intSituation"] == 1) { print " selected"; } ?>> Band needs guitarist
            <option value="2"<?php if ($qryInfo["intSituation"] == 2) { print " selected"; } ?>> Looking to join a band
            <option value="3"<?php if ($qryInfo["intSituation"] == 3) { print " selected"; } ?>> Looking to jam
            <option value="4"<?php if ($qryInfo["intSituation"] == 4) { print " selected"; } ?>> In a band
            <option value="5"<?php if ($qryInfo["intSituation"] == 5) { print " selected"; } ?>> None of the above
        </select></td>
    </tr>
    <tr>
        <td colspan="4"><br></td>
    </tr>
    <tr valign="top">
        <td align="right"><b>Influences:</b></td>
        <td colspan="3"><textarea name="txtInfluences" cols="70" rows="8" class="input"><?php foreach($arrFavs as $fav) { print $fav . chr(10); } ?></textarea></td>
    </tr>
    <tr valign="top">
        <td></td>
        <td colspan="3" class="smalltxt">One musician per line.</td>
    </tr>
    <tr>
        <td colspan="4"><br></td>
    </tr>
    <tr valign="top">
        <td align="right"><b>Comments:</b></td>
        <td colspan="3"><textarea name="txtComments" cols="70" rows="8" class="input"><?php print trim($qryInfo["txtComments"]); ?></textarea></td>
    </tr>
    <tr valign="top">
        <td></td>
        <td colspan="3" class="smalltxt">General comments about you, your band, etc.<br><br></td>
    </tr>
    <tr>
        <td align="right"><b>URL:</b></td>
        <td colspan="3"><input type="text" name="strURL" value="<?php print trim($qryInfo["strURL"]); ?>" size="50" maxlength="150" class="input"></td>
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
    </form>
    </table>
    </div>
    
<?php
    // include our footer file
    require("footer.php");
?>
