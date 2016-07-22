<?php

if ( !class_exists( 'MJ_Taxonomy' ) ) {

  class MJ_Taxonomy {

    private $media_taxonomy = array(
      'single' => 'Media Type',
      'plural' => 'Media Types',
      'capabilites' => array(
        'assign_terms' => 'Administrator',
        'edit_terms' => 'Administrator',
        'delete_terms' => 'Administrator',
      ),
      'types'  => array()
    );
    private $tag_taxonomy = array(
      'single' => 'Primary Tag',
      'plural' => 'Primary Tags',
      'capabilites' => array(
        'assign_terms' => 'Administrator',
        'edit_terms' => 'Administrator',
        'delete_terms' => 'Administrator',
      ),
      'types'  => array()
    );
    private $section_taxonomy = array(
      'single' => 'Section',
      'plural' => 'Sections',
      'capabilites' => array(
        'assign_terms' => 'Administrator',
        'edit_terms' => 'Administrator',
        'delete_terms' => 'Administrator',
      ),
      'types'  => array()
    );

    private $all_taxonomies = array();

    private static $instance;
    public static function instance() {
      if ( ! isset( self::$instance ) ) {
        self::$instance = new MJ_Taxonomy;
        self::$instance->setup();
        print_r('instance set up ');
        print_r(self::$instance);
      }
      return self::$instance;
    }

    public function add_media_taxonomy ($post_type) {
      $this->media_taxonomy['types'][] = $post_type;
    }
    public function add_tag_taxonomy ($post_type) {
      $this->tag_taxonomy['types'][] = $post_type;
    }
    public function add_section_taxonomy ($post_type) {
      $this->section_taxonomy['types'][] = $post_type;
    }
    public function add_mj_taxonomies ($post_type) {
      $this->media_taxonomy['types'][] = $post_type;
      $this->tag_taxonomy['types'][] = $post_type;
      $this->section_taxonomy['types'][] = $post_type;
    }


    public function setup() {
      add_action( 'init', array( $this, 'register' ) );
      $this->all_taxonomies['media_type'] = $this->media_taxonomy;
      $this->all_taxonomies['primary_tag'] = $this->tag_taxonomy;
      $this->all_taxonomies['section'] = $this->section_taxonomy;
    }

    public function register() {
      foreach ( $this->all_taxonomies as $taxonomy => $args ) {
        $plural = $args['plural'];
        $singular = $args['singular'];
        register_taxonomy( $taxonomy, $args['types'], array(
          'capabilites' => $args['capabilities'],
          'labels' => array(
            'name'                       => $plural,
            'singular_name'              => $singular,
            'search_items'               => 'Search ' . $plural,
            'popular_items'              => 'Popular ' . $plural,
            'all_items'                  => 'All ' . $plural,
            'parent_item'                => 'Parent ' . $singular,
            'parent_item_colon'          => "Parent {$singular}:",
            'edit_item'                  => 'Edit ' . $singular,
            'update_item'                => 'Update ' . $singular,
            'add_new_item'               => 'Add New ' . $singular,
            'new_item_name'              => "New {$singular} Name",
            'separate_items_with_commas' => "Separate {$plural} with commas",
            'add_or_remove_items'        => "Add or remove {$plural}",
            'choose_from_most_used'      => "Choose from the most used {$plural}",
            'not_found'                  => "No {$plural} found.",
            'menu_name'                  => $plural
          )
        ) );
      }
    }
    
  }

  function MJ_Taxonomy() {
    return MJ_Taxonomy::instance();
  }
} 

?>
