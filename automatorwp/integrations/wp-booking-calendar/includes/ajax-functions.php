<?php

/**
 * Ajax Functions
 *
 * @package     AutomatorWP\BookingCalendar\Ajax_Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Ajax function for selecting bookings
 *
 * @since 1.0.0
 */
function automatorwp_wp_booking_calendar_ajax_get_bookings() {
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

    // Get the bookings
    $bookings = $wpdb->get_results( $wpdb->prepare(
        "SELECT booking_id, sort_date FROM {$wpdb->prefix}booking WHERE  booking_id LIKE %s",
        "%%{$search}%%"
    ) );

    foreach( $bookings as $booking ) {
        $results[] = array(
            'id' => $booking->booking_id,
            'text' => 'Booking ' . $booking->booking_id . ': ' . $booking->sort_date,
        );
    }

    // Prepend option none
    $results = automatorwp_ajax_get_ajax_results_option_none( $results );

    // Return our results
    wp_send_json_success( $results );
    die;

}
add_action( 'wp_ajax_automatorwp_wp_booking_calendar_get_bookings', 'automatorwp_wp_booking_calendar_ajax_get_bookings' );
