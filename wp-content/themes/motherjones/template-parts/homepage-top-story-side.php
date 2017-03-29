<?php
/**
 * The template for displaying the non-top story featured homepage stories.
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

?>
<li class="article-item">
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
</li>
