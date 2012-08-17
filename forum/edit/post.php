<?php
    /*
        post.php
        
        This is the main script that allows a member to to edit their 
        original post.
    */
    
    // redirect them
    header("Location: /forum/edit/post_bb.php?id=" . $dbConn->quote($_GET["id"]));
?>