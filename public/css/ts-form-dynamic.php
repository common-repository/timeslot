<?php
/**
 * Styles for front end appointment form
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */

global $wpdb;
$appearance_options = get_option('timeslot-appearance-tab');

// Button
$btn_bgc = $appearance_options['btn-bgc'] ?? '';
$btn_bgc_inherit = !empty($btn_bgc) ? $btn_bgc : 'inherit';
$btn_bgc_gray = !empty($btn_bgc) ? $btn_bgc : '#016ee0';

// Button hover
$btn_bgc_hover = $appearance_options['btn-bgc-hover'] ?? '';
$btn_bgc_hover_css = !empty($btn_bgc_hover) ? $btn_bgc_hover : '#2f5eb0';

// Button text color
$btn_color = $appearance_options['btn-color'] ?? '';
$btn_color_css = !empty($btn_color) ? $btn_color : '#fff';
$btn_color_hover = $appearance_options['btn-color-hover'] ?? '';
$btn_color_hover_css = !empty($btn_color_hover) ? $btn_color_hover : '#fff';

// Button text transform
$btn_text_transform = $appearance_options['btn-text-transform'] ?? '';
$btn_text_transform_css = !empty($btn_text_transform) ? $btn_text_transform : 'inherit';

// Button font
$btn_font_size = $appearance_options['btn-font']['size'] ?? '';
$btn_font_size_css = !empty($btn_font_size) ? $btn_font_size . 'px' : 'inherit';
$btn_font_family = $appearance_options['btn-font']['family'] ?? '';
$btn_font_family_css = !empty($btn_font_family) ? $btn_font_family : 'inherit';
$btn_font_weight = $appearance_options['btn-font']['weight'] ?? '';
$btn_font_weight_css = !empty($btn_font_weight) ? $btn_font_weight : '700';

// Button border
$btn_border_width = !empty($appearance_options['btn-border']['width']) ? $appearance_options['btn-border']['width'] : '0';
$btn_border_style = !empty($appearance_options['btn-border']['style']) ? $appearance_options['btn-border']['style'] : 'none';
$btn_border_color = !empty($appearance_options['btn-border']['color']) ? $appearance_options['btn-border']['color'] : 'transparent';
$btn_border_color_hover = $appearance_options['btn-border-color-hover'] ?? '';
$btn_border_color_hover_css = !empty($btn_border_color_hover) ? $btn_border_color_hover : 'inherit';
$btn_border = $btn_border_width . 'px ' . $btn_border_style . ' ' . $btn_border_color;
$btn_border_css = !empty($btn_border) ? $btn_border : 'none';

// Button border radius
$btn_border_radius = $appearance_options['btn-border-radius'] ?? '';
$btn_border_radius_css = !empty($btn_border_radius) ? $btn_border_radius . 'px' : '0';

// Input background color
$input_bgc = $appearance_options['input-bgc'] ?? '';
$input_bgc_css = !empty($input_bgc) ? $input_bgc : 'transparent';

// Input text color
$input_color = $appearance_options['input-color'] ?? '';
$input_color_css = !empty($input_color) ? $input_color : '#444';

// Input font
$input_font_size = $appearance_options['input-font']['size'] ?? '';
$input_font_size_css = !empty($input_font_size) ? $input_font_size . 'px' : 'inherit';
$input_font_family = $appearance_options['input-font']['family'] ?? '';
$input_font_family_css = !empty($input_font_family) ? $input_font_family : 'inherit';
$input_font_weight = $appearance_options['input-font']['weight'] ?? '';
$input_font_weight_css = !empty($input_font_weight) ? $input_font_weight : '400';

// Input border
$input_border_width = !empty($appearance_options['input-border']['width']) ? $appearance_options['input-border']['width'] : '1';
$input_border_width_css = $input_border_width . 'px';

// Select arrow border
$input_border_width_arrow_pos = !empty($input_border_width) ? $input_border_width . 'px' : '1px';
$input_border_width_arrow_height = !empty($input_border_width) ? ($input_border_width * 2) . 'px' : '0';

// Checkbox border
$input_border_width_chk_lft = !empty($input_border_width) ? (10 - $input_border_width) . 'px' : '10px';
$input_border_width_chk_top = !empty($input_border_width) ? (4 - $input_border_width) . 'px' : '4px';

// Input border css
$input_border_style = !empty($appearance_options['input-border']['style']) ? $appearance_options['input-border']['style'] : 'solid';
$input_border_color = !empty($appearance_options['input-border']['color']) ? $appearance_options['input-border']['color'] : '#bbb';

