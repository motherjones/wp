<?php
/**
 * this is for moving over our blocks programmatically
 */
$hostname="localhost";  
$username="root";   
$password=$argv[1];

$wp_db = "pantheon_wp";  
$wp = new PDO("mysql:host=$hostname;dbname=$wp_db", $username, $password);  


$widgets = Array('top_stories_widget', 'mj_author_bio_widget', 
								'mj_related_articles', 'mj_blog_pager');
$widget_options = $wp->prepare("
UPDATE wp_options
SET option_value = 'a:2:{i:2;a:0:{}s:12:\"_multiwidget\";i:1;}'
WHERE option_name = CONCAT('widget_', ?)
;
");
$wp->beginTransaction();
foreach ($widgets as $widget) {
	$widget_options->execute(Array($widget));
}
$wp->commit();

/* Desired result:
a:7:{
s:19:"wp_inactive_widgets";a:0:{}
s:7:"sidebar";a:2:{i:0;s:6:"text-5";i:1;s:6:"text-6";}
s:6:"ticker";a:1:{i:0;s:6:"text-7";}
s:11:"content-end";a:0:{}
s:8:"page-end";a:1:{i:0;s:6:"text-4";}
s:8:"page-top";a:2:{i:0;s:6:"text-2";i:1;s:6:"text-3";}
s:13:"array_version";i:3;}

FUUUCCCCKKK!!!! THis isn't titles this is some weird interior naming fuck
 */
$sidebars_widgets_options = Array(
		'wp_inactive_widgets' => Array(),
		'sidebar' => Array('text-9', 'text-5', 'text-6', 'top_stories_widget-2'),  //RHC ad, RHC membership for blog posts and not blog posts
		'ticker' => Array('text-7'), //Membership ticker
		'content-end' => Array('mj_author_bio_widget-1', 'text-8', 'mj_related_articles-1', 'mj_blog_pager-1'), //FIXME needs to contain author bio, text block for members like you text-8, related articles, and blog pager
		'page-end' => Array('text-4'), //bottom adblock ask
		'page-top' => Array('text-2', 'text-3'),//Ad control, sitewrap
		'array_version' => 3
);
$wp->beginTransaction();
$sidebar_widgets = $wp->prepare('
UPDATE wp_options
SET option_value = ?
WHERE option_name = "sidebars_widgets"
;
');
$sidebar_widgets->execute(Array(
		serialize( $sidebars_widgets_options )
));
$wp->commit();

/* desired block result 
a:7:{
	i:2;a:6:{s:5:"title";s:10:"Ad Control";s:4:"text";s:40:"<script> Something sometihng </script>";s:6:"filter";b:0;s:10:"dw_include";i:0;s:9:"dw_logged";s:0:"";s:9:"other_ids";s:0:"";}
	i:3;a:6:{s:5:"title";s:9:"Site wrap";s:4:"text";s:46:"<script> //whole lot of stuff here </script>";s:6:"filter";b:0;s:10:"dw_include";i:0;s:9:"dw_logged";s:0:"";s:9:"other_ids";s:0:"";}
	i:4;a:6:{s:5:"title";s:18:"Bottom adblock bar";s:4:"text";s:34:"<p>i am the bottom block hello</p>";s:6:"filter";b:0;s:10:"dw_include";i:0;s:9:"dw_logged";s:0:"";s:9:"other_ids";s:0:"";}
	i:5;a:7:{
		s:5:"title";s:23:"Interior RHC Membership";
		s:4:"text";s:74:"<p>whold bunch of stuff goes here for rhc membership not in blog pages</p>";
		s:6:"filter";b:0;
		s:10:"dw_include";i:0;
		s:9:"dw_logged";s:0:"";
		s:9:"other_ids";s:0:"";
		s:17:"type-mj_blog_post";i:1;
	}
	i:6;a:7:{
		s:5:"title";s:29:"RHC Membership for blog posts";
		s:4:"text";s:55:"<p> a membership thing but only for blog posts here</p>";
		s:6:"filter";b:0;
		s:10:"dw_include";i:1;
		s:9:"dw_logged";s:0:"";
		s:9:"other_ids";s:0:"";
		s:17:"type-mj_blog_post";i:1;
	}
	i:7;a:6:{s:5:"title";s:21:"Membership Ticker Bar";s:4:"text";s:63:"<p>remember when this was yellow and the legal defense bar?</p>";s:6:"filter";b:0;s:10:"dw_include";i:0;s:9:"dw_logged";s:0:"";s:9:"other_ids";s:7:"1,2,3,4";}
	s:12:"_multiwidget";i:1;
}
actual result
a:7:{
	i:0;s:12:"_multiwidget";
	i:1;a:6:{
		s:5:"title";s:10:"Ad Control";
		s:4:"text";s:67:"<script> //okay put the show hide variables here for ads </script>";
		s:6:"filter";b:0;
		s:10:"dw_include";i:0;
		s:9:"dw_logged";s:0:"";
		s:9:"other_ids";s:0:"";
	}
	i:2;a:6:{s:5:"title";s:9:"Site wrap";s:4:"text";s:278:"<script language="javascript"> <!-- if (typeof(MJ_HideSiteWrap) === 'undefined') { $('head').append('<link rel="stylesheet" href="http://assets.motherjones.com/advertising/2014/05/sierra_club_sitewrap.css" type="text/css" />'); ad_code('siteskin',1559); } //--> </script>";
	s:6:"filter";b:0;
		s:10:"dw_include";i:0;
		s:9:"dw_logged";s:0:"";
		s:9:"other_ids";s:0:"";
	}
	i:3;a:6:{
		s:5:"title";s:18:"Bottom adblock bar";
		s:4:"text";s:366:"<div id="bottom-donate" style="display:none"> <p> We noticed you have an ad blocker on.  Support nonprofit investigative reporting by pitching in a few bucks.  <a href="https://secure.motherjones.com/fnp/?action=SUBSCRIPTION&list_source=7HEGZS1A&extra_don=1&abver=A">DONATE</a> <span onclick="$('#bottom-donate').remove();">X</span> </p> </div>";
		s:6:"filter";b:0;
		s:10:"dw_include";i:0;
		s:9:"dw_logged";s:0:"";
		s:9:"other_ids";s:0:"";
	}
	i:4;a:7:{
		s:5:"title";s:29:"RHC membership for blog posts";
		s:4:"text";s:1405:"<div id="interior-rhc-membership"> <style> #interior-rhc-membership { width: 300px; display: block; margin: 0 auto; height: 298px; background-image: url('/sites/all/assets/Membership_ADSBasic_9_Short2_.png'); background-size: contain; } #interior-rhc-membership .donation { display: block; height: 30px; position: relative; top: 132px; } #interior-rhc-membership .donation:hover { background: rgba(255, 255, 255, .5) } #interior-rhc-membership .donation.onetime { width: 118px; left: 25px; } #interior-rhc-membership .donation.monthly { top: 102px; left: 155px; width: 117px; } #interior-rhc-membership button:hover { background: rgba(255, 255, 255, .5) } #interior-rhc-membership .subscribe { display: block; position: relative; top: 185px; height: 30px; left: 60px; width: 175px; } #interior-rhc-membership .subscribe:hover { background: rgba(255, 255, 255, .5) } </style> <a class="onetime donation" target="_blank" href="https://secure.motherjones.com/fnp/?action=SUBSCRIPTION&list_source=7HEGPA03&extra_don=1&abver=A"></a> <a class="monthly donation" target="_blank" href="https://secure.motherjones.com/fnp/?action=SUBSCRIPTION&list_source=7HEGPAM3&extra_don=1&abver=B"></a> <a class="subscribe" target="_blank" href="https://secure.motherjones.com/fnx/?action=SUBSCRIPTION&pub_code=MJM&term_pub=MJM&list_source=SEGYN1B&base_country=US"></a> </div>";
		s:6:"filter";b:0;
		s:10:"dw_include";i:1;
		s:9:"dw_logged";s:0:"";
		s:9:"other_ids";s:0:"";
		s:17:"type-mj_blog_post";i:1;
	}
	i:5;a:7:{
		s:5:"title";s:14:"Membership RHC";
		s:4:"text";s:3593:"<style type="text/css"> #interior-rhc-membership { width: 300px; display: block; float: right; height: 721px; background-image: url('http://assets.motherjones.com/sitewide/Membership_AD-421.jpg'); background-size: contain; } #interior-rhc-membership .donation { display: block; height: 30px; position: relative; top: 180px; } #interior-rhc-membership .donation:hover { background: rgba(255, 255, 255, .5) } #interior-rhc-membership .donation.onetime { width: 118px; left: 25px; } #interior-rhc-membership .donation.monthly { width: 118px; top: 150px; left: 155px; } #interior-rhc-membership form { position: relative; top: 240px; clear: both; display: block; margin: 0 auto; width: 244px; overflow: hidden; border-radius: 5px; height: 26px; } #interior-rhc-membership form button { border: transparent; background: transparent; width: 72px; height: 29px; font-size: 0px; position: relative; top: -4px; } #interior-rhc-membership button:hover { background: rgba(255, 255, 255, .5) } #interior-rhc-membership form input { background: white; border: transparent; position: relative; top: 2px; left: -2px; width: 160px; height: 20px; outline: none!important; } #interior-rhc-membership form input { text-align: center; font-family: Mallory, sans-serif; font-weight: bold; line-height: 24px; font-size: 13px; letter-spacing: -1px; } #interior-rhc-membership .subscribe { display: block; position: relative; top: 352px; height: 30px; left: 26px; width: 127px; } #interior-rhc-membership .subscribe:hover { background: rgba(255, 255, 255, .5) } #interior-rhc-membership .subscribe_cover { display: block; position: relative; top: 320px; height: 228px; left: 20px; width: 254px; } #interior-rhc-membership .subscribe_cover:hover { background: rgba(255, 255, 255, .5) } </style> <div id="interior-rhc-membership"> <a class="onetime donation" target="_blank" href="https://secure.motherjones.com/fnp/?action=SUBSCRIPTION&list_source=7HEGPA01&extra_don=1&abver=A"></a> <a class="monthly donation" target="_blank" href="https://secure.motherjones.com/fnp/?action=SUBSCRIPTION&list_source=7HEGPAM1&extra_don=1&abver=B"></a> <form action="https://api.maropost.com/accounts/585/forms/3289/subscribe/177d8ba3b7fe7d39e28dcba73123eeffbd01878b" method="post" id="emailForm" onsubmit="return MJ_check_email(this);"> <button alt="SIGN UP" border="0" height="25" name="commit" id="submit" onclick="ga('send', 'event', 'TopRightFollowBox', 'Email|Click', window.location.pathname);" type="image" value="Submit">SIGN UP</button> <input gtbfieldid="27" include_blank="true" start_year="1950" name="contact_fields[email]" id="cons_email" placeholder="YOUR EMAIL" type="text" /> <input include_blank="true" start_year="1950" type="hidden" name="custom_fields[outreach_affiliate_code]" id="custom_fields_outreach_affiliate_code" value="Article_Membership_Box" /> <input include_blank="true" start_year="1950" type="hidden" name="custom_fields[signup_url]" id="signup_url" value="" /> <input type="hidden" value="" id="email_field" name="email_const_mp" /> </form> <a class="subscribe_cover" target="_blank" href="https://secure.motherjones.com/fnx/?action=SUBSCRIPTION&pub_code=MJM&term_pub=MJM&list_source=SEGYN2&base_country=US "></a> <a class="subscribe" target="_blank" href="https://secure.motherjones.com/fnx/?action=SUBSCRIPTION&pub_code=MJM&term_pub=MJM&list_source=SEGYN2&base_country=US "></a> </div>"; s:6:"filter";b:0;s:10:"dw_include";i:0;s:9:"dw_logged";s:0:"";s:9:"other_ids";s:0:"";s:17:"type-mj_blog_post";i:1;}i:6;a:6:{s:5:"title";s:21:"Membership ticker bar";s:4:"text";s:1283:"<style> #main_bar_div { background: url('http://assets.motherjones.com/sitewide/membership-background-bar-lite-2.png') top left transparent; padding: 2px 5px 4px 5px !important; width: 960px !important; margin: 5px auto 25px auto !important; } a.links_membership_bar { color: #ff6900 !important; font-weight: bold; } a:hover.links_membership_bar { color: #ff9c33 !important; } </style> <div id="main_bar_div"> <p style="text-align: center;font-family: Mallory,sans-serif;font-size: 16px;font-weight: bold;">It's going to take <a href="http://www.motherjones.com/media/2016/12/reporting-on-trumps-conflicts-of-interest" target="_blank" onclick="var url = $(event.target).attr('href'); ga('send', 'event', 'MembershipBar', url, document.location);"class="links_membership_bar">everything we've got to cover the Trump administration</a>.<br /> Help us do it with <a href="https://secure.motherjones.com/fnx/?action=SUBSCRIPTION&pub_code=DON&term_pub=DON&b_country=United+States&list_source=7H6CPOBD3&term=XX.1.50.00.DON.D.0.2246" target="_blank" onclick="var url = $(event.target).attr('href'); ga('send', 'event', 'MembershipBar', url, document.location);"class="links_membership_bar">a tax-deductible, year-end donation today</a>.</p> </div>";
		s:6:"filter";b:0;
		s:10:"dw_include";i:0;
		s:9:"dw_logged";s:0:"";
		s:9:"other_ids";s:0:"";
	}
}

*/ 

/**
 * OMFG THIS IS VERY IMPORTANT!!!!
 * DO NOT REPEAT DO NOT MESS WITH THIS WITHOUT FIXING THE ROW WHERE WE
 * SAY WHAT BLOCKS GO IN WHICH SIDEBAR
 * THAT ROW REQUIRES THE ID TO BE THE POSITION IN THIS ARRAY
 * YEAH, I KNOW, FUCKED UP
 */
$blocks = Array();
$blocks[2] =	Array(
		'title' => 'Ad Control',
		'text' => <<<'HTML'
<script>
	//okay put the show hide variables here for ads
</script>
HTML
		,
		'filter' => False,
		'dw_include' => 0,
		'dw_logged' => '',
		'other_ids' => '',
	); //END Ad Control
$blocks[3] = Array(
		'title' => 'Site wrap',
		'text' => <<<'HTML'
<script language="javascript"> 
<!-- 
if (typeof(MJ_HideSiteWrap) === 'undefined') {
  $('head').append('<link rel="stylesheet" href="http://assets.motherjones.com/advertising/2014/05/sierra_club_sitewrap.css" type="text/css" />');
  ad_code('siteskin',1559);
}
//--> 
</script>
HTML
		,
		'filter' => False, //this is the "show only on, show not on" toggle
		'dw_include' => 0,
		'dw_logged' => '',
		'other_ids' => '',
	); //END site wrap
$blocks[4] =	Array(
		'title' => 'Bottom adblock bar',
		'text' => <<<'HTML'
<div id="bottom-donate" style="display:none">
  <p>
    We noticed you have an ad blocker on.
    Support nonprofit investigative reporting by pitching in a few
    bucks.
    <a href="https://secure.motherjones.com/fnp/?action=SUBSCRIPTION&list_source=7HEGZS1A&extra_don=1&abver=A">DONATE</a>
    <span onclick="$('#bottom-donate').remove();">X</span>
  </p>
</div>
HTML
		,
		'filter' => False, //this is the "show only on, show not on" toggle
		'dw_include' => 0,
		'dw_logged' => '',
		'other_ids' => '',
	); //END bottom adblock bar
$blocks[5] =	Array(
		'title' => 'RHC membership for blog posts',
		'text' => <<<'HTML'
<div id="interior-rhc-membership">
<style>
#interior-rhc-membership {
  width: 300px;
  display: block;
  margin: 0 auto;
  height: 298px;
  background-image: url('/sites/all/assets/Membership_ADSBasic_9_Short2_.png');
  background-size: contain;
}

#interior-rhc-membership .donation {
  display: block;
  height: 30px;
  position: relative;
  top: 132px;
}
#interior-rhc-membership .donation:hover {
  background: rgba(255, 255, 255, .5)
}
#interior-rhc-membership .donation.onetime {
  width: 118px;
  left: 25px;
}
#interior-rhc-membership .donation.monthly {
  top: 102px;
  left: 155px;
  width: 117px;
}
#interior-rhc-membership button:hover {
  background: rgba(255, 255, 255, .5)
}

