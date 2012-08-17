<?php
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    //require("fbconfig.php");

   //ovewrites the cookie
   $user = $facebook->getUser();
   $user = null;

   header('Location: http://www.guitarists.net/index.php');
?>
