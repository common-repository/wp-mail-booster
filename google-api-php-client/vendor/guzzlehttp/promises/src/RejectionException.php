<?php // @codingStandardsIgnoreLine.
/**
 * This Template is RejectionException.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client\vendor\guzzlehttp\promises\src
 * @version 2.0.0
 */
namespace GuzzleHttp\Promise;

/**
 * A special exception that is thrown when waiting on a rejected promise.
 *
 * The reason value is available via the getReason() method.
 */
class RejectionException extends \RuntimeException {

	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $reason  .
	 */
	private $reason;

	/**
	 * This is __construct .
	 *
	 * @param mixed $reason      Rejection reason.
	 * @param null  $description Optional description .
	 */
	public function __construct( $reason, $description = null ) {
		$this->reason = $reason;

		$message = 'The promise was rejected';

		if ( $description ) {
			$message .= ' with reason: ' . $description;
		} elseif ( is_string( $reason )
			|| ( is_object( $reason ) && method_exists( $reason, '__toString' ) )
		) {
			$message .= ' with reason: ' . $this->reason;
		} elseif ( $reason instanceof \JsonSerializable ) {
			$message .= ' with reason: '
				. wp_json_encode( $this->reason, JSON_PRETTY_PRINT );
		}

		parent::__construct( $message );
	}

	/**
	 * Returns the rejection reason.
	 *
	 * @return mixed
	 */
	public function getReason() {
		return $this->reason;
	}
}
