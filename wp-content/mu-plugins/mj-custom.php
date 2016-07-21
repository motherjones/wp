<?php
//FIXME!! needs scheduling (issue date?) taxonomy, file attachments
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

//Add fields to article types

//fullwidth only!
$title_image = new Fieldmanager_Group( array(
  'name' => 'full_width_title_image',
  'children' => array(
    'title_image' => new Fieldmanager_Media( 'Title Image' ),
    'title_image_byline' => new Fieldmanager_TextField( 'Title Image Byline' ),
  ),
) );
//end fullwidth only!

$dek = new Fieldmanager_TextField( array(
  'name' => 'dek',
) );

$social = new Fieldmanager_Group( array(
  'name' => 'social',
  'children' => array(
    'social_title' => new Fieldmanager_TextField( 'Social Title' ),
    'social_dek' => new Fieldmanager_TextField( 'Social Dek' ),
    'standout' => new Fieldmanager_Checkbox( array(
      'label' => 'Mark as Google News Standout',
    ) ),
    'fb_instant_exclude' => new Fieldmanager_Checkbox( array(
      'label' => 'Exclude from Facebook Instant',
    ) ),
  )
) );

$alt = new Fieldmanager_Group( array(
  'name' => 'alt',
  'description' => "How this'll look on the homepage",
  'children' => array(
    'alt_title' => new Fieldmanager_TextField( 'Alt Title' ),
    'alt_dek' => new Fieldmanager_TextField( 'Alt Dek' ),
  )
) );

$master_image = new Fieldmanager_Group( array(
  'name' => 'master_image',
  'children' => array(
    'master_image' => new Fieldmanager_Media( 'Image' ),
    'master_image_byline' => new Fieldmanager_TextField( 'Art Byline' ),
    'master_image_caption' => new Fieldmanager_TextField( 'Caption' ),
    'master_image_suppress' => new Fieldmanager_Checkbox( array(
      'label' => 'Suppress Master Image',
      'unchecked_value' => 'Show Master Image',
      'checked_value' => 'Hide Master Image',
    ) )
  )
) );

$byline = new Fieldmanager_Group( array( 
  'name' => 'Byline',
  'children' => array(
    'authors' => new Fieldmanager_Autocomplete( "Authors", array(
      'datasource' => new Fieldmanager_Datasource_Post( array(
        'query_args' => array( 'post_type' => 'mj_author' )
      ) ),
    ) ),
    'override' => new Fieldmanager_TextField( 'Byline Override' )
  )
) );

$body = new Fieldmanager_TextArea( array(
  'name' => 'body'
) );

//TAXONOMIES!!!! FFFFFFFFFUUUUUUUUUUUU

/* FIXME probly oughta figure out the real query args there
$related = new Fieldmanager_Autocomplete( array(
  'label'      => 'Related Articles',
  'limit'      => 0,
  'minimum_count' => 4,
  'sortable'   => true,
  'add_more_label' => 'Add another',
  'datasource' => new Fieldmanager_Datasource_Post( array(
    'query_args' => array( 'post_type' => 'mj_article,mj_full_width,mj_blog_post' )
  ) ),
) );
 */

//scheduling?


//file attachments?

$css_js = new Fieldmanager_Group( array(
  'name' => 'css_js',
  'children' => array(
    'css' => new Fieldmanager_TextArea( 'CSS' ),
    'js' => new Fieldmanager_TextArea( 'Javascript' ),
  )
) );

add_action( 'fm_post_mj_blog_post', function() {
  $dek->add_meta_box( 'Dek', 'mj_blog_post' );
  $social->add_meta_box( 'Social Titles', 'mj_blog_post' );
  $alt->add_meta_box( 'Alt Titles', 'mj_blog_post' );
  $master_image->add_meta_box( 'Master Image', 'mj_blog_post' );
  $byline->add_meta_box( 'Byline', 'mj_blog_post' );
  $body->add_meta_box( 'Article Body', 'mj_blog_post' );
  //$related->add_meta_box( 'Related Articles', 'mj_blog_post' );
  $css_js->add_meta_box( 'Extra CSS & JS', 'mj_blog_post' );
} );
add_action( 'fm_post_mj_article', function() {
  $dek->add_meta_box( 'Dek', 'mj_article' );
  $social->add_meta_box( 'Social Titles', 'mj_article' );
  $alt->add_meta_box( 'Alt Titles', 'mj_article' );
  $master_image->add_meta_box( 'Master Image', 'mj_article' );
  $byline->add_meta_box( 'Byline', 'mj_article' );
  $body->add_meta_box( 'Article Body', 'mj_article' );
  //$related->add_meta_box( 'Related Articles', 'mj_article' );
  $css_js->add_meta_box( 'Extra CSS & JS', 'mj_article' );
} );
add_action( 'fm_post_mj_full_width', function() {
  $title_image->add_meta_box( 'Title Image', 'mj_full_width' );
  $dek->add_meta_box( 'Dek', 'mj_full_width' );
  $social->add_meta_box( 'Social Titles', 'mj_full_width' );
  $alt->add_meta_box( 'Alt Titles', 'mj_full_width' );
  $master_image->add_meta_box( 'Master Image', 'mj_full_width' );
  $byline->add_meta_box( 'Byline', 'mj_full_width' );
  $body->add_meta_box( 'Article Body', 'mj_full_width' );
  //$related->add_meta_box( 'Related Articles', 'mj_full_width' );
  $css_js->add_meta_box( 'Extra CSS & JS', 'mj_full_width' );
} );


//add fields to author types
add_action( 'fm_post_mj_author', function() {
  $position = new Fieldmanager_TextField( array(
    'name' => 'position',
  ) );
  $position->add_meta_box( 'Position', 'mj_author' );

  $image = new Fieldmanager_Media( array( 
    'name' => 'image'
  ) );
  $image->add_meta_box( 'Author Photo', 'mj_author' );

  $long_bio = new Fieldmanager_TextArea( array(
    'name' => 'long_bio'
  ) );
  $long_bio->add_meta_box( 'Long Bio', 'mj_author' );

  $short_bio = new Fieldmanager_TextArea( array(
    'name' => 'short_bio'
  ) );
  $short_bio->add_meta_box( 'End of Article Bio', 'mj_author' );

  $twitter = new Fieldmanager_TextField( array(
    'name' => 'twitter',
  ) );
  $twitter->add_meta_box( 'Twitter User', 'mj_author' );


} );


//allow users to associate w/ an author type
add_action( 'fm_user', function() {

  $author = new Fieldmanager_Autocomplete( array(
    'label'      => 'author',
    'datasource' => new Fieldmanager_Datasource_Post( array(
      'query_args' => array( 'post_type' => 'mj_author' )
    ) ),
  ) );
  $author->add_user_form( 'Author Bio' );

});




?>
