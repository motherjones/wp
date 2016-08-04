<?php
/**
 * The template for displaying all single posts and attachments
 *
 * @package WordPress
 * @subpackage Mother Jones
 */

get_header(); ?>

<header id="full-width-header" class="group">
  <div id="full-width-header-image">
    <?php print wp_get_attachment_image( 
      get_post_field( 'full_width_title_image', get_the_ID() )['title_image'],
      'full_width_title_image'
    ); ?>
  </div>
  <div id="full-width-header-data">
    <?php the_title( '<h1 class="article hed">', '</h1>' ); ?>
    <?php if ( get_post_field( 'dek', get_the_ID() ) ): ?>
      <h3 class="dek">
        <?php print get_post_field( 'dek', get_the_ID() ); ?>
      </h3>
    <?php endif; ?>
    <p class="byline-dateline">
      <span class="byline">
        <?php print mj_byline( get_the_ID() ); ?>
      </span>
      <span class="dateline">
        <?php print mj_dateline( get_the_ID() ); ?>
      </span>
    </p>
  </div>
</header>
<p class="homepage-art-byline">
  <?php print get_post_field( 'full_width_title_image', get_the_ID() )['title_image_byline']; ?>
</p>


<div id="content" class="site-content group">
  <div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
      <?php while ( have_posts() ) : the_post(); ?>
        <article class="full-width">
          <?php print get_post_field( 'css', get_the_ID() ); ?>
          <div class="social-container article top">
            <ul class="social-tools article top">
              <li class="twitter">
                <?php print mj_flat_twitter_button( get_the_ID() ); ?>
              </li>
              <li class="facebook">
                <?php print mj_flat_facebook_button( get_the_ID() ); ?>
              </li>
            </ul>
          </div>
          
          <?php print get_post_field( 'body', get_the_ID() ); ?>

          <footer class="entry-footer">

            <?php dynamic_sidebar( 'article-end' ); ?>

            <?php get_template_part( 'template-parts/end-article-sharing' ); ?>

            <?php get_template_part( 'template-parts/end-article-bio' ); ?>

            <?php get_template_part( 'template-parts/members-like-you' ); ?>

            <?php get_template_part( 'template-parts/related-articles' ); ?>

            <?php print get_discus_thread( get_the_ID() ); ?>

          </footer><!-- .entry-footer -->
        </article><!-- #post-## -->
        <?php print get_post_field( 'js', get_the_ID() ); ?>
      <?php endwhile; ?>

    </main><!-- .site-main -->


</div><!-- .content-area -->

<?php get_footer(); ?>
