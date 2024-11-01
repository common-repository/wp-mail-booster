<?php // @codingStandardsIgnoreLine.
/**
 * This file is part of the Monolog package.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/fingercrossed.
 * @version 2.0.0
 */

namespace Monolog\Handler\FingersCrossed;

/**
 * Interface for activation strategies for the FingersCrossedHandler.
 */
interface ActivationStrategyInterface {

	/**
	 * Returns whether the given record activates the handler.
	 *
	 * @param  array $record .
	 * @return Boolean
	 */
	public function isHandlerActivated( array $record);// @codingStandardsIgnoreLine.
}
