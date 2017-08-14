<?php

if ( ! class_exists( 'WP_Video_Capture_Check_PRO' ) ) {

	class WP_Video_Capture_Check_PRO {
		/**
		 * Check if Pro Version.
		 *
		 * @return bool result validating Pro account version.
		 */
		public static function validate_pro_account() {
			$pro_account_key       = get_option( 'vidrack_pro_account_key' );
			$pro_account_email     = get_option( 'vidrack_pro_account_email' );
			if ( $pro_account_key && $pro_account_email ) {
				$instance              = str_replace( array( 'http://', 'https://', 'www.' ), '', get_site_url() );
				$status_url_parameters = array(
					'wc-api'      => 'am-software-api',
					'licence_key' => $pro_account_key,
					'instance'    => $instance,
					'email'       => $pro_account_email,
					'request'     => 'status',
					'product_id'  => 'Pro Version',
				);
				$status_url            = 'https://vidrack.com/?' . http_build_query( $status_url_parameters );

				$ch = curl_init();
				curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
				curl_setopt( $ch, CURLOPT_HEADER, 0 );
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
				curl_setopt( $ch, CURLOPT_URL, $status_url );
				curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );

				$response_status = json_decode( curl_exec( $ch ) );
				curl_close( $ch );

				if ( isset( $response_status->status_check ) && 'active' === $response_status->status_check ) {
					return true;
				} else {
					$activate_url_parameters = array(
						'wc-api'      => 'am-software-api',
						'licence_key' => $pro_account_key,
						'instance'    => $instance,
						'email'       => $pro_account_email,
						'request'     => 'activation',
						'product_id'  => 'Pro Version',
					);
					$activate_url            = 'https://vidrack.com/?' . http_build_query( $activate_url_parameters );

					$ch = curl_init();
					curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
					curl_setopt( $ch, CURLOPT_HEADER, 0 );
					curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
					curl_setopt( $ch, CURLOPT_URL, $activate_url );
					curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );

					$response_activate = json_decode( curl_exec( $ch ) );
					curl_close( $ch );

					if ( isset( $response_activate->activated ) && true === $response_activate->activated ) {
						return true;
					} elseif ( isset( $response_activate->activated ) && 'inactive' === $response_activate->activated ) {
						if ( 103 === $response_activate->code ) {
							add_action( 'admin_notices', array( __CLASS__, 'notice_maximum_number_activation_pro_account' ) );
						} else {
							add_action( 'admin_notices', array( __CLASS__, 'notice_validation_pro_account' ) );
						}
						add_action( 'admin_init', array( __CLASS__, 'hide_pro_activation_success_notice' ) );

						return false;
					}
				}
			} else {
				return false;
			}
		}

		/**
		 * Pro account  API credentials notice message.
		 */
		function notice_maximum_number_activation_pro_account() {
			echo '<div><div class="update-nag">' . __( 'This key was already used to activate Vidrack Pro', 'video-capture' ) . '</div></div>';
		}

		/**
		 * Pro account  API credentials notice message.
		 */
		function notice_validation_pro_account() {
			echo '<div><div class="update-nag">' . __( 'Please enter valid Pro License credentials', 'video-capture' ) . '</div></div>';
		}

		/**
		 * Hide registration notice if not Pro version.
		 */
		function hide_pro_activation_success_notice() {
			update_user_meta( get_current_user_id(), '_wp-video-capture_hide_pro_activation_success_notice', false );
		}

		/**
		 * Register Pro version JS and CSS resources.
		 */
		public static function  register_resources_pro(){
			// JS.
			wp_enqueue_script( 'select2', plugin_dir_url( VIDRACK_PLUGIN ) . 'lib/js/select2.min.js', array('jquery'), '', true );
			wp_enqueue_script( 'datepicker', plugin_dir_url( VIDRACK_PLUGIN ) . 'lib/js/datepicker.min.js', array('jquery'), '', true );

			// CSS.
			wp_enqueue_style( 'select2', plugin_dir_url( VIDRACK_PLUGIN ) . 'lib/css/select2.min.css' );
			wp_enqueue_style( 'datepicker', plugin_dir_url( VIDRACK_PLUGIN ) . 'lib/css/datepicker.min.css' );
		}
	}

}