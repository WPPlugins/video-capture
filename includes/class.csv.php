<?php

if ( ! class_exists( 'WP_Video_Capture_CSV' ) ) {

	class WP_Video_Capture_CSV {

		/**
		 * Add Vidrack CSV export links.
		 */
		public static function export_btn(){
			global $post;
			global $pro_account;
			if ( isset( $post->post_type ) && 'vidrack_video' === $post->post_type && $pro_account ) {
				$export_csv_url = '/?vidrack_action=csv_video_export&nonce='.wp_create_nonce("vidrack_nonce_secret");
				echo "<script type='text/javascript'>
                        jQuery(document).ready( function($) {
                             jQuery('.wrap h1:first-child').append('<a href=$export_csv_url id=vidrack_export_csv target=_blank >CSV Export</a>');
                        });
                     </script>";
			}
		}

		/**
		 * Vidrack run custom actions.
		 */
		public static function custom_actions() {
			if ( isset( $_GET['vidrack_action'] ) && isset( $_GET['nonce'] ) && wp_verify_nonce( sanitize_key( $_GET['nonce'] ), 'vidrack_nonce_secret' )  ) { // Input var "post_type" is set.
				$action = $_GET['vidrack_action'];
				switch ($action){
					case 'csv_video_export':
						self::csv_video_export();
						break;
					default:
						break;
				}
				return;
			} else{
				return;
			}
		}

		/**
		 * Vidrack video posts CSV export.
		 */
		public static function csv_video_export() {

			$filename = time()."_video_vidrack.csv";
			$fp = fopen( 'php://output', 'w' );
			header( 'Content-type: application/csv' );
			header( 'Content-Disposition: attachment; filename='.$filename );

			$header = array( __( 'Filename', 'video-capture' ),
				__( 'Download Link', 'video-capture' ),
				__( 'Rating', 'video-capture' ),
				__( 'Configurable Options', 'video-capture' ),
				__( 'IP', 'video-capture'),
				__( 'External ID', 'video-capture' ),
				__( 'Date', 'video-capture' )
			);
			fputcsv( $fp, $header );

			$args = array(
				'post_type'        => 'vidrack_video',
				'posts_per_page' => -1
			);

			$posts_array = get_posts( $args );

			foreach ( $posts_array as $post ) {
				$post_id = $post->ID;
				$post_meta = get_post_meta( $post_id );

				$post_title = $post->post_title;
				$post_download_link = 'http://vidrack-media.s3.amazonaws.com/'.$post->post_title;
				$post_rating = isset( $post_meta['_vidrack_video_rating'][0] ) ? $post_meta['_vidrack_video_rating'][0] : '';
				$post_ip = isset( $post_meta['_vidrack_ip'][0] ) ? $post_meta['_vidrack_ip'][0] : '';
				$post_external_id = isset( $post_meta['_vidrack_external_id'][0] ) ? $post_meta['_vidrack_external_id'][0] : '';
				$post_date =  $post->post_date;
				$post_configure_options_string = '';

				$post_configure_options_array = array();
				$post_configure_options_array['email'] = isset( $post_meta['_vidrack_email'][0] ) ? $post_meta['_vidrack_email'][0] : '';
				$post_configure_options_array['birthday'] = isset( $post_meta['_vidrack_birthday'][0] ) ? $post_meta['_vidrack_birthday'][0] : '';
				$post_configure_options_array['location'] = isset( $post_meta['_vidrack_location'][0] ) ? $post_meta['_vidrack_location'][0] : '';
				$post_configure_options_array['language'] = isset( $post_meta['_vidrack_language'][0] ) ? $post_meta['_vidrack_language'][0] : '';
				$post_configure_options_array['message'] = isset( $post_meta['_vidrack_additional_data'][0] ) ? $post_meta['_vidrack_additional_data'][0] : '';

				foreach( $post_configure_options_array as $key => $value ){
					if( ! $value ) continue;
					$post_configure_options_string .= ucfirst( $key ).": ".$value."; ";
				}

				fputcsv( $fp, array(
					$post_title,
					$post_download_link,
					$post_rating,
					$post_configure_options_string,
					$post_ip,
					$post_external_id,
					$post_date
				) );
			}
			exit;
		}

	}

}