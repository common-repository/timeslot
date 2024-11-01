<?php
/**
 * Add services to translatable strings on plugin load
 * for WPML String Translation and Polylang
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.1.7
 * 
 */

namespace TimeSlot;

class MultiLangPlugins {

	public function __construct(){

		register_activation_hook('polylang/polylang.php', array( $this, 'tslot_multilang_plugin_activated' ));
		register_activation_hook('wpml-string-translation/plugin.php', array( $this, 'tslot_multilang_plugin_activated' ));
		register_activation_hook(TIMESLOT_FILE, array( $this, 'tslot_multilang_plugin_activated' ));
		add_action('tslot_create_multilang_services', array( $this, 'tslot_services_to_i18n' ));
	}

	public function tslot_multilang_plugin_activated(){

		wp_schedule_single_event( MINUTE_IN_SECONDS , 'tslot_create_multilang_services' );
	}

	public function tslot_services_to_i18n() {

		if ( !has_action('wpml_register_single_string') ){
			return;
		}

		global $wpdb;
		$ts_services_table = $wpdb->prefix . 'ts_services';

		$services = 
		$wpdb->get_col(
			"SELECT service_title 
			FROM {$ts_services_table}"
		);

		foreach($services as $service){
			$wpml_name = 'Time Slot Service '. $service;
			do_action( 'wpml_register_single_string', 'timeslot', $wpml_name, $service );
		}
	}
}