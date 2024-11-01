/**
 * Adds add, delete and edit functionality to Services datatable
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */

jQuery(function() {

	const {__} = wp.i18n;
	const editRowI18n = __('Edit Row', 'timeslot');
	const deleteRowI18n = __('Delete Row', 'timeslot');
	const addCats = __('Add Categories', 'timeslot');
	const showing = __('Showing', 'timeslot');
	const servicesI18n = __('Services', 'timeslot');
	const selectRow = __('Select Row', 'timeslot');
	const serviceEditModal = jQuery('#ts-modal-edit--service');
	const serviceEditModalID = 'ts-modal-edit--service';
	const serviceEditForm = jQuery('#ts-modal-form--service');

	// Get Table Data
	const servicesTable = jQuery('#ts-datatable--services').DataTable({

		ajax: {
			dataSrc:'',
			type: 'GET',
			url: tsservices.servicedata,
			headers: {
				'X-WP-Nonce': tsservices.tsServicePermissionNonce
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
				title: `${tsao.companyname} Services`,
			},
			{
				attr: {'aria-label': __('Print Table', 'timeslot')},
				className: 'ts-btn',
				exportOptions: {columns: [1, 2, 3, 4, 5, 6]},
				extend: 'print',
				text: __('Print', 'timeslot'),
				title: `${tsao.companyname} Services`,
			},
			{
				action: function(){MicroModal.show(
					'ts-modal--service-category', {
						disableFocus: true,
						disableScroll: true,
					});
				},
				attr: {'aria-label': `${addCats}`},
				className: 'ts-btn',
				text: `${addCats}`,
				title:`${addCats}`,
			},
		],

		columnDefs: [
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
				width: '20px'
			},
			{
				render: function ( data, type, row ) {
					return data.substr(0,1).toUpperCase()+data.substr(1);
				},
				targets: [5,6],
			},
			{
				render: function ( data, type, row ) {
					const locale = tsservices.locale.replace('_','-');
					const currencyCode = tsservices.currency;
					const price = new Intl.NumberFormat(locale, { style: 'currency', currency: currencyCode }).format(data);
					return price;
				},
				targets: 2,
			},
			{
				render: function ( data, type, row ) {

					data = parseInt(data);

					if (data == 0){
						return __('None', 'timeslot');
					}

					else if (data < 3600){
						return data / 60 + __(' Minutes', 'timeslot');
					}

					else if (data >= 3600 && data < 86400){

						if (data % 3600 !==0){
							return Math.trunc(data / 3600) + (data < 7200 ? __(' Hour +', 'timeslot') : __(' Hours +', 'timeslot'));
						}
						return data / 3600 + (data == 3600 ? __(' Hour', 'timeslot') : __(' Hours', 'timeslot'));

					}

					else {

						if (data % 86400 !==0){
							return Math.trunc(data / 86400) + (data < 172800 ? __(' Day +', 'timeslot') : __(' Days +', 'timeslot'));
						}
						return data / 86400 + (data == 86400 ? __(' Day', 'timeslot') : __(' Days', 'timeslot'));

					}
				},
				targets: [3,4],
			},
			{
				className: 'dt-nowrap always',
				responsivePriority: 3,
				targets: 1,
			},
			{
				render: function ( data, type, row ) {
					return type === 'display' && data.length > 5 ?
					data.substr( 0, 5 ) +'…' :
					data;
				},
				targets: 7,
			}
		],

		deferRender: true,

		dom: 'Bfrtip',

		language: {
			emptyTable: __('No Services Created', 'timeslot'),
			info: `${showing} _START_-_END_ of _TOTAL_ ${servicesI18n}`,
			lengthMenu: `${showing} _MENU_ ${servicesI18n}`,
			loadingRecords: __('Loading Services', 'timeslot'),
			processing: __('Processing Services', 'timeslot'),
			searchPlaceholder: __('Search Services', 'timeslot'),
			zeroRecords: __('No Matching Services Found', 'timeslot'),
		},

	});

	// Clear modal on close
	function clearModal(){

		jQuery('.ts-modal').find(':input').not(':button, :submit, :reset, :hidden, :checkbox, :radio').val('');
		jQuery('.ts-modal').find('select').val(null).trigger('change');
		jQuery('.ts-modal').find('form').validate().resetForm();
	};

	// Formats Duration Picker Values
	function tsDurationPicker(total, hoursInput, minutesInput) {

		let minutes;
		total = parseInt(total);

		if (total < 86400 && total >= 3600){
			hoursInput.val(Math.trunc(total/3600));
			minutes = (total % 3600) !== 0 ? (total % 3600)/60 : 0;
			minutesInput.val(minutes);
		}
		if (total < 3600){
			minutes = total/60;
			minutesInput.val(minutes);
			hoursInput.val(0);
		}

	}

	// Open Edit Modal
	servicesTable.on('click', '.ts-edit', function(){

		const rowClicked = servicesTable.row(jQuery(this).closest('tr'));

		const durationTotal = rowClicked.data()[3];
		const durationHoursInput = jQuery('#ts-duration-group .bdp-hours');
		const durationMinutesInput = jQuery('#ts-duration-group .bdp-minutes');
		tsDurationPicker(durationTotal, durationHoursInput, durationMinutesInput);

		const beforeTotal = rowClicked.data()[4];
		const beforeHoursInput = jQuery('#ts-before-group .bdp-hours');
		const beforeMinutesInput = jQuery('#ts-before-group .bdp-minutes');
		tsDurationPicker(beforeTotal, beforeHoursInput, beforeMinutesInput);

		jQuery('.ts-row-id').val(rowClicked.data()[0]);
		jQuery('#ts-service').val(rowClicked.data()[1]);
		jQuery('#ts-price').val(rowClicked.data()[2]);
		jQuery('.bdp-days').val(0);
		jQuery('#ts-duration').val(durationTotal);
		jQuery('#ts-before').val(beforeTotal);
		jQuery('#ts-category').val(rowClicked.data()[5]).trigger('change');
		jQuery('#ts-service-visibility').val(rowClicked.data()[6]).trigger('change');
		jQuery('#ts-notes').val(rowClicked.data()[7]);

		serviceEditModal.attr('rowindex', rowClicked.index());
		MicroModal.show(serviceEditModalID, {
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
	serviceEditModal.on('click', '.ts-submit-edit', function(){

			if (!serviceEditForm.valid()) {
				return;
			}

			let serviceData = [
				['action', 'tslot_update_service'],
				['beforeservice', jQuery('#ts-before').val()],
				['category', jQuery('#ts-category').val()],
				['duration', jQuery('#ts-duration').val()],
				['info', jQuery('#ts-notes').val()],
				['nonce', tsservices.tsservicesnonce],
				['price', jQuery('#ts-price').val()],
				['rowid', jQuery('.ts-row-id').val()],
				['service', jQuery('#ts-service').val()],
				['visibility', jQuery('#ts-service-visibility').val()],
			];
	
			const serviceFormData = new FormData();
	
			serviceData.forEach((data) =>{
				serviceFormData.append(data[0] , data[1]);
			});
	
			fetch(tsao.ajaxurl, {
				body: serviceFormData,
				credentials: 'same-origin',
				method: 'POST',
			})
	
			.then((response) => {

				response.json().then(jsonResponse => {
					var serviceExists = jsonResponse['service_exists'];
					if (response.ok) {
						return serviceExists;
					}
					else {
						Promise.reject(__('Service Fetch Error', 'timeslot'));
						throw new Error(__('Service Fetch Error', 'timeslot'));
					}
				})

				.then(function(serviceExists){
					if (serviceExists === true){
						serviceEditForm.find('#ts-service').focus();
						errors = { service: __('This service name already exists.', 'timeslot') };
						serviceEditForm.validate().showErrors(errors);
					}
					else {
						MicroModal.close(serviceEditModalID);
						serviceEditModal.find('.ts-input').val('');
						serviceEditModal.find('select').val(null).trigger('change');
						servicesTable.ajax.reload(null, false);
					}
				})

			})

			.catch(function(){
				error => console.error(__('Service Error: ', 'timeslot'), + error.message);
			});

	});

	// DurationPicker
	jQuery('#ts-duration, #ts-before').durationPicker({
		translations: {
			hour: __('Hour', 'timeslot'),
			minute: __('Minute', 'timeslot'),
			hours: __('Hours', 'timeslot'),
			minutes: __('Minutes', 'timeslot'),
		},
		showDays: false
	});

	// Adds greater than jQuery Validate method for duration
	jQuery.validator.addMethod( "durationGreaterThan", function( value, element, param ) {

		jQuery(element).next('.bdp-input-container').find('.ts-duration-input').on('change', function(){
			jQuery(element).valid();
		})

		return parseInt(value) > parseInt(param);

	}, __('Please enter a duration.', 'timeslot'));

	// jQuery Validate
	serviceEditForm.validate({
		errorClass: 'ts-appt-error',
		errorElement: 'div',
		errorPlacement: function(error, element) {
			if (element.hasClass('select2-hidden-accessible')) {
				error.insertAfter(element.next('span.select2'));
			}
			else if (element.hasClass('bdp-input')) {
				error.insertAfter(element.closest('.bdp-input-container'));
			}
			else if (element.hasClass('ts-duration')) {
				error.insertAfter(element.next('.bdp-input-container'));
			}
			else {
				error.insertAfter(element);
			}
		},
		groups: {
			'durationInputs': 'ts-duration-hours ts-duration-minutes',
		},
		ignore: [],
		rules: {
			'price': {
				number: true,
				required: true
			},
			'service':{
				required: true
			},
			'ts-before-hours': {
				number: true,
			},
			'ts-before-minutes': {
				number: true,
			},
			'ts-duration-hours': {
				require_from_group: [1,'.ts-duration-input'],
				number: true,
			},
			'ts-duration-minutes': {
				require_from_group: [1,'.ts-duration-input'],
				number: true,
			},
			'visibility': {
				required: true
			},
			'duration': {
				durationGreaterThan: 0
			}
		},
		messages: {
			'price': {
				number: __('Please enter a valid price.', 'timeslot'),
				required: __('Please enter a price.', 'timeslot'),
				step: __('Please enter a valid price.', 'timeslot'),
			},
			'service': {
				required: __('Please enter a service.', 'timeslot')
			},
			'ts-before-hours': {
				number: __('Please enter a valid number.', 'timeslot'),
			},
			'ts-before-minutes': {
				number: __('Please enter a valid number.', 'timeslot'),
				step:  __('Please use 15 minute increments.', 'timeslot'),
			},
			'ts-duration-hours': {
				require_from_group: __('Please enter a duration.', 'timeslot'),
				number: __('Please enter a valid number.', 'timeslot'),
			},
			'ts-duration-minutes': {
				require_from_group: __('Please enter a duration.', 'timeslot'),
				number: __('Please enter a valid number.', 'timeslot'),
				step:  __('Please use 15 minute increments.', 'timeslot'),
			},
			'visibility': __('Please select an option.', 'timeslot'),
		},
	});

});