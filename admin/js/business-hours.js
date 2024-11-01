/**
 * Toggles business hour sections
 * 
 * Disables business hour inputs on checkbox click.
 * References Business Settings > Business Hours Tab.
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */

jQuery(function() {

	// TimePicker
	jQuery('.ts-timepicker').timepicker({
		'timeFormat': tshours.timeformat
	});

	// Holiday DatePicker
	jQuery('.ts-holiday-date').each(function() {

		jQuery(this).datepicker({
			altField: jQuery(this).next('.ts-format-holiday-date'),
			altFormat: 'yy-mm-dd',
			beforeShow: function() {
				jQuery('#ui-datepicker-div').addClass('ts-datepicker');
			},
			dateFormat: tshours.dateformat,
			firstDay: tshours.startofweek,
			minDate: 0,
			nextText: '',
			prevText: '',
			showAnim: '',
		})

	});

	// Toggles disabled property on time inputs
	const weekdays = [
		'monday',
		'tuesday',
		'wednesday',
		'thursday',
		'friday',
		'saturday',
		'sunday'
	];

	jQuery.each(weekdays, function(key, weekday){

		const breakInput = jQuery(`.ts-break.ts-${weekday}-break`);
		const closedCheck = jQuery(`.ts-closed.ts-${weekday}`);
		const hoursInput = jQuery(`.ts-business-hours.ts-${weekday}`);
		const breakCheck = jQuery(`.ts-break-chk.ts-${weekday}`);
		const breakLabel = jQuery(`.ts-${weekday} .ts-checkbox-label.ts-break-label`);
		const breakWrapper = jQuery(`.ts-break-wrapper.ts-${weekday}`);

		function hoursEnabled(){
			jQuery([hoursInput, breakCheck]).each(function(){
				jQuery(this).prop('disabled', false);
			});
			breakLabel.removeClass('ts-disabled');
		}

		function hoursDisabled(){
			jQuery([hoursInput, breakInput, breakCheck]).each(function(){
				jQuery(this).prop('disabled', true).prop('value','');
			});
			breakCheck.prop('checked', false);
			breakLabel.addClass('ts-disabled');
		}

		closedCheck.is(':checked') ? hoursDisabled() : hoursEnabled();

		closedCheck.on('click', function() {

			if (closedCheck.is(':checked')) {
				hoursDisabled();
				breakWrapper.hide();

				if(hoursInput.hasClass('ts-error')){
					hoursInput.removeClass('ts-error').removeAttr('aria-invalid');
					hoursInput.prev('.ts-error').remove();
				}
			}
			else {
				hoursEnabled();
			}
		})

		// Show and hide break time inputs
		if (breakCheck.is(':checked')) {
			breakWrapper.show();
			breakInput.prop('disabled', false);
		}
		else {
			breakWrapper.hide();
			breakInput.prop('disabled', true).prop('value','');
		}

		breakCheck.on('click', function() {
			breakWrapper.toggle();
			breakInput.prop('disabled', (breakInput.prop('disabled') == true ? false : true)).prop('value','');
		})
	})
})