<?php
/**
 * The template part for displaying content
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

?>
<li id="post-<?php echo get_the_ID(); ?>" <?php post_class( 'article-item group' ); ?>>
	<div class="article-image">
			<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'index_thumb' ); ?></a>
	</div>
	<div class="article-data">
		<h3 class="hed">
			<a href="<?php print the_permalink(); ?>">
				<?php
					if ( $hed = get_post_meta( get_the_ID(), 'mj_promo_hed', true ) ) {
						echo $hed;
					} else {
						the_title();
					}
				?>
			</a>
		</h3>
		<p class="byline">
			<?php
				echo mj_byline( get_the_ID() );
				edit_post_link( 'edit this post', '| <span class="edit-link">', '</span>' );
			?>
		</p>
	</div>
</li>
