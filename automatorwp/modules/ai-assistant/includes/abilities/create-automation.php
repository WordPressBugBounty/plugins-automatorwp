<?php
/**
 * Create Automation Ability
 *
 * @package     AutomatorWP\AI_Assistant\Ability\Create_Automation
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_AI_Assistant_Ability_Create_Automation extends AutomatorWP_AI_Assistant_Ability {

    public $ability = 'create-automation';

    /**
     * Register the ability
     *
     * @since 1.0.0
     */
    public function register() {

        $this->args = array(
            'label'               => esc_html__( 'Create an automation in AutomatorWP', 'automatorwp' ),
            'description'         => esc_html__( 'Design and insert a new automation into the database.', 'automatorwp' ),
            'input_schema'        => array(
                'type'       => 'object',
                'properties' => array(
                    'title' => array(
                        'description' => esc_html__( 'A clean, professional, and descriptive title for the automation.', 'automatorwp' ),
                        'type'        => 'string',
                    ),
                    'type' => array(
                        'description' => __( 'The automation type. By default, "user" (for logged in users).', 'automatorwp' ),
                        'type'        => 'string',
                        'enum'        => array_keys( automatorwp_get_automation_types() ),
                    ),
                    'expiration' => array(
                        'description' => __( 'The automation expiration datetime (YYYY-MM-DD HH:MM:SS).', 'automatorwp' ),
                        'type'        => 'string',
                        'format'      => 'date-time',
                    ),
                    'triggers' => array(
                        'description' => esc_html__( 'List of triggers that launches the automation.', 'automatorwp' ),
                        'type'        => 'array',
                        'items'       => array(
                            'type' => 'object',
                            'properties' => automatorwp_ai_assistant_get_items_properties( 'trigger' ),
                        ),
                    ),
                    'actions' => array(
                        'description' => esc_html__( 'List of action that should be executed when the triggers are meet.', 'automatorwp' ),
                        'type'        => 'array',
                        'items'       => array(
                            'type' => 'object',
                            'properties' => automatorwp_ai_assistant_get_items_properties( 'action' ),
                        ),
                    ),
                ),
                'required' => array( 'title', 'type' ),
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
            'title'         => '',
            'type'          => 'user',
            'expiration'    => '',
            'triggers'      => array(),
            'actions'       => array(),
        ) );

        $title = sanitize_text_field( $args['title'] );
        $type = sanitize_key( strtolower( $args['type'] ) );
        $expiration = sanitize_text_field( $args['expiration'] );
        $triggers = $args['triggers'];
        $actions = $args['actions'];

        // Sanitize automation type
        $type = automatorwp_validate_from_array( $type, array_keys( automatorwp_get_automation_types() ), 'user' );

        // Check the expiration date
        $expiration_date = '';

        if( $expiration !== '' ) {
            $expiration_time = strtotime( $expiration );

            if( $expiration_time )
                $expiration_date = date( 'Y-m-d H:i:s', $expiration_time );
        }

        $automation = array(
            'title'             => ! empty( $title ) ? $title : __( '(no title)', 'automatorwp' ),
            'type'              => $type,
            'user_id'           => get_current_user_id(),
            'sequential'        => 0,
            'times_per_user'    => 1,
            'times'             => 0,
            'status'            => 'inactive',
            'date'              => date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ),
            'expiration'        => $expiration_date,
        );

        ct_setup_table( "automatorwp_automations" );
        $automation_id = ct_insert_object( $automation );
        ct_reset_setup_table();

        if( is_wp_error( $automation_id ) ) {
            // translators: %1$s: Points type singular %2$s: Error messages
            return $this->response_error( sprintf( __( 'I couldn\'t create the %1$s. Reason: %2$s.', 'automatorwp' ), $title, $automation_id->get_error_message() ) );
        }

        // Triggers & Actions
        $updated_triggers = automatorwp_ai_assistant_process_items(
            $triggers,
            $automation_id,
            'trigger'
        );

        $updated_actions = automatorwp_ai_assistant_process_items(
            $actions,
            $automation_id,
            'action'
        );

        // Final Response
        $triggers_count = count( $updated_triggers );
        $actions_count = count( $updated_actions );

        $triggers_answer = automatorwp_ai_assistant_get_items_answer( $updated_triggers, 'trigger' );
        $actions_answer = automatorwp_ai_assistant_get_items_answer( $updated_actions, 'action' );

        $automation_display = $automation['title'];

        if ( current_user_can( automatorwp_get_manager_capability() ) )
            $automation_display = '[' . $automation_display . '](' . ct_get_edit_link( 'automatorwp_automations', $automation_id ) . ')';

        if( $triggers_count > 0 && $actions_count > 0 ) {
            $message = sprintf( __( 'I created the %s automation with:', 'automatorwp' ), $automation_display )
                . "\n"
                . $triggers_answer
                . $actions_answer;
        } else if( $triggers_count > 0 && $actions_count === 0 ) {
            $message = sprintf( __( 'I created the %s automation with:', 'automatorwp' ), $automation_display )
                . "\n"
                . $triggers_answer;
        } else if( $triggers_count === 0 && $actions_count > 0 ) {
            $message = sprintf( __( 'I created the %s automation with:', 'automatorwp' ), $automation_display )
                . "\n"
                . $actions_answer;
        } else {
            $message = sprintf( __( 'I created the %s automation.', 'automatorwp' ), $automation_display );
        }

        return $this->response_success( $message );

    }

}
new AutomatorWP_AI_Assistant_Ability_Create_Automation();