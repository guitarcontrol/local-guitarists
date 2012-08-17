<?php
    /*
        includes.php
        
        This script simply allows us to see all of the vars we need from one location
    */

    // update our include path
    ini_set("include_path", ".:/usr/lib/php:/usr/local/lib/php:/home/gnet/includes/guitarists.net");

    // include our sanitation script
    require("classes/class_input_filter.php");

    // create our input filter
    $filter = new InputFilter();

    // include facebook
    require_once("fbconfig.php");
?>