<?php
/**
 * Settings page.
 *
 * @package wp-video-capture
 */

if ( ! class_exists( 'WP_Video_Capture_Settings' ) ) {

    /**
     * WP_Video_Capture_Settings class.
     */
    class WP_Video_Capture_Settings {

        /**
         * Constructor.
         *
         * @param bool $pro_account active pro version.
         */
        public function __construct( $pro_account ) {
            $this->pro_account = $pro_account;

            // Initialize Mailer.
            $site_url = wp_parse_url( site_url() );
            $this->hostname = $site_url['host'];
            require_once plugin_dir_path( __FILE__ ) . 'includes/class.video-capture-email.php';
            $this->video_capture_email = new Video_Capture_Email( $this->hostname );

            if ( $pro_account ) {
                add_action( 'admin_init', array( &$this, 'admin_init_pro' ) );
            }

            // Register actions.
            add_action( 'admin_init', array( &$this, 'hide_email_notification_notice' ) );
            add_action( 'admin_init', array( &$this, 'hide_pro_activation_notice' ) );
            add_action( 'admin_init', array( &$this, 'admin_init' ) );
            add_action( 'admin_menu', array( &$this, 'add_menu' ) );
        }

        /**
         * Validate email.
         *
         * @param string $email email.
         * @return string $email, if correct $email param.
         */
        public function validate_email( $email ) {
            if ( ! is_email( $email ) && '' !== $email ) {
                add_settings_error( 'vidrack_notifications_email', 'video-capture-invalid-email', __( 'Please enter a correct email','video-capture' ) );
            } else {
                // Register user.
                $this->video_capture_email->register_user( $email );
                return $email;
            }
            return false;
        }

        /**
         * Send email upon registration.
         */
        public function notifications_email_notice() {
            printf(
                '<div><div class="update-nag"><p>%1$s <input type="button" class="button" value="%3$s" onclick="document.location.href=\'%2$s\';" /></div></div>',
                __( 'Please enter your email to get notifications about newly uploaded videos', 'video-capture' ),
                esc_url( add_query_arg( 'wp-video-capture-nag', wp_create_nonce( 'wp-video-capture-nag' ) ) ),
                'Dismiss'
            );
        }

        /**
         * Hide email notification upon click.
         */
        public function hide_email_notification_notice() {
            if ( ! isset( $_GET['wp-video-capture-nag'] ) ) {  // Input var "wp-video-capture-nag" is not set.
                return;
            }

            // Check nonce.
            check_admin_referer( 'wp-video-capture-nag', 'wp-video-capture-nag' );

            // Update user meta to indicate dismissed notice.
            update_user_meta( get_current_user_id(), '_wp-video-capture_hide_email_notification_notice', true );
        }

        /**
         * Update Pro version notification.
         */
        public function pro_activation_notice() {
            printf(
                '<div><div class="update-nag"><p>%1$s  <input type="button" class="button" value="%3$s" onclick="document.location.href=\'%2$s\';" /></div></div>',
                __( 'Upgrade to', 'video-capture' ).' <a href="vidrack.com/product/pro-version/">Vidrack Pro</a>    ',
                esc_url( add_query_arg( 'wp-video-capture-pro', wp_create_nonce( 'wp-video-capture-pro' ) ) ),
                __( 'Dismiss', 'video-capture' )
            );
        }

        /**
         * Hide Pro activation notice upon click.
         */
        public function hide_pro_activation_notice() {
            if ( ! isset( $_GET['wp-video-capture-pro'] ) ) {  // Input var "wp-video-capture-pro" is not set.
                return;
            }

            // Check nonce.
            check_admin_referer( 'wp-video-capture-pro', 'wp-video-capture-pro' );

            // Update user meta to indicate dismissed notice.
            update_user_meta( get_current_user_id(), '_wp-video-capture_hide_pro_activation_notice', true );
        }

        /**
         * Send email upon registration.
         */
        public function pro_activation_success_notice() {
            printf(
                '<div class="updated"><p>%1$s</div>',
                __( 'Vidrack Pro version successfully activated!', 'video-capture' )
            );
        }

        /**
         * Main pro settings init function.
         */
        public function admin_init_pro() {
            global $pagenow;

            // Display notification about registration.
            if ( isset( $_GET['page'] )  // Input var "page" is set.
                && 'edit.php' === $pagenow
                && 'wp_video_capture_settings' === $_GET['page'] // Input var "page" is set.
                && ! get_user_meta( get_current_user_id(), '_wp-video-capture_hide_pro_activation_success_notice', true ) ) {

                add_action( 'admin_notices', array( &$this, 'pro_activation_success_notice' ) );

                update_user_meta( get_current_user_id(), '_wp-video-capture_hide_pro_activation_success_notice', true );
            }

            // Register YouTube options.
            register_setting( 'wp_video_capture-group', 'vidrack_youtube_api_id' );
            register_setting( 'wp_video_capture-group', 'vidrack_youtube_api_secret' );

            // Register collect options.
            register_setting( 'wp_video_capture-group', 'vidrack_collect_email' );
            register_setting( 'wp_video_capture-group', 'vidrack_collect_name' );
            register_setting( 'wp_video_capture-group', 'vidrack_collect_phone' );
            register_setting( 'wp_video_capture-group', 'vidrack_collect_birthday' );
            register_setting( 'wp_video_capture-group', 'vidrack_collect_additional_data' );
            register_setting( 'wp_video_capture-group', 'vidrack_collect_language' );
            register_setting( 'wp_video_capture-group', 'vidrack_collect_location' );
            register_setting( 'wp_video_capture-group', 'vidrack_custom_collect_data_name_1' );
            register_setting( 'wp_video_capture-group', 'vidrack_custom_collect_data_value_1' );
            register_setting( 'wp_video_capture-group', 'vidrack_custom_collect_data_name_2' );
            register_setting( 'wp_video_capture-group', 'vidrack_custom_collect_data_value_2' );
            register_setting( 'wp_video_capture-group', 'vidrack_custom_collect_data_name_3' );
            register_setting( 'wp_video_capture-group', 'vidrack_custom_collect_data_value_3' );

            // Add your settings section.
            add_settings_section(
                'wp_video_capture-section-youtube',
                __( 'YouTube Settings', 'video-capture' ),
                array( &$this, 'settings_section_wp_video_capture_youtube' ),
                'wp_video_capture-youtube'
            );


            add_settings_section(
                'wp_video_capture-section-collect',
                __( 'Collect user data Settings', 'video-capture' ),
                array( &$this, 'settings_section_wp_video_collect_data' ),
                'wp_video_capture-collect'
            );

            // Add YouTube API id field.
            add_settings_field(
                'wp_video_capture-youtube_api_id',
                __( 'YouTube API id', 'video-capture' ),
                array( &$this, 'settings_field_input_text' ),
                'wp_video_capture-youtube',
                'wp_video_capture-section-youtube',
                array(
                    'field' => 'vidrack_youtube_api_id',
                )
            );

            // Add YouTube API secret key field.
            add_settings_field(
                'wp_video_capture-youtube_api_secret',
                __( 'YouTube API secret key', 'video-capture' ),
                array( &$this, 'settings_field_input_text' ),
                'wp_video_capture-youtube',
                'wp_video_capture-section-youtube',
                array(
                    'field' => 'vidrack_youtube_api_secret',
                )
            );
            
            // Add name collect settings field.
            add_settings_field(
                'wp_video_capture-collect_name_options',
                __( 'Collect Name', 'video-capture' ),
                array( &$this, 'settings_field_select_collect_data' ),
                'wp_video_capture-collect',
                'wp_video_capture-section-collect',
                array(
                    'field' => 'vidrack_collect_name',
                )
            );

            // Add email collect settings field.
            add_settings_field(
               'wp_video_capture-collect_email_options',
                __( 'Collect Email', 'video-capture' ),
                array( &$this, 'settings_field_select_collect_data' ),
               'wp_video_capture-collect',
               'wp_video_capture-section-collect',
               array(
                   'field' => 'vidrack_collect_email',
               )
           );

            // Add phone collect settings field.
            add_settings_field(
                'wp_video_capture-collect_phone_options',
                __( 'Collect Phone', 'video-capture' ),
                array( &$this, 'settings_field_select_collect_data' ),
                'wp_video_capture-collect',
                'wp_video_capture-section-collect',
                array(
                    'field' => 'vidrack_collect_phone',
                )
            );

           // Add date of birth collect settings field.
           add_settings_field(
              'wp_video_capture-collect_birthday_options',
               __( 'Collect Date of birth', 'video-capture' ),
               array( &$this, 'settings_field_select_collect_data' ),
              'wp_video_capture-collect',
              'wp_video_capture-section-collect',
              array(
                  'field' => 'vidrack_collect_birthday',
              )
          );

          // Add location collect settings field.
          add_settings_field(
             'wp_video_capture-collect_location_options',
              __( 'Collect Location', 'video-capture' ),
              array( &$this, 'settings_field_select_collect_data' ),
             'wp_video_capture-collect',
             'wp_video_capture-section-collect',
             array(
                 'field' => 'vidrack_collect_location',
             )
          );

          // Add language collect settings field.
          add_settings_field(
             'wp_video_capture-collect_language_options',
              __( 'Collect Language', 'video-capture' ),
              array( &$this, 'settings_field_select_collect_data' ),
             'wp_video_capture-collect',
             'wp_video_capture-section-collect',
             array(
                 'field' => 'vidrack_collect_language',
             )
          );

          // Add additional data collect settings field.
          add_settings_field(
             'wp_video_capture-collect_additional_data_options',
              __( 'Collect Additional message', 'video-capture' ),
              array( &$this, 'settings_field_select_collect_data' ),
             'wp_video_capture-collect',
             'wp_video_capture-section-collect',
             array(
                 'field' => 'vidrack_collect_additional_data',
             )
          );
        }

        /**
         * Main settings init function.
         */
        public function admin_init() {

            // Display notice about Pro activation.
            if ( ( isset( $_GET['post_type'] ) && 'vidrack_video' === $_GET['post_type'] ) || ( isset( $_GET['post_type'] ) && 'wp_video_capture_settings' === $_GET['page'] ) ) { // Input var "page" is set.
                if( ! $this->pro_account && ! get_user_meta( get_current_user_id(), '_wp-video-capture_hide_pro_activation_notice', true ) ) {
                    add_action( 'admin_notices', array( &$this, 'pro_activation_notice' ) );
                }
                if(  ! get_option( 'vidrack_notifications_email' ) && ! get_user_meta( get_current_user_id(), '_wp-video-capture_hide_email_notification_notice', true ) ) {
                    add_action( 'admin_notices', array( &$this, 'notifications_email_notice' ) );
                }
            }

            // Register and validate Mail options.
            register_setting( 'wp_video_capture-group', 'vidrack_notifications_email', array( &$this, 'validate_email' ) );

            // Register Pro account options.
            register_setting( 'wp_video_capture-group', 'vidrack_pro_account_key' );
            register_setting( 'wp_video_capture-group', 'vidrack_pro_account_email' );

            register_setting( 'wp_video_capture-group', 'vidrack_js_callback' );
            register_setting( 'wp_video_capture-group', 'vidrack_display_branding' );
            register_setting( 'wp_video_capture-group', 'vidrack_window_modal' );
            register_setting( 'wp_video_capture-group', 'vidrack_desktop_upload' );

            // Add Pro account settings section.
            add_settings_section(
                'wp_video_capture-section-pro',
                __( 'Pro account credentials', 'video-capture' ),
                array( &$this, 'settings_section_wp_video_capture_pro' ),
                'wp_video_capture_pro'
            );

            add_settings_section(
                'wp_video_capture-section-email',
                __( 'Notifications Email Settings', 'video-capture' ),
                '',
                'wp_video_capture-email'
            );

            // Add your settings section.
            add_settings_section(
                'wp_video_capture-section',
                __( 'Settings', 'video-capture' ),
                '',
                'wp_video_capture'
            );

            // Add Pro account key.
            add_settings_field(
                'vidrack_pro_account_key',
                __( 'License key', 'video-capture' ),
                array( &$this, 'settings_field_input_text' ),
                'wp_video_capture_pro',
                'wp_video_capture-section-pro',
                array(
                    'field' => 'vidrack_pro_account_key',
                )
            );

            // Add Pro account email.
            add_settings_field(
                'vidrack_pro_account_email',
                __( 'License email', 'video-capture' ),
                array( &$this, 'settings_field_input_text' ),
                'wp_video_capture_pro',
                'wp_video_capture-section-pro',
                array(
                    'field' => 'vidrack_pro_account_email',
                )
            );

            // Add JS callback setting.
            add_settings_field(
                'wp_video_capture-js_callback',
                __( 'JavaScript Callback Function', 'video-capture' ),
                array( &$this, 'settings_field_input_text' ),
                'wp_video_capture',
                'wp_video_capture-section',
                array(
                    'field' => 'vidrack_js_callback',
                )
            );

            // Add branding checkbox.
            add_settings_field(
                'wp_video_capture-display_branding',
                __( 'Display branding', 'video-capture' ),
                array( &$this, 'settings_field_input_checkbox' ),
                'wp_video_capture',
                'wp_video_capture-section',
                array(
                    'field' => 'vidrack_display_branding',
                )
            );

            // Add window format checkbox.
            add_settings_field(
                'wp_video_capture-window_modal',
                __( 'Display recorder in a pop-up', 'video-capture' ),
                array( &$this, 'settings_field_input_checkbox' ),
                'wp_video_capture',
                'wp_video_capture-section',
                array(
                    'field' => 'vidrack_window_modal',
                )
            );

            // Add email setting field.
            add_settings_field(
                'wp_video_capture-notifications_email',
                __( 'Notifications email', 'video-capture' ),
                array( &$this, 'settings_field_input_text' ),
                'wp_video_capture-email',
                'wp_video_capture-section-email',
                array(
                    'field' => 'vidrack_notifications_email',
                )
            );

	        // Add desktop upload button.
	        add_settings_field(
		        'wp_video_capture-desktop_upload',
                __( 'Desktop upload', 'video-capture' ),
		        array( &$this, 'settings_field_input_checkbox' ),
		        'wp_video_capture',
		        'wp_video_capture-section',
		        array(
			        'field' => 'vidrack_desktop_upload',
                )
            );
        }

        /**
         * Notification settings about pro activation.
         */
        public function settings_section_wp_video_capture_pro() {
            echo '<a href="http://vidrack.com/product/pro-version/" target="_blank">'.__( 'Pro version', 'video-capture' ).'</a> '. __( 'license key and email', 'video-capture' );
        }

        /**
         * Collecting data settings popup text.
        */
        public function settings_section_wp_video_collect_data() {
            echo __( 'Please choose type of collecting users data.' );
        }

        /**
         * Notification settings about YouTube API data, popup text.
         */
        public function settings_section_wp_video_capture_youtube() {
            echo __( 'Please enter your Google API details to enable YouTube video uploading. You can get these details using', 'video-capture' ).' <a href="https://console.developers.google.com/" target="_blank" >Google Developers Console</a>
            <br/>
            '.__( 'For the detailed instructions on getting credentials check', 'video-capture' ).' <a href="https://developers.google.com/youtube/analytics/registering_an_application" target="_blank">'.__( 'this link', 'video-capture' ).'</a>.
            <br/>
            '.__( 'Select', 'video-capture' ).' <b>Web client</b> '.__( 'and put', 'video-capture' ).' <code>' . site_url() . '</code> in <b>Authorised redirect URIs</b>.
            ';
        }

        /**
         * <input> template for the Settings.
         *
         * @param Array $args arguments.
         */
        public function settings_field_input_text( $args ) {
            $field = $args['field'];
            $value = get_option( $field );
            echo sprintf( '<input type="text" name="%s" id="%s" value="%s" />', esc_html( $field ), esc_html( $field ), esc_html( $value ) );
        }

        /**
         * <input type="checkbox"> template for Settings.
         *
         * @param Array $args arguments.
         */
        public function settings_field_input_checkbox( $args ) {
            $field = $args['field'];
            $value = get_option( $field );
            echo sprintf( '<input type="checkbox" name="%s" id="%s" value="1" %s/>', esc_html( $field ), esc_html( $field ), checked( $value, 1, '' ) );
        }

        /**
         * Select collect data options template for Settings.
         *
         * @param Array $args arguments.
         */
        public function settings_field_select_collect_data( $args ) {
            $field = $args['field'];
            $value = get_option( $field );
            echo sprintf( '<select name="%s" id="%s">
                                           <option value="mandatory" %s>'.__( 'Mandatory', 'video-capture' ).'</option>
                                           <option value="optional" %s>'.__( 'Optional', 'video-capture' ).'</option>
                                           <option value="no" %s>'.__( 'No', 'video-capture' ).'</option>
                                  </select>', esc_html( $field ), esc_html( $field ), selected( $value, 'mandatory', '' ), selected( $value, 'optional', '' ), selected( $value, 'no', '' ) );
        }

        /**
         * Add menu items.
         */
        public function add_menu() {
            // Settings.
            add_submenu_page(
                'edit.php?post_type=vidrack_video',
                __( 'Vidrack - Settings', 'video-capture' ),
                __( 'Settings', 'video-capture' ),
                'manage_options',
                'wp_video_capture_settings',
                array( &$this, 'plugin_settings_page' )
            );
        }

        /**
         * Settings page init.
         */
        public function plugin_settings_page() {
            if ( ! current_user_can( 'manage_options' ) ) {
                wp_die( esc_html( __( 'You do not have sufficient permissions to access this page.' ) ) );
            }

            // Add helper JS.
            wp_enqueue_script( 'record_video_admin_settings' );

            $pro_account = $this->pro_account;

            // Render the settings template.
            include plugin_dir_path( __FILE__ ) . 'templates/settings.php';
        }

    }
}
