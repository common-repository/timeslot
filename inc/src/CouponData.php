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

class CouponData extends TableData {

	function get_data(){

		foreach($this -> data_from_db as $coupon){
			$this -> data_array[]= array(
				$coupon->coupon_id,
				$coupon->coupon_code,
				$coupon->discount_amount,
				$coupon->discount_type,
				$coupon->coupon_status,
			);
		}

		return rest_ensure_response($this -> data_array);

	}

}