<?php
/**
 * Functions
 *
 * @package     AutomatorWP\AI_Assistant\Functions
 * @since       1.0.0
 * @version     1.0.0
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Check if is a AutomatorWP page to render/enqueue our assets
 *
 * @since   1.0.0
 */
function automatorwp_ai_assistant_is_valid_page() {

    if ( ( isset( $_GET['page'] ) && (
            $_GET['page'] === 'automatorwp'
            || $_GET['page'] === 'automatorwp_automations'
            || $_GET['page'] === 'edit_automatorwp_automations'
            || $_GET['page'] === 'automatorwp_logs'
            || $_GET['page'] === 'edit_automatorwp_logs'
            || $_GET['page'] === 'automatorwp_settings'
            || $_GET['page'] === 'automatorwp_licenses'
        )
    ) ) {
        return true;
    }

    return false;
}

/**
 * Get all triggers slugs
 *
 * @since   1.0.0
 *
 * @return array
 */
function automatorwp_ai_assistant_get_triggers_slugs() {

    $slugs = array();

    $excluded = array( 'filter' );

    foreach( AutomatorWP()->triggers as $trigger => $args ) {
        if( in_array( $trigger, $excluded ) ) continue;

        $slugs[] = $trigger;
    }

    return $slugs;

}

/**
 * Get all actions slugs
 *
 * @since   1.0.0
 *
 * @return array
 */
function automatorwp_ai_assistant_get_actions_slugs() {

    $slugs = array();

    $excluded = array( 'filter' );

    foreach( AutomatorWP()->actions as $action => $args ) {
        if( in_array( $action, $excluded ) ) continue;

        $slugs[] = $action;
    }

    return $slugs;

}

/**
 * Get all actions slugs
 *
 * @since   1.0.0
 *
 * @return string
 */
function automatorwp_ai_assistant_get_tags_list() {

    $tags = '';

    foreach( automatorwp_get_tags() as $group => $group_data ) {
        if( isset( $group_data['tags'] ) && is_array( $group_data['tags'] ) ) {
            foreach( $group_data['tags'] as $tag => $data ) {
                $tags .= '{' . $tag . '} ';
            }
        }
    }

    $tags .= '{times} ';

    foreach( automatorwp_utilities_post_tags() as $tag => $data ) {
        $tags .= '{' . $tag . '} ';
    }

    return $tags;

}

/**
 * Get triggers options list
 *
 * @since   1.0.0
 *
 * @return string
 */
function automatorwp_ai_assistant_get_triggers_options_list() {

    $output = '';

    $excluded = array( 'filter' );

    $output .= 'All triggers support the "times" option.' . "\n";

    foreach( AutomatorWP()->triggers as $trigger => $type_args ) {
        if( in_array( $trigger, $excluded ) ) continue;

        $output .= $trigger . ': ';

        foreach( $type_args['options'] as $option => $args ) {
            foreach( $args['fields'] as $field_id => $field_args ) {
                if( $field_id === 'times' ) continue;

                if( isset( $field_args['fields'] ) ) {
                    $output .= $field_id . ' [' . implode( ', ', array_keys( $field_args['fields'] ) ) . '], ';
                } else {
                    $output .= $field_id . ', ';
                }


            }
        }

        $output .= "\n";
    }

    return $output;

}

/**
 * Get actions options list
 *
 * @since   1.0.0
 *
 * @return string
 */
function automatorwp_ai_assistant_get_actions_options_list() {

    $output = '';

    $excluded = array( 'filter', 'automatorwp_anonymous_user' );

    foreach( AutomatorWP()->actions as $action => $type_args ) {
        if( in_array( $action, $excluded ) ) continue;

        $output .= $action . ': ';

        foreach( $type_args['options'] as $option => $args ) {
            foreach( $args['fields'] as $field_id => $field_args ) {
                $output .= $field_id . ', ';
            }
        }

        $output .= "\n";
    }

    return $output;

}

/**
 * Get item ability properties
 *
 * @since   1.0.0
 *
 * @return array
 */
