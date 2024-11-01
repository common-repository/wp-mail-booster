<?php // @codingStandardsIgnoreLine.
/**
 * This file is part of the Monolog package.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/handler
 * @version 2.0.0
 */
namespace Monolog\Handler;

use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\ElasticaFormatter;
use Monolog\Logger;
use Elastica\Client;
use Elastica\Exception\ExceptionInterface;

/**
 * Elastic Search handler
 *
 * Usage example:
 *
 *    $client = new \Elastica\Client();
 *    $options = array(
 *        'index' => 'elastic_index_name',
 *        'type' => 'elastic_doc_type',
 *    );
 *    $handler = new ElasticSearchHandler($client, $options);
 *    $log = new Logger('application');
 *    $log->pushHandler($handler);
 */
class ElasticSearchHandler extends AbstractProcessingHandler {

	/**
	 * The version of this plugin.
	 *
	 * @var Client
	 */
	protected $client;

	/**
	 * The version of this plugin
	 *
	 * @var array Handler config options
	 */
	protected $options = array();

	/**
	 * This functiion is __construct.
	 *
	 * @param Client  $client  Elastica Client object .
	 * @param array   $options Handler configuration .
	 * @param int     $level   The minimum logging level at which this handler will be triggered .
	 * @param Boolean $bubble  Whether the messages that are handled can bubble up the stack or not .
	 */
	public function __construct( Client $client, array $options = array(), $level = Logger::DEBUG, $bubble = true ) {
		parent::__construct( $level, $bubble );
		$this->client  = $client;
		$this->options = array_merge(
			array(
				'index'        => 'monolog',      // Elastic index name .
				'type'         => 'record',       // Elastic document type .
				'ignore_error' => false,          // Suppress Elastica exceptions .
			),
			$options
		);
	}

	/**
	 * This function is  write .
	 *
	 * @param array $record .
	 * {@inheritDoc}.
	 */
	protected function write( array $record ) {
		$this->bulkSend( array( $record['formatted'] ) );
	}

	/**
	 * This function is  write .
	 *
	 * @param FormatterInterface $formatter .
	 * @throws \InvalidArgumentException .
	 * {@inheritdoc}.
	 */
	public function setFormatter( FormatterInterface $formatter ) {
		if ( $formatter instanceof ElasticaFormatter ) {
			return parent::setFormatter( $formatter );
		}
		throw new \InvalidArgumentException( 'ElasticSearchHandler is only compatible with ElasticaFormatter' );
	}

	/**
	 * Getter options
	 *
	 * @return array
	 */
	public function getOptions() {
		return $this->options;
	}

	/**
	 * This function is  write .
	 *
	 * {@inheritDoc}
	 */
	protected function getDefaultFormatter() {
		return new ElasticaFormatter( $this->options['index'], $this->options['type'] );
	}

	/**
	 * This function is  write .
	 *
	 * @param array $records .
	 * {@inheritdoc}.
	 */
	public function handleBatch( array $records ) {
		$documents = $this->getFormatter()->formatBatch( $records );
		$this->bulkSend( $documents );
	}

	/**
	 * Use Elasticsearch bulk API to send list of documents
	 *
	 * @param  array $documents .
	 * @throws \RuntimeException .
	 */
	protected function bulkSend( array $documents ) {
		try {
			$this->client->addDocuments( $documents );
		} catch ( ExceptionInterface $e ) {
			if ( ! $this->options['ignore_error'] ) {
				throw new \RuntimeException( 'Error sending messages to Elasticsearch', 0, $e );
			}
		}
	}
}
