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


add_action( 'fm_post_blog_post', function() {
  $fm = new Fieldmanager_Group( array(
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
  $fm->add_meta_box( 'Master Image', 'mj_article' );
  $fm->add_meta_box( 'Master Image', 'mj_blog_post' );
  $fm->add_meta_box( 'Master Image', 'mj_full_width' );
  $fm->add_meta_box( 'Master Image', 'post' );
} );

?>
