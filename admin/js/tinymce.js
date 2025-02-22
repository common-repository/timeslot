/**
 * Configures TinyMCE button
 *
 * Creates TinyMCE buttons on Business Settings > Emails tab
 * to insert Time Slot variables into email body and send
 * test emails.
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */

const {__} = wp.i18n;

const staffOption = {
	title: top.tinymce.activeEditor.getLang('timeslotButton.staff'),
	type: 'form',
	items: [{
		label: top.tinymce.activeEditor.getLang('timeslotButton.staff'),
		name: 'staffdata',
		type: 'listbox',
		value: '',
		values: [
			{value: '', text: top.tinymce.activeEditor.getLang('timeslotButton.select')},
			{value: '[staff_name]', text: top.tinymce.activeEditor.getLang('timeslotButton.staffname')},
			{value: '[staff_email]', text: top.tinymce.activeEditor.getLang('timeslotButton.staffemail')},
			{value: '[staff_phone]', text: top.tinymce.activeEditor.getLang('timeslotButton.staffphone')},
		]
	}]
};

const staffSection = ts_vars.isstaff ? staffOption : null;

// Creates modal with text options to insert into email body
const insertTextModal = {
	title: top.tinymce.activeEditor.getLang('timeslotButton.tsoptions'),
	bodyType: 'tabpanel',
	height: 150,
	width: 500,
	body: [
		{
			title: top.tinymce.activeEditor.getLang('timeslotButton.appointment'),
			type: 'form',
			items: [{
				label: top.tinymce.activeEditor.getLang('timeslotButton.appointment'),
				name: 'apptdata',
				type: 'listbox',
				values: [
					{value: '', text: top.tinymce.activeEditor.getLang('timeslotButton.select')},
					{value:'[appt_date]', text: top.tinymce.activeEditor.getLang('timeslotButton.appointmentdate')},
					{value: '[appt_time]', text: top.tinymce.activeEditor.getLang('timeslotButton.appointmenttime'),}
				]
			}]
		},
		{
			title: top.tinymce.activeEditor.getLang('timeslotButton.client'),
			type: 'form',
			items: [{
				label: top.tinymce.activeEditor.getLang('timeslotButton.client'),
				name: 'clientdata',
				type: 'listbox',
				values: [
					{ value: '', text: top.tinymce.activeEditor.getLang('timeslotButton.select') },
					{ value: '[client_name]', text: top.tinymce.activeEditor.getLang('timeslotButton.clientname')},
					{ value: '[client_email]', text: top.tinymce.activeEditor.getLang('timeslotButton.clientemail')},
					{ value: '[client_phone]', text: top.tinymce.activeEditor.getLang('timeslotButton.clientphone')},
				]
			}]
		},
		{
			title: top.tinymce.activeEditor.getLang('timeslotButton.company'),
			type: 'form',
			items: [{
				label: top.tinymce.activeEditor.getLang('timeslotButton.company'),
				name: 'companydata',
				type: 'listbox',
				values: [
					{value: '', text: top.tinymce.activeEditor.getLang('timeslotButton.select')},
					{value: ts_vars.companyname, text: top.tinymce.activeEditor.getLang('timeslotButton.companyname')},
					{value: ts_vars.companywebsite, text: top.tinymce.activeEditor.getLang('timeslotButton.companywebsite')},
					{value: ts_vars.companyemail, text: top.tinymce.activeEditor.getLang('timeslotButton.companyemail')},
					{value: ts_vars.companyphone, text: top.tinymce.activeEditor.getLang('timeslotButton.companyphone')},
					{value: ts_vars.companyaddress, text: top.tinymce.activeEditor.getLang('timeslotButton.companyaddress')},
					{value: ts_vars.companylogo, text: top.tinymce.activeEditor.getLang('timeslotButton.companylogo')}
				]
			}]
		},
		{
			title: top.tinymce.activeEditor.getLang('timeslotButton.service'),
			type: 'form',
			items: [{
				label: top.tinymce.activeEditor.getLang('timeslotButton.service'),
				name: 'servicedata',
				type: 'listbox',
				values: [
					{value: '', text: top.tinymce.activeEditor.getLang('timeslotButton.select') },
					{value: '[service_name]', text: top.tinymce.activeEditor.getLang('timeslotButton.servicename')},
					{value: '[service_price]', text: top.tinymce.activeEditor.getLang('timeslotButton.serviceprice')}
				]
			}]
		},
		staffSection
	],
	buttons: [
		{
			onclick: 'close',
			text: top.tinymce.activeEditor.getLang('timeslotButton.cancel')
		},
		{
			onclick: 'submit',
			text: top.tinymce.activeEditor.getLang('timeslotButton.insert')
		}
	],
	onSubmit: function (api) {
		Object.values(api.data).forEach((element) => {
			if (element === '') {
				return;
			}

			top.tinymce.activeEditor.insertContent(element);
		})

		top.tinymce.activeEditor.windowManager.close();
	},
};

