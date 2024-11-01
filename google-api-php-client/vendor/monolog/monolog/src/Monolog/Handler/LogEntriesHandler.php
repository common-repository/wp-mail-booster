<?php // @codingStandardsIgnoreLine.
/**
 * This file is part of the Monolog package.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/handler
 * @version 2.0.0
 */
namespace Monolog\Handler;

use Monolog\Logger;

/**
 * This  class is LogEntriesHandler.
 */
class LogEntriesHandler extends SocketHandler {

	/**
	 * The version of the plugin.
	 *
	 * @var string
	 */
	protected $logToken;// @codingStandardsIgnoreLine.

	/**
	 * This function is __construct.
	 *
	 * @param string $token  Log token supplied by LogEntries .
	 * @param bool   $useSSL Whether or not SSL encryption should be used .
	 * @param int    $level  The minimum logging level to trigger this handler .
	 * @param bool   $bubble Whether or not messages that are handled should bubble up the stack .
	 *
	 * @throws MissingExtensionException If SSL encryption is set to true and OpenSSL is missing .
	 */
	public function __construct( $token, $useSSL = true, $level = Logger::DEBUG, $bubble = true ) {// @codingStandardsIgnoreLine.
		if ( $useSSL && ! extension_loaded( 'openssl' ) ) {// @codingStandardsIgnoreLine.
			throw new MissingExtensionException( 'The OpenSSL PHP plugin is required to use SSL encrypted connection for LogEntriesHandler' );
		}

		$endpoint = $useSSL ? 'ssl://data.logentries.com:443' : 'data.logentries.com:80';// @codingStandardsIgnoreLine.
		parent::__construct( $endpoint, $level, $bubble );
		$this->logToken = $token;// @codingStandardsIgnoreLine.
	}

	/**
	 * This function is generateDataStream.
	 *
	 * {@inheritdoc}
	 *
	 * @param  array $record .
	 * @return string
	 */
	protected function generateDataStream( $record ) {
		return $this->logToken . ' ' . $record['formatted'];// @codingStandardsIgnoreLine.
	}
}
