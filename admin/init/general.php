<?php
/**
 * Adds filters and actions for
 * general WordPress admin area
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.1.8
 * 
 */


/**
 * Sets admin page title
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */
add_filter('admin_title', 'tslot_admin_pg_title', 10, 2);

function tslot_admin_pg_title($admin_title, $title){

	if (!is_admin()) {
		return;
	}

	$ts_current_screen = get_current_screen();
	$ts_slug = 'timeslot';

	if ( isset( $ts_current_screen->base ) && str_contains($ts_current_screen->base, $ts_slug) ) {

		$timeslot_string = 'Time Slot';
		$timeslot_summary_i18n = __('Appointment Booking Plugin for WordPress', 'timeslot');
		$ts_separator = ' | ';
		
		if (isset($_GET['tab'])){
			if($_GET['tab'] == 'import-export'){
				$ts_tab_i18n = __('Import/Export', 'timeslot');
			}
			else{
				$ts_tab_i18n = __(ucwords(str_replace('-', ' ', sanitize_text_field($_GET['tab']))), 'timeslot');
			}
			return $ts_tab_i18n . $ts_separator . $timeslot_string;
		}
		else if ($_GET['page'] == 'timeslot'){
			return $title . $ts_separator . $timeslot_summary_i18n;
		}
		else {
			return $title . $ts_separator . $timeslot_string;
		}
	}
}

/**
 * Adds links to Pro, Docs and Settings under 
 * the plugin in the plugin overview page
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */
add_filter('plugin_action_links_' . plugin_basename(TIMESLOT_FILE), __NAMESPACE__ . '\\tslot_add_action_link' );

function tslot_add_action_link($links) {

	// Add link to settings
	$settings_url = add_query_arg(
		'page',
		'timeslot',
		get_admin_url() . 'admin.php'
	);

	$settings_link = '<a href="' . esc_url( $settings_url ) . '">' . __( 'Settings', 'timeslot' ) . '</a>';
	array_unshift( $links, $settings_link );

	// Add link to docs
	$docs_link = '<a href="' . esc_url('https://timeslotplugins.com/docs/') . '" target="_blank">' . __( 'Docs', 'timeslot' ) . '</a>';
	array_unshift( $links, $docs_link );

	// Add link to pro page
	$pro_link = '<a style="font-weight: bold;" href="' . esc_url( 'https://timeslotplugins.com/') . '" target="_blank">' . __( 'Get Pro', 'timeslot' ) . '</a>';
	array_unshift( $links, $pro_link );

	return $links;
}

/**
 * Updates css for admin bar
 * 
 * Deprecated WordPress 6.4: _admin_bar_bump_cb
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */
add_action('admin_bar_init', 'tslot_admin_bar_css');

function tslot_admin_bar_css() {

	if (!is_user_logged_in()){
		return;
	}

	if (!is_admin_bar_showing()){
		return;
	}

	global $post;

	if (!has_block('timeslot/booking-form') && (is_a($post, 'WP_Post') && !has_shortcode($post->post_content, 'timeslot-form'))){
		return;
	}

	remove_action('wp_head', '_admin_bar_bump_cb');
	add_action('wp_head', '_admin_bar_bump_cb_ts');

	function _admin_bar_bump_cb_ts() {
		?>
		<style media="screen">html{margin-top:0!important;}body{margin-top:32px!important;}@media screen and (max-width:782px){body{margin-top:46px!important;}}</style>
		<?php
	}
}

/**
 * Logs plugin activation error
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */
add_action('activated_plugin', 'tslot_save_error');
error_log(get_option('plugin_error'));

function tslot_save_error() {
	update_option('plugin_error',  ob_get_contents());
}