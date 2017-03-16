<?php

/**
 * Returns the default available featured media types
 */
function largo_default_featured_media_types() {
	$media_types = apply_filters( 'largo_default_featured_media_types', array(
		'image' => array(
			'title' => __( 'Featured image', 'largo' ),
			'id' => 'image',
		),
		'video' => array(
			'title' => __( 'Featured video', 'largo' ),
			'id' => 'video',
		),
		'embed' => array(
			'title' => __( 'Featured embed code', 'largo' ),
			'id' => 'embed-code',
		),
	));
	return array_values( $media_types );

}

/**
 * Prints DOM for hero image.
 *
 * Determines the type of featured media attached to a post,
 * and generates the DOM for that type of media.
 *
 * @param int|WP_Post $post Optional. Post ID or WP_Post object. Default is global $post.
 * @param String      $classes Optional. Class string to apply to outer div.hero.
 */
function largo_hero( $post = null, $classes = '' ) {
	echo largo_get_hero( $post, $classes );
}

/**
 * Return DOM for hero image.
 *
 * Determines the type of featured media attached to a post,
 * and generates the DOM for that type of media.
 *
 * @param int|WP_Post $post Optional. Post ID or WP_Post object. Default is global $post.
 * @param String $classes Optional. Class string to apply to outer div.hero
 */
function largo_get_hero( $post = null, $classes = '' ) {

	$post = get_post( $post );
	$hero_class = largo_hero_class( $post->ID, false );
	$ret = '';
	$values = get_post_custom( $post->ID );

	// If the box is checked to override the featured image display, obey it.
	if ( isset( $values['featured-image-display'][0] ) ) {
		return $ret;
	}

	if ( largo_has_featured_media( $post->ID ) && $hero_class !== 'is-empty' ) {
		$ret = largo_get_featured_hero( $post->ID, $classes );
	}

	/**
	 * Filter the hero's DOM
	 *
	 * @since 0.5.1
	 *
	 * @param String $var    DOM for hero.
	 * @param WP_Post $post  post object.
	 */
	$ret = apply_filters( 'largo_get_hero', $ret, $post, $classes );

	return $ret;
}

/**
 * Prints DOM for a featured image hero.
 *
 * @since 0.5.1
 * @see largo_get_featured_hero()
 *
 * @param int|WP_Post $post Optional. Post ID or WP_Post object. Default is global $post.
 * @param String $classes Optional. Class string to apply to outer div.hero
 */
function largo_featured_image_hero( $post = null, $classes = '' ) {
	echo largo_get_featured_hero( $post, $classes );
}

/**
 * Prints DOM for a featured image hero.
 *
 * @since 0.5.5
 *
 * @param int|WP_Post $post Optional. Post ID or WP_Post object. Default is global $post.
 * @param String $classes Optional. Class string to apply to outer div.hero
 */
function largo_get_featured_hero( $post = null, $classes = '' ) {
	$the_post = get_post( $post );
	$featured_media = largo_get_featured_media( $the_post->ID );
	$hero_class = largo_hero_class( $the_post->ID, false );
	$classes = "hero $hero_class $classes";

	$context = array(
		'classes' => $classes,
		'featured_media' => $featured_media,
		'the_post' => $the_post,
	);

	if ( 'image' == $featured_media['type'] ) {
		$thumb_meta = null;
		if ( $thumb_id = get_post_thumbnail_id( $the_post->ID ) ) {
			$thumb_content = get_post( $thumb_id );
			$thumb_custom = get_post_custom( $thumb_id );

			$thumb_meta = array(
				'caption' => ( ! empty( $thumb_content->post_excerpt ) ) ? $thumb_content->post_excerpt : null,
				'credit' => ( ! empty( $thumb_custom['_media_credit'][0] ) ) ? $thumb_custom['_media_credit'][0] : null,
				'credit_url' => ( ! empty( $thumb_custom['_media_credit_url'][0] ) ) ? $thumb_custom['_media_credit_url'][0] : null,
				'organization' => ( ! empty( $thumb_custom['_media_credit_org'][0] ) ) ? $thumb_custom['_media_credit_org'][0] : null
			);

			$context['thumb_meta'] = $thumb_meta;
		}

	}

	switch( $featured_media['type'] ) {
		// video and embed code use the same partial;
		// empty statement list for a case passes control to next case: https://secure.php.net/manual/en/control-structures.switch.php
		case 'video':
		case 'embed-code':
			$template_slug = 'embed';
			break;
		case 'image':
			$template_slug = 'image';
			break;
	}

	ob_start();
	largo_render_template( 'template-parts/hero', 'featured-' . $template_slug, $context );
	$ret = ob_get_clean();
	return $ret;
}

