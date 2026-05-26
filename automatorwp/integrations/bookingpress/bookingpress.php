<?php
/**
 * Plugin Name:           AutomatorWP - BookingPress
 * Plugin URI:            https://automatorwp.com/add-ons/bookingpress/
 * Description:           Connect AutomatorWP with BookingPress.
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-bookingpress
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          7.0
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\BookingPress
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_BookingPress {

    /**
     * @var         AutomatorWP_Integration_BookingPress $instance The one true AutomatorWP_Integration_BookingPress
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_BookingPress self::$instance The one true AutomatorWP_Integration_BookingPress
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_BookingPress();
            
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

        define( 'AUTOMATORWP_BOOKINGPRESS_VER', '1.0.0' );
        define( 'AUTOMATORWP_BOOKINGPRESS_FILE', __FILE__ );
        define( 'AUTOMATORWP_BOOKINGPRESS_DIR', plugin_dir_path( __FILE__ ) );
        define( 'AUTOMATORWP_BOOKINGPRESS_URL', plugin_dir_url( __FILE__ ) );

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

            require_once AUTOMATORWP_BOOKINGPRESS_DIR . 'includes/functions.php';
            require_once AUTOMATORWP_BOOKINGPRESS_DIR . 'includes/tags.php';
            
            // Triggers
            require_once AUTOMATORWP_BOOKINGPRESS_DIR . 'includes/triggers/new-customer.php';
            require_once AUTOMATORWP_BOOKINGPRESS_DIR . 'includes/triggers/anonymous-new-customer.php';

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
    function register_integration() {

        automatorwp_register_integration( 'bookingpress', array(
            'label' => 'BookingPress',
            'icon'  => AUTOMATORWP_BOOKINGPRESS_URL . 'assets/bookingpress.svg',
        ) );

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

        if ( ! class_exists( 'BookingPress' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_BookingPress' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_BookingPress instance
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_BookingPress The one true AutomatorWP_Integration_BookingPress
 */
function AutomatorWP_Integration_BookingPress() {
    return AutomatorWP_Integration_BookingPress::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_BookingPress' );
