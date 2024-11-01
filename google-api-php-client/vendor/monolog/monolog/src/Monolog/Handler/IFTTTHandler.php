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
 * IFTTTHandler uses cURL to trigger IFTTT Maker actions
 *
 * Register a secret key and trigger/event name at https://ifttt.com/maker
 *
 * value1 will be the channel from monolog's Logger constructor,
 * value2 will be the level name (ERROR, WARNING, ..)
 * value3 will be the log record's message
 */
class IFTTTHandler extends AbstractProcessingHandler {

	private $eventName;// @codingStandardsIgnoreLine.
	private $secretKey;// @codingStandardsIgnoreLine.

	/**
	 * This function is __construct.
	 *
	 * @param string  $eventName The name of the IFTTT Maker event that should be triggered .
	 * @param string  $secretKey A valid IFTTT secret key .
	 * @param int     $level     The minimum logging level at which this handler will be triggered .
	 * @param Boolean $bubble    Whether the messages that are handled can bubble up the stack or not .
	 */
	public function __construct( $eventName, $secretKey, $level = Logger::ERROR, $bubble = true ) {// @codingStandardsIgnoreLine.
		$this->eventName = $eventName;// @codingStandardsIgnoreLine.
		$this->secretKey = $secretKey;// @codingStandardsIgnoreLine.

		parent::__construct( $level, $bubble );
	}

	/**
	 * This function is  write.
	 *
	 * @param array $record .
	 * {@inheritdoc}.
	 */
	public function write( array $record ) {
		$postData   = array(// @codingStandardsIgnoreLine.
			'value1' => $record['channel'],
			'value2' => $record['level_name'],
			'value3' => $record['message'],
		);
		$postString = json_encode( $postData );// @codingStandardsIgnoreLine.

		$ch = curl_init();// @codingStandardsIgnoreLine.
		curl_setopt( $ch, CURLOPT_URL, 'https://maker.ifttt.com/trigger/' . $this->eventName . '/with/key/' . $this->secretKey );// @codingStandardsIgnoreLine.
		curl_setopt( $ch, CURLOPT_POST, true );// @codingStandardsIgnoreLine.
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );// @codingStandardsIgnoreLine.
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $postString );// @codingStandardsIgnoreLine.
		curl_setopt(// @codingStandardsIgnoreLine.
			$ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
			)
		);

		Curl\Util::execute( $ch );
	}
}
