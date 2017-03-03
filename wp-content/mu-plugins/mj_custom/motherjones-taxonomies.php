<?php

if ( !class_exists( 'MJ_Taxonomy' ) ) {

  class MJ_Taxonomy {

    private $media_type_id = 'mj_media_type';
    private $media_type_terms = array(
      'Longreads',
      'Interview',
      'Interactives',
      'Calculators',
      'Cards',
      'Full Width',
      'Podcasts',
      'Video',
      'Quizzes',
      'Maps',
      'Photo Essays',
      'Slideshows',
      'Charts',
      'Cartoon',
    );
    private $media_taxonomy = array(
      'single' => 'Media Type',
      'plural' => 'Media Types',
      'capabilites' => array(
        'assign_terms' => 'edit_posts',
        'edit_terms' => 'update_core',
        'delete_terms' => 'update_core',
      ),
      'types'  => array('post')
    );

    private $tag_id = 'mj_primary_tag';
    private $tag_taxonomy = array(
      'single' => 'Primary Tag',
      'plural' => 'Primary Tags',
      'capabilites' => array(
        'assign_terms' => 'edit_posts',
        'edit_terms' => 'update_core',
        'delete_terms' => 'update_core',
      ),
      'types'  => array('post')
    );

    private $blog_id = 'mj_blog_type';
    private $blog_taxonomy = array(
      'single' => 'Blog Type',
      'plural' => 'Blog Types',
      'hierarchical' => true,
      'capabilites' => array(
        'assign_terms' => 'edit_posts',
        'edit_terms' => 'update_core',
        'delete_terms' => 'update_core',
      ),
      'types'  => array('post')
    );
    private $blog_terms = array(
      'Kevin Drum',
    );

    private $article_type_id = 'mj_article_type';
    private $article_type_taxonomy = array(
      'single' => 'Article Type',
      'plural' => 'Article Types',
      'hierarchical' => false,
      'show_admin_column' => true,
      'meta_box_cb' => 'mj_article_type_meta_box',
      'capabilites' => array(
        'assign_terms' => 'edit_posts',
        'edit_terms' => 'update_core',
        'delete_terms' => 'update_core',
      ),
      'types'  => array('post')
    );
    private $article_type_terms = array(
      'full_width_article',
      'article',
      'blogpost',
    );


    private $all_taxonomies = array();

    private static $instance;
    public static function instance() {
      if ( ! isset( self::$instance ) ) {
        self::$instance = new MJ_Taxonomy;
        self::$instance->setup();
      }
      return self::$instance;
    }

    public function add_media_taxonomy ($post_type) {
      $this->media_taxonomy['types'][] = $post_type;
    }
    public function add_tag_taxonomy ($post_type) {
      $this->tag_taxonomy['types'][] = $post_type;
    }
    public function add_blog_taxonomy ($post_type) {
      $this->blog_taxonomy['types'][] = $post_type;
    }
    public function add_article_type_taxonomy ($post_type) {
      $this->article_type_taxonomy['types'][] = $post_type;
    }
    public function add_mj_taxonomies ($post_type) {
      $this->media_taxonomy['types'][] = $post_type;
      $this->tag_taxonomy['types'][] = $post_type;
      $this->blog_taxonomy['types'][] = $post_type;
      $this->article_type_taxonomy['types'][] = $post_type;
    }


    public function setup() {
      add_action( 'init', array( $this, 'register' ) );
      $this->all_taxonomies[$this->media_type_id] = $this->media_taxonomy;
      $this->all_taxonomies[$this->tag_id] = $this->tag_taxonomy;
      $this->all_taxonomies[$this->blog_id] = $this->blog_taxonomy;
      $this->all_taxonomies[$this->article_type_id] = $this->article_type_taxonomy;
      add_action( 'created_mj_media_type', array($this, 'fill_media_type') );
      add_action( 'created_mj_blog_type', array($this, 'fill_blog_type') );
      add_action( 'created_mj_article_type', array($this, 'fill_article_type') );
    }

    public function fill_media_type() {
      foreach ( $this->media_type_terms as $i => $term ) {
        wp_insert_term($term, $this->media_type_id);
      }
    }
    public function fill_blog_type() {
      foreach ( $this->blog_terms as $i => $term ) {
        wp_insert_term($term, $this->blog_type_id);
      }
    }
    public function fill_article_type() {
      foreach ( $this->article_type_terms as $i => $term ) {
        wp_insert_term($term, $this->article_type_id);
      }
    }

    public function register() {
      foreach ( $this->all_taxonomies as $taxonomy => $args ) {
        $plural = isset( $args['plural'] ) ? $args['plural'] : '';
        $singular = isset( $args['singular'] ) ? $args['singular'] : '';
        $types = isset( $args['types'] ) ? $args['types'] : null;
        $tax_args = array(
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
        );
        if ( isset( $args['capabilites'] ) ) {
          $tax_args['capabilites'] = $args['capabilites'];
        }
        if ( isset( $args['hierarchical'] ) ) {
          $tax_args['hierarchical'] = $args['hierarchical'];
        }
        if ( isset( $args['show_ui'] ) ) {
          $tax_args['show_ui'] = $args['show_ui'];
        }
        if ( isset( $args['show_admin_column'] ) ) {
          $tax_args['show_admin_column'] = $args['show_admin_column'];
        }
        if ( isset( $args['meta_box_cb'] ) ) {
          $tax_args['meta_box_cb'] = $args['meta_box_cb'];
        }
        register_taxonomy( $taxonomy, $types, $tax_args );
      }
    }

  }

  function MJ_Taxonomy() {
    return MJ_Taxonomy::instance();
  }
}

?>
