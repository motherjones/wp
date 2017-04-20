<?php
/**
 * The template for displaying featured images on single posts
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

global $mj;
?>

<div class="<?php echo esc_attr( $classes ); ?>">
	<?php
	echo get_the_post_thumbnail( $the_post->ID, 'large' );

	if ( ! empty( $thumb_meta ) ) {
		if ( ! empty( $thumb_meta['caption'] ) || ! empty( $thumb_meta['credit'] ) ) {
			echo '<p class="wp-caption-text">';
			if ( ! empty( $thumb_meta['caption'] ) ) {
				printf(
					'<span class="media-caption">%s</span>',
					wp_kses( $thumb_meta['caption'], $mj['allowed_tags'] )
				);
			}
			if ( ! empty( $thumb_meta['credit'] || ! empty( $thumb_meta['organization'] ) ) ) {
				echo '<span class="media-credit">';
				if ( ! empty( $thumb_meta['credit_url'] ) ) {
					printf(
						'<a href="%s">',
						esc_url( $thumb_meta['credit_url'] )
					);
				}
				if ( ! empty( $thumb_meta['credit'] ) ) {
					echo esc_html( $thumb_meta['credit'] );
				}
				if ( ! empty( $thumb_meta['credit'] ) && ! empty( $thumb_meta['organization'] ) ) {
					echo '/' . esc_html( $thumb_meta['organization'] );
				} elseif ( ! empty( $thumb_meta['organization'] ) ) {
					echo esc_html( $thumb_meta['organization'] );
				}
				if ( ! empty( $thumb_meta['credit_url'] ) ) {
					echo '</a>';
				}
				echo '</span>';
			}
		}
		if ( ! empty( $thumb_meta['caption'] ) || ! empty( $thumb_meta['credit'] ) ) {
			echo '</p>';
		}
	}
	?>
</div>
