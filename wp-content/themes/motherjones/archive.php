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

    <div class="main-index"> 
		<?php if ( have_posts() ) : ?>

			<header class="page-header">
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
			</header><!-- .page-header -->

      <ul>
      <?php 
        $posts = $wp_query->get_posts;
        print_r($posts);
        echo '<h1>posts should be above';

          // if not the first page, set up the curated posts
        if ($wp_query->get_query_var('offset') > 1) {
          echo 'are we in here?';
          //get the curated posts (but only 4)
          $curated = z_get_zone_query(
            $wp_query->get_queried_object->slug,
            array(
              'posts_per_page' => 4,
            )
          );
          echo 'curated got, is below';
          print_r($curated);
          //remove the curated posts from the posts we'd show otherwise
          for ($i = 0; $i < count($posts); $i++) {
            if (in_array(
              $posts[$i],
              array_map(function($p) { return $p->ID; }, $curated)
            )) {
                unset($posts[$i]);
            }
          }
          echo 'past array';
          //merge curated posts w/ posts we'd show otherwise
          $posts = $curated + $posts;
          print_r($posts);
          //cut down the number of posts to the number we want per page
          if ($wp_query->get_query_var('posts_per_page') > 0) { 
            echo 'cutting down page';
            array_splice(
              $posts, 
              $wp_query->get_query_var('posts_per_page'),
              count($posts) - $wp_query->get_query_var('posts_per_page')
            );
          }
        }
        // end curation mess
         
			// Start the Loop.
      // Manually.
      //Don't know exactly what this does
      $wp_query->in_the_loop = true;
      //Don't know exactly what this does
      do_action_ref_array( 'loop_start', array( &$wp_query ) );
      print_r($posts);
			foreach ( $posts as $post ) :
        $wp_query->setup_postdata( $post );

				/*
				 * Include the Post-Format-specific template for the content.
				 * If you want to override this in a child theme, then include a file
				 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
				 */
				get_template_part( 'template-parts/standard-article-li' );

			// End the loop.
			endforeach;
      //Don't know exactly what this does
      $wp_query->in_the_loop = false;
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
