<?php
/**
 * Enqueues admin scripts and styles
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */

add_action('admin_enqueue_scripts', 'tslot_admin_scripts');

function tslot_admin_scripts($hook) {

	$ts_string = 'timeslot';

	if (strpos($hook, $ts_string) == false) {
		return;
	}

	wp_enqueue_style('ts-admin', TIMESLOT_URL . 'admin/css/ts-admin.min.css');
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-form');
	wp_enqueue_script('ajax-save', TIMESLOT_URL . 'admin/js/ajax-save.min.js', array('jquery', 'wp-i18n'), false, true);
	wp_add_inline_script( 'ajax-save', "jQuery('.ts-load').removeClass('ts-load');");
	wp_enqueue_style('ts-google-fonts-roboto', 'https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap', null, null, 'all');
	wp_enqueue_script('jquery-validate-min');
	wp_enqueue_script('jquery-validate-additional');
	wp_enqueue_style('select2');
	wp_enqueue_script('select2');
	wp_enqueue_script('ts-select2', TIMESLOT_URL . 'admin/js/select2.min.js', array('jquery', 'wp-i18n'), false, true);
	wp_set_script_translations('ts-select2', 'timeslot', TIMESLOT_PATH . 'languages');
	wp_set_script_translations('ajax-save', 'timeslot', TIMESLOT_PATH . 'languages');

	$ts_business_pg = 'time-slot_page_timeslot-business';
	$ts_appts_pg = 'time-slot_page_timeslot-appointments';
	$ts_general_pg = 'time-slot_page_timeslot-general';
	$ts_dashboard_pg = 'toplevel_page_timeslot';

	$datepicker_format = new TimeSlot\DatepickerFormat(get_option('date_format'));
	$datepicker_format = $datepicker_format -> get_format();

	$start_of_week = get_option('start_of_week');
	$time_format = get_option('time_format');
	$site_timezone = get_option('timezone_string');
	$locale = get_locale();
	$payment_options = get_option('timeslot-payment-methods-tab');
	$currency_code = $payment_options['currency'];

	if(!array_key_exists('tab', $_GET)){
		switch($hook){

			case $ts_appts_pg:
				$_GET['tab'] = 'appointments';
				break;

			case $ts_business_pg:
				$_GET['tab'] = 'services';
				break;

			case $ts_dashboard_pg:
			case $ts_general_pg:
				wp_enqueue_script('copy-shortcode');
				break;

			default:
				break;

		}
	}

	if (!isset($_GET['tab'])){
		return;
	}

	if ($hook === $ts_appts_pg || $_GET['tab'] === 'services' || $_GET['tab'] === 'staff'){

		wp_enqueue_style('ts-datatables-main', TIMESLOT_URL . 'inc/datatables/datatables.min.css');
		wp_enqueue_script('ts-datatables-main', TIMESLOT_URL . 'inc/datatables/datatables.min.js', array('jquery'));
		wp_enqueue_script('micromodal');
		wp_enqueue_script('ts-datatable-defaults', TIMESLOT_URL . 'admin/js/tables/defaults.min.js', array('jquery', 'wp-i18n'));
		wp_enqueue_style('ts-print', TIMESLOT_URL . 'admin/css/ts-print.min.css', null, false, 'print');
		wp_enqueue_script('all-tables', TIMESLOT_URL . 'admin/js/tables/all-tables.min.js', array('jquery', 'wp-i18n'), false, true);

		$company_options = get_option('timeslot-company-tab');
		$company_name = $company_options['company-name'] ?? '';

		wp_localize_script('ts-datatable-defaults', 'tsao',
			array(
				'ajaxurl' => admin_url('admin-ajax.php'),
				'companyname' => $company_name,
			)
		);

		wp_localize_script('all-tables', 'tsdatatables',
			array(
				'tsdtnonce' => wp_create_nonce('ts-datatables-nonce'),
				'tab' => sanitize_text_field($_GET['tab']),
			)
		);

		wp_set_script_translations('ts-datatable-defaults', 'timeslot', TIMESLOT_PATH . 'languages');
		wp_set_script_translations('all-tables', 'timeslot', TIMESLOT_PATH . 'languages');

	}

	switch ($_GET['tab']){

		case 'company':
			wp_enqueue_media();
			wp_enqueue_script('media-upload-button', TIMESLOT_URL . 'admin/js/media-uploader.min.js', array('jquery', 'wp-i18n'), false, true);
			wp_enqueue_script('micromodal');
			wp_set_script_translations('media-upload-button', 'timeslot', TIMESLOT_PATH . 'languages');
			break;

		case 'payments':
			wp_enqueue_script('payments', TIMESLOT_URL . 'admin/js/tables/payments.min.js', array('jquery', 'wp-i18n'));
			wp_set_script_translations('payments', 'timeslot', TIMESLOT_PATH . 'languages');
			wp_localize_script('payments', 'tspayments',
				array(
					'tspaymentnonce' => wp_create_nonce('ts-payment-nonce'),
					'tsPaymentPermissionNonce' => wp_create_nonce('wp_rest'),
					'paymentdata' => rest_url('timeslot/v1/ts-payments'),
					'locale' => $locale,
					'currency' => $currency_code,
					'timezone' => $site_timezone,
				)
			);
			break;

		case 'general':
			wp_enqueue_script('copy-shortcode');
			break;

		case 'coupons':
			wp_enqueue_script('coupons', TIMESLOT_URL . 'admin/js/tables/coupons.min.js', array('jquery', 'wp-i18n'));
			wp_set_script_translations('coupons', 'timeslot', TIMESLOT_PATH . 'languages');
			wp_localize_script('coupons', 'tscoupons',
				array(
				'tscouponsnonce' => wp_create_nonce('ts-coupons-nonce'),
				'tsCouponPermissionNonce' => wp_create_nonce('wp_rest'),
				'coupondata' => rest_url('timeslot/v1/ts-coupons'),
				'locale' => $locale,
				'currency' => $currency_code,
				)
			);
			break;

		case 'customers':
			wp_enqueue_script('customers', TIMESLOT_URL . 'admin/js/tables/customers.min.js', array('jquery', 'wp-i18n'));
			wp_set_script_translations('customers', 'timeslot', TIMESLOT_PATH . 'languages');
			wp_localize_script('customers', 'tscustomers',
				array(
					'tscustomersnonce' => wp_create_nonce('ts-customers-nonce'),
					'tsCustomerPermissionNonce' => wp_create_nonce('wp_rest'),
					'customerdata' => rest_url('timeslot/v1/ts-customers'),
				)
			);
			break;

		case 'staff':
			wp_enqueue_script('staff', TIMESLOT_URL . 'admin/js/tables/staff.min.js', array('jquery', 'wp-i18n'));
			wp_set_script_translations('staff', 'timeslot', TIMESLOT_PATH . 'languages');
			wp_localize_script('staff', 'tsstaff',
				array(
					'tsstaffnonce' => wp_create_nonce('ts-staff-nonce'),
					'tsStaffPermissionNonce' => wp_create_nonce('wp_rest'),
					'staffdata' => rest_url('timeslot/v1/ts-staff'),
				)
			);
			break;

		case 'email':
			wp_enqueue_script('hide-email', TIMESLOT_URL . 'admin/js/hide-emails.min.js', array('jquery'));
			break;

		case 'appearance':
			wp_enqueue_script('ts-spectrum-script', TIMESLOT_URL . 'inc/spectrum/spectrum.min.js');
			wp_enqueue_style('ts-spectrum-style', TIMESLOT_URL . 'inc/spectrum/spectrum.min.css');
			wp_enqueue_script('color-picker', TIMESLOT_URL . 'admin/js/color-picker.min.js', array('ts-spectrum-script', 'jquery', 'wp-i18n'), false, true);
			wp_set_script_translations('color-picker', 'timeslot', TIMESLOT_PATH . 'languages');
			break;

		case 'business-hours':
			wp_enqueue_script('hide-hours', TIMESLOT_URL . 'admin/js/business-hours.min.js');
			wp_enqueue_script('repeaters');
			wp_enqueue_script('timepicker');
			wp_enqueue_script('jquery-ui-datepicker');
			wp_enqueue_script('info-toggle');
			wp_localize_script('hide-hours', 'tshours',
				array(
					'dateformat' => $datepicker_format,
					'startofweek' => $start_of_week,
					'timeformat' => $time_format,
				)
			);
			wp_localize_script('repeaters', 'tsrepeat',
				array(
					'dateformat' => $datepicker_format,
					'startofweek' => $start_of_week,
				)
			);
			break;

		case 'services':
			wp_enqueue_script('services', TIMESLOT_URL . 'admin/js/tables/services.min.js', array('jquery', 'wp-i18n'));
			wp_enqueue_script('duration-picker', TIMESLOT_URL . 'admin/js/libs/duration-picker.min.js', array('jquery'), false, true);
			wp_enqueue_script('repeaters');
			wp_localize_script('services', 'tsservices',
				array(
					'tsservicesnonce' => wp_create_nonce('ts-services-nonce'),
					'tsServicePermissionNonce' => wp_create_nonce('wp_rest'),
					'servicedata' => rest_url('timeslot/v1/ts-services'),
					'locale' => $locale,
					'currency' => $currency_code,
				)
			);
			wp_set_script_translations('services', 'timeslot', TIMESLOT_PATH . 'languages');
			break;

		case 'appointments':
			wp_enqueue_script('appointments', TIMESLOT_URL . 'admin/js/tables/appointments.min.js', array('jquery', 'wp-i18n'));
			wp_enqueue_script('timepicker');
			wp_enqueue_script('jquery-ui-datepicker');
			wp_localize_script('appointments', 'tsappts',
				array(
					'tsappointmentsnonce' => wp_create_nonce('ts-appointments-nonce'),
					'tsApptPermissionNonce' => wp_create_nonce('wp_rest'),
					'apptdata' => rest_url('timeslot/v1/ts-appointments'),
					'startofweek' => $start_of_week,
					'locale' => $locale,
					'dateformat' => $datepicker_format,
					'timeformat' => $time_format,
					'timezone' => $site_timezone,
				)
			);
			wp_set_script_translations('appointments', 'timeslot', TIMESLOT_PATH . 'languages');
			break;

		default:
			break;

	}

}