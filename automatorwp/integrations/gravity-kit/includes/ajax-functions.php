<?php
/**
 * Ajax Functions
 *
 * @package     AutomatorWP\Gravity_Kit\Ajax_Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Ajax function for selecting forms
 *
 * @since 1.0.0
 */
function automatorwp_gravity_kit_ajax_get_forms() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    global $wpdb;

    $results = array();

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( $_REQUEST['q'] ) : '';

    // Get the forms
    $forms = RGFormsModel::get_forms();

    foreach( $forms as $form ) {

        // Results should meet the Select2 structure
        $results[] = array(
            'id' => $form->id,
            'text' => $form->title,
        );

    }

    // Prepend option none
    $results = automatorwp_ajax_get_ajax_results_option_none( $results );

    // Return our results
    wp_send_json_success( $results );
    die;

}
add_action( 'wp_ajax_automatorwp_gravity_kit_get_forms', 'automatorwp_gravity_kit_ajax_get_forms', 5 );