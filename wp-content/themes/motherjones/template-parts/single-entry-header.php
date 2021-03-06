<?php
/**
 * The header section of articles and blog posts.
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

global $mj;

$header_classes = 'entry-header grid__col-12';
if ( mj_is_content_type( 'blogpost', get_the_ID() ) ) {
	$header_classes .= ' grid__col--bleed';
}
?>
<header class="<?php echo esc_attr( $header_classes ); ?>">
	<?php
		the_title( '<h1 class="entry-title">', '</h1>' );
	if ( ! empty( $mj['meta']['mj_dek'][0] ) && ! mj_is_content_type( 'blogpost', $post->ID ) ) {
		printf(
			'<h3 class="dek">%s</h3>',
			esc_html( $mj['meta']['mj_dek'][0] )
		);
	}
	?>
	<p class="byline-dateline">
		<?php
		echo '<span class="byline">' . wp_kses( mj_byline( $post->ID ), $mj['allowed_tags'] ) . '</span>';
		echo '<span class="dateline">' . wp_kses( mj_dateline( $post->ID ), $mj['allowed_tags'] ) . '</span>';
		?>
	</p>
	<?php
		mj_share_tools( 'top' );
		mj_post_metadata( get_the_ID() );
	?>
</header><!-- .entry-header -->
