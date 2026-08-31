<?php
/**
 * Admin
 *
 * @package     AutomatorWP\AI_Assistant\Admin
 * @since       1.0.0
 * @version     1.0.0
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * AI Assistant settings
 *
 * @since 1.0.0
 *
 * @param array $settings
 *
 * @return array
 */
function automatorwp_ai_assistant_settings_fields( $settings ) {

    $settings['disable_ai_assistant'] = array(
        'name' => __( 'Disable AI Assistant', 'automatorwp' ),
        'tooltip'   => __( 'Disable the AutomatorWP AI Assistant.', 'automatorwp' ),
        'label_cb' => 'cmb_tooltip_label_cb',
        'type' => 'checkbox',
        'classes' => 'cmb2-switch',
    );

    return $settings;
}
add_filter( 'automatorwp_general_settings_fields', 'automatorwp_ai_assistant_settings_fields' );