$input_border = $input_border_width . 'px ' . $input_border_style . ' ' . $input_border_color;
$input_border_css = !empty($input_border) ? $input_border : '1px solid #bbb';

// Input border radius
$input_border_radius = $appearance_options['input-border-radius'] ?? '';
$input_border_radius_css = !empty($input_border_radius) ? $input_border_radius . 'px' : '0';

// Label color
$label_color = $appearance_options['label-color'] ?? '';
$label_color_css = !empty($label_color) ? $label_color : '#444';

// Label font
$label_font_size = $appearance_options['label-font']['size'] ?? '';
$label_font_size_css = !empty($label_font_size) ? $label_font_size . 'px' : 'inherit';
$label_font_family = $appearance_options['label-font']['family'] ?? '';
$label_font_family_css = !empty($label_font_family) ? $label_font_family : 'inherit';
$label_font_weight = $appearance_options['label-font']['weight'] ?? '';
$label_font_weight_css = !empty($label_font_weight) ? $label_font_weight : '700';

// Summary header color
$heading_color = $appearance_options['heading-color'] ?? '';
$heading_color_css = !empty($heading_color) ? $heading_color : $label_color_css;

// Summary header font
$heading_font_size = $appearance_options['heading-font']['size'] ?? '';
$heading_font_size_css = !empty($heading_font_size) ? $heading_font_size . 'px' : $label_font_size_css;
$heading_font_family = $appearance_options['heading-font']['family'] ?? '';
$heading_font_family_css = !empty($heading_font_family) ? $heading_font_family : $label_font_family_css;
$heading_font_weight = $appearance_options['heading-font']['weight'] ?? '';
$heading_font_weight_css = !empty($heading_font_weight) ? $heading_font_weight : $label_font_weight_css;

// Summary text color
$text_color = $appearance_options['text-color'] ?? '';
$text_color_css = !empty($text_color) ? $text_color : $input_color_css;

// Summary text font
$text_font_size = $appearance_options['text-font']['size'] ?? '';
$text_font_size_css = !empty($text_font_size) ? $text_font_size . 'px' : $input_font_size_css;
$text_font_family = $appearance_options['text-font']['family'] ?? '';
$text_font_family_css = !empty($text_font_family) ? $text_font_family : $input_font_family_css;
$text_font_weight = $appearance_options['text-font']['weight'] ?? '';
$text_font_weight_css = !empty($text_font_weight) ? $text_font_weight : $input_font_weight_css;

// Success text color
$success_color = $appearance_options['success-color'] ?? '';
$success_color_css = !empty($success_color) ? $success_color : $heading_color_css;

// Success text font
$success_font_size = $appearance_options['success-font']['size'] ?? '';
$success_font_size_css = !empty($success_font_size) ? $success_font_size . 'px' : $heading_font_size_css;
$success_font_family = $appearance_options['success-font']['family'] ?? '';
$success_font_family_css = !empty($success_font_family) ? $success_font_family : $heading_font_family_css;
$success_font_weight = $appearance_options['success-font']['weight'] ?? '';
$success_font_weight_css = !empty($success_font_weight) ? $success_font_weight : $heading_font_weight_css;

// Placeholder
$placeholder_color = $appearance_options['placeholder-color'] ?? '';
$placeholder_color_css = !empty($placeholder_color) ? $placeholder_color : '#999';

// Accent Color
$accent_bgc = $appearance_options['accent-bgc'] ?? '';
$accent_bgc_css = !empty($accent_bgc) ? $accent_bgc : '#016ee0';
$accent_color = $appearance_options['accent-color'] ?? '';
$accent_color_css = !empty($accent_color) ? $accent_color : '#fff';

$ts_include_css = "

body .ts-form-wrapper .ts-form .ts-fieldset .ts-btn,
body .ts-form-wrapper .ts-form .ts-fieldset #ts-btn-submit,
body .ts-form-wrapper .ts-form button,
body .ts-form-wrapper .ts-form .ts-fieldset button {
	background-color: {$btn_bgc_gray};
	border: {$btn_border_css};
	border-radius: {$btn_border_radius_css};
	color: {$btn_color_css};
	font-family: {$btn_font_family_css};
	font-size: {$btn_font_size_css};
	font-weight: {$btn_font_weight_css};
	text-transform: {$btn_text_transform_css};
}

