<?php
/**
 * Configures payment settings
 *
 * Creates payment option settings, callbacks,
 * and validations.
 *
 * @link https://timeslotplugin.com
 *
 * @package Time Slot
 * @since 1.0.7
 * 
 */

// Exit if accessed directly
if (!defined('ABSPATH')){
	exit;
}

// Initializes payment settings
add_action( 'admin_init', 'tslot_payment_init' );

function tslot_payment_init() {

	$settings = array(
		'payments-section' => array(
			'title'=> esc_html__('', 'timeslot'),
			'callback'=>'tslot_payments_callback',
			'page'=>'timeslot-payment-methods-tab',
			'fields'=> array(
				array(
					'id'=> 'pay-local',
					'title'=> esc_html__('Pay Locally', 'timeslot'),
					'callback'=> 'tslot_payment_switch_callback',
				),
			)
		),
		'currency-section' => array(
			'title'=> esc_html__('', 'timeslot'),
			'callback'=>'tslot_currency_section_callback',
			'page'=>'timeslot-payment-methods-tab',
			'fields'=> array(
				array(
					'id'=> 'currency',
					'title'=> esc_html__('Currency Code', 'timeslot'),
					'callback'=> 'tslot_currency_select_callback',
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
					'class' => 'ts-settings-row ts-'. $id .' ts-' . $field['id'] . '-field',
					'aria' => $field['title'],
				)
			);
		}

		register_setting($values['page'], $values['page'], 'tslot_payment_validation');

	}

	if ( get_option( 'timeslot-payment-methods-tab') === false ) {

		$defaults = array (
			'currency' => 'USD',
			'pay-local' => 1
		);

		update_option('timeslot-payment-methods-tab', $defaults);

	}

}

// Headers
function tslot_payments_callback() {

	// /* translators: Placeholder is URL https://timeslotplugins.com/ */
	$local_payments_string = __('Accept payments in person, or check out <a href="%s" target="_blank">Time Slot Pro</a> to accept payments online.', 'timeslot');
	$link_kses = array('a' => array('href' => array(),'target' => array()));
	$timeslot_url = esc_url('https://timeslotplugins.com/');
	$h1_payments = __('Payment Methods', 'timeslot');

	?>
	<h1><?php esc_html_e($h1_payments); ?></h1>
	<p class="ts-subtitle">
		<?php
		echo sprintf( wp_kses( $local_payments_string, $link_kses ), $timeslot_url );
		?>
	</p>
	<?php

}

// Switch checkboxes
function tslot_payment_switch_callback($args) {

	$ts_payment_switch = get_option($args[0]);
	$switch_id ='ts-'. $args[1] . '-chk';
	$switch_name = $args[0] .'[' . $args[1] . ']';
	$switch_val = isset($ts_payment_switch["" . $args[1] . ""]) ? esc_attr($ts_payment_switch["" . $args[1] . ""]) : 0;

	?>
	<span style='pointer-events: none;'>
		<label class='ts-switch'>
			<input type='checkbox' id='<?php esc_attr_e($switch_id); ?>' class='ts-payment-chk' name='<?php esc_attr_e($switch_name); ?>' value='1' <?php checked('1', '1');?> aria-label='<?php esc_attr_e($args['aria']); ?>' disabled/>
			<span class='ts-slider'></span>
		</label>
	<span>
	<?php

}

// Currency header
function tslot_currency_section_callback() {

	?>
	<h2><?php esc_html_e('Currency', 'timeslot');?></h2>
	<?php

}

// Currency select
function tslot_currency_select_callback($args) {

	$currency_code_option = get_option($args[0]);
	$currency_code_id = $args[1];
	$currency_code_name = $args[0] .'[' . $args[1] . ']';
	$currency_code_set = isset($currency_code_option["" . $args[1] . ""]) ? esc_attr($currency_code_option["" . $args[1] . ""]) : "";
	$currency_codes = array(
		__('Australian dollar (AUD)', 'timeslot') => 'AUD',
		__('Brazilian real (BRL)', 'timeslot') => 'BRL',
		__('Canadian dollar (CAD)', 'timeslot') => 'CAD',
		__('Chinese renmenbi (CNY)', 'timeslot') => 'CNY',
		__('Czech koruna (CZK)', 'timeslot') => 'CZK',
		__('Danish krone (DKK)', 'timeslot') => 'DKK',
		__('Euro (EUR)', 'timeslot') => 'EUR',
		__('Hong Kong dollar (HKD)', 'timeslot') => 'HKD',
		__('Hungarian forint (HUF)', 'timeslot') => 'HUF',
		__('Indian rupee (INR)', 'timeslot') => 'INR',
		__('Israeli new shekel (ILS)', 'timeslot') => 'ILS',
		__('Japanese yen (JPY)', 'timeslot') => 'JPY',
		__('Malaysian ringgit (MYR)', 'timeslot') => 'MYR',
		__('Mexican peso (MXN)', 'timeslot') => 'MXN',
		__('Moroccan dirham (MAD)', 'timeslot') => 'MAD',
		__('New Taiwan dollar (TWD)', 'timeslot') => 'TWD',
		__('New Zealand dollar (NZD)', 'timeslot') => 'NZD',
		__('Norwegian krone (NOK)', 'timeslot') => 'NOK',
		__('Philippine peso (PHP)', 'timeslot') => 'PHP',
		__('Polish złoty (PLN)', 'timeslot') => 'PLN',
		__('Pound sterling (GBP)', 'timeslot') => 'GBP',
		__('Russian ruble (RUB)', 'timeslot') => 'RUB',
		__('Singapore dollar (SGD)', 'timeslot') => 'SGD',
		__('South African rand (ZAR)', 'timeslot') => 'ZAR',
		__('Swedish krona (SEK)', 'timeslot') => 'SEK',
		__('Swiss franc (CHF)', 'timeslot') => 'CHF',
		__('Thai baht (THB)', 'timeslot') => 'THB',
		__('United States dollar (USD)', 'timeslot') => 'USD'
	);

	?>
	<select id='<?php esc_attr_e($currency_code_id); ?>' class='ts-full-width-select' name='<?php esc_attr_e($currency_code_name); ?>' aria-label='<?php esc_attr_e($args['aria']); ?>'>

		<option></option>

		<?php
		foreach ($currency_codes as $name => $code){ ?>
			<option value='<?php esc_attr_e($code); ?>' <?php selected($currency_code_set, $code); ?>><?php esc_html_e($name, 'timeslot'); ?></option>
			<?php
		} ?>

	</select>
	<?php

}

// Validation
function tslot_payment_validation($input){

	foreach ( $input as $key => $val ) {

		switch($key){

			case 'currency':
	
				$input[$key] = sanitize_text_field($input[$key]);
				break;

			case 'pay-local':

				$input[$key] = absint($input[$key]);
				break;

			default:
				break;

		}

	}

	return $input;

}