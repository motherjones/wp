<?php
/**
 * The template for displaying 404 pages (not found)
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
				<header class="page-header">
					<h1 class="page-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'mj' ); ?></h1>
				</header><!-- .page-header -->

				<section class="page-content">
					<p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try a search?', 'mj' ); ?></p>
					<?php
						get_search_form();
						get_sidebar( 'content-bottom' );
					?>
				</section>

				<?php get_sidebar(); ?>

			</div>
		</main><!-- .site-main -->
	</div><!-- .content-area -->


<?php get_footer(); ?>
