<?php
    /*
        12all_db.php
        
        This script is simply called to create our db connection to the
        db.  We'll now use the PEAR db implementation to process our queries,
        so any dbms can be used (MySQL, Postgres, MS SQL, etc) without 
        changing code.
    */
    
    // include our db code
    require_once("DB.php");
    
    // set our various variables to call MySQL with
    $dbuser = "gnet_db";
    $dbpasswd = "crlPUbN7-k@A";
    $dbtype = "mysql";
    $dbhost = "localhost";
    $dbport = "3306";
    $db = "gnet_12all";
    
    // setup our connection
    $dsn = "$dbtype://$dbuser:$dbpasswd@$dbhost:$dbport/$db";
    
    // create our connection
    $db12All = DB::connect($dsn);
    
    // make dure the connection worked
    if (DB::isError($db12All)) {
        // stop gracefully
        //print "<script language=\"JavaScript\">location.replace(\"/error.php\");</script>\n";
        die(print_r($db12All));
        exit();
    }
?>