function automatorwp_ai_assistant_get_items_properties( $item_type = '' ) {


    if( $item_type === 'trigger' ) {
        $label = __( 'trigger', 'automatorwp' );
        $types = automatorwp_ai_assistant_get_triggers_slugs();
        $options = automatorwp_ai_assistant_get_triggers_options_list();
    } else {
        $label = __( 'action', 'automatorwp' );
        $types = automatorwp_ai_assistant_get_actions_slugs();
        $options = automatorwp_ai_assistant_get_actions_options_list();
    }

    $tags_info = ' ' . esc_html__( 'You can use dynamic data named tags.', 'automatorwp' ) . ' ' . $options
        . ' ' . esc_html__( 'List of tags:', 'automatorwp' ) . ' ' . automatorwp_ai_assistant_get_tags_list();

    $properties = array(
        'type' => array(
            'description' => sprintf( esc_html__( 'The %s type.', 'automatorwp' ), $label ),
            'type' => 'string',
            'enum' => $types,
        ),
        'options' => array(
            'description' => sprintf( esc_html__( 'The %s options.', 'automatorwp' ), $label )
                . ' ' . esc_html__( 'List of options by type:', 'automatorwp' ) . ' ' . $options
                . ( $item_type === 'action' ? $tags_info : '' ),
            'type'       => 'array',
            'items'       => array(
                'type' => 'object',
                'properties' => array(
                    'name' => array(
                        'description' => esc_html__( 'The option name.', 'automatorwp' ),
                        'type' => 'string',
                    ),
                    'value' => array(
                        'description' => esc_html__( 'The option value.', 'automatorwp' ),
                        'type' => 'string',
                    ),
                ),
            ),
        ),
    );



    return $properties;

}

/**
 * Process requirements
 *
 * @since   1.0.0
 */
function automatorwp_ai_assistant_process_items( $items = array(), $automation_id = 0, $item_type = '' ) {

    // Used to access ability helper functions
    $ability = new AutomatorWP_AI_Assistant_Ability();

    $updated_items = array();

    ct_setup_table( "automatorwp_{$item_type}s" );

    // Requirements
    if( is_array( $items ) && count( $items ) ) {

        foreach( $items as $k => $i ) {

            $i = wp_parse_args( $i, array(
                'type' => '',
                'options' => array(),
            ) );

            $i['type'] = sanitize_key( trim( $i['type'] ) );

            if( $item_type === 'trigger' ) {
                $type_args = automatorwp_get_trigger( $i['type'] );
            } else if( $item_type === 'action' ) {
                $type_args = automatorwp_get_action( $i['type'] );
            }

            // Bail if not is a valid type
            if( ! $type_args ) continue;

            $object = array(
                'automation_id' => $automation_id,
                'title' => '',
                'type' => $i['type'],
                'status' => 'active',
                'position' => $k,
                'date' => date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ),
            );

            // Create the new trigger/action
            $id = ct_insert_object( $object );

            // Couldn't insert the requirement
            if( is_wp_error( $id ) ) continue;

            // Append the id to the trigger/action data
            $object['id'] = $id;

            // Parse options given
            $options = automatorwp_ai_assistant_build_options( $i['options'], $type_args );

            // Update object metas
            foreach( $options as $name => $value )
                ct_update_object_meta( $id, $name, $value );


            // Flush cache to ensure that option replacement gets the newest value
            wp_cache_flush();

            $title = automatorwp_parse_automation_item_edit_label( (object) $object, $item_type, 'view' );

            // Update the trigger title
            ct_update_object( array(
                'id' => $id,
                'title' => $title
            ) );

            $updated_items[] = $title;
        }
    }

    ct_reset_setup_table();

    return $updated_items;

}

/**
 * Build item options
 *
 * @since   1.0.0
 *
 * @param array $options
 * @param array $type_args
 *
 * @return array
 */
