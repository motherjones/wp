<?php
/**
 * Anything and everything metabox-related.
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

/**
 * Hide and rearrange some of the default metaboxes.
 * Everyone loses slug, excerpt, trackbacks, revisions and comments.
 * Admins get custom fields.
 */
function mj_remove_metaboxes() {
	global $current_user;
	wp_get_current_user();
	// Remove these for everyone.
	$remove = array( 'trackbacksdiv', 'revisionsdiv', 'commentstatusdiv', 'postexcerpt', 'slugdiv' );
	// Show these for admins only.
	if ( ! current_user_can( 'manage_options' ) ) {
		$remove[] = 'postcustom';
		$remove[] = 'mj_custom_css';
	}
	foreach ( $remove as $box ) {
		remove_meta_box( $box, 'post', 'normal' );
	}
	// Remove the tags metabox so we can replace it with our custom one.
	remove_meta_box( 'tagsdiv-post_tag', 'post', 'side' );
}
add_action( 'do_meta_boxes','mj_remove_metaboxes' );

/**
 * Show all the other metaboxes by default.
 *
 * @param array  $hidden The array of metaboxes that are already hidden.
 * @param string $screen The type of admin screen we're looking at.
 */
function mj_change_default_hidden_metaboxes( $hidden, $screen ) {
	if ( 'post' === $screen->base ) {
		$hidden = array();
	}
	return $hidden;
}
add_filter( 'default_hidden_meta_boxes', 'mj_change_default_hidden_metaboxes', 10, 2 );

/**
 * Load the custom metaboxes.
 */
function mj_metaboxes_require_files() {
	$metaboxes = array(
		'custom-css',
		'custom-tags',
		'extra-fields',
		'headline-extra',
		'misc-social',
	);
	foreach ( $metaboxes as $metabox ) {
		require_once( get_template_directory() . '/inc/metaboxes/' . $metabox . '.php' );
	}
}
add_action( 'init', 'mj_metaboxes_require_files' );


/**
 * Register and sanitize input fields.
 */
largo_register_meta_input(
	array(
		'mj_dek',
		'mj_social_hed',
		'mj_social_dek',
		'mj_promo_hed',
		'mj_promo_dek',
		'mj_byline_override',
		'mj_issue_date',
		'mj_custom_css',
	),
	'sanitize_text_field'
);