/**
 * Prints DOM for an embed code hero.
 *
 * @since 0.5.1
 * @see largo_get_featured_hero()
 *
 * @param int|WP_Post $post Optional. Post ID or WP_Post object. Default is global $post.
 * @param String $classes Optional. Class string to apply to outer div.hero
 */
function largo_featured_embed_hero( $post = null, $classes = '' ) {
	echo largo_get_featured_hero( $post, $classes );
}

/**
 * Returns information about the featured media.
 *
 * @since 0.4
 *
 * @param int|WP_Post $post Optional. Post ID or WP_Post object. Default is global $post.
 * @return array $post_type {
 *
 * 			'id' => int, 		// post id.
 * 			'type' => string, 	// the type of featured_media
 *
 * 			// ... other variables, dependent on what the type is.
 *
 * 		}
 */
function largo_get_featured_media( $post = null ) {

	$post = get_post( $post );

	if ( ! is_object( $post ) ) {
		return;
	}

	$ret = get_post_meta( $post->ID, 'featured_media', true );

	// Check if the post has a thumbnail/featured image set.
	// If yes, send that back as the featured media.
	$post_thumbnail = get_post_thumbnail_id( $post->ID );
	if ( empty( $ret ) && ! empty( $post_thumbnail ) ) {
		$ret = array(
			'id' => $post->ID,
			'attachment' => $post_thumbnail,
			'type' => 'image',
		);
	} elseif ( ! empty( $ret ) && in_array( $ret['type'], array( 'embed', 'video' ), true ) && ! empty( $post_thumbnail ) ) {
		$attachment = wp_prepare_attachment_for_js( $post_thumbnail );
		unset( $attachment['compat'] );
		$ret = array_merge( $ret, array( 'attachment_data' => $attachment ) );
	}

	return $ret;
}

/**
 * Does the post have featured media?
 *
 * @param int|WP_Post $post Optional. Post ID or WP_Post object. Default is global $post.
 * @return bool If a post ID has featured media or not.
 */
function largo_has_featured_media( $post = null ) {
	$post = get_post( $post );
	$id = isset( $post->ID ) ? $post->ID : 0;
	$featured_media = largo_get_featured_media( $id );
	return ! empty( $featured_media );
}

/**
 * Functions that modify the dashboard/load the featured media functionality
 */

/**
 * Enqueue the featured media javascript
 *
 * @global $post
 * @global MJ_DEBUG
 * @param array $hook The page that this function is being run on.
 */
function largo_enqueue_featured_media_js( $hook ) {
	if ( ! in_array( $hook, array( 'edit.php', 'post-new.php', 'post.php' ), true ) ) {
		return;
	}
	global $post, $wp_query;

	if ( ! is_object( $post ) ) {
		return;
	}
	$featured_image_display = get_post_meta( $post->ID, 'featured-image-display', true );

	// The scripts following depend upon the WordPress media APIs.
	wp_enqueue_media();

	$suffix = ( MJ_DEBUG ) ? '' : '.min';
	wp_enqueue_script(
		'largo_featured_media',
		get_template_directory_uri() . '/js/featured-media.js',
		array( 'media-models', 'media-views' ),
		false,
		1
	);
	wp_localize_script(
		'largo_featured_media',
		'largo_featured_media_vars',
		array(
			'image_title' => __( 'Featured image', 'largo' ),
			'video_title' => __( 'Featured video', 'largo' ),
			'embed_title' => __( 'Featured embed code', 'largo' ),
			'error_invalid_url' => __( 'Error: please enter a valid URL.', 'largo' ),
			'error_occurred' => __( 'An error ocurred', 'largo' ),
			'set_featured' => __( 'Set as featured', 'largo' ),
			'confirm_remove_featured' => __( 'Yes, remove featured media', 'largo' ),
			'remove_featured_title' => __( 'Remove featured', 'largo' ),
		)
	);

	wp_localize_script( 'largo_featured_media', 'LFM', array(
		'options' => largo_default_featured_media_types(),
		'featured_image_display' => ! empty( $featured_image_display ),
		'has_featured_media' => (bool) largo_has_featured_media( $post->ID ),
	));
}
add_action( 'admin_enqueue_scripts', 'largo_enqueue_featured_media_js' );

