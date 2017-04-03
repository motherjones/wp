<?php
/**
 * Floating Navbar
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

?>
<div id="navbar">
	<ul>
		<li class="logo">
			<a href="/">
				<img src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/img/MJ_comp.png"
				alt="MotherJones" />
			</a>
		</li>
		<?php if ( 1 === $wp_query->found_posts && 'mj_author' !== $wp_query->query_vars['post_type'] ) : // Is an articlish thing. ?>
			<li class="nav-title">
				<?php print esc_html( $wp_query->posts[0]->post_title ); ?>
			</li>
			<li class="share-button facebook">
				<?php print mj_flat_facebook_button( $wp_query->posts[0]->ID );?>
			</li>
			<li class="share-button twitter">
				<?php print mj_flat_twitter_button( $wp_query->posts[0]->ID );?>
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
		<li class="donate-link article-page hidden-xs hidden-xxs">

			<a href="https://secure.motherjones.com/fnp/?action=SUBSCRIPTION&list_source=7HEGP002&extra_don=1&abver=A"
				target="_blank"
			>
				Donate
			</a>
		</li>
		<li class="subscribe-link article-page hidden-sm hidden-xs hidden-xxs">
			<a href="https://secure.motherjones.com/fnx/?action=SUBSCRIPTION&pub_code=MJM&term_pub=MJM&list_source=SEGYN1&base_country=US"
				target="_blank"
			>
				Subscribe
			</a>
		</li>
	</ul>
	<?php
	wp_nav_menu( array(
		'theme_location' => 'floating-nav',
		'container_id' => 'mj_menu_select',
		'items_wrap' => '<ul id="mj_menu_options">%3$s</ul>',
	) );
	?>
</div>
<!-- end following navbar -->
