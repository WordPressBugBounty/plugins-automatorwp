<?php
/**
 * Delete Option
 *
 * @package     AutomatorWP\Integrations\WordPress\Actions\Delete_Option
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WordPress_Delete_Option extends AutomatorWP_Integration_Action {

    /**
     * Initialize the trigger
     *
     * @since 1.0.0
     */
    public function __construct( $integration ) {

        $this->integration = $integration;
        $this->action = $integration . '_delete_option';

        parent::__construct();

    }

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Delete option', 'automatorwp' ),
            'select_option'     => __( 'Delete <strong>option</strong>', 'automatorwp' ),
            /* translators: %1$s: Option. */
            'edit_label'        => sprintf( __( 'Delete %1$s', 'automatorwp' ), '{option}' ),
            /* translators: %1$s: Option. */
            'log_label'         => sprintf( __( 'Delete %1$s', 'automatorwp' ), '{option}' ),
            'options'           => array(
                'option' => array(
                    'from' => 'option',
                    /* translators: Refers to meta key */
                    'default' => __( 'option', 'automatorwp' ),
                    'fields' => array(
                        'option' => array(
                            'name' => __( 'Option:', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                    )
                )
            ),
            'tags' => array(

            )
        ) );

    }

    /**
     * Register required hooks
     *
     * @since 1.0.0
     */
    public function hooks() {

        // Log meta data
        add_filter( 'automatorwp_user_completed_action_log_meta', array( $this, 'log_meta' ), 10, 5 );

        // Log fields
        add_filter( 'automatorwp_log_fields', array( $this, 'log_fields' ), 10, 5 );

        parent::hooks();

    }

    /**
     * Action execution function
     *
     * @since 1.0.0
     *
     * @param stdClass  $action             The action object
     * @param int       $user_id            The user ID
     * @param array     $action_options     The action's stored options (with tags already passed)
     * @param stdClass  $automation         The action's automation object
     */
    public function execute( $action, $user_id, $action_options, $automation ) {

        // Shorthand
        $option = sanitize_key( $action_options['option'] );

        // Bail if empty option name
        if( empty( $option ) ) {
            $this->result = __( 'Action not processed. Option name is empty.', 'automatorwp' );
            return;
        }

        // Bail if is a protected option
        if( automatorwp_is_protected_option( $option ) ) {
            $this->result = sprintf( __( 'Action not processed."%s" is a protected option. You can filter "automatorwp_protected_options" to modify this.', 'automatorwp' ), $option );
            return;
        }

        // Delete the user meta value
        delete_option( $option );

        $this->result = sprintf( __( 'Option "%s" has been deleted successfully.', 'automatorwp' ), $option );

    }

    /**
     * Action custom log meta
     *
     * @since 1.0.0
     *
     * @param array     $log_meta           Log meta data
     * @param stdClass  $action             The action object
     * @param int       $user_id            The user ID
     * @param array     $action_options     The action's stored options (with tags already passed)
     * @param stdClass  $automation         The action's automation object
     *
     * @return array
     */
    public function log_meta( $log_meta, $action, $user_id, $action_options, $automation ) {

        // Bail if action type don't match this action
        if( $action->type !== $this->action ) {
            return $log_meta;
        }

        $log_meta['result'] = $this->result;

        return $log_meta;
    }

    /**
     * Action custom log fields
     *
     * @since 1.0.0
     *
     * @param array     $log_fields The log fields
     * @param stdClass  $log        The log object
     * @param stdClass  $object     The trigger/action/automation object attached to the log
     *
     * @return array
     */
    public function log_fields( $log_fields, $log, $object ) {

        // Bail if log is not assigned to an action
        if( $log->type !== 'action' || $object->type !== $this->action )
            return $log_fields;

        $log_fields['result'] = array(
            'name' => __( 'Result:', 'automatorwp' ),
            'type' => 'text',
        );

        return $log_fields;
    }

}

new AutomatorWP_WordPress_Delete_Option( 'wordpress' );