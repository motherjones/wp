<?php

// Filter the single template value, and replace it with the selected custom template
add_filter( 'single_template', 'mj_get_post_template' );
function mj_get_post_template( $template ) {
  global $post;
  if ( is_object( $post ) ) {
    $template = get_the_terms( $post->ID, 'mj_article_type' );
    $custom_field = strtolower( $template[0]->slug );
  }
  if ( empty( $custom_field ) || $custom_field === 'article' ) {
    $template = get_stylesheet_directory() . "/single.php";
  } else {
    $template = get_stylesheet_directory() . "/single-{$custom_field}.php";
  }
  echo $template;
  return $template;
}

/**
 * Display meta box
 */
function mj_article_type_meta_box( $post ) {
  $terms = get_terms( 'mj_article_type', array( 'hide_empty' => false ) );
	$post  = get_post();
	$article_type = wp_get_object_terms( $post->ID, 'mj_article_type', array( 'orderby' => 'term_id', 'order' => 'ASC' ) );
  print_r($article_type);
  echo '<label class="hidden" for="mj_article_type">' . __( 'Article Type', 'mj' ) . '</label>';
  echo '<select name="mj_article_type" id="mj_article_type" class="dropdown">';
  foreach ( $terms as $term ) {
    if ( isset( $article_type[0] ) && ( $article_type[0]->name === $term->name ) ) {
      $selected = ' selected="selected"';
    } else {
      $selected = '';
    }
    $opt = '<option value="' . $term->name . '"' . $selected . '>' . $term->name . '</option>';
    echo $opt;
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
 * Modelled on is_page_template, determine if we are in a single post template.
 * You can optionally provide a template name and then the check will be
 * specific to that template.
 *
 * @uses $wp_query
 *
 * @param string $template The specific template name if specific matching is required.
 * @return bool True on success, false on failure.
 */
function mj_is_post_template( $template = '' ) {
  if ( ! is_single() ) {
    return false;
  }
  $post_template = get_post_meta( get_queried_object_id(), '_wp_post_template', true );
  if ( empty( $template ) ) {
    return (bool) $post_template;
  }
  if ( $template == $post_template ) {
    return true;
  }
  if ( 'default' == $template && ! $post_template ) {
    return true;
  }
  return false;
}
