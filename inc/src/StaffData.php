<?php
/**
 * Registers rest routes and gets datatables data
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.9
 * 
 */

namespace TimeSlot;

class StaffData extends TableData {

	function get_data(){

		global $wpdb;
		$ts_staff_table = $wpdb->prefix . 'ts_staff';
		$ts_staff_services_table = $wpdb->prefix . 'ts_staff_services';

		$ts_staff_from_db = 
			$wpdb->get_results(
			"SELECT 
			{$ts_staff_table}.staff_id, 
			staff_name, 
			phone, 
			email, 
			GROUP_CONCAT({$ts_staff_services_table}.service_title) as services, 
			daysoff, 
			visibility, 
			info 
			FROM {$ts_staff_table}, {$ts_staff_services_table} 
			WHERE {$ts_staff_table}.staff_id = {$ts_staff_services_table}.staff_id 
			GROUP BY {$ts_staff_table}.staff_id", OBJECT
		);

		foreach($ts_staff_from_db as $staff){

			$this -> data_array[]= array(
				$staff->staff_id,
				$staff->staff_name,
				$staff->phone,
				$staff->email,
				$staff->services,
				$staff->daysoff,
				$staff->visibility,
				$staff->info,
			);
		}

		return rest_ensure_response($this -> data_array);

	}

}