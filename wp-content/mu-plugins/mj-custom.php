<?php
/*
  Plugin Name: Mother Jones Custom
  Description: Call in everything that makes the site MJ
  Version: 0.1
  Author: Mother Jones
  Author URI: http://www.motherjones.com
*/


require_once('fieldmanager/fieldmanager.php');


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
