<?php
/**
 * The template for displaying all single posts and attachments
 *
 * @package WordPress
 * @subpackage Mother Jones
 */

get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main group" role="main">
    <?php while ( have_posts() ) : the_post(); ?>
      <article class="blog-post">
        <header class="entry-header blog-post">
          <?php the_title( '<h1 class="blog-post hed">', '</h1>' ); ?>
          <h3 class="dek">
            <?php print get_post_field( 'dek', get_the_ID() ); ?>
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
        </header><!-- .entry-header -->
        <?php print get_post_field( 'css', get_the_ID() ); ?>
        
        <?php print get_post_field( 'body', get_the_ID() ); ?>
        <?php print get_post_field( 'js', get_the_ID() ); ?>

        <footer class="entry-footer">

          <?php dynamic_sidebar( 'article-end' ); ?>

          <?php get_template_part( 'template-parts/end-article-sharing' ); ?>

          <?php get_template_part( 'template-parts/end-article-bio' ); ?>

          <?php get_template_part( 'template-parts/members-like-you' ); ?>

          <?php get_template_part( 'template-parts/related-articles' ); ?>


        </footer><!-- .entry-footer -->
      </article><!-- #post-## -->
    <?php endwhile; ?>

    <div id="sidebar-right">
      <?php dynamic_sidebar( 'sidebar-blog' ); ?>
    </div>

	</main><!-- .site-main -->
  <?php print get_discus_thread( get_the_ID() ); ?>


</div><!-- .content-area -->


<?php get_footer(); ?>
