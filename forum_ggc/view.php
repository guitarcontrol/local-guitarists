<?php
    /*
        view.php
        
        Here we'll view the chosen thread.  We first strip out the forum 
        and thread ID's from the PATH_INFO (if supplied).  Then we get the 
        main thread, and then we query any/all replies.  Then just display 
        all of the info.
    */
    
    // redirect them
    header("Location: /forum_ggc/view_bb.php?forum=" . $_GET["forum"] . "&thread=" . $_GET["thread"]);
?>
