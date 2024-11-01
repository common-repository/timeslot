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

class PaymentData extends TableData {

	function get_data(){

		global $wpdb;

		foreach($this -> data_from_db as $payment){

			$appt_id = $payment->appt_id;
			$ts_appt_table = $wpdb->prefix . 'ts_appointments';

			$get_customer_name = 
				$wpdb->get_var(
				$wpdb->prepare(
					"SELECT customer_name 
					FROM {$ts_appt_table} 
					WHERE appt_id = %d", 
					$appt_id
			));

			// Added 1.1.8
			$customer_name = $get_customer_name ?: $payment->customer_name;

			$curr = new I18nCurrency($payment->amount);
			$curr -> to_decimal();

			$this -> data_array[]= array(
				$payment->payment_id,
				$curr->price,
				$payment->created,
				$customer_name,
				$payment->source,
				ucwords(strtolower($payment->payment_status)),
				$payment->coupon,
			);
		}

		return rest_ensure_response($this -> data_array);

	}

}