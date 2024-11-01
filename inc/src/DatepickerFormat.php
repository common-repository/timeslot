<?php
/*
 * Matches each symbol of PHP date format standard
 * with jQuery equivalent codeword
 * @author Tristan Jahier
 */

namespace TimeSlot;

class DatepickerFormat {

	public $php_format;

	public function __construct($php_format){
		$this->php_format = $php_format;
	}

	public function get_format(){

		$jqueryui_format = "";
		$escaping = false;
		$symbols = array(
			// Day
			'd' => 'dd',
			'D' => 'D',
			'j' => 'd',
			'l' => 'DD',
			'N' => '',
			'S' => '',
			'w' => '',
			'z' => 'o',
			// Week
			'W' => '',
			// Month
			'F' => 'MM',
			'm' => 'mm',
			'M' => 'M',
			'n' => 'm',
			't' => '',
			// Year
			'L' => '',
			'o' => '',
			'Y' => 'yy',
			'y' => 'y',
			// Time
			'a' => '',
			'A' => '',
			'B' => '',
			'g' => '',
			'G' => '',
			'h' => '',
			'H' => '',
			'i' => '',
			's' => '',
			'u' => ''
		);

		for($i = 0; $i < strlen($this->php_format); $i++){

			$char = $this->php_format[$i];

			// PHP date format escaping character
			if($char === '\\'){ 
				$i++;
				if($escaping) {
					$jqueryui_format .= $this->php_format[$i];
				}
				else {
					$jqueryui_format .= '\'' . $this->php_format[$i];
					$escaping = true;
				}
			}

			else {
				if($escaping) {
					$jqueryui_format .= "'";
					$escaping = false;
				}
				if(isset($symbols[$char])) {
					$jqueryui_format .= $symbols[$char];
				}
				else {
					$jqueryui_format .= $char;
				}
			}

		}

		$this->php_format = $jqueryui_format;
		return $this->php_format;

	}

}