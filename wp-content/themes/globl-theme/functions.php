<?php
function globl_theme_scripts() {
    wp_enqueue_style('globl-style', get_stylesheet_uri());
    wp_enqueue_script('globl-script', get_template_directory_uri() . '/script.js', array(), null, true);
}
add_action('wp_enqueue_scripts', 'globl_theme_scripts');
?>
