<?php
/**
 * Mother Jones functions and definitions
 *
 * Set up the theme and provides some helper functions, which are used in the
 * theme as custom template tags. Others are attached to action and filter
 * hooks in WordPress to change core functionality.
 *
 * When using a child theme you can override certain functions (those wrapped
 * in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before
 * the parent theme's file, so the child theme functions would be used.
 *
 * @link https://codex.wordpress.org/Theme_Development
 * @link https://codex.wordpress.org/Child_Themes
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are
 * instead attached to a filter or action hook.
 *
 * For more information on hooks, actions, and filters,
 * {@link https://codex.wordpress.org/Plugin_API}
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

/**
 * MJ_DEBUG defines whether or not to use minified assets
 *
 * By default we use minified CSS and JS files.
 * set MJ_DEBUG to TRUE to use unminified JavaScript files
 * and unminified CSS files with sourcemaps for debugging purposes.
 *
 * @since 1.0
 */
if ( ! defined( 'MJ_DEBUG' ) ) {
	define( 'MJ_DEBUG', false );
}

/**
 * A class to represent the one true MJ theme instance
 */
class MJ {
	private static $instance;
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new MJ;
			self::$instance->load();
		}
		return self::$instance;
	}
	/**
	 * Load the theme
	 */
	private function load() {
		$this->require_files();
	}
	/**
	 * Load required files
	 */
	private function require_files() {
		$includes = array(
			'/vendor/largo/largo-metabox-api.php',
			'/vendor/largo/featured-media.php',
			'/inc/archive.php',
			'/inc/editor.php',
			'/inc/enqueue.php',
			'/inc/helpers.php',
			'/inc/images.php',
			'/inc/metaboxes.php',
			'/inc/post-templates.php',
			'/inc/sidebars.php',
			'/inc/template-tags.php',
			'/inc/users.php',
		);
		foreach ( $includes as $include ) {
			require_once( get_template_directory() . $include );
		}

		// Media credit and slideshow plugins.
		if ( ! class_exists( 'Navis_Media_Credit' ) ) {
			require_once dirname( __FILE__ ) . '/vendor/largo/media-credit.php';
		}
	}
}
/**
 * Load our MJ instance
 */
function mj() {
	return MJ::get_instance();
}
add_action( 'after_setup_theme', 'mj' );

if ( ! function_exists( 'mj_setup' ) ) {
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 *
	 * Create your own mj_setup() function to override in a child theme.
	 */
	function mj_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on Mother Jones, use a find and replace
		 * to change 'mj' to the name of your theme in all the template files
		 */
		load_theme_textdomain( 'mj', get_template_directory() . '/languages' );

		// Add default posts RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
		 */
		add_theme_support( 'post-thumbnails' );

		/*
		 * Switch default core markup for search form
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'gallery',
			'caption',
		) );

		add_theme_support( 'mfi-reloaded', array(
			'mj_title_image' => array(
				'post_types' => array( 'post' ),
				'labels' => array(
					'name' => __( 'Title Image' ),
					'set' => __( 'Set title image' ),
					'remove' => __( 'Remove title image' ),
					'popup_title' => __( 'Set Title Image' ),
					'popup_select' => __( 'Set title image' ),
				),
			),
		) );

		add_filter( 'tiny_mce_before_init', 'mj_wysiwyg_config' );

		$suffix = (MJ_DEBUG) ? '' : '.min';
		add_editor_style( 'css/admin/editor-style' . $suffix . '.css' );

	}
} // mj_setup
add_action( 'after_setup_theme', 'mj_setup' );

/**
 * Sets the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 *
 * @since Mother Jones 1.0
 */
function mj_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'mj_content_width', 840 );
}
add_action( 'after_setup_theme', 'mj_content_width', 0 );

function mj_set_posts_per_page($query) {
	if ( array_key_exists('blog', $query->query) ) {
    $query->set( 'posts_per_page', '10' );
	}
}
add_action( 'pre_get_posts', 'mj_set_posts_per_page' );
