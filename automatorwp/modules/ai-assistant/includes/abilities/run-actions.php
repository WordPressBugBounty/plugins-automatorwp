<?php
/**
 * Run Actions Ability
 *
 * @package     AutomatorWP\AI_Assistant\Ability\Run_Actions
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_AI_Assistant_Ability_Run_Actions extends AutomatorWP_AI_Assistant_Ability {

    public $ability = 'run-actions';

    /**
     * Register the ability
     *
     * @since 1.0.0
     */
    public function register() {

        $this->args = array(
            'label'               => esc_html__( 'Run or execute AutomatorWP actions', 'automatorwp' ),
            'description'         => esc_html__( 'Runs actions directly, like a function call.', 'automatorwp' ),
            'input_schema'        => array(
                'type'       => 'object',
                'properties' => array(
                    'actions' => array(
                        'description' => esc_html__( 'List of action that should be executed.', 'automatorwp' ),
                        'type'        => 'array',
                        'items'       => array(
                            'type' => 'object',
                            'properties' => automatorwp_ai_assistant_get_items_properties( 'action' ),
                        ),
                    ),
                ),
                'required' => array( 'actions' ),
                'additionalProperties' => false,
            ),
        );

    }

    /**
     * Ability execute callback
     *
     * @since 1.0.0
     *
     * @param array $args
     *
     * @return array array( 'success' => true|false, 'message' => '' )
     */
    public function execute( $args ) {

        $args = wp_parse_args( (array) $args, array(
            'actions'      => array(),
        ) );

        $actions = $args['actions'];
        $run = array();

        if( is_array( $actions ) && count( $actions ) ) {

            foreach( $actions as $k => $i ) {

                $i = wp_parse_args( $i, array(
                    'type' => '',
                    'options' => array(),
                ) );

                $i['type'] = sanitize_key( trim( $i['type'] ) );

                $type_args = automatorwp_get_action( $i['type'] );

                // Bail if not is a valid type
                if( ! $type_args ) continue;

                $action = ( object ) array( 'type' => $i['type'] );
                $user_id = 0;
                $event = array(
                  'trigger' => 'automatorwp_ai_assistant_run_action',
                  'anonymous' => true,
                  'user_id' => $user_id,
                );
                $options = automatorwp_ai_assistant_build_options( $i['options'], $type_args );
                $automation = ( object ) array( 'id' => 0 );

                //do_action( 'automatorwp_execute_action', $action, $user_id, $event, $options, $automation );
                do_action( 'automatorwp_execute_action_' . $i['type'], $action, $user_id, $event, $options, $automation );

                $log_meta = apply_filters( 'automatorwp_user_completed_action_log_meta', array(), $action, $user_id, $options, $automation );
                $post_id = apply_filters( 'automatorwp_user_completed_action_post_id', 0, $action, $user_id, $event, $options, $automation );

                $log_meta['run_action_type'] = $i['type'];
                $log_meta['run_action_options'] = automatorwp_ai_assistant_sanitize_options( $i['options'] );
                $log_meta['run_action_parsed_options'] = $options;
                $log_meta['ai_assistant_prompt'] = isset( $_POST['prompt'] ) ? sanitize_text_field( $_POST['prompt'] ) : '';

                // Insert a new log entry to register the action run completion
                $log_id = automatorwp_insert_log( array(
                    'title'     => $type_args['label'],
                    'type'      => 'run_action',
                    'object_id' => 0,
                    'user_id'   => get_current_user_id(),
                    'post_id'   => $post_id,
                    'date'      => date( 'Y-m-d H:i:s', current_time( 'timestamp' ) + count( $run ) ),
                ), $log_meta );

                $run[] = array(
                    'action' => $type_args['label'],
                    'options' => $options,
                    'log_id' => $log_id,
                    'log_meta' => $log_meta,
                );
            }
        }

        // Final response
        $count = count( $run );
        $label = _n( 'action', 'actions', $count, 'automatorwp' );

        // translators: %1$s: Actions label.
        $message = sprintf( __( 'I executed the following %1$s:', 'automatorwp' ), $label );
        $message .= "\n";

        $can_manage = current_user_can( automatorwp_get_manager_capability() );

        foreach( $run as $r ) {
            $message .= '- ' . esc_html( $r['action'] );

            if( $can_manage && $r['log_id'] )
                $message .= ' ([' . esc_html__( 'View details' ) . '](' . ct_get_edit_link( 'automatorwp_logs', $r['log_id'] ) . '))';

            $message .= "\n";
        }

        return $this->response_success( $message );

    }

}
new AutomatorWP_AI_Assistant_Ability_Run_Actions();