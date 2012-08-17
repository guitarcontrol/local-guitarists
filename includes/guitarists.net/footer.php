        <!-- end unique content -->
        </td>
    </tr>
    <tr>
        <td colspan="10" align="center">
        <!--- create our bookmark links --->
        <b>Bookmark Us:</b>&nbsp;&nbsp;
        <a href="http://del.icio.us/post?url=http://<?php print $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>" title="Bookmark this page at del.icio.us" target="_new"><img src="/images/delicious.gif" title="Bookmark this page at del.icio.us" alt="Bookmark this page at del.icio.us" height="16" width="16" border="0" /></a>&nbsp;&nbsp;
        <a href="http://digg.com/submit?phase=2&amp;url=http://<?php print $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>" title="Bookmark this page at Digg" target="_new"><img src="/images/digg.gif" title="Bookmark this page at Digg.com" alt="Bookmark this page at Digg.com" border="0" /></a>&nbsp;&nbsp;
        <a href="http://www.spurl.net/spurl.php?title=<?php print urlencode($pageTitle); ?>&url=http://<?php print $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>" title="Bookmark this page at Spurl.net" target="_new"><img src="/images/spurl.gif" title="Bookmark this page at Spurl.net" alt="Bookmark this page at Spurl.net" height="16" width="16" border="0" /></a>&nbsp;&nbsp;
        <a href="http://www.simpy.com/simpy/LinkAdd.do?href=http://<?php print $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>&title=<?php print urlencode($pageTitle); ?>" title="Bookmark this page at Simpy.com" target="_new"><img src="/images/simpy.png" title="Bookmark this page at Simpy.com" alt="Bookmark this page at Simpy.com" height="16" width="16" border="0" /></a>&nbsp;&nbsp;
        <a href="http://www.newsvine.com/_tools/seed&save?u=http://<?php print $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>&h=<?php print urlencode($pageTitle); ?>" title="Bookmark this page at Newsvine" target="_new"><img src="/images/newsvine.gif" title="Bookmark this page at NewsVine" alt="Bookmark this page at NewsVine" height="16" width="16" border="0" /></a>&nbsp;&nbsp;
        <a href="http://www.blinklist.com/index.php?Action=Blink/addblink.php&Description=&Url=http://<?php print $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>&Title=<?php print urlencode($pageTitle); ?>" title="Bookmark this page at blinklist.com" target="_new"><img src="/images/blinklist.gif" title="Bookmark this page at blinklist.com" alt="Bookmark this page at blinklist.com" height="16" width="16" border="0" /></a>&nbsp;&nbsp;
        <a href="http://www.furl.net/storeIt.jsp?t=<?php print urlencode($pageTitle); ?>&u=http://<?php print $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>" title="Bookmark this page at Furl.net" target="_new"><img src="/images/furl.gif" title="Bookmark this page at Furl.net" alt="Bookmark this page at Furl.net" height="16" width="16" border="0" /></a>&nbsp;&nbsp;
        <a href="http://reddit.com/submit?url=http://<?php print $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>&title=<?php print urlencode($pageTitle); ?>" title="Bookmark this page at reddit.com" target="_new"><img src="/images/reddit.gif" title="Bookmark this page at reddit.com" alt="Bookmark this page at reddit.com" height="16" width="16" border="0" /></a>&nbsp;&nbsp;
        <a href="http://cgi.fark.com/cgi/fark/edit.pl?new_url=http://<?php print $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>&new_comment=<?php print urlencode($pageTitle); ?>&new_link_other=Guitarists.net&linktype=Misc" title="Bookmark this page at Fark.com" target="_new"><img src="/images/fark.gif" title="Bookmark this page at Fark.com" alt="Bookmark this page at Fark.com" height="16" width="16" border="0" /></a>&nbsp;&nbsp;
        <a href="http://myweb2.search.yahoo.com/myresults/bookmarklet?t=<?php print urlencode($pageTitle); ?>&u=http://<?php print $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>" title="Bookmark this page at YahooMyWeb" target="_new"><img src="/images/yahoomyweb.gif" title="Bookmark this page at YahooMyWeb" alt="Bookmark this page at YahooMyWeb" height="16" width="16" border="0" /></a>
        </td>
    </tr>
    </table>
    
    </td>
