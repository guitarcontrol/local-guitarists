<?php
    /*
        topics.php
        
        Here we'll display our active threads, based on the forum chosen.
    */
    
    // redirect to the new script
    header("Location: /forum_ggc/topics_bb.php?forum=" . $_GET["forum"]);
?>