#interior-rhc-membership .subscribe {
  display: block;
  position: relative;
  top: 185px;
  height: 30px;
  left: 60px;
  width: 175px;
}
#interior-rhc-membership .subscribe:hover {
  background: rgba(255, 255, 255, .5)
}

</style>
<a class="onetime donation" target="_blank" href="https://secure.motherjones.com/fnp/?action=SUBSCRIPTION&list_source=7HEGPA03&extra_don=1&abver=A"></a>
<a class="monthly donation" target="_blank" href="https://secure.motherjones.com/fnp/?action=SUBSCRIPTION&list_source=7HEGPAM3&extra_don=1&abver=B"></a>
<a class="subscribe" target="_blank" href="https://secure.motherjones.com/fnx/?action=SUBSCRIPTION&pub_code=MJM&term_pub=MJM&list_source=SEGYN1B&base_country=US"></a>
</div>
HTML
		,
		'filter' => False, //this is the "show only on, show not on" toggle
		'dw_include' => 1,
		'dw_logged' => '',
		'other_ids' => '',
		'type-mj_blog_post' => 1
	); //END membership for blog posts
$blocks[6] =	Array(
		'title' => 'Membership RHC',
		'text' => <<<'HTML'
<style type="text/css">
#interior-rhc-membership {
  width: 300px;
  display: block;
  float: right;
  height: 721px;
  /*background-image: url('/sites/all/assets/membership_ads123_8a_.png');*/
  background-image: url('http://assets.motherjones.com/sitewide/Membership_AD-421.jpg');
  background-size: contain;
}

