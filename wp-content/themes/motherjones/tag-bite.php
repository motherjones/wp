<?php
/**
 * The template for displaying the bite podcast page
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * If you'd like to further customize these archive views, you may create a
 * new template file for each one. For example, tag.php (Tag archives),
 * category.php (Category archives), author.php (Author archives), etc.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

get_header(); ?>

<img src="<?php print get_template_directory_uri(); ?>/img/bite_banner.png" alt="Bite" />
<main id="main" class="site-main grid " role="main">
	<section class="main-index grid__col-md-8 grid__col-sm-9 grid__col-xs-12">
		<header class="page-header">
			<link href="https://d2nx6ydw3e5y5d.cloudfront.net/assets/current.css" media="screen" rel="stylesheet" type="text/css">
					<ul class="podcast-follow">
						<li class="itunes"><a href="https://itunes.apple.com/us/podcast/bite/id1090260338?mt=2"><i class="fa fa-headphones"></i>Subscribe on iTunes</a></li>
						<li class="twitter"><a href="https://twitter.com/motherjonesfood"><i class="fa fa-twitter"></i>Follow us on Twitter</a></li>
						<li class="facebook"><a href="https://www.facebook.com/motherjonesfood/"><i class="fa fa-facebook"></i>Like us on Facebook</a></li>
						<li class="rss"><a href="http://feeds.feedburner.com/bite-podcast"><i class="fa fa-rss"></i>Subscribe to RSS</a></li>
					</ul>

			<p class="podcast-intro"><em>Bite</em> is a podcast for people who think hard about their food. Join acclaimed food and farming blogger Tom Philpott, <em>Mother Jones</em> editors Kiera Butler and Maddie Oatman, and a tantalizing guest list of writers, farmers, scientists, and chefs as they uncover the surprising stories behind what ends up on your plate. We'll help you digest the food news du jour, explore the politics and science of what you eat and why&#8212;and deliver plenty of tasty tidbits along the way.<br /> You can reach us by emailing <a href="mailto:bite@motherjones.com" id="email_to">bite@motherjones.com</a></p>

			<h2 class="podcast-subhead">Latest Episode</h2>

			<div class="art19-web-player awp-medium awp-theme-dark-orange" data-series-id="781755d9-1ac1-4d5d-927e-98f95aedcd59" data-pick-from-series="latest"></div>

			<h2 class="podcast-subhead">Full Archive</h2>
			<script src="https://d2nx6ydw3e5y5d.cloudfront.net/assets/current.js" type="text/javascript"></script>

		</header><!-- .page-header -->

		<?php if ( have_posts() ) { ?>
			<ul class="articles-list">
			<?php
				$posts_shown = 0;
				// Start the Loop.
				while ( $wp_query->have_posts() ) : $wp_query->the_post();
						get_template_part( 'template-parts/content' );

					if ( 5 === $posts_shown ) { ?>
						<script>
							if (typeof MJ_HideSectionAdMobile === 'undefined') {
								ad_code({
										yieldmo: true,
										docwrite: true,
										desktop: false,
										placement: 'ym_869408394552483686',
								});
							}
						</script>
					<?php
					} elseif ( 11 === $posts_shown ) { ?>
							<script>
								if (typeof MJ_HideSectionPage970x250BB1 === 'undefined') {
									ad_code({
											yieldmo: false,
											docwrite: true,
											desktop: true,
											placement: 'SectionPage970x250BB1',
									});
								}
							</script>
					<?php
					}
					$posts_shown++;
					// End the loop.
				endwhile;
			?>
			</ul>

			<div id="pager">
				<span class="pager_previous">
					<?php previous_posts_link( 'Previous' ); ?>
				</span>
				<span class="pager_next">
					<?php next_posts_link( 'Next' ); ?>
				</span>
			</div>
			<?php
			// If no content, include the "No posts found" template.
			} else {
				get_template_part( 'template-parts/content', 'none' );
			}
			?>
	</section> <!-- .main-index -->

  <?php get_sidebar(); ?>

</main><!-- .site-main -->

<script>
	if (typeof MJ_HideBottomMobile === 'undefined') {
		ad_code({
			yieldmo: true,
			docwrite: true,
			desktop: false,
			placement: 'ym_869408549909503847',
		});
	}
</script>
<?php get_footer(); ?>
