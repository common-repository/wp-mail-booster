<?php //@codingStandardsIgnoreLine
/**
 * This file is MemoryProcessor.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

namespace Monolog\Processor;

/**
 * Some methods that are common for all memory processors
 */
abstract class MemoryProcessor {

	/**
	 * Version of this plugin.
	 *
	 * @access protected
	 * @var    $realUsage
	 */
	protected $realUsage;// @codingStandardsIgnoreLine

	/**
	 * Version of this plugin.
	 *
	 * @access   protected
	 * @var      $useFormatting.
	 */
	protected $useFormatting;// @codingStandardsIgnoreLine

	/**
	 * This function is __construct.
	 *
	 * @param bool $realUsage     Set this to true to get the real size of memory allocated from system.
	 * @param bool $useFormatting If true, then format memory size to human readable string (MB, KB, B depending on size).
	 */
	public function __construct( $realUsage = true, $useFormatting = true ) {// @codingStandardsIgnoreLine
		$this->realUsage     = (boolean) $realUsage;// @codingStandardsIgnoreLine
		$this->useFormatting = (boolean) $useFormatting;// @codingStandardsIgnoreLine
	}

	/**
	 * Formats bytes into a human readable string if $this->useFormatting is true, otherwise return $bytes as is
	 *
	 * @param  int $bytes passes parameter as bytes.
	 * @return string|int Formatted string if $this->useFormatting is true, otherwise return $bytes as is
	 */
	protected function formatBytes( $bytes ) {// @codingStandardsIgnoreLine
		$bytes = (int) $bytes;

		if ( ! $this->useFormatting ) {// @codingStandardsIgnoreLine
			return $bytes;
		}

		if ( $bytes > 1024 * 1024 ) {
			return round( $bytes / 1024 / 1024, 2 ) . ' MB';
		} elseif ( $bytes > 1024 ) {
			return round( $bytes / 1024, 2 ) . ' KB';
		}

		return $bytes . ' B';
	}
}
