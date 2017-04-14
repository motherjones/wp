<?php
/**
 * The template for displaying stories in most homepage sections.
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

global $mj;
?>

<li class="article-item">
	<?php
	if ( isset( $mj['count'] ) && 1 === $mj['count'] ) {
		$post_thumbnail_id = get_post_thumbnail_id( $post->ID );
		if ( $post_thumbnail_id ) {
			echo '<div class="article-image hidden-sm hidden-xs hidden-xxs"><a href="' . esc_url( get_permalink() ) . '">';
			the_post_thumbnail( 'thumbnail' );
			echo '</a></div>';
		}
	}
	?>
	<div class="article-data">
		<h3 class="hed">
			<a href="<?php the_permalink(); ?>">
				<?php
				if ( $hed = get_post_meta( $post->ID, 'mj_promo_hed', true ) ) {
					echo esc_html( $hed );
				} else {
					the_title();
				}
				?>
			</a>
		</h3>
		<?php
		if ( ! in_category( 'kevin-drum' ) ) {
			echo '<p class="byline">' . mj_byline( $post->ID ) . '</p>';
		} else {
			echo '<p class="dateline">' . mj_dateline( $post->ID ) . '</p>';
		}
		?>
	</div>
</li>