#interior-rhc-membership .donation {
  display: block;
  height: 30px;
  position: relative;
  top: 180px;
}
#interior-rhc-membership .donation:hover {
  background: rgba(255, 255, 255, .5)
}
#interior-rhc-membership .donation.onetime {
  width: 118px;
  left: 25px;
}
#interior-rhc-membership .donation.monthly {
  width: 118px;
  top: 150px;
  left: 155px;
}
#interior-rhc-membership form {
  position: relative;
  top: 240px;
  clear: both;
  display: block;
  margin: 0 auto;
  width: 244px;
  overflow: hidden;
  border-radius: 5px;
  height: 26px;
}
#interior-rhc-membership form button {
  border: transparent;
  background: transparent;
  width: 72px;
  height: 29px;
  font-size: 0px;
  position: relative;
  top: -4px;
}
#interior-rhc-membership button:hover {
  background: rgba(255, 255, 255, .5)
}

#interior-rhc-membership form input {
  background: white;
  border: transparent;
  position: relative;
  top: 2px;
  left: -2px;
  width: 160px;
  height: 20px;
  outline: none!important;
}
#interior-rhc-membership form input {
  text-align: center;
  font-family: Mallory, sans-serif;
  font-weight: bold;
  line-height: 24px;
  font-size: 13px;
  letter-spacing: -1px;
}
#interior-rhc-membership .subscribe {
  display: block;
  position: relative;
  top: 352px;
  height: 30px;
  left: 26px;
  width: 127px;
}
#interior-rhc-membership .subscribe:hover {
  background: rgba(255, 255, 255, .5)
}
#interior-rhc-membership .subscribe_cover {
  display: block;
  position: relative;
  top: 320px;
  height: 228px;
  left: 20px;
  width: 254px;
}
#interior-rhc-membership .subscribe_cover:hover {
  background: rgba(255, 255, 255, .5)
}
</style>
<div id="interior-rhc-membership">
<a class="onetime donation" target="_blank" href="https://secure.motherjones.com/fnp/?action=SUBSCRIPTION&list_source=7HEGPA01&extra_don=1&abver=A"></a>
<a class="monthly donation" target="_blank" href="https://secure.motherjones.com/fnp/?action=SUBSCRIPTION&list_source=7HEGPAM1&extra_don=1&abver=B"></a>

