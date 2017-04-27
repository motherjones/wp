<?php
/**
 * Plugin Name: Media Credit
 * Plugin URI: http://argoproject.org/
 * Description: Adds support for credit fields on media stored in WordPress
 * Version: 0.1
 * Author: Project Argo
 * Author URI: http://argoproject.org/
 * License: GPLv2
 */

function navis_get_media_credit( $id ) {
	// XXX: do we need to get the post->ID or can we just use this ID??
	$post = get_post( $id );
	if ( ! $post ) {
		return false;
	}
	$creditor = new Media_Credit( $post->ID );
	return $creditor;
}

class Navis_Media_Credit {
	function __construct() {
		add_filter(
			'navis_media_credit_for_attachment',
			'get_media_credit_for_attachment', 10, 2
		);

		add_shortcode(
			'inline_image',
			array( &$this, 'inline_image_shortcode' )
		);

		add_shortcode(
			'credit',
			array( &$this, 'credit_shortcode' )
		);

		add_filter(
			'img_caption_shortcode',
			array( &$this, 'do_caption_shortcode' ), 10, 3
		);

		if ( ! is_admin() ) {
			return;
		}

		add_action(
			'admin_init',
			array( &$this, 'admin_init' )
		);

		add_filter(
			'attachment_fields_to_save',
			array( &$this, 'save_media_credit' ), 10, 2
		);

		add_filter(
			'attachment_fields_to_edit',
			array( &$this, 'add_media_credit' ), 10, 2
		);

		add_filter(
			'image_send_to_editor',
			array( &$this, 'add_inline_image_shortcode' ), 19, 8
		);

		add_filter(
			'pre_post_content',
			array( $this, 'filter_pre_post_content_fix_credit' )
		);
	}

	function admin_init() {
		remove_filter( 'image_send_to_editor', 'image_add_caption', 20, 8 );
		$this::setup_tinymce_shortcodes();
	}

