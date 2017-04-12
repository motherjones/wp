<?php
/**
 * The template for displaying full-width articles
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

global $mj;
$mj['meta'] = get_post_meta( get_the_ID() );
get_header();

while ( have_posts() ) : the_post();
?>
<header id="full-width-header" class="group">
	<?php
	$has_image = mfi_reloaded_has_image( 'mj_title_image' );
	if ( $has_image ) {
		echo '<div id="full-width-header-data">';
	}

	the_title( '<h1 class="entry-title">', '</h1>' );
	if ( ! empty( $mj['meta']['mj_dek'][0] ) ) {
		printf(
			'<h3 class="dek">%s</h3>',
			esc_html( $mj['meta']['mj_dek'][0] )
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
	<?php
	if ( $has_image ) { ?>
		</div>
		<div id="full-width-header-image">
			<?php mfi_reloaded_the_image( 'mj_title_image', 'full_width_giant' ); ?>
		</div>
	<?php
	}
	?>
</header>

<?php
// Title image credit.
$title_img_id = mfi_reloaded_get_image_id( 'mj_title_image', get_the_ID() );
$title_img_meta = get_post_custom( $title_img_id );
if ( isset( $title_img_meta['_media_credit'][0] ) && '' !== $title_img_meta['_media_credit'][0] ) {
	if ( isset( $title_img_meta['_media_credit_url'][0] ) && '' !== $title_img_meta['_media_credit_url'][0] ) {
		printf(
			'<p class="full-width-title-art-byline"><a href="%1$s">%2$s</a></p>',
			esc_url( $title_img_meta['_media_credit_url'][0] ),
			esc_html( $title_img_meta['_media_credit'][0] )
		);
	} else {
		printf(
			'<p class="full-width-title-art-byline">%s</p>',
			esc_html( $title_img_meta['_media_credit'][0] )
		);
	}
}
?>


<main id="main" class="site-main" role="main">
	<article class="full-width entry-content">
		<?php
		if ( isset( $mj['meta']['css'][0] ) ) {
			printf(
				'<style>%s</style>',
				esc_html( $mj['meta']['css'][0] )
			);
		}
		mj_share_tools( 'top' );
		?>

		<div id="fullwidth-body">
			<?php the_content(); ?>
		</div>

		<footer class="entry-footer">
			<?php
				mj_share_tools( 'bottom' );
				dynamic_sidebar( 'content-end' );
				comments_template();
				if ( ! isset( $mj['meta']['mj_hide_ads'] ) ) {
			?>
				<script>
					//<!--
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
			<?php } ?>
		</footer><!-- .entry-footer -->
	</article><!-- #post-## -->
	<?php
	if ( ! empty( $mj['meta']['js'][0] ) ) {
		printf(
			'script>%s</script>',
			esc_js( $mj['meta']['js'][0] )
		);
	}
endwhile;

get_footer();
