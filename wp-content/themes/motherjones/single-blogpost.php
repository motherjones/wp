<?php
/**
 * Default template for displaying all single posts, pages and attachments
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

global $mj;
$mj['meta'] = get_post_meta( get_the_ID() );
get_header();
?>

<main id="main" class="site-main group grid" role="main">
	<?php while ( have_posts() ) : the_post();

			get_template_part( 'template-parts/content-single', 'blogpost' );
			get_sidebar();
			comments_template();

		if ( ! empty( $mj['meta']['js'][0] ) ) {
			printf(
				'script>%s</script>',
				esc_js( $mj['meta']['js'][0] )
			);
		}
		the_widget(
			'mj_ad_unit_widget',
			array(
				'placement' => 'BottomROS970x250',
				'height' => 2473,
				'docwrite' => 1,
				'desktop' => 1,
			),
			array(
				'before_widget' => '',
				'after_widget' => '',
			)
		);
		the_widget(
			'mj_ad_unit_widget',
			array(
				'placement' => 'ym_869408549909503847',
				'yieldmo' => 1,
				'docwrite' => 1,
				'desktop' => 0,
			),
			array(
				'before_widget' => '',
				'after_widget' => '',
			)
		);
	endwhile; ?>
<?php get_footer(); ?>
