<?php // @codingStandardsIgnoreLine.
/**
 * This file send notification through web hook
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

use Monolog\Formatter\FormatterInterface;
use Monolog\Logger;
use Monolog\Handler\Slack\SlackRecord;

/**
 * Sends notifications through Slack Webhooks
 */
class SlackWebhookHandler extends AbstractProcessingHandler {

	/**
	 * Slack Webhook token
	 *
	 * @var string
	 */
	private $webhookUrl; // @codingStandardsIgnoreLine.

	/**
	 * Instance of the SlackRecord util class preparing data for Slack API.
	 *
	 * @var SlackRecord
	 */
	private $slackRecord; // @codingStandardsIgnoreLine.

	/**
	 * Public constructor
	 *
	 * @param  string      $webhookUrl             Slack Webhook URL .
	 * @param  string|null $channel                Slack channel (encoded ID or name) .
	 * @param  string|null $username               Name of a bot .
	 * @param  bool        $useAttachment          Whether the message should be added to Slack as attachment (plain text otherwise) .
	 * @param  string|null $iconEmoji              The emoji name to use (or null) .
	 * @param  bool        $useShortAttachment     Whether the the context/extra messages added to Slack as attachments are in a short style .
	 * @param  bool        $includeContextAndExtra Whether the attachment should include context and extra data .
	 * @param  int         $level                  The minimum logging level at which this handler will be triggered .
	 * @param  bool        $bubble                 Whether the messages that are handled can bubble up the stack or not .
	 * @param  array       $excludeFields          Dot separated list of fields to exclude from slack message. E.g. ['context.field1', 'extra.field2'] .
	 */
	public function __construct( $webhookUrl, $channel = null, $username = null, $useAttachment = true, $iconEmoji = null, $useShortAttachment = false, $includeContextAndExtra = false, $level = Logger::CRITICAL, $bubble = true, array $excludeFields = array() ) { // @codingStandardsIgnoreLine.
		parent::__construct( $level, $bubble );
		$this->webhookUrl  = $webhookUrl; // @codingStandardsIgnoreLine.
		$this->slackRecord = new SlackRecord( // @codingStandardsIgnoreLine.
			$channel,
			$username,
			$useAttachment, // @codingStandardsIgnoreLine.
			$iconEmoji, // @codingStandardsIgnoreLine.
			$useShortAttachment, // @codingStandardsIgnoreLine.
			$includeContextAndExtra, // @codingStandardsIgnoreLine.
			$excludeFields, // @codingStandardsIgnoreLine.
			$this->formatter
		);
	}
	/**
	 * This function is use to get slack record
	 */
	public function getSlackRecord() {
		return $this->slackRecord; // @codingStandardsIgnoreLine.
	}

	/**
	 * Function to write
	 *
	 * @param array $record .
	 */
	protected function write( array $record ) {
		$postData   = $this->slackRecord->getSlackData( $record ); // @codingStandardsIgnoreLine.
		$postString = json_encode( $postData ); // @codingStandardsIgnoreLine.
		$ch         = curl_init(); // @codingStandardsIgnoreLine.
		$options    = array(
			CURLOPT_URL            => $this->webhookUrl, // @codingStandardsIgnoreLine.
			CURLOPT_POST           => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER     => array( 'Content-type: application/json' ),
			CURLOPT_POSTFIELDS     => $postString, // @codingStandardsIgnoreLine.
		);
		if ( defined( 'CURLOPT_SAFE_UPLOAD' ) ) {
			$options[ CURLOPT_SAFE_UPLOAD ] = true;
		}
		curl_setopt_array( $ch, $options ); // @codingStandardsIgnoreLine.
		Curl\Util::execute( $ch );
	}
	/**
	 * Function is to set format
	 *
	 * @param FormatterInterface $formatter .
	 */
	public function setFormatter( FormatterInterface $formatter ) {
		parent::setFormatter( $formatter );
		$this->slackRecord->setFormatter( $formatter ); // @codingStandardsIgnoreLine.
		return $this;
	}
	/**
	 * Function to get format
	 */
	public function getFormatter() {
		$formatter = parent::getFormatter();
		$this->slackRecord->setFormatter( $formatter ); // @codingStandardsIgnoreLine.
		return $formatter;
	}
}
