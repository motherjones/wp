<?php
/**
 * The custom tags metabox.
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

/**
 * Register the metabox.
 */
largo_add_meta_box(
	'mj_tags',
	__( 'Tags', 'mj' ),
	'mj_tags_meta_box_display',
	'post',
	'side',
	'default'
);

/**
 * And output the markup.
 *
 * @param object $post the post object.
 */
function mj_tags_meta_box_display( $post ) {
	$all_tags = get_terms( array(
		'taxonomy' => 'post_tag',
		'hide_empty' => 0,
	) );

	// Tags on this post already.
	$post_tags = get_the_terms( $post->ID, 'post_tag' );

	// Create an array of tag IDs for this post.
	$ids = array();
	if ( $post_tags ) {
		foreach ( $post_tags as $tag ) {
			$ids[] = $tag->term_id;
		}
	}

	echo '<div id="taxonomy-post_tag" class="categorydiv">';
	echo '<input type="hidden" name="tax_input[post_tag][]" value="0" />';
	echo '<ul>';
	foreach ( $all_tags as $tag ) {
		// Unchecked by default.
		$checked = '';
		// Check the checkbox if the post has this tag.
		if ( in_array( $tag->term_id, $ids, true ) ) {
			$checked = " checked='checked'";
		}
		$id = 'post_tag-' . $tag->term_id;
		echo '<li id="' . esc_attr( $id ) . '">';
		echo '<label><input type="checkbox" name="tax_input[post_tag][]" id="in-' . esc_attr( $id ) . '"' . esc_attr( $checked ) . ' value="' . esc_attr( $tag->slug ) . '" />' . esc_html( $tag->name ) . '</label><br />';
		echo '</li>';
	}
	echo '</ul></div>';
}
