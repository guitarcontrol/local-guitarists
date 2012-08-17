<?php
    /*
        leaderboard.php
        
        This script determines what banners to display and when.  Based on the page we're in, we'll
        display the appropriate banner (lessons, tablature, etc).
    */
    
    // for redundancy
    $leaderDisplayed = 0;
    $googleDisplayed = 0;
    $localDisplayed = 0;
    
    // create an array of area names and their zones
    $arrZones = array("tab"      => 5,
                      "lessons"  => 7,
                      "gear"     => 8,
                      "music"    => 9,
                      "forums"   => 10,
                      "software" => 11,
                      "chords"   => 13,
                      "scales"   => 14,
                      "tunings"  => 12,
                      "home"     => 6,
                      "news"     => 34,
                      "default"  => 4);
    
    // start our leaderboard cell
    print "<tr><td colspan=\"2\" bgcolor=\"#FFFFFF\" class=\"content\" align=\"center\">\n";
    print "<!-- loading banners for " . $areaName . " -->\n";
    // based on the existence of our area, display our ad
    if (!empty($arrZones[$areaName])) {
        // display this zones ad
        if (@include(getenv('DOCUMENT_ROOT').'/ads/phpadsnew.inc.php')) {
            if (!isset($phpAds_context)) $phpAds_context = array();
            $phpAds_raw = view_raw ('zone:' . $arrZones[$areaName], 0, '', '', '0', $phpAds_context);
            echo $phpAds_raw['html'];
        }
    } else {
        // display the default ad
        if (@include(getenv('DOCUMENT_ROOT').'/ads/phpadsnew.inc.php')) {
            if (!isset($phpAds_context)) $phpAds_context = array();
            $phpAds_raw = view_raw ('zone:' . $arrZones["default"], 0, '', '', '0', $phpAds_context);
            echo $phpAds_raw['html'];
        }
    }
    
    // end the cell
    print "</td></tr>\n";
?>
