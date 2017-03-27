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
		$template = get_the_terms( $post->ID, 'mj_article_type' );
		$custom_field = strtolower( $template[0]->slug );
	}
	if ( empty( $custom_field ) || in_array( $custom_field, array( 'article', 'blogpost' ), true ) ) {
		$template = get_stylesheet_directory() . '/single.php';
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
function mj_article_type_meta_box( $post ) {
	$terms = get_terms( 'mj_article_type', array( 'hide_empty' => false ) );
	$article_type = wp_get_object_terms( $post->ID, 'mj_article_type', array( 'orderby' => 'term_id', 'order' => 'ASC' ) );
	echo '<label class="hidden" for="mj_article_type">' . esc_html_e( 'Article Type', 'mj' ) . '</label>';
	echo '<select name="mj_article_type" id="mj_article_type" class="dropdown">';
	foreach ( $terms as $term ) {
		if ( isset( $article_type[0] ) && ( $article_type[0]->name === $term->name ) ) {
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
function save_mj_article_type_meta_box( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! isset( $_POST['mj_article_type'] ) ) {
		return;
	}
	$article_type = sanitize_text_field( $_POST['mj_article_type'] );

	// set a default, just in case
	if ( empty( $article_type ) ) {
		$term = get_term_by( 'slug', 'article', 'mj_article_type' );
	} else {
		$term = get_term_by( 'name', $article_type, 'mj_article_type' );
	}
	if ( ! is_wp_error( $term ) ) {
		wp_set_object_terms( $post_id, $term->term_id, 'mj_article_type', false );
	}
}
add_action( 'save_post', 'save_mj_article_type_meta_box' );

/**
 * Add a class to the body element for styling purposes.
 *
 * @param array $classes the body classes a given post already has.
 */
function mj_article_type_class( $classes ) {
	if ( is_single() ) {
		$post  = get_queried_object();
		$template = get_the_terms( $post->ID, 'mj_article_type' );
		if ( ! empty( $template ) ) {
			$classes[] = 'mj_article_type-' . $template[0]->slug;
		}
	}
	return $classes;
}
add_filter( 'body_class', 'mj_article_type_class' );

/**
 *  Utility function to see if we're looking at a given post type.
 *
 * @param string $slug the slug for the term we want to check for.
 * @param int    $post_id the post id to check.
 */
function mj_is_article_type( $slug, $post_id ) {
	$article_type = get_the_terms( $post_id, 'mj_article_type' );
	if ( $article_type && $article_type[0]->slug === $slug ) {
		return true;
	}
	return false;
}
