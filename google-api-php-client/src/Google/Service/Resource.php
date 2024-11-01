<?php // @codingStandardsIgnoreLine
/**
 * This file is used to Implement the actual methods/resources of the discovered Google API.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/src/google/services
 * @version 2.0.0
 */

/**
 * Copyright 2010 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

use GuzzleHttp\Psr7\Request;

/**
 * Implements the actual methods/resources of the discovered Google API using magic function
 * calling overloading (__call()), which on call will see if the method name (plus.activities.list)
 * is available in this service, and if so construct an apiHttpRequest representing it.
 */
class Google_Service_Resource {

	/**
	 * Valid query parameters that work, but don't appear in discovery.
	 *
	 * @var $stackParameters .
	 */
	private $stackParameters = array( // @codingStandardsIgnoreLine
		'alt'         => array(
			'type'     => 'string',
			'location' => 'query',
		),
		'fields'      => array(
			'type'     => 'string',
			'location' => 'query',
		),
		'trace'       => array(
			'type'     => 'string',
			'location' => 'query',
		),
		'userIp'      => array(
			'type'     => 'string',
			'location' => 'query',
		),
		'quotaUser'   => array(
			'type'     => 'string',
			'location' => 'query',
		),
		'data'        => array(
			'type'     => 'string',
			'location' => 'body',
		),
		'mimeType'    => array(
			'type'     => 'string',
			'location' => 'header',
		),
		'uploadType'  => array(
			'type'     => 'string',
			'location' => 'query',
		),
		'mediaUpload' => array(
			'type'     => 'complex',
			'location' => 'query',
		),
		'prettyPrint' => array(
			'type'     => 'string',
			'location' => 'query',
		),
	);

	/**
	 * Variable for root url
	 *
	 * @var string $rootUrl
	 */
	private $rootUrl; // @codingStandardsIgnoreLine

	/**
	 * Variablefor google client
	 *
	 * @var Google_Client $client
	 */
	private $client;

	/**
	 * This variable for service name
	 *
	 * @var string $serviceName
	 */
	private $serviceName; // @codingStandardsIgnoreLine

	/**
	 * VAriable for service path
	 *
	 * @var string $servicePath
	 */
	private $servicePath; // @codingStandardsIgnoreLine

	/**
	 * Variable for resource name
	 *
	 * @var string $resourceName
	 */
	private $resourceName; // @codingStandardsIgnoreLine

	/**
	 * This variable for method
	 *
	 * @var array $methods
	 */
	private $methods;
	/**
	 * Public constructor
	 *
	 * @param string $service .
	 * @param string $serviceName .
	 * @param string $resourceName .
	 * @param string $resource .
	 */
	public function __construct( $service, $serviceName, $resourceName, $resource ) { // @codingStandardsIgnoreLine
		$this->rootUrl      = $service->rootUrl; // @codingStandardsIgnoreLine
		$this->client       = $service->getClient();
		$this->servicePath  = $service->servicePath; // @codingStandardsIgnoreLine
		$this->serviceName  = $serviceName; // @codingStandardsIgnoreLine
		$this->resourceName = $resourceName; // @codingStandardsIgnoreLine
		$this->methods      = is_array( $resource ) && isset( $resource['methods'] ) ?
		$resource['methods'] :
		array( $resourceName => $resource ); // @codingStandardsIgnoreLine
	}

