<?php
/**
 * Template partial for displaying investigations on the homepage.
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

global $mj;
?>

<li class="article-item grid__col-md-6 grid__col-sm-12">
	<?php
	$post_thumbnail_id = get_post_thumbnail_id( $post->ID );
	if ( $post_thumbnail_id ) {
		echo '<div class="article-image"><a href="' . esc_url( get_permalink() ) . '">';
		the_post_thumbnail( 'medium' );
		echo '</a></div>';
	}
	?>
	<div class="article-data">
		<?php
		if ( ! empty( $post_thumbnail_id ) ) {
			$thumb_meta = get_post_custom( $post_thumbnail_id );
			if ( $thumb_meta['_media_credit'][0] && '' !== $thumb_meta['_media_credit'][0] ) {
				echo '<p class="homepage-art-byline">' . esc_html( $thumb_meta['_media_credit'][0] ) . '</p>';
			}
		}
		?>
		<h3 class="hed">
			<a href="<?php the_permalink(); ?>">
				<?php
				if ( $hed = get_post_meta( get_the_ID(), 'mj_promo_hed', true ) ) {
					echo esc_html( $hed );
				} else {
					the_title();
				}
				?>
			</a>
		</h3>
		<?php echo '<p class="byline">' . wp_kses( mj_byline( $post->ID ), $mj['allowed_tags'] ) . '</p>'; ?>
	</div>
</li>
