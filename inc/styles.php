<?php
    // include our session code
    require("/home/gnet/includes/guitarists.net/global_vars.php");
    require_once("gnet_db.php");
    require_once("sessions.php");
    
    // set our font family
    switch ($_SESSION["Style"][0]) {
        case 1: $fontFamily = "Tahoma, Verdana, Arial"; break;
        case 2: $fontFamily = "Arial, Helvetica, sans-serif"; break;
        case 3: $fontFamily = "\"Times New Roman\", Times, serif"; break;
        case 4: $fontFamily = "\"Courier New\", Courier, monospace"; break;
        case 5: $fontFamily = "Verdana, Geneva, Arial, Helvetica, sans-serif"; break;
        case 6: $fontFamily = "\"MS Serif\", \"New York\", serif"; break;
        case 7: $fontFamily = "\"MS Sans Serif\", Geneva, sans-serif"; break;
        default: $fontFamily = "Tahoma, Verdana, Arial"; break;
    }
    
    // set our font size
    $fontSize = $_SESSION["Style"][1];
    
    // set our higher and lower font sizes
    $fontHigh = $fontSize + 1;
    $fontLow = $fontSize - 1;
    $fontSmall = $fontSize - 2;
    
    // create the content header
    header("Content-type: text/css");
?>
BODY {
    font-family: <?php print $fontFamily; ?>; 
    font-size : <?php print $fontSize; ?>px; 
    color: #000000;
    padding: 0px;
    margin: 0px;
    background: #ffffff url(/images/bground2.png) repeat-x;
} 
a {
    color: #0B2E68;
    text-decoration: underline;
}
a:hover {
    text-decoration: none;
    color: #204D90;
}
img {
    border-bottom: 0px;
}
td {
    font-family : <?php print $fontFamily; ?>;
    font-size : <?php print $fontSize; ?>px;
}
.input { 
    font-family: <?php print $fontFamily; ?>; 
    font-size: <?php print $fontSize; ?>px; 
    border: 1px solid #666666;
    background: #f6f6f6;
} 
.tabinput { 
    font-family: <?php print $fontFamily; ?>; 
    font-size: <?php print $fontSize; ?>px; 
    border: 1px solid #666666;
    background: #f6f6f6;
}
.button {
    background-color: #6B0A0C; 
    border-color: #ffffff; 
    height: 20px; 
    font-family: <?php print $fontFamily; ?>; 
    font-size: <?php print $fontSize; ?>px; 
    color: #ffffff; 
    /*font-weight: bold;*/
}
.smbutton {
    background-color: #6B0A0C; 
    border-color: #ffffff; 
    height: 18px; 
    font-family: <?php print $fontFamily; ?>; 
    font-size: <?php print $fontLow; ?>px; 
    color: #ffffff; 
    /*font-weight: bold;*/
}
.content {
    background: #ffffff;
    border-left: 2px solid #103874; /*top border style*/
    border-right: 2px solid #103874; /*right border style*/
}
.header {
    background: #ffffff;
    border-top: 2px solid #103874; /*right border style*/
    border-left: 2px solid #103874; /*top border style*/
    border-right: 2px solid #103874; /*right border style*/
    /* padding-top: 6px; */
}
.footer {
    background: #ffffff;
    border-bottom: 2px solid #103874;
    border-left: 2px solid #103874; /*top border style*/
    border-right: 2px solid #103874; /*right border style*/
}
.smalltxt {
    font-family: <?php print $fontFamily; ?>; 
    font-size : <?php print $fontLow; ?>px;
    color: #000000;
}
.tablehead {
    margin: 0;
    padding: 0;
    font: bold 11px <?php print $fontFamily; ?>;
    float: left;
    color: #ffffff;
    /*padding: 3px 10px 3px 10px; padding of tabs*/
    padding-top: 3px;
    padding-bottom: 3px;
    text-decoration: none;
    background: transparent url(/images/tablehead.gif) top right repeat-x;
    border-top: 1px solid #103874; /*top border style*/
    border-left: 1px solid #103874; /*left border style*/
    border-bottom: 3px solid #103874; /*thick bottom border below tabs*/
    border-right: 1px solid #103874; /*right border style*/
    width:775px;
}
.tablehead a {
    color: #f6f6f6;
    text-decoration: underline;
}
.tablehead a:hover {
    color: #ff8744;
    text-decoration: none;
}
.dropdown {
    font-family: <?php print $fontFamily; ?>;
    font-size : <?php print $fontSize; ?>px;
    color: #000000;
}
/* our main navigation styles */
#thicktabs {
    margin: 0;
    padding: 0;
    font: bold 11px <?php print $fontFamily; ?>;
}
#thicktabs li{
    display: inline;
}
#thicktabs li a {
    float: left;
    color: #ffffff;
    padding: 3px 10px 3px 10px; /*padding of tabs*/
    text-decoration: none;
    background: url(/images/color_tabs_bground.gif) top right no-repeat;
    background-color: #387ED0;
    border-top: 1px solid #103874; /*top border style*/
    border-bottom: 3px solid #103874; /*thick bottom border below tabs*/
}
#thicktabs li a#currentitem{
    float: left;
    color: #ffffff;
    padding: 3px 10px 3px 10px; /*padding of tabs*/
    text-decoration: none;
    background: transparent url(/images/color_tabs_hover2.gif) top right no-repeat;
    border-top: 1px solid #103874; /*top border style*/
    border-bottom: 3px solid #103874; /*thick bottom border below tabs*/
}
#thicktabs li a#leftmostitem { /*Extra CSS for left most menu item*/
    border-left: 1px solid #103874; /*left border style*/
}
#thicktabs li a#rightmostitem { /*Extra CSS for right most menu item*/
    border-right: 1px solid #103874; /*right border style*/
    background-position: top left; /*Position background image to the left instead of default right, to hide indented underline for this link*/
}
#thicktabs li a:visited {
    color: #ffffff;
}
#thicktabs li a:hover {
    color: #ffffff;
    background-image: url(/images/color_tabs_hover.gif); /*background image swap on hover*/
    background-color: #DB5D30;
}

