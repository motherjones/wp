<?php
/**
 * Various helpful little functions
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

	/**
	 * Converts a HEX value to RGB.
	 *
	 * @since Mother Jones 1.0
	 *
	 * @param string $color The original color, in 3- or 6-digit hexadecimal form.
	 * @return array Array containing RGB (red, green, and blue) values for the given
	 *               HEX code, empty array otherwise.
	 */
function mj_hex2rgb( $color ) {
		$color = trim( $color, '#' );

	if ( strlen( $color ) === 3 ) {
		$r = hexdec( substr( $color, 0, 1 ) . substr( $color, 0, 1 ) );
		$g = hexdec( substr( $color, 1, 1 ) . substr( $color, 1, 1 ) );
		$b = hexdec( substr( $color, 2, 1 ) . substr( $color, 2, 1 ) );
	} elseif ( strlen( $color ) === 6 ) {
		$r = hexdec( substr( $color, 0, 2 ) );
		$g = hexdec( substr( $color, 2, 2 ) );
		$b = hexdec( substr( $color, 4, 2 ) );
	} else {
		return array();
	}
		return array(
		'red' => $r,
		'green' => $g,
		'blue' => $b,
	);
}

/**
 * @param string $slug the slug of the template file to render.
 * @param string $name the name identifier for the template file; works like get_template_part.
 * @param array  $context an array with the variables that should be made available in the template being loaded.
 * @since 0.4
 */
function largo_render_template( $slug, $name = null, $context = array() ) {
	global $wp_query;
	if ( is_array( $name ) && empty( $context ) ) {
		$context = $name;
	}
	if ( ! empty( $context ) ) {
		$context = apply_filters( 'largo_render_template_context', $context, $slug, $name );
		$wp_query->query_vars = array_merge( $wp_query->query_vars, $context );
	}
	get_template_part( $slug, $name );
}

/**
 * Get the current URL, including the protocol and host
 *
 * @since 0.5
 */
function largo_get_current_url() {
	$url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	return ( ! empty( is_ssl() ) ) ? 'https://' . $url : 'http://' . $url;
}

/**
 * Returns a Facebook username or ID from the URL
 *
 * @param   string $url a Facebook url
 * @return  string  the Facebook username or id extracted from the input string
 * @since   0.4
 */
function fb_url_to_username( $url ) {
	$urlParts = explode( '/', $url );
	if ( end( $urlParts ) == '' ) {
		// URL has a trailing slash
		$urlParts = array_slice( $urlParts, 0 , -1 );
	}
	$username = end( $urlParts );
	if ( preg_match( '/profile.php/', $username ) ) {
		// a profile id
		preg_match( '/id=([0-9]+)/', $username, $matches );
		$username = $matches[1];
	} else {
		// hopefully there's a username
		preg_match( '/[^\?&#]+/', $username, $matches );
		if ( isset( $matches[0] ) ) {
			$username = $matches[0];
		}
	}
	return $username;
}

/**
 * Checks to see if a given Facebook username or ID has following enabled by
 * checking the iframe of that user's "Follow" button for <table>.
 * Usernames that can be followed have <tables>.
 * Users that can't be followed don't.
 * Users that don't exist don't.
 *
 * @param   string $username a valid Facebook username or page name. They're generally indistinguishable, except pages get to use '-'
 * @uses    wp_remote_get
 * @return  bool    The user specified by the username or ID can be followed
 */
function fb_user_is_followable( $username ) {
		// syntax for this iframe taken from https://developers.facebook.com/docs/plugins/follow-button/
		$get = wp_remote_get( 'https://www.facebook.com/plugins/follow.php?href=https%3A%2F%2Fwww.facebook.com%2F' . $username . '&amp;width&amp;height=80&amp;colorscheme=light&amp;layout=button&amp;show_faces=true' );
	if ( ! is_wp_error( $get ) ) {
		$response = $get['body'];
		if ( strpos( $response, 'table' ) !== false ) {
			return true; // can follow
		}
		return false; // cannot follow
	}
}

