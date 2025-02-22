/**
 * Adds add, delete and edit functionality to datatables
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */

 jQuery(function() {

	if (!jQuery('.ts-datatable').length){
		return;
	}

	const { __, sprintf } = wp.i18n;

	// Get Table Data
	const thisTable = jQuery('.ts-datatable').DataTable();
	let tab = tsdatatables.tab;
	let single, plural;
	const modalEdit = jQuery('.ts-modal-edit');
	const modalEditID = modalEdit.attr('id');
	const modalConfirm = jQuery('.ts-modal-confirm');
	const modalConfirmID = modalConfirm.attr('id');
	const datatable = jQuery('.ts-datatable');

	const locale = [
		{single:__('appointment', 'timeslot'), plural:__('appointments', 'timeslot')},
		{single:__('customer', 'timeslot'), plural:__('customers', 'timeslot')},
		{single:__('payment', 'timeslot'), plural:__('payments', 'timeslot')},
		{single:__('coupon', 'timeslot'), plural:__('coupons', 'timeslot')},
		{single:__('service', 'timeslot'), plural:__('services', 'timeslot')},
		{single:__('staff member', 'timeslot'), plural:__('staff members', 'timeslot')},
	];

	// Set singular and plural tab names
	switch (tab){

		case 'staff':
			single = __('staff member', 'timeslot');
			plural = __('staff members', 'timeslot');
			break;
		case 'customers':
			// translators: %s is a placeholder html span tags
			jQuery('.ts-modal-confirm .ts-confirm-msg').after(sprintf(__('%1$s This will also delete their appointments.%2$s', 'timeslot'), '<span>','</span>'));
		default:
			single = __(tab.slice(0, -1), 'timeslot');
			plural = __(tab, 'timeslot');
			break;

	}

	// Add Row
	datatable.on('click', '.ts-add', function(){

		jQuery('.ts-row-id').val('');
		modalEdit.find('select').val(null).trigger('change');
		MicroModal.show(modalEditID, {
			disableFocus: true,
			disableScroll: true,
			onClose: modal => {clearModal()},
		});

		select2Aria();

	});

	// Delete Row
	datatable.on('click', '.ts-delete', function() {

		const rowClicked = thisTable.row(jQuery(this).closest('tr'));
		const rowDeleteId = rowClicked.data()[0];
		// translators: %s is a placeholder for the page name
		jQuery('.ts-modal-confirm .ts-confirm-msg').text(sprintf(__('Are you sure you want to delete this %s?', 'timeslot'), single));
		MicroModal.show(modalConfirmID, {
			disableFocus: true,
			disableScroll: true,
		});

		modalConfirm.off('click', '.ts-submit-confirm').on('click', '.ts-submit-confirm', function(){
			MicroModal.close(modalConfirmID);
			jQuery.ajax({
				url: tsao.ajaxurl,
				type: 'POST',
				data: {
					action: 'tslot_delete_row',
					nonce: tsdatatables.tsdtnonce,
					'rowid': rowDeleteId,
					'tab': tab,
				},
				success: () => {
					thisTable.ajax.reload(null, false);
				},
				error: (e) => {
					console.error(__('Delete Row Error ', 'timeslot') + e);
				}
			});
		})

	});

	// Delete multiple rows
	datatable.on('click', '.ts-delete-multi', function() {

		selectRows = thisTable.rows({
			selected: true
		}).data();

		rowDeleteIds = [];

		for (var i=0; i < selectRows.length ;i++){
			rowDeleteId = selectRows[i][0];
			rowDeleteIds.push(rowDeleteId);
		}

		// translators: %s is a placeholder for the page name
		jQuery('.ts-modal-confirm .ts-confirm-msg').text(sprintf(__('Are you sure you want to delete these %s?', 'timeslot'), plural));
		MicroModal.show(modalConfirmID, {
			disableFocus: true,
			disableScroll: true,
		});

		modalConfirm.off('click', '.ts-submit-confirm').on('click', '.ts-submit-confirm', function(){

			MicroModal.close(modalConfirmID);

			jQuery.ajax({
				url: tsao.ajaxurl,
				type: 'POST',
				data: {
					action: 'tslot_delete_multi_rows',
					nonce: tsdatatables.tsdtnonce,
					'rowid[]': rowDeleteIds,
					'tab': tab,
				},
				success: () => {
					thisTable.ajax.reload(null, false);
				},
				error: (e) => {
					console.log(__('Delete Rows Error ', 'timeslot') + e);
				}
			});

			jQuery('.ts-select-all').prop('checked', false);

		})

	});

	// Open Edit Modal
	datatable.not('#ts-datatable--services').not('#ts-datatable--staff').not('#ts-datatable--appointments').on('click', '.ts-edit', function(){

		const rowClicked = thisTable.row(jQuery(this).closest('tr'));
		let i = 0;

		jQuery.each(jQuery('.ts-modal-edit input, .ts-modal-edit select, .ts-modal-edit textarea'), function(){
			jQuery(this).val(rowClicked.data()[i]).trigger('change.select2');
			i++;
		});

		modalEdit.attr('rowindex', rowClicked.index());
		MicroModal.show(modalEditID, {
			disableFocus: true,
			disableScroll: true,
			onClose: modal => {clearModal()},
		});

		select2Aria();

	});

	// Close Modal
	jQuery('.ts-modal-footer').on('click', '.ts-close', function(){clearModal()});

	// Clear modal on close
	function clearModal(){

		jQuery('.ts-modal').not('#ts-modal--service-category').find(':input').not(':button, :submit, :reset, :hidden, :checkbox, :radio').val('');
		jQuery('.ts-modal').find('select').val(null).trigger('change');
		jQuery('.ts-modal').find('form').validate().resetForm();
	};

	// Select All
	datatable.on('click', '.ts-select-all', function() {

		if (jQuery('.ts-select-all:checked').val() === 'on') {
			thisTable.rows({page: 'current'}).select();
			jQuery('.selected .select-checkbox').prop('checked', true);
		}

		else {
			jQuery('.selected .select-checkbox').prop('checked', false);
			thisTable.rows({page: 'current'}).deselect();
		}

	});

	// Uncheck Select All on different page
	jQuery('.ts-datatable_paginate').on('click', function() {

		if (jQuery(".ts-datatable tbody tr.selected").length === jQuery(".ts-datatable tbody tr").length) {
			jQuery('.ts-select-all').prop('checked', true);
		}

		else{
			jQuery('.ts-select-all').prop('checked', false);
		}

	});

	// Enable Delete button on checkbox check
	datatable.on('change', jQuery('.select-checkbox'), function() {

		if (jQuery(this).find(jQuery('.select-checkbox')).is(':checked')) {
			jQuery('tfoot button').prop('disabled', false);
		}

		else{
			jQuery('tfoot button').prop('disabled', true);
		}

	});

	// Validate Select2
	jQuery('.ts-modal-form').find('select').on('select2:select', function () {
		jQuery(this).valid();
	});

	// Select2 Aria Label in Modal
	function select2Aria(){

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
	}

});