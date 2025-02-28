<?php // @codingStandardsIgnoreLine
/**
 * This file implement PSR-7 response
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor/composer
 * @version 2.0.0
 */

namespace GuzzleHttp\Psr7;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * PSR-7 response implementation.
 */
class Response implements ResponseInterface {

	use MessageTrait;
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      array    $phrases  .
	 */
	private static $phrases = [
		100 => 'Continue',
		101 => 'Switching Protocols',
		102 => 'Processing',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		207 => 'Multi-status',
		208 => 'Already Reported',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		306 => 'Switch Proxy',
		307 => 'Temporary Redirect',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Time-out',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Large',
		415 => 'Unsupported Media Type',
		416 => 'Requested range not satisfiable',
		417 => 'Expectation Failed',
		418 => 'I\'m a teapot',
		422 => 'Unprocessable Entity',
		423 => 'Locked',
		424 => 'Failed Dependency',
		425 => 'Unordered Collection',
		426 => 'Upgrade Required',
		428 => 'Precondition Required',
		429 => 'Too Many Requests',
		431 => 'Request Header Fields Too Large',
		451 => 'Unavailable For Legal Reasons',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Time-out',
		505 => 'HTTP Version not supported',
		506 => 'Variant Also Negotiates',
		507 => 'Insufficient Storage',
		508 => 'Loop Detected',
		511 => 'Network Authentication Required',
	];

	private $reasonPhrase = '';// @codingStandardsIgnoreLine

	private $statusCode = 200;// @codingStandardsIgnoreLine

	/**
	 * This function is __construct.
	 *
	 * @param int                                  $status  Status code.
	 * @param array                                $headers Response headers.
	 * @param string|null|resource|StreamInterface $body    Response body.
	 * @param string                               $version Protocol version.
	 * @param string|null                          $reason  Reason phrase (when empty a default will be used based on the status code).
	 */
	public function __construct(
		$status = 200,
		array $headers = [],
		$body = null,
		$version = '1.1',
		$reason = null
	) {
		$this->statusCode = (int) $status;// @codingStandardsIgnoreLine

		if ( '' !== $body && null !== $body ) {
			$this->stream = stream_for( $body );
		}

		$this->setHeaders( $headers );
		if ( $reason == '' && isset( self::$phrases[ $this->statusCode ] ) ) {// @codingStandardsIgnoreLine
			$this->reasonPhrase = self::$phrases[ $this->statusCode ];// @codingStandardsIgnoreLine
		} else {
			$this->reasonPhrase = (string) $reason;// @codingStandardsIgnoreLine
		}

		$this->protocol = $version;
	}
	/**
	 * This function is getStatusCode.
	 */
	public function getStatusCode() {
		return $this->statusCode;// @codingStandardsIgnoreLine
	}
	/**
	 * This function is getReasonPhrase.
	 */
	public function getReasonPhrase() {
		return $this->reasonPhrase;// @codingStandardsIgnoreLine
	}

	public function withStatus( $code, $reasonPhrase = '' ) {// @codingStandardsIgnoreLine
		$new             = clone $this;
		$new->statusCode = (int) $code;// @codingStandardsIgnoreLine
		if ( $reasonPhrase == '' && isset( self::$phrases[ $new->statusCode ] ) ) {// @codingStandardsIgnoreLine
			$reasonPhrase = self::$phrases[ $new->statusCode ];// @codingStandardsIgnoreLine
		}
		$new->reasonPhrase = $reasonPhrase;// @codingStandardsIgnoreLine
		return $new;
	}
}
