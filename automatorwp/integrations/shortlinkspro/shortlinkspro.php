<?php
/**
 * Plugin Name:           AutomatorWP - ShortLinks Pro integration
 * Plugin URI:            https://automatorwp.com/add-ons/shortlinkspro/
 * Description:           Connect AutomatorWP with ShortLinks Pro.
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-shortlinkspro-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          6.8
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\ShortLinksPro
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_ShortLinksPro {

    /**
     * @var         AutomatorWP_Integration_ShortLinksPro $instance The one true AutomatorWP_Integration_ShortLinksPro
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_ShortLinksPro self::$instance The one true AutomatorWP_Integration_ShortLinksPro
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_ShortLinksPro();

            if( ! self::$instance->pro_installed() ) {

                self::$instance->constants();
                self::$instance->includes();

            }

            self::$instance->hooks();
        }

        return self::$instance;
    }

    /**
     * Setup plugin constants
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function constants() {
        // Plugin version
        define( 'AUTOMATORWP_SHORTLINKSPRO_VER', '1.0.0' );

        // Plugin file
        define( 'AUTOMATORWP_SHORTLINKSPRO_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_SHORTLINKSPRO_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_SHORTLINKSPRO_URL', plugin_dir_url( __FILE__ ) );
    }

    /**
     * Include plugin files
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function includes() {

        if( $this->meets_requirements() ) {

            // Includes
            require_once AUTOMATORWP_SHORTLINKSPRO_DIR . 'includes/ajax-functions.php';
            require_once AUTOMATORWP_SHORTLINKSPRO_DIR . 'includes/functions.php';
            require_once AUTOMATORWP_SHORTLINKSPRO_DIR . 'includes/tags.php';

            // Triggers
            require_once AUTOMATORWP_SHORTLINKSPRO_DIR . 'includes/triggers/click-link.php';
            require_once AUTOMATORWP_SHORTLINKSPRO_DIR . 'includes/triggers/click-link-from-category.php';
            require_once AUTOMATORWP_SHORTLINKSPRO_DIR . 'includes/triggers/click-link-from-tag.php';

            // Actions
            require_once AUTOMATORWP_SHORTLINKSPRO_DIR . 'includes/actions/create-link.php';
            
        }
    }

    /**
     * Setup plugin hooks
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function hooks() {

        add_action( 'automatorwp_init', array( $this, 'register_integration' ) );

        // Setup our activation and deactivation hooks
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
    }

    /**
     * Registers this integration
     *
     * @since 1.0.0
     */
    function register_integration() {

        automatorwp_register_integration( 'shortlinkspro', array(
            'label' => 'ShortLinksPro',
            'icon'  => AUTOMATORWP_SHORTLINKSPRO_URL . 'assets/shortlinkspro.svg',
        ) );

    }

    /**
     * Activation hook for the plugin.
     *
     * @since  1.0.0
     */
    function activate() {

        if( $this->meets_requirements() ) {

        }

    }

    /**
     * Deactivation hook for the plugin.
     *
     * @since  1.0.0
     */
    function deactivate() {

    }   

    /**
     * Check if there are all plugin requirements
     *
     * @since  1.0.0
     *
     * @return bool True if installation meets all requirements
     */
    private function meets_requirements() {

        if ( ! class_exists( 'AutomatorWP' ) ) {
            return false;
        }

        if ( ! class_exists( 'ShortLinksPro' ) ) {
            return false;
        }

        return true;

    }

    /**
     * Check if the pro version of this integration is installed
     *
     * @since  1.0.0
     *
     * @return bool True if pro version installed
     */
    private function pro_installed() {

        if ( ! class_exists( 'AutomatorWP_ShortLinksPro' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_ShortLinksPro instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_ShortLinksPro The one true AutomatorWP_Integration_ShortLinksPro
 */
function AutomatorWP_Integration_ShortLinksPro() {
    return AutomatorWP_Integration_ShortLinksPro::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_ShortLinksPro' );
