<?php
/**
 * Displays staff datatable
 *
 * Selects staff data from database and displays
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

// Registers rest route and returns staff datatable data
$staff_data = new TimeSlot\StaffData('staff');

// Displays staff datatable and editor modal
function tslot_staff_tab_init(){

	global $wpdb;
	$ts_services_table = $wpdb->prefix . 'ts_services';
	
	$services = 
		$wpdb->get_col(
			"SELECT service_title 
			FROM {$ts_services_table}"
	);

	$title = __('Staff', 'timeslot');

	$headers = array(
		__('ID', 'timeslot'),
		__('Name', 'timeslot'),
		__('Phone', 'timeslot'),
		__('Email', 'timeslot'),
		__('Services', 'timeslot'),
		__('Days Off', 'timeslot'),
		__('Visibility', 'timeslot'),
		__('Info', 'timeslot'),
	);

	$weekdays = array(
		__('Monday', 'timeslot'),
		__('Tuesday', 'timeslot'),
		__('Wednesday', 'timeslot'),
		__('Thursday', 'timeslot'),
		__('Friday', 'timeslot'),
		__('Saturday', 'timeslot'),
		__('Sunday', 'timeslot'),
	);

	$visibility = array(
		__('Visible', 'timeslot'),
		__('Hidden', 'timeslot'),
	);

	$edit_fields = array(
		'title' => __('staff', 'timeslot'),
		'name' => 'staff',
		'fields' => array(
			array(
				'name' => 'name',
				'type' => 'text',
				'width' => '100'
			),
			array(
				'name' => 'phone',
				'type' => 'tel',
				'width' => '50'
			),
			array(
				'name' => 'email',
				'type' => 'email',
				'width' => '50'
			),
			array(
				'name' => 'services',
				'type' => 'multiple',
				'width' => '100',
				'options' => $services
			),
			array(
				'name' => 'days-off',
				'type' => 'multiple',
				'width' => '100',
				'options' => $weekdays
			),
			array(
				'name' => 'visibility',
				'type' => 'select',
				'width' => '100',
				'options' => $visibility
			),
			array(
				'name' => 'notes',
				'type' => 'textarea',
				'width' => '100',
			),
		),
	);

	tslot_build_datatable($headers, $title);
	tslot_build_confirm_modal();
	tslot_build_edit_modal($edit_fields);

}

// Updates staff table
add_action( 'wp_ajax_tslot_update_staff', 'tslot_update_staff' );

function tslot_update_staff() {

	if (!current_user_can('manage_options')) {
		wp_die();
	}

	if (!check_ajax_referer( 'ts-staff-nonce', 'nonce' )) {
		wp_die();
	}

	global $wpdb;
	$ts_staff_table = $wpdb->prefix . 'ts_staff';
	$ts_staff_services_table = $wpdb->prefix . 'ts_staff_services';
	$ts_services_table = $wpdb->prefix . 'ts_services';

	$row_id = intval($_POST['rowid']);
	$name = sanitize_text_field($_POST['name']);
	$phone = sanitize_text_field($_POST['phone']);
	$email = sanitize_email($_POST['email']);
	$services = explode(',',sanitize_text_field($_POST['services']));
	$days_off = !empty($_POST['daysoff']) ? implode(',', array(sanitize_text_field($_POST['daysoff']))) : '';
	$visibility = sanitize_text_field($_POST['visibility']);
	$info = sanitize_text_field($_POST['info']);

	$email_exists = 
		$wpdb->get_var(
		$wpdb->prepare(
			"SELECT email 
			FROM {$ts_staff_table} 
			WHERE email = %s", 
			$email
	));

	$staff_array = array(
		'staff_name' => $name,
		'phone' => $phone,
		'email' => $email,
		'visibility' => $visibility,
		'daysoff' => $days_off,
		'info' => $info,
	);

	$is_email = !is_null($email_exists) ? true : false;

	$staff_continue = [];
	$staff_continue['continue'] = true;

	// Updates existing staff member
	if ($row_id !== 0){

		echo wp_json_encode($staff_continue);

		$wpdb->update(
			$ts_staff_table,
			$staff_array,
			array('staff_id' => $row_id)
		);

		$wpdb->delete(
			$ts_staff_services_table,
			array('staff_id' => $row_id)
		);

		foreach($services as $service){

			$service_id = 
				$wpdb->get_var(
				$wpdb->prepare(
					"SELECT service_id 
					FROM {$ts_services_table} 
					WHERE service_title = %s", 
					$service
			));

			$wpdb->insert(
				$ts_staff_services_table,
				array(
					'staff_id' => $row_id,
					'service_id' => $service_id,
					'service_title' => $service,
				)
			);

		}

	}

	else if ($is_email){

		$staff_continue['continue'] = false;
		echo wp_json_encode($staff_continue);
		wp_die();

	}

	// Adds new staff member
	else {

		echo wp_json_encode($staff_continue);

		$wpdb->insert(
			$ts_staff_table,
			$staff_array
		);

		$staff_id = $wpdb->insert_id;

		foreach($services as $service){

			$service_id = 
				$wpdb->get_var(
				$wpdb->prepare(
					"SELECT service_id 
					FROM {$ts_services_table} 
					WHERE service_title = %s", 
					$service
			));

			$wpdb->insert(
				$ts_staff_services_table,
				array(
					'staff_id' => $staff_id,
					'service_id' => $service_id,
					'service_title' => $service,
				)
			);
		}
	}

	wp_die();

}