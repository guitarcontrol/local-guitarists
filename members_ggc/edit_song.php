<?php
    // include our needed file(s)
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    require("functions.php");
    
    // make sure they're logged in
    verify_access($_SESSION["AccessLevel"], 1);
    
    // query the db for this song info
    $qrySong = $dbConn->getRow("
        select  *
        from    music
        where   ID = " . $dbConn->quote($_GET["id"]) . "",
        DB_FETCHMODE_ASSOC);
    
    // set our page variables
    $pageTitle = "Guitar Resources: Members Area: Edit '" . $qrySong["Title"] . "'";
    $pageDescription = "";
    $pageKeywords = "";
    $areaName = "members";
    
    // include our header
    require("header.php");
?>
    
    <br>
    <table width="100%" cellspacing="0" cellpadding="2" border="0">
    <tr>
        <td class="tablehead">&nbsp;&raquo;&nbsp;<a href="/index.php"><b>Home</b></a>&nbsp;&raquo;&nbsp;<a href="/members_ggc/index.php"><b>Members Area</b></a>&nbsp;&raquo;&nbsp;Edit '<?php print $qrySong["Title"]; ?>'</td>
    </tr>
    </table>
    
    <table width="100%" cellspacing="0" cellpadding="2" border="0">
    <tr valign="top">
        <?php if ($adPlace == 1) { require("fastclick.php"); } ?>
        <td>
        <table width="100%" cellspacing="0" cellpadding="1" border="0">
        <tr>
            <td colspan="2">
            Use the following form to edit your song posted in the "Our Music" section.
            <br><br>
            </td>
        </tr>
        <form name="mySong" action="update_song.php" method="post" onSubmit="return valSongPost()">
        <input type="Hidden" name="ID" value="<?php print $qrySong["ID"]; ?>">
        <tr>
            <td width="130"><b>Category:</b></td>
            <td>
            <select name="CategoryID" class="dropdown">
                <option value=""> [ Choose a Category ]
                <?php
                    show_categories(88, "", $qrySong["CategoryID"], $dbConn);
                ?>
            </select>
            </td>
        </tr>
        <tr>
            <td colspan="2"><br></td>
        </tr>
        <tr>
            <td nowrap><b>Title:</b></td>
            <td><input type="Text" name="Title" value="<?php print $qrySong["Title"]; ?>" size="50" maxlength="100" class="input"></td>
        </tr>
        <tr>
            <td></td>
            <td class="smalltxt">Example: Peruvian Skies</td>
        </tr>
        <tr>
            <td colspan="2"><br></td>
        </tr>
        <tr>
            <td nowrap><b>File Size:</b></td>
            <td><input type="Text" name="FileSize" value="<?php print $qrySong["FileSize"]; ?>" size="10" maxlength="20" class="input"></td>
        </tr>
        <tr>
            <td></td>
            <td class="smalltxt">Example: 3.2MB</td>
        </tr>
        <tr>
            <td colspan="2"><br></td>
        </tr>
        <tr>
            <td nowrap><b>Bit Rate:</b></td>
            <td>
            <select name="BitRate" class="dropdown">
                <option value="1"<?php if ($qrySong["BitRate"] == 1) { print " selected"; } ?>> 80 kbps</option>
                <option value="2"<?php if ($qrySong["BitRate"] == 2) { print " selected"; } ?>> 96 kbps</option>
                <option value="3"<?php if ($qrySong["BitRate"] == 3) { print " selected"; } ?>> 112 kbps</option>
                <option value="4"<?php if ($qrySong["BitRate"] == 4) { print " selected"; } ?>> 128 kbps</option>
                <option value="5"<?php if ($qrySong["BitRate"] == 5) { print " selected"; } ?>> 160 kbps</option>
                <option value="6"<?php if ($qrySong["BitRate"] == 6) { print " selected"; } ?>> 192 kbps</option>
                <option value="7"<?php if ($qrySong["BitRate"] == 7) { print " selected"; } ?>> 224 kbps</option>
                <option value="8"<?php if ($qrySong["BitRate"] == 8) { print " selected"; } ?>> 256 kbps</option>
                <option value="9"<?php if ($qrySong["BitRate"] == 9) { print " selected"; } ?>> 320 kbps</option>
            </select>
            </td>
        </tr>
        <tr>
            <td></td>
            <td class="smalltxt">The bitrate the song was encoded in.</td>
        </tr>
        <tr>
            <td colspan="2"><br></td>
        </tr>
        <tr>
            <td nowrap><b>Brief Description:</b></td>
            <td><input type="Text" name="Blurb" value="<?php print $qrySong["Blurb"]; ?>" size="60" maxlength="250" class="input"></td>
        </tr>
        <tr>
            <td></td>
            <td class="smalltxt">A short description (250 characters or less).</td>
        </tr>
        <tr>
            <td colspan="2"><br></td>
        </tr>
        <tr valign="top">
            <td nowrap><b>Long Description:</b></td>
            <td><textarea name="Description" cols="65" rows="10" wrap="off" class="input"><?php print $qrySong["Description"]; ?></textarea></td>
        </tr>
        <tr>
            <td colspan="2"><br></td>
        </tr>
        <tr valign="top">
            <td nowrap><b>Gear Used:</b></td>
            <td><textarea name="GearUsed" cols="65" rows="10" wrap="off" class="input"><?php print $qrySong["GearUsed"]; ?></textarea></td>
        </tr>
        <tr>
            <td></td>
            <td class="smalltxt">One item per line.</td>
        </tr>
        <tr>
            <td colspan="2"><br></td>
        </tr>
        <tr>
            <td nowrap><b>Download URL:</b></td>
            <td><input type="Text" name="SongURL" value="<?php print $qrySong["SongURL"]; ?>" size="50" maxlength="250" class="input"></td>
        </tr>
        <tr>
            <td></td>
            <td class="smalltxt">Example: http://www.yourdomain.com/your_song.mp3</td>
        </tr>
        <tr>
            <td colspan="2"><br></td>
        </tr>
        <tr>
            <td nowrap><b>Image URL:</b></td>
            <td><input type="Text" name="ImageURL" value="<?php print $qrySong["ImageURL"]; ?>" size="50" maxlength="250" class="input"></td>
        </tr>
        <tr>
            <td></td>
            <td class="smalltxt">Used to display your photo with the post, an album cover, etc.</td>
        </tr>
        <tr>
            <td colspan="2"><br></td>
        </tr>
        <tr>
            <td nowrap><b>Active:</b></td>
            <td>
            <input type="Radio" name="Active" value="1"<?php if ($qrySong["Active"] == 1) { print " checked"; } ?> /> Yes
            <input type="Radio" name="Active" value="0"<?php if ($qrySong["Active"] == 0) { print " checked"; } ?> /> No
            </td>
        </tr>
        <tr>
            <td></td>
            <td class="smalltxt">
            Inactive posts will still be in the database.  You can update it at anytime via 
            your <a href="/members_ggc/index.php"><b>account editor</b></a>.
            </td>
        </tr>
        <tr>
            <td></td>
            <td><br>
            <input type="submit" name="action" value="Update Song &raquo;" class="button">
            <input type="reset" value="Clear" class="button">
            <input type="button" value="Cancel" onClick="location.href='index.php'" class="button">
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