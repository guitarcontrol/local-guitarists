<?php
    
    /*
        preview.php
        
        This allows a member to view their post before they add it to the site.
    */
    
    // redirect them to the new script
    header("Location: /forum/post/index_bb.php?forum=" . $_POST["intForum"]);
?>
