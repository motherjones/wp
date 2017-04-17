<?php
/**
 * The template for displaying the photoessay page
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

<main id="main" class="site-main grid " role="main">
	<header class="page-header grid__col-12">
		<h1 class="page-title"><span class="promo">Photos</span></h1>
		<?php
		if ( have_posts() ) {
			$posts_shown = 0;
			while ( $wp_query->have_posts() ) : $wp_query->the_post();
				if ( 0 === $posts_shown ) {
					get_template_part( 'template-parts/photoessay-top-article' );
			?>
	</header>
	<section class="main-index grid__col-md-8 grid__col-sm-9 grid__col-xs-12">
		<ul class="articles-list">
			<?php
				} else {
					get_template_part( 'template-parts/content' );
				}

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
				} elseif ( 9 === $posts_shown ) {
					the_widget(
						'mj_ad_unit_widget',
						array(
							'placement' => 'SectionPage970x250BB1',
							'yieldmo' => 0,
							'docwrite' => 1,
							'desktop' => 1,
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
		}
		?>
	</section>

	<?php get_sidebar(); ?>
</main><!-- .site-main -->

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
