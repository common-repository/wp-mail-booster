<?php // @codingStandardsIgnoreLine
/**
 * This file to Stream that when read returns bytes for a streaming multipart.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor/composer
 * @version 2.0.0
 */

namespace GuzzleHttp\Psr7;

use Psr\Http\Message\StreamInterface;

/**
 * Stream that when read returns bytes for a streaming multipart or
 * multipart/form-data stream.
 */
class MultipartStream implements StreamInterface {

	use StreamDecoratorTrait;
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $boundary  .
	 */
	private $boundary;

	/**
	 * This function is __construct.
	 *
	 * @param array  $elements Array of associative arrays, each containing a.
	 * @param string $boundary You can optionally provide a specific boundary.
	 * @throws \InvalidArgumentException On error.
	 */
	public function __construct( array $elements = [], $boundary = null ) {
		$this->boundary = $boundary ?: sha1( uniqid( '', true ) );
		$this->stream   = $this->createStream( $elements );
	}

	/**
	 * Get the boundary
	 *
	 * @return string
	 */
	public function getBoundary() {
		return $this->boundary;
	}
	/**
	 * This function is isWritable.
	 *
	 * @return string
	 */
	public function isWritable() {
		return false;
	}

	/**
	 * Get the headers needed before transferring the content of a POST file.
	 *
	 * @param array $headers passes parameter as headers.
	 */
	private function getHeaders( array $headers ) {
		$str = '';
		foreach ( $headers as $key => $value ) {
			$str .= "{$key}: {$value}\r\n";
		}

		return "--{$this->boundary}\r\n" . trim( $str ) . "\r\n\r\n";
	}

	/**
	 * Create the aggregate stream that will be used to upload the POST data
	 *
	 * @param array $elements passes parameter as elements.
	 */
	protected function createStream( array $elements ) {
		$stream = new AppendStream();

		foreach ( $elements as $element ) {
			$this->addElement( $stream, $element );
		}

		// Add the trailing boundary with CRLF.
		$stream->addStream( stream_for( "--{$this->boundary}--\r\n" ) );

		return $stream;
	}
	/**
	 * Create the aggregate stream that will be used to upload the POST data
	 *
	 * @param AppendStream $stream passes parameter as stream.
	 * @param array        $element passes parameter as element.
	 * @throws \InvalidArgumentException .
	 */
	private function addElement( AppendStream $stream, array $element ) {
		foreach ( [ 'contents', 'name' ] as $key ) {
			if ( ! array_key_exists( $key, $element ) ) {
				throw new \InvalidArgumentException( "A '{$key}' key is required" );
			}
		}

		$element['contents'] = stream_for( $element['contents'] );

		if ( empty( $element['filename'] ) ) {
			$uri = $element['contents']->getMetadata( 'uri' );
			if ( substr( $uri, 0, 6 ) !== 'php://' ) {
				$element['filename'] = $uri;
			}
		}

		list($body, $headers) = $this->createElement(
			$element['name'],
			$element['contents'],
			isset( $element['filename'] ) ? $element['filename'] : null,
			isset( $element['headers'] ) ? $element['headers'] : []
		);

		$stream->addStream( stream_for( $this->getHeaders( $headers ) ) );
		$stream->addStream( $body );
		$stream->addStream( stream_for( "\r\n" ) );
	}

	/**
	 * This function is createElement.
	 *
	 * @param string          $name passes parameter as name.
	 * @param StreamInterface $stream passes parameter as stream.
	 * @param string          $filename passes parameter as filename.
	 * @param array           $headers passes parameter as headers.
	 * @return array
	 */
	private function createElement( $name, StreamInterface $stream, $filename, array $headers ) {
		// Set a default content-disposition header if one was no provided.
		$disposition = $this->getHeader( $headers, 'content-disposition' );
		if ( ! $disposition ) {
			$headers['Content-Disposition'] = ( '0' === $filename || $filename )
				? sprintf(
					'form-data; name="%s"; filename="%s"',
					$name,
					basename( $filename )
				)
				: "form-data; name=\"{$name}\"";
		}

		// Set a default content-length header if one was no provided.
		$length = $this->getHeader( $headers, 'content-length' );
		if ( ! $length ) {
			if ( $length = $stream->getSize() ) {// @codingStandardsIgnoreLine
				$headers['Content-Length'] = (string) $length;
			}
		}

		// Set a default Content-Type if one was not supplied.
		$type = $this->getHeader( $headers, 'content-type' );
		if ( ! $type && ( '0' === $filename || $filename ) ) {
			if ( $type = mimetype_from_filename( $filename ) ) {// @codingStandardsIgnoreLine
				$headers['Content-Type'] = $type;
			}
		}

		return [ $stream, $headers ];
	}
	/**
	 * This function is getHeader.
	 *
	 * @param array  $headers passes parameter as headers.
	 * @param string $key passes parameter as key.
	 */
	private function getHeader( array $headers, $key ) {
		$lowercaseHeader = strtolower( $key );// @codingStandardsIgnoreLine
		foreach ( $headers as $k => $v ) {
			if ( strtolower( $k ) === $lowercaseHeader ) {// @codingStandardsIgnoreLine
				return $v;
			}
		}

		return null;
	}
}
