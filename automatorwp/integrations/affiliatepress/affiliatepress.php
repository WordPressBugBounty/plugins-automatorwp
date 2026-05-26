<?php
/**
 * Plugin Name:           AutomatorWP - AffiliatePress
 * Plugin URI:            https://automatorwp.com/
 * Description:           Connect AutomatorWP with AffiliatePress.
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-affiliatepress
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          7.0
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\AffiliatePress
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_AffiliatePress {

    /**
     * @var         AutomatorWP_Integration_AffiliatePress $instance The one true AutomatorWP_Integration_AffiliatePress
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_AffiliatePress self::$instance The one true AutomatorWP_Integration_AffiliatePress
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_AffiliatePress();
            
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
        define( 'AUTOMATORWP_AFFILIATEPRESS_VER',  '1.0.0' );

        // Plugin file
        define( 'AUTOMATORWP_AFFILIATEPRESS_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_AFFILIATEPRESS_DIR',  plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_AFFILIATEPRESS_URL',  plugin_dir_url( __FILE__ ) );
    }

    /**
     * Include plugin files
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function includes() {

        if ( $this->meets_requirements() ) {
            
            // Functions
            require_once AUTOMATORWP_AFFILIATEPRESS_DIR . 'includes/functions.php';

            // Triggers
            require_once AUTOMATORWP_AFFILIATEPRESS_DIR . 'includes/triggers/user-registers-affiliate.php';
           
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

    }

    /**
     * Registers this integration
     *
     * @since 1.0.0
     */
    public function register_integration() {

        automatorwp_register_integration( 'affiliatepress', array(
            'label' => 'AffiliatePress',
            'icon'  => AUTOMATORWP_AFFILIATEPRESS_URL . 'assets/affiliatepress.svg',
        ) );
    }

    private function meets_requirements() {

        if ( ! class_exists( 'AutomatorWP' ) ) {
            return false;
        }

        if ( ! class_exists( 'AffiliatePress' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_AffiliatePress' ) ) {
            return false;
        }

        return true;

    }
}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_AffiliatePress instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_AffiliatePress The one true AutomatorWP_Integration_AffiliatePress
 */
function AutomatorWP_Integration_AffiliatePress() {
    return AutomatorWP_Integration_AffiliatePress::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_AffiliatePress' );