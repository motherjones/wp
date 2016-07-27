<?php
/**
 * The template for displaying all single posts and attachments
 *
 * @package WordPress
 * @subpackage Mother Jones
 */

get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
    <?php while ( have_posts() ) : the_post(); ?>
      <article id="post-<?php get_the_ID(); ?>" class="article">
        <?php print get_post_field( 'css', get_the_ID() ); ?>
        <header class="entry-header">
          <?php the_title( '<h1 class="article hed">', '</h1>' ); ?>
          <h3 class="dek">
            <?php print get_post_field( 'dek', get_the_ID() ); ?>
          </h3>
          <p class="byline-dateline">
            <span class="byline">
              <?php print mj_byline( get_the_ID() ); ?>
            </span>
            <span class="byline">
              <?php print mj_dateline( get_the_ID() ); ?>
            </span>
          </p>
        </header><!-- .entry-header -->
        <ul class="social-tools article top">
          <li class="twitter">
            <?php print mj_flat_twitter_button( get_the_ID() ); ?>
          </li>
          <li class="facebook">
            <?php print mj_flat_facebook_button( get_the_ID() ); ?>
          </li>
        </ul>
        
        <?php print get_post_field( 'body', get_the_ID() ); ?>

        <footer class="entry-footer">

          <?php get_template_part( 'template-parts/end-article-bio', 'single' ); ?>

          <?php dynamic_sidebar( 'article-end' ); ?>

          <?php print get_discus_thread( get_the_ID() ); ?>

        </footer><!-- .entry-footer -->
      </article><!-- #post-## -->
      <?php print get_post_field( 'js', get_the_ID() ); ?>
    <?php endwhile; ?>

	</main><!-- .site-main -->


</div><!-- .content-area -->

<?php dynamic_sidebar( 'sidebar-article' ); ?>

<?php get_footer(); ?>
