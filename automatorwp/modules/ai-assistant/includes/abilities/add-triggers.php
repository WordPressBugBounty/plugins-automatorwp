<?php
/**
 * Add Triggers Ability
 *
 * @package     AutomatorWP\AI_Assistant\Ability\Add_Triggers
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_AI_Assistant_Ability_Add_Triggers extends AutomatorWP_AI_Assistant_Ability {

    public $ability = 'add-triggers';

    /**
     * Register the ability
     *
     * @since 1.0.0
     */
    public function register() {

        $this->args = array(
            'label'               => esc_html__( 'Add new triggers to an AutomatorWP automation', 'automatorwp' ),
            'description'         => esc_html__( 'Design and insert a new triggers into the database.', 'automatorwp' ),
            'input_schema'        => array(
                'type'       => 'object',
                'properties' => array(
                    'automation' => array(
                        'description' => __( 'The automation ID or title.', 'automatorwp' ),
                        'type' => 'string',
                    ),
                    'triggers' => array(
                        'description' => esc_html__( 'List of triggers that launches the automation.', 'automatorwp' ),
                        'type'        => 'array',
                        'items'       => array(
                            'type' => 'object',
                            'properties' => automatorwp_ai_assistant_get_items_properties( 'trigger' ),
                        ),
                    ),
                ),
                'required' => array( 'automation', 'triggers' ),
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
            'automation'    => '',
            'triggers'      => array(),
        ) );

        $automation_search = sanitize_text_field( $args['automation'] );
        $triggers = $args['triggers'];

        // Check the automation
        $automation = automatorwp_ai_assistant_get_automation( $automation_search );

        if( $this->is_response_error( $automation ) )
            return $automation;

        // Triggers
        $updated_triggers = automatorwp_ai_assistant_process_items(
            $triggers,
            $automation->id,
            'trigger'
        );

        $count = count( $updated_triggers );
        $label = _n( 'trigger', 'triggers', $count, 'automatorwp' );
        $answer = automatorwp_ai_assistant_get_items_answer( $updated_triggers, 'trigger', false );

        // Final response
        $automation_display = $automation->title;

        if ( current_user_can( automatorwp_get_manager_capability() ) )
            $automation_display = '[' . $automation_display . '](' . ct_get_edit_link( 'automatorwp_automations', $automation->id ) . ')';

        // translators: %1$s: Award label. %2$s: Point type name
        $message = sprintf( __( 'I added the following %1$s to %2$s:', 'automatorwp' ), $label, $automation_display );
        $message .= "\n";
        $message .= $answer;

        return $this->response_success( $message );

    }

}
new AutomatorWP_AI_Assistant_Ability_Add_Triggers();