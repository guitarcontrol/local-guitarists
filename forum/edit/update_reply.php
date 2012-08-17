<?php
    /*
        update_reply.php
        
        Here we process the form and add the post into the database.
    */
    
    // redirect them
    header("Location: /forum/edit/reply_bb.php?id=" . $_POST["ID"]);
?>