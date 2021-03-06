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
global $mj;

$shown_ids = array();
?>
<main id="main" class="site-main grid" role="main">
	<section id="homepage-top" class="grid">
		<div id="homepage-top-story" class="grid__col-md-9 grid__col-sm-8 grid__col-xs-12">
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
		</div>
		<ul id="homepage-top-story-side" class="grid__col-md-3 grid__col-sm-4 grid__col-xs-12">
		<?php
		if ( $top_stories->have_posts() ) {
			for ( $i = 0; $i < 3; $i++ ) {
				$top_stories->the_post();
				$shown_ids[] = get_the_ID();
				get_template_part( 'template-parts/homepage-story' );
			}
		}
		?>
		</ul>
	</section>

	<?php
		the_widget(
			'mj_ad_unit_widget',
			array(
				'placement' => 'HomepageATF970x250',
				'height' => 2473,
				'docwrite' => 1,
				'desktop' => 1,
			),
			array(
				'before_widget' => '<section id="homepage-first-ad" class="homepage-ad grid__col-12 hidden-sm hidden-xs hidden-xxs">',
				'after_widget' => '</section>',
			)
		);
	?>

	<section id="homepage-more-top-stories-section" class="grid">
		<div id="homepage-more-top-stories-main" class="grid__col-md-8 grid__col-sm-12">
			<h2>
				<span class="promo">More Top Stories</span>
			</h2>
			<ul id="homepage-more-top-stories">
			<?php
			if ( $top_stories->have_posts() ) {
				for ( $i = 0; $i < 6; $i++ ) {
					$top_stories->the_post();
					$shown_ids[] = get_the_ID();
					get_template_part( 'template-parts/homepage-story' );
				}
			}
			?>
			</ul>
			</div>
			<div id="homepage-more-stories-sidebar" class="grid__col-4 hidden-sm hidden-xs hidden-xxs">
				<?php dynamic_sidebar( 'homepage-more-top-stories' ); ?>
			</div>
		</section>

		<section id="homepage-featured" class="homepage-fullwidth grid grid--bleed">
			<div class="homepage-featured-content grid__col-12">
				<?php
					$featured_story = z_get_zone_query(
						'homepage_featured',
						array(
							'posts_per_page' => 1,
						)
					);
					$featured_story->the_post();
					$shown_ids[] = get_the_ID();
					$mj['fullwidth_title'] = 'Featured';
					get_template_part( 'template-parts/homepage-fullwidth' );
				?>
			</div>
		</section>

		<section id="homepage-sections">
			<ul id="homepage-sections-list" class="grid">
				<?php
				$sections = array( 'Politics', 'Environment', 'Media', 'Food', 'Crime & Justice' );
				foreach ( $sections as $section ) :
					$slug = ( 'Crime & Justice' === $section ) ? 'crime-justice' : strtolower( $section );
				?>
					<li class="homepage-section grid__col-md-auto">
						<h2>
							<span class="promo"><a href="/<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $section ); ?></a></span>
						</h2>
						<ul class="homepage-section-list">
							<?php
							$cat_query = new WP_Query( array(
								'category_name' => $slug,
								'tax_query' => array(
									array(
										'taxonomy' => 'mj_content_type',
										'field' => 'slug',
										'terms' => 'blogpost',
										'operator' => 'NOT IN',
									),
								),
								'posts_per_page' => 2,
								'post__not_in' 	=> $shown_ids,
							) );
							if ( $cat_query->have_posts() ) {
								$mj['count'] = 1;
								while ( $cat_query->have_posts() ) : $cat_query->the_post();
									$shown_ids[] = get_the_ID();
									get_template_part( 'template-parts/homepage-story' );
									$mj['count']++;
								endwhile;
								unset( $mj['count'] );
							}
							?>
						</ul>
					</li>
				<?php
					endforeach;
				?>
				</ul>
			</section>

			<section id="homepage-kdrum" class="grid">
				<div id="homepage-kdrum-side" class="grid__col-md-8 grid__col-sm-12">
					<h2>
						<span class="promo"><a href="/kevin-drum">Kevin Drum</a></span>
					</h2>
					<img class="banner" src="<?php echo esc_url( get_template_directory_uri() ); ?>/img/KEVIN.png" alt="Kevin Drum" />
					<?php
						$kdrum = new WP_Query( array(
							'category_name' => 'kevin-drum',
							'posts_per_page' => 4,
							'post_status' => 'publish',
							'post__not_in' 	=> $shown_ids,
						) );
						if ( $kdrum->have_posts() ) {
							echo '<ul id="kdrum-post-list">';
							while ( $kdrum->have_posts() ) {
								$kdrum->the_post();
								$shown_ids[] = get_the_ID();
								get_template_part( 'template-parts/homepage-story' );
							}
							echo '</ul>';
						}
					?>
				</div>
				<?php
					the_widget(
						'mj_ad_unit_widget',
						array(
							'placement' => 'RightTopHP300x600',
							'height' => 529,
							'docwrite' => 1,
							'desktop' => 1,
						),
						array(
							'before_widget' => '<div id="homepage-kdrum-ad" class="grid__col-4 hidden-sm hidden-xs hidden-xxs">',
							'after_widget' => '</div>',
						)
					);
				?>
			</section>

			<section id="homepage-exposure" class="homepage-fullwidth grid grid--bleed">
				<div class="homepage-exposure-content grid__col-12">
				<?php
					$exposure_story = new WP_Query( array(
						'tag' => 'photoessays',
						'tax_query' => array(
							array(
								'taxonomy' => 'mj_content_type',
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
						$mj['fullwidth_title'] = 'Exposure';
						get_template_part( 'template-parts/homepage-fullwidth' );
					}
				?>
				</div>
			</section>

			<?php
				the_widget(
					'mj_ad_unit_widget',
					array(
						'placement' => 'HomepageBTF970x250',
						'height' => 2473,
						'docwrite' => 1,
						'desktop' => 1,
					),
					array(
						'before_widget' => '<section id="homepage-second-ad" class="homepage-ad grid__col-12 hidden-sm hidden-xs hidden-xxs">',
						'after_widget' => '</section>',
					)
				);
			?>

			<section id="homepage-investigations">
				<h2 class="promo">
					<a href="/topics/investigations">Investigations</a>
				</h2>
				<ul id="homepage-investigations-list" class="grid">
					<?php
						$investigations = new WP_Query( array(
							'tag' => 'investigations',
							'tax_query' => array(
								array(
									'taxonomy' => 'mj_content_type',
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
			</section>

<?php
the_widget(
	'mj_ad_unit_widget',
	array(
		'placement' => 'ym_869408549909503847',
		'yieldmo' => 1,
		'docwrite' => 1,
		'desktop' => 0,
	),
	array(
		'before_widget' => '',
		'after_widget' => '',
	)
);
get_footer();
