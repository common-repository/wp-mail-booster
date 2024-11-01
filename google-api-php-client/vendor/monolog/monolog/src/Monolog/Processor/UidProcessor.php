<?php //@codingStandardsIgnoreLine
/**
 * This file is UidProcessor.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

namespace Monolog\Processor;

/**
 * Adds a unique identifier into records
 */
class UidProcessor {
	/**
	 * Version of this plugin.
	 *
	 * @access private
	 * @var    $uid
	 */
	private $uid;
	/**
	 * This function is __construct.
	 *
	 * @param int $length passes parameter as length.
	 * @throws \InvalidArgumentException On error.
	 */
	public function __construct( $length = 7 ) {
		if ( ! is_int( $length ) || $length > 32 || $length < 1 ) {
			throw new \InvalidArgumentException( 'The uid length must be an integer between 1 and 32' );
		}

		$this->uid = substr( hash( 'md5', uniqid( '', true ) ), 0, $length );
	}
	/**
	 * This function is __invoke.
	 *
	 * @param array $record passes parameter as record.
	 */
	public function __invoke( array $record ) {
		$record['extra']['uid'] = $this->uid;

		return $record;
	}

	/**
	 * This function is getuid.
	 *
	 * @return string
	 */
	public function getUid() {// @codingStandardsIgnoreLine
		return $this->uid;
	}
}
