<?php
    /*
        update_post.php
        
        Here we process the form and add the post into the database.
    */
    
    // redirect them
    header("Location: /forum/edit/post_bb.php?id=" . $_POST["ID"]);
?>