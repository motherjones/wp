<?php
/**
 * The template for displaying the inquiring minds podcast page
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

<img src="<?php print get_template_directory_uri(); ?>/img/inquiring_minds_banner.jpeg" alt="Inquiring Minds" />
<main id="main" class="site-main grid " role="main">
	<section class="main-index grid__col-md-8 grid__col-sm-9 grid__col-xs-12">
		<header class="page-header">
			<link href="https://d2nx6ydw3e5y5d.cloudfront.net/assets/current.css" media="screen" rel="stylesheet" type="text/css">
			<ul class="podcast-follow">
        <li class="itunes"><a href="https://itunes.apple.com/us/podcast/inquiring-minds/id711675943?mt=2"><i class="fa fa-headphones"></i>Subscribe on iTunes</a></li>
        <li class="twitter"><a href="https://twitter.com/inquiringshow"><i class="fa fa-twitter"></i>Follow us on Twitter</a></li>
        <li class="facebook"><a href="https://www.facebook.com/inquiringmindspodcast"><i class="fa fa-facebook"></i>Like us on Facebook</a></li>
        <li class="soundcloud"><a href="https://soundcloud.com/inquiringminds"><i class="fa fa-soundcloud"></i>Our SoundCloud page</a></li>
        <li class="rss"><a href="http://feeds.feedburner.com/inquiring-minds"><i class="fa fa-rss"></i>Subscribe to RSS</a></li>
        <li class="stitcher"><a href="http://www.stitcher.com/podcast/inquiring-minds"><img src="<?php print get_template_directory_uri(); ?>/img/stitcherIcon.png"></img>Subscribe on Stitcher</a></li>
			</ul>

      <p class="podcast-intro"> Each week the <em>Inquiring Minds</em> podcast brings you a new, in-depth exploration of the place where science, politics, and society collide. We're committed to the idea that making an effort to understand the world around you through science and critical thinking can benefit everyone—and lead to better decisions. We endeavor to find out what's true, what's left to discover, and why it all matters with weekly coverage of the latest headlines and probing discussions with leading scientists and thinkers.  </p>

			<h2 class="podcast-subhead">Latest Episode</h2>

      <div class="art19-web-player awp-medium awp-theme-dark-blue" data-series-id="57a44066-708b-488c-99cc-588c98834fee" data-pick-from-series="latest"></div>
			<script src="https://d2nx6ydw3e5y5d.cloudfront.net/assets/current.js" type="text/javascript"></script>

			<h2 class="podcast-subhead">Full Archive</h2>

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

	<aside id="sidebar-right" class="grid__col-4 hidden-sm hidden-xs hidden-xxs">
<?php
$widget = $wp_registered_widgets['text-9'];
$params = Array(
		array_merge(
			$wp_registered_sidebars['sidebar'],
			Array(
				'widget_id' => 'text-9',
				'widget_name' => 'Text',
			)
		),
		Array (
				'number' => 9
		)

);
call_user_func_array($widget['callback'], $params);

 ?>
<ul class="podcast-bio author-bios group">
  <li class="author-bio group vcard">
    <div class="author-image"><img src="<?php print get_template_directory_uri(); ?>/img/inq_minds_profiles/viskontas-indre60x71.jpg" alt="Indre Viskontas" /></div>
    <div class="author-data">
				<span class="byline">Indre Viskontas</span>
				<span class="author-position">Inquiring Minds co-host</span>
				<p>Indre Viskontas is a neuroscientist and artist. She's published more than 35 original research papers and book chapters on the neural basis of memory and creativity. </p>
    </div>
  </li>

  <li class="author-bio group vcard">
    <div class="author-image"><img src="<?php print get_template_directory_uri(); ?>/img/inq_minds_profiles/kishorehari60x71.jpg" alt="Kishore Hari" /></div>
    <div class="author-data">
			<span class="byline">Kishore Hari</span>
			<span class="author-position">Inquiring Minds co-host</span>
			<p>Kishore Hari is a science educator with more than a decade's experience producing live science events. He's the director of the Bay Area Science Festival based out of UC-San Francisco.</p>
    </div>
  </li>

  <li class="author-bio group vcard">
    <div class="author-image"><img src="<?php print get_template_directory_uri(); ?>/img/inq_minds_profiles/adam_headshot_2014-01.jpg" alt="Adam Isaak" /></div>
    <div class="author-data">
			<span class="byline">Adam Isaak</span>
			<span class="author-position">Inquiring Minds producer</span>
			<p>  Adam Isaak is a media producer with a decade of experience creating science-focused videos and podcasts. He produces the Inquiring Minds podcast.</p>
    </div>
  </li>
</ul>

<a href="https://itunes.apple.com/us/podcast/inquiring-minds/id711675943?mt=2">
<img src="<?php print get_template_directory_uri(); ?>/img/itunes300x109.gif" width="300px" height="109px" style="margin-top: 5px;" class="hover-opacity">
</a>

<?php
$widget = $wp_registered_widgets['text-11'];
$params = Array(
		array_merge(
			$wp_registered_sidebars['sidebar'],
			Array(
				'widget_id' => 'text-11',
				'widget_name' => 'Text',
			)
		),
		Array (
				'number' => 11
		)

);
call_user_func_array($widget['callback'], $params);

 ?>
	</aside>
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
