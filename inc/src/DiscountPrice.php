<?php
/**
 * Gets discount information from coupon code,
 * gets price from service name, and calculates
 * discounted price.
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.6
 * 
 */

namespace TimeSlot;

class DiscountPrice {

	public $price;
	public $price_discounted;
	public $discount_type;
	public $discount_amount;
	public $coupon_code;
	public $active;
	public $percentage;
	public $flat_rate;

	public function __construct(){

		// I18n strings
		$i18n_strings = new MultilangStrings();
		$this -> active = $i18n_strings -> active_string;
		$this -> percentage = $i18n_strings -> percentage_string;
		$this -> flat_rate = $i18n_strings -> flat_rate_string;

	}

	public function get_discount_info($coupon_code){
		$this -> coupon_code = $coupon_code;

		global $wpdb;
		$ts_coupons_table = $wpdb->prefix . 'ts_coupons';

		$discount_info = 
			$wpdb->get_row(
			$wpdb->prepare(
				"SELECT discount_amount, discount_type
				FROM {$ts_coupons_table} 
				WHERE coupon_status = %s 
				AND coupon_code = %s", 
				$this -> active, $this -> coupon_code
		));

		if($discount_info){
			$this -> discount_amount = $discount_info -> discount_amount;
			$this -> discount_type = $discount_info -> discount_type;
		}
		else{
			$this -> discount_amount = null;
			$this -> discount_type = null;
		}
	}

	public function get_discount_price($price){

		$this -> price = $price;

		switch ($this -> discount_type){

			case $this -> percentage:
				$this -> price_discounted = round(($this -> discount_amount / 100) * $this -> price);
				$this -> price = round(((100 - $this -> discount_amount) / 100) * $this -> price);
				break;

			case $this -> flat_rate:
				$flat_rate = new I18nCurrency($this -> discount_amount);
				$flat_rate -> to_min_unit();
				$this -> price_discounted = $flat_rate -> price;
				$this -> price = $this -> price - $flat_rate -> price;
				break;

			default:
				break;
		}
	}
}