body .ts-form-wrapper .ts-form .ts-fieldset .ts-btn:hover,
body .ts-form-wrapper .ts-form .ts-fieldset #ts-btn-submit:hover,
body .ts-form-wrapper .ts-form button:hover,
body .ts-form-wrapper .ts-form .ts-fieldset button:hover,
body .ts-form-wrapper .ts-form .ts-fieldset .ts-btn:focus,
body .ts-form-wrapper .ts-form button:focus,
body .ts-form-wrapper .ts-form .ts-fieldset button:focus {
	background-color: {$btn_bgc_hover_css};
	border-color: {$btn_border_color_hover_css};
	color: {$btn_color_hover_css};
}

.ts-form-wrapper #ts-btn-prev {
	color: {$btn_bgc_gray};
}

.ts-form-wrapper #ts-btn-prev:hover {
	color: {$btn_bgc_hover_css};
}

.ts-form-wrapper label,
.ts-form-wrapper .ts-form label.components-text {
	color: {$label_color_css}!important;
	font-size: {$label_font_size_css};
	font-family: {$label_font_family_css};
	font-weight: {$label_font_weight_css};
	text-transform: inherit;
}
.ts-form-wrapper .ts-summary h2 {
	font-size: calc({$heading_font_size_css} * 1.4);
}

.ts-form-wrapper .ts-summary h3,
.ts-form-wrapper .ts-summary h5 {
	font-size: {$heading_font_size_css};
}

.ts-form-wrapper .ts-summary h2,
.ts-form-wrapper .ts-summary h3,
.ts-form-wrapper .ts-summary h5 {
	color: {$heading_color_css};
	font-family: {$heading_font_family_css};
	font-weight: {$heading_font_weight_css};
}

.ts-form-wrapper .ts-summary p,
.ts-form-wrapper .ts-summary span {
	color: {$text_color_css};
	font-family: {$text_font_family_css};
	font-size: {$text_font_size_css};
	font-weight: {$text_font_weight_css};
}

.ts-form-wrapper .ts-summary .ts-error-msg,
.ts-form-wrapper .ts-summary .ts-success-msg {
	color: {$success_color_css};
	font-family: {$success_font_family_css};
	font-size: {$success_font_size_css};
	font-weight: {$success_font_weight_css};
}

.ts-form-wrapper input::placeholder {
	color: {$placeholder_color_css};
	opacity: 1;
}

.ts-form-wrapper input::-webkit-input-placeholder {
	color: {$placeholder_color_css};
	opacity: 1;
}

.ts-form-wrapper input:-ms-input-placeholder {
	color: {$placeholder_color_css};
	opacity: 1;
}

.ts-form-wrapper .select2-container--default .select2-selection--single .select2-selection__placeholder {
	color: {$placeholder_color_css};
	opacity: 1;
}

.ts-form-wrapper input[type=text]:focus,
.ts-form-wrapper input[type=email]:focus,
.ts-form-wrapper input[type=tel]:focus,
body .ts-form-wrapper select:focus,
.ts-form-wrapper input[type=text]:focus-visible,
.ts-form-wrapper input[type=email]:focus-visible,
.ts-form-wrapper input[type=tel]:focus-visible,
body .ts-form-wrapper select:focus-visible,
body .ts-form-wrapper .select2-container *:focus,
body .ts-form-wrapper .select2-container *:focus,
body .ts-form-wrapper .select2-container *:focus-visible,
body .ts-form-wrapper .select2-container *:focus-visible,
.select2-container--default.select2-container--focus .select2-selection--multiple.ts-select2-container {
	border-color: {$accent_bgc_css};
	box-shadow: none;
	outline: 2px solid {$accent_bgc_css};
	outline-offset: -2px;
}

.ts-form-wrapper input[type=text],
.ts-form-wrapper input[type=email],
.ts-form-wrapper input[type=tel],
body .ts-form-wrapper select,
body .ts-form-wrapper .ts-block-select .components-select-control__input {
	background-color: {$input_bgc_css};
	border: {$input_border_css};
	border-radius: {$input_border_radius_css};
	color: {$input_color_css};
	font-family: {$input_font_family_css};
	font-size: {$input_font_size_css};
	font-weight: {$input_font_weight_css};
}

.ts-form-wrapper input.ts-appt-error,
.ts-form-wrapper select.ts-appt-error + .select2,
.ts-checkbox-label input:checked:focus + .ts-checkmark,
.ts-checkbox-label input:not(:checked):focus + .ts-checkmark {
	outline-width: {$input_border_width_arrow_pos};
	outline-offset: -{$input_border_width_arrow_pos};
}

.ts-form-wrapper div.ts-appt-error {
	color: {$label_color_css};
}

