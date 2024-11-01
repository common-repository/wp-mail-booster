<?php // @codingStandardsIgnoreLine
/**
 * This file for Prepares requests that contain a body.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

namespace GuzzleHttp;

use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7;
use Psr\Http\Message\RequestInterface;

/**
 * Prepares requests that contain a body, adding the Content-Length,
 * Content-Type, and Expect headers.
 */
class PrepareBodyMiddleware {

	private $nextHandler;// @codingStandardsIgnoreLine

	/**
	 * This function is __construct.
	 *
	 * @param callable $nextHandler Next handler to invoke.
	 */
	public function __construct( callable $nextHandler ) {// @codingStandardsIgnoreLine
		$this->nextHandler = $nextHandler;// @codingStandardsIgnoreLine
	}

	/**
	 * This function is __invoke.
	 *
	 * @param RequestInterface $request passes parameter as request.
	 * @param array            $options passes parameter as request.
	 *
	 * @return PromiseInterface
	 */
	public function __invoke( RequestInterface $request, array $options ) {
		$fn = $this->nextHandler;// @codingStandardsIgnoreLine

		// Don't do anything if the request has no body.
		if ( $request->getBody()->getSize() === 0 ) {
			return $fn( $request, $options );
		}

		$modify = [];

		// Add a default content-type if possible.
		if ( ! $request->hasHeader( 'Content-Type' ) ) {
			if ( $uri = $request->getBody()->getMetadata( 'uri' ) ) {// @codingStandardsIgnoreLine
				if ( $type = Psr7\mimetype_from_filename( $uri ) ) {// @codingStandardsIgnoreLine
					$modify['set_headers']['Content-Type'] = $type;
				}
			}
		}

		// Add a default content-length or transfer-encoding header.
		if ( ! $request->hasHeader( 'Content-Length' )
			&& ! $request->hasHeader( 'Transfer-Encoding' )
		) {
			$size = $request->getBody()->getSize();
			if ( null !== $size ) {
				$modify['set_headers']['Content-Length'] = $size;
			} else {
				$modify['set_headers']['Transfer-Encoding'] = 'chunked';
			}
		}

		// Add the expect header if needed.
		$this->addExpectHeader( $request, $options, $modify );

		return $fn( Psr7\modify_request( $request, $modify ), $options );
	}

	private function addExpectHeader(// @codingStandardsIgnoreLine
		RequestInterface $request,
		array $options,
		array &$modify
	) {
		// Determine if the Expect header should be used.
		if ( $request->hasHeader( 'Expect' ) ) {
			return;
		}

		$expect = isset( $options['expect'] ) ? $options['expect'] : null;

		// Return if disabled or if you're not using HTTP/1.1 or HTTP/2.0.
		if ( false === $expect || $request->getProtocolVersion() < 1.1 ) {
			return;
		}

		// The expect header is unconditionally enabled.
		if ( true === $expect ) {
			$modify['set_headers']['Expect'] = '100-Continue';
			return;
		}

		// By default, send the expect header when the payload is > 1mb.
		if ( null === $expect ) {
			$expect = 1048576;
		}

		// Always add if the body cannot be rewound, the size cannot be
		// determined, or the size is greater than the cutoff threshold.
		$body = $request->getBody();
		$size = $body->getSize();

		if ( null === $size || $size >= (int) $expect || ! $body->isSeekable() ) {
			$modify['set_headers']['Expect'] = '100-Continue';
		}
	}
}
