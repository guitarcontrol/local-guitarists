<?php
	
	/*
		home_rotation.php
		
		This includes the main items we'll loop through on the home page.  Here we 
		can incorporate links to advertisers, friends, G-Net sections, etc.
	*/
	
	// set the # to display
	$intItem = rand(1,2);
	
	// set the data to display
	switch ($intItem) {
		case 1:
			print "
			<a href=\"/links/track.php?id=79\" target=\"_new\"><img src=\"images/lgm_custom.jpg\" width=\"100\" height=\"113\" align=\"right\" alt=\"LGM Guitars\" border=\"0\"></a>
			<img src=\"images/pointer.gif\" width=\"11\" height=\"11\" border=\"0\"> <a href=\"/links/track.php?id=79\" target=\"_new\"><b style=\"font-size: 12px;\">LGM Guitars</b></a><br>
			Do you have a guitar that needs that little something special to make it truly yours?
			Got the killer paintjob in mind but are having trouble finding someone skilled and
			reliable enough to make it reality? What about a custom inlay to replace those boring dots?  
			Than check out LGM Guitars!  They offer custom inlay, body modification and finishing work to 
			your specifications and at reasonable prices!";
			break;
		case 2:
			print "
			<a href=\"/links/track.php?id=195\" target=\"_new\"><img src=\"images/paul_smith.jpg\" width=\"100\" height=\"120\" align=\"right\" alt=\"Paul Smith Music\" border=\"0\"></a>
			<img src=\"images/pointer.gif\" width=\"11\" height=\"11\" border=\"0\"> <a href=\"http://www.paulsmithmusic.net\" target=\"_new\"><b style=\"font-size: 12px;\">Paul Smith Music</b></a><br>
            Learn how to finger pick like Peter Paul and Mary, Gordon Lightfoot, John Denver, Jim Croce, Paul Simon, James Taylor and others.  Order picks online, download finger picking tab and patterns, and more.  Make the fastest possible progress with the best results.  Soon you will be a finger picking pro.
			<!--- If you like to listen to folk, pop, ballads, country western, blue grass, or soft rock, then 
			we recommend that you check out 
			<a href=\"http://www.paulsmithmusic.net\" target=\"_new\"><b>Paul Smith's music</b></a>.  
			Paul has been noted for his gentle voice and harmonies and the way they richly blend with 
			his finger picking style of acoustical guitar work. --->";
			break;
	}
?>