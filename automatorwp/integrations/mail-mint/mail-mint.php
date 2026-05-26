<?php
/**
 * Plugin Name:           AutomatorWP - Mail Mint
 * Plugin URI:            https://automatorwp.com/add-ons/mail-mint/
 * Description:           Connect AutomatorWP with Mail Mint
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-mail-mint
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          7.0
 * Requires PHP:          7.4
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\Mail_Mint
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

final class AutomatorWP_Integration_Mail_Mint {
    /**
     * @var         AutomatorWP_Integration_Mail_Mint $instance The one true AutomatorWP_Integration_Mail_Mint
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_Mail_Mint self::$instance
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_Mail_Mint();
            
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
        define( 'AUTOMATORWP_MAIL_MINT_VER',  '1.0.0' );

        // Plugin file
        define( 'AUTOMATORWP_MAIL_MINT_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_MAIL_MINT_DIR',  plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_MAIL_MINT_URL',  plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_MAIL_MINT_DIR . 'includes/functions.php';
            require_once AUTOMATORWP_MAIL_MINT_DIR . 'includes/ajax-functions.php';
            require_once AUTOMATORWP_MAIL_MINT_DIR . 'includes/tags.php';

            // Triggers
            require_once AUTOMATORWP_MAIL_MINT_DIR . 'includes/triggers/user-contact-added.php';
            require_once AUTOMATORWP_MAIL_MINT_DIR . 'includes/triggers/tag-added.php';

            // Actions
            require_once AUTOMATORWP_MAIL_MINT_DIR . 'includes/actions/create-user-contact.php';
            require_once AUTOMATORWP_MAIL_MINT_DIR . 'includes/actions/user-add-tag.php';

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

        automatorwp_register_integration( 'mail_mint', array(
            'label' => 'Mail Mint',
            'icon'  => AUTOMATORWP_MAIL_MINT_URL . 'assets/mail-mint.svg',
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

        if ( ! defined( 'MRM_VERSION' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_Mail_Mint' ) ) {
            return false;
        }

        return true;

    }
}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_Mail_Mint instance
 *
 * @since       1.0.0
 * @return      AutomatorWP_Integration_Mail_Mint
 */
function AutomatorWP_Integration_Mail_Mint() {
    return AutomatorWP_Integration_Mail_Mint::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_Mail_Mint' );
