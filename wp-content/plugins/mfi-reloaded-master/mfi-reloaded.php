<?php
/*
 Plugin Name: Multiple Featured Images: Reloaded
 Plugin URI: https://github.com/nickohrn/mfi-reloaded
 Description: Allows developers to quickly add new image pickers to any content type.
 Version: 1.0.0
 Author: Nick Ohrn of Plugin-Developer.com
 Author URI: http://plugin-developer.com/
 */

if(!class_exists('MFI_Reloaded')) {
	class MFI_Reloaded {
		/// Version
		const VERSION = '1.0.0';

		public static function init() {
			self::add_actions();
			self::add_filters();
			self::initialize_defaults();
		}

		private static function add_actions() {
			// Common actions
			add_action('wp_ajax_mfi_reloaded_set_image_id', array(__CLASS__, 'ajax_mfi_reloaded_set_image_id'));

			if(is_admin()) {
				add_action('add_meta_boxes', array(__CLASS__, 'add_image_picker_meta_boxes'));
				add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_administrative_resources'));
			} else {
				// Frontend only actions
			}
		}

		private static function add_filters() {
			// Common filters

			if(is_admin()) {
				// Administrative only filters
			} else {
				// Frontend only filters
			}
		}

		private static function initialize_defaults() {

		}

		/// AJAX Callbacks

		public static function ajax_mfi_reloaded_set_image_id() {
			$data = stripslashes_deep($_REQUEST);

			$image_id = $data['image_id'];
			$name = $data['name'];
			$post_id = $data['post_id'];

			if($post_id && current_user_can('edit_post', $post_id)) {
				$images = self::_get_meta($post_id);

				if(empty($image_id)) {
					unset($images[$name]);
				} else {
					$images[$name] = $image_id;
				}

				self::_set_meta($post_id, $images);
			}

			exit;
		}

		/// Callbacks

		public static function add_image_picker_meta_boxes($post_type) {
			$support = get_theme_support('mfi-reloaded');

			$pickers = is_array($support) && isset($support[0]) && is_array($support[0]) ? $support[0] : array();

			foreach($pickers as $image_picker_name => $image_picker_args) {
				$image_picker_args = self::_normalize_args($image_picker_args);

				if(in_array($post_type, $image_picker_args['post_types'])) {
					add_meta_box(
						'mfi-reloaded-' . sanitize_title_with_dashes($image_picker_name),
						$image_picker_args['labels']['name'],
						array(__CLASS__, 'display_image_picker_meta_box'),
						$post_type,
						'side',
						'default',
						compact('image_picker_name', 'image_picker_args')
					);
				}
			}
		}

		public static function enqueue_administrative_resources() {
			$screen = get_current_screen();

			if('post' === $screen->base) {
				wp_enqueue_media();

				wp_enqueue_script('mfi-reloaded', plugins_url('resources/backend/mfi-reloaded.js', __FILE__), array('jquery'), self::VERSION, true);
				wp_enqueue_style('mfi-reloaded', plugins_url('resources/backend/mfi-reloaded.css', __FILE__), array(), self::VERSION);
			}
		}

		/// Display callbacks

		public static function display_image_picker_meta_box($post, $meta_box) {
			$image_picker_args = $meta_box['args']['image_picker_args'];
			$image_picker_name = $meta_box['args']['image_picker_name'];

			$image_id = mfi_reloaded_get_image_id($image_picker_name, $post->ID);
			$image = mfi_reloaded_get_image($image_picker_name, 'full', $post->ID);

			include('views/meta-boxes/image-picker.php');
		}

		/// Post meta

		private static function _get_meta($post_id, $meta_key = null) {
			$post_id = empty($post_id) && in_the_loop() ? get_the_ID() : $post_id;

			$meta = get_post_meta($post_id, 'mfi-reloaded-images', true);

			if(!is_array($meta)) {
				$meta = array();
			}

			return is_null($meta_key) ? $meta : (isset($meta[$meta_key]) ? $meta[$meta_key] : false);
		}

		private static function _set_meta($post_id, $meta) {
			$post_id = empty($post_id) && in_the_loop() ? get_the_ID() : $post_id;

			update_post_meta($post_id, 'mfi-reloaded-images', $meta);

			return $meta;
		}

		/// Utility

		private static function _normalize_args($args) {
			$normalized_args = array();

			if(!isset($args['post_types'])) {
				$normalized_args['post_types'] = array('post', 'page');
			} else if(!is_array($args['post_types'])) {
				$normalized_args['post_types'] = array($args['post_types']);
			} else {
				$normalized_args['post_types'] = $args['post_types'];
			}

			$default_labels = array(
				'name' => __('Featured Image'),
				'set' => __('Set featured image'),
				'remove' => __('Remove featured image'),
				'popup_title' => __('Set Featured Image'),
				'popup_select' => __('Set featured image'),
			);

			if(!isset($args['labels']) || !is_array($args['labels'])) {
				$normalized_args['labels'] = $default_labels;
			} else {
				$normalized_args['labels'] = shortcode_atts($default_labels, $args['labels']);
			}

			return $normalized_args;
		}

		/// Template tags

		public static function get_image_id($name, $post_id) {
			return self::_get_meta($post_id, $name);
		}

		public static function get_image($name, $size, $post_id, $attributes) {
			$image_id = self::get_image_id($name, $post_id);

			if($image_id) {
				return wp_get_attachment_image($image_id, $size, false, $attributes);
			}

			return false;
		}
	}

	require_once('lib/template-tags.php');
	MFI_Reloaded::init();
}
