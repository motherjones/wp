<?php
/**
 * @package Mother Jones Article Content Types
 * @version 0.1
 */

add_action( 'init', 'create_article_type' );
function create_article_type() {
  register_post_type( 'mj_article',
    array(
      'labels' => array(
        'name' => __( 'Articles' ),
        'singular_name' => __( 'Article' )
      ),
      'public' => true,
      'has_archive' => true,
    )
  );
}
add_action( 'init', 'create_full_width_article_type' );
function create_article_full_width_article_type() {
  register_post_type( 'mj_full_width_article',
    array(
      'labels' => array(
        'name' => __( 'Full Width Articles' ),
        'singular_name' => __( 'Full Width Article' )
      ),
      'public' => true,
      'has_archive' => true,
    )
  );
}

add_action( 'init', 'create_blog_post_type' );
function create_blog_post_type() {
  register_post_type( 'mj_blog_post',
    array(
      'labels' => array(
        'name' => __( 'Blog Posts' ),
        'singular_name' => __( 'Blog Post' )
      ),
      'public' => true,
      'has_archive' => true,
    )
  );
}
// Show the motherjones custom types on home page
//
add_action( 'pre_get_posts', 'add_custom_types_to_query' );
function add_custom_types_to_query( $query ) {
  if ( is_home() && $query->is_main_query() )
    $query->set( 'post_type', 
      array( 'mj_article', 'mj_full_width_article', 'mj_blog_post' )
    );
  return $query;
}

?>
