<?php
/**
 * The template for displaying authors
 *
 * @package WordPress
 * @subpackage Mother Jones
 */

get_header(); ?>

<div id="content" class="site-content group">
<div id="primary" class="content-area">
	<main id="main" class="site-main author" role="main">
    <div class="main-index"> 
      <?php global $wp_query;
        $author_name = $wp_query->query['author_name'];
        $author_query = new WP_User_Query( array( 
          'search' => $author_name,
          'search_columns' => array('user_nicename'),
        ) );
        $author = $author_query->get_results()[0];
       ?>

      <div class="author-bio group">
        <div class="author-image">
          <?php
            print wp_get_attachment_image( $author->image[0], 
              array('100', '100')
            );
          ?>
        </div>
        <div class="author-data">
          <p class="author-bio-byline byline">
            <?php print $author->display_name; ?>
            <span class="author-position">
              <?php print get_user_meta($author->id, 'position', true); ?>
            </span>
          </p>
          <div class="author-bio-text">
            <?php print get_user_meta($author->id, 'long_bio', true); ?>
          </div>
        </div>
      </div>

      <ul class="article-list">
        <?php $author_query = new WP_Query(array(
          'author'        =>  $author->id,
          'orderby'       =>  'post_date',
          'order'         =>  'DESC',
          'post_type' => array('mj_article', 'mj_full_width', 'mj_blog_post'),
          'posts_per_page' => 20,
        ) ); 
        while ( $author_query->have_posts() ) : $author_query->the_post();
            get_template_part( 'template-parts/standard-article-li');
        endwhile;
        ?>
      </ul>
    </div>

    <div id="sidebar-right">
      <?php dynamic_sidebar( 'sidebar-section' ); ?>
    </div>

	</main><!-- .site-main -->

</div><!-- .content-area -->

<?php get_footer(); ?>
