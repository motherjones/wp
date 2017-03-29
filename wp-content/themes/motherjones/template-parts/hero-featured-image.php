<div class="<?php echo $classes; ?>">
	<?php echo get_the_post_thumbnail( $the_post->ID, 'large' ); ?>
	<?php
		if ( ! empty( $thumb_meta ) ) {
			if ( ! empty( $thumb_meta['credit'] ) ) {
				echo '<p class="wp-media-credit">';
				if ( ! empty( $thumb_meta['credit_url'] ) ) {
					echo '<a href="' . $thumb_meta['credit_url'] . '">';
				}
				echo $thumb_meta['credit'];
				if ( ! empty($thumb_meta['organization'] ) ) {
					echo '/' . $thumb_meta['organization'];
				}
				if ( ! empty( $thumb_meta['credit_url'] ) ) {
					echo '</a>';
				}
				echo '</p>';
			}
			if ( ! empty( $thumb_meta['caption'] ) ) {
				echo '<p class="wp-caption-text">' . $thumb_meta['caption'] . '</p>';
			}
		}
	?>
</div>
