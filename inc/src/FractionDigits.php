<?php
/**
 * Finds fraction digits for use in
 * input type number with decimal inputmode
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.7
 * 
 */

namespace TimeSlot;

use Money\Currency;
use Money\Currencies\ISOCurrencies;

class FractionDigits {

	public $step;
	public $subunit;

	public function __construct(){

		$payment_options = get_option('timeslot-payment-methods-tab');
		$currency_code = $payment_options['currency'];
		$iso = new ISOCurrencies();
		$this -> subunit = $iso->subunitFor(new Currency($currency_code));

	}

	public function get_step(){

		switch($this -> subunit){
			case 0:
				$this -> step = 1;
				break;
			case 2:
			default:
				$this -> step = 0.01;
				break;
		}

	}

}