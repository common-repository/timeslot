/**
 * Configures Spectrum color inputs
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * @link https://bgrins.github.io/spectrum/
 * 
 */

jQuery(function() {

	// Internationalization
	const { __ } = wp.i18n;

	// Initialize color picker
	jQuery('.ts-color-input').spectrum({
		allowEmpty: true,
		cancelText: __('Cancel', 'timeslot'),
		chooseText: __('Save', 'timeslot'),
		containerClassName: 'ts-spectrum',
		preferredFormat: 'hex3',
		replacerClassName: 'ts-sp-replacer',
		showAlpha: false,
		showInput: true,
		showPalette: false,
	});

	// Open color picker on keyup, close on blur, add aria label on focus
	jQuery('.ts-color-input').on({

		'keydown': function(e){
			//Shift key
			if (e.keyCode == 16) {
				return;
			};
			//Enter key
			if (e.keyCode == 13) {
				var colorInput = jQuery(this);
				colorInput.spectrum('toggle');
				e.preventDefault();
				return false;
			};
		},

		'keydown blur': function(e){
			//Tab key or Shift + Tab
			if ((e.keyCode === 9) || (e.shiftKey && e.keyCode === 9)) {
				var colorInput = jQuery(this);
				colorInput.spectrum('hide');
			}
		},

	})

});