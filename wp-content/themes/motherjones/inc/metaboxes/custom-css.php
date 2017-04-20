<?php
/**
 * The custom css metabox.
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

/**
 * Register the metabox.
 */
largo_add_meta_box(
	'mj_custom_css',
	__( 'Custom CSS', 'mj' ),
	'mj_custom_css_meta_box_display',
	'post',
	'normal',
	'low'
);

/**
 * And output the markup.
 */
function mj_custom_css_meta_box_display() {
	global $post;
	$values = get_post_custom( $post->ID );
	wp_nonce_field( 'largo_meta_box_nonce', 'meta_box_nonce' );
	$field_value = isset( $values['custom_css'] ) ? $values['custom_css'][0] : '';
	printf(
		'<p>
			<label for="mj_custom_css">Custom CSS <span>Inline CSS to be applied to this post.</span></label>
			<textarea name="mj_custom_css" id="mj_custom_css">%s</textarea>
		</p>',
		esc_attr( $field_value )
	);
}
