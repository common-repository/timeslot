<?php
/**
 * Creates media uploader
 *
 * Creates styled media uploader to save images in WordPress options.
 * Based on code from Arthur Gareginyan (arthurgareginyan.com). References
 * admin/js/media-uploader.js.
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */

// Exit if accessed directly
if (!defined('ABSPATH')){
	exit;
}

function tslot_media_upload( $name, $width, $height ) {

	$ts_company_options = get_option( 'timeslot-company-tab' );
	$ts_company_logo = $ts_company_options['company-logo'] ?? '';
	$default_image = TIMESLOT_URL . 'admin/images/company-default-logo.jpg';
	$text = esc_html__( 'Upload', 'timeslot' );

	if (!empty($ts_company_options[$name])) {

		$image_attributes = wp_get_attachment_image_src($ts_company_options[$name], array($width, $height));
		$src = $image_attributes[0];
		$value = $ts_company_options[$name];

	}

	else {
		$src = $default_image;
		$value = '';
	}
	
	?>
	<div class='ts-upload'>

		<img class='ts-company-logo-thumb' data-src='<?php echo esc_url($default_image);?>' src='<?php echo esc_url($src);?>' width='<?php esc_attr_e($width . "px");?>' height='<?php esc_attr_e($height . "px");?>'/>

		<div>
			<input type='hidden' id='timeslot-company-tab[<?php esc_attr_e($name);?>]' class='ts-upload-img-hidden' name='timeslot-company-tab[<?php esc_attr_e($name);?>]' value='<?php esc_attr_e($value);?>'/>

			<span class='ts-media-btn-wrapper'>
				<button type='submit' class='ts-upload-img-btn ts-btn <?php if (empty($ts_company_logo)){ echo "no-img-saved";}?>' title='<?php esc_attr_e('Upload Image', 'timeslot');?>' aria-label='<?php esc_attr_e('Upload Image', 'timeslot');?>'><?php esc_html_e($text);?></button>

				<?php
				if (!empty($ts_company_logo)){
					?>
					<button type='submit' class='ts-remove-img-btn ts-btn' title='<?php esc_attr_e('Remove Image', 'timeslot');?>' aria-label='<?php esc_attr_e('Remove Image', 'timeslot');?>'><span class='dashicons dashicons-no' aria-hidden='true'></span></button>
					<?php
				}
				?>
			</span>
		</div>

	</div>
	
	<!-- Delete confirm modal -->
	<div id='ts-media-confirm' class='modal ts-modal ts-modal-confirm' tabindex='-1' role='dialog' aria-label='<?php esc_attr_e('Confirm Image Removal', 'timeslot');?>' aria-modal='true' aria-describedby='ts-confirm-msg'>

		<div class='modal-dialog ts-modal-dialog modal-dialog-centered'>
			<div class='ts-modal-content'>

				<div class='ts-modal-body'>
					<p id='ts-confirm-msg' class='ts-confirm-msg'><?php esc_html_e('Are you sure you want to remove this image?', 'timeslot');?></p>
				</div>

				<div class='ts-modal-footer'>
					<button type='button' class='ts-btn ts-close' data-micromodal-close aria-label='<?php esc_attr_e('No, Close Dialog', 'timeslot');?>'><?php esc_html_e('No', 'timeslot');?></button>
					<button type='submit' class='ts-btn ts-submit-confirm' aria-label='<?php esc_attr_e('Yes, Remove Image', 'timeslot');?>'><?php esc_html_e('Yes', 'timeslot');?></button>
				</div>

			</div>
		</div>

	</div>
	<?php
}