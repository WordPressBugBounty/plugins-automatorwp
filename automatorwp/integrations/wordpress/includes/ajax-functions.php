<?php
/**
 * Ajax Functions
 *
 * @package     AutomatorWP\Integrations\WordPress\Ajax_Functions
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Ajax function for selecting taxonomies
 *
 * @since 1.0.0
 */
function automatorwp_wordpress_ajax_get_taxonomies() {
    // Security check
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    // Permissions check
    if( ! current_user_can( automatorwp_get_manager_capability() ) ) {
        wp_send_json_error( __( 'You\'re not allowed to perform this action.', 'automatorwp' ) );
    }

    $results = automatorwp_wordpress_get_taxonomies( isset( $_REQUEST['q'] ) ? $_REQUEST['q'] : '' );

    // Prepend option none
    $results = automatorwp_ajax_parse_extra_options( $results );

    // Return our results
    wp_send_json_success( $results );
    die;

}
add_action( 'wp_ajax_automatorwp_wordpress_get_taxonomies', 'automatorwp_wordpress_ajax_get_taxonomies' );

/**
 * Function for selecting taxonomies
 *
 * @since 1.0.0
 *
 * @param string $search
 *
 * @return array
 */
function automatorwp_wordpress_get_taxonomies( $search = '' ) {

    global $wpdb;

    // Pull back the search string
    $search = $wpdb->esc_like( sanitize_text_field( $search ) );

    $taxonomies = automatorwp_wordpress_get_public_taxonomies();

    $results = array();

    // Parse terms results to match select2 results
    foreach ( $taxonomies as $taxonomy ) {

        if( ! empty( $search ) ) {
            if( strpos( strtolower( $taxonomy['name'] ), strtolower( $search ) ) === false ) {
                continue;
            }
        }

        $results[] = array(
            'id'   => $taxonomy['id'],
            'text' => $taxonomy['name']
        );
    }

    return $results;

}

/**
 * Ajax function for selecting terms
 *
 * @since 1.0.0
 */
function automatorwp_wordpress_ajax_get_terms() {
    // Security check
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    // Permissions check
    if( ! current_user_can( automatorwp_get_manager_capability() ) ) {
        wp_send_json_error( __( 'You\'re not allowed to perform this action.', 'automatorwp' ) );
    }

    $results = automatorwp_wordpress_get_terms(
        isset( $_REQUEST['q'] ) ? $_REQUEST['q'] : '',
        isset( $_REQUEST['table'] ) ? $_REQUEST['table'] : ''
    );

    // Prepend option none
    $results = automatorwp_ajax_parse_extra_options( $results );

    // Return our results
    wp_send_json_success( $results );
    die;

}
add_action( 'wp_ajax_automatorwp_wordpress_get_terms', 'automatorwp_wordpress_ajax_get_terms' );

/**
 * Function for selecting terms
 *
 * @since 1.0.0
 *
 * @param string $search
 *
 * @return array
 */
function automatorwp_wordpress_get_terms( $search = '', $taxonomy_id = '' ) {

    global $wpdb;

    // Pull back the search string
    $search = $wpdb->esc_like( sanitize_text_field( $search ) );

    // Get the taxonomy
    $taxonomy_id = sanitize_text_field( $taxonomy_id );

    $terms = automatorwp_wordpress_get_taxonomy_terms( $taxonomy_id );

    $results = array();

    // Parse terms results to match select2 results
    foreach ( $terms as $term ) {

        if( ! empty( $search ) ) {
            if( strpos( strtolower( $term['name'] ), strtolower( $search ) ) === false ) {
                continue;
            }
        }

        $results[] = array(
            'id'   => $term['id'],
            'text' => $term['name']
        );
    }

    return $results;

}