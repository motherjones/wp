<?php
/**
 * The template for displaying all single posts and attachments
 *
 * @package WordPress
 * @subpackage Mother Jones
 */

get_header(); ?>
<div id="content" class="site-content">

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<?php
		// Start the loop.
		while ( have_posts() ) : the_post();

			// Include the single post content template.
			get_template_part( 'template-parts/content', 'single' );

			// End of the loop.
		endwhile;
		?>

	</main><!-- .site-main -->

  <?php dynamic_sidebar( 'content-end' ); ?>

</div><!-- .content-area -->

<div id="sidebar-right">
  <?php dynamic_sidebar( 'sidebar' ); ?>
</div>

<?php get_footer(); ?>
