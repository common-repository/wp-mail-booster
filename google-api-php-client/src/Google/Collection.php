<?php // @codingStandardsIgnoreLine
/**
 * This file is for google collection
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/src/google
 * @version 2.0.0
 */

if ( ! class_exists( 'Google_Client' ) ) {
	require_once __DIR__ . '/autoload.php';
}

/**
 * Extension to the regular Google_Model that automatically
 * exposes the items array for iteration, so you can just
 * iterate over the object rather than a reference inside.
 */
class Google_Collection extends Google_Model implements Iterator, Countable {
	/**
	 * Variable is collection key .
	 *
	 * @var $collection_key
	 */
	protected $collection_key = 'items';
	/**
	 * This function is used to rewind
	 */
	public function rewind() {
		if ( isset( $this->{$this->collection_key} )
		&& is_array( $this->{$this->collection_key} ) ) {
			reset( $this->{$this->collection_key} );
		}
	}
	/**
	 * This function is for current
	 */
	public function current() {
		$this->coerceType( $this->key() );
		if ( is_array( $this->{$this->collection_key} ) ) {
			return current( $this->{$this->collection_key} );
		}
	}

	/**
	 * This function is for key of collection
	 */
	public function key() {
		if ( isset( $this->{$this->collection_key} )
		&& is_array( $this->{$this->collection_key} ) ) {
			return key( $this->{$this->collection_key} );
		}
	}

	/**
	 * This function is used to rewind
	 */
	public function next() {
		return next( $this->{$this->collection_key} );
	}

	/**
	 * This function is used to valid key
	 */
	public function valid() {
		$key = $this->key();
		return null !== $key && false !== $key;
	}

	/**
	 * This function is used to count
	 */
	public function count() {
		if ( ! isset( $this->{$this->collection_key} ) ) {
			return 0;
		}
		return count( $this->{$this->collection_key} );
	}
	/**
	 * This function is used to check offset exist
	 *
	 * @param string $offset .
	 */
	public function offsetExists( $offset ) {
		if ( ! is_numeric( $offset ) ) {
			return parent::offsetExists( $offset );
		}
		return isset( $this->{$this->collection_key}[ $offset ] );
	}
	/**
	 * This function is used to get offset
	 *
	 * @param string $offset .
	 */
	public function offsetGet( $offset ) {
		if ( ! is_numeric( $offset ) ) {
			return parent::offsetGet( $offset );
		}
		$this->coerceType( $offset );
		return $this->{$this->collection_key}[ $offset ];
	}
	/**
	 * This function is used to count
	 *
	 * @param string $offset .
	 * @param string $value .
	 */
	public function offsetSet( $offset, $value ) {
		if ( ! is_numeric( $offset ) ) {
			return parent::offsetSet( $offset, $value );
		}
		$this->{$this->collection_key}[ $offset ] = $value;
	}
	/**
	 * This function is used to count
	 *
	 * @param string $offset .
	 */
	public function offsetUnset( $offset ) {
		if ( ! is_numeric( $offset ) ) {
			return parent::offsetUnset( $offset );
		}
		unset( $this->{$this->collection_key}[ $offset ] );
	}
	/**
	 * This function is used to count
	 *
	 * @param string $offset .
	 */
	private function coerceType( $offset ) {
		$typeKey = $this->keyType( $this->collection_key ); // @codingStandardsIgnoreLine
		if ( isset( $this->$typeKey ) && ! is_object( $this->{$this->collection_key}[ $offset ] ) ) { // @codingStandardsIgnoreLine
			$type                                     = $this->$typeKey; // @codingStandardsIgnoreLine
			$this->{$this->collection_key}[ $offset ] =
			new $type( $this->{$this->collection_key}[ $offset ] );
		}
	}
}
