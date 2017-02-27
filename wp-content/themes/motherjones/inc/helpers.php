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
 		$r = hexdec( substr( $color, 0, 1 ).substr( $color, 0, 1 ) );
 		$g = hexdec( substr( $color, 1, 1 ).substr( $color, 1, 1 ) );
 		$b = hexdec( substr( $color, 2, 1 ).substr( $color, 2, 1 ) );
 	} else if ( strlen( $color ) === 6 ) {
 		$r = hexdec( substr( $color, 0, 2 ) );
 		$g = hexdec( substr( $color, 2, 2 ) );
 		$b = hexdec( substr( $color, 4, 2 ) );
 	} else {
 		return array();
 	}
 	return array( 'red' => $r, 'green' => $g, 'blue' => $b );
}
