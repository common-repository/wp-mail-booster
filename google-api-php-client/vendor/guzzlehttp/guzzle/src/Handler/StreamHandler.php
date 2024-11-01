<?php // @codingStandardsIgnoreLine
/**
 * This file to HTTP handler that uses PHP's HTTP stream wrapper.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

namespace GuzzleHttp\Handler;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\RejectedPromise;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7;
use GuzzleHttp\TransferStats;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * HTTP handler that uses PHP's HTTP stream wrapper.
 */
class StreamHandler {

	private $lastHeaders = [];// @codingStandardsIgnoreLine

	/**
	 * Sends an HTTP request.
	 *
	 * @param RequestInterface $request Request to send.
	 * @param array            $options Request transfer options.
	 *
	 * @return PromiseInterface
	 * @throws \InvalidArgumentException On error.
	 */
	public function __invoke( RequestInterface $request, array $options ) {
		// Sleep if there is a delay specified.
		if ( isset( $options['delay'] ) ) {
			usleep( $options['delay'] * 1000 );
		}

		$startTime = isset( $options['on_stats'] ) ? microtime( true ) : null;// @codingStandardsIgnoreLine

		try {
			// Does not support the expect header.
			$request = $request->withoutHeader( 'Expect' );

			// Append a content-length header if body size is zero to match
			// cURL's behavior.
			if ( 0 === $request->getBody()->getSize() ) {
				$request = $request->withHeader( 'Content-Length', 0 );
			}

			return $this->createResponse(
				$request,
				$options,
				$this->createStream( $request, $options ),
				$startTime// @codingStandardsIgnoreLine
			);
		} catch ( \InvalidArgumentException $e ) {
			throw $e;
		} catch ( \Exception $e ) {
			// Determine if the error was a networking error.
			$message = $e->getMessage();
			// This list can probably get more comprehensive.
			if ( strpos( $message, 'getaddrinfo' ) // DNS lookup failed.
				|| strpos( $message, 'Connection refused' )
				|| strpos( $message, "couldn't connect to host" ) // error on HHVM.
			) {
				$e = new ConnectException( $e->getMessage(), $request, $e );
			}
			$e = RequestException::wrapException( $request, $e );
			$this->invokeStats( $options, $request, $startTime, null, $e );// @codingStandardsIgnoreLine

			return \GuzzleHttp\Promise\rejection_for( $e );
		}
	}

	private function invokeStats(// @codingStandardsIgnoreLine
		array $options,
		RequestInterface $request,
		$startTime,// @codingStandardsIgnoreLine
		ResponseInterface $response = null,
		$error = null
	) {
		if ( isset( $options['on_stats'] ) ) {
			$stats = new TransferStats(
				$request,
				$response,
				microtime( true ) - $startTime,// @codingStandardsIgnoreLine
				$error,
				[]
			);
			call_user_func( $options['on_stats'], $stats );
		}
	}

	private function createResponse(// @codingStandardsIgnoreLine
		RequestInterface $request,
		array $options,
		$stream,
		$startTime// @codingStandardsIgnoreLine
	) {
		$hdrs                    = $this->lastHeaders;// @codingStandardsIgnoreLine
		$this->lastHeaders       = [];// @codingStandardsIgnoreLine
		$parts                   = explode( ' ', array_shift( $hdrs ), 3 );
		$ver                     = explode( '/', $parts[0] )[1];
		$status                  = $parts[1];
		$reason                  = isset( $parts[2] ) ? $parts[2] : null;
		$headers                 = \GuzzleHttp\headers_from_lines( $hdrs );
		list ($stream, $headers) = $this->checkDecode( $options, $headers, $stream );
		$stream                  = Psr7\stream_for( $stream );
		$sink                    = $stream;

		if ( strcasecmp( 'HEAD', $request->getMethod() ) ) {
			$sink = $this->createSink( $stream, $options );
		}

		$response = new Psr7\Response( $status, $headers, $sink, $ver, $reason );

		if ( isset( $options['on_headers'] ) ) {
			try {
				$options['on_headers']($response);
			} catch ( \Exception $e ) {
				$msg = 'An error was encountered during the on_headers event';
				$ex  = new RequestException( $msg, $request, $response, $e );
				return \GuzzleHttp\Promise\rejection_for( $ex );
			}
		}

		// Do not drain when the request is a HEAD request because they have
		// no body.
		if ( $sink !== $stream ) {
			$this->drain(
				$stream,
				$sink,
				$response->getHeaderLine( 'Content-Length' )
			);
		}

		$this->invokeStats( $options, $request, $startTime, $response, null );// @codingStandardsIgnoreLine

		return new FulfilledPromise( $response );
	}
	/**
	 * This function is createSink.
	 *
	 * @param StreamInterface $stream passes parameter as stream.
	 * @param array           $options passes parameter as options.
	 */
	private function createSink( StreamInterface $stream, array $options ) {// @codingStandardsIgnoreLine
		if ( ! empty( $options['stream'] ) ) {
			return $stream;
		}

		$sink = isset( $options['sink'] )
			? $options['sink']
			: fopen( 'php://temp', 'r+' );// @codingStandardsIgnoreLine

		return is_string( $sink )
			? new Psr7\LazyOpenStream( $sink, 'w+' )
			: Psr7\stream_for( $sink );
	}

