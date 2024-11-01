<?php
/**
 * Checks coupon code validity
 *
 * Checks coupon code against database 
 * when code is entered into appointment form.
 * References public/js/ts-form.js.
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */

// Exit if accessed directly
if (!defined('ABSPATH')){
	exit;
}

add_action( 'wp_ajax_nopriv_tslot_check_coupon_codes', 'tslot_check_coupon_codes' );
add_action( 'wp_ajax_tslot_check_coupon_codes', 'tslot_check_coupon_codes' );

function tslot_check_coupon_codes() {

	if (empty($_POST['coupon'])){
		wp_die();
	}

	if (empty($_POST['service'])){
		wp_die();
	}

	$ts_coupon_code = sanitize_text_field($_POST['coupon']);
	$service = sanitize_text_field($_POST['service']);
	$coupon_data = array();

	$discount = new TimeSlot\DiscountPrice();
	$discount -> get_discount_info($ts_coupon_code);

	if(!$discount -> discount_type || !$discount -> discount_amount){
		$coupon_invalid = wp_json_encode(
			array('valid' => false)
		);
		echo $coupon_invalid;
		wp_die();
	}

	$price = new TimeSlot\PriceFromService($service);
	$price -> get_price();
	$service_price = $price -> service_price;
	$discount -> get_discount_price($service_price);

	// Adjusts price for coupon codes
	$discount_to_decimal = new TimeSlot\I18nCurrency($discount->price);
	$discount_to_decimal -> to_decimal();
	$discounted_price = $discount_to_decimal->price;

	$service_to_decimal = new TimeSlot\I18nCurrency($service_price);
	$service_to_decimal -> to_decimal();
	$original_price = $service_to_decimal->price;

	$coupon_data = wp_json_encode(array(
		'valid' => true,
		'discountedPrice' => $discounted_price,
		'originalPrice' => $original_price,
	));

	echo $coupon_data;

	wp_die();
}