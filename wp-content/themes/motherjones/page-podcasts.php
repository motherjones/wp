<?php
/**
 * Special page for showing off our podcasts
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

global $meta;
$meta = get_post_meta( get_the_ID() );
get_header();
?>

<main id="main" class="site-main grid" role="main">
	<section id="post-<?php the_ID(); ?>" class="grid__col-md-8 grid__col-sm-9 grid__col-xs-12">
		<header class="entry-header grid__col-12">
			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
		</header><!-- .entry-header -->

		<article class="entry-content">

			<div class="podcast-item">
				<a href="/category/secondary-tags/bite"
					 class="grid__col-sm-6 podcast-image">
					<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/img/bite-logo-large.png" />
				</a>
				<div class="podcast-data grid__col-sm-6">
					<h2 class="title">
						<a href="/topics/bite">Bite</a>
					</h2>

					<p>
						<em>Bite</em> is a podcast for people who think hard about their
						food. Join acclaimed food and farming blogger Tom Philpott,
						<em>Mother Jones</em> editors Kiera Butler and Maddie Oatman, and a
						tantalizing guest list of writers, farmers, scientists, and chefs as
						they uncover the surprising stories behind what ends up on your plate.
						We'll help you digest the food news du jour, explore the politics and
						science of what you eat and why&mdash;and deliver plenty of tasty
						tidbits along the way.
					</p>
				</div>
				<a class="itunes-link" href="https://itunes.apple.com/us/podcast/bite/id1090260338?mt=2">
					<span>
						<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/img/podcast-icon.png" />
						Subscribe on iTunes
					</span>
				</a>
			</div>

			<div class="podcast-item">
				<a href="/topics/inquiring-minds"
					 class="podcast-image grid__col-sm-6">
					<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/img/inq-minds-logo-large.png" />
				</a>
				<div class="podcast-data grid__col-sm-6">
					<h2 class="title">
						<a href="/topics/inquiring-minds">Inquiring Minds</a>
					</h2>

					<p>
						Each week the <em>Inquiring Minds</em> podcast brings you a new,
						in-depth exploration of the place where science, politics, and society
						collide. We're committed to the idea that making an effort to
						understand the world around you through science and critical thinking
						can benefit everyone&mdash;and lead to better decisions. We endeavor
						to find out what's true, what's left to discover, and why it all
						matters with weekly coverage of the latest headlines and probing
						discussions with leading scientists and thinkers.
					</p>
				</div>
				<a class="itunes-link" href="https://itunes.apple.com/us/podcast/inquiring-minds/id711675943">
					<span>
						<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/img/podcast-icon.png" />
						Subscribe on iTunes
					</span>
				</a>
			</div>

		</article><!-- .entry-content -->

		<?php edit_post_link( 'edit this. text is in version control', '| <span class="edit-link">', '</span>' ); ?>

	</section><!-- #post-## -->


	<?php
				get_sidebar();
	if ( ! empty( $meta['js'][0] ) ) {
		printf(
			'script>%s</script>',
			$meta['js'][0]
		);
	}
	?>
	<script language="javascript">
			<!--
			if (typeof MJ_HideBottomROS970x250 === 'undefined') {
				ad_code({
					desktop: true,
					placement: 'BottomROS970x250',
					height: 2473,
					doc_write: true,
				});
			}
			if (typeof MJ_HideBottomMobile === 'undefined') {
				ad_code({
					placement: 'ym_869408549909503847',
					yieldmo: true,
					docwrite: true,
					desktop: false,
				});
			}
			//-->
	</script>
<?php get_footer(); ?>
