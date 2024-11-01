<?php
/**
 * Gets staff members after service choice
 *
 * Selects staff members from database after service
 * is selected from front end form. Used to populate
 * staff select options. AJAX action referenced from
 * public/js/ts-form.js.
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */

add_action( 'rest_api_init', function () {
	register_rest_route( 'timeslot/v1', '/ts-staff-available/', array(
			'methods' => WP_REST_Server::READABLE,
			'callback' => 'tslot_staff_from_service',
			'permission_callback' => '__return_true',
			'args' => array(
				'service' => array(
					'required' => true,
					'type' => 'string',
					'description' => 'Selected service',
				),
			)
	) );
} );

function tslot_staff_from_service($request) {

	global $wpdb;
	$ts_staff_table = $wpdb->prefix . 'ts_staff';
	$ts_staff_services_table = $wpdb->prefix . 'ts_staff_services';

	$chosen_service = $request['service'];

	// I18n strings
	$i18n_strings = new TimeSlot\MultilangStrings();
	$visible_i18n = $i18n_strings -> visible_string;

	$ts_staff = 
		$wpdb->get_results(
		$wpdb->prepare(
			"SELECT 
			{$ts_staff_table}.staff_name 
			FROM {$ts_staff_table}, {$ts_staff_services_table} 
			WHERE {$ts_staff_table}.staff_id = {$ts_staff_services_table}.staff_id 
			AND {$ts_staff_services_table}.service_title = %s 
			AND visibility = %s", 
			$chosen_service, $visible_i18n), OBJECT
	);

	if(!$ts_staff){
		return new WP_REST_Response(
			array(
				'status' => 404,
				'response' => __('There are no active Time Slot staff members', 'timeslot'),
			),
			404
		);
	}

	$staff_count = 
		$wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT({$ts_staff_table}.staff_id) 
			FROM {$ts_staff_table}, {$ts_staff_services_table}
			WHERE {$ts_staff_table}.staff_id = {$ts_staff_services_table}.staff_id 
			AND {$ts_staff_services_table}.service_title = %s
			AND visibility = %s",
			$chosen_service, $visible_i18n
	));

	$staff_available =[
		[
			'id' => '',
			'text' => ''
		],
	];

	if($staff_count != 1){
		$staff_available[]= array(
			'id' => esc_html__('Any Staff Member', 'timeslot'),
			'text' => esc_html__('Any', 'timeslot')
		);
	}

	foreach($ts_staff as $staff_option){
		$staff_available[]= array(
			'id' => $staff_option->staff_name,
			'text' => $staff_option->staff_name,
		);
	}

	$response = new WP_REST_Response( $staff_available, 200 );
	$response -> header( 'Content-type', 'application/json; charset=UTF-8' );
	return $response;
}