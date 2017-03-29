<?php
/**
 * Default template for displaying all single posts, pages and attachments
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

global $meta;
$meta = get_post_meta( get_the_ID() );
get_header();
?>

<main id="content" class="site-main group grid" role="main">
	<?php while ( have_posts() ) : the_post();

			$template_part = ( is_page() ) ? 'page' : 'single';
			get_template_part( 'template-parts/content', $template_part );

			if ( ! is_page() ) {
				get_sidebar();
				comments_template();
			}

			if ( ! empty( $meta['js'][0] ) ) {
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
<?php get_footer(); ?>
