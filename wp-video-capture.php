<?php
/**
 * Plugin Name: Video Recorder
 * Plugin URI: http://vidrack.com
 * Description: Add a video camera to your website!
 * Version: 1.8.7
 * Author: Vidrack.com
 * Author URI: http://vidrack.com
 * License: GPLv2 or later
 *
 * @package wp-video-capture
 */

/**
 * Current plugin version.
 * Changes manually with every upgrade.
 */
define( 'VIDRACK_VERSION', '1.8.7' );

define( 'VIDRACK_PLUGIN', __FILE__ );

define( 'VIDRACK_PLUGIN_BASENAME', plugin_basename( VIDRACK_PLUGIN ) );

define( 'VIDRACK_PLUGIN_DIR', untrailingslashit( dirname( VIDRACK_PLUGIN ) ) );

$site_url = wp_parse_url( site_url() );
define( 'HOSTNAME', $site_url['host'] );

require_once VIDRACK_PLUGIN_DIR . '/includes/wp-video-capture-integration.php';