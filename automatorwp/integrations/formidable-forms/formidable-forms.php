<?php
/**
 * Plugin Name:           AutomatorWP - Formidable Forms integration
 * Plugin URI:            https://automatorwp.com/add-ons/formidable-forms/
 * Description:           Connect AutomatorWP with Formidable Forms.
 * Version:               1.0.6
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-formidable-forms-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\Formidable_Forms
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_Formidable_Forms {

    /**
     * @var         AutomatorWP_Integration_Formidable_Forms $instance The one true AutomatorWP_Integration_Formidable_Forms
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_Formidable_Forms self::$instance The one true AutomatorWP_Integration_Formidable_Forms
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_Formidable_Forms();
            self::$instance->constants();
            self::$instance->includes();
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
        define( 'AUTOMATORWP_FORMIDABLE_FORMS_VER', '1.0.6' );

        // Plugin file
        define( 'AUTOMATORWP_FORMIDABLE_FORMS_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_FORMIDABLE_FORMS_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_FORMIDABLE_FORMS_URL', plugin_dir_url( __FILE__ ) );
    }

    /**
     * Include plugin files
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function includes() {

        if( $this->meets_requirements() && ! $this->pro_installed() ) {

            // Triggers
            require_once AUTOMATORWP_FORMIDABLE_FORMS_DIR . 'includes/triggers/submit-form.php';
            // Anonymous Triggers
            require_once AUTOMATORWP_FORMIDABLE_FORMS_DIR . 'includes/triggers/anonymous-submit-form.php';

            // Includes
            require_once AUTOMATORWP_FORMIDABLE_FORMS_DIR . 'includes/ajax-functions.php';
            require_once AUTOMATORWP_FORMIDABLE_FORMS_DIR . 'includes/functions.php';

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

        automatorwp_register_integration( 'formidable_forms', array(
            'label' => 'Formidable Forms',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/formidable-forms.svg',
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

        if ( ! function_exists( 'load_formidable_forms' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_Formidable_Forms' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_Formidable_Forms instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_Formidable_Forms The one true AutomatorWP_Integration_Formidable_Forms
 */
function AutomatorWP_Integration_Formidable_Forms() {
    return AutomatorWP_Integration_Formidable_Forms::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_Formidable_Forms' );
