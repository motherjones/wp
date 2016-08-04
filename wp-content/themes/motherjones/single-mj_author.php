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
		<?php
		// Start the loop.
		while ( have_posts() ) : $author = the_post();
    ?>

      <div class="author-image">
        <?php
          print wp_get_attachment_image( $author['image'][0], 
            array('80', '80')
          );
        ?>
      </div>
      <div class="author-data">
        <p class="author-bio byline">
          <?php print get_the_title( $author_id ); ?>
          <span class="author-position">
            <?php print $author['position']; ?>
          </span>
        </p>
        <p class="author-bio-text">
          <?php print $author['long_bio'][0] ?>
        </p>
      </div>

    <?php
			// End of the loop.
		endwhile;
		?>
    <ul class="article-list">
      <?php
        $authors_articles = new WP_Query( array(
          'posts_per_page' => 20,
          'post_type' => array('mj_full_width', 'mj_article', 'mj_blog_post'),
          'meta_query' => array(
            'key' => 'authors',
            'value' => $author['slug'],
          ),
        ) );
        print_r ($authors_articles);
        while ( $authors_articles->have_posts() ) : $authors_articles->the_post();
          get_template_part( 'template-parts/standard-article-li');
        endwhile;
      ?>
    </ul>

	</main><!-- .site-main -->

</div><!-- .content-area -->

<?php dynamic_sidebar( 'sidebar-article' ); ?>
<?php get_footer(); ?>
