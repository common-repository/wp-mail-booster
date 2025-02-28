<?php // @codingStandardsIgnoreLine
/**
 * This file to Encodes whatever record data is passed to it as json.
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

use Exception;
use Throwable;

/**
 * Encodes whatever record data is passed to it as json
 *
 * This can be useful to log to databases or remote APIs
 */
class JsonFormatter extends NormalizerFormatter {

	const BATCH_MODE_JSON     = 1;
	const BATCH_MODE_NEWLINES = 2;

	protected $batchMode;// @codingStandardsIgnoreLine.
	protected $appendNewline;// @codingStandardsIgnoreLine.

	/**
	 * The version of this plugin .
	 *
	 * @var bool
	 */
	protected $includeStacktraces = false;// @codingStandardsIgnoreLine.

	/**
	 * The version of this plugin .
	 *
	 * @param int  $batchMode .
	 * @param bool $appendNewline .
	 */
	public function __construct( $batchMode = self::BATCH_MODE_JSON, $appendNewline = true ) { //@codingStandardsIgnoreLine
		$this->batchMode     = $batchMode;// @codingStandardsIgnoreLine.
		$this->appendNewline = $appendNewline;// @codingStandardsIgnoreLine.
	}

	/**
	 * The batch mode option configures the formatting style for
	 * multiple records. By default, multiple records will be
	 * formatted as a JSON-encoded array. However, for
	 * compatibility with some API endpoints, alternative styles
	 * are available.
	 *
	 * @return int
	 */
	public function getBatchMode() {
		return $this->batchMode;// @codingStandardsIgnoreLine.
	}

	/**
	 * True if newlines are appended to every formatted record
	 *
	 * @return bool
	 */
	public function isAppendingNewlines() {
		return $this->appendNewline;// @codingStandardsIgnoreLine.
	}

	/**
	 * The version of this plugin.
	 *
	 * @param array $record .
	 * {@inheritdoc} .
	 */
	public function format( array $record ) {
		return $this->toJson( $this->normalize( $record ), true ) . ( $this->appendNewline ? "\n" : '' );// @codingStandardsIgnoreLine.
	}

	/**
	 * This function is formatBatch.
	 *
	 * @param array $records .
	 * {@inheritdoc} .
	 */
	public function formatBatch( array $records ) {
		switch ( $this->batchMode ) {// @codingStandardsIgnoreLine.
			case static::BATCH_MODE_NEWLINES:
				return $this->formatBatchNewlines( $records );

			case static::BATCH_MODE_JSON:
			default:
				return $this->formatBatchJson( $records );
		}
	}

	/**
	 * This function is includeStacktraces .
	 *
	 * @param bool $include .
	 */
	public function includeStacktraces( $include = true ) {
		$this->includeStacktraces = $include;// @codingStandardsIgnoreLine.
	}

	/**
	 * Return a JSON-encoded array of records.
	 *
	 * @param  array $records .
	 * @return string
	 */
	protected function formatBatchJson( array $records ) {
		return $this->toJson( $this->normalize( $records ), true );
	}

	/**
	 * Use new lines to separate records instead of a
	 * JSON-encoded array.
	 *
	 * @param  array $records .
	 * @return string
	 */
	protected function formatBatchNewlines( array $records ) {
		$instance = $this;

		$oldNewline          = $this->appendNewline;// @codingStandardsIgnoreLine.
		$this->appendNewline = false;// @codingStandardsIgnoreLine.
		array_walk(
			$records, function ( &$value, $key ) use ( $instance ) {
				$value = $instance->format( $value );
			}
		);
		$this->appendNewline = $oldNewline;// @codingStandardsIgnoreLine.

		return implode( "\n", $records );
	}

	/**
	 * Normalizes given $data.
	 *
	 * @param mixed $data .
	 *
	 * @return mixed
	 */
	protected function normalize( $data ) {
		if ( is_array( $data ) || $data instanceof \Traversable ) {
			$normalized = array();

			$count = 1;
			foreach ( $data as $key => $value ) {
				if ( $count++ >= 1000 ) {
					$normalized['...'] = 'Over 1000 items, aborting normalization';
					break;
				}
				$normalized[ $key ] = $this->normalize( $value );
			}

			return $normalized;
		}

		if ( $data instanceof Exception || $data instanceof Throwable ) {
			return $this->normalizeException( $data );
		}

		return $data;
	}

	/**
	 * Normalizes given exception with or without its own stack trace based on
	 * `includeStacktraces` property.
	 *
	 * @param string $e .
	 * @throws  \InvalidArgumentException .
	 *
	 * @return array
	 */
	protected function normalizeException( $e ) {
		// TODO 2.0 only check for Throwable .
		if ( ! $e instanceof Exception && ! $e instanceof Throwable ) {
			throw new \InvalidArgumentException( 'Exception/Throwable expected, got ' . gettype( $e ) . ' / ' . get_class( $e ) );
		}

		$data = array(
			'class'   => get_class( $e ),
			'message' => $e->getMessage(),
			'code'    => $e->getCode(),
			'file'    => $e->getFile() . ':' . $e->getLine(),
		);

		if ( $this->includeStacktraces ) {// @codingStandardsIgnoreLine.
			$trace = $e->getTrace();
			foreach ( $trace as $frame ) {
				if ( isset( $frame['file'] ) ) {
					$data['trace'][] = $frame['file'] . ':' . $frame['line'];
				} elseif ( isset( $frame['function'] ) && $frame['function'] === '{closure}' ) {// @codingStandardsIgnoreLine.
					// We should again normalize the frames, because it might contain invalid items .
					$data['trace'][] = $frame['function'];
				} else {
					// We should again normalize the frames, because it might contain invalid items .
					$data['trace'][] = $this->normalize( $frame );
				}
			}
		}

		if ( $previous = $e->getPrevious() ) {// @codingStandardsIgnoreLine.
			$data['previous'] = $this->normalizeException( $previous );
		}

		return $data;
	}
}
