<?php

/*
  Plugin Name: Mother Jones Custom
  Description: Call in everything that makes the site MJ
  Version: 0.1
  Author: Mother Jones
  Author URI: http://www.motherjones.com
  This is the part where I define all the fields our custom parts need
*/

define('__ROOT__', dirname(dirname(__FILE__))); 
require_once(__ROOT__.'/fieldmanager/fieldmanager.php');

if ( !class_exists( 'MJ_Custom_Fields' ) ) {

  class MJ_Custom_Fields {

    private static $instance;
    public static function instance() {
      if ( ! isset( self::$instance ) ) {
        self::$instance = new MJ_Custom_Fields;
      }
      return self::$instance;
    }

    /* Article type fields (also blogposts, full widths) */
    public function title_image() { 
      return new Fieldmanager_Group( array(
        'name' => 'full_width_title_image',
        'children' => array(
          'title_image' => new Fieldmanager_Media( 'Title Image' ),
          'title_image_byline' => new Fieldmanager_TextField( 'Title Image Byline' ),
        ),
      ) );
    }

    public function dek() {
      return new Fieldmanager_TextField( array(
        'name' => 'dek',
      ) );
    }

    public function social() {
      return new Fieldmanager_Group( array(
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
    }

    public function alt() {
      return new Fieldmanager_Group( array(
        'name' => 'alt',
        'description' => "How this'll look on the homepage",
        'children' => array(
          'alt_title' => new Fieldmanager_TextField( 'Alt Title' ),
          'alt_dek' => new Fieldmanager_TextField( 'Alt Dek' ),
        )
      ) );
    }

    public function master_image() {
      return new Fieldmanager_Group( array(
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
    }

    public function body() { 
      return new Fieldmanager_RichTextArea( array(
        'name' => 'body'
      ) );
    }

    public function taxonomies() {
      $children = array(
        'section' => self::section(),
        'media_type' => self::media_type(),
        'primary_tags' => self::primary_tags(),
      );

      return new Fieldmanager_Group( array(
        'name' => 'taxonomies',
        'children' => $children
      ) );
    }

    public function blog_taxonomies() {
      $children = array(
        'blog_type' => self::blog_type(),
        'media_type' => self::media_type(),
        'primary_tags' => self::primary_tags(),
      );

      return new Fieldmanager_Group( array(
        'name' => 'taxonomies',
        'children' => $children
      ) );
    }

    public function section() {
      return new Fieldmanager_Select("Section", array(
        'name' => 'section',
        'datasource' => new Fieldmanager_Datasource_Term( array(
          'taxonomy' => 'category',
        ) )
      ) );
    }

    public function blog_type() {
      return new Fieldmanager_Select("Blog Type", array(
        'name' => 'blog_type',
        'datasource' => new Fieldmanager_Datasource_Term( array(
          'taxonomy' => 'mj_blog_type',
        ) )
      ) );
    }

    public function media_type() {
      return new Fieldmanager_Select("Media Type", array(
        'name' => 'media_type',
        'datasource' => new Fieldmanager_Datasource_Term( array(
          'taxonomy' => 'mj_media_type',
        ) )
      ) );
    }

    public function primary_tags() {
      return new Fieldmanager_Autocomplete( "Primary Tags", array(
        'name' => 'primary_tags',
        'limit'      => 0,
        'add_more_label' => 'Add another tag',
        'datasource' => new Fieldmanager_Datasource_Term( array(
          'taxonomy' => 'mj_primary_tag',
        ) )
      ) );
    }


    public function file_attachments() {
      return new Fieldmanager_Group( array( 
        'name' => 'file_attachments',
        'children' => array(
          'files' => new Fieldmanager_Media( 'File Attachments', array(
            'limit'      => 0,
            'sortable'   => true,
            'add_more_label' => 'Add another file',
          ) )
        )
      ) );
    }

    public function css_js() {
      return new Fieldmanager_Group( array(
        'name' => 'css_js',
        'children' => array(
          'css' => new Fieldmanager_TextArea( 'CSS' ),
          'js' => new Fieldmanager_TextArea( 'Javascript' ),
        )
      ) );
    }

    public function dateline_override() {
      return new Fieldmanager_TextField( array(
        'name' => 'dateline_override',
      ) );
    }


    public function byline() {
      return new Fieldmanager_TextField( array( 
        'name' => 'byline_override',
      ) );
    }

    public function related() {
      return new Fieldmanager_Group( array( 
        'name' => 'related_articles',
        'children' => array(
          'relateds' => new Fieldmanager_Autocomplete( "Article", array(
            'limit'      => 0,
            'sortable'   => true,
            'add_more_label' => 'Add another article',
            'datasource' => new Fieldmanager_Datasource_Post( array(
              'query_args' => array( 
                'post_type' => array('mj_blog_post', 'mj_article', 'mj_full_width'),
                'post_status' => 'publish'
              )
            ) ),
          ) )
        )
      ) );
    }

    /* end article type fields */

    /* begin author type fields */

    public function position() {
      return new Fieldmanager_TextField( array(
        'name' => 'position'
      ) );
    }

    public function image() {
      return new Fieldmanager_Media( array( 
        'name' => 'image'
      ) );
    }

    public function long_bio() {
      return new Fieldmanager_RichTextArea( array(
        'name' => 'long_bio'
      ) );
    }

    public function short_bio() {
      return new Fieldmanager_TextArea( array(
        'name' => 'short_bio'
      ) );
    }

    public function twitter() {
      return new Fieldmanager_TextField( array(
        'name' => 'twitter',
      ) );
    }


    /* end author type fields */

  }

}

function MJ_Custom_Fields() {
  return MJ_Custom_Fields::instance();
}

?>
