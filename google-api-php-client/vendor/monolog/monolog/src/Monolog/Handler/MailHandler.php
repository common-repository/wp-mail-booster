<?php // @codingStandardsIgnoreLine.
/**
 * This file is part of the Monolog package.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/handler
 * @version 2.0.0
 */

namespace Monolog\Handler;

/**
 * Base class for all mail handlers
 */
abstract class MailHandler extends AbstractProcessingHandler {

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
			$this->send( (string) $this->getFormatter()->formatBatch( $messages ), $messages );
		}
	}

	/**
	 * Send a mail with the given content
	 *
	 * @param string $content formatted email body to be sent .
	 * @param array  $records the array of log records that formed this content .
	 */
	abstract protected function send( $content, array $records);

	/**
	 * This function is handleBatch.
	 *
	 * @param array $record .
	 * {@inheritdoc}.
	 */
	protected function write( array $record ) {
		$this->send( (string) $record['formatted'], array( $record ) );
	}
	/**
	 * This function is handleBatch.
	 *
	 * @param array $records .
	 * {@inheritdoc}.
	 */
	protected function getHighestRecord( array $records ) {
		$highestRecord = null;// @codingStandardsIgnoreLine.
		foreach ( $records as $record ) {
			if ( $highestRecord === null || $highestRecord['level'] < $record['level'] ) {// @codingStandardsIgnoreLine.
				$highestRecord = $record;// @codingStandardsIgnoreLine.
			}
		}

		return $highestRecord;// @codingStandardsIgnoreLine.
	}
}
