/**
 * Sets defaults for datatables
 * 
 * Sets default column definitions for datatables,
 * including edit and delete buttons, select columns,
 * and table export buttons.
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
	const selectedI18n = __('Selected', 'timeslot');
	const rowI18n = __('Row', 'timeslot');
	const rowsI18n = __('Rows', 'timeslot');
	const previousI18n = __('Previous', 'timeslot');
	const nextI18n = __('Next', 'timeslot');
	const selectRow = __('Select Row', 'timeslot');


	// DataTable Defaults
	jQuery.extend(true, jQuery.fn.dataTable.defaults, {

		buttons: [
			{
				attr: {'aria-label': __('Save Table as CSV', 'timeslot'),},
				className: 'ts-btn',
				exportOptions: {columns: [1, 2, 3, 4, 5]},
				extend: 'csv',
			},
			{
				attr: {'aria-label': __('Print Table', 'timeslot')},
				className: 'ts-btn',
				exportOptions: {columns: [1, 2, 3, 4, 5]},
				extend: 'print',
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
		],

		deferRender: true,

		dom: 'Bfrtip',

		drawCallback: function() {
			var pagination = jQuery(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
			pagination.toggle(this.api().page.info().pages > 1);
		},

		language: {
			aria: {
				sortAscending: __(': Click or return to sort ascending', 'timeslot'),
				sortDescending: __(': Click or return to sort descending', 'timeslot')
			},
			infoEmpty: '',
			infoFiltered: '',
			paginate: {
				next: `<span>${nextI18n}</span> <i class='dashicons dashicons-arrow-right-alt2'></i>`,
				previous: `<i class='dashicons dashicons-arrow-left-alt2'></i> <span>${previousI18n}</span>`
			},
			search: '<i class="dashicons dashicons-search"></i>',
			select: {
				rows: {
					_: `${selectedI18n} %d ${rowsI18n}`,
					1: `${selectedI18n} 1 ${rowI18n}`,
					0: '',
				}
			},
		},

		orderClasses: false,

		responsive: {
			details: {
				renderer: function ( api, rowIdx, columns ) {
					var data = jQuery.map( columns, function ( col, i ) {

						if (col.hidden && col.data){
							return `<li data-dt-row='${col.rowIndex}' data-dt-column='${col.columnIndex}'>
										<span class='dtr-title'>${col.title}</span>
										<span class='dtr-data'>${col.data}</span>
									</li>`;
						}
						if (col.hidden && !col.data){
							return `<li data-dt-row='${col.rowIndex}' data-dt-column='${col.columnIndex}'>
										<span class='dtr-title'>${col.title}</span>
										<span class='dtr-data'>None</span>
									</li>`;
						}

					}).join('');

					return data ? jQuery('<ul class="dtr-details"/>').append( data ) : false;
				},
			}
		},

		select: {
			selector: '.select-checkbox',
			style: 'multi',
		},

	});

});