<?php
/**
 * Configures email settings
 *
 * Creates email option settings, callbacks,
 * and validations.
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

// Enqueues TinyMCE

function tslot_register_tiny_btn($buttons) {

	array_push($buttons, 'timeslot-btn', 'test-email');
	return $buttons;

}

function tslot_tinymce_plugin($plugin_array) {

	$plugin_array['tslot_tinymce_btn'] = TIMESLOT_URL . 'admin/js/tinymce.min.js';
	return $plugin_array;

}

add_action( 'load-time-slot_page_timeslot-business', 'tslot_load_tiny_buttons' );

function tslot_load_tiny_buttons() {
	if (current_user_can('edit_posts') && current_user_can('edit_pages')) {
		add_filter('mce_buttons', 'tslot_register_tiny_btn');
		add_filter('mce_external_plugins', 'tslot_tinymce_plugin');
	}
}

add_filter('mce_external_languages', 'tslot_tinymce_locale');

function tslot_tinymce_locale($locales) {

	$locales ['timeslotButton'] = TIMESLOT_PATH . 'languages/ts-tinymce-lang.php';
	return $locales;

}

function tslot_is_staff() {

	$count_staff = new TimeSlot\CountStaff();
	return $count_staff -> is_staff();
}

// Initializes email settings
add_action( 'admin_init', 'tslot_email_tab_init' );

function tslot_email_tab_init() {

	add_settings_section( 'email-section','', 'tslot_email_callback', 'timeslot-email-tab' );

	$settings = array(
		'email-info-section' => array(
			'title'=> esc_html__('Email Information', 'timeslot'),
			'page'=>'timeslot-email-tab',
			'fields'=> array(
				array(
					'id'=> 'email-sender-name',
					'title'=> esc_html__('Sender Name', 'timeslot'),
					'callback'=> 'tslot_sender_name_callback'
				),
				array(
					'id'=> 'email-sender-email',
					'title'=> esc_html__('Sender Email', 'timeslot'),
					'callback'=> 'tslot_sender_email_callback'
				),
			)
		),
		'customer-email-section' => array(
			'title'=>__('Customer Emails', 'timeslot'),
			'page'=>'timeslot-email-tab',
			'fields'=> array(
				array(
					'id'=> 'customer-email-approved',
					'title'=> esc_html__('Approved Appointment', 'timeslot'),
					'callback'=> 'tslot_customer_email_switch_callback'
				),
				array(
					'id'=> 'customer-email-approved-subject',
					'title'=> esc_html__('Approved Subject', 'timeslot'),
					'callback'=> 'tslot_email_subject_callback'
				),
				array(
					'id'=> 'customer-email-approved-tinymce',
					'title'=> esc_html__('Approved Message', 'timeslot'),
					'callback'=> 'tslot_email_tinymce_callback'
				),
				array(
					'id'=> 'customer-email-canceled',
					'title'=> esc_html__('Canceled Appointment', 'timeslot'),
					'callback'=> 'tslot_customer_email_switch_callback'
				),
				array(
					'id'=> 'customer-email-canceled-subject',
					'title'=> esc_html__('Canceled Subject', 'timeslot'),
					'callback'=> 'tslot_email_subject_callback'
				),
				array(
					'id'=> 'customer-email-canceled-tinymce',
					'title'=> esc_html__('Canceled Message', 'timeslot'),
					'callback'=> 'tslot_email_tinymce_callback'
				),
			)
		),
		'staff-email-section' => array(
			'title'=>__('Staff Emails', 'timeslot'),
			'page'=>'timeslot-email-tab',
			'fields'=> array(
				array(
					'id'=> 'staff-email-approved',
					'title'=> esc_html__('Approved Appointment', 'timeslot'),
					'callback'=> 'tslot_staff_email_switch_callback'
				),
				array(
					'id'=> 'staff-email-approved-subject',
					'title'=> esc_html__('Approved Subject', 'timeslot'),
					'callback'=> 'tslot_email_subject_callback'
				),
				array(
					'id'=> 'staff-email-approved-tinymce',
					'title'=> esc_html__('Approved Message', 'timeslot'),
					'callback'=> 'tslot_email_tinymce_callback'
				),
				array(
					'id'=> 'staff-email-canceled',
					'title'=> esc_html__('Canceled Appointment', 'timeslot'),
					'callback'=> 'tslot_staff_email_switch_callback'
				),
				array(
					'id'=> 'staff-email-canceled-subject',
					'title'=> esc_html__('Canceled Subject', 'timeslot'),
					'callback'=> 'tslot_email_subject_callback'
				),
				array(
					'id'=> 'staff-email-canceled-tinymce',
					'title'=> esc_html__('Canceled Message', 'timeslot'),
					'callback'=> 'tslot_email_tinymce_callback'
				),
			)
		),
	);

	foreach($settings as $id => $values){
		add_settings_section(
			$id,
			$values['title'],
			'',
			$values['page']
		);

		foreach ($values['fields'] as $field) {
			add_settings_field(
				$field['id'],
				$field['title'],
				$field['callback'],
				$values['page'],
				$id,
				array(
					$values['page'],
					$field['id'],
					'label_for' => $field['id'],
					'class' => 'ts-settings-row '. $id .' ts-' . $field['id'] . '-field',
					'aria' => $field['title'],
				)
			);
		}

		register_setting($values['page'], $values['page'], 'tslot_email_validation');
	}

	if(!tslot_is_staff()){

		$ts_email_option = get_option('timeslot-email-tab');

		if ( isset($ts_email_option['staff-email-approved']) ) {
			unset($ts_email_option['staff-email-approved']);
		}
		if ( isset($ts_email_option['staff-email-canceled']) ) {
			unset($ts_email_option['staff-email-canceled']);
		}

		update_option('timeslot-email-tab', $ts_email_option);
	}
}

// Sets default email body values on activation
function tslot_email_body_defaults() {

	$default = '';
	$email_options = get_option('timeslot-email-tab');

	if(!empty($email_options)){
		return;
	}

	$ts_email_tinymce_keys = array(
		'customer-email-approved-tinymce',
		'customer-email-canceled-tinymce',
		'staff-email-approved-tinymce',
		'staff-email-canceled-tinymce'
	);

	foreach($ts_email_tinymce_keys as $key){

		switch ($key) {

			case 'customer-email-approved-tinymce':

				$h1 = 'Thank you for booking with us';
				$name = '[client_name]';
				$detail = '[service_price]';
				break;

			case 'customer-email-canceled-tinymce':

				$h1 = 'Your appointment has been canceled';
				$name = '[client_name]';
				$detail = '[service_price]';
				break;

			case 'staff-email-approved-tinymce':

				$h1 = 'Your appointment has been scheduled';
				$name = '[staff_name]';
				$detail = '[client_name]';
				break;

			case 'staff-email-canceled-tinymce':

				$h1 = 'Your appointment has been canceled';
				$name = '[staff_name]';
				$detail = '[client_name]';
				break;

			default:
				break;

		}

		switch ($key) {

			case 'customer-email-approved-tinymce':
			case 'customer-email-canceled-tinymce':
			case 'staff-email-approved-tinymce':
			case 'staff-email-canceled-tinymce':
				$default = '<header style="padding: 5px 10px;">
				<h1 style="text-align: center; font-size: 18px;">'.$h1.'</h1>
				<hr style="border: 1px solid #d9d9d9; background-color: #d9d9d9;" /></header>
				<div style="padding: 20px;">
				<p style="text-align: center;"><strong>'.$name.'</strong></p>
				<p style="margin-bottom: 0px; text-align: center;">[appt_date] at [appt_time]</p>
				<p style="text-align: center;">[service_name] for '.$detail.'</p>
				</div>
				<footer style="padding: 5px 10px;"><hr style="border: 1px solid #d9d9d9; background-color: #d9d9d9;" />
				<p style="text-align: center;"><strong style="font-size: 12px;">Contact [company_name]</strong></p>
				<p style="text-align: center; font-size: 1vw;"><span style="padding: 0 3px;">[company_phone]</span> | <span style="padding: 0 3px;">[company_email]</span> | <span style="padding: 0 3px;">[company_website]</span></p>
				</footer>';
				break;

			default:
				$default = '';
				break;

		}

		if(!is_array($email_options)){
			$email_options = [];
		}

		$email_options[$key] = $default;
		update_option('timeslot-email-tab', $email_options);

	}

}

// Loads replacement values to be inserted into TinyMCE from button
add_action( 'before_wp_tiny_mce', 'tslot_localize_email' );

function tslot_localize_email() {

	if (isset($_GET['tab']) && $_GET['tab'] !== 'email'){
		return;
	}

	// Check if any visible staff exist
	$count_staff = new TimeSlot\CountStaff();
	$is_staff = $count_staff -> is_staff();

	$ts_vars = array(
		'companyname' => '[company_name]',
		'companywebsite'=> '[company_website]',
		'companyemail'=> '[company_email]',
		'companyphone' =>  '[company_phone]',
		'companyaddress'=> '[company_full_address]',
		'companylogo' => '[company_logo]',
		'servicename'=> '[service_name]',
		'serviceduration' => '[service_duration]',
		'serviceprice'=> '[service_price]',
		'staffname' => '[staff_name]',
		'staffemail'=> '[staff_email]',
		'staffphone' => '[staff_phone]',
		'tspluginurl' => plugins_url('/timeslot/'),
		'isstaff' => $is_staff
	);

	$ts_vars_encode = wp_json_encode($ts_vars);

	?>
	<script type='text/javascript'>
		var ts_vars = <?php echo $ts_vars_encode; ?>;
	</script>
	<?php

}

// Email Subject
function tslot_email_subject_callback($args) {

	$ts_email_subject_setting = get_option($args[0]);
	$ts_email_subject_id = $args[1];
	$ts_email_subject_name = $args[0].'['.$args[1].']';
	$ts_email_subject_val = isset( $ts_email_subject_setting[""  . $args[1] . ""] ) ? esc_attr( $ts_email_subject_setting[""  . $args[1] . ""] ) : "";

	?>
	<input type='text' id='<?php esc_attr_e($ts_email_subject_id); ?>' class='ts-full-width' name='<?php esc_attr_e($ts_email_subject_name); ?>' value='<?php esc_attr_e($ts_email_subject_val); ?>' placeholder='<?php esc_attr_e('Email Subject', 'timeslot');?>'/>
	<?php

}

// TinyMCE Email Body
function tslot_email_tinymce_callback($args) {

	$ts_email_tinymce_setting = (array) get_option($args[0]);
	$ts_textarea_name = $args[0].'['.$args[1].']';
	$ts_textarea_id = $args[1];
	$ts_textarea_content = $ts_email_tinymce_setting[$args[1]] ?? '';
	$ts_email_tinymce = !empty($ts_textarea_content) ? esc_attr( $ts_textarea_content ) : '';

	$ts_tinymce_settings = array(
		'textarea_rows'=> '10',
		'media_buttons' => false,
		'textarea_name' => $ts_textarea_name,
		'editor_class' => 'ts-tinymce-textarea',
		'wpautop' => false,
		'tinymce' => array(
			'content_css' => TIMESLOT_URL . 'admin/css/ts-tinymce.css',
			'toolbar1' => 'bold,italic,underline | fontsizeselect | alignleft,aligncenter,alignright | link,unlink | timeslot-btn | test-email',
			'toolbar2' => '',
			'toolbar3' => '',
			'fontsize_formats' => '10px 12px 14px 18px 24px 36px'
		),
	);

	wp_editor( html_entity_decode(stripcslashes($ts_email_tinymce)), $ts_textarea_id, $ts_tinymce_settings);

}

// Customer Email Switch
function tslot_customer_email_switch_callback($args) {

	$ts_email_switch = get_option($args[0]);
	$ts_switch_value = empty($ts_email_switch[$args[1]]) ? 0 : 1;
	$ts_switch_name = $args[0].'['.$args[1].']';
	$ts_switch_id = 'ts-'.$args[1].'-chk';
	$ts_switch_label = 'Send '. str_replace('-'," ",$args[1]);

	?>
	<label class='ts-switch'>
		<input type='checkbox' id='<?php esc_attr_e($ts_switch_id); ?>' name='<?php esc_attr_e($ts_switch_name); ?>' value='1' <?php checked( $ts_switch_value, 1);?> aria-label='<?php esc_attr_e($ts_switch_label); ?>' />
		<span class='ts-slider' aria-hidden='true'></span>
	</label>
	<?php

}

// Staff Email Switch
function tslot_staff_email_switch_callback($args) {

	$ts_email_switch = get_option($args[0]);
	$ts_switch_value = empty($ts_email_switch[$args[1]]) ? 0 : 1;
	$ts_switch_name = $args[0].'['.$args[1].']';
	$ts_switch_id = 'ts-'.$args[1].'-chk';
	$ts_switch_label = 'Send '. str_replace('-'," ",$args[1]);

	$is_staff = tslot_is_staff();
	$staff_disabled = $is_staff ? '' : 'disabled="disabled"';
	$staff_disabled_title = $is_staff ? '' : __('No Staff Available', 'timeslot');

	?>
	<label class='ts-switch'>
		<input type='checkbox' id='<?php esc_attr_e($ts_switch_id); ?>' name='<?php esc_attr_e($ts_switch_name); ?>' value='1' <?php checked( $ts_switch_value, 1);?> aria-label='<?php esc_attr_e($ts_switch_label); ?>' <?php echo $staff_disabled; ?> title='<?php esc_attr_e($staff_disabled_title); ?>' />
		<span class='ts-slider' aria-hidden='true' title='<?php esc_attr_e($staff_disabled_title); ?>'></span>
	</label>
	<?php

}

// Headers
function tslot_email_callback() {

	$h1_email = __('Email', 'timeslot');
	$subtitle_email = __('Send email to staff and customers when appointments are approved or canceled.', 'timeslot');
	echo ('<h1>'. esc_html($h1_email) .'</h1><p class="ts-subtitle">'. esc_html($subtitle_email) .'</p>');

}

// Sender name
function tslot_sender_name_callback($args) {

	$ts_sender_name = get_option($args[0]);
	$ts_sender_name_id = $args[1];
	$ts_sender_name_attr = $args[0].'['.$args[1].']';
	$ts_sender_name_val = isset( $ts_sender_name[""  . $args[1] . ""] ) ? esc_attr( $ts_sender_name[""  . $args[1] . ""] ) : "";

	?>
	<input type='text' id='<?php esc_attr_e($ts_sender_name_id); ?>' class='ts-full-width' name='<?php esc_attr_e($ts_sender_name_attr); ?>' value='<?php esc_attr_e($ts_sender_name_val); ?>' placeholder='<?php esc_attr_e('Company Name', 'timeslot'); ?>' autocomplete='organization' aria-label='<?php esc_attr_e($args['aria']); ?>'/>
	<?php

}

// Sender email address
function tslot_sender_email_callback($args) {

	$ts_sender_email_setting = get_option($args[0]);
	$ts_sender_email_id = $args[1];
	$ts_sender_email_name = $args[0].'['.$args[1].']';
	$ts_sender_email_val = isset( $ts_sender_email_setting[""  . $args[1] . ""] ) ? esc_attr( $ts_sender_email_setting[""  . $args[1] . ""] ) : "";

	?>
	<input type='email' id='<?php esc_attr_e($ts_sender_email_id); ?>' class='ts-full-width' name='<?php esc_attr_e($ts_sender_email_name); ?>' value='<?php esc_attr_e($ts_sender_email_val); ?>'  placeholder='<?php esc_attr_e('Sender Email Address', 'timeslot'); ?>' autocomplete='email' aria-label='<?php esc_attr_e($args['aria']); ?>'/>
	<?php

}

// Sends test email from TinyMCE button click
add_action( 'wp_ajax_tslot_test_email', 'tslot_test_email' );

function tslot_test_email() {

	$test_email_recipient = sanitize_email($_POST['testemail']);
	$test_email_body = stripslashes($_POST['testemailBody']);

	$msg_wrapper_start = '<html><body>';
	$msg_wrapper_end = '</body></html>';
	$test_email_body = $msg_wrapper_start . $test_email_body . $msg_wrapper_end;

	$test_email_vars = array(
		'date' => null,
		'time' => null,
		'customer_name' => 'Jane Doe',
		'customer_phone' => '555-555-5555',
		'customer_email' => 'jane.doe@email.com',
		'service' => null,
		'staff' => null,
		'body' => $test_email_body
	);

	$ts_email = new TimeSlot\EmailReplacements($test_email_vars);

	// translators: Placeholder is company name
	$subject_string = __('%s Test Email', 'timeslot');
	$company_name = $ts_email -> company_name;
	$subject = sprintf( $subject_string, $company_name );
	$to = '<' . $test_email_recipient . '>';

	wp_mail( $to, $subject, $ts_email -> message, $ts_email -> headers );

}

// Validation
function tslot_email_validation($input) {

	foreach ( $input as $key => $val ) {

		switch($key){

			case 'email-sender-name':
			case 'email-sender-email':
			case 'customer-email-approved-subject':
			case 'customer-email-canceled-subject':
			case 'staff-email-approved-subject':
			case 'staff-email-canceled-subject':

				$input[$key] = sanitize_text_field($input[$key]);
				break;

			case 'customer-email-approved-tinymce':
			case 'customer-email-canceled-tinymce':
			case 'staff-email-approved-tinymce':
			case 'staff-email-canceled-tinymce':

				$email_allowed = new TimeSlot\EmailAllowed();
				$email_allowed = $email_allowed -> kses;
				$input[$key] = wp_kses($input[$key], $email_allowed);
				break;

			case 'customer-email-approved':
			case 'customer-email-canceled':
			case 'staff-email-approved':
			case 'staff-email-canceled':

				$input[$key] = absint($input[$key]);
				break;

			default:
				break;

		}

	}

	return $input;

}