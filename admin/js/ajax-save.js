/**
 * Submits ajax settings forms and validates
 *
 * @link https://timeslotplugins.com
 * @link https://jqueryvalidation.org/documentation/
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */

jQuery(function() {

	const { __ } = wp.i18n;

	//Form submit with success message
	function tssubmit(form) {

		var formId = jQuery(form).attr('id');
		if (formId === 'ts-email-form'){
			tinyMCE.triggerSave();
		}

		jQuery(form).ajaxSubmit({
			success: function(){
				jQuery('body').addClass('timeslot-saving');
			},
			timeout: 3000,
			error: function(xhr, text, e){
				console.log(text);
			}
		});

		setTimeout(() => {
			jQuery('body').removeClass('timeslot-saving');
		
		}, 5000);

		return false;
	}

	//Sets default admin form validation error requirements
	jQuery.validator.setDefaults({
		errorElement: 'label',
		errorClass: 'ts-error',
		errorPlacement: function(error, element) {
			if (element.hasClass('select2-hidden-accessible')) {
				error.insertAfter(element.next('span.select2'));
			}
			else if (element.parent().hasClass('ts-unit-wrapper')) {
				error.insertAfter(element.parent('.ts-unit-wrapper'));
			}
			else {
				error.insertAfter(element);
			}
		}
	});

	//Company form validation
	jQuery('#ts-company-form').validate({
		rules: {
			'timeslot-company-tab[company-phone]': {
				maxlength: 20
			},
			'timeslot-company-tab[company-email]': {
				email: true
			},
			'timeslot-company-tab[company-website]': {
				url: true
			},
		},
		messages: {
			'timeslot-company-tab[company-phone]': __('Please enter a valid phone number.', 'timeslot'),
			'timeslot-company-tab[company-email]': __('Please enter a valid email address.', 'timeslot'),
			'timeslot-company-tab[company-website]': __('Please include http:// in url.', 'timeslot')
		},
		submitHandler: function(form){tssubmit(form);}
	});

	//General form validation
	jQuery('#ts-general-form').validate({
		rules: {
			'timeslot-general-tab[purchase-code]': {
				pattern: /^(\w{8})-((\w{4})-){3}(\w{12})$/
			},
			'timeslot-general-tab[default-before-booking][num]': {
				digits: true
			},
		},
		messages: {
			'timeslot-general-tab[purchase-code]': {
				pattern: __('Please enter a valid purchase code.', 'timeslot')
			}
		},
		submitHandler: function(form){tssubmit(form);}
	});

	//Appearance form submit
	jQuery('#ts-appearance-form').on('submit',function(){
		tssubmit(jQuery(this));
		return false;
	});

	//Business hours form validation
	jQuery.validator.addClassRules({
		timepicker: {
			maxlength: 8,
		}
	});

	jQuery.validator.addMethod('hoursRequired', jQuery.validator.methods.require_from_group,
	__('Please enter your business hours.', 'timeslot'));
	jQuery.validator.addMethod('breakRequired', jQuery.validator.methods.require_from_group,
	__('Please enter your break hours.', 'timeslot'));

	const weekdays = [
		'monday',
		'tuesday',
		'wednesday',
		'thursday',
		'friday',
		'saturday',
		'sunday'
	];

	jQuery('#ts-business-hours-form').validate({
		errorPlacement: function(error, element) {
			if (element.hasClass('ts-business-hours')) {
				error.insertAfter(element.prevAll('.ts-day-label'));
			}
			if (element.hasClass('ts-break')) {
				error.insertAfter(element.prevAll('.ts-break-label'));
			}
		},
		groups: (function(){
			hourGroups = {};
			jQuery.each(weekdays, function(key, day){
				hourGroups[`${day}Hours`] = `timeslot-business-hours[${day}][start-hour] timeslot-business-hours[${day}][end-hour]`
				hourGroups[`${day}Breaks`] = `timeslot-business-hours[${day}][break-start] timeslot-business-hours[${day}][break-end]`
			})
			return hourGroups;
		})(),
		onkeyup: false,
		rules: (function(){
			hourRules = {};
			jQuery.each(weekdays, function(key, day){
				hourRules[`timeslot-business-hours[${day}][start-hour]`] = { hoursRequired: [2,`.ts-business-hours.ts-${day}`] };
				hourRules[`timeslot-business-hours[${day}][end-hour]`] = { hoursRequired: [2,`.ts-business-hours.ts-${day}`] };
				hourRules[`timeslot-business-hours[${day}][break-start]`] = { breakRequired: [2,`.ts-${day}-break`] };
				hourRules[`timeslot-business-hours[${day}][break-end]`] = { breakRequired: [2,`.ts-${day}-break`] };
			})
			return hourRules;
		})(),
		messages:(function(){
			hourMessages = {};
			jQuery.each(weekdays, function(key, day){
				hourMessages[`timeslot-business-hours[${day}][start-hour]`] = { maxlength: __('Please enter a valid time.', 'timeslot') },
				hourMessages[`timeslot-business-hours[${day}][end-hour]`] = { maxlength: __('Please enter a valid time.', 'timeslot') },
				hourMessages[`timeslot-business-hours[${day}][break-start]`] = { maxlength: __('Please enter a valid time.', 'timeslot') },
				hourMessages[`timeslot-business-hours[${day}][break-end]`] = { maxlength: __('Please enter a valid time.', 'timeslot') }
			})
			return hourMessages;
		})(),
		submitHandler: function(form){tssubmit(form);}
	});

	//Payment methods form submit
	jQuery('#ts-payment-methods-form').on('submit',function(){
		tssubmit(jQuery(this));
		return false;
	});

	//Emails form validation
	jQuery('#ts-email-form').validate({
		rules: {
			'timeslot-email-tab[email-sender-email]': {
				email: true
			},
		},
		messages: {
			'timeslot-email-tab[email-sender-email]': __('Please enter a valid email address.', 'timeslot')
		},
		submitHandler: function(form){tssubmit(form);}
	});
});

// Service categories submit success
function tscatsubmit() {

	const { __ } = wp.i18n;

	var options = {
		success: function(){
			const successMsg = jQuery('#ts-success-msg--service-category');
			successMsg.html(__('Settings Saved', 'timeslot'));
			successMsg.slideDown(300);
			successMsg.delay(2000).slideUp('slow');
		},
		timeout: 5000,
		error: function(err){
			console.error(err);
		}
	};

	jQuery('#ts-form--service-category').one('submit', function(){
		jQuery(this).ajaxSubmit(options);
		return false;
	});
}