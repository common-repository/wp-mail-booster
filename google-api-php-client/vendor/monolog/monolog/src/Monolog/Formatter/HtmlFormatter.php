<?php // @codingStandardsIgnoreLine
/**
 * This file to Formats incoming records into an HTML table.
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

use Monolog\Logger;

/**
 * Formats incoming records into an HTML table
 *
 * This is especially useful for html email logging
 */
class HtmlFormatter extends NormalizerFormatter {

	/**
	 * Translates Monolog log levels to html color priorities.
	 *
	 * @var string  $logLevels .
	 */
	protected $logLevels = array(// @codingStandardsIgnoreLine.
		Logger::DEBUG     => '#cccccc',
		Logger::INFO      => '#468847',
		Logger::NOTICE    => '#3a87ad',
		Logger::WARNING   => '#c09853',
		Logger::ERROR     => '#f0ad4e',
		Logger::CRITICAL  => '#FF7708',
		Logger::ALERT     => '#C12A19',
		Logger::EMERGENCY => '#000000',
	);

	/**
	 * This function is _construct.
	 *
	 * @param string $dateFormat The format of the timestamp: one supported by DateTime::format .
	 */
	public function __construct( $dateFormat = null ) {// @codingStandardsIgnoreLine.
		parent::__construct( $dateFormat );// @codingStandardsIgnoreLine.
	}

	/**
	 * Creates an HTML table row
	 *
	 * @param  string $th       Row header content .
	 * @param  string $td       Row standard cell content .
	 * @param  bool   $escapeTd false if td content must not be html escaped .
	 * @return string
	 */
	protected function addRow( $th, $td = ' ', $escapeTd = true ) {// @codingStandardsIgnoreLine.
		$th = htmlspecialchars( $th, ENT_NOQUOTES, 'UTF-8' );
		if ( $escapeTd ) {// @codingStandardsIgnoreLine.
			$td = '<pre>' . htmlspecialchars( $td, ENT_NOQUOTES, 'UTF-8' ) . '</pre>';
		}

		return "<tr style=\"padding: 4px;spacing: 0;text-align: left;\">\n<th style=\"background: #cccccc\" width=\"100px\">$th:</th>\n<td style=\"padding: 4px;spacing: 0;text-align: left;background: #eeeeee\">" . $td . "</td>\n</tr>";
	}

	/**
	 * Create a HTML h1 tag
	 *
	 * @param  string $title Text to be in the h1 .
	 * @param  int    $level Error level .
	 * @return string
	 */
	protected function addTitle( $title, $level ) {
		$title = htmlspecialchars( $title, ENT_NOQUOTES, 'UTF-8' );

		return '<h1 style="background: ' . $this->logLevels[ $level ] . ';color: #ffffff;padding: 5px;" class="monolog-output">' . $title . '</h1>';// @codingStandardsIgnoreLine.
	}

	/**
	 * Formats a log record.
	 *
	 * @param  array $record A record to format .
	 * @return mixed The formatted record .
	 */
	public function format( array $record ) {
		$output  = $this->addTitle( $record['level_name'], $record['level'] );
		$output .= '<table cellspacing="1" width="100%" class="monolog-output">';

		$output .= $this->addRow( 'Message', (string) $record['message'] );
		$output .= $this->addRow( 'Time', $record['datetime']->format( $this->dateFormat ) );// @codingStandardsIgnoreLine.
		$output .= $this->addRow( 'Channel', $record['channel'] );
		if ( $record['context'] ) {
			$embeddedTable = '<table cellspacing="1" width="100%">';// @codingStandardsIgnoreLine.
			foreach ( $record['context'] as $key => $value ) {
				$embeddedTable .= $this->addRow( $key, $this->convertToString( $value ) );// @codingStandardsIgnoreLine.
			}
			$embeddedTable .= '</table>';// @codingStandardsIgnoreLine.
			$output        .= $this->addRow( 'Context', $embeddedTable, false );// @codingStandardsIgnoreLine.
		}
		if ( $record['extra'] ) {
			$embeddedTable = '<table cellspacing="1" width="100%">';// @codingStandardsIgnoreLine.
			foreach ( $record['extra'] as $key => $value ) {
				$embeddedTable .= $this->addRow( $key, $this->convertToString( $value ) );// @codingStandardsIgnoreLine.
			}
			$embeddedTable .= '</table>';// @codingStandardsIgnoreLine.
			$output        .= $this->addRow( 'Extra', $embeddedTable, false );// @codingStandardsIgnoreLine.
		}

		return $output . '</table>';
	}

	/**
	 * Formats a set of log records.
	 *
	 * @param  array $records A set of records to format .
	 * @return mixed The formatted set of records .
	 */
	public function formatBatch( array $records ) {
		$message = '';
		foreach ( $records as $record ) {
			$message .= $this->format( $record );
		}

		return $message;
	}
	/**
	 * This function is convertToString .
	 *
	 * @param string $data .
	 */
	protected function convertToString( $data ) {
		if ( null === $data || is_scalar( $data ) ) {
			return (string) $data;
		}

		$data = $this->normalize( $data );
		if ( version_compare( PHP_VERSION, '5.4.0', '>=' ) ) {
			return json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );// @codingStandardsIgnoreLine.
		}

		return str_replace( '\\/', '/', json_encode( $data ) );// @codingStandardsIgnoreLine.
	}
}
