<?php
/**
 * The template for displaying blogpost indexes
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
	<section class="main-index grid__col-md-8 grid__col-sm-9 grid__col-xs-12">
		<header class="page-header">
			<img src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/img/KEVIN.png"></img>
			<h1 class="page-title visuallyhidden">Kevin Drum</h1>
		</header><!-- .page-header -->

		<?php if ( have_posts() ) { ?>
		<ul class="articles-list">
			<?php
			$posts_shown = 0;
			// Start the Loop.
			while ( $wp_query->have_posts() ) : $wp_query->the_post();
				get_template_part( 'template-parts/index-blogpost' );

				$posts_shown++;
				if ( 4 === $posts_shown ) { ?>
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
			<?php } elseif ( 3 === $posts_shown ) { ?>
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
			<?php } elseif ( 6 === $posts_shown ) { ?>
					<script>
						if (typeof MJ_HideSectionPage970x250BB2 === 'undefined') {
							ad_code({
								yieldmo: false,
								docwrite: true,
								desktop: true,
								placement: 'SectionPage970x250BB2',
							});
						}
					</script>
			<?php }
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
}// End if().
		?>
	</section>

	<?php get_sidebar(); ?>
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
