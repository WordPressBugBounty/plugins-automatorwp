<?php
/**
 * Add Achievement Requirements Ability
 *
 * @package     AutomatorWP\AI_Assistant\Ability\Add_Achievement_Requirements
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_AI_Assistant_Ability_Add_Achievement_Requirements extends AutomatorWP_AI_Assistant_Ability {

    public $ability = 'add-achievement-requirements';

    /**
     * Register the ability
     *
     * @since 1.0.0
     */
    public function register() {

        $this->args = array(
            'label'               => esc_html__( 'Add requirements to an achievement in AutomatorWP', 'automatorwp' ),
            'description'         => esc_html__( 'Design and insert new achievement requirements.', 'automatorwp' ),
            'input_schema'        => array(
                'type'       => 'object',
                'properties' => array(
                    'achievement' => array(
                        'description' => esc_html__( 'The achievement ID, title or slug.', 'automatorwp' ),
                        'type'        => 'string',
                    ),
                    'requirements' => array(
                        'description' => esc_html__( 'List of achievement steps, requirements or triggers. Define the criteria to meet to earn that achievement.', 'automatorwp' ),
                        'type'        => 'array',
                        'items'       => array(
                            'type' => 'object',
                            'properties' => automatorwp_ai_assistant_get_requirements_properties( 'step' ),
                        ),
                    ),
                ),
                'required' => array( 'achievement', 'requirements' ),
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
            'achievement' => '',
            'requirements' => array(),
        ) );

        $achievement = sanitize_text_field( $args['achievement'] );
        $requirements = $args['requirements'];

        // Check the achievement type
        $post = $this->get_post( $achievement );
        if( $this->is_response_error( $post ) )
            return $post;

        if( ! automatorwp_get_achievement_type( $post->post_type ) )
            return $this->response_error( sprintf( __( 'The post %s is not an achievement.', 'automatorwp' ), '#' . $post->ID . ' - ' . $post->post_title ) );

        // Requirements
        $updated_steps = automatorwp_ai_assistant_process_requirements(
            $requirements,
            $post->ID,
            'step'
        );

        $steps_count = count( $updated_steps );
        $steps_label = _n( 'step', 'steps', $steps_count, 'automatorwp' );
        $steps_answer = automatorwp_ai_assistant_get_requirements_answer( $updated_steps );

        // Final response
        $post_link = get_edit_post_link( $post );
        $post_display = empty( $post->post_title ) ? esc_html__( '(no title)' ) : $post->post_title;

        if( $post_link !== null ) {
            $post_display = '[' . $post_display . '](' . $post_link . ')';
        }

        // translators: %1$s: Step label. %2$s: Achievement name
        $message = sprintf( __( 'I added the following %1$s to %2$s:', 'automatorwp' ), $steps_label, $post_display );
        $message .= $steps_answer;

        return $this->response_success( $message );

    }

}
new AutomatorWP_AI_Assistant_Ability_Add_Achievement_Requirements();