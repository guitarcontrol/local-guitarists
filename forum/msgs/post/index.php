<?php
    /*
        index.php
        
        This is the main script that allows a member to post a new 
        thread in the forums.
    */
    
    // redirect them
    if (!empty($_GET["user"])) {
        header("Location: index_bb.php?id=" . $_GET["user"]);
    } else {
        header("Location: index_bb.php");
    }
?>
