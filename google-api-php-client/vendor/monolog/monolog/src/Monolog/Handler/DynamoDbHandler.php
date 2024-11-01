<?php  // @codingStandardsIgnoreLine.
/**
 * This file is part of the Monolog package.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/handler
 * @version 2.0.0
 */
namespace Monolog\Handler;

use Aws\Sdk;
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;
use Monolog\Formatter\ScalarFormatter;
use Monolog\Logger;

/**
 * Amazon DynamoDB handler (http://aws.amazon.com/dynamodb/)
 *
 * @link https://github.com/aws/aws-sdk-php/
 */
class DynamoDbHandler extends AbstractProcessingHandler {

	const DATE_FORMAT = 'Y-m-d\TH:i:s.uO';

	/**
	 * The version of this plugin.
	 *
	 * @var DynamoDbClient
	 */
	protected $client;

	/**
	 * The version of this plugin.
	 *
	 * @var string
	 */
	protected $table;

	/**
	 * The version of this plugin.
	 *
	 * @var int
	 */
	protected $version;

	/**
	 * The version of this plugin.
	 *
	 * @var Marshaler
	 */
	protected $marshaler;

	/**
	 * This function is __construct .
	 *
	 * @param DynamoDbClient $client .
	 * @param string         $table .
	 * @param int            $level .
	 * @param bool           $bubble .
	 */
	public function __construct( DynamoDbClient $client, $table, $level = Logger::DEBUG, $bubble = true ) {
		if ( defined( 'Aws\Sdk::VERSION' ) && version_compare( Sdk::VERSION, '3.0', '>=' ) ) {
			$this->version   = 3;
			$this->marshaler = new Marshaler();
		} else {
			$this->version = 2;
		}

		$this->client = $client;
		$this->table  = $table;

		parent::__construct( $level, $bubble );
	}

	/**
	 * The version of this plugin.
	 *
	 * @param  array $record .
	 * {@inheritdoc}.
	 */
	protected function write( array $record ) {
		$filtered = $this->filterEmptyFields( $record['formatted'] );
		if ( $this->version === 3 ) {// @codingStandardsIgnoreLine.
			$formatted = $this->marshaler->marshalItem( $filtered );
		} else {
			$formatted = $this->client->formatAttributes( $filtered );
		}

		$this->client->putItem(
			array(
				'TableName' => $this->table,
				'Item'      => $formatted,
			)
		);
	}

	/**
	 * This function is filterEmptyFields .
	 *
	 * @param  array $record .
	 * @return array
	 */
	protected function filterEmptyFields( array $record ) {
		return array_filter(
			$record, function ( $value ) {
				return ! empty( $value ) || false === $value || 0 === $value;
			}
		);
	}

	/**
	 * This function is getDefaultFormatter.
	 *
	 * {@inheritdoc}
	 */
	protected function getDefaultFormatter() {
		return new ScalarFormatter( self::DATE_FORMAT );
	}
}
