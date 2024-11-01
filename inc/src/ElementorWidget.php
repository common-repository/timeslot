<?php
/**
 * Registers Elementor widget
 *
 * Registers booking form Elementor widget
 * and enqueues styles and scripts for editor
 *
 * @link https://timeslotplugins.com
 * @package Time Slot
 * @since 1.0.1
 * 
 */

namespace TimeSlot;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class ElementorWidget {

	public function __construct(){

		if ( $this -> is_elementor_active() ) {
			add_action( 'elementor/init', [ $this, 'init' ] );
		}
	}

	public function is_elementor_active() {

		if ( ! did_action( 'elementor/loaded' ) ) {
			return false;
		}
		return true;
	}

	public function init() {

		add_action('elementor/widgets/register', [ $this, 'tslot_register_form_widget' ]);
		add_action('elementor/editor/before_enqueue_styles', [ $this, 'tslot_elementor_admin_styles' ]);
		add_action('elementor/preview/enqueue_styles', [ $this, 'tslot_preview_styles' ]);
		add_action('elementor/preview/enqueue_scripts', [ $this, 'tslot_preview_scripts' ]);
	}

	public function tslot_register_form_widget( $widgets_manager ) {

		require ( TIMESLOT_PATH . 'admin/inc/elementor/form.php' );
		$widgets_manager->register( new \Elementor_Time_Slot_Form_Widget() );
	}

	public function tslot_preview_scripts() {

		wp_register_script('select2', TIMESLOT_URL . 'inc/select2-4.1.0-rc.0/select2.min.js', array('jquery'), false, true);
		wp_register_script('ts-elementor', TIMESLOT_URL . 'admin/inc/elementor/ts-elementor.js', ['elementor-frontend', 'jquery', 'select2'], null, true);
		wp_enqueue_script('select2');
		wp_enqueue_script('ts-elementor');
	}

	public function tslot_elementor_admin_styles() {

		wp_register_style('ts-elementor', TIMESLOT_URL . 'admin/inc/elementor/ts-elementor.css');
		wp_enqueue_style( 'ts-elementor' );
	}

	public function tslot_preview_styles() {

		$this -> tslot_elementor_admin_styles();
		$styles = new EnqueueFrontend();
		$styles -> tslot_frontend_styles();
		wp_register_style('select2', TIMESLOT_URL . 'inc/select2-4.1.0-rc.0/select2.min.css', null, false, 'screen');
		wp_enqueue_style('select2');
	}
}