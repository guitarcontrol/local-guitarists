<?php
    /*
        index.php
        
        This is our main reply script that allows a member to reply 
        to a given topic.
    */
    
    // redirect them
    if (!empty($_GET["quote"]) && !empty($_GET["id"])) {
        header("Location: index_bb.php?msg=" . $_GET["msg"] . "&quote=" . $_GET["quote"] . "&id=" . $dbConn->quote($_GET["id"]));
    } else {
        header("Location: index_bb.php?msg=" . $_GET["msg"]);
    }
?>
    
