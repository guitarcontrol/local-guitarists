<?php
    /*
        fastclick.php
        
        This script allows me to display any type of banner I choose on a page.  
        We can easily maintain our own banners, and link to whomever we like.
    */
    
    // see if they have banners turned off
    if (empty($_SESSION["HideAds"])) {
        // create an array of area names and their zones
        $arrZones = array("tab"      => 18,
                          "lessons"  => 19,
                          "gear"     => 20,
                          "music"    => 21,
                          "forums"   => 22,
                          "software" => 23,
                          "chords"   => 25,
                          "scales"   => 26,
                          "tunings"  => 24,
                          "home"     => 17,
                          "news"     => 35,
                          "default"  => 16);
        
        // start our leaderboard cell
        print "<td align=\"center\" width=\"130\" class=\"smalltxt\">\n";
        print "<!-- loading skyscraper for " . $areaName . " -->\n";
        
        // based on the existence of our area, display our ad
        if (!empty($arrZones[$areaName])) {
            // display this zones ad
            if (@include(getenv('DOCUMENT_ROOT').'/ads/phpadsnew.inc.php')) {
                if (!isset($phpAds_context)) $phpAds_context = array();
                $phpAds_raw = view_raw ('zone:' . $arrZones[$areaName], 0, '', '', '0', $phpAds_context);
                echo $phpAds_raw['html'];
            }
        } else {
            // display this zones ad
            if (@include(getenv('DOCUMENT_ROOT').'/ads/phpadsnew.inc.php')) {
                if (!isset($phpAds_context)) $phpAds_context = array();
                $phpAds_raw = view_raw ('zone:' . $arrZones["default"], 0, '', '', '0', $phpAds_context);
                echo $phpAds_raw['html'];
            }
        }
        
        // end our cell
        print "</td>\n";
    } else {
        print "<td width=\"1\">&nbsp;</td>\n";
    }
?>
