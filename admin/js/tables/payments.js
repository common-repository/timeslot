/**
 * Adds add, delete and edit functionality to Payment datatable
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */

 jQuery(function() {

	const { __ } = wp.i18n;

	const editRowI18n = __('Edit Row', 'timeslot');
	const deleteRowI18n = __('Delete Row', 'timeslot');
	const showing = __('Showing', 'timeslot');
	const selectRow = __('Select Row', 'timeslot');
	const paymentForm = jQuery('#ts-modal-form--payment');
	const paymentEditModal = jQuery('#ts-modal-edit--payment');
	const paymentEditModalID = 'ts-modal-edit--payment';
	const refundConfirmMsg = jQuery('#ts-modal-confirm--refund-unavailable .ts-confirm-msg');
	const refundConfirmModalID = 'ts-modal-confirm--refund-unavailable';
	const paymentConfirmModal = jQuery('#ts-modal-confirm--payment');
	const paymentConfirmModalID = 'ts-modal-confirm--payment';
	const paymentConfirmMsg = jQuery('#ts-modal-confirm--payment .ts-confirm-msg');
	const paymentsI18n = __('Payments', 'timeslot');
	const refundI18n = __('Refund', 'timeslot');
	const refundPaymentsI18n = __('Refund Payment', 'timeslot');
	const locale = tspayments.locale.replace('_','-');

	// Get Table Data
	const paymentTable = jQuery('#ts-datatable--payments').DataTable({

		ajax: {
			url: tspayments.paymentdata,
			type: 'GET',
			dataSrc:'',
			headers: {
				'X-WP-Nonce': tspayments.tsPaymentPermissionNonce
			}
		},

		buttons: [
			{
				attr: {'aria-label': __('Save Table as CSV', 'timeslot')},
				className: 'ts-btn',
				exportOptions: {columns: [1, 2, 3, 4, 5, 6]},
				extend: 'csv',
				title: `${tsao.companyname} Payments`,
			},
			{
				attr: {'aria-label': __('Print Table', 'timeslot')},
				className: 'ts-btn',
				exportOptions: {columns: [1, 2, 3, 4, 5, 6]},
				extend: 'print',
				text: __('Print', 'timeslot'),
				title: `${tsao.companyname} Payments`,
			},
		],

		columnDefs: [
			{
				className: 'no-select ts-table-buttons',
				data: null,
				defaultContent:
					`<button class="ts-refund" aria-label="${refundPaymentsI18n}" title="${refundI18n}">
						<i class="dashicons dashicons-undo" aria-hidden="true"></i>
					</button>`,
				orderable: false,
				targets: -3,
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
				targets: -1,
				responsivePriority: 1,
				width: '20px',
			},
			{
				className: 'never',
				targets: 0,
				width: '20px'
			},
			{
				className: 'dt-nowrap always',
				render: function ( data, type, row ) {
					const currencyCode = tspayments.currency;
					const price = new Intl.NumberFormat(locale, { style: 'currency', currency: currencyCode }).format(data);
					return price;
				},
				targets: 1,
			},
			{
				render:function (data) {
					var date = new Date(data);
					return date.toLocaleDateString(locale, {dateStyle: 'medium', timeZone: tspayments.timezone});
				},
				targets: 2,
				type: 'date',
			},
		],

		deferRender: true,

		dom: 'Bfrtip',

		language: {
			emptyTable: __('No Payments Created', 'timeslot'),
			info: `${showing} _START_-_END_ of _TOTAL_ ${paymentsI18n}`,
			lengthMenu: `${showing} _MENU_ ${paymentsI18n}`,
			loadingRecords: __('Loading Payments', 'timeslot'),
			processing: __('Processing Payments', 'timeslot'),
			searchPlaceholder: __('Search Payments', 'timeslot'),
			zeroRecords: __('No Matching Payments Found', 'timeslot'),
		},

		order: [[ 0, 'desc' ]],

	});

	// Refund Payment
	paymentTable.on('click', '.ts-refund', function(){

		const rowClicked = paymentTable.row(jQuery(this).closest('tr'));
		const paymentSource = rowClicked.data()[4];
		const paymentStatus = rowClicked.data()[5];

		if (paymentStatus === __('Refunded', 'timeslot')){

			refundConfirmMsg.text(__('This payment has already been refunded.', 'timeslot'));
			MicroModal.show(refundConfirmModalID, {
				disableFocus: true,
				disableScroll: true,
			});
			return;

		}

		if (paymentSource === __('Local', 'timeslot')){

			paymentConfirmMsg.text(__('Are you sure you want to refund this payment?', 'timeslot'));
			MicroModal.show(paymentConfirmModalID, {
				disableFocus: true,
				disableScroll: true,
			});

			paymentConfirmModal.one('click', '.ts-submit-confirm', function(){

			let localRefundData = [
				['action', 'tslot_update_payment'],
				['nonce', tspayments.tspaymentnonce],
				['rowid', rowClicked.data()[0]],
				['status', __('Refunded', 'timeslot')],
			];

			const localRefundFormData = new FormData();

			localRefundData.forEach((data) =>{
				localRefundFormData.append(data[0] , data[1]);
			});

			fetch(tsao.ajaxurl, {
				body: localRefundFormData,
				credentials: 'same-origin',
				method: 'POST',
			})

			.then((response) => {
				if (response.ok) {
					return;
				}
				else {
					Promise.reject(__('Payment Fetch Error', 'timeslot'));
					throw new Error(__('Payment Fetch Error', 'timeslot'));
				}
			})

			.then(function(){
				MicroModal.close(paymentConfirmModalID);
				paymentTable.ajax.reload(null, false);
			})

			.then(() => {
				refundConfirmMsg.text(__('Your refund was successful.', 'timeslot'));
				MicroModal.show(refundConfirmModalID, {
					disableFocus: true,
					disableScroll: true,
				});
			})

			.catch(function(){
				error => console.error(__('Payment Error: ', 'timeslot'), + error.message);
			});
			})

		}

	});

	// Edit Row
	paymentEditModal.on('click', '.ts-submit-edit', function(){

		if (!paymentForm.valid()) {
			return;
		}

		let paymentData = [
			['action', 'tslot_update_payment'],
			['nonce', tspayments.tspaymentnonce],
			['rowid', jQuery('.ts-row-id').val()],
			['status', jQuery('#ts-status').val()],
		];
	
		const paymentFormData = new FormData();

		paymentData.forEach((data) =>{
			paymentFormData.append(data[0] , data[1]);
		});

		fetch(tsao.ajaxurl, {
			body: paymentFormData,
			credentials: 'same-origin',
			method: 'POST',
		})

		.then((response) => {
			if (response.ok) {
				return;
			}
			else {
				Promise.reject(__('Payment Fetch Error', 'timeslot'));
				throw new Error(__('Payment Fetch Error', 'timeslot'));
			}
		})

		.then(function(){
			MicroModal.close(paymentEditModalID);
			paymentEditModal.find('.ts-input').val('');
			paymentEditModal.find('select').val(null).trigger('change');
			paymentTable.ajax.reload(null, false);
		})

		.catch(function(){
			error => console.error(__('Payment Error: ', 'timeslot'), + error.message);
		});

	});

	// jQuery Validate
	paymentForm.validate({
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
			'status': {
				required: true
			},
		},
		messages: {
			'status': __('Please select a status.', 'timeslot'),
		},
	});

});