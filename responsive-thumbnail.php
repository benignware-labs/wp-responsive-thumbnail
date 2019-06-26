<?php

/**
 Plugin Name: WP Responsive Thumbnail
 Plugin URI: https://github.com/benignware-labs/wp-reponsive-thumbnail
 Description: Responsive thumbnails per breakpoints
 Version: 0.0.3
 Author: Rafael Nowrotek, Benignware
 Author URI: http://benignware.com
 License: MIT
*/

require_once 'lib/functions.php';

add_filter('post_thumbnail_html', function($html, $post_id = null, $post_thumbnail_id = null, $size = null, $attr = null) {

  $html = get_responsive_thumbnail($post_thumbnail_id, $size, false, $attr) ?: $html;

  return $html;
}, 10, 5);


add_filter( 'image_size_names_choose', function($sizes) {
  global $__responsive_image_sizes;

  if (!isset($__responsive_image_sizes)) {
    return $sizes;
  }

  $sizes = array_merge( array_reduce(array_keys($__responsive_image_sizes), function($result, $size) use($__responsive_image_sizes) {
    $sizes = array_merge(array($size), $__responsive_image_sizes[$size]);

    foreach ($sizes as $size) {
      $result = array_merge($result, array(
        $size => __(implode(' ', array_map('ucwords', preg_split('~[-_\s]+~', $size))))
      ));
    }

    return $result;
  }, array()), $sizes);

  return $sizes;
});
