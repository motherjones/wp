<div class="<?php echo $classes; ?>">
	<div class="embed-container">
		<?php echo $featured_media['embed']; ?>
	</div>
	<div class="embed-details wp-caption">
	<?php
		if ( ! empty( $featured_media['credit'] ) ) {
			echo '<p class="wp-media-credit featured-credit">' . $featured_media['credit'] . '</p>';
		}
		if ( ! empty( $featured_media['caption'] ) ) {
			echo '<p class="wp-caption-text featured-caption">' . $featured_media['caption'] . '</p>';
		}
	?>
	</div>
</div>
