<?php
/**
 * Displays coupon datatable
 *
 * Selects coupon data from database and displays
 * in datatable. Includes modal editor, delete,
 * and update functions. References admin/js/datatables.js.
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

// Registers rest route and returns coupons datatable data
$coupon_data = new TimeSlot\CouponData('coupons');

// Displays coupon datatable and editor modal
function tslot_coupons_tab_init(){

	$title = __('Coupons', 'timeslot');

	$headers = array(
		__('ID', 'timeslot'),
		__('Coupon Code', 'timeslot'),
		__('Discount Amount', 'timeslot'),
		__('Discount Type', 'timeslot'),
		__('Status', 'timeslot'),
	);

	$discount_type = array(
		__('Percentage', 'timeslot'),
		__('Flat Rate', 'timeslot'),
	);

	$coupon_status = array(
		__('Active', 'timeslot'),
		__('Disabled', 'timeslot'),
	);

	$edit_fields = array(
		'title' => __('coupon', 'timeslot'),
		'name' => 'coupon',
		'fields' => array(
			array(
				'name' => 'coupon-code',
				'type' => 'text',
				'width' => '100'
			),
			array(
				'name' => 'discount-amount',
				'type' => 'number',
				'width' => '50',
				'atts' => array(
					'min' => '0',
					'step' => '1',
				)
			),
			array(
				'name' => 'discount-type',
				'type' => 'select',
				'width' => '50',
				'options' => $discount_type
			),
			array(
				'label' => __('Coupon Status', 'timeslot'),
				'name' => 'coupon-status',
				'type' => 'select',
				'width' => '100',
				'options' => $coupon_status
			),
		),
	);

	tslot_build_datatable($headers, $title);
	tslot_build_confirm_modal();
	tslot_build_edit_modal($edit_fields);

}

// Updates appointment table
add_action( 'wp_ajax_tslot_update_coupons', 'tslot_update_coupons' );

function tslot_update_coupons() {

	if (!current_user_can('manage_options')) {
		wp_die();
	}

	if (!check_ajax_referer( 'ts-coupons-nonce', 'nonce' )) {
		wp_die();
	}

	global $wpdb;
	$ts_coupons_table = $wpdb->prefix . 'ts_coupons';
	$row_id = intval($_POST['rowid']);
	$coupon_code = sanitize_text_field($_POST['couponcode']);
	$discount_amount = floatval($_POST['discountamount']);
	$discount_type = sanitize_text_field($_POST['discounttype']);
	$coupon_status = sanitize_text_field($_POST['couponstatus']);

	$coupons_array = array(
		'coupon_code' => $coupon_code,
		'discount_amount' => $discount_amount,
		'discount_type' => $discount_type,
		'coupon_status' => $coupon_status,
	);

	// Updates existing coupon
	if ($row_id !== 0){
		$wpdb->update(
			$ts_coupons_table,
			$coupons_array,
			array('coupon_id' => $row_id)
		);
	}

	// Adds new coupon
	else {
		$wpdb->insert(
			$ts_coupons_table,
			$coupons_array
		);
	}

	wp_die();

}