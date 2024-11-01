<?php // @codingStandardsIgnoreLine.
/**
 * This file send notification through slack slackbot
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

/**
 * Sends notifications through Slack's Slackbot
 *
 * @author Haralan Dobrev <hkdobrev@gmail.com>
 * @see    https://slack.com/apps/A0F81R8ET-slackbot
 */
class SlackbotHandler extends AbstractProcessingHandler {

	/**
	 * The slug of the Slack team
	 *
	 * @var string
	 */
	private $slackTeam; // @codingStandardsIgnoreLine.

	/**
	 * Slackbot token
	 *
	 * @var string
	 */
	private $token;

	/**
	 * Slack channel name
	 *
	 * @var string
	 */
	private $channel;

	/**
	 * Public constructor
	 *
	 * @param  string $slackTeam Slack team slug .
	 * @param  string $token     Slackbot token .
	 * @param  string $channel   Slack channel (encoded ID or name) .
	 * @param  int    $level     The minimum logging level at which this handler will be triggered .
	 * @param  bool   $bubble    Whether the messages that are handled can bubble up the stack or not .
	 */
	public function __construct( $slackTeam, $token, $channel, $level = Logger::CRITICAL, $bubble = true ) { // @codingStandardsIgnoreLine.
		parent::__construct( $level, $bubble );

		$this->slackTeam = $slackTeam; // @codingStandardsIgnoreLine.
		$this->token     = $token;
		$this->channel   = $channel;
	}

	/**
	 * Function to write record
	 *
	 * @param array $record .
	 */
	protected function write( array $record ) {
		$slackbotUrl = sprintf( // @codingStandardsIgnoreLine.
			'https://%s.slack.com/services/hooks/slackbot?token=%s&channel=%s',
			$this->slackTeam, // @codingStandardsIgnoreLine.
			$this->token,
			$this->channel
		);

		$ch = curl_init(); // @codingStandardsIgnoreLine.
		curl_setopt( $ch, CURLOPT_URL, $slackbotUrl ); // @codingStandardsIgnoreLine.
		curl_setopt( $ch, CURLOPT_POST, true ); // @codingStandardsIgnoreLine.
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true ); // @codingStandardsIgnoreLine.
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $record['message'] ); // @codingStandardsIgnoreLine.

		Curl\Util::execute( $ch );
	}
}
