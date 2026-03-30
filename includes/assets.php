<?php
if ( ! defined( 'ABSPATH' ) ) exit;
add_action( 'wp_enqueue_scripts', 'whg_enqueue_assets' );
function whg_enqueue_assets() {
    wp_enqueue_style( 'whg-styles', WHG_URL . 'assets/style.css', array(), '4.1.0' );
}