<?php
/**
 * Plugin Name:           AutomatorWP - Paid Membership Subscriptions
 * Plugin URI:            https://automatorwp.com/add-ons/paid-membership-subscriptions/
 * Description:           Connect AutomatorWP with Paid Membership Subscriptions.
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-paid-membership-subscriptions
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          6.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\Paid_Membership_Subscriptions
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_Paid_Membership_Subscriptions {

    /**
     * @var         AutomatorWP_Integration_Paid_Membership_Subscriptions $instance The one true AutomatorWP_Integration_Paid_Membership_Subscriptions
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_Paid_Membership_Subscriptions self::$instance The one true AutomatorWP_Integration_Paid_Membership_Subscriptions
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_Paid_Membership_Subscriptions();
            
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
        define( 'AUTOMATORWP_PMS_VER', '1.0.0' );

        // Plugin file
        define( 'AUTOMATORWP_PMS_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_PMS_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_PMS_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_PMS_DIR . 'includes/tags.php';
            require_once AUTOMATORWP_PMS_DIR . 'includes/functions.php';

            // Triggers
            require_once AUTOMATORWP_PMS_DIR . 'includes/triggers/purchase-subscription.php';
            
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

        automatorwp_register_integration( 'paid_membership_subscriptions', array(
                'label' => 'Paid Memberships Subscriptions',
                'icon' => AUTOMATORWP_PMS_URL . 'assets/paid-membership-subscriptions.svg',
            )
        );

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

        if ( ! class_exists( 'Paid_Member_Subscriptions' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_Paid_Membership_Subscriptions' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_Paid_Membership_Subscriptions instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_Paid_Membership_Subscriptions The one true AutomatorWP_Integration_Paid_Membership_Subscriptions
 */
function AutomatorWP_Integration_Paid_Membership_Subscriptions() {
    return AutomatorWP_Integration_Paid_Membership_Subscriptions::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_Paid_Membership_Subscriptions' );
