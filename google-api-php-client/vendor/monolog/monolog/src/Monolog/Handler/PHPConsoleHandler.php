<?php // @codingStandardsIgnoreLine.
/**
 * This file used for Monolog handler for Google Chrome extension "PHP Console".
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

use Exception;
use Monolog\Formatter\LineFormatter;
use Monolog\Logger;
use PhpConsole\Connector;
use PhpConsole\Handler;
use PhpConsole\Helper;

/**
 * Monolog handler for Google Chrome extension "PHP Console"
 *
 * Display PHP error/debug log messages in Google Chrome console and notification popups, executes PHP code remotely
 */
class PHPConsoleHandler extends AbstractProcessingHandler {
	/**
	 * Varaible for option
	 *
	 * @var array
	 */
	private $options = array( // @codingStandardsIgnoreLine.
		'enabled'                    => true, // bool Is PHP Console server enabled .
		'classesPartialsTraceIgnore' => array( 'Monolog\\' ), // array Hide calls of classes started with.
		'debugTagsKeysInContext'     => array( 0, 'tag' ), // bool Is PHP Console server enabled .
		'useOwnErrorsHandler'        => false, // bool Enable errors handling .
		'useOwnExceptionsHandler'    => false, // bool Enable exceptions handling .
		'sourcesBasePath'            => null, // string Base path of all project sources to strip in errors source paths .
		'registerHelper'             => true, // bool Register PhpConsole\Helper that allows short debug calls like PC::debug($var, 'ta.g.s') .
		'serverEncoding'             => null, // string|null Server internal encoding  .
		'headersLimit'               => null, // int|null Set headers size limit for your web-server .
		'password'                   => null, // string|null Protect PHP Console connection by password .
		'enableSslOnlyMode'          => false, // bool Force connection by SSL for clients with PHP Console installed .
		'ipMasks'                    => array(), // array Set IP masks of clients that will be allowed to connect to PHP Console: array('192.168.*.*', '127.0.0.1') .
		'enableEvalListener'         => false, // bool Enable eval request to be handled by eval dispatcher(if enabled, 'password' option is also required) .
		'dumperDetectCallbacks'      => false, // bool Convert callback items in dumper vars to (callback SomeClass::someMethod) strings .
		'dumperLevelLimit'           => 5, // int Maximum dumped vars array or object nested dump level .
		'dumperItemsCountLimit'      => 100, // int Maximum dumped var same level array items or object properties number .
		'dumperItemSizeLimit'        => 5000, // int Maximum length of any string or dumped array item .
		'dumperDumpSizeLimit'        => 500000, // int Maximum approximate size of dumped vars result formatted in JSON .
		'detectDumpTraceAndSource'   => false, // bool Autodetect and append trace data to debug .
		'dataStorage'                => null, // PhpConsole\Storage|null Fixes problem with custom $_SESSION handler(see http://goo.gl/Ne8juJ) .
	);

	/**
	 * Varaible for connector
	 *
	 * @var Connector
	 */
	private $connector;

	/**
	 * Public constructor
	 *
	 * @param  array          $options   See \Monolog\Handler\PHPConsoleHandler::$options for more details .
	 * @param  Connector|null $connector Instance of \PhpConsole\Connector class (optional) .
	 * @param  int            $level .
	 * @param  bool           $bubble .
	 * @throws Exception .
	 */
	public function __construct( array $options = array(), Connector $connector = null, $level = Logger::DEBUG, $bubble = true ) {
		if ( ! class_exists( 'PhpConsole\Connector' ) ) {
			throw new Exception( 'PHP Console library not found. See https://github.com/barbushin/php-console#installation' );
		}
		parent::__construct( $level, $bubble );
		$this->options   = $this->initOptions( $options );
		$this->connector = $this->initConnector( $connector );
	}