/* our member options box (login or member links) */
#memberopts {
	text-align:center;
	padding: 2px 8px 8px 8px; /* top right bottom left */
    background-color: #103773;
	color: #ffffff;
	font-family: <?php print $fontFamily; ?>;
    letter-spacing: 1px;
    font-size: 11px;
    border-right: 2px solid #639fd9;
    border-bottom: 2px solid #639fd9;
    border-left: 2px solid #639fd9;
}
#memberopts a {
	color: #ff8744;
	/*font-weight: bold;*/
	text-decoration: none;
    padding-bottom: 0px;
	/*border-bottom:#ff8744 1px dashed;*/
}
#memberopts a:hover {
	text-decoration: underline;
	border-bottom: 0px;
}
.bluebox {
	font-family: <?php print $fontFamily; ?>;
	font-size: 10px;
	background-color: #f6f6f6;
	border: 2px solid #D16514;
}
.bluebutton {
	font-family: <?php print $fontFamily; ?>;
	font-size: 10px;
	color: #FFFFFF;
	background-color: #093657;
	border-top-width: 2px;
	border-right-width: 2px;
	border-bottom-width: 2px;
	border-left-width: 2px;
	border-top-color: #D16514;
	border-right-color: #D16514;
	border-bottom-color: #D16514;
	border-left-color: #D16514;
}

