<?php
/**
 * Registers form block
 *
 * Registers booking form block, shortcode,
 * scripts and styles.
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */

namespace TimeSlot;

class GutenbergBlocks {

	public function __construct() {

		// Block editor is not available
		if (!function_exists('register_block_type')) {
			return;
		}

		// Register block editor script
		wp_register_script(
			'timeslot-block',
			TIMESLOT_URL . 'admin/inc/blocks/timeslot-block.min.js',
			array('wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-i18n', 'jquery', 'select2')
		);

		wp_localize_script('timeslot-block', 'tsvars',
			array(
				'tspluginurl' => plugins_url('/timeslot/'),
				'tsadminurl' => admin_url('admin.php')
			)
		);

		wp_set_script_translations(
			'timeslot-block',
			'timeslot',
			TIMESLOT_PATH . 'languages'
		);

		// Register styles, including inline
		require (TIMESLOT_PATH . 'public/css/ts-form-dynamic.php');
		wp_register_style('ts-block-preview', TIMESLOT_URL . 'admin/css/ts-block-preview.min.css', array('select2'));
		wp_add_inline_style('ts-block-preview', $ts_form_styles_min);

		// Register block
		register_block_type('timeslot/booking-form', array(
			'render_callback' => 'tslot_form_shortcode',
			'editor_script' => 'timeslot-block',
			'editor_style' => 'ts-block-preview',
		));

		// Define shortcode using same render function as block
		add_shortcode('timeslot-form', 'tslot_form_shortcode');
	}
}