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
        print_r ($author);
     ?>

      <div class="author-image">
        <?php
          print wp_get_attachment_image( get_post_field('image')[0], 
            array('80', '80')
          );
        ?>
      </div>
      <div class="author-data">
        <p class="author-bio byline">
          <?php print get_the_title( get_the_ID() ); ?>
          <span class="author-position">
            <?php print get_post_field('position'); ?>
          </span>
        </p>
        <p class="author-bio-text">
          <?php print get_post_field('long_bio')[0] ?>
        </p>
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
