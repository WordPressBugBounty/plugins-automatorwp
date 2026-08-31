<?php
/**
 * Scripts
 *
 * @package     AutomatorWP\AI_Assistant\Scripts
 * @since       1.0.0
 * @version     1.0.0
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function automatorwp_ai_assistant_admin_register_scripts() {

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    // Stylesheets
    wp_register_style( 'automatorwp-ai-assistant-css', AUTOMATORWP_AI_ASSISTANT_URL . 'assets/css/automatorwp-ai-assistant' . $suffix . '.css', array( ), AUTOMATORWP_AI_ASSISTANT_VER, 'all' );

    // Scripts
    wp_register_script( 'automatorwp-ai-assistant-showdown-js', AUTOMATORWP_AI_ASSISTANT_URL . 'assets/libs/showdown.min.js', array(), AUTOMATORWP_AI_ASSISTANT_VER, true );
    wp_register_script( 'automatorwp-ai-assistant-js', AUTOMATORWP_AI_ASSISTANT_URL . 'assets/js/automatorwp-ai-assistant' . $suffix . '.js', array( 'jquery', 'automatorwp-ai-assistant-showdown-js' ), AUTOMATORWP_AI_ASSISTANT_VER, true );

}
add_action( 'admin_init', 'automatorwp_ai_assistant_admin_register_scripts' );

/**
 * Enqueue admin scripts
 *
 * @since       1.0.0
 *
 * @param string $hook
 *
 * @return      void
 */
function automatorwp_ai_assistant_admin_enqueue_scripts( $hook ) {

    if( ! automatorwp_ai_assistant_is_valid_page() ) return;

    // Stylesheets
    wp_enqueue_style( 'automatorwp-ai-assistant-css' );

    // Scripts (libs)
    wp_enqueue_script( 'automatorwp-ai-assistant-showdown-js' );

    // Localize scripts
    wp_localize_script( 'automatorwp-ai-assistant-js', 'automatorwp_ai_assistant', array(
        'nonce' => automatorwp_get_admin_nonce(),
        'i18n'                  => array(
            'first_message' => __( 'Hi! What can I help you with today?', 'automatorwp' ),
            'loading'       => __( 'Thinking', 'automatorwp' ),
            'error_message' => __( 'Oops! Something went wrong. Please, try again.', 'automatorwp' ),
        ),
    ) );

    // Scripts
    wp_enqueue_script( 'automatorwp-ai-assistant-js' );

}
add_action( 'admin_enqueue_scripts', 'automatorwp_ai_assistant_admin_enqueue_scripts' );