function automatorwp_ai_assistant_build_options( $options, $type_args ) {

    $options = automatorwp_ai_assistant_sanitize_options( $options );

    // Loop all options looking for their fields
    foreach( $type_args['options'] as $option => $args ) {

        // Loop all option fields to initialize them
        foreach( $args['fields'] as $field_id => $field_args ) {

            $options[$field_id] = automatorwp_ai_assistant_get_option_value( $field_id, $field_args, $options );

        }

    }

    return $options;

}

/**
 * Sanitize items options
 *
 * @since   1.0.0
 *
 * @param array $options
 *
 * @return array
 */
function automatorwp_ai_assistant_sanitize_options( $options ) {

    $new = array();

    foreach( $options as $option ) {
        $option['name'] = sanitize_key( trim( $option['name'] ) );
        $option['value'] = sanitize_text_field( $option['value'] );

        $new[$option['name']] = $option['value'];
    }

    return $new;

}

/**
 * Update option value
 *
 * @since   1.0.0
 *
 * @param string $field_id
 * @param array $field_args
 * @param array $options
 */
function automatorwp_ai_assistant_get_option_value( $field_id, $field_args, $options ) {

    $ability = new AutomatorWP_AI_Assistant_Ability();

    if( ! isset( $options[$field_id] ) && isset( $field_args['default'] ) ) {
        return $field_args['default'];
    }

    if( isset( $field_args['classes'] ) ) {
        if( $field_args['classes'] === 'automatorwp-post-selector' ) {

            $post_type = isset( $field_args['attributes'] ) && isset( $field_args['attributes']['data-post-type'] ) ? $field_args['attributes']['data-post-type'] : '';

            $post = $ability->get_post( $options[$field_id], array( 'post_type' => $post_type ) );

            if( ! $ability->is_response_error( $post ) ) {
                $options[$field_id] = $post->ID;
            }
        } else if( $field_args['classes'] === 'automatorwp-ajax-selector' ) {

            $action_cb = '';

            if( isset( $field_args['attributes'] ) && isset( $field_args['attributes']['data-action'] ) ) {
                $action_cb = $field_args['attributes']['data-action'];
            }

            if( $action_cb === '' ) return;

            $result = null;

            // Try to call to action_cb if defined
            if( is_callable( $action_cb ) ) {
                $result = call_user_func( $action_cb, $options[$field_id] );

                // Expected result is an array of id & text keys
                if( is_array( $result ) && isset( $result[0] ) && isset( $result[0]['id'] ) )
                    $result = $result[0]['id'];
            }

            /**
             * Custom ajax selector search for AI (without ajax)
             *
             * @param mixed $result
             * @param string $search
             *
             * @return mixed null if no results found, can return a response error
             */
            $result = apply_filters( 'automatorwp_ai_' . $action_cb, $result, $options[$field_id] );

            if( $result === null ) return;

            if( ! $ability->is_response_error( $result ) )
                $options[$field_id] = $result;
            else
                $options[$field_id] = '';

        }
    }

    if( $field_id === 'user' || $field_id === 'user_id' ) {
        $user = $ability->get_user( $options[$field_id] );

        if( ! $ability->is_response_error( $user ) ) {
            $options[$field_id] = $user->ID;
        }
    }

    if( isset( $options[$field_id] ) )
        return $options[$field_id];

    return '';

}

/**
 * Get items answer
 *
 * @since   1.0.0
 * 
 * @param array $items
 * @param string $items_type
 *
 * @return string
 */
function automatorwp_ai_assistant_get_items_answer( $items = array(), $items_type = '', $title = true ) {

    // Bail if not requirements
    if ( empty( $items ) ) {
        return '';
    }

    $text = '';

    $count = count( $items );

    if( $title ) {
        if( $items_type === 'trigger' ) {
            $title = _n( 'Trigger', 'Triggers', $count, 'automatorwp' );
        } else {
            $title = _n( 'Action', 'Actions', $count, 'automatorwp' );
        }

        $text .=  '####' . $title . "\n";
        $text .= "\n";
    }

    foreach( $items as $item ) {
        
        $title = $item;
        $text .= '- ' . esc_html( $title ) . "\n";
        
    }

    $text .= "\n";

    return $text;
}