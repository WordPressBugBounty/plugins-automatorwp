<?php
/**
 * Update Option
 *
 * @package     AutomatorWP\Integrations\WordPress\Actions\Update_Option
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WordPress_Update_Option extends AutomatorWP_Integration_Action {

    /**
     * Initialize the trigger
     *
     * @since 1.0.0
     */
    public function __construct( $integration ) {

        $this->integration = $integration;
        $this->action = $integration . '_update_option';

        parent::__construct();

    }

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Update option', 'automatorwp' ),
            'select_option'     => __( 'Update <strong>option</strong>', 'automatorwp' ),
            /* translators: %1$s: Operation (Set, insert, increment or decrement). %2$s: Value. %3$s: Option. */
            'edit_label'        => sprintf( __( '%1$s %2$s to %3$s', 'automatorwp' ), '{operation}', '{value}', '{option}' ),
            /* translators: %1$s: Operation (Set, insert, increment or decrement). %2$s: Value. %3$s: Option. */
            'log_label'         => sprintf( __( '%1$s %2$s to %3$s', 'automatorwp' ), '{operation}', '{value}', '{option}' ),
            'options'           => array(
                'operation' => array(
                    'from' => 'operation',
                    'fields' => array(
                        'operation' => array(
                            'name' => __( 'Operation:', 'automatorwp' ),
                            'desc' => __( 'Operation defines how the value will be applied. The available options are:', 'automatorwp' )
                                . '<br><br>' . __( '<strong>Set:</strong> The new value will override the current value.', 'automatorwp' )
                                . '<br>' . __( 'Example: If old value is "Word" and new value is "Press", the final value will be "Press".', 'automatorwp' )
                                . '<br><br>' . __( '<strong>Insert:</strong> The new value will be inserted to the current value (for arrays, the new value will be inserted at the end, for other types, the new value will be appended).', 'automatorwp' )
                                . '<br>' . __( 'Example for arrays: If old value is array( "Word" ) and new value is "Press", the final value will be array( "Word", "Press" ).', 'automatorwp' )
                                . '<br>' . __( 'Example for other types: If old value is "Word" and new value is "Press", the final value will be "WordPress".', 'automatorwp' )
                                . '<br><br>' . __( '<strong>Increment:</strong> For numeric values, the current value will be incremented the same amount as the new value.', 'automatorwp' )
                                . '<br>' . __( 'Example: If old value is "5" and new value is "1", the final value will be "6".', 'automatorwp' )
                                . '<br><br>' . __( '<strong>Decrement:</strong> For numeric values, the current value will be decremented the same amount as the new value.', 'automatorwp' )
                                . '<br>' . __( 'Example: If old value is "5" and new value is "1", the final value will be "4".', 'automatorwp' ),
                            'type' => 'select',
                            'options' => array(
                                'set'       => __( 'Set', 'automatorwp' ),
                                'insert'    => __( 'Insert', 'automatorwp' ),
                                'increment' => __( 'Increment', 'automatorwp' ),
                                'decrement' => __( 'Decrement', 'automatorwp' ),
                            ),
                            'default' => 'set'
                        ),
                    )
                ),
                'value' => array(
                    'from' => 'value',
                    'default' => __( 'value', 'automatorwp' ),
                    'fields' => array(
                        'value' => array(
                            'name' => __( 'Value:', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                    )
                ),
                'option' => array(
                    'from' => 'option',
                    /* translators: Refers to meta key */
                    'default' => __( 'option', 'automatorwp' ),
                    'fields' => array(
                        'option' => array(
                            'name' => __( 'Option:', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'autoload' => array(
                            'name' => __( 'Autoload:', 'automatorwp' ),
                            'type' => 'select',
                            'options' => array(
                                '' => __( 'Yes', 'automatorwp' ),
                                'yes' => __( 'Yes', 'automatorwp' ),
                                'no' => __( 'No', 'automatorwp' ),
                            ),
                            'default' => ''
                        ),
                    )
                )
            ),
            'tags' => array(

            )
        ) );

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
        $operation = $action_options['operation'];
        $option = sanitize_key( $action_options['option'] );
        $new_value = sanitize_text_field( $action_options['value'] );
        $autoload = $action_options['autoload'];

        // Bail if empty option name
        if( empty( $option ) ) {
            $this->result = __( 'Action not processed. Option name is empty.', 'automatorwp' );
            return;
        }

        // Bail if is a protected option
        if( automatorwp_is_protected_option( $option ) ) {
            $this->result = sprintf( __( 'Action not processed."%s" is a protected option. You can filter "automatorwp_protected_options" to modify this.', 'automatorwp' ), $option );
            return;
        }

        // Get the current meta value
        $value = get_option( $option, '' );

        // For increment and decrement, is required to turn values into a numeric value
        if( in_array( $operation, array( 'increment', 'decrement' ) ) ) {

            if( strpos( $new_value, '.' ) !== false ) {
                // Treat values as float
                $new_value = (float) $new_value;
                $value = (float) $value;
            } else {
                // Treat values as int
                $new_value = (int) $new_value;
                $value = (int) $value;
            }

        }

        switch ( $operation ) {
            case 'set':
                // Override old meta value
                $value = $new_value;
                break;
            case 'insert':
                if( is_array( $value ) ) {
                    // If value is an array, append the new value
                    $value[] = $new_value;
                } else {
                    // If not, concat the new value
                    $value .= $new_value;
                }
                break;
            case 'increment':
                // Increase meta value
                $value += $new_value;
                break;
            case 'decrement':
                // Decrease meta value
                $value -= $new_value;
                break;
        }

        switch ( $autoload ) {
            case 'yes': $autoload = true;
            break;
            case 'no': $autoload = false;
            break;
            default: $autoload = null;
            break;
        }

        // Update the user meta value
        update_option( $option, $value, $autoload );

        $this->result = sprintf( __( 'Option "%s" updated with value "%s".', 'automatorwp' ), $option, $value );

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

        $option = sanitize_key( $action_options['option'] );
        $new_value = sanitize_text_field( $action_options['value'] );

        $log_meta['option'] = $option;
        $log_meta['value'] = $new_value;
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
        if( $log->type !== 'action' || $object->type !== $this->action )
            return $log_fields;

        $log_fields['result'] = array(
            'name' => __( 'Result:', 'automatorwp' ),
            'type' => 'text',
        );

        return $log_fields;
    }

}

new AutomatorWP_WordPress_Update_Option( 'wordpress' );