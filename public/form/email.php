<?php
/**
 * Sends appointment confirmation emails
 *
 * Sends email, creates and attaches ICS file
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

// Sends email to customers or staff
add_action( 'wp_ajax_tslot_appt_email', 'tslot_appt_email' );
add_action( 'wp_ajax_nopriv_tslot_appt_email', 'tslot_appt_email' );

function tslot_appt_email(){

	$count_staff = new TimeSlot\CountStaff();
	$is_staff = $count_staff -> is_staff();

	$email_options = get_option('timeslot-email-tab');
	$customer_approved = $email_options['customer-email-approved'] ?? null;
	$staff_approved = isset($email_options['staff-email-approved']) && $is_staff ? $email_options['staff-email-approved'] : null;

	if (!$customer_approved && !$staff_approved){
		return;
	}

	$staff = $is_staff ? sanitize_text_field($_POST['staff']) : '';
	$date = sanitize_text_field($_POST['date']);
	$time = sanitize_text_field($_POST['time']);
	$customer_name = sanitize_text_field($_POST['name']);
	$customer_phone = sanitize_text_field($_POST['phone']);
	$customer_email = sanitize_email($_POST['email']);
	$service = sanitize_text_field($_POST['service']);

	$email_vars = array(
		'date' => $date,
		'time' => $time,
		'customer_name' => $customer_name,
		'customer_phone' => $customer_phone,
		'customer_email' => $customer_email,
		'service' => $service,
		'staff' => $staff
	);

	// Send approved email to customer
	if ($customer_approved){

		$customer_approved_subject = $email_options['customer-email-approved-subject'];
		$customer_approved_tinymce = $email_options['customer-email-approved-tinymce'];

		$customer_email_vars = $email_vars;
		$customer_email_vars['body'] = $customer_approved_tinymce;

		$ts_email = new TimeSlot\EmailReplacements($customer_email_vars);
		$ts_email -> tslot_ics_setup();
		$to = $customer_name . '<' . $customer_email . '>';

		wp_mail( $to, $customer_approved_subject, $ts_email -> message, $ts_email -> headers);
		$ts_email -> tslot_clear_ics();
	}

	// Send approved email to staff
	if ($staff_approved && $is_staff){

		$staff_approved_subject = $email_options['staff-email-approved-subject'];
		$staff_approved_tinymce = $email_options['staff-email-approved-tinymce'];

		$staff_email_vars = $email_vars;
		$staff_email_vars['body'] = $staff_approved_tinymce;

		$ts_email = new TimeSlot\EmailReplacements($staff_email_vars);
		$to = $staff . '<' . $ts_email -> staff_email . '>';

		wp_mail( $to, $staff_approved_subject, $ts_email -> message, $ts_email -> headers );
	}

	wp_die();
}