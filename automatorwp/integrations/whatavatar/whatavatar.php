<?php
/**
 * Plugin Name:           AutomatorWP - WhatAvatar
 * Plugin URI:            https://automatorwp.com/add-ons/whatavatar/
 * Description:           Connect AutomatorWP with WhatAvatar.
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-whatavatar
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          7.1
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\WhatAvatar
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_WhatAvatar {

    /**
     * @var         AutomatorWP_Integration_WhatAvatar $instance The one true instance
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_WhatAvatar self::$instance The one true instance
     */
    public static function instance() {
        if( ! self::$instance ) {

            self::$instance = new AutomatorWP_Integration_WhatAvatar();

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
        define( 'AUTOMATORWP_WHATAVATAR_VER', '1.0.0' );

        // Plugin file
        define( 'AUTOMATORWP_WHATAVATAR_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_WHATAVATAR_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_WHATAVATAR_URL', plugin_dir_url( __FILE__ ) );
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

            require_once AUTOMATORWP_WHATAVATAR_DIR . 'includes/ajax-functions.php';
            require_once AUTOMATORWP_WHATAVATAR_DIR . 'includes/actions/unlock-avatar-part.php';

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

        automatorwp_register_integration( 'whatavatar', array(
            'label' => 'WhatAvatar',
            'icon'  => AUTOMATORWP_WHATAVATAR_URL . 'assets/whatavatar.svg',
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

        if ( ! class_exists( 'WhatAvatar' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_WhatAvatar' ) ) {
            return false;
        }

        return true;

    }    

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_WhatAvatar instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_WhatAvatar The one true AutomatorWP_Integration_WhatAvatar
 */
function AutomatorWP_Integration_WhatAvatar() {
    return AutomatorWP_Integration_WhatAvatar::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_WhatAvatar' );
