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
	'mj-floating-ad-widget', 'mj-related-articles-widget', 'mj-blog-pager-widget');
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

$rhc_ads = $wp->prepare("
REPLACE INTO wp_options
(option_name, option_value)
VALUES (
'widget_mj-ad-unit-widget',
?
)
;
");
$rhc_ad_array = Array(
	'placement' => 'RightTopROS300x600',
	'height' => 529,
	'docwrite' => '1',
	'desktop' => '1',
	'yieldmo' => '0',
);
$rhc_all_ads = Array();
$rhc_all_ads[2] = $rhc_all_ads[3] = $rhc_all_ads[4] = $rhc_all_ads[5] = $rhc_ad_array;
$rhc_all_ads['_multiwidget'] = 1; //below are the blocks
$wp->beginTransaction();
$rhc_ads->execute(Array(serialize($rhc_all_ads)));
$wp->commit();

// be aware that the -9 here refers to the index in the array below.
$sidebars_widgets_options = Array(
		'wp_inactive_widgets' => Array(),
		'sidebar' => Array('mj-ad-unit-widget-2', 'text-6', 'mj_top_stories_widget-2', 'mj-floating-ad-widget-2'),  //RHC ad, RHC membership for  not blog posts, top stories, ad
		'sidebar-blog' => Array('mj-ad-unit-widget-3', 'text-5', 'mj-floating-ad-widget-2'),  //RHC ad, RHC membership for blog posts and not blog posts, ad
		'sidebar-bite' => Array('mj-ad-unit-widget-4', 'text-15', 'mj-floating-ad-widget-2'),  //RHC ad, bite author block, ad
		'sidebar-inquiring-minds' => Array('mj-ad-unit-widget-5', 'text-13', 'mj-floating-ad-widget-2'),  //RHC ad, inq minds author block, ad
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
		'filter' => false,
	); //END Ad Control
$blocks[3] = Array(
		'title' => 'Site wrap',
		'text' => <<<'HTML'
<script>
<!--
if (typeof(MJ_HideSiteWrap) === 'undefined') {
  jQuery('head').append('<link rel="stylesheet" href="http://assets.motherjones.com/advertising/2014/05/sierra_club_sitewrap.css" type="text/css" />');
  ad_code('siteskin',1559);
}
//-->
</script>
HTML
		,
		'filter' => false, //this is the "show only on, show not on" toggle
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
		'filter' => false, //this is the "show only on, show not on" toggle
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
		'filter' => false, //this is the "show only on, show not on" toggle
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
		'filter' => false, //this is the "show only on, show not on" toggle
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
		'filter' => false, //this is the "show only on, show not on" toggle
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
		'filter' => false,
	); //END members like you block
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
		'filter' => false,
	); //END homepage promo block
$blocks[13] =	Array( //BEGIN inquiring minds block
		'title' => 'Inquiring minds authors',
		'text' => <<<'HTML'
<ul class="podcast-bio author-bios group">
  <li class="author-bio group vcard">
    <div class="author-image"><img src="/wp-content/themes/motherjones/img/inq_minds_profiles/viskontas-indre60x71.jpg" alt="Indre Viskontas" /></div>
    <div class="author-data">
				<span class="byline">Indre Viskontas</span>
				<span class="author-position">Inquiring Minds co-host</span>
				<p>Indre Viskontas is a neuroscientist and artist. She's published more than 35 original research papers and book chapters on the neural basis of memory and creativity. </p>
    </div>
  </li>

  <li class="author-bio group vcard">
    <div class="author-image"><img src="/wp-content/themes/motherjones/img/inq_minds_profiles/kishorehari60x71.jpg" alt="Kishore Hari" /></div>
    <div class="author-data">
			<span class="byline">Kishore Hari</span>
			<span class="author-position">Inquiring Minds co-host</span>
			<p>Kishore Hari is a science educator with more than a decade's experience producing live science events. He's the director of the Bay Area Science Festival based out of UC-San Francisco.</p>
    </div>
  </li>

  <li class="author-bio group vcard">
    <div class="author-image"><img src="/wp-content/themes/motherjones/img/inq_minds_profiles/adam_headshot_2014-01.jpg" alt="Adam Isaak" /></div>
    <div class="author-data">
			<span class="byline">Adam Isaak</span>
			<span class="author-position">Inquiring Minds producer</span>
			<p>  Adam Isaak is a media producer with a decade of experience creating science-focused videos and podcasts. He produces the Inquiring Minds podcast.</p>
    </div>
  </li>
</ul>

<a href="https://itunes.apple.com/us/podcast/inquiring-minds/id711675943?mt=2">
<img src="/wp-content/themes/motherjones/img/itunes300x109.gif" width="300px" height="109px" style="margin-top: 5px;" class="hover-opacity">
</a>

HTML
		,
		'filter' => false,
	); //END inquiring minds block
