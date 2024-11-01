<?php // @codingStandardsIgnoreLine
/**
 * This file for interface formatter.
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
 * Interface for formatters
 */
interface FormatterInterface {

	/**
	 * Formats a log record.
	 *
	 * @param  array $record A record to format .
	 * @return mixed The formatted record .
	 */
	public function format( array $record);

	/**
	 * Formats a set of formatBatch.
	 *
	 * @param  array $records A set of records to format .
	 * @return mixed The formatted set of records .
	 */
	public function formatBatch( array $records);// @codingStandardsIgnoreLine.
}
