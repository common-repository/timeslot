<?php
/**
 * Creates Time Slot plugin admin pages
 * and WordPress sidebar menu items
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.1.8
 * 
 */

// Exit if accessed directly
if (!defined('ABSPATH')){
	exit;
}

/**
 * Includes settings pages
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.1.9
 * 
 */
add_action( 'plugins_loaded', 'tslot_include_admin_pages' );

function tslot_include_admin_pages() {
	require (TIMESLOT_PATH . 'admin/pages/tables/appointments.php');
	require (TIMESLOT_PATH . 'admin/pages/tables/coupons.php');
	require (TIMESLOT_PATH . 'admin/pages/tables/customers.php');
	require (TIMESLOT_PATH . 'admin/pages/tables/payments.php');
	require (TIMESLOT_PATH . 'admin/pages/tables/services.php');
	require (TIMESLOT_PATH . 'admin/pages/tables/staff.php');
	require (TIMESLOT_PATH . 'admin/pages/tables/build-table.php');
	require (TIMESLOT_PATH . 'admin/pages/tables/data-delete.php');
	require (TIMESLOT_PATH . 'admin/pages/settings/appearance.php');
	require (TIMESLOT_PATH . 'admin/pages/settings/company.php');
	require (TIMESLOT_PATH . 'admin/pages/settings/general.php');
	require (TIMESLOT_PATH . 'admin/pages/settings/hours.php');
	require (TIMESLOT_PATH . 'admin/pages/settings/import-export.php');
	require (TIMESLOT_PATH . 'admin/pages/settings/payment.php');
}

/**
 * Sets default email body values on activation
 * and includes email settings file
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */
require (TIMESLOT_PATH . 'admin/pages/settings/email.php');
register_activation_hook( TIMESLOT_FILE, 'tslot_email_body_defaults' );

/**
 * Adds pages to admin menu
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.1.8
 * 
 */
add_action( 'admin_menu', 'tslot_create_submenu' );

function tslot_create_submenu(){

	$icon = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz48c3ZnIGlkPSJMYXllcl8yIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCI+PGRlZnM+PHN0eWxlPi5jbHMtMXtmaWxsOiNhN2FhYWQ7fTwvc3R5bGU+PC9kZWZzPjxnIGlkPSJpY29uIj48cGF0aCBjbGFzcz0iY2xzLTEiIGQ9Ik01LjAxLDRjLjU1LDAsMS0uNDUsMS0xVjFjMC0uNTUtLjQ1LTEtMS0xcy0xLC40NS0xLDFWM2MwLC41NSwuNDUsMSwxLDFaIi8+PHBhdGggY2xhc3M9ImNscy0xIiBkPSJNMTkuMDEsNGMuNTUsMCwxLS40NSwxLTFWMWMwLS41NS0uNDUtMS0xLTFzLTEsLjQ1LTEsMVYzYzAsLjU1LC40NSwxLDEsMVoiLz48Zz48cGF0aCBjbGFzcz0iY2xzLTEiIGQ9Ik0yMSwydjFjMCwxLjEtLjksMi0yLDJzLTItLjktMi0yVjJIN3YxYzAsMS4xLS45LDItMiwycy0yLS45LTItMlYySDBWNi41NUgyNFYyaC0zWiIvPjxwYXRoIGNsYXNzPSJjbHMtMSIgZD0iTTAsMjRIMjRWNy43SDBWMjRabTEyLjE2LTYuNTloMS41OGMwLC42LC4yLDEuMDQsLjYxLDEuMzMsLjQxLC4zLC45NSwuNDQsMS42MSwuNDQsLjYsMCwxLjA3LS4xMiwxLjQxLS4zNiwuMzMtLjI0LC41LS41NywuNS0uOTlzLS4xNS0uNzMtLjQ1LS45OWMtLjMtLjI2LS44NC0uNDktMS42LS43LTEuMS0uMjgtMS45NC0uNjYtMi41My0xLjE0LS41OC0uNDctLjg4LTEuMDktLjg4LTEuODVzLjMzLTEuNDQsLjk4LTEuOTVjLjY1LS41MSwxLjQ5LS43NiwyLjUyLS43NnMxLjk0LC4yOSwyLjU5LC44NmMuNjUsLjU4LC45NiwxLjI4LC45NCwyLjA5di4wNGgtMS41OGMwLS41LS4xNy0uOTEtLjUxLTEuMjItLjM0LS4zMS0uODItLjQ3LTEuNDUtLjQ3cy0xLjA2LC4xMy0xLjM4LC4zOS0uNDgsLjYtLjQ4LDEuMDFjMCwuMzcsLjE4LC42OCwuNTIsLjkyLC4zNSwuMjUsLjkyLC40OCwxLjczLC43LDEuMDUsLjI4LDEuODUsLjY3LDIuMzksMS4xNywuNTUsLjQ5LC44MiwxLjEzLC44MiwxLjksMCwuODEtLjMyLDEuNDYtLjk3LDEuOTQtLjY2LC40OC0xLjUxLC43Mi0yLjU3LC43MnMtMS45Mi0uMjctMi42OS0uODFjLS43Ny0uNTUtMS4xNC0xLjI5LTEuMTItMi4yM3YtLjA0Wk0zLjg3LDEwLjU4aDcuNTR2MS4zMWgtMi45NXY4LjQ2aC0xLjYzVjExLjg5SDMuODd2LTEuMzFaIi8+PC9nPjwvZz48L3N2Zz4=';

	add_menu_page(
		'Time Slot',
		'Time Slot',
		'manage_options',
		'timeslot',
		'tslot_dashboard_html',
		$icon,
		99
	);

	add_submenu_page(
		'timeslot',
		'Time Slot',
		__('Overview', 'timeslot'),
		'manage_options',
		'timeslot'
	);

	$submenu_pages = array(
		array(
			'title' => __('Appointments', 'timeslot'),
			'slug' => 'appointments'
		),
		array(
			'title' => __('Business Settings', 'timeslot'),
			'slug' => 'business'
		),
		array(
			'title' => __('General Settings', 'timeslot'),
			'slug' => 'general'
		)
	);

	foreach($submenu_pages as $page){

		add_submenu_page(
			'timeslot',
			$page['title'],
			$page['title'],
			'manage_options',
			'timeslot-' . $page['slug'],
			'tslot_' . $page['slug'] . '_html'
		);
	}
}

/**
 * Creates appointments menu page and tabs
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */
function tslot_appointments_html() {

	$nav = array(
		'appointments',
		'customers',
		'payments',
		'coupons',
	);

	$appointment_pages = new TimeSlot\AdminPages('appointments', $nav);
}

/**
 * Creates business settings menu page and tabs
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */
function tslot_business_html() {

	$nav = array(
		'services',
		'staff',
		'email',
		'company',
		'business-hours',
	);

	$business_pages = new TimeSlot\AdminPages('business', $nav);
}

/**
 * Creates general settings menu page and tabs
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */
function tslot_general_html() {

	$nav = array(
		'general',
		'payment-methods',
		'appearance',
		'import-export',
	);

	$general_pages = new TimeSlot\AdminPages('general', $nav);
}

/**
 * Creates main dashboard page
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */
function tslot_dashboard_html() {

	if (! current_user_can('manage_options')) {
		return;
	}

	require (TIMESLOT_PATH . 'admin/pages/dashboard.php');
}