	/**
	 * TODO: This function needs simplifying.
	 *
	 * @param string $name .
	 * @param string $arguments .
	 * @param string $expectedClass - optional, the expected class name .
	 * @return Google_Http_Request|expectedClass
	 * @throws Google_Exception .
	 */
	public function call( $name, $arguments, $expectedClass = null ) { // @codingStandardsIgnoreLine
		if ( ! isset( $this->methods[ $name ] ) ) {
			$this->client->getLogger()->error(
				'Service method unknown',
				array(
					'service'  => $this->serviceName, // @codingStandardsIgnoreLine
					'resource' => $this->resourceName, // @codingStandardsIgnoreLine
					'method'   => $name,
				)
			);

			throw new Google_Exception(
				'Unknown function: ' .
				"{$this->serviceName}->{$this->resourceName}->{$name}()"
			);
		}
		$method     = $this->methods[ $name ];
		$parameters = $arguments[0];

		// postBody is a special case since it's not defined in the discovery
		// document as parameter, but we abuse the param entry for storing it.
		$postBody = null; // @codingStandardsIgnoreLine
		if ( isset( $parameters['postBody'] ) ) {
			if ( $parameters['postBody'] instanceof Google_Model ) {
				// In the cases the post body is an existing object, we want
				// to use the smart method to create a simple object for
				// for JSONification.
				$parameters['postBody'] = $parameters['postBody']->toSimpleObject();
			} elseif ( is_object( $parameters['postBody'] ) ) {
				// If the post body is another kind of object, we will try and
				// wrangle it into a sensible format.
				$parameters['postBody'] =
				$this->convertToArrayAndStripNulls( $parameters['postBody'] );
			}
			$postBody = (array) $parameters['postBody']; // @codingStandardsIgnoreLine
			unset( $parameters['postBody'] );
		}

		// TODO: optParams here probably should have been
		// handled already - this may well be redundant code.
		if ( isset( $parameters['optParams'] ) ) {
			$optParams = $parameters['optParams']; // @codingStandardsIgnoreLine
			unset( $parameters['optParams'] );
			$parameters = array_merge( $parameters, $optParams ); // @codingStandardsIgnoreLine
		}

		if ( ! isset( $method['parameters'] ) ) {
			$method['parameters'] = array();
		}

		$method['parameters'] = array_merge(
			$this->stackParameters, // @codingStandardsIgnoreLine
			$method['parameters']
		);

		foreach ( $parameters as $key => $val ) {
			if ( 'postBody' != $key && ! isset( $method['parameters'][ $key ] ) ) { // WPCS:Loose comparison ok .
				$this->client->getLogger()->error(
					'Service parameter unknown',
					array(
						'service'   => $this->serviceName, // @codingStandardsIgnoreLine
						'resource'  => $this->resourceName, // @codingStandardsIgnoreLine
						'method'    => $name,
						'parameter' => $key,
					)
				);
				throw new Google_Exception( "($name) unknown parameter: '$key'" );
			}
		}

		foreach ( $method['parameters'] as $paramName => $paramSpec ) { // @codingStandardsIgnoreLine
			if ( isset( $paramSpec['required'] ) && // @codingStandardsIgnoreLine
			$paramSpec['required'] && // @codingStandardsIgnoreLine
			! isset( $parameters[ $paramName ] ) // @codingStandardsIgnoreLine
			) {
				$this->client->getLogger()->error(
					'Service parameter missing',
					array(
						'service'   => $this->serviceName, // @codingStandardsIgnoreLine
						'resource'  => $this->resourceName, // @codingStandardsIgnoreLine
						'method'    => $name,
						'parameter' => $paramName, // @codingStandardsIgnoreLine
					)
				);
				throw new Google_Exception( "($name) missing required param: '$paramName'" ); // @codingStandardsIgnoreLine
			}
			if ( isset( $parameters[ $paramName ] ) ) { // @codingStandardsIgnoreLine
				$value                             = $parameters[ $paramName ]; // @codingStandardsIgnoreLine
				$parameters[ $paramName ]          = $paramSpec; // @codingStandardsIgnoreLine
				$parameters[ $paramName ]['value'] = $value; // @codingStandardsIgnoreLine
				unset( $parameters[ $paramName ]['required'] ); // @codingStandardsIgnoreLine
			} else {
				// Ensure we don't pass nulls.
				unset( $parameters[ $paramName ] ); // @codingStandardsIgnoreLine
			}
		}

		$this->client->getLogger()->info(
			'Service Call',
			array(
				'service'   => $this->serviceName, // @codingStandardsIgnoreLine
				'resource'  => $this->resourceName, // @codingStandardsIgnoreLine
				'method'    => $name,
				'arguments' => $parameters,
			)
		);

		// build the service uri .
		$url = $this->createRequestUri(
			$method['path'],
			$parameters
		);

		// NOTE: because we're creating the request by hand,
		// and because the service has a rootUrl property
		// the "base_uri" of the Http Client is not accounted for .
		$request = new Request(
			$method['httpMethod'],
			$url,
			[ 'content-type' => 'application/json' ],
			$postBody ? json_encode( $postBody ) : '' // @codingStandardsIgnoreLine
		);

		// support uploads .
		if ( isset( $parameters['data'] ) ) {
			$mimeType = isset( $parameters['mimeType'] )  // @codingStandardsIgnoreLine
			? $parameters['mimeType']['value']
			: 'application/octet-stream';
			$data     = $parameters['data']['value'];
			$upload   = new Google_Http_MediaFileUpload( $this->client, $request, $mimeType, $data ); // @codingStandardsIgnoreLine

			// pull down the modified request .
			$request = $upload->getRequest();
		}

		// if this is a media type, we will return the raw response
		// rather than using an expected class .
		if ( isset( $parameters['alt'] ) && 'media' == $parameters['alt']['value'] ) { // WPCS:Loose comparison ok .
			$expectedClass = null; // @codingStandardsIgnoreLine
		}

		// if the client is marked for deferring, rather than
		// execute the request, return the response .
		if ( $this->client->shouldDefer() ) {
			// @TODO find a better way to do this
			$request = $request
			->withHeader( 'X-Php-Expected-Class', $expectedClass ); // @codingStandardsIgnoreLine

			return $request;
		}

		return $this->client->execute( $request, $expectedClass ); // @codingStandardsIgnoreLine
	}

