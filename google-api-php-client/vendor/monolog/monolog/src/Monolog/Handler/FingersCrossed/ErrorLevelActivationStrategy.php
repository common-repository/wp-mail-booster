<?php // @codingStandardsIgnoreLine.
/**
 * This file is part of the Monolog package.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/fingercrossed
 * @version 2.0.0
 */

namespace Monolog\Handler\FingersCrossed;

use Monolog\Logger;

/**
 * Error level based activation strategy.
 */
class ErrorLevelActivationStrategy implements ActivationStrategyInterface {

	private $actionLevel;// @codingStandardsIgnoreLine.

	public function __construct( $actionLevel ) {// @codingStandardsIgnoreLine.
		$this->actionLevel = Logger::toMonologLevel( $actionLevel );// @codingStandardsIgnoreLine.
	}
	/**
	 * This function is isHandlerActivated.
	 *
	 * @param array $record .
	 * {@inheritdoc}.
	 */
	public function isHandlerActivated( array $record ) {
		return $record['level'] >= $this->actionLevel;// @codingStandardsIgnoreLine.
	}
}
