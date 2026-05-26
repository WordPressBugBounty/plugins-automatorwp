<?php
/**
 * User Contact Added
 *
 * @package     AutomatorWP\Integrations\Mail_Mint\Triggers\User_Contact_Added
 * @author      AutomatorWP <contact@automatorwp.com>
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Mail_Mint_User_Contact_Added extends AutomatorWP_Integration_Trigger
{
    public $integration = 'mail_mint';
    public $trigger     = 'mail_mint_user_contact_added';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register()
    {
        automatorwp_register_trigger( $this->trigger, array(
            'integration'   => $this->integration,
            'label'         => __( 'User added to contacts', 'automatorwp' ),
            'select_option' => __( 'User added to <strong>contacts</strong>', 'automatorwp' ),
            /* translators: %1$s: Number of times. */
            'edit_label'    => sprintf( __( 'User added to contacts %1$s time(s)', 'automatorwp' ), '{times}' ),
            'log_label'     => __( 'User added to contacts', 'automatorwp' ),
            'action'        => 'mailmint_contacts_saved',
            'function'      => array( $this, 'listener' ),
            'priority'      => 10,
            'accepted_args' => 2,
            'options'       => array(
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_mail_mint_contact_tags(),
                automatorwp_utilities_times_tag()
            ),
        ) );
    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param int   $contact_id Contact ID.
	 * @param array $params     Saved data.
     */
    public function listener( $contact_id, $params ) {
    
        if( ! class_exists ( 'Mint\MRM\DataBase\Models\ContactModel' ) )
            return;

        // Get contact email
        $contact_email = Mint\MRM\DataBase\Models\ContactModel::get_email_by_id( $contact_id );

        // Bail if not contact email
        if ( ! $contact_email )
            return;
        
        $user = get_user_by( 'email', $contact_email['email'] );
        
        // Make sure contact has an user ID assigned
        if ( $user->ID === 0 ) {
            return;
        }

        // Trigger the user added to contacts
        automatorwp_trigger_event( array(
            'trigger'   => $this->trigger,
            'user_id'   => $user->ID,
            'contact_id' => $contact_id,
        ) );

    }

    /**
     * Register the required hooks
     *
     * @since 1.0.0
     */
    public function hooks() {

        // Log meta data
        add_filter( 'automatorwp_user_completed_trigger_log_meta', array( $this, 'log_meta' ), 10, 6 );

        parent::hooks();
    }

    /**
     * Trigger custom log meta
     *
     * @since 1.0.0
     *
     * @param array     $log_meta           Log meta data
     * @param stdClass  $trigger            The trigger object
     * @param int       $user_id            The user ID
     * @param array     $event              Event information
     * @param array     $trigger_options    The trigger's stored options
     * @param stdClass  $automation         The trigger's automation object
     *
     * @return array
     */
    function log_meta( $log_meta, $trigger, $user_id, $event, $trigger_options, $automation ) {

        // Bail if action type don't match this action
        if( $trigger->type !== $this->trigger ) {
            return $log_meta;
        }

        $log_meta['contact_id'] = ( isset( $event['contact_id'] ) ? $event['contact_id'] : '' );

        return $log_meta;

    }
}

new AutomatorWP_Mail_Mint_User_Contact_Added();
