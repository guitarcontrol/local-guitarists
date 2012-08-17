<?php
    /*
        index.php
        
        This is our main reply script that allows a member to reply 
        to a given topic.
    */
    
    // set the URL to redirect to
    $myURL = "/forum/reply/index_bb.php?thread=" . $_GET["thread"];
    
    // if they passed a reply to quote
    if (!empty($_GET["reply"])) {
        $myURL .= "&reply=" . $_GET["reply"];
    }
    
    // if they passed a quote option
    if (!empty($_GET["quote"])) {
        $myURL .= "&quote=1";
    }
    
    // redirect them
    header("Location: " . $myURL);
?>
    
