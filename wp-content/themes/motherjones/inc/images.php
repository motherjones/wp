<?php
/**
 * Some image utilities and mods
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

	/**
	 * Add custom image sizes attribute to enhance responsive image functionality
	 * for content images
	 *
	 * @since Mother Jones 1.0
	 *
	 * @param string $sizes A source size value for use in a 'sizes' attribute.
	 * @param array  $size  Image size. Accepts an array of width and height
	 *                      values in pixels (in that order).
	 * @return string A source size value for use in a content image 'sizes' attribute.
	 */
function mj_content_image_sizes_attr( $sizes, $size ) {
		$width = $size[0];

		840 <= $width && $sizes = '(max-width: 709px) 85vw, (max-width: 909px) 67vw, (max-width: 1362px) 62vw, 840px';

	if ( 'page' === get_post_type() ) {
		840 > $width && $sizes = '(max-width: ' . $width . 'px) 85vw, ' . $width . 'px';
	} else {
		840 > $width && 600 <= $width && $sizes = '(max-width: 709px) 85vw, (max-width: 909px) 67vw, (max-width: 984px) 61vw, (max-width: 1362px) 45vw, 600px';
		600 > $width && $sizes = '(max-width: ' . $width . 'px) 85vw, ' . $width . 'px';
	}

		return $sizes;
}
	add_filter( 'wp_calculate_image_sizes', 'mj_content_image_sizes_attr', 10 , 2 );

	/**
	 * Add custom image sizes attribute to enhance responsive image functionality
	 * for post thumbnails
	 *
	 * @since Mother Jones 1.0
	 *
	 * @param array $attr Attributes for the image markup.
	 * @param int   $attachment Image attachment ID.
	 * @param array $size Registered image size or flat array of height and width dimensions.
	 * @return string A source size value for use in a post thumbnail 'sizes' attribute.
	 */
function mj_post_thumbnail_sizes_attr( $attr, $attachment, $size ) {
	if ( 'post-thumbnail' === $size ) {
		is_active_sidebar( 'sidebar-1' ) && $attr['sizes'] = '(max-width: 709px) 85vw, (max-width: 909px) 67vw, (max-width: 984px) 60vw, (max-width: 1362px) 62vw, 840px';
		! is_active_sidebar( 'sidebar-1' ) && $attr['sizes'] = '(max-width: 709px) 85vw, (max-width: 909px) 67vw, (max-width: 1362px) 88vw, 1200px';
	}
		return $attr;
}
	add_filter( 'wp_get_attachment_image_attributes', 'mj_post_thumbnail_sizes_attr', 10 , 3 );
