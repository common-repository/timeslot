<?php
/**
 * Delete controls for datatables
 *
 * Deletes single and multiple rows from database.
 * References admin/js/tables/all-tables.js.
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

// Sets table id var
function tslot_set_table_info($tab){

	global $wpdb, $table_id, $ts_table, $ts_appt_table;
	$ts_table = $wpdb->prefix . 'ts_' . $tab;
	$ts_appt_table = $wpdb->prefix . 'ts_appointments';

	switch ($tab){

		case 'staff':
			$table_id = $tab . '_id';
			break;

		case 'appointments':
			$table_id = 'appt_id';
			break;

		default:
			$table_id = substr($tab, 0, -1) . '_id';
			break;

	}

}

// Deletes single row
add_action( 'wp_ajax_tslot_delete_row', 'tslot_delete_row' );

function tslot_delete_row() {

	if (!current_user_can('manage_options')) {
		wp_die();
	}

	if ( !check_ajax_referer( 'ts-datatables-nonce', 'nonce' )) {
		wp_die();
	}

	global $wpdb, $table_id, $ts_table, $ts_appt_table;
	$row_id = intval($_POST['rowid']);
	$tab = sanitize_text_field($_POST['tab']);
	tslot_set_table_info($tab);

	if ($tab == 'customers'){
		$wpdb->delete(
			$ts_appt_table,
			array('customer_id' => $row_id)
		);
	}

	$wpdb->delete(
		$ts_table,
		array($table_id => $row_id)
	);

	wp_die();

}

// Deletes multiple rows
add_action( 'wp_ajax_tslot_delete_multi_rows', 'tslot_delete_multi_rows' );

function tslot_delete_multi_rows() {

	if (!current_user_can('manage_options')) {
		wp_die();
	}

	if ( !check_ajax_referer( 'ts-datatables-nonce', 'nonce' )) {
		wp_die();
	}

	global $wpdb, $table_id, $ts_table, $ts_appt_table;

	$row_ids = isset( $_POST['rowid'] ) ? (array) $_POST['rowid'] : array();
	$row_ids = array_map( 'absint', $row_ids );
	$placeholders = implode(', ', array_fill(0, count($row_ids), '%s'));

	$tab = sanitize_text_field($_POST['tab']);
	tslot_set_table_info($tab);

	if ($tab == 'customers'){
		$wpdb->query(
		$wpdb->prepare(
			"DELETE 
			FROM {$ts_appt_table} 
			WHERE customer_id IN({$placeholders})",
			$row_ids
		));
	}

	$wpdb->query(
	$wpdb->prepare(
		"DELETE 
		FROM {$ts_table}
		WHERE {$table_id} IN({$placeholders})",
		$row_ids
	));

	wp_die();

}