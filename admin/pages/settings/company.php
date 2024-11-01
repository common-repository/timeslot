<?php
/**
 * Configures company settings
 *
 * Creates company option settings, callbacks,
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

// Initializes company settings
add_action( 'admin_init', 'tslot_company_init' );

function tslot_company_init() {

	add_settings_section( 'company-section','', 'tslot_company_callback', 'timeslot-company-tab' );

	$settings = array(
		'company-info-section' => array(
			'title'=> esc_html__('Company Information', 'timeslot'),
			'page'=>'timeslot-company-tab',
			'fields'=> array(
				array(
					'id'=> 'company-logo',
					'title'=> esc_html__('Company Logo', 'timeslot'),
					'callback'=> 'tslot_company_logo_callback'
				),
				array(
					'id'=> 'company-name',
					'title'=> esc_html__('Company Name', 'timeslot'),
					'callback'=> 'tslot_company_name_callback'
				),
				array(
					'id'=> 'company-phone',
					'title'=> esc_html__('Phone', 'timeslot'),
					'callback'=> 'tslot_company_phone_callback'
				),
				array(
					'id'=> 'company-email',
					'title'=> esc_html__('Email', 'timeslot'),
					'callback'=> 'tslot_company_email_callback'
				),
				array(
					'id'=> 'company-website',
					'title'=> esc_html__('Website', 'timeslot'),
					'callback'=> 'tslot_company_website_callback'
				),
			)
		),
		'address-section' => array(
			'title'=>__('Address', 'timeslot'),
			'page'=>'timeslot-company-tab',
			'fields'=> array(
				array(
					'id'=> 'company-address-1',
					'title'=> esc_html__('Street Address', 'timeslot'),
					'callback'=> 'tslot_company_address1_callback'
				),
				array(
					'id'=> 'company-address-2',
					'title'=> esc_html__('Street Address 2', 'timeslot'),
					'callback'=> 'tslot_company_address2_callback'
				),
				array(
					'id'=> 'company-city',
					'title'=> esc_html__('City', 'timeslot'),
					'callback'=> 'tslot_company_city_callback',
				),
				array(
					'id'=> 'company-state',
					'title'=> esc_html__('State', 'timeslot'),
					'callback'=> 'tslot_company_state_callback'
				),
				array(
					'id'=> 'company-zip',
					'title'=> esc_html__('Zip Code', 'timeslot'),
					'callback'=> 'tslot_company_zip_callback'
				),
			)
		),
	);

	foreach( $settings as $id => $values){
		add_settings_section(
			$id,
			$values['title'],
			'',
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

		register_setting($values['page'], $values['page'], 'tslot_company_validation');

	}

}

// Headers
function tslot_company_callback() {

	$h1_company = __('Company', 'timeslot');
	$subtitle_company = __('Contact information for your business.', 'timeslot');
	echo('<h1>'. esc_html($h1_company).'</h1><p class="ts-subtitle">'. esc_html($subtitle_company) .'</p>');

}

// Logo
function tslot_company_logo_callback($args) {

	require ( TIMESLOT_PATH . 'admin/inc/media-uploader.php' );
	tslot_media_upload( 'company-logo', $width = 115, $height = 115 );

}

// Name
function tslot_company_name_callback($args) {

	$ts_company_name = get_option($args[0]);
	$ts_company_name_val = isset( $ts_company_name["" . $args[1] . ""] ) ? esc_attr( $ts_company_name["" . $args[1] . ""] ) : "";
	$ts_company_name_attr = $args[0] ."[" . $args[1] . "]";
	$ts_company_name_id = $args[1];

	?>
	<input type='text' id='<?php esc_attr_e($ts_company_name_id); ?>' class='ts-full-width' name='<?php esc_attr_e($ts_company_name_attr); ?>' value='<?php esc_attr_e($ts_company_name_val); ?>' autocomplete='organization' aria-label='<?php esc_attr_e($args['aria']); ?>'/>
	<?php

}

// Phone
function tslot_company_phone_callback($args) {

	$ts_company_phone = get_option($args[0]);
	$ts_company_phone_val = isset( $ts_company_phone["" . $args[1] . ""] ) ? esc_attr( $ts_company_phone["" . $args[1] . ""] ) : "";
	$ts_company_phone_name = $args[0] ."[" . $args[1] . "]";
	$ts_company_phone_id = $args[1];

	?>
	<input type='tel' id='<?php esc_attr_e($ts_company_phone_id); ?>' name='<?php esc_attr_e($ts_company_phone_name); ?>' value='<?php esc_attr_e($ts_company_phone_val); ?>' autocomplete='tel' aria-label='<?php esc_attr_e($args['aria']); ?>'/>
	<?php

}

// Email
function tslot_company_email_callback($args) {

	$ts_company_email = get_option($args[0]);
	$ts_company_email_val = isset( $ts_company_email["" . $args[1] . ""] ) ? esc_attr( $ts_company_email["" . $args[1] . ""] ) : "";
	$ts_company_email_name = $args[0] ."[" . $args[1] . "]";
	$ts_company_email_id = $args[1];

	?>
	<input type='email' id='<?php esc_attr_e($ts_company_email_id); ?>' name='<?php esc_attr_e($ts_company_email_name); ?>' value='<?php esc_attr_e($ts_company_email_val); ?>' autocomplete='email' aria-label='<?php esc_attr_e($args['aria']); ?>' />
	<?php

}

// Website
function tslot_company_website_callback($args) {

	$ts_company_website = get_option($args[0]);
	$ts_company_website_val = isset( $ts_company_website["" . $args[1] . ""] ) ? esc_attr( $ts_company_website["" . $args[1] . ""] ) : "";
	$ts_company_website_name = $args[0] ."[" . $args[1] . "]";
	$ts_company_website_id = $args[1];

	?>
	<input type='url' id='<?php esc_attr_e($ts_company_website_id); ?>' class='ts-full-width' name='<?php esc_attr_e($ts_company_website_name); ?>' value='<?php esc_attr_e($ts_company_website_val); ?>' autocomplete='url' aria-label='<?php esc_attr_e($args['aria']); ?>' />
	<?php

}

// Address Line 1
function tslot_company_address1_callback($args) {

	$ts_company_address_1 = get_option($args[0]);
	$ts_company_address_1_val = isset( $ts_company_address_1["" . $args[1] . ""] ) ? esc_attr( $ts_company_address_1["" . $args[1] . ""] ) : "";
	$ts_company_address_1_name = $args[0] ."[" . $args[1] . "]";
	$ts_company_address_1_id = $args[1];

	?>
	<input type='text' id='<?php esc_attr_e($ts_company_address_1_id); ?>' class='ts-full-width' name='<?php esc_attr_e($ts_company_address_1_name); ?>' value='<?php esc_attr_e($ts_company_address_1_val); ?>' autocomplete='street-address' aria-label='<?php esc_attr_e($args['aria']); ?>' />
	<?php

}

// Address Line 2
function tslot_company_address2_callback($args) {

	$ts_company_address_2 = get_option($args[0]);
	$ts_company_address_2_val = isset( $ts_company_address_2["" . $args[1] . ""] ) ? esc_attr( $ts_company_address_2["" . $args[1] . ""] ) : "";
	$ts_company_address_2_name = $args[0] ."[" . $args[1] . "]";
	$ts_company_address_2_id = $args[1];

	?>
	<input type='text' id='<?php esc_attr_e($ts_company_address_2_id); ?>' class='ts-full-width' name='<?php esc_attr_e($ts_company_address_2_name); ?>' value='<?php esc_attr_e($ts_company_address_2_val); ?>' autocomplete='address-line2' aria-label='<?php esc_attr_e($args['aria']); ?>' />
	<?php

}

// City
function tslot_company_city_callback($args) {

	$ts_company_city = get_option($args[0]);
	$ts_company_city_val = isset( $ts_company_city["" . $args[1] . ""] ) ? esc_attr( $ts_company_city["" . $args[1] . ""] ) : "";
	$ts_company_city_name = $args[0] ."[" . $args[1] . "]";
	$ts_company_city_id = $args[1];

	?>
	<input type='text' id='<?php esc_attr_e($ts_company_city_id); ?>' class='ts-full-width' name='<?php esc_attr_e($ts_company_city_name); ?>' value='<?php esc_attr_e($ts_company_city_val); ?>' autocomplete='address-level2' aria-label='<?php esc_attr_e($args['aria']); ?>' />
	<?php

}

// State
function tslot_company_state_callback($args) {

	$ts_company_state = get_option($args[0]);
	$ts_company_state_val = isset(  $ts_company_state["" . $args[1] . ""] ) ? esc_attr(  $ts_company_state["" . $args[1] . ""] ) : "";
	$ts_company_state_name = $args[0] ."[" . $args[1] . "]";
	$ts_company_state_id = $args[1];

	?>
	<input type='text' id='<?php esc_attr_e($ts_company_state_id); ?>' class='ts-full-width' name='<?php esc_attr_e($ts_company_state_name); ?>' value='<?php esc_attr_e($ts_company_state_val); ?>' autocomplete='address-level1' aria-label='<?php esc_attr_e($args['aria']); ?>' />
	<?php

}

// Zip Code
function tslot_company_zip_callback($args) {

	$ts_company_zip = get_option($args[0]);
	$ts_company_zip_val = isset( $ts_company_zip["" . $args[1] . ""] ) ? esc_attr( $ts_company_zip["" . $args[1] . ""] ) : "";
	$ts_company_zip_name = $args[0] ."[" . $args[1] . "]";
	$ts_company_zip_id = $args[1];

	?>
	<input type='text' id='<?php esc_attr_e($ts_company_zip_id); ?>' class='ts-full-width' name='<?php esc_attr_e($ts_company_zip_name); ?>' value='<?php esc_attr_e($ts_company_zip_val); ?>' maxlength='20' autocomplete='postal-code' aria-label='<?php esc_attr_e($args['aria']); ?>' />
	<?php

}

// Validation
function tslot_company_validation($input) {

	if (isset( $input['company-logo'])){
		$input['company-logo'] = sanitize_file_name($input['company-logo']);
	}

	if (isset( $input['company-name'])){
		$input['company-name'] = sanitize_text_field($input['company-name']);
	}

	if (isset( $input['company-phone'])){
		$input['company-phone'] = sanitize_text_field($input['company-phone']);
	}

	if (isset( $input['company-website'])){
		$input['company-website'] = esc_url_raw($input['company-website']);
	}

	if (isset( $input['company-email'])){
		$input['company-email'] = sanitize_email($input['company-email']);
	}

	if (isset( $input['company-address-1'])){
		$input['company-address-1'] = sanitize_text_field($input['company-address-1']);
	}

	if (isset( $input['company-address-2'])){
		$input['company-address-2'] = sanitize_text_field($input['company-address-2']);
	}

	if (isset( $input['company-city'])){
		$input['company-city'] = sanitize_text_field($input['company-city']);
	}

	if (isset( $input['company-state'])){
		$input['company-state'] = sanitize_text_field($input['company-state']);
	}

	if (isset( $input['company-zip'])){
		$input['company-zip'] = sanitize_text_field($input['company-zip']);
	}

	return $input;

}