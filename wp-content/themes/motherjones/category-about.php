<?php
/**
 * The template for displaying archive pages
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
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */

get_header(); ?>

<div id="content" class="site-content">
	<div id="primary" class="content-area">
    <main id="main" class="site-main group" role="main">

		<?php
    global $wp_query;
		// Start the loop.
    $page = $wp_query->get_post(64);
    print "HERE: ";
    print_r($page);
    print " : END";
		while ( $wp_query->have_posts() ) : $wp_query->the_post();

			// Include the single post content template.
			get_template_part( 'template-parts/content', 'single' );

			// End of the loop.
		endwhile;
		?>

		</main><!-- .site-main -->
	</div><!-- .content-area -->
</div><!-- site-content -->
<?php get_footer(); ?>
