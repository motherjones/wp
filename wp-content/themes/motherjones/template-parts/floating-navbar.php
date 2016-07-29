<!-- begin following navbar -->
<div id="navbar">
	<ul>
		<li class="logo">
			<a href="/">
				<img src="/wp-content/themes/motherjones/img/MJ_comp.png" 
				alt="MotherJones" />
			</a>
		</li>
		<?php if($wp_query->found_posts === 1): //is an articlish thing ?>
      <li class="nav-title">
        <?php print $title; ?>
      </li>
      <li class="share-button facebook">
        <?php print mj_flat_facebook_button(get_defined_vars() );?>
      </li>
      <li class="share-button twitter">
        <?php print mj_flat_twitter_button(get_defined_vars() );?>
      </li>
		<?php endif; ?>
		<li class="menu-button">
			<a onclick="expandMenu();">
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Your_Icon" x="0px" y="0px" width="25px" height="25px" viewBox="0 0 125 125" enable-background="new 0 0 125 125" xml:space="preserve">
					<path d="M0,100.869"></path>
					<rect y="30" width="125" height="15"></rect>
					<rect y="70" width="125" height="15"></rect>
					<rect y="110" width="125" height="15"></rect>
				</svg>
			</a>
		</li>
		<li class="donate-link article-page">
			<a href="https://secure.motherjones.com/fnp/?action=SUBSCRIPTION&list_source=7HEGP002&extra_don=1&abver=A"
				target="_blank"
			>
				Donate
			</a>
		</li>
		<li class="subscribe-link article-page">
			<a href="https://secure.motherjones.com/fnx/?action=SUBSCRIPTION&pub_code=MJM&term_pub=MJM&list_source=SEGYN1&base_country=US"
				target="_blank"
			>
				Subscribe
			</a>
		</li>
	</ul>
	<div id="mj_menu_select" class="">
			<ul id="mj_menu_options">
					<li id="menu_search_select"><a href="/search/apachesolr_search">Search</a></li>
					<li id="menu_newsletter"><a href="/about/interact-engage/free-email-newsletter">Newsletter</a></li>
					<li id="menu_magazine"><a href="/magazine">Magazine</a></li>
					<li id="menu_politics_select"><a href="/politics">Politics</a></li>
					<li id="menu_environment_select"><a href="/environment">Environment</a></li>
					<li id="menu_culture_select"><a href="/media">Media</a></li>
					<li id="menu_justice_select"><a href="/topics/crime-and-justice">Crime and Justice</a></li>
					<li id="menu_food_select"><a href="/topics/food">Food</a></li>
					<li id="menu_guns_select"><a href="/topics/guns">Guns</a></li>
					<li id="menu_dark_money_select"><a href="/topics/dark-money">Dark Money</a></li>
					<li id="menu_photo_select"><a href="/photoessays">Photos</a></li>
					<li id="menu_investigations_select"><a href="/topics/investigations">Investigations</a></li>
					<li id="menu_podcast_select"><a href="/podcasts">Podcasts</a></li>
					<li id="menu_drum_select"><a href="/kevin-drum">Kevin Drum</a></li>
					<li id="menu_about_select"><a href="/about">About</a></li>
					<li id="menu_subscribe_select"><a target="_blank" href="https://secure.motherjones.com/fnx/?action=SUBSCRIPTION&pub_code=MJM&term_pub=MJM&list_source=SEGYN1A&base_country=US">Subscribe</a></li>
					<li id="menu_donate_select"><a target="_blank" href="https://secure.motherjones.com/fnp/?action=SUBSCRIPTION&list_source=7HEGP003&extra_don=1&abver=A">Donate</a></li>
			</ul>
	</div>
</div>
<!-- end following navbar -->
