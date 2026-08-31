<?php
/**
 * Utilities
 *
 * @package     AutomatorWP\AI_Assistant\Utilities
 * @since       1.0.0
 * @version     1.0.0
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get the list of available models
 *
 * @since	    1.0.0
 *
 * @return array
 */
function automatorwp_ai_assistant_get_models() {

    $models = array();

    if( class_exists( 'WordPress\AiClient\AiClient' ) ) {
        $registry = WordPress\AiClient\AiClient::defaultRegistry();

        $requirements = new WordPress\AiClient\Providers\Models\DTO\ModelRequirements(
            array( WordPress\AiClient\Providers\Models\Enums\CapabilityEnum::textGeneration() ),
            array()
        );

        foreach( $registry->findModelsMetadataForSupport( $requirements ) as $result ) {
            $provider = $result->getProvider();
            $provider_id = $provider->getId();

            $models[$provider_id] = array(
                'name' => $provider->getName(),
                'models' => array(),
            );

            foreach( $result->getModels() as $model ) {
                $models[$provider_id]['models'][$model->getId()] = $model->getName();
            }
        }
    }

    return $models;

}

/**
 * Get models IDs
 *
 * @since	    1.0.0
 *
 * @return array
 */
function automatorwp_ai_assistant_get_models_ids() {

    $providers = automatorwp_ai_assistant_get_models();
    $models_ids = array();

        foreach( $providers as $provider ) {


            foreach( $provider['models'] as $id => $name ) {
                $models_ids[] = $id;
            }
        }


    return $models_ids;

}

/**
 * Get a function response from a message
 *
 * @since   1.0.0
 *
 * @param \WordPress\AiClient\Messages\DTO\Message $message
 *
 * @return mixed|false
 */
function automatorwp_ai_assistant_get_function_response( $message ) {
    $array = $message->toArray();
    $response = false;

    if(
        isset( $array['parts'] )
        && isset( $array['parts'][0] )
        && isset( $array['parts'][0]['functionResponse'] )
        && isset( $array['parts'][0]['functionResponse']['response'] )
    ) {
        $response = $array['parts'][0]['functionResponse']['response'];
    }

    return $response;

}

/**
 * Get all function responses from a message
 *
 * @since   1.0.0
 *
 * @param \WordPress\AiClient\Messages\DTO\Message $message
 *
 * @return array
 */
function automatorwp_ai_assistant_get_all_function_responses( $message ) {
    $array = $message->toArray();
    $responses = array();

    if( isset( $array['parts'] ) && is_array( $array['parts'] ) ) {
        foreach( $array['parts'] as $part ) {
            if( isset( $part['functionResponse'] )
                && isset( $part['functionResponse']['response'] ) ) {
                $responses[] = $part['functionResponse']['response'];
            }
        }
    }

    return $responses;

}

function automatorwp_ai_assistant_resolve_automation_ids( $search = '', $args = array() ) {

    global $wpdb;

    if ( empty( $search ) ) return array();

    $args = wp_parse_args( $args, array(
        'limit' => 3,
    ) );

    $ct_table = ct_setup_table( 'automatorwp_automations' );

    // Pull back the search string
    $search = esc_sql( $wpdb->esc_like( $search ) );
    $from = "FROM {$ct_table->db->table_name} as a ";

    $where = "WHERE ( ";

    // Search for title or slug
    $where .= "a.title LIKE '%{$search}%' ";

    // Search by id
    if( is_numeric( $search ) )
        $where .= "OR a.id = '{$search}' ";

    $where .= " ) ";

    $limit = isset( $args['limit'] ) ? absint( $args['limit'] ) : 3;

    $ids = $wpdb->get_col(
        "SELECT a.id
             {$from}
             {$where}
             LIMIT {$limit}",
    );

    ct_reset_setup_table();

    return $ids;

}

function automatorwp_ai_assistant_get_automation( $search = '', $args = array() ) {

    $automation_ids = automatorwp_ai_assistant_resolve_automation_ids( $search, $args );
    $ability = new AutomatorWP_AI_Assistant_Ability();

    $singular = __( 'automation', 'automatorwp' );
    $plural = __( 'automations', 'automatorwp' );

    // Bail if can not find the post
    if( count( $automation_ids ) === 0 ) {
        return $ability->response_error( sprintf(
        // translators: %1$s: Post type singular %2$s: Search term
            __( 'I couldn\'t find the %1$s "%2$s".', 'automatorwp' )
            . ' ' . __( 'Try providing a different title or the ID number.', 'automatorwp' ),
            $singular,
            $search
        ) );
    } else if( count( $automation_ids ) > 1 ) {
        // Bail if more than 1 post found
        return $ability->response_error( sprintf(
        // translators: %1$s: Post type plural %2$s: Search term
            __( 'I found several %1$s matching with "%2$s".', 'automatorwp' )
            . ' ' . __( 'Try providing a different title or the ID number.', 'automatorwp' ),
            $plural,
            $search
        ) );
    }

    ct_setup_table( 'automatorwp_automations' );
    $automation = ct_get_object( absint( $automation_ids[0] ) );
    ct_reset_setup_table();

    if( ! $automation ) {
        return $ability->response_error( sprintf(
        // translators: %1$s: Post type singular %2$s: Search term
            __( 'I couldn\'t find the %1$s "%2$s".', 'automatorwp' )
            . ' ' . __( 'Try providing a different title or the ID number.', 'automatorwp' ),
            $singular,
            $search
        ) );
    }

    return $automation;

}