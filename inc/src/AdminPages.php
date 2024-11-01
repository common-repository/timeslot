<?php
/**
 * Creates admin page content
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.1.8
 * 
 */

namespace TimeSlot;

class AdminPages {

	public $name;
	public $nav;
	public $title;
	public $link;
	public $aria;
	public $active_tab;
	public $init;

	public function __construct($name, $nav){

		$this -> name = $name;
		$this -> title = ucwords(__($this -> name, 'timeslot'));
		$this -> link = "timeslot-" . $this -> name;
		$this -> nav = $nav;
		$this -> aria = $this -> name . ' section menu';
		$this -> active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : $this -> name;
		$this -> init = 'tslot_' . $this -> active_tab . '_tab_init';

		self::tslot_create_header();
		self::tslot_check_timezone();
		$this -> tslot_create_nav();
		$this -> tslot_create_html();
	}

	public static function tslot_create_header(){
		?>

		<header class='timeslot-header'>

			<a href='<?php echo esc_url('https://timeslotplugins.com/') ?>' class='logo-link' target='_blank' aria-label='<?php esc_attr_e('Visit the Time Slot website', 'timeslot') ?>'>
				<img src='<?php echo esc_url(TIMESLOT_URL . 'admin/images/timeslot-logo.svg') ?>' height='35' width='200' alt='Time Slot'>
			</a>

			<nav>
				<a href='<?php echo esc_url('https://timeslotplugins.com/docs/') ?>' class='ts-nav-link' target='_blank'>
					<?php esc_html_e('Docs', 'timeslot') ?>
				</a>
				<a href='<?php echo esc_url('https://timeslotplugins.com/contact/') ?>' class='ts-nav-link' target='_blank'>
					<?php esc_html_e('Contact', 'timeslot') ?>
				</a>
				<a href='<?php echo esc_url('https://timeslotplugins.com/') ?>' class='ts-nav-link ts-highlight-link' target='_blank'>
					<?php esc_attr_e('Get Pro', 'timeslot') ?>
				</a>
			</nav>

		</header>
		<?php
	}

	public static function tslot_check_timezone(){

		$timezone_string = get_option( 'timezone_string' );

		if ($timezone_string !== '' && $timezone_string !== 'UTC') {
			return;
		}

		/* translators: Placeholder is URL */
		$timezone_help_string = __(' Please set your site timezone to a city name on the <a href="%s">WordPress General settings page</a>.', 'timeslot');
		$general_url = admin_url( 'options-general.php#WPLANG' );
		$link_kses = array('a' => array('href' => array()));
		?>

		<p class='ts-notice'>
			<?php echo sprintf( wp_kses( $timezone_help_string, $link_kses ), $general_url ); ?>
		</p>
		<?php
	}

	public function tslot_create_nav(){
		?>

		<nav class='ts-nav nav-tab-wrapper' aria-label='<?php esc_attr_e($this -> aria);?>'>
			<ul>
				<?php 
				foreach($this -> nav as $item){
	
					$class = $this -> active_tab === $item ? 'nav-tab-active' : '';
					$label = __(ucwords(str_replace('-', ' ', $item)), 'timeslot');
					$label =  $item === 'import-export' ? __('Import/Export', 'timeslot') : $label;
					$url = add_query_arg( array(
						'page' => $this -> link,
						'tab' => $item,
					));
	
					?>
					<li>
						<a href='<?php echo esc_url($url); ?>' class='nav-tab <?php esc_attr_e($class); ?>'><?php esc_html_e($label);?></a>
					</li>
					<?php
	
				}
				?>
			</ul>
		</nav>
	
		<?php
	}

	public function tslot_create_html(){
		switch($this -> active_tab){
			case 'services':
			case 'staff':
			case 'appointments':
			case 'customers':
			case 'payments':
			case 'coupons':
				$active_tab_init = 'tslot_' . $this -> active_tab . '_tab_init';
				$active_tab_init();
				break;
			case 'import-export':
				tslot_import_export_tab_init();
				break;
			default:
				$this -> tslot_settings_form();
				break;
		}
	}

	public function tslot_settings_form(){
		?>
		<form method='post' action='<?php echo esc_url('options.php'); ?>' id='<?php esc_attr_e('ts-' . $this -> active_tab . '-form');?>' class='ts-settings-form ts-load'>
			<?php
			$this -> tslot_settings_sections();
			submit_button(__('Save Changes', 'timeslot'), 'ts-btn');
			?>
		</form>
		<p class='ts-success-msg' role='alert'><?php esc_html_e('Settings Saved', 'timeslot')?></p>
		<?php
	}

	public function tslot_settings_sections(){
		if($this -> active_tab === 'business-hours'){
			settings_fields('timeslot-business-hours-group');
			self::tslot_do_settings_sections('timeslot-business-hours-tab');
		}
		else {
			settings_fields('timeslot-'. $this -> active_tab .'-tab');
			do_settings_sections('timeslot-'. $this -> active_tab .'-tab');
		}
	}

	public static function tslot_do_settings_sections($page){

		global $wp_settings_sections, $wp_settings_fields;

		if (!isset($wp_settings_sections[ $page ])) {
			return;
		}

		foreach ((array) $wp_settings_sections[$page] as $section) {

			if ($section['title']) {
				echo "<h2>" . esc_html($section['title']) . "</h2>\n";
			}
			if ($section['callback']) {
				call_user_func($section['callback'], $section);
			}
			if (!isset($wp_settings_fields) || !isset($wp_settings_fields[$page]) || !isset($wp_settings_fields[$page][$section['id']])) {
				continue;
			}

			echo '<div class="ts-settings-group">';
			do_settings_fields($page, $section['id']);
			echo '</div>';

		}
	}
}