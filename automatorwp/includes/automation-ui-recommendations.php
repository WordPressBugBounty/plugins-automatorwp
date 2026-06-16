<?php
/**
 * Automation UI Recommendations
 *
 * @package     AutomatorWP\Automation_UI_Recommendations
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Automation UI add-ons recommendations
 *
 * @since 1.1.2
 *
 * @param stdClass  $automation         The automation object
 * @param string    $item_type          The item type (trigger|action)
 */
function automatorwp_automation_ui_integrations_recommendations( $automation, $item_type ) {

    if( class_exists( 'AutomatorWP_Pro' ) ) {
        return;
    }

    if( (bool) automatorwp_get_option( 'disable_pro_recommendations' ) ) {
        return;
    }

    ?>

    <div class="automatorwp-more-integrations">
        <span><?php if ( $item_type === 'trigger' ) : _e( 'Looking for more triggers?', 'automatorwp' ); elseif ( $item_type === 'action' ) : _e( 'Looking for more actions?', 'automatorwp' ); endif; ?></span>
        <a href="https://automatorwp.com/add-ons/" target="_blank"><?php _e( 'View all add-ons', 'automatorwp' ); ?></a>
    </div>

    <?php

}
add_action( 'automatorwp_automation_ui_add_item_content_bottom', 'automatorwp_automation_ui_integrations_recommendations', 10, 2 );

/**
 * Get recommended integrations
 *
 * @since 1.1.2
 *
 * @return array|WP_Error Object with recommended integrations
 */
function automatorwp_get_recommended_integrations() {

    if( (bool) automatorwp_get_option( 'disable_pro_recommendations' ) ) {
        return array();
    }

    $integrations = automatorwp_integrations_api();

    if( is_wp_error( $integrations ) ) {
        return $integrations;
    }

    $recommended_integrations = array();

    foreach ( $integrations as $integration ) {

        if( $integration === null ) {
            continue;
        }

        // Skip integration if can't determine its class
        if( empty( $integration->integration_class ) ) {
            continue;
        }

        // Skip integration if already installed
        if( class_exists( $integration->integration_class ) ) {
            continue;
        }

        // Skip integration if free version already installed
        if( class_exists( $integration->integration_class . '_Integration' ) ) {
            continue;
        }

        // Bail if integration plugin is not installed
        if( ! automatorwp_automation_ui_integration_plugin_installed( $integration ) ) {
            continue;
        }

        $recommended_integrations[] = $integration;

    }

    return $recommended_integrations;

}

/**
 * Function to contact with the AutomatorWP integrations API
 *
 * @since  1.1.2
 *
 * @return object|WP_Error Object with AutomatorWP integrations
 */
function automatorwp_integrations_api() {

    $cache = automatorwp_get_cache( 'automatorwp_integrations_api', false, false );

    // If result already cached, return it
    if( $cache !== false ) {
        return $cache;
    }

    // If a integrations api request has been cached already, then use cached integrations
    if ( false !== ( $res = get_transient( 'automatorwp_integrations_api' ) ) ) {
        return $res;
    }

    $url = $http_url = 'http://automatorwp.com/wp-json/automatorwp/integrations';

    if ( $ssl = wp_http_supports( array( 'ssl' ) ) ) {
        $url = set_url_scheme( $url, 'https' );
    }

    $http_args = array(
        'timeout' => 15,
    );

    $request = wp_remote_get( $url, $http_args );

    if ( $ssl && is_wp_error( $request ) ) {
        // Try again without SSL
        $request = wp_remote_get( $http_url, $http_args );
    }

    if ( is_wp_error( $request ) ) {
        // No way to contact server
        $res = array();

        // Set a transient for 1 day
        set_transient( 'automatorwp_integrations_api', $res, ( 24 * 1 ) * HOUR_IN_SECONDS );
    } else {
        $res = json_decode( $request['body'] );

        $res = (array) $res;

        // Set a transient for 1 week with api integrations
        set_transient( 'automatorwp_integrations_api', $res, ( 24 * 7 ) * HOUR_IN_SECONDS );
    }

    // Update cache
    automatorwp_set_cache( 'automatorwp_integrations_api', $res );

    return $res;

}

/**
 * Inform about integration pro choices
 *
 * @since 1.2.4
 *
 * @param string    $integration_name   The integration name
 * @param array     $args               Integration arguments
 * @param stdClass  $automation         The automation object
 * @param string    $item_type          The item type
 */
