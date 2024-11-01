<?php // @codingStandardsIgnoreLine.
/**
 * This file is part of the Monolog package.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/slack
 * @version 2.0.0
 */
namespace Monolog\Handler;

/**
 * Base Handler class providing the Handler structure
 *
 * Classes extending it should (in most cases) only implement write($record)
 */
abstract class AbstractProcessingHandler extends AbstractHandler {

	/**
	 * This function is handle.
	 *
	 * @param array $record .
	 * {@inheritdoc}.
	 */
	public function handle( array $record ) {
		if ( ! $this->isHandling( $record ) ) {
			return false;
		}

		$record = $this->processRecord( $record );

		$record['formatted'] = $this->getFormatter()->format( $record );

		$this->write( $record );

		return false === $this->bubble;
	}

	/**
	 * Writes the record down to the log of the implementing handler
	 *
	 * @param  array $record .
	 * @return void
	 */
	abstract protected function write( array $record);

	/**
	 * Processes a record.
	 *
	 * @param  array $record .
	 * @return array
	 */
	protected function processRecord( array $record ) {
		if ( $this->processors ) {
			foreach ( $this->processors as $processor ) {
				$record = call_user_func( $processor, $record );
			}
		}

		return $record;
	}
}