<form action="https://api.maropost.com/accounts/585/forms/3289/subscribe/177d8ba3b7fe7d39e28dcba73123eeffbd01878b" method="post" id="emailForm" onsubmit="return MJ_check_email(this);">
  <button alt="SIGN UP" border="0" height="25" name="commit" id="submit" onclick="ga('send', 'event', 'TopRightFollowBox', 'Email|Click', window.location.pathname);" type="image" value="Submit">SIGN UP</button>
  <input gtbfieldid="27" include_blank="true" start_year="1950" name="contact_fields[email]" id="cons_email" placeholder="YOUR EMAIL" type="text" />
  <input include_blank="true" start_year="1950" type="hidden" name="custom_fields[outreach_affiliate_code]" id="custom_fields_outreach_affiliate_code" value="Article_Membership_Box" />
  <input include_blank="true" start_year="1950" type="hidden" name="custom_fields[signup_url]" id="signup_url" value="" />
  <input type="hidden" value="" id="email_field" name="email_const_mp" />
</form>
<a class="subscribe_cover" target="_blank" href="https://secure.motherjones.com/fnx/?action=SUBSCRIPTION&pub_code=MJM&term_pub=MJM&list_source=SEGYN2&base_country=US "></a>
<a class="subscribe" target="_blank" href="https://secure.motherjones.com/fnx/?action=SUBSCRIPTION&pub_code=MJM&term_pub=MJM&list_source=SEGYN2&base_country=US "></a>
</div>
HTML
		,
		'filter' => False, //this is the "show only on, show not on" toggle
		'dw_include' => 0,
		'dw_logged' => '',
		'other_ids' => '',
		'type-mj_blog_post' => 1
	); //END membership RHC
