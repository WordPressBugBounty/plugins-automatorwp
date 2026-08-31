<?php
/**
 * AutomatorWP AI Assistant
 *
 * @package     AutomatorWP\AI_Assistant
 * @since       1.0.0
 * @version     1.0.0
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Constants
define( 'AUTOMATORWP_AI_ASSISTANT_VER', '1.0.0' );
define( 'AUTOMATORWP_AI_ASSISTANT_FILE', __FILE__ );
define( 'AUTOMATORWP_AI_ASSISTANT_DIR', plugin_dir_path( __FILE__ ) );
define( 'AUTOMATORWP_AI_ASSISTANT_URL', plugin_dir_url( __FILE__ ) );

// Classes
require_once AUTOMATORWP_AI_ASSISTANT_DIR . 'classes/automatorwp-ai-assistant-ability.php';

// Includes
require_once AUTOMATORWP_AI_ASSISTANT_DIR . 'includes/abilities.php';
require_once AUTOMATORWP_AI_ASSISTANT_DIR . 'includes/admin.php';
require_once AUTOMATORWP_AI_ASSISTANT_DIR . 'includes/ajax-functions.php';
require_once AUTOMATORWP_AI_ASSISTANT_DIR . 'includes/functions.php';
require_once AUTOMATORWP_AI_ASSISTANT_DIR . 'includes/help.php';
require_once AUTOMATORWP_AI_ASSISTANT_DIR . 'includes/logs.php';
require_once AUTOMATORWP_AI_ASSISTANT_DIR . 'includes/scripts.php';
require_once AUTOMATORWP_AI_ASSISTANT_DIR . 'includes/templates.php';
require_once AUTOMATORWP_AI_ASSISTANT_DIR . 'includes/utilities.php';

// Abilities
require_once AUTOMATORWP_AI_ASSISTANT_DIR . 'includes/abilities/create-automation.php';
require_once AUTOMATORWP_AI_ASSISTANT_DIR . 'includes/abilities/add-triggers.php';
require_once AUTOMATORWP_AI_ASSISTANT_DIR . 'includes/abilities/add-actions.php';
require_once AUTOMATORWP_AI_ASSISTANT_DIR . 'includes/abilities/run-actions.php';