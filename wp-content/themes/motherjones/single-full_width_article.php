<?php
/**
 * The template for displaying full-width articles
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

get_header();

while ( have_posts() ) : the_post();
	$meta = get_post_meta( get_the_ID() );
?>
<header id="full-width-header" class="group">
	<?php if ( mfi_reloaded_has_image( 'mj_title_image' ) ) { ?>
		<div id="full-width-header-image">
			<?php mfi_reloaded_the_image( 'mj_title_image', 'full_width_giant' ); ?>
		</div>
		<div id="full-width-header-data">
			<?php
				the_title( '<h1 class="article hed">', '</h1>' );
		    if ( ! empty( $meta['mj_dek'][0] ) ) {
					printf(
						'<h3 class="dek">%s</h3>',
						esc_html( $meta['mj_dek'][0] )
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
		</div>
	<?php } else { ?>
			<?php
				the_title( '<h1 class="article hed">', '</h1>' );
				if ( ! empty( $meta['mj_dek'][0] ) ) {
					printf(
						'<h3 class="dek">%s</h3>',
						esc_html( $meta['mj_dek'][0] )
					);
				}
			?>
			<p class="byline-dateline">
				<span class="byline">
					<?php print esc_html( mj_byline( get_the_ID() ) ); ?>
				</span>
				<span class="dateline">
					<?php print esc_html( mj_dateline( get_the_ID() ) ); ?>
				</span>
			</p>
	<?php } ?>
</header>

<?php
	// Title image credit.
	$title_img_id = mfi_reloaded_get_image_id( 'mj_title_image', get_the_ID() );
	$title_img_meta = get_post_custom( $title_img_id );
	if ( $title_img_meta['_media_credit'][0] && $title_img_meta['_media_credit'][0] !== '' ) {
		if ( $title_img_meta['_media_credit_url'][0] && $title_img_meta['_media_credit_url'][0] !== '' ) {
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

<div id="content" class="site-content group">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
			<article class="full-width">
				<?php
					if ( isset( $meta['css'][0] ) ) {
						printf(
							'<style>%s</style>',
							esc_html( $meta['css'][0] )
						);
					}
					mj_share_tools( 'top' );
				?>

				<div id="fullwidth-body">
					<?php the_content(); ?>
				</div>

				<footer class="entry-footer">
					<?php
						dynamic_sidebar( 'content-end' );
						mj_share_tools( 'bottom' );
						get_template_part( 'template-parts/end-article-bio' );
						get_template_part( 'template-parts/members-like-you' );
						get_template_part( 'template-parts/related-articles' );
						print esc_html( get_disqus_thread( get_the_ID() ) );
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
				</footer><!-- .entry-footer -->
			</article><!-- #post-## -->
			<?php
				if ( ! empty( $meta['js'][0] ) ) {
					printf(
						'script>%s</script>',
						esc_js( $meta['js'][0] )
					);
				}
			?>

		</main><!-- .site-main -->


</div><!-- .content-area -->

<?php get_footer();
endwhile;
?>
