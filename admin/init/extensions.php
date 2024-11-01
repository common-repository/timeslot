<?php
/**
 * Creates actions for plugin dependencies
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.1.8
 * 
 */

/**
 * Registers Gutenberg block
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */
add_action( 'init', 'tslot_register_gutenberg_block' );

function tslot_register_gutenberg_block(){
	require TIMESLOT_PATH . 'admin/inc/blocks/timeslot-block.php';
	$ts_gutenberg_block = new TimeSlot\GutenbergBlocks();
}

/**
 * Registers Elementor widget
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.1
 * 
 */
add_action( 'plugins_loaded', 'tslot_register_elementor_widget', 20 );

function tslot_register_elementor_widget(){
	$ts_elementor_widget = new TimeSlot\ElementorWidget();
}

/**
 * Add services to translatable strings
 * on WPML or Polylang plugin load
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.1.7
 * 
 */
add_action( 'plugins_loaded', 'tslot_multi_lang_plugins', 20 );

function tslot_multi_lang_plugins(){
	$multi_lang_plugins = new TimeSlot\MultiLangPlugins();
}

/**
 * Registers Beaver Builder module
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.1.9
 * 
 */
add_action( 'init', 'tslot_register_bb_widget' );

function tslot_register_bb_widget() {

	if( class_exists( 'FLBuilder' ) ) {
		require TIMESLOT_PATH . 'admin/inc/beaver-builder/register.php';
	}
}