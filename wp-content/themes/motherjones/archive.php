<?php
/**
 * The template for displaying archive pages
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
			<div class="main-index">
				<div class="page-header">
				<?php
					if ( is_tax() || is_category() ) {
						global $wp_query;
						$term = $wp_query->get_queried_object();
						echo '<h1 class="page-title promo">' . esc_html( $term->name ) . '</h1>';
					} elseif ( is_author() ) {
						the_widget( 'mj_author_bio_widget', array( 'title' => '' ) );
					} else {
						the_archive_title( '<h1 class="page-title promo">', '</h1>' );
					}
					?>
				</div><!-- .page-header -->

				<?php if ( have_posts() ) { ?>
					<ul class="articles-list">
					<?php
						$posts_shown = 0;
						// Start the Loop.
						while ( $wp_query->have_posts() ) : $wp_query->the_post();

							if ( 0 === $posts_shown ) {
								get_template_part( 'template-parts/top-index-article' );
							} else {
								get_template_part( 'template-parts/content' );
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
							<?php
							}

							$posts_shown++;

						// End the loop.
						endwhile;
					?>
				</ul>
				<div id="pager">
					<span class="pager_previous">
						<?php previous_posts_link( 'Previous' ); ?>
					</span>
					<span class="pager_next">
						<?php next_posts_link( 'Next' ); ?>
					</span>
				</div>
				<?php
				// If no content, include the "No posts found" template.
				} else {
					get_template_part( 'template-parts/content', 'none' );
				}
				?>
			</div> <!-- .main-index -->

			<div id="sidebar-right">
				<script language="javascript">
						<!--
						if ( typeof MJ_HideRightColAds === 'undefined' ) {
							ad_code({
								desktop: true,
								placement: 'RightTopROS300x600',
								height: 529,
								doc_write: true,
							});
						}
						//-->
				</script>
				<?php get_sidebar(); ?>
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
