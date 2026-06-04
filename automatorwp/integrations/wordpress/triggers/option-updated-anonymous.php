<?php
/**
 * Option Updated Anonymous
 *
 * @package     AutomatorWP\Integrations\WordPress\Triggers\Option_Updated_Anonymous
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WordPress_Option_Updated_Anonymous extends AutomatorWP_Integration_Trigger {

    /**
     * Initialize the trigger
     *
     * @since 1.0.0
     */
    public function __construct( $integration ) {

        $this->integration = $integration;
        $this->trigger = $integration . '_option_updated_anonymous';

        parent::__construct();

    }

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'anonymous'         => true,
            'label'             => __( 'Option gets updated with a value', 'automatorwp' ),
            'select_option'     => __( 'Option <strong>gets updated</strong> with a value', 'automatorwp' ),
            /* translators: %1$s: Post title. %2$s: Key. %3$s: Condition. %4$s: Value. %5$s: Number of times. */
            'edit_label'        => sprintf( __( '%1$s option gets updated with a value %2$s %3$s %4$s time(s)', 'automatorwp' ), '{option}', '{condition}', '{value}', '{times}' ),
            /* translators: %1$s: Post title. %2$s: Key. %3$s: Condition. %4$s: Value. */
            'log_label'         => sprintf( __( '%1$s option gets updated with a value %2$s %3$s', 'automatorwp' ), '{option}', '{condition}', '{value}' ),
            'action'            => 'update_option',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 3,
            'options'           => array(
                'condition' => automatorwp_utilities_condition_option(),
                'option' => array(
                    'from' => 'option',
                    /* translators: Refers to meta key */
                    'default' => __( 'Any', 'automatorwp' ),
                    'fields' => array(
                        'option' => array(
                            'name' => __( 'Option:', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                    )
                ),
                'value' => array(
                    'from' => 'value',
                    'default' => __( 'any value', 'automatorwp' ),
                    'fields' => array(
                        'value' => array(
                            'name' => __( 'Value:', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                    )
                ),
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                array(
                    'option_name' => array(
                        'label'     => __( 'Updated option name', 'automatorwp' ),
                        'type'      => 'text',
                        'preview'   => __( 'Name of the updated option', 'automatorwp' ),
                    ),
                    'option_new_value' => array(
                        'label'     => __( 'Option new value', 'automatorwp' ),
                        'type'      => 'text',
                        'preview'   => __( 'The option\'s new value', 'automatorwp' ),
                    ),
                    'option_old_value' => array(
                        'label'     => __( 'Option old value', 'automatorwp' ),
                        'type'      => 'text',
                        'preview'   => __( 'The option\'s old value', 'automatorwp' ),
                    ),
                ),
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @param string $option    Name of the option to update.
     * @param mixed $old_value The old option value.
     * @param mixed $value The new option value.
     * @since 1.0.0
     *
     */
    public function listener( $option, $old_value, $value ) {

        $user_id = get_current_user_id();

        // Bail if user is logged in
        if( $user_id !== 0 ) {
            return;
        }

        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'option'        => $option,
            'value'         => $value,
            'old_value'     => $old_value,

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

        // Don't deserve if event vars not received
        if( ! isset( $event['option'] ) && ! isset( $event['value'] ) ) {
            return false;
        }

        // Don't deserve if option doesn't match with the trigger option
        if( $trigger_options['option'] !== '' && $trigger_options['option'] !== $event['option'] ) {
            return false;
        }

        // Don't deserve if value doesn't match with the trigger option
		if ( $trigger_options['value'] !== '' && ! automatorwp_condition_matches( $event['value'], $trigger_options['value'], $trigger_options['condition'] ) ) {
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

        // Tags replacement
        add_filter( 'automatorwp_get_trigger_tag_replacement', array( $this, 'tags_replacement' ), 10, 6 );

        // Log meta data
        add_filter( 'automatorwp_user_completed_trigger_log_meta', array( $this, 'log_meta' ), 10, 6 );

        // Log fields
        add_filter( 'automatorwp_log_fields', array( $this, 'log_fields' ), 10, 5 );

        parent::hooks();
    }

    /**
     * Trigger custom tags replacement
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
    function tags_replacement( $replacement, $tag_name, $trigger, $user_id, $content, $log ) {

        // Bail if action type don't match this action
        if( $trigger->type !== $this->trigger ) {
            return $replacement;
        }

        switch( $tag_name ) {
            case 'option_name':
                $replacement = automatorwp_get_log_meta( $log->id, 'option_name', true );
                break;
            case 'option_new_value':
                $replacement = automatorwp_get_log_meta( $log->id, 'option_new_value', true );
                break;
            case 'option_old_value':
                $replacement = automatorwp_get_log_meta( $log->id, 'option_old_value', true );
                break;
        }

        return $replacement;

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

        $log_meta['option_name'] = ( isset( $event['option'] ) ? $event['option'] : '' );
        $log_meta['option_new_value'] = ( isset( $event['value'] ) ? $event['value'] : '' );
        $log_meta['option_old_value'] = ( isset( $event['old_value'] ) ? $event['old_value'] : '' );

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

        // Bail if log is not assigned to a trigger
        if( $log->type !== 'trigger' ) {
            return $log_fields;
        }

        // Bail if trigger type don't match this trigger
        if( $object->type !== $this->trigger ) {
            return $log_fields;
        }

        $log_fields['option_name'] = array(
            'name' => __( 'Option', 'automatorwp' ),
            'desc' => __( 'Name of the updated option.', 'automatorwp' ),
            'type' => 'text',
        );

        $log_fields['option_new_value'] = array(
            'name' => __( 'Option new value', 'automatorwp' ),
            'desc' => __( 'The option\'s new value.', 'automatorwp' ),
            'type' => 'text',
        );

        $log_fields['option_old_value'] = array(
            'name' => __( 'Option old value', 'automatorwp' ),
            'desc' => __( 'The option\'s old value.', 'automatorwp' ),
            'type' => 'text',
        );

        return $log_fields;

    }

}

new AutomatorWP_WordPress_Option_Updated_Anonymous( 'wordpress' );