<?php
/**
 * Replaces shortcodes in email body
 * 
 * For front end form, appointment table, and test
 * emails on tinymce. Builds ics file for customer
 * approved emails.
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.1.2
 * 
 */

namespace TimeSlot;

class EmailReplacements {

	public $company_name;
	public $company_email;
	public $company_address_1;
	public $company_address_2;
	public $company_city;
	public $company_state;
	public $company_zip;
	public $company_phone;
	public $company_website;
	public $company_logo;
	public $company_full_address;
	public $company_full_address_ics;
	public $date;
	public $time;
	public $format_date;
	public $format_time;
	public $customer_name;
	public $customer_phone;
	public $customer_email;
	public $service;
	public $service_i18n;
	public $service_price;
	public $staff_phone;
	public $staff_email;
	public $staff;
	public $body;
	public $message;
	public $site_timezone;
	public $service_end;
	public $full_start;
	public $headers;
	public $ics_content;

	// Set global vars and run replace
	public function __construct($vars){

		$this -> date =  $vars['date'];
		$this -> time =  $vars['time'];
		$this -> customer_name =  $vars['customer_name'];
		$this -> customer_phone =  $vars['customer_phone'];
		$this -> customer_email =  $vars['customer_email'];
		$this -> service =  $vars['service'];
		$this -> staff =  $vars['staff'];
		$this -> body =  $vars['body'];
		$this -> site_timezone = get_option('timezone_string');

		$this -> tslot_run_replace();
	}

	// Initialize email replacement
	public function tslot_run_replace(){

		$this -> tslot_company_options();

		$this -> tslot_company_address();

		$this -> tslot_get_staff_info();

		$this -> tslot_get_service_price();

		$this -> tslot_format_date_and_time();

		$this -> tslot_customer_info();

		$this -> tslot_set_email_headers();

		$this -> tslot_set_i18n_service_name();

		$this -> tslot_replace_vars();
	}

	// Get company settings options
	public function tslot_company_options(){

		//ics, replacements, test email, addresses
		$company_options = get_option('timeslot-company-tab');
		$this -> company_name = $company_options['company-name'] ?? '';
		$this -> company_email = $company_options['company-email'] ?? '';
		$this -> company_address_1 = $company_options['company-address-1'] ?? '';
		$this -> company_address_2 = $company_options['company-address-2'] ?? '';
		$this -> company_city = $company_options['company-city'] ?? '';
		$this -> company_state = $company_options['company-state'] ?? '';
		$this -> company_zip = $company_options['company-zip'] ?? '';
		$this -> company_phone = $company_options['company-phone'] ?? '';
		$this -> company_website = $company_options['company-website'] ?? '';
		$company_logo_id = $company_options['company-logo'] ?? '';
		$company_logo_url = wp_get_attachment_url( $company_logo_id );
		$this -> company_logo = '<img src="'.$company_logo_url.'" style="max-width:300px;height:auto;">';
	}

	// Set full address for replacement
	public function tslot_company_address(){

		$company_address_2_space = !empty($this -> company_address_2) ? ' ' : '';

		$company_full_address  = $this -> company_address_1;
		$company_full_address .= $company_address_2_space;
		$company_full_address .= $this -> company_address_2;
		$company_full_address .= ', ';
		$company_full_address .= $this -> company_city;
		$company_full_address .= ', ';
		$company_full_address .= $this -> company_state;
		$company_full_address .= ' ';
		$company_full_address .= $this -> company_zip;

		$this -> company_full_address = $company_full_address;
	}

	// Set full address for ics
	public function tslot_company_address_ics(){

		$company_address_2_ics_comma = !empty($company_address_2) ? '\, ' : '';
		$company_full_address_ics  = $this -> company_name;
		$company_full_address_ics .= '\, ';
		$company_full_address_ics .= $this -> company_address_1;
		$company_full_address_ics .= '\, ';
		$company_full_address_ics .= $this -> company_address_2;
		$company_full_address_ics .= $company_address_2_ics_comma;
		$company_full_address_ics .= $this -> company_city;
		$company_full_address_ics .= '\, ';
		$company_full_address_ics .= $this -> company_state;
		$company_full_address_ics .= ' ';
		$company_full_address_ics .= $this -> company_zip;

		$this -> company_full_address_ics = $company_full_address_ics;
	}

