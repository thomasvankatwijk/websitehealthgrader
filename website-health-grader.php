<?php
/**
 * Plugin Name: Website Health Grader Plugin
 * Description: Grade a website's health for SEO and performance. Default Dutch, English when caller page contains /en/. Renders via shortcode [website_health_grader] and a Gutenberg block.
 * Version: 4.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'WHG_PATH', plugin_dir_path( __FILE__ ) );
define( 'WHG_URL',  plugin_dir_url( __FILE__ ) );

require_once WHG_PATH . 'includes/helpers.php';
require_once WHG_PATH . 'includes/ajax.php';
require_once WHG_PATH . 'includes/shortcode.php';
require_once WHG_PATH . 'includes/block.php';
require_once WHG_PATH . 'includes/assets.php';