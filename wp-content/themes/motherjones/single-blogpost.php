<?php
/**
 * The template for displaying blog posts
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

get_header(); ?>
<div id="content" class="site-content">
	<div id="primary" class="content-area">
		<?php
	    while ( have_posts() ) : the_post();
	      $meta = get_post_meta( get_the_ID() );
	  ?>
		<main id="main" class="site-main group" role="main">
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'hnews blog-post' ); ?> itemscope itemtype="http://schema.org/Article">
	      <header  class="entry-header">
	        <?php the_title( '<h1 class="blog-post hed">', '</h1>' ); ?>
					<?php
						if ( $meta['dek'][0] ) {
							printf(
								'<h3 class="dek">%s</h3>',
								$meta['dek'][0]
							);
						}
					?>
	        <p class="byline-dateline">
	          <span class="byline">
	            <?php print mj_byline( get_the_ID() ); ?>
	          </span>
	          <span class="dateline">
	            <?php print mj_dateline( get_the_ID() ); ?>
	          </span>
	        </p>
	        <div class="social-container top">
	          <ul class="social-tools">
	            <li class="twitter">
	              <?php print mj_flat_twitter_button( get_the_ID() ); ?>
	            </li>
	            <li class="facebook">
	              <?php print mj_flat_facebook_button( get_the_ID() ); ?>
	            </li>
	          </ul>
	        </div>
	      </header><!-- .entry-header -->
				<section class="entry-content" itemprop="articleBody">
					<?php
						if ( $meta['css'][0] ) {
							printf(
								'<style>%s</style>',
								$meta['css'][0]
							);
						}

						get_template_part( 'template-parts/master-image-630' );

						the_content();
					?>
				</section>

	      <footer class="entry-footer">
					<?php
						dynamic_sidebar( 'content-end' );
						get_template_part( 'template-parts/end-article-sharing' );
						get_template_part( 'template-parts/end-article-bio' );
						get_template_part( 'template-parts/members-like-you' );
					?>

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
					<?php
						print get_disqus_thread( get_the_ID() );
					?>
	      </footer><!-- .entry-footer -->
	    </article><!-- #post-## -->

	    <div id="sidebar-right">

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

	      <?php dynamic_sidebar( 'sidebar' ); ?>

	    </div>

		</main><!-- .site-main -->
		<?php
	    if ( $meta['js'][0] ) {
	      printf(
	        'script>%s</script>',
	        $meta['js'][0]
	      );
	    }
	  ?>
	  <script language="javascript">
	      <!--
	      if (typeof MJ_HideBottomROS970x250 === 'undefined') {
	        ad_code({
	          desktop: true,
	          placement: 'BottomROS970x250',
	          height: 2473,
	          doc_write: true,
	        });
	      }
	      if (typeof MJ_HideBottomMobile === 'undefined') {
	        ad_code({
	          placement: 'ym_869408549909503847',
	            yieldmo: true,
	           docwrite: true,
	            desktop: false,
	        });
	      }
	      //-->
	  </script>
		<?php endwhile; ?>
	</div><!-- .content-area -->
<?php get_footer(); ?>
