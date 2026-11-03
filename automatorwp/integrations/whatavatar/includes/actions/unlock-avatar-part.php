<?php
/**
 * Unlock Avatar Part
 *
 * @package     WhatAvatar\Integrations\AutomatorWP\Actions\Unlock_Avatar_Part
 * @author      WhatAvatar <contact@whatavatar.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WhatAvatar_Unlock_Avatar_Part_Action extends AutomatorWP_Integration_Action {

    public $integration = 'whatavatar';
    public $action = 'whatavatar_unlock_avatar_part';
    public $user_id = 0;

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Unlock avatar part', 'automatorwp' ),
            'select_option'     => __( 'Unlock <strong>avatar part</strong>', 'automatorwp' ),
            /* translators: %1$s: Avatar Part %2$s: User */
            'edit_label'        => sprintf( __( 'Unlock %1$s to %2$s', 'automatorwp' ), '{avatar_part}', '{user}' ),
            /* translators: %1$s: Avatar Part %2$s: User */
            'log_label'         => sprintf( __( 'Unlock %1$s to %2$s', 'automatorwp' ), '{avatar_part}', '{user}' ),
            'options'           => array(
                'avatar_part' => automatorwp_utilities_ajax_selector_option( array(
                    'field'             => 'avatar_part',
                    'name'              => __( 'Avatar Part:', 'automatorwp' ),
                    'desc'              => __( 'Only avatar parts configured to be unlocked by AutomatorWP will be listed here.', 'automatorwp' ),
                    'option_none_value' => 'any',
                    'option_none_label' => __( 'any avatar part', 'automatorwp' ),
                    'action_cb'         => 'automatorwp_whatavatar_get_parts',
                    'options_cb'        => 'automatorwp_whatavatar_options_cb_part',
                    'default'           => 'any'
                ) ),
                'user' => array(
                    'default' => __ ( 'user', 'automatorwp' ),
                    'fields' => array(
                        'user_id' => array(
                            'name' => __( 'User ID:', 'automatorwp' ),
                            'desc' => __( 'The user\'s ID to give the avatar part. Leave empty to assign the user that completes the automation.', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                    ),
                ),

            ),
        ) );

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
        $part_id = $action_options['avatar_part'];
        $this->user_id = absint( $action_options['user_id'] );

        if( $this->user_id === 0 ) $this->user_id = $user_id;

        // Bail if empty meta key
        if( empty( $part_id ) ) {
            $this->result = __( 'Empty avatar part.', 'automatorwp' );
            return;
        }

        $user = get_userdata( $this->user_id );

        // Bail if user to delete does not exists
        if ( ! $user ) {
            $this->result = sprintf( __( 'User #%d not found.', 'automatorwp' ), $this->user_id );
            return;
        }

        $part_label = whatavatar_get_part_label( $part_id );

        $lock = whatavatar_editor_is_part_locked( $part_id, $this->user_id );

        if( ! $lock ) {
            $this->result = sprintf(  __( 'User already unlocked %s.', 'automatorwp' ), $part_label );
            return;
        }

        // Register unlock
        whatavatar_add_user_unlock( array(
            'type' => 'avatar_part',
            'user_id' => $this->user_id,
            'element_id' => $part_id,
        ) );

        $this->result = sprintf( __( '%s unlocked to user successfully!', 'automatorwp' ), $part_label );

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

        $log_meta['user_id'] = $this->user_id;
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
        if( $log->type !== 'action' ) {
            return $log_fields;
        }

        // Bail if action type don't match this action
        if( $object->type !== $this->action ) {
            return $log_fields;
        }

        $log_fields['user_id_meta'] = array(
            'name' => __( 'User:', 'automatorwp' ),
            'desc' => __( 'The user the action runs for.', 'automatorwp' ),
            'type' => 'text',
        );

        $log_fields['result'] = array(
            'name' => __( 'Result:', 'automatorwp' ),
            'type' => 'text',
        );

        return $log_fields;
    }

}

new AutomatorWP_WhatAvatar_Unlock_Avatar_Part_Action();