/**
 * Cleans a Facebook url to the bare username or id when the user is edited
 *
 * Edits $_POST directly because there's no other way to save the corrected username
 * from this callback. The action hooks this is used for run before edit_user in
 * wp-admin/user-edit.php, which overwrites the user's contact methods. edit_user
 * reads from $_POST.
 *
 * @param  object $user_id the WP_User object being edited
 * @param  array  $_POST
 * @since  0.4
 * @uses   fb_url_to_username
 * @link   http://codex.wordpress.org/Plugin_API/Action_Reference/edit_user_profile_update
 * @link   http://codex.wordpress.org/Plugin_API/Action_Reference/personal_options_update
 */
function clean_user_fb_username( $user_id ) {
	if ( current_user_can( 'edit_user', $user_id ) ) {
		$fb = fb_url_to_username( $_POST['fb'] );
		if ( preg_match( '/[^a-zA-Z0-9\.\-]/', $fb ) ) {
			// it's not a valid Facebook username, because it uses an invalid character
			$fb = '';
		}
		update_user_meta( $user_id, 'fb', $fb );
		if ( get_user_meta( $user_id, 'fb', true ) != $fb ) {
			wp_die( __( 'An error occurred.', 'largo' ) );
		}
		$_POST['fb'] = $fb;
	}
}

/**
 * Checks that the Facebook URL submitted is valid and the user is followable and causes an error if not
 *
 * @uses  fb_url_to_username
 * @uses  fb_user_is_followable
 * @param   $errors the error object
 * @param   bool                    $update whether this is a user update
 * @param   object                  $user a WP_User object
 * @link    http://codex.wordpress.org/Plugin_API/Action_Reference/user_profile_update_errors
 * @since   0.4
 */
function validate_fb_username( $errors, $update, $user ) {
	if ( isset( $_POST['fb'] ) ) {
		$fb_suspect = trim( $_POST['fb'] );
		if ( ! empty( $fb_suspect ) ) {
			$fb_user = largo_fb_url_to_username( $fb_suspect );
			if ( preg_match( '/[^a-zA-Z0-9\.\-]/', $fb_user ) ) {
				// it's not a valid Facebook username, because it uses an invalid character
				$errors->add( 'fb_username', '<b>' . $fb_suspect . '</b> ' . __( 'is an invalid Facebook username.', 'largo' ) . '</p>' . '<p>' . __( 'Facebook usernames only use the uppercase and lowercase alphabet letters (a-z A-Z), the Arabic numbers (0-9), periods (.) and dashes (-)', 'largo' ) );
			}
			if ( ! largo_fb_user_is_followable( $fb_user ) ) {
				$errors->add( 'fb_username',' <b>' . $fb_suspect . '</b> ' . __( 'does not allow followers on Facebook.', 'largo' ) . '</p>' . '<p>' . __( '<a href="https://www.facebook.com/help/201148673283205#How-can-I-let-people-follow-me?">Follow these instructions</a> to allow others to follow you.', 'largo' ) );
			}
		}
	}
}

/**
 * Returns a Twitter username (without the @ symbol)
 *
 * @param 	string $url a twitter url.
 * @return 	string the twitter username extracted from the input string
 * @since 	0.3
 */
function twitter_url_to_username( $url ) {
	$url_parts = explode( '/', $url );
	if ( end( $url_parts ) === '' ) {
		// URL has a trailing slash.
		$url_parts = array_slice( $url_parts, 0 , -1 );
	}
	$username = preg_replace( '/@/', '', end( $url_parts ) );
	// strip the ?&# URL parameters if they're present
	// this will let through all other characters.
	preg_match( '/[^\?&#]+/', $username, $matches );
	if ( isset( $matches[0] ) ) {
		$username = $matches[0];
	}
	return $username;
}

/**
 * Cleans a Twitter url or an @username to the bare username when the user is edited
 *
 * Edits $_POST directly because there's no other way to save the corrected username
 * from this callback. The action hooks this is used for run before edit_user in
 * wp-admin/user-edit.php, which overwrites the user's contact methods. edit_user
 * reads from $_POST.
 *
 * @param  object $user_id the WP_User object being edited.
 * @uses   largo_twitter_url_to_username
 * @link   http://codex.wordpress.org/Plugin_API/Action_Reference/edit_user_profile_update
 * @link   http://codex.wordpress.org/Plugin_API/Action_Reference/personal_options_update
 */
