<?php
/**
 * Registers rest routes and gets datatables data
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.9
 * 
 */

namespace TimeSlot;

abstract class TableData {

	protected $wpdb;
	public $name;
	public $table_name;
	private $rest_route;

	public function __construct($name){

		global $wpdb;
		$this -> name = $name;
		$this -> table_name = $wpdb -> prefix . "ts_" . $this -> name;
		$this -> rest_route = "/ts-" . $this -> name . "/";
		$this -> data_array = array();

		// Registers rest route for table data
		add_action( 'rest_api_init', function () {
			register_rest_route( 'timeslot/v1', $this -> rest_route, array(
					'methods' => \WP_REST_Server::READABLE,
					'callback' => array($this, 'get_data'),
					'permission_callback' => array($this, 'tslot_admin_permission'),
			));
		});

		// Queries table data from db
		$this -> data_from_db = 
			$wpdb -> get_results(
			"SELECT *
			FROM {$this -> table_name}",
			OBJECT
		);

	}

	abstract public function get_data();

	public function tslot_admin_permission() {
		return current_user_can( 'edit_others_posts' );
	}
}