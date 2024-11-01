<?php
/**
 * Counts visible staff members
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.2.9
 * 
 */

namespace TimeSlot;

class CountStaff {

	public $staff_count;
	
	public function __construct(){

		global $wpdb;
		$ts_staff_table = $wpdb->prefix . 'ts_staff';
		$i18n_strings = new MultilangStrings();
		$visible_i18n = $i18n_strings -> visible_string;

		$this -> staff_count =
			$wpdb->get_var(
			$wpdb->prepare(
				"SELECT
				COUNT(staff_id)
				FROM {$ts_staff_table}
				WHERE visibility = %s",
				$visible_i18n
		));
	}
	
	public function get_count(){
		return $this -> staff_count;
	}
	
	public function is_staff(){
		if ($this -> staff_count > 0){
			return true;
		}
		return false;
	}
}