<?php
/**
 * Plugin Name:     	AutomatorWP
 * Plugin URI:      	https://automatorwp.com
 * Description:     	Connect your WordPress plugins together and create automated workflows with no code!
 * Version:         	5.3.3
 * Author:          	AutomatorWP
 * Author URI:      	https://automatorwp.com/
 * Text Domain:     	automatorwp
 * Domain Path: 		/languages/
 * Requires PHP:        7.0
 * Requires at least: 	4.4
 * Tested up to: 		6.8
 * License:         	GPLv3 (http://www.gnu.org/licenses/gpl-3.0.html)
 *
 * @package         	AutomatorWP
 * @author          	AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @copyright       	Copyright (c) AutomatorWP
 */

/*
 * Copyright (c) AutomatorWP (contact@automatorwp.com), Ruben Garcia (rubengcdev@gmail.com)
 *
 * This program is free software: you can redistribute it and/or modify it
 * under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Affero General
 * Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

final class AutomatorWP {

    /**
     * @var         AutomatorWP $instance The one true AutomatorWP
     * @since       1.0.0
     */
    private static $instance;

    /**
     * @var         array $settings Stored settings
     * @since       1.0.2
     */
    public $settings = null;

    /**
     * @var         array $integrations Registered integrations
     * @since       1.0.0
     */
    public $integrations = array();

    /**
     * @var         array $triggers Registered triggers
     * @since       1.0.0
     */
    public $triggers = array();

    /**
     * @var         array $actions Registered actions
     * @since       1.0.0
     */
    public $actions = array();

    /**
     * @var         array $filters Registered filters
     * @since       1.0.0
     */
    public $filters = array();

    /**
     * @var         AutomatorWP_Database $db Database object
     * @since       1.0.0
     */
    public $db;

    /**
     * @var         array $cache Cache class
     * @since       1.0.0
     */
    public $cache = array();

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP self::$instance The one true AutomatorWP
     */
    public static function instance() {

        if( ! self::$instance ) {

            self::$instance = new AutomatorWP();
            self::$instance->constants();
            self::$instance->libraries();
            self::$instance->classes();
            self::$instance->compatibility();
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
        define( 'AUTOMATORWP_VER', '5.3.3' );

        // Plugin file
        define( 'AUTOMATORWP_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_URL', plugin_dir_url( __FILE__ ) );

    }

    /**
     * Include plugin libraries
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function libraries() {

        // Custom Tables
        require_once AUTOMATORWP_DIR . 'libraries/ct/init.php';
        require_once AUTOMATORWP_DIR . 'libraries/ct-ajax-list-table/ct-ajax-list-table.php';

        // CMB2
        require_once AUTOMATORWP_DIR . 'libraries/cmb2/init.php';
        require_once AUTOMATORWP_DIR . 'libraries/cmb2-metatabs-options/cmb2_metatabs_options.php';
        require_once AUTOMATORWP_DIR . 'libraries/cmb2-tabs/cmb2-tabs.php';
        require_once AUTOMATORWP_DIR . 'libraries/cmb2-field-edd-license/cmb2-field-edd-license.php';
        require_once AUTOMATORWP_DIR . 'libraries/cmb2-field-switch/cmb2-field-switch.php';
        require_once AUTOMATORWP_DIR . 'libraries/cmb2-field-js-controls/cmb2-field-js-controls.php';

        // Custom CMB2 fields
        require_once AUTOMATORWP_DIR . 'libraries/automatorwp-select.php';
        require_once AUTOMATORWP_DIR . 'libraries/automatorwp-select-filter.php';

    }

    /**
     * Include plugin classes
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function classes() {

        require_once AUTOMATORWP_DIR . 'classes/database.php';
        require_once AUTOMATORWP_DIR . 'classes/automation-type.php';
        require_once AUTOMATORWP_DIR . 'classes/automation-loop.php';
        require_once AUTOMATORWP_DIR . 'classes/integration-trigger.php';
        require_once AUTOMATORWP_DIR . 'classes/integration-action.php';
        require_once AUTOMATORWP_DIR . 'classes/integration-filter.php';

    }

    /**
	 * Include compatibility files
	 *
	 * @access      private
	 * @since       4.2.0
	 * @return      void
	 */
	private function compatibility() {

		// GamiPress backward compatibility
		require_once AUTOMATORWP_DIR . 'includes/compatibility/4.2.0.php';

	}

    /**
     * Include plugin files
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function includes() {

        // The rest of files
        require_once AUTOMATORWP_DIR . 'includes/admin.php';
        require_once AUTOMATORWP_DIR . 'includes/custom-tables.php';
        require_once AUTOMATORWP_DIR . 'includes/ajax-functions.php';
        require_once AUTOMATORWP_DIR . 'includes/filters.php';
        require_once AUTOMATORWP_DIR . 'includes/functions.php';
        require_once AUTOMATORWP_DIR . 'includes/cache.php';
        require_once AUTOMATORWP_DIR . 'includes/cmb2.php';
        require_once AUTOMATORWP_DIR . 'includes/cron.php';
        require_once AUTOMATORWP_DIR . 'includes/scripts.php';
        require_once AUTOMATORWP_DIR . 'includes/automation-ui.php';
        require_once AUTOMATORWP_DIR . 'includes/automations.php';
        require_once AUTOMATORWP_DIR . 'includes/integrations.php';
        require_once AUTOMATORWP_DIR . 'includes/triggers.php';
        require_once AUTOMATORWP_DIR . 'includes/actions.php';
        require_once AUTOMATORWP_DIR . 'includes/tags.php';
        require_once AUTOMATORWP_DIR . 'includes/tags-replacements.php';
        require_once AUTOMATORWP_DIR . 'includes/events.php';
        require_once AUTOMATORWP_DIR . 'includes/logs.php';
        require_once AUTOMATORWP_DIR . 'includes/users.php';
        require_once AUTOMATORWP_DIR . 'includes/utilities.php';

    }

    /**
     * Include integrations files
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function integrations() {

        $integrations_dir = AUTOMATORWP_DIR . 'integrations';

        require_once AUTOMATORWP_DIR . 'integrations/filter/filter.php';
        require_once AUTOMATORWP_DIR . 'integrations/automatorwp/automatorwp.php';
        require_once AUTOMATORWP_DIR . 'integrations/wordpress/wordpress.php';

        // Setup active plugins
        $active_plugins = array();

        if( function_exists( 'get_option' ) ) {
            $active_plugins = (array) get_option( 'active_plugins', array() );
        }

        // Setup active sitewide plugins
        $active_sitewide_plugins = array();

        if ( is_multisite() && function_exists( 'get_site_option' ) ) {
            $active_sitewide_plugins = get_site_option( 'active_sitewide_plugins' );

            if( ! is_array( $active_sitewide_plugins ) ) {
                $active_sitewide_plugins = array();
            }
        }

        // Skip if integration is already active
        if( $this->is_integration_active( 'pro', $active_plugins, $active_sitewide_plugins ) ) {
            return;
        }

        $integrations = @opendir( $integrations_dir );

        while ( ( $integration = @readdir( $integrations ) ) !== false ) {

            if ( $integration === '.' || $integration === '..' || $integration === 'index.php' ) {
                continue;
            }

            if ( $integration === 'filter' || $integration === 'automatorwp' || $integration === 'wordpress' ) {
                continue;
            }

            /**
             * Filter to allow third party plugins skip any integration
             *
             * @since 1.0.0
             *
             * @param bool      $skip
             * @param string    $integration The integration slug as named in automatorwp/includes/integrations
             * @param array     $active_plugins
             * @param array     $active_sitewide_plugins
             *
             * @return bool
             */
            if( apply_filters( 'automatorwp_skip_integration', false, $integration, $active_plugins, $active_sitewide_plugins ) ) {
                continue;
            }

            // Skip if integration is already active
            if( $this->is_integration_active( $integration, $active_plugins, $active_sitewide_plugins ) ) {
                continue;
            }

            $integration_file = $integrations_dir . DIRECTORY_SEPARATOR . $integration . DIRECTORY_SEPARATOR . $integration . '.php';

            // Skip if no file to load
            if( ! file_exists( $integration_file ) ) {
                continue;
            }

            require_once $integration_file;

        }

        closedir( $integrations );

    }

    /**
     * Include integrations files
     *
     * @access      private
     * @since       1.0.0
     * @param       string  $integration
     * @param       array   $active_plugins
     * @param       array   $active_sitewide_plugins
     * @return      bool
     */
    private function is_integration_active( $integration, $active_plugins, $active_sitewide_plugins ) {

        $plugins = array(
            "automatorwp-{$integration}/automatorwp-{$integration}.php",
        );

        if( $integration === 'elementor' ) {
            $plugins = array(
                "automatorwp-{$integration}-forms/automatorwp-{$integration}-forms.php",
            );
        }

        foreach( $plugins as $plugin ) {

            // Bail if plugin is active
            if( in_array( $plugin, $active_plugins, true ) ) {
                return true;
            }

            // Bail if plugin is network wide active
            if ( isset( $active_sitewide_plugins[$plugin] ) ) {
                return true;
            }

            // Consider integration active during it's activation
            if( isset( $_REQUEST['action'] ) && $_REQUEST['action'] === 'activate'
                && isset( $_REQUEST['plugin'] ) && $_REQUEST['plugin'] === $plugin ) {
                return true;
            }

            // Support for bulk activate
            if( isset( $_REQUEST['action'] ) && $_REQUEST['action'] === 'activate-selected'
                && isset( $_REQUEST['checked'] ) && is_array( $_REQUEST['checked'] )
                && in_array( $plugin, $_REQUEST['checked'] ) ) {
                return true;
            }

        }

        return false;

    }

    /**
     * Setup plugin hooks
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function hooks() {

        // Setup our activation and deactivation hooks
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

        // Hook in all our important pieces
        add_action( 'plugins_loaded', array( $this, 'pre_init' ), 20 );
        add_action( 'plugins_loaded', array( $this, 'init' ), 50 );
        add_action( 'plugins_loaded', array( $this, 'post_init' ), 999 );

        add_action( 'init', array( $this, 'load_textdomain' ), 10 );

    }

    /**
     * Pre init function
     *
     * @access      private
     * @since       1.4.6
     * @return      void
     */
    function pre_init() {

        // Load all integrations
        $this->integrations();

        global $wpdb;

        $this->db = new AutomatorWP_Database();

        // Setup WordPress database tables
        $this->db->posts 				= $wpdb->posts;
        $this->db->postmeta 			= $wpdb->postmeta;
        $this->db->users 				= $wpdb->users;
        $this->db->usermeta 			= $wpdb->usermeta;

        // Setup AutomatorWP database tables
        $this->db->automations 			= $wpdb->automatorwp_automations;
        $this->db->automations_meta     = $wpdb->automatorwp_automations_meta;
        $this->db->triggers 		    = $wpdb->automatorwp_triggers;
        $this->db->triggers_meta 		= $wpdb->automatorwp_triggers_meta;
        $this->db->actions 		        = $wpdb->automatorwp_actions;
        $this->db->actions_meta 		= $wpdb->automatorwp_actions_meta;
        $this->db->logs 		        = $wpdb->automatorwp_logs;
        $this->db->logs_meta 		    = $wpdb->automatorwp_logs_meta;

        // Trigger our action to let other plugins know that AutomatorWP is getting initialized
        do_action( 'automatorwp_pre_init' );
    }

    /**
     * Init function
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    function init() {

        // Prevent to load text domains here
        add_filter( 'lang_dir_for_domain', array( $this, 'prevent_load_textdomain' ) );

        // Trigger our action to let other plugins know that AutomatorWP is ready
        do_action( 'automatorwp_init' );

        // Restore text domains load
        remove_filter( 'lang_dir_for_domain', array( $this, 'prevent_load_textdomain' ) );

    }

    /**
     * Post init function
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    function post_init() {

        // Trigger our action to let other plugins know that AutomatorWP has been initialized
        do_action( 'automatorwp_post_init' );

    }

    /**
     * Activation
     *
     * @access      private
     * @since       1.0.0
     */
    function activate() {

        // Include our important bits
        $this->libraries();
        $this->includes();

        require_once AUTOMATORWP_DIR . 'includes/install.php';

        automatorwp_install();

    }

    /**
     * Deactivation
     *
     * @access      private
     * @since       1.0.0
     */
    function deactivate() {

        // Include our important bits
        $this->libraries();
        $this->includes();

        require_once AUTOMATORWP_DIR . 'includes/uninstall.php';

        automatorwp_uninstall();

    }

    public function prevent_load_textdomain() {
        return false;
    }

    /**
     * Internationalization
     *
     * @access      public
     * @since       1.0.0
     * @return      void
     */
    public function load_textdomain() {

        // Set filter for language directory
        $lang_dir = AUTOMATORWP_DIR . '/languages/';
        $lang_dir = apply_filters( 'automatorwp_languages_directory', $lang_dir );

        // Traditional WordPress plugin locale filter
        $locale = apply_filters( 'plugin_locale', get_locale(), 'automatorwp' );
        $mofile = sprintf( '%1$s-%2$s.mo', 'automatorwp', $locale );

        // Setup paths to current locale file
        $mofile_local   = $lang_dir . $mofile;
        $mofile_global  = WP_LANG_DIR . '/automatorwp/' . $mofile;

        if( file_exists( $mofile_global ) ) {
            // Look in global /wp-content/languages/automatorwp/ folder
            load_textdomain( 'automatorwp', $mofile_global );
        } elseif( file_exists( $mofile_local ) ) {
            // Look in local /wp-content/plugins/automatorwp/languages/ folder
            load_textdomain( 'automatorwp', $mofile_local );
        } else {
            // Load the default language files
            load_plugin_textdomain( 'automatorwp', false, $lang_dir );
        }

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP The one true AutomatorWP
 */
function AutomatorWP() {
    return AutomatorWP::instance();
}

AutomatorWP();