<?php
/**
 * The template for displaying all single posts and attachments
 *
 * @package WordPress
 * @subpackage Mother Jones
 */

while ( have_posts() ) : the_post();
get_header(); ?>

<div id="content" class="site-content">
<div id="primary" class="content-area">
	<main id="main" class="site-main group" role="main">
    <article class="blog-post">
      <div class="entry-header blog-post">
        <?php the_title( '<h1 class="blog-post hed">', '</h1>' ); ?>
        <h3 class="dek">
          <?php print get_post_meta(get_the_ID(), 'dek' )[0]; ?>
        </h3>
        <p class="byline-dateline">
          <span class="byline">
            <?php print mj_byline( get_the_ID() ); ?>
          </span>
          <span class="dateline">
            <?php print mj_dateline( get_the_ID() ); ?>
          </span>
        </p>
        <div class="social-container blog-post top">
          <ul class="social-tools blog-post top">
            <li class="twitter">
              <?php print mj_flat_twitter_button( get_the_ID() ); ?>
            </li>
            <li class="facebook">
              <?php print mj_flat_facebook_button( get_the_ID() ); ?>
            </li>
          </ul>
        </div>
      </div><!-- .entry-header -->
      <?php print get_post_meta( get_the_ID(), 'css' )[0]; ?>
      
      <?php print get_post_meta( get_the_ID(), 'body' )[0]; ?>
      <?php print get_post_meta( get_the_ID(), 'js' )[0]; ?>

      <footer class="entry-footer">

        <?php dynamic_sidebar( 'content-end' ); ?>

        <?php get_template_part( 'template-parts/end-article-sharing' ); ?>

        <?php get_template_part( 'template-parts/end-article-bio' ); ?>

        <?php get_template_part( 'template-parts/members-like-you' ); ?>

        <ul id="prev-next">
          <li class="previous">
            <?php echo previous_post_link( 
              ' <span class="label">Previous:</span> %link',
               '%title',
               TRUE,
               ' ',
               'mj_blog_type' ); ?>
          </li>
          <li class="next">
            <?php echo next_post_link( 
              ' <span class="label">Next:</span> %link',
               '%title',
               TRUE,
               ' ',
               'mj_blog_type' ); ?>
          </li>
        </ul>


      </footer><!-- .entry-footer -->
    </article><!-- #post-## -->

    <div id="sidebar-right">
      <?php dynamic_sidebar( 'sidebar' ); ?>
    </div>

	</main><!-- .site-main -->
  <?php print get_discus_thread( get_the_ID() ); ?>


</div><!-- .content-area -->


<?php get_footer();
endwhile; ?>
