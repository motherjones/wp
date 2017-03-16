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

		add_filter(
			'img_caption_shortcode',
			array( &$this, 'caption_shortcode' ), 10, 3
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
			array( &$this, 'add_caption_shortcode' ), 19, 8
		);

		add_filter(
			'pre_post_content',
			array( $this, 'filter_pre_post_content_fix_credit' )
		);
	}

	function admin_init() {
		remove_filter( 'image_send_to_editor', 'image_add_caption', 20, 8 );
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
	 function add_caption_shortcode( $html, $id, $caption, $title, $align, $url, $size, $alt = '' ) {
		 	$creditor = navis_get_media_credit( $id );
			if ( empty( $caption ) && ! $creditor->to_string() ) {
				return $html;
			}
			$id = ( 0 < (int) $id ) ? 'attachment_' . $id : '';
			if ( ! preg_match( '/width="([0-9]+)/', $html, $matches ) ) {
				return $html;
			}

			$width = $matches[1];

			// XXX: not sure what this does
			$html = preg_replace( '/(class=["\'][^\'"]*)align(none|left|right|center)\s?/', '$1', $html );
			if ( empty($align) ) {
				$align = 'none';
			}

			$shcode = '[caption id="' . $id . '" align="align' . $align .
				'" width="' . $width . '" caption="' . $caption . '"]' . $html
				. '[/caption]';
			return $shcode;
		}

	/**
	 * Renders caption shortcodes with our layout
	 * and credit field.
	 */
	function caption_shortcode( $text, $atts, $content ) {
		$atts = shortcode_atts( array(
			'id' => '',
			'align' => 'alignnone',
			'width' => '',
			'credit' => '',
			'caption' => '',
		), $atts );
		$atts = apply_filters( 'navis_image_layout_defaults', $atts );
		extract( $atts );

		if ( $id && ! $credit ) {
			$post_id = str_replace( 'attachment_', '', $id );
			$creditor = navis_get_media_credit( $post_id );
			$credit = ! empty( $creditor ) ? $creditor->to_string() : '';
		}

		if ( $id ) {
			$id = 'id="' . esc_attr( $id ) . '" ';
		}

		$out = sprintf( '<div %s class="wp-caption %s" style="max-width: %spx;">%s', $id, $align, $width, do_shortcode( $content ) );
		if ( $credit ) {
			$out .= sprintf( '<p class="wp-media-credit">%s</p>', $credit );
		}
		if ( $caption ) {
			$out .= sprintf( '<p class="wp-caption-text">%s</p>', $caption );
		}
		$out .= '</div>';
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
		if ( $this->credit && $this->org ) {
			return sprintf( '%s / %s', esc_attr( $this->credit ), esc_attr( $this->org ) );
		} elseif ( $this->credit ) {
			return esc_attr( $this->credit );
		} elseif ( $this->org ) {
			return esc_attr( $this->org );
		}
	}
	function update( $field, $value ) {
		return update_post_meta( $this->post_id, '_' . $field, $value );
	}
}
