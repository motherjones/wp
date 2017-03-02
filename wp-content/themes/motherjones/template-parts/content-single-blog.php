<?php
/**
 * The default template part for displaying single posts
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */
  global $meta;
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'hnews' ); ?> itemscope itemtype="http://schema.org/Article">

	<?php get_template_part( 'template-parts/single', 'entry-header' ); ?>

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
			mj_share_tools( 'bottom' );
			get_template_part( 'template-parts/end-article-bio' );
			get_template_part( 'template-parts/members-like-you' );
		?>
		<ul id="prev-next">
			<li class="previous">
				<?php echo previous_post_link(
					' <span class="label">Previous:</span> %link',
					 '%title',
					 TRUE,
					 ' ',
					 'mj_blog_type' ); ?>
			</li>
			<li class="next">
				<?php echo next_post_link(
					' <span class="label">Next:</span> %link',
					 '%title',
					 TRUE,
					 ' ',
					 'mj_blog_type' ); ?>
			</li>
		</ul>
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
