<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

get_header();
?>

<main id="main" class="site-main grid" role="main">
	<section class="main-index grid__col-md-8 grid__col-sm-9 grid__col-xs-12">
		<header class="page-header">
			<h1 class="page-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'mj' ); ?></h1>
		</header><!-- .page-header -->

		<section class="page-content">
			<p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try a search?', 'mj' ); ?></p>
			<?php get_search_form(); ?>
		</section>
	</section>
	<?php get_sidebar(); ?>
</main><!-- .site-main -->

<?php get_footer(); ?>
