<?php
/**
 * Imports / Exports Time Slot settings
 *
 * Form that generates a JSON file of 
 * Time Slot settings options to download,
 * and a form to upload a JSON file to set
 * Time Slot settings.
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */

// Exit if accessed directly
 if (!defined('ABSPATH')){
	exit;
}

// Builds forms
function tslot_import_export_tab_init(){
	?>
	<div class='ts-settings-form ts-export-form'>
		<h1><?php esc_html_e('Import / Export Settings', 'timeslot'); ?></h1>
		<h2><?php esc_html_e('Export Settings', 'timeslot'); ?></h2>
			<p><?php esc_html_e('Export the Time Slot plugin settings.', 'timeslot'); ?></p>
			<form method='post'>
				<p><input type='hidden' name='ts_action' value='ts_export_settings' /></p>
				<div>
					<?php wp_nonce_field('ts_export_nonce', 'ts_export_nonce'); ?>
					<?php submit_button(esc_html__('Export', 'timeslot'), 'ts-btn', 'submit', false); ?>
				</div>
			</form>
	</div>

	<div class='ts-settings-form ts-import-form'>
		<h2><?php esc_html_e('Import Settings', 'timeslot'); ?></h2>
		<p><?php esc_html_e('Import the Time Slot plugin settings from a .json file.', 'timeslot'); ?></p>
		<form id='ts-import-form' method='post' enctype='multipart/form-data'>
			<div>
				<input id='ts-import-input' type='file' name='ts_import_file'/>
				<?php

				global $ts_import_error;

				if (is_wp_error($ts_import_error)) {
					foreach ($ts_import_error->get_error_messages() as $error) {
						?>
						<p><strong><?php esc_html_e('Error:', 'timeslot'); ?></strong> <?php esc_html_e($error); ?></p><br/>
						<?php
					}
				}

				if ( get_transient('timeslot_import') ) {
					?>
					<h3 role='alert'><?php esc_html_e('Settings imported successfully', 'timeslot'); ?></h3>
					<?php

					delete_transient('timeslot_import');
				}

				?>
			</div>
			<div>
				<input type='hidden' name='ts_action' value='ts_import_settings' />
				<?php wp_nonce_field('ts_import_nonce', 'ts_import_nonce'); ?>
				<?php submit_button(esc_html__('Import', 'timeslot'), 'ts-btn', 'submit', false); ?>
			</div>
		</form>
	</div>
	<?php
}

// Generates .json file of settings
add_action('admin_init', 'tslot_export');

function tslot_export() {

	if (empty($_POST['ts_action']) || 'ts_export_settings' != $_POST['ts_action']){
		return;
	}

	if (! wp_verify_nonce($_POST['ts_export_nonce'], 'ts_export_nonce')){
		return;
	}

	if (! current_user_can('manage_options')){
		return;
	}

	global $wpdb;

	$is_wp_multisite = is_multisite();

	if ($is_wp_multisite) {
		$options_results = 
			$wpdb->get_results(
			$wpdb->prepare(
				"SELECT *
				FROM {$wpdb->sitemeta}
				WHERE meta_key LIKE %s 
				AND site_id = %d;", 
				'timeslot%', $wpdb->siteid
				), ARRAY_A);
	}
	else {
		$options_results = 
			$wpdb->get_results(
				"SELECT * 
				FROM {$wpdb->options} 
				WHERE `option_name` 
				LIKE 'timeslot%';", ARRAY_A 
			);
	}

	$options = array();

	foreach ((array) $options_results as $option) {

		$name = $is_wp_multisite ? $option['meta_key'] : $option['option_name'];

		$options[] = array(
			'name'  => $name,
			'value' => maybe_unserialize($is_wp_multisite ? $option['meta_value'] : $option['option_value']),
			'auto'  => (empty($option['autoload']) || 'yes' === $option['autoload']) ? 'yes' : 'no',
		);
	}

	$content = array(
		'timeslot-options' => $options,
	);

	ignore_user_abort(true);

	nocache_headers();
	header('Content-Type: application/json; charset=utf-8');
	header('Content-Disposition: attachment; filename=ts-settings-export-' . date('m-d-Y') . '.json');
	header('Expires: 0');

	echo wp_json_encode($content);
	exit;
}

// Imports .json file and updates settings
add_action('admin_init', 'tslot_import');

function tslot_import() {

	if (empty($_POST['ts_action']) || 'ts_import_settings' != $_POST['ts_action']){
		return;
	}

	if (! wp_verify_nonce($_POST['ts_import_nonce'], 'ts_import_nonce')){
		return;
	}

	if (! current_user_can('manage_options')){
		return;
	}

	global $ts_import_error;
	$ts_import_error = new WP_Error;

	$ts_import_file = $_FILES['ts_import_file']['tmp_name'];

	if (!is_uploaded_file($ts_import_file)){
		return;
	}

	if (!file_exists($ts_import_file)){
		return;
	}

	if (empty($ts_import_file)) {
		$ts_import_error->add('file_empty', __('Please upload a file to import', 'timeslot'));
		return;
	}

	$ts_filesize = filesize($ts_import_file);
	$ts_filetype = mime_content_type($ts_import_file);

	if ($ts_filetype !== 'application/json') {
		$ts_import_error->add('file_not_json_extension', __('Please upload a JSON file', 'timeslot'));
		return;
	}

	if ($ts_filesize === 0) {
		$ts_import_error->add('file_empty', __('Please upload a valid JSON file', 'timeslot'));
		return;
	}

	if ($ts_filesize > 1048576) {
		$ts_import_error->add('file_size_limit', __('The file is too large.', 'timeslot'));
		return;
	}

	$settings = (array) json_decode(file_get_contents($ts_import_file), true);

	if (json_last_error() !== 0) {
		$ts_import_error->add('invalid_json_data', __('The JSON data is invalid.', 'timeslot'));
		return;
	}

	if (is_null($settings)) {
		$ts_import_error->add('no_json_data', __('The JSON file is invalid.', 'timeslot'));
		return;
	}

	if (!is_array($settings)) {
		$ts_import_error->add('invalid_json_array', __('The JSON data array is invalid.', 'timeslot'));
		return;
	}

	if (array_key_first($settings) !== 'timeslot-options') {
		$ts_import_error->add('invalid_settings_array', __('The settings data is invalid.', 'timeslot'));
		return;
	}

	foreach ($settings['timeslot-options'] as $option) {
		if (is_multisite()) {
			delete_site_option($option['name']);
			add_site_option($option['name'], $option['value']);
		} else {
			delete_option($option['name']);
			add_option($option['name'], $option['value'], null, $option['auto']);
		}
	}

	set_transient('timeslot_import', 1, 45);

}