	function setup_tinymce_shortcodes() {
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}
		if ( get_user_option( 'rich_editing' ) !== 'true' ) {
			return;
		}
		add_filter( 'mce_external_plugins', array( &$this, 'add_tinymce_shortcodes' ) );
	}
	function add_tinymce_shortcodes($plugin_array) {
		$plugin_array['custom_link_class'] = get_template_directory_uri() . '/js/tinymce-inline-image.min.js';
		return $plugin_array;
	}

	function get_media_credit_for_attachment( $text = '', $id ) {
		$creditor = navis_get_media_credit( $id );
		if ( ! $creditor ) {
			return $text;
		}
		return $text . $creditor->to_string();
	}

	function add_media_credit( $fields, $post ) {
		$creditor = navis_get_media_credit( $post );
		$fields['media_credit'] = array(
			'label' => 'Credit',
			'input' => 'text',
			'value' => ! empty( $creditor ) ? $creditor->credit : '',
		);
		$fields['media_credit_url'] = array(
			'label' => 'Credit URL',
			'input' => 'text',
			'value' => ! empty( $creditor ) ? $creditor->credit_url : '',
		);
		$fields['media_credit_org'] = array(
			'label' => 'Organization',
			'input' => 'text',
			'value' => ! empty( $creditor->org ) ? $creditor->org : '',
		);
		return $fields;
	}

	function save_media_credit( $post, $attachment ) {
		$creditor = new Media_Credit( $post['ID'] );
		$fields = array( 'media_credit', 'media_credit_url', 'media_credit_org' );

		foreach ( $fields as $field ) {
			if ( $_POST['attachments'] && isset( $_POST['attachments'][ $post['ID'] ][ $field ] ) ) {
				$input = sanitize_text_field( $_POST['attachments'][ $post['ID'] ][ $field ] );
			} else {
				// XXX: not sure if this branch is ever followed
				if ( isset( $_POST[ $field ] ) ) {
					$input = sanitize_text_field( $_POST[ $field ] );
				} else if ( isset( $_POST[ "attachments[" . $post['ID'] . "][" . $field . "]" ] ) ) {
					$input = sanitize_text_field( $_POST[ "attachments[" . $post['ID'] . "][" . $field . "]" ] );
				} else {
					$input = '';
				}
			}
			$creditor->update( $field, $input );
		}
		return $post;
	}


	/**
	 * Replaces the built-in caption shortcode
	 * with one that supports a credit field.
	 */
	function add_inline_image_shortcode( $html, $id, $caption, $title, $align, $url, $size, $alt = '' ) {
		$creditor = navis_get_media_credit( $id );
		$credit = $creditor->to_string();

		$id = ( 0 < (int) $id ) ? 'attachment_' . $id : '';

		$width = '';
		if ( preg_match( '/width="([0-9]+)/', $html, $matches ) ) {
			$width = ' width="' . $matches[1] . '" ';
		}

		// XXX: not sure what this does.
		$html = preg_replace( '/(class=["\'][^\'"]*)align(none|left|right|center)\s?/', '$1', $html );
		if ( empty( $align ) ) {
			$align = 'none';
		}

		$figcap = '';
		if ( $caption || $credit ) {
			if ( $caption ) {
				$figcap .= sprintf( '[caption]%s', $caption );
			}
			if ( $credit ) {
				$figcap .= sprintf( '[credit]%s[/credit]', $credit );
			}
			$figcap .= '[/caption]';
		}

		$shcode = '[inline_image id="' . $id . '" align="align' . $align .
			'" ' . $width . ']' . $html . $figcap . '[/inline_image]';
		return $shcode;
	}

	/**
	 * Renders caption shortcodes.
	 */
	function do_caption_shortcode( $text, $atts, $content ) {
		$figcap = '<figcaption class="wp-caption-text">';
		$figcap .= '<span class="media-caption">';
		$figcap .= do_shortcode( $content );
		$figcap .= '</span>';
		$figcap .= '</figcaption>';
		return $figcap;
	}

	/**
	 * Renders credit shortcodes.
	 */
	function credit_shortcode( $atts, $content ) {
		return sprintf( '</span><span class="media-credit">%s', $content );
	}

	/**
	 * Renders inline image shortcodes.
	 */
	function inline_image_shortcode( $atts, $content ) {
		$atts = shortcode_atts( array(
			'id' => '',
			'align' => 'alignnone',
			'width' => '',
		), $atts );
		$atts = apply_filters( 'navis_image_layout_defaults', $atts );
		extract( $atts );

		if ( $id ) {
			$id = 'id="' . esc_attr( $id ) . '" ';
		}

		$out = sprintf( '<figure %s class="inline-image %s" style="max-width: %spx;">%s</figure>',
			$id, $align, $width, do_shortcode( $content )
		);
		return $out;
	}

	/**
	 * For TinyMCE 4 and greater, fix mangled [caption shortcodes]
	 */
	function filter_pre_post_content_fix_credit( $post_content ) {
		if ( empty( $post_content ) || false === strpos( $post_content, '\\" credit=\\"' ) ) {
			return $post_content;
		}

		// [caption id="attachment_18" align="alignright" width="336" caption=" " credit="Daniel Bachhuber / The pants"]<a href="http://largo.dev/wp-content/uploads/2014/04/IMG_0502.jpg"><img class="size-medium wp-image-18" alt="IMG_0502" src="http://largo.dev/wp-content/uploads/2014/04/IMG_0502-336x252.jpg" width="336" height="252" /></a>[/caption]
		// [caption id="attachment_18" align="alignright" width="336"]<a href="http://largo.dev/wp-content/uploads/2014/04/IMG_0502.jpg"><img class="size-medium wp-image-18" src="http://largo.dev/wp-content/uploads/2014/04/IMG_0502-336x252.jpg" alt="IMG_0502" width="336" height="252" /></a>  " credit="Daniel Bachhuber / The pants[/caption]
		$post_content = preg_replace( '/\\"\scredit=\\\"[\w\s\/\\\]+[^"[]/', '', $post_content );

		return $post_content;
	}
}
new Navis_Media_Credit;

class Media_Credit {
	function __construct( $post_id ) {
		$this->post_id = $post_id;
		$this->credit = get_post_meta( $post_id,
			'_media_credit', true
		);
		$this->credit_url = get_post_meta( $post_id,
			'_media_credit_url', true
		);
		$this->org = get_post_meta( $post_id,
			'_media_credit_org', true
		);
	}
	function to_string() {
		$out = '';
		if ( $this->credit_url ) {
			$out .= sprintf( '<a href="%s">', esc_url( $this->credit_url ) );
		}
		if ( $this->credit && $this->org ) {
			$out .= sprintf( '%s / %s', esc_attr( $this->credit ), esc_attr( $this->org ) );
		} elseif ( $this->credit ) {
			$out .= esc_attr( $this->credit );
		} elseif ( $this->org ) {
			$out .= esc_attr( $this->org );
		}
		if ( $this->credit_url ) {
			$out .= '</a>';
		}
		return $out;
	}
	function update( $field, $value ) {
		return update_post_meta( $this->post_id, '_' . $field, $value );
	}
}
