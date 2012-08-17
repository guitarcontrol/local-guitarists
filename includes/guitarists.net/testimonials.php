<?php
	/*
		testimonials.php
		
		Displays text links to promo items with testimonials by other users.  
		Hopefully this will increase our revenue.
	*/
    
	// set a random number
	$randID = rand(1,7);
	
	switch($randID) {
		case 1:
			print "I know you're inner-most desire is to become the best musician you possibly can, and 
			<a href=\"http://www.absolutepitchpower.com/cgi-bin/affiliates/clickthru.cgi?id=joelf\"><b>Absolute Pitch 
			Power</b></a> was created to serve that desire, by tapping into your musical genius and getting all you want 
			right now.
			<p>
			<i>\"I have been doing this program for 2 weeks and I can already pick out pitches in music!\"</i> - <b>Robert Fulton</b>";
			break;
		case 2:
			print "If you're looking to learn the guitar, we recommend you check out 
			<a href=\"/process/tracker.php?id=18\"><b>Riff Interactive Guitar Lessons</b></a>.  It's a great way to learn 
			to play guitar, taught by real professionals.  It's officially endorsed by many members here at 
			G-Net.  Here's a sample:
			<p>
			<i>\"I've been playing for about 13 years and have never taken a lesson. Now I have the chance to 
			learn all the stuff that I should have years ago. Keep up the GREAT work!\"</i>";
			break;
		case 3:
			print "<a href=\"http://www.emediamusic.com/cgi-bin/affiliate.cgi/guitarists.net\"><b>eMedia Guitar Method</b></a> 
			makes learning fun regardless of the type of guitar you own. 155 comprehensive lessons cover basics 
			to chord strumming, playing melodies and fingerpicking.
			<p>
			<i>\"This is the ultimate way to learn how to play guitar!\"</i> - <b>Peter Frampton</b>";
			break;
		case 4:
			print "Do you seriously admire guitarists like Jimi Hendrix, Eddie Van Halen, Stevie Ray Vaughan, Joe Satriani, 
			and Eric Clapton? Have you ever dreamed of playing serious rock and roll?  At 
			<a href=\"http://www.guitaralliance.com/cgi-bin/affiliates/clickthru.cgi?id=gnet\"><b>GuitarAlliance.com</b></a>, 
			you can!
			<p>
			<i>\"I learned more in 5 minutes than I did by myself in 3 months.\"</i> - <b>Shane Lavalley</b>";
			break;
		case 5:
			print "Would you like to learn to sing, or even sing better?  Then we recommend you check out 
			this great vocal training course from <a href=\"http://www.vocalrelease.com?gnet\"><b>VocalRealease.com</b></a>. 
			Here's what one user said about the program:
			<p>
			<i>Just wanted to let you know that so far I have learned a lot from your program. In fact, 
			cords zipped-up last night and I was singing like Mariah Carey... Why the heck didn't the loads 
			of vocal coaches tell me before that I was forcing my voice?</i>";
			break;
		case 6:
			print "<a href=\"http://www.sheetmusicplus.com/a/button.html?id=10460\" target=\"_new\"><img src=\"http://gfx.sheetmusicplus.com/store/gfx/smp_100x70_findall_w.gif\" align=\"right\" width=\"100\" height=\"70\" alt=\"Sheet Music Plus\" border=\"0\"></a>
			I personally buy from <a href=\"http://www.sheetmusicplus.com/a/home.html?id=10460\"><b>Sheet Music Plus</b></a> 
			because they have a huge selection and free shipping for orders over \$25!  Plus they have 100's of thousands 
			of titles, covering all of my favorite artists.  They include 
			<a href=\"http://www.sheetmusicplus.com/a/phrase.html?id=10460&phrase=Jimi+Hendrix\"><b>Jimi Hendrix</b></a>, 
			<a href=\"http://www.sheetmusicplus.com/a/phrase.html?id=10460&phrase=Stevie+Ray+Vaughan\"><b>Stevie Ray Vaughan</b></a>, 
			<a href=\"http://www.sheetmusicplus.com/a/phrase.html?id=10460&phrase=Metallica\"><b>Metallica</b></a>, 
			<a href=\"http://www.sheetmusicplus.com/a/phrase.html?id=10460&phrase=Dream+Theater\"><b>Dream Theater</b></a>, 
			and more.  Check them out today!";
			break;
        case 7:
			print "You find and buy a guitar pedal that hopefully will sound great with your setup. Once you play with the pedal on your own equipment, you would eventually realize that this pedal was NOT fulfilling your need...you wanted butt kicking tone, not this thin, 'doesn't have 'punch/dynamics', 'doesn't sound anything like a big, full, & fat' guitar tone.  
            <p>
            So, you either stick it in a closet to collect dust, or sell it and try yet another pedal.  Then the whole cycle is repeated. Sound familiar?
            <p>
            Break the cycle and try <a href=\"http://hop.clickbank.net/?guirat/indyguitar\" target=\"_new\"><b>The DIY Effect Pedal Modification How-To Guide</b></a>.\n";
			break;
	}

?>