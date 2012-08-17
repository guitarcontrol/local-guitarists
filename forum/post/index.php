<?php
    
    /*
        index.php
        
        This is the main script that allows a member to post a new 
        thread in the forums.
    */
    
    // redirect them to the new script
    header("Location: /forum/post/index_bb.php?forum=" . $_GET["forum"]);
?>
