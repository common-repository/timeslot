/**
 * Configures repeater fields
 * 
 * Configures repeater fields for 
 * Business Settings > Company Tab and 
 * Business Settings > Services Tab categories.
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */

jQuery(function() {

	// Prepare new attributes for the repeating section
	function resetAttributeNames(repeater) {

		var repeaterInputs = repeater.find('input, button');
		var idx = repeater.index();

		repeaterInputs.each(function() {

			var attrs = ['id', 'name'];
			thisTag =jQuery(this);

			jQuery.each(attrs, function(i, attr) {
				var attr_val = thisTag.attr(attr);
				if (attr_val) {
					thisTag.prop(attr , attr_val.replace(/\d+/, idx + 1));
				}
				
			})

		})

	}

	// Clone the previous repeater section, and remove all of the values
	jQuery('.ts-repeat').on('click', function(e){

		e.preventDefault();

		var lastRepeatingGroup = jQuery('.ts-repeating').last();
		var cloned = lastRepeatingGroup.clone(true);

		cloned.insertAfter(lastRepeatingGroup);
		cloned.find('input').val('');

		if (cloned.hasClass('ts-holidays')){

			cloned.find('.ts-holiday-date').attr('value','');
			cloned.find('.ts-format-holiday-date').attr('value','');
			cloned.find('.ts-holiday-date').removeClass('hasDatepicker');
			cloned.find('input:checkbox').prop('checked', false);
			
			resetAttributeNames(cloned);
			
			jQuery('.ts-holiday-date').each(function() {
				jQuery(this).datepicker({
					altField: jQuery(this).next('.ts-format-holiday-date'),
					altFormat: 'yy-mm-dd',
					beforeShow: function() {
						jQuery('#ui-datepicker-div').addClass('ts-datepicker');
					},
					dateFormat: tsrepeat.dateformat,
					firstDay: tsrepeat.startofweek,
					minDate: 0,
					nextText: '',
					prevText: '',
					showAnim: '',
				})
			});
		}
		
		else {
			cloned.find('input').val('');
			resetAttributeNames(cloned);
		}

	});

	// Remove repeater row on click
	jQuery('.ts-remove').on('click', function(e){

		e.preventDefault();
		jQuery(this).parents('.ts-repeating').remove();

	});

	// Clear alternative field
	jQuery('.ts-holiday-date').change(function(){

		if (!jQuery(this).val()) {
			jQuery(this).next('.ts-format-holiday-date').val('');
		};

	});

})