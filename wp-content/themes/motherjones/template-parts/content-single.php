<?php
/**
 * The default template part for displaying single posts
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

global $meta;
$article_classes = 'hnews hentry item grid__col-md-8 grid__col-sm-12';
?>

<?php get_template_part( 'template-parts/single', 'entry-header' ); ?>

<section id="post-<?php the_ID(); ?>" <?php post_class( $article_classes ); ?> itemscope itemtype="http://schema.org/Article">

	<article class="entry-content" itemprop="articleBody">
		<?php
		if ( isset( $meta['css'][0] ) ) {
			printf(
				'<style>%s</style>',
				esc_html( $meta['css'][0] )
			);
		}
			largo_hero();
			the_content();
		?>
	</article>

	<footer class="entry-footer">
		<?php
			mj_share_tools( 'bottom' );
			dynamic_sidebar( 'content-end' );
		?>
	</footer>

</section>
