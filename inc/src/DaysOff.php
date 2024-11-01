<?php
/**
 * Disables dates in datepicker
 *
 * Controls closed business days and
 * holidays in frontend form datepicker
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */

namespace TimeSlot;

class DaysOff {

	private $wpdb;
	private $to_datepicker;
	private $chosen_staff;
	private $chosen_service;
	private $hours_wp_option;
	private $timezone;
	private $weekdays;
	private $weekday;
	private $ts_services_table;
	private $ts_appt_table;
	private $ts_staff_services_table;
	private $approved_i18n;
	private $visible_i18n;
	private $num_appts;
	private $start_time;
	private $end_time;
	private $business_closed_unix;
	private $appt_duration;

	public function __construct() {

		global $wpdb;
		$this->wpdb = $wpdb;
		$this->to_datepicker = array();
		$this->weekdays = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');

		// I18n strings
		$i18n_strings = new MultilangStrings();
		$this->visible_i18n = $i18n_strings -> visible_string;
		$this->approved_i18n = $i18n_strings -> approved_string;

		// Tables
		$this->ts_services_table = $this->wpdb->prefix . 'ts_services';
		$this->ts_appt_table = $this->wpdb->prefix . 'ts_appointments';
		$this->ts_staff_services_table = $this->wpdb->prefix . 'ts_staff_services';

		// WP options
		$this->hours_wp_option = get_option('timeslot-business-hours');
		$this->timezone = get_option('timezone_string');

		add_action('rest_api_init', array($this, 'tslotregisterRestRoute'), 10, 0);
	}

	public function tslotregisterRestRoute() {
		register_rest_route('timeslot/v1', '/ts-business-hours/', array(
			'methods' => \WP_REST_Server::READABLE,
			'callback' => array($this, 'tslotGetBusinessHours'),
			'permission_callback' => '__return_true',
			'args' => array(
				'service' => array(
					'required' => false,
					'type' => 'string',
					'description' => 'Selected service',
				),
				'staff' => array(
					'required' => false,
					'type' => 'string',
					'description' => 'Selected staff member',
				)
			)
		));
	}

	public function tslotGetBusinessHours($request) {

		$this->chosen_staff = $request['staff'];
		$this->chosen_service = $request['service'];
		$this->tslotStaffAndBusinessDays();
		$this->tslotBookingsAndHours();
		$this->tslotHolidays();

		$response = new \WP_REST_Response( $this->to_datepicker, '200' );
		$response -> header( 'Content-type', 'application/json; charset=UTF-8' );
		return $response;
	}

