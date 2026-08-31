<?php
/**
 * Help
 *
 * @package     AutomatorWP\AI_Assistant\Help
 * @since       1.0.0
 * @version     1.0.0
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Render AI assistant help
 *
 * @since	    1.0.0
 */
function automatorwp_ai_assistant_render_help() {

    $user = wp_get_current_user();
    $user_first = $user->user_login;
    $user_email = $user->user_email;
    $user_id = $user->ID;

    $abilities = array(
        'create-automations' => array(
            'label' => __( 'Create New Automations', 'automatorwp' ),
            'icon' => 'heart',
            'desc' => __( 'I\'m able to create new automations with multiples triggers and actions in the same prompt.', 'automatorwp' ),
            'prompts' => automatorwp_ai_assistant_get_random_prompts( 'create-automations' ),
        ),
        'run-actions' => array(
            'label' => __( 'Run Actions', 'automatorwp' ),
            'icon' => 'controls-play',
            'desc' => __( 'I\'m able to execute any action available in AutomatorWP.', 'automatorwp' )
                . ' ' . __( 'For points types, I can also define points awards and deductions in the same prompt.', 'automatorwp' ) ,
            'prompts' => automatorwp_ai_assistant_get_random_prompts( 'run-actions' ),
        ),
        'create-triggers-actions' => array(
            'label' => __( 'Set Up Triggers & Actions', 'automatorwp' ),
            'icon' => 'admin-tools',
            'desc' => __( 'I can configure new triggers an actions for existent automations.', 'automatorwp' ),
            'prompts' => automatorwp_ai_assistant_get_random_prompts( 'create-triggers-actions' ),
        ),
    );

    $first_ability = array_keys( $abilities )[0];

    ?>

    <div class="automatorwp-ai-assistant-help-presentation">
        <?php _e( 'I\'m an <strong>AI assistant</strong> specially designed to help you manage <strong>AutomatorWP</strong> using natural language.', 'automatorwp' ); ?>
        <br>
        <br>
        <?php esc_html_e( 'Here\'s what I can do:', 'automatorwp' ); ?>
    </div>

    <?php foreach( $abilities as $ability => $data ) : ?>
        <div class="automatorwp-ai-assistant-help-ability automatorwp-ai-assistant-help-ability-<?php echo esc_attr( $ability ); ?> <?php echo ( $ability === $first_ability ? 'automatorwp-ai-assistant-help-ability-open' : 'automatorwp-ai-assistant-help-ability-close' ); ?>">
            <div class="automatorwp-ai-assistant-help-ability-label"><?php
                echo automatorwp_dashicon( $data['icon'] )
                    . ' ' .  esc_html( $data['label'] )
                    . ' ' .  automatorwp_dashicon( 'arrow-up-alt2' );
                ?></div>
            <div class="automatorwp-ai-assistant-help-ability-desc" style="<?php echo ( $ability === $first_ability ? '' : 'display: none;' ); ?>">
                <div class="automatorwp-ai-assistant-help-ability-desc-text"><?php echo esc_html( $data['desc'] ); ?></div>
                <strong><small><?php esc_html_e( 'Examples', 'automatorwp' ); ?></small></strong>
                <div class="automatorwp-ai-assistant-help-ability-prompts">
                    <?php foreach( $data['prompts'] as $prompt ) : ?>
                        <span class="automatorwp-ai-assistant-prompt"><?php echo esc_html( $prompt ); ?></span>
                        <div class="automatorwp-ai-assistant-send-prompt cmb-tooltip" data-prompt="<?php echo esc_attr( $prompt ); ?>">
                            <?php echo automatorwp_dashicon( 'edit' ); ?>
                            <div class="cmb-tooltip-desc cmb-tooltip-top"><?php echo esc_html( __( 'Edit Prompt', 'automatorwp' ) ); ?></div>
                        </div>
                        <div class="automatorwp-ai-assistant-send-prompt cmb-tooltip" data-prompt="<?php echo esc_attr( $prompt ); ?>" data-send="true">
                            <?php echo automatorwp_dashicon( 'share-alt2' ); ?>
                            <div class="cmb-tooltip-desc cmb-tooltip-top"><?php echo esc_html( __( 'Send to Assistant', 'automatorwp' ) ); ?></div>
                        </div>
                        <br>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <?php
}

