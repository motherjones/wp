<?php
/**
 * The template for displaying image attachments
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

get_header(); ?>

<main id="content" class="site-main grid" role="main">
	<?php
		// Start the loop.
	while ( have_posts() ) : the_post();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'item grid__col-12' ); ?>>
	<?php get_template_part( 'template-parts/single', 'entry-header' ); ?>
	<section class="entry-content">
	<div class="entry-attachment">
		<?php
			echo wp_get_attachment_image( get_the_ID(), 'large' );
			$thumb_content = get_post( get_the_ID() );
			$thumb_custom = get_post_custom( get_the_ID() );
			$thumb_meta = array(
				'caption' => ( ! empty( $thumb_content->post_excerpt ) ) ? $thumb_content->post_excerpt : null,
				'credit' => ( ! empty( $thumb_custom['_media_credit'][0] ) ) ? $thumb_custom['_media_credit'][0] : null,
				'credit_url' => ( ! empty( $thumb_custom['_media_credit_url'][0] ) ) ? $thumb_custom['_media_credit_url'][0] : null,
				'organization' => ( ! empty( $thumb_custom['_media_credit_org'][0] ) ) ? $thumb_custom['_media_credit_org'][0] : null,
			);

		if ( ! empty( $thumb_meta ) ) {
			if ( ! empty( $thumb_meta['credit'] ) ) {
				echo '<p class="wp-media-credit">';
				if ( ! empty( $thumb_meta['credit_url'] ) ) {
					echo '<a href="' . $thumb_meta['credit_url'] . '">';
				}
				echo $thumb_meta['credit'];
				if ( ! empty( $thumb_meta['organization'] ) ) {
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
			mj_share_tools( 'bottom' );
			dynamic_sidebar( 'content-end' );
			?>
		</footer><!-- .entry-footer -->
	</article> <!-- #post-## -->
</main><!-- .site-main -->
			<?php
			if ( ! is_page() ) {
				comments_template();
			}
			the_widget(
				'mj_ad_unit_widget',
				array(
					'placement' => 'BottomROS970x250',
					'height' => 2473,
					'docwrite' => 1,
					'desktop' => 1,
				),
				array(
					'before_widget' => '',
					'after_widget' => '',
				)
			);
			the_widget(
				'mj_ad_unit_widget',
				array(
					'placement' => 'ym_869408549909503847',
					'yieldmo' => 1,
					'docwrite' => 1,
					'desktop' => 0,
				),
				array(
					'before_widget' => '',
					'after_widget' => '',
				)
			);
		endwhile; ?>
	</div><!-- .content-area -->
<?php get_footer(); ?>
