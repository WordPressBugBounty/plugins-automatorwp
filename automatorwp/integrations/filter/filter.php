<?php
/**
 * Filter
 *
 * @package     AutomatorWP\Integrations\Filter
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Triggers
require_once plugin_dir_path( __FILE__ ) . 'triggers/trigger-filter.php';
// Actions
require_once plugin_dir_path( __FILE__ ) . 'actions/action-filter.php';

/**
 * Registers this integration
 *
 * @since 1.0.0
 */
function automatorwp_register_filter_integration() {

    automatorwp_register_integration( 'filter', array(
        'label' => 'Filter',
        'icon'  => plugin_dir_url( __FILE__ ) . 'assets/filter.svg',
    ) );

}
add_action( 'automatorwp_init', 'automatorwp_register_filter_integration', 1 );

/**
 * Registers integration labels (due to text domain that requires to be in init)
 *
 * @since 1.0.0
 */
function automatorwp_register_filter_integration_label() {

    AutomatorWP()->integrations['filter']['label'] = __( 'Filter', 'automatorwp' );

}
add_action( 'init', 'automatorwp_register_filter_integration_label' );