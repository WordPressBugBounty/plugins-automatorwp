<?php
/**
 * Logs
 *
 * @package     AutomatorWP\AI_Assistant\Logs
 * @since       1.0.0
 * @version     1.0.0
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Log types
 *
 * @since 1.0.0
 *
 * @param array $log_types The log types
 *
 * @return array
 */
function automatorwp_ai_assistant_log_types( $log_types ) {

    $log_types['run_action'] = __( 'Action (Direct Run)', 'automatorwp' );

    return $log_types;
}
add_filter( 'automatorwp_log_types', 'automatorwp_ai_assistant_log_types' );

/**
 * Override log icon
 *
 * @since 6.0.0
 *
 * @param string    $icon       The icon URL
 * @param stdClass  $log        The log object
 *
 * @return string
 */
function automatorwp_ai_assistant_get_log_icon( $icon, $log ) {

    if( $log->type !== 'run_action' ) return $icon;

    ct_setup_table('automatorwp_logs');
    $type = ct_get_object_meta( $log->id, 'run_action_type', true );
    ct_reset_setup_table();

    $type_args = automatorwp_get_action( $type );

    if( $type_args ) {
        $integration = automatorwp_get_integration( $type_args['integration'] );

        if( $integration )  {
            $object = (object) array( 'type' => $type );
            $integration = apply_filters( 'automatorwp_get_log_integration_icon_integration', $integration, $object, $log->type, $log );

            return $integration['icon'];
        }
    }

    return $icon;

}
add_filter( 'automatorwp_get_log_default_icon', 'automatorwp_ai_assistant_get_log_icon', 10, 2 );

/**
 * Override log icon title
 *
 * @since 6.0.0
 *
 * @param string    $title      The icon title
 * @param stdClass  $log        The log object
 *
 * @return string
 */
function automatorwp_ai_assistant_get_log_icon_title( $title, $log ) {

    if( $log->type !== 'run_action' ) return $title;

    ct_setup_table('automatorwp_logs');
    $type = ct_get_object_meta( $log->id, 'run_action_type', true );
    ct_reset_setup_table();

    $type_args = automatorwp_get_action( $type );

    if( $type_args ) {
        $integration = automatorwp_get_integration( $type_args['integration'] );

        if( $integration )  {
            $object = (object) array( 'type' => $type );
            $integration = apply_filters( 'automatorwp_get_log_integration_icon_integration', $integration, $object, $log->type, $log );

            return $integration['label'];
        }
    }

    return $title;

}
add_filter( 'automatorwp_get_log_default_icon_title', 'automatorwp_ai_assistant_get_log_icon_title', 10, 2 );

/**
 * Columns rendering for logs list view
 *
 * @since  1.0.0
 *
 * @param string $column_name
 * @param integer $object_id
 */
function automatorwp_ai_assistant_manage_logs_custom_column( $column_name, $object_id ) {

    // Setup vars
    $log = ct_get_object( $object_id );

    if( $log->type !== 'run_action' ) return;

    switch( $column_name ) {
        case 'user_id':
            ?>

            <br>
            <small><?php echo sprintf( __( 'Run with %s', 'automatorwp' ), '<strong>' . __( 'AI Assistant', 'automatorwp' ) .  '</strong>' ); ?></small>

            <?php
            break;
    }

}
add_action( 'manage_automatorwp_logs_custom_column', 'automatorwp_ai_assistant_manage_logs_custom_column', 15, 2 );
function automatorwp_ai_assistant_log_details_meta_box_after_user( $log ) {

    if( $log->type !== 'run_action' ) return;

    ?>

    <br>
    <small style="margin-left: 26px;"><?php echo sprintf( __( 'Run with %s', 'automatorwp' ), '<strong>' . __( 'AI Assistant', 'automatorwp' ) .  '</strong>' ); ?></small>

    <?php

}
add_action( 'automatorwp_log_details_meta_box_after_user', 'automatorwp_ai_assistant_log_details_meta_box_after_user' );

/**
 * Filter to override the log if needed
 *
 * @since 6.0.0
 *
 * @param stdClass  $log        The log object
 * @param stdClass  $object     The trigger/action/automation object attached to the log
 *
 * @return stdClass
 */
function automatorwp_ai_assistant_log_fields_log( $log, $object ) {

    global $run_action_override;

    $run_action_override = false;

    if( $log->type === 'run_action' ) {
        $run_action_override = true;
        $log->type = 'action';
    }

    return $log;

}
add_filter( 'automatorwp_log_fields_log', 'automatorwp_ai_assistant_log_fields_log', 10, 2 );

/**
 * Filter to override the object if needed
 *
 * @since 6.0.0
 *
 * @param stdClass  $object     The trigger/action/automation object attached to the log
 * @param stdClass  $log        The log object
 *
 * @return stdClass
 */
function automatorwp_ai_assistant_log_fields_object( $object, $log ) {

    global $run_action_override;

    if( $run_action_override !== true ) return $object;

    ct_setup_table('automatorwp_logs');
    $type = ct_get_object_meta( $log->id, 'run_action_type', true );
    ct_reset_setup_table();

    $object = (object) array( 'type' => $type );

    return $object;

}
add_filter( 'automatorwp_log_fields_object', 'automatorwp_ai_assistant_log_fields_object', 10, 2 );

/**
 * Filter to set custom log fields
 *
 * @since 1.0.0
 *
 * @param array     $log_fields The log fields
 * @param stdClass  $log        The log object
 * @param stdClass  $object     The trigger/action/automation object attached to the log
 *
 * @return array
 */
function automatorwp_ai_assistant_log_fields( $log_fields, $log, $object ) {

    global $run_action_override;

    if( $run_action_override !== true ) return $log_fields;

    $log_fields['ai_assistant_title'] = array(
        'name' => __( 'AI Assistant', 'automatorwp' ),
        'desc' => __( 'Information about the data processed internally by the AI Assistant.', 'automatorwp' ),
        'type' => 'title',
    );

    $log_fields['ai_assistant_prompt'] = array(
        'name' => __( 'Prompt', 'automatorwp' ),
        'desc' => __( 'The prompt used to execute this action.', 'automatorwp' ),
        'type' => 'text',
    );

    $log_fields['run_action_options'] = array(
        'name' => __( 'Data', 'automatorwp' ),
        'desc' => __( 'The data passed to the action by the AI Assistant.', 'automatorwp' ),
        'type' => 'text',
    );

    return $log_fields;

}
add_filter( 'automatorwp_log_fields', 'automatorwp_ai_assistant_log_fields', 99, 3 );