	// Get customer phone and email
	public function tslot_customer_info(){

		if(!empty($this -> customer_phone) && !empty($this -> customer_email)){
			return;
		}

		global $wpdb;
		$ts_customers_table = $wpdb->prefix . 'ts_customers';
		$customer_info = 
		$wpdb->get_row(
			$wpdb->prepare(
				"SELECT phone, email 
				FROM {$ts_customers_table} 
				WHERE customer_name = %s", 
				$this -> customer_name
		));

		$this -> customer_phone = $customer_info->phone;
		$this -> customer_email = $customer_info->email;
	}

	// Format start date and time
	public function tslot_format_date_and_time(){

		$wp_option_locale = get_locale();

		$date_obj = new \IntlDateFormatter(
			$wp_option_locale,
			\IntlDateFormatter::LONG,
			\IntlDateFormatter::NONE,
		);

		$time_obj = new \IntlDateFormatter(
			$wp_option_locale,
			\IntlDateFormatter::NONE,
			\IntlDateFormatter::SHORT,
		);

		if(empty($this -> date) && empty($this -> time)){
			$dateTime = new \DateTime();
			$this -> time = 'now';
		}
		else{
			$dateTime = new \DateTime($this -> date);
		}

		$this -> format_date = $date_obj->format($dateTime);
		$this -> format_time = $time_obj->format(strtotime($this -> time));
	}

	// Get staff phone and email
	public function tslot_get_staff_info(){

		$count_staff = new CountStaff();
		$is_staff = $count_staff -> is_staff();

		if(!$is_staff){
			$this -> staff = '';
			$this -> staff_email = '';
			$this -> staff_phone = '';
			return;
		}

		if(empty($this -> staff)){
			$this -> staff = 'John Doe';
			$this -> staff_email = 'staff@email.com';
			$this -> staff_phone = '222-222-2222';
			return;
		}

		global $wpdb;
		$ts_staff_table = $wpdb->prefix . 'ts_staff';
		$staff_info = 
			$wpdb->get_row(
			$wpdb->prepare(
				"SELECT phone, email 
				FROM {$ts_staff_table} 
				WHERE staff_name = %s", 
				$this -> staff
		));

		$this -> staff_email = $staff_info -> email;
		$this -> staff_phone = $staff_info -> phone;
	}

	// Get service end for ics
	public function tslot_get_service_end(){

		global $wpdb;
		$ts_services_table = $wpdb->prefix . 'ts_services';

		// I18n strings
		$i18n_strings = new MultilangStrings();
		$visible_i18n = $i18n_strings -> visible_string;

		$service_duration = 
		$wpdb->get_var(
		$wpdb->prepare(
			"SELECT duration 
			FROM {$ts_services_table} 
			WHERE visibility = %s 
			AND service_title = %s", 
			$visible_i18n, $this -> service
		));

		$this -> service_end = intval(strtotime($this -> full_start) + $service_duration);
	}

	// Get date and time for ics
	public function tslot_get_full_start(){

		$this -> full_start = $this -> date . 'T' . $this -> time;
	}

	// Get service price
	public function tslot_get_service_price(){

		if(empty($this -> service)){
			$this -> tslot_get_service_info();
		}

		$price = new PriceFromService($this -> service);
		$price -> get_price();
		$price_from_service = $price -> service_price;

		$curr = new I18nCurrency($price_from_service);
		$curr -> to_display();
		$this -> service_price = $curr->price;
	}

	// Get service for test email
	public function tslot_get_service_info(){

		global $wpdb;
		$ts_services_table = $wpdb->prefix . 'ts_services';
		$services_info = 
			$wpdb->get_row(
				"SELECT service_title
				FROM {$ts_services_table}"
		);

		$this -> service = $services_info -> service_title;
	}

	// Sets email headers
	public function tslot_set_email_headers(){

		$email_options = get_option('timeslot-email-tab');
		$sender_name = $email_options['email-sender-name'];
		$sender_email = $email_options['email-sender-email'];

		$this -> headers = array(
			"Content-Type: text/html; charset=UTF-8",
			"From: $sender_name <$sender_email>",
			"Reply-To: $sender_name <$sender_email>"
		);
	}

