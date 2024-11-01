<?php //@codingStandardsIgnoreLine
/**
 * This file is ProcessIDProcessor.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

namespace Monolog\Processor;

/**
 * Adds value of getmypid into records
 */
class ProcessIdProcessor {

	/**
	 * This function is __invoke.
	 *
	 * @param  array $record passes parameter as record.
	 * @return array
	 */
	public function __invoke( array $record ) {
		$record['extra']['process_id'] = getmypid();

		return $record;
	}
}
