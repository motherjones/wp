<?php
/**
 * The template for displaying featured videos or embeds
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

global $mj;
?>

<div class="<?php echo esc_attr( $classes ); ?>">
	<div class="embed-container">
		<?php echo $featured_media['embed']; ?>
	</div>
	<?php
	if ( ! empty( $featured_media['caption'] ) || ! empty( $featured_media['credit'] ) ) {
		echo '<p class="wp-caption-text">';
		if ( ! empty( $featured_media['caption'] ) ) {
			printf(
				'<span class="media-caption">%s</span>',
				wp_kses( $featured_media['caption'], $mj['allowed_tags'] )
			);
		}
		if ( ! empty( $featured_media['credit'] ) ) {
			printf(
				'<span class="media-credit">%s</span>',
				esc_html( $featured_media['credit'] )
			);
		}
		echo '</p>';
	}
	?>
</div>
