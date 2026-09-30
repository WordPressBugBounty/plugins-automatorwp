<?php
/**
 * Tag Added
 *
 * @package     AutomatorWP\Integrations\MailerPress\Triggers\Tag_Added
 * @author      AutomatorWP <contact@automatorwp.com>
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class AutomatorWP_MailerPress_Tag_Added extends AutomatorWP_Integration_Trigger
{
    public $integration = 'mailerpress';
    public $trigger     = 'mailerpress_tag_added';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'A tag is added to user', 'automatorwp' ),
            'select_option'     => __( 'A <strong>tag</strong> is added to user', 'automatorwp' ),
            /* translators: %1$s: Tag. %2$s: Number of times. */
            'edit_label'        => sprintf( __( '%1$s is added to user %2$s time(s)', 'automatorwp' ), '{tag}', '{times}' ),
            /* translators: %1$s: Tag. */
            'log_label'         => sprintf( __( '%1$s is added to user', 'automatorwp' ), '{tag}' ),
            'action'            => 'mailerpress_contact_tag_added',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 2,
            'options'           => array(
                'tag' => automatorwp_utilities_ajax_selector_option( array(
                    'field'             => 'tag',
                    'name'              => __( 'Tag:', 'automatorwp' ),
                    'option_none_value' => 'any',
                    'option_none_label' => __( 'any tag', 'automatorwp' ),
                    'action_cb'         => 'automatorwp_mailerpress_get_tags',
                    'options_cb'        => 'automatorwp_mailerpress_options_cb_tag',
                    
                ) ),
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_mailerpress_contact_tags(),
                automatorwp_mailerpress_tag_tags(),
                automatorwp_utilities_times_tag()
            ),
        ) );

    }

    /**
     * Trigger listener 
     *
     * @since 1.0.0
     *
     * @param int $contact_id Contact ID.
	 * @param int $tag_id Tag ID.
     */
    public function listener( $contact_id, $tag_id ) {

        if( ! class_exists ( 'MailerPress\Models\Contacts' ) )
            return;

        // Get contact data
        $contact_data = new MailerPress\Models\Contacts;
        $contact = $contact_data->get( $contact_id );

        // Bail if not contact email
        if ( ! $contact )
            return;
        
        $user = get_user_by( 'email', $contact->email );
        
        // Make sure contact has an user ID assigned
        if ( empty( $user->ID ) ) {
            return;
        }

        // Trigger the user added to list
        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'user_id'       => $user->ID,
            'tag_id'        => $tag_id,
            'contact_id'    => $contact_id,
        ) );

    }

    /**
     * User deserves check
     *
     * @since 1.0.0
     *
     * @param bool      $deserves_trigger   True if user deserves trigger, false otherwise
     * @param stdClass  $trigger            The trigger object
     * @param int       $user_id            The user ID
     * @param array     $event              Event information
     * @param array     $trigger_options    The trigger's stored options
     * @param stdClass  $automation         The trigger's automation object
     *
     * @return bool                          True if user deserves trigger, false otherwise
     */
    public function user_deserves_trigger( $deserves_trigger, $trigger, $user_id, $event, $trigger_options, $automation ) {

        // Don't deserve if list is not received
        if( ! isset( $event['tag_id'] ) ) {
            return false;
        }

        // Don't deserve if list doesn't match with the trigger option
        if( $trigger_options['tag'] !== 'any' && absint( $trigger_options['tag'] ) !== absint( $event['tag_id'] ) ) {
            return false;
        }

        return $deserves_trigger;

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
        $log_meta['tag_id'] = ( isset( $event['tag_id'] ) ? $event['tag_id'] : '' );

        return $log_meta;

    }
}

new AutomatorWP_MailerPress_Tag_Added();
