<?php
/**
 * @package Mother Jones Article Content Types
 * @version 0.1
 */

/**
 * Example:
 *
 * function custom_post_type() {
 *
 *   $labels = array(
 *     'name'                  => _x( 'Post Types', 'Post Type General Name', 'text_domain' ),
 *     'singular_name'         => _x( 'Post Type', 'Post Type Singular Name', 'text_domain' ),
 *     'menu_name'             => __( 'Post Types', 'text_domain' ),
 *     'name_admin_bar'        => __( 'Post Type', 'text_domain' ),
 *     'archives'              => __( 'Item Archives', 'text_domain' ),
 *     'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
 *     'all_items'             => __( 'All Items', 'text_domain' ),
 *     'add_new_item'          => __( 'Add New Item', 'text_domain' ),
 *     'add_new'               => __( 'Add New', 'text_domain' ),
 *     'new_item'              => __( 'New Item', 'text_domain' ),
 *     'edit_item'             => __( 'Edit Item', 'text_domain' ),
 *     'update_item'           => __( 'Update Item', 'text_domain' ),
 *     'view_item'             => __( 'View Item', 'text_domain' ),
 *     'search_items'          => __( 'Search Item', 'text_domain' ),
 *     'not_found'             => __( 'Not found', 'text_domain' ),
 *     'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
 *     'featured_image'        => __( 'Featured Image', 'text_domain' ),
 *     'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
 *     'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
 *     'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
 *     'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
 *     'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
 *     'items_list'            => __( 'Items list', 'text_domain' ),
 *     'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
 *     'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
 *   );
 *   $args = array(
 *     'label'                 => __( 'Post Type', 'text_domain' ),
 *     'description'           => __( 'Post Type Description', 'text_domain' ),
 *     'labels'                => $labels,
 *     'supports'              => array( ),
 *     'taxonomies'            => array( 'category', 'post_tag' ),
 *     'hierarchical'          => false,
 *     'public'                => true,
 *     'show_ui'               => true,
 *     'show_in_menu'          => true,
 *     'menu_position'         => 5,
 *     'show_in_admin_bar'     => true,
 *     'show_in_nav_menus'     => true,
 *     'can_export'            => true,
 *     'has_archive'           => true,
 *     'exclude_from_search'   => false,
 *     'publicly_queryable'    => true,
 *     'capability_type'       => 'page',
 *   );
 *   register_post_type( 'post_type', $args );
 *
 * }
 * add_action( 'init', 'custom_post_type', 0 );
 *
 */

