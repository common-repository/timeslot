<?php
/**
 * Sets up appointment form
 *
 * Initializes frontend appointment form,
 * adds shortcode.
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

// Check if any staff exist, then require relevant files
$count_staff = new TimeSlot\CountStaff();
$is_staff = $count_staff -> is_staff();

if ($is_staff){
	require (TIMESLOT_PATH . 'public/form/staff.php');
	require (TIMESLOT_PATH . 'public/form/any-staff.php');
}

$days_off = new TimeSlot\DaysOff();

require (TIMESLOT_PATH . 'public/form/coupons.php');
require (TIMESLOT_PATH . 'public/form/email.php');
require (TIMESLOT_PATH . 'public/form/submit.php');
require (TIMESLOT_PATH . 'public/form/times.php');

// Builds appointment form and registers shortcode
function tslot_form_shortcode($ts_block_atts) {

	ob_start();
	global $wpdb, $is_staff;

	// Form attributes
	$ts_form_id = 'ts-form-'. get_post_field( 'post_name');
	$block_class = has_block( 'timeslot/booking-form' ) && !empty($ts_block_atts['className']) ? $ts_block_atts['className'] : '';
	$load_class = is_admin() || defined( 'REST_REQUEST' ) && REST_REQUEST ? "ts-block-preview-wrapper" : "ts-load";

	// Messages
	$generaloptions = get_option('timeslot-general-tab');
	$summary_msg = $generaloptions['summary-msg'] ?? '';
	$success_msg_option = $generaloptions['success-msg'] ?? '';
	$success_msg = !empty($success_msg_option) ? $success_msg_option : esc_html__('Thank you for booking with us!', 'timeslot');
	$error_msg =  esc_html__('There was a problem submitting your appointment.', 'timeslot');

	// Tables
	$ts_services_table = $wpdb->prefix . 'ts_services';
	$ts_coupons_table = $wpdb->prefix . 'ts_coupons';
	?>

	<div id='<?php esc_attr_e($ts_form_id);?>' class='ts-form-wrapper <?php esc_attr_e($load_class . ' ' . $block_class);?>'>
		<form method='post' class='ts-form' aria-label='<?php esc_attr_e("Schedule an Appointment", "timeslot");?>'>
			<?php wp_nonce_field( 'ts_submit_form', 'ts_form_nonce' ); ?>
			<fieldset class='ts-fieldset ts-fieldset-form'>
				<div class='ts-fieldset-inner'>
					<div class='ts-input-wrapper'>
						<label for='ts-select-service'><?php esc_html_e('Service', 'timeslot');?></label>
						<select id='ts-select-service' class='ts-service ts-select' name='ts-service' title='<?php esc_attr_e("Please select a service.", "timeslot");?>' data-placeholder='<?php esc_attr_e("Select Service", "timeslot");?>' required>
							<option class='ts-placeholder'></option>

							<?php
							// I18n strings
							$i18n_strings = new TimeSlot\MultilangStrings();
							$visible_i18n = $i18n_strings -> visible_string;
							$active_i18n = $i18n_strings -> active_string;

							$service_rows = 
							$wpdb->get_results(
							$wpdb->prepare(
								"SELECT service_title, price 
								FROM {$ts_services_table} 
								WHERE visibility = %s",
								$visible_i18n
							));

							foreach($service_rows as $service_row){

								$service_value = $service_row->service_title;
								$i18n_strings -> set_i18n_service($service_value);
								$service_title = $i18n_strings -> service_string;

								$curr = new TimeSlot\I18nCurrency($service_row->price);
								$curr -> to_decimal();
								$service_price = $curr->price;

								?>
								<option value='<?php esc_attr_e($service_value); ?>' data-price='<?php esc_attr_e($service_price); ?>'><?php esc_html_e($service_title); ?></option>
								<?php
							}
							?>

						</select>

						<?php if ($is_staff){ ?>
						<label for='ts-select-staff'><?php esc_html_e('Staff', 'timeslot');?></label>
						<select id='ts-select-staff' class='ts-staff ts-select' name='ts-staff' title='<?php esc_attr_e("Please select a staff member.", "timeslot");?>' data-placeholder='<?php esc_attr_e("Select Staff", "timeslot");?>'>
							<option class='ts-placeholder'></option>
						</select>
						<?php } ?>

						<label for='ts-input-date'><?php esc_html_e('Appointment Date', 'timeslot');?></label>
						<input type='text' id='ts-input-date' class='ts-input-date' name='ts-date' placeholder='<?php esc_attr_e("Select Date", "timeslot");?>' inputmode='none'></input>
						<input id='ts-format-date' hidden></input>

						<label for='ts-select-time'><?php esc_html_e('Appointment Time', 'timeslot');?></label>
						<select id='ts-select-time' class='ts-time ts-select' name='ts-time' title='<?php esc_attr_e("Please select a time.", "timeslot");?>' data-placeholder='<?php esc_attr_e("Select Time", "timeslot");?>' data-minimum-results-for-search='0' required>
							<option class='ts-placeholder'></option>
						</select>

						<label for='ts-input-name'><?php esc_html_e('Full Name', 'timeslot');?></label>
						<input type='text' id='ts-input-name' class='ts-customer-info' name='ts-name' value='' autocomplete='name' placeholder='<?php esc_attr_e("Name", "timeslot");?>' required>

						<label for='ts-input-email'><?php esc_html_e('Email', 'timeslot');?></label>
						<input type='email' id='ts-input-email' class='ts-customer-info' name='ts-email' value='' autocomplete='email' placeholder='<?php esc_attr_e("Email", "timeslot");?>' required>

						<label for='ts-input-phone'><?php esc_html_e('Phone', 'timeslot');?></label>
						<input type='tel' id='ts-input-phone' class='ts-customer-info' name='ts-phone' value='' autocomplete='tel' placeholder='<?php esc_attr_e("Phone", "timeslot");?>' required>

						<?php
						$coupon_rows = 
							$wpdb->get_results(
							$wpdb->prepare(
								"SELECT coupon_code, discount_type, discount_amount, coupon_status 
								FROM {$ts_coupons_table} 
								WHERE coupon_status = %s",
								$active_i18n
						));

						if (($wpdb->num_rows) > 0){
							?>
							<label for='ts-checkbox-coupon' class='ts-checkbox-label'><?php esc_html_e('Have a Coupon Code?', 'timeslot');?>
								<input type='checkbox' id='ts-checkbox-coupon' class='ts-checkbox-coupon' name='ts-checkbox-coupon'>
								<span class='ts-checkmark'></span>
							</label>
							<label for='ts-input-coupon' class='ts-coupon-code'><?php esc_html_e('Coupon Code', 'timeslot');?></label>
							<input type='text' id='ts-input-coupon' class='ts-coupon-code ts-input-coupon' name='coupon' value='' data-discount='' data-discounttype='' placeholder='<?php esc_attr_e("Enter Coupon Code", "timeslot");?>'>
							<?php
						}
						?>

						<input type='button' id='ts-btn-next' class='ts-btn' value='<?php esc_attr_e("Continue", "timeslot");?>'>
					</div>
				</div>
			</fieldset>

			<fieldset class='ts-fieldset ts-fieldset-summary'>
				<div class='ts-summary'>

					<button type='button' id='ts-btn-prev' class='ts-btn-prev'>
						<i class='back-arrow' aria-hidden='true'>&#10094;</i> <?php esc_html_e('Previous', 'timeslot');?>
					</button>

					<h2 class='ts-summary-hdr'><?php esc_html_e('Appointment Summary', 'timeslot');?></h2>

					<div id='ts-summary-customer'>
						<h3><?php esc_html_e('Contact Info', 'timeslot');?></h3>
						<p id='ts-summary-name'></p>
						<p id='ts-summary-email'></p>
						<p id='ts-summary-phone'></p>
					</div>

					<div id='ts-summary-appt'>
						<h3><?php esc_html_e('Order Summary', 'timeslot');?></h3>
						<p><span id='ts-summary-service'></span><?php if ($is_staff){ ?><span id='ts-summary-staff'></span><?php } ?></p>
						<p id='ts-summary-date'></p>
						<p><span id='ts-summary-discount'></span><span id='ts-summary-price'></span></p>
					</div>

					<div id='ts-summary-msg'>
						<p><?php esc_html_e($summary_msg); ?></p>
						<p class='ts-success-msg' style='display:none;'><?php esc_html_e($success_msg); ?></p>
						<p class='ts-error-msg' style='display:none;'><?php esc_html_e($error_msg); ?></p>
					</div>
				</div>

				<input type='submit' id='ts-btn-submit' class='ts-btn' value='<?php esc_attr_e("Submit", "timeslot");?>'>
			</fieldset>
		</form>

		<div class='ts-dot-wrapper' style='display:none;'>
			<ul class='ts-dots' role='status'>
				<li class='ts-dot'></li>
				<li class='ts-dot'></li>
				<li class='ts-dot'></li>
			</ul>
		</div>
	</div>

	<?php
	return ob_get_clean();
}

add_shortcode( 'timeslot-form', 'tslot_form_shortcode' );