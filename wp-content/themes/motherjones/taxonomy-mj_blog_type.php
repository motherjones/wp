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

	<div id="primary" class="content-area">
    <main id="main" class="site-main group" role="main">

		<?php if ( have_posts() ) : ?>

    <div class="page-header">
      <h1 class="page-title promo">
        <?php
          if (is_tax() || is_category()) {
            global $wp_query;
            $term = $wp_query->get_queried_object();
            print $term->name;
          } else {
            the_archive_title();
          }
        ?>
      </h1>
    </div><!-- .page-header -->

    <div class="main-index"> 
      <ul class="blog-posts-list">
      <?php 
         
			// Start the Loop.
			while ( $wp_query->have_posts() ) : $wp_query->the_post();
        get_template_part( 'template-parts/index-blogpost' );

			// End the loop.
			endwhile;
    ?>
    </ul>
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

