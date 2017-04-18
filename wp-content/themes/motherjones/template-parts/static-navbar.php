<?php
/**
 * The main header + navbar.
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

?>
<nav id="static-navbar" role="navigation">
	<a name="navigation" id="navigation"></a>
	<ul class="menu">
		<li class="first menu-item-home">
			<a href="/" title="Mother Jones Homepage">
				<img src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/img/MJ_comp.png" alt="Mother Jones">
			</a>
		</li>
		<?php
		wp_nav_menu( array(
			'theme_location' => 'static-navbar',
			'container' => false,
			'items_wrap' => '%3$s', // Just output li tags, no wrapper.
		) );
		?>
		<li class="menu-item-subscribe"><a href="https://secure.motherjones.com/fnx/?action=SUBSCRIPTION&amp;pub_code=MJM&amp;term_pub=MJM&amp;list_source=SEGYN&amp;base_country=US" title="Subscribe">Subscribe</a></li>
		<li class="last menu-item-donate"><a href="https://secure.motherjones.com/fnp/?action=SUBSCRIPTION&amp;list_source=7HEGP001&amp;extra_don=1&amp;abver=A" title="Donate">Donate</a></li>
	</ul>
</nav>
