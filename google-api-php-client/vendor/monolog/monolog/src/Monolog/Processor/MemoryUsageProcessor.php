<?php //@codingStandardsIgnoreLine
/**
 * This file is MemoryUsageProcessor.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

namespace Monolog\Processor;

/**
 * Injects memory_get_usage in all records
 */
class MemoryUsageProcessor extends MemoryProcessor {

	/**
	 * This function is __invoke.
	 *
	 * @param  array $record passes parameter as record.
	 * @return array
	 */
	public function __invoke( array $record ) {
		$bytes     = memory_get_usage( $this->realUsage );// @codingStandardsIgnoreLine
		$formatted = $this->formatBytes( $bytes );

		$record['extra']['memory_usage'] = $formatted;

		return $record;
	}
}
