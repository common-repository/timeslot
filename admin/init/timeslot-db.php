<?php

/**
* Time Slot Database Tables
*
* Creates database tables for Time Slot plugin.
*
* @link https://timeslotplugins.com
*
* @package Time Slot
* @subpackage Admin
* @since 1.0.0
*/

// Exit if accessed directly
if (!defined('ABSPATH')){
	exit;
}

register_activation_hook( TIMESLOT_FILE, 'tslot_create_db_tables' );

global $ts_db_version;
$ts_db_version = '1.0.5';

function tslot_create_db_tables() {

	global $wpdb;
	global $ts_db_version;

	$ts_appt_table = $wpdb->prefix . 'ts_appointments';
	$ts_customer_table = $wpdb->prefix . 'ts_customers';
	$ts_services_table = $wpdb->prefix . 'ts_services';
	$ts_staff_table = $wpdb->prefix . 'ts_staff';
	$ts_staff_services_table = $wpdb->prefix . 'ts_staff_services';
	$ts_payment_table = $wpdb->prefix . 'ts_payments';
	$ts_coupons_table = $wpdb->prefix . 'ts_coupons';

	$charset_collate = $wpdb->get_charset_collate();
	require_once ( ABSPATH . 'wp-admin/includes/upgrade.php' );

	// Customer table
	if ( $wpdb->get_var("SHOW TABLES LIKE '$ts_customer_table'") != $ts_customer_table ) {
		$db_customer = "CREATE TABLE IF NOT EXISTS $ts_customer_table (
		customer_id int(11) NOT NULL AUTO_INCREMENT,
		customer_name varchar(55) NOT NULL,
		phone varchar(20) NOT NULL,
		email varchar(191) NOT NULL,
		PRIMARY KEY  (customer_id),
		UNIQUE (email)
		) $charset_collate;";
		dbDelta( $db_customer );
	}

	// Appointment table
	if ( $wpdb->get_var("SHOW TABLES LIKE '$ts_appt_table'") != $ts_appt_table ) {
		$db_appt = "CREATE TABLE IF NOT EXISTS $ts_appt_table (
		appt_id int(11) NOT NULL AUTO_INCREMENT,
		created datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
		service_title varchar(55) NOT NULL,
		staff varchar(55) NOT NULL,
		start_appt datetime NOT NULL,
		appt_status varchar(20) NOT NULL,
		customer_name varchar(55) NOT NULL,
		customer_id int(11) NOT NULL,
		FOREIGN KEY  (customer_id) REFERENCES $ts_customer_table(customer_id) ON DELETE CASCADE,
		PRIMARY KEY  (appt_id)
		) $charset_collate;";
		dbDelta( $db_appt );
	}

	// Services table
	if ( $wpdb->get_var("SHOW TABLES LIKE '$ts_services_table'") != $ts_services_table ) {
		$db_services = "CREATE TABLE IF NOT EXISTS $ts_services_table (
		service_id int(11) NOT NULL AUTO_INCREMENT,
		service_title varchar(55) NOT NULL,
		visibility varchar(20) NOT NULL,
		price int(20) NOT NULL,
		duration int(8) NOT NULL,
		before_service int(8) NOT NULL,
		category varchar(55) NOT NULL,
		info varchar(255) NOT NULL,
		PRIMARY KEY  (service_id)
		) $charset_collate;";
		dbDelta( $db_services );
	}

	// Staff table
	if ( $wpdb->get_var("SHOW TABLES LIKE '$ts_staff_table'") != $ts_staff_table ) {
		$db_staff = "CREATE TABLE IF NOT EXISTS $ts_staff_table (
		staff_id int(11) NOT NULL AUTO_INCREMENT,
		staff_name varchar(55) NOT NULL,
		phone varchar(20) NOT NULL,
		email varchar(191) NOT NULL,
		visibility varchar(20) NOT NULL,
		daysoff varchar(255),
		info varchar(255) NOT NULL,
		PRIMARY KEY  (staff_id),
		UNIQUE (email)
		) $charset_collate;";
		dbDelta( $db_staff );
	}

	// Staff Services table
	if ( $wpdb->get_var("SHOW TABLES LIKE '$ts_staff_services_table'") != $ts_staff_services_table ) {
		$db_staff_service = "CREATE TABLE IF NOT EXISTS $ts_staff_services_table (
		staff_service_id int(11) NOT NULL AUTO_INCREMENT,
		staff_id int(11) NOT NULL,
		service_id int(11) NOT NULL,
		service_title varchar(55) NOT NULL,
		FOREIGN KEY  (staff_id) REFERENCES $ts_staff_table(staff_id) ON DELETE CASCADE,
		FOREIGN KEY  (service_id) REFERENCES $ts_services_table(service_id) ON DELETE CASCADE,
		PRIMARY KEY  (staff_service_id)
		) $charset_collate;";
		dbDelta( $db_staff_service );
	}

	// Payment table
	if ( $wpdb->get_var("SHOW TABLES LIKE '$ts_payment_table'") != $ts_payment_table ) {
		$db_payments = "CREATE TABLE IF NOT EXISTS $ts_payment_table (
		payment_id int(11) NOT NULL AUTO_INCREMENT,
		created datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
		customer_name varchar(55) NOT NULL,
		source varchar(55) NOT NULL,
		amount int(20) NOT NULL,
		payment_status varchar(20) NOT NULL,
		coupon varchar(55) NOT NULL,
		capture_id varchar(50),
		appt_id int(11),
		customer_id int(11),
		FOREIGN KEY  (appt_id) REFERENCES $ts_appt_table(appt_id) ON DELETE SET NULL,
		FOREIGN KEY  (customer_id) REFERENCES $ts_customer_table(customer_id) ON DELETE SET NULL,
		PRIMARY KEY  (payment_id)
		) $charset_collate;";
		dbDelta( $db_payments );
	}

	// Coupons table
	if ( $wpdb->get_var("SHOW TABLES LIKE '$ts_coupons_table'") != $ts_coupons_table ) {
		$db_coupons = "CREATE TABLE IF NOT EXISTS $ts_coupons_table (
		coupon_id int(11) NOT NULL AUTO_INCREMENT,
		coupon_code varchar(55) NOT NULL,
		discount_type varchar(20) NOT NULL,
		discount_amount int(4) NOT NULL,
		coupon_status varchar(20) NOT NULL,
		PRIMARY KEY  (coupon_id)
		) $charset_collate;";
		dbDelta( $db_coupons );
	}

	if ( !get_option('timeslot_db_version') ) {
		update_option( 'timeslot_db_version', $ts_db_version );
	}
}