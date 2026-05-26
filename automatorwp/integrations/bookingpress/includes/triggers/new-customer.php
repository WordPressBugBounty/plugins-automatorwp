<?php
/**
 * New Customer
 *
 * @package     AutomatorWP\Integrations\BookingPress\Triggers\New_Customer
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_BookingPress_New_Customer extends AutomatorWP_Integration_Trigger {

    public $integration = 'bookingpress';
    public $trigger = 'bookingpress_new_customer';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'   => $this->integration,
            'label'         => __( 'User is registered as a customer', 'automatorwp' ),
            'select_option' => __( 'User is registered as a <strong>customer</strong>', 'automatorwp' ),
            /* translators: %1$s: Number of times. */
            'edit_label'    => sprintf( __( 'User is registered as a customer %1$s time(s)', 'automatorwp' ), '{times}' ),
            'log_label'     => __( 'User is registered as a customer', 'automatorwp' ),
            'action'        => 'bookingpress_after_create_customer',
            'function'      => array( $this, 'listener' ),
            'priority'      => 10,
            'accepted_args' => 1,
            'options'       => array(
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_bookingpress_get_customer_tags(),
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param int $customer_id BookingPress customer ID
     */
    public function listener( $customer_id ) {

        $user_id = automatorwp_bookingpress_get_wp_user_id_from_customer_id( $customer_id );

        if( empty( $user_id ) ) {
            return;
        }

        automatorwp_trigger_event( array(
            'trigger'     => $this->trigger,
            'user_id'     => $user_id,
            'customer_id' => $customer_id,
        ) );

    }

    /**
     * Register the required hooks
     *
     * @since 1.0.0
     */
    public function hooks() {

        // Log meta data
        add_filter( 'automatorwp_user_completed_trigger_log_meta', array( $this, 'log_meta' ), 10, 6 );

        parent::hooks();
    }

    /**
     * Trigger custom log meta
     *
     * @since 1.0.0
     *
     * @param array     $log_meta           Log meta data
     * @param stdClass  $trigger            The trigger object
     * @param int       $user_id            The user ID
     * @param array     $event              Event information
     * @param array     $trigger_options    The trigger's stored options
     * @param stdClass  $automation         The trigger's automation object
     *
     * @return array
     */
    function log_meta( $log_meta, $trigger, $user_id, $event, $trigger_options, $automation ) {

        // Bail if action type don't match this action
        if( $trigger->type !== $this->trigger ) {
            return $log_meta;
        }

        $log_meta['customer_id'] = ( isset( $event['customer_id'] ) ? $event['customer_id'] : '' );

        return $log_meta;

    }

}

new AutomatorWP_BookingPress_New_Customer();
