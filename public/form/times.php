<?php
/**
 * Gets available appointment times after date choice
 *
 * Populates time options in select after date
 * is selected from front end form. Action referenced from
 * public/js/ts-form.js.
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */

add_action( 'rest_api_init', function () {
	register_rest_route( 'timeslot/v1', '/ts-times/', array(
		'methods' => WP_REST_Server::READABLE, //GET
		'callback' => 'tslot_times_from_date',
		'permission_callback' => '__return_true',
		'args' => array(
			'service' => array(
				'required' => true,
				'type' => 'string',
				'description' => 'Selected service',
			),
			'staff' => array(
				'required' => false,
				'type' => 'string',
				'description' => 'Selected staff member',
			),
			'day' => array(
				'required' => true,
				'type' => 'string',
				'description' => 'Selected day of week',
			),
			'isodate' => array(
				'required' => true,
				'type' => 'string',
				'description' => 'Selected date',
			),
		)
	));
});

function tslot_times_from_date($request) {

	global $wpdb;
	$selected_service = $request['service'];
	$selected_staff = $request['staff'];
	$appt_day = $request['day'];
	$site_timezone = get_option('timezone_string');
	$iso_date = new DateTime($request['isodate'], new DateTimeZone($site_timezone));
	$iso_date = $iso_date->format('Y-m-d');

	if (!$selected_service){
		return 'No Info';
	}

	$ts_services_table = $wpdb->prefix . 'ts_services';
	$ts_staff_services_table = $wpdb->prefix . 'ts_staff_services';
	$ts_staff_table = $wpdb->prefix . 'ts_staff';
	$ts_appt_table = $wpdb->prefix . 'ts_appointments';

	// I18n strings
	$i18n_strings = new TimeSlot\MultilangStrings();
	$visible_i18n = $i18n_strings -> visible_string;
	$approved_i18n = $i18n_strings -> approved_string;
	$any_staff_i18n = __('Any Staff Member', 'timeslot');

	$service_info = 
		$wpdb->get_row(
		$wpdb->prepare(
			"SELECT duration, before_service 
			FROM {$ts_services_table} 
			WHERE visibility = %s 
			AND service_title = %s", 
			$visible_i18n, $selected_service), OBJECT
	);

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

	$selected_date_appts = 
		$wpdb->get_results(
		$wpdb->prepare(
			"SELECT 
			DISTINCT TIME({$ts_appt_table}.start_appt) as start_time, 
			{$ts_services_table}.duration as duration, 
			{$ts_services_table}.before_service as before_service, 
			{$ts_appt_table}.staff as staff 
			FROM {$ts_appt_table}, {$ts_services_table} 
			WHERE DATE({$ts_appt_table}.start_appt) = %s 
			AND {$ts_appt_table}.start_appt >= TIMESTAMP(CURDATE()) 
			AND {$ts_appt_table}.service_title = {$ts_services_table}.service_title 
			AND {$ts_appt_table}.appt_status = %s", 
			$iso_date, $approved_i18n), OBJECT
	);

	// Get start and end times in seconds
	function get_time_in_seconds($time_option) {
		$hour = date('G', strtotime($time_option));
		$minute = date('i', strtotime($time_option));
		$time = ($hour * 3600) + ($minute * 60);
		return $time;
	}

	// Get number of appointments available for full day, or before and after break
	function get_num_appts($end, $start, $service_info) {
		$total = floor(($end - $start + $service_info->before_service) / ($service_info->duration + $service_info->before_service));
		return $total;
	}

	// Get Options
	$timezone = get_option('timezone_string');
	$general_wp_option = get_option('timeslot-general-tab');
	$hours_wp_option = get_option('timeslot-business-hours');

	// Get business hours
	$day_start = $hours_wp_option[$appt_day]['start-hour'] ?? '9:00am';
	$day_end = $hours_wp_option[$appt_day]['end-hour'] ?? '5:00pm';

	// Get business day start and end times in seconds
	$start_time = get_time_in_seconds($day_start);
	$end_time = get_time_in_seconds($day_end);

	// Arrays
	$times_unavailable = array();
	$time_options = array();
	$available_totals = array();
	
	// Available staff
	if($available_staff){
		$count_staff = count($available_staff);
		$available_staff = array_column($available_staff, 'staff_name');
		if ($count_staff == 1 && $selected_staff == $any_staff_i18n){
			$selected_staff = $available_staff[0];
		}
	}

	// Cutoff date and time
	$cutoff_sec = $general_wp_option['default-before-booking']['sec'] ?? 0;
	$cutoff_ms = $cutoff_sec * 1000;
	$today = new DateTime('now', new DateTimeZone($timezone));
	$today_plus_cutoff = $today->modify('+ '.$cutoff_ms.' milliseconds');
	$cutoff_date = $today_plus_cutoff->format('Y-m-d');
	$cutoff_time = $today_plus_cutoff->format('H:i:s');

	// Generate every 15 minute booked time slot for each appointment
	foreach($selected_date_appts as $appt){

		if ($available_staff && !in_array($appt->staff, $available_staff)){
			continue;
		}

		$duration = ($appt->duration + $appt->before_service) / 60 ;
		$appt_start_time= strtotime($appt->start_time);

		if($selected_staff) {
			if(($selected_staff === $appt->staff) || ($selected_staff === $any_staff_i18n)) {
				$times_unavailable[]= $appt->start_time;
			}
		}
		else {
			$times_unavailable[]= $appt->start_time;
		}

		for($i=15; $i < $duration; $i+=15 ){

			$appt_time_interval = strtotime("+". $i . " minutes", $appt_start_time);

			if($selected_staff) {
				if(($selected_staff === $appt->staff) || ($selected_staff === $any_staff_i18n)) {
					$times_unavailable[]= date('H:i:s', $appt_time_interval);
				}
			}
			else {
				$times_unavailable[]= date('H:i:s', $appt_time_interval);
			}
		}
	}

	// Break time slots and hours
	if (isset($hours_wp_option[$appt_day]['break-start'])){

		// Get break hours
		$break_start = $hours_wp_option[$appt_day]['break-start'];
		$break_end = $hours_wp_option[$appt_day]['break-end'];

		// Get break times in seconds
		$break_start_time = get_time_in_seconds($break_start);
		$break_end_time = get_time_in_seconds($break_end);

		// Get number of appointments available before and after break
		$before_break_total = get_num_appts($break_start_time, $start_time, $service_info);
		$after_break_total = get_num_appts($end_time, $break_end_time, $service_info);

		// Add break info for time option loop
		$available_totals[] = [
			'total' => $before_break_total,
			'start' => $start_time
		];
		$available_totals[] = [
			'total' => $after_break_total,
			'start' => $break_end_time
		];

		// Set time slots during break hours to unavailable
		$break_duration = ($break_end_time - $break_start_time) / 60 ;
		$times_unavailable[]= date('H:i:s', $break_start_time);

		for($i=15; $i < $break_duration; $i+=15 ){
			$break_time_interval = strtotime("+". $i . " minutes", $break_start_time);
			$times_unavailable[]= date('H:i:s', $break_time_interval);
		}
	}
	else {
		// Add full business day info for time option loop
		$num_appts = get_num_appts($end_time, $start_time, $service_info);
		$available_totals = array(
			array(
				'total' => $num_appts,
				'start' => $start_time
			)
		);
	}

	// If any staff is selected, find time slots that are 
	// completely booked by all staff that offer selected service
	if($available_staff){
		if (($selected_staff === $any_staff_i18n) && ($count_staff > 1)){

			$duplicates = array_count_values($times_unavailable);
			$duplicates_removed = array();

			foreach($duplicates as $time => $count){
				if ($count <= 1){
					continue;
				}
				if ($count >= $count_staff){
					$duplicates_removed[] = $time;
				}
			}

			$times_unavailable = $duplicates_removed;
		}
	}

	foreach($available_totals as $available_total){

		$interval = 0;
		$service_total = $service_info->duration + $service_info->before_service;

		// Generates each available appointment time
		for($i=0; $i < $available_total['total']; $i++ ){

			$appt_time = strtotime("+". $interval . " seconds", $available_total['start']);
			$appt_time_format = date('H:i:s', $appt_time);
			$wp_option_time_format = get_option('time_format');

			$interval += $service_total;

			if (in_array(date('H:i:s', $appt_time), $times_unavailable)){
				continue;
			}

			// Times after cutoff not available
			if ($iso_date === $cutoff_date && $appt_time_format < $cutoff_time){
				continue;
			}

			$time_options[]= array(
				'id' => date('H:i:s', $appt_time),
				'text' => date($wp_option_time_format, $appt_time)
			);
		}
	}

	// Return array to populate form select options
	$times_available = array(
		'available' => $time_options,
	);

	$response = new WP_REST_Response( $times_available, 200 );
	$response -> header( 'Content-type', 'application/json; charset=UTF-8' );
	return $response;
}