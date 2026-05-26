<?php
/**
 * User Registers as Affiliate Trigger
 *
 * @package     AutomatorWP\Integrations\AffiliatePress\Triggers\User_Registers_Affiliate
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class AutomatorWP_AffiliatePress_User_Registers_Affiliate extends AutomatorWP_Integration_Trigger {

    public $integration = 'affiliatepress';
    public $trigger     = 'affiliatepress_user_registers_affiliate';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     * @return void
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User becomes an affiliate', 'automatorwp' ),
            'select_option'     => __( 'User <strong>becomes</strong> an affiliate', 'automatorwp' ),
            'edit_label'        => __( 'User becomes an affiliate', 'automatorwp' ),
            'log_label'         => __( 'User becomes an affiliate', 'automatorwp' ),
            'action'            => 'affiliatepress_after_signup_affiliate',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 1,
            'options'           => array(),
            'tags'              => array()
        ));
    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     * 
     * @param int $affiliate_id 
     */
    public function listener( $affiliate_id ) {

        $user_id = automatorwp_affiliatepress_get_user_id( $affiliate_id );

        // Bail if no user id
        if ( empty( $user_id ) ) {
            return; 
        }

        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'user_id'       => $user_id,
            'affiliate_id'  => $affiliate_id,
        ) );
    }
}

new AutomatorWP_AffiliatePress_User_Registers_Affiliate();