<?php
/**
 * Sets allowable html elements for emails
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */

namespace TimeSlot;

class EmailAllowed {

	public $kses;

	public function __construct(){

		$this -> kses = array(
			'a' => array(
					'href' => array(),
					'target' => array(),
					'class' => array(),
					'id' => array(),
					'style' => array(),
			),

			'b' => array(
					'class' => array(),
					'id' => array(),
					'style' => array(),
			),

			'body' => array(),

			'br' => array(
					'class' => array(),
					'id' => array(),
					'style' => array(),
			),

			'div' => array(
					'align' => array(),
					'class' => array(),
					'dir' => array(),
					'id' => array(),
					'style' => array(),
			),

			'font' => array(
					'color' => array(),
					'face' => array(),
					'size' => array(),
					'class' => array(),
					'id' => array(),
					'style' => array(),
			),

			'footer' => array(
					'align' => array(),
					'class' => array(),
					'id' => array(),
					'style' => array(),
			),

			'html' => array(),

			'h1' => array(
					'align' => array(),
					'class' => array(),
					'dir' => array(),
					'id' => array(),
					'style' => array(),
			),

			'h2' => array(
					'align' => array(),
					'class' => array(),
					'dir' => array(),
					'id' => array(),
					'style' => array(),
			),

			'h3' => array(
					'align' => array(),
					'class' => array(),
					'dir' => array(),
					'id' => array(),
					'style' => array(),
			),

			'h4' => array(
					'align' => array(),
					'class' => array(),
					'dir' => array(),
					'id' => array(),
					'style' => array(),
			),

			'h5' => array(
					'align' => array(),
					'class' => array(),
					'dir' => array(),
					'id' => array(),
					'style' => array(),
			),

			'h6' => array(
					'align' => array(),
					'class' => array(),
					'dir' => array(),
					'id' => array(),
					'style' => array(),
			),

			'header' => array(
					'align' => array(),
					'class' => array(),
					'id' => array(),
					'style' => array(),
			),

			'hr' => array(
					'align' => array(),
					'size' => array(),
					'width' => array(),
					'style' => array(),
			),

			'i' => array(
					'class' => array(),
					'id' => array(),
					'style' => array(),
			),

			'img' => array(
					'alt' => array(),
					'align' => array(),
					'border' => array(),
					'height' => array(),
					'hspace' => array(),
					'vspace' => array(),
					'src' => array(),
					'usemap' => array(),
					'width' => array(),
					'class' => array(),
					'id' => array(),
					'style' => array(),
			),

			'li' => array(
					'class' => array(),
					'dir' => array(),
					'id' => array(),
					'style' => array(),
					'type' => array(),
			),

			'label' => array(
					'class' => array(),
					'id' => array(),
					'style' => array(),
			),

			'p' => array(
					'class' => array(),
					'dir' => array(),
					'id' => array(),
					'style' => array(),
					'align' => array(),
			),

			'span' => array(
					'class' => array(),
					'id' => array(),
					'style' => array(),
			),

			'strong' => array(
					'class' => array(),
					'id' => array(),
					'style' => array(),
			),

			'table' => array(
					'align' => array(),
					'bgcolor' => array(),
					'border' => array(),
					'cellpadding' => array(),
					'cellspacing' => array(),
					'rules' => array(),
					'width' => array(),
					'class' => array(),
					'dir' => array(),
					'id' => array(),
					'style' => array(),
			),

			'tbody' => array(
					'align' => array(),
					'valign' => array(),
					'class' => array(),
					'dir' => array(),
					'id' => array(),
					'style' => array(),
			),

			'td' => array(
					'abbr' => array(),
					'align' => array(),
					'bgcolor' => array(),
					'colspan' => array(),
					'height' => array(),
					'rowspan' => array(),
					'scope' => array(),
					'valign' => array(),
					'width' => array(),
					'class' => array(),
					'dir' => array(),
					'id' => array(),
					'lang' => array(),
					'style' => array(),
			),

			'tfoot' => array(
					'align' => array(),
					'valign' => array(),
					'class' => array(),
					'dir' => array(),
					'id' => array(),
					'style' => array(),
			),

			'th' => array(
					'abbr' => array(),
					'align' => array(),
					'bgcolor' => array(),
					'colspan' => array(),
					'height' => array(),
					'rowspan' => array(),
					'scope' => array(),
					'valign' => array(),
					'width' => array(),
					'class' => array(),
					'dir' => array(),
					'id' => array(),
					'style' => array(),
			),

			'thead' => array(
					'align' => array(),
					'valign' => array(),
					'class' => array(),
					'dir' => array(),
					'id' => array(),
					'style' => array(),
			),

			'title' => array(
					'class' => array(),
					'id' => array(),
					'style' => array(),
			),

			'tr' => array(
					'align' => array(),
					'bgcolor' => array(),
					'valign' => array(),
					'class' => array(),
					'dir' => array(),
					'id' => array(),
					'style' => array(),
			),

			'u' => array(
					'class' => array(),
					'id' => array(),
					'style' => array(),
			),

			'ul' => array(
					'class' => array(),
					'dir' => array(),
					'id' => array(),
					'style' => array(),
			),

			'ol' => array(
					'class' => array(),
					'dir' => array(),
					'id' => array(),
					'style' => array(),
					'type' => array(),
			),
		);
	}
}