<?php
/**
 * The template for displaying all single posts and attachments
 *
 * @package WordPress
 * @subpackage Mother Jones
 */

while ( have_posts() ) : the_post();
get_header(); 
print_r(get_post_type());
?>

<header id="full-width-header" class="group">
  <?php if ( get_post_meta( get_the_ID(), 'full_width_title_image' )[0]['title_image'] ): ?>
    <div id="full-width-header-image">
      <?php print wp_get_attachment_image( 
        get_post_meta( get_the_ID(), 'full_width_title_image' )[0]['title_image'],
        'full_width_giant'
      ); ?>
    </div>
    <div id="full-width-header-data">
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
    </div>
  <?php else: ?>
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
  <?php endif; ?>
</header>
<p class="homepage-art-byline">
  <?php print get_post_meta( get_the_ID(), 'full_width_title_image'  )[0]['title_image_byline']; ?>
</p>


<div id="content" class="site-content group">
  <div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
      <article class="full-width">
        <?php print get_post_meta( get_the_ID(), 'css' )[0]; ?>
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
        
        <div id="fullwidth-body">
          <?php print get_post_meta( get_the_ID(), 'body' )[0]; ?>
        </div>

        <footer class="entry-footer">

          <?php dynamic_sidebar( 'content-end' ); ?>

          <?php get_template_part( 'template-parts/end-article-sharing' ); ?>

          <?php get_template_part( 'template-parts/end-article-bio' ); ?>

          <?php get_template_part( 'template-parts/members-like-you' ); ?>

          <?php get_template_part( 'template-parts/related-articles' ); ?>

          <?php print get_discus_thread( get_the_ID() ); ?>

          <script>
            //<!--
            if (typeof MJ_HideBottomMobile === 'undefined') {
              ad_code({
                placement: 'ym_869408549909503847',
                  yieldmo: true,
                doc_write: true,
              });
            }
            //--> 
          </script>

        </footer><!-- .entry-footer -->
      </article><!-- #post-## -->
      <?php print get_post_meta( get_the_ID(), 'js' )[0]; ?>

    </main><!-- .site-main -->


</div><!-- .content-area -->

<?php get_footer(); 
endwhile;
?>
