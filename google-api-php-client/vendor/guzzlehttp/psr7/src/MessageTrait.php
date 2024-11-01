<?php // @codingStandardsIgnoreLine
/**
 * This file for Trait implementing functionality common to requests and responses.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor/composer
 * @version 2.0.0
 */

namespace GuzzleHttp\Psr7;

use Psr\Http\Message\StreamInterface;

/**
 * Trait implementing functionality common to requests and responses.
 */
trait MessageTrait {

	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      array    $headers  .
	 */
	private $headers = [];

	private $headerNames = [];// @codingStandardsIgnoreLine
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      array    $protocol  .
	 */
	private $protocol = '1.1';

	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      array    $stream  .
	 */
	private $stream;

	public function getProtocolVersion() {// @codingStandardsIgnoreLine
		return $this->protocol;
	}

	public function withProtocolVersion( $version ) {// @codingStandardsIgnoreLine
		if ( $this->protocol === $version ) {
			return $this;
		}

		$new           = clone $this;
		$new->protocol = $version;
		return $new;
	}

	public function getHeaders() {// @codingStandardsIgnoreLine
		return $this->headers;
	}

	public function hasHeader( $header ) {// @codingStandardsIgnoreLine
		return isset( $this->headerNames[ strtolower( $header ) ] );// @codingStandardsIgnoreLine
	}

	public function getHeader( $header ) {// @codingStandardsIgnoreLine
		$header = strtolower( $header );

		if ( ! isset( $this->headerNames[ $header ] ) ) {// @codingStandardsIgnoreLine
			return [];
		}

		$header = $this->headerNames[ $header ];// @codingStandardsIgnoreLine

		return $this->headers[ $header ];
	}

	public function getHeaderLine( $header ) {// @codingStandardsIgnoreLine
		return implode( ', ', $this->getHeader( $header ) );
	}

	public function withHeader( $header, $value ) {// @codingStandardsIgnoreLine
		if ( ! is_array( $value ) ) {
			$value = [ $value ];
		}

		$value      = $this->trimHeaderValues( $value );
		$normalized = strtolower( $header );

		$new = clone $this;
		if ( isset( $new->headerNames[ $normalized ] ) ) {// @codingStandardsIgnoreLine
			unset( $new->headers[ $new->headerNames[ $normalized ] ] );// @codingStandardsIgnoreLine
		}
		$new->headerNames[ $normalized ] = $header;// @codingStandardsIgnoreLine
		$new->headers[ $header ]         = $value;

		return $new;
	}

	public function withAddedHeader( $header, $value ) {// @codingStandardsIgnoreLine
		if ( ! is_array( $value ) ) {
			$value = [ $value ];
		}

		$value      = $this->trimHeaderValues( $value );
		$normalized = strtolower( $header );

		$new = clone $this;
		if ( isset( $new->headerNames[ $normalized ] ) ) {// @codingStandardsIgnoreLine
			$header                  = $this->headerNames[ $normalized ];// @codingStandardsIgnoreLine
			$new->headers[ $header ] = array_merge( $this->headers[ $header ], $value );
		} else {
			$new->headerNames[ $normalized ] = $header;// @codingStandardsIgnoreLine
			$new->headers[ $header ]         = $value;
		}

		return $new;
	}

	public function withoutHeader( $header ) {// @codingStandardsIgnoreLine
		$normalized = strtolower( $header );

		if ( ! isset( $this->headerNames[ $normalized ] ) ) {// @codingStandardsIgnoreLine
			return $this;
		}

		$header = $this->headerNames[ $normalized ];// @codingStandardsIgnoreLine

		$new = clone $this;
		unset( $new->headers[ $header ], $new->headerNames[ $normalized ] );// @codingStandardsIgnoreLine

		return $new;
	}

	public function getBody() {// @codingStandardsIgnoreLine
		if ( ! $this->stream ) {
			$this->stream = stream_for( '' );
		}

		return $this->stream;
	}

	public function withBody( StreamInterface $body ) {// @codingStandardsIgnoreLine
		if ( $body === $this->stream ) {
			return $this;
		}

		$new         = clone $this;
		$new->stream = $body;
		return $new;
	}

	private function setHeaders( array $headers ) {// @codingStandardsIgnoreLine
		$this->headerNames = $this->headers = [];// @codingStandardsIgnoreLine
		foreach ( $headers as $header => $value ) {
			if ( ! is_array( $value ) ) {
				$value = [ $value ];
			}

			$value      = $this->trimHeaderValues( $value );
			$normalized = strtolower( $header );
			if ( isset( $this->headerNames[ $normalized ] ) ) {// @codingStandardsIgnoreLine
				$header                   = $this->headerNames[ $normalized ];// @codingStandardsIgnoreLine
				$this->headers[ $header ] = array_merge( $this->headers[ $header ], $value );
			} else {
				$this->headerNames[ $normalized ] = $header;// @codingStandardsIgnoreLine
				$this->headers[ $header ]         = $value;
			}
		}
	}

	/**
	 * Trims whitespace from the header values.
	 *
	 * Spaces and tabs ought to be excluded by parsers when extracting the field value from a header field.
	 *
	 * header-field = field-name ":" OWS field-value OWS
	 * OWS          = *( SP / HTAB )
	 *
	 * @param string[] $values Header values.
	 *
	 * @return string[] Trimmed header values
	 *
	 * @see https://tools.ietf.org/html/rfc7230#section-3.2.4
	 */
	private function trimHeaderValues( array $values ) {// @codingStandardsIgnoreLine
		return array_map(
			function ( $value ) {
				return trim( $value, " \t" );
			}, $values
		);
	}
}
