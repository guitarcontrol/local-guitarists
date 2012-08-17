<?php
    /*
        gnet_db.php
        
        This script is simply called to create our db connection to the
        db.  We'll now use the PEAR db implementation to process our queries,
        so any dbms can be used (MySQL, Postgres, MS SQL, etc) without 
        changing code.
    */
    
    // include our db code
    require_once("DB.php");

    // set our various variables to call MySQL with
    $dbuser = "gnet_int";
    $dbpasswd = "joelf411";
    $dbtype = "mysql";
    $dbhost = "ss0456.svwh.net";
    $dbport = "3306";
    $db = "guitargodclub_gnet_logins";
    
    // setup our connection
    $dsn = "$dbtype://$dbuser:$dbpasswd@$dbhost:$dbport/$db";
    
    // create our connection
    $dbConnL = DB::connect($dsn) or die('Cannot connect to login DB.');
    
    // make dure the connection worked
    if (DB::isError($dbConnL)) {
        // stop gracefully
        print "<script language=\"JavaScript\">location.replace(\"/error.php\");</script>\n";
        exit();
        //die ("Database error encountered.\n<!-- " . $dbConnL->getDebugInfo() . $dbConnL->getMessage() . " -->\n");
    }
    
    function generatePassword($length) {
       $chars = array(48,49,50,51,52,53,54,55,56,57,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,121,122);
       $password = "";
       for ($i = 1; $i <= $length; $i++) {
           $password .= chr($chars[rand(0, count($chars))]);
       }
       return $password;
   }
?>