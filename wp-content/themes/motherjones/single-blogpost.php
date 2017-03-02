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
				global $meta;
 	      $meta = get_post_meta( get_the_ID() );
 	  ?>
 		<main id="main" class="site-main group" role="main">
 			<?php get_template_part( 'template-parts/content-single', 'blog' ); ?>
 		</main><!-- .site-main -->
 		<?php
 	    print get_disqus_thread( get_the_ID() );
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
