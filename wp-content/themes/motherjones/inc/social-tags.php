<?php
/**
 * Place meta tags for integration w/ social networking sites
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */


class MJ_social_tags {
  /**
   * Places an action to print meta tags at head creation time
   */
	function __construct() {
		add_action( 'wp_head', array( &$this, 'place_social_meta_tags'  ) );
	}

  CONST FB_SOCIAL_FIELDS = Array(
    'url' => 'og:url',
    'content_type' => 'og:type',
    'title' => 'og:title',
    'dek' => 'og:description',
    'image' => 'og:image',
    'first_name' => 'profile:first_name',
    'last_name' => 'profile:last_name',
    'user_name' => 'profile:username',
    'published' => 'article:published',
    'modified' => 'article:modified',
    'author' => 'article:author',
    'category' => 'article:section',
    'tag' => 'article:tag',
  );
  CONST FB_STATIC_TAGS = Array( 
    'og:site_name' => 'Mother Jones',
    'fb:admins' => '13301307,670317733,1198876232,38509772,106892,2907757,306289,602422192,4101301,513450539',
  );

  CONST TWITTER_SOCIAL_FIELDS = Array(
    'url' => 'url',
    'title' => 'title',
    'dek' => 'description',
    'image' => 'image',
  );
  CONST TWITTER_STATIC_TAGS = Array(
    'site' => '@Motherjones',
    'card' => 'summary_large_image',
  );

  /**
   * Determine which function to use to get the social data,
   * then write the facebook and twitter tags with it
   */
  public function place_social_meta_tags() {
    if ( is_author() ) {
      $social_information = $this->get_social_data_for_authors();
    } elseif ( is_single() ) {
      $social_information = $this->get_social_data_for_stories();
    } elseif ( is_archive() ) {
      $social_information = $this->get_social_data_for_indexes();
    } elseif ( is_home() ) {     
      $social_information = $this->get_social_data_for_the_homepage();
    } else {
      return;
    }
    $this->write_facebook_tags($social_information);
    $this->write_twitter_tags($social_information);
  }

  /**
   * Get the information that will be used to create the social meta tags
   * on author pages
   */
  private function get_social_data_for_authors() {
    $author = get_queried_object();
    $author_social_data = Array();
    $author_social_data []= Array('url', largo_get_current_url());
    $author_social_data []= Array('content_type', 'profile');
    $author_social_data []= Array('title', $author->display_name); 
    $author_social_data []= Array('first_name', $author->user_firstname);
    $author_social_data []= Array('last_name', $author->user_lastname);
    $author_social_data []= Array('user_name', $author->display_name);
    $author_social_data []= Array('image',
      wp_get_attachment_image_src( $author->mj_author_image_id )[0]);
    return $author_social_data;
  }

  /**
   * Get the information that will be used to create the social meta tags
   * for posts and pages
   */
  private function get_social_data_for_stories() {
    $story_social_data = Array();
    $story_social_data []= Array('url', largo_get_current_url());
    $story_social_data []= Array('content_type', 'article');
    $story_social_data []= Array('title', $meta['mj_social_hed'][0] 
      ?: get_the_title());
    $story_social_data []= Array('dek', $meta['mj_social_dek'][0] 
      ?: $meta['mj_dek'][0]);
    $story_social_data []= Array('image', 
      get_the_post_thumbnail_url(null, 'social_card') 
      ?: (has_term('kevin-drum', 'blog') 
        ? get_site_url() . '/themes/motherjones/img/drum_1024.jpg'
        : get_site_url() . '/themes/motherjones/img/mojo_nomaster.jpg')
    );

    $story_social_data []= Array('published', get_the_date('c')); //should be ISO 8601
    $story_social_data []= Array('modified', get_the_modified_date('c'));
    $story_social_data []= Array('category', get_the_category()[0]->name);

    $terms = get_the_terms(get_the_ID(), 'post_tag') ?: Array();
    foreach ($terms as $term) {
      $story_social_data []= Array('tag', $term->name);
    }

    if ( function_exists( 'get_coauthors' ) ) {
      $authors = get_coauthors( get_queried_object_id() );
    } else {
      $authors = array( get_user_by( 'id', get_queried_object()->post_author ) );
    }
    foreach ($authors as $author) {
      $story_social_data []= Array('author', $author->display_name);
    }
    return $story_social_data;
  }

  /**
   * Get the information that will be used to create the social meta tags
   * for index pages, which includes tag pages, category pages, and blog indexes
   */
  private function get_social_data_for_indexes() {
    $index_social_data = Array();
    $index_social_data []= Array('url', largo_get_current_url());
    $index_social_data []= Array('title', 'Mother Jones: ' . get_queried_object()->name);
    $index_social_data []= Array('content_type', 'webpage');
    $index_social_data []= Array('image', 
      get_site_url() . '/themes/motherjones/img/mojo_nomaster.jpg');
    return $index_social_data;
  }

  /**
   * Get the information that will be used to create the social meta tags
   * for the homepage
   */
  private function get_social_data_for_the_homepage() {
    $homepage_social_data = Array();
    $homepage_social_data []= Array('url', largo_get_current_url());
    $homepage_social_data []= Array('title', "Mother Jones Magazine");
    $homepage_social_data []= Array('content_type', 'webpage');
    $homepage_social_data []= Array('image', 
      get_site_url() . '/themes/motherjones/img/mojo_nomaster.jpg');
    return $homepage_social_data;
  }

  /**
   * writes the meta tags for facebook from the data collected earlier and 
   * the constants
   */
  private function write_facebook_tags($social_data) {
    foreach ( $social_data as $value ) {
      if (!array_key_exists($value[0], self::FB_SOCIAL_FIELDS)) { continue; }
      printf("<meta property='%s' content='%s'/>\n", 
        self::FB_SOCIAL_FIELDS[$value[0]], $value[1]);
    }
    foreach( self::FB_STATIC_TAGS as $property => $content ) {
      printf("<meta property='%s' content='%s'/>\n", $property, $content);
    }
  }

  /**
   * writes the meta tags for twitter from the collected data and 
   * the constants
   */
  private function write_twitter_tags($social_data) {
    foreach ( $social_data as $value) {
      if (!array_key_exists($value[0], self::TWITTER_SOCIAL_FIELDS)) { continue; }
      printf("<meta property='twitter:%s' content='%s'/>\n", 
        self::TWITTER_SOCIAL_FIELDS[$value[0]], $value[1]);
    }
    foreach( self::TWITTER_STATIC_TAGS as $property => $content ) {
      printf("<meta property='twitter:%s' content='%s'/>\n", $property, $content);
    }
  }
}
new MJ_social_tags;