	// Set service name for i18n emails
	public function tslot_set_i18n_service_name(){

		$i18n_strings = new MultilangStrings();
		$i18n_strings -> set_i18n_service($this -> service);
		$this -> service_i18n = $i18n_strings -> service_string;
	}

	// Replace shortcodes in email body
	public function tslot_replace_vars(){

		$ts_replace = array(
			'[appt_date]',
			'[appt_time]',
			'[client_name]',
			'[client_phone]',
			'[client_email]',
			'[service_name]',
			'[service_price]',
			'[staff_name]',
			'[staff_email]',
			'[staff_phone]',
			'[company_name]',
			'[company_website]',
			'[company_email]',
			'[company_phone]',
			'[company_full_address]',
			'[company_logo]'
		);

		$ts_replace_with = array(
			$this -> format_date,
			$this -> format_time,
			$this -> customer_name,
			'<a href="tel:' . $this -> customer_phone . '">' . $this -> customer_phone . '</a>',
			'<a href="mailto:' . $this -> customer_email . '">' . $this -> customer_email . '</a>',
			$this -> service_i18n,
			$this -> service_price,
			$this -> staff,
			'<a href="mailto:' . $this -> staff_email . '">' . $this -> staff_email .'</a>',
			'<a href="tel:' . $this -> staff_phone . '">' . $this -> staff_phone . '</a>',
			$this -> company_name,
			'<a href="' . $this -> company_website . '">' . $this -> company_website . '</a>',
			'<a href="mailto:' . $this -> company_email . '">' . $this -> company_email . '</a>',
			'<a href="tel:' . $this -> company_phone . '">' . $this -> company_phone . '</a>',
			$this -> company_full_address,
			$this -> company_logo,
		);

		$email_allowed = new EmailAllowed();
		$email_allowed = $email_allowed -> kses;
		$this -> message = str_replace($ts_replace, $ts_replace_with, $this -> body);
		$this -> message = wp_kses($this -> message, $email_allowed);
		return $this -> message;
	}

	// Builds ics file
	function tslot_ics_setup(){

		$this -> tslot_get_full_start();
		$this -> tslot_get_service_end();

		function tslot_format_timestamp($timestamp) {
			$dt = date( 'Ymd\THis', strtotime( $timestamp ) );
			return $dt;
		}

		$ics_content ="BEGIN:VCALENDAR
		PRODID:-//Time Slot//Time Slot Plugin//EN
		VERSION:2.0
		CALSCALE:GREGORIAN
		METHOD:REQUEST
		BEGIN:VEVENT
		DTSTART;TZID=".$this -> site_timezone.":".tslot_format_timestamp($this -> full_start)."
		DTEND;TZID=".$this -> site_timezone.":".date( 'Ymd\THis', $this -> service_end)."
		DTSTAMP:" . tslot_format_timestamp('now') . "Z
		UID:".time()."@timeslot
		ORGANIZER;CN=".$this -> company_name.":mailto:".$this -> company_email."
		ATTENDEE;CN=".$this -> customer_name.":mailto:".$this -> customer_email."
		DESCRIPTION:".$this -> service_i18n."
		LOCATION:".$this -> company_full_address_ics."
		SUMMARY:".$this -> company_name."
		END:VEVENT
		END:VCALENDAR";

		$this -> ics_content = trim(preg_replace('/\t/', '', $ics_content));
		add_action('phpmailer_init', array($this, 'tslot_set_phpmailer_atts'));
	}

	// Sets email plain text body and attaches ics
	function tslot_set_phpmailer_atts($phpmailer) {
		$phpmailer -> AltBody = strip_tags($phpmailer->Body);
		$phpmailer -> addStringAttachment($this -> ics_content,'ical.ics','base64','text/calendar');
	}

	// Clears ics attachment and altBody from phpmailer
	public function tslot_clear_ics(){
		remove_action( 'phpmailer_init', array($this, 'tslot_set_phpmailer_atts') );
	}
}