/**
 * Prints the templates used by featured media modal.
 */
function largo_featured_media_templates() {
?>
	<script type="text/template" id="tmpl-featured-embed-code">
		<form id="featured-embed-code-form">
			<input type="hidden" name="type" value="embed-code" />

			<# var model = data.controller.model #>
			<div>
				<label for="title"><span><?php esc_html_e( 'Title', 'largo' ); ?></span></label>
				<input type="text" name="title" <# if (model.get('type') == 'embed-code') { #>value="{{ model.get('title') }}"<# } #> />
			</div>

			<div>
				<label for="caption"><span><?php esc_html_e( 'Caption', 'largo' ); ?></span></label>
				<input type="text" name="caption" <# if (model.get('type') == 'embed-code') { #>value="{{ model.get('caption') }}"<# } #> />
			</div>

			<div>
				<label for="credit"><span><?php esc_html_e( 'Credit', 'largo' ); ?></span></label>
				<input type="text" name="credit" <# if (model.get('type') == 'embed-code') { #>value="{{ model.get('credit') }}"<# } #> />
			</div>

			<div>
				<label for="url"><span><?php esc_html_e( 'URL', 'largo' ); ?></span></label>
				<input type="text" name="url" <# if (model.get('type') == 'embed-code') { #>value="{{ model.get('url') }}"<# } #> />
			</div>

			<div>
				<label for="embed"><span><?php esc_html_e( 'Embed code', 'largo' ); ?></span></label>
				<textarea name="embed"><# if (model.get('type') == 'embed-code') { #>{{ model.get('embed') }}<# } #></textarea>
			</div>

			<div>
				<label><span><?php echo esc_html_e( 'Embed thumbnail', 'largo' ); ?></span></span></label>
				<div id="embed-thumb"></div>
			</div>
		</form>
	</script>

	<script type="text/template" id="tmpl-featured-video">
		<form id="featured-video-form">
			<input type="hidden" name="type" value="video" />

			<p><?php esc_html_e( 'Enter a video URL to get started', 'largo' ); ?>.</p>
			<# var model = data.controller.model #>
			<div>
				<label for="url"><span><?php esc_html_e( 'Video URL', 'largo' ); ?>  <span class="spinner" style="display: none;"></span></label>
				<input type="text" class="url" name="url" <# if (model.get('type') == 'video') { #>value="{{ model.get('url') }}"<# } #>/>
				<p class="error"></p>
			</div>

			<div>
			<label for="embed"><span><?php esc_html_e( 'Video embed code', 'largo' ); ?></span></label>
				<textarea name="embed"><# if (model.get('type') == 'video') { #>{{ model.get('embed') }}<# } #></textarea>
			</div>

			<div>
				<label><span><?php esc_html_e( 'Video thumbnail', 'largo' ); ?></span></span></label>
				<div id="embed-thumb"></div>
			</div>

			<div>
				<label for="title"><span><?php esc_html_e( 'Title', 'largo' ); ?></span></span></label>
				<input type="text" name="title" <# if (model.get('type') == 'video') { #>value="{{ model.get('title') }}"<# } #> />
			</div>

			<div>
				<label for="caption"><span><?php esc_html_e( 'Caption', 'largo' ); ?></span></label>
				<input type="text" name="caption" <# if (model.get('type') == 'video') { #>value="{{ model.get('caption') }}"<# } #> />
			</div>

			<div>
				<label for="credit"><span><?php esc_html_e( 'Credit', 'largo' ); ?></span></label>
				<input type="text" name="credit" <# if (model.get('type') == 'video') { #>value="{{ model.get('credit') }}"<# } #> />
			</div>

		</form>
	</script>

	<script type="text/template" id="tmpl-featured-thumb">
		<div class="thumb-container">
			<# if (typeof data.model.get('sizes') !== 'undefined') { #>
				<img src="{{ data.model.get('sizes').medium.url }}" title="Thumbnail: '{{ data.model.get('title') }}'" />
				<input type="hidden" name="attachment" value="{{ data.model.get('id') }}" />
			<# } else if (data.model.get('thumbnail_url')) { #>
				<img src="{{ data.model.get('thumbnail_url') }}" title="Thumbnail for '{{ data.model.get('title') }}'" />
				<input type="hidden" name="thumbnail_url" value="{{ data.model.get('thumbnail_url') }}" />
				<input type="hidden" name="thumbnail_type" value="oembed" />
			<# } #>
			<a href="#" class="remove-thumb"><?php esc_html_e( 'Remove thumbnail', 'largo' ); ?></a>
		</div>
	</script>

	<script type="text/template" id="tmpl-featured-remove-featured">
		<h1><?php esc_html_e( 'Are you sure you want to remove featured media from this post?', 'largo' ); ?></h1>
	</script>
<?php }
add_action( 'admin_print_footer_scripts', 'largo_featured_media_templates', 1 );

