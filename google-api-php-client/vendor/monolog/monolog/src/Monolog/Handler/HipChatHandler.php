<?php // @codingStandardsIgnoreLine.
/**
 * This file is part of the Monolog package.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/handler
 * @version 2.0.0
 */

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Handler;

use Monolog\Logger;

/**
 * Sends notifications through the hipchat api to a hipchat room
 *
 * Notes:
 * API token - HipChat API token
 * Room      - HipChat Room Id or name, where messages are sent
 * Name      - Name used to send the message (from)
 * notify    - Should the message trigger a notification in the clients
 * version   - The API version to use (HipChatHandler::API_V1 | HipChatHandler::API_V2)
 *
 * @see    https://www.hipchat.com/docs/api
 */
class HipChatHandler extends SocketHandler {

	/**
	 * Use API version 1
	 */
	const API_V1 = 'v1';

	/**
	 * Use API version v2
	 */
	const API_V2 = 'v2';

	/**
	 * The maximum allowed length for the name used in the "from" field.
	 */
	const MAXIMUM_NAME_LENGTH = 15;

	/**
	 * The maximum allowed length for the message.
	 */
	const MAXIMUM_MESSAGE_LENGTH = 9500;

	/**
	 * The version  of the plugin.
	 *
	 * @var string
	 */
	private $token;

	/**
	 * The version  of the plugin.
	 *
	 * @var string
	 */
	private $room;

	/**
	 * The version  of the plugin.
	 *
	 * @var string
	 */
	private $name;

	/**
	 * The version  of the plugin.
	 *
	 * @var bool
	 */
	private $notify;

	/**
	 * The version  of the plugin.
	 *
	 * @var string
	 */
	private $format;

	/**
	 * The version  of the plugin.
	 *
	 * @var string
	 */
	private $host;

	/**
	 * The version  of the plugin.
	 *
	 * @var string
	 */
	private $version;

	/**
	 * The version  of the plugin.
	 *
	 * @param string $token   HipChat API Token .
	 * @param string $room    The room that should be alerted of the message (Id or Name) .
	 * @param string $name    Name used in the "from" field .
	 * @param bool   $notify  Trigger a notification in clients or not .
	 * @param int    $level   The minimum logging level at which this handler will be triggered .
	 * @param bool   $bubble  Whether the messages that are handled can bubble up the stack or not .
	 * @param bool   $useSSL  Whether to connect via SSL .
	 * @param string $format  The format of the messages (default to text, can be set to html if you have html in the messages) .
	 * @param string $host    The HipChat server hostname .
	 * @param string $version The HipChat API version (default HipChatHandler::API_V1) .
	 * @throws \InvalidArgumentException .
	 */
	public function __construct( $token, $room, $name = 'Monolog', $notify = false, $level = Logger::CRITICAL, $bubble = true, $useSSL = true, $format = 'text', $host = 'api.hipchat.com', $version = self::API_V1 ) {// @codingStandardsIgnoreLine.
		if ( $version == self::API_V1 && ! $this->validateStringLength( $name, static::MAXIMUM_NAME_LENGTH ) ) {// @codingStandardsIgnoreLine.
			throw new \InvalidArgumentException( 'The supplied name is too long. HipChat\'s v1 API supports names up to 15 UTF-8 characters.' );
		}

		$connectionString = $useSSL ? 'ssl://' . $host . ':443' : $host . ':80';// @codingStandardsIgnoreLine.
		parent::__construct( $connectionString, $level, $bubble );// @codingStandardsIgnoreLine.

		$this->token   = $token;
		$this->name    = $name;
		$this->notify  = $notify;
		$this->room    = $room;
		$this->format  = $format;
		$this->host    = $host;
		$this->version = $version;
	}

	/**
	 * This function is generateDataStream.
	 *
	 * {@inheritdoc}
	 *
	 * @param  array $record .
	 * @return string
	 */
	protected function generateDataStream( $record ) {
		$content = $this->buildContent( $record );

		return $this->buildHeader( $content ) . $content;
	}

