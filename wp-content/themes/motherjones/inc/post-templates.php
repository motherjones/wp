<?php

// Layout options for post templates
largo_add_meta_box(
  'mj_layout_meta',
  __( 'Post Template', 'mj-post-templates' ),
  mj_layout_meta_box_display,
  array( 'post' ),
  'side',
  'core'
);
largo_register_meta_input( '_wp_post_template', 'sanitize_text_field' );

//	Scan the template files of the active theme,
//	returns an array of [Template Name => {file}.php]
function mj_get_post_templates() {
  $theme = wp_get_theme();
  $templates = $theme->get_files( 'php', 1, true );
  $post_templates = array();
  $base = array( trailingslashit( get_template_directory() ), trailingslashit( get_stylesheet_directory() ) );
  foreach ( (array) $templates as $template ) {
    $template = WP_CONTENT_DIR . str_replace( WP_CONTENT_DIR, '', $template );
    $basename = str_replace( $base, '', $template );
    $template_data = implode( '', file( $template ) );
    $name = '';
    if ( preg_match( '|Post Template:(.*)$|mi', $template_data, $name ) ) {
      $name = _cleanup_header_comment( $name[1] );
    }
    if ( ! empty( $name ) ) {
      if( basename( $template ) != basename(__FILE__) ) {
        $post_templates[trim($name)] = $basename;
      }
    }
  }
  return $post_templates;
}

// Filter the single template value, and replace it with the selected custom template
add_filter( 'single_template', 'mj_get_post_template' );
function mj_get_post_template( $template ) {
  global $post;
  if ( is_object( $post ) ) {
    $custom_field = get_post_meta( $post->ID, '_wp_post_template', true );
  }
  //if we have a custom field and it's not the default (article)
  if ( ! empty( $custom_field ) ) {
    if ( file_exists( get_stylesheet_directory() . "/{$custom_field}" ) ) {
      $template = get_stylesheet_directory() . "/{$custom_field}";
    } else if ( file_exists( get_template_directory() . "/{$custom_field}" ) ) {
      $template = get_template_directory() . "/{$custom_field}";
    }
  }
  return $template;
}

// Build the dropdown menu displayed in the post edit UI
function mj_post_templates_dropdown() {
  global $post;
  $post_templates = mj_get_post_templates();
  foreach ( $post_templates as $template_name => $template_file ) { //loop through templates, make them options
    if ( $template_file == get_post_meta( $post->ID, '_wp_post_template', true ) ) {
      $selected = ' selected="selected"';
    } else {
      $selected = '';
    }
    $opt = '<option value="' . $template_file . '"' . $selected . '>' . $template_name . '</option>';
    echo $opt;
  }
}

// Output the markup for the metabox
function mj_layout_meta_box_display() {
  global $post;
  wp_nonce_field( 'largo_meta_box_nonce', 'meta_box_nonce' );
  if ( $post->post_type != 'page' ) {
    echo '<p><strong>' . __( 'Template', 'mj-post-templates' ) . '</strong></p>';
    echo '<p>' . __( 'Select the post template you wish this post to use.', 'mj-post-templates' ) . '</p>';
    echo '<label class="hidden" for="post_template">' . __( 'Post Template', 'mj-post-templates' ) . '</label>';
    echo '<select name="_wp_post_template" id="post_template" class="dropdown">';
    echo '<option value="">' . __( 'Article', 'mj' ) . '</option>';
    mj_post_templates_dropdown(); //get the options
    echo '</select>';
  }
}

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
