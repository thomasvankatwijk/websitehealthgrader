<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'init', 'whg_register_block' );

function whg_register_block() {
    if ( ! function_exists( 'register_block_type' ) ) {
        return;
    }

    wp_register_script(
        'whg-block-editor',
        WHG_URL . 'assets/block-editor.js',
        array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components' ),
        '4.0.0',
        false
    );

    register_block_type( 'whg/health-grader', array(
        'editor_script'   => 'whg-block-editor',
        'render_callback' => 'whg_block_render_callback',
        'attributes'      => array(
            'contactUrl' => array( 'type' => 'string', 'default' => '' ),
        ),
    ) );
}

function whg_block_render_callback( $attributes ) {
    $contact_url = isset( $attributes['contactUrl'] ) ? esc_url( $attributes['contactUrl'] ) : '';
    return whg_render_html( $contact_url );
}
