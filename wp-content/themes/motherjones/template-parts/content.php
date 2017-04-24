<?php
/**
 * The template part for displaying content
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

global $mj;
?>
<li id="post-<?php echo esc_attr( get_the_ID() ); ?>" <?php post_class( 'article-item grid' ); ?>>

	<?php // the thumbnail image, if we have one.
	$article_grid_class = 'grid__col-12';
	if ( has_post_thumbnail() ) {
		$article_grid_class = 'grid__col-8';
	?>
	<div class="article-image grid__col-4">
			<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'thumbnail' ); ?></a>
	</div>
	<?php } ?>

	<div class="article-data <?php echo esc_attr( $article_grid_class ); ?>">
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
		<p class="byline">
			<?php
			echo wp_kses( mj_byline( get_the_ID() ), $mj['allowed_tags'] );
			edit_post_link( 'edit this post', '| <span class="edit-link">', '</span>' );
			?>
		</p>
	</div>
</li>
