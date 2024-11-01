<?php // @codingStandardsIgnoreLine.
/**
 * This file to record a log on a NewRelic application.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Handler;

use Monolog\Logger;
use Monolog\Formatter\NormalizerFormatter;

/**
 * Class to record a log on a NewRelic application.
 * Enabling New Relic High Security mode may prevent capture of useful information.
 */
class NewRelicHandler extends AbstractProcessingHandler {

	/**
	 * Name of the New Relic application that will receive logs from this handler.
	 *
	 * @var string
	 */
	protected $appName; // @codingStandardsIgnoreLine.

	/**
	 * Name of the current transaction
	 *
	 * @var string
	 */
	protected $transactionName; // @codingStandardsIgnoreLine.

	/**
	 * Some context and extra data is passed into the handler as arrays of values. Do we send them as is
	 * (useful if we are using the API), or explode them for display on the NewRelic RPM website?
	 *
	 * @var bool
	 */
	protected $explodeArrays; // @codingStandardsIgnoreLine.

	/**
	 * Public constructor
	 *
	 * @param string $level .
	 * @param string $bubble .
	 * @param string $appName .
	 * @param bool   $explodeArrays .
	 * @param string $transactionName .
	 */
	public function __construct(
		$level = Logger::ERROR,
		$bubble = true,
		$appName = null, // @codingStandardsIgnoreLine.
		$explodeArrays = false, // @codingStandardsIgnoreLine.
		$transactionName = null // @codingStandardsIgnoreLine.
	) {
		parent::__construct( $level, $bubble );

		$this->appName         = $appName; // @codingStandardsIgnoreLine.
		$this->explodeArrays   = $explodeArrays; // @codingStandardsIgnoreLine.
		$this->transactionName = $transactionName; // @codingStandardsIgnoreLine.
	}

	/**
	 * Function to write record
	 *
	 * @param array $record .
	 * @throws MissingExtensionException .
	 */
	protected function write( array $record ) {
		if ( ! $this->isNewRelicEnabled() ) {
			throw new MissingExtensionException( 'The newrelic PHP extension is required to use the NewRelicHandler' );
		}
		if ( $appName = $this->getAppName( $record['context'] ) ) { // @codingStandardsIgnoreLine.
			$this->setNewRelicAppName( $appName ); // @codingStandardsIgnoreLine.
		}
		if ( $transactionName = $this->getTransactionName( $record['context'] ) ) { // @codingStandardsIgnoreLine.
			$this->setNewRelicTransactionName( $transactionName ); // @codingStandardsIgnoreLine.
			unset( $record['formatted']['context']['transaction_name'] );
		}

		if ( isset( $record['context']['exception'] ) && $record['context']['exception'] instanceof \Exception ) {
			newrelic_notice_error( $record['message'], $record['context']['exception'] );
			unset( $record['formatted']['context']['exception'] );
		} else {
			newrelic_notice_error( $record['message'] );
		}

		if ( isset( $record['formatted']['context'] ) && is_array( $record['formatted']['context'] ) ) {
			foreach ( $record['formatted']['context'] as $key => $parameter ) {
				if ( is_array( $parameter ) && $this->explodeArrays ) { // @codingStandardsIgnoreLine.
					foreach ( $parameter as $paramKey => $paramValue ) { // @codingStandardsIgnoreLine.
						$this->setNewRelicParameter( 'context_' . $key . '_' . $paramKey, $paramValue ); // @codingStandardsIgnoreLine.
					}
				} else {
					$this->setNewRelicParameter( 'context_' . $key, $parameter );
				}
			}
		}

		if ( isset( $record['formatted']['extra'] ) && is_array( $record['formatted']['extra'] ) ) {
			foreach ( $record['formatted']['extra'] as $key => $parameter ) {
				if ( is_array( $parameter ) && $this->explodeArrays ) { // @codingStandardsIgnoreLine.
					foreach ( $parameter as $paramKey => $paramValue ) { // @codingStandardsIgnoreLine.
						$this->setNewRelicParameter( 'extra_' . $key . '_' . $paramKey, $paramValue ); // @codingStandardsIgnoreLine.
					}
				} else {
					$this->setNewRelicParameter( 'extra_' . $key, $parameter );
				}
			}
		}
	}

	/**
	 * Checks whether the NewRelic extension is enabled in the system.
	 *
	 * @return bool
	 */
	protected function isNewRelicEnabled() {
		return extension_loaded( 'newrelic' );
	}

	/**
	 * Returns the appname where this log should be sent. Each log can override the default appname, set in this
	 * handler's constructor, by providing the appname in it's context.
	 *
	 * @param  array $context .
	 * @return null|string
	 */
	protected function getAppName( array $context ) {
		if ( isset( $context['appname'] ) ) {
			return $context['appname'];
		}

		return $this->appName; // @codingStandardsIgnoreLine.
	}

	/**
	 * Returns the name of the current transaction. Each log can override the default transaction name, set in this
	 * handler's constructor, by providing the transaction_name in it's context
	 *
	 * @param array $context .
	 *
	 * @return null|string
	 */
	protected function getTransactionName( array $context ) {
		if ( isset( $context['transaction_name'] ) ) {
			return $context['transaction_name'];
		}
		return $this->transactionName; // @codingStandardsIgnoreLine.
	}

	/**
	 * Sets the NewRelic application that should receive this log.
	 *
	 * @param string $appName .
	 */
	protected function setNewRelicAppName( $appName ) { // @codingStandardsIgnoreLine.
		newrelic_set_appname( $appName ); // @codingStandardsIgnoreLine.
	}

	/**
	 * Overwrites the name of the current transaction
	 *
	 * @param string $transactionName .
	 */
	protected function setNewRelicTransactionName( $transactionName ) { // @codingStandardsIgnoreLine.
		newrelic_name_transaction( $transactionName ); // @codingStandardsIgnoreLine.
	}

	/**
	 * Function to set new relic parameter
	 *
	 * @param string $key .
	 * @param mixed  $value .
	 */
	protected function setNewRelicParameter( $key, $value ) {
		if ( null === $value || is_scalar( $value ) ) {
			newrelic_add_custom_parameter( $key, $value );
		} else {
			newrelic_add_custom_parameter( $key, @json_encode( $value ) ); // @codingStandardsIgnoreLine.
		}
	}

	/**
	 * {@inheritDoc}
	 */
	protected function getDefaultFormatter() {
		return new NormalizerFormatter();
	}
}
