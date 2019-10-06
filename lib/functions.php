<?php


function get_responsive_thumbnail($attachment_id, $size, $icon = false, $attr = array()) {
  global $__responsive_image_sizes;

  if (isset($__responsive_image_sizes) && isset($__responsive_image_sizes[$size])) {
    $responsive_image_sizes = $__responsive_image_sizes[$size];

    if (!$responsive_image_sizes) {
      return $responsive_image_sizes;
    }

    $id = $attachment_id; // gets the id of the current post_thumbnail (in the loop)

    list($url, $width) = wp_get_attachment_image_src($id, $size);

    $attr = array_merge(
      array(
        'alt' => get_the_title($id),
        'src' => $url
      ),
      $attr ?: array()
    );

    ksort($responsive_image_sizes);

    $alt = get_the_title($id); // gets the post thumbnail title

    $output = '<picture>';

    // Inject reference to `$result` into closure scope.
    // $result will get initialized on it's first usage.

    $output.= implode('', array_map(function($max_width) use($responsive_image_sizes, $id) {
      list($url, $width) = wp_get_attachment_image_src($id, $responsive_image_sizes[$max_width]);
      return sprintf('<source media="(max-width: %spx)" srcset="%s %sw"/>', $max_width, $url, $width);
    }, array_keys($responsive_image_sizes)));

    $attr = is_array($attr) ? $attr : array();

    $output.= '<img ' . implode(' ', array_map(function ($key) use($attr) {
      return ' ' . $key . '="' . htmlspecialchars($attr[$key]) . '"';
    }, array_keys($attr))) . '/>';

    $output.= '</picture>';

    return $output;
  }
  return null;
}



function add_responsive_thumbnail($image_size, $responsive_image_sizes) {
  global $__responsive_image_sizes;

  if (!isset($__responsive_image_sizes)) {
    $__responsive_image_sizes = array();
  }

  $__responsive_image_sizes[$image_size] = $responsive_image_sizes;
}


function get_responsive_image_sizes() {
  global $__responsive_image_sizes;
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
