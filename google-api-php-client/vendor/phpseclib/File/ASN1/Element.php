<?php // @codingStandardsIgnoreLine
/**
 * This file for raw element.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

/**
 * Pure-PHP ASN.1 Parser
 *
 * PHP version 5
 */

namespace phpseclib\File\ASN1;

/**
 * ASN.1 Element
 *
 * Bypass normal encoding rules in phpseclib\File\ASN1::encodeDER()
 *
 * @access  public
 */
class Element {

	/**
	 * Raw element value
	 *
	 * @var string
	 * @access private
	 */
	var $element; // @codingStandardsIgnoreLine

	/**
	 * Constructor
	 *
	 * @param string $encoded .
	 * @return \phpseclib\File\ASN1\Element
	 * @access public
	 */
	public function __construct( $encoded ) {
		$this->element = $encoded;
	}
}
