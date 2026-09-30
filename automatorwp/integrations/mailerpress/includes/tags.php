<?php
/**
 * Tags
 *
 * @package     AutomatorWP\MailerPress\Tags
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Contact tags
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_mailerpress_contact_tags() {

    return array(
        'contact_id' => array(
            'label'     => __( 'Contact ID', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => '123',
        ),
        'contact_email' => array(
            'label'     => __( 'Contact email', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'contact@automatorwp.com',
        ),
        'contact_first_name' => array(
            'label'     => __( 'Contact first name', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'John',
        ),
        'contact_last_name' => array(
            'label'     => __( 'Contact last name', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'Doe',
        ),
        'contact_status' => array(
            'label'     => __( 'Contact status', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'subscribed',
        ),
        'contact_created_date' => array(
            'label'     => __( 'Contact date addition', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'YYYY-MM-DD HH:MM:SS',
        ),
    );
}


/**
 * Custom trigger tag replacement
 *
 * @since 1.0.0
 *
 * @param string    $replacement    The tag replacement
 * @param string    $tag_name       The tag name (without "{}")
 * @param stdClass  $trigger        The trigger object
 * @param int       $user_id        The user ID
 * @param string    $content        The content to parse
 * @param stdClass  $log            The last trigger log object
 *
 * @return string
 */
function automatorwp_mailerpress_get_trigger_contact_tag_replacement( $replacement, $tag_name, $trigger, $user_id, $content, $log ) {

    $trigger_args = automatorwp_get_trigger( $trigger->type );

    // Skip if trigger is not from this integration
    if( $trigger_args['integration'] !== 'mailerpress' ) {
        return $replacement;
    }

    if( ! class_exists ( 'MailerPress\Models\Contacts' ) ){            
        return $replacement;       
    }
        
    // Get contact data
    $contact_id = automatorwp_get_log_meta( $log->id, 'contact_id', true );
    $contact_data = new MailerPress\Models\Contacts;
    $contact = $contact_data->get( $contact_id );

    switch( $tag_name ) {
        case 'contact_id':
            $replacement = $contact_id;
            break;
        case 'contact_email':
            $replacement = $contact->email;
            break;
        case 'contact_first_name':
            $replacement = $contact->first_name;
            break;
        case 'contact_last_name':
            $replacement = $contact->last_name;
            break;
        case 'contact_status':
            $replacement = $contact->subscription_status;
            break;
        case 'contact_created_date':
            $replacement = $contact->created_at;
            break;
    }

    return $replacement;  
    
}
add_filter( 'automatorwp_get_trigger_tag_replacement', 'automatorwp_mailerpress_get_trigger_contact_tag_replacement', 10, 6 );

/**
 * Tag tags
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_mailerpress_tag_tags() {

    return array(
        'tag_id' => array(
            'label'     => __( 'Tag ID', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => '123',
        ),
        'tag_title' => array(
            'label'     => __( 'Tag title', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'Tag',
        ),
    );
}


/**
 * Custom trigger tag replacement
 *
 * @since 1.0.0
 *
 * @param string    $replacement    The tag replacement
 * @param string    $tag_name       The tag name (without "{}")
 * @param stdClass  $trigger        The trigger object
 * @param int       $user_id        The user ID
 * @param string    $content        The content to parse
 * @param stdClass  $log            The last trigger log object
 *
 * @return string
 */
function automatorwp_mailerpress_get_trigger_tag_tag_replacement( $replacement, $tag_name, $trigger, $user_id, $content, $log ) {


    $trigger_args = automatorwp_get_trigger( $trigger->type );

    // Skip if trigger is not from this integration
    if( $trigger_args['integration'] !== 'mailerpress' ) {
        return $replacement;
    }

    // Get tag data
    $tag_id = automatorwp_get_log_meta( $log->id, 'tag_id', true );

    switch( $tag_name ) {
        case 'tag_id':
            $replacement = $tag_id;
            break;
        case 'tag_title':
            $replacement = automatorwp_mailerpress_get_tag_name( $tag_id );
            break;        
    }

    return $replacement;

}
add_filter( 'automatorwp_get_trigger_tag_replacement', 'automatorwp_mailerpress_get_trigger_tag_tag_replacement', 10, 6 );