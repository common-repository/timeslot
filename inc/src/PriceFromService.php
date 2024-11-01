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

class PriceFromService {

	public $service;
	public $service_price;
	
	public function __construct($service){

		$i18n_strings = new MultilangStrings();
		$lang = has_action('wpml_translate_single_string') ? apply_filters('wpml_default_language', null ) : null;
		$i18n_strings -> set_i18n_service($service, $lang);
		$this -> service = $i18n_strings -> service_string;

	}
	
	public function get_price(){
		global $wpdb;
		$services_table = $wpdb -> prefix . 'ts_services';
		$this -> service_price = 
		$wpdb -> get_var(
			$wpdb -> prepare(
			"SELECT price 
			FROM {$services_table} 
			WHERE service_title = %s", 
			$this -> service
		));
	}

}