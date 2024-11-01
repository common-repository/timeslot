<?php
/**
 * Builds admin datatables and modals
 *
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */

function tslot_build_datatable($headers, $title){

	global $tab;
	$tab = sanitize_text_field($_GET['tab']);
	// translators: %s is a placeholder for the page title
	$add_i18n = sprintf(__('Add %s', 'timeslot'), $title);
	// translators: %s is a placeholder for the page title
	$delete_i18n = sprintf(__('Delete %s', 'timeslot'), $title);
	?>

	<div class='ts-datatable-wrapper'>
		<table id='<?php esc_attr_e('ts-datatable--' . $tab )?>' class='display ts-datatable nowrap'>
			<caption><?php esc_html_e($title);?></caption>

			<thead>
				<tr role='row'>
					<?php
					foreach($headers as $header){
						?>
						<th><?php esc_html_e($header, 'timeslot');?></th>
						<?php
					}
					if($tab !== 'payments'){
					?>
					<th class='no-select'>
						<button class='ts-btn ts-add' title='<?php esc_attr_e($add_i18n) ?>' aria-label='<?php esc_attr_e($add_i18n) ?>'>
							<i class='dashicons dashicons-plus' aria-hidden='true'></i>
							<?php esc_html_e('Add', 'timeslot');?>
						</button>
					</th>
					<?php
					}
					?>
					<th>
						<input type='checkbox' class='ts-select-all' name='select_all' autocomplete='off'  aria-label='<?php esc_html_e('Select all rows', 'timeslot');?>'/>
					</th>
				</tr>
			</thead>

			<tbody></tbody>

			<tfoot>
				<tr>

					<?php
					switch($tab){
						case 'payments':
							$i = 0;
							break;
						default:
							$i = -1;
							break;
					}

					for($i; $i < count($headers); $i++){
						echo '<td></td>';
					}
					?>

					<td>
						<button class='ts-btn ts-delete-multi' title='<?php esc_attr_e($delete_i18n) ?>' aria-label='<?php esc_attr_e($delete_i18n) ?>' disabled>
							<i class='dashicons dashicons-trash' aria-hidden='true'></i>
							<span><?php esc_html_e('Delete', 'timeslot');?></span>
						</button>
					</td>
				</tr>
			</tfoot>

		</table>
	</div>
	<?php

}

function tslot_build_confirm_modal(){

	global $tab;

	?>
	<div id='<?php esc_attr_e('ts-modal-confirm--' . $tab) ?>' class='modal micromodal ts-modal ts-modal-confirm'>
		<div class='modal-dialog ts-modal-dialog modal-dialog-centered' tabindex='-1' data-micromodal-close>
			<div class='ts-modal-content' role='dialog' aria-modal='true'  aria-label='<?php esc_attr_e(__('Confirm row removal', 'timeslot'))?>' aria-describedby='<?php esc_attr_e('ts-confirm-msg--' . $tab)?>'>

				<div class='ts-modal-body'>
					<span id='<?php esc_attr_e('ts-confirm-msg--' . $tab);?>' class='ts-confirm-msg'></span>
				</div>

				<div class='ts-modal-footer'>
					<button type='button' class='ts-btn ts-close' data-micromodal-close aria-label='<?php esc_html_e('No, close dialog', 'timeslot');?>'><?php esc_html_e('No', 'timeslot');?></button>
					<button type='submit' class='ts-btn ts-submit ts-submit-confirm' aria-label='<?php esc_attr_e(__('Yes, delete row', 'timeslot'));?>'><?php esc_html_e('Yes', 'timeslot');?></button>
				</div>

			</div>
		</div>
	</div>
	<?php

}

function tslot_get_atts($atts){

	$all_atts = '';

	foreach ($atts as $att => $val){
		$all_atts .= $att . '="' . esc_attr($val) . '" ';
	}

	return $all_atts;

}

