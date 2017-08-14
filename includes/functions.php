<?php
/**
 * Add Settings link to the Plugins page.
 *
 * @param Array $links list of current links on the Plugins page.
 * @return Array $links updated links.
 */
function plugin_settings_link( $links ) {
	$settings_link = '<a href="admin.php?page=wp_video_capture_settings">'.__( 'Settings','video-capture' ).'</a>';
	array_unshift( $links, $settings_link );
	return $links;
}

$plugin = VIDRACK_PLUGIN_BASENAME;
add_filter( "plugin_action_links_$plugin", 'plugin_settings_link' );
$pro_account = $wp_video_capture->pro_account;

/**
 * Add additional links to the Plugins page.
 *
 * @param Array  $links list of current links on the Plugins list page.
 * @param String $plugin_file current modified.
 * @return Array $links updated links.
 */
function plugin_row_additional_links( $links, $plugin_file ) {
	global $pro_account;
	$file = VIDRACK_PLUGIN_BASENAME;
	if ( $plugin_file === $file ) {
		$additional_links = array(
			'install' => '<a href="http://vidrack.com/product/install/" target="_blank">'.__( 'Help to Install', 'video-capture' ).'</a>',
			'webapp'  => '<a href="http://vidrack.me/account/signup/" target="_blank">'.__( 'Try Vidrack Web App', 'video-capture' ).'</a>',
			'shop'    => '<a href="http://vidrack.com/shop/" target="_blank">'.__( 'Shop', 'video-capture' ).'</a>',
			'invest'  => '<a href="http://vidrack.com/invest/" target="_blank">'.__( 'Invest', 'video-capture' ).'</a>',
			'donate'  => '<a href="http://vidrack.com/donate/" target="_blank">'.__( 'Donate', 'video-capture' ).'</a>',
		);
		$new_links = array_merge( $links, $additional_links );
		if ( ! $pro_account ) {
			$new_links = array_merge(
				$new_links,
				array(
					'pro' => '<a href="http://vidrack.com/product/pro-version/" target="_blank" style="font-weight:bold;color:darkgreen;">'.__( 'Upgrade to Pro', 'video-capture' ).'</a>'
				)
			);
		}
		return $new_links;
	}
	return $links;
}
add_filter( 'plugin_row_meta', 'plugin_row_additional_links', 10, 2 );

/**
 * Add the resource to video list and Vidrack settings page.
 */
function register_resource_video_page() {
	global $post;
	global $wp_video_capture;
	if ( isset( $post->post_type ) && 'vidrack_video' === $post->post_type ) {
		wp_enqueue_script('dashboard_video', plugin_dir_url(VIDRACK_PLUGIN) . 'js/dashboard_video.js');
		wp_enqueue_script('download_video', plugin_dir_url(VIDRACK_PLUGIN) . 'js/download_video.js');
		wp_localize_script(
			'download_video',
			'VideoDownload',
			array(
				'ajaxurl' => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce('vidrack_nonce_secret'),
			)
		);

		if ( $wp_video_capture->pro_account ) {

			// Youtube upload JS content.
			wp_enqueue_script('upload_youtube', plugin_dir_url(VIDRACK_PLUGIN) . 'js/upload_youtube.js');
			wp_localize_script(
				'upload_youtube',
				'YouTubeUpload',
				array(
					'ajaxurl' => admin_url('admin-ajax.php'),
					'nonce' => wp_create_nonce('vidrack_nonce_secret'),
				)
			);

			// Spinner JS content.
			wp_register_script(
				'spin',
				plugin_dir_url(VIDRACK_PLUGIN) . 'lib/js/spin.min.js',
				array(), '2.3.2'
			);
			wp_register_script(
				'spinner_runner',
				plugin_dir_url(VIDRACK_PLUGIN) . 'js/spinner_runner.js', array('jquery', 'spin')
			);
			wp_enqueue_script('spinner_runner');

			// Rating video JS and CSS content.
			wp_register_script(
				'star-rating',
				plugin_dir_url(VIDRACK_PLUGIN) . 'lib/js/star-rating.min.js',
				array(), '1.1.0'
			);
			wp_register_script(
				'video_rating',
				plugin_dir_url(VIDRACK_PLUGIN) . 'js/video_rating.js', array('jquery', 'star-rating')
			);
			wp_enqueue_script('video_rating');
			wp_enqueue_style('star-rating', plugin_dir_url(VIDRACK_PLUGIN) . 'lib/css/star-rating.min.css');
			wp_localize_script(
				'video_rating',
				'VideoRating',
				array(
					'ajaxurl' => admin_url('admin-ajax.php'),
					'nonce' => wp_create_nonce('vidrack_nonce_secret'),
				)
			);

			// Video preview JS and CSS content.
			wp_register_script(
				'video_player',
				plugin_dir_url(VIDRACK_PLUGIN) . 'lib/js/video-js.min.js',
				array(), '2.0.0'
			);
			wp_register_script(
				'play_video',
				plugin_dir_url(VIDRACK_PLUGIN) . 'js/play_video.js', array('jquery', 'video_player')
			);
			wp_enqueue_script('play_video');
			wp_enqueue_style('video-js', plugin_dir_url(VIDRACK_PLUGIN) . 'lib/css/video-js.min.css');


			// Video sharing.
			wp_register_script(
				'video-share',
				plugin_dir_url(VIDRACK_PLUGIN) . 'js/video-share.js', array('jquery')
			);
			wp_enqueue_script('video-share');
			wp_enqueue_script('twitter-js', '//platform.twitter.com/widgets.js' );

		}
	}
	if ( isset( $_GET['post_type'] ) && 'vidrack_video' === $_GET['post_type'] ) { // Input var "post_type" is set.
		wp_enqueue_style( 'upload_youtube', plugin_dir_url( VIDRACK_PLUGIN ) . 'css/vidrack_admin.css' );
	}
}

