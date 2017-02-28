<?php
/**
 * The default template part for displaying single posts
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'hnews item article' ); ?> itemscope itemtype="http://schema.org/Article">
	<header class="entry-header">
		<?php the_title( '<h1 class="article hed">', '</h1>' ); ?>
		<?php
			if ( $meta['dek'][0] ) {
				printf(
					'<h3 class="dek">%s</h3>',
					$meta['dek'][0]
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
		<div class="social-container top">
			<ul class="social-tools">
				<li class="twitter">
					<?php print mj_flat_twitter_button( get_the_ID() ); ?>
				</li>
				<li class="facebook">
					<?php print mj_flat_facebook_button( get_the_ID() ); ?>
				</li>
			</ul>
		</div>
		<?php mj_post_metadata( $post->ID ); ?>
	</header><!-- .entry-header -->
	<section class="entry-content" itemprop="articleBody">
		<?php
			if ( $meta['css'][0] ) {
				printf(
					'<style>%s</style>',
					$meta['css'][0]
				);
			}

			get_template_part( 'template-parts/master-image-630' );

			the_content();
		?>
	</section>

	<footer class="entry-footer">
		<?php
			dynamic_sidebar( 'content-end' );
			get_template_part( 'template-parts/end-article-sharing' );
			get_template_part( 'template-parts/end-article-bio' );
			get_template_part( 'template-parts/members-like-you' );
			get_template_part( 'template-parts/related-articles' );
		?>
	</footer><!-- .entry-footer -->

	<div id="sidebar-right">
		<?php dynamic_sidebar( 'sidebar' ); ?>
		<script language="javascript">
				<!--
				if (typeof MJ_HideRightColAds === 'undefined') {
					ad_code({
						desktop: true,
						placement: 'RightTopROS300x600',
						height: 529,
						doc_write: true,
					});
				}
				//-->
		</script>
	</div>

</article><!-- #post-## -->
