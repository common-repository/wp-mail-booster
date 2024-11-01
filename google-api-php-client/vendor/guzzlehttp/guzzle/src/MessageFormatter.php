<?php // @codingStandardsIgnoreLine
/**
 * This file used to  Formats log messages using variable.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

namespace GuzzleHttp;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Formats log messages using variable substitutions for requests, responses,
 * and other transactional data.
 *
 * The following variable substitutions are supported:
 *
 * - {request}:        Full HTTP request message
 * - {response}:       Full HTTP response message
 * - {ts}:             ISO 8601 date in GMT
 * - {date_iso_8601}   ISO 8601 date in GMT
 * - {date_common_log} Apache common log date using the configured timezone.
 * - {host}:           Host of the request
 * - {method}:         Method of the request
 * - {uri}:            URI of the request
 * - {host}:           Host of the request
 * - {version}:        Protocol version
 * - {target}:         Request target of the request (path + query + fragment)
 * - {hostname}:       Hostname of the machine that sent the request
 * - {code}:           Status code of the response (if available)
 * - {phrase}:         Reason phrase of the response  (if available)
 * - {error}:          Any error messages (if available)
 * - {req_header_*}:   Replace `*` with the lowercased name of a request header to add to the message
 * - {res_header_*}:   Replace `*` with the lowercased name of a response header to add to the message
 * - {req_headers}:    Request headers
 * - {res_headers}:    Response headers
 * - {req_body}:       Request body
 * - {res_body}:       Response body
 */
class MessageFormatter {

	/**
	 * Apache Common Log Format.
	 *
	 * @link http://httpd.apache.org/docs/2.4/logs.html#common
	 * @var string
	 */
	const CLF   = '{hostname} {req_header_User-Agent} - [{date_common_log}] "{method} {target} HTTP/{version}" {code} {res_header_Content-Length}';
	const DEBUG = ">>>>>>>>\n{request}\n<<<<<<<<\n{response}\n--------\n{error}";
	const SHORT = '[{ts}] "{method} {target} HTTP/{version}" {code}';
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      array    $template.
	 */
	private $template;
	/**
	 * This function is __construct.
	 *
	 * @param string $template passes parameter as template.
	 */
	public function __construct( $template = self::CLF ) {
		$this->template = $template ?: self::CLF;
	}

	/**
	 * Returns a formatted message string.
	 *
	 * @param RequestInterface  $request  Request that was sent.
	 * @param ResponseInterface $response Response that was received.
	 * @param \Exception        $error    Exception that was received.
	 *
	 * @return string
	 */
	public function format(
		RequestInterface $request,
		ResponseInterface $response = null,
		\Exception $error = null
	) {
		$cache = [];

		return preg_replace_callback(
			'/{\s*([A-Za-z_\-\.0-9]+)\s*}/',
			function ( array $matches ) use ( $request, $response, $error, &$cache ) {

				if ( isset( $cache[ $matches[1] ] ) ) {
					return $cache[ $matches[1] ];
				}

				$result = '';// @codingStandardsIgnoreLine
				switch ( $matches[1] ) {
					case 'request':
						$result = Psr7\str( $request );// @codingStandardsIgnoreLine
						break;
					case 'response':
						$result = $response ? Psr7\str( $response ) : '';// @codingStandardsIgnoreLine
						break;
					case 'req_headers':
						$result = trim(// @codingStandardsIgnoreLine
							$request->getMethod()
								. ' ' . $request->getRequestTarget()
						)
							. ' HTTP/' . $request->getProtocolVersion() . "\r\n"
							. $this->headers( $request );
						break;
					case 'res_headers':
						$result = $response ?// @codingStandardsIgnoreLine
							sprintf(
								'HTTP/%s %d %s',
								$response->getProtocolVersion(),
								$response->getStatusCode(),
								$response->getReasonPhrase()
							) . "\r\n" . $this->headers( $response )
							: 'NULL';
						break;
					case 'req_body':
						$result = $request->getBody();// @codingStandardsIgnoreLine
						break;
					case 'res_body':
						$result = $response ? $response->getBody() : 'NULL';// @codingStandardsIgnoreLine
						break;
					case 'ts':
					case 'date_iso_8601':
						$result = gmdate( 'c' );// @codingStandardsIgnoreLine
						break;
					case 'date_common_log':
						$result = date( 'd/M/Y:H:i:s O' );// @codingStandardsIgnoreLine
						break;
					case 'method':
						$result = $request->getMethod();// @codingStandardsIgnoreLine
						break;
					case 'version':
						$result = $request->getProtocolVersion();// @codingStandardsIgnoreLine
						break;
					case 'uri':
					case 'url':
						$result = $request->getUri();// @codingStandardsIgnoreLine
						break;
					case 'target':
						$result = $request->getRequestTarget();// @codingStandardsIgnoreLine
						break;
					case 'req_version':
						$result = $request->getProtocolVersion();// @codingStandardsIgnoreLine
						break;
					case 'res_version':
						$result = $response// @codingStandardsIgnoreLine
							? $response->getProtocolVersion()
							: 'NULL';
						break;
					case 'host':
						$result = $request->getHeaderLine( 'Host' );// @codingStandardsIgnoreLine
						break;
					case 'hostname':
						$result = gethostname();// @codingStandardsIgnoreLine
						break;
					case 'code':
						$result = $response ? $response->getStatusCode() : 'NULL';// @codingStandardsIgnoreLine
						break;
					case 'phrase':
						$result = $response ? $response->getReasonPhrase() : 'NULL';// @codingStandardsIgnoreLine
						break;
					case 'error':
						$result = $error ? $error->getMessage() : 'NULL';// @codingStandardsIgnoreLine
						break;
					default:
						// handle prefixed dynamic headers.
						if ( strpos( $matches[1], 'req_header_' ) === 0 ) {
							$result = $request->getHeaderLine( substr( $matches[1], 11 ) );// @codingStandardsIgnoreLine
						} elseif ( strpos( $matches[1], 'res_header_' ) === 0 ) {
							$result = $response// @codingStandardsIgnoreLine
								? $response->getHeaderLine( substr( $matches[1], 11 ) )
								: 'NULL';
						}
				}

				$cache[ $matches[1] ] = $result;
				return $result;
			},
			$this->template
		);
	}
	/**
	 * This function is headers.
	 *
	 * @param MessageInterface $message passes parameter as message.
	 */
	private function headers( MessageInterface $message ) {
		$result = '';
		foreach ( $message->getHeaders() as $name => $values ) {
			$result .= $name . ': ' . implode( ', ', $values ) . "\r\n";
		}

		return trim( $result );
	}
}
