<?php
/**
 * Exclude blog posts from category page queries
 *
 * @since 1.0
 */
function mj_exclude_blogs( $query ) {
  if ( !is_admin() && $query->is_main_query() ) {
		if ( is_category() ) {
			$tax_query = $query->get( 'tax_query' ) ?: array();
			$tax_query[] = array(
        'taxonomy' => 'mj_article_type',
        'field' => 'slug',
        'terms' => 'blogpost',
        'operator' => 'NOT IN',
			);
			$query->set( 'tax_query', $tax_query );
		}
	}
	return $query;
}
add_action( 'pre_get_posts', 'mj_exclude_blogs');
