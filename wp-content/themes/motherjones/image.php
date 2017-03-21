<?php
/**
 * The template for displaying image attachments
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

get_header(); ?>
<div id="content" class="site-content">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php
				// Start the loop.
				while ( have_posts() ) : the_post();
			?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

					<?php get_template_part( 'template-parts/single', 'entry-header' ); ?>

					<section class="entry-content">

						<div class="entry-attachment">
							<?php echo wp_get_attachment_image( get_the_ID(), 'large' ); ?>

							<?php
								$thumb_content = get_post( get_the_ID() );
								$thumb_custom = get_post_custom( get_the_ID() );

								$thumb_meta = array(
									'caption' => ( ! empty( $thumb_content->post_excerpt ) ) ? $thumb_content->post_excerpt : null,
									'credit' => ( ! empty( $thumb_custom['_media_credit'][0] ) ) ? $thumb_custom['_media_credit'][0] : null,
									'credit_url' => ( ! empty( $thumb_custom['_media_credit_url'][0] ) ) ? $thumb_custom['_media_credit_url'][0] : null,
									'organization' => ( ! empty( $thumb_custom['_media_credit_org'][0] ) ) ? $thumb_custom['_media_credit_org'][0] : null
								);

								if ( ! empty( $thumb_meta ) ) {
									if ( ! empty( $thumb_meta['credit'] ) ) {
										echo '<p class="wp-media-credit">';
										if ( ! empty( $thumb_meta['credit_url'] ) ) {
											echo '<a href="' . $thumb_meta['credit_url'] . '">';
										}
										echo $thumb_meta['credit'];
										if ( ! empty($thumb_meta['organization'] ) ) {
											echo '/' . $thumb_meta['organization'];
										}
										if ( ! empty( $thumb_meta['credit_url'] ) ) {
											echo '</a>';
										}
										echo '</p>';
									}
									if ( ! empty( $thumb_meta['caption'] ) ) {
										echo '<p class="wp-caption-text">' . $thumb_meta['caption'] . '</p>';
									}
								}
							?>

						</div><!-- .entry-attachment -->

					</section><!-- .entry-content -->

					<footer class="entry-footer">
						<?php
							dynamic_sidebar( 'content-end' );
							mj_share_tools( 'bottom' );
							get_template_part( 'template-parts/author-bio' );
							get_template_part( 'template-parts/members-like-you' );
							if ( mj_is_article_type( 'blogpost', $post->ID ) ) {
								get_template_part( 'template-parts/blog-pager' );
							} else {
								get_template_part( 'template-parts/related-articles' );
							}
						?>
					</footer><!-- .entry-footer -->

					<?php get_sidebar(); ?>

				</article> <!-- #post-## -->
			</main><!-- .site-main -->
			<?php
				if ( ! is_page() ) {
					print get_disqus_thread( get_the_ID() );
				}
				if ( ! empty( $meta['js'][0] ) ) {
					printf(
						'script>%s</script>',
						$meta['js'][0]
					);
				}
			?>
			<script language="javascript">
				<!--
				if (typeof MJ_HideBottomROS970x250 === 'undefined') {
					ad_code({
						desktop: true,
						placement: 'BottomROS970x250',
						height: 2473,
						doc_write: true,
					});
				}
				if (typeof MJ_HideBottomMobile === 'undefined') {
					ad_code({
						placement: 'ym_869408549909503847',
						yieldmo: true,
						docwrite: true,
						desktop: false,
					});
				}
				//-->
			</script>
		<?php endwhile; ?>
	</div><!-- .content-area -->
<?php get_footer(); ?>
