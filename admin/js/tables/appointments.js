/**
 * Adds add, delete and edit functionality to Appointment datatable
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */

jQuery(function() {

	const { __ } = wp.i18n;

	const appts = __('Appointments', 'timeslot');
	const apptModal = jQuery('#ts-modal-edit--appointment');
	const apptModalID = 'ts-modal-edit--appointment';
	const apptForm = jQuery('#ts-modal-form--appointment');
	const deleteRowI18n = __('Delete Row', 'timeslot');
	const editRowI18n = __('Edit Row', 'timeslot');
	const selectRow = __('Select Row', 'timeslot');
	const showing = __('Showing', 'timeslot');
	const locale = tsappts.locale.replace('_','-');
	const timezone = tsappts.timezone;

	// Get Table Data
	const apptTable = jQuery('#ts-datatable--appointments').DataTable({

		ajax: {
			url: tsappts.apptdata,
			type: 'GET',
			dataSrc:'',
			headers: {
				'X-WP-Nonce': tsappts.tsApptPermissionNonce
			}
		},

		buttons: [
			{
				attr: {'aria-label': __('Save Table as CSV', 'timeslot')},
				className: 'ts-btn',
				exportOptions: {columns: [1, 2, 3, 4, 5, 6]},
				extend: 'csv',
				title: `${tsao.companyname} ${appts}`,
			},
			{
				attr: {'aria-label': __('Print Table', 'timeslot')},
				className: 'ts-btn',
				exportOptions: {columns: [1, 2, 3, 4, 5]},
				extend: 'print',
				text: __('Print', 'timeslot'),
				title: `${tsao.companyname} ${appts}`,
			},
		],

		columnDefs: [
			{
				render:function (data) {
					var date = new Date(data);
					return date.toLocaleDateString(locale, {dateStyle: 'medium'});
				},
				targets: 1,
				type: 'date',
			},
			{
				render:function (data) {
					var time = new Date(data);
					return time.toLocaleTimeString(locale, {timeStyle: 'short'});
				},
				targets: 2,
			},
			{
				className: 'no-select ts-table-buttons',
				data: null,
				defaultContent: 
					`<button class="ts-edit" aria-label="${editRowI18n}" title="${editRowI18n}">
						<i class="dashicons dashicons-edit" aria-hidden="true"></i>
					</button>
					<button class="ts-delete" aria-label="${deleteRowI18n}" title="${deleteRowI18n}">
						<i class="dashicons dashicons-trash" aria-hidden="true"></i>
					</button>`,
				orderable: false,
				responsivePriority: 2,
				targets: -2,
			},
			{
				className: 'select-checkboxes always',
				data: null,
				defaultContent: `<input type="checkbox" class="select-checkbox" tab-index="0" aria-label="${selectRow}">`,
				orderable: false,
				responsivePriority: 1,
				targets: -1,
				width: '20px',
			},
			{
				className: 'never',
				targets: 0,
				width: '20px',
			},
			{
				className: 'never',
				targets: -3,
				width: '20px',
			}
		],

		deferRender: true,

		language: {
			emptyTable: __('No Appointments Scheduled', 'timeslot'),
			info: `${showing} _START_-_END_ of _TOTAL_ ${appts}`,
			lengthMenu: `${showing} _MENU_ ${appts}`,
			loadingRecords: __('Loading Appointments', 'timeslot'),
			processing: __('Processing Appointments', 'timeslot'),
			searchPlaceholder: __('Search Appointments', 'timeslot'),
			zeroRecords: __('No Matching Appointments Found', 'timeslot'),
		},

		order: [[ 0, 'desc' ]],

	});

	// Clear modal on close
	function clearModal(){

		jQuery('.ts-modal').find(':input').not(':button, :submit, :reset, :hidden, :checkbox, :radio').val('');
		jQuery('.ts-modal').find('select').val(null).trigger('change');
		jQuery('.ts-modal').find('form').validate().resetForm();
	};

	// Open Edit Modal
	apptTable.on('click', '.ts-edit', function(){

		const rowClicked = apptTable.row(jQuery(this).closest('tr'));
		const rowClickedData = rowClicked.data();
		const date = new Date(rowClickedData[1]);
		const dateFormatted = date.toLocaleDateString(locale, {dateStyle: 'medium', timeZone: timezone});
		const time = new Date(rowClickedData[2]);
		const timeFormatted = time.toLocaleTimeString(locale, {timeStyle: 'short'});
		const staffDisabled = jQuery('#ts-staff').prop('disabled');
		const staffVal = staffDisabled ? '' : rowClickedData[4];
		const customerIndex = staffDisabled ? 4 : 5;
		const statusIndex = staffDisabled ? 5 : 6;
		const startAltIndex = staffDisabled ? 6 : 7;

		jQuery('.ts-row-id').val(rowClickedData[0]);
		jQuery('#ts-start-day').val(dateFormatted);
		jQuery('#ts-start-time').val(timeFormatted);
		jQuery('#ts-service').val(rowClickedData[3]).trigger('change');
		jQuery('#ts-staff').val(staffVal).trigger('change');
		jQuery('#ts-customer').val(rowClickedData[customerIndex]).trigger('change');
		jQuery('#ts-appt-status').val(rowClickedData[statusIndex]).trigger('change');
		jQuery('#ts-start-day-alt').val(rowClickedData[startAltIndex]);

		apptModal.attr('rowindex', rowClicked.index());
		MicroModal.show(apptModalID, {
			disableFocus: true,
			disableScroll: true,
			onClose: modal => {clearModal()},
		});

		jQuery('.select2-selection').each(function(){
			jQuery(this).removeAttr('aria-labelledby');
			jQuery(this).attr('aria-label', function(){
				return (jQuery(this).closest('.ts-form-col').children('label').text()) + ': ' + (jQuery(this).children('.select2-selection__rendered').text());
			})
		});
	});

	// Edit Row
	apptModal.on('click', '.ts-submit-edit', function(){

		if (!apptForm.valid()) {
			return;
		}

		let staff = jQuery('#ts-staff').prop('disabled') ? '' : jQuery('#ts-staff').val();

		let apptData = [
			['action', 'tslot_update_appt'],
			['customer', jQuery('#ts-customer').val()],
			['nonce', tsappts.tsappointmentsnonce],
			['rowid', jQuery('.ts-row-id').val()],
			['service', jQuery('#ts-service').val()],
			['staff', staff],
			['start', jQuery('#ts-start-day-alt').val()],
			['starttime', jQuery('#ts-start-time').val()],
			['status', jQuery('#ts-appt-status').val()],
		];

		const apptFormData = new FormData();

		apptData.forEach((data) =>{
			apptFormData.append(data[0] , data[1]);
		});

		fetch(tsao.ajaxurl, {
			method: 'POST',
			credentials: 'same-origin',
			body: apptFormData
		})

		.then((response) => {

			response.json().then(jsonResponse => {
				if (response.ok) {
					const status = {};
					status.original = jsonResponse[0]['original_status'];
					return status;
				}
				else {
					Promise.reject(__('Appointment Fetch Error', 'timeslot'));
					throw new Error(__('Appointment Fetch Error', 'timeslot'));
				}
			})

			.then(function(status){
				status.current = jQuery('#ts-appt-status').val();
				MicroModal.close(apptModalID);
				apptModal.find('.ts-input').val('');
				apptModal.find('select').val(null).trigger('change');
				apptTable.ajax.reload(null, false);
				return status;
			})

			.then(function(status){
				if (status.original !== __('Canceled', 'timeslot') && status.current == __('Canceled', 'timeslot')){
					apptFormData.set('action', 'tslot_canceled_email');
					fetch(tsao.ajaxurl, {
						method: 'POST',
						credentials: 'same-origin',
						body: apptFormData
					})
				}
				return status;
			})
		})

		.catch(function(){
			error => console.error(__('Appointment Table Error: ', 'timeslot'), + error.message);
		});
	});

	// Initiates Select2 in modal form
	apptModal.find('select').select2({
		dropdownCssClass: 'ts-select2',
		dropdownParent: apptModal,
		minimumResultsForSearch: Infinity,
		placeholder: ''
	});

	// TimePicker
	jQuery('#ts-start-time').timepicker({ 
		'timeFormat': tsappts.timeformat
	});

	// DatePicker
	jQuery('#ts-start-day').datepicker({
		altField: jQuery('#ts-start-day-alt'),
		altFormat: 'yy-mm-dd',
		beforeShow: function() {
			jQuery('#ui-datepicker-div').addClass('ts-datepicker');
		},
		dateFormat: tsappts.dateformat,
		firstDay: tsappts.startofweek,
		minDate: 0,
		nextText: '',
		prevText: '',
		showAnim: '',
	}).on('change', function () {
		jQuery(this).valid();
	});

	// TimePicker
	jQuery('#ts-start-time').timepicker({ 
		'timeFormat': tsappts.timeformat
	});

	// jQuery Validate
	apptForm.validate({
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
			'customer': {
				required: true
			},
			'name':{
				required: true
			},
			'service': {
				required: true
			},
			'staff': {
				required: jQuery('#ts-staff').prop('disabled') ? false : true
			},
			'startday': {
				required: true
			},
			'starttime': {
				required: true
			},
			'status': {
				required: true
			},
		},
		messages: {
			'customer': __('Please select a customer.', 'timeslot'),
			'name': __('Please enter a name.', 'timeslot'),
			'service': __('Please select a service.', 'timeslot'),
			'staff': __('Please select a staff member.', 'timeslot'),
			'startday': __('Please select a date.', 'timeslot'),
			'starttime': __('Please enter a time.', 'timeslot'),
			'status': __('Please select a status.', 'timeslot'),
		},
	});
});