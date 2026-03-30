<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_shortcode( 'website_health_grader', 'whg_shortcode' );

function whg_shortcode( $atts ) {
    $atts = shortcode_atts( array( 'contact_url' => '' ), $atts, 'website_health_grader' );
    return whg_render_html( $atts['contact_url'] );
}
