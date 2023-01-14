<?php
/**
 Plugin Name: Responsive Thumbnail
 Plugin URI: https://github.com/benignware-labs/wp-reponsive-thumbnail
 Description: Responsive thumbnails per breakpoints
 Version: 1.0.0-beta.1
 Author: Rafael Nowrotek, Benignware
 Author URI: http://benignware.com
 License: MIT
*/

require_once 'lib/functions.php';

use function benignware\responsive_thumbnail\get_responsive_thumbnail;
use function benignware\responsive_thumbnail\get_responsive_image_sizes;

/*
add_filter('post_thumbnail_html', function($html, $post_id = null, $post_thumbnail_id = null, $size = null, $attr = array()) {
  $args = get_theme_support('responsive-thumbnails');

  if (!isset($args) || !isset($args[0]) || !isset($args[0][$size])) {
    return $html;
  }

  // Merge
  $doc = new DOMDocument();
  @$doc->loadHTML(sprintf("<?xml encoding=\"utf-8\" ?>%s", $html));
  $doc_xpath = new DOMXpath($doc);


  $img = $doc_xpath->query("//img")->item(0);

  if (!$img) {
    return $html;
  }

  $thumbnail_content_html = '';

  if ($img->tagName === 'img') {
    $thumbnail_content_html = get_responsive_thumbnail($post_thumbnail_id, $size, false, $attr);
  }

  if (!$thumbnail_content_html) {
    $thumbnail_content_html = $doc->saveHTML($img);
  }

  $template = <<< EOT
  <div class="responsive-thumbnail">
    %s
  </div>
  EOT;

  $thumbnail_html = sprintf($template, $thumbnail_content_html);
 
  $thumbnail_doc = new DOMDocument();
  @$thumbnail_doc->loadHTML('<?xml encoding="utf-8" ?>' . $thumbnail_html );
  $thumbnail_doc_xpath = new DOMXpath($thumbnail_doc);
  $thumbnail_root_elems = $thumbnail_doc_xpath->query('/html/body/*');

  $thumbnail_img = $thumbnail_doc->getElementsByTagName('img')->item(0);

  if ($thumbnail_img) {
    foreach ($img->attributes as $key => $attr) {
      if (in_array($key, ['srcset', 'sizes', 'width', 'height'])) {
        continue;
      }
  
      $thumbnail_img->setAttribute($key, $attr->value);
    }
  }

  foreach ($thumbnail_root_elems as $thumbnail_root_elem) {
    $thumbnail_root_elem_imp = $doc->importNode($thumbnail_root_elem, true);
    $img->parentNode->insertBefore($thumbnail_root_elem_imp->cloneNode(true), $img);
  }

  $img->parentNode->removeChild($img);

  $html = preg_replace('~(?:<\?[^>]*>|<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>)\s*~i', '', $doc->saveHTML());

  return $html;
}, 110, 5);
*/

add_filter('wp_get_attachment_image', function($html, $attachment_id, $size, $icon, $attr) {
  $args = get_theme_support('responsive-thumbnails');

  if (!isset($args) || !isset($args[0]) || !isset($args[0][$size])) {
    return $html;
  }

  // Merge
  $doc = new DOMDocument();
  @$doc->loadHTML('<?xml encoding="utf-8" ?>' . $html);
  $doc_xpath = new DOMXpath($doc);

  $img = $doc_xpath->query("//img")->item(0);

  if (!$img) {
    return $html;
  }

  $thumbnail_content_html = '';

  if ($img->tagName === 'img') {
    $thumbnail_content_html = get_responsive_thumbnail($attachment_id, $size, false, $attr);
  }

  if (!$thumbnail_content_html) {
    $thumbnail_content_html = $doc->saveHTML($img);
  }

  $thumbnail_html = $thumbnail_content_html;
 
  $thumbnail_doc = new DOMDocument();
  @$thumbnail_doc->loadHTML('<?xml encoding="utf-8" ?>' . $thumbnail_html );
  $thumbnail_doc_xpath = new DOMXpath($thumbnail_doc);
  $thumbnail_root_elems = $thumbnail_doc_xpath->query('/html/body/*');

  $thumbnail_img = $thumbnail_doc->getElementsByTagName('img')->item(0);

  if ($thumbnail_img) {
    foreach ($img->attributes as $key => $attr) {
      if (in_array($key, ['srcset', 'sizes', 'width', 'height'])) {
        continue;
      }
  
      $thumbnail_img->setAttribute($key, $attr->value);
    }
  }

  foreach ($thumbnail_root_elems as $thumbnail_root_elem) {
    $thumbnail_root_elem_imp = $doc->importNode($thumbnail_root_elem, true);
    $img->parentNode->insertBefore($thumbnail_root_elem_imp->cloneNode(true), $img);
  }

  $img->parentNode->removeChild($img);

  $html = preg_replace('~(?:<\?[^>]*>|<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>)\s*~i', '', $doc->saveHTML());

  return $html;
}, 10, 5);

// add_filter('post_thumbnail_html', function($html, $post_id = null, $post_thumbnail_id = null, $size = null, $attr = null) {
//   global $__responsive_image_sizes;

//   if (preg_match('~^\s*<picture~', $html) || preg_match('~^\s*<img~', $html)) {
//     return $html;
//   }

//   $sizes = get_responsive_image_sizes();
//   $size_data = $sizes[$size];
//   $width = $size_data['width'];
//   $height = $size_data['height'];