function clean_user_twitter_username( $user_id ) {
	if ( current_user_can( 'edit_user', $user_id ) ) {
		$twitter = twitter_url_to_username( $_POST['mj_user_twitter'] );
		if ( preg_match( '/[^a-zA-Z0-9_]/', $twitter ) ) {
			// it's not a valid twitter username, because it uses an invalid character.
			$twitter = '';
		}
		update_user_meta( $user_id, 'mj_user_twitter', $twitter );
		if ( get_user_meta( $user_id, 'mj_user_twitter', true ) !== $twitter ) {
			wp_die( esc_html__( 'Invalid Twitter username. Please provide either a Twitter profile URL or username.', 'mj' ) );
		}
		$_POST['mj_user_twitter'] = $twitter;
	}
}

/**
 * Checks that the Twitter URL is composed of valid characters [a-zA-Z0-9_] and
 * causes an error if there is not.
 *
 * @param   object $errors the error object.
 * @param   bool   $update whether this is a user update.
 * @param   object $user a WP_User object.
 * @uses    twitter_url_to_username
 * @link    http://codex.wordpress.org/Plugin_API/Action_Reference/user_profile_update_errors
 * @since   0.4
 */
function validate_twitter_username( $errors, $update, $user ) {
	if ( isset( $_POST['mj_user_twitter'] ) ) {
		$tw_suspect = trim( $_POST['mj_user_twitter'] );
		if ( ! empty( $tw_suspect ) ) {
			if ( preg_match( '/[^a-zA-Z0-9_]/', twitter_url_to_username( $tw_suspect ) ) ) {
				// it's not a valid twitter username, because it uses an invalid character.
				$errors->add( 'twitter_username', '<b>' . $tw_suspect . '</b>' . __( 'is an invalid Twitter username.', 'largo' ) . '</p><p>' . __( 'Twitter usernames only use the uppercase and lowercase alphabet letters (a-z A-Z), the Arabic numbers (0-9), and underscores (_).', 'mj' ) );
			}
		}
	}
}

/**
 * Give it a YouTube URL, it'll give you just the video ID
 *
 * @param 	string $url a YouTube URL (e.g. - https://www.youtube.com/watch?v=i5vfw5f1CZo)
 * @return 	string	just the video ID (e.g. - i5vfw5f1CZo)
 * @since 0.4
 */
function youtube_url_to_ID( $url ) {
	parse_str( parse_url( $url, PHP_URL_QUERY ), $var_array );
	$youtubeID = $var_array['v'];
	return $youtubeID;
}

/**
 * For a given YouTube URL, return an iframe to embed
 *
 * @param 	string $url a YouTube URL (e.g. - https://www.youtube.com/watch?v=i5vfw5f1CZo)
 * @param 	bool   $echo return or echo the output
 * @return 	string	a standard YouTube iframe embed code
 * @uses 	largo_youtube_url_to_ID
 * @since 	0.4
 */
function youtube_iframe_from_url( $url, $echo = true ) {
	$output = '<iframe  src="//www.youtube.com/embed/' . youtube_url_to_ID( $url ) . '" frameborder="0" allowfullscreen></iframe>';
	if ( ! $echo ) {
		return $output;
	}
	echo $output;
}

/**
 * For a given YouTube URL, return the image url for various thumbnail sizes
 *
 * @param 	string                                                                         $url a YouTube URL (e.g. - https://www.youtube.com/watch?v=i5vfw5f1CZo)
 * @param	string the image size you'd like (options are: thumb | small | medium | large)
 * @param 	bool                                                                           $echo return or echo the output
 * @return 	string	a youtube image url
 * @uses 	largo_youtube_url_to_ID
 * @since 0.4
 */
