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

class CustomerData extends TableData {

	function get_data(){

		foreach($this -> data_from_db as $customer){
			$this -> data_array[]= array(
				$customer->customer_id,
				$customer->customer_name,
				$customer->email,
				$customer->phone
			);
		}

		return rest_ensure_response($this -> data_array);

	}

}