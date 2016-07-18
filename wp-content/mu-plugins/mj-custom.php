<?php
/*
  Plugin Name: Mother Jones Custom
  Description: Call in everything that makes the site MJ
  Version: 0.1
  Author: Mother Jones
  Author URI: http://www.motherjones.com
*/


//Add custom types for mj_article, mj_blog_post, mj_full_width
require_once('mj_custom/motherjones-content-types.php');

require_once('fieldmanager/fieldmanager.php');


$master_image = new Fieldmanager_Group( array(
  'name' => 'master_image',
  'children' => array(
    'master_image' => new Fieldmanager_Media( 'Image' ),
    'master_image_byline' => new Fieldmanager_Textfield( 'Art Byline' ),
    'master_image_caption' => new Fieldmanager_Textfield( 'Caption' ),
    'master_image_suppress' => new Fieldmanager_Checkbox( array(
      'label' => 'Suppress Master Image',
      'unchecked_value' => 'Show Master Image',
      'checked_value' => 'Hide Master Image',
    ) )
  )
) );

add_action( 'fm_post_mj_blog_post', function() {
  $master_image->add_meta_box( 'Master Image', 'mj_blog_post' );
} );
add_action( 'fm_post_mj_article', function() {
  $master_image->add_meta_box( 'Master Image', 'mj_article' );
} );
add_action( 'fm_post_mj_full_width', function() {
  $master_image->add_meta_box( 'Master Image', 'mj_full_width' );
} );

//oh boy, what fun
add_action( 'fm_user', function() {
  $position = new Fieldmanager_Textfield(
    'name' => 'position',
  );
  $position->add_user_form( 'Position' );
} );


?>
