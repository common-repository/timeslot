<?php
/**
 * Configures appearance settings
 *
 * Creates appearance option settings, callbacks,
 * and validations.
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

// Initializes appearance settings
add_action( 'admin_init', 'tslot_appearance_init' );

function tslot_appearance_init() {

	add_settings_section( 'appearance-section','', 'tslot_appearance_callback', 'timeslot-appearance-tab' );

	$settings = array(
		'button-section' => array(
			'title'=> esc_html__('Buttons', 'timeslot'),
			'page'=>'timeslot-appearance-tab',
			'fields'=> array(
				array(
					'id'=> 'btn-bgc',
					'title'=> esc_html__('Background Color', 'timeslot'),
					'callback'=> 'tslot_color_input_callback'
				),
				array(
					'id'=> 'btn-bgc-hover',
					'title'=> esc_html__('Background Hover Color', 'timeslot'),
					'callback'=> 'tslot_color_input_callback'
				),
				array(
					'id'=> 'btn-color',
					'title'=> esc_html__('Text Color', 'timeslot'),
					'callback'=> 'tslot_color_input_callback',
				),
				array(
					'id'=> 'btn-color-hover',
					'title'=> esc_html__('Text Hover Color', 'timeslot'),
					'callback'=> 'tslot_color_input_callback'
				),
				array(
					'id'=> 'btn-font',
					'title'=> esc_html__('Font', 'timeslot'),
					'callback'=> 'tslot_font_callback'
				),
				array(
					'id'=> 'btn-text-transform',
					'title'=> esc_html__('Letter Case', 'timeslot'),
					'callback'=> 'tslot_text_transform_callback'
				),
				array(
					'id'=> 'btn-border',
					'title'=> esc_html__('Border', 'timeslot'),
					'callback'=> 'tslot_border_callback'
				),
				array(
					'id'=> 'btn-border-color-hover',
					'title'=> esc_html__('Button Border Hover Color', 'timeslot'),
					'callback'=> 'tslot_color_input_callback'
				),
				array(
					'id'=> 'btn-border-radius',
					'title'=> esc_html__('Border Radius', 'timeslot'),
					'callback'=> 'tslot_border_radius_callback'
				)
			)
		),
		'input-section' => array(
			'title'=> esc_html__('Input Fields', 'timeslot'),
			'page'=>'timeslot-appearance-tab',
			'fields'=> array(
				array(
					'id'=> 'input-bgc',
					'title'=> esc_html__('Background Color', 'timeslot'),
					'callback'=> 'tslot_color_input_callback',
				),
				array(
					'id'=> 'input-color',
					'title'=> esc_html__('Text Color', 'timeslot'),
					'callback'=> 'tslot_color_input_callback',
				),
				array(
					'id'=> 'input-font',
					'title'=> esc_html__('Font', 'timeslot'),
					'callback'=> 'tslot_font_callback',
				),
				array(
					'id'=> 'input-border',
					'title'=> esc_html__('Border', 'timeslot'),
					'callback'=> 'tslot_border_callback'
				),
				array(
					'id'=> 'input-border-radius',
					'title'=> esc_html__('Border Radius', 'timeslot'),
					'callback'=> 'tslot_border_radius_callback'
				),
			)
		),
		'label-section' => array(
			'title'=> esc_html__('Labels', 'timeslot'),
			'page'=>'timeslot-appearance-tab',
			'fields'=> array(
				array(
					'id'=> 'label-color',
					'title'=> esc_html__('Label Color', 'timeslot'),
					'callback'=> 'tslot_color_input_callback',
				),
				array(
					'id'=> 'label-font',
					'title'=> esc_html__('Font', 'timeslot'),
					'callback'=> 'tslot_font_callback',
				),
			)
		),
		'placeholder-section' => array(
			'title'=> esc_html__('Placeholders', 'timeslot'),
			'page'=>'timeslot-appearance-tab',
			'fields'=> array(
				array(
					'id'=> 'placeholder-color',
					'title'=> esc_html__('Placeholder Color', 'timeslot'),
					'callback'=> 'tslot_color_input_callback',
				),
			)
		),
		'accent-section' => array(
			'title'=> esc_html__('Accent', 'timeslot'),
			'page'=>'timeslot-appearance-tab',
			'fields'=> array(
				array(
					'id'=> 'accent-bgc',
					'title'=> esc_html__('Accent Background Color', 'timeslot'),
					'callback'=> 'tslot_color_input_callback',
				),
				array(
					'id'=> 'accent-color',
					'title'=> esc_html__('Accent Color', 'timeslot'),
					'callback'=> 'tslot_color_input_callback',
				),
			)
		),
		'summary-section' => array(
			'title'=> esc_html__('Summary', 'timeslot'),
			'page'=>'timeslot-appearance-tab',
			'fields'=> array(
				array(
					'id'=> 'heading-color',
					'title'=> esc_html__('Heading Color', 'timeslot'),
					'callback'=> 'tslot_color_input_callback',
				),
				array(
					'id'=> 'heading-font',
					'title'=> esc_html__('Heading Font', 'timeslot'),
					'callback'=> 'tslot_font_callback',
				),
				array(
					'id'=> 'text-color',
					'title'=> esc_html__('Text Color', 'timeslot'),
					'callback'=> 'tslot_color_input_callback',
				),
				array(
					'id'=> 'text-font',
					'title'=> esc_html__('Text Font', 'timeslot'),
					'callback'=> 'tslot_font_callback',
				),
			)
		),
		'success-section' => array(
			'title'=> esc_html__('Success Message', 'timeslot'),
			'page'=>'timeslot-appearance-tab',
			'fields'=> array(
				array(
					'id'=> 'success-color',
					'title'=> esc_html__('Text Color', 'timeslot'),
					'callback'=> 'tslot_color_input_callback',
				),
				array(
					'id'=> 'success-font',
					'title'=> esc_html__('Font', 'timeslot'),
					'callback'=> 'tslot_font_callback',
				),
			)
		),
	);

	foreach( $settings as $id => $values){
		add_settings_section(
			$id,
			$values['title'],
			'',
			$values['page']
		);

		foreach ($values['fields'] as $field) {
			add_settings_field(
				$field['id'],
				$field['title'],
				$field['callback'],
				$values['page'],
				$id,
				array(
					$values['page'],
					$field['id'],
					'label_for' => $field['id'],
					'class' => 'ts-settings-row',
					'aria' => $field['title'],
				)
			);
		}

		register_setting($values['page'], $values['page'], 'tslot_appearance_validation');

	}

}

// Page headers
function tslot_appearance_callback() {
	$h1_appearance = __('Appearance', 'timeslot');
	$subtitle_appearance = __('Set the appearance of the appointment form.', 'timeslot');
	echo('<h1>'. esc_html($h1_appearance) .'</h1><p class="ts-subtitle">'. esc_html($subtitle_appearance) .'</p>');
}

// Colors
function tslot_color_input_callback($args) {

	$ts_color = get_option($args[0]);
	$val = isset( $ts_color[""  . $args[1] . ""] ) ? esc_attr( $ts_color[""  . $args[1] . ""] ) : "";
	$id = $args[1];
	$name = $args[0] ."[" . $args[1] . "]";

	?>
	<input type='text' id='<?php esc_attr_e($id); ?>' class='ts-color-input' name='<?php esc_attr_e($name); ?>' value='<?php esc_attr_e($val); ?>' aria-label='<?php esc_attr_e($args['aria']); ?>'/>
	<?php

}

// Fonts
function tslot_font_callback($args) {

	$ts_fonts = (array) get_option($args[0]);
	$font_size_id = $args[1] . "-size";
	$font_size_name = $args[0] ."[" . $args[1] . "][size]";
	$font_family_name = $args[0] ."[" . $args[1] . "][family]";
	$font_weight_name = $args[0] ."[" . $args[1] . "][weight]";
	$font_size_set = $ts_fonts["" . $args[1] . ""]["size"] ?? '';
	$font_family_set = $ts_fonts[""  . $args[1] . ""]["family"] ?? '';
	$font_weight_set = $ts_fonts[""  . $args[1] . ""]["weight"] ?? '';
	$font_size_value = isset( $font_size_set ) ? esc_attr( $font_size_set ) : "";
	$font_family_selected = isset( $font_family_set ) ? esc_attr( $font_family_set ) : "";
	$font_weight_selected = isset( $font_weight_set ) ? esc_attr( $font_weight_set ) : "";

	$ts_font_families = array(
		'Arial' => 'sans-serif',
		'Brush Script MT' => 'cursive',
		'Courier New' => 'monospace',
		'Georgia' => 'serif',
		'Helvetica' => 'sans-serif',
		'Tahoma' => 'sans-serif',
		'Times New Roman' => 'serif',
		'Trebuchet MS' => 'sans-serif',
		'Verdana' => 'sans-serif'
	);

	$font_weights = array(
		'Thin' => '100',
		'Extra Light' => '200',
		'Light' => '300',
		'Normal' => '400',
		'Medium' => '500',
		'Semi Bold' => '600',
		'Bold' => '700',
		'Extra Bold' => '800',
		'Heavy' => '900'
	);

	?>

	<span class='ts-css-group-wrapper'>

		<!-- Font Size -->
		<span class='ts-unit-wrapper'>
			<input type='number' id='<?php esc_attr_e($font_size_id); ?>' class='ts-number' name='<?php esc_attr_e($font_size_name); ?>' value='<?php esc_attr_e($font_size_value); ?>' min='0' aria-label='<?php esc_attr_e('Font Size in Pixels', 'timeslot');?>' />
			<label for='<?php esc_attr_e($font_size_id); ?>' class='ts-px-block' aria-hidden='true'>px</label>
		</span>


		<!-- Font Family -->
		<select class='ts-font-family' name='<?php esc_attr_e($font_family_name); ?>' aria-label='<?php esc_attr_e('Font Family', 'timeslot');?>'>

			<option></option>
			<option value='inherit' <?php selected($font_family_selected, 'inherit'); ?>>
				<?php esc_html_e('Inherit', 'timeslot')?>
			</option>

			<?php
			foreach ($ts_font_families as $ts_font_family => $ts_font_style){

				$full_font = $ts_font_family . ', ' . $ts_font_style;

				?>
				<option value='<?php esc_attr_e($full_font); ?>' <?php selected($font_family_selected, $full_font); ?>>
					<?php esc_html_e($ts_font_family, 'timeslot')?>
				</option>
				<?php
			}?>

		</select>


		<!-- Font Weight -->
		<select class='ts-font-weight' name='<?php esc_attr_e($font_weight_name); ?>' aria-label='<?php esc_attr_e('Font Weight', 'timeslot');?>'>

			<option></option>

			<?php
			foreach ($font_weights as $name => $number){
				?>
				<option value='<?php esc_attr_e($number); ?>' <?php selected($font_weight_selected, $number); ?> >
					<?php esc_html_e($name, 'timeslot')?>
				</option>
				<?php
			}?>

		</select>

	</span>
	<?php
}

// Text Transform
function tslot_text_transform_callback($args) {

	$ts_transforms = (array) get_option($args[0]);
	$text_transform_name = $args[0] ."[" . $args[1] . "]";
	$text_transform_set = $ts_transforms[""  . $args[1] . ""] ?? '';
	$text_transform_selected = isset( $text_transform_set ) ? esc_attr( $text_transform_set ) : "";
	$id = $args[1];

	$ts_text_transforms = array(
		'none',
		'uppercase',
		'lowercase',
		'capitalize'
	);
	?>

	<!-- Font Family -->
	<select class='ts-text-transform' id='<?php esc_attr_e($id); ?>' name='<?php esc_attr_e($text_transform_name); ?>' aria-label='<?php esc_attr_e('Letter Case', 'timeslot');?>'>

		<option></option>

		<?php
		foreach ($ts_text_transforms as $ts_text_transform){
			?>
			<option value='<?php esc_attr_e($ts_text_transform); ?>' <?php selected($text_transform_selected, $ts_text_transform); ?>>
				<?php esc_html_e(ucfirst($ts_text_transform), 'timeslot')?>
			</option>
			<?php
		}?>

	</select>
	<?php
}

// Borders
function tslot_border_callback($args) {

	$ts_border = (array) get_option($args[0]);
	$ts_border_set = $ts_border[""  . $args[1] . ""] ?? '';
	$border_width_id = $args[1] . "-width";
	$border_width_name = $args[0] ."[" . $args[1] . "][width]";
	$border_style_name = $args[0] ."[" . $args[1] . "][style]";
	$border_color_name = $args[0] ."[" . $args[1] . "][color]";
	$border_width_set = isset( $ts_border_set["width"] ) ? esc_attr( $ts_border_set["width"] ) : "";
	$border_style_set = isset( $ts_border_set["style"] ) ? esc_attr( $ts_border_set["style"] ) : "";
	$border_color_set = isset( $ts_border_set["color"] ) ? esc_attr( $ts_border_set["color"] ) : "";

	$border_styles = array(
		'None',
		'Solid',
		'Dashed',
		'Dotted',
		'Double'
	);

	?>

	<span class='ts-css-group-wrapper'>

		<!-- Border Width -->
		<span class='ts-unit-wrapper'>
			<input type='number' id='<?php esc_attr_e($border_width_id); ?>' class='ts-number' name='<?php esc_attr_e($border_width_name); ?>' value='<?php esc_attr_e($border_width_set); ?>' min='0' aria-label='<?php esc_attr_e('Border Width in Pixels', 'timeslot');?>' />
			<label for='<?php esc_attr_e($border_width_id); ?>' class='ts-px-block' aria-hidden='true'>px</label>
		</span>

		<!-- Border Style -->
		<select class='ts-border-style' name='<?php esc_attr_e($border_style_name); ?>' aria-label='<?php esc_attr_e('Border Style', 'timeslot');?>'>

			<option></option>

			<?php
			foreach ($border_styles as $border_style){

				$border_style_lower = strtolower($border_style);

				?>
				<option value='<?php esc_attr_e($border_style_lower);?>' <?php selected( $border_style_set, $border_style_lower); ?> >
					<?php esc_html_e($border_style, 'timeslot')?>
				</option>
				<?php

			}?>

		</select>

		<!-- Border Color -->
		<input type='text' class='ts-color-input' name='<?php esc_attr_e($border_color_name); ?>' value='<?php esc_attr_e($border_color_set); ?>' aria-label='<?php esc_attr_e('Border Color', 'timeslot');?>' />

	</span>
	<?php
}

// Border radius
function tslot_border_radius_callback($args) {

	$ts_border_radius = get_option($args[0]);
	$border_radius_id = $args[1];
	$border_radius_name = $args[0] ."[" . $args[1] . "]";
	$border_radius_set = isset( $ts_border_radius[""  . $args[1] . ""] ) ? esc_attr( $ts_border_radius[""  . $args[1] . ""] ) : "";

	?>
	<span class='ts-unit-wrapper'>
		<input type='number' id='<?php esc_attr_e($border_radius_id); ?>' name='<?php esc_attr_e($border_radius_name); ?>' value='<?php esc_attr_e($border_radius_set); ?>' min='0' aria-label='<?php esc_attr_e('Border Radius in Pixels', 'timeslot');?>' />
		<label for='<?php esc_attr_e($border_radius_id); ?>' class='ts-px-block' aria-hidden='true'>px</label>
	</span>
	<?php

}

// Validation
function tslot_appearance_validation($input) {

	foreach ( $input as $key => $val ) {

		$setting = $input[$key];

		switch($key){

			case 'btn-bgc':
			case 'btn-bgc-hover':
			case 'btn-color':
			case 'btn-color-hover':
			case 'btn-border-color-hover':
			case 'input-bgc':
			case 'input-color':
			case 'label-color':
			case 'placeholder-color':
			case 'accent-bgc':
			case 'accent-color':

				if (isset($setting)){
					$setting = (
						preg_match('/^#([0-9A-F]{3}){1,2}$/i', $val) ? $val : ''
					);
				}

				break;

			case 'input-border-radius':
			case 'btn-border-radius':

				if (isset($setting)){
					$setting = (
						absint($val) ? $val : ''
					);
				}

				break;

			case 'btn-font':
			case 'input-font':
			case 'label-font':

				if (isset($setting['size'])){
					$setting['size'] = (
						absint($setting['size']) ? $setting['size'] : ''
					);
				}

				if (isset($setting['family'])){
					$setting['family'] = sanitize_text_field($setting['family']);
				}

				if (isset($setting['weight'])){
					$setting['weight'] = sanitize_text_field($setting['weight']);
				}

				break;

			case 'btn-border':
			case 'input-border':

				if (isset($setting['width'])){
					$setting['width'] = (
						absint($setting['width']) ? $setting['width'] : ''
					);
				}

				if (isset($setting['style'])){
					$setting['style'] = sanitize_text_field($setting['style']);
				}

				if (isset($setting['color'])){
					$setting['color'] = (
						preg_match('/^#([0-9A-F]{3}){1,2}$/i',$setting['color']) ? $setting['color'] : ''
					);
				}

				break;

				
			case 'btn-text-transform':

				if (isset($setting)){
					$setting = sanitize_text_field($setting);
				}

				break;

			default:
				break;
		}

	}

	return $input;
}