<?php
/**
 * The template for displaying the photoessay page
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * If you'd like to further customize these archive views, you may create a
 * new template file for each one. For example, tag.php (Tag archives),
 * category.php (Category archives), author.php (Author archives), etc.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

get_header(); ?>

<div id="content" class="site-content">
	<div id="primary" class="content-area">
		<main id="main" class="site-main group" role="main">
			<div class="page-header">
				<h1 class="page-title promo">Photos</h1>
			</div><!-- .page-header -->
			<?php if ( have_posts() ) : ?>
				<ul class="articles-list">
				<?php
					$posts_shown = 0;
					while ( $wp_query->have_posts() ) : $wp_query->the_post();
						if ( 0 === $posts_shown ) {
							get_template_part( 'template-parts/photoessay-top-article' );
							print '<div class="main-index">';
						} else {
							get_template_part( 'template-parts/standard-article-li' );
						}

						if ( 5 === $posts_shown ) { ?>
							<script>
								ad_code({
									yieldmo: true,
								 docwrite: true,
									desktop: false,
									placement: 'ym_869408394552483686',
								});
							</script>
						<?php }

						$posts_shown++;

					// End the loop.
					endwhile;
					?>
			</ul>
		<?php

			// Previous/next page navigation.
			the_posts_pagination( array(
				'prev_text'          => __( 'Previous page', 'mj' ),
				'next_text'          => __( 'Next page', 'mj' ),
				'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'mj' ) . ' </span>',
			) );

		// If no content, include the "No posts found" template.
		else :
			get_template_part( 'template-parts/content', 'none' );

		endif;
		?>
		</div>

		<div id="sidebar-right">
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
			<?php dynamic_sidebar( 'sidebar-section' ); ?>
		</div>

		</main><!-- .site-main -->
	</div><!-- .content-area -->
	<script>
		ad_code({
			yieldmo: true,
		 docwrite: true,
			desktop: false,
			placement: 'ym_869408549909503847',
		});
	</script>
<?php get_footer(); ?>
