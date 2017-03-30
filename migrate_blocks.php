<?php
/**
 * this is for moving over our blocks programmatically
 */
$hostname="localhost";
$username="root";
$password=$argv[1];

$wp_db = "pantheon_wp";
$wp = new PDO("mysql:host=$hostname;dbname=$wp_db", $username, $password);


$widgets = Array('mj_top_stories_widget', 'mj-author-bio-widget',
								'mj-related-articles-widget', 'mj-blog-pager-widget');
$widget_options = $wp->prepare("
REPLACE INTO wp_options
(option_name, option_value)
VALUES (
CONCAT('widget_', ?),
'a:2:{i:2;a:0:{}s:12:\"_multiwidget\";i:1;}'
)
;
");
$wp->beginTransaction();
foreach ($widgets as $widget) {
	$widget_options->execute(Array($widget));
}
$wp->commit();

// be aware that the -9 here refers to the index in the array below
$sidebars_widgets_options = Array(
		'wp_inactive_widgets' => Array(),
		'sidebar' => Array('text-9', 'text-6', 'mj_top_stories_widget-2', 'text-11'),  //RHC ad, RHC membership for  not blog posts, top stories, ad
		'sidebar-blog' => Array('text-9', 'text-5', 'text-11'),  //RHC ad, RHC membership for blog posts and not blog posts, ad
		'ticker' => Array('text-7'), //Membership ticker
		'homepage-more-top-stories' => Array('text-10'), //Membership ticker
		'content-end' => Array('mj-author-bio-widget-2', 'text-8', 'mj-related-articles-widget-2', 'mj-blog-pager-widget-2'), //FIXME needs to contain author bio, text block for members like you text-8, related articles, and blog pager
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
  jQuery('head').append('<link rel="stylesheet" href="http://assets.motherjones.com/advertising/2014/05/sierra_club_sitewrap.css" type="text/css" />');
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
    <span onclick="jQuery('#bottom-donate').remove();">X</span>
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
<div class="interior-rhc-membership blog">
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
<div class="interior-rhc-membership">
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
<div class="ticker-content">
  <p>It's going to take <a href="http://www.motherjones.com/media/2016/12/reporting-on-trumps-conflicts-of-interest" target="_blank" onclick="var url = jQuery(event.target).attr('href'); ga('send', 'event', 'MembershipBar', url, document.location);">everything we've got to cover the Trump administration</a>.<br />
Help us do it with <a href="https://secure.motherjones.com/fnx/?action=SUBSCRIPTION&pub_code=DON&term_pub=DON&b_country=United+States&list_source=7H6CPOBD3&term=XX.1.50.00.DON.D.0.2246" target="_blank" onclick="var url = jQuery(event.target).attr('href'); ga('send', 'event', 'MembershipBar', url, document.location);">a tax-deductible, year-end donation today</a>.</p>
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
$blocks[9] =	Array( //BEGIN RHC ad like you block
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
	); //END RHC adblock
$blocks[10] =	Array( //BEGIN homepage promo block
		'title' => 'Homepage Promo',
		'text' => <<<'HTML'
<div class="interior-rhc-membership">
<a class="onetime donation" target="_blank" href="https://secure.motherjones.com/fnp/?action=SUBSCRIPTION&amp;list_source=7HEGPA01&amp;extra_don=1&amp;abver=A"></a>
<a class="monthly donation" target="_blank" href="https://secure.motherjones.com/fnp/?action=SUBSCRIPTION&amp;list_source=7HEGPAM1&amp;extra_don=1&amp;abver=B"></a>

<form action="https://api.maropost.com/accounts/585/forms/3289/subscribe/177d8ba3b7fe7d39e28dcba73123eeffbd01878b" method="post" id="emailForm" onsubmit="return MJ_check_email(this);">
  <button alt="SIGN UP" border="0" height="25" name="commit" id="submit" onclick="ga('send', 'event', 'TopRightFollowBox', 'Email|Click', window.location.pathname);" type="image" value="Submit">SIGN UP</button>
  <input gtbfieldid="27" include_blank="true" start_year="1950" name="contact_fields[email]" id="cons_email" placeholder="YOUR EMAIL" type="text">
  <input include_blank="true" start_year="1950" type="hidden" name="custom_fields[outreach_affiliate_code]" id="custom_fields_outreach_affiliate_code" value="Article_Membership_Box">
  <input include_blank="true" start_year="1950" type="hidden" name="custom_fields[signup_url]" id="signup_url" value="">
  <input type="hidden" value="" id="email_field" name="email_const_mp">
</form>
<a class="subscribe_cover" target="_blank" href="https://secure.motherjones.com/fnx/?action=SUBSCRIPTION&amp;pub_code=MJM&amp;term_pub=MJM&amp;list_source=SEGYN2&amp;base_country=US "></a>
<a class="subscribe" target="_blank" href="https://secure.motherjones.com/fnx/?action=SUBSCRIPTION&amp;pub_code=MJM&amp;term_pub=MJM&amp;list_source=SEGYN2&amp;base_country=US "></a>
</div>
HTML
		,
		'filter' => False,
		'dw_include' => 0,
		'dw_logged' => '',
		'other_ids' => '',
	); //END homepage promo block
$blocks[11] =	Array( //BEGIN Criteoscroller ad
		'title' => 'RHC criteoscroller ad',
		'text' => <<<'HTML'
<div class="advertise-top"></div>

<div id="criteoscroller">

<script type="text/javascript" src="http://aka-cdn-ns.adtechus.com/dt/common/DAC.js"></script>


<div id="4170840"><noscript><a href="http://adserver.adtechus.com/adlink|3.0|5443.1|4170840|0|529|ADTECH;loc=300;key=key1+key2+key3+key4;alias=" target="_blank"><img src="http://adserver.adtechus.com/adserv|3.0|5443.1|4170840|0|529|ADTECH;loc=300;key=key1+key2+key3+key4;alias=" border="0" width="300" height="600"></a></noscript></div>

</div>
<script type="text/javascript">
jQuery(window).load(function() {
  if (typeof MJ_HideRightColAds === 'undefined' &&   jQuery('#page-closure').offset().top > 2800 ) {

    ADTECH.config.page = { protocol: 'http', server: 'adserver.adtechus.com', network: '5443.1', pageid: 634599, params: { loc: '100' }};
    ADTECH.config.placements[4170840] = { sizeid: 529, params: { alias: '', target: '_blank' }};
    ADTECH.loadAd(4170840);
    // Set a function to load an ad every 55,000 miliseconds (55 seconds)
    setInterval(function(){ ADTECH.loadAd(4170840); }, 30000);

    var criteo_scrollingAd = jQuery('#criteoscroller');var criteo_adTop = criteo_scrollingAd.offset().top;
    function fixDiv() {
        var criteo_stoppingHeight = jQuery('#page-closure').offset().top;
        if (jQuery(window).scrollTop() >= (criteo_stoppingHeight - 650))
            criteo_scrollingAd.css({
                'position': 'relative',
                'top': (criteo_stoppingHeight - criteo_adTop - 650) + 'px'
            });
        else if (jQuery(window).scrollTop() >= (criteo_adTop - 50))
            criteo_scrollingAd.css({
                'position': 'fixed',
                'top': '50px'
            });
        else
            criteo_scrollingAd.css({
                'position': 'static',
                'top': 'auto'
            });
    }
    jQuery(window).scroll(fixDiv);
    fixDiv();
  }
});
</script>
HTML
		,
		'filter' => False,
		'dw_include' => 0,
		'dw_logged' => '',
		'other_ids' => '',
	); //END Criteoscroller ad
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
