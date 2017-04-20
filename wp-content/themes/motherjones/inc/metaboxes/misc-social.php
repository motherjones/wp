<?php
/**
 * The misc social options metabox.
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

/**
 * Register the metabox.
 */
largo_add_meta_box(
	'mj_misc_social_toggles',
	__( 'Meta Options', 'mj' ),
	'mj_misc_social_toggles_meta_box_display',
	'post',
	'side',
	'default'
);

/**
 * Get post meta in a callback
 *
 * @param WP_Post $post    The current post.
 */
function mj_misc_social_toggles_meta_box_display( $post ) {
	global $post;
	$fields = array(
		'mj_google_standout' => 'Mark as Google News Standout?',
		'mj_fb_instant_exclude' => 'Exclude from Facebook Instant?',
		'mj_hide_ads' => 'Hide ads on this post?',
		'mj_overlay_hide' => 'Hide text overlay on title image?',
	);
	wp_nonce_field( 'mj_misc_social_toggles', 'mj_misc_social_toggles_nonce' );
	foreach ( $fields as $field => $copy ) {
		$checked = ( 1 === intval( get_post_meta( $post->ID, $field, true ) ) ) ? 'checked="checked"' : '';
		echo '<p><label class="selectit"><input type="checkbox" value="true" name="' . esc_attr( $field ) . '"' . esc_attr( $checked ) . '> ' . esc_html( $copy ) . '</label></p>';
	}
}

/**
 * Save data from meta box
 *
 * @param int    $post_id the post ID.
 * @param object $post the post object.
 */
function mj_misc_social_toggles_save( $post_id, $post ) {

	// Verify the nonce before proceeding.
	if ( ! isset( $_POST['mj_misc_social_toggles_nonce'] ) || ! wp_verify_nonce( $_POST['mj_misc_social_toggles_nonce'], 'mj_misc_social_toggles' ) ) {
		return false;
	}

	// Get the post type object.
	$post_type = get_post_type_object( $post->post_type );

	// Check if the current user has permission to edit the post.
	if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
		return $post_id;
	}

	$fields = array( 'mj_google_standout', 'mj_fb_instant_exclude', 'mj_hide_ads', 'mj_overlay_hide' );

	foreach ( $fields as $meta_key ) {
		// Get the posted data and sanitize it for use as an HTML class.
		$new_meta_value = ( isset( $_POST[ $meta_key ] ) ? sanitize_html_class( $_POST[ $meta_key ] ) : '' );

		// Get the meta value of the custom field key.
		$meta_value = get_post_meta( $post_id, $meta_key, true );

		/*
		 * If the checkbox was checked, update the meta_value
		 * If the checkbox was unchecked, delete the meta_value
		 */
		if ( ! empty( $new_meta_value ) ) {
			add_post_meta( $post_id, $meta_key, 1, true );
		} else {
			delete_post_meta( $post_id, $meta_key );
		}
	}

}
add_action( 'save_post', 'mj_misc_social_toggles_save', 10, 2 );
