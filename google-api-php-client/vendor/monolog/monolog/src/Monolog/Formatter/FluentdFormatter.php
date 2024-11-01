<?php // @codingStandardsIgnoreLine
/**
 * This file to Serializes a log message to Fluentd unix socket protocol.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Formatter;

/**
 * Class FluentdFormatter
 *
 * Serializes a log message to Fluentd unix socket protocol
 */
class FluentdFormatter implements FormatterInterface {

	/**
	 * The version of this plugin .
	 *
	 * @var bool $levelTag should message level be a part of the fluentd tag
	 */
	protected $levelTag = false; //@codingStandardsIgnoreLine.
	/**
	 * This function is __construct.
	 *
	 * @param string $levelTag .
	 * @throws \RuntimeException .
	 */
	public function __construct( $levelTag = false ) {//@codingStandardsIgnoreLine
		if ( ! function_exists( 'json_encode' ) ) {
			throw new \RuntimeException( 'PHP\'s json extension is required to use Monolog\'s FluentdUnixFormatter' );
		}

		$this->levelTag = (bool) $levelTag; //@codingStandardsIgnoreLine
	}
	/**
	 * This function is isUsingLevelsInTag.
	 */
	public function isUsingLevelsInTag() {
		return $this->levelTag;//@codingStandardsIgnoreLine
	}
	/**
	 * This function is format.
	 *
	 * @param array $record .
	 */
	public function format( array $record ) {
		$tag = $record['channel'];
		if ( $this->levelTag ) {//@codingStandardsIgnoreLine
			$tag .= '.' . strtolower( $record['level_name'] );
		}

		$message = array(
			'message' => $record['message'],
			'extra'   => $record['extra'],
		);

		if ( ! $this->levelTag ) {//@codingStandardsIgnoreLine
			$message['level']      = $record['level'];
			$message['level_name'] = $record['level_name'];
		}

		return json_encode( array( $tag, $record['datetime']->getTimestamp(), $message ) );//@codingStandardsIgnoreLine
	}
	/**
	 * This function is formatBatch.
	 *
	 * @param array $records .
	 */
	public function formatBatch( array $records ) {
		$message = '';
		foreach ( $records as $record ) {
			$message .= $this->format( $record );
		}

		return $message;
	}
}
