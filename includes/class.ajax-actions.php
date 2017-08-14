<?php
if ( ! class_exists( 'WP_Video_Capture_Ajax_Actions' ) ) {

	class WP_Video_Capture_Ajax_Actions {

		/**
		 * Process file uploading for mobile and desktop.
		 */
		public static function store_video_file() {
			header( 'Content-Type: application/json' );

			if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['nonce'] ), 'vidrack_nonce_secret' ) ) { // Input var "nonce" is set?
				echo wp_json_encode( array( 'status' => 'error', 'message' => __( 'An error occurred.', 'video-capture' ) ) );
				die();
			}

			if ( ! isset( $_POST['filename'] ) ) { // Input var "filename" is set?
				echo wp_json_encode( array( 'status' => 'error', 'message' => __( 'Filename is not set.', 'video-capture' ) ) );
				die();
			}

			if ( ! isset( $_POST['ip'] ) or ! filter_var( wp_unslash( $_POST['ip'] ), FILTER_VALIDATE_IP ) ) { // Input var "ip" is set?
				echo wp_json_encode( array( 'status' => 'error', 'message' => __( 'IP address is not set.', 'video-capture' ) ) );
				die();
			}

			// Insert new video info into the DB.
			$video = array(
				'post_type' => 'vidrack_video',
				'post_title' => sanitize_text_field( wp_unslash( $_POST['filename'] ) ), // Input var "filename" set!
				'post_status' => 'publish',
			);
			$post_id = wp_insert_post( $video, true );

			if ( is_wp_error( $post_id ) ) {
				echo wp_json_encode( array( 'status' => 'error', 'message' => $post_id->get_error_message() ) );
				die;
			}
			$r1 = add_post_meta( $post_id, '_vidrack_ip', sanitize_text_field( wp_unslash( $_POST['ip'] ), true ) ); // Input var "ip" is set.
			if ( isset( $_POST['external_id'] ) ) { // Input var "external_id" is set?
				$r2 = add_post_meta( $post_id, '_vidrack_external_id', sanitize_text_field( wp_unslash( $_POST['external_id'] ), true ) ); // Input var "external_id" is set.
			} else {
				$r2 = true;
			}

			if ( isset( $_POST['email'] ) ) { // Input var "email" is set?
				$r3 = add_post_meta( $post_id, '_vidrack_email', sanitize_text_field( wp_unslash( $_POST['email'] ), true ) ); // Input var "email" is set.
			} else {
				$r3 = true;
			}

			if ( isset( $_POST['birthday'] ) ) { // Input var "birthday" is set?
				$r4 = add_post_meta( $post_id, '_vidrack_birthday', sanitize_text_field( wp_unslash( $_POST['birthday'] ), true ) ); // Input var "birthday" is set.
			} else {
				$r4 = true;
			}

			if ( isset( $_POST['location'] ) ) { // Input var "location" is set?
				$r5 = add_post_meta( $post_id, '_vidrack_location', sanitize_text_field( wp_unslash( $_POST['location'] ), true ) ); // Input var "location" is set.
			} else {
				$r5 = true;
			}

			if ( isset( $_POST['language'] ) ) { // Input var "language" is set?
				$r6 = add_post_meta( $post_id, '_vidrack_language', sanitize_text_field( wp_unslash( $_POST['language'] ), true ) ); // Input var "language" is set.
			} else {
				$r6 = true;
			}

			if ( isset( $_POST['additional_data'] ) ) { // Input var "additional_data" is set?
				$r7 = add_post_meta( $post_id, '_vidrack_additional_data', sanitize_text_field( wp_unslash( $_POST['additional_data'] ), true ) ); // Input var "additional_data" is set.
			} else {
				$r7 = true;
			}

			if ( isset( $_POST['tag'] ) ) { // Input var "tag" is set?
				$r8 = add_post_meta( $post_id, '_vidrack_tag', sanitize_text_field( wp_unslash( $_POST['tag'] ), true ) ); // Input var "tag" is set.
			} else {
				$r8 = true;
			}

			if ( isset( $_POST['desc'] ) ) { // Input var "additional_data" is set?
				$r9 = add_post_meta( $post_id, '_vidrack_desc', sanitize_text_field( wp_unslash( $_POST['desc'] ), true ) ); // Input var "desc" is set.
			} else {
				$r9 = true;
			}

			if ( isset( $_POST['name'] ) ) { // Input var "name" is set?
				$r10 = add_post_meta( $post_id, '_vidrack_name', sanitize_text_field( wp_unslash( $_POST['name'] ), true ) ); // Input var "name" is set.
			} else {
				$r10 = true;
			}

			if ( isset( $_POST['phone'] ) ) { // Input var "phone" is set?
				$r11 = add_post_meta( $post_id, '_vidrack_phone', sanitize_text_field( wp_unslash( $_POST['phone'] ), true ) ); // Input var "phone" is set.
			} else {
				$r11 = true;
			}

			if ( isset( $_POST['custom_fields'] ) ) { // Input var "custom_fields" is set?
				$custom_fields = json_decode( stripslashes( $_POST['custom_fields'] ) );
				foreach ( $custom_fields as $key => $value ) {
					add_post_meta( $post_id, str_replace( 'vidrack-capture-', '_vidrack_', $key ), sanitize_text_field( wp_unslash( $value ), true ) );
				}
			}

			if ( ! $r1 || ! $r2 || ! $r3 || ! $r4 || ! $r5 || ! $r6 || ! $r7 || ! $r8 || ! $r9 || ! $r10|| ! $r11 ) {
				echo wp_json_encode( array( 'status' => 'error', 'message' => __( 'Cannot add post attributes.', 'video-capture' ) ) );
			} else {
				// Send email notification.
				if ( $to = get_option( 'vidrack_notifications_email' ) ) {
					// Initialize Mailer class.
					require_once plugin_dir_path( VIDRACK_PLUGIN ) . 'includes/class.video-capture-email.php';
					$video_capture_email = new Video_Capture_Email( HOSTNAME );

					$video_capture_email->send_new_video_email( $to,  sanitize_text_field( wp_unslash( $_POST['filename'] ) ) ); // Input var "filename" is set.
				}

				echo wp_json_encode( array( 'status' => 'success', 'message' => __( 'Done!', 'video-capture' ) ) );
			}

			die();
		}

		/**
		 * Check if the video is actually on S3.
		 */
		public static function validate_video_download_link() {

			if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['nonce'] ), 'vidrack_nonce_secret' ) ) { // Input var "nonce" is set?
				echo wp_json_encode( array( 'status' => 'error' ) );
				die;
			}

			if ( isset( $_POST['video_link'] ) ) { // Input var "ip" video_link set?
				$video_link = esc_url_raw( wp_unslash( $_POST['video_link'] ) );

				$ch = curl_init();
				curl_setopt( $ch, CURLOPT_URL,            $video_link );
				curl_setopt( $ch, CURLOPT_HEADER,         true );
				curl_setopt( $ch, CURLOPT_NOBODY,         true );
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
				curl_setopt( $ch, CURLOPT_TIMEOUT,        15 );
				curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
				curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
				$r = curl_exec( $ch );
				$headers_response = explode( "\n", $r );

				if ( false !== strpos( $headers_response[0], '200' ) ) {
					echo wp_json_encode( array( 'status' => 'success' ) );
					die;
				} else {
					echo wp_json_encode( array( 'status' => 'error' ) );
					die;
				}
			} else {
				return;
			}
		}

		/**
		 * Set video rating on video list page.
		 */
		public static function set_rating_video() {

			if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['nonce'] ), 'vidrack_nonce_secret' ) ) { // Input var "nonce" is set?
				echo wp_json_encode( array( 'status' => 'error' ) );
				die;
			}

			if ( isset( $_POST['rating_value'] ) && isset( $_POST['post_id'] )  ) { // Input var "rating_value" and "post_id" set?
				$rating_value = esc_html( wp_unslash( $_POST['rating_value'] ) );
				$post_id = esc_html( wp_unslash( $_POST['post_id'] ) );
				$result = update_post_meta($post_id, '_vidrack_video_rating', $rating_value);
				echo wp_json_encode( array( 'result' => $result ) );
				die;
			} else {
				echo wp_json_encode( array( 'result' => false ) );
				die;
			}
		}

	}

}