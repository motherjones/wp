<?php
/**
 * The header section of articles and blog posts.
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

global $meta;

$header_classes = 'entry-header grid__col-12';
if ( mj_is_article_type( 'blogpost', get_the_ID() ) ) {
	$header_classes .= ' grid__col--bleed';
}
?>
<header class="<?php echo $header_classes; ?>">
	<?php
		the_title( '<h1 class="entry-title">', '</h1>' );
	if ( ! empty( $meta['mj_dek'][0] ) && ! mj_is_article_type( 'blogpost', $post->ID ) ) {
		printf(
			'<h3 class="dek">%s</h3>',
			esc_html( $meta['mj_dek'][0] )
		);
	}
	?>
	<p class="byline-dateline">
		<span class="byline">
			<?php print mj_byline( get_the_ID() ); ?>
		</span>
		<span class="dateline">
			<?php print mj_dateline( get_the_ID() ); ?>
		</span>
	</p>
	<?php mj_share_tools( 'top' ); ?>
</header><!-- .entry-header -->