//   $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $width . ' ' . $height . '"><rect x="0" y="0" width="' . $width . '" height="' . $height . '"/></svg>';
//   $url = 'data:image/svg+xml;utf8, ' . rawurlencode($svg);

//   $img = '';
//   if (isset($__responsive_image_sizes) && isset($__responsive_image_sizes[$size])) {
//     $responsive_image_sizes = $__responsive_image_sizes[$size];
//     $ris = array_map(function($size, $max_width) use($sizes) {
//       return $max_width;
//     }, $responsive_image_sizes, array_keys($responsive_image_sizes));

//     $img.= '<style>';
//     $img.= implode('', array_map(function($max_width, $index) use($ris) {
//       $minq = null;
//       $maxq = null;

//       if ($index > 0) {
//         $min_width = $ris[$index - 1] + 0.0001;
//         $minq = "(min-width: {$min_width}px)";
//       }

//       if ($index < count($ris)) {
//         $maxq = "(max-width: {$max_width}px)";
//       }

//       $mq = ($minq && $maxq) ? $minq . ' and ' . $maxq : ($maxq ? $maxq : $minq);

//       $html = <<<EOT

// @media screen and {$mq} {
//   /* $max_width ---- $index */

//   img[data-max-width]:not(*[data-max-width="{$max_width}"]) {
//     display: none;
//   }

// EOT;

// $html.= <<<EOT

// }

// EOT;

// if ($index === count($ris) -1) {
//   $min_width = $max_width + 0.0001;
//   $minq = "(min-width: {$min_width}px)";
//   $html.= <<<EOT

// @media screen and {$minq} {
//   img[data-max-width]:not(*[data-max-width="none"]) {
//     display: none;
//   }
// }

// EOT;

// }

//       return $html;
//       // return '<source media="(max-width: ' . $max_width . 'px)" srcset="' . $url . ' ' . $width . 'px"/>';
//     }, $ris, array_keys($ris)));
//     $img.= '</style>';

//     $img.= implode('', array_map(function($max_width) use($responsive_image_sizes, $sizes) {
//       $constraints = $sizes[$responsive_image_sizes[$max_width]];
//       $width = $constraints['width'];
//       $height = $constraints['height'];

//       $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $width . ' ' . $height . '"><rect x="0" y="0" width="' . $width . '" height="' . $height . '"/></svg>';
//       $url = 'data:image/svg+xml;utf8, ' . rawurlencode($svg);

//       $html = '<img data-max-width="' . $max_width . '" class="responsive-thumbnail-scaler" src="' . $url . '"/>';

//       return $html;
//     }, array_keys($responsive_image_sizes)));

//     $img.= '<img data-max-width="none" class="responsive-thumbnail-scaler" src="' . $url . '"/>';

//   } else {

//     $img = '<img class="responsive-thumbnail-scaler" src="' . $url . '"/>';
//   }

//   $html = <<<EOT

// <div class="responsive-thumbnail aspectRatioSizer" data-responsive-thumbnail-size="$size">

//     $img
//     <div class="responsive-thumbnail-content">
//       $html
//     </div>

// </div>

// EOT;


//   return $html;
// }, 100, 5);


// add_filter( 'image_size_names_choose', function($sizes) {
//   global $__responsive_image_sizes;

//   if (!isset($__responsive_image_sizes)) {
//     return $sizes;
//   }

//   $sizes = array_merge( array_reduce(array_keys($__responsive_image_sizes), function($result, $size) use($__responsive_image_sizes) {
//     $sizes = array_merge(array($size), $__responsive_image_sizes[$size]);

//     foreach ($sizes as $size) {
//       $result = array_merge($result, array(
//         $size => __(implode(' ', array_map('ucwords', preg_split('~[-_\s]+~', $size))))
//       ));
//     }

//     return $result;
//   }, array()), $sizes);

//   return $sizes;
// });


// add_action('init', function() {
//   global $__responsive_image_sizes;
//   global $_wp_additional_image_sizes;

//   $sizes = array_reduce(get_intermediate_image_sizes(), function($result, $size) {
//     global $_wp_additional_image_sizes;

//     if ( in_array( $size, array('thumbnail', 'medium', 'medium_large', 'large') ) ) {
//       $result[$size] = array(
//         'width' => get_option( "{$size}_size_w" ),
//         'height' => get_option( "{$size}_size_h" ),
//         'crop' => get_option( "{$size}_size_crop" )
//       );
//     } elseif ( isset( $_wp_additional_image_sizes[ $size ] ) ) {
//       $result[$size] = $_wp_additional_image_sizes[ $size ];
//     }

//     return $result;
//   }, array());

//   print_r($sizes);
// });

// add_action('init', function() {
//   wp_register_script(
//     'responsive-thumbnail-js', // Handle.
//     plugins_url( '/js/responsive-thumbnail.js',  __FILE__  ),
//     null, // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.build.js' ), // Version: filemtime — Gets file modification time.
//     true // Enqueue the script in the footer.
//   );

//   wp_register_style(
//     'responsive-thumbnail-css', // Handle.
//     plugins_url( '/css/responsive-thumbnail.css',  __FILE__  )
//   );

//   wp_localize_script( 'responsive-thumbnail-js', 'ResponsiveThumbnail',
//     array(
//       'data' => json_encode(array(
//         'options' => array(
//           'sizes' => $sizes
//         )
//       ))
//     )
//   );

//   wp_enqueue_script( 'responsive-thumbnail-js' );
//   wp_enqueue_style( 'responsive-thumbnail-css' );
// }, 90, 5);
