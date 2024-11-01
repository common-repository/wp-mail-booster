<?php // @codingStandardsIgnoreLine.
/**
 * This file to send error to rollbar
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

use RollbarNotifier;
use Exception;
use Monolog\Logger;

/**
 * Sends errors to Rollbar
 *
 * If the context data contains a `payload` key, that is used as an array
 * of payload options to RollbarNotifier's report_message/report_exception methods.
 *
 * Rollbar's context info will contain the context + extra keys from the log record
 * merged, and then on top of that a few keys:
 *
 *  - level (rollbar level name)
 *  - monolog_level (monolog level name, raw level, as rollbar only has 5 but monolog 8)
 *  - channel
 *  - datetime (unix timestamp)
 */
class RollbarHandler extends AbstractProcessingHandler {

	/**
	 * Rollbar notifier
	 *
	 * @var RollbarNotifier
	 */
	protected $rollbarNotifier; // @codingStandardsIgnoreLine.
	/**
	 * Level map
	 *
	 * @var $levelMap
	 */
	protected $levelMap = array( // @codingStandardsIgnoreLine.
		Logger::DEBUG     => 'debug',
		Logger::INFO      => 'info',
		Logger::NOTICE    => 'info',
		Logger::WARNING   => 'warning',
		Logger::ERROR     => 'error',
		Logger::CRITICAL  => 'critical',
		Logger::ALERT     => 'critical',
		Logger::EMERGENCY => 'critical',
	);

	/**
	 * Records whether any log records have been added since the last flush of the rollbar notifier
	 *
	 * @var bool
	 */
	private $hasRecords = false; // @codingStandardsIgnoreLine.
	/**
	 * Variable to initialize roll bar
	 *
	 * @var bool
	 */
	protected $initialized = false;

	/**
	 * Public constructor
	 *
	 * @param RollbarNotifier $rollbarNotifier RollbarNotifier object constructed with valid token .
	 * @param int             $level           The minimum logging level at which this handler will be triggered .
	 * @param bool            $bubble          Whether the messages that are handled can bubble up the stack or not .
	 */
	public function __construct( RollbarNotifier $rollbarNotifier, $level = Logger::ERROR, $bubble = true ) { // @codingStandardsIgnoreLine.
		$this->rollbarNotifier = $rollbarNotifier; // @codingStandardsIgnoreLine.
		parent::__construct( $level, $bubble );
	}

	/**
	 * Function for write
	 *
	 * @param array $record .
	 */
	protected function write( array $record ) {
		if ( ! $this->initialized ) {
			// __destructor() doesn't get called on Fatal errors
			register_shutdown_function( array( $this, 'close' ) );
			$this->initialized = true;
		}

		$context = $record['context'];
		$payload = array();
		if ( isset( $context['payload'] ) ) {
			$payload = $context['payload'];
			unset( $context['payload'] );
		}
		$context = array_merge(
			$context, $record['extra'], array(
				'level'         => $this->levelMap[ $record['level'] ], // @codingStandardsIgnoreLine.
				'monolog_level' => $record['level_name'],
				'channel'       => $record['channel'],
				'datetime'      => $record['datetime']->format( 'U' ),
			)
		);

		if ( isset( $context['exception'] ) && $context['exception'] instanceof Exception ) {
			$payload['level'] = $context['level'];
			$exception        = $context['exception'];
			unset( $context['exception'] );

			$this->rollbarNotifier->report_exception( $exception, $context, $payload ); // @codingStandardsIgnoreLine.
		} else {
			$this->rollbarNotifier->report_message( // @codingStandardsIgnoreLine.
				$record['message'],
				$context['level'],
				$context,
				$payload
			);
		}
		$this->hasRecords = true; // @codingStandardsIgnoreLine.
	}
	/**
	 * Function for flush
	 */
	public function flush() {
		if ( $this->hasRecords ) { // @codingStandardsIgnoreLine.
			$this->rollbarNotifier->flush(); // @codingStandardsIgnoreLine.
			$this->hasRecords = false; // @codingStandardsIgnoreLine.
		}
	}

	/**
	 * Function for close
	 */
	public function close() {
		$this->flush();
	}
}
