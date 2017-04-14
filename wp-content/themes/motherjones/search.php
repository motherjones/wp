<?php
/**
 * The template for displaying search results pages
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

get_header(); ?>

<div id="content" class="site-content">
	<div id="primary" class="content-area">
		<main id="main" class="site-main group" role="main">
			<div class="main-index">
				<?php if ( have_posts() ) { ?>
					<ul class="articles-list">
					<?php
						$posts_shown = 0;
						// Start the Loop.
					while ( $wp_query->have_posts() ) : $wp_query->the_post();

						get_template_part( 'template-parts/content' );

						if ( 5 === $posts_shown ) {
							the_widget(
								'mj_ad_unit_widget',
								array(
									'placement' => 'ym_869408394552483686',
									'yieldmo' => 1,
									'docwrite' => 1,
									'desktop' => 0,
								),
								array(
									'before_widget' => '',
									'after_widget' => '',
								)
							);
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
} // End if().
				?>
			</div>

			<div id="sidebar-right">
				<?php
				the_widget(
					'mj_ad_unit_widget',
					array(
						'placement' => 'RightTopROS300x600',
						'height' => 529,
						'docwrite' => 1,
						'desktop' => 1,
					),
					array(
						'before_widget' => '',
						'after_widget' => '',
					)
				);
				get_sidebar();
				?>
			</div>
		</main><!-- .site-main -->
	</div><!-- .content-area -->

<?php get_footer(); ?>