	/**
	 * Builds the body of API call
	 *
	 * @param  array $record .
	 * @return string
	 */
	private function buildContent( $record ) {
		$dataArray = array(// @codingStandardsIgnoreLine.
			'notify'         => $this->version == self::API_V1 ?// @codingStandardsIgnoreLine.
				( $this->notify ? 1 : 0 ) :
				( $this->notify ? 'true' : 'false' ),
			'message'        => $record['formatted'],
			'message_format' => $this->format,
			'color'          => $this->getAlertColor( $record['level'] ),
		);

		if ( ! $this->validateStringLength( $dataArray['message'], static::MAXIMUM_MESSAGE_LENGTH ) ) {// @codingStandardsIgnoreLine.
			if ( function_exists( 'mb_substr' ) ) {
				$dataArray['message'] = mb_substr( $dataArray['message'], 0, static::MAXIMUM_MESSAGE_LENGTH ) . ' [truncated]';// @codingStandardsIgnoreLine.
			} else {
				$dataArray['message'] = substr( $dataArray['message'], 0, static::MAXIMUM_MESSAGE_LENGTH ) . ' [truncated]';// @codingStandardsIgnoreLine.
			}
		}

		// if we are using the legacy API then we need to send some additional information .
		if ( $this->version == self::API_V1 ) {// @codingStandardsIgnoreLine.
			$dataArray['room_id'] = $this->room;// @codingStandardsIgnoreLine.
		}

		// append the sender name if it is set .
		// always append it if we use the v1 api (it is required in v1) .
		if ( $this->version == self::API_V1 || $this->name !== null ) {// @codingStandardsIgnoreLine.
			$dataArray['from'] = (string) $this->name;// @codingStandardsIgnoreLine.
		}

		return http_build_query( $dataArray );// @codingStandardsIgnoreLine.
	}

	/**
	 * Builds the header of the API Call
	 *
	 * @param  string $content .
	 * @return string
	 */
	private function buildHeader( $content ) {
		if ( $this->version == self::API_V1 ) {// @codingStandardsIgnoreLine.
			$header = "POST /v1/rooms/message?format=json&auth_token={$this->token} HTTP/1.1\r\n";
		} else {
			// needed for rooms with special (spaces, etc) characters in the name .
			$room   = rawurlencode( $this->room );
			$header = "POST /v2/room/{$room}/notification?auth_token={$this->token} HTTP/1.1\r\n";
		}

		$header .= "Host: {$this->host}\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= 'Content-Length: ' . strlen( $content ) . "\r\n";
		$header .= "\r\n";

		return $header;
	}

