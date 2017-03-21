<?php
/**
 * The template for displaying related stories on article pages.
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

?>
<div id="related-articles" class="group">
	<h2 class="promo">Related</h2>
	<ul class="related-articles-list">
		<?php
			$related = get_post_meta( get_the_ID(), 'related_articles' );
			$related_query = new WP_Query(array(
				'post__in' => $related['relateds'],
				'post_type' => array( 'post' ),
				'post_status' => 'publish',
				'posts_per_page' => 2,
			) );
			if ( $related_query->have_posts() ) {
				while ( $related_query->have_posts() ) : $related_query->the_post();
					get_template_part( 'template-parts/content' );
				endwhile;
			}
		?>
	</ul>
</div>
