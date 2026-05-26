<?php
/**
 * Tags
 *
 * @package     AutomatorWP\Integrations\BookingPress\Tags
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Customer tags
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_bookingpress_get_customer_tags() {

    return array(
        'customer_id' => array(
            'label'     => __( 'Customer ID', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => '123',
        ),
        'customer_first_name' => array(
            'label'     => __( 'Customer first name', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'John',
        ),
        'customer_last_name' => array(
            'label'     => __( 'Customer last name', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'Doe',
        ),
        'customer_email' => array(
            'label'     => __( 'Customer email', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'contact@automatorwp.com',
        ),
        'customer_phone' => array(
            'label'     => __( 'Customer phone', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => '123456789',
        ),
    );

}

/**
 * Custom trigger tag replacement
 *
 * @since 1.0.0
 *
 * @param string    $replacement    The tag replacement
 * @param string    $tag_name       The tag name (without "{}")
 * @param stdClass  $trigger        The trigger object
 * @param int       $user_id        The user ID
 * @param string    $content        The content to parse
 * @param stdClass  $log            The last trigger log object
 *
 * @return string
 */
function automatorwp_bookingpress_get_trigger_customer_tag_replacement( $replacement, $tag_name, $trigger, $user_id, $content, $log ) {


    $trigger_args = automatorwp_get_trigger( $trigger->type );

    // Skip if trigger is not from this integration
    if( $trigger_args['integration'] !== 'bookingpress' ) {
        return $replacement;
    }

    $customer_id = automatorwp_get_log_meta( $log->id, 'customer_id', true );
    $customer = automatorwp_bookingpress_get_customer_data( $customer_id );

    switch( $tag_name ) {
        case 'customer_id':
            $replacement = $customer->bookingpress_customer_id;
            break;
        case 'customer_first_name':
            $replacement = $customer->bookingpress_firstname;
            break;
        case 'customer_last_name':
            $replacement = $customer->bookingpress_lastname;
            break;
        case 'customer_email':
            $replacement = $customer->bookingpress_user_email;
            break;  
        case 'customer_phone':
            $replacement = $customer->bookingpress_user_phone;
            break;   
    }

    return $replacement;

}
add_filter( 'automatorwp_get_trigger_tag_replacement', 'automatorwp_bookingpress_get_trigger_customer_tag_replacement', 10, 6 );