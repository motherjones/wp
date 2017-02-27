<?php
/**
 * Various editor and dashboard mods
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

 /**
  * set wysiwyg config
  */
if ( ! function_exists( 'mj_wysiwyg_config' ) ) {
   function mj_wysiwyg_config($config) {
 		$config['remove_linebreaks'] = false;
 		$config['gecko_spellcheck'] = true;
 		$config['keep_styles'] = true;
 		$config['accessibility_focus'] = true;
 		$config['tabfocus_elements'] = 'major-publishing-actions';
 		$config['media_strict'] = false;
 		$config['paste_remove_styles'] = true;
 		$config['paste_remove_spans'] = true;
 		$config['paste_strip_class_attributes'] = 'none';
 		$config['paste_text_use_dialog'] = true;
 		$config['wpeditimage_disable_captions'] = true;
 		$config['wpautop'] = true;
 		$config['apply_source_formatting'] = false;

 		$style_formats = array(
 			array(
 				'title' => 'Subheader',
 				'block' => 'h3',
 				'classes' => 'subheader',
 				'wrapper' => false,
 			),
 			array(
 				'title' => 'Section Lead',
 				'inline' => 'span',
 				'classes' => 'section-lead',
 				'wrapper' => false,
 			),
 			array(
 				'title' => 'Pullquote',
 				'block' => 'blockquote',
 				'classes' => 'pullquote-left',
 				'wrapper' => false,
 			),
 		);
 		// Insert the array, JSON ENCODED, into 'style_formats'
 		$config['style_formats'] = json_encode( $style_formats );

 		$config['toolbar1'] = 'bold,italic,strikethrough,bullist,numlist,blockquote,hr,styleselect,link,unlink,wp_fullscreen,wp_adv ';
 		$config['toolbar2'] = 'spellchecker,underline,removeformat,charmap,outdent,indent,undo,redo,wp_help ';

 		return $config;
   }
}
//change default height of text editing area in wysiwyg
add_action( 'admin_head', 'content_textarea_height' );
function content_textarea_height() {
	echo'
        <style type="text/css">
					.post-type-mj_blog_post #fm-body-0_ifr {height: 280px !important;}
					.post-type-mj_blog_post #fm-body-0 {height: 280px !important;}
					.post-type-mj_article #fm-body-0_ifr {height: 560px !important;}
					.post-type-mj_article #fm-body-0 {height: 560px !important;}
					.post-type-mj_full_width #fm-body-0_ifr {height: 840px !important;}
					.post-type-mj_full_width #fm-body-0 {height: 840px !important;}
        </style>
	';
}
