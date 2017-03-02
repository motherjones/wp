<?php
/**
 * Hide some less commonly-used metaboxes to cleanup the post and page edit screens
 */
function mj_remove_default_post_screen_metaboxes() {
  global $current_user;
  get_currentuserinfo();
  // remove these for everyone
  $remove = array( 'trackbacksdiv', 'revisionsdiv', 'commentsdiv' );
  // remove custom fields for everyone except administrators
  if ( ! current_user_can( 'manage_options' ) ) {
    $remove[] = 'postcustom';
  }
  foreach( $remove as $box ) {
    remove_meta_box( $box,'post','normal' );
  }
}
add_action( 'admin_menu','mj_remove_default_post_screen_metaboxes' );