/**
 * Get a random prompts
 *
 * @since   1.0.0
 *
 * @return array
 */
function automatorwp_ai_assistant_get_random_prompts( $section, $desired = 3 ) {

    $pompts = array();

    while ( count( $pompts ) < $desired ) {
        $new = automatorwp_ai_assistant_get_random_prompt( $section );

        if( ! in_array( $new, $pompts ) )
            $pompts[] = $new;
    }

    return $pompts;
}

/**
 * Get a random prompt
 *
 * @since   1.0.0
 *
 * @return string
 */
function automatorwp_ai_assistant_get_random_prompt( $section ) {

    $options = array();

    switch ( $section ) {
        case 'create-automations':
            $options = automatorwp_ai_assistant_get_create_automations_prompts();
            break;
        case 'create-triggers-actions':
            $options = automatorwp_ai_assistant_get_create_triggers_actions_prompts();
            break;
        case 'run-actions':
            $options = automatorwp_ai_assistant_get_run_actions_prompts();
            break;
    }

    $k = array_rand( $options );

    return $options[$k];

}

function automatorwp_ai_assistant_get_create_automations_prompts() {
    $plugin = automatorwp_ai_assistant_get_random_plugin();

    switch ( $plugin ) {
        case 'GamiPress':
        case 'LearnDash':
        case 'LearnPress':
        case 'LifterLMS':
        case 'Tutor LMS':
        case 'H5P':
            $options = array(
                __( 'New automation: When user passes a quiz, add the tag "Student" in ActiveCampaign', 'automatorwp' ),
                __( 'Build an automation when a user views a course, send an email to the author ', 'automatorwp' ),
                __( 'When user completes a course, award them a GamiPress achievement and send them a congratulations email', 'automatorwp' ),
            );
            break;
        case 'Easy Digital Downloads':
        case 'SureCart':
        case 'WooCommerce':
            $options = array(
                __( 'New automation: When a user completes a purchase, subscribe them to my Mailchimp audience', 'automatorwp' ),
                __( 'Build an automation when a user purchases "Sample Product", add the tag "Customer" in ActiveCampaign', 'automatorwp' ),
                __( 'Build an automation when a user purchases "Course Product", enroll them in "Sample Course" course', 'automatorwp' ),
                __( 'When a customer buys a product, award them 100 GamiPress points and add them to a coupon', 'automatorwp' ),
            );
            break;
        case 'BuddyBoss':
        case 'BuddyPress':
            $options = array(
                __( 'New automation: When user completes a course, award them a GamiPress achievement and send them a congratulations email', 'automatorwp' ),
                __( 'Build an automation when a user views a post, send an email to the author ', 'automatorwp' ),
                __( 'When user completes a course, award them a GamiPress achievement and send them a congratulations email', 'automatorwp' ),
            );
            break;
        case 'WordPress':
        default:
            $options = array(
                __( 'New automation: When user registers on the site, send them a welcome email', 'automatorwp' ),
                __( 'When a post is published in the "News" category, create a post on Bluesky with the post title and URL', 'automatorwp' ),
                __( 'Build an automation when a user comments on a post, award them 10 points in GamiPress', 'automatorwp' ),
            );
            break;
    }

    return $options;
}

