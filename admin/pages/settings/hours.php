<?php
/**
 * Configures business hour settings
 *
 * Creates business hour option settings, callbacks,
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

// Initializes business hour settings
add_action( 'admin_init', 'tslot_business_hours_init' );

function tslot_business_hours_init() {

	add_settings_section( 
		'biz-hours-section',
		'',
		'tslot_hours_header_callback',
		'timeslot-business-hours-tab'
	);

	add_settings_section(
		'holidays-section',
		__('Holidays', 'timeslot'),
		'tslot_holidays_callback',
		'timeslot-business-hours-tab'
	);

	add_settings_field(
		'timeslot-business-hours',
		'',
		'tslot_hours_callback',
		'timeslot-business-hours-tab',
		'biz-hours-section',
		array('class' => 'ts-settings-row')
	);

	add_settings_field(
		'timeslot-holidays',
		'',
		'tslot_repeater_callback',
		'timeslot-business-hours-tab',
		'holidays-section',
		array('num' => 'yes')
	);

	register_setting('timeslot-business-hours-group', 'timeslot-business-hours', 'tslot_hours_validation');
	register_setting('timeslot-business-hours-group', 'timeslot-holidays', 'tslot_holiday_validation');

}

// Headers
function tslot_hours_header_callback() {

	$h1_hours = __('Business Hours', 'timeslot');
	$h2_hours = __('Hours of Operation', 'timeslot');
	$subtitle_hours = __('The hours of operation and holiday days off for your business.', 'timeslot');
	$hours_info_title = __('Business Hours Info', 'timeslot');
	?>

	<h1><?php esc_html_e($h1_hours) ?></h1>
	<p class='ts-subtitle'><?php esc_html_e($subtitle_hours)?></p>
	<span class='ts-info-wrapper'>
		<h2 class='ts-hdr-w-info'><?php esc_html_e($h2_hours) ?></h2>
		<span class='dashicons dashicons-info ts-info-icon' id='ts-hours-info-icon' title='<?php esc_html_e($hours_info_title) ?>' tabindex='0'>
		</span>
	</span>
	<p class='ts-hours-info ts-info-toggle' role='alert'>

		<?php
		$hours_info_string = __('The default business hours are 9am to 5pm daily.', 'timeslot');
		esc_html_e($hours_info_string);
		?>

	</p>
	<?php

}

// Hours of operation
function tslot_hours_callback($args) {

	$days_of_week_group = array(
		'monday',
		'tuesday',
		'wednesday',
		'thursday',
		'friday',
		'saturday',
		'sunday'
	);

	foreach ($days_of_week_group as $ts_day){

		$options = (array) get_option('timeslot-business-hours');
		$dailyhours = $options[$ts_day] ?? '';
		$ts_day_upper = ucfirst($ts_day);
		$day_class = 'ts-' . $ts_day;
		$break_class = 'ts-' . $ts_day . '-break';
		$start_id = 'timeslot-business-hours-' . $ts_day . '-start-hour';
		$end_id = 'timeslot-business-hours-' . $ts_day . '-end-hour';
		$start_name = 'timeslot-business-hours[' . $ts_day . '][start-hour]';
		$end_name = 'timeslot-business-hours[' . $ts_day . '][end-hour]';
		$break_start_id = 'timeslot-business-hours-' . $ts_day . '-break-start';
		$break_end_id = 'timeslot-business-hours-' . $ts_day . '-break-end';
		$break_start_name = 'timeslot-business-hours[' . $ts_day . '][break-start]';
		$break_end_name = 'timeslot-business-hours[' . $ts_day . '][break-end]';
		$break_name = 'timeslot-business-hours[' . $ts_day . '][break]';
		$closed_name = 'timeslot-business-hours[' . $ts_day . '][closed]';
		$start_val = isset($dailyhours['start-hour']) ? esc_attr($dailyhours['start-hour']) : '';
		$end_val = isset($dailyhours['end-hour']) ? esc_attr($dailyhours['end-hour']) : '';
		$break_start_val = isset($dailyhours['break-start']) ? esc_attr($dailyhours['break-start']) : '';
		$break_end_val = isset($dailyhours['break-end']) ? esc_attr($dailyhours['break-end']) : '';
		$closed_val = !isset($dailyhours['closed']) ? 0 : $dailyhours['closed'];
		$break_val = !isset($dailyhours['break']) ? 0 : $dailyhours['break'];
		$start_i18n = __(' Start Time', 'timeslot');
		$end_i18n = __(' End Time', 'timeslot');
		$break_i18n = __('Break', 'timeslot');
		// translators: %s is a placeholder for day name
		$closed_i18n = sprintf(__('Closed every %s', 'timeslot'), $ts_day_upper);
		$wp_option_time_format = get_option('time_format');
		$start_placeholder = DateTimeImmutable::createFromFormat('g:i a','9:00 am')->format($wp_option_time_format);
		$end_placeholder = DateTimeImmutable::createFromFormat('g:i a','5:00 pm')->format($wp_option_time_format);
		$break_start_placeholder = DateTimeImmutable::createFromFormat('g:i a','11:00 am')->format($wp_option_time_format);
		$break_end_placeholder = DateTimeImmutable::createFromFormat('g:i a','1:00 pm')->format($wp_option_time_format);
		?>

		<span class="ts-hours-wrapper <?php esc_attr_e($day_class); ?>">

			<span class="ts-workday-wrapper <?php esc_attr_e($day_class); ?>">
				<label class="ts-day-label"><?php esc_html_e($ts_day_upper); ?></label>

				<span class="ts-time-wrapper">
					<input type='text' id='<?php esc_attr_e($start_id); ?>' class='ts-business-hours ts-timepicker <?php esc_attr_e($day_class); ?>' name='<?php esc_attr_e($start_name); ?>' value='<?php esc_attr_e($start_val); ?>' placeholder='<?php esc_attr_e($start_placeholder); ?>' aria-label='<?php esc_attr_e($ts_day_upper) . esc_attr_e($start_i18n);?>'/>

					<span> <?php esc_html_e('to', 'timeslot');?> </span>

					<input type='text' id='<?php esc_attr_e($end_id); ?>' class='ts-business-hours ts-timepicker <?php esc_attr_e($day_class); ?>' name='<?php esc_attr_e($end_name); ?>' value='<?php esc_attr_e($end_val); ?>' placeholder='<?php esc_attr_e($end_placeholder); ?>' aria-label='<?php esc_attr_e($ts_day_upper) . esc_attr_e($end_i18n);?>'/>
				</span>

				<span class="ts-check-wrapper">
					<label class='ts-inline ts-checkbox-label'>
						<span><?php esc_html_e('Closed', 'timeslot');?></span>
						<input type='checkbox' class='ts-closed <?php esc_attr_e($day_class); ?>' name='<?php esc_attr_e($closed_name); ?>' value=1 <?php checked(1, $closed_val, true); ?> aria-label='<?php esc_attr_e($closed_i18n)?>'/>
						<span class='ts-checkmark'></span>
					</label>

					<label class='ts-inline ts-checkbox-label'>
						<span><?php esc_html_e($break_i18n);?></span>
						<input type='checkbox' class='ts-break-chk <?php esc_attr_e($day_class); ?>' name='<?php esc_attr_e($break_name); ?>' value=1 <?php checked(1, $break_val, true); ?> aria-label='<?php esc_attr_e($break_i18n)?>'/>
						<span class='ts-checkmark'></span>
					</label>
				</span>
			</span>
			<span class="ts-break-wrapper ts-time-wrapper <?php esc_attr_e($day_class); ?>">
				<label class="ts-break-label"><?php esc_html_e($break_i18n); ?></label>

				<input type='text' id='<?php esc_attr_e($break_start_id); ?>' class='ts-break ts-timepicker <?php esc_attr_e($break_class); ?>' name='<?php esc_attr_e($break_start_name); ?>' value='<?php esc_attr_e($break_start_val); ?>' placeholder='<?php esc_attr_e($break_start_placeholder); ?>' aria-label='<?php esc_attr_e($ts_day_upper . " ") . esc_attr_e($break_i18n);?>'/>

				<span class='ts-break'> <?php esc_html_e('to', 'timeslot');?> </span>

				<input type='text' id='<?php esc_attr_e($break_end_id); ?>' class='ts-break ts-timepicker <?php esc_attr_e($break_class); ?>' name='<?php esc_attr_e($break_end_name); ?>' value='<?php esc_attr_e($break_end_val); ?>' placeholder='<?php esc_attr_e($break_end_placeholder); ?>' aria-label='<?php esc_attr_e($ts_day_upper . " ") . esc_attr_e($break_i18n);?>'/>
			</span>
		</span>
		<?php
	}
}

// Holiday description
function tslot_holidays_callback() {

	$holiday_desc = __('Add or remove additional days when your business will be closed.', 'timeslot');
	?>
	<p><?php esc_html_e($holiday_desc); ?></p>
	<?php
}

// Holiday repeater fields
function tslot_repeater_callback($args){

	$options = (array) get_option('timeslot-holidays');
	$customfields = !empty($options['closed']) ? $options['closed'] : $options;
	$i = 1;

	foreach($customfields as $field) {

		$checked = isset($field['annual']) ? 'checked="checked"' : '';
		$datepicker_id = 'timeslot-holidays[closed][' . $i . '][datepicker]';
		$date_id = 'timeslot-holidays[closed][' . $i . '][date]';
		$annual_id = 'timeslot-holidays[closed][' . $i . '][annual]';
		$datepicker_val = isset($field['datepicker']) ? esc_attr($field['datepicker']) : '';
		$date_val = isset($field['date']) ? esc_attr($field['date']) : '';
		?>

		<div class='ts-repeating ts-holidays'>

			<input type='text' id='<?php esc_attr_e($datepicker_id); ?>' class='ts-holiday-date' name='<?php esc_attr_e($datepicker_id); ?>' value='<?php esc_attr_e($datepicker_val); ?>' autocomplete='off' placeholder='<?php esc_attr_e('Select Date', 'timeslot');?>'>

			<input type='hidden' id='<?php esc_attr_e($date_id); ?>' class='ts-format-holiday-date' name='<?php esc_attr_e($date_id); ?>' value='<?php esc_attr_e($date_val); ?>'>

			<label class='ts-checkbox-label'>
				<?php _e('Annual', 'timeslot')?>
				<input type='checkbox' id='<?php esc_attr_e($annual_id); ?>' name='<?php esc_attr_e($annual_id); ?>' value='annual' <?php echo $checked; ?> aria-label='<?php esc_attr_e('Date closed annually', 'timeslot')?>'/>
				<span class='ts-checkmark'></span>
			</label>

			<button type='button' class='ts-remove' name='remove-<?php esc_attr_e($i); ?>' aria-label='<?php esc_attr_e('Remove Holiday', 'timeslot');?>'>
				<span class='dashicons dashicons-no-alt'></span>
			</button>

		</div>

		<?php $i++;

	}

	?>
	<p>
		<button type='button' class='ts-repeat' aria-label='<?php esc_html_e('Add Holiday', 'timeslot');?>'>
			<span class='dashicons dashicons-plus'></span>
			<span><?php esc_html_e('Add Holiday', 'timeslot');?></span>
		</button>
	</p>
	<?php

}

// Validation for business hours
function tslot_hours_validation($input) {

	$days_of_week_group = array(
		'monday',
		'tuesday',
		'wednesday',
		'thursday',
		'friday',
		'saturday',
		'sunday'
	);

	foreach ($days_of_week_group as $ts_day){

		if (isset($input[$ts_day]['start-hour'])){
			$input[$ts_day]['start-hour'] = sanitize_text_field($input[$ts_day]['start-hour']);
		}

		if (isset($input[$ts_day]['end-hour'])){
			$input[$ts_day]['end-hour'] = sanitize_text_field($input[$ts_day]['end-hour']);
		}

		if (isset($input[$ts_day]['break-start'])){
			$input[$ts_day]['break-start'] = sanitize_text_field($input[$ts_day]['break-start']);
		}

		if (isset($input[$ts_day]['break-end'])){
			$input[$ts_day]['break-end'] = sanitize_text_field($input[$ts_day]['break-end']);
		}
	}

	return $input;

}

// Validation for holidays
function tslot_holiday_validation($input) {

	if (!isset( $input['closed'])){
		return $input;
	}

	$i = 1;

	foreach( $input['closed'] as $closed){

		$closed_date = $input['closed'][$i]['date'];
		$d = DateTime::createFromFormat('Y-m-d', $closed_date);
		$input['closed'][$i]['date'] = ($d && $d->format('Y-m-d') == $closed_date) ? $closed_date : '';

		$closed_datepicker = $input['closed'][$i]['datepicker'];
		$input['closed'][$i]['datepicker'] = sanitize_text_field($closed_datepicker);

		if (isset( $input['closed'][$i]['annual'])){
			$input['closed'][$i]['annual'] = 'annual';
		}

		$i++;

	}

	return $input;

}