/**
 * Configures Media Uploader
 * 
 * References Business Settings > Company Tab logo upload.
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */

jQuery(function () {

	const { __ } = wp.i18n;

	//Add image from media library on button or image click
	jQuery('.ts-upload-img-btn, .ts-company-logo-thumb').click(function(e) {

		e.preventDefault();

		var tsLogoUploader = wp.media({
			title: __('Choose Your Company Logo', 'timeslot'),
			button: {text: __('Set Company Logo', 'timeslot')},
			multiple: false
		})

		.on('select', function() {
			var attachment = tsLogoUploader.state().get('selection').first().toJSON();
			jQuery('.ts-company-logo-thumb').attr('src', attachment.url);
			jQuery('.ts-upload-img-btn').parents().find('.ts-upload-img-hidden').val(attachment.id);
		})

		.open();

	});

	//Remove image from setting on button click
	jQuery('.ts-remove-img-btn').on('click', function(e) {

		e.preventDefault();

		if (jQuery(this).parents().find('.ts-upload-img-hidden').val()){

			MicroModal.show('ts-media-confirm', {
				disableFocus: true,
				disableScroll: true,
			});

			jQuery('#ts-media-confirm').on('click', '.ts-submit-confirm', function(){
				MicroModal.close('ts-media-confirm');
				const imgThumb = jQuery(this).parents('.ts-modal').prev('.ts-upload').find('.ts-company-logo-thumb');
				const imgHidden = jQuery(this).parents('.ts-modal').prev('.ts-upload').find('.ts-upload-img-hidden');
				const src = imgThumb.attr('data-src');
				imgThumb.attr('src', src);
				imgHidden.val('');
			})

		}

	});

});