<?php
/**
 * Functions pertaining to archive pages
 *
 * @package    WordPress
 * @subpackage Mother_Jones
 * @since      Mother Jones 1.0
 */

/**
 * Exclude blog posts from category page queries
 *
 * @param array $query the original query to exclude blog posts from.
 */
function mj_exclude_blogs( $query ) 
{
    if (! is_admin() && $query->is_main_query() ) {
        if (is_category() ) {
            $tax_query = $query->get('tax_query') ?: array();
            $tax_query[] = array(
             'taxonomy' => 'mj_article_type',
             'field' => 'slug',
             'terms' => 'blogpost',
             'operator' => 'NOT IN',
            );
            $query->set('tax_query', $tax_query);
        }
    }
    return $query;
}
add_action('pre_get_posts', 'mj_exclude_blogs');

/**
 * Only show 10 posts on blog archive pages.
 *
 * @param array $query the original query to modify.
 */
function mj_set_posts_per_page( $query ) 
{
    if (array_key_exists('blog', $query->query) ) {
        $query->set('posts_per_page', '10');
    }
}
add_action('pre_get_posts', 'mj_set_posts_per_page');
