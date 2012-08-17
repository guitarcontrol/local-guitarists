<?php
    /*
        view.php
        
        Allows a member to view the posts and replies to all messages posted 
        to and from him/her.
        
    */
    
    // redirect them to the newer version of the page
    header("Location: view_bb.php?id=" . $dbConn->quote($_GET["id"]));
?>
