<?php //@codingStandardsIgnoreLine
/**
 * This file is TagProcessor.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

namespace Monolog\Processor;

/**
 * Adds a tags array into record
 */
class TagProcessor {
	/**
	 * Version of this plugin.
	 *
	 * @access private
	 * @var    $tags
	 */
	private $tags;
	/**
	 * This function is __construct.
	 *
	 * @param array $tags passes parameter as tags.
	 */
	public function __construct( array $tags = array() ) {
		$this->setTags( $tags );
	}
	/**
	 * This function is addTags.
	 *
	 * @param array $tags passes parameter as tags.
	 */
	public function addTags( array $tags = array() ) {// @codingStandardsIgnoreLine
		$this->tags = array_merge( $this->tags, $tags );
	}
	/**
	 * This function is setTags.
	 *
	 * @param array $tags passes parameter as tags.
	 */
	public function setTags( array $tags = array() ) {// @codingStandardsIgnoreLine
		$this->tags = $tags;
	}
	/**
	 * This function is __invoke.
	 *
	 * @param array $record passes parameter as record.
	 */
	public function __invoke( array $record ) {
		$record['extra']['tags'] = $this->tags;

		return $record;
	}
}
