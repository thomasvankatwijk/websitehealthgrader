<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Detect whether the request should use English based on the referrer URL.
 */
function whg_detect_is_english() {
    $ref = wp_get_referer();

    if ( empty( $ref ) && ! empty( $_SERVER['HTTP_REFERER'] ) ) {
        $ref = wp_unslash( $_SERVER['HTTP_REFERER'] );
    }

    if ( empty( $ref ) ) {
        return false;
    }

    $parts = parse_url( $ref );

    // Host: en.example.com
    if ( isset( $parts['host'] ) && preg_match( '#^en\.#i', $parts['host'] ) ) {
        return true;
    }

    // Path: /en/ or /something/en/
    $path = isset( $parts['path'] ) ? trim( $parts['path'], '/' ) : '';
    if ( $path !== '' ) {
        foreach ( explode( '/', $path ) as $seg ) {
            if ( strtolower( $seg ) === 'en' ) {
                return true;
            }
        }
    }

    // Query: ?lang=en or ?locale=en_US
    if ( isset( $parts['query'] ) ) {
        parse_str( $parts['query'], $q );
        if ( isset( $q['lang'] ) && strtolower( $q['lang'] ) === 'en' ) {
            return true;
        }
        if ( isset( $q['locale'] ) && stripos( $q['locale'], 'en' ) === 0 ) {
            return true;
        }
    }

    return false;
}

/**
 * Return the HTML template for the grader widget.
 *
 * @param string $contact_page_url Optional override for the contact button URL.
 */
function whg_render_html( $contact_page_url = '' ) {
    if ( empty( $contact_page_url ) && function_exists( 'pll_get_post' ) ) {
        $contact_page_url = esc_url( get_page_link( pll_get_post( 5 ) ) );
    }

    ob_start();
    include WHG_PATH . 'templates/widget.php';
    return ob_get_clean();
}
