<?php
	/*
		messages.php
		
		This script will randomly display a simple message at the top of the page, 
		between the current member counter and login/logout links. It's just simple 
		marketing purposes.
	*/
	
    // based on the area, display our PR messages
    if ($areaName == "tab" || $areaName == "chords" || $areaName == "scales") {
        switch(rand(1, 7)) {
            case 1: $prmessage = "We're looking for new tabs. <a href=\"submit.php\"><b>Submit some tabs</b></a> you may have."; break;
            case 2: $prmessage = "Support the community by <a href=\"/donate/index.php\"><b>donating</b></a> to the site."; break;
            case 3: $prmessage = "Learn to play any song with <a href=\"http://www.absolutepitchpower.com/cgi-bin/affiliates/clickthru.cgi?id=joelf\"><b>absolute pitch</b></a>."; break;
            case 4: $prmessage = "Check out our <a href=\"http://www.riffinteractive.com/lod/todayslickgn.asp\" target=\"_new\"><b>Lick of the Day</b></a> from Riff Interactive."; break;
            case 5: $prmessage = "Find the latest sheet music at <a href=\"http://www.sheetmusicplus.com/a/home.html?id=10460\"><b>Sheet Music Plus</b></a>."; break;
            case 6: $prmessage = "You can save items in your <a href=\"/members/saved.php\"><b>favorites</b></a>. It's simple."; break;
            case 7: $prmessage = "Can't find your song?  Have one of our teachers <a href=\"http://www.riffinteractive.com/store/shopaff.asp?affid=11&directurl=/privatelessons.htm\" target=\"_new\"><b>transcribe it for you</b></a>."; break;
        }
    } else if ($areaName == "gear") {
        switch(rand(1, 4)) {
            case 1: $prmessage = "Find great deals on gear at <a href=\"http://www.instrumentpro.com?kbid=1084&img=ip_120x600_google.gif\" target=\"_new\"><b>InstrumentPro.com</b></a>."; break;
            case 2: $prmessage = "<a href=\"/links/track.php?id=79\" target=\"_new\"><b>LGM Guitars</b></a> does some GREAT custom modifications."; break;
            case 3: $prmessage = "Learn to modify your <a href=\"http://hop.clickbank.net/?guirat/indyguitar\" target=\"_new\"><b>effects pedals</b></a>."; break;
            case 4: $prmessage = "Browse our collection of recent <a href=\"/news/index.php\"><b>guitar news</b></a>."; break;
        }
    } else if ($areaName == "lessons") {
        switch(rand(1, 7)) {
            case 1: $prmessage = "Take private lessons online with <a href=\"http://www.riffinteractive.com/store/shopaff.asp?affid=11&directurl=/privatelessons.htm\" target=\"_new\"><b>Riff Interactive</b></a>."; break;
            case 2: $prmessage = "Check out our <a href=\"http://www.riffinteractive.com/store/shopaff.asp?affid=11&directurl=/lod/todayslickgn.asp\" target=\"_new\"><b>Lick of the Day</b></a>."; break;
            case 3: $prmessage = "Want to add your lesson(s) here? <a href=\"submit.php\"><b>Let us know</b></a>."; break;
            case 4: $prmessage = "You too can achieve <a href=\"http://www.absolutepitchpower.com/cgi-bin/affiliates/clickthru.cgi?id=joelf\" target=\"_new\"><b>Perfect Pitch</b></a>."; break;
            case 5: $prmessage = "Learn to play like <a href=\"http://www.guitaralliance.com/cgi-bin/affiliates/clickthru.cgi?id=gnet\" target=\"_new\"><b>Hendrix, Clapton, and more</b></a>."; break;
            case 6: $prmessage = "<a href=\"http://www.emediamusic.com/cgi-bin/affiliate.cgi/guitarists.net\" target=\"_new\"><b>eMedia Guitar Method</b></a> makes learning fun."; break;
            case 7: $prmessage = "Learn to sing better with <a href=\"http://www.vocalrelease.com?gnet\" target=\"_new\"><b>VocalRealease.com</b></a>."; break;
        }
    } else {
    	switch(rand(1, 15)) {
            case 1: $prmessage = "Why not <a href=\"/gear/submit.php\"><b>submit some reviews</b></a> on the equipment you use?"; break;
            case 2: $prmessage = "Know of a cool tuning? <a href=\"/tunings/submit.php\"><b>Let us know</b></a> about it."; break;
            case 3: $prmessage = "We're looking for new content. <a href=\"/contact.php\"><b>Let us know</b></a> if you can help."; break;
            case 4: $prmessage = "Find other players like yourself in our <a href=\"/players/index.php\"><b>players search</b></a>."; break;
            case 5: $prmessage = "Support the community by <a href=\"/donate/index.php\"><b>donating</b></a> to the site."; break;
            case 6: $prmessage = "Learn to play any song with <a href=\"http://www.absolutepitchpower.com/cgi-bin/affiliates/clickthru.cgi?id=joelf\"><b>absolute pitch</b></a>."; break;
            case 7: $prmessage = "Check out our <a href=\"http://www.riffinteractive.com/lod/todayslickgn.asp\" target=\"_new\"><b>Lick of the Day</b></a> from Riff Interactive."; break;
            case 8: $prmessage = "<a href=\"http://www.vocalrelease.com/?gnet\" target=\"_new\"><b>Learn to sing professionally</b></a> with this course."; break;
            case 9: $prmessage = "Find the latest sheet music at <a href=\"http://www.sheetmusicplus.com/a/home.html?id=10460\"><b>Sheet Music Plus</b></a>."; break;
            case 10: $prmessage = "Guirat. Guirat? Guirat! What is it? Find out <a href=\"/forum/view.php?forum=11&thread=1940\"><b>here</b></a>."; break;
            case 11: $prmessage = "You can save items in your <a href=\"/members/saved.php\"><b>favorites</b></a>. It's simple."; break;
            case 12: $prmessage = "View your <a href=\"/members/msgs/index.php\"><b>private messages</b></a> from other members."; break;
            case 13: $prmessage = "Tell us what you think.  Answer a <a href=\"/poll/index.php\"><b>poll</b></a>."; break;
            case 14: $prmessage = "Check out our new classifieds site - <a href=\"http://www.guitarads.net/\" target=\"_new\"><b>Guitarads.net</b></a>."; break;
            case 15: $prmessage = "Browse our collection of recent <a href=\"/news/index.php\"><b>guitar news</b></a>."; break;
            case 16: $prmessage = "Check out the new <a href=\"http://www.shadowstand.com/\" target=\"_new\"><b>Shadow Stands</b></a> guitar stands."; break;
            default: $prmessage = "Check out our <a href=\"http://www.riffinteractive.com/lod/todayslickgn.asp\" target=\"_new\"><b>Lick of the Day</b></a> from Riff Interactive."; break;
        }
    }
	
	// display our data
	print $prmessage;
?>