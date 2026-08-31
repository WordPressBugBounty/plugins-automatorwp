<?php
/**
 * Create Achievement Ability
 *
 * @package     AutomatorWP\AI_Assistant\Ability\Create_Achievement
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_AI_Assistant_Ability_Create_Achievement extends AutomatorWP_AI_Assistant_Ability {

    public $ability = 'create-achievement';

    /**
     * Register the ability
     *
     * @since 1.0.0
     */
    public function register() {

        $this->args = array(
            'label'               => esc_html__( 'Create an achievement in AutomatorWP', 'automatorwp' ),
            'description'         => esc_html__( 'Design and insert a new achievement of a type into the database.', 'automatorwp' ),
            'input_schema'        => array(
                'type'       => 'object',
                'properties' => array(
                    'title' => array(
                        'description' => esc_html__( 'A clean, professional, and descriptive title for the achievement.', 'automatorwp' ),
                        'type'        => 'string',
                    ),
                    'description' => array(
                        'description' => esc_html__( 'A clean, professional, and descriptive description for the achievement.', 'automatorwp' ),
                        'type'        => 'string',
                    ),
                    'achievement_type' => array(
                        'description' => esc_html__( 'The AutomatorWP achievement type.', 'automatorwp' ),
                        'type' => 'string',
                        //'enum' => automatorwp_get_achievement_types_slugs(),
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
                'required' => array( 'title', 'achievement_type' ),
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
            'title' => '',
            'description' => '',
            'achievement_type' => '',
            'requirements' => array(),
        ) );

        $title = sanitize_text_field( $args['title'] );
        $description = sanitize_text_field( $args['description'] );
        $achievement_type = sanitize_text_field( $args['achievement_type'] );
        $requirements = $args['requirements'];

        // Check the achievement type
        $type = automatorwp_get_achievement_type( $achievement_type );
        if( ! $type )
            return $this->response_error( sprintf( __( 'I couldn\'t find the achievement type "%s".', 'automatorwp' ), $achievement_type ) );

        // Make description block compatible
        $description = '<!-- wp:paragraph -->' . $description . '<!-- /wp:paragraph -->';

        // Insert post
        $post_data = array(
            'post_title' => $title,
            'post_content' => $description,
            'post_type' => $achievement_type,
            'post_status' => 'draft', // TODO: FUTURE: Add optional input schema to manage status, for the moment, draft to prevent published unwanted results
        );

        $post_id = wp_insert_post( $post_data, true );

        if( is_wp_error( $post_id ) ) {
            // translators: %1$s: Achievement type singular %2$s: Error messages
            return $this->response_error( sprintf( __( 'I couldn\'t create the %1$s. Reason: %2$s.', 'automatorwp' ), $type['singular_name'], $post_id->get_error_message() ) );
        }

        // Post metas
        $prefix = '_automatorwp_';

        update_post_meta( $post_id, $prefix . 'earned_by', 'triggers' );
        update_post_meta( $post_id, $prefix . 'maximum_earnings', '1' );
        update_post_meta( $post_id, $prefix . 'global_maximum_earnings', '0' );
        update_post_meta( $post_id, $prefix . 'hidden', 'show' );

        // Requirements
        $updated_steps = automatorwp_ai_assistant_process_requirements(
            $requirements,
            $post_id,
            'step'
        );

        $steps_count = count( $updated_steps );

        $steps_answer = automatorwp_ai_assistant_get_requirements_answer( $updated_steps );

        // Final response
        $post = get_post( $post_id );
        $post_link = get_edit_post_link( $post );
        $post_display = empty( $post->post_title ) ? esc_html__( '(no title)' ) : $post->post_title;

        if( $post_link !== null ) {
            $post_display = '[' . $post_display . '](' . $post_link . ')';
        }

        if ( $steps_count > 0 ) {
            $message = sprintf( __( 'I created the %1$s %2$s that requires:', 'automatorwp' ), $type['singular_name'], $post_display );
            $message .= $steps_answer;
        } else{
            // translators: %1$s: Achievement type singular %2$s: Achievement name
            $message = sprintf( __( 'I created the %1$s %2$s.', 'automatorwp' ), $type['singular_name'], $post_display );
        }

        return $this->response_success( $message );

    }

}
new AutomatorWP_AI_Assistant_Ability_Create_Achievement();