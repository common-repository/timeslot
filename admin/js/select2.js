/**
 * Configures Select2 inputs
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */

jQuery(function() {

	if (!jQuery('select').length){
		return;
	}

	const { __ } = wp.i18n;

	// General Settings > Appearance Tab
	function formatFontFamily(fontFamily) {
		return jQuery('<span style="font-family:' + fontFamily.id +';">' + fontFamily.text + '</span>');
	}

	function formatFontWeight(fontWeight) {
		return jQuery('<span style="font-weight:' + fontWeight.id +';">' + fontWeight.text + '</span>');
	}

	function formatBorderStyle(borderStyle) {
		return jQuery('<div style="border: 2px ' + borderStyle.id +' #676767; padding: 0 5px;">' + borderStyle.text + '</div>');
	}

	function formatTextTransform(textTransform) {
		return jQuery('<div style="text-transform: ' + textTransform.id +';">' + textTransform.text + '</div>');
	}

	jQuery('.ts-font-family').select2({
		dropdownCssClass: 'ts-select2',
		dropdownAutoWidth: true,
		placeholder: __('Font Family', 'timeslot'),
		templateResult: formatFontFamily
	});

	jQuery('.ts-font-weight').select2({
		dropdownCssClass: 'ts-select2',
		dropdownAutoWidth: true,
		placeholder: __('Font Weight', 'timeslot'),
		templateResult: formatFontWeight
	});

	jQuery('.ts-border-style').select2({
		dropdownCssClass: 'ts-select2',
		dropdownAutoWidth: true,
		placeholder: __('Border Style', 'timeslot'),
		templateResult: formatBorderStyle
	});

	jQuery('.ts-text-transform').select2({
		dropdownCssClass: 'ts-select2',
		dropdownAutoWidth: true,
		placeholder: __('Button Letter Case', 'timeslot'),
		selectionCssClass: 'ts-appearance-full-width',
		templateResult: formatTextTransform
	});

	// Appointments > Coupons Tab Modal
	jQuery('#ts-discount-type').select2({
		dropdownCssClass: 'ts-select2',
		dropdownParent: jQuery('#ts-modal-edit--coupon'),
		minimumResultsForSearch: Infinity,
		placeholder: ''
	});

	jQuery('#ts-coupon-status').select2({
		dropdownCssClass: 'ts-select2',
		dropdownParent: jQuery('#ts-modal-edit--coupon'),
		minimumResultsForSearch: Infinity,
		placeholder: ''
	});

	// Appointments > Payments Tab Modal
	jQuery('#ts-status').select2({
		dropdownCssClass: 'ts-select2',
		dropdownParent: jQuery('#ts-modal-edit--payment'),
		minimumResultsForSearch: Infinity,
		placeholder: ''
	});

	// Business Settings > Services Tab Modal
	jQuery('#ts-service-visibility').select2({
		dropdownCssClass: 'ts-select2',
		dropdownParent: jQuery('#ts-modal-edit--service'),
		minimumResultsForSearch: Infinity,
		placeholder: ''
	});

	jQuery('#ts-category').select2({
		dropdownCssClass: 'ts-select2',
		dropdownParent: jQuery('#ts-modal-edit--service'),
		minimumResultsForSearch: Infinity,
		placeholder: ''
	});

	// Business Settings > Staff Tab Modal
	jQuery('#ts-services').select2({
		placeholder: __('Please select a service.', 'timeslot'),
		dropdownParent: jQuery('#ts-modal-edit--staff'),
		multiple: true,
		dropdownCssClass: 'ts-select2',
		minimumResultsForSearch: Infinity,
		selectionCssClass: 'ts-select2-container',
	}).on('select2:opening select2:closing', function( event ) {
		var searchfield = jQuery(this).parent().find('.select2-search__field');
		searchfield.prop('disabled', true);
	});

	jQuery('#ts-days-off').select2({
		dropdownCssClass: 'ts-select2',
		dropdownParent: jQuery('#ts-modal-edit--staff'),
		minimumResultsForSearch: Infinity,
		multiple: true,
		placeholder: '',
		selectionCssClass: 'ts-select2-container'
	});

	jQuery('#ts-visibility').select2({
		dropdownCssClass: 'ts-select2',
		dropdownParent: jQuery('#ts-modal-edit--staff'),
		minimumResultsForSearch: Infinity,
		placeholder: ''
	});

	// Time Slot Settings > General Tab
	jQuery('.ts-general-select').select2({
		dropdownCssClass: 'ts-select2',
		minimumResultsForSearch: Infinity,
		placeholder: ''
	});

	// Time Slot Settings > Payments Tab
	jQuery('#currency').select2({
		dropdownCssClass: 'ts-select2',
		minimumResultsForSearch: Infinity,
		placeholder: __('Select Currency', 'timeslot'),
	});

	// Select2 Aria Labels
	jQuery('.ts-settings-row .select2-selection--single').each(function(){

		jQuery(this).removeAttr('aria-labelledby');

		jQuery(this).attr('aria-label', function(){

			return (jQuery(this).closest('.select2-container').prev('select').attr('aria-label')) + ': ' + (jQuery(this).children('.select2-selection__rendered').contents().filter(function() {

				return this.nodeType == 3;

			}).text());

		})

	});

});