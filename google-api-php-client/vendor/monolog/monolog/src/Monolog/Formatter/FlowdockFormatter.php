<?php // @codingStandardsIgnoreLine
/**
 * This file to Formats the record to be used in the FlowdockHandler
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
 * Formats the record to be used in the FlowdockHandler
 */
class FlowdockFormatter implements FormatterInterface {

	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $source.
	 */
	private $source;

	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $sourceEmail.
	 */
	private $sourceEmail;// @codingStandardsIgnoreLine.

	/**
	 * This function is __construct.
	 *
	 * @param string $source .
	 * @param string $sourceEmail .
	 */
	public function __construct( $source, $sourceEmail ) { // @codingStandardsIgnoreLine.
		$this->source      = $source;
		$this->sourceEmail = $sourceEmail; // @codingStandardsIgnoreLine.
	}

	/**
	 * This function is format.
	 *
	 * @param array $record .
	 * {@inheritdoc}.
	 */
	public function format( array $record ) {
		$tags = array(
			'#logs',
			'#' . strtolower( $record['level_name'] ),
			'#' . $record['channel'],
		);

		foreach ( $record['extra'] as $value ) {
			$tags[] = '#' . $value;
		}

		$subject = sprintf(
			'in %s: %s - %s',
			$this->source,
			$record['level_name'],
			$this->getShortMessage( $record['message'] )
		);

		$record['flowdock'] = array(
			'source'       => $this->source,
			'from_address' => $this->sourceEmail,//@codingStandardsIgnoreLine
			'subject'      => $subject,
			'content'      => $record['message'],
			'tags'         => $tags,
			'project'      => $this->source,
		);

		return $record;
	}

	/**
	 * This function is formatBatch . array
	 *
	 * @param array $records .
	 * {@inheritdoc} .
	 */
	public function formatBatch( array $records ) {
		$formatted = array();

		foreach ( $records as $record ) {
			$formatted[] = $this->format( $record );
		}

		return $formatted;
	}

	/**
	 * This function is getShortMessage .
	 *
	 * @param string $message .
	 *
	 * @return string
	 */
	public function getShortMessage( $message ) {
		static $hasMbString; //@codingStandardsIgnoreLine

		if ( null === $hasMbString ) {//@codingStandardsIgnoreLine
			$hasMbString = function_exists( 'mb_strlen' );//@codingStandardsIgnoreLine
		}

		$maxLength = 45;//@codingStandardsIgnoreLine

		if ( $hasMbString ) {//@codingStandardsIgnoreLine
			if ( mb_strlen( $message, 'UTF-8' ) > $maxLength ) {//@codingStandardsIgnoreLine
				$message = mb_substr( $message, 0, $maxLength - 4, 'UTF-8' ) . ' ...';//@codingStandardsIgnoreLine
			}
		} else {
			if ( strlen( $message ) > $maxLength ) {//@codingStandardsIgnoreLine
				$message = substr( $message, 0, $maxLength - 4 ) . ' ...';//@codingStandardsIgnoreLine
			}
		}

		return $message;
	}
}
