<?php
/**
 * Add Actions Ability
 *
 * @package     AutomatorWP\AI_Assistant\Ability\Add_Actions
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_AI_Assistant_Ability_Add_Actions extends AutomatorWP_AI_Assistant_Ability {

    public $ability = 'add-actions';

    /**
     * Register the ability
     *
     * @since 1.0.0
     */
    public function register() {

        $this->args = array(
            'label'               => esc_html__( 'Add new actions to an AutomatorWP automation', 'automatorwp' ),
            'description'         => esc_html__( 'Design and insert a new actions into the database.', 'automatorwp' ),
            'input_schema'        => array(
                'type'       => 'object',
                'properties' => array(
                    'automation' => array(
                        'description' => __( 'The automation ID or title.', 'automatorwp' ),
                        'type' => 'string',
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
                'required' => array( 'automation', 'actions' ),
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
            'actions'      => array(),
        ) );

        $automation_search = sanitize_text_field( $args['automation'] );
        $actions = $args['actions'];

        // Check the automation
        $automation = automatorwp_ai_assistant_get_automation( $automation_search );

        if( $this->is_response_error( $automation ) )
            return $automation;

        // Actions
        $updated_actions = automatorwp_ai_assistant_process_items(
            $actions,
            $automation->id,
            'action'
        );

        $count = count( $updated_actions );
        $label = _n( 'action', 'actions', $count, 'automatorwp' );
        $answer = automatorwp_ai_assistant_get_items_answer( $updated_actions, 'action', false );

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
new AutomatorWP_AI_Assistant_Ability_Add_Actions();