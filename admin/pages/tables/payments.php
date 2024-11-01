<?php
/**
 * Displays payments datatable
 *
 * Selects payment data from database and displays
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

// Registers rest route and returns payment datatable data
$payment_data = new TimeSlot\PaymentData('payments');

// Displays payment datatable and editor modal
function tslot_payments_tab_init(){

	$ts_locale = get_locale();
	$payment_options = get_option('timeslot-payment-methods-tab');
	$currency_code = $payment_options['currency'];
	$amount_i18n = __('Amount', 'timeslot');

	$ts_nf = new NumberFormatter($ts_locale, NumberFormatter::CURRENCY);
	$ts_nf -> setTextAttribute(NumberFormatter::CURRENCY_CODE , $currency_code);
	$ts_symbol  = $ts_nf -> getSymbol(NumberFormatter::CURRENCY_SYMBOL);
	$ts_symbol_string = $amount_i18n . ' ( ' . $ts_symbol . ' )';

	$step = new TimeSlot\FractionDigits();
	$step -> get_step();
	$decimal = $step->step;

	$title = __('Payments', 'timeslot');

	$headers = array(
		__('ID', 'timeslot'),
		__('Amount', 'timeslot'),
		__('Created', 'timeslot'),
		__('Customer', 'timeslot'),
		__('Source', 'timeslot'),
		__('Status', 'timeslot'),
		__('Coupon', 'timeslot'),
		__('Refund', 'timeslot'),
		__('Edit', 'timeslot'),
	);

	$status = array(
		__('Complete', 'timeslot'),
		__('Refunded', 'timeslot'),
	);

	$edit_fields = array(
		'title' => __('payment', 'timeslot'),
		'name' => 'payment',
		'fields' => array(
			array(
				'label' => $ts_symbol_string,
				'name' => 'amount',
				'type' => 'number',
				'width' => '50',
				'atts' => array(
					'readonly' => 'readonly',
					'inputmode' => 'decimal',
					'lang' => $ts_locale,
					'step' => $decimal
				)
			),
			array(
				'name' => 'created',
				'type' => 'text',
				'width' => '50',
				'atts' => array(
					'readonly' => 'readonly'
				)
			),
			array(
				'name' => 'customer',
				'type' => 'text',
				'width' => '50',
				'atts' => array(
					'readonly' => 'readonly'
				)
			),
			array(
				'name' => 'source',
				'type' => 'text',
				'width' => '50',
				'atts' => array(
					'readonly' => 'readonly'
				)
			),
			array(
				'name' => 'status',
				'type' => 'select',
				'width' => '50',
				'options' => $status,
			),
			array(
				'name' => 'coupon',
				'type' => 'text',
				'width' => '50',
				'atts' => array(
					'readonly' => 'readonly'
				)
			),
		),
	);

	tslot_build_datatable($headers, $title);
	tslot_build_edit_modal($edit_fields);

	?>

	<!-- Refund and Delete confirm modal -->
	<div id='ts-modal-confirm--payment' class='modal ts-modal ts-modal-confirm' tabindex='-1' role='dialog' aria-label='<?php esc_html_e('Confirm', 'timeslot');?>' aria-modal='true' aria-describedby='ts-confirm-msg--payment'>
		<div class='modal-dialog ts-modal-dialog modal-dialog-centered' role='document'>
			<div class='ts-modal-content'>
				<div class='ts-modal-body'>
					<p id='ts-confirm-msg--payment' class='ts-confirm-msg'></p>
				</div>
				<div class='ts-modal-footer'>
					<button type='button' class='ts-btn ts-close' data-micromodal-close aria-label='<?php esc_html_e('No, Close Dialog', 'timeslot');?>'><?php esc_html_e('No', 'timeslot');?></button>
					<button type='submit' class='ts-btn ts-submit ts-submit-confirm' aria-label='<?php esc_html_e('Yes, Continue', 'timeslot');?>'><?php esc_html_e('Yes', 'timeslot');?></button>
				</div>
			</div>
		</div>
	</div>

	<!-- Refund Unavailable Modal -->
	<div id='ts-modal-confirm--refund-unavailable' class='modal ts-modal ts-modal-confirm-no-refund' tabindex='-1' role='dialog' aria-label='Refund Unavailable' aria-modal='true' aria-describedby='ts-confirm-msg--refund-unavailable'>
		<div class='modal-dialog ts-modal-dialog modal-dialog-centered' role='document'>
			<div class='ts-modal-content'>
				<div class='ts-modal-body'>
					<p id='ts-confirm-msg--refund-unavailable' class='ts-confirm-msg'></p>
				</div>
				<div class='ts-modal-footer'>
					<button type='button' class='ts-btn ts-close' data-micromodal-close aria-label='<?php esc_html_e('Accept and Close', 'timeslot');?>'><?php esc_html_e('OK', 'timeslot');?></button>
				</div>
			</div>
		</div>
	</div>
	<?php
}

// Updates payment table
add_action( 'wp_ajax_tslot_update_payment', 'tslot_update_payment' );

function tslot_update_payment() {

	if (!current_user_can('manage_options')) {
		wp_die();
	}

	if (!check_ajax_referer( 'ts-payment-nonce', 'nonce' )) {
		wp_die();
	}

	global $wpdb;
	$ts_payment_table = $wpdb->prefix . 'ts_payments';
	$row_id = intval($_POST['rowid']);
	$status = sanitize_text_field($_POST['status']);

	$payment_array = array(
		'created' => current_time('mysql'),
		'payment_status' => $status,
	);

	// Updates existing payment
	if ($row_id !== 0){
		$wpdb->update(
			$ts_payment_table,
			$payment_array,
			array('payment_id' => $row_id)
		);
	}

	wp_die();

}