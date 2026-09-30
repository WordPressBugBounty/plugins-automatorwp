<?php
/**
 * Functions
 *
 * @package     AutomatorWP\Integrations\MailerPress\Functions
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
function automatorwp_mailerpress_options_cb_tag( $field ) {

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

            $options[$tag_id] = automatorwp_mailerpress_get_tag_name( $tag_id );
        }
    }

    return $options;

}

/**
 * Get all MailerPress tags
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_mailerpress_get_tags() {

    $tags = array();

    // Bail if class does not exist
    if( ! class_exists ( 'MailerPress\Models\Tags' ) )
        return;

    // Get tags data
    $tags_data = new MailerPress\Models\Tags;
    $all_tags = $tags_data->getAll();

    foreach ( $all_tags as $tag ) {

        $tags[] = array(
            'id'    => $tag->tag_id,
            'name'  => $tag->name,
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
function automatorwp_mailerpress_get_tag_name( $tag_id ) {

    // Empty title if no ID provided
    if( absint( $tag_id ) === 0 ) {
        return '';
    }

    global $wpdb;

    $tag_name = $wpdb->get_var( "SELECT name FROM {$wpdb->prefix}mailerpress_tags WHERE tag_id = {$tag_id}" );

    return $tag_name;

}

/**
* Add tag to contact
*
* @since 1.0.0
* 
* @param int $tag_id
* @param int $contact_id
*
* @return bool
*/
function automatorwp_mailerpress_add_tag_contact( $tag_id, $contact_id ) {

    global $wpdb;

    $contact_tags_table = $wpdb->prefix . 'mailerpress_contact_tags';

    // Check if contact has the tag
    $contact_has_tag = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM {$contact_tags_table} WHERE contact_id = %d AND tag_id = %d",
            $contact_id,
            $tag_id
        )
    );

    // Add tag to contact
    if ( ! $contact_has_tag ){
        $insert_result = $wpdb->insert(
            $contact_tags_table,
            array(
                'contact_id' => $contact_id,
                'tag_id'     => $tag_id,
            ),
            array( '%d', '%d' ),
        );

        return true;
    }

    return false;

}

/**
* Add contact
*
* @since 1.0.0
* 
* @param int $args
*
* @return bool
*/
function automatorwp_mailerpress_add_contact( $args ) {

    global $wpdb;

    $contact_table = $wpdb->prefix . 'mailerpress_contact';
	
    // Add contact
    $result = $wpdb->insert(
        $contact_table,
        $args,
    );

    return (bool) $result;

}