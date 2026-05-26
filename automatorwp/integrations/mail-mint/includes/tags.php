<?php
/**
 * Tags
 *
 * @package     AutomatorWP\Mail_Mint\Tags
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
function automatorwp_mail_mint_contact_tags() {

    return array(
        'contact_id' => array(
            'label'     => __( 'Contact ID', 'automatorwp-mail-mint' ),
            'type'      => 'text',
            'preview'   => '123',
        ),
        'contact_email' => array(
            'label'     => __( 'Contact email', 'automatorwp-mail-mint' ),
            'type'      => 'text',
            'preview'   => 'contact@automatorwp.com',
        ),
        'contact_first_name' => array(
            'label'     => __( 'Contact first name', 'automatorwp-mail-mint' ),
            'type'      => 'text',
            'preview'   => 'John',
        ),
        'contact_last_name' => array(
            'label'     => __( 'Contact last name', 'automatorwp-mail-mint' ),
            'type'      => 'text',
            'preview'   => 'Doe',
        ),
        'contact_status' => array(
            'label'     => __( 'Contact status', 'automatorwp-mail-mint' ),
            'type'      => 'text',
            'preview'   => 'subscribed',
        ),
        'contact_created_date' => array(
            'label'     => __( 'Contact date addition', 'automatorwp-mail-mint' ),
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
function automatorwp_mail_mint_get_trigger_contact_tag_replacement( $replacement, $tag_name, $trigger, $user_id, $content, $log ) {


    $trigger_args = automatorwp_get_trigger( $trigger->type );

    // Skip if trigger is not from this integration
    if( $trigger_args['integration'] !== 'mail_mint' ) {
        return $replacement;
    }

    if( ! class_exists ( 'Mint\MRM\DataBase\Models\ContactModel' ) )
        return '';

    // Get contact data
    $contact_id = automatorwp_get_log_meta( $log->id, 'contact_id', true );
    $contact_data = Mint\MRM\DataBase\Models\ContactModel::get( $contact_id );

    switch( $tag_name ) {
        case 'contact_id':
            $replacement = $contact_id;
            break;
        case 'contact_email':
            $replacement = $contact_data['email'];
            break;
        case 'contact_first_name':
            $replacement = $contact_data['first_name'];
            break;
        case 'contact_last_name':
            $replacement = $contact_data['last_name'];
            break;
        case 'contact_status':
            $replacement = $contact_data['status'];
            break;
        case 'contact_created_date':
            $replacement = $contact_data['created_at'];
            break;
    }

    return $replacement;

}
add_filter( 'automatorwp_get_trigger_tag_replacement', 'automatorwp_mail_mint_get_trigger_contact_tag_replacement', 10, 6 );

/**
 * Tag tags
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_mail_mint_tag_tags() {

    return array(
        'tag_id' => array(
            'label'     => __( 'Tag ID', 'automatorwp-mail-mint' ),
            'type'      => 'text',
            'preview'   => '123',
        ),
        'tag_title' => array(
            'label'     => __( 'Tag title', 'automatorwp-mail-mint' ),
            'type'      => 'text',
            'preview'   => 'Tag',
        ),
        'tag_created_date' => array(
            'label'     => __( 'Tag date creation', 'automatorwp-mail-mint' ),
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
function automatorwp_mail_mint_get_trigger_tag_tag_replacement( $replacement, $tag_name, $trigger, $user_id, $content, $log ) {


    $trigger_args = automatorwp_get_trigger( $trigger->type );

    // Skip if trigger is not from this integration
    if( $trigger_args['integration'] !== 'mail_mint' ) {
        return $replacement;
    }

    if( ! class_exists ( 'Mint\MRM\DataBase\Models\ContactGroupModel' ) )
        return '';

    // Get tag data
    $tag_id = automatorwp_get_log_meta( $log->id, 'tag_id', true );
    $tag_data = Mint\MRM\DataBase\Models\ContactGroupModel::get( $tag_id );

    switch( $tag_name ) {
        case 'tag_id':
            $replacement = $tag_id;
            break;
        case 'tag_title':
            $replacement = $tag_data['title'];
            break;
        case 'tag_created_date':
            $replacement = $tag_data['created_at'];
            break;
        
    }

    return $replacement;

}
add_filter( 'automatorwp_get_trigger_tag_replacement', 'automatorwp_mail_mint_get_trigger_tag_tag_replacement', 10, 6 );