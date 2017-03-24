<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://nerds.inn.org
 * @since      1.0.0
 *
 * @package    Largo_Related_Posts
 * @subpackage Largo_Related_Posts/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Largo_Related_Posts
 * @subpackage Largo_Related_Posts/public
 * @author     INN Nerds <nerds@inn.org>
 */
class Largo_Related_Posts_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Largo_Related_Posts_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Largo_Related_Posts_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/largo-related-posts.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Largo_Related_Posts_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Largo_Related_Posts_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/largo-related-posts-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register the related posts widget 
	 *
	 * @since    1.0.0
	 */
	public function related_posts_widget() {
		if ( ! class_exists( 'largo_related_posts_widget' ) ) {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-largo-related-posts-widget.php';
		}	

		if ( ! class_exists( 'Largo_Related' ) ) {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-largo-related-posts-logic.php';
		}	

		register_widget( 'largo_related_posts_widget' );

	}


}
