<?php
/**
 * Template partial for displaying full-width stories on the homepage.
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

global $fullwidth_title, $post;

$post_thumbnail_id = get_post_thumbnail_id( $post->ID );
if ( $post_thumbnail_id ) {
	echo '<div class="article-image"><a href="' . esc_url( get_permalink() ) . '">';
	the_post_thumbnail( 'large' );
	echo '</a></div>';
}
?>

<div class="article-data">
	<?php
	if ( ! empty( $fullwidth_title ) ) {
		echo '<h2 class="section-label"><span class="promo">' . esc_html( $fullwidth_title ) . '<span></h2>';
	}

	if ( ! empty( $post_thumbnail_id ) ) {
		$thumb_meta = get_post_custom( $post_thumbnail_id );
		if ( isset( $thumb_meta['_media_credit'][0] ) && '' !== $thumb_meta['_media_credit'][0] ) {
			echo '<p class="homepage-art-byline">' . esc_html( $thumb_meta['_media_credit'][0] ) . '</p>';
		}
	}
	?>
	<h1 class="entry-title">
		<a href="<?php the_permalink(); ?>">
		<?php
			if ( $hed = get_post_meta( get_the_ID(), 'mj_promo_hed', true ) ) {
				echo esc_html( $hed );
			} else {
				the_title();
			}
		?>
		</a>
	</h1>
	<?php
		if ( $dek = get_post_meta( get_the_ID(), 'mj_promo_dek', true ) ) {
			echo '<h4 class="dek"><a href="' . esc_url( get_permalink() ) . '">' . esc_html( $dek ) . '</a></h4>';
		} elseif ( $dek = get_post_meta( get_the_ID(), 'mj_dek', true ) ) {
			echo '<h4 class="dek"><a href="' . esc_url( get_permalink() ) . '">' . esc_html( $dek ) . '</a></h4>';
		}
	?>
	<p class="byline">
		<?php print mj_byline( get_the_ID() ); ?>
	</p>
</div>
