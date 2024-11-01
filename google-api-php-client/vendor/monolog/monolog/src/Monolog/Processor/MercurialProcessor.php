<?php //@codingStandardsIgnoreLine
/**
 * This file is MercurialProcessor.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

namespace Monolog\Processor;

use Monolog\Logger;

/**
 * Injects Hg branch and Hg revision number in all records
 */
class MercurialProcessor {
	/**
	 * Version of this plugin
	 *
	 * @access private
	 * @var    $level.
	 */
	private $level;
	/**
	 * Version of this plugin
	 *
	 * @access private
	 * @var    $cache.
	 */
	private static $cache;
	/**
	 * This function is __construct.
	 *
	 * @param string $level passes parameter as level.
	 */
	public function __construct( $level = Logger::DEBUG ) {
		$this->level = Logger::toMonologLevel( $level );
	}

	/**
	 * This function is __invoke.
	 *
	 * @param  array $record passes Parameter as record.
	 * @return array
	 */
	public function __invoke( array $record ) {
		// return if the level is not high enough.
		if ( $record['level'] < $this->level ) {
			return $record;
		}

		$record['extra']['hg'] = self::getMercurialInfo();

		return $record;
	}
	/**
	 * This function is getMercurialInfo.
	 */
	private static function getMercurialInfo() {// @codingStandardsIgnoreLine
		if ( self::$cache ) {
			return self::$cache;
		}

		$result = explode( ' ', trim( `hg id -nb` ) ); // @codingStandardsIgnoreLine
		if ( count( $result ) >= 3 ) {
			return self::$cache = array(// @codingStandardsIgnoreLine
				'branch'   => $result[1],
				'revision' => $result[2],
			);
		}

		return self::$cache = array();// @codingStandardsIgnoreLine
	}
}