function automatorwp_ai_assistant_get_create_triggers_actions_prompts() {

    $plugin = automatorwp_ai_assistant_get_random_plugin();

    $automation_names = array(
        __( 'Sample Automation', 'automatorwp' ),
        __( 'My Automation', 'automatorwp' ),
        __( 'Test Automation', 'automatorwp' ),
        __( 'Demo Automation', 'automatorwp' ),
    );

    $automation_name = $automation_names[array_rand( $automation_names )];

    switch ( $plugin ) {
        case 'GamiPress':
        case 'LearnDash':
        case 'LearnPress':
        case 'LifterLMS':
        case 'Tutor LMS':
        case 'H5P':
            $options = array(
                sprintf( __( 'Add an action to award 10 points in GamiPress to "%s"', 'automatorwp' ), $automation_name ),
                sprintf( __( 'Add an action to enroll user in "Sample" course to "%s"', 'automatorwp' ), $automation_name ),
                sprintf( __( 'Add a trigger for unlock "Sample" badge in GamiPress to "%s"', 'automatorwp' ), $automation_name ),
                sprintf( __( 'Add a trigger for complete "Sample" course to "%s"', 'automatorwp' ), $automation_name ),
            );
            break;
        case 'Easy Digital Downloads':
        case 'SureCart':
        case 'WooCommerce':
            $options = array(
                sprintf( __( 'Add an action to add the user to "BIGSALE" coupon to "%s"', 'automatorwp' ), $automation_name ),
                sprintf( __( 'Add a trigger for purchase a product in "Offers" category to "%s"', 'automatorwp' ), $automation_name ),
                sprintf( __( 'Add a trigger for viewing "T-Shirt" product to "%s"', 'automatorwp' ), $automation_name ),
            );
            break;
        case 'BuddyBoss':
        case 'BuddyPress':
            $options = array(
                sprintf( __( 'Add a trigger for joining "Sample" group to "%s"', 'automatorwp' ), $automation_name ),
                sprintf( __( 'Add a trigger for completing the user profile to "%s"', 'automatorwp' ), $automation_name ),
                sprintf( __( 'Add an action to add the user to "Sample" group to "%s"', 'automatorwp' ), $automation_name ),
            );
            break;
        case 'WordPress':
        default:
            $options = array(
                sprintf( __( 'Add an action to send a welcome email to "%s"', 'automatorwp' ), $automation_name ),
                sprintf( __( 'Add a trigger for publishing a post in "News" category to "%s"', 'automatorwp' ), $automation_name ),
                // External
                sprintf( __( 'Add an action to tag the user in ActiveCampaign to "%s"', 'automatorwp' ), $automation_name ),
                sprintf( __( 'Add an action to subscribe the user in Mailchimp to "%s"', 'automatorwp' ), $automation_name ),
                sprintf( __( 'Add an action to add a post in Bluesky to "%s"', 'automatorwp' ), $automation_name ),
            );
            break;
    }

    return $options;

}

function automatorwp_ai_assistant_get_run_actions_prompts() {

    $plugin = automatorwp_ai_assistant_get_random_plugin();

    $user = wp_get_current_user();
    $user_first = $user->user_login;
    $user_email = $user->user_email;
    $user_id = $user->ID;

    switch ( $plugin ) {
        case 'GamiPress':
        case 'LearnDash':
        case 'LearnPress':
        case 'LifterLMS':
        case 'Tutor LMS':
        case 'H5P':
            $options = array(
                sprintf( __( 'Award 10 points to %s', 'automatorwp' ), $user_first ),
                sprintf( __( 'Award the badge "Sample Badge" to %s', 'automatorwp' ), $user_first ),
                sprintf( __( 'Award the rank "Sample Rank" to %s', 'automatorwp' ), $user_first ),
                sprintf( __( 'Enroll %s in course "Sample Course"', 'automatorwp' ), $user_first ),
            );
            break;
        case 'Easy Digital Downloads':
        case 'SureCart':
        case 'WooCommerce':
            $options = array(
                sprintf( __( 'Add %1$s to coupon "%2$s"', 'automatorwp' ), $user_first, 'BFCM' . date('Y') ),
            );
            break;
        case 'BuddyBoss':
        case 'BuddyPress':
            $options = array(
                sprintf( __( 'Add %s to group "Sample Group"', 'automatorwp' ), $user_first ),
                sprintf( __( 'Set the profile type "Member" to %s', 'automatorwp' ), $user_first ),
            );
            break;
        case 'WordPress':
        default:
            $options = array(
                sprintf( __( 'Set the role "Contributor" to %s', 'automatorwp' ), $user_first ),
                sprintf( __( 'Send a congratulations email to %s', 'automatorwp' ), $user_first ),
                sprintf( __( 'Update %s user meta "approved" to "yes"', 'automatorwp' ), $user_first ),
                sprintf( __( 'Create a privacy policy page and a link for that page in ShortLinks Pro', 'automatorwp' ), $user_first ),
                sprintf( __( 'Create an about us page and a link for that page in ShortLinks Pro', 'automatorwp' ), $user_first ),
                sprintf( __( 'Delete the page "Sample Page"', 'automatorwp' ), $user_first ),
                sprintf( __( 'Delete the user "Test"', 'automatorwp' ), $user_first ),
                sprintf( __( 'Delete the user "Test"', 'automatorwp' ), $user_first ),
                // External
                sprintf( __( 'Add the tag "New" to %s in ActiveCampaign', 'automatorwp' ), $user_first ),
                sprintf( __( 'Add %s to Mailchimp', 'automatorwp' ), $user_first ),
                sprintf( __( 'Publish a post in Bluesky about this site', 'automatorwp' ), $user_first ),
            );
            break;
    }

    return $options;

}

