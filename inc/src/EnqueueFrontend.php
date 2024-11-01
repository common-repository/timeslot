<?php
/**
 * Enqueues frontend scripts and styles
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.1.4
 * 
 */

namespace TimeSlot;

class EnqueueFrontend {

	public function __construct() {
		add_action('wp_enqueue_scripts', array( $this, 'tslot_enqueue_frontend' ), 10, 0);
		add_filter('body_class', array( $this, 'tslot_add_body_class' ), 10, 1);
		add_action('elementor/editor/before_enqueue_scripts', array( $this, 'tslot_enqueue_frontend' ), 10, 0);
	}

	public function tslot_enqueue_frontend() {

		global $post;
		$elementor_data = !is_null($post -> _elementor_data) ? $post -> _elementor_data : '';

		switch(true){
			case has_block('timeslot/booking-form'):
			case is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'timeslot-form'):
			case str_contains( $elementor_data, 'timeslot-form' ):
				break;
			default:
				return;
		}

		self::tslot_universal_scripts();
		self::tslot_frontend_styles();

		if (is_admin()){
			return;
		}

		self::tslot_localize_form();
		self::tslot_dependencies();
		self::tslot_payment_scripts();
		self::tslot_set_script_translations();

	}

	public function tslot_add_body_class($ts_classes) {
		$ts_classes[] = 'timeslot';
		return $ts_classes;
	}

	public function tslot_universal_scripts() {
		wp_enqueue_script('jquery');
		wp_enqueue_style('select2');
		wp_enqueue_script('select2');
	}

	public function tslot_frontend_styles() {
		require (TIMESLOT_PATH . 'public/css/ts-form-dynamic.php');
		wp_enqueue_style('ts-form', TIMESLOT_URL . 'public/css/ts-form.min.css');
		wp_add_inline_style('ts-form', $ts_form_styles_min);
	}

	public function tslot_localize_form() {

		$datepicker_format = new DatepickerFormat(get_option('date_format'));
		$datepicker_format = $datepicker_format -> get_format();
		$start_of_week = get_option('start_of_week');

		$ts_locale = get_locale();
		$payment_options = get_option('timeslot-payment-methods-tab');
		$currency_code = $payment_options['currency'];
		$ajax_url = admin_url('admin-ajax.php');

		$ts_nf = new \NumberFormatter($ts_locale, \NumberFormatter::CURRENCY);
		$ts_nf -> setTextAttribute(\NumberFormatter::CURRENCY_CODE , $currency_code);
		$currency_symbol  = $ts_nf -> getSymbol(\NumberFormatter::CURRENCY_SYMBOL);

		// Register frontend script
		wp_enqueue_script('ts-form');
		wp_localize_script('ts-form', 'tsao',
			array(
				'ajaxurl' => $ajax_url,
				'pluginUrl' => plugins_url(),
				'bizhourevents' => rest_url('timeslot/v1/ts-business-hours'),
				'staffavailable' => rest_url('timeslot/v1/ts-staff-available'),
				'timesavailable' => rest_url('timeslot/v1/ts-times'),
				'getstaff' => rest_url('timeslot/v1/ts-get-staff'),
				'dateformat' => $datepicker_format,
				'startofweek' => $start_of_week,
				'currencysymbol' => $currency_symbol,
				'locale' => $ts_locale,
				'currency' => $currency_code,
			)
		);
	}

	public function tslot_set_script_translations() {
		wp_set_script_translations('ts-form', 'timeslot', TIMESLOT_PATH . 'languages');
		wp_set_script_translations('ts-local-payment', 'timeslot', TIMESLOT_PATH . 'languages');
	}

	public function tslot_dependencies() {
		wp_enqueue_script('jquery-form');
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('jquery-validate-min');
		wp_enqueue_script('jquery-validate-additional');
	}

	public function tslot_payment_scripts() {
		wp_enqueue_script('ts-local-payment', TIMESLOT_URL . 'public/js/local.min.js', array('jquery', 'wp-i18n'));
	}
}