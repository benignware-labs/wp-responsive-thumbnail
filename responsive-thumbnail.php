<?php
/**
 Plugin Name: Responsive Thumbnail
 Plugin URI: https://github.com/benignware-labs/wp-reponsive-thumbnail
 Description: Responsive thumbnails per breakpoints
 Version: 1.0.2
 Author: Rafael Nowrotek, Benignware
 Author URI: http://benignware.com
 License: MIT
*/

require_once 'lib/functions.php';

use function benignware\responsive_thumbnail\get_responsive_thumbnail;
use function benignware\responsive_thumbnail\get_responsive_image_sizes;

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

  // Override attributes
  if ($thumbnail_img) {
    foreach ($img->attributes as $key => $attr) {
      if (in_array($key, ['alt', 'src', 'srcset', 'sizes', 'width', 'height'])) {
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
