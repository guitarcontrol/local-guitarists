<?php
    
    /*
        submit.php
        
        Here we write the data to the database, and update the stats for all 
        of the topics, as well as the member, and then redirect them to the 
        post, setting them at their post.
    */
    
    // redirect them
    header("Location: /forum_ggc/reply/index_bb.php?thread=" . $_POST["intTopic"]);
?>
