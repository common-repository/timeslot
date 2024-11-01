/**
 * Adds add, delete and edit functionality to Customer datatable
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */

jQuery(function() {

	const { __ } = wp.i18n;
	const customerForm = jQuery('#ts-modal-form--customer');
	const customerModal = jQuery('#ts-modal-edit--customer');
	const customerModalID = 'ts-modal-edit--customer';
	const customersI18n = __('Customers', 'timeslot');
	const showing = __('Showing', 'timeslot');

	// Get Table Data
	const customersTable = jQuery('#ts-datatable--customers').DataTable({

		ajax: {
			dataSrc:'',
			type: 'GET',
			url: tscustomers.customerdata,
			headers: {
				'X-WP-Nonce': tscustomers.tsCustomerPermissionNonce
			}
		},

		buttons: [
			{
				className: 'ts-btn',
				exportOptions: {columns: [1, 2, 3]},
				extend: 'csv',
				title: `${tsao.companyname} ${customersI18n}`,
			},
			{
				className: 'ts-btn',
				exportOptions: {columns: [1, 2, 3]},
				extend: 'print',
				text: __('Print', 'timeslot'),
				title: `${tsao.companyname} ${customersI18n}`,
			},
		],

		deferRender: true,

		language: {
			emptyTable: __('No Customers Created', 'timeslot'),
			info: `${showing} _START_-_END_ of _TOTAL_ ${customersI18n}`,
			lengthMenu: `${showing} _MENU_ ${customersI18n}`,
			loadingRecords: __('Loading Customers', 'timeslot'),
			processing: __('Processing Customers', 'timeslot'),
			searchPlaceholder: __('Search Customers', 'timeslot'),
			zeroRecords: __('No Matching Customers Found', 'timeslot'),
		},

	});

	// Edit Row
	customerModal.on('click', '.ts-submit-edit', function(){

		if (!customerForm.valid()) {
			return;
		}

		let customerData = [
			['action', 'tslot_update_customers'],
			['customername', jQuery('#ts-customer-name').val()],
			['phone', jQuery('#ts-customer-phone').val()],
			['email', jQuery('#ts-customer-email').val()],
			['nonce', tscustomers.tscustomersnonce],
			['rowid', jQuery('.ts-row-id').val()],
		];

		const customerFormData = new FormData();

		customerData.forEach((data) =>{
			customerFormData.append(data[0] , data[1]);
		});

		fetch(tsao.ajaxurl, {
			body: customerFormData,
			credentials: 'same-origin',
			method: 'POST',
		})

		.then((response) => {
			if (response.ok) {
				return;
			}
			else {
				Promise.reject(__('Customer Fetch Error', 'timeslot'));
				throw new Error(__('Customer Fetch Error', 'timeslot'));
			}
		})

		.then(function(){
			MicroModal.close(customerModalID);
			customerModal.find('.ts-input').val('');
			customersTable.ajax.reload(null, false);
		})

		.catch(function(){
			error => console.error(__('Customer Error: ', 'timeslot'), + error.message);
		});

	});

	// jQuery Validate
	customerForm.validate({
		errorClass: 'ts-appt-error',
		errorElement: 'div',
		errorPlacement: function(error, element) {
			error.insertAfter(element);
		},
		rules: {
			'customer-name': {
				required: true
			},
			'customer-email': {
				email: true,
				required: true
			},
			'customer-phone': {
				maxlength: 20,
				required: true
			},
		},
		messages: {
			'customer-name': __('Please enter a customer name.', 'timeslot'),
			'customer-email': {
				required: __('Please enter an email address.', 'timeslot'),
				email: __('Please enter a valid email.', 'timeslot'),
			},
			'customer-phone': {
				required: __('Please enter a phone number.', 'timeslot'),
				maxlength: __('Please enter a valid phone number.', 'timeslot'),
			},
		},
	});

});