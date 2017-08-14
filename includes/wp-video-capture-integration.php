<?php

require_once VIDRACK_PLUGIN_DIR . '/includes/class.validate-pro.php';
require_once VIDRACK_PLUGIN_DIR . '/includes/class.csv.php';
require_once VIDRACK_PLUGIN_DIR . '/includes/class.ajax-actions.php';
require_once VIDRACK_PLUGIN_DIR . '/includes/class.shortcodes.php';
require_once VIDRACK_PLUGIN_DIR . '/includes/class.vidrack.php';

if ( ! class_exists( 'WP_Video_Capture' ) ) {

	/**
	 * WP_Video_Capture main class.
	 */
	class WP_Video_Capture {

		/**
		 * Constructor.
		 */
		public function  __construct() {

			$this->pro_account = $pro_account = WP_Video_Capture_Check_PRO::validate_pro_account();

			// Initialize Settings.
			require_once plugin_dir_path( VIDRACK_PLUGIN ) . 'settings.php';
			$wp_video_capture_settings = new WP_Video_Capture_Settings( $pro_account );

			if ( $pro_account ) {

				if ( get_option('vidrack_youtube_api_secret') != NULL && get_option('vidrack_youtube_api_id') != NULL ) {
					// Initialize YouTube Class.
					require_once plugin_dir_path( VIDRACK_PLUGIN ) . 'includes/class.youtube.php';
					$this->yotube = new WP_YouTube();
				}

				// Initialize Pro version  JS and CSS resources.
				add_action( 'wp_enqueue_scripts', array( WP_Video_Capture_Check_PRO, 'register_resources_pro' ) );
			}

			// Initialize JS and CSS resources.
			add_action( 'wp_enqueue_scripts', 'register_resources' );

			// Initialize AJAX actions.
			add_action( 'wp_ajax_store_video_file', array( WP_Video_Capture_Ajax_Actions, 'store_video_file' ) );
			add_action( 'wp_ajax_nopriv_store_video_file', array( WP_Video_Capture_Ajax_Actions, 'store_video_file' ) );
			add_action( 'wp_ajax_validate_video_download_link', array( WP_Video_Capture_Ajax_Actions, 'validate_video_download_link' ) );
			add_action( 'wp_ajax_set_rating_video', array( WP_Video_Capture_Ajax_Actions, 'set_rating_video' ) );

			// On plugin init.
			add_action( 'init', array( WP_Video_Capture_Vidrack, 'plugin_init' ) );

			// Plugin admin page footer text.
			add_action( 'admin_footer_text', 'vidrack_admin_footer' );

			//Custom Vidrack actions.
			add_action( 'template_redirect', array( WP_Video_Capture_CSV, 'custom_actions') );

			//Vidrack export CSV link.
			add_action('admin_head-edit.php', array( WP_Video_Capture_CSV, 'export_btn') );

			// Initialize shortcode.
			add_shortcode( 'vidrack', array( WP_Video_Capture_Shortcode, 'record_video' ) );
			// [record_video] is added for compatibility with previous versions.
			add_shortcode( 'record_video', array( WP_Video_Capture_Shortcode, 'record_video' ) );

			//Change Vidrack submenu name.
			add_action( 'admin_menu', 'vidrack_submenu_name' );
		}
	}
}

if ( class_exists( 'WP_Video_Capture' ) ) {

	// Instantiate the plugin class.
	$wp_video_capture = new WP_Video_Capture();

	// Add a link to the settings page onto the plugin page.
	if ( isset( $wp_video_capture ) ) {

		require_once VIDRACK_PLUGIN_DIR . '/includes/languages.php';

		require_once VIDRACK_PLUGIN_DIR . '/includes/functions.php';
	}
}