<?php
/**
 * Plugin Name:           AutomatorWP - MailerPress
 * Plugin URI:            https://automatorwp.com/add-ons/mailerpress/
 * Description:           Connect AutomatorWP with MailerPress
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-mailerpress
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          7.1
 * Requires PHP:          7.4
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\MailerPress
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

final class AutomatorWP_Integration_MailerPress {
    /**
     * @var         AutomatorWP_Integration_MailerPress $instance The one true AutomatorWP_Integration_MailerPress
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_MailerPress self::$instance
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_MailerPress();
            
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
        define( 'AUTOMATORWP_MAILERPRESS_VER',  '1.0.0' );

        // Plugin file
        define( 'AUTOMATORWP_MAILERPRESS_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_MAILERPRESS_DIR',  plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_MAILERPRESS_URL',  plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_MAILERPRESS_DIR . 'includes/functions.php';
            require_once AUTOMATORWP_MAILERPRESS_DIR . 'includes/ajax-functions.php';
            require_once AUTOMATORWP_MAILERPRESS_DIR . 'includes/tags.php';

            // Triggers
            require_once AUTOMATORWP_MAILERPRESS_DIR . 'includes/triggers/user-contact-added.php';
            require_once AUTOMATORWP_MAILERPRESS_DIR . 'includes/triggers/tag-added.php';
            
            // Actions
            require_once AUTOMATORWP_MAILERPRESS_DIR . 'includes/actions/create-user-contact.php';
            require_once AUTOMATORWP_MAILERPRESS_DIR . 'includes/actions/user-add-tag.php';
            
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
     * Registers this integration with AutomatorWP
     *
     * @since 1.0.0
     */
    public function register_integration() {

        automatorwp_register_integration( 'mailerpress', array(
            'label' => 'MailerPress',
            'icon'  => AUTOMATORWP_MAILERPRESS_URL . 'assets/mailerpress.svg',
        ) );

    }


    /**
     * Check if all plugin requirements are met
     *
     * @since 1.0.0
     *
     * @return bool
     */
    private function meets_requirements() {

        if ( ! class_exists( 'AutomatorWP' ) ) {
            return false;
        }

        if ( ! class_exists( 'MailerPress\Core\Kernel' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_MailerPress' ) ) {
            return false;
        }

        return true;

    }
}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_MailerPress instance
 *
 * @since       1.0.0
 * @return      AutomatorWP_Integration_MailerPress
 */
function AutomatorWP_Integration_MailerPress() {
    return AutomatorWP_Integration_MailerPress::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_MailerPress' );
