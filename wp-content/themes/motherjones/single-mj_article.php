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
            <span class="dateline">
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

          <?php dynamic_sidebar( 'article-end' ); ?>

          put end of article sharing here

          <?php get_template_part( 'template-parts/end-article-bio', 'single' ); ?>

          <p class="members-like-you">
            <em>Mother Jones</em> is a nonprofit, and stories like this are
             made possible by readers like you. 
            <a class="donate" href="https://secure.motherjones.com/fnp/?action=SUBSCRIPTION&amp;list_source=7HEGP004&amp;extra_don=1&amp;abver=A">Donate</a>
             or <a class="subscribe" href="https://secure.motherjones.com/fnx/?action=SUBSCRIPTION&amp;pub_code=MJM&amp;term_pub=MJM&amp;list_source=SEGYN4&amp;base_country=US">subscribe</a>
             to help fund independent journalism.
          </p>

          <?php get_template_part( 'template-parts/related-articles', 'single' ); ?>

          <?php print get_discus_thread( get_the_ID() ); ?>

        </footer><!-- .entry-footer -->
      </article><!-- #post-## -->
      <?php print get_post_field( 'js', get_the_ID() ); ?>
    <?php endwhile; ?>

	</main><!-- .site-main -->


</div><!-- .content-area -->

<?php dynamic_sidebar( 'sidebar-article' ); ?>

<?php get_footer(); ?>
