<?php // @codingStandardsIgnoreLine.
/**
 * This file uses Swift_Mailer to send the emails
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
use Swift;

/**
 * SwiftMailerHandler uses Swift_Mailer to send the emails
 */
class SwiftMailerHandler extends MailHandler {
	/**
	 * Variable for mailer .
	 *
	 * @var string
	 */
	protected $mailer;
	/**
	 * Variable for message template
	 *
	 * @var string
	 */
	private $messageTemplate; // @codingStandardsIgnoreLine.

	/**
	 * Public constructor
	 *
	 * @param \Swift_Mailer           $mailer  The mailer to use .
	 * @param callable|\Swift_Message $message An example message for real messages, only the body will be replaced .
	 * @param int                     $level   The minimum logging level at which this handler will be triggered .
	 * @param Boolean                 $bubble  Whether the messages that are handled can bubble up the stack or not .
	 */
	public function __construct( \Swift_Mailer $mailer, $message, $level = Logger::ERROR, $bubble = true ) {
		parent::__construct( $level, $bubble );

		$this->mailer          = $mailer;
		$this->messageTemplate = $message; // @codingStandardsIgnoreLine.
	}

	/**
	 * Function to send
	 *
	 * @param string $content .
	 * @param array  $records .
	 */
	protected function send( $content, array $records ) {
		$this->mailer->send( $this->buildMessage( $content, $records ) );
	}

	/**
	 * Creates instance of Swift_Message to be sent
	 *
	 * @param  string $content formatted email body to be sent .
	 * @param  array  $records Log records that formed the content .
	 * @return \Swift_Message
	 * @throws \InvalidArgumentException .
	 */
	protected function buildMessage( $content, array $records ) {
		$message = null;
		if ( $this->messageTemplate instanceof \Swift_Message ) { // @codingStandardsIgnoreLine.
			$message = clone $this->messageTemplate; // @codingStandardsIgnoreLine.
			$message->generateId();
		} elseif ( is_callable( $this->messageTemplate ) ) { // @codingStandardsIgnoreLine.
			$message = call_user_func( $this->messageTemplate, $content, $records ); // @codingStandardsIgnoreLine.
		}

		if ( ! $message instanceof \Swift_Message ) {
			throw new \InvalidArgumentException( 'Could not resolve message as instance of Swift_Message or a callable returning it' );
		}

		if ( $records ) {
			$subjectFormatter = new LineFormatter( $message->getSubject() ); // @codingStandardsIgnoreLine.
			$message->setSubject( $subjectFormatter->format( $this->getHighestRecord( $records ) ) ); // @codingStandardsIgnoreLine.
		}

		$message->setBody( $content );
		if ( version_compare( Swift::VERSION, '6.0.0', '>=' ) ) {
			$message->setDate( new \DateTimeImmutable() );
		} else {
			$message->setDate( time() );
		}

		return $message;
	}

	/**
	 * BC getter, to be removed in 2.0
	 *
	 * @param string $name .
	 * @throws \InvalidArgumentException .
	 */
	public function __get( $name ) {
		if ( 'message' === $name ) {
			trigger_error( 'SwiftMailerHandler->message is deprecated, use ->buildMessage() instead to retrieve the message', E_USER_DEPRECATED ); // @codingStandardsIgnoreLine.
			return $this->buildMessage( null, array() );
		}
		throw new \InvalidArgumentException( 'Invalid property ' . $name );
	}
}