	private function checkDecode( array $options, array $headers, $stream ) {// @codingStandardsIgnoreLine
		// Automatically decode responses when instructed.
		if ( ! empty( $options['decode_content'] ) ) {
			$normalizedKeys = \GuzzleHttp\normalize_header_keys( $headers );// @codingStandardsIgnoreLine
			if ( isset( $normalizedKeys['content-encoding'] ) ) {// @codingStandardsIgnoreLine
				$encoding = $headers[ $normalizedKeys['content-encoding'] ];// @codingStandardsIgnoreLine
				if ( 'gzip' === $encoding[0] || 'deflate' === $encoding[0] ) {
					$stream                                = new Psr7\InflateStream(
						Psr7\stream_for( $stream )
					);
					$headers['x-encoded-content-encoding']
						= $headers[ $normalizedKeys['content-encoding'] ];// @codingStandardsIgnoreLine
					// Remove content-encoding header.
					unset( $headers[ $normalizedKeys['content-encoding'] ] );// @codingStandardsIgnoreLine
					// Fix content-length header.
					if ( isset( $normalizedKeys['content-length'] ) ) {// @codingStandardsIgnoreLine
						$headers['x-encoded-content-length']
							= $headers[ $normalizedKeys['content-length'] ];// @codingStandardsIgnoreLine

						$length = (int) $stream->getSize();
						if ( 0 === $length ) {
							unset( $headers[ $normalizedKeys['content-length'] ] );// @codingStandardsIgnoreLine
						} else {
							$headers[ $normalizedKeys['content-length'] ] = [ $length ];// @codingStandardsIgnoreLine
						}
					}
				}
			}
		}

		return [ $stream, $headers ];
	}

	/**
	 * Drains the source stream into the "sink" client option.
	 *
	 * @param StreamInterface $source passes parameter as source.
	 * @param StreamInterface $sink passes parameter as sink.
	 * @param string          $contentLength Header specifying the amount of
	 *                                       data to read.
	 *
	 * @return StreamInterface
	 * @throws \RuntimeException When the sink option is invalid.
	 */
	private function drain(
		StreamInterface $source,
		StreamInterface $sink,
		$contentLength// @codingStandardsIgnoreLine
	) {
		// If a content-length header is provided, then stop reading once
		// that number of bytes has been read. This can prevent infinitely
		// reading from a stream when dealing with servers that do not honor
		// Connection: Close headers.
		Psr7\copy_to_stream(
			$source,
			$sink,
			( strlen( $contentLength ) > 0 && (int) $contentLength > 0 ) ? (int) $contentLength : -1// @codingStandardsIgnoreLine
		);

		$sink->seek( 0 );
		$source->close();

		return $sink;
	}