</tr>
<tr>
    <td align="center" colspan="2" class="footer"><br />
     <a href="http://www.freeguitar.com/" title="Shop"><b>Guitar Lessons</b></a> 
    ::  <?php
        // if the user isn't logged in, display an interstitial every 15 mins
        /*if (!$_SESSION["MemberID"]) {
            ?>
            <script type="text/javascript"><!--
            google_ad_client = "ca-pub-3777083047736569";
            google_ad_width = 180;
            google_ad_height = 60;
            google_ad_format = "180x60_as_rimg";
            google_cpa_choice = "CAAQ24Oy0QEaCGbgW7AaXRokKMu293M";
            //--></script>
            <script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
            </script>
            <p />
            <?php
        }*/
        
        // if the user isn't logged in, display an interstitial every 15 mins
        if (!$_SESSION["MemberID"]) {
            // see if we should display the interstitial code
            $totalTime = ceil((time() - strtotime($_SESSION["LastLogin"])) / 60);
            
            // if they've been on the site for 3 minutes or more, display the code
            /*if ($totalTime > 15) {
                ?>
                <!-- ValueClick Media GLOBAL INTERSTITIAL CODE for guitarists.net -->
                <script language="javascript"><!--
                var min=15; // frequency cap in minutes, set to 15 or more
                var sid=8404;
                document.writeln('<scr'+'ipt language="javascript" src="http://code.fastclick.net/is.js"></scr'+'ipt>');
                // -->
                </script>
                <!-- ValueClick Media GLOBAL INTERSTITIAL CODE for guitarists.net -->
                <?php
            } else {
                ?>
                <script language="javascript"><!--
                <!-- FASTCLICK.COM InVue CODE v0.1b for guitarists.net -->
                var doc=document; if(doc.all && doc.getElementById){
                var url=escape(doc.location.href);var mjo=Math.floor(Math.random()*7777);
                doc.cookie='h2=o; path=/;'; var ht=doc.body.clientHeight;var wt=doc.body.clientWidth;
                if(doc.cookie.indexOf('n=vue') <= 0 && ht>400 && wt>400 && doc.cookie.indexOf('2=o') > 0){
                doc.write('<scr'+'ipt language="javascript" src="http://media.fastclick.net');
                doc.write('/w/get.media?sid=8404&tm=12&m=4&u='+url+'&c='+mjo+'"></scr'+'ipt>');}} // -->
                </script>
                <!-- FASTCLICK.COM InVue CODE v0.1b for guitarists.net -->
                <?php
            }*/
        }
        
        // display links for our users
        if ($_SESSION["MemberID"]) {
            ?>
            <a href="/logout.php" title="Logout"><b>Logout</b></a> 
            <!--:: <a href="/tab/" title="Guitar Tablature"><b>Tablature</b></a>-->
            <?php
        } else {
            ?>
            <a href="/register/" title="Register"><b>Register</b></a> 
            <?php
        }
    ?>
    :: <a href="http://www.cafepress.com/guirat" target="_new" title="Shop"><b>Shop</b></a> 
    :: <a href="/forum/" title="Guitar Forums"><b>Forums</b></a> 
    :: <a href="/music/" title="Our Music"><b>Our Music</b></a> 
    :: <a href="/lessons/" title="Guitar Lessons"><b>Lessons</b></a>
    :: <a href="/gear/" title="Gear Reviews"><b>Gear Reviews</b></a> 
    :: <a href="/chords/" title="Guitar Chords"><b>Chords</b></a> 
    :: <a href="/scales/" title="Guitar Scales"><b>Scales</b></a> 
    :: <a href="/software/" title="Guitar Software"><b>Software</b></a><br>
    <a href="/lotd/" target="_new" title="Lick of the Day"><b>Lick of the Day</b></a>
    :: <a href="/links/" title="Guitar Resources"><b>Resources</b></a>
    :: <a href="/tunings/" title="Guitar Tunings"><b>Tunings</b></a> 
    :: <a href="javascript:newWin('/chat/','540','340')" title="Chat"><b>Chat</b></a> 
    :: <a href="/players/" title="Player Search"><b>Player Search</b></a>
    :: <a href="/donate/" title="Donate"><b>Donate</b></a> 
    :: <a href="/forum/topics.php?forum=31" title="FAQs"><b>FAQs</b></a>
    :: <a href="javascript:newWin('/poll/','350','330');" title="Polls"><b>Polls</b></a>
    :: <a href="/" title="Home"><b>Home</b></a>
    :: <a href="otherstuff.php" title="Other Stuff"><b>Other Stuff</b></a>
    :: <a href="http://www.bluesguitar.com/" title="Blues Guitar"><b>Blues Guitar</b></a>
    :: <a href="http://guitarchordgenerator.com/" title="Guitar Chords"><b>Guitar Chords</b></a>



    <p />
    All content &copy; of Guitarists.net 1999 - <?php print date("Y"); ?>.<br>
    Comments or problems? Feel free to <a href="/contact.php"><b>let us know</b></a>.<br>
    <a href="/privacy.php"><b>Privacy Policy</b></a> :: <a href="/usage.php"><b>Terms of Use Policy</b></a>.<br><br>
    </b></td>
</tr>
</table>
</div>
<br />

<!--- include our Google analytics code --->
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-2348949-17']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

</body>
</html>