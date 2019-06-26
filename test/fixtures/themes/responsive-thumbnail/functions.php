<?php
add_action( 'wp_enqueue_scripts', function() {
  wp_deregister_style( 'twentyseventeen-style');
  wp_register_style('twentyseventeen-style', get_template_directory_uri(). '/style.css');
  wp_enqueue_style('twentyseventeen-style', get_template_directory_uri(). '/style.css');
  wp_enqueue_style( 'childtheme-style', get_stylesheet_directory_uri().'/style.css', array('twentyseventeen-style') );
});



// add_image_size( 'twentyseventeen-featured-image', 2000, 1200, true );
add_image_size( 'twentyseventeen-featured-image-tablet', 1024, 620, true );
add_image_size( 'twentyseventeen-featured-image-mobile', 660, 480, true );


if (function_exists('add_responsive_thumbnail')) {
  add_responsive_thumbnail('twentyseventeen-featured-image', array(
    480 => 'twentyseventeen-featured-image-mobile',
    768 => 'twentyseventeen-featured-image-tablet'
  ));
}
