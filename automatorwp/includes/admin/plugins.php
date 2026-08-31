<?php
/**
 * Admin Plugins
 *
 * @package     AutomatorWP\Admin\Plugins
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Plugins row action links
 *
 * @since   1.0.0
 *
 * @param array     $links  Array of plugin action links
 * @param string    $file   Plugin file
 *
 * @return array    $links
 */
function automatorwp_plugin_action_links( $links, $file ) {

    if ( $file != 'automatorwp/automatorwp.php' )
        return $links;

    $settings_link = '<a href="' . admin_url( 'admin.php?page=automatorwp_settings' ) . '">' . esc_html__( 'Settings', 'automatorwp' ) . '</a>';

    array_unshift( $links, $settings_link );

    return $links;

}
add_filter( 'plugin_action_links', 'automatorwp_plugin_action_links', 10, 2 );


/**
 * Plugin row meta links
 *
 * @since 1.0.0
 *
 * @param array     $input  Array of plugin meta links
 * @param string    $file   Plugin file
 *
 * @return array    $input
 */
function automatorwp_plugin_row_meta( $input, $file ) {

    if ( $file != 'automatorwp/automatorwp.php' )
        return $input;

    $links = array(
        '<a href="https://automatorwp.com/add-ons/">' . esc_html__( 'Add-ons', 'automatorwp' ) . '</a>',
        '<a href="https://automatorwp.com/docs/">' . esc_html__( 'Docs', 'automatorwp' ) . '</a>',
        '<a href="https://automatorwp.com/contact-us/">' . esc_html__( 'Support', 'automatorwp' ) . '</a>',
    );

    $input = array_merge( $input, $links );

    return $input;
}
add_filter( 'plugin_row_meta', 'automatorwp_plugin_row_meta', 10, 2 );
