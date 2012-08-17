<?php
    /*
        preview_post.php
        
        This allows a member to view their post before they add it to the site.
    */
    
    // redirect them
    header("Location: /forum/edit/post_bb.php?id=" . $_POST["ID"]);
?>
