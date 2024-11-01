<?php // @codingStandardsIgnoreLine.
/**
 * This Template is PromisorInterface.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client\vendor\guzzlehttp\promises\src
 * @version 2.0.0
 */
namespace GuzzleHttp\Promise;

/**
 * Interface used with classes that return a promise.
 */
interface PromisorInterface {

	/**
	 * Returns a promise.
	 *
	 * @return PromiseInterface
	 */
	public function promise();
}
