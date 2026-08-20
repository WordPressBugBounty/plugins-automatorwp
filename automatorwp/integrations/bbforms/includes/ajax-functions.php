<?php
/**
 * Ajax Functions
 *
 * @package     AutomatorWP\BBForms\Ajax_Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Ajax function for selecting forms
 *
 * @since 1.0.0
 */
function automatorwp_bbforms_ajax_get_forms() {

    // Security check
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    // Permissions check
    if( ! current_user_can( automatorwp_get_manager_capability() ) ) {
        wp_send_json_error( __( 'You\'re not allowed to perform this action.', 'automatorwp' ) );
    }

    $results = automatorwp_bbforms_get_forms( isset( $_REQUEST['q'] ) ? $_REQUEST['q'] : '' );

    // Prepend option none
    $results = automatorwp_ajax_get_ajax_results_option_none( $results );

    // Return our results
    wp_send_json_success( $results );
    die;

}
add_action( 'wp_ajax_automatorwp_bbforms_get_forms', 'automatorwp_bbforms_ajax_get_forms', 5 );

/**
 * Function for selecting forms
 *
 * @since 1.0.0
 *
 * @param string $search
 *
 * @return array
 */
function automatorwp_bbforms_get_forms( $search = '' ) {

    global $wpdb;

    $search = $wpdb->esc_like( $search );

    $results = array();

    // Setup table
    $ct_table = ct_setup_table( 'bbforms_forms' );

    $forms = $wpdb->get_results( $wpdb->prepare(
        "SELECT id, title
        FROM {$ct_table->db->table_name}
        WHERE title LIKE %s",
        "%%{$search}%%"
    ) );

    ct_reset_setup_table();

    foreach( $forms as $form ) {

        if( $form->title === '' ) $form->title = '(no title)';

        $results[] = array(
            'id' => $form->id,
            'text' => $form->title,
        );
    }

    return $results;

}
