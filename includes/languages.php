<?php
// Add localization settings.
function plugin_localization () {
	load_plugin_textdomain( 'video-capture', false, dirname( VIDRACK_PLUGIN_BASENAME).'/language/' );
}
add_action( 'plugins_loaded', 'plugin_localization' );