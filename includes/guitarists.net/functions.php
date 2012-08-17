<?php
    /*
        functions.php
        
        This script handles any/all functions developed by myself.  This way we 
        can easily reuse code.  No need to write out a million query statements.  
        They can all be accessed here.  I also use this to build functions to 
        mimic CF code (listgetat, cfquery, etc).
    */
    
    // create our curse filter
    function curseFilter($text) {
        // create our list of curse words
        $fwords="fuck,fuq,phuq,phuck,phuk,fuk,fucker,motherfucker,fuckah,fuckr,phucker,phuker,fuker,motherfuckah,motherfuckr,motherphucker,motherphuker,motherfuker,fuckwad,phuckwad,fukwad,phukwad,fuckhead,fukhead,phukhead,phuckhead,fucking,phucking,phuking,fuking,ffuck,ffucking,fucks,phucks,phuks,fuks,fuckers,motherfuckers,fuckahs,fuckrs,phuckers,phukers,fukers,motherfuckahs,motherfuckrs,motherphuckers,motherphukers,motherfukers,fuckwads,phuckwads,fukwads,phukwads,fuckheads,fukheads,phukheads,phuckheads,fucking,fuckin";
        $swords="shit,shithead,shithole,shitter,shitman,shitpacker,fudgepacker,shits,shitheads,shitholes,shitty";
        $awords="asshole,asswipe,asslicker,fatass,assmunch,asswhole,asswholes,dumbass,piss,assholes,asswipes,asslickers,fatasses";
        $sexwords="cunt,cuntlicker,cocksucker,rugmuncher,cunts,cuntlickers,bitch,pussy,pussie";
        $wordlist = $fwords . "," . $swords . "," . $awords . "," . $sexwords;
        
        // create an array of the words
        $arrWords = explode(",", $wordlist);
        $count = count($arrWords);
        $badCount = 0;
        
        // loop though the array, and see if the text can be found
        for ($i = 0; $i < $count; $i++) {
            // seperate the curse word with a space on either side
            $curse1 = " " . $arrWords[$i] . " ";
            $curse2 = " " . $arrWords[$i] . " ";
            $curse3 = " " . $arrWords[$i] . "\n";
            $curse4 = "\n" . $arrWords[$i] . " ";
            $curst5 = "\n" . $arrWords[$i] . "\n";
            
            // if (stristr($text, $curseLeft) || stristr($text, $curseRight)) {
            // if (stristr($text, $curse1) || stristr($text, $curse2) || stristr($text, $curst3) || stristr($text, $curst4) || stristr($text, $curst5)) {
            if (stristr($text, $arrWords[$i])) {
                // tell them the word that failed
                print "
                <script language=\"JavaScript\">
                alert(\"Your post contains questionable language \\(" . $arrWords[$i] . "\\).  Please change this.\");
                </script>";
                
                $badCount = 1;
                break;
            }
        }
        
        //return this to the calling script
        return $badCount;
    }
    
    // build our recursive category tool
    function show_categories($parent_id="29", $insert_text="", $chosen="0", $dbConn) {
        // get our category list from the db
        $categories = $dbConn->query("
			select     ID,
                                   strTitle
			from       categories
			where      intParent = '" . $parent_id . "'
			order by   strTitle"); 
        
        // loop through the query
        while ($qryRow = $categories->fetchRow(DB_FETCHMODE_ASSOC)) {
            // display our option
            print "
            <option value=\"" . $qryRow["ID"] . "\"";
            
            if ($chosen == $qryRow["ID"]) {
                print " selected";
            }
            
            print ">" . $insert_text . "&raquo;&nbsp;" . $qryRow["strTitle"] . "</option>"; 
            
            // recursively get the next ID
            show_categories($qryRow["ID"], $insert_text."&nbsp;&nbsp;&nbsp;&nbsp;", $chosen, $dbConn); 
        }
        
        // all done!
        return true; 
    }
    
    // create our pagination function
    function f_prevnext($totals, $times, $startRow, $colSpan, $linkText) {
        
        /*
            $totals:    the total number to iterate through
            $times:        the number of steps between
            $startRow:    our currently chosen record
            $colSpan:    number of table columns to span
            $linkText:    our page to link back to
        */
        
        // set our variables we'll use throughout
        $counter = 1;
        $maxiterations = $totals / $times;
        $loops = $maxiterations / $times;
        $ceiling = 20 * $times;
        $currentpage = $startRow;
        $maxpage = (ceil($maxiterations) - 1) * $times;
        
        // decide our start and end measures
        for ($i = 0; $i < $totals; $i = $i + $ceiling) {
            // set our start and end ranges
            $intStart = $i;
            $intEnd = ($intStart + $ceiling) - 1;
            
            // see if our start row is in this set
            if ($startRow >= $intStart && $startRow <= $intEnd) {
                // set our counter starting position
                $counter = round($intStart / $times) + 1;
                break;
            }
        }
        
        // display the total number of pages found
        print "
        <tr>
            <td colspan=\"" . $colSpan . "\"><br />
            <div class=\"pagination\">
            <ul>
            <li><b>Pages</b> (" . ceil($maxiterations) . "):</li>\n";
        
        // display our previous link (if needed)
        if ($intStart > 1) {
            $intPrevious = $intStart - $ceiling;
            print "
            <li><a href=\"" . $linkText . "\"><b>&laquo;&laquo;</b></a>
            <a href=\"" . $linkText . "page=" . $intPrevious . "\"><b>&laquo;</b></a></li>\n";
        }
        
        // now loop through our set areas and display page links
        for ($i = $intStart; $i <= $intEnd; $i = $i + $times) {
            // only display if it's within our result set
            if ($i <= $totals) {
                // see if it's the chosen item
                if ($i == $currentpage) {
                    print "<li class=\"currentpage\">" . $counter . "</li>\n";
                } else {
                    print "<li><a href=\"" . $linkText . "page=" . $i . "\">$counter</a></li>\n";
                }
                
                // update our counter
                $counter++;
            } else {
                break;
            }
        }
        
        // display our previous link (if needed)
        if ($intEnd < $totals) {
            $intNext = $intEnd + 1;
            print " <li><a href=\"" . $linkText . "page=" . $intNext . "\"><b>&raquo;</b></a></li>
            <li><a href=\"" . $linkText . "page=" . $maxpage . "\"><b>&raquo;&raquo;</b></a></li>\n";
        }
        
        // finish off our table
        print "
            </ul>
            </div>
            </td>
        </tr>\n";
    }
    
    // function to add and delete from 'saved'
    function saveItem($type, $item, $memid, $status, $dbConn) {
        /*
            types:
            1 = tab            5 = gear
            2 = threads        6 = resources
            3 = lessons        7 = software
            4 = buddies        8 = music
            
            status:
            1 = add        0 = delete
        */
        
        // depending on the status, continue
        if ($status == 1) {
            // make sure it's not already there
            $qrySaved = $dbConn->query("
                select  intItem
                from    saved
                where   intType = " . $type . " and
                        intMemID = " . $memid . " and
                        intItem = " . $item);
            
            // continue, based on the results
            if (!$qrySaved->numRows()) {
                // add it now
                $qrySave = $dbConn->query("
                    insert into saved (
                        intType,
                        intMemID,
                        intItem
                    ) values (
                        " . $type . ",
                        " . $memid . ",
                        " . $item . "
                    )");
                
                // all done!
                print "
                <script language=\"JavaScript\">
                alert(\"The item was successfully added to your favorites.\");
                </script>";
            } else {
                // if it's already saved, tell them
                print "
                <script language=\"JavaScript\">
                alert(\"You already have this item saved.\");
                </script>";
            }
        } else {
            // they chose to delete it
            $qryKill = $dbConn->query("
                delete
                from    saved
                where   intType = " . $type . " and
                        intMemID = " . $memid . " and
                        intItem = " . $item);
            
            // all done!
            print "<script language=\"JavaScript\">alert(\"The item was successfully removed from your favorites.\");</script>\n";
        }
    }
    
    // our function to see if a value is in our db list
    function checkBlocked($arrName, $value) {
        // set the default result
        $intFound = 0;
        
        // loop through our array, and see if the value is found
        while ($arrVal = mysql_fetch_array($arrName)) {
            print "
            <!-- " . $arrVal["intBlockID"] . " - " . $value . " -->";
            if ($arrVal["intBlockID"] == $value) {
                $intFound = 1;
                break;
            }
        }
        
        // reset the index pointer
        mysql_data_seek($arrName, 0);
        
        // if we've gotten this far, it wasn't found
        return $intFound;
    }
    
    // our time processing calculator
    function microtime_diff($a,$b) {
        list($a_micro, $a_int)=explode(' ',$a);
        list($b_micro, $b_int)=explode(' ',$b);
        
        if ($a_int>$b_int) {
            return ($a_int-$b_int)+($a_micro-$b_micro);
        } elseif ($a_int==$b_int) {
            if ($a_micro>$b_micro) {
                return ($a_int-$b_int)+($a_micro-$b_micro);
            } elseif ($a_micro<$b_micro) {
                return ($b_int-$a_int)+($b_micro-$a_micro);
            } else {
                return 0;
            }
         } else { 
             // $a_int<$b_int
            return ($b_int-$a_int)+($b_micro-$a_micro);
         }
    }
    
    // include our smilies replacer
    function smilies($text,$status) {
        /*
            replaces text with other text, depending on the action chosen
            
            $status:    1 = convert images back to smilies
                        0 = convert smilies to images
        */
        
        // set our array of various charachters
        $arrSmilies = array(":-)",":-(",":-o",":-D",":-P",":-I",":-S",";-)",":@",":-O",":-*",">-@",":)",":(",":P",";)",":D");
        $newText = $text;
        
        // depending on the status, continue
        if (!$status) {
            // loop through and replace any of the text with the image equivalent
            for ($i = 0; $i < count($arrSmilies); $i++) {
                
                // set our position for the images
                $pos = $i + 1;
                
                // replace our strings with the images
                $newText = str_replace($arrSmilies[$i], "<img src=\"/forum/images/smilies/" . $pos . ".gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"Smiley\" align=\"absmiddle\">", $newText);
            }
        } else {
            // convert the images (if any) back to smilies
            for ($i = 0; $i < count($arrSmilies); $i++) {
                // set our position for the images
                $pos = $i + 1;
                
                // create the image text to look for
                $imageText = "<img src=\"/forum/images/smilies/" . $pos . ".gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"Smiley\" align=\"absmiddle\">";
                
                // replace our strings with the images
                $newText = str_replace($imageText, $arrSmilies[$i], $newText);
            }
        }
        
        // return our text back
        return $newText;
    }
    
    // include our smilies replacer
    function smilies2($text,$status) {
        /*
            replaces text with other text, depending on the action chosen
            
            $status:    1 = convert images back to smilies
                        0 = convert smilies to images
        */
        
        // set our array of various charachters
        $arrSmilies = array(":-)" => 1,
                            ":-(" => 26,
                            ":-o" => 3,
                            ":-D" => 4,
                            ":-P" => 5,
                            ":-I" => 6,
                            ":-S" => 7,
                            ";-)" => 8,
                            ":@"  => 17,
                            ":-O" => 10,
                            ":-*" => 13,
                            ">-@" => 2,
                            ":)"  => 1,
                            ":("  => 26,
                            ":P"  => 5,
                            ";)"  => 8,
                            ":D"  => 4);
        $newText = $text;
        
        // depending on the status, continue
        if (!$status) {
            // loop through and replace any of the text with the image equivalent
            foreach ($arrSmilies as $key => $value) {
                // replace our strings with the images
                $newText = str_replace($key, "<img src=\"/images/smilies/" . $value . ".gif\" border=\"0\" alt=\"Smiley\" align=\"absmiddle\">", $newText);
            }
            
            // loop through our numbers and swap out our images
            for ($i = 1; $i <= 74; $i++) {
                // update any text with the appropriate image
                $newText = str_replace(":sm" . $i . ":", "<img src=\"/images/smilies/" . $i . ".gif\" border=\"0\" alt=\"Smiley\" align=\"absmiddle\">", $newText);
            }
        } else {
            // convert the images (if any) back to smilies
            foreach ($arrSmilies as $key => $value) {
                // create the image text to look for
                $imageText = "<img src=\"/images/smilies/" . $value . ".gif\" border=\"0\" alt=\"Smiley\" align=\"absmiddle\">";
                
                // replace our strings with the images
                $newText = str_replace($imageText, ":sm" . $value . ":", $newText);
            }
            
            // loop through our numbers and swap out our images
            for ($i = 1; $i <= 74; $i++) {
                // create the image text to look for
                $imageText = "<img src=\"/images/smilies/" . $i . ".gif\" border=\"0\" alt=\"Smiley\" align=\"absmiddle\">";
                
                // update any text with the appropriate image
                $newText = str_replace($imageText, ":sm" . $i . ":", $newText);
            }
        }
        
        // return our text back
        return $newText;
    }
    
    // build our recursive category tool
    function show_forums($parent_id, $insert_text="", $chosen, $access, $dbConn) {
        // create our SQL
        $sqlText = "
            select  ID,
                    strName,
                    intSort
            from    forums
            where   intParent = " . $parent_id;
        
        // see if they're a mod or not
        if ($access < 90) {
            $sqlText .= " and
                        intActive = 1 ";
        }
        
        $sqlText .= " order by    intSort,
                                strName";
        
        // get our category list from the db
        $categories = $dbConn->query($sqlText);
        
        // loop through the query
        while ($category = $categories->fetchRow(DB_FETCHMODE_ASSOC)) {
            // display our option
            print "
            <option value=\"" . $category["ID"] . "\"";
            
            // if it's the chosen, mark it as selected
            if ($category["ID"] == $chosen) {
                print " selected";
            }
            
            print ">" . $insert_text . "&raquo;&nbsp;" . $category["strName"] . "</option>"; 
            
            // recursively get the next ID
            // show_forums($category["ID"],$insert_text."&nbsp;&nbsp;&nbsp;&nbsp;",$chosen,$_SESSION["AccessLevel"]); 
        }
        
        // all done!
        return true; 
    }
    
    function regkey() {
        // generate a random registration key to confirm the process
        $arrKeyList = array("48","49","50","51","52","53","54","55","56","57","97","98","99","100","101","102","103","104","105","106","107","108","109","110","111","112","113","114","115","116","117","118","119","120","121","122");
        $regKey = "";
        
        // loop through and create our registration key
        for ($i = 1; $i <= 15; $i++) {
            $pos = rand(0,35);
            $regKey .= strtoupper(chr($arrKeyList[$pos]));
        }
        
        // return the results
        return $regKey;
    }
    
    function createNewPassword() {
        // generate a random registration key to confirm the process
        $arrKeyList = array("48","49","50","51","52","53","54","55","56","57","97","98","99","100","101","102","103","104","105","106","107","108","109","110","111","112","113","114","115","116","117","118","119","120","121","122");
        $password = "";
        
        // loop through and create our registration key
        for ($i = 1; $i <= 8; $i++) {
            $pos = rand(0,35);
            $password .= strtoupper(chr($arrKeyList[$pos]));
        }
        
        // return the results
        return $password;
    }
    
    function safeSQL($text, $field) {
        // create an array of bad terms to loop through
        $arrTerms = array("ALTER COLUMN","ALTER TABLE","ANALYZE TABLE","BACKUP TABLE","DELETE FROM","DROP TABLE");
        $intValid = 1;
        
        // check and see if this statement exists in the text passed
        foreach ($arrTerms as $term) {
            if (stristr($text, $term)) {
                print "
                <script language=\"JavaScript\">
                alert(\"Your " . $field . " includes possibly malicious statements \\(" . $term . "\\)\\n\" +
                      \"Please fix and try again.\");
                </script>";
                
                // stop here
                $intValid = 0;
                break;
            }
        }
        
        // all good
        return $intValid;
    }
    
    // create our function to mask email addresses
    function mask_email($email) {
        $email = str_replace("@", " at ", $email);
        $email = str_replace(".", " dot ", $email);
        
        // return the variable
        return $email;
    }
    
    // validate email addresses
    function validate_email($email) {
        // check the passed email address
        if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)){
            return false;
        } else {
            return true;
        }
    }
    
    // function to decipher their access level
    function get_member_level($access, $unique, $banned=0) {
        // display their status
        if ($banned) {
            print "BANNED";
        } else {
            if ($access >= 20 && strlen($unique)) {
                print $unique;
            } else {
                switch ($access) {
                	case 1: print "New Member"; break;
                	case 2: print "Member"; break;
                	case 3: print "Junior Member"; break;
                	case 4: print "Intermediate Member"; break;
                	case 5: print "Advanced Member"; break;
                	case 6: print "Power User"; break;
                	case 10: print "Affiliate"; break;
                	case 11: print "Supporter"; break;
                	case 12: print "Teacher"; break;
                	case 13: print "Advertiser"; break;
                	case 14: print "Preferred Member"; break;
                	case 20: print "Preferred Member"; break;
                	case 90: print "Moderator"; break;
                	case 95: print "Official Editor"; break;
                	case 99: print "Administrator"; break;
                	default: print "Member"; break;
                }
            }
        }
    }
    
    // function to round the the decimal (3.0, 3.5, etc)
    function round_halves($number) {
        $_rating_round_half = round($number / 0.5) * 0.5;
        $_remainder = fmod($_rating_round_half, 1);
        
        $_rating = (int) $_rating_round_half - $_remainder;
        $_half_star = $_remainder == 0.5;
        
        $rating = $_rating + $_half_star;
        
        return $rating;
    }
    
    // our function to swap out text and insert a square banner ad
    function display_square_banner($content, $seperator, $pos) {
        // turn our content into an array
        $arrContent = explode($seperator, $content);
        
        // set the ad in the middle of the text
        $adPos = floor((count($arrContent) - 1) / 2);
        
        // loop through our first half and display
        if (count($arrContent) < 5) {
            print $content;
        } else {
            for ($i = 0; $i < count($arrContent); $i++) {
                // print the code out
                print trim($arrContent[$i]) . "\n<P />\n";
                
                // see if we need to display an ad
                if ($i > 0 && $i % $adPos == 0) {
                    // display our Google ad
                    if (@include(getenv('DOCUMENT_ROOT').'/ads/phpadsnew.inc.php')) {
                        if (!isset($phpAds_context)) $phpAds_context = array();
                        $phpAds_raw = view_raw ('zone:28', 0, '', '', '0', $phpAds_context);
                        echo $phpAds_raw['html'];
                    }
                }
            }
        }
    }
    
    // create a local array of data from the data submitted in a form
    function post_to_array($postData) {
        // create our array we'll use
        $arrData = array();
        
        // loop through our POST fields and add to a local array
        foreach ($postData as $key => $value) {
            // append the data to our array
            $arrData[$key] = trim($value);
        }
        
        // return the array
        return $arrData;
    }
    
    // the function to determine the newest items added to the site
    function gen_new_items($dbConn, $lastLogin) {
        // max the last login date to 30 days
        $lastLogin = strtotime($lastLogin);
        
        // if the last login is older than 30 days, reset it
        if ($lastLogin < (time() - (60 * 60 * 24 * 30))) {
            $lastLogin = time() - (60 * 60 * 24 * 30);
        }
        
        // create our array of newest items
        $arrItems = array();
        
        // check for the newest lessons added
        $qryLessons = $dbConn->query("
            select      lessons.ID,
                        lessons.strTitle,
                        lessons.dateAdded,
                        categories.strTitle as catTitle
            from        lessons,
                        categories
            where       lessons.dateAdded >= '" . date("Y-m-d H:i:s", $lastLogin) . "' and
                        lessons.intActive = 1 and
                        lessons.intCatID = categories.ID
            order by    lessons.dateAdded desc");
        
        // loop through and add our items to our array
        while ($qryRow = $qryLessons->fetchRow(DB_FETCHMODE_ASSOC)) {
            $arrItems["lessons"][] = array(
                                        "ID"       => $qryRow["ID"],
                                        "title"    => $qryRow["strTitle"],
                                        "category" => $qryRow["catTitle"],
                                        "date"     => $qryRow["dateAdded"]
                                           );
        }
        
        // check for the newest lessons added
        $qryGear = $dbConn->query("
            select      ratings.ID,
                        ratings.dateAdded,
                        gear.ID as gearID,
                        gear.strModelName,
                        makers.strCompany,
                        categories.strTitle
            from        ratings,
                        gear,
                        makers,
                        categories
            where       ratings.intArea = 1 and
                        ratings.intActive = 1 and
                        ratings.dateAdded>= '" . date("Y-m-d H:i:s", $lastLogin) . "' and
                        ratings.intItemID = gear.ID and
                        gear.intCompany = makers.ID and
                        gear.intOrigID = 0 and
                        gear.intType = categories.ID
            order by    ratings.dateAdded desc");
        
        // display our data
        while ($qryRow = $qryGear->fetchRow(DB_FETCHMODE_ASSOC)) {
            $arrItems["gear"][] = array(
                                    "ID"       => $qryRow["ID"],
                                    "title"    => $qryRow["strCompany"] . " " . $qryRow["strModelName"],
                                    "category" => $qryRow["strTitle"],
                                    "date"     => $qryRow["dateAdded"]
                                       );
        }
        
        // check for the newest lessons added
        $qrySongs = $dbConn->query("
            select      music.ID,
                        music.Title,
                        music.DateAdded,
                        categories.strTitle as catName
            from        music,
                        categories
            where       music.DateAdded >= '" . date("Y-m-d H:i:s", $lastLogin) . "' and
                        music.Active = 1 and
                        music.CategoryID = categories.ID
            order by    music.DateAdded desc");
        
        // loop through our results (if any)
        while ($qryRow = $qrySongs->fetchRow(DB_FETCHMODE_ASSOC)) {
            $arrItems["songs"][] = array(
                                     "ID"       => $qryRow["ID"],
                                     "title"    => $qryRow["Title"],
                                     "category" => $qryRow["catName"],
                                     "date"     => $qryRow["DateAdded"]
                                        );
        }
        
        // query the newest topics and forums in these categories
        $qryForums = $dbConn->query("
            select      COUNT(topics.ID) as totals,
                        topics.intForum,
                        topics.dateLastPost,
                        forums.strName
            from        topics,
                        forums
            where       topics.intForum NOT IN (11,12,14,15,16,17,20,21,22,23,24,25,26,28,30,31,32,36,37,38) and
                        (topics.datePosted >= '" . date("Y-m-d H:i:s", $lastLogin) . "' or
                         topics.dateLastPost >= '" . date("Y-m-d H:i:s", $lastLogin) . "') and
                        topics.intForum = forums.ID
            group by    topics.intForum
            order by    topics.dateLastPost desc");
        
        // loop through our results, and display if needed
        while ($qryRow = $qryForums->fetchRow(DB_FETCHMODE_ASSOC)) {
            $arrItems["forums"][] = array(
                                      "ID"       => $qryRow["intForum"],
                                      "title"    => $qryRow["strName"],
                                      "category" => $qryRow["totals"],
                                      "date"     => $qryRow["dateLastPost"]
                                         );
        }
        
        // check for the newest lessons added
        $qrySoftware = $dbConn->query("
            select      software.ID,
                        software.strName,
                        software.dateAdded,
                        categories.strTitle
            from        software,
                        categories
            where       software.intActive = 1 and
                        software.dateAdded >= '" . date("Y-m-d H:i:s", $lastLogin) . "' and
                        software.intCatID = categories.ID
            order by    software.dateAdded desc");
        
        // loop through our results (if any)
        while ($qryRow = $qrySoftware->fetchRow(DB_FETCHMODE_ASSOC)) {
            $arrItems["software"][] = array(
                                        "ID"       => $qryRow["ID"],
                                        "title"    => $qryRow["strName"],
                                        "category" => $qryRow["strTitle"],
                                        "date"     => $qryRow["dateAdded"]
                                           );
        }
        
        // return the array
        return $arrItems;
    }
    
    // our function to kill bad chars in text
    function swap_bad_post_data($text) {
        // create our array of characters
        $arrChars = array(226 => "", 128 => "", 132 => "", 152 => "'", 153 => "'", 156 => '"', 157 => '"', 162 => "<sup>TM</sup>", 174 => "&reg;", 194 => "", "226" => "");
        
        // loop through our char array and replace values
        foreach ($arrChars as $ord => $value) {
            // replace the values
            $text = str_replace(chr($ord), $arrChars[$ord], $text);
        }
        
        // return our text
        return $text;
    }
    
    // our function to find all files with a given extension in a given directory
    function create_lotd_array($path, $type) {
        // create our end array
        $licks = array();
        
        // set the full path
        $files = $path . "/*" . $type;
        
        // return the list of files
        $list = glob($files);
        
        // loop through our files and add the data to our array
        foreach ($list as $file) {
            $ini = parse_ini_file($file);
            
            // add this data to our array
            $licks[] = $ini;
        }
        
        // sort our files by date
        usort($licks, "compare_lick_dates");
        
        // return the array
        return $licks;
    }
    
    // our function to sort our dates by newest to oldest
    function compare_lick_dates($x, $y) {
        if ($x["Date"] == $y["Date"]) {
            return 0;
        } else if ($x["Date"] < $y["Date"]) {
            return 1;
        } else {
            return -1;
        }
    }
?>