	private function tslotStaffAndBusinessDays() {

		$ts_staff_table = $this->wpdb->prefix . 'ts_staff';
		$first_of_month = new \DateTime('first day of this month');
		$one_year_ahead = new \DateTime('first day of this month +1 year');
		$one_day_interval = new \DateInterval('P1D');
		$days_off_range = new \DatePeriod($first_of_month, $one_day_interval, $one_year_ahead);

		// Get staff days off
		switch ($this->chosen_staff) {

			case '':
				break;

			case __('Any Staff Member', 'timeslot'):
				$available_staff = 
					$this->wpdb->get_results(
					$this->wpdb->prepare(
						"SELECT 
						{$ts_staff_table}.daysoff
						FROM {$ts_staff_table}, {$this->ts_staff_services_table} 
						WHERE {$this->ts_staff_services_table}.service_title = %s 
						AND {$this->ts_staff_services_table}.staff_id = {$ts_staff_table}.staff_id 
						AND {$ts_staff_table}.visibility = %s", 
						$this->chosen_service, $this->visible_i18n
				));

				$any_staff_days_off = array_column($available_staff, 'daysoff');
				$staff_dayoff_count = count($any_staff_days_off);
				$staff_days_off = array();
				$duplicate_days = array();

				foreach($any_staff_days_off as $any_staff_day_off){
					$staff_days_off = array_merge($staff_days_off, explode(",", $any_staff_day_off));
				}

				$duplicates = array_count_values($staff_days_off);

				foreach($duplicates as $day => $count){
					if ($count <= 1){
						continue;
					}
					if ($count >= $staff_dayoff_count){
						$duplicate_days[] = $day;
					}
				}
	
				$staff_days_off = $duplicate_days;

				break;

			default:
				$staff_days =
					$this->wpdb->get_var(
					$this->wpdb->prepare(
						"SELECT daysoff 
						FROM {$ts_staff_table} 
						WHERE staff_name = %s",
						$this->chosen_staff
				));

				$staff_days_off = explode(',', $staff_days ?? '');
				break;
		}

		foreach($days_off_range as $range) {

			$first_of_month->add(new \DateInterval('P1D'));
			$day = $first_of_month->format('Y-m-d');
			$day_of_week = $first_of_month->format('l');

			// Disable business days off
			foreach($this->weekdays as $weekday){
				$weekday_closed = isset($this->hours_wp_option[strtolower($weekday)]['closed']) ? $this->hours_wp_option[strtolower($weekday)]['closed'] : null;

				if (!isset($weekday_closed)){
					continue;
				}
				if ($day_of_week !== $weekday && $weekday_closed !== 1) {
					continue;
				}
				if (!in_array($day, $this->to_datepicker)){
					$this->to_datepicker[]= $day;
				}
			}

			// Disable staff days off
			if (!isset($staff_days_off) || !$staff_days_off){
				continue;
			}

			foreach($staff_days_off as $staff_day_off){
				$day_of_week = __($day_of_week, 'timeslot');
				if ($staff_day_off !== $day_of_week){
					continue;
				}
				if (!in_array($day, $this->to_datepicker)){
					$this->to_datepicker[]= $day;
				}
			}
		}
	}

	private function tslotBookingsAndHours() {

		foreach($this->weekdays as $weekday){

			if (empty($this->chosen_service) || (empty($this->chosen_staff) && empty($this->chosen_service))){
				continue;
			}

			$this->weekday = $weekday;

			$this->tslotBusinessHours();
			$this->tslotApptTotals();
			$this->tslotAfterCloseToday();
			$this->tslotCutoffDate();

			if ($this->num_appts == 0) {
				continue;
			}

			$this->tslotStaffBookedForService();
			$this->tslotServiceFullyBooked();
			$this->tslotFifteenMinuteIntervals();
		}
	}

	private function tslotHolidays() {

		$holiday_wp_option = get_option('timeslot-holidays');

		if (empty($holiday_wp_option)){
			return;
		}

		$saved_holiday = $holiday_wp_option['closed'];
		$first_of_month = new \DateTime('first day of this month');
		$five_years_ahead = new \DateTime('first day of this month +5 years');
		$one_year_interval = new \DateInterval('P1Y');
		$holiday_range = new \DatePeriod($first_of_month, $one_year_interval, $five_years_ahead);
		$now = new \DateTime('now', new \DateTimeZone($this->timezone));
		$yr = 0;

		foreach($saved_holiday as $holiday){

			$holiday_date = $holiday['date'];

			if (empty($holiday_date)){
				continue;
			}

			$new_holiday = new \DateTime($holiday_date);

			if ($new_holiday > $now){
				if (!in_array($holiday_date, $this->to_datepicker)){
					$this->to_datepicker[]= $holiday_date;
				}
			}

			if (!isset($holiday['annual']) || $holiday['annual'] !== 'annual'){
				continue;
			}

			foreach($holiday_range as $range) {
				$future_holiday = date('Y-m-d',strtotime('+'.$yr.' year',strtotime($holiday_date)));
				if (!in_array($future_holiday, $this->to_datepicker)){
					$this->to_datepicker[]= $future_holiday;
				}
				$yr++;
			}
		}
	}

