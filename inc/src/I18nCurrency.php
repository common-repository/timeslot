<?php
/**
 * Formats international currency
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.6
 * 
 */

namespace TimeSlot;

use Money\Currency;
use Money\Currencies\ISOCurrencies;
use Money\Parser\DecimalMoneyParser;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;

class I18nCurrency {

	public $price;

	public function __construct($price){

		$payment_options = get_option('timeslot-payment-methods-tab');
		$this -> currency_code = $payment_options['currency'];
		$this -> iso = new ISOCurrencies();
		$this -> price = $price;
		$this -> locale = get_locale();

	}

	public function to_min_unit(){

		$moneyParser = new DecimalMoneyParser($this -> iso);
		$money = $moneyParser->parse($this -> price, new Currency($this -> currency_code));
		$this -> price = $money->getAmount();

	}

	public function to_decimal(){

		$money = new Money($this -> price, new Currency($this -> currency_code));
		$moneyFormatter = new DecimalMoneyFormatter($this -> iso);
		$this -> price = $moneyFormatter->format($money);

	}

	public function to_display(){

		$money = new Money($this -> price, new Currency($this -> currency_code));
		$numberFormatter = new \NumberFormatter($this -> locale, \NumberFormatter::CURRENCY);
		$moneyFormatter = new IntlMoneyFormatter($numberFormatter, $this -> iso);
		$this -> price = $moneyFormatter->format($money);

	}

}