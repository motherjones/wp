<?php
/**
 * Hide and rearrange some of the default metaboxes
 */

// Hide trackbacks, revisions, comments and (sometimes) custom fields
function mj_remove_metaboxes() {
  global $current_user;
  wp_get_current_user();
  // remove for everyone
  $remove = array( 'trackbacksdiv', 'revisionsdiv', 'commentstatusdiv' );
  // show for editors and above
  if ( ! current_user_can( 'edit_others_posts' ) ) {
    $remove[] = 'postexcerpt';
    $remove[] = 'slugdiv';
  }
  // show for admins only
  if ( ! current_user_can( 'manage_options' ) ) {
    $remove[] = 'postcustom';
  }
  foreach( $remove as $box ) {
    remove_meta_box( $box, 'post', 'normal' );
  }
}
add_action( 'admin_menu','mj_remove_metaboxes' );

// Put the authors metabox in the right column
function mj_coauthors_metabox_context( $context ) {
    $context = 'side';
    return $context;
}
function mj_coauthors_metabox_priority( $priority ) {
    $priority = 'high';
    return $priority;
}
add_filter( 'coauthors_meta_box_context', 'mj_coauthors_metabox_context' );
add_filter( 'coauthors_meta_box_priority', 'mj_coauthors_metabox_priority' );

// Show all the other metaboxes by default
function mj_change_default_hidden_metaboxes( $hidden, $screen ) {
    if ( 'post' == $screen->base ) {
        $hidden = array();
    }
    return $hidden;
}
add_filter( 'default_hidden_meta_boxes', 'mj_change_default_hidden_metaboxes', 10, 2 );


//edit_form_after_title
//edit_form_after_editor
//edit_form_advanced
largo_add_meta_box(
	'mj_title_extra',
	__( 'Title Extra', 'mj' ),
	'mj_title_extra_meta_box_display',
	'post',
	'after_title',
	'high'
);
// usually the fifth argument in add_meta_box is side, core or advanced,
// but apparently you can set it to something arbitrary and then call do_meta_boxes
// with that as the context arg, hooked on the edit_form hook you want
// (options: edit_form_after_title, edit_form_after_editor, edit_form_advanced)
function mj_move_dek() {
  global $post, $wp_meta_boxes;
  do_meta_boxes( get_current_screen(), 'after_title', $post );
  unset( $wp_meta_boxes['post']['test'] );
}
add_action('edit_form_after_title', 'mj_move_dek');

function mj_title_extra_meta_box_display() {
	global $post;
	$values = get_post_custom( $post->ID );
  $prefix = 'mj_';
  $fields = array(
    'dek' => 'Dek',
    'social_hed' => 'Social Hed',
    'social_dek' => 'Social Dek',
  );
  wp_nonce_field( 'largo_meta_box_nonce', 'meta_box_nonce' );
  foreach ( $fields as $field => $title ) {
    $field_name = $prefix . $field;
    $field_value = isset( $values[$field_name] ) ? esc_attr( $values[$field_name][0] ) : '';
    printf(
      '<p>
    		<label for="%1$s">%2$s</label>
    		<input type="text" name="%1$s" id="%1$s" value="%3$s" />
    	</p>',
      $field_name,
      $title,
      $field_value
    );
  }
}

/**
 * Custom meta boxes, starting with our extra fields for every post
 */
// Register the post custom metabox
largo_add_meta_box(
	'mj_custom_fields',
	__( 'Mother Jones Extra Fields', 'mj' ),
	'mj_custom_meta_box_display',
	'post',
	'normal',
	'core'
);
// display the metabox
function mj_custom_meta_box_display() {
	global $post;
	$values = get_post_custom( $post->ID );
  $prefix = 'mj_';
  $fields = array(
    'promo_hed' => 'Promo (Homepage) Hed',
    'promo_dek' => 'Promo (Homepage) Dek',
    'byline_override' => 'Byline Override',
    'dateline_override' => 'Dateline Override',
  );
  wp_nonce_field( 'largo_meta_box_nonce', 'meta_box_nonce' );
  foreach ( $fields as $field => $title ) {
    $field_name = $prefix . $field;
    $field_value = isset( $values[$field_name] ) ? esc_attr( $values[$field_name][0] ) : '';
    printf(
      '<p>
    		<label for="%1$s">%2$s</label>
    		<input type="text" name="%1$s" id="%1$s" value="%3$s" />
    	</p>',
      $field_name,
      $title,
      $field_value
    );
  }
}
// register and sanitize the input fields
largo_register_meta_input(
  array(
    'mj_dek',
    'mj_social_hed',
    'mj_social_dek',
    'mj_promo_hed',
    'mj_promo_dek',
    'mj_byline_override',
    'mj_dateline_override'
  ),
  'sanitize_text_field'
);
