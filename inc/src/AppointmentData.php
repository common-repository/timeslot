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

class AppointmentData extends TableData {

	function get_data(){

		foreach($this -> data_from_db as $appt){

			$date_alt = new \DateTimeImmutable($appt->start_appt);
			$date_alt =  $date_alt->format('Y-m-d');

			$count_staff = new CountStaff();
			$is_staff = $count_staff -> is_staff();

			$staff_data = array(
				$appt->appt_id,
				$appt->start_appt,
				$appt->start_appt,
				$appt->service_title,
				$appt->customer_name,
				$appt->appt_status,
				$date_alt,
			);

			if($is_staff){
				array_splice($staff_data, 4, 0, $appt->staff);
			}

			$this -> data_array[]= $staff_data;
		}

		return rest_ensure_response($this -> data_array);

	}
}