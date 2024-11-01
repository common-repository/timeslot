/**
 * Toggles additional information
 * 
 * Hides and shows additional information based
 * on icon clicks. References General Settings > 
 * Payments and Calendars Tabs.
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */

jQuery(function() {

	jQuery('.ts-info-icon').on({

		'click': function() {
			jQuery(this).parents('.ts-info-wrapper').next('.ts-info-toggle').toggle();
		},

		'keyup': function(e){
			//Shift key
			if (e.keyCode == 16) {
				return;
			};
			//Enter key
			if (e.keyCode == 13) {
				jQuery(this).parents('.ts-info-wrapper').next('.ts-info-toggle').toggle();
			};
		},

	});

});