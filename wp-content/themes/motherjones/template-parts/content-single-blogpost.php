<?php
/**
 * The default template part for displaying single posts
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

global $mj;
$article_classes = 'hnews hentry item grid__col-md-8 grid__col-sm-12';

?>
<section id="post-<?php the_ID(); ?>" <?php post_class( $article_classes ); ?> itemscope itemtype="http://schema.org/Article">

	<?php get_template_part( 'template-parts/single', 'entry-header' ); ?>

	<article class="entry-content" itemprop="articleBody">
		<?php
		if ( isset( $mj['meta']['mj_custom_css'][0] ) ) {
			printf(
				'<style>%s</style>',
				esc_html( $mj['meta']['mj_custom_css'][0] )
			);
		}
			mj_hero();
			the_content();
		?>
	</article>

	<footer class="entry-footer">
		<?php
			do_action( 'post_end', get_post() );
			get_template_part( 'template-parts/newsletter-signup' );
			mj_share_tools( 'bottom' );
			dynamic_sidebar( 'content-end' );
		?>
	</footer>

</section>
