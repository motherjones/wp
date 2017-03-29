<?php
/**
 * The template for displaying the first post in the homepage sections list.
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

?>

<li class="article-item">
	<?php
	$post_thumbnail_id = get_post_thumbnail_id( $post->ID );
	if ( $post_thumbnail_id ) {
		echo '<div class="article-image hidden-sm hidden-xs hidden-xxs"><a href="' . esc_url( get_permalink() ) . '">';
		the_post_thumbnail( 'thumbnail' );
		echo '</a></div>';
	}
	?>
	<div class="article-data">
		<h3 class="hed">
			<a href="<?php print esc_url( get_permalink() ); ?>">
				<?php
				if ( $hed = get_post_meta( get_the_ID(), 'mj_promo_hed', true ) ) {
					echo esc_html( $hed );
				} else {
					the_title();
				}
				?>
			</a>
		</h3>
		<p class="byline">
			<?php print mj_byline( get_the_ID() ); ?>
		</p>
	</div>
</li>
