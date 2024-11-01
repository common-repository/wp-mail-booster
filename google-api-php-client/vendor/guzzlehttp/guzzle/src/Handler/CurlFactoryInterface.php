<?php // @codingStandardsIgnoreLine
/**
 * This file for curl interface handler
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

namespace GuzzleHttp\Handler;

use Psr\Http\Message\RequestInterface;

interface CurlFactoryInterface {

	/**
	 * Creates a cURL handle resource.
	 *
	 * @param RequestInterface $request Request.
	 * @param array            $options Transfer options.
	 *
	 * @return EasyHandle
	 * @throws \RuntimeException When an option cannot be applied.
	 */
	public function create( RequestInterface $request, array $options);

	/**
	 * Release an easy handle, allowing it to be reused or closed.
	 *
	 * This function must call unset on the easy handle's "handle" property.
	 *
	 * @param EasyHandle $easy passes parameter as easy.
	 */
	public function release( EasyHandle $easy);
}