/**
 * Remove the default featured image meta box from post pages
 */
function largo_remove_featured_image_meta_box() {
	remove_meta_box( 'postimagediv', 'post', 'normal' );
	remove_meta_box( 'postimagediv', 'post', 'side' );
	remove_meta_box( 'postimagediv', 'post', 'advanced' );
}
add_action( 'do_meta_boxes', 'largo_remove_featured_image_meta_box' );

/**
 * Add new featured image meta box to post pages
 */
function largo_add_featured_image_meta_box() {
	add_meta_box(
		'largo_featured_image_metabox',
		__( 'Featured Media', 'largo' ),
		'largo_featured_image_metabox_callback',
		array( 'post' ),
		'after_title',
		'core'
	);
}
add_action( 'add_meta_boxes', 'largo_add_featured_image_meta_box' );

/**
 * Get post meta in a callback
 *
 * @param WP_Post $post    The current post.
 * @param array   $metabox With metabox id, title, callback, and args elements.
 */
function largo_featured_image_metabox_callback( $post, $metabox ) {
	global $post;

	$has_featured_media = largo_has_featured_media( $post->ID );

	$language = ( ! empty( $has_featured_media ) ) ?
	 	__( 'Edit Featured Media', 'largo' ) :
		__( 'Set Featured Media', 'largo' );

	$checked = 'false' === get_post_meta( $post->ID, 'featured-image-display', true ) ? 'checked="checked"' : '';
	echo wp_nonce_field( basename( __FILE__ ), 'featured_image_display_nonce' );

	echo '<a href="#" class="set-featured-media">' . get_the_post_thumbnail() . '</a>';
	echo '<a href="#" id="set-featured-media-button" class="button set-featured-media add_media" data-editor="content" title="' . esc_html( $language ) . '"></span> ' . esc_html( $language ) . '</a> <span class="spinner" style="display: none;"></span>';

	echo '<p><label class="selectit"><input type="checkbox" value="true" name="featured-image-display"' . $checked . '> ' . esc_html__( 'Hide image at top of story.', 'largo' ) . '</label></p>';
}

/**
 * Save data from meta box
 *
 * @param int    $post_id the post ID.
 * @param object $post the post object.
 */
