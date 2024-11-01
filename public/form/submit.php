<?php
/**
 * Completes appointment on successful payment
 *
 * Inserts appointment, customer, and payment info
 * into database AJAX action referenced from public/js/.
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */

add_action( 'wp_ajax_tslot_install_data', 'tslot_install_data' );
add_action( 'wp_ajax_nopriv_tslot_install_data', 'tslot_install_data' );

function tslot_install_data() {

	if (!isset( $_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'ts_submit_form' )){
		$failed = __('Form verification failed', 'timeslot');
		error_log(print_r($failed, true));
		http_response_code(400);
		die( wp_json_encode(array('error' => 'Bad Request', 'message' => $failed)));
	}

	global $wpdb;
	$ts_appt_table = $wpdb->prefix . 'ts_appointments';
	$ts_customer_table = $wpdb->prefix . 'ts_customers';
	$ts_payment_table = $wpdb->prefix . 'ts_payments';

	// I18n strings
	$i18n_strings = new TimeSlot\MultilangStrings();
	$payment_status = $i18n_strings -> complete_string;
	$appt_status = $i18n_strings -> approved_string;
	$no_coupon = $i18n_strings -> none_string;

	$start = sanitize_text_field($_POST['start']);
	$start = new DateTime($start);
	$start = $start->format('c');

	$service = sanitize_text_field($_POST['service']);
	$staff = $_POST['staff'] ? sanitize_text_field($_POST['staff']) : '';
	$email = sanitize_email($_POST['email']);
	$phone = sanitize_text_field($_POST['phone']);
	$name = sanitize_text_field($_POST['name']);
	$source = sanitize_text_field($_POST['source']);
	$coupon = !empty($_POST['coupon']) ? sanitize_text_field($_POST['coupon']) : $no_coupon;
	$capture = sanitize_text_field($_POST['capture_id']);

	$price = new TimeSlot\PriceFromService($service);
	$price -> get_price();
	$amount = $price -> service_price;

	// Adjusts price for coupon codes
	if (!empty($_POST['coupon'])){
		$discount_price = new TimeSlot\DiscountPrice();
		$discount_price -> get_discount_info($coupon);
		$discount_price -> get_discount_price($amount);
		$amount = $discount_price->price;
	}

	// Inserts customer info into database
	$customer_array = array(
		'customer_name' => $name,
		'email' => $email,
		'phone' => $phone,
	);

	$cust_email = 
		$wpdb->get_var(
		$wpdb->prepare(
			"SELECT email 
			FROM {$ts_customer_table}
			WHERE email = %s",
			$email
		)
	);

	// Updates existing customer, email already exists
	if ($cust_email){
		$wpdb->update(
			$ts_customer_table,
			$customer_array,
			array('email' =>  $email)
		);

		$customer_id = 
			$wpdb->get_var(
			$wpdb->prepare(
				"SELECT customer_id 
				FROM {$ts_customer_table} 
				WHERE email = %s",
				$email
		));

		$wpdb->update(
			$ts_appt_table,
			array('customer_name' => $name),
			array('customer_id' =>  $customer_id)
		);

		$wpdb->update(
			$ts_payment_table,
			array('customer_name' => $name),
			array('customer_id' =>  $customer_id)
		);
	}

	// Adds new customer
	else {
		$wpdb->insert(
			$ts_customer_table,
			$customer_array
		);

		$customer_id = $wpdb->insert_id;
	}

	// Inserts appointment info into database
	$wpdb->insert(
		$ts_appt_table,
		array(
			'created' => current_time( 'mysql' ),
			'customer_name' => $name,
			'service_title' => $service,
			'staff' => $staff,
			'start_appt' => $start,
			'appt_status' => $appt_status,
			'customer_id' => $customer_id,
		)
	);

	$appt_id = $wpdb->insert_id;

	// Inserts payment info into database
	$wpdb->insert(
		$ts_payment_table,
		array(
			'created' => current_time( 'mysql' ),
			'customer_name' => $name,
			'source' => $source,
			'amount' => $amount,
			'payment_status' => $payment_status,
			'coupon' => $coupon,
			'capture_id' => $capture,
			'appt_id' => $appt_id,
			'customer_id' => $customer_id,
		)
	);

	wp_die();
}