/**
 * Get a random plugin name
 *
 * @since   1.0.0
 *
 * @return string
 */
function automatorwp_ai_assistant_get_random_plugin() {
    return automatorwp_assoc_array_rand( automatorwp_ai_assistant_get_available_plugins(), 'WordPress' );
}

/**
 * Get a list of random plugins names
 *
 * @since   1.0.0
 *
 * @return string
 */
function automatorwp_ai_assistant_get_random_plugins( $desired = 3 ) {
    $available = automatorwp_ai_assistant_get_available_plugins();

    if( $desired === 1 )
        return automatorwp_ai_assistant_get_random_plugin();


    if( count( $available ) < $desired )
        return automatorwp_join_words( $available );

    $results = array();

    for( $i = 0; $i < $desired; $i++ ) {
        $result = automatorwp_assoc_array_rand( $available, 'WordPress' );

        $k = array_search( $result, $available );
        unset( $available[$k] );

        $results[] = $result;
    }

    return automatorwp_join_words( array_unique( $results ) );
}

/**
 * Get available plugins names
 *
 * @since   1.0.0
 *
 * @return array
 */
function automatorwp_ai_assistant_get_available_plugins() {

    $options = array();

    if( class_exists( 'GamiPress' ) ) $options[] = 'GamiPress';
//    if( class_exists( 'AutomatorWP' ) ) $options[] = 'AutomatorWP';
    if( class_exists( 'ShortLinksPro' ) ) $options[] = 'ShortLinks Pro';
    if( class_exists( 'BBForms' ) ) $options[] = 'BBForms';

    if( defined( 'BP_PLATFORM_VERSION' ) ) $options[] = 'BuddyBoss';
    if( class_exists( 'BuddyPress' ) && ! defined( 'BP_PLATFORM_VERSION' ) ) $options[] = 'BuddyPress';

    if( class_exists( 'SFWD_LMS' ) ) $options[] = 'LearnDash';
    if( class_exists( 'LearnPress' ) ) $options[] = 'LearnPress';
    if( class_exists( 'LifterLMS' ) ) $options[] = 'LifterLMS';
    if( function_exists( 'tutor' ) ) $options[] = 'Tutor LMS';
    if( class_exists( 'H5P_Plugin' ) ) $options[] = 'H5P';

    if( class_exists( 'Easy_Digital_Downloads' ) ) $options[] = 'Easy Digital Downloads';
    if( class_exists( 'SureCart' ) ) $options[] = 'SureCart';
    if( class_exists( 'WooCommerce' ) ) $options[] = 'WooCommerce';

    $options[] = 'WordPress';

    return $options;
}