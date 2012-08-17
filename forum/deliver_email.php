<?php
    /*
        deliver_email.php
        
        This takes the info submitted from deliver.php and process the data.  
        We'll query the db for the data, drop it into an email, and then send 
        it off.
    */
    
    // include our needed files
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require("gnet_db.php");
    require("sessions.php");
    require("ads.php");
    
    // make sure they're logged in
    verify_access($_SESSION["AccessLevel"], 1);
    
    // query the data from the db for this thread
    $qryThread = $dbConn->getRow("
        select  ID,
                intForum,
                strTitle
        from    topics
        where   ID = '" . $_POST["ID"] . "'",
        DB_FETCHMODE_ASSOC);
    
    // build our email text to send
    $emailText = $_POST["name"] . ":\n\n";
    $emailText .= "A friend found this thread and thought you might find it useful.\nClick below to view it:\n\n";
    $emailText .= "http://www.guitarists.net/forum/view_bb.php?forum=" . $qryThread["intForum"] . "&thread=" . $qryThread["ID"] . "\n";
    $emailText .= "- or -\n";
    $emailText .= "<a href=\"http://www.guitarists.net/forum/view_bb.php?forum=" . $qryThread["intForum"] . "&thread=" . $qryThread["ID"] . "\">Click Here</a>\n\n";
    $emailText .= "Note: we did not save your email address, and this is not beng sent\nunsolicited.\n\n";
    $emailText .= "Thanks.\n\nThe Guitarists Network\nhttp://www.guitarists.net/";
    
    // send the email
    mail(trim($_POST["email"]),
         "Guitarists.net Thread Recommendation",
         $emailText,
         "From: member.support@guitarists.net\r\n" .
         "Reply-To: member.support@guitarists.net");
    
    // tell them it was successful
    print "
    <script language=\"JavaScript\">
    alert(\"Your request has been sent. Thanks for helping to spread\\n\" +
          \"the word about our site!\");
    location.replace(\"/forum/view_bb.php?forum=" . $qryThread["intForum"] . "&thread=" . $qryThread["ID"] . "\");
    </script>";
    exit();
    
?>