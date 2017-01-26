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
      <header class="entry-header article">
        <?php the_title( '<h1 class="article hed">', '</h1>' ); ?>
        <?php if ( get_post_meta( get_the_ID(), 'dek' ) ): ?>
          <h3 class="dek">
            <?php print get_post_meta( get_the_ID(), 'dek' )[0]; ?>
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
      </header><!-- .entry-header -->
      <article class="article">
        <style>
          <?php print get_post_meta( get_the_ID(), 'css' )[0]; ?>
        </style>
        
        <?php get_template_part( 'template-parts/master-image-630' ); ?>

        <?php print get_post_meta( get_the_ID(), 'body' )[0]; ?>
        <script>
          <?php print get_post_meta( get_the_ID(), 'js' )[0]; ?>
        </script>

        <footer class="entry-footer">

          <?php dynamic_sidebar( 'content-end' ); ?>

          <?php get_template_part( 'template-parts/end-article-sharing' ); ?>

          <?php get_template_part( 'template-parts/end-article-bio' ); ?>

          <?php get_template_part( 'template-parts/members-like-you' ); ?>

          <?php get_template_part( 'template-parts/related-articles' ); ?>


        </footer><!-- .entry-footer -->
      </article><!-- #post-## -->

      <div id="sidebar-right">
        <?php dynamic_sidebar( 'sidebar' ); ?>
        <script language="javascript"> 
            <!-- 
            if (typeof MJ_HideRightColAds === 'undefined') {
              ad_code({
                desktop: true,
                placement: 'RightTopROS300x600',
                height: 529,
                doc_write: true,
              });
            }
            //--> 
        </script>
      </div>

    </main><!-- .site-main -->
    <?php print get_discus_thread( get_the_ID() ); ?>

    <script language="javascript"> 
				<!-- 
				if (typeof MJ_HideBottomROS970x250 === 'undefined') {
          ad_code({
            placement: 'BottomROS970x250',
               height: 2473,
            doc_write: true,
          });
				}
				if (typeof MJ_HideBottomMobile === 'undefined') {
          ad_code({
            placement: 'ym_869408549909503847',
              yieldmo: true,
            doc_write: true,
          });
				}
				//--> 
		</script>

</div><!-- .content-area -->


<?php get_footer();
endwhile; ?>
