<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

global $mj;
?>

		</main>
	</div><!-- #page -->

	<footer id="colophon" class="site-footer" role="contentinfo">

			<a href="/" id="footer-logo" class=>
				<img src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/img/MJ_comp_grey.png" alt="Mother Jones" />
			</a>
			<ul id="footer-social">
					<li class="circled-icon toolbar-btn fblike">
							<a href="https://www.facebook.com/motherjones">
									<i class="fa fa-facebook fw"></i>
							</a>
					</li>
					<li class="circled-icon toolbar-btn tweet">
							<a href="https://twitter.com/motherjones">
									<i class="fa fa-twitter fw"></i>
							</a>
					</li>
					<li class="circled-icon toolbar-btn newsletter">
							<a href="/about/interact-engage/free-email-newsletter" class="hover-newsletter">
								<i class="fa fa-envelope fw"></i>
							</a>
					</li>
			</ul>
			<?php
			wp_nav_menu( array(
				'theme_location' => 'footer-list',
				'container_class' => 'footer-list-container grid',
				'items_wrap' => '<ul id="footer-list" class="grid__col-md-2 grid__col-sm-12">%3$s</ul>',
			) );
			?>
			<div id="copyright">
					<p>
						Copyright &copy;2016 Mother Jones and the Foundation for National Progress. All Rights Reserved.
					</p>
					<?php
					wp_nav_menu( array(
						'theme_location' => 'copyright',
						'container_class' => 'copyright-menu-container grid',
						'items_wrap' => '<ul id="copyright-menu" class="grid__col-md-4 grid__col-sm-12">%3$s</ul>',
					) );
					?>
			</div>
			<?php
			if ( ! isset( $mj['meta']['mj_hide_ads'] ) ) {
				if ( ! mj_is_content_type( 'full_width_article', get_the_ID() ) ) {
					the_widget(
						'mj_ad_unit_widget',
						array(
							'placement' => 'InContentAdUnit',
							'height' => 16,
							'docwrite' => 1,
							'desktop' => 1,
						),
						array(
							'before_widget' => '',
							'after_widget' => '',
						)
					);
				}

				the_widget(
					'mj_ad_unit_widget',
					array(
						'placement' => 'overlay',
						'height' => 67,
						'docwrite' => 1,
						'desktop' => 1,
					),
					array(
						'before_widget' => '',
						'after_widget' => '',
					)
				);
			}
			?>

			<div id="bottom-donate" style="display:none">
				<p>
					We noticed you have an ad blocker on.
					Support nonprofit investigative reporting by pitching in a few
					bucks.
					<a href="https://secure.motherjones.com/fnp/?action=SUBSCRIPTION&list_source=7HEGZS1A&extra_don=1&abver=A">DONATE</a>
					<span onclick="jQuery('#bottom-donate').remove();">X</span>
				</p>
			</div>

			<?php dynamic_sidebar( 'page-end' ); ?>
	</footer><!-- .site-footer -->
	<?php wp_footer(); ?>
</body>
</html>
