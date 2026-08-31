<?php
/**
 * Abilities
 *
 * @package     AutomatorWP\AI_Assistant\Abilities
 * @since       1.0.0
 * @version     1.0.0
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get the system instructions
 *
 * @since 1.0.0
 */
function automatorwp_ai_assistant_get_system_instructions() {

    $system_instructions = array(
        "You are an AI assistant for AutomatorWP.",
        "Be helpful, precise, and professional.",
        "You have strict catalog restrictions due to ability parameters.",
        "Do not guess slugs for triggers or actions.",
        "Always use the listed abilities to perform tasks instead of guessing or pretending.",
        "Do not generate a text response after using abilities. They already return one.",
    );

    /**
     * Filter to override the system instructions
     *
     * @since 1.0.0
     *
     * @param string $system_instructions
     *
     * @return string
     */
    return apply_filters( 'automatorwp_ai_assistant_get_system_instructions', implode( "\n", $system_instructions ) );
}

/**
 * Register abilities category
 *
 * @since 1.0.0
 */
function automatorwp_ai_assistant_register_category() {
    if ( ! function_exists( 'wp_register_ability_category' ) ) return;

    wp_register_ability_category( 'automatorwp', array(
        'label'       => __( 'AutomatorWP', 'automatorwp' ),
        'description' => __( 'Ability to manage, create and design AutomatorWP elements such as points, achievements and ranks using AI.', 'automatorwp' ),
    ) );

}
add_action( 'wp_abilities_api_categories_init', 'automatorwp_ai_assistant_register_category' );

/**
 * Get abilities
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_ai_assistant_get_abilities() {

    $registered = wp_get_abilities();
    $categories = array();

    foreach( $registered as $ability => $object ) {
        if( strpos( $ability, 'automatorwp' ) !== false ) {
            $categories[$ability] = $object;
        }
    }

    return $categories;
}

/**
 * Get abilities slugs
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_ai_assistant_get_abilities_slugs() {
    return array_keys( automatorwp_ai_assistant_get_abilities() );
}