$blocks[7] = Array(
		'title' => 'Membership ticker bar',
		'text' => <<<'HTML'
<style>
  #main_bar_div {
    background: url('http://assets.motherjones.com/sitewide/membership-background-bar-lite-2.png') top left transparent;
    padding: 2px 5px 4px 5px !important;
    width: 960px !important;
    margin: 5px auto 25px auto !important;
  }
  a.links_membership_bar {
    color: #ff6900 !important;
    font-weight: bold;
  }
  a:hover.links_membership_bar {
    color: #ff9c33 !important;
  }
</style>

<div id="main_bar_div">
  <p style="text-align: center;font-family: Mallory,sans-serif;font-size: 16px;font-weight: bold;">It's going to take <a href="http://www.motherjones.com/media/2016/12/reporting-on-trumps-conflicts-of-interest" target="_blank" onclick="var url = $(event.target).attr('href'); ga('send', 'event', 'MembershipBar', url, document.location);"class="links_membership_bar">everything we've got to cover the Trump administration</a>.<br />
Help us do it with <a href="https://secure.motherjones.com/fnx/?action=SUBSCRIPTION&pub_code=DON&term_pub=DON&b_country=United+States&list_source=7H6CPOBD3&term=XX.1.50.00.DON.D.0.2246" target="_blank" onclick="var url = $(event.target).attr('href'); ga('send', 'event', 'MembershipBar', url, document.location);"class="links_membership_bar">a tax-deductible, year-end donation today</a>.</p>
</div>
HTML
		,
		'filter' => False, //this is the "show only on, show not on" toggle
		'dw_include' => 0,
		'dw_logged' => '',
		'other_ids' => '', //FIXME get a list of all the full width asks and put the node ids in here separated by commas
	); //END membership ticker bar
