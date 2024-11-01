/**
 * Registers form block
 *
 * Registers booking form block with icon
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */

const el = wp.element.createElement;
const registerBlockType = wp.blocks.registerBlockType;
const textControl = wp.components.TextControl;
const inspectorControls = wp.blockEditor.InspectorControls;
const selectControl = wp.components.SelectControl;
const checkboxControl = wp.components.CheckboxControl;
const buttonComponent = wp.components.Button;
const disabledComponent = wp.components.Disabled;
const panelBody = wp.components.PanelBody;
const fragmentElement = wp.element.Fragment;
const { __ } = wp.i18n;
const useBlockProps = wp.blockEditor.useBlockProps;

// Include staff select control if staff exists
const staffSelect =el(
	selectControl,{
		label: __('Staff', 'timeslot'),
		value: 'staff',
		className: 'ts-block-select',
		options:
		[{
				label: __('Select Staff Member', 'timeslot'),
				value: 'select-staff'
			},]
	}
);
const staffEl = tsvars.is_staff ? staffSelect : null;

// Create block icon from svg
const tsCalendarIcon = el('svg',
	{
		width: 24,
		height: 24
	},
	el( 'path',
		{
			d: "M5.01,4c.55,0,1-.45,1-1V1c0-.55-.45-1-1-1s-1,.45-1,1V3c0,.55,.45,1,1,1Z"
		}
	),
	el( 'path',
		{
			d: "M19.01,4c.55,0,1-.45,1-1V1c0-.55-.45-1-1-1s-1,.45-1,1V3c0,.55,.45,1,1,1Z"
		}
	),
	el( 'path',
		{
			d: "M21,2v1c0,1.1-.9,2-2,2s-2-.9-2-2V2H7v1c0,1.1-.9,2-2,2s-2-.9-2-2V2H0V6.55H24V2h-3Z"
		}
	),
	el( 'path',
		{
			d: "M0,24H24V7.7H0V24Zm12.16-6.59h1.58c0,.6,.2,1.04,.61,1.33,.41,.3,.95,.44,1.61,.44,.6,0,1.07-.12,1.41-.36,.33-.24,.5-.57,.5-.99s-.15-.73-.45-.99c-.3-.26-.84-.49-1.6-.7-1.1-.28-1.94-.66-2.53-1.14-.58-.47-.88-1.09-.88-1.85s.33-1.44,.98-1.95c.65-.51,1.49-.76,2.52-.76s1.94,.29,2.59,.86c.65,.58,.96,1.28,.94,2.09v.04h-1.58c0-.5-.17-.91-.51-1.22-.34-.31-.82-.47-1.45-.47s-1.06,.13-1.38,.39-.48,.6-.48,1.01c0,.37,.18,.68,.52,.92,.35,.25,.92,.48,1.73,.7,1.05,.28,1.85,.67,2.39,1.17,.55,.49,.82,1.13,.82,1.9,0,.81-.32,1.46-.97,1.94-.66,.48-1.51,.72-2.57,.72s-1.92-.27-2.69-.81c-.77-.55-1.14-1.29-1.12-2.23v-.04ZM3.87,10.58h7.54v1.31h-2.95v8.46h-1.63V11.89H3.87v-1.31Z"
		}
	),
);

registerBlockType( 'timeslot/booking-form', {

	icon: tsCalendarIcon,

	edit: function( props ) {

		// Initiate Select2 on select inputs
		jQuery('.ts-block-select select').select2();

		var blockProps = useBlockProps(
			{
			className: 'ts-block-wrapper',
			}
		);

		return[

			el(
				fragmentElement,
				null,
				el(
					inspectorControls,
					null,
					el(
						panelBody, {
							initialOpen: true,
							title: __('Form Options', 'timeslot'),
						},
						el(
							'span', {},
							__('Want to style your form? ', 'timeslot'),
						),
						el(
							'a', {
								href: tsvars.tsadminurl + '?page=timeslot-general&tab=appearance',
							},
							__(' Go to the Time Slot settings.', 'timeslot'),
						)
					)
				),
			),
			el(
				'div',
				blockProps,
				el(
					disabledComponent,
					null,
					el(
						'div',{
							className: 'ts-form-wrapper'
						},
						el(
							'form',{
								className: 'ts-form'
							},
							el(
								selectControl,{
									label: __('Service', 'timeslot'),
									value: 'service',
									className: 'ts-block-select',
									options:
										[{
											label: __('Select Service', 'timeslot'),
											value: 'select-service'
										},]

								}
							),
							staffEl,
							el(
								textControl,{
									className: 'ts-block-input',
									label: __('Appointment Date', 'timeslot'),
									value:  __('Select Date', 'timeslot'),
								},
							),
							el(
								selectControl,{
									label:  __('Appointment Time', 'timeslot'),
									value: 'time',
									className: 'ts-block-select',
									options:
										[{
											label:  __('Select Time', 'timeslot'),
											value: 'select-time'
										},]
								}
							),
							el(
								textControl,{
									className: 'ts-block-input',
									label: __('Name', 'timeslot'),
									value: __('Name', 'timeslot'),
								},
							),
							el(
								textControl,{
									className: 'ts-block-input',
									label: __('Email', 'timeslot'),
									value: __('Email', 'timeslot'),
								},
							),
							el(
								textControl,{
									className: 'ts-block-input',
									label: __('Phone', 'timeslot'),
									value: __('Phone', 'timeslot'),
								},
							),
							el(
								checkboxControl,{
									className: 'ts-block-checkbox',
									label: __('Have a Coupon Code?', 'timeslot'),
									value: 'coupon',
								},
							),
							el(
								buttonComponent,{
									className: 'ts-btn',
									text: __('Continue', 'timeslot'),
								},
							),
						)
					)
				)
			)
		]
	},

	save: function() {
		return null;
	}
});