	/**
	 * Function for init option
	 *
	 * @param array $options .
	 * @throws Exception .
	 */
	private function initOptions( array $options ) {
		$wrongOptions = array_diff( array_keys( $options ), array_keys( $this->options ) ); // @codingStandardsIgnoreLine.
		if ( $wrongOptions ) { // @codingStandardsIgnoreLine.
			throw new Exception( 'Unknown options: ' . implode( ', ', $wrongOptions ) ); // @codingStandardsIgnoreLine.
		}

		return array_replace( $this->options, $options );
	}
	/**
	 * Function to initate connector
	 *
	 * @param Connector $connector .
	 */
	private function initConnector( Connector $connector = null ) {
		if ( ! $connector ) {
			if ( $this->options['dataStorage'] ) {
				Connector::setPostponeStorage( $this->options['dataStorage'] );
			}
			$connector = Connector::getInstance();
		}

		if ( $this->options['registerHelper'] && ! Helper::isRegistered() ) {
			Helper::register();
		}

		if ( $this->options['enabled'] && $connector->isActiveClient() ) {
			if ( $this->options['useOwnErrorsHandler'] || $this->options['useOwnExceptionsHandler'] ) {
				$handler = Handler::getInstance();
				$handler->setHandleErrors( $this->options['useOwnErrorsHandler'] );
				$handler->setHandleExceptions( $this->options['useOwnExceptionsHandler'] );
				$handler->start();
			}
			if ( $this->options['sourcesBasePath'] ) {
				$connector->setSourcesBasePath( $this->options['sourcesBasePath'] );
			}
			if ( $this->options['serverEncoding'] ) {
				$connector->setServerEncoding( $this->options['serverEncoding'] );
			}
			if ( $this->options['password'] ) {
				$connector->setPassword( $this->options['password'] );
			}
			if ( $this->options['enableSslOnlyMode'] ) {
				$connector->enableSslOnlyMode();
			}
			if ( $this->options['ipMasks'] ) {
				$connector->setAllowedIpMasks( $this->options['ipMasks'] );
			}
			if ( $this->options['headersLimit'] ) {
				$connector->setHeadersLimit( $this->options['headersLimit'] );
			}
			if ( $this->options['detectDumpTraceAndSource'] ) {
				$connector->getDebugDispatcher()->detectTraceAndSource = true;
			}
			$dumper                  = $connector->getDumper();
			$dumper->levelLimit      = $this->options['dumperLevelLimit']; // @codingStandardsIgnoreLine.
			$dumper->itemsCountLimit = $this->options['dumperItemsCountLimit']; // @codingStandardsIgnoreLine.
			$dumper->itemSizeLimit   = $this->options['dumperItemSizeLimit']; // @codingStandardsIgnoreLine.
			$dumper->dumpSizeLimit   = $this->options['dumperDumpSizeLimit']; // @codingStandardsIgnoreLine.
			$dumper->detectCallbacks = $this->options['dumperDetectCallbacks']; // @codingStandardsIgnoreLine.
			if ( $this->options['enableEvalListener'] ) {
				$connector->startEvalRequestsListener();
			}
		}

		return $connector;
	}
	/**
	 * Function to get connection
	 */
	public function getConnector() {
		return $this->connector;
	}
	/**
	 * Function to get option
	 */
	public function getOptions() {
		return $this->options;
	}
	/**
	 * Function to handle record
	 *
	 * @param array $record .
	 */
	public function handle( array $record ) {
		if ( $this->options['enabled'] && $this->connector->isActiveClient() ) {
			return parent::handle( $record );
		}

		return ! $this->bubble;
	}

	/**
	 * Writes the record down to the log of the implementing handler
	 *
	 * @param  array $record .
	 * @return void
	 */
	protected function write( array $record ) {
		if ( $record['level'] < Logger::NOTICE ) {
			$this->handleDebugRecord( $record );
		} elseif ( isset( $record['context']['exception'] ) && $record['context']['exception'] instanceof Exception ) {
			$this->handleExceptionRecord( $record );
		} else {
			$this->handleErrorRecord( $record );
		}
	}
	/**
	 * Function to handle debug record
	 *
	 * @param array $record .
	 */
	private function handleDebugRecord( array $record ) {
		$tags    = $this->getRecordTags( $record );
		$message = $record['message'];
		if ( $record['context'] ) {
			$message .= ' ' . json_encode( $this->connector->getDumper()->dump( array_filter( $record['context'] ) ) ); // @codingStandardsIgnoreLine.
		}
		$this->connector->getDebugDispatcher()->dispatchDebug( $message, $tags, $this->options['classesPartialsTraceIgnore'] );
	}
	/**
	 * Function to handle exception record
	 *
	 * @param array $record .
	 */
	private function handleExceptionRecord( array $record ) {
		$this->connector->getErrorsDispatcher()->dispatchException( $record['context']['exception'] );
	}
	/**
	 * Function to handle error record
	 *
	 * @param array $record .
	 */
	private function handleErrorRecord( array $record ) {
		$context = $record['context'];

		$this->connector->getErrorsDispatcher()->dispatchError(
			isset( $context['code'] ) ? $context['code'] : null,
			isset( $context['message'] ) ? $context['message'] : $record['message'],
			isset( $context['file'] ) ? $context['file'] : null,
			isset( $context['line'] ) ? $context['line'] : null,
			$this->options['classesPartialsTraceIgnore']
		);
	}
	/**
	 * Function to get record tag
	 *
	 * @param array $record .
	 */
	private function getRecordTags( array &$record ) {
		$tags = null;
		if ( ! empty( $record['context'] ) ) {
			$context = & $record['context'];
			foreach ( $this->options['debugTagsKeysInContext'] as $key ) {
				if ( ! empty( $context[ $key ] ) ) {
					$tags = $context[ $key ];
					if ( 0 === $key ) {
						array_shift( $context );
					} else {
						unset( $context[ $key ] );
					}
					break;
				}
			}
		}

		return $tags ?: strtolower( $record['level_name'] );
	}

	/**
	 * Function to get default formatter
	 */
	protected function getDefaultFormatter() {
		return new LineFormatter( '%message%' );
	}
}
