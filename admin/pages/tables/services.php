<?php
/**
 * Displays service datatable
 *
 * Selects service data from database and displays
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

// Registers rest route and returns service datatable data
$service_data = new TimeSlot\ServiceData('services');

// Registers service category setting and
// builds repeater fields for modal
add_action( 'admin_init', 'tslot_service_cats_init' );

function tslot_service_cats_init() {

	add_settings_section( 'service-cats-section',  '', '', 'timeslot-service-categories' );
	add_settings_field( 'timeslot-service-cats', '', 'tslot_service_cat_repeater_callback', 'timeslot-service-categories', 'service-cats-section');
	register_setting('timeslot-service-categories', 'timeslot-service-categories');

}

// Builds repeater fields
function tslot_service_cat_repeater_callback($args){

	$options = (array) get_option('timeslot-service-categories');
	$service_repeater = !empty($options['service-category']) ? $options['service-category'] : $options;
	$i = 1;

	foreach($service_repeater as $repeater) { ?>

		<div class='ts-repeating'>

			<input type='text' class='ts-input' name='timeslot-service-categories[service-category][<?php esc_attr_e($i); ?>][category]' value='<?php echo isset( $repeater["category"] ) ? esc_attr( $repeater["category"] ) : ""; ?>'/>

			<button type='button' class='ts-remove' name='remove-<?php esc_attr_e($i); ?>' aria-label='<?php esc_html_e('Remove Category', 'timeslot');?>'>
				<span class='dashicons dashicons-no-alt' aria-hidden='true'>
			</button>

		</div>

		<?php $i++;

	}

	?>
	<p>
		<button type='button' class='ts-repeat' name='add-<?php esc_attr_e($i); ?>' aria-label='<?php esc_html_e('Add Category', 'timeslot');?>'>
			<span class='dashicons dashicons-plus' aria-hidden='true'></span>
		</button>
	</p>
	<?php

}

// Displays service datatable and editor modal
function tslot_services_tab_init(){

	$title = __('Services', 'timeslot');

	$headers = array(
		__('ID', 'timeslot'),
		__('Service', 'timeslot'),
		__('Price', 'timeslot'),
		__('Duration', 'timeslot'),
		__('Before', 'timeslot'),
		__('Category', 'timeslot'),
		__('Visibility', 'timeslot'),
		__('Notes', 'timeslot')
	);

	$visibility = array(
		__('Visible', 'timeslot'),
		__('Hidden', 'timeslot'),
	);

	$ts_locale = get_locale();
	$payment_options = get_option('timeslot-payment-methods-tab');
	$currency_code = $payment_options['currency'];
	$price_i18n = __('Price', 'timeslot');

	$ts_nf = new NumberFormatter($ts_locale, NumberFormatter::CURRENCY);
	$ts_nf -> setTextAttribute(NumberFormatter::CURRENCY_CODE , $currency_code);
	$ts_symbol  = $ts_nf -> getSymbol(NumberFormatter::CURRENCY_SYMBOL);
	$ts_symbol_string = $price_i18n . ' ( ' . $ts_symbol . ' )';

	$step = new TimeSlot\FractionDigits();
	$step -> get_step();
	$decimal = $step->step;

	$service_cat_option = get_option('timeslot-service-categories');
	$ts_service_cat = !empty($service_cat_option) ? array_column($service_cat_option['service-category'], 'category') : array();

	$edit_fields = array(
		'title' => __('service', 'timeslot'),
		'name' => 'service',
		'fields' => array(
			array(
				'name' => 'service',
				'type' => 'text',
				'width' => '50'
			),
			array(
				'label' => $ts_symbol_string,
				'name' => 'price',
				'type' => 'number',
				'width' => '50',
				'atts' => array(
					'min' => '0',
					'inputmode' => 'decimal',
					'lang' => $ts_locale,
					'step' => $decimal
				)
			),
			array(
				'name' => 'duration',
				'type' => 'duration',
				'width' => '50'
			),
			array(
				'label' => __('Before Service', 'timeslot'),
				'name' => 'before',
				'type' => 'duration',
				'width' => '50'
			),
			array(
				'name' => 'category',
				'type' => 'select',
				'width' => '50',
				'options' => $ts_service_cat
			),
			array(
				'id' => 'ts-service-visibility',
				'name' => 'visibility',
				'type' => 'select',
				'width' => '50',
				'options' => $visibility
			),
			array(
				'name' => 'notes',
				'type' => 'textarea',
				'width' => '100',
			),
		),
	);

	tslot_build_datatable($headers, $title);
	tslot_build_confirm_modal();
	tslot_build_edit_modal($edit_fields);

	?>

	<!-- Service Categories Modal -->
	<div id='ts-modal--service-category' class='modal ts-modal' aria-hidden='true'>
		<div class='modal-dialog ts-modal-dialog modal-dialog-centered' tabindex='-1' data-micromodal-close>
			<div class='ts-modal-content' role='dialog' aria-labelledby='ts-modal-title--service-category' aria-describedby='ts-modal-desc--service-category' aria-modal='true'>

				<div class='ts-modal-header'>
					<h5 id='ts-modal-title--service-category' class='ts-modal-title'><?php esc_html_e('Service Categories', 'timeslot');?></h5>
					<button type='button' class='ts-btn ts-close' data-micromodal-close aria-label='<?php esc_html_e('Close', 'timeslot');?>'><span class="dashicons dashicons-no-alt" aria-hidden="true"></span></button>
					<p id='ts-modal-desc--service-category'><?php esc_html_e('Add and remove service categories', 'timeslot');?></p>
					<div id='ts-success-msg--service-category' role='alert'></div>
				</div>

				<div class='ts-modal-body'>
					<form method='post' action='<?php echo esc_url('options.php'); ?>' id='ts-form--service-category' class='ts-load' aria-label='Edit Service Categories'>
						<?php
						settings_fields( 'timeslot-service-categories' );
						TimeSlot\AdminPages::tslot_do_settings_sections('timeslot-service-categories');
						submit_button( esc_html__( 'Save Categories', 'timeslot' ),'ts-btn', 'ts-submit-cats', false, array( 'id' => 'ts-save-service-cats', 'onclick' => 'tscatsubmit()'));
						?>
					</form>
				</div>

				<div class='ts-modal-footer'>
					<button type='button' class='ts-btn ts-close' data-micromodal-close aria-label='<?php esc_html_e('Close', 'timeslot');?>'><?php esc_html_e('Close', 'timeslot');?></button>
				</div>

			</div>
		</div>
	</div>
	<?php

}

// Updates services table
add_action( 'wp_ajax_tslot_update_service', 'tslot_update_service' );

function tslot_update_service() {

	if (!current_user_can('manage_options')) {
		wp_die();
	}

	if (!check_ajax_referer( 'ts-services-nonce', 'nonce' )) {
		wp_die();
	}

	global $wpdb;
	$ts_service_table = $wpdb->prefix . 'ts_services';
	$ts_staff_service_table = $wpdb->prefix . 'ts_staff_services';

	$price_post = sanitize_text_field($_POST['price']);
	$curr = new TimeSlot\I18nCurrency($price_post);
	$curr -> to_min_unit();
	$price = $curr->price;

	$row_id = intval($_POST['rowid']);
	$service = sanitize_text_field($_POST['service']);
	$duration = intval($_POST['duration']);
	$before_service = intval($_POST['beforeservice']);
	$category = sanitize_text_field($_POST['category']);
	$visibility = sanitize_text_field($_POST['visibility']);
	$info = sanitize_text_field($_POST['info']);

	$service_exists = 
		$wpdb->get_var(
		$wpdb->prepare(
			"SELECT service_title 
			FROM {$ts_service_table} 
			WHERE service_title = %s", 
			$service
	));

	$service_array = array(
		'service_title' => $service,
		'price' => $price,
		'duration' => $duration,
		'before_service' => $before_service,
		'category' => $category,
		'visibility' => $visibility,
		'info' => $info,
	);

	$is_service = !is_null($service_exists) ? true : false;

	$service_continue = [];
	$service_continue['service_exists'] = false;

	// Updates existing service
	if ($row_id !== 0){

		echo wp_json_encode($service_continue);

		$wpdb->update(
			$ts_staff_service_table,
			array('service_title' => $service),
			array('service_id' => $row_id)
		);
		$wpdb->update(
			$ts_service_table,
			$service_array,
			array('service_id' => $row_id)
		);
	}

	else if ($is_service){

		$service_continue['service_exists'] = true;
		echo wp_json_encode($service_continue);
		wp_die();

	}

	// Adds new service
	else {

		echo wp_json_encode($service_continue);

		$wpdb->insert(
			$ts_service_table,
			$service_array
		);

		// I18n strings
		if (has_action('wpml_register_single_string')){
			$wpml_name = 'Time Slot Service '. $service;
			do_action( 'wpml_register_single_string', 'timeslot', $wpml_name, $service );
		}
	}

	wp_die();

}