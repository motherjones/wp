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
		add_action( 'wp_head', array( &$this, 'place_social_meta_tags' ) );
	}

	const FB_SOCIAL_FIELDS = array(
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
	const FB_STATIC_TAGS = array(
		'og:site_name' => 'Mother Jones',
		'fb:admins' => '13301307,670317733,1198876232,38509772,106892,2907757,306289,602422192,4101301,513450539',
	);

	const TWITTER_SOCIAL_FIELDS = array(
		'url' => 'url',
		'title' => 'title',
		'dek' => 'description',
		'image' => 'image',
	);
	const TWITTER_STATIC_TAGS = array(
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
		$this->write_facebook_tags( $social_information );
		$this->write_twitter_tags( $social_information );
		$this->write_google_tags();
	}

	/**
	 * Get the information that will be used to create the social meta tags
	 * on author pages
	 */
	private function get_social_data_for_authors() {
		$author = get_queried_object();
		$author_social_data = array();
		$author_social_data [] = array( 'url', largo_get_current_url() );
		$author_social_data [] = array( 'content_type', 'profile' );
		$author_social_data [] = array( 'title', $author->display_name );
		$author_social_data [] = array( 'first_name', $author->user_firstname );
		$author_social_data [] = array( 'last_name', $author->user_lastname );
		$author_social_data [] = array( 'user_name', $author->display_name );
		$author_social_data [] = array( 'image', wp_get_attachment_image_src( $author->mj_author_image_id )[0] );
		return $author_social_data;
	}

	/**
	 * Get the information that will be used to create the social meta tags
	 * for posts and pages
	 */
	private function get_social_data_for_stories() {
		global $meta;
		$story_social_data = array();
		$story_social_data [] = array( 'url', largo_get_current_url() );
		$story_social_data [] = array( 'content_type', 'article' );
		$story_social_data [] = array(
			'title',
			isset( $meta['mj_social_hed'][0] ) ? $meta['mj_social_hed'][0] : get_the_title(),
		);
		$story_social_data [] = array(
			'dek',
			$this->which_dek(),
		);
		$story_social_data [] = array(
			'image',
			get_the_post_thumbnail_url( null, 'social_card' )
			?: ( has_term( 'kevin-drum', 'blog' )
				? get_template_directory_uri() . '/img/drum_1024.jpg'
				: get_template_directory_uri() . '/img/mojo_nomaster.jpg' ),
		);

		$story_social_data [] = array( 'published', get_the_date( 'c' ) ); // should be ISO 8601.
		$story_social_data [] = array( 'modified', get_the_modified_date( 'c' ) );
		$story_social_data [] = array( 'category', get_the_category()[0]->name );

		$terms = get_the_terms( get_the_ID(), 'post_tag' ) ?: array();
		foreach ( $terms as $term ) {
			$story_social_data [] = array( 'tag', $term->name );
		}

		if ( function_exists( 'get_coauthors' ) ) {
			$authors = get_coauthors( get_queried_object_id() );
		} else {
			$authors = array( get_user_by( 'id', get_queried_object()->post_author ) );
		}
		foreach ( $authors as $author ) {
			$story_social_data [] = array( 'author', $author->display_name );
		}
		return $story_social_data;
	}

	/**
	 * Figure out which dek to use.
	 * Use nothing if neither field is populated.
	 */
	private function which_dek() {
		global $meta;
		if ( isset( $meta['mj_social_dek'][0] ) && '' !== $meta['mj_social_dek'][0] ) {
			return $meta['mj_social_dek'][0];
		} elseif ( isset( $meta['mj_dek'][0] ) && '' !== $meta['mj_dek'][0] ) {
			return $meta['mj_dek'][0];
		} else {
			return '';
		}
	}
	/**
	 * Get the information that will be used to create the social meta tags
	 * for index pages, which includes tag pages, category pages, and blog indexes
	 */
	private function get_social_data_for_indexes() {
		$index_social_data = array();
		$index_social_data [] = array( 'url', largo_get_current_url() );
		$index_social_data [] = array( 'title', 'Mother Jones: ' . get_queried_object()->name );
		$index_social_data [] = array( 'content_type', 'webpage' );
		$index_social_data [] = array(
			'image',
			get_template_directory_uri() . '/img/mojo_nomaster.jpg',
		);
		return $index_social_data;
	}

	/**
	 * Get the information that will be used to create the social meta tags
	 * for the homepage
	 */
	private function get_social_data_for_the_homepage() {
		$homepage_social_data = array();
		$homepage_social_data [] = array( 'url', largo_get_current_url() );
		$homepage_social_data [] = array( 'title', 'Mother Jones Magazine' );
		$homepage_social_data [] = array( 'content_type', 'webpage' );
		$homepage_social_data [] = array(
			'image',
			get_template_directory_uri() . '/img/mojo_nomaster.jpg',
		);
		return $homepage_social_data;
	}

	/**
	 * Writes the meta tags for facebook from the data collected earlier and
	 * the constants
	 *
	 * @param array $social_data the array of fields to output.
	 */
	private function write_facebook_tags( $social_data ) {
		foreach ( $social_data as $value ) {
			if ( ! array_key_exists( $value[0], self::FB_SOCIAL_FIELDS ) ) {
				continue;
			}
			printf(
				"<meta property='%s' content='%s'/>\n",
				esc_attr( self::FB_SOCIAL_FIELDS[ $value[0] ] ),
				esc_attr( $value[1] )
			);
		}
		foreach ( self::FB_STATIC_TAGS as $property => $content ) {
			printf(
				"<meta property='%s' content='%s' />\n",
				esc_attr( $property ),
				esc_attr( $content )
			);
		}
	}

	/**
	 * Writes the meta tags for twitter from the collected data and
	 * the constants.
	 *
	 * @param array $social_data that array of fields to output.
	 */
	private function write_twitter_tags( $social_data ) {
		foreach ( $social_data as $value ) {
			if ( ! array_key_exists( $value[0], self::TWITTER_SOCIAL_FIELDS ) ) {
				continue;
			}
			printf(
				"<meta property='twitter:%s' content='%s' />\n",
				esc_attr( self::TWITTER_SOCIAL_FIELDS[ $value[0] ] ),
				esc_attr( $value[1] )
			);
		}
		foreach ( self::TWITTER_STATIC_TAGS as $property => $content ) {
			printf(
				"<meta property='twitter:%s' content='%s' />\n",
				esc_attr( $property ),
				esc_attr( $content )
			);
		}
	}

	/**
	 * Writes the meta tags for google from the data collected earlier and
	 * the constants
	 */
	private function write_google_tags() {
		if ( ! is_single() ) {
			return;
		}
		global $meta;
		if ( ! empty( $this->get_news_keywords() ) ) {
			echo '<meta name="news_keywords" content="' . esc_attr( $this->get_news_keywords() ) . '">';
		}
		if ( isset( $meta['mj_google_standout'][0] ) ) {
			echo '<meta name="standout" content="' . esc_url( largo_get_current_url() ) . '"/>';
		}
	}

	/**
	 * Get up to 10 categories and tags to use for the goole news_keywords meta tag.
	 */
	private function get_news_keywords() {
		$cats = get_the_category();
		$tags = get_the_tags();
		$output = array();
		if ( $cats ) {
			foreach ( $cats as $cat ) {
				$output[] = $cat->name;
			}
		}
		if ( $tags ) {
			foreach ( $tags as $tag ) {
				$output[] = $tag->name;
			}
		}
		// limit to ten per google news guidelines.
		$output = implode( ', ', array_slice( $output, 0, 10 ) );
		return $output;
	}
}
new MJ_social_tags;
