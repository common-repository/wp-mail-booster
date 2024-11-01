<?php // @codingStandardsIgnoreLine.
/**
 * This file to Stores logs to files that are rotated every day
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
 * Stores logs to files that are rotated every day and a limited number of files are kept.
 *
 * This rotation is only intended to be used as a workaround. Using logrotate to
 * handle the rotation is strongly encouraged when you can use it.
 *
 * @author Christophe Coevoet <stof@notk.org>
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class RotatingFileHandler extends StreamHandler {

	const FILE_PER_DAY   = 'Y-m-d';
	const FILE_PER_MONTH = 'Y-m';
	const FILE_PER_YEAR  = 'Y';
	/**
	 * Variable for file name
	 *
	 * @var string
	 */
	protected $filename;
	/**
	 * Variable for maximum files
	 *
	 * @var int
	 */
	protected $maxFiles; // @codingStandardsIgnoreLine.
	/**
	 * Variable for mostly rotate
	 *
	 * @var string
	 */
	protected $mustRotate; // @codingStandardsIgnoreLine.
	/**
	 * Variable for next rotation
	 *
	 * @var string
	 */
	protected $nextRotation; // @codingStandardsIgnoreLine.
	/**
	 * Variable for file name format
	 *
	 * @var string
	 */
	protected $filenameFormat; // @codingStandardsIgnoreLine.
	/**
	 * Variable for date format
	 *
	 * @var array
	 */
	protected $dateFormat; // @codingStandardsIgnoreLine.

	/**
	 * Public constructor
	 *
	 * @param string   $filename .
	 * @param int      $maxFiles       The maximal amount of files to keep (0 means unlimited) .
	 * @param int      $level          The minimum logging level at which this handler will be triggered .
	 * @param Boolean  $bubble         Whether the messages that are handled can bubble up the stack or not .
	 * @param int|null $filePermission Optional file permissions (default (0644) are only for owner read/write) .
	 * @param Boolean  $useLocking     Try to lock log file before doing any writes .
	 */
	public function __construct( $filename, $maxFiles = 0, $level = Logger::DEBUG, $bubble = true, $filePermission = null, $useLocking = false ) { // @codingStandardsIgnoreLine.
		$this->filename       = $filename;
		$this->maxFiles       = (int) $maxFiles; // @codingStandardsIgnoreLine.
		$this->nextRotation   = new \DateTime( 'tomorrow' ); // @codingStandardsIgnoreLine.
		$this->filenameFormat = '{filename}-{date}'; // @codingStandardsIgnoreLine.
		$this->dateFormat     = 'Y-m-d'; // @codingStandardsIgnoreLine.

		parent::__construct( $this->getTimedFilename(), $level, $bubble, $filePermission, $useLocking ); // @codingStandardsIgnoreLine.
	}

	/**
	 * {@inheritdoc}
	 */
	public function close() {
		parent::close();

		if ( true === $this->mustRotate ) { // @codingStandardsIgnoreLine.
			$this->rotate();
		}
	}
	/**
	 * Function to set file name format
	 *
	 * @param array  $filenameFormat .
	 * @param string $dateFormat .
	 */
	public function setFilenameFormat( $filenameFormat, $dateFormat ) { // @codingStandardsIgnoreLine.
		if ( ! preg_match( '{^Y(([/_.-]?m)([/_.-]?d)?)?$}', $dateFormat ) ) { // @codingStandardsIgnoreLine.
			trigger_error( // @codingStandardsIgnoreLine.
				'Invalid date format - format must be one of ' .
				'RotatingFileHandler::FILE_PER_DAY ("Y-m-d"), RotatingFileHandler::FILE_PER_MONTH ("Y-m") ' .
				'or RotatingFileHandler::FILE_PER_YEAR ("Y"), or you can set one of the ' .
				'date formats using slashes, underscores and/or dots instead of dashes.',
				E_USER_DEPRECATED
			);
		}
		if ( substr_count( $filenameFormat, '{date}' ) === 0 ) { // @codingStandardsIgnoreLine.
			trigger_error( // @codingStandardsIgnoreLine.
				'Invalid filename format - format should contain at least `{date}`, because otherwise rotating is impossible.',
				E_USER_DEPRECATED
			);
		}
		$this->filenameFormat = $filenameFormat; // @codingStandardsIgnoreLine.
		$this->dateFormat     = $dateFormat; // @codingStandardsIgnoreLine.
		$this->url            = $this->getTimedFilename();
		$this->close();
	}

	/**
	 * Function to write
	 *
	 * @param array $record .
	 */
	protected function write( array $record ) {
		// on the first record written, if the log is new, we should rotate (once per day) .
		if ( null === $this->mustRotate ) { // @codingStandardsIgnoreLine.
			$this->mustRotate = ! file_exists( $this->url ); // @codingStandardsIgnoreLine.
		}

		if ( $this->nextRotation < $record['datetime'] ) { // @codingStandardsIgnoreLine.
			$this->mustRotate = true; // @codingStandardsIgnoreLine.
			$this->close();
		}

		parent::write( $record );
	}

	/**
	 * Rotates the files.
	 */
	protected function rotate() {
		// update filename .
		$this->url          = $this->getTimedFilename();
		$this->nextRotation = new \DateTime( 'tomorrow' ); // @codingStandardsIgnoreLine.

		// skip GC of old logs if files are unlimited .
		if ( 0 === $this->maxFiles ) { // @codingStandardsIgnoreLine.
			return;
		}

		$logFiles = glob( $this->getGlobPattern() ); // @codingStandardsIgnoreLine.
		if ( $this->maxFiles >= count( $logFiles ) ) { // @codingStandardsIgnoreLine.
			// no files to remove .
			return;
		}

		// Sorting the files by name to remove the older ones .
		usort(
			$logFiles, function ( $a, $b ) { // @codingStandardsIgnoreLine.
				return strcmp( $b, $a );
			}
		);

		foreach ( array_slice( $logFiles, $this->maxFiles ) as $file ) { // @codingStandardsIgnoreLine.
			if ( is_writable( $file ) ) { // @codingStandardsIgnoreLine.
				// suppress errors here as unlink() might fail if two processes
				// are cleaning up/rotating at the same time .
				set_error_handler( function ( $errno, $errstr, $errfile, $errline ) {} ); // @codingStandardsIgnoreLine.
				unlink( $file ); // @codingStandardsIgnoreLine.
				restore_error_handler();
			}
		}
		$this->mustRotate = false; // @codingStandardsIgnoreLine.
	}
	/**
	 * Function use to get timed file name
	 */
	protected function getTimedFilename() {
		$fileInfo      = pathinfo( $this->filename ); // @codingStandardsIgnoreLine.
		$timedFilename = str_replace( // @codingStandardsIgnoreLine.
			array( '{filename}', '{date}' ),
			array( $fileInfo['filename'], date( $this->dateFormat ) ), // @codingStandardsIgnoreLine.
			$fileInfo['dirname'] . '/' . $this->filenameFormat // @codingStandardsIgnoreLine.
		);

		if ( ! empty( $fileInfo['extension'] ) ) { // @codingStandardsIgnoreLine.
			$timedFilename .= '.' . $fileInfo['extension']; // @codingStandardsIgnoreLine.
		}
		return $timedFilename; // @codingStandardsIgnoreLine.
	}

	/**
	 * Function use to get global pattern
	 */
	protected function getGlobPattern() {
		$fileInfo = pathinfo( $this->filename ); // @codingStandardsIgnoreLine.
		$glob     = str_replace(
			array( '{filename}', '{date}' ),
			array( $fileInfo['filename'], '*' ), // @codingStandardsIgnoreLine.
			$fileInfo['dirname'] . '/' . $this->filenameFormat // @codingStandardsIgnoreLine.
		);
		if ( ! empty( $fileInfo['extension'] ) ) { // @codingStandardsIgnoreLine.
			$glob .= '.' . $fileInfo['extension']; // @codingStandardsIgnoreLine.
		}
		return $glob;
	}
}
