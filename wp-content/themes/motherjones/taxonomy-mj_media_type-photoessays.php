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
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */

get_header(); ?>

<div id="content" class="site-content">
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

      <ul class="articles-list">
      <?php 
      $curated_length = 0;
      $is_first_post = true;
      $posts_shown = 0;
        // if it's the first page, set up the curated posts
      if (!$wp_query->get_query_var('offset')) {
        //get the curated posts (but only 4)
        $curated = z_get_zone_query(
          $wp_query->get_queried_object()->slug, array(
            'posts_per_page' => 4,
        ));
        $curated_length = $curated->post_count;
        while ( $curated->have_posts() ) : $curated->the_post();
          if ($is_first_post) {
            //do sometihng funky for first post?
            $is_first_post = false;
            get_template_part( 'template-parts/photoessay-top-article' );
            print '<div class="main-index">';
          } else {
            get_template_part( 'template-parts/standard-article-li' );
          }
          $posts_shown++;
        endwhile;
      } // end curation mess
         
			// Start the Loop.
			while ( $wp_query->have_posts() ) : $wp_query->the_post();
        //don't do it if it's in the curated bits
        if ($curated_length && in_array(
          $post->ID,
          array_map(function($p) { return $p->ID; }, $curated->posts)
        )) {
          continue;
        } elseif ($is_first_post) {
          //do sometihng funky for first post?
          $is_first_post = false;
          get_template_part( 'template-parts/photoessay-top-article' );
          print '<div class="main-index">';
        } else {
          get_template_part( 'template-parts/standard-article-li' );
        }
        $posts_shown++;

        if ($posts_shown === 5): ?>
          <script>
            ad_code({
              yieldmo: true,
              desktop: false,
              placement: 'ym_869408394552483686',
            });
          </script>
        <?php endif;

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
      <script language="javascript"> 
          <!-- 
          if (typeof MJ_HideRightColAds === 'undefined') {
            ad_code({
              desktop: true,
              placement: 'RightTopROS300x600',
              height: 529,
              doc_write: true,
            });
          }
          //--> 
      </script>
      <?php dynamic_sidebar( 'sidebar-section' ); ?>
    </div>

		</main><!-- .site-main -->
	</div><!-- .content-area -->
  <script>
    ad_code({
      yieldmo: true,
      desktop: false,
      placement: 'ym_869408549909503847',
    });
  </script>
<?php get_footer(); ?>

