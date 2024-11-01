<?php // @codingStandardsIgnoreLine
/**
 * This file for Pure-PHP implementation of RC4.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

/**
 * Pure-PHP implementation of RC4.
 *
 * Uses mcrypt, if available, and an internal implementation, otherwise.
 *
 * PHP version 5
 *
 * Useful resources are as follows:
 *
 *  - {@link http://www.mozilla.org/projects/security/pki/nss/draft-kaukonen-cipher-arcfour-03.txt ARCFOUR Algorithm}
 *  - {@link http://en.wikipedia.org/wiki/RC4 - Wikipedia: RC4}
 *
 * RC4 is also known as ARCFOUR or ARC4.  The reason is elaborated upon at Wikipedia.  This class is named RC4 and not
 * ARCFOUR or ARC4 because RC4 is how it is referred to in the SSH1 specification.
 */

namespace phpseclib\Crypt;

/**
 * Pure-PHP implementation of RC4.
 *
 * @access  public
 */
class RC4 extends Base {

	/**
	 * Constant to encrpt and decrypt
	 *
	 * @access private
	 * @see \phpseclib\Crypt\RC4::_crypt()
	*/
	const ENCRYPT = 0;
	const DECRYPT = 1;
	/**#@-*/

	/**
	 * Block Length of the cipher
	 *
	 * RC4 is a stream cipher
	 * so we the block_size to 0
	 *
	 * @see \phpseclib\Crypt\Base::block_size
	 * @var int
	 * @access private
	 */
	var $block_size = 0; // @codingStandardsIgnoreLine

	/**
	 * Key Length (in bytes)
	 *
	 * @see \phpseclib\Crypt\RC4::setKeyLength()
	 * @var int
	 * @access private
	 */
	var $key_length = 128;// @codingStandardsIgnoreLine

	/**
	 * The mcrypt specific name of the cipher
	 *
	 * @see \phpseclib\Crypt\Base::cipher_name_mcrypt
	 * @var string
	 * @access private
	 */
	var $cipher_name_mcrypt = 'arcfour';// @codingStandardsIgnoreLine

	/**
	 * Holds whether performance-optimized $inline_crypt() can/should be used.
	 *
	 * @see \phpseclib\Crypt\Base::inline_crypt
	 * @var mixed
	 * @access private
	 */
	var $use_inline_crypt = false; // @codingStandardsIgnoreLine

	/**
	 * The Key
	 *
	 * @see self::setKey()
	 * @var string
	 * @access private
	 */
	var $key = "\0"; // @codingStandardsIgnoreLine

	/**
	 * The Key Stream for decryption and encryption
	 *
	 * @see self::setKey()
	 * @var array
	 * @access private
	 */
	var $stream; // @codingStandardsIgnoreLine

	/**
	 * Default Constructor.
	 *
	 * Determines whether or not the mcrypt extension should be used.
	 *
	 * @see \phpseclib\Crypt\Base::__construct()
	 * @return \phpseclib\Crypt\RC4
	 * @access public
	 */
	public function __construct() {
		parent::__construct( Base::MODE_STREAM );
	}

	/**
	 * Test for engine validity
	 *
	 * This is mainly just a wrapper to set things up for \phpseclib\Crypt\Base::isValidEngine()
	 *
	 * @see \phpseclib\Crypt\Base::__construct()
	 * @param int $engine .
	 * @access public
	 * @return bool
	 */
	public function isValidEngine( $engine ) {
		if ( Base::ENGINE_OPENSSL == $engine ) { // WPCS:Loose comparison ok .
			if ( version_compare( PHP_VERSION, '5.3.7' ) >= 0 ) {
				$this->cipher_name_openssl = 'rc4-40';
			} else {
				switch ( strlen( $this->key ) ) {
					case 5:
						$this->cipher_name_openssl = 'rc4-40';
						break;
					case 8:
						$this->cipher_name_openssl = 'rc4-64';
						break;
					case 16:
						$this->cipher_name_openssl = 'rc4';
						break;
					default:
						return false;
				}
			}
		}

		return parent::isValidEngine( $engine );
	}

	/**
	 * Dummy function.
	 *
	 * Some protocols, such as WEP, prepend an "initialization vector" to the key, effectively creating a new key [1].
	 * If you need to use an initialization vector in this manner, feel free to prepend it to the key, yourself, before
	 * calling setKey().
	 *
	 * [1] WEP's initialization vectors (IV's) are used in a somewhat insecure way.  Since, in that protocol,
	 * the IV's are relatively easy to predict, an attack described by
	 * {@link http://www.drizzle.com/~aboba/IEEE/rc4_ksaproc.pdf Scott Fluhrer, Itsik Mantin, and Adi Shamir}
	 * can be used to quickly guess at the rest of the key.  The following links elaborate:
	 *
	 * {@link http://www.rsa.com/rsalabs/node.asp?id=2009 http://www.rsa.com/rsalabs/node.asp?id=2009}
	 * {@link http://en.wikipedia.org/wiki/Related_key_attack http://en.wikipedia.org/wiki/Related_key_attack}
	 *
	 * @param string $iv .
	 * @see self::setKey()
	 * @access public
	 */
	public function setIV( $iv ) {
	}