	/**
	 * Assigns a color to each level of log records.
	 *
	 * @param  int $level .
	 * @return string
	 */
	protected function getAlertColor( $level ) {
		switch ( true ) {
			case $level >= Logger::ERROR:
				return 'red';
			case $level >= Logger::WARNING:
				return 'yellow';
			case $level >= Logger::INFO:
				return 'green';
			case $level == Logger::DEBUG:// @codingStandardsIgnoreLine.
				return 'gray';
			default:
				return 'yellow';
		}
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param array $record .
	 */
	protected function write( array $record ) {
		parent::write( $record );
		$this->closeSocket();
	}

	/**
	 * This function is handleBatch.
	 *
	 * @param array $records .
	 * {@inheritdoc}.
	 */
	public function handleBatch( array $records ) {
		if ( count( $records ) == 0 ) {// @codingStandardsIgnoreLine.
			return true;
		}

		$batchRecords = $this->combineRecords( $records );// @codingStandardsIgnoreLine.

		$handled = false;
		foreach ( $batchRecords as $batchRecord ) {// @codingStandardsIgnoreLine.
			if ( $this->isHandling( $batchRecord ) ) {// @codingStandardsIgnoreLine.
				$this->write( $batchRecord );// @codingStandardsIgnoreLine.
				$handled = true;
			}
		}

		if ( ! $handled ) {
			return false;
		}

		return false === $this->bubble;
	}

	/**
	 * Combines multiple records into one. Error level of the combined record
	 * will be the highest level from the given records. Datetime will be taken
	 * from the first record.
	 *
	 * @param string $records .
	 * @return array
	 */
	private function combineRecords( $records ) {
		$batchRecord       = null;// @codingStandardsIgnoreLine.
		$batchRecords      = array();// @codingStandardsIgnoreLine.
		$messages          = array();
		$formattedMessages = array();// @codingStandardsIgnoreLine.
		$level             = 0;
		$levelName         = null;// @codingStandardsIgnoreLine.
		$datetime          = null;

		foreach ( $records as $record ) {
			$record = $this->processRecord( $record );

			if ( $record['level'] > $level ) {
				$level     = $record['level'];
				$levelName = $record['level_name'];// @codingStandardsIgnoreLine.
			}

			if ( null === $datetime ) {
				$datetime = $record['datetime'];
			}

			$messages[]          = $record['message'];
			$messageStr          = implode( PHP_EOL, $messages );// @codingStandardsIgnoreLine.
			$formattedMessages[] = $this->getFormatter()->format( $record );// @codingStandardsIgnoreLine.
			$formattedMessageStr = implode( '', $formattedMessages );// @codingStandardsIgnoreLine.

			$batchRecord = array(// @codingStandardsIgnoreLine.
				'message'   => $messageStr,// @codingStandardsIgnoreLine.
				'formatted' => $formattedMessageStr,// @codingStandardsIgnoreLine.
				'context'   => array(),
				'extra'     => array(),
			);

			if ( ! $this->validateStringLength( $batchRecord['formatted'], static::MAXIMUM_MESSAGE_LENGTH ) ) {// @codingStandardsIgnoreLine.
				// Pop the last message and implode the remaining messages
				$lastMessage              = array_pop( $messages );// @codingStandardsIgnoreLine.
				$lastFormattedMessage     = array_pop( $formattedMessages );// @codingStandardsIgnoreLine.
				$batchRecord['message']   = implode( PHP_EOL, $messages );// @codingStandardsIgnoreLine.
				$batchRecord['formatted'] = implode( '', $formattedMessages );// @codingStandardsIgnoreLine.

				$batchRecords[]    = $batchRecord;// @codingStandardsIgnoreLine.
				$messages          = array( $lastMessage );// @codingStandardsIgnoreLine.
				$formattedMessages = array( $lastFormattedMessage );// @codingStandardsIgnoreLine.

				$batchRecord = null;// @codingStandardsIgnoreLine.
			}
		}

		if ( null !== $batchRecord ) {// @codingStandardsIgnoreLine.
			$batchRecords[] = $batchRecord;// @codingStandardsIgnoreLine.
		}

		// Set the max level and datetime for all records .
		foreach ( $batchRecords as &$batchRecord ) {// @codingStandardsIgnoreLine.
			$batchRecord = array_merge(// @codingStandardsIgnoreLine.
				$batchRecord,// @codingStandardsIgnoreLine.
				array(
					'level'      => $level,
					'level_name' => $levelName,// @codingStandardsIgnoreLine.
					'datetime'   => $datetime,
				)
			);
		}

		return $batchRecords;// @codingStandardsIgnoreLine.
	}

	/**
	 * Validates the length of a string.
	 *
	 * If the `mb_strlen()` function is available, it will use that, as HipChat
	 * allows UTF-8 characters. Otherwise, it will fall back to `strlen()`.
	 *
	 * Note that this might cause false failures in the specific case of using
	 * a valid name with less than 16 characters, but 16 or more bytes, on a
	 * system where `mb_strlen()` is unavailable.
	 *
	 * @param string $str .
	 * @param int    $length .
	 *
	 * @return bool
	 */
	private function validateStringLength( $str, $length ) {
		if ( function_exists( 'mb_strlen' ) ) {
			return ( mb_strlen( $str ) <= $length );
		}

		return ( strlen( $str ) <= $length );
	}
}
