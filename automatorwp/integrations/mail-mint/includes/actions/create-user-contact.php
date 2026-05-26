<?php
/**
 * Create User Contact
 *
 * @package     AutomatorWP\Integrations\Mail_Mint\Actions\Create_User_Contact
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly

if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Mail_Mint_Create_User_Contact extends AutomatorWP_Integration_Action {

    public $integration = 'mail_mint';
    public $action = 'mail_mint_create_user_contact';
    public $result = '';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Add user to Mail Mint', 'automatorwp' ),
            'select_option'     => __( 'Add <strong>user</strong> to Mail Mint', 'automatorwp' ),
            /* translators: %1$s: Contact. */
            'edit_label'        => sprintf( __( 'Add %1$s to Mail Mint', 'automatorwp' ), '{contact}' ),
            /* translators: %1$s: Contact. */
            'log_label'         => sprintf( __( 'Add %1$s to Mail Mint', 'automatorwp' ), '{contact}' ),
            'options'           => array(
                'contact' => array(
                    'default' => __( 'user', 'automatorwp' ),
                    'fields' => array(
                        'email' => array(
                            'name' => __( 'User Email:', 'automatorwp' ),
                            'desc' => __( 'The email of the user to add to Mail Mint. By default, Email of user who completes this automation.', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'subscribed' => array(
                            'name' => __( 'Subscribed:', 'automatorwp' ),
                            'desc' => __( 'Mark Contact as subscribed.', 'automatorwp' ),
                            'type' => 'checkbox',
                            'classes'   => 'cmb2-switch',
                        ),
                     ) )
            ),
        ) );

    }

    /**
     * Action execution function
     *
     * @since 1.0.0
     *
     * @param stdClass  $action             The action object
     * @param int       $user_id            The user ID
     * @param array     $action_options     The action's stored options (with tags already passed)
     * @param stdClass  $automation         The action's automation object
     */
    public function execute( $action, $user_id, $action_options, $automation ) {

        // Shorthand
        $status = (bool) $action_options['subscribed'];
        $user_email = $action_options['email'];

        if ( empty( $user_email ) ) {
            $user = get_user_by( 'id', $user_id );
        } else {
            $user = get_user_by( 'email', $user_email );

            if ( empty( $user ) ) {
                $this->result = __( 'User is not registered', 'automatorwp' );
                return;
            }
        }
       
        if( ! class_exists ( 'Mint\MRM\DataBase\Models\ContactModel' ) ||
            ! class_exists ( 'Mint\MRM\DataStores\ContactData' ) )
            return;

        // Get contact ID
        $contact_id = Mint\MRM\DataBase\Models\ContactModel::get_id_by_email( $user->user_email );

        // Bail if contact ID
        if ( $contact_id ){
            $this->result = __( 'User is already in Mail Mint.', 'automatorwp' );
            return;
        }

        // Status
        $status = $status ? 'subscribed' : 'pending';

        $args = array(
            'email'         => $user->user_email,
            'first_name'    => $user->first_name,
            'last_name'     => $user->last_name,
            'status'        => $status,
        );

        // Create contact object
        $contact_data = new Mint\MRM\DataStores\ContactData( $user->user_email, $args );

		$response =  Mint\MRM\DataBase\Models\ContactModel::insert( $contact_data );
		
        if ( $response ){
            $this->result = __( 'User added to Mail Mint', 'automatorwp' );
        } else {
            $this->result = __( 'Could not add user to Mail Mint', 'automatorwp' );
        }
        
    }

    /**
     * Register required hooks
     *
     * @since 1.0.0
     */
    public function hooks() {

        // Log meta data
        add_filter( 'automatorwp_user_completed_action_log_meta', array( $this, 'log_meta' ), 10, 5 );

        // Log fields
        add_filter( 'automatorwp_log_fields', array( $this, 'log_fields' ), 10, 5 );

        parent::hooks();

    }

    /**
     * Action custom log meta
     *
     * @since 1.0.0
     *
     * @param array     $log_meta           Log meta data
     * @param stdClass  $action             The action object
     * @param int       $user_id            The user ID
     * @param array     $action_options     The action's stored options (with tags already passed)
     * @param stdClass  $automation         The action's automation object
     *
     * @return array
     */
    public function log_meta( $log_meta, $action, $user_id, $action_options, $automation ) {

        // Bail if action type don't match this action
        if( $action->type !== $this->action ) {
            return $log_meta;
        }

        // Store the action's result
        $log_meta['result'] = $this->result;

        return $log_meta;
    }

    /**
     * Action custom log fields
     *
     * @since 1.0.0
     *
     * @param array     $log_fields The log fields
     * @param stdClass  $log        The log object
     * @param stdClass  $object     The trigger/action/automation object attached to the log
     *
     * @return array
     */
    public function log_fields( $log_fields, $log, $object ) {

        // Bail if log is not assigned to an action
        if( $log->type !== 'action' ) {
            return $log_fields;
        }

        // Bail if action type don't match this action
        if( $object->type !== $this->action ) {
            return $log_fields;
        }

        $log_fields['result'] = array(
            'name' => __( 'Result:', 'automatorwp' ),
            'type' => 'text',
        );

        return $log_fields;
    }

}

new AutomatorWP_Mail_Mint_Create_User_Contact();