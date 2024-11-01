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
 * Channel and Error level based monolog activation strategy. Allows to trigger activation
 * based on level per channel. e.g. trigger activation on level 'ERROR' by default, except
 * for records of the 'sql' channel; those should trigger activation on level 'WARN'.
 *
 * Example:
 *
 * <code>
 *   $activationStrategy = new ChannelLevelActivationStrategy(
 *       Logger::CRITICAL,
 *       array(
 *           'request' => Logger::ALERT,
 *           'sensitive' => Logger::ERROR,
 *       )
 *   );
 *   $handler = new FingersCrossedHandler(new StreamHandler('php://stderr'), $activationStrategy);
 * </code>
 */
class ChannelLevelActivationStrategy implements ActivationStrategyInterface {

	private $defaultActionLevel;// @codingStandardsIgnoreLine.
	private $channelToActionLevel;// @codingStandardsIgnoreLine.

	/**
	 * This function is __construct.
	 *
	 * @param int   $defaultActionLevel   The default action level to be used if the record's category doesn't match any .
	 * @param array $channelToActionLevel An array that maps channel names to action levels.
	 */
	public function __construct( $defaultActionLevel, $channelToActionLevel = array() ) {// @codingStandardsIgnoreLine.
		$this->defaultActionLevel   = Logger::toMonologLevel( $defaultActionLevel );// @codingStandardsIgnoreLine.
		$this->channelToActionLevel = array_map( 'Monolog\Logger::toMonologLevel', $channelToActionLevel );// @codingStandardsIgnoreLine.
	}
	/**
	 * This function is isHandlerActivated.
	 *
	 * @param array $record .
	 * {@inheritdoc}.
	 */
	public function isHandlerActivated( array $record ) {
		if ( isset( $this->channelToActionLevel[ $record['channel'] ] ) ) {// @codingStandardsIgnoreLine.
			return $record['level'] >= $this->channelToActionLevel[ $record['channel'] ];// @codingStandardsIgnoreLine.
		}

		return $record['level'] >= $this->defaultActionLevel;// @codingStandardsIgnoreLine.
	}
}
