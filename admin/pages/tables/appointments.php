<?php
/**
 * Displays appointment datatable
 *
 * Selects appointment data from database and displays
 * in datatable. Includes modal editor, delete,
 * and update functions. References admin/js/datatables.js.
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

// Registers rest route and returns appointment datatable data
$appointment_data = new TimeSlot\AppointmentData('appointments');

// Displays appointment datatable and editor modal
function tslot_appointments_tab_init(){

	global $wpdb;
	$ts_services_table = $wpdb->prefix . 'ts_services';
	$ts_staff_table = $wpdb->prefix . 'ts_staff';
	$ts_customer_table = $wpdb->prefix . 'ts_customers';

	$count_staff = new TimeSlot\CountStaff();
	$is_staff = $count_staff -> is_staff();
	$staff_atts = $is_staff ? array() : array('disabled' => 'disabled');

	$i18n_strings = new TimeSlot\MultilangStrings();
	$visible_i18n = $i18n_strings -> visible_string;

	$title = __('Appointments', 'timeslot');
	
	$headers = array(
		__('ID', 'timeslot'),
		__('Date', 'timeslot'),
		__('Time', 'timeslot'),
		__('Service', 'timeslot'),
		__('Staff', 'timeslot'),
		__('Customer', 'timeslot'),
		__('Status', 'timeslot'),
		__('Date Alt', 'timeslot'),
	);

	if(!$is_staff){
		unset($headers[4]);
		$headers = array_values($headers);
	}

	$status = array(
		__('Approved', 'timeslot'),
		__('Canceled', 'timeslot'),
	);

	$service_select = 
		$wpdb->get_col(
			"SELECT service_title 
			FROM {$ts_services_table}"
	);

	$staff_select = 
		$wpdb->get_col(
		$wpdb->prepare(
			"SELECT staff_name 
			FROM {$ts_staff_table}
			WHERE visibility = %s",
			$visible_i18n
	));

	$customer_select = 
		$wpdb->get_col(
			"SELECT customer_name 
			FROM {$ts_customer_table}"
	);

	$edit_fields = array(
		'title' => __('appointment', 'timeslot'),
		'name' => 'appointment',
		'fields' => array(
			array(
				'id' => 'ts-start-day',
				'label' => __('Date', 'timeslot'),
				'name' => 'startday',
				'type' => 'text',
				'width' => '50'
			),
			array(
				'id' => 'ts-start-time',
				'label' => __('Start Time', 'timeslot'),
				'name' => 'starttime',
				'type' => 'text',
				'width' => '50',
			),
			array(
				'name' => 'service',
				'options' => $service_select,
				'type' => 'select',
				'width' => '50',
			),
			array(
				'name' => 'staff',
				'options' => $staff_select,
				'type' => 'select',
				'width' => '50',
				'atts' => $staff_atts
			),
			array(
				'name' => 'customer',
				'options' => $customer_select,
				'type' => 'select',
				'width' => '50',
			),
			array(
				'id' => 'ts-appt-status',
				'name' => 'status',
				'type' => 'select',
				'width' => '50',
				'options' => $status
			),
			array(
				'id' => 'ts-start-day-alt',
				'label' => '',
				'name' => 'startdayalt',
				'type' => 'hidden',
				'width' => '0',
				'atts' => array(
					'readonly' => 'readonly'
				)
			),
		),
	);

	tslot_build_datatable($headers, $title);
	tslot_build_confirm_modal();
	tslot_build_edit_modal($edit_fields);

}

// Updates appointment table
add_action( 'wp_ajax_tslot_update_appt', 'tslot_update_appt' );

function tslot_update_appt() {

	if (!current_user_can('manage_options')) {
		wp_die();
	}

	if (!check_ajax_referer('ts-appointments-nonce', 'nonce')) {
		wp_die();
	}

	$count_staff = new TimeSlot\CountStaff();
	$is_staff = $count_staff -> is_staff();

	global $wpdb;
	$ts_appt_table = $wpdb->prefix . 'ts_appointments';
	$ts_customer_table = $wpdb->prefix . 'ts_customers';
	$row_id = intval($_POST['rowid']);
	$start_day = sanitize_text_field($_POST['start']);
	$start_time = date('H:i', strtotime(sanitize_text_field($_POST['starttime'])));
	$start = $start_day . 'T' . $start_time;
	$service = sanitize_text_field($_POST['service']);
	$staff = $is_staff ? sanitize_text_field($_POST['staff']) : '';
	$customer = sanitize_text_field($_POST['customer']);
	$appt_status = sanitize_text_field($_POST['status']);

	// Updates existing appointment
	if ($row_id !== 0){

		$original_status = 
		$wpdb->get_var(
		$wpdb->prepare(
			"SELECT appt_status 
			FROM {$ts_appt_table} 
			WHERE appt_id = %d", 
			$row_id
		));

		$customer_id = 
		$wpdb->get_var(
		$wpdb->prepare(
			"SELECT customer_id 
			FROM {$ts_customer_table} 
			WHERE customer_name = %s", 
			$customer
		));

		$wpdb->update(
			$ts_appt_table,
			array(
				'start_appt' => $start,
				'service_title' => $service,
				'staff' => $staff,
				'customer_id' => $customer_id,
				'customer_name' => $customer,
				'appt_status' => $appt_status,
			),
			array('appt_id' => $row_id)
		);

	}

	// Adds new appointment
	else {

		$customer_id = 
			$wpdb->get_var(
			$wpdb->prepare(
				"SELECT customer_id 
				FROM {$ts_customer_table} 
				WHERE customer_name = %s", 
				$customer 
		));

		$wpdb->insert(
			$ts_appt_table,
			array(
				'created' => current_time( 'mysql' ),
				'start_appt' => $start,
				'service_title' => $service,
				'staff' => $staff,
				'customer_name' => $customer,
				'customer_id' => $customer_id,
				'appt_status' => $appt_status,
			)
		);

		$original_status = 'New';
		$row_id = $wpdb->insert_id;

	}

	// Sends status for canceled email
	$status[] = array(
		'original_status' => $original_status
	);

	echo wp_json_encode($status);

	wp_die();
}

// Sends canceled email
add_action( 'wp_ajax_tslot_canceled_email', 'tslot_canceled_email' );
add_action( 'wp_ajax_nopriv_tslot_canceled_email', 'tslot_canceled_email' );

function tslot_canceled_email() {

	if (!current_user_can('manage_options')) {
		wp_die();
	}

	if (!check_ajax_referer( 'ts-appointments-nonce', 'nonce' )) {
		wp_die();
	}

	$count_staff = new TimeSlot\CountStaff();
	$is_staff = $count_staff -> is_staff();

	$email_options = get_option('timeslot-email-tab');
	$customer_canceled = $email_options['customer-email-canceled'] ?? null;
	$staff_canceled = isset($email_options['staff-email-canceled']) && $is_staff ?? null;
	$appt_status = sanitize_text_field($_POST['status']);

	if ($appt_status !== __('Canceled', 'timeslot')){
		return;
	}

	if (!$customer_canceled && !$staff_canceled){
		return;
	}

	$service = sanitize_text_field($_POST['service']);
	$staff = $is_staff ? sanitize_text_field($_POST['staff']) : '';
	$customer = sanitize_text_field($_POST['customer']);
	$start_date = sanitize_text_field($_POST['start']);
	$start_time = sanitize_text_field($_POST['starttime']);

	$email_vars = array(
		'date' => $start_date,
		'time' => $start_time,
		'customer_name' => $customer,
		'customer_phone' => null,
		'customer_email' => null,
		'service' => $service,
		'staff' => $staff,
	);

	if ($customer_canceled){

		$customer_canceled_subject = $email_options['customer-email-canceled-subject'];
		$customer_canceled_tinymce = $email_options['customer-email-canceled-tinymce'];

		$customer_email_vars = $email_vars;
		$customer_email_vars['body'] = $customer_canceled_tinymce;

		$ts_email = new TimeSlot\EmailReplacements($customer_email_vars);

		$to = $customer . '<' . $ts_email -> customer_email . '>';
		wp_mail( $to, $customer_canceled_subject, $ts_email -> message, $ts_email -> headers );

	}

	if ($staff_canceled){

		$staff_canceled_subject = $email_options['staff-email-canceled-subject'];
		$staff_canceled_tinymce = $email_options['staff-email-canceled-tinymce'];

		$staff_email_vars = $email_vars;
		$staff_email_vars['body'] = $staff_canceled_tinymce;

		$ts_email = new TimeSlot\EmailReplacements($staff_email_vars);

		$to = $staff . '<' . $ts_email -> staff_email . '>';
		wp_mail( $to, $staff_canceled_subject, $ts_email -> message, $ts_email -> headers );

	}

	wp_die();
}