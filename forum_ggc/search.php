<?php
    /*
        search.php
        
        This script allows a visitor (member or otherwise) to search any/all of the 
        threads posted to the site. It's based on a boolean search. They'll choose 
        their keywords, a timeframe to search by, and any/all forums. The results 
        will then display.
    */
    
    // include our needed file(s)
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");

    // query the main topics from the db
    if ($_SESSION["AccessLevel"] < 90) {
        $sqlText = "
            select        ID,
                        strName
            from        forums
            where        intPrivate = 0
                        and intActive = 1
            order by    strName";
    } else {
        $sqlText = "
                        select        ID,
                        strName
            from        forums
            order by    strName";
    }
    
    // process our query
    $qryForums = $dbConn->query($sqlText);
    
    // set our variables
    $pageTitle = "Guitar Forums: Forum Search";
    $pageDescription = "Search our forums for keywords, and view the results.";
    $pageKeywords = "guitar, chat, forum, topic, thread, guitarists, guitars";
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
    
    <table width="100%" cellspacing="0" cellpadding="1" border="0">
    <tr valign="top">
        <td align="center">
        
        <table width="720" cellspacing="1" cellpadding="3" border="0">
        <form name="searchIT" action="searching.php" method="get" onSubmit="return valSearchIt()">
        <input type="hidden" name="ggc" value="" />
        <tr>
            <td class="tablehead">&nbsp;&raquo;&nbsp;<a href="/index.php"><b>Home</b></a>&nbsp;&raquo;&nbsp;<a href="/forum_ggc/index.php"><b>Guitar Discussions</b></a>&nbsp;&raquo;&nbsp;Forum Search</td>
        </tr>
        </table>
<?php } else { ?>
<form name="searchIT" action="searching.php" method="get" onSubmit="return valSearchIt()">
<input type="hidden" name="ggc" value="<?php print $_SESSION["GGCIFrame"]; ?>" />
<?php }?>
        
        <table width="720" cellspacing="0" cellpadding="2" border="0">
        <tr>
            <td align="right" nowrap><b>Search Phrase:</b></td>
            <td><input type="text" name="terms" size="50" class="input"></td>
        </tr>
        <tr>
            <td></td>
            <td class="smalltxt">
            Separate your search terms with spaces.
            </td>
        </tr>
        <tr>
            <td colspan="2"><br /></td>
        </tr>
        <tr>
            <td align="right"><b>Username:</b></td>
            <td><input type="text" name="username" size="20" class="input"></td>
        </tr>
        <tr>
            <td></td>
            <td class="smalltxt">
            Enter a username or user ID to limit results to posts they've started and/or replied to.
            </td>
        </tr>
        <tr>
            <td colspan="2"><br /></td>
        </tr>
        <tr>
            <td align="right"><b>Search Option:</b></td>
            <td>
            <input type="Radio" name="searchOption" value="1" checked> Topics
            <input type="Radio" name="searchOption" value="2"> Replies
            </td>
        </tr>
        <tr>
            <td></td>
            <td class="smalltxt">
            What part of the database should we search.
            </td>
        </tr>
        <tr>
            <td colspan="2"><br /></td>
        </tr>
        <tr>
            <td align="right"><b>Search In:</b></td>
            <td>
            <input type="Radio" name="searchIn" value="1" checked> Title
            <input type="Radio" name="searchIn" value="2"> Body
            <input type="Radio" name="searchIn" value="3"> Both
            </td>
        </tr>
        <tr>
            <td></td>
            <td class="smalltxt">
            How we should restrict our search (title only, message body only, or both).
            </td>
        </tr>
        <tr>
            <td colspan="2"><br /></td>
        </tr>
        <tr>
            <td align="right"><b>Last:</b></td>
            <td>
            <select name="days" class="dropdown">
                <option value="1"> Yesterday</option>
                <option value="3"> 3 Days</option>
                <option value="10"> 10 Days</option>
                <option value="15"> 15 Days</option>
                <option value="30" selected> 30 Days</option>
                <option value="60"> 60 Days</option>
                <option value="90"> 90 Days</option>
                <option value=""> All Days</option>
            </select>
            </td>
        </tr>
        <tr>
            <td></td>
            <td class="smalltxt">
            The number of days to search back (all days has no limit).
            </td>
        </tr>
        <tr>
            <td colspan="2"><br /></td>
        </tr>
        <tr valign="top">
            <td align="right"><b>Forums:</b></td>
            <td>
            <select name="forumID" class="dropdown" size="10">
                <option value="0" selected> All Forums</option>
                <?php
                // loop through our results
                while ($qryRow = $qryForums->fetchRow(DB_FETCHMODE_ASSOC)) {
                    ?>
                    <option value="<?php print $qryRow["ID"]; ?>"> &raquo; <?php print $qryRow["strName"]; ?></option>
                    <?php
                }
                ?>
            </select>
            </td>
        </tr>
        <tr>
            <td></td>
            <td class="smalltxt">
            What forum(s) to search in.
            </td>
        </tr>
        <tr>
            <td colspan="2"><br /></td>
        </tr>
        <tr>
            <td align="right"><b>Sort By:</b></td>
            <td>
            <select name="sort" class="dropdown">
                <option value="1" selected> Last Post Date</option>
                <option value="2"> Title</option>
                <option value="3"> # of Views</option>
                <option value="4"> # of Replies</option>
            </select>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
            <input type="Radio" name="order" value="asc" checked> Ascending
            <input type="Radio" name="order" value="desc"> Descending
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td><br />
            <input type="Submit" value="Search" class="button">
            <input type="Button" value="Cancel" onClick="history.back()" class="button">
            </td>
        </tr>
        </table>
        </form>
        
        </td>
    </tr>
    </table>
    
    <script language="JavaScript">
    function valSearchIt() {
        // set our message
        var strMessage = "The following fields are required:\n";
        var strTerms = document.searchIT.terms.value;
        var intCount = 0;
        
        // make sure they supplied what we need
        if (strTerms == "") { strMessage += " - Search term\n"; intCount++; }
        
        // continue, based on the results
        if (intCount > 0) {
            alert(strMessage);
            return false;
        }
        
        // make sure the term is 4 chars or more
        if (strTerms.length < 3) {
            alert("Please choose a phrase 4 characters or more in length.");
            return false;
        }
        
        // all done!
        return true;
    }
    </script>
    
<?php
    // include our 
    if (empty($_SESSION["GGCIFrame"])) {
        require("footer.php");
    }
?>
