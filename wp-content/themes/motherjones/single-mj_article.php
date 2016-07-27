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
      <article id="post-<?php the_ID(); ?>" class="article">
        <header class="entry-header">
          <?php the_title( '<h1 class="article hed">', '</h1>' ); ?>
          <h3 class="dek">
            <?php print get_post_field( 'dek', the_ID() ); ?>
          </h3>
          <p class="byline-dateline">
            <span class="byline">
              <?php print mj_byline( the_ID() ); ?>
            </span>
            <span class="byline">
              <?php print mj_dateline( the_ID() ); ?>
            </span>
          </p>
        </header><!-- .entry-header -->

        <footer class="entry-footer">
        </footer><!-- .entry-footer -->
      </article><!-- #post-## -->
    <?php endwhile; ?>

	</main><!-- .site-main -->

  <?php dynamic_sidebar( 'article-end' ); ?>

</div><!-- .content-area -->

<?php dynamic_sidebar( 'sidebar-article' ); ?>

<?php get_footer(); ?>
