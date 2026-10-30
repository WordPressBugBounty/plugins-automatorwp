<?php
/**
 * Ajax Functions
 *
 * @package     AutomatorWP\WhatAvatar\Ajax_Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Ajax function for selecting avatar parts
 *
 * @since 1.0.0
 */
function automatorwp_whatavatar_ajax_get_parts() {

    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    // Permissions check
    if( ! current_user_can( automatorwp_get_manager_capability() ) ) {
        wp_send_json_error( __( 'You\'re not allowed to perform this action.', 'automatorwp' ) );
    }

    $results = automatorwp_whatavatar_get_parts( isset( $_REQUEST['q'] ) ? $_REQUEST['q'] : '' );

    // Prepend option none
    $results = automatorwp_ajax_parse_extra_options( $results );

    // Return our results
    wp_send_json_success( $results );
    die;

}
add_action( 'wp_ajax_automatorwp_whatavatar_get_parts', 'automatorwp_whatavatar_ajax_get_parts', 5 );

/**
 * Function for selecting parts
 *
 * @since 1.0.0
 *
 * @param string $search
 *
 * @return array
 */
function automatorwp_whatavatar_get_parts( $search = '' ) {

    global $wpdb;

    $search = $wpdb->esc_like( $search );

    $results = array();

    $available_parts = array();

    $settings = whatavatar_get_all_editor_settings();
  
    foreach( $settings as $group_id => $group ) {
        foreach( $group['parts'] as $part_id => $part ) {
            $part = wp_parse_args( $part, array(
                'lock' => false,
                'unlock' => '',
            ) );

            if( $part['lock'] && $part['unlock'] === 'automatorwp_action' )
                $available_parts[$part_id] = whatavatar_get_part_label( $part_id );
        }
    }

    foreach( $available_parts as $part_id => $part_label ) {

        if( ! empty( $search ) ) {
            if( strpos( strtolower( $part_label ), strtolower( $search ) ) === false ) {
                continue;
            }
        }

        $results[] = array(
            'id' => $part_id,
            'text' => $part_label,
        );
    }

    return $results;

}

/**
 * Options callback for select2 fields assigned to forms
 *
 * @since 1.0.0
 *
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_whatavatar_options_cb_part( $field ) {

    // Setup vars
    $value = $field->escaped_value;
    $none_value = 'any';
    $none_label = __( 'any avatar part', 'automatorwp' );
    $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );

    if( ! empty( $value ) ) {
        if( ! is_array( $value ) ) {
            $value = array( $value );
        }

        foreach( $value as $part_id ) {

            // Skip option none
            if( $part_id === $none_value ) {
                continue;
            }

            $options[$part_id] = whatavatar_get_part_label( $part_id );
        }
    }

    return $options;

}