$blocks[15] =	Array( //BEGIN Bite podcast block
		'title' => 'Bite podcast authors',
		'text' => <<<'HTML'
<ul class="podcast-bio author-bios group">
  <li class="author-bio group vcard">
    <div class="author-image"><img src="/wp-content/themes/motherjones/img/bite_profiles/philpott80x95.jpg" alt="Tom Philpott" /></div>
    <div class="author-data">
        <span class="byline">Tom Philpott</span>
        <span class="author-position">Bite co-host</span>
        <p>Tom has been at <em>MoJo</em> since 2011. His award-winning writing on food politics has appeared in numerous publications, and he was a cofounder in 2004 of Maverick Farms in Valle Crucis, North Carolina. He is currently based in Austin, Texas.</p>
    </div>
  </li>

  <li class="author-bio group vcard">
    <div class="author-image"><img src="/wp-content/themes/motherjones/img/bite_profiles/kiera-butler80x95.jpg" alt="Kiera Butler" /></div>
    <div class="author-data">
        <span class="byline">Kiera Butler</span>
        <span class="author-position">Bite co-host</span>
        <p>
          A senior editor at <em>Mother Jones</em>, Kiera covers health, food, and the environment.
          She is the author of the 2014 book <em>Raise: What 4-H Teaches 7 Million Kids&emdash;and How Its Lessons Could Change Food and Farming Forever</em>.
        </p>
    </div>
  </li>

  <li class="author-bio group vcard">
    <div class="author-image"><img src="/wp-content/themes/motherjones/img/bite_profiles/maddie80x95.jpg" alt="Maddie Oatman" /></div>
    <div class="author-data">
        <span class="byline">Maddie Oatman</span>
        <span class="author-position">Bite co-host</span>
        <p>Maddie is a story editor at <em>Mother Jones</em>, where she writes about food, environment, and culture. She's been featured in <em>The Best American Science and Nature Writing</em>.</p>
    </div>
  </li>

  <li class="author-bio group vcard">
    <div class="author-image"><img src="/wp-content/themes/motherjones/img/bite_profiles/casey_miner80x95.jpg" alt="Casey Miner" /></div>
    <div class="author-data">
        <span class="byline">Casey Miner</span>
        <span class="author-position">Sound Editor</span>
        <p>Casey Miner is a radio producer and editor who's contributed to NPR, KALW, <em>Marketplace</em>, <em>Mother Jones</em>, and Pop-Up Magazine, among other outlets. She is host and executive producer of <em><a href="http://specialistpodcast.com/">The Specialist</a></em>, a podcast about work we don't think about and the people who do it.</p>
    </div>
  </li>

  <li class="author-bio group vcard">
    <div class="author-image"><img src="/wp-content/themes/motherjones/img/bite_profiles/seth_samuel_80x95.png" alt="Seth Samuel" /></div>
    <div class="author-data">
        <span class="byline">Seth Samuel</span>
        <span class="author-position">Composer and Interim Sound Editor</span>
        <p>Seth Samuel is a multi-award-winning composer, sound engineer, and radio producer. He lives in Atlanta, Georgia, with his wife, son, cat, and dog. When he's not working on "Bite," he scores KQED/PBS's short-form science film series "Deep Look" and mixes and scores "<a href="http://specialistpodcast.com/">The Specialist</a>," a podcast about peculiar jobs. </p>
    </div>
  </li>
</ul>
HTML
		,
		'filter' => false,
	); //END Bite podcast block
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