if ( !class_exists( 'MJ_Custom_Types' ) ) {

  require_once('motherjones-content-fields.php');
  require_once('motherjones-taxonomies.php');

  class MJ_Custom_Types {

    private static $sectionable_types = [];
    private static $instance;
    public static function instance() {
      if ( ! isset( self::$instance ) ) {
        self::$instance = new MJ_Custom_Types;
        self::$instance->setup();
      }
      return self::$instance;
    }


    public function setup() {
      $this->taxonomies = MJ_Taxonomy();
      $this->fields = MJ_Custom_Fields();
      //self::create_full_width_type();
      //self::create_article_type();
      //self::create_blog_post_type();
      self::create_author_type();
      add_filter('pre_get_posts', array($this, 'set_index_query') );
    }

    public function create_full_width_type() {
      $type = 'mj_full_width';
      $this->taxonomies->add_mj_taxonomies($type);
      add_action( 'init', array( $this, 'full_width_type' ) );
      add_action( 'fm_post_'.$type, array( $this, full_width_fields)  );
      $this->sectionable_types[] = $type;
    }

    public function create_article_type() {
      $type = 'mj_article';
      $this->taxonomies->add_mj_taxonomies($type);
      add_action( 'init', array( $this, 'article_type' ) );
      add_action( 'fm_post_'.$type, array( $this, article_fields ) );
      $this->sectionable_types[] = $type;
    }

    public function create_blog_post_type() {
      $type = 'mj_blog_post';
      $this->taxonomies->add_mj_taxonomies($type);
      add_action( 'init', array( $this, 'blog_post_type' ) );
      add_action( 'fm_post_'.$type, array( $this, blog_post_fields ) );
      //$this->sectionable_types[] = $type; No blog posts in sections/tags/etc
    }

    public function create_author_type() {
      add_action( 'fm_user', array( $this, author_fields ) );
    }

    public function full_width_fields() {
      MJ_Custom_Fields::title_image()->add_meta_box( 'Title Image', 'mj_full_width' );
      MJ_Custom_Fields::dek()->add_meta_box( 'Dek', 'mj_full_width' );
      MJ_Custom_Fields::social()->add_meta_box( 'Social Titles', 'mj_full_width' );
      MJ_Custom_Fields::alt()->add_meta_box( 'Alt Titles', 'mj_full_width' );
      MJ_Custom_Fields::master_image()->add_meta_box( 'Master Image', 'mj_full_width' );
      MJ_Custom_Fields::byline()->add_meta_box( 'Byline Override', 'mj_full_width' );
      MJ_Custom_Fields::body()->add_meta_box( 'Article Body', 'mj_full_width' );
      MJ_Custom_Fields::related()->add_meta_box( 'Related Articles', 'mj_full_width' );
      MJ_Custom_Fields::css_js()->add_meta_box( 'Extra CSS & JS', 'mj_full_width' );
      MJ_Custom_Fields::file_attachments()->add_meta_box( 'Extra File Attachments', 'mj_full_width' );
      MJ_Custom_Fields::dateline_override()->add_meta_box( 'Issue Date', 'mj_full_width' );
    }
    public function full_width_type() {
      register_post_type( 'mj_full_width',
        array(
          'labels' => array(
            'name' => __( 'Full Widths' ),
            'singular_name' => __( 'Full Width' )
          ),
          'taxonomies' => array('category', 'mj_media_type', 'mj_primary_tag'),

          'rewrite' => false,
          'query_arg' => true,
          'public' => true,
          'supports' => array('title', 'zoninator_zones', 'author'),
          'has_archive' => true
        )
      );
			global $wp_rewrite;
      $article_url_structure = '/%category%/%year%/%monthnum%/%mj_full_width%';
      $wp_rewrite->add_rewrite_tag("%mj_full_width%", '([^/]+)', "mj_full_width=");
      $wp_rewrite->add_permastruct('mj_full_width', $article_url_structure, false);
    }


    public function article_type() {
      register_post_type( 'mj_article',
        array(
          'labels' => array(
            'name' => __( 'Articles' ),
            'singular_name' => __( 'Article' )
          ),
          'taxonomies' => array('category', 'mj_media_type', 'mj_primary_tag'),
          'rewrite' => false,
          'query_arg' => true,
          'public' => true,
          'supports' => array('title', 'zoninator_zones', 'author'),
          'has_archive' => true
        )
      );

			global $wp_rewrite;
      $article_url_structure = '/%category%/%year%/%monthnum%/%mj_article%';
      $wp_rewrite->add_rewrite_tag("%mj_article%", '([^/]+)', "mj_article=");
      $wp_rewrite->add_permastruct('mj_article', $article_url_structure, false);
    }
    public function article_fields() {
      MJ_Custom_Fields::dek()->add_meta_box( 'Dek', 'mj_article' );
      MJ_Custom_Fields::social()->add_meta_box( 'Social Titles', 'mj_article' );
      MJ_Custom_Fields::alt()->add_meta_box( 'Alt Titles', 'mj_article' );
      MJ_Custom_Fields::master_image()->add_meta_box( 'Master Image', 'mj_article' );
      MJ_Custom_Fields::byline()->add_meta_box( 'Byline Override', 'mj_article' );
      MJ_Custom_Fields::body()->add_meta_box( 'Article Body', 'mj_article' );
      MJ_Custom_Fields::related()->add_meta_box( 'Related Articles', 'mj_article' );
      MJ_Custom_Fields::css_js()->add_meta_box( 'Extra CSS & JS', 'mj_article' );
      MJ_Custom_Fields::file_attachments()->add_meta_box( 'Extra File Attachments', 'mj_article' );
      MJ_Custom_Fields::dateline_override()->add_meta_box( 'Issue Date', 'mj_article' );
    }


    public function blog_post_type() {
      register_post_type( 'mj_blog_post',
        array(
          'labels' => array(
            'name' => __( 'Blog Posts' ),
            'singular_name' => __( 'Blog Post' )
          ),
          'taxonomies' => array('mj_blog_type', 'mj_media_type', 'mj_primary_tag'),
          'public' => true,
          'rewrite' => false,
          'query_arg' => true,
          'supports' => array('title', 'zoninator_zones', 'author'),
          'has_archive' => true
        )
      );

			global $wp_rewrite;
      $blog_url_structure = '/%mj_blog_type%/%year%/%monthnum%/%mj_blog_post%';
      $wp_rewrite->add_rewrite_tag("%mj_blog_post%", '([^/]+)', "mj_blog_post=");
      $wp_rewrite->add_permastruct('mj_blog_post', $blog_url_structure, false);
    }
    public function blog_post_fields() {
      MJ_Custom_Fields::dek()->add_meta_box( 'Dek', 'mj_blog_post' );
      MJ_Custom_Fields::social()->add_meta_box( 'Social Titles', 'mj_blog_post' );
      MJ_Custom_Fields::alt()->add_meta_box( 'Alt Titles', 'mj_blog_post' );
      MJ_Custom_Fields::master_image()->add_meta_box( 'Master Image', 'mj_blog_post' );
      MJ_Custom_Fields::byline()->add_meta_box( 'Byline Override', 'mj_blog_post' );
      MJ_Custom_Fields::body()->add_meta_box( 'Article Body', 'mj_blog_post' );
      MJ_Custom_Fields::css_js()->add_meta_box( 'Extra CSS & JS', 'mj_blog_post' );
      MJ_Custom_Fields::file_attachments()->add_meta_box( 'Extra File Attachments', 'mj_blog_post' );
      MJ_Custom_Fields::dateline_override()->add_meta_box( 'Issue Date', 'mj_blog_post' );

    }
    public function author_fields() {
      MJ_Custom_Fields::position()->add_user_form( 'Position', 'mj_author' );
      MJ_Custom_Fields::image()->add_user_form( 'Author Photo', 'mj_author' );
      MJ_Custom_Fields::long_bio()->add_user_form( 'Long Bio', 'mj_author' );
      MJ_Custom_Fields::short_bio()->add_user_form( 'End of Article Bio', 'mj_author' );
      MJ_Custom_Fields::twitter()->add_user_form( 'Twitter User', 'mj_author' );
    }


    public function set_index_query( $query ) {
      if(is_category() || is_tag() || is_tax()) {
        $post_type = get_query_var('post_type');
        if(!$post_type) {
          $query->set('post_type', $this->sectionable_types);
        }
        return $query;
      }
    }


  }
}

function MJ_Custom_Types() {
  return MJ_Custom_Types::instance();
}

?>