	/**
	 * Sets the key length
	 *
	 * Keys can be between 1 and 256 bytes long.
	 *
	 * @access public
	 * @param int $length .
	 */
	public function setKeyLength( $length ) {
		if ( $length < 8 ) {
			$this->key_length = 1;
		} elseif ( $length > 2048 ) {
			$this->key_length = 256;
		} else {
			$this->key_length = $length >> 3;
		}

		parent::setKeyLength( $length );
	}

	/**
	 * Encrypts a message.
	 *
	 * @see \phpseclib\Crypt\Base::decrypt()
	 * @see self::_crypt()
	 * @access public
	 * @param string $plaintext .
	 * @return string $ciphertext
	 */
	public function encrypt( $plaintext ) {
		if ( Base::ENGINE_INTERNAL != $this->engine ) { // WPCS:Loose comparison ok .
			return parent::encrypt( $plaintext );
		}
		return $this->_crypt( $plaintext, self::ENCRYPT );
	}

	/**
	 * Decrypts a message.
	 *
	 * $this->decrypt($this->encrypt($plaintext)) == $this->encrypt($this->encrypt($plaintext)).
	 * At least if the continuous buffer is disabled.
	 *
	 * @see \phpseclib\Crypt\Base::encrypt()
	 * @see self::_crypt()
	 * @access public
	 * @param string $ciphertext .
	 * @return string $plaintext
	 */
	public function decrypt( $ciphertext ) {
		if ( Base::ENGINE_INTERNAL != $this->engine ) { // WPCS:Loose comparison ok .
			return parent::decrypt( $ciphertext );
		}
		return $this->_crypt( $ciphertext, self::DECRYPT );
	}

	/**
	 * Encrypts a block
	 *
	 * @access private
	 * @param string $in .
	 */
	private function _encryptBlock( $in ) {// @codingStandardsIgnoreLine
		// RC4 does not utilize this method .
	}

	/**
	 * Decrypts a block
	 *
	 * @access private
	 * @param string $in .
	 */
	private function _decryptBlock( $in ) { // @codingStandardsIgnoreLine
		// RC4 does not utilize this method
	}

	/**
	 * Setup the key (expansion)
	 *
	 * @see \phpseclib\Crypt\Base::_setupKey()
	 * @access private
	 */
	private function _setupKey() { // @codingStandardsIgnoreLine
		$key       = $this->key;
		$keyLength = strlen( $key ); // @codingStandardsIgnoreLine
		$keyStream = range( 0, 255 ); // @codingStandardsIgnoreLine
		$j         = 0;
		for ( $i = 0; $i < 256; $i++ ) {
			$j               = ( $j + $keyStream[ $i ] + ord( $key[ $i % $keyLength ] ) ) & 255; // @codingStandardsIgnoreLine
			$temp            = $keyStream[ $i ]; // @codingStandardsIgnoreLine
			$keyStream[ $i ] = $keyStream[ $j ]; // @codingStandardsIgnoreLine
			$keyStream[ $j ] = $temp; // @codingStandardsIgnoreLine
		}

		$this->stream                  = array();
		$this->stream[ self::DECRYPT ] = $this->stream[ self::ENCRYPT ] = array( // @codingStandardsIgnoreLine
			0, // index $i .
			0, // index $j .
			$keyStream, // @codingStandardsIgnoreLine
		);
	}

	/**
	 * Encrypts or decrypts a message.
	 *
	 * @see self::encrypt()
	 * @see self::decrypt()
	 * @access private
	 * @param string $text .
	 * @param int    $mode .
	 * @return string $text
	 */
	private function _crypt( $text, $mode ) { // @codingStandardsIgnoreLine
		if ( $this->changed ) {
			$this->_setup();
			$this->changed = false;
		}

		$stream = &$this->stream[ $mode ];
		if ( $this->continuousBuffer ) { // @codingStandardsIgnoreLine
			$i         = &$stream[0];
			$j         = &$stream[1];
			$keyStream = &$stream[2]; // @codingStandardsIgnoreLine
		} else {
			$i         = $stream[0];
			$j         = $stream[1];
			$keyStream = $stream[2]; // @codingStandardsIgnoreLine
		}

		$len = strlen( $text );
		for ( $k = 0; $k < $len; ++$k ) {
			$i   = ( $i + 1 ) & 255; // @codingStandardsIgnoreLine
			$ksi = $keyStream[ $i ]; // @codingStandardsIgnoreLine
			$j   = ( $j + $ksi ) & 255;
			$ksj = $keyStream[ $j ]; // @codingStandardsIgnoreLine

			$keyStream[ $i ] = $ksj; // @codingStandardsIgnoreLine
			$keyStream[ $j ] = $ksi; // @codingStandardsIgnoreLine
			$text[ $k ]      = $text[ $k ] ^ chr( $keyStream[ ( $ksj + $ksi ) & 255 ] ); // @codingStandardsIgnoreLine
		}

		return $text;
	}
}
