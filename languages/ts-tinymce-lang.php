<?php
/**
 * TinyMCE button localization
 *
 * Returns strings available for translation.
 * Based on wp-includes/js/tinymce/langs/wp-langs.php
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.1
 * 
 */

if ( ! defined( 'ABSPATH' ) ){
	exit;
}

if ( ! class_exists( '_WP_Editors' ) ){
	require ( ABSPATH . WPINC . '/class-wp-editor.php' );
}

function tslot_tinymce_button_translation() {

	$strings = array(
		'companylogo' => __('Company Logo', 'timeslot'),
		'companyname' => __('Company Name', 'timeslot'),
		'company' => __('Company', 'timeslot'),
		'service' => __('Service', 'timeslot'),
		'staff' => __('Staff', 'timeslot'),
		'appointmentdate' => __('Appointment Date', 'timeslot'),
		'appointmenttime' => __('Appointment Time', 'timeslot'),
		'cancel' => __('Cancel', 'timeslot'),
		'tsoptions' => __('Time Slot Options', 'timeslot'),
		'appointment' => __('Appointment', 'timeslot'),
		'client' => __('Client', 'timeslot'),
		'select' => __('Select', 'timeslot'),
		'clientname' => __('Client Name', 'timeslot'),
		'clientemail' => __('Client Email', 'timeslot'),
		'clientphone' => __('Client Phone', 'timeslot'),
		'companywebsite' => __('Company Website', 'timeslot'),
		'companyemail' => __('Company Email', 'timeslot'),
		'companyphone' => __('Company Phone', 'timeslot'),
		'companyaddress' => __('Company Address', 'timeslot'),
		'servicename' => __('Service Name', 'timeslot'),
		'serviceprice' => __('Service Price', 'timeslot'),
		'staffname' => __('Staff Name', 'timeslot'),
		'staffemail' => __('Staff Email', 'timeslot'),
		'staffphone' => __('Staff Phone', 'timeslot'),
		'insert' => __('Insert', 'timeslot'),
		'sendtest' => __('Send Test Email', 'timeslot'),
		'emailrecipient' => __('Email recipient', 'timeslot'),
		'send' => __('Send', 'timeslot'),
		'emailtesterror' => __('Email Test Error', 'timeslot'),
		'insertinfo' => __('Insert appointment info', 'timeslot'),
		'sendatest' => __('Send a test email', 'timeslot'),
		'messagesent' => __('Message Sent', 'timeslot'),
	);

	$locale = _WP_Editors::$mce_locale;
	$translated = 'tinyMCE.addI18n("' . $locale . '.timeslotButton", ' . json_encode( $strings ) . ");\n";

	return $translated;
}

$strings = tslot_tinymce_button_translation();