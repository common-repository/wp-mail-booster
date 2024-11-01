<?php // @codingStandardsIgnoreLine
/**
 * This file to Serializes a log message according to Wildfire's header requirements.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Formatter;

use Monolog\Logger;

/**
 * Serializes a log message according to Wildfire's header requirements
 */
class WildfireFormatter extends NormalizerFormatter {

	const TABLE = 'table';

	/**
	 * The version of this plugin.
	 *
	 * @var string $logLevels .
	 * Translates Monolog log levels to Wildfire levels.
	 */
	private $logLevels = array(// @codingStandardsIgnoreLine
		Logger::DEBUG     => 'LOG',
		Logger::INFO      => 'INFO',
		Logger::NOTICE    => 'INFO',
		Logger::WARNING   => 'WARN',
		Logger::ERROR     => 'ERROR',
		Logger::CRITICAL  => 'ERROR',
		Logger::ALERT     => 'ERROR',
		Logger::EMERGENCY => 'ERROR',
	);

	/**
	 * This function is format.
	 *
	 * @param array $record .
	 * {@inheritdoc}.
	 */
	public function format( array $record ) {
		// Retrieve the line and file if set and remove them from the formatted extra .
		$file = $line = '';// @codingStandardsIgnoreLine
		if ( isset( $record['extra']['file'] ) ) {
			$file = $record['extra']['file'];
			unset( $record['extra']['file'] );
		}
		if ( isset( $record['extra']['line'] ) ) {
			$line = $record['extra']['line'];
			unset( $record['extra']['line'] );
		}

		$record      = $this->normalize( $record );
		$message     = array( 'message' => $record['message'] );
		$handleError = false;// @codingStandardsIgnoreLine
		if ( $record['context'] ) {
			$message['context'] = $record['context'];
			$handleError        = true;// @codingStandardsIgnoreLine
		}
		if ( $record['extra'] ) {
			$message['extra'] = $record['extra'];
			$handleError      = true;// @codingStandardsIgnoreLine
		}
		if ( count( $message ) === 1 ) {
			$message = reset( $message );
		}

		if ( isset( $record['context'][ self::TABLE ] ) ) {
			$type    = 'TABLE';
			$label   = $record['channel'] . ': ' . $record['message'];
			$message = $record['context'][ self::TABLE ];
		} else {
			$type  = $this->logLevels[ $record['level'] ];// @codingStandardsIgnoreLine
			$label = $record['channel'];
		}

		// Create JSON object describing the appearance of the message in the console .
		$json = $this->toJson(
			array(
				array(
					'Type'  => $type,
					'File'  => $file,
					'Line'  => $line,
					'Label' => $label,
				),
				$message,
			), $handleError// @codingStandardsIgnoreLine
		);

		// The message itself is a serialization of the above JSON object + it's length .
		return sprintf(
			'%s|%s|',
			strlen( $json ),
			$json
		);
	}
	/**
	 * This function is formatBatch.
	 *
	 * @param array $records .
	 * @throws \BadMethodCallException .
	 * {@inheritdoc}.
	 */
	public function formatBatch( array $records ) {
		throw new \BadMethodCallException( 'Batch formatting does not make sense for the WildfireFormatter' );
	}
	/**
	 * This function is formatBatch.
	 *
	 * @param array $data .
	 * @throws \BadMethodCallException .
	 * {@inheritdoc}.
	 */
	protected function normalize( $data ) {
		if ( is_object( $data ) && ! $data instanceof \DateTime ) {
			return $data;
		}

		return parent::normalize( $data );
	}
}
