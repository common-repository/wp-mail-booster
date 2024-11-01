<?php // @codingStandardsIgnoreLine.
/**
 * This file name as nullhandler.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Handler;

use Monolog\Logger;

/**
 * Blackhole
 *
 * Any record it can handle will be thrown away. This can be used
 * to put on top of an existing stack to override it temporarily.
 */
class NullHandler extends AbstractHandler {

	/**
	 * Public construtor
	 *
	 * @param int $level The minimum logging level at which this handler will be triggered .
	 */
	public function __construct( $level = Logger::DEBUG ) {
		parent::__construct( $level, false );
	}

	/**
	 * Function to handle
	 *
	 * @param array $record .
	 */
	public function handle( array $record ) {
		if ( $record['level'] < $this->level ) {
			return false;
		}

		return true;
	}
}
