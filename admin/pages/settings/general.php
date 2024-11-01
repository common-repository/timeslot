<?php
/**
 * Configures general settings
 *
 * Creates general option settings, callbacks,
 * and validations.
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

// Initializes general settings
add_action( 'admin_init', 'tslot_general_init' );

function tslot_general_init() {

	$settings = array(
		'header-section' => array(
			'title'=> esc_html__('', 'timeslot'),
			'callback'=>'tslot_general_callback',
			'page'=>'timeslot-general-tab',
			'fields'=> array()
		),
		'appt-status-section' => array(
			'title'=>__('Appointment Settings', 'timeslot'),
			'callback' => '',
			'page'=>'timeslot-general-tab',
			'fields'=> array(
				array(
					'id'=> 'default-before-booking',
					'title'=> esc_html__('Minimum Time Before Booking', 'timeslot'),
					'callback'=> 'tslot_default_time_before_callback'
				),
			)
		),
		'form-fields-section' => array(
			'title'=> esc_html__('Appointment Form Fields', 'timeslot'),
			'callback'=>'',
			'page'=>'timeslot-general-tab',
			'fields'=> array(
				array(
					'id'=> 'summary-msg',
					'title'=> esc_html__('Summary Message', 'timeslot'),
					'callback'=> 'tslot_form_msg_callback'
				),
				array(
					'id'=> 'success-msg',
					'title'=> esc_html__('Success Message', 'timeslot'),
					'callback'=> 'tslot_form_msg_callback'
				),
			)
		),
	);

	foreach( $settings as $id => $values){
		add_settings_section(
			$id,
			$values['title'],
			$values['callback'],
			$values['page']
		);

		foreach ($values['fields'] as $field) {
			add_settings_field(
				$field['id'],
				$field['title'],
				$field['callback'],
				$values['page'],
				$id,
				array(
					$values['page'],
					$field['id'],
					'label_for' => $field['id'],
					'class' => 'ts-settings-row',
					'aria' => $field['title'],
				)
			);
		}

		register_setting($values['page'], $values['page'], 'tslot_general_validation');

	}

}

// Headers
function tslot_general_callback() {

	/* translators: Placeholder is URL https://timeslotplugins.com/docs/ */
	$guide_string = __('<p class="ts-subtitle">Copy and paste this shortcode into your content to show the Time Slot booking form. For more information, visit the <a href="%s" target="_blank">Time Slot documentation</a>.</p>', 'timeslot');
	$guide_kses = array('a' => array('href' => array(),'target' => array()),'p' => array('class' => array()),'h1' => array());
	$guide_docs_url = esc_url('https://timeslotplugins.com/docs/');
	$h1_general = __('General', 'timeslot');

	?>
	<h1><?php esc_html_e($h1_general);?></h1>
	<?php
	echo sprintf( wp_kses( $guide_string, $guide_kses ), $guide_docs_url );
	?>

	<p class='ts-subtitle'>
		<b><?php esc_html_e('Shortcode: ', 'timeslot');?></b>
		<span class='ts-copytext' data-copytext='[timeslot-form]'>[timeslot-form]</span>
	</p>

	<?php

}

// Minimum time before booking
function tslot_default_time_before_callback($args) {

	$ts_min_before = get_option($args[0]);
	$ts_min_before_num_val = $ts_min_before["" . $args[1] . ""]["num"] ?? '';
	$ts_min_before_sec_val = $ts_min_before["" . $args[1] . ""]["sec"] ?? '';
	$ts_min_before_num_name = $args[0] ."[" . $args[1] . "][num]";
	$ts_min_before_sec_name = $args[0] ."[" . $args[1] . "][sec]";
	$label_hours = __('Hours', 'timeslot');

	?>

	<span class='ts-unit-wrapper'>
		<input type='number' id='default-before-booking' class='ts-number' name='<?php esc_attr_e($ts_min_before_num_name); ?>' value='<?php esc_attr_e($ts_min_before_num_val); ?>' placeholder='5'  aria-label='<?php esc_attr_e($args['aria']); ?>'/>
		<label for='default-before-booking' class='ts-px-block'><?php esc_html_e($label_hours);?></label>
	</span>

	<input type='hidden' name='<?php esc_attr_e($ts_min_before_sec_name); ?>' value='<?php esc_attr_e($ts_min_before_sec_val); ?>' />

	<?php

}

// Summary and success messages
function tslot_form_msg_callback($args) {

	$ts_form_msg = get_option($args[0]);
	$ts_form_msg_val = isset( $ts_form_msg[""  . $args[1] . ""] ) ? esc_attr( $ts_form_msg[""  . $args[1] . ""] ) : "";
	$ts_form_msg_name = $args[0] ."[" . $args[1] . "]";
	$ts_form_msg_id = $args[1];

	?>
	<textarea id='<?php esc_attr_e($ts_form_msg_id); ?>' class='ts-full-width' name='<?php esc_attr_e($ts_form_msg_name); ?>' aria-label='<?php esc_attr_e($args['aria']); ?>'><?php esc_html_e($ts_form_msg_val);?></textarea>
	<?php

}

// Begin Validation
function tslot_general_validation($input){

	if (isset( $input['default-before-booking']['num'])){
		$input['default-before-booking']['num'] = intval($input['default-before-booking']['num']);
	}

	if (isset( $input['default-before-booking']['sec'])){
		$input['default-before-booking']['sec'] = intval($input['default-before-booking']['num'] * 3600);
	}

	if (isset( $input['summary-msg'])){
		$input['summary-msg'] = sanitize_text_field($input['summary-msg']);
	}

	if (isset( $input['success-msg'])){
		$input['success-msg'] = sanitize_text_field($input['success-msg']);
	}

	return $input;

}