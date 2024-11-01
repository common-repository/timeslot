<?php
/**
 * Displays customer datatable
 *
 * Selects customer data from database and displays
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

// Registers rest route and returns customers datatable data
$customer_data = new TimeSlot\CustomerData('customers');

// Displays customer datatable and editor modal
function tslot_customers_tab_init(){

	$title = __('Customers', 'timeslot');

	$headers = array(
		__('ID', 'timeslot'),
		__('Name', 'timeslot'),
		__('Email', 'timeslot'),
		__('Phone', 'timeslot'),
	);

	$edit_fields = array(
		'title' => __('customer', 'timeslot'),
		'name' => 'customer',
		'fields' => array(
			array(
				'label' => __('Name', 'timeslot'),
				'name' => 'customer-name',
				'type' => 'text',
				'width' => '100'
			),
			array(
				'label' => __('Email', 'timeslot'),
				'name' => 'customer-email',
				'type' => 'email',
				'width' => '50'
			),
			array(
				'label' => __('Phone', 'timeslot'),
				'name' => 'customer-phone',
				'type' => 'tel',
				'width' => '50'
			),
		),
	);

	tslot_build_datatable($headers, $title);
	tslot_build_confirm_modal();
	tslot_build_edit_modal($edit_fields);

}

// Updates appointment table
add_action( 'wp_ajax_tslot_update_customers', 'tslot_update_customers' );

function tslot_update_customers() {

	if (!current_user_can('manage_options')) {
		wp_die();
	}

	if (!check_ajax_referer('ts-customers-nonce', 'nonce')) {
		wp_die();
	}

	global $wpdb;
	$ts_customers_table = $wpdb->prefix . 'ts_customers';
	$ts_appt_table = $wpdb->prefix . 'ts_appointments';
	$ts_payment_table = $wpdb->prefix . 'ts_payments';
	$row_id = intval($_POST['rowid']);
	$customer_name = sanitize_text_field($_POST['customername']);
	$phone = sanitize_text_field($_POST['phone']);
	$email = sanitize_email($_POST['email']);

	$customers_array = array(
		'customer_name' => $customer_name,
		'phone' => $phone,
		'email' => $email,
	);

	$appt_array = array(
		'customer_name' => $customer_name,
	);

	// Updates existing customer
	if ($row_id !== 0){

		$wpdb->update(
			$ts_customers_table,
			$customers_array,
			array('customer_id' => $row_id)
		);

		$wpdb->update(
			$ts_appt_table,
			$appt_array,
			array('customer_id' =>  $row_id)
		);

		$wpdb->update(
			$ts_payment_table,
			$appt_array,
			array('customer_id' =>  $row_id)
		);
	}

	// Adds new customer
	else {
		$wpdb->insert(
			$ts_customers_table,
			$customers_array
		);
	}

	wp_die();
}