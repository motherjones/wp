<?php
/**
 * The template for displaying blog posts on an archive page.
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

?>
<li class="article-item blog">
	<div class="entry-header">
		<h1 class="blog hed">
			<a href="<?php print esc_url( get_permalink() ); ?>">
				<?php the_title(); ?>
			</a>
		</h1>
		<?php
		if ( $dek = get_post_meta( get_the_ID(), 'mj_dek', true ) ) {
			printf(
				'<h3 class="dek">%s</h3>',
				esc_html( $dek )
			);
		}
		?>
		<p class="byline-dateline">
			<?php
			echo '<span class="byline">' . wp_kses( mj_byline( $post->ID ), $mj['allowed_tags'] ) . '</span>';
			echo '<span class="dateline">' . wp_kses( mj_dateline( $post->ID ), $mj['allowed_tags'] ) . '</span>';
			?>
		</p>
	</div><!-- .entry-header -->

	<?php
		mj_hero();
		the_content();
	?>

	<footer class="entry-footer">
		<?php mj_share_tools( 'blog' );?>
	</footer>

</li>
