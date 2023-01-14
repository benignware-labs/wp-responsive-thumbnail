<?php
namespace benignware\responsive_thumbnail {
  function get_image_sizes() {
    global $_wp_additional_image_sizes;
  
    $sizes = array_reduce(get_intermediate_image_sizes(), function($result, $size) {
      global $_wp_additional_image_sizes;
  
      if ( in_array( $_size, array('thumbnail', 'medium', 'medium_large', 'large') ) ) {
        $result[$size] = array(
          'width' => get_option( "{$size}_size_w" ),
          'height' => get_option( "{$size}_size_h" ),
          'crop' => get_option( "{$size}_size_crop" )
        );
      } elseif ( isset( $_wp_additional_image_sizes[ $size ] ) ) {
        $result[$size] = $_wp_additional_image_sizes[ $size ];
      }
  
      return $result;
    }, array());
  
    return $sizes;
  }

  function get_responsive_image_sizes($size = null) {
    $responsive_image_sizes = [];
  
    $args = get_theme_support('responsive-thumbnails');
  
    if (isset($args) && isset($args[0])) {
      $responsive_image_sizes = array_merge($responsive_image_sizes, $args[0]);
    }

    if ($size !== null) {
      return isset($responsive_image_sizes[$size]) ? $responsive_image_sizes[$size] : null;
    }
  
    return $responsive_image_sizes;
  }

  function get_responsive_thumbnail($attachment_id, $size, $attr = array()) {
    $responsive_image_sizes = get_responsive_image_sizes();
  
    if (isset($responsive_image_sizes) && isset($responsive_image_sizes[$size])) {
      $responsive_image_size = $responsive_image_sizes[$size];
  
      $id = $attachment_id; // gets the id of the current post_thumbnail (in the loop)
  
      list($url, $width) = wp_get_attachment_image_src($id, $size);
  
      $attr = array_merge(
        array(
          'alt' => get_the_title($id),
          'src' => $url
        ),
        $attr ?: array()
      );
  
      ksort($responsive_image_size);
  
      $alt = get_the_title($id);
  
      $output = '<picture>';
  
      $output.= implode('', array_map(function($max_width) use ($responsive_image_size, $id) {
        list($url, $width) = wp_get_attachment_image_src($id, $responsive_image_size[$max_width]);
        
        return sprintf('<source media="(max-width: %spx)" srcset="%s %sw"/>', $max_width, $url, $width);
      }, array_keys($responsive_image_size)));
  
      $attr = is_array($attr) ? $attr : array();
  
      $output.= '<img ' . implode(' ', array_map(function ($key) use($attr) {
        return ' ' . $key . '="' . htmlspecialchars($attr[$key]) . '"';
      }, array_keys($attr))) . '/>';

      $output.= '</picture>';
      return $output;
    }

    return null;
  }
}
