<?php
/**
 * Booking form Elementor widget
 *
 * Elementor widget that inserts the Time Slot
 * booking form into the page
 *
 * @link https://timeslotplugins.com
 * @package Time Slot
 * @since 1.0.1
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Elementor_Time_Slot_Form_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'timeslot-form';
	}

	public function get_title() {
		return esc_html__( 'Booking Form', 'timeslot' );
	}

	public function get_icon() {
		return 'ts-icon';
	}

	public function get_custom_help_url() {
		return 'https://timeslotplugins.com/docs/';
	}

	public function get_keywords() {
		return [ __('form', 'timeslot'), __('booking', 'timeslot'), 'timeslot', 'time slot' ];
	}

	public function get_categories() {
		return [ 'wordpress' ];
	}

	public function is_reload_preview_required() {
		return false;
	}

	protected function register_controls() {

		$this -> start_controls_section(
			'section_timeslot',
			[
				'label' => esc_html__( 'Time Slot', 'timeslot' ),
			]
		);

		$this -> add_control(
			'tslot-id',
			[
				'label' => esc_html__( 'ID', 'timeslot' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__('my-id', 'timeslot'),
				'default' => '',
			]
		);

		$this -> add_control(
			'tslot-class',
			[
				'label' => esc_html__( 'Class', 'timeslot' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__('my-class', 'timeslot'),
				'default' => '',
			]
		);

		$this -> end_controls_section();
	}

	protected function render() {

		$class = $this -> get_settings_for_display( 'tslot-class' );
		$id = $this -> get_settings_for_display( 'tslot-id' );
		$shortcode = do_shortcode(shortcode_unautop('[timeslot-form]'));

		?>
		<div <?php echo $id ? 'id="'. esc_attr($id) .'"' : '';?> class="ts-elementor <?php esc_attr_e($class) ?: '';?>">
			<?php echo $shortcode; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<?php
	}

	public function render_plain_content() {
		// In plain mode, render without shortcode
		$this -> print_unescaped_setting( 'timeslot-form' );
	}

	protected function content_template() {}

}
