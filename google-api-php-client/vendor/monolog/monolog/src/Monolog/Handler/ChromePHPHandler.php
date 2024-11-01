<?php // @codingStandardsIgnoreLine.
/**
 * This file is part of the Monolog package.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/handler
 * @version 2.0.0
 */
namespace Monolog\Handler;

use Monolog\Formatter\ChromePHPFormatter;
use Monolog\Logger;

/**
 * Handler sending logs to the ChromePHP extension (http://www.chromephp.com/)
 *
 * This also works out of the box with Firefox 43+
 */
class ChromePHPHandler extends AbstractProcessingHandler {

	/**
	 * Version of the extension
	 */
	const VERSION = '4.0';

	/**
	 * Header name
	 */
	const HEADER_NAME = 'X-ChromeLogger-Data';

	/**
	 * Regular expression to detect supported browsers (matches any Chrome, or Firefox 43+)
	 */
	const USER_AGENT_REGEX = '{\b(?:Chrome/\d+(?:\.\d+)*|HeadlessChrome|Firefox/(?:4[3-9]|[5-9]\d|\d{3,})(?:\.\d)*)\b}';
	/**
	 * The version of this plugin.
	 *
	 * @var $initialized
	 */
	protected static $initialized = false;

	/**
	 * Tracks whether we sent too much data
	 *
	 * Chrome limits the headers to 256KB, so when we sent 240KB we stop sending
	 *
	 * @var Boolean
	 */
	protected static $overflowed = false;
	/**
	 * The version of this plugin.
	 *
	 * @var $initialized
	 */
	protected static $json = array(
		'version' => self::VERSION,
		'columns' => array( 'label', 'log', 'backtrace', 'type' ),
		'rows'    => array(),
	);

	protected static $sendHeaders = true;// @codingStandardsIgnoreLine.

	/**
	 * This function is __construct.
	 *
	 * @param int     $level  The minimum logging level at which this handler will be triggered .
	 * @param Boolean $bubble Whether the messages that are handled can bubble up the stack or not .
	 * @throws \RuntimeException .
	 */
	public function __construct( $level = Logger::DEBUG, $bubble = true ) {
		parent::__construct( $level, $bubble );
		if ( ! function_exists( 'json_encode' ) ) {
			throw new \RuntimeException( 'PHP\'s json extension is required to use Monolog\'s ChromePHPHandler' );
		}
	}

	/**
	 * This function is handleBatch.
	 *
	 * @param array $records .
	 * {@inheritdoc}.
	 */
	public function handleBatch( array $records ) {
		$messages = array();

		foreach ( $records as $record ) {
			if ( $record['level'] < $this->level ) {
				continue;
			}
			$messages[] = $this->processRecord( $record );
		}

		if ( ! empty( $messages ) ) {
			$messages           = $this->getFormatter()->formatBatch( $messages );
			self::$json['rows'] = array_merge( self::$json['rows'], $messages );
			$this->send();
		}
	}

	/**
	 * {@inheritDoc}
	 */
	protected function getDefaultFormatter() {
		return new ChromePHPFormatter();
	}

	/**
	 * Creates & sends header for a record
	 *
	 * @see sendHeader()
	 * @see send()
	 * @param array $record .
	 */
	protected function write( array $record ) {
		self::$json['rows'][] = $record['formatted'];

		$this->send();
	}

	/**
	 * Sends the log header
	 *
	 * @see sendHeader()
	 */
	protected function send() {
		if ( self::$overflowed || ! self::$sendHeaders ) {// @codingStandardsIgnoreLine.
			return;
		}

		if ( ! self::$initialized ) {
			self::$initialized = true;

			self::$sendHeaders = $this->headersAccepted();// @codingStandardsIgnoreLine.
			if ( ! self::$sendHeaders ) {// @codingStandardsIgnoreLine.
				return;
			}

			self::$json['request_uri'] = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '';// @codingStandardsIgnoreLine.
		}

		$json = @json_encode( self::$json );// @codingStandardsIgnoreLine.
		$data = base64_encode( utf8_encode( $json ) );
		if ( strlen( $data ) > 240 * 1024 ) {
			self::$overflowed = true;

			$record = array(
				'message'    => 'Incomplete logs, chrome header size limit reached',
				'context'    => array(),
				'level'      => Logger::WARNING,
				'level_name' => Logger::getLevelName( Logger::WARNING ),
				'channel'    => 'monolog',
				'datetime'   => new \DateTime(),
				'extra'      => array(),
			);
			self::$json['rows'][ count( self::$json['rows'] ) - 1 ] = $this->getFormatter()->format( $record );
			$json = @json_encode( self::$json );// @codingStandardsIgnoreLine.
			$data = base64_encode( utf8_encode( $json ) );
		}

		if ( trim( $data ) !== '' ) {
			$this->sendHeader( self::HEADER_NAME, $data );
		}
	}

	/**
	 * Send header string to the client
	 *
	 * @param string $header .
	 * @param string $content .
	 */
	protected function sendHeader( $header, $content ) {
		if ( ! headers_sent() && self::$sendHeaders ) { // @codingStandardsIgnoreLine.
			header( sprintf( '%s: %s', $header, $content ) );
		}
	}

	/**
	 * Verifies if the headers are accepted by the current user agent
	 *
	 * @return Boolean
	 */
	protected function headersAccepted() {
		if ( empty( $_SERVER['HTTP_USER_AGENT'] ) ) {// @codingStandardsIgnoreLine.
			return false;
		}

		return preg_match( self::USER_AGENT_REGEX, $_SERVER['HTTP_USER_AGENT'] );// @codingStandardsIgnoreLine.
	}

	/**
	 * BC getter for the sendHeaders property that has been made static .
	 *
	 * @param string $property .
	 * @throws \InvalidArgumentException .
	 */
	public function __get( $property ) {
		if ( 'sendHeaders' !== $property ) {
			throw new \InvalidArgumentException( 'Undefined property ' . $property );
		}

		return static::$sendHeaders;// @codingStandardsIgnoreLine.
	}

	/**
	 * BC setter for the sendHeaders property that has been made static .
	 *
	 * @param string $property .
	 * @param string $value .
	 * @throws \InvalidArgumentException .
	 */
	public function __set( $property, $value ) {
		if ( 'sendHeaders' !== $property ) {
			throw new \InvalidArgumentException( 'Undefined property ' . $property );
		}

		static::$sendHeaders = $value;// @codingStandardsIgnoreLine.
	}
}
