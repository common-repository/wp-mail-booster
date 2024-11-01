<?php // @codingStandardsIgnoreLine.
/**
 * This file to NativeMailerHandler uses the mail() function to send the emails
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
use Monolog\Formatter\LineFormatter;

/**
 * NativeMailerHandler uses the mail() function to send the emails
 */
class NativeMailerHandler extends MailHandler {

	/**
	 * The email addresses to which the message will be sent
	 *
	 * @var array
	 */
	protected $to;

	/**
	 * The subject of the email
	 *
	 * @var string
	 */
	protected $subject;

	/**
	 * Optional headers for the message
	 *
	 * @var array
	 */
	protected $headers = array();

	/**
	 * Optional parameters for the message
	 *
	 * @var array
	 */
	protected $parameters = array();

	/**
	 * The wordwrap length for the message
	 *
	 * @var int
	 */
	protected $maxColumnWidth; // @codingStandardsIgnoreLine.

	/**
	 * The Content-type for the message
	 *
	 * @var string
	 */
	protected $contentType = 'text/plain'; // @codingStandardsIgnoreLine.

	/**
	 * The encoding for the message
	 *
	 * @var string
	 */
	protected $encoding = 'utf-8';

	/**
	 * Public constructor
	 *
	 * @param string|array $to             The receiver of the mail .
	 * @param string       $subject        The subject of the mail .
	 * @param string       $from           The sender of the mail .
	 * @param int          $level          The minimum logging level at which this handler will be triggered .
	 * @param bool         $bubble         Whether the messages that are handled can bubble up the stack or not .
	 * @param int          $maxColumnWidth The maximum column width that the message lines will have .
	 */
	public function __construct( $to, $subject, $from, $level = Logger::ERROR, $bubble = true, $maxColumnWidth = 70 ) { // @codingStandardsIgnoreLine.
		parent::__construct( $level, $bubble );
		$this->to      = is_array( $to ) ? $to : array( $to );
		$this->subject = $subject;
		$this->addHeader( sprintf( 'From: %s', $from ) );
		$this->maxColumnWidth = $maxColumnWidth; // @codingStandardsIgnoreLine.
	}

	/**
	 * Add headers to the message
	 *
	 * @param  string|array $headers Custom added headers .
	 * @throws \InvalidArgumentException .
	 */
	public function addHeader( $headers ) {
		foreach ( (array) $headers as $header ) {
			if ( strpos( $header, "\n" ) !== false || strpos( $header, "\r" ) !== false ) {
				throw new \InvalidArgumentException( 'Headers can not contain newline characters for security reasons' );
			}
			$this->headers[] = $header;
		}

		return $this;
	}

	/**
	 * Add parameters to the message
	 *
	 * @param  string|array $parameters Custom added parameters .
	 * @return self
	 */
	public function addParameter( $parameters ) {
		$this->parameters = array_merge( $this->parameters, (array) $parameters );

		return $this;
	}

	/**
	 * Function to send content
	 *
	 * @param string $content .
	 * @param array  $records .
	 */
	protected function send( $content, array $records ) {
		$content  = wordwrap( $content, $this->maxColumnWidth ); // @codingStandardsIgnoreLine.
		$headers  = ltrim( implode( "\r\n", $this->headers ) . "\r\n", "\r\n" );
		$headers .= 'Content-type: ' . $this->getContentType() . '; charset=' . $this->getEncoding() . "\r\n";
		if ( $this->getContentType() == 'text/html' && false === strpos( $headers, 'MIME-Version:' ) ) { // WPCS:Loose comparison ok.
			$headers .= 'MIME-Version: 1.0' . "\r\n";
		}

		$subject = $this->subject;
		if ( $records ) {
			$subjectFormatter = new LineFormatter( $this->subject ); // @codingStandardsIgnoreLine.
			$subject          = $subjectFormatter->format( $this->getHighestRecord( $records ) ); // @codingStandardsIgnoreLine.
		}

		$parameters = implode( ' ', $this->parameters );
		foreach ( $this->to as $to ) {
			mail( $to, $subject, $content, $headers, $parameters );
		}
	}

	/**
	 * Function to get content type
	 *
	 * @return string $contentType .
	 */
	public function getContentType() {
		return $this->contentType; // @codingStandardsIgnoreLine.
	}

	/**
	 * Function to get encoding
	 *
	 * @return string $encoding .
	 */
	public function getEncoding() {
		return $this->encoding;
	}

	/**
	 *  The content type of the email - Defaults to text/plain. Use text/html for HTML
	 *                             messages
	 *
	 * @param string $contentType .
	 * @throws \InvalidArgumentException .
	 */
	public function setContentType( $contentType ) { // @codingStandardsIgnoreLine.
		if ( strpos( $contentType, "\n" ) !== false || strpos( $contentType, "\r" ) !== false ) { // @codingStandardsIgnoreLine.
			throw new \InvalidArgumentException( 'The content type can not contain newline characters to prevent email header injection' );
		}

		$this->contentType = $contentType; // @codingStandardsIgnoreLine.
		return $this;
	}

	/**
	 * Function to set encoding
	 *
	 * @param  string $encoding .
	 * @throws \InvalidArgumentException .
	 */
	public function setEncoding( $encoding ) {
		if ( strpos( $encoding, "\n" ) !== false || strpos( $encoding, "\r" ) !== false ) {
			throw new \InvalidArgumentException( 'The encoding can not contain newline characters to prevent email header injection' );
		}

		$this->encoding = $encoding;

		return $this;
	}
}
