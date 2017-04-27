<?php
/**
 * Custom Post Templates
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

/**
 * Filter the single template value, and replace it with the selected custom template.
 *
 * @param string $template the slug for the template we want to fetch.
 */
function mj_get_post_template( $template ) {
	global $post;
	if ( is_object( $post ) ) {
		$template = get_the_terms( $post->ID, 'mj_content_type' );
		$custom_field = ! empty( $template ) ? strtolower( $template[0]->slug ) : false;
	}
	if ( ! $custom_field || in_array( $custom_field, array( 'article' ), true ) ) {
		$template = get_stylesheet_directory() . '/singular.php';
	} else {
		$template = get_stylesheet_directory() . '/single-' . $custom_field . '.php';
	}
	return $template;
}
add_filter( 'single_template', 'mj_get_post_template' );

/**
 * Display the article typemeta box
 *
 * @param object $post the post object.
 */
function mj_content_type_meta_box( $post ) {
	$terms = get_terms( 'mj_content_type', array(
		'hide_empty' => false,
	) );
	$content_type = get_the_terms( $post->ID, 'mj_content_type' );
	echo '<select name="mj_content_type" id="mj_content_type" class="dropdown">';
	foreach ( $terms as $term ) {
		if ( isset( $content_type[0] ) && ( $content_type[0]->name === $term->name ) ) {
			$selected = ' selected="selected"';
		} else {
			$selected = '';
		}
		echo '<option value="' . esc_attr( $term->name ) . '"' . esc_attr( $selected ) . '>' . esc_html( $term->name ) . '</option>';
	}
	echo '</select>';
}

/**
 * Save the meta box.
 *
 * @param int $post_id The ID of the post that's being saved.
 */
function save_mj_content_type_meta_box( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! isset( $_POST['mj_content_type'] ) ) {
		return;
	}
	$content_type = sanitize_text_field( $_POST['mj_content_type'] );

	// set a default, just in case
	if ( empty( $content_type ) ) {
		$term = get_term_by( 'slug', 'article', 'mj_content_type' );
	} else {
		$term = get_term_by( 'name', $content_type, 'mj_content_type' );
	}
	if ( ! is_wp_error( $term ) ) {
		wp_set_object_terms( $post_id, $term->term_id, 'mj_content_type', false );
	}
}
add_action( 'save_post', 'save_mj_content_type_meta_box' );

/**
 * Add a class to the body element for styling purposes.
 *
 * @param array $classes the body classes a given post already has.
 */
function mj_content_type_class( $classes ) {
	if ( is_single() ) {
		$post  = get_queried_object();
		$template = get_the_terms( $post->ID, 'mj_content_type' );
		if ( ! empty( $template ) ) {
			$classes[] = 'mj_content_type-' . $template[0]->slug;
		}
	}
	return $classes;
}
add_filter( 'body_class', 'mj_content_type_class' );

/**
 *  Utility function to see if we're looking at a given post type.
 *
 * @param string $slug the slug for the term we want to check for.
 * @param int    $post_id the post id to check.
 */
function mj_is_content_type( $slug, $post_id ) {
	$content_type = get_the_terms( $post_id, 'mj_content_type' );
	if ( $content_type && $content_type[0]->slug === $slug ) {
		return true;
	}
	return false;
}

/**
 *  Add body classes when we need them.
 */
function mj_body_classes() {
	global $post, $mj;
	$mj['body_classes'] = '';
	// add a draft class if a single posts is not published.
	if ( is_single() && 'draft' === get_post_status( $post->ID ) ) {
		$mj['body_classes'] .= 'mj_post_status-draft';
	}
}
add_action( 'wp_head', 'mj_body_classes' );
