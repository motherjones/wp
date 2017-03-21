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
<?php
	$related = get_post_meta( get_the_ID(), 'mj_related_articles' );
	if ( ! empty( $related ) ) {
		$related_query = new WP_Query(array(
			'post__in' => $related['relateds'],
			'post_type' => array( 'post' ),
			'post_status' => 'publish',
			'posts_per_page' => 2,
		) );
		$heading = 'Related';
	} else { // Just show most recent posts.
		$related_query = new WP_Query(array(
			'post_type' => array( 'post' ),
			'post_status' => 'publish',
			'posts_per_page' => 2,
		) );
		$heading = 'The Latest';
	}
	if ( $related_query->have_posts() ) {
		echo '<h2 class="promo">' . esc_html( $heading ) . '</h2>';
		echo '<ul class="related-articles-list">';
		while ( $related_query->have_posts() ) : $related_query->the_post();
			get_template_part( 'template-parts/content' );
		endwhile;
		echo '</ul>';
	}
?>
</div>
