<?php
    
    /*
        preview_bb.php
        
        This allows a member to view their post before they add it to the site.
    */
    
    // redirect them
    header("Location: /forum_ggc/reply/index_bb.php?thread=" . $_POST["intTopic"]);
?>
