<?php // @codingStandardsIgnoreLine
/**
 * This file is part of the Monolog package.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

/*
 * This file is part of the Monolog package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Processor;

use Monolog\Logger;

/**
 * Injects Git branch and Git commit SHA in all records
 */
class GitProcessor {
	/**
	 * Version of this plugin
	 *
	 * @access private
	 * @var string $level
	 */
	private $level;
	/**
	 * Version of this plugin
	 *
	 * @access private
	 * @var string $cache
	 */
	private static $cache;
	/**
	 * This function is __construct
	 *
	 * @param string $level passes parameter as level.
	 */
	public function __construct( $level = Logger::DEBUG ) {
		$this->level = Logger::toMonologLevel( $level );
	}

	/**
	 * This function is __invoke.
	 *
	 * @param  array $record passes parameter as record.
	 * @return array
	 */
	public function __invoke( array $record ) {
		// return if the level is not high enough.
		if ( $record['level'] < $this->level ) {
			return $record;
		}

		$record['extra']['git'] = self::getGitInfo();

		return $record;
	}
	/**
	 * This function is getGitInfo.
	 */
	private static function getGitInfo() {// @codingStandardsIgnoreLine
		if ( self::$cache ) {
			return self::$cache;
		}

		$branches = `git branch -v --no-abbrev`;// @codingStandardsIgnoreLine
		if ( preg_match( '{^\* (.+?)\s+([a-f0-9]{40})(?:\s|$)}m', $branches, $matches ) ) {
			return self::$cache = array(// @codingStandardsIgnoreLine
				'branch' => $matches[1],
				'commit' => $matches[2],
			);
		}

		return self::$cache = array();// @codingStandardsIgnoreLine
	}
}
