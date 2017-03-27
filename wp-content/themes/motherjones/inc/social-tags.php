<?php
/**
 * Place meta tags for integration w/ social networking sites
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */


class MJ_social_tags {
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

  private $social_information = Array();

  public function place_social_meta_tags() {
    if ( is_author() ) {
      $this->set_social_data_for_authors();
    } elseif ( is_single() ) {
      $this->set_social_data_for_stories();
    } elseif ( is_archive() ) {
      $this->set_social_data_for_indexes();
    } elseif ( is_home() ) {     
      $this->set_social_data_for_the_homepage();
    } else {
      return;
    }
    $this->write_facebook_tags();
    $this->write_twitter_tags();
  }

  private function set_social_data_for_authors() {
    $author = get_queried_object();
    $this->social_information []= Array('url', largo_get_current_url());
    $this->social_information []= Array('content_type', 'profile');
    $this->social_information []= Array('title', $author->display_name); 
    $this->social_information []= Array('first_name', $author->user_firstname);
    $this->social_information []= Array('last_name', $author->user_lastname);
    $this->social_information []= Array('user_name', $author->display_name);
    $this->social_information []= Array('image',
      wp_get_attachment_image_src( $author->mj_author_image_id )[0]);

  }

  private function set_social_data_for_stories() {
    $this->social_information []= Array('url', largo_get_current_url());
    $this->social_information []= Array('content_type', 'article');
    $this->social_information []= Array('title', $meta['mj_social_hed'][0] 
      ?: get_the_title());
    $this->social_information []= Array('dek', $meta['mj_social_dek'][0] 
      ?: $meta['mj_dek'][0]);
    $this->social_information []= Array('image', 
      get_the_post_thumbnail_url(null, 'social_card') 
      ?: (has_term('kevin-drum', 'blog') 
        ? get_site_url() .'/themes/motherjones/img/drum_1024.jpg'
        : get_site_url() .'/themes/motherjones/img/mojo_nomaster.jpg')
    );

    $this->social_information []= Array('published', get_the_date('c')); //should be ISO 8601
    $this->social_information []= Array('modified', get_the_modified_date('c'));
    $this->social_information []= Array('category', get_the_category()[0]->name);

    $terms = get_the_terms(get_the_ID(), 'post_tag');
    foreach ($terms as $term) {
      $this->social_information []= Array('tag', $term->name);
    }

    if ( function_exists( 'get_coauthors' ) ) {
      $authors = get_coauthors( get_queried_object_id() );
    } else {
      $authors = array( get_user_by( 'id', get_queried_object()->post_author ) );
    }
    foreach ($authors as $author) {
      $this->social_information []= Array('author', $author->display_name);
    }
  }

  private function set_social_data_for_indexes() {
    $this->social_information []= Array('url', largo_get_current_url());
    $this->social_information []= Array('title', 'Mother Jones: ' . get_queried_object()->name);
    $this->social_information []= Array('content_type', 'webpage');
    $this->social_information []= Array('image', get_site_url() .'/themes/motherjones/img/mojo_nomaster.jpg');
  }

  private function set_social_data_for_the_homepage() {
    $this->social_information []= Array('url', largo_get_current_url());
    $this->social_information []= Array('title', "Mother Jones Magazine");
    $this->social_information []= Array('content_type', 'webpage');
    $this->social_information []= Array('image', get_site_url() .'/themes/motherjones/img/mojo_nomaster.jpg');
  }

  private function write_facebook_tags() {
    foreach ( $this->social_information as $value ) {
      if (!array_key_exists($value[0], self::FB_SOCIAL_FIELDS)) { continue; }
      printf("<meta property='%s' content='%s'/>\n", 
        self::FB_SOCIAL_FIELDS[$value[0]], $value[1]);
    }
    foreach( self::FB_STATIC_TAGS as $property => $content ) {
      printf("<meta property='%s' content='%s'/>\n", $property, $content);
    }
  }

  private function write_twitter_tags() {
    foreach ( $this->social_information as $value) {
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
