<?php
/**
 * The extra fields metabox.
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

/**
 * Register the metabox.
 */
largo_add_meta_box(
	'mj_extra_fields',
	__( 'Extra Fields', 'mj' ),
	'mj_extra_meta_box_display',
	'post',
	'normal',
	'core'
);
/**
 * And output the markup.
 */
function mj_extra_meta_box_display() {
	global $post;
	$values = get_post_custom( $post->ID );
	$prefix = 'mj_';
	$fields = array(
		'promo_hed' => array(
			'title' => 'Homepage Headline',
			'desc' => 'Headline used for this story on the homepage only.',
		),
		'promo_dek' => array(
			'title' => 'Homepage Dek',
			'desc' => 'Subtitle used for this story on the homepage only.',
		),
		'byline_override' => array(
			'title' => 'Byline Override',
			'desc' => 'Text to display instead of the default byline.',
		),
		'issue_date' => array(
			'title' => 'Issue Date',
			'desc' => 'Issue number/date to display instead of the default dateline.',
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