body .ts-form-wrapper .ts-form .ts-fieldset ::selection {
	background: {$accent_bgc_css};
}

body .ts-form-wrapper .ts-form .ts-fieldset ::-moz-selection {
	background: {$accent_bgc_css};
}

.ts-checkmark,
.ts-block-checkbox input[type=checkbox] {
	background-color: {$input_bgc_css};
	border: {$input_border_css};
	border-radius: {$input_border_radius_css};
}

.ts-checkbox-label input:checked ~ .ts-checkmark {
	background-color: {$btn_bgc_gray};
}

.ts-checkbox-label .ts-checkmark:after {
	border: solid {$btn_color_css};
	left: {$input_border_width_chk_lft};
	top: {$input_border_width_chk_top};
}

.ts-checkbox-label input:checked:focus + .ts-checkmark,
.ts-checkbox-label input:not(:checked):focus + .ts-checkmark {
	outline: solid {$btn_bgc_gray};
}

.ts-form-wrapper .select2-container--default .select2-selection--single .select2-selection__rendered {
	background-color: {$input_bgc_css};
	border-radius: {$input_border_radius_css};
	color: {$input_color_css};
	font-family: {$input_font_family_css};
	font-size: {$input_font_size_css};
	font-weight: {$input_font_weight_css};
}

.ts-form-wrapper .select2-container--default .select2-selection--single,
.ts-block-select .select2 + span.components-input-control__suffix + .components-input-control__backdrop {
	border: {$input_border_css};
	border-radius: {$input_border_radius_css};
}

.ts-block-select .components-input-control__container,
.ts-form-wrapper .select2-container--default .select2-selection--single {
	background-color: {$input_bgc_css};
}

.ts-form-wrapper .select2-container--default .select2-selection--single .select2-selection__arrow {
	background-color: {$accent_bgc_css};
	height: calc(100% - {$input_border_width_arrow_height});
	right: {$input_border_width_arrow_pos};
	top: {$input_border_width_arrow_pos};
}

.ts-block-select select + span.components-input-control__suffix > div {
	background-color: {$accent_bgc_css};
	fill: {$accent_color_css};
	right: {$input_border_width_css};
}

.ts-form-wrapper .select2-container--default .select2-selection--single .select2-selection__arrow b {
	border-color: {$accent_color_css} transparent transparent transparent;
}

.ts-form-wrapper .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
	border-color: transparent transparent {$accent_color_css} transparent;
}

.ts-form span.select2 {
	border-radius: {$input_border_radius_css};
}

.select2-container--default .ts-select2 .select2-results__option--highlighted[aria-selected],
.select2-results .ts-select2 .select2-highlighted {
	background-color: {$accent_bgc_css};
	color: {$accent_color_css};
}

.select2-container--default .ts-select2 .select2-results__option--selected {
	background-color: {$accent_bgc_css};
	color: {$accent_color_css};
}

body .ts-form-wrapper .select2-dropdown.ts-select2 ul {
	color: {$input_color_css};
	font-family: {$input_font_family_css};
	font-size: {$input_font_size_css};
	font-weight: {$input_font_weight_css};
}

#ts-input-date.hasDatepicker,
.ts-block-preview-wrapper #ts-input-date {
	background-color: {$input_bgc_css};
	border: {$input_border_css};
	border-radius: {$input_border_radius_css};
	color: {$input_color_css};
	font-family: {$input_font_family_css};
	font-size: {$input_font_size_css};
	font-weight: {$input_font_weight_css};
}

.ts-datepicker .ui-datepicker-header {
	background: {$accent_bgc_css};
	color: {$accent_color_css};
}

.ts-datepicker .ui-datepicker-header .ui-datepicker-next,
.ts-datepicker .ui-datepicker-header .ui-datepicker-prev,
.ts-datepicker .ui-datepicker-header .ui-datepicker-next:after,
.ts-datepicker .ui-datepicker-header .ui-datepicker-prev:after {
	color: {$accent_color_css};
}

.ts-datepicker a.ui-datepicker-next.ui-state-disabled:after,
.ts-datepicker a.ui-datepicker-prev.ui-state-disabled:after {
	color: {$accent_bgc_css};
}

@keyframes ts-dot-1 {
	30% {
		background-color: {$accent_bgc_css};
	}
}

@keyframes ts-dot-2 {
	45% {
		background-color: {$accent_bgc_css};
	}
}

@keyframes ts-dot-3 {
	60% {
			background-color: {$accent_bgc_css};
	}
}
";

$ts_form_styles_min = str_replace( array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $ts_include_css );