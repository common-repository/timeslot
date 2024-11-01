<?php
/**
 * Creates main admin page
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

// Sets up link groups
$dashboard_groups = array(
	'appts'=> array(
		'nickname' => 'appts',
		'header' => __('Appointments', 'timeslot'),
		'page' => 'timeslot-appointments',
		'links' => array(
			'appointments' => __('View or edit your appointments', 'timeslot'),
			'customers' => __('View or edit your customers', 'timeslot'),
			'payments' => __('View or edit your payments', 'timeslot'),
			'coupons' => __('Coupon codes to use at checkout', 'timeslot')
		)),
	'biz' => array(
		'nickname' => 'biz',
		'page' => 'timeslot-business',
		'header' => __('Business Settings', 'timeslot'),
		'links' => array(
			'services' => __('Services you offer', 'timeslot'),
			'staff' => __('Available team members', 'timeslot'),
			'email' => __('Notifications for staff and customers', 'timeslot'),
			'company' => __('Location and contact info', 'timeslot'),
			'business-hours' => __('Hours and days off', 'timeslot')
		)),
	'general' => array(
		'nickname' => 'general',
		'header' => __('General Settings', 'timeslot'),
		'page' => 'timeslot-general',
		'links' => array(
			'general' => __('Miscellaneous settings', 'timeslot'),
			'payment-methods' => __('Set up your payment currency', 'timeslot'),
			'appearance' => __('Styles for the booking form', 'timeslot'),
			'import-export' => __('Import or export plugin settings', 'timeslot')
	)),
	'setup' => array(
		'nickname' => 'setup',
		'header' => __('Set Up Guide', 'timeslot'),
	)
);

// Creates admin page header
TimeSlot\AdminPages::tslot_create_header();
TimeSlot\AdminPages::tslot_check_timezone();

// Displays links
?>
<div class='ts-dashboard'>
	<?php
	foreach($dashboard_groups as $group){

		$class = 'ts-dash-group-' .  $group['nickname'];
		$hdr_label = 'ts-dash-hdr-' . $group['nickname'];
		$hdr = $group['header'];

		?>
		<div class='ts-dashboard-group <?php esc_attr_e($class);?>' aria-labelledby='<?php esc_attr_e($hdr_label);?>'>
			<h2 id='<?php esc_attr_e($hdr_label);?>'><?php esc_html_e($hdr);?></h2>
			<?php

			if ($group['nickname'] == 'setup'){

				/* translators: Placeholder is URL https://timeslotplugins.com/docs/ */
				$guide_string = __('<p>Copy and paste this shortcode into your content to show the Time Slot booking form. For more information, visit the <a href="%s" target="_blank">Time Slot documentation</a>.</p>', 'timeslot');
				$guide_kses = array('a' => array('href' => array(),'target' => array()),'p' => array());
				$guide_docs_url = esc_url('https://timeslotplugins.com/docs/');
				echo sprintf( wp_kses( $guide_string, $guide_kses ), $guide_docs_url );
				?>

				</br>
				<p><b><?php esc_html_e('Shortcode:', 'timeslot');?></b> <span class='ts-copytext' data-copytext='[timeslot-form]'>[timeslot-form]</span></p>

				<?php
				continue;
			}

			foreach($group['links'] as $href => $details){

				$link_label_id = 'link-label-' . $href;
				$link_label = __(ucwords(str_replace('-', ' ', $href)), 'timeslot');
				if ($href === 'import-export'){
					$link_label = __(str_replace(' ', '/', $link_label), 'timeslot');
				}
				$link_desc = 'link-desc-' . $href;
				$url = add_query_arg( array(
					'page' => $group['page'],
					'tab' => $href,
				));

				?>
				<a href='<?php echo esc_url($url);?>' aria-labelledby='<?php esc_attr_e($link_label_id);?>' aria-describedby='<?php esc_attr_e($link_desc);?>'>
					<strong id='<?php esc_attr_e($link_label_id);?>'><?php esc_html_e($link_label);?></strong>
					<span class='ts-display-flex'>
						<p id='<?php esc_attr_e($link_desc);?>'><?php esc_html_e($details);?></p>
						<span class='dashicons dashicons-arrow-right-alt2'></span>
					</span>
				</a>
				<?php
			}
			?>
		</div>
		<?php
	}
	?>
</div>