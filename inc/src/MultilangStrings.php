<?php
/**
 * Sets strings for WPML Compatibility
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.1.7
 * 
 */

namespace TimeSlot;

class MultilangStrings {

	public $active_string;
	public $approved_string;
	public $complete_string;
	public $none_string;
	public $service_string;
	public $visible_string;
	public $percentage_string;
	public $flat_rate_string;
	public $customer_reminder_subject;
	public $customer_reminder_tinymce;
	
	public function __construct(){

		$this -> active_string = __('Active', 'timeslot');
		$this -> approved_string = __('Approved', 'timeslot');
		$this -> complete_string = __('Complete', 'timeslot');
		$this -> none_string = __('None', 'timeslot');
		$this -> visible_string = __('Visible', 'timeslot');
		$this -> percentage_string = __('Percentage', 'timeslot');
		$this -> flat_rate_string = __('Flat Rate', 'timeslot');

		// I18n strings
		if (has_action('wpml_register_single_string')){
			$this -> set_i18n_strings();
		}
	}

	public function set_i18n_strings(){

		if (!has_action('wpml_register_single_string')){
			return;
		}

		$wpml_default_lang = apply_filters('wpml_default_language', NULL );

		$strings = array(
			'active_string' => 'Active',
			'approved_string' => 'Approved',
			'complete_string' => 'Complete',
			'none_string' => 'None',
			'visible_string' => 'Visible',
			'percentage_string' => 'Percentage',
			'flat_rate_string' => 'Flat Rate'
		);

		// Register and return strings in WPML default language for MySQL queries
		foreach($strings as $var => $string){
			do_action( 'wpml_register_single_string', 'timeslot', $string, $string );
			$this -> $var = apply_filters('wpml_translate_single_string', $string, 'timeslot', $string, $wpml_default_lang);
		}
	}

	public function set_i18n_service($service_title, $lang = null){

		$this -> service_string = $service_title;

		if (has_action('wpml_translate_single_string')){
			$wpml_name = 'Time Slot Service '. $service_title;
			$this -> service_string = apply_filters('wpml_translate_single_string', $service_title, 'timeslot', $wpml_name, $lang);
		}
	}
}