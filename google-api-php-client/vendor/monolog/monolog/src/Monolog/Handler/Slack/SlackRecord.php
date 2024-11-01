<?php // @codingStandardsIgnoreLine.
/**
 * This file is part of the Monolog package.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/slack
 * @version 2.0.0
 */

namespace Monolog\Handler\Slack;

use Monolog\Logger;
use Monolog\Formatter\NormalizerFormatter;
use Monolog\Formatter\FormatterInterface;

/**
 * Slack record utility helping to log to Slack webhooks or API.
 *
 * @see    https://api.slack.com/incoming-webhooks
 * @see    https://api.slack.com/docs/message-attachments
 */
class SlackRecord {

	const COLOR_DANGER = 'danger';

	const COLOR_WARNING = 'warning';

	const COLOR_GOOD = 'good';

	const COLOR_DEFAULT = '#e3e4e6';

	/**
	 * Slack channel (encoded ID or name)
	 *
	 * @var string|null
	 */
	private $channel;

	/**
	 * Name of a bot
	 *
	 * @var string|null
	 */
	private $username;

	/**
	 * User icon e.g. 'ghost', 'http://example.com/user.png'
	 *
	 * @var string
	 */
	private $userIcon;// @codingStandardsIgnoreLine.

	/**
	 * Whether the message should be added to Slack as attachment (plain text otherwise)
	 *
	 * @var bool
	 */
	private $useAttachment;// @codingStandardsIgnoreLine.

	/**
	 * Whether the the context/extra messages added to Slack as attachments are in a short style
	 *
	 * @var bool
	 */
	private $useShortAttachment;// @codingStandardsIgnoreLine.

	/**
	 * Whether the attachment should include context and extra data
	 *
	 * @var bool
	 */
	private $includeContextAndExtra;// @codingStandardsIgnoreLine.

	/**
	 * Dot separated list of fields to exclude from slack message. E.g. ['context.field1', 'extra.field2']
	 *
	 * @var array
	 */
	private $excludeFields;// @codingStandardsIgnoreLine.

	/**
	 * The version of this plugin.
	 *
	 * @var $formatter
	 */
	private $formatter;

	/**
	 * The version of this plugin.
	 *
	 * @var NormalizerFormatter .
	 */
	private $normalizerFormatter;// @codingStandardsIgnoreLine.

	public function __construct( $channel = null, $username = null, $useAttachment = true, $userIcon = null, $useShortAttachment = false, $includeContextAndExtra = false, array $excludeFields = array(), FormatterInterface $formatter = null ) {// @codingStandardsIgnoreLine.
		$this->channel                = $channel;
		$this->username               = $username;
		$this->userIcon               = trim( $userIcon, ':' );// @codingStandardsIgnoreLine.
		$this->useAttachment          = $useAttachment;// @codingStandardsIgnoreLine.
		$this->useShortAttachment     = $useShortAttachment;// @codingStandardsIgnoreLine.
		$this->includeContextAndExtra = $includeContextAndExtra;// @codingStandardsIgnoreLine.
		$this->excludeFields          = $excludeFields;// @codingStandardsIgnoreLine.
		$this->formatter              = $formatter;

		if ( $this->includeContextAndExtra ) {// @codingStandardsIgnoreLine.
			$this->normalizerFormatter = new NormalizerFormatter();// @codingStandardsIgnoreLine.
		}
	}

	public function getSlackData( array $record ) {// @codingStandardsIgnoreLine.
		$dataArray = array();// @codingStandardsIgnoreLine.
		$record    = $this->excludeFields( $record );

		if ( $this->username ) {
			$dataArray['username'] = $this->username;// @codingStandardsIgnoreLine.
		}

		if ( $this->channel ) {
			$dataArray['channel'] = $this->channel;// @codingStandardsIgnoreLine.
		}

		if ( $this->formatter && ! $this->useAttachment ) {// @codingStandardsIgnoreLine.
			$message = $this->formatter->format( $record );
		} else {
			$message = $record['message'];
		}

		if ( $this->useAttachment ) {// @codingStandardsIgnoreLine.
			$attachment = array(
				'fallback'  => $message,
				'text'      => $message,
				'color'     => $this->getAttachmentColor( $record['level'] ),
				'fields'    => array(),
				'mrkdwn_in' => array( 'fields' ),
				'ts'        => $record['datetime']->getTimestamp(),
			);

			if ( $this->useShortAttachment ) {// @codingStandardsIgnoreLine.
				$attachment['title'] = $record['level_name'];
			} else {
				$attachment['title']    = 'Message';
				$attachment['fields'][] = $this->generateAttachmentField( 'Level', $record['level_name'] );
			}

			if ( $this->includeContextAndExtra ) {// @codingStandardsIgnoreLine.
				foreach ( array( 'extra', 'context' ) as $key ) {
					if ( empty( $record[ $key ] ) ) {
						continue;
					}

					if ( $this->useShortAttachment ) {// @codingStandardsIgnoreLine.
						$attachment['fields'][] = $this->generateAttachmentField(
							ucfirst( $key ),
							$record[ $key ]
						);
					} else {
						// Add all extra fields as individual fields in attachment .
						$attachment['fields'] = array_merge(
							$attachment['fields'],
							$this->generateAttachmentFields( $record[ $key ] )
						);
					}
				}
			}

			$dataArray['attachments'] = array( $attachment );// @codingStandardsIgnoreLine.
		} else {
			$dataArray['text'] = $message;// @codingStandardsIgnoreLine.
		}

		if ( $this->userIcon ) {// @codingStandardsIgnoreLine.
			if ( filter_var( $this->userIcon, FILTER_VALIDATE_URL ) ) {// @codingStandardsIgnoreLine.
				$dataArray['icon_url'] = $this->userIcon;// @codingStandardsIgnoreLine.
			} else {
				$dataArray['icon_emoji'] = ":{$this->userIcon}:";// @codingStandardsIgnoreLine.
			}
		}

		return $dataArray;// @codingStandardsIgnoreLine.
	}

