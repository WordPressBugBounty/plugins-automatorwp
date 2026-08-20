<?php
/**
 * Ajax Functions
 *
 * @package     AutomatorWP\FluentCommunity\Ajax_Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Ajax function for selecting spaces
 *
 * @since 1.0.0
 */
function automatorwp_fluentcommunity_ajax_get_spaces() {
    // Security check
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    // Permissions check
    if( ! current_user_can( automatorwp_get_manager_capability() ) ) {
        wp_send_json_error( __( 'You\'re not allowed to perform this action.', 'automatorwp' ) );
    }

    global $wpdb;

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( $_REQUEST['q'] ) : '';

    $results = array();

    // Get the spaces
    $spaces = automatorwp_fluentcommunity_get_spaces();

    foreach( $spaces as $space ) {
        $results[] = array(
            'id' => $space['id'],
            'text' => $space['title'],
        );
    }

    // Prepend option none
    $results = automatorwp_ajax_get_ajax_results_option_none( $results );

    // Return our results
    wp_send_json_success( $results );
    die;

}
add_action( 'wp_ajax_automatorwp_fluentcommunity_get_spaces', 'automatorwp_fluentcommunity_ajax_get_spaces', 5 );

/**
 * Ajax function for selecting courses
 *
 * @since 1.0.0
 */
function automatorwp_fluentcommunity_ajax_get_courses() {
    // Security check
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    // Permissions check
    if( ! current_user_can( automatorwp_get_manager_capability() ) ) {
        wp_send_json_error( __( 'You\'re not allowed to perform this action.', 'automatorwp' ) );
    }

    global $wpdb;

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( $_REQUEST['q'] ) : '';

    $results = array();

    // Get the courses
    $courses = automatorwp_fluentcommunity_get_courses();

    foreach( $courses as $course ) {

        if( ! empty( $search ) ) {
            if( strpos( strtolower( $course['title'] ), strtolower( $search ) ) === false ) {
                continue;
            }
        }

        $results[] = array(
            'id' => $course['id'],
            'text' => $course['title'],
        );
    }

    // Prepend option none
    $results = automatorwp_ajax_get_ajax_results_option_none( $results );

    // Return our results
    wp_send_json_success( $results );
    die;

}
add_action( 'wp_ajax_automatorwp_fluentcommunity_get_courses', 'automatorwp_fluentcommunity_ajax_get_courses', 5 );