	function tslotBusinessHours(){

		$weekday_lower = strtolower($this->weekday);
		$day_start = isset($this->hours_wp_option[$weekday_lower]['start-hour']) ? $this->hours_wp_option[$weekday_lower]['start-hour'] : null;
		$day_end = isset($this->hours_wp_option[$weekday_lower]['end-hour']) ? $this->hours_wp_option[$weekday_lower]['end-hour'] : null;

		if (!isset($day_start) || !isset($day_end)){
			$this->end_time = 0;
			$this->start_time = 0;
			$this->business_closed_unix = 0;
			return;
		}

		$business_closed = new \DateTime($day_end, new \DateTimeZone($this->timezone));
		$this->business_closed_unix =  $business_closed->format('U');

		// Start of Business Day
		$start_hour = date('G', strtotime($day_start));
		$start_minute = date('i', strtotime($day_start));
		$this->start_time = ($start_hour * 3600) + ($start_minute * 60);

		// End of Business Day
		$end_hour = date('G', strtotime($day_end));
		$end_minute = date('i', strtotime($day_end));
		$this->end_time = ($end_hour * 3600) + ($end_minute * 60);
	}

	function tslotApptTotals(){

		$service_info = 
		$this->wpdb->get_row(
		$this->wpdb->prepare(
			"SELECT 
			duration, before_service 
			FROM {$this->ts_services_table} 
			WHERE visibility = %s 
			AND service_title = %s", 
			$this->visible_i18n, $this->chosen_service
		));

		$service_before = $service_info -> before_service;
		$service_duration = $service_info -> duration;

		$this->appt_duration = $service_duration + $service_before;

		if ($this->end_time == 0 || $this->start_time == 0){
			return $this->num_appts = 0;
		}

		$this->num_appts = floor(($this->end_time - $this->start_time + $service_before) / ($service_duration + $service_before));
	}

	function tslotAfterCloseToday(){

		$now = new \DateTime('now', new \DateTimeZone($this->timezone));
		$today = $now->format('Y-m-d');
		$current_time = $now->format('U');
		$current_day_name = $now->format('l');

		if ($current_time > $this->business_closed_unix && $current_day_name === $this->weekday){
			if (!in_array($today, $this->to_datepicker)){
				$this->to_datepicker[]= $today;
			}
		}
	}

	function tslotCutoffDate(){

		// Cutoff date from minimum time before booking
		$general_wp_option = get_option('timeslot-general-tab');

		if (!isset($general_wp_option['default-before-booking']['sec'])){
			return;
		}

		$before_booking_sec = $general_wp_option['default-before-booking']['sec'];
		$before_booking_ms = $before_booking_sec * 1000;
		$start_now = new \DateTime('now', new \DateTimeZone($this->timezone));
		$end_date = new \DateTime('now', new \DateTimeZone($this->timezone));
		$booking_cutoff = $end_date->modify('+ '.$before_booking_ms.' milliseconds +1 minute');
		$booking_cutoff_unix =  $booking_cutoff->format('U');
		$plus_one_day = new \DateInterval('P1D');
		$cutoff_date_range = new \DatePeriod($start_now, $plus_one_day , $booking_cutoff);

		// Get all dates between now and cutoff date
		foreach($cutoff_date_range as $date){

			$before_date = $date->format('Y-m-d');
			$before_day = $date->format('l');

			if ($booking_cutoff_unix < $this->business_closed_unix){
				continue;
			}
			if ($before_day !== $this->weekday){
				continue;
			}
			if (!in_array($before_date, $this->to_datepicker)){
				$this->to_datepicker[]= $before_date;
			}
		}
	}

	function tslotStaffBookedForService(){

		$get_appts_by_staff_and_service =
			$this->wpdb->get_results(
			$this->wpdb->prepare(
				"SELECT DATE(start_appt) as booked 
				FROM {$this->ts_appt_table}
				WHERE service_title = %s
				AND staff = %s
				AND start_appt >= NOW()
				AND DAYNAME(start_appt) = %s
				AND appt_status = %s
				GROUP BY DATE(start_appt)
				HAVING COUNT(start_appt) >= %d",
				$this->chosen_service, $this->chosen_staff, $this->weekday, $this->approved_i18n, $this->num_appts
		));

		foreach($get_appts_by_staff_and_service as $appt_date){

			if (!in_array($appt_date->booked, $this->to_datepicker)){
				$this->to_datepicker[]= $appt_date->booked;
			}
		}
	}

