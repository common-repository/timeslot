/**
 * Adds add, delete and edit functionality to Staff datatable
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */

jQuery(function() {

	const { __ } = wp.i18n;
	const showingI18n = __('Showing', 'timeslot');
	const staffForm = jQuery('#ts-modal-form--staff');
	const staffI18n = __('Staff Members', 'timeslot');
	const staffModal = jQuery('#ts-modal-edit--staff');
	const staffModalID = 'ts-modal-edit--staff';

	// Get Table Data
	const staffTable = jQuery('#ts-datatable--staff').DataTable({

		ajax: {
			dataSrc:'',
			type: 'GET',
			url: tsstaff.staffdata,
			headers: {
				'X-WP-Nonce': tsstaff.tsStaffPermissionNonce
			}
		},

		buttons: [
			{
				attr: {'aria-label': __('Save Table as CSV', 'timeslot')},
				className: 'ts-btn',
				exportOptions: {
					columns: [1, 2, 3, 4, 5, 6, 7],
					orthogonal: 'export',
				},
				extend: 'csv',
				title: `${tsao.companyname} Staff`,
			},
			{
				attr: {'aria-label': __('Print Table', 'timeslot')},
				className: 'ts-btn',
				exportOptions: {columns: [1, 2, 3, 4, 5, 6]},
				extend: 'print',
				text: __('Print', 'timeslot'),
				title: `${tsao.companyname} Staff`,
			},
		],

		deferRender: true,

		language: {
			emptyTable: __('No Staff Created', 'timeslot'),
			info: `${showingI18n} _START_-_END_ of _TOTAL_ ${staffI18n}`,
			lengthMenu: `${showingI18n} _MENU_ ${staffI18n}`,
			loadingRecords: __('Loading Staff', 'timeslot'),
			processing: __('Processing Staff', 'timeslot'),
			searchPlaceholder: __('Search Staff', 'timeslot'),
			zeroRecords: __('No Matching Staff Found', 'timeslot'),
		},

		orderClasses: false,

	});

	// Clear modal on close
	function clearModal(){

		jQuery('.ts-modal').find(':input').not(':button, :submit, :reset, :hidden, :checkbox, :radio').val('');
		jQuery('.ts-modal').find('select').val(null).trigger('change');
		jQuery('.ts-modal').find('form').validate().resetForm();
	};

	// Open Edit Modal
	staffTable.on('click', '.ts-edit', function(){

		const rowClicked = staffTable.row(jQuery(this).closest('tr'));
		var i = 0;
		jQuery.each(jQuery('#ts-modal-edit--staff input.ts-input'), function(){
			jQuery(this).val(rowClicked.data()[i]);
			i++;
		});

		var servicesSelect = rowClicked.data()[4];
		servicesOptions = servicesSelect ? servicesSelect.split(',') : servicesSelect;
		
		var daysoffSelect = rowClicked.data()[5];
		daysoffOptions = daysoffSelect ? daysoffSelect.split(',') : daysoffSelect;
		
		jQuery('#ts-services').val(servicesOptions).trigger('change');
		jQuery('#ts-days-off').val(daysoffOptions).trigger('change');
		jQuery('#ts-visibility').val(rowClicked.data()[6]).trigger('change');
		jQuery('#ts-notes').val(rowClicked.data()[7]);

		staffModal.attr('rowindex', rowClicked.index());
		MicroModal.show(staffModalID, {
			disableFocus: true,
			disableScroll: true,
			onClose: modal => {clearModal()},
		});

		// Select2 Aria Label in Modal
		jQuery('.select2-selection').each(function(){

			if (jQuery(this).hasClass('select2-selection--single')){

				jQuery(this).removeAttr('aria-labelledby');

				jQuery(this).attr('aria-label', function(){
					return (jQuery(this).closest('.ts-form-col').children('label').text()) + ': ' + (jQuery(this).children('.select2-selection__rendered').text());
				})
			}

			if (jQuery(this).hasClass('select2-selection--multiple')){

				jQuery(this).attr('tabindex', 0);

				var multiSelections = jQuery(this).find(jQuery('.select2-selection__choice__display')).map(function(){ 
					return jQuery(this).text(); 
				}).get().join(', ');

				jQuery(this).attr('aria-label', function(){
					return (jQuery(this).closest('.ts-form-col').children('label').text()) + ': ' + multiSelections;
				})

			}

		});

	});

	// Edit Row and Save
	staffModal.on('click', '.ts-submit-edit', function(){

		if (!staffForm.valid()) {
			return;
		}

		let staffData = [
			['action', 'tslot_update_staff'],
			['daysoff', jQuery('#ts-days-off').val()],
			['email', jQuery('#ts-email').val()],
			['info', jQuery('#ts-notes').val()],
			['name', jQuery('#ts-name').val()],
			['nonce', tsstaff.tsstaffnonce],
			['phone', jQuery('#ts-phone').val()],
			['rowid', jQuery('.ts-row-id').val()],
			['services', jQuery('#ts-services').val()],
			['visibility', jQuery('#ts-visibility').val()],
		];

		const staffFormData = new FormData();

		staffData.forEach((data) =>{
			staffFormData.append(data[0] , data[1]);
		});

		fetch(tsao.ajaxurl, {
			body: staffFormData,
			credentials: 'same-origin',
			method: 'POST',
		})

		.then((response) => {

			response.json().then(jsonResponse => {
				var staffContinue = jsonResponse['continue'];
				if (response.ok) {
					return staffContinue;
				}
				else {
					Promise.reject(__('Staff Fetch Error', 'timeslot'));
					throw new Error(__('Staff Fetch Error', 'timeslot'));
				}
			})

			.then(function(staffContinue){
				if (!staffContinue){
					staffModal.find('#ts-email').focus();
					errors = { email: __('This email is already in use.', 'timeslot') };
					staffForm.validate().showErrors(errors);
				}
				else {
					MicroModal.close(staffModalID);
					staffModal.find('.ts-input').val('');
					staffModal.find('select').val(null).trigger('change');
					staffTable.ajax.reload(null, false);
				}
			})

		})

		.catch(function(){
			error => console.error(__('Staff Error: ', 'timeslot'), + error.message);
		});

	});

	// jQuery Validate
	staffForm.validate({
		errorClass: 'ts-appt-error',
		errorElement: 'div',
		errorPlacement: function(error, element) {
			if (element.hasClass('select2-hidden-accessible')) {
				error.insertAfter(element.next('span.select2'));
			}
			else {
				error.insertAfter(element);
			}
		},
		rules: {
			'email':{
				email: true,
				required: true
			},
			'name':{
				required: true
			},
			'phone': {
				maxlength: 20,
				required: true
			},
			'services[]': {
				required: true
			},
			'visibility': {
				required: true
			},
		},
		messages: {
			'email': {
				email: __('Please enter a valid email address.', 'timeslot'),
				required: __('Please enter an email address.', 'timeslot')
			},
			'name': __('Please enter a name.', 'timeslot'),
			'phone': {
				maxlength: __('Please enter a valid phone number.', 'timeslot'),
				required: __('Please enter a phone number.', 'timeslot')
			},
			'services[]': __('Please select a service.', 'timeslot'),
			'visibility': __('Please select an option.', 'timeslot'),
		},
	});

});