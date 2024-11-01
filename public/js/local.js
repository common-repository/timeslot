/**
 * Submits form for local payments
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */

jQuery(function(){

	const { __ } = wp.i18n;

	jQuery('#ts-btn-submit').on('click', function(e){

		e.preventDefault();

		// Styles form loading
		jQuery('.ts-dot-wrapper').show();
		jQuery('.ts-summary').css('filter', 'blur(2px)');
		jQuery('#ts-btn-prev,#ts-btn-submit').hide();

		// Sets up form data
		var date = jQuery('#ts-format-date').val();
		var time = jQuery('#ts-select-time').val();
		var fullstart = date + 'T' + time;
		let staff = jQuery('#ts-select-staff') ? jQuery('#ts-select-staff').val() : '';

		let apptFormData = [
			['action', 'tslot_install_data'],
			['service', jQuery('.ts-service').val()],
			['staff', staff],
			['date', date],
			['time', time],
			['start', fullstart],
			['name', jQuery('#ts-input-name').val()],
			['email', jQuery('#ts-input-email').val()],
			['phone', jQuery('#ts-input-phone').val()],
			['source', __('Local', 'timeslot')],
			['coupon', jQuery('#ts-input-coupon').length ? jQuery('#ts-input-coupon').val() : ''],
			['capture_id', 'None'],
			['nonce', jQuery('#ts_form_nonce').val()],
		];

		const apptform = new FormData();

		apptFormData.forEach((data) =>{
			apptform.append(data[0] , data[1]);
		});

		// Saves appointment details
		fetch(tsao.ajaxurl, {
			method: 'POST',
			credentials: 'same-origin',
			body: apptform
		})

		.then((response) => {
			if (response.ok) {
				return;
			}
			else {
				Promise.reject(__('Local Payment Fetch Error', 'timeslot'));
				throw new Error(__('Local Payment Fetch Error', 'timeslot'));
			}
		})

		// Styles form success
		.then(function(){
			jQuery('.ts-summary').css('filter', 'blur(0)');
			jQuery('.ts-dot-wrapper').hide();
			jQuery('.ts-success-msg').show();
		})

		// Sends email
		.then(function(){
			apptform.set('action', 'tslot_appt_email');
			fetch(tsao.ajaxurl, {
				method: 'POST',
				credentials: 'same-origin',
				body: apptform
			})
		})

		// Clears form
		.then(function(){
			jQuery('.ts-form')[0].reset();
		})

		// Handles errors
		.catch(function(){
			error => console.error(__('Local Payment Network Error: ', 'timeslot'), + error.message);
			jQuery('.ts-error-msg').show();
			jQuery('.ts-summary').css('filter', 'blur(0)');
			jQuery('.ts-dot-wrapper').hide();
		});
	});
});