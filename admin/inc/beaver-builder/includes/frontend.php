<?php
/**
 * Displays Beaver Builder module
 *
 * Runs shortcode to disply booking form
 * and enqueues scripts and styles
 *
 * @link https://timeslotplugins.com
 * @package Time Slot
 * @since 1.1.9
 * 
 */

$styles = new TimeSlot\EnqueueFrontend();
$styles -> tslot_universal_scripts();
$styles -> tslot_frontend_styles();

if (class_exists('FLBuilderModel') && !FLBuilderModel::is_builder_active()){
	$styles -> tslot_localize_form();
	$styles -> tslot_dependencies();
	$styles -> tslot_payment_scripts();
	$styles -> tslot_set_script_translations();
}

echo do_shortcode('[timeslot-form]');