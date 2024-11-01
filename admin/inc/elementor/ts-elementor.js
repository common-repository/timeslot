/**
 * Scripts for Elementor widget preview
 *
 * Disables input fields and initializes Select2
 * for booking form in Elementor preview mode
 *
 * @link https://timeslotplugins.com
 * @package Time Slot
 * @since 1.0.1
 * 
 */

class TimeslotElementorHandler extends elementorModules.frontend.handlers.Base {

	getDefaultSettings() {
		return {
			selectors: {
				tsselect2: '.ts-select',
				tsinput: 'input',
			},
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );
		return {
			$tsselect2: this.$element.find( selectors.tsselect2 ),
			$tsinput: this.$element.find( selectors.tsinput ),
		};

	}

	bindEvents() {
		this.elements.$tsinput.attr('disabled', true);
		this.elements.$tsselect2.select2({
			disabled: true,
			selectionCssClass: 'ts-select2-container'
		});
	}

}

jQuery( window ).on( 'elementor/frontend/init', () => {

		const addHandler = ( $element ) => {
			elementorFrontend.elementsHandler.addHandler( TimeslotElementorHandler, {
				$element,
			} );
		};
		elementorFrontend.hooks.addAction( 'frontend/element_ready/timeslot-form.default', addHandler );

});