// Creates modal to send test email
const testEmailModal = {
	title: top.tinymce.activeEditor.getLang('timeslotButton.sendtest'),
	bodyType: 'panel',
	height: 80,
	width: 400,
	body: [{
		title: top.tinymce.activeEditor.getLang('timeslotButton.appointment'),
		type: 'form',
		items: [{
			label: top.tinymce.activeEditor.getLang('timeslotButton.emailrecipient'),
			name: 'testemailaddress',
			subtype: 'email',
			type: 'textbox',
		}]
	}],
	buttons: [
		{
			onclick: 'close',
			text: top.tinymce.activeEditor.getLang('timeslotButton.cancel')
		},
		{
			onclick: 'submit',
			text: top.tinymce.activeEditor.getLang('timeslotButton.send')
		}
	],
	onSubmit: function (api) {

		Object.values(api.data).forEach((element) => {

			if (element === '') {
				return;
			}

			jQuery.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'tslot_test_email',
					'testemail': element,
					'testemailBody': top.tinymce.activeEditor.getContent(),
				},
				success: () => {
					jQuery('.ts-success-msg').text(top.tinymce.activeEditor.getLang('timeslotButton.messagesent'));
					jQuery('body').addClass('timeslot-saving');
				},
				timeout: 5000,
				error: (e) => {
					console.error(top.tinymce.activeEditor.getLang('timeslotButton.emailtesterror'), e.statusText);
				}
			});

			setTimeout(() => {
				jQuery('body').removeClass('timeslot-saving');
				jQuery('.ts-success-msg').text(__('Settings Saved', 'timeslot'));
			}, 8000);

		})

		top.tinymce.activeEditor.windowManager.close();

	}
};

// Create insert text and test email buttons
tinymce.create('tinymce.plugins.timeslotButton', {
	init : function(ed, url) {
		ed.addButton( 'timeslot-btn', {
			classes: 'timeslot-btn',
			cmd: 'open_ts_dialog',
			image: ts_vars.tspluginurl + 'admin/images/ts-icon.svg',
			title : top.tinymce.activeEditor.getLang('timeslotButton.insertinfo'),
		});
		ed.addButton( 'test-email', {
			classes: 'timeslot-btn test-email-btn',
			cmd: 'open_ts_test_email_dialog',
			image: ts_vars.tspluginurl + 'admin/images/email-icon.svg',
			title : top.tinymce.activeEditor.getLang('timeslotButton.sendatest'),
		});
		ed.addCommand( 'open_ts_dialog', function() {
			ed.windowManager.open(insertTextModal)
		});
		ed.addCommand( 'open_ts_test_email_dialog', function() {
			ed.windowManager.open(testEmailModal)
		});
	},
	createControl : function(n, cm) {
		return null;
	},
});

// Add buttons to TinyMCE
tinymce.PluginManager.add('tslot_tinymce_btn', tinymce.plugins.timeslotButton);