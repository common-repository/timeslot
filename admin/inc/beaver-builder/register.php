<?php
/**
 * Registers Beaver Builder module
 *
 * Registers booking form Beaver Builder module
 * and enqueues styles and scripts for editor
 *
 * @link https://timeslotplugins.com
 * @package Time Slot
 * @since 1.1.9
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class TimeSlotBookingFormBBModule extends FLBuilderModule {

	public function __construct(){

		parent::__construct(array(
			'name'            => __( 'Time Slot Booking Form', 'timeslot' ),
			'description'     => __( 'Add the Time Slot booking form to your page or post', 'timeslot' ),
			'category'        => __( 'Time Slot', 'timeslot' ),
			'dir'             => TIMESLOT_PATH . '/admin/inc/beaver-builder/',
			'url'             => TIMESLOT_URL . '/admin/inc/beaver-builder/',
			'icon'            => 'editor-table.svg',
			'partial_refresh' => true,
			'editor_export'   => false,
		));

		$this->add_css('ts-bb', $this->url . 'css/bb.min.css');
		$this -> tslot_bb_active_check();
	}

	public function tslot_bb_active_check() {
		if (class_exists('FLBuilderModel') && FLBuilderModel::is_builder_active()){
			wp_add_inline_style('select2', '.fl-builder-edit .ts-form-wrapper{pointer-events:none;} .fl-builder-edit .ts-form-wrapper .ts-form .ts-fieldset .ts-coupon-code {display: none;}');
			wp_add_inline_script( 'select2', "jQuery('.ts-form input').attr('disabled', true);jQuery('.ts-form .ts-select').select2({disabled: true,selectionCssClass: 'ts-select2-container'});jQuery('.ts-form-wrapper').removeClass('ts-load');");
		}
	}
}

FLBuilder::register_module( 'TimeSlotBookingFormBBModule', array());