function largo_save_featured_media_data( $post_id, $post ) {

	// Verify the nonce before proceeding.
	if ( ! isset( $_POST['featured_image_display_nonce'] ) || ! wp_verify_nonce( $_POST['featured_image_display_nonce'], basename( __FILE__ ) ) ) {
		return $post_id;
	}

	// Get the post type object.
	$post_type = get_post_type_object( $post->post_type );

	// Check if the current user has permission to edit the post.
	if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
		return $post_id;
	}

	// Get the posted data and sanitize it for use as an HTML class.
	$new_meta_value = ( isset( $_POST['featured-image-display'] ) ? sanitize_html_class( $_POST['featured-image-display'] ) : '' );

	// Get the meta key.
	$meta_key = 'featured-image-display';

	// Get the meta value of the custom field key.
	$meta_value = get_post_meta( $post_id, $meta_key, true );

	/*
	 * If the checkbox was checked, update the meta_value, but save it as 'false' for compatibility with older Largo versions (<.5.5)
	 * If the checkbox was unchecked, delete the meta_value
	 */
	if ( $new_meta_value && 'true' === $new_meta_value && '' === $meta_value ) {
		add_post_meta( $post_id, $meta_key, 'false', true );
	} elseif ( empty( $new_meta_value ) ) {
		delete_post_meta( $post_id, $meta_key );
	}
}
add_action( 'save_post', 'largo_save_featured_media_data', 10, 2 );

/**
 * AJAX functions
 */

/**
 * Read the `featured_media` meta for a given post. Expects array $_POST['data']
 * with an `id` key corresponding to post ID to look up.
 */
function largo_featured_media_read() {
	if ( ! empty( $_POST['data'] ) ) {
		$data = json_decode( stripslashes( $_POST['data'] ), true );
		$ret = largo_get_featured_media( $data['id'] );

		// Otherwise, check for `featured_media` post meta
		if ( ! empty( $ret ) ) {
			print wp_json_encode( $ret );
			wp_die();
		}

		// No featured thumbnail and not `featured_media`, so just return
		// an array with the post ID.
		print wp_json_encode( array( 'id' => $data['id'] ) );
		wp_die();
	}
}
add_action( 'wp_ajax_largo_featured_media_read', 'largo_featured_media_read' );

/**
 * Save `featured_media` post meta. Expects array $_POST['data'] with at least
 * an `id` key corresponding to the post ID that needs meta saved.
 */
function largo_featured_media_save() {
	if ( ! empty( $_POST['data'] ) ) {
		$data = json_decode( stripslashes( $_POST['data'] ), true );

		// If an attachment ID is present, update the post thumbnail/featured image.
		if ( ! empty( $data['attachment'] ) ) {
			set_post_thumbnail( $data['id'], $data['attachment'] );
		} else {
			delete_post_thumbnail( $data['id'] );
		}

		// Set the featured image for embed or oembed types.
		if ( isset( $data['thumbnail_url'] ) && isset( $data['thumbnail_type'] ) && 'oembed' === $data['thumbnail_type'] ) {
			$thumbnail_id = largo_media_sideload_image( $data['thumbnail_url'], null );
		} elseif ( isset( $data['attachment'] ) ) {
			$thumbnail_id = $data['attachment'];
		}

		if ( isset( $thumbnail_id ) ) {
			update_post_meta( $data['id'], '_thumbnail_id', $thumbnail_id );
			$data['attachment_data'] = wp_prepare_attachment_for_js( $thumbnail_id );
			unset( $data['attachment_data']['compat'] );
		}

		// Don't save the post ID in post meta.
		$save = $data;
		unset( $save['id'] );

		// Save what's sent over the wire as `featured_media` post meta.
		$ret = update_post_meta( $data['id'], 'featured_media', $save );

		print wp_json_encode( $data );
		wp_die();
	}
}
add_action( 'wp_ajax_largo_featured_media_save', 'largo_featured_media_save' );

/**
 * Saves the option that determines whether a featured image should be displayed
 * at the top of the post page or not.
 */
