<?php

/*
  sessions.php

  We'll use the PEAR database connection to store our session data in the
  database.  Much safer than storing all of the session files in /tmp.
 */

require_once("login_db.php");

// make sure this page isn't called directly
if (basename($_SERVER["PHP_SELF"]) == "sessions.php") {
    print "Direct browsing disabled for this file.";
    exit();
}

// our function to verify someone has access
function verify_access($userID, $level) {
    if ($userID < $level) {
        // redirect them to a page to choose what to do
        print "
        <script language=\"JavaScript\">
        alert(\"You need to be a registered member to access this\\n\" +
        \"section.  Please login and try again.  Thanks.\");
        location.replace(\"/index.php\");
        </script>\n";
        exit();
    }
}

// see if the user is frozen
function verify_frozen($userID, $dbConn) {
    // set our default var
    $frozen = 1;

    // see if an ID was even passed
    if ($userID) {
        // see if they're frozen
        $qryMember = $dbConn->getRow("select intFrozen from members where ID = '" . $userID . "'", DB_FETCHMODE_ASSOC);

        // continue, based on the results
        if (count($qryMember)) {
            // extract our results
            if (!$qryMember["intFrozen"]) {
                $frozen = 0;
            }
        }
    }

    // go back, if they're not allowed here
    if ($frozen) {
        print "
        <script language=\"JavaScript\">
        alert(\"Your account has been frozen by a moderator.  This is the\\n\" +
                \"last step taken before a ban is enabled.  Please view\\n\" +
                \"your Private messages to discuss this.\");
        location.replace(\"/index.php\");
        </script>";
        exit();
    }
}

// create our session class
$mySess = new session($dbConn);

// set sessions handler on php
session_set_save_handler(array(&$mySess, 'open'), array(&$mySess, 'close'), array(&$mySess, 'read'), array(&$mySess, 'write'), array(&$mySess, 'destroy'), array(&$mySess, 'gc'));

// start our class
class session {

    //life of sessions in seconds
    var $session_limit = 1200;

    // use the db connection passed
    function session(&$db) {
        //set db handler
        $this->db = $db;
    }

    // open the session
    function open($path, $name) {
        if (isset($this->db)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    // close session
    function close() {
        // manually call the garbage collector
        $this->gc(0);
        return TRUE;
    }

    // read session data from database
    function read($ses_id) {
        // query the session data
        $session_res = $this->db->getRow("
            SELECT  *
            FROM    sessions
            WHERE   SessID = '" . trim($ses_id) . "'", DB_FETCHMODE_ASSOC);

        // if nothing was found, return nothing
        if (!$session_res) {
            return '';
        }

        // if we found data, process
        if ($session_res) {
            // return our our session data from the db
            $ses_data = $session_res["Value"];
            return $ses_data;
        } else {
            // return nothing
            return '';
        }
    }

    // update the db with our newest session data
    function write($ses_id, $data) {
        // process the update
        $session_res = $this->db->query("
            UPDATE  sessions
            SET     SessTime = '" . time() . "',
                    Value='" . trim($data) . "',
                    Username = '" . $_SESSION["Username"] . "',
                    UserID = '" . $_SESSION["MemberID"] . "'
            WHERE   SessID = '" . trim($ses_id) . "'");

        // if nothing was updated, retrun false
        if (!$session_res) {
            return FALSE;
        }

        // return true if we updated something
        if ($this->db->affectedRows()) {
            return TRUE;
        } else {
            // create our array of IP ranges for the various spiders
            $arrIPClass = array("65.214.44", "66.249.65", "80.3.32", "87.66.0", "65.54.188", "207.46.98", "66.249.65",
                "66.249.66", "66.249.72", "68.142.249", "68.142.250", "68.142.251", "72.30", "84.9.194");

            // create an array from our IP address
            list($pos1, $pos2, $pos3, $pos4) = explode(".", $_SERVER["REMOTE_ADDR"]);

            // create our comparison variables
            $ip3 = $pos1 . "." . $pos2 . "." . $pos3;
            $ip2 = $pos1 . "." . $pos2;

            // if the IP addy isn't in our block array, create it
            if (!in_array($ip3, $arrIPClass) && !in_array($ip2, $arrIPClass)) {
                // if we made it here, we need to insert our session data
                $session_res = $this->db->query("
                    INSERT INTO sessions (
                        SessID,
                        UserID,
                        Username,
                        IPAddress,
                        SessTime,
                        SessStart,
                        Value,
                        SessDate
                    ) VALUES (
                        '" . $ses_id . "',
                        '" . $_SESSION["MemberID"] . "',
                        '" . $_SESSION["Username"] . "',
                        '" . $_SERVER["REMOTE_ADDR"] . "',
                        '" . time() . "',
                        '" . time() . "',
                        '" . trim($data) . "',
                        Now()
                    )");

                // return our status from the query
                if (!$session_res) {
                    return FALSE;
                } else {
                    return TRUE;
                }
            } else {
                return FALSE;
            }
        }
    }

    // destroy the session record in database
    function destroy($ses_id) {
        // delete the record
        $session_res = $this->db->query("
            DELETE
            FROM    sessions
            WHERE   SessID = '" . trim($ses_id) . "'
            LIMIT 1");

        // return the status of the deletion
        if (!$session_res) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    // garbage collection removes old sessions
    function gc($life) {
        // set our cutoff timeframe
        $ses_life = time() - $this->session_limit;

        // delete any old records
        $session_res = $this->db->query("
            DELETE
            FROM    sessions
            WHERE   SessTime < '" . $ses_life . "'");

        // return the status
        if (!$session_res) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    // get the users currently online
    function users() {
        // get the # of users online
        $users_res = $this->db->getOne("
            SELECT  COUNT(SessID)
            FROM    sessions");

        // return our results
        if (!$users_res) {
            return NULL;
        } else {
            return $users_res;
        }
    }

}

// start the session
@session_start();

// see if they have established their session info
if (!isset($_SESSION["MemberID"])) {
    // set our defaults
    $_SESSION["MemberID"] = 0;
    $_SESSION["Username"] = "Guest";
    $_SESSION["HideAds"] = 0;
    $_SESSION["AccessLevel"] = 0;
    $_SESSION["LastLogin"] = date("n/j/Y g:i:s");
    $_SESSION["Style"] = array(1, 12);
    $_SESSION["SubText"] = "";
}

// see if there's a cookie with their user ID
if (!$_SESSION["MemberID"] && !empty($_COOKIE["MEMID"]) && $_COOKIE["MEMID"] != 4) {
    // get the users info to reset the session vars
    $qryUser = $dbConn->getRow("
        select  ID,
                strUsername,
                intHideAds,
                intAccess,
                dateLVisit,
                intValidated,
                intBanned,
                intFrozen,
                FontID,
                FontSize
        from    members
        where   ID = '" . $_COOKIE["MEMID"] . "'
        limit 1", DB_FETCHMODE_ASSOC);

    // see if we found a record
    if (count($qryUser)) {
        // make sure they're not banned or frozen
        if (!$qryUser["intFrozen"] && !$qryUser["intBanned"]) {
            // update the db with the users newest login date
            $qrySessUpdate = $dbConn->query("
                update   members
                set      dateLVisit = Now(),
                            strIP = '" . $_SERVER["REMOTE_ADDR"] . "'
                where    ID = '" . $qryUser["ID"] . "'
                limit 1");

            // update our session vars
            $_SESSION["MemberID"] = $qryUser["ID"];
            $_SESSION["Username"] = trim($qryUser["strUsername"]);
            $_SESSION["HideAds"] = $qryUser["intHideAds"];
            $_SESSION["AccessLevel"] = $qryUser["intAccess"];
            $_SESSION["LastLogin"] = $qryUser["dateLVisit"];
            $_SESSION["Style"] = array($qryUser["FontID"], $qryUser["FontSize"]);
        } else {
            // kill the cookie
            setcookie("MEMID", "", time() - 3600, "/");
        }
    }
}

// banned IP Ranges
$myIP = sprintf("%u", ip2long($_SERVER['REMOTE_ADDR']));

$qryBannedIpRange = $dbConn->query('SELECT * FROM banned_ip_ranges WHERE ( ' . $myIP . ' BETWEEN ipRangeStart AND ipRangeEnd )');
if ($row = $qryBannedIpRange->fetchRow(DB_FETCHMODE_ASSOC)) {
    // User IP is in banned range
    print 'banned !';
    exit;
}

//If the user tries to get to guitarists from GGC
if (empty($_SESSION["MemberID"]) && empty($_SESSION['ip_address'])) {
    $query = $dbConnL->getOne("SELECT COUNT(ip_address) AS totals FROM logins WHERE ip_address = '" . $_SERVER["REMOTE_ADDR"] . "'");

    if ($query == 1) {
        $getUser = $dbConnL->getOne("SELECT email FROM logins WHERE ip_address = '" . $_SERVER["REMOTE_ADDR"] . "'");
        $getResults = $dbConn->getRow("SELECT * FROM members WHERE strEmail = '" . $getUser . "'", DB_FETCHMODE_ASSOC);

        //$_SESSION["login_key"] = $_GET["login_key"];
        $_SESSION["MemberID"] = $getResults["ID"];
        $_SESSION["ip_address"] = $_SERVER["REMOTE_ADDR"];
        $_SESSION["Username"] = trim($getResults["strUsername"]);
        $_SESSION["HideAds"] = $getResults["intHideAds"];
        $_SESSION["AccessLevel"] = $getResults["intAccess"];
        $_SESSION["LastLogin"] = $getResults["dateLVisit"];
        $_SESSION["Style"] = array($getResults["FontID"], $qryUser["FontSize"]);
        $_SESSION["GGCUser"] = 1;

        $qryProcess = $dbConnL->query("delete from logins where ip_address = '" . $_SESSION["ip_address"] . "'");
    } else {
        //header('Location: /index.php');
    }
}

//Updates the time of the users last page viewed
if (!empty($_SESSION["MemberID"]) && !empty($_SESSION['ip_address'])) {
    $qryUpdate = $dbConnL->query("UPDATE logins SET last_hit = NOW() where ip_address = '" . $_SESSION["ip_address"] . "'");
}

// Deletes the row if the user hasn't viewed a page in 30 minutes
$dbConnL->query("DELETE FROM `logins` WHERE last_hit < DATE_SUB(NOW(), INTERVAL 90 MINUTES");

// see if they came from the GGC forum
if (!empty($_GET["ggc"])) {
    $_SESSION["GGCIFrame"] = 1;
}

// if they're trying to load the forums and it's not me, redirect them
/* if (strpos($_SERVER["SCRIPT_NAME"], "/forum") !== false && $_SESSION["MemberID"] != 1) {
  // redirect them
  print "<script language=\"JavaScript\">location.replace(\"http://www.guitarists.net/forum/maintenance.php\");</script>\n";
  exit();
  }

$delete_me = $dbConnL->query("DELETE FROM `logins` WHERE email = 'test9@gnetconsulting.com'"); */
?>
