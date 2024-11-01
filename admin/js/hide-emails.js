/**
 * Toggles email sections
 * 
 * Hides and shows email sections based
 * on checkbox clicks. References Business
 * Settings > Emails Tab.
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */

jQuery(function() {

	const emails = [
		'customer-email-approved',
		'customer-email-canceled',
		'staff-email-approved',
		'staff-email-canceled',
	];

	jQuery.each(emails, function(key, email){

		const check = jQuery(`#ts-${email}-chk`);
		const textbox = jQuery(`.ts-${email}-tinymce-field, .ts-${email}-subject-field`);
	
		if (check.is(':checked')) {
			textbox.show();
		}
		else {
			textbox.hide();
		}

		check.on('click', function() {
			textbox.toggle();
		})

	});

});