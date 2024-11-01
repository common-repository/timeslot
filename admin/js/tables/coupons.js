/**
 * Adds add, delete and edit functionality to Coupons datatable
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */

jQuery(function() {

	const {__} = wp.i18n;
	const couponForm = jQuery('#ts-modal-form--coupon');
	const couponsI18n = __('Coupons', 'timeslot');
	const couponModal = jQuery('#ts-modal-edit--coupon');
	const couponModalID = 'ts-modal-edit--coupon';
	const deleteRowI18n = __('Delete Row', 'timeslot');
	const editRowI18n = __('Edit Row', 'timeslot');
	const showing = __('Showing', 'timeslot');
	const selectRow = __('Select Row', 'timeslot');
	const percentageI18n = __('Percentage', 'timeslot');

	// Get Table Data
	const couponsTable = jQuery('.ts-datatable').DataTable({

		ajax: {
			dataSrc:'',
			type: 'GET',
			url: tscoupons.coupondata,
			headers: {
				'X-WP-Nonce': tscoupons.tsCouponPermissionNonce
			}
		},

		buttons: [
			{
				attr: {'aria-label': __('Save Table as CSV', 'timeslot')},
				className: 'ts-btn',
				exportOptions: {columns: [1, 2, 3, 4]},
				extend: 'csv',
				title: `${tsao.companyname} ${couponsI18n}`,
			},
			{
				attr: {'aria-label': __('Print Table', 'timeslot')},
				className: 'ts-btn',
				exportOptions: {columns: [1, 2, 3, 4]},
				extend: 'print',
				text: __('Print', 'timeslot'),
				title: `${tsao.companyname} ${couponsI18n}`,
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
				className: 'dt-nowrap always',
				responsivePriority: 3,
				targets: 1,
			},
			{
				render: function ( data, type, row ) {
					if (row[3] == percentageI18n){
						return data + '%';
					}
					else{
						const locale = tscoupons.locale.replace('_','-');
						const currencyCode = tscoupons.currency;
						const price = new Intl.NumberFormat(locale, { style: 'currency', currency: currencyCode, maximumFractionDigits: 0 }).format(data);
						return price;
					}
				},
				targets: 2,
			},
		],

		deferRender: true,

		dom: 'Bfrtip',

		language: {
			emptyTable: __('No Coupons Created', 'timeslot'),
			info: `${showing} _START_-_END_ of _TOTAL_ ${couponsI18n}`,
			lengthMenu: `${showing} _MENU_ ${couponsI18n}`,
			loadingRecords: __('Loading Coupons', 'timeslot'),
			processing: __('Processing Coupons', 'timeslot'),
			searchPlaceholder: __('Search Coupons', 'timeslot'),
			zeroRecords: __('No Matching Coupons Found', 'timeslot'),
		},

	});

	// Edit Row
	couponModal.on('click', '.ts-submit-edit', function(){

		if (!couponForm.valid()) {
			return;
		}

		let couponData = [
			['action', 'tslot_update_coupons'],
			['couponcode', jQuery('#ts-coupon-code').val()],
			['couponstatus', jQuery('#ts-coupon-status').val()],
			['discountamount', jQuery('#ts-discount-amount').val()],
			['discounttype', jQuery('#ts-discount-type').val()],
			['nonce', tscoupons.tscouponsnonce],
			['rowid', jQuery('.ts-row-id').val()],
		];

		const couponFormData = new FormData();

		couponData.forEach((data) =>{
			couponFormData.append(data[0] , data[1]);
		});

		fetch(tsao.ajaxurl, {
			body: couponFormData,
			credentials: 'same-origin',
			method: 'POST',
		})

		.then((response) => {
			if (response.ok) {
				return;
			}
			else {
				Promise.reject(__('Coupon Fetch Error', 'timeslot'));
				throw new Error(__('Coupon Fetch Error', 'timeslot'));
			}
		})

		.then(function(){
			MicroModal.close(couponModalID);
			couponModal.find('.ts-input').val('');
			couponModal.find('select').val(null).trigger('change');
			couponsTable.ajax.reload(null, false);
		})

		.catch(function(){
			error => console.error(__('Coupon Error: ', 'timeslot'), + error.message);
		});

	});

	// jQuery Validate
	couponForm.validate({
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
			'coupon-code': {
				required: true
			},
			'coupon-status': {
				required: true
			},
			'discount-amount': {
				required: true
			},
			'discount-type': {
				required: true
			},
		},
		messages: {
			'coupon-code': __('Please enter a coupon code.', 'timeslot'),
			'coupon-status': __('Please select a status.', 'timeslot'),
			'discount-amount': {
				required: __('Please enter an amount.', 'timeslot'),
				step: __('Please enter a whole number.', 'timeslot'),
			},
			'discount-type': __('Please select a discount type.', 'timeslot'),
		},
	});

});