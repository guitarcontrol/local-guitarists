<?php
    /*
        top_banner.php
        
        This will either:
        
        1) display the static players image, or;
        2) display a 468 x 60 banner
    */
    
    // set our static header ID
    $headerID = 4;
    
    // see if they chose to turn banners off
    if (!$_SESSION["HideAds"]) {
        // make sure we're not in the forums
        if ($areaName == "forums") {
            $randID = rand(1,6);
        } else {
            // set our number to display
            $randID = rand(1,6);
        }
        
        // display our banner, based on the header selected
        if ($headerID < 3) {
            // see what banner type to display (FastClick, local, Google, etc)
            if ($randID < 3) {
                // display our FastClick ads
                ?>
                <!--- direct FastClick display (not our php ads) --->
                <!-- FASTCLICK.COM 468x60 v1.4 for guitarists.net -->
                <script language="Javascript"><!--
                var i=j=p=t=u=x=z=dc='';var id=f=0;var f=Math.floor(Math.random()*7777);
                id=8404; dc=document;u='ht'+'tp://media.fastclick.net/w'; x='/get.media?t=s';
                z=' width=468 height=60 border=0 ';t=z+'marginheight=0 marginwidth=';
                i=u+x+'&sid='+id+'&m=1&f=b&v=1.4&c='+f+'&r='+escape(dc.referrer);
                u='<a  hr'+'ef="'+u+'/click.here?sid='+id+'&m=1&c='+f+'"  target="_top">';
                dc.writeln('<ifr'+'ame src="'+i+'&d=f"'+t+'0 hspace=0 vspace=0 frameborder=0 scrolling=no>');
                if(navigator.appName.indexOf('Mic')<=0){dc.writeln(u+'<img src="'+i+'&d=n"'+z+'></a>');}
                dc.writeln('</iframe>'); // --></script><noscript>
                <a href="http://media.fastclick.net/w/click.here?sid=8404&m=1&c=1"  target="_top">
                <img src="http://media.fastclick.net/w/get.media?sid=8404&m=1&d=s&c=1&f=b&v=1.4"
                width=468 height=60 border=1></a></noscript>
                <!-- FASTCLICK.COM 468x60 v1.4 for guitarists.net -->
                <?php
            } else if ($randID == 7) {
                // include our php ads
                print "\t\t<!--- direct PHP Ads display --->\n";
                if (@require(getenv('DOCUMENT_ROOT').'/ads/phpadsnew.inc.php')) {
                    if (!isset($phpAds_context)) $phpAds_context = array();
                    $phpAds_raw = view_raw ('zone:2', 0, '', '', '0', $phpAds_context);
                    print $phpAds_raw['html'];
                }
            } else if ($randID >= 3 && $randID <= 6) {
                // display our Google AdWords
                ?>
                <!--- direct Google display (not our php ads) --->
                <script type="text/javascript"><!--
                google_ad_client = "ca-pub-3777083047736569";
                google_ad_width = 468;
                google_ad_height = 60;
                google_ad_format = "468x60_as";
                google_ad_channel ="9040558104";
                google_ad_type = "text";
                google_color_border = ["6699CC","FF4500","191933","660000"];
                google_color_bg = ["003366","FFEBCD","333366","7D2626"];
                google_color_link = ["FFFFFF","DE7008","99CC33","FFFFFF"];
                google_color_url = ["AECCEB","E0AD12","FFCC00","DAA520"];
                google_color_text = ["AECCEB","8B4513","FFFFFF","BDB76B"];
                //--></script>
                <script type="text/javascript"
                  src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
                </script>
                <?php
                // update the Google tracker
                $intGoogled = 1;
            }
        } else {
            if ($randID < 3) {
                // display the large FastClick banner
                ?>
                <!-- FASTCLICK.COM 468x60 and 728x90 Banner CODE for guitarists.net -->
                <script language="javascript" src="http://media.fastclick.net/w/get.media?sid=8404&m=1&tp=5&d=j&t=s"></script>
                <noscript><a href="http://media.fastclick.net/w/click.here?sid=8404&m=1&c=1" target="_top">
                <img src="http://media.fastclick.net/w/get.media?sid=8404&m=1&tp=5&d=s&c=1"
                width=728 height=90 border=1></a></noscript>
                <!-- FASTCLICK.COM 468x60 and 728x90 Banner CODE for guitarists.net -->
                <?php
            } else {
                // display the Google AdWords
                ?>
                <script type="text/javascript"><!--
                google_ad_client = "ca-pub-3777083047736569";
                google_ad_width = 728;
                google_ad_height = 90;
                google_ad_format = "728x90_as";
                google_ad_channel ="9040558104";
                google_ad_type = "text";
                google_color_border = ["6699CC","FF4500","191933","660000"];
                google_color_bg = ["003366","FFEBCD","333366","7D2626"];
                google_color_link = ["FFFFFF","DE7008","99CC33","FFFFFF"];
                google_color_url = ["AECCEB","E0AD12","FFCC00","DAA520"];
                google_color_text = ["AECCEB","8B4513","FFFFFF","BDB76B"];
                //--></script>
                <script type="text/javascript"
                  src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
                </script>
                <?php
                // update the Google tracker
                $intGoogled = 1;
            }
        }
    } else {
        // display the banner, based on the header chosen
        if ($headerID < 3) {
            ?>
            <script type="text/javascript"><!--
            google_ad_client = "ca-pub-3777083047736569";
            google_ad_width = 468;
            google_ad_height = 60;
            google_ad_format = "468x60_as";
            google_ad_channel ="9040558104";
            google_ad_type = "text";
            google_color_border = ["6699CC","FF4500","191933","660000"];
            google_color_bg = ["003366","FFEBCD","333366","7D2626"];
            google_color_link = ["FFFFFF","DE7008","99CC33","FFFFFF"];
            google_color_url = ["AECCEB","E0AD12","FFCC00","DAA520"];
            google_color_text = ["AECCEB","8B4513","FFFFFF","BDB76B"];
            //--></script>
            <script type="text/javascript"
              src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
            </script>
            <?php
        } else {
            ?>
            <script type="text/javascript"><!--
            google_ad_client = "ca-pub-3777083047736569";
            google_ad_width = 728;
            google_ad_height = 90;
            google_ad_format = "728x90_as";
            google_ad_channel ="9040558104";
            google_ad_type = "text";
            google_color_border = ["6699CC","FF4500","191933","660000"];
            google_color_bg = ["003366","FFEBCD","333366","7D2626"];
            google_color_link = ["FFFFFF","DE7008","99CC33","FFFFFF"];
            google_color_url = ["AECCEB","E0AD12","FFCC00","DAA520"];
            google_color_text = ["AECCEB","8B4513","FFFFFF","BDB76B"];
            //--></script>
            <script type="text/javascript"
              src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
            </script>
            <?php
            // update the Google tracker
            $intGoogled = 1;
        }
    }
?>
