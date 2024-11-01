<?php
/**
 * Plugin Name: Time Slot
 * Plugin URI: https://timeslotplugins.com/
 * Description: Book appointments online directly from your website with an easy to use plugin and simple booking form.
 * Version: 1.3.7
 * Requires at least: 5.2
 * Requires PHP: 8.0
 * Author: Time Slot Booking
 * Author URI: https://timeslotplugins.com/
 * Text Domain: timeslot
 */

// Exit if accessed directly
if (! defined('ABSPATH')) {
	exit;
}

/**
 * Defines Time Slot constants
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */
define('TIMESLOT_PATH', plugin_dir_path(__FILE__));
define('TIMESLOT_URL', plugin_dir_url(__FILE__));
define('TIMESLOT_FILE', __FILE__);
define('TIMESLOT_VERSION', '1.3.7');
define('TIMESLOT_UPDATE_PATH', plugin_basename( __FILE__ ));

/**
 * Includes plugin files
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */
require (TIMESLOT_PATH . 'inc/vendor/autoload.php');
require (TIMESLOT_PATH . 'admin/init/timeslot-db.php');
require (TIMESLOT_PATH . 'admin/init/scripts.php');
require (TIMESLOT_PATH . 'admin/init/general.php');
require (TIMESLOT_PATH . 'admin/init/extensions.php');
require (TIMESLOT_PATH . 'admin/init/menu-pages.php');
require (TIMESLOT_PATH . 'public/form/shortcode.php');

/**
 * Register scripts and styles
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */
add_action('init', 'tslot_register_scripts');

function tslot_register_scripts() {

	// jQuery Validate
	wp_register_script('jquery-validate-min', TIMESLOT_URL . 'inc/jquery-validate/1.20.0/jquery.validate.min.js', array('jquery'), false, true);
	wp_register_script('jquery-validate-additional', TIMESLOT_URL . 'inc/jquery-validate/1.20.0/additional-methods.min.js', array('jquery'), false, true);

	// Select2
	wp_register_style('select2', TIMESLOT_URL . 'inc/select2-4.1.0-rc.0/select2.min.css', null, false, 'screen');
	wp_register_script('select2', TIMESLOT_URL . 'inc/select2-4.1.0-rc.0/select2.min.js', array('jquery'), false, true);

	// Repeater Fields
	wp_register_script('repeaters', TIMESLOT_URL . 'admin/js/repeaters.min.js', array('jquery', 'wp-i18n'));
	wp_set_script_translations('repeaters', 'timeslot', TIMESLOT_PATH . 'languages');

	// Time Picker
	wp_register_script('timepicker',TIMESLOT_URL . 'inc/timepicker/jquery.timepicker.min.js', array('jquery'), false, true);

	// Appointments General
	wp_register_script('ts-form', TIMESLOT_URL . 'public/js/ts-form.min.js', array('jquery', 'wp-i18n', 'select2'));

	// Copy Shortcode
	wp_register_script('copy-shortcode', TIMESLOT_URL . 'admin/js/copy-shortcode.min.js', null, false, true);

	// Info Toggle
	wp_register_script('info-toggle', TIMESLOT_URL . 'admin/js/info-toggle.min.js', array('jquery'), false, true);

	// Micro Modal
	wp_register_script('micromodal', TIMESLOT_URL . 'inc/micromodal/micromodal.min.js', array('jquery'));

	// Localization
	load_plugin_textdomain( 'timeslot', false, TIMESLOT_PATH . 'languages' );
}

/**
 * Enqueues frontend scripts and styles
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */
$tslot_frontend = new TimeSlot\EnqueueFrontend();