// Add resource data for admin page.
add_action( 'admin_enqueue_scripts', 'register_resource_video_page' );


/**
 * Admin page footer text.
 *
 * @return string Vidrack footer text.
 */
function vidrack_admin_footer() {
	global $post;
	global $pro_account;
	if ( ( isset( $post->post_type ) && 'vidrack_video' === $post->post_type ) || ( isset( $_GET['page'] ) && 'wp_video_capture_settings' === $_GET['page'] ) // Input var "page" is set.
	) {
		$footer_text = '<ul class="wp-video-capture-footer-items">';
		if ( ! $pro_account ) {
			$footer_text .= '<li><a href="http://vidrack.com/product/pro-version/" target="_blank" class="wp-video-capture-pro-link">'.__( 'Upgrade to Pro', 'video-capture' ).'</a></li>';
		}
		$footer_text .= '<li><a class="wp-video-capture-tnc-link" href="http://vidrack.com/terms-conditions/" target="_blank">'.__( 'Terms and Conditions', 'video-capture' ).'</a></li>';
		$footer_text .= '<li>'.__( 'Powered by', 'video-capture' ).' <a href="http://vidrack.com" target="_blank">vidrack.com</a></li>';
		$footer_text .= '</ul>';
		return $footer_text;
	}
}

/**
 * Register JS and CSS resources.
 */
function register_resources() {
	global $pro_account;

	// Initialize Mobile Detect class.
	require_once plugin_dir_path( VIDRACK_PLUGIN ) . 'includes/class.mobile-detect.php';
	$mobile_detect = new Mobile_Detect;

	// JS.
	wp_register_script(
		'magnific-popup',
		plugin_dir_url( VIDRACK_PLUGIN ) . 'lib/js/magnific-popup.min.js',
		array( 'jquery' ), '1.0.0'
	);
	wp_register_script(
		'swfobject',
		plugin_dir_url( VIDRACK_PLUGIN ) . 'lib/js/swfobject.js',
		array(), '2.2'
	);
	wp_register_script(
		'record_video',
		plugin_dir_url( VIDRACK_PLUGIN ) . 'js/record_video.js', array( 'jquery', 'magnific-popup', 'swfobject' ),
		VIDRACK_VERSION
	);

	// CSS.
	wp_register_style(
		'magnific-popup',
		plugin_dir_url( VIDRACK_PLUGIN ) . 'lib/css/magnific-popup.css',
		array(), '1.0.0', 'screen'
	);
	wp_register_style(
		'record_video',
		plugin_dir_url( VIDRACK_PLUGIN ) . 'css/record_video.css',
		array( 'magnific-popup' ), VIDRACK_VERSION
	);

	if ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
		// Pass variables to the frontend.
		wp_localize_script(
			'record_video',
			'VideoCapture',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'ip' => esc_html( sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) ), // Input var OK.
				'site_name' => HOSTNAME,
				'plugin_url' => plugin_dir_url( VIDRACK_PLUGIN ),
				'display_branding' => get_option( 'vidrack_display_branding' ),
				'window_modal' => get_option( 'vidrack_window_modal' ),
				'mobile' => $mobile_detect->isMobile(),
				'js_callback' => get_option( 'vidrack_js_callback' ),
				'desktop_upload' => get_option( 'vidrack_desktop_upload' ),
				'collect_email' => get_option( 'vidrack_collect_email' ),
				'collect_name' => get_option( 'vidrack_collect_name' ),
				'collect_phone' => get_option( 'vidrack_collect_phone' ),
				'collect_birthday' => get_option( 'vidrack_collect_birthday' ),
				'collect_location' => get_option( 'vidrack_collect_location' ),
				'collect_language' => get_option( 'vidrack_collect_language' ),
				'collect_additional_data' => get_option( 'vidrack_collect_additional_data' ),
				'collect_custom_data' => array(
					array( 'name' => get_option( 'vidrack_custom_collect_data_name_1' ),
					       'value' => get_option( 'vidrack_custom_collect_data_value_1' )
					),
					array( 'name' => get_option( 'vidrack_custom_collect_data_name_2' ),
					       'value' => get_option( 'vidrack_custom_collect_data_value_2' )
					),
					array( 'name' => get_option( 'vidrack_custom_collect_data_name_3' ),
					       'value' => get_option( 'vidrack_custom_collect_data_value_3' )
					)
				),
				'pro_version' => $pro_account,
				'nonce' => wp_create_nonce( 'vidrack_nonce_secret' ),
			)
		);
	}
}

/**
 * Vidrack submenu name.
 */
function vidrack_submenu_name() {
	global $submenu;
	$submenu['edit.php?post_type=vidrack_video'][5][0] = __( 'Dashboard','video-capture' );
}

function post_scripts() {
	global $post;

	if ( !is_admin() && isset( $post->post_type ) &&  'vidrack_video' === $post->post_type ) {

		// Video preview JS and CSS content.
		wp_register_script(
			'video_player',
			plugin_dir_url(VIDRACK_PLUGIN) . 'lib/js/video-js.min.js',
			array(), '2.0.0'
		);
		wp_register_script(
			'play_video',
			plugin_dir_url(VIDRACK_PLUGIN) . 'js/play_video.js', array('jquery', 'video_player')
		);
		wp_enqueue_script('play_video');
		wp_enqueue_style('video-js', plugin_dir_url(VIDRACK_PLUGIN) . 'lib/css/video-js.min.css');
		wp_enqueue_style('vidrack-post-page', plugin_dir_url(VIDRACK_PLUGIN) . 'css/vidrack-post-page.css');

	}
}

add_action( 'wp_print_scripts', 'post_scripts');