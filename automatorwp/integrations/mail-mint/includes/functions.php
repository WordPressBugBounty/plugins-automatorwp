<?php
/**
 * Functions
 *
 * @package     AutomatorWP\Integrations\Mail_Mint\Functions
 * @author      AutomatorWP <contact@automatorwp.com>
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Options callback for select2 fields assigned to tags
 *
 * @since 1.0.0
 * 
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_mail_mint_options_cb_tag( $field ) {

    // Setup vars
    $value = $field->escaped_value;
    $none_value = 'any';
    $none_label = __( 'any tag', 'automatorwp' );
    $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );
    
    if( ! empty( $value ) ) {
        if( ! is_array( $value ) ) {
            $value = array( $value );
        }
    
        foreach( $value as $tag_id ) {

            // Skip option none
            if( $tag_id === $none_value ) {
                continue;
            }

            $options[$tag_id] = automatorwp_mail_mint_get_tag_name( $tag_id );
        }
    }

    return $options;

}

/**
 * Get all Mail Mint tags
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_mail_mint_get_tags() {

    $tags = array();

    // Bail if class does not exist
    if( ! class_exists ( 'Mint\MRM\DataBase\Models\ContactGroupModel' ) )
        return;

    //Type: lists or tags
    $all_tags = Mint\MRM\DataBase\Models\ContactGroupModel::get_all_lists_or_tags( 'tags' );

    foreach ( $all_tags as $tag ) {
        $tags[] = array(
            'id'    => $tag['id'],
            'name'  => $tag['title'],
        );
    }

    return $tags;

}

/**
* Get the tag name
*
* @since 1.0.0
* 
* @param string $list_id
*
* @return array
*/
function automatorwp_mail_mint_get_tag_name( $tag_id ) {

    // Empty title if no ID provided
    if( absint( $tag_id ) === 0 ) {
        return '';
    }

    // Bail if class does not exist
    if( ! class_exists ( 'Mint\MRM\DataBase\Models\ContactGroupModel' ) || ! class_exists ( 'MRM\Common\MrmCommon' ) )
            return;

    $all_tags = Mint\MRM\DataBase\Models\ContactGroupModel::get_all_lists_or_tags( 'tags' );

    $tag = MRM\Common\MrmCommon::search_for_id( $tag_id, $all_tags );

    return $tag['title'];

}