function tslot_build_edit_fields($edit_fields){

	foreach($edit_fields['fields'] as $field){

		$type = $field['type'];
		$width = 'ts-width-' . $field['width'];
		$name = $field['name'];
		$id = $field['id'] ?? 'ts-' . $name;
		$label = $field['label'] ?? ucwords(str_replace('-',' ',$name));
		$atts = isset($field['atts']) ? tslot_get_atts($field['atts']) : '';
		$options = $field['options'] ?? '';

		if ($type !== 'duration'){
			?>
			<div class='ts-form-col <?php esc_attr_e($width); ?>'>
			<label for='<?php esc_attr_e($id); ?>'><?php esc_html_e($label, 'timeslot');?></label>
			<?php
		}

		switch ($type){
			case 'text':
			case 'tel':
			case 'email':
			default: ?>
				<input type='<?php esc_attr_e($type); ?>' id='<?php esc_attr_e($id); ?>' class='ts-input' name='<?php esc_attr_e($name); ?>' <?php echo $atts ?>></input>
				<?php
				break;

			case 'multiple': ?>
				<select id='<?php esc_attr_e($id); ?>' class='ts-input' name='<?php esc_attr_e($name . '[]'); ?>' multiple='multiple' tabindex='0'>
					<?php
					foreach($options as $option){
						?>
						<option value='<?php esc_attr_e($option); ?>'><?php esc_html_e($option, 'timeslot'); ?></option>
						<?php
					}
					?>
				</select>
				<?php
				break;

			case 'select': ?>
				<select id='<?php esc_attr_e($id); ?>' class='ts-input' name='<?php esc_attr_e($name); ?>' <?php echo $atts ?>>
					<option></option>
					<?php
					foreach($options as $option){
						?>
						<option value='<?php esc_attr_e($option); ?>'><?php esc_html_e($option, 'timeslot'); ?></option>
						<?php
					}
					?>
				</select>
				<?php
				break;

			case 'textarea': ?>
				<textarea id='<?php esc_attr_e($id); ?>' class='ts-input' name='<?php esc_attr_e($name); ?>'></textarea>
				<?php
				break;

			case 'number': ?>
				<input type='<?php esc_attr_e($type); ?>' id='<?php esc_attr_e($id); ?>' class='ts-input' name='<?php esc_attr_e($name); ?>' <?php echo $atts ?> >
				</input>
				<?php
				break;

			case 'duration': ?>
				<fieldset id='<?php esc_attr_e('ts-' . $name . '-group');?>' class='ts-form-col ts-width-50'>
					<legend><?php esc_html_e($label, 'timeslot');?></legend>
					<input type='text' id='<?php esc_attr_e($id); ?>' class='ts-input ts-duration' name='<?php esc_attr_e($name); ?>'></input>
				</fieldset>
				<?php
				break;
		}

		if ($type !== 'duration'){
			?>
			</div>
			<?php
		}
	}
}

function tslot_build_edit_modal($edit_fields){

	$ts_pg_title = $edit_fields['title'];
	$ts_pg_name = $edit_fields['name'];
	$details_i18n = __('Details', 'timeslot');
	//translators: %s is a placeholder for the page title
	$edit_i18n = sprintf(__('Edit %s details', 'timeslot'), $ts_pg_title);
	?>

	<div id='<?php esc_attr_e('ts-modal-edit--' . $ts_pg_name); ?>' class='modal micromodal ts-modal ts-modal-edit' aria-hidden='true' data-micromodal-close>
		<div class='modal-dialog ts-modal-dialog modal-dialog-centered' tabindex='-1'>
			<div class='ts-modal-content' role='dialog' aria-labelledby='<?php esc_attr_e('ts-modal-title--' . $ts_pg_name); ?>' aria-modal='true'>

				<div class='ts-modal-header'>
					<h5 id='<?php esc_attr_e('ts-modal-title--' . $ts_pg_name); ?>' class='ts-modal-title'><?php esc_html_e(ucwords($ts_pg_title) . ' ' . $details_i18n, 'timeslot')?></h5>
					<button type='button' class='ts-btn ts-close' data-micromodal-close aria-label='<?php esc_attr_e('Close', 'timeslot');?>'><span class="dashicons dashicons-no-alt" aria-hidden="true"></span></button>
				</div>

				<div class='ts-modal-body'>

					<form method='post' id='<?php esc_attr_e('ts-modal-form--' . $ts_pg_name); ?>' class='ts-modal-form' aria-label='<?php esc_attr_e($edit_i18n)?>'>
						<div class='ts-hidden-id'>
							<input class='ts-row-id ts-input' disabled></input>
						</div>
						<div class='ts-form-row'>
							<?php
							tslot_build_edit_fields($edit_fields);
							?>
						</div>
					</form>
				</div>

				<div class='ts-modal-footer'>
					<button type='button' class='ts-btn ts-close' data-micromodal-close aria-label='<?php esc_attr_e('Close', 'timeslot');?>'><?php esc_html_e('Close', 'timeslot');?></button>
					<button type='submit' class='ts-btn ts-submit ts-submit-edit' aria-label='<?php esc_attr_e('Save Changes', 'timeslot');?>'><?php esc_html_e('Save Changes', 'timeslot');?></button>
				</div>
			</div>
		</div>
	</div>
	<?php
}