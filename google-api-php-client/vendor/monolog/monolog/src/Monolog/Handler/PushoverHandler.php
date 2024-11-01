<?php // @codingStandardsIgnoreLine.
/**
 * This file to handle send notification through push over api
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
 * Sends notifications through the pushover api to mobile phones
 */
class PushoverHandler extends SocketHandler {
	/**
	 * Variable token
	 *
	 * @var string
	 */
	private $token;
	/**
	 * Variable user
	 *
	 * @var string
	 */
	private $users;
	/**
	 * Variable title
	 *
	 * @var string
	 */
	private $title;
	/**
	 * Variable user
	 *
	 * @var string
	 */
	private $user;
	/**
	 * Variable retry
	 *
	 * @var string
	 */
	private $retry;
	/**
	 * Variable expire
	 *
	 * @var string
	 */
	private $expire;
	/**
	 * Variable for high priority leval
	 *
	 * @var string
	 */
	private $highPriorityLevel; // @codingStandardsIgnoreLine.
	/**
	 * Variable emergency level
	 *
	 * @var string
	 */
	private $emergencyLevel; // @codingStandardsIgnoreLine.
	/**
	 * Variable use format message
	 *
	 * @var bool
	 */
	private $useFormattedMessage = false; // @codingStandardsIgnoreLine.

	/**
	 * All parameters that can be sent to Pushover
	 *
	 * @var array
	 */
	private $parameterNames = array( // @codingStandardsIgnoreLine.
		'token'     => true,
		'user'      => true,
		'message'   => true,
		'device'    => true,
		'title'     => true,
		'url'       => true,
		'url_title' => true,
		'priority'  => true,
		'timestamp' => true,
		'sound'     => true,
		'retry'     => true,
		'expire'    => true,
		'callback'  => true,
	);

	/**
	 * Sounds the api supports by default
	 *
	 * @var array
	 */
	private $sounds = array(
		'pushover',
		'bike',
		'bugle',
		'cashregister',
		'classical',
		'cosmic',
		'falling',
		'gamelan',
		'incoming',
		'intermission',
		'magic',
		'mechanical',
		'pianobar',
		'siren',
		'spacealarm',
		'tugboat',
		'alien',
		'climb',
		'persistent',
		'echo',
		'updown',
		'none',
	);

