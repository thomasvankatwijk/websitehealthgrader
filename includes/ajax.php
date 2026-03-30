<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'wp_ajax_website_health_grade',        'handle_website_health_grade' );
add_action( 'wp_ajax_nopriv_website_health_grade', 'handle_website_health_grade' );

function handle_website_health_grade() {
    header( 'Content-Type: application/json' );
    header( 'Access-Control-Allow-Origin: *' );
    header( 'Access-Control-Allow-Methods: POST, GET, OPTIONS' );
    header( 'Access-Control-Allow-Headers: Content-Type' );

    $lang_from_post = null;
    if ( isset( $_POST['lang'] ) && in_array( strtolower( trim( (string) $_POST['lang'] ) ), array( 'en', 'nl' ), true ) ) {
        $lang_from_post = strtolower( trim( (string) $_POST['lang'] ) );
    }

    if ( $lang_from_post === 'en' ) {
        $is_english = true;
    } elseif ( $lang_from_post === 'nl' ) {
        $is_english = false;
    } else {
        $is_english = whg_detect_is_english();
    }

    $lang = array(
        'missing_url'      => $is_english ? 'URL parameter is missing.' : 'URL ontbreekt.',
        'invalid_url'      => $is_english ? 'Invalid URL provided.' : 'Ongeldige URL opgegeven.',
        'no_content'       => $is_english ? 'No content received.' : 'Geen inhoud ontvangen.',
        'no_response'      => $is_english ? 'Website not reachable.' : 'Website niet bereikbaar.',
        'title_ok'         => $is_english ? 'Title Tag: Present and optimized.' : 'Titel Tag: Aanwezig en goed geoptimaliseerd.',
        'title_fail'       => $is_english ? 'Title Tag: Missing or not optimal length (30-60 characters).' : 'Titel Tag: Ontbreekt of niet optimale lengte.',
        'meta_ok'          => $is_english ? 'Meta Description: Present.' : 'Meta Description: Aanwezig.',
        'meta_fail'        => $is_english ? 'Meta Description: Missing or not optimal length.' : 'Meta Description: Ontbreekt of niet optimale lengte.',
        'h1_ok'            => $is_english ? 'H1 Heading: Found.' : 'H1 Kop: Gevonden.',
        'h1_fail'          => $is_english ? 'H1 Heading: Not found.' : 'H1 Kop: Niet gevonden.',
        'viewport_ok'      => $is_english ? 'Mobile-Friendly: Viewport tag present.' : 'Mobielvriendelijk: Viewport tag aanwezig.',
        'viewport_fail'    => $is_english ? 'Mobile-Friendly: Viewport tag missing.' : 'Mobielvriendelijk: Viewport tag ontbeekt.',
        'robots_ok'        => $is_english ? 'Robots.txt: Found.' : 'Robots.txt: Gevonden.',
        'robots_warn'      => $is_english ? 'Robots.txt: Not found.' : 'Robots.txt: Niet gevonden.',
        'sitemap_ok'       => $is_english ? 'Sitemap: Found.' : 'Sitemap: Gevonden.',
        'sitemap_warn'     => $is_english ? 'Sitemap: Not found.' : 'Sitemap: Niet gevonden.',
        'images_excellent' => $is_english ? 'Image Alt Tags: %d of %d images have alt tags.' : 'Afbeeldingen Alt Tags: %d van %d hebben alt tags.',
        'images_none'      => $is_english ? 'Image Alt Tags: No images found.' : 'Afbeeldingen Alt Tags: Geen afbeeldingen gevonden.',
        'links_info'       => $is_english ? 'Link Structure: %d internal and %d external links.' : 'Linkstructuur: %d interne en %d externe links.',
        'perf_excellent'   => $is_english ? 'Performance: Response time of %ss.' : 'Prestaties: Responstijd van %ss.',
        'broken_none'      => $is_english ? 'Broken Links: None found among first %d checked.' : 'Gebroken links: Geen gevonden.',
    );

    if ( empty( $_POST['url'] ) ) {
        wp_send_json_error( array( 'message' => $lang['missing_url'] ) );
        wp_die();
    }

    $url = esc_url_raw( trim( (string) $_POST['url'] ) );
    if ( ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
        wp_send_json_error( array( 'message' => $lang['invalid_url'] ) );
        wp_die();
    }

    $start_time = microtime( true );
    $response   = wp_remote_get( $url, array( 'timeout' => 20, 'sslverify' => false ) );

    if ( is_wp_error( $response ) ) {
        wp_send_json_error( array( 'message' => $lang['no_response'] ) );
        wp_die();
    }

    $html_body = wp_remote_retrieve_body( $response );
    if ( empty( $html_body ) ) {
        wp_send_json_error( array( 'message' => $lang['no_content'] ) );
        wp_die();
    }

    $fetch_time = round( microtime( true ) - $start_time, 2 );
    $score = 0;
    $checks = array();

    // -- Simplified Analysis Logic --
    // Title
    if ( preg_match( '/<title[^>]*>(.*?)<\/title>/is', $html_body, $m ) && strlen(trim($m[1])) >= 30 ) {
        $checks[] = array( 'text' => $lang['title_ok'], 'status' => 'pass' ); $score += 20;
    } else { $checks[] = array( 'text' => $lang['title_fail'], 'status' => 'fail' ); }

    // H1
    if ( preg_match( '/<h1[^>]*>.*?<\/h1>/i', $html_body ) ) {
        $checks[] = array( 'text' => $lang['h1_ok'], 'status' => 'pass' ); $score += 20;
    } else { $checks[] = array( 'text' => $lang['h1_fail'], 'status' => 'fail' ); }

    // Performance
    if ($fetch_time < 1.5) {
        $checks[] = array( 'text' => sprintf($lang['perf_excellent'], $fetch_time), 'status' => 'pass' ); $score += 30;
    }

    // Viewport
    if (stripos($html_body, 'viewport') !== false) {
        $checks[] = array( 'text' => $lang['viewport_ok'], 'status' => 'pass' ); $score += 30;
    }

    wp_send_json( array( 'status' => 'success', 'score' => min($score, 100), 'checks' => $checks ) );
    wp_die();
}