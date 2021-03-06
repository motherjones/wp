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
	$has_image = false;
	if ( class_exists( 'MultiPostThumbnails' ) && MultiPostThumbnails::has_post_thumbnail( 'post', 'mj_title_image' ) ) {
		$has_image = true;
	}

	if ( $has_image && ! isset( $mj['meta']['mj_overlay_hide'] ) ) {
		echo '<div id="full-width-overlay-data">';
	} else {
		echo '<div id="full-width-overlay-data" class="visuallyhidden">';
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
		<?php
		echo '<span class="byline">' . wp_kses( mj_byline( $post->ID ), $mj['allowed_tags'] ) . '</span>';
		echo '<span class="dateline">' . wp_kses( mj_dateline( $post->ID ), $mj['allowed_tags'] ) . '</span>';
		?>
	</p>
	<?php
	if ( $has_image ) { ?>
		</div>
		<div id="full-width-header-image">
			<?php
			if ( class_exists( 'MultiPostThumbnails' ) ) {
				MultiPostThumbnails::the_post_thumbnail(
					get_post_type(),
					'mj_title_image',
					get_the_ID(),
					'full_width_giant'
				);
			}
			?>
		</div>
	<?php
	}
	mj_post_metadata( get_the_ID() );
	?>
</header>

<?php
// Title image credit.
if ( class_exists( 'MultiPostThumbnails' ) ) {
	$title_img_id = MultiPostThumbnails::get_post_thumbnail_id( 'post', 'mj_title_image', get_the_ID() );
}
if ( isset( $title_img_id ) ) {
	$title_img_meta = get_post_custom( $title_img_id );
}
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
		if ( isset( $mj['meta']['mj_custom_css'][0] ) ) {
			printf(
				'<style>%s</style>',
				esc_html( $mj['meta']['mj_custom_css'][0] )
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
				do_action( 'post_end', get_post() );
				comments_template();
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
			?>
		</footer><!-- .entry-footer -->
	</article><!-- #post-## -->
<?php
endwhile;
get_footer();
