<?php // @codingStandardsIgnoreLine.
/**
 * This file is part of the Monolog package.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/handler
 * @version 2.0.0
 */

namespace Monolog\Handler;

use Monolog\Logger;

/**
 * MandrillHandler uses cURL to send the emails to the Mandrill API
 */
class MandrillHandler extends MailHandler {

	/**
	 * The version of the plugin.
	 *
	 * @param array $records .
	 * @var string
	 */
	protected $message;
	protected $apiKey;// @codingStandardsIgnoreLine.

	/**
	 * This function is __construct.
	 *
	 * @param string                  $apiKey  A valid Mandrill API key .
	 * @param callable|\Swift_Message $message An example message for real messages, only the body will be replaced .
	 * @param int                     $level   The minimum logging level at which this handler will be triggered .
	 * @param Boolean                 $bubble  Whether the messages that are handled can bubble up the stack or not .
	 * @throws \InvalidArgumentException .
	 */
	public function __construct( $apiKey, $message, $level = Logger::ERROR, $bubble = true ) {// @codingStandardsIgnoreLine.
		parent::__construct( $level, $bubble );

		if ( ! $message instanceof \Swift_Message && is_callable( $message ) ) {
			$message = call_user_func( $message );
		}
		if ( ! $message instanceof \Swift_Message ) {
			throw new \InvalidArgumentException( 'You must provide either a Swift_Message instance or a callable returning it' );
		}
		$this->message = $message;
		$this->apiKey  = $apiKey;// @codingStandardsIgnoreLine.
	}

	/**
	 * This function is send.
	 *
	 * @param string $content .
	 * @param array  $records .
	 */
	protected function send( $content, array $records ) {
		$message = clone $this->message;
		$message->setBody( $content );
		$message->setDate( time() );

		$ch = curl_init();// @codingStandardsIgnoreLine.

		curl_setopt( $ch, CURLOPT_URL, 'https://mandrillapp.com/api/1.0/messages/send-raw.json' );// @codingStandardsIgnoreLine.
		curl_setopt( $ch, CURLOPT_POST, 1 );// @codingStandardsIgnoreLine.
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );// @codingStandardsIgnoreLine.
		curl_setopt(// @codingStandardsIgnoreLine.
			$ch, CURLOPT_POSTFIELDS, http_build_query(
				array(
					'key'         => $this->apiKey,// @codingStandardsIgnoreLine.
					'raw_message' => (string) $message,
					'async'       => false,
				)
			)
		);

		Curl\Util::execute( $ch );
	}
}