function automatorwp_automation_ui_integration_pro_choice( $integration_name, $args, $automation, $item_type ) {

    if( (bool) automatorwp_get_option( 'disable_pro_recommendations' ) ) {
        return;
    }

    $integrations = automatorwp_integrations_api();

    if ( is_array( $integrations ) && empty( $integrations) ) {
        return;
    }

    if( is_wp_error( $integrations ) ) {
        return;
    }

    foreach ( $integrations as $integration ) {
        if( ! isset( $integration ) ) {
            continue;
        }

        if( ! is_object( $integration ) ) {
            continue;
        }

        // Break if found the integration
        if( $integration->code === $integration_name ) {
            break;
        }
    }

    if( $integration === null ) {
        return;
    }

    // Bail if integration if already installed
    if( class_exists( $integration->integration_class ) ) {
        return;
    }

    // Bail if integration plugin is not installed
    if( ! automatorwp_automation_ui_integration_plugin_installed( $integration ) ) {
        return;
    }

    // Get integration items
    $items = array();

    if( $item_type === 'trigger' ) {

        $choices = automatorwp_get_integration_triggers( $integration_name );

        // Don't list if already listed
        if( count( $choices ) ) {
            return;
        }

        $items = $integration->triggers;

    } else if( $item_type === 'action' ) {

        $choices = automatorwp_get_integration_actions( $integration_name );

        // Don't list if already listed
        if( count( $choices ) ) {
            return;
        }

        $items = $integration->actions;
    }

    // Check the free and pro elements
    $has_free = false;
    $has_pro = false;

    foreach( $items as $item ) {

        // For triggers, only get anonymous choices
        if( $item_type === 'trigger' && $automation->type === 'anonymous' && ! $item->anonymous ) {
            continue;
        }

        if( $item->free ) {
            $has_free = true;
        } else {
            $has_pro = true;
        }

    }

    if( ! $has_free && $has_pro ) : ?>

        <div class="automatorwp-integration automatorwp-integration-pro"
             data-integration="<?php echo esc_attr( $integration_name ); ?>"
             data-label="<?php echo esc_attr( $args['label'] ); ?>"
             data-icon="<?php echo esc_attr( $args['icon'] ); ?>">
            <span class="automatorwp-integration-pro-badge">PRO</span>
            <div class="automatorwp-integration-icon">
                <img src="<?php echo esc_attr( $args['icon'] ); ?>" alt="<?php echo esc_attr( $args['label'] ); ?>">
            </div>
            <div class="automatorwp-integration-label"><?php echo $args['label']; ?></div>
        </div>

    <?php endif;

}
add_action( 'automatorwp_automation_ui_after_integration_choice', 'automatorwp_automation_ui_integration_pro_choice', 10, 4 );

/**
 * Inform about integration triggers pro choices
 *
 * @since 1.2.4
 *
 * @param string    $integration_name   The integration name
 * @param array     $args               Integration arguments
 * @param stdClass  $automation         The automation object
 * @param string    $item_type          The item type
 */
function automatorwp_automation_ui_integration_triggers_pro_choices( $integration_name, $args, $automation, $item_type ) {

    if( (bool) automatorwp_get_option( 'disable_pro_recommendations' ) ) {
        return;
    }

    $integrations = automatorwp_integrations_api();

    if ( is_array( $integrations ) && empty( $integrations) ) {
        return;
    }

    if( is_wp_error( $integrations ) ) {
        return;
    }

    foreach ( $integrations as $integration ) {
        if( ! isset( $integration ) ) {
            continue;
        }

        // Break if found the integration
        if( $integration->code === $integration_name ) {
            break;
        }
    }

    if( $integration === null ) {
        return;
    }

    // Bail if integration is already installed
    if( class_exists( $integration->integration_class ) ) {
        return;
    }

    // Bail if integration plugin is not installed
    if( ! automatorwp_automation_ui_integration_plugin_installed( $integration ) ) {
        return;
    }

    // Get the list of already listed triggers
    $choices = automatorwp_get_integration_triggers( $integration_name );

    foreach( $integration->triggers as $i => $trigger ) {

        // For triggers, only get anonymous choices
        if( $automation->type === 'anonymous' && ! $trigger->anonymous ) {
            continue;
        }

        // Skip free triggers
        if( $trigger->free ) {
            continue;
        }

        // Skip if trigger label is empty
        if( empty( $trigger->label ) ) {
            continue;
        }

        // Skip if integration is already installed
        if( property_exists( $trigger, 'required_class' ) && ! empty( $trigger->required_class ) ) {
            if( class_exists( $trigger->required_class ) ) {
                continue;
            }
        }

        $already_listed = false;

        foreach( $choices as $choice ) {
            // Check if trigger has been already listed
            if( $choice['label'] === $trigger->label ) {
                $already_listed = true;
                break;
            }
        }

        // Skip triggers already listed
        if( $already_listed ) {
            continue;
        } ?>
        <option value="<?php echo esc_attr( $integration_name ) . '_' . $i; ?>" disabled="disabled"><?php echo $trigger->label; ?></option>
        <?php
    }

}
add_action( 'automatorwp_automation_ui_after_integration_triggers_choices', 'automatorwp_automation_ui_integration_triggers_pro_choices', 10, 4 );

