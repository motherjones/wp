<?php

if ( !class_exists( 'MJ_Users' ) ) {

  class MJ_Users {
    private static $instance;
    public static function instance() {
      if ( ! isset( self::$instance ) ) {
        self::$instance = new MJ_Taxonomy;
        self::$instance->setup();
      }
      return self::$instance;
    }

    public function setup() {
      $former_author = add_role( 'former_author', __(
        'Former Author' ),
      array( 

        'read' => false, // true allows this capability
        'edit_posts' => false, // Allows user to edit their own posts
        'edit_pages' => false, // Allows user to edit pages
        'edit_others_posts' => false, // Allows user to edit others posts not just their own
        'create_posts' => false, // Allows user to create new posts
        'manage_categories' => false, // Allows user to manage post categories
        'publish_posts' => false, // Allows the user to publish, otherwise posts stays in draft mode
        'edit_themes' => false, // false denies this capability. User can’t edit your theme
        'install_plugins' => false, // User cant add new plugins
        'update_plugin' => false, // User can’t update any plugins
        'update_core' => false // user cant perform core updates

      ) );
      $former_author->add_cap('level_1');

    }

  }

  function MJ_Users() {
    return MJ_Users::instance();
  }
} 

?>