/* top right-hand menu */
#chromemenu {
    /* width: 99%; */
    height: 17px;
    text-align: center;
    /* font-weight: bold; */
    font-size: 90%;
}
#chromemenu:after { /*Add margin between menu and rest of content in Firefox*/
    content: "."; 
    display: block; 
    height: 0; 
    clear: both; 
    visibility: hidden;
}
#chromemenu ul {
    width: 100%;
    /* background: #103773; */
    padding: 0;
    margin: 0;
    text-align: left; /*set value to "right" for example to align menu to the left of page*/
}
#chromemenu ul li{
    display: inline;
}
#chromemenu ul li a{
    color: #ffffff;
    font-family: <?php print $fontFamily; ?>;
    font-size: 11px;
    height: 19px;
    line-height: 19px;
    padding: 0px 8px 0 8px;
    margin: 0;
    text-decoration: none;
    border-bottom: 0px;
}
#chromemenu ul li a:hover{
    color: #ffffff; 
    background: #135095;
}
.innertitle {
    color: #ffffff;
    font-weight: bold;
    background-color: #387ed0;
}
.innertitle a {
    color: #ffffff;
    text-decoration: underline;
}
.innertitle a:hover {
    text-decoration: none;
}
.innerhead {
    background-color: #71b2ea;
}
html>body #feature {
    margin-right: -6px;
}
#feature {
    width: 150px;
    padding: 0px;
    margin-left: 10px;
    margin-right: 0px;
    margin-top: 10px;
    float:right;
    voice-family: "\"}\"";
    voice-family: inherit;
}
.features {
    border-left: 2px solid #103874; 
    border-bottom: 2px solid #103874;
    border-top: 2px solid #103874;
    border-right: 2px solid #103874;
    background: #ffffff;
    color: #000000;
    padding: 0px;
    margin-bottom: 2px;
    text-align: left;
    font-size: 10px;
}
.featuretitle {
    font-family: <?php print $fontFamily; ?>;
    font-size: 12px;
    font-weight: bold;
    background: #397FD1;
    color: #ffffff;
    padding-top: 2px;
    padding-bottom: 2px;
    padding-left: 2px;
}
.featuretitle a {
    color: #ffffff;
    text-decoration: none;
}
.featuretitle a:hover {
    color: #ffffff;
    text-decoration: underline;
}
.featurecontent {
    font-family: <?php print $fontFamily; ?>;
    font-size: 11px;
    color: #000000;
    background-color: #ffffff;
    padding-top: 0px;
    padding-left: 2px;
    padding-bottom: 0px;
}
.featurecontent td {
    font-family: <?php print $fontFamily; ?>;
    font-size: 11px;
    color: #000000;
}
.featurecontent ul {
    list-style: none;
	margin-left: 0;
    margin-top: 2px;
    margin-bottom: 4px;
	padding-left: 1.2em;
	text-indent: -1em;
}
.featurecontent a {
    text-decoration: none;
    border-bottom: 1px dotted #469;
    font-weight: bold;
}
.featurecontent a:hover {
    border-bottom-style: solid;
}
#hilite {
    width: 98%;
    padding: 0px;
    margin-left: 15px;
    margin-right: 5px;
    margin-top: 10px;
    float:right;
    voice-family: "\"}\"";
    voice-family: inherit;
}
#mainfeature {
    width: 100%;
    padding: 0px;
    margin-left: 10px;
    margin-right: 0px;
    margin-top: 10px;
    float:right;
    voice-family: "\"}\"";
    voice-family: inherit;
}
.mainfeatures {
    /*border-left: 2px solid #BACCE7; 
    border-bottom: 2px solid #BACCE7;
    border-top: 2px solid #BACCE7;
    border-right: 2px solid #BACCE7;*/
    border-top: 2px dashed #BACCE7;
    background: #ffffff;
    color: #000000;
    padding: 0px;
    margin-bottom: 2px;
    text-align: left;
    font-size: 10px;
}
.maintitle {
    font-family: <?php print $fontFamily; ?>;
    font-size: 12px;
    font-weight: bold;
    color: #0B2E68;
    /*background: #BACCE7;*/
    padding-top: 10px;
    padding-bottom: 2px;
    padding-left: 2px;
}
.maintitle a {
    text-decoration: none;
    color: #0B2E68;
}
.maintitle a:hover {
    font-color: #204D90;
    text-decoration: underline;
}
.pagination {
    padding: 2px;
}
.pagination ul {
    margin: 0;
    padding: 0;
    text-align: left; /*Set to "right" to right align pagination interface*/
    font-size: 11px;
}
.pagination li {
    list-style-type: none;
    display: inline;
    padding-top: 2px;
    padding-bottom: 2px;
}
.pagination a, .pagination a:visited {
    padding: 0 5px;
    border: 1px solid #103874;
    text-decoration: none; 
    background-color: #387ED0;
    color: #ffffff;
    font-weight: bold;
}
.pagination a:hover, .pagination a:active {
    border: 1px solid #103874;
    color: #ffffff;
    background-color: #DB5D30;
    text-decoration: underline;
}
.pagination li.currentpage {
    font-weight: bold;
    padding: 0 5px;
    border: 1px solid #103874;
    background-color: #1D4B8F;
    color: #ffffff;
}
.pagination li.disablepage {
    padding: 0 5px;
    border: 1px solid #929292;
    color: #929292;
}
.pagination li.nextpage {
    font-weight: bold;
}
* html .pagination li.currentpage, * html .pagination li.disablepage{ /*IE 6 and below. Adjust non linked LIs slightly to account for bugs*/
    margin-right: 5px;
    padding-right: 0;
}