	/**
	 * Public constructor
	 *
	 * @param string       $token             Pushover api token .
	 * @param string|array $users             Pushover user id or array of ids the message will be sent to .
	 * @param string       $title             Title sent to the Pushover API .
	 * @param int          $level             The minimum logging level at which this handler will be triggered .
	 * @param Boolean      $bubble            Whether the messages that are handled can bubble up the stack or not .
	 * @param Boolean      $useSSL            Whether to connect via SSL. Required when pushing messages to users that are not .
	 *                                        the pushover.net app owner. OpenSSL is required for this option .
	 * @param int          $highPriorityLevel The minimum logging level at which this handler will start
	 *                                        sending "high priority" requests to the Pushover API .
	 * @param int          $emergencyLevel    The minimum logging level at which this handler will start
	 *                                        sending "emergency" requests to the Pushover API .
	 * @param int          $retry             The retry parameter specifies how often (in seconds) the Pushover servers will send the same notification to the user.
	 * @param int          $expire            The expire parameter specifies how many seconds your notification will continue to be retried for (every retry seconds).
	 */
	public function __construct( $token, $users, $title = null, $level = Logger::CRITICAL, $bubble = true, $useSSL = true, $highPriorityLevel = Logger::CRITICAL, $emergencyLevel = Logger::EMERGENCY, $retry = 30, $expire = 25200 ) { // @codingStandardsIgnoreLine.
		$connectionString = $useSSL ? 'ssl://api.pushover.net:443' : 'api.pushover.net:80'; // @codingStandardsIgnoreLine.
		parent::__construct( $connectionString, $level, $bubble ); // @codingStandardsIgnoreLine.

		$this->token             = $token;
		$this->users             = (array) $users;
		$this->title             = $title ?: gethostname();
		$this->highPriorityLevel = Logger::toMonologLevel( $highPriorityLevel ); // @codingStandardsIgnoreLine.
		$this->emergencyLevel    = Logger::toMonologLevel( $emergencyLevel ); // @codingStandardsIgnoreLine.
		$this->retry             = $retry;
		$this->expire            = $expire;
	}
	/**
	 * Function to generate data stream
	 *
	 * @param string $record .
	 */
	protected function generateDataStream( $record ) {
		$content = $this->buildContent( $record );

		return $this->buildHeader( $content ) . $content;
	}
	/**
	 * Function to build content
	 *
	 * @param string $record .
	 */
	private function buildContent( $record ) {
		// Pushover has a limit of 512 characters on title and message combined.
		$maxMessageLength = 512 - strlen( $this->title ); // @codingStandardsIgnoreLine.

		$message = ( $this->useFormattedMessage ) ? $record['formatted'] : $record['message']; // @codingStandardsIgnoreLine.
		$message = substr( $message, 0, $maxMessageLength ); // @codingStandardsIgnoreLine.
		$timestamp = $record['datetime']->getTimestamp();
		$dataArray = array( // @codingStandardsIgnoreLine.
			'token'     => $this->token,
			'user'      => $this->user,
			'message'   => $message,
			'title'     => $this->title,
			'timestamp' => $timestamp,
		);

		if ( isset( $record['level'] ) && $record['level'] >= $this->emergencyLevel ) { // @codingStandardsIgnoreLine.
			$dataArray['priority'] = 2; // @codingStandardsIgnoreLine.
			$dataArray['retry']    = $this->retry; // @codingStandardsIgnoreLine.
			$dataArray['expire']   = $this->expire; // @codingStandardsIgnoreLine.
		} elseif ( isset( $record['level'] ) && $record['level'] >= $this->highPriorityLevel ) { // @codingStandardsIgnoreLine.
			$dataArray['priority'] = 1; // @codingStandardsIgnoreLine.
		}

		// First determine the available parameters .
		$context = array_intersect_key( $record['context'], $this->parameterNames ); // @codingStandardsIgnoreLine.
		$extra   = array_intersect_key( $record['extra'], $this->parameterNames ); // @codingStandardsIgnoreLine.

		// Least important info should be merged with subsequent info .
		$dataArray = array_merge( $extra, $context, $dataArray ); // @codingStandardsIgnoreLine.

		// Only pass sounds that are supported by the API .
		if ( isset( $dataArray['sound'] ) && ! in_array( $dataArray['sound'], $this->sounds ) ) { // @codingStandardsIgnoreLine.
			unset( $dataArray['sound'] ); // @codingStandardsIgnoreLine.
		}

		return http_build_query( $dataArray ); // @codingStandardsIgnoreLine.
	}
	/**
	 * Function to build header
	 *
	 * @param string $content .
	 */
	private function buildHeader( $content ) {
		$header  = "POST /1/messages.json HTTP/1.1\r\n";
		$header .= "Host: api.pushover.net\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= 'Content-Length: ' . strlen( $content ) . "\r\n";
		$header .= "\r\n";

		return $header;
	}
	/**
	 * Function to write record
	 *
	 * @param array $record .
	 */
	protected function write( array $record ) {
		foreach ( $this->users as $user ) {
			$this->user = $user;

			parent::write( $record );
			$this->closeSocket();
		}

		$this->user = null;
	}
	/**
	 * Function to set high priority
	 *
	 * @param string $value .
	 */
	public function setHighPriorityLevel( $value ) {
		$this->highPriorityLevel = $value; // @codingStandardsIgnoreLine.
	}
	/**
	 * Function to set emergency level
	 *
	 * @param string $value .
	 */
	public function setEmergencyLevel( $value ) {
		$this->emergencyLevel = $value; // @codingStandardsIgnoreLine.
	}

	/**
	 * Use the formatted message?
	 *
	 * @param bool $value .
	 */
	public function useFormattedMessage( $value ) {
		$this->useFormattedMessage = (boolean) $value; // @codingStandardsIgnoreLine.
	}
}
