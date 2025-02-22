/**
 * Configures front end appointment form
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */

jQuery(function(){

	const { __, sprintf } = wp.i18n;
	const dateInput = jQuery('#ts-input-date');
	const serviceSelect = jQuery('#ts-select-service');
	const staffSelect = jQuery('#ts-select-staff');
	const timeSelect = jQuery('#ts-select-time');
	const couponInput = jQuery('#ts-input-coupon');
	const couponSection = jQuery('.ts-coupon-code');
	const couponCheckbox = jQuery('#ts-checkbox-coupon');
	const locale = tsao.locale.replace('_','-');
	const currencyCode = tsao.currency;
	let businessDays = [];

	// Removes FOUC helper class
	jQuery('.ts-load').removeClass('ts-load');

	// Remove multiple forms
	if(jQuery('.ts-form').length > 1){

		let i = 1;
		jQuery('.ts-form-wrapper').each(function(){
			
			jQuery('.ts-form-wrapper').eq(i).empty();
			
			if(jQuery('body').hasClass('logged-in')){
				jQuery('.ts-form-wrapper').eq(i).text(__('Note:  Only one form per page is currently supported. This message is only visible to logged in users.', 'timeslot'));
			}

			i++;

		});

		console.warn(__('Time Slot: Only one form per page is currently supported.', 'timeslot'));
	}

	// Retrieve closed business days and holidays
	jQuery.ajax({
		url: tsao.bizhourevents,
		type: 'GET',
		dataType: 'json',
		success: function(data){
			businessDays = data;
		},
		error:function(){
			console.error(__('AJAX Error: Searching for business hours ', 'timeslot'));
		}
	})

	// Datepicker
	function initializeDatepicker() {

		const tsDatepickerOptions = {
			altField: '#ts-format-date',
			altFormat: 'yy-mm-dd',
			dateFormat: tsao.dateformat,
			defaultDate: 0,
			firstDay: tsao.startofweek,
			minDate: 0,
			nextText: '',
			prevText: '',
			showAnim: '',
			beforeShow: function() {
				jQuery('#ui-datepicker-div').addClass('ts-datepicker');
			},
			beforeShowDay: function(date){
				let selectableDate = jQuery.datepicker.formatDate('yy-mm-dd', date);
				return( businessDays.indexOf(selectableDate) ? [ businessDays.indexOf(selectableDate) === -1, '' ] : [true, ''] );
			},
		};

		// Checks if all dates of current month are filled
		dateInput.datepicker(tsDatepickerOptions).one('focus', function () {
			const dateUnselectable = jQuery('.ts-datepicker .ui-datepicker-unselectable').length;
			if (dateUnselectable === 35 || dateUnselectable === 42){
				const toNextMonth = new Date();
				toNextMonth.setMonth(toNextMonth.getMonth() + 1, 1);
				dateInput.datepicker('setDate', toNextMonth);
			}
			else {
				dateInput.datepicker( 'setDate', '+0m' );
			}
		});
	}

	// Initialize Datepicker
	initializeDatepicker();

	// Initialize Select2
	jQuery('.ts-select').select2({
		dropdownCssClass: 'ts-select2',
		dropdownParent: jQuery('.ts-input-wrapper'),
		minimumResultsForSearch: Infinity,
		selectionCssClass: 'ts-select2-container'
	});

	// Sets Select2 ARIA and validation
	jQuery('.ts-select').on('select2:select', function () {

		const nextContainer = jQuery(this).next('.select2-container');

		// Set aria-label
		nextContainer
		.find('.select2-selection--single')
		.removeAttr('aria-labelledby');

		nextContainer
		.find('.select2-selection')
		.attr('aria-label', function(){
			const label = jQuery(this).closest('.select2-container').prev('select').prev('label').text();
			const value = jQuery(this).find('.select2-selection__rendered').text();
			return `${label}: ${value}`;
		});

		// Validate
		jQuery(this).valid();
	});

	// Handles staff functionality
	if(staffSelect.length){
		// Populates staff after service choice
		serviceSelect.on('change', function(){

			dateInput.val('');
			timeSelect.empty();

			staffSelect
			.data('placeholder', __('Searching...', 'timeslot'))
			.empty()
			.select2({ disabled: true }
			);

			jQuery.ajax({
				url: tsao.staffavailable,
				type: 'GET',
				dataType: 'json',
				data: {
					'service': serviceSelect.val(),
				},
				success: function(staffNames){

					staffSelect.data('placeholder', __('Select Staff', 'timeslot'))
					.select2({
						data: staffNames,
						disabled: false,
						dropdownCssClass: 'ts-select2',
						dropdownParent: jQuery('.ts-input-wrapper'),
						minimumResultsForSearch: Infinity,
						selectionCssClass: 'ts-select2-container'
					});

					dateInput.datepicker('destroy');
					initializeDatepicker();

					if (couponCheckbox.is(':checked') && couponInput.val()){
						couponInput
						.removeData('previousValue')
						.valid();
					}

				},
				error:function(xhr, status, error){
					staffSelect
						.data('placeholder', __('Select Staff', 'timeslot'))
						.select2({ disabled: false }
					);
					let errorMsg = xhr.hasOwnProperty('responseJSON') ? xhr.responseJSON.response : __('AJAX Error: Searching for Staff Members ', 'timeslot');
					console.error(errorMsg);
				}
			});
		});

		// Disables staff days off after staff choice
		staffSelect.on('select2:select', function(){

			dateInput.val('');
			timeSelect.empty();

			jQuery.ajax({
				url: tsao.bizhourevents,
				type: 'GET',
				dataType: 'json',
				data: {
					'staff': staffSelect.val(),
					'service': serviceSelect.val(),
				},
				success: function(datesClosed){
					jQuery('#ui-datepicker-div').css('display', 'none');
					staffDaysOff(datesClosed);
					initializeDatepicker();
				},
				error: function(){
					console.error(__('AJAX Error: Searching for Staff Days Off ', 'timeslot'));
				}
			});
		});

		// Selects random staff member if Any option is chosen
		timeSelect.on('select2:select', function(){

			let staffVal = staffSelect.val();
			if (staffVal !== __('Any Staff Member', 'timeslot')){
				return;
			}

			const selectedTime = timeSelect.val();
			const serviceVal = serviceSelect.val();
			const isoDate = jQuery('#ts-format-date').val();

			jQuery.ajax({
				url: tsao.getstaff,
				type: 'GET',
				dataType: 'json',
				data: {
					'time': selectedTime,
					'service': serviceVal,
					'isodate' : isoDate,
				},
				success: function (data){
					staffSelect.val(data);
				},
				error:function(){
					console.error(__('AJAX Error: Searching for Available Staff ', 'timeslot'));
				}
			});

		});
	}

	// Clears date and time on service change if no staff
	else {
		serviceSelect.on('change', function(){
			dateInput.val('');
			timeSelect.empty();
			
			jQuery.ajax({
				url: tsao.bizhourevents,
				type: 'GET',
				dataType: 'json',
				data: {
					'service': serviceSelect.val(),
				},
				success: function(datesClosed){
					jQuery('#ui-datepicker-div').css('display', 'none');
					staffDaysOff(datesClosed);
					initializeDatepicker();
				},
				error: function(){
					console.error(__('AJAX Error: Searching for booked appointments ', 'timeslot'));
				}
			});
		})
	}

	// Sets datepicker beforeShowDay option
	function staffDaysOff(datesClosed) {
		dateInput.datepicker( 'option', 'beforeShowDay', function(date){
			let selectableDate = jQuery.datepicker.formatDate('yy-mm-dd', date);
			if (datesClosed.indexOf(selectableDate)) {
				return [ datesClosed.indexOf(selectableDate) === -1, '' ];
			}
			else {
				return [true, ''];
			}
		});
	}

	// Populates time options after date choice
	dateInput.on('change', function(){

		const serviceVal = serviceSelect.val();
		const staffVal = staffSelect.val();
		const selectedDate = dateInput.datepicker('getDate');
		const dayOfWeek = (selectedDate.toLocaleDateString('en-US', {weekday: 'long'})).toLowerCase();
		const isoDate = jQuery('#ts-format-date').val();

		if (dateInput.val()==''){
			timeSelect.empty();
			return;
		}

		timeSelect
		.empty()
		.data('placeholder', __('Searching...', 'timeslot'))
		.select2({ disabled:true }
		);

		jQuery.ajax({
			url: tsao.timesavailable,
			type: 'GET',
			dataType: 'json',
			data: {
				'service': serviceVal,
				'staff': staffVal,
				'day': dayOfWeek,
				'isodate' : isoDate,
			},
			success: function (availability){
				setTimes(availability);
			},
			error:function(){
				console.error(__('AJAX Error: Searching for Appointment Times ', 'timeslot'));
			}
		});
	});

	// Calculates times
	function setTimes(availability){

		const timeArray = [{text: '', id:''}];

		// Empty time field and exit if staff and service are not selected
		if (availability === 'No Info'){
			timeSelect
			.data('placeholder', __('Select Time', 'timeslot'))
			.select2({
				disabled: false,
				dropdownCssClass: 'ts-select2',
				dropdownParent: jQuery('.ts-input-wrapper'),
				selectionCssClass: 'ts-select2-container'
			});

			return;
		}

		availability.available.forEach((time) => {
			timeArray.push({text: time.text, id:time.id});
		});

		// Add time options to select2
		timeSelect
			.data('placeholder', __('Select Time', 'timeslot'))
			.select2({
				data: timeArray,
				disabled: false,
				dropdownCssClass: 'ts-select2',
				dropdownParent: jQuery('.ts-input-wrapper'),
				selectionCssClass: 'ts-select2-container'
		});
	}

	// Shows Summary
	jQuery('#ts-btn-next').on('click', function(e){

		e.preventDefault();

		if (jQuery('.ts-form').valid()) {

			const time = jQuery('.ts-time option:selected').text();
			const date = dateInput.val();
			const service = jQuery('#ts-select-service option:selected').text();
			// translators: name of service with name of staff
			const summaryStaff = sprintf(__(`%1$s with %2$s` ,'timeslot'), service, staffSelect.val());
			// translators: date at time
			const summaryDate = sprintf(__(`%1$s at %2$s` ,'timeslot'), date, time);

			jQuery('#ts-summary-name').text(jQuery('#ts-input-name').val());
			jQuery('#ts-summary-email').text(jQuery('#ts-input-email').val());
			jQuery('#ts-summary-phone').text(jQuery('#ts-input-phone').val());
			jQuery('#ts-summary-service').text(staffSelect.length ? summaryStaff : service);
			jQuery('#ts-summary-date').text(summaryDate);

			if (!couponInput.val()){
				let servicePrice = jQuery('#ts-select-service option:selected').data('price');
				servicePrice = new Intl.NumberFormat(locale, { style: 'currency', currency: currencyCode }).format(servicePrice);
				jQuery('#ts-summary-price').text(`${servicePrice}`);
				jQuery('#ts-summary-discount').text('').hide();
			}

			jQuery('.ts-fieldset-summary').fadeIn('fast');
			jQuery('.ts-fieldset-form').css({'display': 'none'});
		}
	});

	// Shows Form
	jQuery('#ts-btn-prev').on('click', function(){
		jQuery('.ts-fieldset-form').fadeIn('fast');
		jQuery('.ts-fieldset-summary').css({'display': 'none'});
	});

	// Coupon Code Fields
	if (couponSection.length){

		couponCheckbox.is(':checked') ? couponSection.show() : couponSection.hide();

		couponCheckbox.on('click', function() {
			couponSection.toggle();
			couponInput.val('');
			jQuery('#ts-input-coupon-error').hide();
		});

		couponInput.on('blur', function(){
			if (couponInput.val() === ''){
				couponInput.removeClass('ts-appt-error');
				jQuery('#ts-input-coupon-error').hide();
			}
		})

		function checkCoupons(data) {

			const couponData = JSON.parse(data);
			let originalPrice = couponData.originalPrice;
			originalPrice = new Intl.NumberFormat(locale, { style: 'currency', currency: currencyCode }).format(originalPrice);

			if (couponData.valid === 'true' || couponData.valid === true) {
				let discountedPrice = couponData.discountedPrice;
				const discountedPriceIntl = new Intl.NumberFormat(locale, { style: 'currency', currency: currencyCode }).format(discountedPrice);

				discountedPrice = (discountedPrice > 0) ? `${discountedPriceIntl}` : __('Free', 'timeslot');
				jQuery('#ts-summary-discount').text(`${originalPrice}`).css('display','inline');
				jQuery('#ts-summary-price').text(`${discountedPrice}`);
				return '"true"';
			}

			else {
				jQuery('#ts-summary-discount').text('').hide();
				jQuery('#ts-summary-price').text(`${originalPrice}`);
				return false;
			}
		}
	}

	// jQuery Validate
	jQuery('.ts-form').validate({
		errorElement: 'div',
		errorClass: 'ts-appt-error',
		errorPlacement: function(error, element) {
			element.hasClass('select2-hidden-accessible') ? error.insertAfter(element.next('span.select2')) : error.insertAfter(element);
		},
		onkeyup: false,
		rules: {
			'coupon': {
				remote: {
					async: false,
					url: tsao.ajaxurl,
					type: 'POST',
					data: {
						'coupon': () => couponInput.val(),
						'service': () => serviceSelect.val(),
						action: 'tslot_check_coupon_codes'
					},
					dataFilter: (data) => checkCoupons(data),
				}
			},
			'ts-email':{
				email: true,
				required: true
			},
			'ts-phone': {
				maxlength: 20,
				required: true
			},
			'ts-service': 'required',
			'ts-date': 'required',
			'ts-time': 'required',
			'ts-staff': 'required',
			'ts-name': 'required',
		},
		messages: {
			'coupon': __('Please enter a valid coupon code.', 'timeslot'),
			'ts-email': {
				email: __('Please enter a valid email address.', 'timeslot'),
				required: __('Please enter your email address.', 'timeslot'),
			},
			'ts-phone': {
				maxlength: __('Please enter a valid phone number.', 'timeslot'),
				required: __('Please enter your phone number.', 'timeslot'),
			},
			'ts-name': __('Please enter your name.', 'timeslot'),
			'ts-service': __('Please select a service.', 'timeslot'),
			'ts-date': __('Please select a date.', 'timeslot'),
			'ts-time': __('Please select a time.', 'timeslot'),
			'ts-staff': __('Please select a staff member.', 'timeslot'),
		},
	});
});