$blocks[8] =	Array( //BEGIN members like you block
		'title' => 'Members like you',
		'text' => <<<'HTML'
<p class="members-like-you">
  <em>Mother Jones</em> is a nonprofit, and stories like this are
   made possible by readers like you. 
  <a class="donate" href="https://secure.motherjones.com/fnp/?action=SUBSCRIPTION&amp;list_source=7HEGP004&amp;extra_don=1&amp;abver=A">Donate</a>
   or <a class="subscribe" href="https://secure.motherjones.com/fnx/?action=SUBSCRIPTION&amp;pub_code=MJM&amp;term_pub=MJM&amp;list_source=SEGYN4&amp;base_country=US">subscribe</a>
   to help fund independent journalism.
</p>
HTML
		,
		'filter' => False,
		'dw_include' => 0,
		'dw_logged' => '',
		'other_ids' => '',
	); //END members like you block
$blocks[9] =	Array( //BEGIN members like you block
		'title' => 'RHC ad',
		'text' => <<<'HTML'
<script language="javascript">
		<!--
		if (typeof MJ_HideRightColAds === 'undefined') {
			ad_code({
				desktop: true,
				placement: 'RightTopROS300x600',
				height: 529,
				doc_write: true,
			});
		}
		//-->
</script>
HTML
		,
		'filter' => False,
		'dw_include' => 0,
		'dw_logged' => '',
		'other_ids' => '',
	); //END members like you block
$blocks[12] = "_multiwidget"; //below are the blocks
$blocks['_multiwidget'] = 1; //below are the blocks

$wp->beginTransaction();
$blocks_options = $wp->prepare('
UPDATE wp_options
SET option_value = ?
WHERE option_name = "widget_text"
;
');
$blocks_options->execute(Array(
		serialize( $blocks )
));
$wp->commit();
?>
