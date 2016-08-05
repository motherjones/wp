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
	<main id="main" class="site-main author" role="main">
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
        <p class="author-bio-text">
          <?php print get_user_meta($author->id, 'long_bio', true); ?>
        </p>
      </div>
    </div>

    <ul class="article-list">
      <?php while ( have_posts() ) : the_post();
          get_template_part( 'template-parts/standard-article-li');
        endwhile;
      ?>
    </ul>

	</main><!-- .site-main -->

</div><!-- .content-area -->

<?php dynamic_sidebar( 'sidebar-article' ); ?>
<?php get_footer(); ?>
