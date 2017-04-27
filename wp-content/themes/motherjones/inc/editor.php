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
			'block' => 'p',
			'classes' => 'pullquote-left',
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


/**
 * Publish_Confirm
 * A slimmed down version of https://wordpress.org/plugins/publish-confirm/
 * Basically just add a speedbump before publishing.
 */
class Publish_Confirm {
	/**
	 * Publish_Confirm constructor.
	 */
	public function __construct() {
		// Check user role.
		if ( ! current_user_can( 'publish_posts' ) ) {
			return;
		}
		foreach ( array( 'post-new.php', 'post.php' ) as $page ) {
			add_action( 'admin_footer-' . $page, array( $this, 'inject_js' ), 11 );
		}
	}

	/**
	 * Prepares the JS code integration
	 *
	 * @since   0.0.3
	 * @version 0.0.4
	 *
	 * @hook    array  publish_confirm_message
	 */
	public static function inject_js() {

		// Filter published posts.
		if ( get_post()->post_status === 'publish' ) {
			return;
		}

		// Is jQuery loaded.
		if ( ! wp_script_is( 'jquery', 'done' ) ) {
			return;
		}

		// Print javascript.
		self::_print_js( esc_attr__( 'Are you sure you want to publish this now?', 'mj' ) );
	}

	/**
	 * Prints the JS code into the footer
	 *
	 * @since   0.0.3
	 * @version 2015-11-30
	 *
	 * @param   string $msg JS confirm message.
	 */
	private static function _print_js( $msg ) {

		?>
		<script type="text/javascript">
			jQuery( document ).ready(
				function( $ ) {
					var scheduleLabel = postL10n.schedule; // if the language is English, this is "Schedule"
					$( '#publish' ).on(
						'click',
						function( event ) {
							if ( $( this ).attr( 'name' ) !== 'publish' || $( this ).attr( 'value' ) === scheduleLabel ) {
								return;
							}
							if ( ! confirm( <?php echo wp_json_encode( $msg ) ?> ) ) {
								event.preventDefault();
							}
						}
					);
				}
			);
		</script>
	<?php }
}
/**
 * New instance on the admin_init hook.
 */
function mj_publish_confirm() {
	new Publish_Confirm;
}
add_action( 'admin_init', 'mj_publish_confirm' );