function youtube_image_from_url( $url, $size = large, $echo = true ) {
	$id = youtube_url_to_ID( $url );

	$output = 'http://img.youtube.com/vi/' . $id;
	switch ( $size ) {
		case 'thumb':
			$output .= '/default.jpg'; // 120 x 90
			break;
		case 'small':
			$output .= '/hqdefault.jpg'; // 480 x 360
			break;
		case 'medium':
			$output .= '/sddefault.jpg'; // 640 x 480
			break;
		case 'large':
			$output .= '/maxresdefault.jpg'; // 1280 x 720
			break;
	}

	if ( ! $echo ) {
		return $output;
	}
	echo $output;
}

/**
 * Determine whether or not an author has a valid gravatar image
 * see: http://codex.wordpress.org/Using_Gravatars
 *
 * @param string $email an author's email address.
 * @return bool true if a gravatar is available for this user
 */
function has_gravatar( $email ) {
	// Craft a potential url and test its headers.
	$hash = md5( strtolower( trim( $email ) ) );

	$cache_key = 'has_gravatar_' . $hash;
	if ( false !== ( $cache_value = get_transient( $cache_key ) ) ) {
		return (bool) $cache_value;
	}

	$uri = 'http://www.gravatar.com/avatar/' . $hash . '?d=404';
	$response = wp_remote_head( $uri );
	if ( 200 === wp_remote_retrieve_response_code( $response ) ) {
		$cache_value = '1';
	} else {
		$cache_value = '0';
	}
	set_transient( $cache_key, $cache_value );
	return (bool) $cache_value;
}

/**
 * Determine whether or not a user has an avatar. Fallback checks if user has a gravatar.
 *
 * @param string $email an author's email address.
 * @return bool true if an avatar is available for this user
 */
function has_avatar( $email ) {
	$user = get_user_by( 'email', $email );
	$result = get_user_meta( $user->ID, 'mj_author_image_id', true );
	if ( ! empty( $result ) ) {
		return true;
	} else {
		if ( has_gravatar( $email ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Get size information for all currently-registered image sizes.
 *
 * @global $_wp_additional_image_sizes
 * @uses   get_intermediate_image_sizes()
 * @return array $sizes Data for all currently-registered image sizes.
 */
function get_image_sizes() {
	global $_wp_additional_image_sizes;

	$sizes = array();

	foreach ( get_intermediate_image_sizes() as $_size ) {
		if ( in_array( $_size, array( 'thumbnail', 'medium', 'medium_large', 'large' ), true ) ) {
			$sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
			$sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
			$sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
		} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
			$sizes[ $_size ] = array(
				'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
				'height' => $_wp_additional_image_sizes[ $_size ]['height'],
				'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
			);
		}
	}

	return $sizes;
}

/**
 * Get size information for a specific image size.
 *
 * @uses   get_image_sizes()
 * @param  string $size The image size for which to retrieve data.
 * @return bool|array $size Size data about an image size or false if the size doesn't exist.
 */
function get_image_size( $size ) {
	$sizes = get_image_sizes();

	if ( isset( $sizes[ $size ] ) ) {
		return $sizes[ $size ];
	}

	return false;
}

/**
 * Get the width of a specific image size.
 *
 * @uses   get_image_size()
 * @param  string $size The image size for which to retrieve data.
 * @return bool|string $size Width of an image size or false if the size doesn't exist.
 */
function get_image_width( $size ) {
	if ( ! $size = get_image_size( $size ) ) {
		return false;
	}

	if ( isset( $size['width'] ) ) {
		return $size['width'];
	}

	return false;
}

/**
 * Get the height of a specific image size.
 *
 * @uses   get_image_size()
 * @param  string $size The image size for which to retrieve data.
 * @return bool|string $size Height of an image size or false if the size doesn't exist.
 */
function get_image_height( $size ) {
	if ( ! $size = get_image_size( $size ) ) {
		return false;
	}

	if ( isset( $size['height'] ) ) {
		return $size['height'];
	}

	return false;
}

/**
 * See if a given image is square.
 *
 * @param  array $arg an array of image sizes to check.
 * @return bool true if it's square, false if it's not.
 */
function is_square( $arg ) {
	if ( intval( $arg['height'] ) === 0 ) {
		return 0;
	} else {
		return ( $arg['width'] / $arg['height'] ) === 1;
	}
}
