<?php
/**
 * @package Mother Jones Content Fields
 * @version 0.1
 *
 * wherein we define the fields that our custom types need
 */
require_once( '../wordpress-fieldmanager/fieldmanager.php' );

add_action( 'fm_post_post', function() {
  $fm = new Fieldmanager_Group( array(
    'name' => 'contact_information',
    'children' => array(
      'name' => new Fieldmanager_Textfield( 'Name' ),
      'phone' => new Fieldmanager_Textfield( 'Phone Number' ),
      'website' => new Fieldmanager_Link( 'Website' ),
    ),
  ) );
  $fm->add_meta_box( 'Contact Information', 'post' );
} );

?>
