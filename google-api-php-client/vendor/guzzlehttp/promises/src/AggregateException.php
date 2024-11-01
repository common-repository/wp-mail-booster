<?php // @codingStandardsIgnoreLine.
/**
 * This Template is TaskQueueInterface.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client\vendor\guzzlehttp\promises\src
 * @version 2.0.0
 */
namespace GuzzleHttp\Promise;

/**
 * Exception thrown when too many errors occur in the some() or any() methods.
 */
class AggregateException extends RejectionException {
	/**
	 * The function is __construct .
	 *
	 * @param string $msg .
	 * @param array  $reasons .
	 */
	public function __construct( $msg, array $reasons ) {
		parent::__construct(
			$reasons,
			sprintf( '%s; %d rejected promises', $msg, count( $reasons ) )
		);
	}
}
