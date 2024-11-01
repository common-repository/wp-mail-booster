<?php // @codingStandardsIgnoreLine.
/**
 * This file is part of the Monolog package.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/handler
 * @version 2.0.0
 */
namespace Monolog\Handler;

use Monolog\Logger;

/**
 * Simple handler wrapper that filters records based on a list of levels
 *
 * It can be configured with an exact list of levels to allow, or a min/max level.
 */
class FilterHandler extends AbstractHandler {

	/**
	 * Handler or factory callable($record, $this)
	 *
	 * @var callable|\Monolog\Handler\HandlerInterface
	 */
	protected $handler;

	/**
	 * Minimum level for logs that are passed to handler
	 *
	 * @var int[]
	 */
	protected $acceptedLevels;// @codingStandardsIgnoreLine.

	/**
	 * Whether the messages that are handled can bubble up the stack or not
	 *
	 * @var Boolean
	 */
	protected $bubble;

	/**
	 * This function is __construct.
	 *
	 * @param callable|HandlerInterface $handler        Handler or factory callable($record, $this).
	 * @param int|array                 $minLevelOrList A list of levels to accept or a minimum level if maxLevel is provided .
	 * @param int                       $maxLevel       Maximum level to accept, only used if $minLevelOrList is not an array .
	 * @param Boolean                   $bubble         Whether the messages that are handled can bubble up the stack or not .
	 * @throws \RuntimeException .
	 */
	public function __construct( $handler, $minLevelOrList = Logger::DEBUG, $maxLevel = Logger::EMERGENCY, $bubble = true ) {// @codingStandardsIgnoreLine.
		$this->handler = $handler;
		$this->bubble  = $bubble;
		$this->setAcceptedLevels( $minLevelOrList, $maxLevel );// @codingStandardsIgnoreLine.

		if ( ! $this->handler instanceof HandlerInterface && ! is_callable( $this->handler ) ) {
			throw new \RuntimeException( 'The given handler (' . json_encode( $this->handler ) . ') is not a callable nor a Monolog\Handler\HandlerInterface object' );// @codingStandardsIgnoreLine.
		}
	}

	/**
	 * This function is getAcceptedLevels.
	 *
	 * @return array
	 */
	public function getAcceptedLevels() {
		return array_flip( $this->acceptedLevels );// @codingStandardsIgnoreLine.
	}

	/**
	 * This function is setAcceptedLevels .
	 *
	 * @param int|string|array $minLevelOrList A list of levels to accept or a minimum level or level name if maxLevel is provided .
	 * @param int|string       $maxLevel       Maximum level or level name to accept, only used if $minLevelOrList is not an array .
	 */
	public function setAcceptedLevels( $minLevelOrList = Logger::DEBUG, $maxLevel = Logger::EMERGENCY ) {// @codingStandardsIgnoreLine.
		if ( is_array( $minLevelOrList ) ) {// @codingStandardsIgnoreLine.
			$acceptedLevels = array_map( 'Monolog\Logger::toMonologLevel', $minLevelOrList );// @codingStandardsIgnoreLine.
		} else {
			$minLevelOrList = Logger::toMonologLevel( $minLevelOrList );// @codingStandardsIgnoreLine.
			$maxLevel       = Logger::toMonologLevel( $maxLevel );// @codingStandardsIgnoreLine.
			$acceptedLevels = array_values(// @codingStandardsIgnoreLine.
				array_filter(
					Logger::getLevels(), function ( $level ) use ( $minLevelOrList, $maxLevel ) {// @codingStandardsIgnoreLine.
						return $level >= $minLevelOrList && $level <= $maxLevel;// @codingStandardsIgnoreLine.
					}
				)
			);
		}
		$this->acceptedLevels = array_flip( $acceptedLevels );// @codingStandardsIgnoreLine.
	}

	/**
	 * This function is isHandling.
	 *
	 * @param array $record .
	 * {@inheritdoc}.
	 */
	public function isHandling( array $record ) {
		return isset( $this->acceptedLevels[ $record['level'] ] );// @codingStandardsIgnoreLine.
	}

	/**
	 * This function is isHandling.
	 *
	 * @param array $record .
	 * @throws \RuntimeException .
	 * {@inheritdoc}.
	 */
	public function handle( array $record ) {
		if ( ! $this->isHandling( $record ) ) {
			return false;
		}

		// The same logic as in FingersCrossedHandler .
		if ( ! $this->handler instanceof HandlerInterface ) {
			$this->handler = call_user_func( $this->handler, $record, $this );
			if ( ! $this->handler instanceof HandlerInterface ) {
				throw new \RuntimeException( 'The factory callable should return a HandlerInterface' );
			}
		}

		if ( $this->processors ) {
			foreach ( $this->processors as $processor ) {
				$record = call_user_func( $processor, $record );
			}
		}

		$this->handler->handle( $record );

		return false === $this->bubble;
	}

	/**
	 * This function is isHandling.
	 *
	 * @param array $records .
	 * {@inheritdoc}.
	 */
	public function handleBatch( array $records ) {
		$filtered = array();
		foreach ( $records as $record ) {
			if ( $this->isHandling( $record ) ) {
				$filtered[] = $record;
			}
		}

		$this->handler->handleBatch( $filtered );
	}
}
