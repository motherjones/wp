=== Multiple Featured Images: Reloaded ===

Contributors: nickohrn
Donate link: http://example.com/
Tags: admin, images
Requires at least: 3.6
Tested up to: 3.6
Stable tag: 1.0.0
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

This plugin allows developers to easily register additional image pickers for any post type.

== Description ==

This plugin allows developers to easily register additional image pickers for any post type.

== Installation ==

1. Upload `mfi-reloaded` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Use the provided template tags to create image pickers and display the data you need

== Frequently Asked Questions ==

= How do I add an image picker? =

Easy! In your theme's functions.php file, add the following code:

`function register_custom_image_pickers() {
	add_theme_support('mfi-reloaded', array(
		'hero-image' => array(
			'post_types' => array('post'),
			'labels' => array(
				'name' => __('Hero Image'),
				'set' => __('Set hero image'),
				'remove' => __('Remove hero image'),
				'popup_title' => __('Set Hero Image'),
				'popup_select' => __('Set hero image'),
			),
		),
		'sidekick-image' => array(
			'post_types' => array('post'),
			'labels' => array(
				'name' => __('Sidekick Image'),
				'set' => __('Set sidekick image'),
				'remove' => __('Remove sidekick image'),
				'popup_title' => __('Set Sidekick Image'),
				'popup_select' => __('Set sidekick image'),
			),
		),
	));
}
add_action('after_setup_theme', 'register_custom_image_pickers');`

Feel free to provide whatever values you need.

== Changelog ==

= 1.0.0 = 

* Changed from using a custom template tag for adding image pickers to the WordPress function `add_theme_support`

= 1.0.0-RC1 =

* Initial release

== Upgrade Notice ==

= 1.0.0 =

If you were previously using the `mfi_reloaded_add_image_picker` template tag to add image pickers, you should switch to the new `add_theme_support` method.

= 1.0.0-RC1 =

Initial release.