	/**
	 * Returned a Slack message attachment color associated with
	 * provided level.
	 *
	 * @param  int $level .
	 * @return string
	 */
	public function getAttachmentColor( $level ) {// @codingStandardsIgnoreLine.
		switch ( true ) {
			case $level >= Logger::ERROR:
				return self::COLOR_DANGER;
			case $level >= Logger::WARNING:
				return self::COLOR_WARNING;
			case $level >= Logger::INFO:
				return self::COLOR_GOOD;
			default:
				return self::COLOR_DEFAULT;
		}
	}

	/**
	 * Stringifies an array of key/value pairs to be used in attachment fields
	 *
	 * @param array $fields .
	 *
	 * @return string
	 */
	public function stringify( $fields ) {
		$normalized      = $this->normalizerFormatter->format( $fields );// @codingStandardsIgnoreLine.
		$prettyPrintFlag = defined( 'JSON_PRETTY_PRINT' ) ? JSON_PRETTY_PRINT : 128;// @codingStandardsIgnoreLine.

		$hasSecondDimension = count( array_filter( $normalized, 'is_array' ) );// @codingStandardsIgnoreLine.
		$hasNonNumericKeys  = ! count( array_filter( array_keys( $normalized ), 'is_numeric' ) );// @codingStandardsIgnoreLine.

		return $hasSecondDimension || $hasNonNumericKeys// @codingStandardsIgnoreLine.s
			? json_encode( $normalized, $prettyPrintFlag )// @codingStandardsIgnoreLine.
			: json_encode( $normalized );// @codingStandardsIgnoreLine.
	}

	/**
	 * Sets the formatter
	 *
	 * @param FormatterInterface $formatter .
	 */
	public function setFormatter( FormatterInterface $formatter ) {// @codingStandardsIgnoreLine.
		$this->formatter = $formatter;
	}

	/**
	 * Generates attachment field
	 *
	 * @param string       $title .
	 * @param string|array $value .
	 *
	 * @return array
	 */
	private function generateAttachmentField( $title, $value ) {// @codingStandardsIgnoreLine.
		$value = is_array( $value )
			? sprintf( '```%s```', $this->stringify( $value ) )
			: $value;

		return array(
			'title' => $title,
			'value' => $value,
			'short' => false,
		);
	}

	/**
	 * Generates a collection of attachment fields from array
	 *
	 * @param array $data .
	 *
	 * @return array
	 */
	private function generateAttachmentFields( array $data ) {// @codingStandardsIgnoreLine.
		$fields = array();
		foreach ( $data as $key => $value ) {
			$fields[] = $this->generateAttachmentField( $key, $value );
		}

		return $fields;
	}

	/**
	 * Get a copy of record with fields excluded according to $this->excludeFields
	 *
	 * @param array $record .
	 *
	 * @return array
	 */
	private function excludeFields( array $record ) {// @codingStandardsIgnoreLine.
		foreach ( $this->excludeFields as $field ) {// @codingStandardsIgnoreLine.
			$keys    = explode( '.', $field );
			$node    = &$record;
			$lastKey = end( $keys );// @codingStandardsIgnoreLine.
			foreach ( $keys as $key ) {
				if ( ! isset( $node[ $key ] ) ) {
					break;
				}
				if ( $lastKey === $key ) {// @codingStandardsIgnoreLine.
					unset( $node[ $key ] );
					break;
				}
				$node = &$node[ $key ];
			}
		}

		return $record;
	}
}
