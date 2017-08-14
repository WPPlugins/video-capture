<?php

if ( ! class_exists( 'WP_Video_Capture_Shortcode' ) ) {

	class WP_Video_Capture_Shortcode {

		/**
		 * [vidrack] tag implementation.
		 *
		 * @param String $atts tag attributes (left, right, etc).
		 * @param String $content tag content (empty).
		 * @return Source $record_video_contents data buffer.
		 */
		public static function record_video( $atts, $content = null ) {
			// Extract attributes.
			$atts = shortcode_atts( array( 'align' => 'left', 'ext_id' => null, 'tag' => null, 'desc' => null ), $atts );
			$align = $atts['align'];
			$ext_id = $atts['ext_id'];
			$tag = $atts['tag'];
			$desc = $atts['desc'];

			// Enable output buffering.
			ob_start();

			// Render template.
			wp_enqueue_style( 'record_video' );
			wp_enqueue_script( 'record_video' );
			include plugin_dir_path( VIDRACK_PLUGIN ) . 'templates/record-video.php';

			// Return buffer.
			$record_video_contents = ob_get_contents();
			ob_end_clean();
			return $record_video_contents;
		}

	}

}