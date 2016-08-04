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
 */

get_header(); ?>
<div id="content" class="site-content">

	<div id="primary" class="content-area">
    <main id="main" class="site-main group" role="main">

    <div class="page-header">
      <?php
        global $wp_query;
        $term = $wp_query->get_queried_object();
        if ($term->name === "Kevin Drum") {
          print '<img src="/wp-content/themes/motherjones/img/KEVIN.png"></img>';
        } else {
          print '<h1 class="page-title promo">';
          print $term->name;
          print '</h1>';
        }
      ?>
    </div><!-- .page-header -->

    <div class="main-index"> 
		<?php if ( have_posts() ) : ?>


      <?php 
         
			// Start the Loop.
			while ( $wp_query->have_posts() ) : $wp_query->the_post();
        get_template_part( 'template-parts/index-blogpost' );

			// End the loop.
			endwhile;
    ?>
    <?php

			// Previous/next page navigation.
			the_posts_pagination( array(
				'prev_text'          => __( 'Previous page', 'twentysixteen' ),
				'next_text'          => __( 'Next page', 'twentysixteen' ),
				'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'twentysixteen' ) . ' </span>',
			) );

		// If no content, include the "No posts found" template.
		else :
			get_template_part( 'template-parts/content', 'none' );

		endif;
		?>
    </div>

    <div id="sidebar-right">
      <?php dynamic_sidebar( 'sidebar-section' ); ?>
    </div>

		</main><!-- .site-main -->
	</div><!-- .content-area -->
<?php get_footer(); ?>