	/**
	 * Create a resource and check to ensure it was created successfully
	 *
	 * @param callable $callback Callable that returns stream resource.
	 *
	 * @return resource
	 * @throws \RuntimeException On error.
	 */
	private function createResource( callable $callback ) {// @codingStandardsIgnoreLine
		$errors = null;
		set_error_handler(// @codingStandardsIgnoreLine
			function ( $_, $msg, $file, $line ) use ( &$errors ) {
				$errors[] = [
					'message' => $msg,
					'file'    => $file,
					'line'    => $line,
				];
				return true;
			}
		);

		$resource = $callback();
		restore_error_handler();

		if ( ! $resource ) {
			$message = 'Error creating resource: ';
			foreach ( $errors as $err ) {
				foreach ( $err as $key => $value ) {
					$message .= "[$key] $value" . PHP_EOL;
				}
			}
			throw new \RuntimeException( trim( $message ) );
		}

		return $resource;
	}

	private function createStream( RequestInterface $request, array $options ) {// @codingStandardsIgnoreLine
		static $methods;
		if ( ! $methods ) {
			$methods = array_flip( get_class_methods( __CLASS__ ) );
		}

		// HTTP/1.1 streams using the PHP stream wrapper require a
		// Connection: close header.
		if ( $request->getProtocolVersion() == '1.1'// WPCS: Loose comparison ok.
			&& ! $request->hasHeader( 'Connection' )
		) {
			$request = $request->withHeader( 'Connection', 'close' );
		}

		// Ensure SSL is verified by default.
		if ( ! isset( $options['verify'] ) ) {
			$options['verify'] = true;
		}

		$params  = [];
		$context = $this->getDefaultContext( $request, $options );

		if ( isset( $options['on_headers'] ) && ! is_callable( $options['on_headers'] ) ) {
			throw new \InvalidArgumentException( 'on_headers must be callable' );
		}

		if ( ! empty( $options ) ) {
			foreach ( $options as $key => $value ) {
				$method = "add_{$key}";
				if ( isset( $methods[ $method ] ) ) {
					$this->{$method}( $request, $context, $value, $params );
				}
			}
		}

		if ( isset( $options['stream_context'] ) ) {
			if ( ! is_array( $options['stream_context'] ) ) {
				throw new \InvalidArgumentException( 'stream_context must be an array' );
			}
			$context = array_replace_recursive(
				$context,
				$options['stream_context']
			);
		}

		// Microsoft NTLM authentication only supported with curl handler.
		if ( isset( $options['auth'] )
			&& is_array( $options['auth'] )
			&& isset( $options['auth'][2] )
			&& 'ntlm' == $options['auth'][2]// WPCS: Loose comparison ok.
		) {

			throw new \InvalidArgumentException( 'Microsoft NTLM authentication only supported with curl handler' );
		}

		$uri = $this->resolveHost( $request, $options );

		$context = $this->createResource(
			function () use ( $context, $params ) {
				return stream_context_create( $context, $params );
			}
		);

		return $this->createResource(
			function () use ( $uri, &$http_response_header, $context, $options ) {
				$resource          = fopen( (string) $uri, 'r', null, $context );// @codingStandardsIgnoreLine
				$this->lastHeaders = $http_response_header;// @codingStandardsIgnoreLine

				if ( isset( $options['read_timeout'] ) ) {
					$readTimeout = $options['read_timeout'];// @codingStandardsIgnoreLine
					$sec         = (int) $readTimeout;// @codingStandardsIgnoreLine
					$usec        = ( $readTimeout - $sec ) * 100000;// @codingStandardsIgnoreLine
					stream_set_timeout( $resource, $sec, $usec );
				}

				return $resource;
			}
		);
	}
	/**
	 * This function is resolveHost.
	 *
	 * @param RequestInterface $request passes parameter as request.
	 * @param array            $options passes parameter as options.
	 * @throws ConnectException .
	 */
	private function resolveHost( RequestInterface $request, array $options ) {// @codingStandardsIgnoreLine
		$uri = $request->getUri();

		if ( isset( $options['force_ip_resolve'] ) && ! filter_var( $uri->getHost(), FILTER_VALIDATE_IP ) ) {
			if ( 'v4' === $options['force_ip_resolve'] ) {
				$records = dns_get_record( $uri->getHost(), DNS_A );
				if ( ! isset( $records[0]['ip'] ) ) {
					throw new ConnectException( sprintf( "Could not resolve IPv4 address for host '%s'", $uri->getHost() ), $request );
				}
				$uri = $uri->withHost( $records[0]['ip'] );
			} elseif ( 'v6' === $options['force_ip_resolve'] ) {
				$records = dns_get_record( $uri->getHost(), DNS_AAAA );
				if ( ! isset( $records[0]['ipv6'] ) ) {
					throw new ConnectException( sprintf( "Could not resolve IPv6 address for host '%s'", $uri->getHost() ), $request );
				}
				$uri = $uri->withHost( '[' . $records[0]['ipv6'] . ']' );
			}
		}

		return $uri;
	}
	/**
	 * This function is getDefaultContext.
	 *
	 * @param RequestInterface $request passes parameter as request.
	 */
	private function getDefaultContext( RequestInterface $request ) {// @codingStandardsIgnoreLine
		$headers = '';
		foreach ( $request->getHeaders() as $name => $value ) {
			foreach ( $value as $val ) {
				$headers .= "$name: $val\r\n";
			}
		}

		$context = [
			'http' => [
				'method'           => $request->getMethod(),
				'header'           => $headers,
				'protocol_version' => $request->getProtocolVersion(),
				'ignore_errors'    => true,
				'follow_location'  => 0,
			],
		];

		$body = (string) $request->getBody();

		if ( ! empty( $body ) ) {
			$context['http']['content'] = $body;
			// Prevent the HTTP handler from adding a Content-Type header.
			if ( ! $request->hasHeader( 'Content-Type' ) ) {
				$context['http']['header'] .= "Content-Type:\r\n";
			}
		}

		$context['http']['header'] = rtrim( $context['http']['header'] );

		return $context;
	}
	/**
	 * This function is used to add proxy.
	 *
	 * @param RequestInterface $request passes parameter as request.
	 * @param string           $options passes parameter as options.
	 * @param string           $value passes parameter as value.
	 * @param string           $params passes parameter as params.
	 */
	private function add_proxy( RequestInterface $request, &$options, $value, &$params ) {
		if ( ! is_array( $value ) ) {
			$options['http']['proxy'] = $value;
		} else {
			$scheme = $request->getUri()->getScheme();
			if ( isset( $value[ $scheme ] ) ) {
				if ( ! isset( $value['no'] )
					|| ! \GuzzleHttp\is_host_in_noproxy(
						$request->getUri()->getHost(),
						$value['no']
					)
				) {
					$options['http']['proxy'] = $value[ $scheme ];
				}
			}
		}
	}
	/**
	 * This function is used to add proxy.
	 *
	 * @param RequestInterface $request passes parameter as request.
	 * @param string           $options passes parameter as options.
	 * @param string           $value passes parameter as value.
	 * @param string           $params passes parameter as params.
	 */
	private function add_timeout( RequestInterface $request, &$options, $value, &$params ) {
		if ( $value > 0 ) {
			$options['http']['timeout'] = $value;
		}
	}
	/**
	 * This function is used to add proxy.
	 *
	 * @param RequestInterface $request passes parameter as request.
	 * @param string           $options passes parameter as options.
	 * @param string           $value passes parameter as value.
	 * @param string           $params passes parameter as params.
	 * @throws \RuntimeException On error.
	 * @throws \InvalidArgumentException On error.
	 */
	private function add_verify( RequestInterface $request, &$options, $value, &$params ) {
		if ( true === $value ) {
			// PHP 5.6 or greater will find the system cert by default. When
			// < 5.6, use the Guzzle bundled cacert.
			if ( PHP_VERSION_ID < 50600 ) {
				$options['ssl']['cafile'] = \GuzzleHttp\default_ca_bundle();
			}
		} elseif ( is_string( $value ) ) {
			$options['ssl']['cafile'] = $value;
			if ( ! file_exists( $value ) ) {
				throw new \RuntimeException( "SSL CA bundle not found: $value" );
			}
		} elseif ( false === $value ) {
			$options['ssl']['verify_peer']      = false;
			$options['ssl']['verify_peer_name'] = false;
			return;
		} else {
			throw new \InvalidArgumentException( 'Invalid verify request option' );
		}

		$options['ssl']['verify_peer']       = true;
		$options['ssl']['verify_peer_name']  = true;
		$options['ssl']['allow_self_signed'] = false;
	}
	/**
	 * This Function is add_cert.
	 *
	 * @param RequestInterface $request passes parameter as request.
	 * @param string           $options passes parameter as options.
	 * @param string           $value passes parameter as value.
	 * @param string           $params passes parameter as params.
	 * @throws \RuntimeException On error.
	 */
	private function add_cert( RequestInterface $request, &$options, $value, &$params ) {
		if ( is_array( $value ) ) {
			$options['ssl']['passphrase'] = $value[1];
			$value                        = $value[0];
		}

		if ( ! file_exists( $value ) ) {
			throw new \RuntimeException( "SSL certificate not found: {$value}" );
		}

		$options['ssl']['local_cert'] = $value;
	}
	/**
	 * This Function is add_cert.
	 *
	 * @param RequestInterface $request passes parameter as request.
	 * @param string           $options passes parameter as options.
	 * @param string           $value passes parameter as value.
	 * @param string           $params passes parameter as params.
	 * @throws \RuntimeException On error.
	 */
	private function add_progress( RequestInterface $request, &$options, $value, &$params ) {
		$this->addNotification(
			$params,
			function ( $code, $a, $b, $c, $transferred, $total ) use ( $value ) {
				if ( STREAM_NOTIFY_PROGRESS == $code ) {// WPCS: Loose comparison ok.
					$value( $total, $transferred, null, null );
				}
			}
		);
	}
	/**
	 * This Function is add_cert.
	 *
	 * @param RequestInterface $request passes parameter as request.
	 * @param string           $options passes parameter as options.
	 * @param string           $value passes parameter as value.
	 * @param string           $params passes parameter as params.
	 * @throws \RuntimeException On error.
	 */
	private function add_debug( RequestInterface $request, &$options, $value, &$params ) {
		if ( false === $value ) {
			return;
		}

		static $map  = [
			STREAM_NOTIFY_CONNECT       => 'CONNECT',
			STREAM_NOTIFY_AUTH_REQUIRED => 'AUTH_REQUIRED',
			STREAM_NOTIFY_AUTH_RESULT   => 'AUTH_RESULT',
			STREAM_NOTIFY_MIME_TYPE_IS  => 'MIME_TYPE_IS',
			STREAM_NOTIFY_FILE_SIZE_IS  => 'FILE_SIZE_IS',
			STREAM_NOTIFY_REDIRECTED    => 'REDIRECTED',
			STREAM_NOTIFY_PROGRESS      => 'PROGRESS',
			STREAM_NOTIFY_FAILURE       => 'FAILURE',
			STREAM_NOTIFY_COMPLETED     => 'COMPLETED',
			STREAM_NOTIFY_RESOLVE       => 'RESOLVE',
		];
		static $args = [
			'severity',
			'message',
			'message_code',
			'bytes_transferred',
			'bytes_max',
		];

		$value = \GuzzleHttp\debug_resource( $value );
		$ident = $request->getMethod() . ' ' . $request->getUri()->withFragment( '' );
		$this->addNotification(
			$params,
			function () use ( $ident, $value, $map, $args ) {
				$passed = func_get_args();
				$code   = array_shift( $passed );
				fprintf( $value, '<%s> [%s] ', $ident, $map[ $code ] );
				foreach ( array_filter( $passed ) as $i => $v ) {
					fwrite( $value, $args[ $i ] . ': "' . $v . '" ' );// @codingStandardsIgnoreLine
				}
				fwrite( $value, "\n" );// @codingStandardsIgnoreLine
			}
		);
	}

	private function addNotification( array &$params, callable $notify ) {// @codingStandardsIgnoreLine
		// Wrap the existing function if needed.
		if ( ! isset( $params['notification'] ) ) {
			$params['notification'] = $notify;
		} else {
			$params['notification'] = $this->callArray(
				[
					$params['notification'],
					$notify,
				]
			);
		}
	}

	private function callArray( array $functions ) {// @codingStandardsIgnoreLine
		return function () use ( $functions ) {
			$args = func_get_args();
			foreach ( $functions as $fn ) {
				call_user_func_array( $fn, $args );
			}
		};
	}
}