/**
 * Inform about integration actions pro choices
 *
 * @since 1.2.4
 *
 * @param string    $integration_name   The integration name
 * @param array     $args               Integration arguments
 * @param stdClass  $automation         The automation object
 * @param string    $item_type          The item type
 */
function automatorwp_automation_ui_integration_actions_pro_choices( $integration_name, $args, $automation, $item_type ) {

    if( (bool) automatorwp_get_option( 'disable_pro_recommendations' ) ) {
        return;
    }

    $integrations = automatorwp_integrations_api();

    if ( is_array( $integrations ) && empty( $integrations) ) {
        return;
    }

    if( is_wp_error( $integrations ) ) {
        return;
    }

    foreach ( $integrations as $integration ) {
        if( ! isset( $integration ) ) {
            continue;
        }
        // Break if found the integration
        if( $integration->code === $integration_name ) {
            break;
        }
    }

    if( $integration === null ) {
        return;
    }

    // Bail if integration is already installed
    if( class_exists( $integration->integration_class ) ) {
        return;
    }

    // Bail if integration plugin is not installed
    if( ! automatorwp_automation_ui_integration_plugin_installed( $integration ) ) {
        return;
    }

    // Get the list of already listed actions
    $choices = automatorwp_get_integration_actions( $integration_name );

    foreach( $integration->actions as $i => $action ) {
        // Skip free actions
        if( $action->free ) {
            continue;
        }

        // Skip if action label is empty
        if( empty( $action->label ) ) {
            continue;
        }

        // Skip if integration is already installed
        if( property_exists( $action, 'required_class' ) && ! empty( $action->required_class ) ) {
            if( class_exists( $action->required_class ) ) {
                continue;
            }
        }

        $already_listed = false;

        foreach( $choices as $choice ) {
            // Check if action has been already listed
            if( $choice['label'] === $action->label ) {
                $already_listed = true;
                break;
            }
        }

        // Skip actions already listed
        if( $already_listed ) {
            continue;
        } ?>
        <option value="<?php echo esc_attr( $integration_name ) . '_' . $i; ?>" disabled="disabled"><?php echo $action->label; ?></option>

        <?php
    }

}
add_action( 'automatorwp_automation_ui_after_integration_actions_choices', 'automatorwp_automation_ui_integration_actions_pro_choices', 10, 4 );

/**
 * Check if integration plugin is installed
 *
 * @since 1.5.1
 *
 * @param stdClass $integration
 */
function automatorwp_automation_ui_integration_plugin_installed( $integration ) {

    if( $integration === null  || ! is_object( $integration ) ) {
        return false;
    }

    if( $integration->code === 'automatorwp' || $integration->code === 'wordpress' ) {
        return true;
    }

    // Check if required class exists
    if( property_exists( $integration, 'required_class' ) && ! empty( $integration->required_class ) ) {
        return class_exists( $integration->required_class );
    }

    // Check if required function exists
    if( property_exists( $integration, 'required_function' ) && ! empty( $integration->required_function ) ) {
        return function_exists( $integration->required_function );
    }

    // Check if required constant is defined
    if( property_exists( $integration, 'required_constant' ) && ! empty( $integration->required_constant ) ) {
        return defined( $integration->required_constant );
    }

    return false;

}