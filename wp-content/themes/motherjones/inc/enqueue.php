<?php
/**
 * Enqueue all the scripts, styles, etc.
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

if ( ! function_exists( 'mj_enqueue' ) ) {
	/**
	 * Front end styles.
	 */
	function mj_enqueue() {
		$suffix = (MJ_DEBUG) ? '' : '.min';
		$version = '1.0';

		// Theme stylesheet.
		wp_enqueue_style(
			'mj-style',
			get_template_directory_uri() . '/css/style' . $suffix . '.css',
			null,
			$version
		);

		// Icons.
		wp_enqueue_style(
			'font-awesome',
			get_template_directory_uri() . '/css/font-awesome-4.6.3/css/font-awesome' . $suffix . '.css',
			null,
			$version
		);

		wp_enqueue_script(
			'mj-html5',
			get_template_directory_uri() . '/js/html5' . $suffix . '.js',
			array(),
			'3.7.3'
		);
		wp_script_add_data( 'mj-html5', 'conditional', 'lt IE 9' );

		wp_enqueue_script(
			'mj-skip-link-focus-fix',
			get_template_directory_uri() . '/js/skip-link-focus-fix' . $suffix . '.js',
			array(),
			$version,
			true
		);

		if ( is_singular() && wp_attachment_is_image() ) {
			wp_enqueue_script(
				'mj-keyboard-image-navigation',
				get_template_directory_uri() . '/js/keyboard-image-navigation' . $suffix . '.js',
				array( 'jquery' ),
				$version
			);
		}

		wp_enqueue_script(
			'mj-script',
			get_template_directory_uri() . '/js/functions' . $suffix . '.js',
			array( 'jquery' ),
			$version,
			true
		);
		wp_enqueue_script(
			'nav',
			get_template_directory_uri() . '/js/nav' . $suffix . '.js',
			array( 'jquery' ),
			$version,
			true
		);
		wp_enqueue_script(
			'video-embed',
			get_template_directory_uri() . '/js/video-embed' . $suffix . '.js',
			array( 'jquery' ),
			$version,
			true
		);
		wp_enqueue_script(
			'ad_code',
			get_template_directory_uri() . '/js/ad_code' . $suffix . '.js',
			array( 'jquery' ),
			$version
		);
		wp_localize_script( 'mj-script', 'screenReaderText', array(
			'expand'   => __( 'expand child menu', 'mj' ),
			'collapse' => __( 'collapse child menu', 'mj' ),
		) );

		// Slideshows on single posts only.
		if ( is_single() ) {
			$slick_css = get_template_directory_uri() . '/vendor/largo/slideshows/vendor/slick/slick.css';
			wp_enqueue_style( 'mj_slick', $slick_css, array(), '1.0' );

			$slides_src = get_template_directory_uri() . '/vendor/largo/slideshows/vendor/slick/slick.min.js';
			wp_enqueue_script( 'mj_jquery-slick', $slides_src, array( 'jquery' ), '3.0', true );

			$slides_css = get_template_directory_uri() . '/vendor/largo/slideshows/css/slides.css';
			wp_enqueue_style( 'mj_slides', $slides_css, array(), '1.0' );

			$show_src = get_template_directory_uri() . '/vendor/largo/slideshows/js/navis-slideshows.js';
			wp_enqueue_script( 'mj_slideshows', $show_src, array( 'jquery-slick' ), '0.11', true );
		}

	}
}
add_action( 'wp_enqueue_scripts', 'mj_enqueue' );

/**
 * Enqueue admin JS and styles.
 *
 * @param string $hook the admin page we're viewing.
 */
function mj_admin_style( $hook ) {
	$suffix = (MJ_DEBUG) ? '' : '.min';
	$version = '1.0';
	if ( ( 'post.php' === $hook ) || ( 'post-new.php' === $hook ) ) {
		wp_enqueue_style(
			'mj-post-admin-style',
			get_template_directory_uri() . '/css/admin/post-admin' . $suffix . '.css',
			false,
			'1.0'
		);
	}
}
add_action( 'admin_enqueue_scripts', 'mj_admin_style' );
/**
 * Handles JavaScript detection.
 *
 * Adds a `js` class to the root `<html>` element when JavaScript is detected.
 */
function mj_javascript_detection() {
	echo "<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>\n";
}
add_action( 'wp_head', 'mj_javascript_detection', 0 );
