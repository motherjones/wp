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
		add_action( 'wp_head', array( &$this, 'place_social_meta_tags' ) );
	}

	const FB_SOCIAL_FIELDS = Array(
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
	const FB_STATIC_TAGS = Array(
		'og:site_name' => 'Mother Jones',
		'fb:admins' => '13301307,670317733,1198876232,38509772,106892,2907757,306289,602422192,4101301,513450539',
	);

	const TWITTER_SOCIAL_FIELDS = Array(
		'url' => 'url',
		'title' => 'title',
		'dek' => 'description',
		'image' => 'image',
	);
	const TWITTER_STATIC_TAGS = Array(
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
		$this->social_information['url'] = largo_get_current_url();
		$this->social_information['content_type'] = 'profile';
		$this->social_information['title'] = $author->display_name;
		$this->social_information['first_name'] = $author->user_firstname;
		$this->social_information['last_name'] = $author->user_lastname;
		$this->social_information['user_name'] = $author->display_name;
		$this->social_information['image']
			= wp_get_attachment_image_src( $author->mj_author_image_id )[0];

	}

	private function set_social_data_for_stories() {
		$this->social_information['url'] = largo_get_current_url();
		$this->social_information['content_type'] = 'article';
		$this->social_information['title'] = $meta['mj_social_hed'][0]
			?: get_the_title();
		$this->social_information['dek'] = $meta['mj_social_dek'][0]
			?: $meta['mj_dek'][0];
		$this->social_information['image'] =
			get_the_post_thumbnail_url( null, 'social_card' )
			?: ( has_term( 'kevin-drum', 'blog' )
				? get_template_directory_uri() . '/img/drum_1024.jpg'
				: get_template_directory_uri() . '/img/mojo_nomaster.jpg' );

		$this->social_information['published'] = get_the_date( 'c' ); // should be ISO 8601.
		$this->social_information['modified'] = get_the_modified_date( 'c' );
		$this->social_information['category'] = get_the_category()[0]->name;

		$terms = wp_get_post_terms( get_the_ID(), 'post_tag', array( 'fields' => 'names' ) );
		foreach ( $terms as $term ) {
			$this->social_information['tag'] = $term;
		}

		if ( function_exists( 'get_coauthors' ) ) {
			$authors = get_coauthors( get_queried_object_id() );
		} else {
			$authors = array( get_user_by( 'id', get_queried_object()->post_author ) );
		}
		foreach ( $authors as $author ) {
			$this->social_information['author'] = $author->display_name;
		}
	}

	private function set_social_data_for_indexes() {
		$this->social_information['url'] = largo_get_current_url();
		$this->social_information['title'] = 'Mother Jones: ' . get_queried_object()->name;
		$this->social_information['content_type'] = 'webpage';
		$this->social_information['image'] = get_template_directory_uri() . '/img/mojo_nomaster.jpg';
	}

	private function set_social_data_for_the_homepage() {
		$this->social_information['url'] = largo_get_current_url();
		$this->social_information['title'] = 'Mother Jones Magazine';
		$this->social_information['content_type'] = 'webpage';
		$this->social_information['image'] = get_template_directory_uri() . '/img/mojo_nomaster.jpg';
	}

	private function write_facebook_tags() {
		foreach ( $this->social_information as $property => $value ) {
			if ( ! array_key_exists( $property, self::FB_SOCIAL_FIELDS ) ) {
				continue;
			}
			printf( "<meta property='%s' content='%s'/>\n",
				self::FB_SOCIAL_FIELDS[ $property ], esc_attr( $value ) );
		}
		foreach ( self::FB_STATIC_TAGS as $property => $content ) {
			printf( "<meta property='%s' content='%s'/>\n", esc_attr( $property ), esc_attr( $content ) );
		}
	}

	private function write_twitter_tags() {
		foreach ( $this->social_information as $property => $value) {
			if ( ! array_key_exists( $property, self::TWITTER_SOCIAL_FIELDS ) ) {
				continue;
			}
			printf( "<meta property='twitter:%s' content='%s'/>\n",
				self::TWITTER_SOCIAL_FIELDS[ $property ], esc_attr( $value ) );
		}
		foreach ( self::TWITTER_STATIC_TAGS as $property => $content ) {
			printf( "<meta property='twitter:%s' content='%s'/>\n", esc_attr( $property ), esc_attr( $content ) );
		}
	}
}
new MJ_social_tags;
