<?php
    /*
        preview_reply_bb.php
        
        This allows a member to view their post before they add it to the site.
    */
    
    // redirect them
    header("Location: /forum_ggc/edit/reply_bb.php?id=" . $_POST["ID"]);
?>
