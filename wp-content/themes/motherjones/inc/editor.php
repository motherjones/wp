<?php
/**
 * Various editor and dashboard mods
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

global $mj;

// A canonical list of allowed tags to pass to wp_kses() when we need it.
$mj['allowed_tags'] = array(
	'a' => array(
		'href' => array(),
		'title' => array(),
	),
	'em' => array(),
	'strong' => array(),
);

/**
 * Set wysiwyg config.
 *
 * @param array $config the original tinymce config.
 */
function mj_wysiwyg_config( $config ) {
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
			'title' => 'Paragraph',
			'block' => 'p',
			'wrapper' => false,
		),
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
		array(
			'title' => 'Right Rail',
			'block' => 'div',
			'classes' => 'right-rail',
			'wrapper' => false,
		),
	);
	// Insert the array, JSON ENCODED, into 'style_formats'.
	$config['style_formats'] = wp_json_encode( $style_formats );
	$config['toolbar1'] = 'bold,italic,strikethrough,bullist,numlist,blockquote,hr,styleselect,link,unlink,wp_fullscreen,wp_adv ';
	$config['toolbar2'] = 'spellchecker,underline,removeformat,charmap,outdent,indent,undo,redo,wp_help ';
	return $config;
}

/**
 * Change the "enter title here" prompt for posts
 *
 * @param string $title the original placeholder text.
 */
function mj_change_title_text( $title ) {
	$screen = get_current_screen();
	if ( 'post' === $screen->base ) {
		$title = 'Enter headline';
	}
	return $title;
}
add_filter( 'enter_title_here', 'mj_change_title_text' );