	/**
	 * This function is use to convert to arrray and strip null
	 *
	 * @param string $o .
	 */
	protected function convertToArrayAndStripNulls( $o ) { // @codingStandardsIgnoreLine
		$o = (array) $o;
		foreach ( $o as $k => $v ) {
			if ( null === $v ) {
				unset( $o[ $k ] );
			} elseif ( is_object( $v ) || is_array( $v ) ) {
				$o[ $k ] = $this->convertToArrayAndStripNulls( $o[ $k ] );
			}
		}
		return $o;
	}

	/**
	 * Parse/expand request parameters and create a fully qualified
	 * request uri.
	 *
	 * @static
	 * @param string $restPath .
	 * @param array  $params .
	 * @return string $requestUrl .
	 */
	public function createRequestUri( $restPath, $params ) { // @codingStandardsIgnoreLine
		// code for leading slash .
		$requestUrl = $this->servicePath . $restPath; // @codingStandardsIgnoreLine
		if ( $this->rootUrl ) { // @codingStandardsIgnoreLine
			if ( '/' !== substr( $this->rootUrl, -1 ) && '/' !== substr( $requestUrl, 0, 1 ) ) { // @codingStandardsIgnoreLine
				$requestUrl = '/' . $requestUrl; // @codingStandardsIgnoreLine
			}
			$requestUrl = $this->rootUrl . $requestUrl; // @codingStandardsIgnoreLine
		}
		$uriTemplateVars = array(); // @codingStandardsIgnoreLine
		$queryVars       = array(); // @codingStandardsIgnoreLine
		foreach ( $params as $paramName => $paramSpec ) { // @codingStandardsIgnoreLine
			if ( $paramSpec['type'] == 'boolean' ) { // @codingStandardsIgnoreLine
				$paramSpec['value'] = $paramSpec['value'] ? 'true' : 'false'; // @codingStandardsIgnoreLine
			}
			if ( $paramSpec['location'] == 'path' ) { // @codingStandardsIgnoreLine
				$uriTemplateVars[ $paramName ] = $paramSpec['value']; // @codingStandardsIgnoreLine
			} elseif ( $paramSpec['location'] == 'query' ) { // @codingStandardsIgnoreLine
				if ( isset( $paramSpec['repeated'] ) && is_array( $paramSpec['value'] ) ) { // @codingStandardsIgnoreLine
					foreach ( $paramSpec['value'] as $value ) { // @codingStandardsIgnoreLine
						$queryVars[] = $paramName . '=' . rawurlencode( rawurldecode( $value ) ); // @codingStandardsIgnoreLine
					}
				} else {
					$queryVars[] = $paramName . '=' . rawurlencode( rawurldecode( $paramSpec['value'] ) ); // @codingStandardsIgnoreLine
				}
			}
		}

		if ( count( $uriTemplateVars ) ) { // @codingStandardsIgnoreLine
			$uriTemplateParser = new Google_Utils_UriTemplate(); // @codingStandardsIgnoreLine
			$requestUrl        = $uriTemplateParser->parse( $requestUrl, $uriTemplateVars ); // @codingStandardsIgnoreLine
		}

		if ( count( $queryVars ) ) { // @codingStandardsIgnoreLine
			$requestUrl .= '?' . implode( $queryVars, '&' ); // @codingStandardsIgnoreLine
		}

		return $requestUrl; // @codingStandardsIgnoreLine
	}
}
