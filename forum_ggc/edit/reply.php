<?php
    /*
        reply.php
        
        This is the script the member (or a mod) will use to edit his/her reply 
        to a given thread.
    */
    
    // redirect them
    header("Location: /forum_ggc/edit/reply_bb.php?id=" . $_GET["ID"]);
?>
