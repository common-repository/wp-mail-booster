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
use Monolog\Formatter\JsonFormatter;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Channel\AMQPChannel;
use AMQPExchange;

	/**
	 * This class is AmqpHandler.
	 */
class AmqpHandler extends AbstractProcessingHandler {

	/**
	 * The version of this plugin.
	 *
	 * @var AMQPExchange|AMQP Channel $exchange
	 */
	protected $exchange;

	/**
	 * The version of this plugin.
	 *
	 * @var string
	 */
	protected $exchangeName; // @codingStandardsIgnoreLine.

	/**
	 * This function is __construct .
	 *
	 * @param AMQPExchange|AMQPChannel $exchange     AMQPExchange (php AMQP ext) or PHP AMQP lib channel, ready for use .
	 * @param string                   $exchangeName .
	 * @param int                      $level .
	 * @param bool                     $bubble       Whether the messages that are handled can bubble up the stack or not .
	 * @throws \InvalidArgumentException .
	 */
	public function __construct( $exchange, $exchangeName = 'log', $level = Logger::DEBUG, $bubble = true ) {// @codingStandardsIgnoreLine.
		if ( $exchange instanceof AMQPExchange ) {
			$exchange->setName( $exchangeName );// @codingStandardsIgnoreLine.
		} elseif ( $exchange instanceof AMQPChannel ) {
			$this->exchangeName = $exchangeName;// @codingStandardsIgnoreLine.
		} else {
			throw new \InvalidArgumentException( 'PhpAmqpLib\Channel\AMQPChannel or AMQPExchange instance required' );
		}
		$this->exchange = $exchange;

		parent::__construct( $level, $bubble );
	}

	/**
	 * The version of this plugin.
	 *
	 * @param array $record .
	 * {@inheritDoc}.
	 */
	protected function write( array $record ) {
		$data       = $record['formatted'];
		$routingKey = $this->getRoutingKey( $record );// @codingStandardsIgnoreLine.

		if ( $this->exchange instanceof AMQPExchange ) {
			$this->exchange->publish(
				$data,
				$routingKey,// @codingStandardsIgnoreLine.
				0,
				array(
					'delivery_mode' => 2,
					'content_type'  => 'application/json',
				)
			);
		} else {
			$this->exchange->basic_publish(
				$this->createAmqpMessage( $data ),
				$this->exchangeName,// @codingStandardsIgnoreLine.
				$routingKey// @codingStandardsIgnoreLine.
			);
		}
	}

	/**
	 * The version of this plugin.
	 *
	 * @param array $records .
	 * {@inheritDoc}.
	 */
	public function handleBatch( array $records ) {
		if ( $this->exchange instanceof AMQPExchange ) {
			parent::handleBatch( $records );

			return;
		}

		foreach ( $records as $record ) {
			if ( ! $this->isHandling( $record ) ) {
				continue;
			}

			$record = $this->processRecord( $record );
			$data   = $this->getFormatter()->format( $record );

			$this->exchange->batch_basic_publish(
				$this->createAmqpMessage( $data ),
				$this->exchangeName,// @codingStandardsIgnoreLine.
				$this->getRoutingKey( $record )
			);
		}

		$this->exchange->publish_batch();
	}

	/**
	 * Gets the routing key for the AMQP exchange
	 *
	 * @param  array $record .
	 * @return string
	 */
	protected function getRoutingKey( array $record ) {
		$routingKey = sprintf(// @codingStandardsIgnoreLine.
			'%s.%s',
			// TODO 2.0 remove substr call .
			substr( $record['level_name'], 0, 4 ),
			$record['channel']
		);

		return strtolower( $routingKey );// @codingStandardsIgnoreLine.
	}

	/**
	 * This function is createAmqpMessage.
	 *
	 * @param  string $data .
	 * @return AMQPMessage
	 */
	private function createAmqpMessage( $data ) {
		return new AMQPMessage(
			(string) $data,
			array(
				'delivery_mode' => 2,
				'content_type'  => 'application/json',
			)
		);
	}

	/**
	 * {@inheritDoc}
	 */
	protected function getDefaultFormatter() {
		return new JsonFormatter( JsonFormatter::BATCH_MODE_JSON, false );
	}
}
