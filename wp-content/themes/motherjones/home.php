<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

get_header();
global $fullwidth_title;

$shown_ids = array();
?>
<main id="main" class="site-main homepage grid" role="main">
	<div id="homepage-top" class="group grid">
		<?php
			$top_stories = z_get_zone_query(
				'top_stories',
				array(
					'posts_per_page' => 10,
				)
			);
			if ( $top_stories->have_posts() ) {
				$top_stories->the_post();
				$shown_ids[] = get_the_ID();
				get_template_part( 'template-parts/homepage-top-story' );
			}
		?>
		<ul id="homepage-top-story-side" class="grid__col-md-3 grid__col-sm-4 grid__col-xs-12">
		<?php
			if ( $top_stories->have_posts() ) {
				for ( $i = 0; $i < 3; $i++ ) {
					$top_stories->the_post();
					$shown_ids[] = get_the_ID();
					get_template_part( 'template-parts/homepage-top-story-side' );
				}
			}
		?>
		</ul>
	</div>

	<div id="homepage-first-ad" class="homepage-ad grid__col-12 hidden-sm hidden-xs hidden-xxs">
		<script language="javascript">
			<!--
			ad_code({
				desktop: true,
				placement: 'HomepageATF970x250',
				height: 2473,
				doc_write: true,
			});
			//-->
		</script>
	</div>

			<div id="homepage-more-top-stories-section" class="group">
				<div id="homepage-more-top-stories-main">
					<h2 class="promo">More Top Stories</h2>
					<ul id="homepage-more-top-stories">
						<?php
							if ( $top_stories->have_posts() ) {
								for ( $i = 0; $i < 6; $i++ ) {
									$top_stories->the_post();
									$shown_ids[] = get_the_ID();
									get_template_part( 'template-parts/homepage-top-story-side' );
								}
							}
						?>
					</ul>
				</div>
				<div id="homepage-more-stories-sidebar">
					<p>put membership image here. Possibly a sidebar thing?</p>
				</div>
			</div>

			<div id="homepage-featured" class="homepage-fullwidth group">
				<?php
					$featured_story = z_get_zone_query(
						'homepage_featured',
						array(
							'posts_per_page' => 1,
						)
					);
					$featured_story->the_post();
					$shown_ids[] = get_the_ID();
					$fullwidth_title = 'Featured';
					get_template_part( 'template-parts/homepage-fullwidth' );
				?>
			</div>

			<div id="homepage-sections" class="group">
				<ul id="homepage-sections-list">
				<?php
					$sections = array( 'Politics', 'Environment', 'Media', 'Food', 'Crime & Justice' );
					foreach ( $sections as $section ) :
						$slug = ( 'Crime & Justice' === $section ) ? 'crime-justice' : strtolower( $section );
				?>
					<li class="homepage-section">
						<h2 class="promo">
							<a href="/<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $section ); ?></a>
						</h2>
						<ul class="homepage-section-list">
							<?php
								$cat_query = new WP_Query( array(
									'category_name' => $slug,
									'tax_query' => array(
										array(
											'taxonomy' => 'mj_article_type',
											'field' => 'slug',
											'terms' => 'blogpost',
											'operator' => 'NOT IN',
										),
									),
									'posts_per_page' => 2,
									'post__not_in' 	=> $shown_ids,
								) );
								if ( $cat_query->have_posts() ) {
									$count = 1;
									while ( $cat_query->have_posts() ) : $cat_query->the_post();
										$shown_ids[] = get_the_ID();
										if ( $count === 1 ) {
											get_template_part( 'template-parts/homepage-section-first' );
											$count++;
										} else {
											get_template_part( 'template-parts/homepage-section' );
										}
									endwhile;
								}
							?>
						</ul>
					</li>
				<?php
					endforeach;
				?>
				</ul>
			</div>

			<div id="homepage-kdrum" class="group">
				<div id="homepage-kdrum-side">
					<h2 class="promo">
						<a href="/blog/kevin-drum">Kevin Drum</a>
					</h2>
					<img src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/img/KEVIN.png"></img>
					<ul id="kdrum-post-list">
						<?php
							$kdrum = new WP_Query( array(
								'tax_query' => array(
									array(
										'taxonomy' => 'blog',
										'field' => 'slug',
										'terms' => 'kevin-drum',
									),
									array(
										'taxonomy' => 'mj_article_type',
										'field' => 'slug',
										'terms' => 'blogpost',
									),
								),
								'posts_per_page' => 4,
								'post_status' => 'publish',
								'post__not_in' 	=> $shown_ids,
							) );
							while ( $kdrum->have_posts() ) {
								$kdrum->the_post();
								$shown_ids[] = get_the_ID();
								get_template_part( 'template-parts/homepage-kdrum-story' );
							}
						?>
				</div>
				<div id="homepage-kdrum-ad">
					<script>
						ad_code({
							desktop: true,
							placement: 'RightTopHP300x600',
							height: 529,
							doc_write: true,
						});
					</script>
				</div>
			</div>

			<div id="homepage-exposure" class="homepage-fullwidth group">
				<?php
					$exposure_story = new WP_Query( array(
						'tag' => 'photoessays',
						'tax_query' => array(
							array(
								'taxonomy' => 'mj_article_type',
								'field' => 'slug',
								'terms' => 'blogpost',
								'operator' => 'NOT IN',
							),
						),
						'posts_per_page' => 1,
						'post_status' => 'publish',
					) );
					if ( $exposure_story->have_posts() ) {
						$exposure_story->the_post();
						$shown_ids[] = get_the_ID();
						$fullwidth_title = 'Exposure';
						get_template_part( 'template-parts/homepage-fullwidth' );
					}
				?>
			</div>

			<div id="homepage-second-ad" class="group">
					<script language="javascript">
						<!--
						ad_code({
							desktop: true,
							placement: 'HomepageBTF970x250',
							height: 2473,
							doc_write: true,
						});
						//-->
					//</script>
			</div>

			<div id="homepage-investigations" class="group">
				<h2 class="promo">
					<a href="/topics/investigations">Investigations</a>
				</h2>
				<ul id="homepage-investigations-list" class="group">
					<?php
						$investigations = new WP_Query( array(
							'tag' => 'investigations',
							'tax_query' => array(
								array(
									'taxonomy' => 'mj_article_type',
									'field' => 'slug',
									'terms' => 'blogpost',
									'operator' => 'NOT IN',
								),
							),
							'posts_per_page' => 4,
							'post_status' => 'publish',
							'post__not_in' 	=> $shown_ids,
						) );
						while ( $investigations->have_posts() ) {
							$investigations->the_post();
							$shown_ids[] = get_the_ID();
							get_template_part( 'template-parts/homepage-investigations' );
						}
					?>
				</ul>
			</div>
		</main><!-- .site-main -->

	<script>
		ad_code({
				yieldmo: true,
				docwrite: true,
				desktop: false,
			placement: 'ym_869408549909503847',
		});
	</script>
<?php get_footer(); ?>
