<?php
    /*
        ads.php
        
        This script creates our ad options to display on the site.
    */
    
    // create our array of Google colors
    /*$arrBorderCol = array("336699","000000","B4D0DC","A8DDA0","DDB7BA","FDEFD2","E0FFE3","F9DFF9",
                          "DFF2FD","FDFFCA","6699CC","B0E0E6","003366","FF4500","003366","669966",
                          "CC99CC","2D5893","CCCCCC","333333","DDAAAA","578A24","191933","660000");
    $arrBGCol     = array("FFFFFF","F0F0F0","ECF8FF","EBFFED","FFF5F6","FDEFD2","E0FFE3","F9DFF9",
                          "DFF2FD","FDFFCA","003366","FFFFFF","003366","FFEBCD","000000","99CC99",
                          "E7C6E8","99AACC","FFFFFF","000000","ECF8FF","CCFF99","333366","7D2626");
    $arrLinkCol   = array("0000FF","0000FF","0000CC","0000CC","0000CC","0000CC","0000CC","0000CC",
                          "0000CC","0000CC","FFFFFF","000000","FF6600","DE7008","FFFFFF","000000",
                          "000000","000000","000000","FFFFFF","0033FF","00008B","99CC33","FFFFFF");
    $arrURLCol    = array("008000","008000","008000","008000","008000","008000","008000","008000",
                          "008000","008000","AECCEB","336699","99CCFF","E0AD12","FF6600","00008B",
                          "00008B","000099","666666","999999","0033FF","00008B","FFCC00","DAA520");
    $arrTextCol   = array("000000","000000","6F6F6F","6F6F6F","6F6F6F","000000","000000","000000",
                          "000000","000000","AECCEB","333333","FFFFFF","8B4513","FF6600","336633",
                          "663366","003366","333333","CCCCCC","000000","000000","FFFFFF","BDB76B");
    
    // set our leaderboard/top banner colors
    $ranColor = rand(0, count($arrBorderCol) - 1);
    $arrLeaderCols = array($arrBorderCol[$ranColor],
                           $arrBGCol[$ranColor],
                           $arrLinkCol[$ranColor],
                           $arrURLCol[$ranColor],
                           $arrTextCol[$ranColor]);
    
    // set our skyscraper banner colors
    $ranColor = rand(0, count($arrBorderCol) - 1);
    $arrSkyCols = array($arrBorderCol[$ranColor],
                        $arrBGCol[$ranColor],
                        $arrLinkCol[$ranColor],
                        $arrURLCol[$ranColor],
                        $arrTextCol[$ranColor]);
    
    // set our skyscraper banner colors
    $ranColor = rand(0, count($arrBorderCol) - 1);
    $arrSky2Cols = array($arrBorderCol[$ranColor],
                         $arrBGCol[$ranColor],
                         $arrLinkCol[$ranColor],
                         $arrURLCol[$ranColor],
                         $arrTextCol[$ranColor]);
    
    // set our square banner colors
    $ranColor = rand(0, count($arrBorderCol) - 1);
    $arrSqCols = array($arrBorderCol[$ranColor],
                       $arrBGCol[$ranColor],
                       $arrLinkCol[$ranColor],
                       $arrURLCol[$ranColor],
                       $arrTextCol[$ranColor]);*/
    
    // set which fastclick set of ads to display
    //$adPlace = rand(1,2);
    $adPlace = 1;
?>
