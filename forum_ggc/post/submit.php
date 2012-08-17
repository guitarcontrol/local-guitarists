<?php
    /*
        submit.php
        
        Here we process the form and add the post into the database.
    */
    
    // redirect them to the new script
    header("Location: /forum_ggc/post/index_bb.php?forum=" . $_POST["intForum"]);
?>
