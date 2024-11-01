<?php // @codingStandardsIgnoreLine
/**
 * This file to Lazily reads or writes to a file.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor/composer
 * @version 2.0.0
 */

namespace GuzzleHttp\Psr7;

use Psr\Http\Message\StreamInterface;

/**
 * Lazily reads or writes to a file that is opened only after an IO operation
 * take place on the stream.
 */
class LazyOpenStream implements StreamInterface {

	use StreamDecoratorTrait;
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      array    $filename  .
	 */
	private $filename;
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      array    $mode  .
	 */
	private $mode;

	/**
	 * This function is __construct.
	 *
	 * @param string $filename File to lazily open.
	 * @param string $mode     fopen mode to use when opening the stream.
	 */
	public function __construct( $filename, $mode ) {
		$this->filename = $filename;
		$this->mode     = $mode;
	}

	/**
	 * Creates the underlying stream lazily when required.
	 *
	 * @return StreamInterface
	 */
	protected function createStream() {
		return stream_for( try_fopen( $this->filename, $this->mode ) );
	}
}
