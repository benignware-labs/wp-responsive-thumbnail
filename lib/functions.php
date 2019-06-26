<?php


function get_responsive_thumbnail($attachment_id, $size, $icon = false, $attr = array()) {
  global $__responsive_image_sizes;

  if (isset($__responsive_image_sizes) && isset($__responsive_image_sizes[$size])) {
    $responsive_image_sizes = $__responsive_image_sizes[$size];
    // Check to see if a 'retina' class exists in the array when calling "the_post_thumbnail()", if so output different <img/> html

    if (!$responsive_image_sizes) {
      return $responsive_image_sizes;
    }

    $id = $attachment_id; // gets the id of the current post_thumbnail (in the loop)

    list($url, $width) = wp_get_attachment_image_src($id, $size);

    $attr = array(
      'alt' => get_the_title($id),
      'src' => $url
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


    $output.= '<img ' . implode(' ', array_map(function ($key) use($attr) {
      return ' ' . $key . '="' . htmlspecialchars($attr[$key]) . '"';
    }, array_keys($attr))) . '/>';

    $output.= '</picture>';

    return $output;

    /*
    if ($url) {


      $pattern = "~<img[^>]*src=['\"]" . preg_quote($src[0], "~") . "['\"][^>]*\/?>~";
      $matched = preg_match($pattern, $html, $match, PREG_OFFSET_CAPTURE);

      $before_image = "<picture>";
      foreach ($responsive_image_sizes as $width => $image_size) {
        $img_src = wp_get_attachment_image_src($id, $image_size);
        $before_image.= "<source media=\"(max-width: " . $width . "px)\" srcset=\"" . $img_src[0] . " " . $img_src[1] . "w" . "\"/>";
      }
      $after_image = "</picture>";

      if ($matched) {
        $html = substr_replace($html, $before_image . $match[0][0] . $after_image, $match[0][1], strlen($match[0][0]));
      } else {

        $html_attrs = array_merge(array(
          'alt' => get_the_title($id),
          'src' => $src
        ), $attr);

        $str = '<img' . implode(' ', array_map(function ($k, $v) {
          return ' ' . $k .'="'. htmlspecialchars($v) .'"';
        }, array_keys($html_attrs), $html_attrs)) . '/>';


        // Fallback to default img tag:
        echo 'ID: ' . $id . ' SIZE: ' . $size . ' -> SRC: ' . $src . ' -> <pre>' . $str . '</pre>';

        exit;
        $class = isset($attr['class']) ? $attr['class'] : ''; // gets classes passed to the post thumbnail, defined here for easier function access
        $html.= $before_image . '<img src="' . $src[0] . '" class="' . $class . '" />' . $after_image;


        echo $greeting;
        exit;
      }
    }
    */
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
