<?php
/**
 * Gets available staff members if any was selected
 *
 * Assigns staff to scheduled appointment
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */

add_action( 'rest_api_init', function () {

	register_rest_route( 'timeslot/v1', '/ts-get-staff/', array(
			'methods' => WP_REST_Server::READABLE,
			'callback' => 'tslot_get_staff',
			'permission_callback' => '__return_true',
			'args' => array(
				'time' => array(
					'required' => true,
					'type' => 'string',
					'description' => 'Selected time',
				),
				'service' => array(
					'required' => true,
					'type' => 'string',
					'description' => 'Selected service',
				),
				'isodate' => array(
					'required' => true,
					'type' => 'string',
					'description' => 'Selected date',
				),
			)
	));

});


function tslot_get_staff($request) {

	global $wpdb;

	$selected_time = $request['time'];
	$selected_service = $request['service'];
	$iso_date = $request['isodate'];

	if (!$selected_time || !$selected_service || !$iso_date){
		return 'No Info';
	}

	$staff_names = array();
	$times_unavailable = array();

	$ts_services_table = $wpdb->prefix . 'ts_services';
	$ts_staff_services_table = $wpdb->prefix . 'ts_staff_services';
	$ts_staff_table = $wpdb->prefix . 'ts_staff';
	$ts_appt_table = $wpdb->prefix . 'ts_appointments';

	// I18n strings
	$i18n_strings = new TimeSlot\MultilangStrings();
	$visible_i18n = $i18n_strings -> visible_string;
	$approved_i18n = $i18n_strings -> approved_string;

	$lang = has_action('wpml_translate_single_string') ? apply_filters('wpml_default_language', null ) : null;
	$i18n_strings -> set_i18n_service($selected_service, $lang);
	$selected_service = $i18n_strings -> service_string;

	$available_staff = 
		$wpdb->get_results(
		$wpdb->prepare(
			"SELECT 
			{$ts_staff_table}.staff_name 
			FROM {$ts_staff_table}, {$ts_staff_services_table} 
			WHERE {$ts_staff_services_table}.service_title = %s 
			AND {$ts_staff_services_table}.staff_id = {$ts_staff_table}.staff_id 
			AND {$ts_staff_table}.visibility = %s", 
			$selected_service, $visible_i18n
	));

	$available_staff = array_column($available_staff, 'staff_name');

	foreach($available_staff as $staff){

		$ts_staff_appts = 
			$wpdb->get_results(
			$wpdb->prepare(
				"SELECT 
				DISTINCT TIME({$ts_appt_table}.start_appt) as start_time, 
				{$ts_services_table}.duration as duration, 
				{$ts_services_table}.before_service as before_service 
				FROM {$ts_appt_table}, {$ts_services_table} 
				WHERE DATE({$ts_appt_table}.start_appt) = %s 
				AND {$ts_appt_table}.staff = %s 
				AND {$ts_appt_table}.service_title = {$ts_services_table}.service_title 
				AND {$ts_appt_table}.appt_status = %s", 
				$iso_date, $staff, $approved_i18n), OBJECT);

		foreach($ts_staff_appts as $appt){

			if (!in_array($appt->start_time, $times_unavailable)){
				$times_unavailable[]= $appt->start_time;
			}

			$duration = ($appt->duration + $appt->before_service) / 60 ;
			$start_time= strtotime($appt->start_time);

			for($i=15; $i < $duration; $i+=15 ){
				$unavailable_time = strtotime("+". $i . " minutes", $start_time);

				if (!in_array($unavailable_time, $times_unavailable)){
					$times_unavailable[]= date('H:i:s', $unavailable_time);
				}
			}

		}

		if (!in_array($selected_time, $times_unavailable)){
			$staff_names[] = $staff;
		}

	}

	$random_staff = array_rand(array_flip($staff_names),1);

	$response = new WP_REST_Response( $random_staff, 200 );
	$response -> header( 'Content-type', 'application/json; charset=UTF-8' );
	return $response;
}