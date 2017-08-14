<?php
/**
 * Settings page template.
 *
 * @package wp-video-capture
 */

?>

<div class="wrap">
    <h2><?php _e( 'Video Recorder','video-capture' );?></h2>
    <?php if ( ! $pro_account ) {?>
        <h4><a href="http://vidrack.com/product/pro-version/" target="_blank" class="wp-video-capture-pro-link"><?php _e( 'Upgrade to Vidrack Pro','video-capture' );?></a></h4>
    <?php } ?>
    <h4><?php _e( 'Have trouble playing videos? Download ','video-capture' );?><a href="http://www.videolan.org/" target="_blank">VLC media player</a>!</h4>
    <form method="post" action="options.php">
        <?php settings_errors( 'vidrack_notifications_email' ) ?>
        <?php settings_fields( 'wp_video_capture-group' ); ?>
        <?php do_settings_fields( 'wp_video_capture-group', 'wp_video_capture-section' ); ?>
        <?php do_settings_sections( 'wp_video_capture-email' ); ?>
        <?php do_settings_sections( 'wp_video_capture_pro' ); ?>
        <?php do_settings_sections( 'wp_video_capture' ); ?>
        <?php do_settings_sections( 'wp_video_capture-youtube' ); ?>
        <?php do_settings_sections( 'wp_video_capture-collect' ); ?>

        <?php if ( $pro_account ) { ?>
            <table class="form-table" id="custom-capture-fields">
            <tr>
                <th scope="row">Custom Collect Data field 1</th>
                <td><input type="text" name="vidrack_custom_collect_data_name_1" value="<?php echo get_option( 'vidrack_custom_collect_data_name_1' ); ?>" /></td>
                <td><select name="vidrack_custom_collect_data_value_1" >
                        <option value="mandatory" <?php if( 'mandatory' === get_option( 'vidrack_custom_collect_data_value_1' ) ) echo 'selected' ?> >Mandatory</option>
                        <option value="optional" <?php if( 'optional' === get_option( 'vidrack_custom_collect_data_value_1' ) ) echo 'selected' ?> >Optional</option>
                        <option value="no" <?php if( 'no' === get_option( 'vidrack_custom_collect_data_value_1' ) ) echo 'selected' ?> >No</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">Custom Collect Data field 2</th>
                <td><input type="text" name="vidrack_custom_collect_data_name_2" value="<?php echo get_option( 'vidrack_custom_collect_data_name_2' ); ?>" /></td>
                <td><select name="vidrack_custom_collect_data_value_2" >
                        <option value="mandatory" <?php if( 'mandatory' === get_option( 'vidrack_custom_collect_data_value_2' ) ) echo 'selected' ?> >Mandatory</option>
                        <option value="optional" <?php if( 'optional' === get_option( 'vidrack_custom_collect_data_value_2' ) ) echo 'selected' ?> >Optional</option>
                        <option value="no" <?php if( 'no' === get_option( 'vidrack_custom_collect_data_value_2' ) ) echo 'selected' ?> >No</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">Custom Collect Data field 3</th>
                <td><input type="text" name="vidrack_custom_collect_data_name_3" value="<?php echo get_option( 'vidrack_custom_collect_data_name_3' ); ?>" /></td>
                <td><select name="vidrack_custom_collect_data_value_3" >
                        <option value="mandatory" <?php if( 'mandatory' === get_option( 'vidrack_custom_collect_data_value_3' ) ) echo 'selected' ?> >Mandatory</option>
                        <option value="optional" <?php if( 'optional' === get_option( 'vidrack_custom_collect_data_value_3' ) ) echo 'selected' ?> >Optional</option>
                        <option value="no" <?php if( 'no' === get_option( 'vidrack_custom_collect_data_value_3' ) ) echo 'selected' ?> >No</option>
                    </select>
                </td>
            </tr>
            </table>
        <?php } ?>
        <?php submit_button(); ?>
    </form>

    <h2><?php _e( 'How to use','video-capture' );?></h2>
    <p><?php _e( 'Add shortcode','video-capture' );?> <strong>[vidrack]</strong> <?php _e( 'anywhere on the page','video-capture' );?>.</p>
    <p><?php _e( 'It accept the following parameters', 'video-capture' );?>:</p>
    <ul>
		<li><?php _e( 'Align to the right', 'video-capture' );?>: <strong>[vidrack align="right"]</strong></li>
		<li><?php _e( 'Align to the center', 'video-capture' );?>: <strong>[vidrack align="center"]</strong></li>
		<li><?php _e( 'Align to the left', 'video-capture' );?>: <strong>[vidrack align="left"]</strong></li>
		<li><?php _e( 'External ID for 3rd party integration', 'video-capture' );?>: <strong>[vidrack ext_id="123"]</strong></li>
	</ul>
</div>
