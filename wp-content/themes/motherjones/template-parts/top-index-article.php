<?php
/**
 * The template part for displaying the first item on some archive pages.
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

global $mj;
?>

<li class="top-article-item group">
	<div class="article-image">
		<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'medium_large' ); ?></a>
	</div>
	<div class="article-data">
	<h3 class="hed">
		<a href="<?php print the_permalink(); ?>">
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