	function tslotServiceFullyBooked(){

		$num_appts_available = 
			$this->wpdb->get_var(
			$this->wpdb->prepare(
				"SELECT
				COUNT(service_title) * %d as available_count
				FROM {$this->ts_staff_services_table}
				WHERE service_title = %s", 
				$this->num_appts, $this->chosen_service
		));

		$get_appts_by_service = 
		$this->wpdb->get_results(
		$this->wpdb->prepare(
			"SELECT
			DATE(start_appt) as appt_date,
			COUNT(start_appt) as appt_count
			FROM {$this->ts_appt_table}
			WHERE service_title = %s
			AND start_appt >= NOW()
			AND DAYNAME(start_appt) = %s
			AND appt_status = %s
			GROUP BY start_appt , service_title
			HAVING COUNT(start_appt) > 1
			AND COUNT(service_title) > 1", 
			$this->chosen_service, $this->weekday, $this->approved_i18n
		));

		foreach($get_appts_by_service as $staff_booked){

			$appt_count = $staff_booked->appt_count;
			$appt_date = $staff_booked->appt_date;

			if ($appt_count < $num_appts_available){
				continue;
			}
			if (!in_array($appt_date, $this->to_datepicker)){
				$this->to_datepicker[]= $appt_date;
			}
		}
	}

	function tslotFifteenMinuteIntervals(){

		$booked_dates_and_times = array();
		$booked_times = array();
		$appt_time_slots = array();

		$get_appts_by_staff = 
			$this->wpdb->get_results(
			$this->wpdb->prepare(
				"SELECT
				TIME({$this->ts_appt_table}.start_appt) as start_time,
				DATE({$this->ts_appt_table}.start_appt) as start_date,
				{$this->ts_services_table}.duration as duration,
				{$this->ts_services_table}.before_service as before_service
				FROM {$this->ts_appt_table}, {$this->ts_services_table}
				WHERE {$this->ts_appt_table}.start_appt >= NOW()
				AND DAYNAME({$this->ts_appt_table}.start_appt) = %s
				AND {$this->ts_appt_table}.service_title = {$this->ts_services_table}.service_title
				AND {$this->ts_appt_table}.appt_status = %s
				AND {$this->ts_appt_table}.staff = %s",
				$this->weekday, $this->approved_i18n, $this->chosen_staff
		));

		$service_time_slot = new \DateTime("@$this->start_time");
		$appt_duration_interval = new \DateInterval('PT'. $this->appt_duration . 'S');

		// Create all available time slots for selected service
		for($i=0; $i < $this->num_appts; $i++ ){

			$appt_time_slots[] = $service_time_slot->format('H:i:s');
			$service_time_slot->add( $appt_duration_interval );
		}

		//Create 15 minute booked time slots for each appointment
		foreach($get_appts_by_staff as $appt){

			$appt_date = date('Y-m-d', strtotime($appt->start_date));
			$duration = (($appt->duration + $appt->before_service) / 60) / 15;
			$booked_interval = new \DateTime($appt->start_time);
			$fifteen = new \DateInterval('PT15M');

			for($i=0; $i < $duration; $i++ ){

				$booked_interval_format = $booked_interval->format('H:i:s');

				if (!in_array($booked_interval_format, $booked_times)){
					$booked_times[]= $booked_interval_format;
				}

				$booked_interval->add( $fifteen );
			}

			$booked_dates_and_times[]= array($appt_date => $booked_times);

			// Clear array for next appointment date
			$booked_times = array();
		}

		$booked_dates_and_times = array_merge_recursive(...$booked_dates_and_times);

		// Check if available times are in array of unavailable times
		foreach($booked_dates_and_times as $date => $times){

			$available_in_booked = array_diff($appt_time_slots, $times);

			if (!$available_in_booked){
				$this->to_datepicker[]= $date;
			}
		}
	}
}