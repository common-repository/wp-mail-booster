<?php // @codingStandardsIgnoreLine
/**
 * This file to Format a log message into an Elastica Document.
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

use Elastica\Document;

/**
 * Format a log message into an Elastica Document
 */
class ElasticaFormatter extends NormalizerFormatter {

	/**
	 * The version of this plugin.
	 *
	 * @access protected
	 * @var string $index .
	 */
	protected $index;

	/**
	 * The version of this plugin.
	 *
	 * @access protected
	 * @var string $type .
	 */
	protected $type;

	/**
	 * This function is __construct.
	 *
	 * @param string $index Elastic Search index name .
	 * @param string $type  Elastic Search document type .
	 */
	public function __construct( $index, $type ) {
		// elasticsearch requires a ISO 8601 format date with optional millisecond precision.
		parent::__construct( 'Y-m-d\TH:i:s.uP' );

		$this->index = $index;
		$this->type  = $type;
	}

	/**
	 * This function is format.
	 *
	 * @param array $record .
	 * {@inheritdoc} .
	 */
	public function format( array $record ) {
		$record = parent::format( $record );

		return $this->getDocument( $record );
	}

	/**
	 * Getter index
	 *
	 * @return string
	 */
	public function getIndex() {
		return $this->index;
	}

	/**
	 * Getter type
	 *
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * Convert a log message into an Elastica Document
	 *
	 * @param  array $record Log message .
	 * @return Document
	 */
	protected function getDocument( $record ) {
		$document = new Document();
		$document->setData( $record );
		$document->setType( $this->type );
		$document->setIndex( $this->index );

		return $document;
	}
}
