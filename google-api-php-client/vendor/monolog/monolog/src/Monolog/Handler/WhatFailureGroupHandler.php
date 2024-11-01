<?php // @codingStandardsIgnoreLine.
/**
 * This file records to multiple handlers suppressing failures of each handler
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Handler;

/**
 * Forwards records to multiple handlers suppressing failures of each handler
 * and continuing through to give every handler a chance to succeed.
 */
class WhatFailureGroupHandler extends GroupHandler {

	/**
	 * Function handler
	 *
	 * @param array $record .
	 */
	public function handle( array $record ) {
		if ( $this->processors ) {
			foreach ( $this->processors as $processor ) {
				$record = call_user_func( $processor, $record );
			}
		}

		foreach ( $this->handlers as $handler ) {
			try {
				$handler->handle( $record );
			} catch ( \Exception $e ) { // @codingStandardsIgnoreLine.
				// What failure?
			} catch ( \Throwable $e ) { // @codingStandardsIgnoreLine.
				// What failure?
			}
		}

		return false === $this->bubble;
	}

	/**
	 * To handle batch
	 *
	 * @param array $records .
	 */
	public function handleBatch( array $records ) {
		foreach ( $this->handlers as $handler ) {
			try {
				$handler->handleBatch( $records );
			} catch ( \Exception $e ) { // @codingStandardsIgnoreLine.
				// What failure?
			} catch ( \Throwable $e ) { // @codingStandardsIgnoreLine.
				// What failure?
			}
		}
	}
}
