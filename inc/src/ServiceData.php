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

class ServiceData extends TableData {

	function get_data(){

		foreach($this -> data_from_db as $service){

			$curr = new I18nCurrency($service->price);
			$curr -> to_decimal();

			$this -> data_array[]= array(
				$service->service_id,
				$service->service_title,
				$curr->price,
				$service->duration,
				$service->before_service,
				$service->category,
				$service->visibility,
				$service->info,
			);
		}

		return rest_ensure_response($this -> data_array);

	}

}