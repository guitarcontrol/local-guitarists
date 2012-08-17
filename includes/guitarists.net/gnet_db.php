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
    $dbuser = "gnet_db";
    $dbpasswd = "crlPUbN7-k@A";
    $dbtype = "mysql";
    $dbhost = "localhost";
    $dbport = "3306";
    $db = "gnet_guitarists";
    
    // setup our connection
    $dsn = "$dbtype://$dbuser:$dbpasswd@$dbhost:$dbport/$db";
    
    // create our connection
    $dbConn = DB::connect($dsn);
    
    // make dure the connection worked
    if (DB::isError($dbConn)) {
        // stop gracefully
        print "<script language=\"JavaScript\">location.replace(\"/error.php\");</script>\n";
        exit();
        //die ("Database error encountered.\n<!-- " . $dbConn->getDebugInfo() . $dbConn->getMessage() . " -->\n");
    }
    
    // sanitize our GET array (if needed)
    if (!empty($_GET)) {
        //$_GET = clean_variables($_GET, $dbConn);
        $_GET = $filter->process($_GET);
        $get_cleaned = 1;
        $found = 0;
        
        // create our array of text to search for
        $arrSQLWords = array("select", "union", "concat", "from");
        
        // loop through our array and see if the value is found in our URL
        foreach ($arrSQLWords as $word) {
            if (stristr($_SERVER["QUERY_STRING"], $word)) {
                $found++;
            }
        }
        
        // see if our query string is too long
        if ($found) {
            // set the file to use
            $filename = "/home/gnet/public_html/hacks.txt";
            
            // Let's make sure the file exists and is writable first.
            if (is_writable($filename)) {
                $handle = @fopen($filename, 'a+');
                @fwrite($handle, "\n" . $_SERVER["REQUEST_URI"] . " - " . $_SERVER["REMOTE_ADDR"]) . " (" . date("Y-m-d H:i:s") . ")";
                @fclose($handle);
            }
        }
    }
    
    // sanitize our POST array (if needed)
    if (!empty($_POST)) {
        //$_POST = clean_variables($_POST, $dbConn);
        $_POST = $filter->process($_POST);
        $post_cleaned = 1;
    }
    
    // loop through an array to clean each variable
    function clean_variables($array, $dbConn) {
        // create our temp array
        $temp = array();
        
        // loop through the array to process
        foreach ($array as $key => $value) {
            // see if the value is an array or not
            if (is_array($value)) {
                $temp[$key] = clean_variables($value);
            } else {
                $temp[$key] = $dbConn->quote($value);
            }
        }
        
        // return the data
        return $temp;
    }
?>