/* our buddies styles */
#buddies-table {
    float: right;
    padding: 0 0 0 10px;
}

/* our base fluid design */
#blog-left, #blog-right, #blog-footer {
    overflow: hidden;
    display: inline-block;
}
#blog-footer {
    width: 100%;
}
#blog-left, #blog-right {
    float: left;
}
#blog-left {
    width: 80%;
}
#blog-right {
    width: 19.9%;
}
#blog-footer {
    clear: left;
}

/* our blog styling */
.blog_post {
    float: left;
    width: 95%;
    padding: 10px;
    margin: 4px;
    background-color: #f6f6f6;
    border: 1px solid #AAD4FF;
    clear: both;
}
.blog_post_full {
    float: left;
    width: 95%;
    padding: 10px;
    margin: 4px;
    background-color: #f6f6f6;
    border: 1px solid #AAD4FF;
    clear: both;
}
.blog_title h2 {
    font-size: 14px;
    padding: 0 0 2px 0;
    margin: 0;
}
.blog_owner, .blog_comment_owner, .description {
    font-size: 10px;
}
.blog_owner a, .blog_read a, .blog_comment_owner a {
    font-weight: bold;
}
.blog_teaser, .blog_comment_text {
    padding: 10px 0;
}
.blog_tags {
    padding: 6px;
}
.blog_edit {
    color: green;
}
.blog_delete {
    color: red;
}
.blog_comment {
    float: left;
    width: 93%;
    padding: 10px;
    margin: 4px 4px 4px 20px;
    background-color: #f6f6f6;
    border: 1px solid #AAD4FF;
    clear: both;
}
.blog_comment_title h3 {
    font-size: 12px;
    padding: 0 0 2px 0;
    margin: 0;
}
.blog_options {
    clear: both;
    float: left;
}
#comment_form {
    padding: 10px;
    margin: 4px 4px 4px 20px;
    background-color: #f6f6f6;
    border: 1px solid #FFD47F;
    clear: both;
    width: 450px;
}
#comment_form .input {
    background-color: #ffffff;
}
.form-item {
    padding: 4px;
    margin: 4px;
}

/* tag cloud styles */
#cloud_tag, #cloud_user {
    padding-bottom: 10px;
    margin-bottom: 10px;
    float: left;
    width: 95%;
    clear: both;
}
#cloud_tag .head, #cloud_user .head {
    font-size: 14px;
    padding: 0 0 2px 0;
    margin: 0;
}
#blog-tag-cloud, #blog-user-cloud {
    padding-bottom: 10px;
    margin-bottom: 10px;
    clear: left;
}
.tag-cloud {
    margin: 0;
    padding: 0;
    text-indent: 0;
    margin-left: 0;
    padding-left: 0;
}
.tag-cloud li {
    display: inline;
}
.tag-cloud span {
    position: absolute;
    left: -999px;
    width: 990px;
}
.tag-cloud .cloud1 {
    font-size: 1em;
}
.tag-cloud .cloud2 {
    font-size: 1.2em;
}
.tag-cloud .cloud3 {
    font-size: 1.5em;
}
.tag-cloud .cloud4 {
    font-size: 1.7em;
}
.tag-cloud .cloud5 {
    font-size: 1.9em;
}
.tag-cloud .cloud6 {
    font-size: 2.2em;
}

.clearout {
    clear: both;
}

ul#licks {
    margin-left: 0;
    padding-left: 0;
}
ul#licks li {
    list-style-type: none;
    padding-bottom: 10px;
}
ul#licks li a {
    font-weight: bold;
}
div#lotd-movie {
    float: right;
}