function largo_save_featured_image_display() {
	if ( ! empty( $_POST['data'] ) ) {
		$data = json_decode( stripslashes( $_POST['data'] ), true );

		$post_id = (int) $data['id'];
		$post_type = get_post_type( $post_id );
		$post_status = get_post_status( $post_id );

		if ( $post_type && isset( $data['featured-image-display'] ) && 'on' === $data['featured-image-display'] ) {
			update_post_meta( $post_id, 'featured-image-display', 'false' );
		} else {
			delete_post_meta( $post_id, 'featured-image-display' );
		}
		print json_encode( $data );
		wp_die();
	}
}
add_action( 'wp_ajax_largo_save_featured_image_display', 'largo_save_featured_image_display' );

/**
 * When a URL is typed/pasted into the url field of the featured video view,
 * this function tries to fetch the oembed information for that video.
 */
function largo_fetch_video_oembed() {
	if ( ! empty( $_POST['data'] ) ) {
		$data = json_decode( stripslashes( $_POST['data'] ), true );

		require_once( ABSPATH . WPINC . '/class-oembed.php' );
		$oembed = _wp_oembed_get_object();
		$url = $data['url'];
		$provider = $oembed->get_provider( $url );
		$data = $oembed->fetch( $provider, $url );
		$embed = $oembed->data2html( $data, $url );
		$ret = array_merge( array( 'embed' => $embed ), (array) $data );
		print wp_json_encode( $ret );
		wp_die();
	}
}
add_action( 'wp_ajax_largo_fetch_video_oembed', 'largo_fetch_video_oembed' );

/**
 * Add post classes to indicate whether a post has featured media and what type it is
 *
 * @param array $classes the post classes.
 * @since 0.5.2
 */
function largo_featured_media_post_classes( $classes ) {
	global $post;

	$featured = largo_get_featured_media( $post->ID );
	if ( ! empty( $featured ) ) {
		$classes = array_merge( $classes, array(
			'featured-media',
			'featured-media-' . $featured['type'],
		));
	}

	return $classes;
}
add_filter( 'post_class', 'largo_featured_media_post_classes' );

/**
 * Determines what type of hero image/video a single post should use
 * and returns a class that gets added to its container div
 *
 * @since 0.4
 */
if ( ! function_exists( 'largo_hero_class' ) ) {
	function largo_hero_class( $post_id, $echo = true ) {
		$hero_class = 'is-empty';
		$featured_media = ( largo_has_featured_media( $post_id ) ) ? largo_get_featured_media( $post_id ) : array();
		$type = ( isset( $featured_media['type'] ) ) ? $featured_media['type'] : false;

		if ( 'video' === $type ) {
			$hero_class = 'is-video';
		} elseif ( 'embed-code' === $type ) {
			$hero_class = 'is-embed';
		} elseif ( has_post_thumbnail( $post_id ) || 'image' === $type ) {
			$hero_class = 'is-image';
		}

		if ( $echo ) {
			echo $hero_class;
		} else {
			return $hero_class;
		}
	}
}

/**
 * Similar to `media_sideload_image` except that it simply returns the attachment's ID on success
 *
 * @param string $file the url of the image to download and attach to the post.
 * @param int    $post_id the post ID to attach the image to.
 * @param string $desc an optional description for the image.
 *
 * @since 0.5.2
 */
function largo_media_sideload_image( $file, $post_id, $desc = null ) {
	if ( ! empty( $file ) ) {
		include_once ABSPATH . 'wp-admin/includes/image.php';
		include_once ABSPATH . 'wp-admin/includes/file.php';
		include_once ABSPATH . 'wp-admin/includes/media.php';

		// Set variables for storage, fix file filename for query strings.
		preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $file, $matches );
		$file_array = array();
		$file_array['name'] = basename( $matches[0] );
		// Download file to temp location.
		$file_array['tmp_name'] = download_url( $file );
		// If error storing temporarily, return the error.
		if ( is_wp_error( $file_array['tmp_name'] ) ) {
			return $file_array['tmp_name'];
		}
		// Do the validation and storage stuff.
		$id = media_handle_sideload( $file_array, $post_id, $desc );
		// If error storing permanently, unlink.
		if ( is_wp_error( $id ) ) {
			@unlink( $file_array['tmp_name'] );
		}
		return $id;
	}
}
