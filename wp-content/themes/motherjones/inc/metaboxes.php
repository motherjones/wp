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
	}
	foreach ( $remove as $box ) {
		remove_meta_box( $box, 'post', 'normal' );
	}
}
add_action( 'admin_menu','mj_remove_metaboxes' );

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
 * Register the extra headline fields metabox
 */
largo_add_meta_box(
	'mj_headline_extra',
	__( 'Other Headline Fields', 'mj' ),
	'mj_headline_extra_meta_box_display',
	'post',
	'after_title',
	'high'
);
/**
 * Move the mj_headline_extra metabox to just below the title/headline.
 *
 * Usually the fifth argument in add_meta_box is side, core or advanced,
 * but apparently you can set it to something arbitrary and then call do_meta_boxes
 * with that as the context arg, hooked on the edit_form hook you want
 * (options: edit_form_after_title, edit_form_after_editor, edit_form_advanced)
 */
function mj_move_headline_extra() {
	global $post, $wp_meta_boxes;
	do_meta_boxes( get_current_screen(), 'after_title', $post );
	unset( $wp_meta_boxes['post']['test'] );
}
add_action( 'edit_form_after_title', 'mj_move_headline_extra' );
/**
 * And output the markup.
 */
function mj_headline_extra_meta_box_display() {
	global $post;
	$values = get_post_custom( $post->ID );
	$prefix = 'mj_';
	$fields = array(
		'dek' => 'Dek',
		'social_hed' => 'Social Headline',
		'social_dek' => 'Social Dek',
	);
	wp_nonce_field( 'largo_meta_box_nonce', 'meta_box_nonce' );
	foreach ( $fields as $field => $title ) {
		$field_name = $prefix . $field;
		$field_value = isset( $values[ $field_name ] ) ? esc_attr( $values[ $field_name ][0] ) : '';
		printf(
			'<p>
				<label for="%1$s">%2$s</label>
				<input type="text" name="%1$s" id="%1$s" value="%3$s" />
			</p>',
			esc_html( $field_name ),
			esc_html( $title ),
			esc_html( $field_value )
		);
	}
}


/**
 * Register the post custom fields metabox
 */
largo_add_meta_box(
	'mj_custom_fields',
	__( 'Mother Jones Extra Fields', 'mj' ),
	'mj_custom_meta_box_display',
	'post',
	'normal',
	'core'
);
/**
 * And output the markup.
 */
function mj_custom_meta_box_display() {
	global $post;
	$values = get_post_custom( $post->ID );
	$prefix = 'mj_';
	$fields = array(
		'promo_hed' => 'Homepage Headline',
		'promo_dek' => 'Homepage Dek',
		'byline_override' => 'Byline Override',
		'dateline_override' => 'Dateline Override',
	);
	wp_nonce_field( 'largo_meta_box_nonce', 'meta_box_nonce' );
	foreach ( $fields as $field => $title ) {
		$field_name = $prefix . $field;
		$field_value = isset( $values[ $field_name ] ) ? esc_attr( $values[ $field_name ][0] ) : '';
		printf(
			'<p>
				<label for="%1$s">%2$s</label>
				<input type="text" name="%1$s" id="%1$s" value="%3$s" />
			</p>',
			esc_html( $field_name ),
			esc_html( $title ),
			esc_html( $field_value )
		);
	}
}
/**
 * Register and sanitize the input fields.
 */
largo_register_meta_input(
	array(
		'mj_dek',
		'mj_social_hed',
		'mj_social_dek',
		'mj_promo_hed',
		'mj_promo_dek',
		'mj_byline_override',
		'mj_dateline_override',
	),
	'sanitize_text_field'
);
