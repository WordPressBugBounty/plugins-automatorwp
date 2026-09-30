<?php
/**
 * User Add Tag
 *
 * @package     AutomatorWP\Integrations\MailerPress\Actions\User_Add_Tag
 * @author      AutomatorWP <contact@automatorwp.com>
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class AutomatorWP_MailerPress_User_Add_Tag extends AutomatorWP_Integration_Action
{
    public $integration = 'mailerpress';
    public $action      = 'mailerpress_user_add_tag';
    public $result      = '';

    /**
     * Register the action
     *
     * @since 1.0.0
     */
    public function register() {
        automatorwp_register_action( $this->action, array(
            'integration'   => $this->integration,
            'label'         => __( 'Add tag to user', 'automatorwp' ),
            'select_option' => __( 'Add <strong>tag</strong> to user', 'automatorwp' ),
            /* translators: %1$s: Tag title. */
            'edit_label'    => sprintf( __( 'Add %1$s to user', 'automatorwp' ), '{tag}' ),
            /* translators: %1$s: Tag title. */
            'log_label'     => sprintf( __( 'Add %1$s to user', 'automatorwp' ), '{tag}' ),
            'options'           => array(
                'tag' => automatorwp_utilities_ajax_selector_option( array(
                    'field'             => 'tag',
                    'option_default'    => __( 'Tag', 'automatorwp' ),
                    'name'              => __( 'Tag:', 'automatorwp' ),
                    'option_none'       => false,
                    'action_cb'         => 'automatorwp_mailerpress_get_tags',
                    'options_cb'        => 'automatorwp_mailerpress_options_cb_tag',
                    'placeholder'       => __( 'Select a tag', 'automatorwp' ),
                    'default'           => ''
                ) ),
            ),
        ) );
    }

    /**
     * Action execution function
     *
     * @since 1.0.0
     *
     * @param stdClass $action         The action object
     * @param int      $user_id        The user ID
     * @param array    $action_options The action's stored options (with tags already parsed)
     * @param stdClass $automation     The action's automation object
     */
    public function execute( $action, $user_id, $action_options, $automation ) {

        $user  = get_user_by( 'ID', $user_id );
        $user_email = $user->user_email;

        $tag_id = $action_options['tag']; 

        // Bail if no tag selected
        if ( empty( $tag_id ) ) {
            $this->result = __( 'Select a tag to add the user.', 'automatorwp' );
            return;
        }

        if( ! class_exists ( 'MailerPress\Models\Contacts' ) )
            return;

        // Get contact data
        $contact_data = new MailerPress\Models\Contacts;
        $contact = $contact_data->getContactByEmail( $user_email );

        // Bail if not contact ID
        if ( ! $contact ){
            $this->result = __( 'User is not a contact in MailerPress.', 'automatorwp' );
            return;
        }      

        $tag_added = automatorwp_mailerpress_add_tag_contact( $tag_id, $contact->contact_id );

        if ( $tag_added ){
            $this->result = sprintf( __( 'Tag added to %1$s.', 'automatorwp' ), $user_email );    
        } else {
            $this->result = sprintf( __( 'Tag could not be added to %1$s.', 'automatorwp' ), $user_email );
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

new AutomatorWP_MailerPress_User_Add_Tag();
