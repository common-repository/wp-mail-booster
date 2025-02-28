<?php // @codingStandardsIgnoreLine.
/**
 * This file is part of the Monolog package.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/handler
 * @version 2.0.0
 */
namespace Monolog\Handler;

use Monolog\Formatter\LineFormatter;
use Monolog\Logger;

/**
 * Sends logs to Fleep.io using Webhook integrations
 *
 * You'll need a Fleep.io account to use this handler.
 *
 * @see https://fleep.io/integrations/webhooks/ Fleep Webhooks Documentation
 */
class FleepHookHandler extends SocketHandler {

	const FLEEP_HOST = 'fleep.io';

	const FLEEP_HOOK_URI = '/hook/';

	/**
	 * The version of plugin.
	 *
	 * @var string Webhook token (specifies the conversation where logs are sent)
	 */
	protected $token;

	/**
	 * Construct a new Fleep.io Handler.
	 *
	 * For instructions on how to create a new web hook in your conversations
	 * see https://fleep.io/integrations/webhooks/
	 *
	 * @param  string   $token  Webhook token .
	 * @param  bool|int $level  The minimum logging level at which this handler will be triggered .
	 * @param  bool     $bubble Whether the messages that are handled can bubble up the stack or not .
	 * @throws MissingExtensionException .
	 */
	public function __construct( $token, $level = Logger::DEBUG, $bubble = true ) {
		if ( ! extension_loaded( 'openssl' ) ) {
			throw new MissingExtensionException( 'The OpenSSL PHP extension is required to use the FleepHookHandler' );
		}

		$this->token = $token;

		$connectionString = 'ssl://' . self::FLEEP_HOST . ':443';// @codingStandardsIgnoreLine.
		parent::__construct( $connectionString, $level, $bubble );// @codingStandardsIgnoreLine.
	}

	/**
	 * Returns the default formatter to use with this handler .
	 *
	 * Overloaded to remove empty context and extra arrays from the end of the log message .
	 *
	 * @return LineFormatter
	 */
	protected function getDefaultFormatter() {
		return new LineFormatter( null, null, true, true );
	}

	/**
	 * Handles a log record
	 *
	 * @param array $record .
	 */
	public function write( array $record ) {
		parent::write( $record );
		$this->closeSocket();
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param  array $record .
	 * @return string
	 */
	protected function generateDataStream( $record ) {
		$content = $this->buildContent( $record );

		return $this->buildHeader( $content ) . $content;
	}

	/**
	 * Builds the header of the API Call
	 *
	 * @param  string $content .
	 * @return string
	 */
	private function buildHeader( $content ) {
		$header  = 'POST ' . self::FLEEP_HOOK_URI . $this->token . " HTTP/1.1\r\n";
		$header .= 'Host: ' . self::FLEEP_HOST . "\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= 'Content-Length: ' . strlen( $content ) . "\r\n";
		$header .= "\r\n";

		return $header;
	}

	/**
	 * Builds the body of API call
	 *
	 * @param  array $record .
	 * @return string
	 */
	private function buildContent( $record ) {
		$dataArray = array(// @codingStandardsIgnoreLine.
			'message' => $record['formatted'],
		);

		return http_build_query( $dataArray );// @codingStandardsIgnoreLine.
	}
}
