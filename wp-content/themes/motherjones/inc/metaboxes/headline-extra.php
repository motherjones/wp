<?php
/**
 * The headline extras metabox.
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

/**
 * Register the metabox.
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
		'dek' => array(
			'title' => 'Dek',
			'desc' => 'Short subtitle for this post.',
		),
		'social_hed' => array(
			'title' => 'Social Headline',
			'desc' => 'The headline used when sharing on social.',
		),
		'social_dek' => array(
			'title' => 'Social Dek',
			'desc' => 'The subtitle used when sharing on social.',
		),
	);
	wp_nonce_field( 'largo_meta_box_nonce', 'meta_box_nonce' );
	foreach ( $fields as $field => $attr ) {
		$field_slug = $prefix . $field;
		$field_title = isset( $attr['title'] ) ? $attr['title'] : '';
		$field_desc = isset( $attr['desc'] ) ? ' <span>' . $attr['desc'] . '</span>' : '';
		$field_value = isset( $values[ $field_slug ] ) ? $values[ $field_slug ][0] : '';
		printf(
			'<p>
				<label for="%1$s">%2$s%3$s</label>
				<input type="text" name="%1$s" id="%1$s" value="%4$s" />
			</p>',
			esc_attr( $field_slug ),
			esc_html( $field_title ),
			wp_kses( $field_desc, array(
				'span' => array(),
			) ),
			esc_attr( $field_value )
		);
	}
}
