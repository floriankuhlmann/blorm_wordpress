<?php

// Enqueue Stylesheet and Js for frontend rendering.
add_action( 'wp_enqueue_scripts', 'enqueue_blorm_frontend_theme_style');
add_action( 'wp_enqueue_scripts', 'enqueue_blorm_frontend_js');

function enqueue_blorm_frontend_theme_style() {

    wp_enqueue_style ('blorm-theme-style', plugins_url('blorm/assets/css/blorm_frontend.css'));

}

function enqueue_blorm_frontend_js() {

    wp_enqueue_script ('blorm-theme-js', plugins_url('blorm/assets/js/blorm.js'));

}
