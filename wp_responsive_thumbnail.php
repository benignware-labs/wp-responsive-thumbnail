<?php

/**
 Plugin Name: WP Responsive Thumbnail
 Plugin URI: http://github.com/benignware-labs/wp-responsive-thumbnail
 Description: Responsive thumbnails per breakpoints
 Version: 0.0.1
 Author: Rafael Nowrotek, Benignware
 Author URI: http://benignware.com
 License: MIT
*/


function wp_responsive_thumbnail_html($html, $post_id, $post_thumbnail_id, $size, $attr) {
  global $__responsive_image_sizes;
  if (isset($__responsive_image_sizes) && isset($__responsive_image_sizes[$size])) {
    $responsive_image_sizes = $__responsive_image_sizes[$size];
      
    $id = $post_thumbnail_id; // gets the id of the current post_thumbnail (in the loop)
    $src = wp_get_attachment_image_src($id, $size); // gets the image url specific to the passed in size (aka. custom image size)
    
    if ($src) {
      $alt = get_the_title($id); // gets the post thumbnail title
    
      $pattern = "~<img[^>]*src=['\"]" . preg_quote($src[0], "~") . "['\"][^>]*\/?>~";
      $matched = preg_match($pattern, $html, $match, PREG_OFFSET_CAPTURE);
      
      echo $attr['class'];
      
      $before_image = "<picture>";
      foreach ($responsive_image_sizes as $width => $image_size) {
        $img_src = wp_get_attachment_image_src($id, $image_size);
        $before_image.= "<source media=\"(max-width: " . $width . "px)\" srcset=\"" . $img_src[0] . " " . $img_src[1] . "w" . "\"/>"; 
      }
      $after_image = "</picture>";
      
      if ($matched) {
        $html = substr_replace($html, $before_image . $match[0][0] . $after_image, $match[0][1], strlen($match[0][0]));
      } else {
        // Fallback to default img tag:
        $class = isset($attr['class']) ? $attr['class'] : ''; // gets classes passed to the post thumbnail, defined here for easier function access
        $html.= $before_image . '<img src="' . $src[0] . '" class="' . $class . '" />' . $after_image;
      }
    }
    
  }
  return $html;
}
add_filter('post_thumbnail_html', 'wp_responsive_thumbnail_html', 99, 5);


function add_responsive_thumbnail($image_size, $responsive_image_sizes) {
  global $__responsive_image_sizes;
  if (!isset($__responsive_image_sizes)) {
    $__responsive_image_sizes = array();
  }
  $__responsive_image_sizes[$image_size] = $responsive_image_sizes;
}