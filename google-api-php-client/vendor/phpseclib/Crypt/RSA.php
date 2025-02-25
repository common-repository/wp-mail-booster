<?php // @codingStandardsIgnoreLine.
/**
 * This file for Pure-PHP PKCS#1 (v2.1) compliant implementation of RSA.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

/**
 * Pure-PHP PKCS#1 (v2.1) compliant implementation of RSA.
 *
 * PHP version 5
 */

namespace phpseclib\Crypt;

use phpseclib\Math\BigInteger;

/**
 * Pure-PHP PKCS#1 compliant implementation of RSA.
 *
 * @access  public
 */
class RSA {

	/**
	 * Use {@link http://en.wikipedia.org/wiki/Optimal_Asymmetric_Encryption_Padding Optimal Asymmetric Encryption Padding}
	 * (OAEP) for encryption / decryption.
	 *
	 * Uses sha1 by default.
	 *
	 * @see self::setHash()
	 * @see self::setMGFHash()
	 */
	const ENCRYPTION_OAEP = 1;
	/**
	 * Use PKCS#1 padding.
	 *
	 * Although self::ENCRYPTION_OAEP offers more security, including PKCS#1 padding is necessary for purposes of backwards
	 * compatibility with protocols (like SSH-1) written before OAEP's introduction.
	 */
	const ENCRYPTION_PKCS1 = 2;
	/**
	 * Do not use any padding
	 *
	 * Although this method is not recommended it can none-the-less sometimes be useful if you're trying to decrypt some legacy
	 * stuff, if you're trying to diagnose why an encrypted message isn't decrypting, etc.
	 */
	const ENCRYPTION_NONE = 3;

	/**
	 * Use the Probabilistic Signature Scheme for signing
	 *
	 * Uses sha1 by default.
	 *
	 * @see self::setSaltLength()
	 * @see self::setMGFHash()
	 */
	const SIGNATURE_PSS = 1;
	/**
	 * Use the PKCS#1 scheme by default.
	 *
	 * Although self::SIGNATURE_PSS offers more security, including PKCS#1 signing is necessary for purposes of backwards
	 * compatibility with protocols (like SSH-2) written before PSS's introduction.
	 */
	const SIGNATURE_PKCS1 = 2;
	/**#@-*/

	/**#@+
	 *
	 * @access private
	 * @see \phpseclib\Crypt\RSA::createKey()
	*/
	/**
	 * ASN1 Integer
	 */
	const ASN1_INTEGER = 2;
	/**
	 * ASN1 Bit String
	 */
	const ASN1_BITSTRING = 3;
	/**
	 * ASN1 Octet String
	 */
	const ASN1_OCTETSTRING = 4;
	/**
	 * ASN1 Object Identifier
	 */
	const ASN1_OBJECT = 6;
	/**
	 * ASN1 Sequence (with the constucted bit set)
	 */
	const ASN1_SEQUENCE = 48;
	/**#@-*/

	/**#@+
	 *
	 * @access private
	 * @see \phpseclib\Crypt\RSA::__construct()
	*/
	/**
	 * To use the pure-PHP implementation
	 */
	const MODE_INTERNAL = 1;
	/**
	 * To use the OpenSSL library
	 *
	 * (if enabled; otherwise, the internal implementation will be used)
	 */
	const MODE_OPENSSL = 2;
	/**#@-*/

	/**#@+
	 *
	 * @access public
	 * @see \phpseclib\Crypt\RSA::createKey()
	 * @see \phpseclib\Crypt\RSA::setPrivateKeyFormat()
	*/
	/**
	 * PKCS#1 formatted private key
	 *
	 * Used by OpenSSH
	 */
	const PRIVATE_FORMAT_PKCS1 = 0;
	/**
	 * PuTTY formatted private key
	 */
	const PRIVATE_FORMAT_PUTTY = 1;
	/**
	 * XML formatted private key
	 */
	const PRIVATE_FORMAT_XML = 2;
	/**
	 * PKCS#8 formatted private key
	 */
	const PRIVATE_FORMAT_PKCS8 = 8;
	/**#@-*/

	/**#@+
	 *
	 * @access public
	 * @see \phpseclib\Crypt\RSA::createKey()
	 * @see \phpseclib\Crypt\RSA::setPublicKeyFormat()
	*/
	/**
	 * Raw public key
	 *
	 * An array containing two \phpseclib\Math\BigInteger objects.
	 *
	 * The exponent can be indexed with any of the following:
	 *
	 * 0, e, exponent, publicExponent
	 *
	 * The modulus can be indexed with any of the following:
	 *
	 * 1, n, modulo, modulus
	 */
	const PUBLIC_FORMAT_RAW = 3;
	/**
	 * PKCS#1 formatted public key (raw)
	 *
	 * Used by File/X509.php
	 *
	 * Has the following header:
	 *
	 * -----BEGIN RSA PUBLIC KEY-----
	 *
	 * Analogous to ssh-keygen's pem format (as specified by -m)
	 */
	const PUBLIC_FORMAT_PKCS1     = 4;
	const PUBLIC_FORMAT_PKCS1_RAW = 4;
	/**
	 * XML formatted public key
	 */
	const PUBLIC_FORMAT_XML = 5;
	/**
	 * OpenSSH formatted public key
	 *
	 * Place in $HOME/.ssh/authorized_keys
	 */
	const PUBLIC_FORMAT_OPENSSH = 6;
	/**
	 * PKCS#1 formatted public key (encapsulated)
	 *
	 * Used by PHP's openssl_public_encrypt() and openssl's rsautl (when -pubin is set)
	 *
	 * Has the following header:
	 *
	 * -----BEGIN PUBLIC KEY-----
	 *
	 * Analogous to ssh-keygen's pkcs8 format (as specified by -m). Although PKCS8
	 * is specific to private keys it's basically creating a DER-encoded wrapper
	 * for keys. This just extends that same concept to public keys (much like ssh-keygen)
	 */
	const PUBLIC_FORMAT_PKCS8 = 7;
	/**#@-*/

	/**
	 * Precomputed Zero
	 *
	 * @var \phpseclib\Math\BigInteger
	 * @access private
	 */
	var $zero; // @codingStandardsIgnoreLine.

	/**
	 * Precomputed One
	 *
	 * @var \phpseclib\Math\BigInteger
	 * @access private
	 */
	var $one; // @codingStandardsIgnoreLine.

	/**
	 * Private Key Format
	 *
	 * @var int
	 * @access private
	 */
	var $privateKeyFormat = self::PRIVATE_FORMAT_PKCS1; // @codingStandardsIgnoreLine.

	/**
	 * Public Key Format
	 *
	 * @var int
	 * @access public
	 */
	var $publicKeyFormat = self::PUBLIC_FORMAT_PKCS8; // @codingStandardsIgnoreLine.

	/**
	 * Modulus (ie. n)
	 *
	 * @var \phpseclib\Math\BigInteger
	 * @access private
	 */
	var $modulus; // @codingStandardsIgnoreLine.

	/**
	 * Modulus length
	 *
	 * @var \phpseclib\Math\BigInteger
	 * @access private
	 */
	var $k; // @codingStandardsIgnoreLine.

	/**
	 * Exponent (ie. e or d)
	 *
	 * @var \phpseclib\Math\BigInteger
	 * @access private
	 */
	var $exponent; // @codingStandardsIgnoreLine.

	/**
	 * Primes for Chinese Remainder Theorem (ie. p and q)
	 *
	 * @var array
	 * @access private
	 */
	var $primes; // @codingStandardsIgnoreLine.

	/**
	 * Exponents for Chinese Remainder Theorem (ie. dP and dQ)
	 *
	 * @var array
	 * @access private
	 */
	var $exponents; // @codingStandardsIgnoreLine.

	/**
	 * Coefficients for Chinese Remainder Theorem (ie. qInv)
	 *
	 * @var array
	 * @access private
	 */
	var $coefficients; // @codingStandardsIgnoreLine.

	/**
	 * Hash name
	 *
	 * @var string
	 * @access private
	 */
	var $hashName; // @codingStandardsIgnoreLine.

	/**
	 * Hash function
	 *
	 * @var \phpseclib\Crypt\Hash
	 * @access private
	 */
	var $hash; // @codingStandardsIgnoreLine.

	/**
	 * Length of hash function output
	 *
	 * @var int
	 * @access private
	 */
	var $hLen; // @codingStandardsIgnoreLine.

	/**
	 * Length of salt
	 *
	 * @var int
	 * @access private
	 */
	var $sLen; // @codingStandardsIgnoreLine.

	/**
	 * Hash function for the Mask Generation Function
	 *
	 * @var \phpseclib\Crypt\Hash
	 * @access private
	 */
	var $mgfHash; // @codingStandardsIgnoreLine.

	/**
	 * Length of MGF hash function output
	 *
	 * @var int
	 * @access private
	 */
	var $mgfHLen; // @codingStandardsIgnoreLine.

	/**
	 * Encryption mode
	 *
	 * @var int
	 * @access private
	 */
	var $encryptionMode = self::ENCRYPTION_OAEP; // @codingStandardsIgnoreLine.

	/**
	 * Signature mode
	 *
	 * @var int
	 * @access private
	 */
	var $signatureMode = self::SIGNATURE_PSS; // @codingStandardsIgnoreLine.

	/**
	 * Public Exponent
	 *
	 * @var mixed
	 * @access private
	 */
	var $publicExponent = false; // @codingStandardsIgnoreLine.

	/**
	 * Password
	 *
	 * @var string
	 * @access private
	 */
	var $password = false; // @codingStandardsIgnoreLine.

	/**
	 * Components
	 *
	 * For use with parsing XML formatted keys.  PHP's XML Parser functions use utilized - instead of PHP's DOM functions -
	 * because PHP's XML Parser functions work on PHP4 whereas PHP's DOM functions - although surperior - don't.
	 *
	 * @see self::_start_element_handler()
	 * @var array
	 * @access private
	 */
	var $components = array(); // @codingStandardsIgnoreLine.

	/**
	 * Current String
	 *
	 * For use with parsing XML formatted keys.
	 *
	 * @see self::_character_handler()
	 * @see self::_stop_element_handler()
	 * @var mixed
	 * @access private
	 */
	var $current; // @codingStandardsIgnoreLine.

	/**
	 * OpenSSL configuration file name.
	 *
	 * Set to null to use system configuration file.
	 *
	 * @see self::createKey()
	 * @var mixed
	 * @Access public
	 */
	var $configFile; // @codingStandardsIgnoreLine.

	/**
	 * Public key comment field.
	 *
	 * @var string
	 * @access private
	 */
	var $comment = 'phpseclib-generated-key'; // @codingStandardsIgnoreLine.

	/**
	 * The constructor
	 *
	 * If you want to make use of the openssl extension, you'll need to set the mode manually, yourself.  The reason
	 * \phpseclib\Crypt\RSA doesn't do it is because OpenSSL doesn't fail gracefully.  openssl_pkey_new(), in particular, requires
	 * openssl.cnf be present somewhere and, unfortunately, the only real way to find out is too late.
	 *
	 * @return \phpseclib\Crypt\RSA
	 * @access public
	 */
	public function __construct() {
		$this->configFile = dirname( __FILE__ ) . '/../openssl.cnf'; // @codingStandardsIgnoreLine.

		if ( ! defined( 'CRYPT_RSA_MODE' ) ) {
			switch ( true ) {
				// Math/BigInteger's openssl requirements are a little less stringent than Crypt/RSA's. in particular,
				// Math/BigInteger doesn't require an openssl.cfg file whereas Crypt/RSA does. so if Math/BigInteger
				// can't use OpenSSL it can be pretty trivially assumed, then, that Crypt/RSA can't either.
				case defined( 'MATH_BIGINTEGER_OPENSSL_DISABLE' ):
					define( 'CRYPT_RSA_MODE', self::MODE_INTERNAL );
					break;
				case extension_loaded( 'openssl' ) && file_exists( $this->configFile ): // @codingStandardsIgnoreLine.
					// some versions of XAMPP have mismatched versions of OpenSSL which causes it not to work .
					ob_start();
					@phpinfo(); // @codingStandardsIgnoreLine.
					$content = ob_get_contents();
					ob_end_clean();

					preg_match_all( '#OpenSSL (Header|Library) Version(.*)#im', $content, $matches );

					$versions = array();
					if ( ! empty( $matches[1] ) ) {
						for ( $i = 0; $i < count( $matches[1] ); $i++ ) { // @codingStandardsIgnoreLine.
							$fullVersion = trim( str_replace( '=>', '', strip_tags( $matches[2][ $i ] ) ) ); // @codingStandardsIgnoreLine.

							// Remove letter part in OpenSSL version .
							if ( ! preg_match( '/(\d+\.\d+\.\d+)/i', $fullVersion, $m ) ) { // @codingStandardsIgnoreLine.
								$versions[ $matches[1][ $i ] ] = $fullVersion; // @codingStandardsIgnoreLine.
							} else {
								$versions[ $matches[1][ $i ] ] = $m[0];
							}
						}
					}

					// it doesn't appear that OpenSSL versions were reported upon until PHP 5.3+ .
					switch ( true ) {
						case ! isset( $versions['Header'] ):
						case ! isset( $versions['Library'] ):
						case $versions['Header'] == $versions['Library']: // WPCS:Loose comparison ok.
						case version_compare( $versions['Header'], '1.0.0' ) >= 0 && version_compare( $versions['Library'], '1.0.0' ) >= 0:
							define( 'CRYPT_RSA_MODE', self::MODE_OPENSSL );
							break;
						default:
							define( 'CRYPT_RSA_MODE', self::MODE_INTERNAL );
							define( 'MATH_BIGINTEGER_OPENSSL_DISABLE', true );
					}
					break;
				default:
					define( 'CRYPT_RSA_MODE', self::MODE_INTERNAL );
			}
		}

		$this->zero = new BigInteger();
		$this->one  = new BigInteger( 1 );

		$this->hash     = new Hash( 'sha1' );
		$this->hLen     = $this->hash->getLength(); // @codingStandardsIgnoreLine.
		$this->hashName = 'sha1'; // @codingStandardsIgnoreLine.
		$this->mgfHash  = new Hash( 'sha1' ); // @codingStandardsIgnoreLine.
		$this->mgfHLen  = $this->mgfHash->getLength(); // @codingStandardsIgnoreLine.
	}

	/**
	 * Create public / private key pair
	 *
	 * Returns an array with the following three elements:
	 *  - 'privatekey': The private key.
	 *  - 'publickey':  The public key.
	 *  - 'partialkey': A partially computed key (if the execution time exceeded $timeout).
	 *                  Will need to be passed back to \phpseclib\Crypt\RSA::createKey() as the third parameter for further processing.
	 *
	 * @access public
	 * @param int   $bits .
	 * @param int   $timeout .
	 * @param array $partial .
	 */
	public function createKey( $bits = 1024, $timeout = false, $partial = array() ) { // @codingStandardsIgnoreLine.
		if ( ! defined( 'CRYPT_RSA_EXPONENT' ) ) {
			// http://en.wikipedia.org/wiki/65537_%28number%29 .
			define( 'CRYPT_RSA_EXPONENT', '65537' );
		}
		// per <http://cseweb.ucsd.edu/~hovav/dist/survey.pdf#page=5>, this number ought not result in primes smaller
		// than 256 bits. as a consequence if the key you're trying to create is 1024 bits and you've set CRYPT_RSA_SMALLEST_PRIME
		// to 384 bits then you're going to get a 384 bit prime and a 640 bit prime (384 + 1024 % 384). at least if
		// CRYPT_RSA_MODE is set to self::MODE_INTERNAL. if CRYPT_RSA_MODE is set to self::MODE_OPENSSL then
		// CRYPT_RSA_SMALLEST_PRIME is ignored (ie. multi-prime RSA support is more intended as a way to speed up RSA key
		// generation when there's a chance neither gmp nor OpenSSL are installed) .
		if ( ! defined( 'CRYPT_RSA_SMALLEST_PRIME' ) ) {
			define( 'CRYPT_RSA_SMALLEST_PRIME', 4096 );
		}

		// OpenSSL uses 65537 as the exponent and requires RSA keys be 384 bits minimum .
		if ( CRYPT_RSA_MODE == self::MODE_OPENSSL && $bits >= 384 && CRYPT_RSA_EXPONENT == 65537 ) { // WPCS:Loose comparison ok.
			$config = array();
			if ( isset( $this->configFile ) ) { // @codingStandardsIgnoreLine.
				$config['config'] = $this->configFile; // @codingStandardsIgnoreLine.
			}
			$rsa = openssl_pkey_new( array( 'private_key_bits' => $bits ) + $config );
			openssl_pkey_export( $rsa, $privatekey, null, $config );
			$publickey = openssl_pkey_get_details( $rsa );
			$publickey = $publickey['key'];

			$privatekey = call_user_func_array( array( $this, '_convertPrivateKey' ), array_values( $this->_parseKey( $privatekey, self::PRIVATE_FORMAT_PKCS1 ) ) );
			$publickey  = call_user_func_array( array( $this, '_convertPublicKey' ), array_values( $this->_parseKey( $publickey, self::PUBLIC_FORMAT_PKCS1 ) ) );

			// clear the buffer of error strings stemming from a minimalistic openssl.cnf .
			while ( openssl_error_string() !== false ) { // @codingStandardsIgnoreLine.
			}

			return array(
				'privatekey' => $privatekey,
				'publickey'  => $publickey,
				'partialkey' => false,
			);
		}

		static $e;
		if ( ! isset( $e ) ) {
			$e = new BigInteger( CRYPT_RSA_EXPONENT );
		}

		extract( $this->_generateMinMax( $bits ) ); // @codingStandardsIgnoreLine.
		$absoluteMin = $min; // @codingStandardsIgnoreLine.
		$temp        = $bits >> 1; // divide by two to see how many bits P and Q would be .
		if ( $temp > CRYPT_RSA_SMALLEST_PRIME ) {
			$num_primes = floor( $bits / CRYPT_RSA_SMALLEST_PRIME );
			$temp       = CRYPT_RSA_SMALLEST_PRIME;
		} else {
			$num_primes = 2;
		}
		extract( $this->_generateMinMax( $temp + $bits % $temp ) ); // @codingStandardsIgnoreLine.
		$finalMax = $max; // @codingStandardsIgnoreLine.
		extract( $this->_generateMinMax( $temp ) ); // @codingStandardsIgnoreLine.

		$generator = new BigInteger();

		$n = $this->one->copy();
		if ( ! empty( $partial ) ) {
			extract( unserialize( $partial ) ); // @codingStandardsIgnoreLine.
		} else {
			$exponents = $coefficients = $primes = array(); // @codingStandardsIgnoreLine.
			$lcm       = array(
				'top'    => $this->one->copy(),
				'bottom' => false,
			);
		}

		$start = time();
		$i0    = count( $primes ) + 1;

		do {
			for ( $i = $i0; $i <= $num_primes; $i++ ) {
				if ( false !== $timeout ) {
					$timeout -= time() - $start;
					$start    = time();
					if ( $timeout <= 0 ) {
						return array(
							'privatekey' => '',
							'publickey'  => '',
							'partialkey' => serialize( // @codingStandardsIgnoreLine.
								array(
									'primes'       => $primes,
									'coefficients' => $coefficients,
									'lcm'          => $lcm,
									'exponents'    => $exponents,
								)
							),
						);
					}
				}

				if ( $i == $num_primes ) { // WPCS:Loose comparison ok.
					list($min, $temp) = $absoluteMin->divide( $n ); // @codingStandardsIgnoreLine.
					if ( ! $temp->equals( $this->zero ) ) {
						$min = $min->add( $this->one ); // ie. ceil() .
					}
					$primes[ $i ] = $generator->randomPrime( $min, $finalMax, $timeout ); // @codingStandardsIgnoreLine.
				} else {
					$primes[ $i ] = $generator->randomPrime( $min, $max, $timeout );
				}

				if ( false === $primes[ $i ] ) { // if we've reached the timeout .
					if ( count( $primes ) > 1 ) {
						$partialkey = '';
					} else {
						array_pop( $primes );
						$partialkey = serialize( // @codingStandardsIgnoreLine.
							array(
								'primes'       => $primes,
								'coefficients' => $coefficients,
								'lcm'          => $lcm,
								'exponents'    => $exponents,
							)
						);
					}

					return array(
						'privatekey' => '',
						'publickey'  => '',
						'partialkey' => $partialkey,
					);
				}

				// the first coefficient is calculated differently from the rest
				// ie. instead of being $primes[1]->modInverse($primes[2]), it's $primes[2]->modInverse($primes[1]) .
				if ( $i > 2 ) {
					$coefficients[ $i ] = $n->modInverse( $primes[ $i ] );
				}

				$n = $n->multiply( $primes[ $i ] );

				$temp = $primes[ $i ]->subtract( $this->one );

				// textbook RSA implementations use Euler's totient function instead of the least common multiple.
				// see http://en.wikipedia.org/wiki/Euler%27s_totient_function .
				$lcm['top']    = $lcm['top']->multiply( $temp );
				$lcm['bottom'] = false === $lcm['bottom'] ? $temp : $lcm['bottom']->gcd( $temp );

				$exponents[ $i ] = $e->modInverse( $temp );
			}

			list($temp) = $lcm['top']->divide( $lcm['bottom'] );
			$gcd        = $temp->gcd( $e );
			$i0         = 1;
		} while ( ! $gcd->equals( $this->one ) );

		$d = $e->modInverse( $temp );

		$coefficients[2] = $primes[2]->modInverse( $primes[1] );

		return array(
			'privatekey' => $this->_convertPrivateKey( $n, $e, $d, $primes, $exponents, $coefficients ),
			'publickey'  => $this->_convertPublicKey( $n, $e ),
			'partialkey' => false,
		);
	}

	/**
	 * Convert a private key to the appropriate format.
	 *
	 * @access private
	 * @see self::setPrivateKeyFormat()
	 * @param string $n .
	 * @param string $e .
	 * @param string $d .
	 * @param string $primes .
	 * @param string $exponents .
	 * @param string $coefficients .
	 * @return string
	 */
	private function _convertPrivateKey( $n, $e, $d, $primes, $exponents, $coefficients ) { // @codingStandardsIgnoreLine.
		$signed     = $this->privateKeyFormat != self::PRIVATE_FORMAT_XML; // @codingStandardsIgnoreLine.
		$num_primes = count( $primes );
		$raw        = array(
			'version'         => 2 == $num_primes ? chr( 0 ) : chr( 1 ), // WPCS:Loose comparison ok.
			'modulus'         => $n->toBytes( $signed ),
			'publicExponent'  => $e->toBytes( $signed ),
			'privateExponent' => $d->toBytes( $signed ),
			'prime1'          => $primes[1]->toBytes( $signed ),
			'prime2'          => $primes[2]->toBytes( $signed ),
			'exponent1'       => $exponents[1]->toBytes( $signed ),
			'exponent2'       => $exponents[2]->toBytes( $signed ),
			'coefficient'     => $coefficients[2]->toBytes( $signed ),
		);

		// if the format in question does not support multi-prime rsa and multi-prime rsa was used,
		// call _convertPublicKey() instead.
		switch ( $this->privateKeyFormat ) { // @codingStandardsIgnoreLine.
			case self::PRIVATE_FORMAT_XML:
				if ( 2 != $num_primes ) { // WPCS:Loose comparison ok.
					return false;
				}
				return "<RSAKeyValue>\r\n" .
						'  <Modulus>' . base64_encode( $raw['modulus'] ) . "</Modulus>\r\n" .
						'  <Exponent>' . base64_encode( $raw['publicExponent'] ) . "</Exponent>\r\n" .
						'  <P>' . base64_encode( $raw['prime1'] ) . "</P>\r\n" .
						'  <Q>' . base64_encode( $raw['prime2'] ) . "</Q>\r\n" .
						'  <DP>' . base64_encode( $raw['exponent1'] ) . "</DP>\r\n" .
						'  <DQ>' . base64_encode( $raw['exponent2'] ) . "</DQ>\r\n" .
						'  <InverseQ>' . base64_encode( $raw['coefficient'] ) . "</InverseQ>\r\n" .
						'  <D>' . base64_encode( $raw['privateExponent'] ) . "</D>\r\n" .
						'</RSAKeyValue>';
				break; // @codingStandardsIgnoreLine.
			case self::PRIVATE_FORMAT_PUTTY:
				if ( 2 != $num_primes ) { // WPCS:Loose comparison ok.
					return false;
				}
				$key        = "PuTTY-User-Key-File-2: ssh-rsa\r\nEncryption: ";
				$encryption = ( ! empty( $this->password ) || is_string( $this->password ) ) ? 'aes256-cbc' : 'none';
				$key       .= $encryption;
				$key       .= "\r\nComment: " . $this->comment . "\r\n";
				$public     = pack(
					'Na*Na*Na*',
					strlen( 'ssh-rsa' ),
					'ssh-rsa',
					strlen( $raw['publicExponent'] ),
					$raw['publicExponent'],
					strlen( $raw['modulus'] ),
					$raw['modulus']
				);
				$source     = pack(
					'Na*Na*Na*Na*',
					strlen( 'ssh-rsa' ),
					'ssh-rsa',
					strlen( $encryption ),
					$encryption,
					strlen( $this->comment ),
					$this->comment,
					strlen( $public ),
					$public
				);
				$public     = base64_encode( $public );
				$key       .= 'Public-Lines: ' . ( ( strlen( $public ) + 63 ) >> 6 ) . "\r\n";
				$key       .= chunk_split( $public, 64 );
				$private    = pack(
					'Na*Na*Na*Na*',
					strlen( $raw['privateExponent'] ),
					$raw['privateExponent'],
					strlen( $raw['prime1'] ),
					$raw['prime1'],
					strlen( $raw['prime2'] ),
					$raw['prime2'],
					strlen( $raw['coefficient'] ),
					$raw['coefficient']
				);
				if ( empty( $this->password ) && ! is_string( $this->password ) ) {
					$source .= pack( 'Na*', strlen( $private ), $private );
					$hashkey = 'putty-private-key-file-mac-key';
				} else {
					$private .= Random::string( 16 - ( strlen( $private ) & 15 ) );
					$source  .= pack( 'Na*', strlen( $private ), $private );
					$sequence = 0;
					$symkey   = '';
					while ( strlen( $symkey ) < 32 ) { // @codingStandardsIgnoreLine.
						$temp    = pack( 'Na*', $sequence++, $this->password );
						$symkey .= pack( 'H*', sha1( $temp ) );
					}
					$symkey = substr( $symkey, 0, 32 );
					$crypto = new AES();

					$crypto->setKey( $symkey );
					$crypto->disablePadding();
					$private = $crypto->encrypt( $private );
					$hashkey = 'putty-private-key-file-mac-key' . $this->password;
				}

				$private = base64_encode( $private );
				$key    .= 'Private-Lines: ' . ( ( strlen( $private ) + 63 ) >> 6 ) . "\r\n";
				$key    .= chunk_split( $private, 64 );
				$hash    = new Hash( 'sha1' );
				$hash->setKey( pack( 'H*', sha1( $hashkey ) ) );
				$key .= 'Private-MAC: ' . bin2hex( $hash->hash( $source ) ) . "\r\n";

				return $key;
			default: // eg. self::PRIVATE_FORMAT_PKCS1 .
				$components = array();
				foreach ( $raw as $name => $value ) {
					$components[ $name ] = pack( 'Ca*a*', self::ASN1_INTEGER, $this->_encodeLength( strlen( $value ) ), $value );
				}

				$RSAPrivateKey = implode( '', $components ); // @codingStandardsIgnoreLine.

				if ( $num_primes > 2 ) {
					$OtherPrimeInfos = ''; // @codingStandardsIgnoreLine.
					for ( $i = 3; $i <= $num_primes; $i++ ) {

						$OtherPrimeInfo   = pack( 'Ca*a*', self::ASN1_INTEGER, $this->_encodeLength( strlen( $primes[ $i ]->toBytes( true ) ) ), $primes[ $i ]->toBytes( true ) ); // @codingStandardsIgnoreLine.
						$OtherPrimeInfo  .= pack( 'Ca*a*', self::ASN1_INTEGER, $this->_encodeLength( strlen( $exponents[ $i ]->toBytes( true ) ) ), $exponents[ $i ]->toBytes( true ) ); // @codingStandardsIgnoreLine.
						$OtherPrimeInfo  .= pack( 'Ca*a*', self::ASN1_INTEGER, $this->_encodeLength( strlen( $coefficients[ $i ]->toBytes( true ) ) ), $coefficients[ $i ]->toBytes( true ) ); // @codingStandardsIgnoreLine.
						$OtherPrimeInfos .= pack( 'Ca*a*', self::ASN1_SEQUENCE, $this->_encodeLength( strlen( $OtherPrimeInfo ) ), $OtherPrimeInfo ); // @codingStandardsIgnoreLine.
					}
					$RSAPrivateKey .= pack( 'Ca*a*', self::ASN1_SEQUENCE, $this->_encodeLength( strlen( $OtherPrimeInfos ) ), $OtherPrimeInfos ); // @codingStandardsIgnoreLine.
				}

				$RSAPrivateKey = pack( 'Ca*a*', self::ASN1_SEQUENCE, $this->_encodeLength( strlen( $RSAPrivateKey ) ), $RSAPrivateKey ); // @codingStandardsIgnoreLine.

				if ( $this->privateKeyFormat == self::PRIVATE_FORMAT_PKCS8 ) { // @codingStandardsIgnoreLine.
					$rsaOID        = pack( 'H*', '300d06092a864886f70d0101010500' ); // hex version of MA0GCSqGSIb3DQEBAQUA . // @codingStandardsIgnoreLine.
					$RSAPrivateKey = pack( // @codingStandardsIgnoreLine.
						'Ca*a*Ca*a*',
						self::ASN1_INTEGER,
						"\01\00",
						$rsaOID, // @codingStandardsIgnoreLine.
						4,
						$this->_encodeLength( strlen( $RSAPrivateKey ) ), // @codingStandardsIgnoreLine.
						$RSAPrivateKey // @codingStandardsIgnoreLine.
					);
					$RSAPrivateKey = pack( 'Ca*a*', self::ASN1_SEQUENCE, $this->_encodeLength( strlen( $RSAPrivateKey ) ), $RSAPrivateKey ); // @codingStandardsIgnoreLine.
					if ( ! empty( $this->password ) || is_string( $this->password ) ) {
						$salt           = Random::string( 8 );
						$iterationCount = 2048; // @codingStandardsIgnoreLine.

						$crypto = new DES();
						$crypto->setPassword( $this->password, 'pbkdf1', 'md5', $salt, $iterationCount ); // @codingStandardsIgnoreLine.
						$RSAPrivateKey = $crypto->encrypt( $RSAPrivateKey ); // @codingStandardsIgnoreLine.

						$parameters           = pack(
							'Ca*a*Ca*N',
							self::ASN1_OCTETSTRING,
							$this->_encodeLength( strlen( $salt ) ),
							$salt,
							self::ASN1_INTEGER,
							$this->_encodeLength( 4 ),
							$iterationCount // @codingStandardsIgnoreLine.
						);
						$pbeWithMD5AndDES_CBC = "\x2a\x86\x48\x86\xf7\x0d\x01\x05\x03"; // @codingStandardsIgnoreLine.

						$encryptionAlgorithm = pack( // @codingStandardsIgnoreLine.
							'Ca*a*Ca*a*',
							self::ASN1_OBJECT,
							$this->_encodeLength( strlen( $pbeWithMD5AndDES_CBC ) ), // @codingStandardsIgnoreLine.
							$pbeWithMD5AndDES_CBC, // @codingStandardsIgnoreLine.
							self::ASN1_SEQUENCE,
							$this->_encodeLength( strlen( $parameters ) ),
							$parameters
						);

						$RSAPrivateKey = pack( // @codingStandardsIgnoreLine.
							'Ca*a*Ca*a*',
							self::ASN1_SEQUENCE,
							$this->_encodeLength( strlen( $encryptionAlgorithm ) ), // @codingStandardsIgnoreLine.
							$encryptionAlgorithm, // @codingStandardsIgnoreLine.
							self::ASN1_OCTETSTRING,
							$this->_encodeLength( strlen( $RSAPrivateKey ) ), // @codingStandardsIgnoreLine.
							$RSAPrivateKey // @codingStandardsIgnoreLine.
						);

						$RSAPrivateKey = pack( 'Ca*a*', self::ASN1_SEQUENCE, $this->_encodeLength( strlen( $RSAPrivateKey ) ), $RSAPrivateKey ); // @codingStandardsIgnoreLine.
						 // @codingStandardsIgnoreStart.
						$RSAPrivateKey = "-----BEGIN ENCRYPTED PRIVATE KEY-----\r\n" .
										chunk_split( base64_encode( $RSAPrivateKey ), 64 ) .
										'-----END ENCRYPTED PRIVATE KEY-----';
					} else {
						$RSAPrivateKey = "-----BEGIN PRIVATE KEY-----\r\n" .
										chunk_split( base64_encode( $RSAPrivateKey ), 64 ) .
										'-----END PRIVATE KEY-----';
					}
					return $RSAPrivateKey; // @codingStandardsIgnoreLine.
				}

				if ( ! empty( $this->password ) || is_string( $this->password ) ) {
					$iv      = Random::string( 8 );
					$symkey  = pack( 'H*', md5( $this->password . $iv ) ); // symkey is short for symmetric key .
					$symkey .= substr( pack( 'H*', md5( $symkey . $this->password . $iv ) ), 0, 8 );
					$des     = new TripleDES();
					$des->setKey( $symkey );
					$des->setIV( $iv );
					$iv            = strtoupper( bin2hex( $iv ) );
					$RSAPrivateKey = "-----BEGIN RSA PRIVATE KEY-----\r\n" .
									"Proc-Type: 4,ENCRYPTED\r\n" .
									"DEK-Info: DES-EDE3-CBC,$iv\r\n" .
									"\r\n" .
									chunk_split( base64_encode( $des->encrypt( $RSAPrivateKey ) ), 64 ) .
									'-----END RSA PRIVATE KEY-----';
				} else {
					$RSAPrivateKey = "-----BEGIN RSA PRIVATE KEY-----\r\n" .
									chunk_split( base64_encode( $RSAPrivateKey ), 64 ) .
									'-----END RSA PRIVATE KEY-----';
				}
				return $RSAPrivateKey;
		} // @codingStandardsIgnoreEnd.
	}

	/**
	 * Convert a public key to the appropriate format
	 *
	 * @access private
	 * @see self::setPublicKeyFormat()
	 * @param string $n .
	 * @param string $e .
	 * @return string
	 */
	private function _convertPublicKey( $n, $e ) { // @codingStandardsIgnoreLine.
		$signed = $this->publicKeyFormat != self::PUBLIC_FORMAT_XML; // @codingStandardsIgnoreLine.

		$modulus        = $n->toBytes( $signed );
		$publicExponent = $e->toBytes( $signed ); // @codingStandardsIgnoreLine.

		switch ( $this->publicKeyFormat ) { // @codingStandardsIgnoreLine.
			case self::PUBLIC_FORMAT_RAW:
				return array(
					'e' => $e->copy(),
					'n' => $n->copy(),
				);
			case self::PUBLIC_FORMAT_XML:
				return "<RSAKeyValue>\r\n" .
						'  <Modulus>' . base64_encode( $modulus ) . "</Modulus>\r\n" .
						'  <Exponent>' . base64_encode( $publicExponent ) . "</Exponent>\r\n" . // @codingStandardsIgnoreLine.
						'</RSAKeyValue>';
				break; // @codingStandardsIgnoreLine.
			case self::PUBLIC_FORMAT_OPENSSH:
				$RSAPublicKey = pack( 'Na*Na*Na*', strlen( 'ssh-rsa' ), 'ssh-rsa', strlen( $publicExponent ), $publicExponent, strlen( $modulus ), $modulus ); // @codingStandardsIgnoreLine.
				$RSAPublicKey = 'ssh-rsa ' . base64_encode( $RSAPublicKey ) . ' ' . $this->comment; // @codingStandardsIgnoreLine.
				return $RSAPublicKey; // @codingStandardsIgnoreLine.
			default: // eg. self::PUBLIC_FORMAT_PKCS1_RAW or self::PUBLIC_FORMAT_PKCS1 .
				$components = array(
					'modulus'        => pack( 'Ca*a*', self::ASN1_INTEGER, $this->_encodeLength( strlen( $modulus ) ), $modulus ),
					'publicExponent' => pack( 'Ca*a*', self::ASN1_INTEGER, $this->_encodeLength( strlen( $publicExponent ) ), $publicExponent ), // @codingStandardsIgnoreLine.
				);

				$RSAPublicKey = pack( // @codingStandardsIgnoreLine.
					'Ca*a*a*',
					self::ASN1_SEQUENCE,
					$this->_encodeLength( strlen( $components['modulus'] ) + strlen( $components['publicExponent'] ) ),
					$components['modulus'],
					$components['publicExponent']
				);

				if ( $this->publicKeyFormat == self::PUBLIC_FORMAT_PKCS1_RAW ) { // @codingStandardsIgnoreLine.
					$RSAPublicKey = "-----BEGIN RSA PUBLIC KEY-----\r\n" . // @codingStandardsIgnoreLine.
									chunk_split( base64_encode( $RSAPublicKey ), 64 ) . // @codingStandardsIgnoreLine.
									'-----END RSA PUBLIC KEY-----';
				} else {
					$rsaOID       = pack( 'H*', '300d06092a864886f70d0101010500' ); // hex version of MA0GCSqGSIb3DQEBAQUA . // @codingStandardsIgnoreLine.
					$RSAPublicKey = chr( 0 ) . $RSAPublicKey; // @codingStandardsIgnoreLine.
					$RSAPublicKey = chr( 3 ) . $this->_encodeLength( strlen( $RSAPublicKey ) ) . $RSAPublicKey; // @codingStandardsIgnoreLine.

					$RSAPublicKey = pack( // @codingStandardsIgnoreLine.
						'Ca*a*',
						self::ASN1_SEQUENCE,
						$this->_encodeLength( strlen( $rsaOID . $RSAPublicKey ) ), // @codingStandardsIgnoreLine.
						$rsaOID . $RSAPublicKey // @codingStandardsIgnoreLine.
					);

					$RSAPublicKey = "-----BEGIN PUBLIC KEY-----\r\n" . // @codingStandardsIgnoreLine.
									chunk_split( base64_encode( $RSAPublicKey ), 64 ) . // @codingStandardsIgnoreLine.
									'-----END PUBLIC KEY-----';
				}

				return $RSAPublicKey; // @codingStandardsIgnoreLine.
		}
	}

	/**
	 * Break a public or private key down into its constituant components
	 *
	 * @access private
	 * @see self::_convertPublicKey()
	 * @see self::_convertPrivateKey()
	 * @param string $key .
	 * @param int    $type .
	 * @return array
	 */
	private function _parseKey( $key, $type ) { // @codingStandardsIgnoreLine.
		if ( self::PUBLIC_FORMAT_RAW != $type && ! is_string( $key ) ) { // WPCS:Loose comparison ok.
			return false;
		}

		switch ( $type ) {
			case self::PUBLIC_FORMAT_RAW:
				if ( ! is_array( $key ) ) {
					return false;
				}
				$components = array();
				switch ( true ) {
					case isset( $key['e'] ):
						$components['publicExponent'] = $key['e']->copy();
						break;
					case isset( $key['exponent'] ):
						$components['publicExponent'] = $key['exponent']->copy();
						break;
					case isset( $key['publicExponent'] ):
						$components['publicExponent'] = $key['publicExponent']->copy();
						break;
					case isset( $key[0] ):
						$components['publicExponent'] = $key[0]->copy();
				}
				switch ( true ) {
					case isset( $key['n'] ):
						$components['modulus'] = $key['n']->copy();
						break;
					case isset( $key['modulo'] ):
						$components['modulus'] = $key['modulo']->copy();
						break;
					case isset( $key['modulus'] ):
						$components['modulus'] = $key['modulus']->copy();
						break;
					case isset( $key[1] ):
						$components['modulus'] = $key[1]->copy();
				}
				return isset( $components['modulus'] ) && isset( $components['publicExponent'] ) ? $components : false;
			case self::PRIVATE_FORMAT_PKCS1:
			case self::PRIVATE_FORMAT_PKCS8:
			case self::PUBLIC_FORMAT_PKCS1:
				if ( preg_match( '#DEK-Info: (.+),(.+)#', $key, $matches ) ) {
					$iv      = pack( 'H*', trim( $matches[2] ) );
					$symkey  = pack( 'H*', md5( $this->password . substr( $iv, 0, 8 ) ) ); // symkey is short for symmetric key .
					$symkey .= pack( 'H*', md5( $symkey . $this->password . substr( $iv, 0, 8 ) ) );
					// remove the Proc-Type / DEK-Info sections as they're no longer needed .
					$key        = preg_replace( '#^(?:Proc-Type|DEK-Info): .*#m', '', $key );
					$ciphertext = $this->_extractBER( $key );
					if ( false === $ciphertext ) {
						$ciphertext = $key;
					}
					switch ( $matches[1] ) {
						case 'AES-256-CBC':
							$crypto = new AES();
							break;
						case 'AES-128-CBC':
							$symkey = substr( $symkey, 0, 16 );
							$crypto = new AES();
							break;
						case 'DES-EDE3-CFB':
							$crypto = new TripleDES( Base::MODE_CFB );
							break;
						case 'DES-EDE3-CBC':
							$symkey = substr( $symkey, 0, 24 );
							$crypto = new TripleDES();
							break;
						case 'DES-CBC':
							$crypto = new DES();
							break;
						default:
							return false;
					}
					$crypto->setKey( $symkey );
					$crypto->setIV( $iv );
					$decoded = $crypto->decrypt( $ciphertext );
				} else {
					$decoded = $this->_extractBER( $key );
				}

				if ( false !== $decoded ) {
					$key = $decoded;
				}

				$components = array();

				if ( ord( $this->_string_shift( $key ) ) != self::ASN1_SEQUENCE ) { // WPCS:Loose comparison ok.
					return false;
				}
				if ( $this->_decodeLength( $key ) != strlen( $key ) ) { // WPCS:Loose comparison ok.
					return false;
				}

				$tag = ord( $this->_string_shift( $key ) );

				if ( self::ASN1_INTEGER == $tag && substr( $key, 0, 3 ) == "\x01\x00\x30" ) { // WPCS:Loose comparison ok.
					$this->_string_shift( $key, 3 );
					$tag = self::ASN1_SEQUENCE;
				}

				if ( self::ASN1_SEQUENCE == $tag ) { // WPCS:Loose comparison ok.
					$temp = $this->_string_shift( $key, $this->_decodeLength( $key ) );
					if ( ord( $this->_string_shift( $temp ) ) != self::ASN1_OBJECT ) { // WPCS:Loose comparison ok.
						return false;
					}
					$length = $this->_decodeLength( $temp );
					switch ( $this->_string_shift( $temp, $length ) ) {
						case "\x2a\x86\x48\x86\xf7\x0d\x01\x01\x01": // rsaEncryption .
							break;
						case "\x2a\x86\x48\x86\xf7\x0d\x01\x05\x03": // pbeWithMD5AndDES-CBC .
							if ( ord( $this->_string_shift( $temp ) ) != self::ASN1_SEQUENCE ) { // WPCS:Loose comparison ok.
								return false;
							}
							if ( $this->_decodeLength( $temp ) != strlen( $temp ) ) { // WPCS:Loose comparison ok.
								return false;
							}
							$this->_string_shift( $temp ); // assume it's an octet string .
							$salt = $this->_string_shift( $temp, $this->_decodeLength( $temp ) );
							if ( ord( $this->_string_shift( $temp ) ) != self::ASN1_INTEGER ) { // WPCS:Loose comparison ok.
								return false;
							}
							$this->_decodeLength( $temp );
							list(, $iterationCount) = unpack( 'N', str_pad( $temp, 4, chr( 0 ), STR_PAD_LEFT ) ); // @codingStandardsIgnoreLine.
							$this->_string_shift( $key ); // assume it's an octet string .
							$length = $this->_decodeLength( $key );
							if ( strlen( $key ) != $length ) { // WPCS:Loose comparison ok.
								return false;
							}

							$crypto = new DES();
							$crypto->setPassword( $this->password, 'pbkdf1', 'md5', $salt, $iterationCount ); // @codingStandardsIgnoreLine.
							$key = $crypto->decrypt( $key );
							if ( false === $key ) {
								return false;
							}
							return $this->_parseKey( $key, self::PRIVATE_FORMAT_PKCS1 );
						default:
							return false;
					}

					$tag = ord( $this->_string_shift( $key ) ); // skip over the BIT STRING / OCTET STRING tag .
					$this->_decodeLength( $key ); // skip over the BIT STRING / OCTET STRING length
					// "The initial octet shall encode, as an unsigned binary integer wtih bit 1 as the least significant bit, the number of
					// unused bits in the final subsequent octet. The number shall be in the range zero to seven."
					// -- http://www.itu.int/ITU-T/studygroups/com17/languages/X.690-0207.pdf (section 8.6.2.2) .
					if ( self::ASN1_BITSTRING == $tag ) { // WPCS:Loose comparison ok.
						$this->_string_shift( $key );
					}
					if ( ord( $this->_string_shift( $key ) ) != self::ASN1_SEQUENCE ) { // WPCS:Loose comparison ok.
						return false;
					}
					if ( $this->_decodeLength( $key ) != strlen( $key ) ) { // WPCS:Loose comparison ok.
						return false;
					}
					$tag = ord( $this->_string_shift( $key ) );
				}
				if ( self::ASN1_INTEGER != $tag ) { // WPCS:Loose comparison ok.
					return false;
				}

				$length = $this->_decodeLength( $key );
				$temp   = $this->_string_shift( $key, $length );
				if ( strlen( $temp ) != 1 || ord( $temp ) > 2 ) { // WPCS:Loose comparison ok.
					$components['modulus'] = new BigInteger( $temp, 256 );
					$this->_string_shift( $key ); // skip over self::ASN1_INTEGER .
					$length = $this->_decodeLength( $key );
					$components[ self::PUBLIC_FORMAT_PKCS1 == $type ? 'publicExponent' : 'privateExponent' ] = new BigInteger( $this->_string_shift( $key, $length ), 256 ); // WPCS:Loose comparison ok.

					return $components;
				}
				if ( ord( $this->_string_shift( $key ) ) != self::ASN1_INTEGER ) { // WPCS:Loose comparison ok.
					return false;
				}
				$length                = $this->_decodeLength( $key );
				$components['modulus'] = new BigInteger( $this->_string_shift( $key, $length ), 256 );
				$this->_string_shift( $key );
				$length                       = $this->_decodeLength( $key );
				$components['publicExponent'] = new BigInteger( $this->_string_shift( $key, $length ), 256 );
				$this->_string_shift( $key );
				$length                        = $this->_decodeLength( $key );
				$components['privateExponent'] = new BigInteger( $this->_string_shift( $key, $length ), 256 );
				$this->_string_shift( $key );
				$length               = $this->_decodeLength( $key );
				$components['primes'] = array( 1 => new BigInteger( $this->_string_shift( $key, $length ), 256 ) );
				$this->_string_shift( $key );
				$length                 = $this->_decodeLength( $key );
				$components['primes'][] = new BigInteger( $this->_string_shift( $key, $length ), 256 );
				$this->_string_shift( $key );
				$length                  = $this->_decodeLength( $key );
				$components['exponents'] = array( 1 => new BigInteger( $this->_string_shift( $key, $length ), 256 ) );
				$this->_string_shift( $key );
				$length                    = $this->_decodeLength( $key );
				$components['exponents'][] = new BigInteger( $this->_string_shift( $key, $length ), 256 );
				$this->_string_shift( $key );
				$length                     = $this->_decodeLength( $key );
				$components['coefficients'] = array( 2 => new BigInteger( $this->_string_shift( $key, $length ), 256 ) );

				if ( ! empty( $key ) ) {
					if ( ord( $this->_string_shift( $key ) ) != self::ASN1_SEQUENCE ) { // WPCS:Loose comparison ok.
						return false;
					}
					$this->_decodeLength( $key );
					while ( ! empty( $key ) ) {
						if ( ord( $this->_string_shift( $key ) ) != self::ASN1_SEQUENCE ) { // WPCS:Loose comparison ok.
							return false;
						}
						$this->_decodeLength( $key );
						$key                    = substr( $key, 1 );
						$length                 = $this->_decodeLength( $key );
						$components['primes'][] = new BigInteger( $this->_string_shift( $key, $length ), 256 );
						$this->_string_shift( $key );
						$length                    = $this->_decodeLength( $key );
						$components['exponents'][] = new BigInteger( $this->_string_shift( $key, $length ), 256 );
						$this->_string_shift( $key );
						$length                       = $this->_decodeLength( $key );
						$components['coefficients'][] = new BigInteger( $this->_string_shift( $key, $length ), 256 );
					}
				}

				return $components;
			case self::PUBLIC_FORMAT_OPENSSH:
				$parts = explode( ' ', $key, 3 );

				$key = isset( $parts[1] ) ? base64_decode( $parts[1] ) : false;
				if ( false === $key ) {
					return false;
				}

				$comment = isset( $parts[2] ) ? $parts[2] : false;

				$cleanup = substr( $key, 0, 11 ) == "\0\0\0\7ssh-rsa"; // WPCS:Loose comparison ok.

				if ( strlen( $key ) <= 4 ) {
					return false;
				}
				extract( unpack( 'Nlength', $this->_string_shift( $key, 4 ) ) ); // @codingStandardsIgnoreLine.
				$publicExponent = new BigInteger( $this->_string_shift( $key, $length ), -256 ); // @codingStandardsIgnoreLine.
				if ( strlen( $key ) <= 4 ) {
					return false;
				}
				extract( unpack( 'Nlength', $this->_string_shift( $key, 4 ) ) ); // @codingStandardsIgnoreLine.
				$modulus = new BigInteger( $this->_string_shift( $key, $length ), -256 );

				if ( $cleanup && strlen( $key ) ) {
					if ( strlen( $key ) <= 4 ) {
						return false;
					}
					extract( unpack( 'Nlength', $this->_string_shift( $key, 4 ) ) ); // @codingStandardsIgnoreLine.
					$realModulus = new BigInteger( $this->_string_shift( $key, $length ), -256 ); // @codingStandardsIgnoreLine.
					return strlen( $key ) ? false : array(
						'modulus'        => $realModulus, // @codingStandardsIgnoreLine.
						'publicExponent' => $modulus,
						'comment'        => $comment,
					);
				} else {
					return strlen( $key ) ? false : array(
						'modulus'        => $modulus,
						'publicExponent' => $publicExponent, // @codingStandardsIgnoreLine.
						'comment'        => $comment,
					);
				}

			case self::PRIVATE_FORMAT_XML:
			case self::PUBLIC_FORMAT_XML:
				$this->components = array();

				$xml = xml_parser_create( 'UTF-8' );
				xml_set_object( $xml, $this );
				xml_set_element_handler( $xml, '_start_element_handler', '_stop_element_handler' );
				xml_set_character_data_handler( $xml, '_data_handler' );
				if ( ! xml_parse( $xml, '<xml>' . $key . '</xml>' ) ) {
					return false;
				}

				return isset( $this->components['modulus'] ) && isset( $this->components['publicExponent'] ) ? $this->components : false;
			case self::PRIVATE_FORMAT_PUTTY:
				$components = array();
				$key        = preg_split( '#\r\n|\r|\n#', $key );
				$type       = trim( preg_replace( '#PuTTY-User-Key-File-2: (.+)#', '$1', $key[0] ) );
				if ( 'ssh-rsa' != $type ) { // WPCS:Loose comparison ok.
					return false;
				}
				$encryption = trim( preg_replace( '#Encryption: (.+)#', '$1', $key[1] ) );
				$comment    = trim( preg_replace( '#Comment: (.+)#', '$1', $key[2] ) );

				$publicLength = trim( preg_replace( '#Public-Lines: (\d+)#', '$1', $key[3] ) ); // @codingStandardsIgnoreLine.
				$public       = base64_decode( implode( '', array_map( 'trim', array_slice( $key, 4, $publicLength ) ) ) ); // @codingStandardsIgnoreLine.
				$public       = substr( $public, 11 );
				extract( unpack( 'Nlength', $this->_string_shift( $public, 4 ) ) ); // @codingStandardsIgnoreLine.
				$components['publicExponent'] = new BigInteger( $this->_string_shift( $public, $length ), -256 );
				extract( unpack( 'Nlength', $this->_string_shift( $public, 4 ) ) ); // @codingStandardsIgnoreLine.
				$components['modulus'] = new BigInteger( $this->_string_shift( $public, $length ), -256 );

				$privateLength = trim( preg_replace( '#Private-Lines: (\d+)#', '$1', $key[ $publicLength + 4 ] ) ); // @codingStandardsIgnoreLine.
				$private       = base64_decode( implode( '', array_map( 'trim', array_slice( $key, $publicLength + 5, $privateLength ) ) ) ); // @codingStandardsIgnoreLine.

				switch ( $encryption ) {
					case 'aes256-cbc':
						$symkey   = '';
						$sequence = 0;
						while ( strlen( $symkey ) < 32 ) { // @codingStandardsIgnoreLine.
							$temp    = pack( 'Na*', $sequence++, $this->password );
							$symkey .= pack( 'H*', sha1( $temp ) );
						}
						$symkey = substr( $symkey, 0, 32 );
						$crypto = new AES();
				}

				if ( 'none' != $encryption ) { // WPCS:Loose comparison ok.
					$crypto->setKey( $symkey );
					$crypto->disablePadding();
					$private = $crypto->decrypt( $private );
					if ( false === $private ) {
						return false;
					}
				}

				extract( unpack( 'Nlength', $this->_string_shift( $private, 4 ) ) ); // @codingStandardsIgnoreLine.
				if ( strlen( $private ) < $length ) {
					return false;
				}
				$components['privateExponent'] = new BigInteger( $this->_string_shift( $private, $length ), -256 ); // @codingStandardsIgnoreLine.
				extract( unpack( 'Nlength', $this->_string_shift( $private, 4 ) ) ); // @codingStandardsIgnoreLine.
				if ( strlen( $private ) < $length ) {
					return false;
				}
				$components['primes'] = array( 1 => new BigInteger( $this->_string_shift( $private, $length ), -256 ) );
				extract( unpack( 'Nlength', $this->_string_shift( $private, 4 ) ) ); // @codingStandardsIgnoreLine.
				if ( strlen( $private ) < $length ) {
					return false;
				}
				$components['primes'][] = new BigInteger( $this->_string_shift( $private, $length ), -256 );

				$temp                      = $components['primes'][1]->subtract( $this->one );
				$components['exponents']   = array( 1 => $components['publicExponent']->modInverse( $temp ) );
				$temp                      = $components['primes'][2]->subtract( $this->one );
				$components['exponents'][] = $components['publicExponent']->modInverse( $temp );

				extract( unpack( 'Nlength', $this->_string_shift( $private, 4 ) ) ); // @codingStandardsIgnoreLine.
				if ( strlen( $private ) < $length ) {
					return false;
				}
				$components['coefficients'] = array( 2 => new BigInteger( $this->_string_shift( $private, $length ), -256 ) );

				return $components;
		}
	}

	/**
	 * Returns the key size
	 *
	 * More specifically, this returns the size of the modulo in bits.
	 *
	 * @access public
	 * @return int
	 */
	public function getSize() { // @codingStandardsIgnoreLine.
		return ! isset( $this->modulus ) ? 0 : strlen( $this->modulus->toBits() );
	}

	/**
	 * Start Element Handler
	 *
	 * Called by xml_set_element_handler()
	 *
	 * @access private
	 * @param resource $parser .
	 * @param string   $name .
	 * @param array    $attribs .
	 */
	private function _start_element_handler( $parser, $name, $attribs ) { // @codingStandardsIgnoreLine.
		switch ( $name ) {
			case 'MODULUS':
				$this->current = &$this->components['modulus'];
				break;
			case 'EXPONENT':
				$this->current = &$this->components['publicExponent'];
				break;
			case 'P':
				$this->current = &$this->components['primes'][1];
				break;
			case 'Q':
				$this->current = &$this->components['primes'][2];
				break;
			case 'DP':
				$this->current = &$this->components['exponents'][1];
				break;
			case 'DQ':
				$this->current = &$this->components['exponents'][2];
				break;
			case 'INVERSEQ':
				$this->current = &$this->components['coefficients'][2];
				break;
			case 'D':
				$this->current = &$this->components['privateExponent'];
		}
		$this->current = '';
	}

	/**
	 * Stop Element Handler
	 *
	 * Called by xml_set_element_handler()
	 *
	 * @access private
	 * @param resource $parser .
	 * @param string   $name .
	 */
	private function _stop_element_handler( $parser, $name ) { // @codingStandardsIgnoreLine.
		if ( isset( $this->current ) ) {
			$this->current = new BigInteger( base64_decode( $this->current ), 256 );
			unset( $this->current );
		}
	}

	/**
	 * Data Handler
	 *
	 * Called by xml_set_character_data_handler()
	 *
	 * @access private
	 * @param resource $parser .
	 * @param string   $data .
	 */
	private function _data_handler( $parser, $data ) { // @codingStandardsIgnoreLine.
		if ( ! isset( $this->current ) || is_object( $this->current ) ) {
			return;
		}
		$this->current .= trim( $data );
	}

	/**
	 * Loads a public or private key
	 *
	 * Returns true on success and false on failure (ie. an incorrect password was provided or the key was malformed)
	 *
	 * @access public
	 * @param string $key .
	 * @param bool   $type optional .
	 */
	public function loadKey( $key, $type = false ) { // @codingStandardsIgnoreLine.
		if ( $key instanceof RSA ) {
			$this->privateKeyFormat = $key->privateKeyFormat; // @codingStandardsIgnoreLine.
			$this->publicKeyFormat  = $key->publicKeyFormat; // @codingStandardsIgnoreLine.
			$this->k                = $key->k;
			$this->hLen             = $key->hLen; // @codingStandardsIgnoreLine.
			$this->sLen             = $key->sLen; // @codingStandardsIgnoreLine.
			$this->mgfHLen          = $key->mgfHLen; // @codingStandardsIgnoreLine.
			$this->encryptionMode   = $key->encryptionMode; // @codingStandardsIgnoreLine.
			$this->signatureMode    = $key->signatureMode; // @codingStandardsIgnoreLine.
			$this->password         = $key->password;
			$this->configFile       = $key->configFile; // @codingStandardsIgnoreLine.
			$this->comment          = $key->comment;

			if ( is_object( $key->hash ) ) {
				$this->hash = new Hash( $key->hash->getHash() );
			}
			if ( is_object( $key->mgfHash ) ) { // @codingStandardsIgnoreLine.
				$this->mgfHash = new Hash( $key->mgfHash->getHash() ); // @codingStandardsIgnoreLine.
			}

			if ( is_object( $key->modulus ) ) {
				$this->modulus = $key->modulus->copy();
			}
			if ( is_object( $key->exponent ) ) {
				$this->exponent = $key->exponent->copy();
			}
			if ( is_object( $key->publicExponent ) ) { // @codingStandardsIgnoreLine.
				$this->publicExponent = $key->publicExponent->copy(); // @codingStandardsIgnoreLine.
			}

			$this->primes       = array();
			$this->exponents    = array();
			$this->coefficients = array();

			foreach ( $this->primes as $prime ) {
				$this->primes[] = $prime->copy();
			}
			foreach ( $this->exponents as $exponent ) {
				$this->exponents[] = $exponent->copy();
			}
			foreach ( $this->coefficients as $coefficient ) {
				$this->coefficients[] = $coefficient->copy();
			}

			return true;
		}

		if ( false === $type ) {
			$types = array(
				self::PUBLIC_FORMAT_RAW,
				self::PRIVATE_FORMAT_PKCS1,
				self::PRIVATE_FORMAT_XML,
				self::PRIVATE_FORMAT_PUTTY,
				self::PUBLIC_FORMAT_OPENSSH,
			);
			foreach ( $types as $type ) {
				$components = $this->_parseKey( $key, $type );
				if ( false !== $components ) {
					break;
				}
			}
		} else {
			$components = $this->_parseKey( $key, $type );
		}

		if ( false === $components ) {
			$this->comment        = null;
			$this->modulus        = null;
			$this->k              = null;
			$this->exponent       = null;
			$this->primes         = null;
			$this->exponents      = null;
			$this->coefficients   = null;
			$this->publicExponent = null; // @codingStandardsIgnoreLine.

			return false;
		}

		if ( isset( $components['comment'] ) && false !== $components['comment'] ) {
			$this->comment = $components['comment'];
		}
		$this->modulus  = $components['modulus'];
		$this->k        = strlen( $this->modulus->toBytes() );
		$this->exponent = isset( $components['privateExponent'] ) ? $components['privateExponent'] : $components['publicExponent'];
		if ( isset( $components['primes'] ) ) {
			$this->primes         = $components['primes'];
			$this->exponents      = $components['exponents'];
			$this->coefficients   = $components['coefficients'];
			$this->publicExponent = $components['publicExponent']; // @codingStandardsIgnoreLine.
		} else {
			$this->primes         = array();
			$this->exponents      = array();
			$this->coefficients   = array();
			$this->publicExponent = false; // @codingStandardsIgnoreLine.
		}

		switch ( $type ) {
			case self::PUBLIC_FORMAT_OPENSSH:
			case self::PUBLIC_FORMAT_RAW:
				$this->setPublicKey();
				break;
			case self::PRIVATE_FORMAT_PKCS1:
				switch ( true ) {
					case strpos( $key, '-BEGIN PUBLIC KEY-' ) !== false:
					case strpos( $key, '-BEGIN RSA PUBLIC KEY-' ) !== false:
						$this->setPublicKey();
				}
		}

		return true;
	}

	/**
	 * Sets the password
	 *
	 * Private keys can be encrypted with a password.  To unset the password, pass in the empty string or false.
	 * Or rather, pass in $password such that empty($password) && !is_string($password) is true.
	 *
	 * @see self::createKey()
	 * @see self::loadKey()
	 * @access public
	 * @param bool $password .
	 */
	public function setPassword( $password = false ) { // @codingStandardsIgnoreLine.
		$this->password = $password;
	}

	/**
	 * Defines the public key
	 *
	 * Some private key formats define the public exponent and some don't.  Those that don't define it are problematic when
	 * used in certain contexts.  For example, in SSH-2, RSA authentication works by sending the public key along with a
	 * message signed by the private key to the server.  The SSH-2 server looks the public key up in an index of public keys
	 * and if it's present then proceeds to verify the signature.  Problem is, if your private key doesn't include the public
	 * exponent this won't work unless you manually add the public exponent. phpseclib tries to guess if the key being used
	 * is the public key but in the event that it guesses incorrectly you might still want to explicitly set the key as being
	 * public.
	 *
	 * Do note that when a new key is loaded the index will be cleared.
	 *
	 * Returns true on success, false on failure
	 *
	 * @see self::getPublicKey()
	 * @access public
	 * @param bool $key optional .
	 * @param bool $type optional .
	 * @return bool
	 */
	public function setPublicKey( $key = false, $type = false ) { // @codingStandardsIgnoreLine.
		// if a public key has already been loaded return false .
		if ( ! empty( $this->publicExponent ) ) { // @codingStandardsIgnoreLine.
			return false;
		}

		if ( false === $key && ! empty( $this->modulus ) ) {
			$this->publicExponent = $this->exponent; // @codingStandardsIgnoreLine.
			return true;
		}

		if ( false === $type ) {
			$types = array(
				self::PUBLIC_FORMAT_RAW,
				self::PUBLIC_FORMAT_PKCS1,
				self::PUBLIC_FORMAT_XML,
				self::PUBLIC_FORMAT_OPENSSH,
			);
			foreach ( $types as $type ) {
				$components = $this->_parseKey( $key, $type );
				if ( false !== $components ) {
					break;
				}
			}
		} else {
			$components = $this->_parseKey( $key, $type );
		}

		if ( false === $components ) {
			return false;
		}

		if ( empty( $this->modulus ) || ! $this->modulus->equals( $components['modulus'] ) ) {
			$this->modulus  = $components['modulus'];
			$this->exponent = $this->publicExponent = $components['publicExponent']; // @codingStandardsIgnoreLine.
			return true;
		}

		$this->publicExponent = $components['publicExponent']; // @codingStandardsIgnoreLine.

		return true;
	}

	/**
	 * Defines the private key
	 *
	 * If phpseclib guessed a private key was a public key and loaded it as such it might be desirable to force
	 * phpseclib to treat the key as a private key. This function will do that.
	 *
	 * Do note that when a new key is loaded the index will be cleared.
	 *
	 * Returns true on success, false on failure
	 *
	 * @see self::getPublicKey()
	 * @access public
	 * @param bool $key optional .
	 * @param bool $type optional .
	 * @return bool
	 */
	public function setPrivateKey( $key = false, $type = false ) { // @codingStandardsIgnoreLine.
		if ( $key === false && ! empty( $this->publicExponent ) ) { // @codingStandardsIgnoreLine.
			$this->publicExponent = false; // @codingStandardsIgnoreLine.
			return true;
		}

		$rsa = new RSA();
		if ( ! $rsa->loadKey( $key, $type ) ) {
			return false;
		}
		$rsa->publicExponent = false; // @codingStandardsIgnoreLine.

		// don't overwrite the old key if the new key is invalid .
		$this->loadKey( $rsa );
		return true;
	}

	/**
	 * Returns the public key
	 *
	 * The public key is only returned under two circumstances - if the private key had the public key embedded within it
	 * or if the public key was set via setPublicKey().  If the currently loaded key is supposed to be the public key this
	 * function won't return it since this library, for the most part, doesn't distinguish between public and private keys.
	 *
	 * @see self::getPublicKey()
	 * @access public
	 * @param string $type .
	 */
	public function getPublicKey( $type = self::PUBLIC_FORMAT_PKCS8 ) { // @codingStandardsIgnoreLine.
		if ( empty( $this->modulus ) || empty( $this->publicExponent ) ) { // @codingStandardsIgnoreLine.
			return false;
		}

		$oldFormat             = $this->publicKeyFormat; // @codingStandardsIgnoreLine.
		$this->publicKeyFormat = $type; // @codingStandardsIgnoreLine.
		$temp                  = $this->_convertPublicKey( $this->modulus, $this->publicExponent ); // @codingStandardsIgnoreLine.
		$this->publicKeyFormat = $oldFormat; // @codingStandardsIgnoreLine.
		return $temp;
	}

	/**
	 * Returns the public key's fingerprint
	 *
	 * The public key's fingerprint is returned, which is equivalent to running `ssh-keygen -lf rsa.pub`. If there is
	 * no public key currently loaded, false is returned.
	 * Example output (md5): "c1:b1:30:29:d7:b8:de:6c:97:77:10:d7:46:41:63:87" (as specified by RFC 4716)
	 *
	 * @access public
	 * @param string $algorithm The hashing algorithm to be used. Valid options are 'md5' and 'sha256'. False is returned
	 * for invalid values.
	 * @return mixed
	 */
	public function getPublicKeyFingerprint( $algorithm = 'md5' ) { // @codingStandardsIgnoreLine.
		if ( empty( $this->modulus ) || empty( $this->publicExponent ) ) { // @codingStandardsIgnoreLine.
			return false;
		}

		$modulus        = $this->modulus->toBytes( true );
		$publicExponent = $this->publicExponent->toBytes( true ); // @codingStandardsIgnoreLine.

		$RSAPublicKey = pack( 'Na*Na*Na*', strlen( 'ssh-rsa' ), 'ssh-rsa', strlen( $publicExponent ), $publicExponent, strlen( $modulus ), $modulus ); // @codingStandardsIgnoreLine.

		switch ( $algorithm ) {
			case 'sha256':
				$hash = new Hash( 'sha256' );
				$base = base64_encode( $hash->hash( $RSAPublicKey ) ); // @codingStandardsIgnoreLine.
				return substr( $base, 0, strlen( $base ) - 1 );
			case 'md5':
				return substr( chunk_split( md5( $RSAPublicKey ), 2, ':' ), 0, -1 ); // @codingStandardsIgnoreLine.
			default:
				return false;
		}
	}

	/**
	 * Returns the private key
	 *
	 * The private key is only returned if the currently loaded key contains the constituent prime numbers.
	 *
	 * @see self::getPublicKey()
	 * @access public
	 * @param int $type optional .
	 * @return mixed
	 */
	public function getPrivateKey( $type = self::PUBLIC_FORMAT_PKCS1 ) { // @codingStandardsIgnoreLine.
		if ( empty( $this->primes ) ) {
			return false;
		}

		$oldFormat              = $this->privateKeyFormat; // @codingStandardsIgnoreLine.
		$this->privateKeyFormat = $type; // @codingStandardsIgnoreLine.
		$temp                   = $this->_convertPrivateKey( $this->modulus, $this->publicExponent, $this->exponent, $this->primes, $this->exponents, $this->coefficients ); // @codingStandardsIgnoreLine.
		$this->privateKeyFormat = $oldFormat; // @codingStandardsIgnoreLine.
		return $temp;
	}

	/**
	 * Returns a minimalistic private key
	 *
	 * Returns the private key without the prime number constituants.  Structurally identical to a public key that
	 * hasn't been set as the public key
	 *
	 * @see self::getPrivateKey()
	 * @access private
	 * @param string $mode .
	 */
	private function _getPrivatePublicKey( $mode = self::PUBLIC_FORMAT_PKCS8 ) { // @codingStandardsIgnoreLine.
		if ( empty( $this->modulus ) || empty( $this->exponent ) ) {
			return false;
		}

		$oldFormat             = $this->publicKeyFormat; // @codingStandardsIgnoreLine.
		$this->publicKeyFormat = $mode; // @codingStandardsIgnoreLine.
		$temp                  = $this->_convertPublicKey( $this->modulus, $this->exponent );
		$this->publicKeyFormat = $oldFormat; // @codingStandardsIgnoreLine.
		return $temp;
	}

	/**
	 *  __toString() magic method
	 *
	 * @access public
	 * @return string
	 */
	public function __toString() {
		$key = $this->getPrivateKey( $this->privateKeyFormat ); // @codingStandardsIgnoreLine.
		if ( false !== $key ) {
			return $key;
		}
		$key = $this->_getPrivatePublicKey( $this->publicKeyFormat ); // @codingStandardsIgnoreLine.
		return false !== $key ? $key : '';
	}

	/**
	 *  __clone() magic method
	 *
	 * @access public
	 * @return Crypt_RSA
	 */
	public function __clone() {
		$key = new RSA();
		$key->loadKey( $this );
		return $key;
	}

	/**
	 * Generates the smallest and largest numbers requiring $bits bits
	 *
	 * @access private
	 * @param int $bits .
	 * @return array
	 */
	private function _generateMinMax( $bits ) { // @codingStandardsIgnoreLine.
		$bytes = $bits >> 3;
		$min   = str_repeat( chr( 0 ), $bytes );
		$max   = str_repeat( chr( 0xFF ), $bytes );
		$msb   = $bits & 7;
		if ( $msb ) {
			$min = chr( 1 << ( $msb - 1 ) ) . $min;
			$max = chr( ( 1 << $msb ) - 1 ) . $max;
		} else {
			$min[0] = chr( 0x80 );
		}

		return array(
			'min' => new BigInteger( $min, 256 ),
			'max' => new BigInteger( $max, 256 ),
		);
	}

	/**
	 * DER-decode the length
	 *
	 * DER supports lengths up to (2**8)**127, however, we'll only support lengths up to (2**8)**4.  See
	 * {@link http://itu.int/ITU-T/studygroups/com17/languages/X.690-0207.pdf#p=13 X.690 paragraph 8.1.3} for more information.
	 *
	 * @access private
	 * @param string $string .
	 * @return int
	 */
	private function _decodeLength( &$string ) { // @codingStandardsIgnoreLine.
		$length = ord( $this->_string_shift( $string ) );
		if ( $length & 0x80 ) { // definite length, long form .
			$length        &= 0x7F;
			$temp           = $this->_string_shift( $string, $length );
			list(, $length) = unpack( 'N', substr( str_pad( $temp, 4, chr( 0 ), STR_PAD_LEFT ), -4 ) );
		}
		return $length;
	}

	/**
	 * DER-encode the length
	 *
	 * DER supports lengths up to (2**8)**127, however, we'll only support lengths up to (2**8)**4.  See
	 * {@link http://itu.int/ITU-T/studygroups/com17/languages/X.690-0207.pdf#p=13 X.690 paragraph 8.1.3} for more information.
	 *
	 * @access private
	 * @param int $length .
	 * @return string
	 */
	private function _encodeLength( $length ) { // @codingStandardsIgnoreLine.
		if ( $length <= 0x7F ) {
			return chr( $length );
		}

		$temp = ltrim( pack( 'N', $length ), chr( 0 ) );
		return pack( 'Ca*', 0x80 | strlen( $temp ), $temp );
	}

	/**
	 * String Shift
	 *
	 * Inspired by array_shift
	 *
	 * @param string $string .
	 * @param int    $index .
	 * @return string
	 * @access private
	 */
	private function _string_shift( &$string, $index = 1 ) { // @codingStandardsIgnoreLine.
		$substr = substr( $string, 0, $index );
		$string = substr( $string, $index );
		return $substr;
	}

	/**
	 * Determines the private key format
	 *
	 * @see self::createKey()
	 * @access public
	 * @param int $format .
	 */
	public function setPrivateKeyFormat( $format ) { // @codingStandardsIgnoreLine.
		$this->privateKeyFormat = $format; // @codingStandardsIgnoreLine.
	}

	/**
	 * Determines the public key format
	 *
	 * @see self::createKey()
	 * @access public
	 * @param int $format .
	 */
	public function setPublicKeyFormat( $format ) { // @codingStandardsIgnoreLine.
		$this->publicKeyFormat = $format; // @codingStandardsIgnoreLine.
	}

	/**
	 * Determines which hashing function should be used
	 *
	 * Used with signature production / verification and (if the encryption mode is self::ENCRYPTION_OAEP) encryption and
	 * decryption.  If $hash isn't supported, sha1 is used.
	 *
	 * @access public
	 * @param string $hash .
	 */
	public function setHash( $hash ) { // @codingStandardsIgnoreLine.
		// \phpseclib\Crypt\Hash supports algorithms that PKCS#1 doesn't support.  md5-96 and sha1-96, for example.
		switch ( $hash ) {
			case 'md2':
			case 'md5':
			case 'sha1':
			case 'sha256':
			case 'sha384':
			case 'sha512':
				$this->hash     = new Hash( $hash );
				$this->hashName = $hash; // @codingStandardsIgnoreLine.
				break;
			default:
				$this->hash     = new Hash( 'sha1' );
				$this->hashName = 'sha1'; // @codingStandardsIgnoreLine.
		}
		$this->hLen = $this->hash->getLength(); // @codingStandardsIgnoreLine.
	}

	/**
	 * Determines which hashing function should be used for the mask generation function
	 *
	 * The mask generation function is used by self::ENCRYPTION_OAEP and self::SIGNATURE_PSS and although it's
	 * best if Hash and MGFHash are set to the same thing this is not a requirement.
	 *
	 * @access public
	 * @param string $hash .
	 */
	public function setMGFHash( $hash ) { // @codingStandardsIgnoreLine.
		// \phpseclib\Crypt\Hash supports algorithms that PKCS#1 doesn't support.  md5-96 and sha1-96, for example.
		switch ( $hash ) {
			case 'md2':
			case 'md5':
			case 'sha1':
			case 'sha256':
			case 'sha384':
			case 'sha512':
				$this->mgfHash = new Hash( $hash ); // @codingStandardsIgnoreLine.
				break;
			default:
				$this->mgfHash = new Hash( 'sha1' ); // @codingStandardsIgnoreLine.
		}
		$this->mgfHLen = $this->mgfHash->getLength(); // @codingStandardsIgnoreLine.
	}

	/**
	 * Determines the salt length
	 *
	 * To quote from {@link http://tools.ietf.org/html/rfc3447#page-38 RFC3447#page-38}:
	 *
	 *    Typical salt lengths in octets are hLen (the length of the output
	 *    of the hash function Hash) and 0.
	 *
	 * @access public
	 * @param int $sLen .
	 */
	public function setSaltLength( $sLen ) { // @codingStandardsIgnoreLine.
		$this->sLen = $sLen; // @codingStandardsIgnoreLine.
	}

	/**
	 * Integer-to-Octet-String primitive
	 *
	 * See {@link http://tools.ietf.org/html/rfc3447#section-4.1 RFC3447#section-4.1}.
	 *
	 * @access private
	 * @param \phpseclib\Math\BigInteger $x .
	 * @param int                        $xLen .
	 * @return string
	 */
	private function _i2osp( $x, $xLen ) { // @codingStandardsIgnoreLine.
		$x = $x->toBytes();
		if ( strlen( $x ) > $xLen ) { // @codingStandardsIgnoreLine.
			user_error( 'Integer too large' );
			return false;
		}
		return str_pad( $x, $xLen, chr( 0 ), STR_PAD_LEFT ); // @codingStandardsIgnoreLine.
	}

	/**
	 * Octet-String-to-Integer primitive
	 *
	 * See {@link http://tools.ietf.org/html/rfc3447#section-4.2 RFC3447#section-4.2}.
	 *
	 * @access private
	 * @param string $x .
	 * @return \phpseclib\Math\BigInteger
	 */
	private function _os2ip( $x ) { // @codingStandardsIgnoreLine.
		return new BigInteger( $x, 256 );
	}

	/**
	 * Exponentiate with or without Chinese Remainder Theorem
	 *
	 * See {@link http://tools.ietf.org/html/rfc3447#section-5.1.1 RFC3447#section-5.1.2}.
	 *
	 * @access private
	 * @param \phpseclib\Math\BigInteger $x .
	 * @return \phpseclib\Math\BigInteger
	 */
	private function _exponentiate( $x ) { // @codingStandardsIgnoreLine.
		switch ( true ) {
			case empty( $this->primes ):
			case $this->primes[1]->equals( $this->zero ):
			case empty( $this->coefficients ):
			case $this->coefficients[2]->equals( $this->zero ):
			case empty( $this->exponents ):
			case $this->exponents[1]->equals( $this->zero ):
				return $x->modPow( $this->exponent, $this->modulus );
		}

		$num_primes = count( $this->primes );

		if ( defined( 'CRYPT_RSA_DISABLE_BLINDING' ) ) {
			$m_i       = array(
				1 => $x->modPow( $this->exponents[1], $this->primes[1] ),
				2 => $x->modPow( $this->exponents[2], $this->primes[2] ),
			);
			$h         = $m_i[1]->subtract( $m_i[2] );
			$h         = $h->multiply( $this->coefficients[2] );
			list(, $h) = $h->divide( $this->primes[1] );
			$m         = $m_i[2]->add( $h->multiply( $this->primes[2] ) );

			$r = $this->primes[1];
			for ( $i = 3; $i <= $num_primes; $i++ ) {
				$m_i = $x->modPow( $this->exponents[ $i ], $this->primes[ $i ] );

				$r = $r->multiply( $this->primes[ $i - 1 ] );

				$h         = $m_i->subtract( $m );
				$h         = $h->multiply( $this->coefficients[ $i ] );
				list(, $h) = $h->divide( $this->primes[ $i ] );

				$m = $m->add( $r->multiply( $h ) );
			}
		} else {
			$smallest = $this->primes[1];
			for ( $i = 2; $i <= $num_primes; $i++ ) {
				if ( $smallest->compare( $this->primes[ $i ] ) > 0 ) {
					$smallest = $this->primes[ $i ];
				}
			}

			$one = new BigInteger( 1 );

			$r = $one->random( $one, $smallest->subtract( $one ) );

			$m_i       = array(
				1 => $this->_blind( $x, $r, 1 ),
				2 => $this->_blind( $x, $r, 2 ),
			);
			$h         = $m_i[1]->subtract( $m_i[2] );
			$h         = $h->multiply( $this->coefficients[2] );
			list(, $h) = $h->divide( $this->primes[1] );
			$m         = $m_i[2]->add( $h->multiply( $this->primes[2] ) );

			$r = $this->primes[1];
			for ( $i = 3; $i <= $num_primes; $i++ ) {
				$m_i = $this->_blind( $x, $r, $i );

				$r = $r->multiply( $this->primes[ $i - 1 ] );

				$h         = $m_i->subtract( $m );
				$h         = $h->multiply( $this->coefficients[ $i ] );
				list(, $h) = $h->divide( $this->primes[ $i ] );

				$m = $m->add( $r->multiply( $h ) );
			}
		}

		return $m;
	}

	/**
	 * Performs RSA Blinding
	 *
	 * Protects against timing attacks by employing RSA Blinding.
	 * Returns $x->modPow($this->exponents[$i], $this->primes[$i])
	 *
	 * @access private
	 * @param \phpseclib\Math\BigInteger $x .
	 * @param \phpseclib\Math\BigInteger $r .
	 * @param int                        $i .
	 * @return \phpseclib\Math\BigInteger
	 */
	private function _blind( $x, $r, $i ) { // @codingStandardsIgnoreLine.
		$x = $x->multiply( $r->modPow( $this->publicExponent, $this->primes[ $i ] ) ); // @codingStandardsIgnoreLine.
		$x = $x->modPow( $this->exponents[ $i ], $this->primes[ $i ] );

		$r         = $r->modInverse( $this->primes[ $i ] );
		$x         = $x->multiply( $r );
		list(, $x) = $x->divide( $this->primes[ $i ] );

		return $x;
	}

	/**
	 * Performs blinded RSA equality testing
	 *
	 * Protects against a particular type of timing attack described.
	 *
	 * See {@link http://codahale.com/a-lesson-in-timing-attacks/ A Lesson In Timing Attacks (or, Don't use MessageDigest.isEquals)}
	 *
	 * Thanks for the heads up singpolyma!
	 *
	 * @access private
	 * @param string $x .
	 * @param string $y .
	 * @return bool
	 */
	private function _equals( $x, $y ) { // @codingStandardsIgnoreLine.
		if ( strlen( $x ) != strlen( $y ) ) { // WPCS:Loose comparison ok.
			return false;
		}

		$result = 0;
		for ( $i = 0; $i < strlen( $x ); $i++ ) { // @codingStandardsIgnoreLine.
			$result |= ord( $x[ $i ] ) ^ ord( $y[ $i ] );
		}

		return 0 == $result; // WPCS:Loose comparison ok.
	}

	/**
	 * RSAEP
	 *
	 * See {@link http://tools.ietf.org/html/rfc3447#section-5.1.1 RFC3447#section-5.1.1}.
	 *
	 * @access private
	 * @param \phpseclib\Math\BigInteger $m .
	 * @return \phpseclib\Math\BigInteger
	 */
	private function _rsaep( $m ) { // @codingStandardsIgnoreLine.
		if ( $m->compare( $this->zero ) < 0 || $m->compare( $this->modulus ) > 0 ) {
			user_error( 'Message representative out of range' );
			return false;
		}
		return $this->_exponentiate( $m );
	}

	/**
	 * RSADP
	 *
	 * See {@link http://tools.ietf.org/html/rfc3447#section-5.1.2 RFC3447#section-5.1.2}.
	 *
	 * @access private
	 * @param \phpseclib\Math\BigInteger $c .
	 * @return \phpseclib\Math\BigInteger
	 */
	private function _rsadp( $c ) { // @codingStandardsIgnoreLine.
		if ( $c->compare( $this->zero ) < 0 || $c->compare( $this->modulus ) > 0 ) {
			user_error( 'Ciphertext representative out of range' );
			return false;
		}
		return $this->_exponentiate( $c );
	}

	/**
	 * RSASP1
	 *
	 * See {@link http://tools.ietf.org/html/rfc3447#section-5.2.1 RFC3447#section-5.2.1}.
	 *
	 * @access private
	 * @param \phpseclib\Math\BigInteger $m .
	 * @return \phpseclib\Math\BigInteger
	 */
	private function _rsasp1( $m ) { // @codingStandardsIgnoreLine.
		if ( $m->compare( $this->zero ) < 0 || $m->compare( $this->modulus ) > 0 ) {
			user_error( 'Message representative out of range' );
			return false;
		}
		return $this->_exponentiate( $m );
	}

	/**
	 * RSAVP1
	 *
	 * See {@link http://tools.ietf.org/html/rfc3447#section-5.2.2 RFC3447#section-5.2.2}.
	 *
	 * @access private
	 * @param \phpseclib\Math\BigInteger $s .
	 * @return \phpseclib\Math\BigInteger
	 */
	private function _rsavp1( $s ) { // @codingStandardsIgnoreLine.
		if ( $s->compare( $this->zero ) < 0 || $s->compare( $this->modulus ) > 0 ) {
			user_error( 'Signature representative out of range' );
			return false;
		}
		return $this->_exponentiate( $s );
	}

	/**
	 * MGF1
	 *
	 * See {@link http://tools.ietf.org/html/rfc3447#appendix-B.2.1 RFC3447#appendix-B.2.1}.
	 *
	 * @access private
	 * @param string $mgfSeed .
	 * @param int    $maskLen .
	 * @return string
	 */
	private function _mgf1( $mgfSeed, $maskLen ) { // @codingStandardsIgnoreLine.
		$t     = '';
		$count = ceil( $maskLen / $this->mgfHLen ); // @codingStandardsIgnoreLine.
		for ( $i = 0; $i < $count; $i++ ) {
			$c  = pack( 'N', $i );
			$t .= $this->mgfHash->hash( $mgfSeed . $c ); // @codingStandardsIgnoreLine.
		}

		return substr( $t, 0, $maskLen ); // @codingStandardsIgnoreLine.
	}

	/**
	 * RSAES-OAEP-ENCRYPT
	 *
	 * See {@link http://tools.ietf.org/html/rfc3447#section-7.1.1 RFC3447#section-7.1.1} and
	 * {http://en.wikipedia.org/wiki/Optimal_Asymmetric_Encryption_Padding OAES}.
	 *
	 * @access private
	 * @param string $m .
	 * @param string $l .
	 * @return string
	 */
	private function _rsaes_oaep_encrypt( $m, $l = '' ) { // @codingStandardsIgnoreLine.
		$mLen = strlen( $m ); // @codingStandardsIgnoreLine.

		// Length checking
		// if $l is larger than two million terrabytes and you're using sha1, PKCS#1 suggests a "Label too long" error
		// be output.
		if ( $mLen > $this->k - 2 * $this->hLen - 2 ) { // @codingStandardsIgnoreLine.
			user_error( 'Message too long' );
			return false;
		}

		// EME-OAEP encoding .
		$lHash      = $this->hash->hash( $l ); // @codingStandardsIgnoreLine.
		$ps         = str_repeat( chr( 0 ), $this->k - $mLen - 2 * $this->hLen - 2 ); // @codingStandardsIgnoreLine.
		$db         = $lHash . $ps . chr( 1 ) . $m; // @codingStandardsIgnoreLine.
		$seed       = Random::string( $this->hLen ); // @codingStandardsIgnoreLine.
		$dbMask     = $this->_mgf1( $seed, $this->k - $this->hLen - 1 ); // @codingStandardsIgnoreLine.
		$maskedDB   = $db ^ $dbMask; // @codingStandardsIgnoreLine.
		$seedMask   = $this->_mgf1( $maskedDB, $this->hLen ); // @codingStandardsIgnoreLine.
		$maskedSeed = $seed ^ $seedMask; // @codingStandardsIgnoreLine.
		$em         = chr( 0 ) . $maskedSeed . $maskedDB; // @codingStandardsIgnoreLine.

		// RSA encryption .
		$m = $this->_os2ip( $em );
		$c = $this->_rsaep( $m );
		$c = $this->_i2osp( $c, $this->k );

		// Output the ciphertext C .
		return $c;
	}

	/**
	 * RSAES-OAEP-DECRYPT
	 *
	 * See {@link http://tools.ietf.org/html/rfc3447#section-7.1.2 RFC3447#section-7.1.2}.  The fact that the error
	 * messages aren't distinguishable from one another hinders debugging, but, to quote from RFC3447#section-7.1.2:
	 *
	 *    Note.  Care must be taken to ensure that an opponent cannot
	 *    distinguish the different error conditions in Step 3.g, whether by
	 *    error message or timing, or, more generally, learn partial
	 *    information about the encoded message EM.  Otherwise an opponent may
	 *    be able to obtain useful information about the decryption of the
	 *    ciphertext C, leading to a chosen-ciphertext attack such as the one
	 *    observed by Manger [36].
	 *
	 * As for $l...  to quote from {@link http://tools.ietf.org/html/rfc3447#page-17 RFC3447#page-17}:
	 *
	 *    Both the encryption and the decryption operations of RSAES-OAEP take
	 *    the value of a label L as input.  In this version of PKCS #1, L is
	 *    the empty string; other uses of the label are outside the scope of
	 *    this document.
	 *
	 * @access private
	 * @param string $c .
	 * @param string $l .
	 * @return string
	 */
	private function _rsaes_oaep_decrypt( $c, $l = '' ) { // @codingStandardsIgnoreLine.
		// Length checking
		// if $l is larger than two million terrabytes and you're using sha1, PKCS#1 suggests a "Label too long" error
		// be output.
		if ( strlen( $c ) != $this->k || $this->k < 2 * $this->hLen + 2 ) { // @codingStandardsIgnoreLine.
			user_error( 'Decryption error' );
			return false;
		}

		// RSA decryption .
		$c = $this->_os2ip( $c );
		$m = $this->_rsadp( $c );
		if ( false === $m ) {
			user_error( 'Decryption error' );
			return false;
		}
		$em = $this->_i2osp( $m, $this->k );

		// EME-OAEP decoding .
		$lHash      = $this->hash->hash( $l ); // @codingStandardsIgnoreLine.
		$y          = ord( $em[0] );
		$maskedSeed = substr( $em, 1, $this->hLen ); // @codingStandardsIgnoreLine.
		$maskedDB   = substr( $em, $this->hLen + 1 ); // @codingStandardsIgnoreLine.
		$seedMask   = $this->_mgf1( $maskedDB, $this->hLen ); // @codingStandardsIgnoreLine.
		$seed       = $maskedSeed ^ $seedMask; // @codingStandardsIgnoreLine.
		$dbMask     = $this->_mgf1( $seed, $this->k - $this->hLen - 1 ); // @codingStandardsIgnoreLine.
		$db         = $maskedDB ^ $dbMask; // @codingStandardsIgnoreLine.
		$lHash2     = substr( $db, 0, $this->hLen ); // @codingStandardsIgnoreLine.
		$m          = substr( $db, $this->hLen ); // @codingStandardsIgnoreLine.
		if ( $lHash != $lHash2 ) { // @codingStandardsIgnoreLine.
			user_error( 'Decryption error' );
			return false;
		}
		$m = ltrim( $m, chr( 0 ) );
		if ( ord( $m[0] ) != 1 ) { // WPCS:Loose comparison ok.
			user_error( 'Decryption error' );
			return false;
		}

		// Output the message M .
		return substr( $m, 1 );
	}

	/**
	 * Raw Encryption / Decryption
	 *
	 * Doesn't use padding and is not recommended.
	 *
	 * @access private
	 * @param string $m .
	 * @return string
	 */
	private function _raw_encrypt( $m ) { // @codingStandardsIgnoreLine.
		$temp = $this->_os2ip( $m );
		$temp = $this->_rsaep( $temp );
		return $this->_i2osp( $temp, $this->k );
	}

	/**
	 * RSAES-PKCS1-V1_5-ENCRYPT
	 *
	 * See {@link http://tools.ietf.org/html/rfc3447#section-7.2.1 RFC3447#section-7.2.1}.
	 *
	 * @access private
	 * @param string $m .
	 * @return string
	 */
	private function _rsaes_pkcs1_v1_5_encrypt( $m ) { // @codingStandardsIgnoreLine.
		$mLen = strlen( $m ); // @codingStandardsIgnoreLine.

		// Length checking .
		if ( $mLen > $this->k - 11 ) { // @codingStandardsIgnoreLine.
			user_error( 'Message too long' );
			return false;
		}

		// EME-PKCS1-v1_5 encoding .
		$psLen = $this->k - $mLen - 3; // @codingStandardsIgnoreLine.
		$ps    = '';
		while ( strlen( $ps ) != $psLen ) { // @codingStandardsIgnoreLine.
			$temp = Random::string( $psLen - strlen( $ps ) ); // @codingStandardsIgnoreLine.
			$temp = str_replace( "\x00", '', $temp );
			$ps  .= $temp;
		}
		$type = 2;
		// see the comments of _rsaes_pkcs1_v1_5_decrypt() to understand why this is being done .
		if ( defined( 'CRYPT_RSA_PKCS15_COMPAT' ) && ( ! isset( $this->publicExponent ) || $this->exponent !== $this->publicExponent ) ) { // @codingStandardsIgnoreLine.
			$type = 1;
			// "The padding string PS shall consist of k-3-||D|| octets. ... for block type 01, they shall have value FF"
			$ps = str_repeat( "\xFF", $psLen ); // @codingStandardsIgnoreLine.
		}
		$em = chr( 0 ) . chr( $type ) . $ps . chr( 0 ) . $m;

		// RSA encryption .
		$m = $this->_os2ip( $em );
		$c = $this->_rsaep( $m );
		$c = $this->_i2osp( $c, $this->k );

		// Output the ciphertext C .
		return $c;
	}

	/**
	 * RSAES-PKCS1-V1_5-DECRYPT
	 *
	 * See {@link http://tools.ietf.org/html/rfc3447#section-7.2.2 RFC3447#section-7.2.2}.
	 *
	 * For compatibility purposes, this function departs slightly from the description given in RFC3447.
	 * The reason being that RFC2313#section-8.1 (PKCS#1 v1.5) states that ciphertext's encrypted by the
	 * private key should have the second byte set to either 0 or 1 and that ciphertext's encrypted by the
	 * public key should have the second byte set to 2.  In RFC3447 (PKCS#1 v2.1), the second byte is supposed
	 * to be 2 regardless of which key is used.  For compatibility purposes, we'll just check to make sure the
	 * second byte is 2 or less.  If it is, we'll accept the decrypted string as valid.
	 *
	 * As a consequence of this, a private key encrypted ciphertext produced with \phpseclib\Crypt\RSA may not decrypt
	 * with a strictly PKCS#1 v1.5 compliant RSA implementation.  Public key encrypted ciphertext's should but
	 * not private key encrypted ciphertext's.
	 *
	 * @access private
	 * @param string $c .
	 * @return string
	 */
	private function _rsaes_pkcs1_v1_5_decrypt( $c ) { // @codingStandardsIgnoreLine.
		// Length checking .
		if ( strlen( $c ) != $this->k ) { // WPCS:Loose comparison ok.
			user_error( 'Decryption error' );
			return false;
		}

		// RSA decryption .
		$c = $this->_os2ip( $c );
		$m = $this->_rsadp( $c );

		if ( false === $m ) {
			user_error( 'Decryption error' );
			return false;
		}
		$em = $this->_i2osp( $m, $this->k );

		// EME-PKCS1-v1_5 decoding .
		if ( ord( $em[0] ) != 0 || ord( $em[1] ) > 2 ) { // WPCS:Loose comparison ok.
			user_error( 'Decryption error' );
			return false;
		}

		$ps = substr( $em, 2, strpos( $em, chr( 0 ), 2 ) - 2 );
		$m  = substr( $em, strlen( $ps ) + 3 );

		if ( strlen( $ps ) < 8 ) {
			user_error( 'Decryption error' );
			return false;
		}

		// Output M .
		return $m;
	}

	/**
	 * EMSA-PSS-ENCODE
	 *
	 * See {@link http://tools.ietf.org/html/rfc3447#section-9.1.1 RFC3447#section-9.1.1}.
	 *
	 * @access private
	 * @param string $m .
	 * @param int    $emBits .
	 */
	private function _emsa_pss_encode( $m, $emBits ) { // @codingStandardsIgnoreLine.
		// if $m is larger than two million terrabytes and you're using sha1, PKCS#1 suggests a "Label too long" error
		// be output.
		$emLen = ( $emBits + 1 ) >> 3; // @codingStandardsIgnoreLine.
		$sLen  = $this->sLen !== null ? $this->sLen : $this->hLen; // @codingStandardsIgnoreLine.

		$mHash = $this->hash->hash( $m ); // @codingStandardsIgnoreLine.
		if ( $emLen < $this->hLen + $sLen + 2 ) { // @codingStandardsIgnoreLine.
			user_error( 'Encoding error' );
			return false;
		}

		$salt        = Random::string( $sLen ); // @codingStandardsIgnoreLine.
		$m2          = "\0\0\0\0\0\0\0\0" . $mHash . $salt; // @codingStandardsIgnoreLine.
		$h           = $this->hash->hash( $m2 );
		$ps          = str_repeat( chr( 0 ), $emLen - $sLen - $this->hLen - 2 ); // @codingStandardsIgnoreLine.
		$db          = $ps . chr( 1 ) . $salt;
		$dbMask      = $this->_mgf1( $h, $emLen - $this->hLen - 1 ); // @codingStandardsIgnoreLine.
		$maskedDB    = $db ^ $dbMask; // @codingStandardsIgnoreLine.
		$maskedDB[0] = ~chr( 0xFF << ( $emBits & 7 ) ) & $maskedDB[0]; // @codingStandardsIgnoreLine.
		$em          = $maskedDB . $h . chr( 0xBC ); // @codingStandardsIgnoreLine.

		return $em;
	}

	/**
	 * EMSA-PSS-VERIFY
	 *
	 * See {@link http://tools.ietf.org/html/rfc3447#section-9.1.2 RFC3447#section-9.1.2}.
	 *
	 * @access private
	 * @param string $m .
	 * @param string $em .
	 * @param int    $emBits .
	 * @return string
	 */
	private function _emsa_pss_verify( $m, $em, $emBits ) { // @codingStandardsIgnoreLine.
		// if $m is larger than two million terrabytes and you're using sha1, PKCS#1 suggests a "Label too long" error
		// be output.
		$emLen = ( $emBits + 1 ) >> 3; // @codingStandardsIgnoreLine.
		$sLen  = $this->sLen !== null ? $this->sLen : $this->hLen; // @codingStandardsIgnoreLine.

		$mHash = $this->hash->hash( $m ); // @codingStandardsIgnoreLine.
		if ( $emLen < $this->hLen + $sLen + 2 ) { // @codingStandardsIgnoreLine.
			return false;
		}

		if ( chr( 0xBC ) != $em[ strlen( $em ) - 1 ] ) { // WPCS:Loose comparison ok.
			return false;
		}

		$maskedDB = substr( $em, 0, -$this->hLen - 1 ); // @codingStandardsIgnoreLine.
		$h        = substr( $em, -$this->hLen - 1, $this->hLen ); // @codingStandardsIgnoreLine.
		$temp     = chr( 0xFF << ( $emBits & 7 ) ); // @codingStandardsIgnoreLine.
		if ( ( ~$maskedDB[0] & $temp ) != $temp ) { // @codingStandardsIgnoreLine.
			return false;
		}
		$dbMask = $this->_mgf1( $h, $emLen - $this->hLen - 1 ); // @codingStandardsIgnoreLine.
		$db     = $maskedDB ^ $dbMask; // @codingStandardsIgnoreLine.
		$db[0]  = ~chr( 0xFF << ( $emBits & 7 ) ) & $db[0]; // @codingStandardsIgnoreLine.
		$temp   = $emLen - $this->hLen - $sLen - 2; // @codingStandardsIgnoreLine.
		if ( substr( $db, 0, $temp ) != str_repeat( chr( 0 ), $temp ) || ord( $db[ $temp ] ) != 1 ) { // WPCS:Loose comparison ok.
			return false;
		}
		$salt = substr( $db, $temp + 1 ); // should be $sLen long .
		$m2   = "\0\0\0\0\0\0\0\0" . $mHash . $salt; // @codingStandardsIgnoreLine.
		$h2   = $this->hash->hash( $m2 );
		return $this->_equals( $h, $h2 );
	}

	/**
	 * RSASSA-PSS-SIGN
	 *
	 * See {@link http://tools.ietf.org/html/rfc3447#section-8.1.1 RFC3447#section-8.1.1}.
	 *
	 * @access private
	 * @param string $m .
	 * @return string
	 */
	private function _rsassa_pss_sign( $m ) { // @codingStandardsIgnoreLine.
		// EMSA-PSS encoding .
		$em = $this->_emsa_pss_encode( $m, 8 * $this->k - 1 );

		// RSA signature .
		$m = $this->_os2ip( $em );
		$s = $this->_rsasp1( $m );
		$s = $this->_i2osp( $s, $this->k );

		// Output the signature S .
		return $s;
	}

	/**
	 * RSASSA-PSS-VERIFY
	 *
	 * See {@link http://tools.ietf.org/html/rfc3447#section-8.1.2 RFC3447#section-8.1.2}.
	 *
	 * @access private
	 * @param string $m .
	 * @param string $s .
	 * @return string
	 */
	private function _rsassa_pss_verify( $m, $s ) { // @codingStandardsIgnoreLine.
		// Length checking .
		if ( strlen( $s ) != $this->k ) { // WPCS:Loose comparison ok.
			user_error( 'Invalid signature' );
			return false;
		}

		// RSA verification .
		$modBits = 8 * $this->k; // @codingStandardsIgnoreLine.

		$s2 = $this->_os2ip( $s );
		$m2 = $this->_rsavp1( $s2 );
		if ( false === $m2 ) {
			user_error( 'Invalid signature' );
			return false;
		}
		$em = $this->_i2osp( $m2, $modBits >> 3 ); // @codingStandardsIgnoreLine.
		if ( false === $em ) {
			user_error( 'Invalid signature' );
			return false;
		}

		// EMSA-PSS verification .
		return $this->_emsa_pss_verify( $m, $em, $modBits - 1 ); // @codingStandardsIgnoreLine.
	}

	/**
	 * EMSA-PKCS1-V1_5-ENCODE
	 *
	 * See {@link http://tools.ietf.org/html/rfc3447#section-9.2 RFC3447#section-9.2}.
	 *
	 * @access private
	 * @param string $m .
	 * @param int    $emLen .
	 * @return string
	 */
	private function _emsa_pkcs1_v1_5_encode( $m, $emLen ) { // @codingStandardsIgnoreLine.
		$h = $this->hash->hash( $m );
		if ( false === $h ) {
			return false;
		}

		switch ( $this->hashName ) { // @codingStandardsIgnoreLine.
			case 'md2':
				$t = pack( 'H*', '3020300c06082a864886f70d020205000410' );
				break;
			case 'md5':
				$t = pack( 'H*', '3020300c06082a864886f70d020505000410' );
				break;
			case 'sha1':
				$t = pack( 'H*', '3021300906052b0e03021a05000414' );
				break;
			case 'sha256':
				$t = pack( 'H*', '3031300d060960864801650304020105000420' );
				break;
			case 'sha384':
				$t = pack( 'H*', '3041300d060960864801650304020205000430' );
				break;
			case 'sha512':
				$t = pack( 'H*', '3051300d060960864801650304020305000440' );
		}
		$t   .= $h;
		$tLen = strlen( $t ); // @codingStandardsIgnoreLine.

		if ( $emLen < $tLen + 11 ) { // @codingStandardsIgnoreLine.
			user_error( 'Intended encoded message length too short' );
			return false;
		}

		$ps = str_repeat( chr( 0xFF ), $emLen - $tLen - 3 ); // @codingStandardsIgnoreLine.

		$em = "\0\1$ps\0$t";

		return $em;
	}

	/**
	 * RSASSA-PKCS1-V1_5-SIGN
	 *
	 * See {@link http://tools.ietf.org/html/rfc3447#section-8.2.1 RFC3447#section-8.2.1}.
	 *
	 * @access private
	 * @param string $m .
	 * @return string
	 */
	private function _rsassa_pkcs1_v1_5_sign( $m ) { // @codingStandardsIgnoreLine.
		// EMSA-PKCS1-v1_5 encoding .
		$em = $this->_emsa_pkcs1_v1_5_encode( $m, $this->k );
		if ( false === $em ) {
			user_error( 'RSA modulus too short' );
			return false;
		}

		// RSA signature .
		$m = $this->_os2ip( $em );
		$s = $this->_rsasp1( $m );
		$s = $this->_i2osp( $s, $this->k );

		// Output the signature S .
		return $s;
	}

	/**
	 * RSASSA-PKCS1-V1_5-VERIFY
	 *
	 * See {@link http://tools.ietf.org/html/rfc3447#section-8.2.2 RFC3447#section-8.2.2}.
	 *
	 * @access private
	 * @param string $m .
	 * @param string $s .
	 * @return string
	 */
	private function _rsassa_pkcs1_v1_5_verify( $m, $s ) { // @codingStandardsIgnoreLine.
		// Length checking .
		if ( strlen( $s ) != $this->k ) { // WPCS:Loose comparison ok.
			user_error( 'Invalid signature' );
			return false;
		}

		// RSA verification .
		$s  = $this->_os2ip( $s );
		$m2 = $this->_rsavp1( $s );
		if ( false === $m2 ) {
			user_error( 'Invalid signature' );
			return false;
		}
		$em = $this->_i2osp( $m2, $this->k );
		if ( false === $em ) {
			user_error( 'Invalid signature' );
			return false;
		}

		// EMSA-PKCS1-v1_5 encoding .
		$em2 = $this->_emsa_pkcs1_v1_5_encode( $m, $this->k );
		if ( false === $em2 ) {
			user_error( 'RSA modulus too short' );
			return false;
		}

		// Compare .
		return $this->_equals( $em, $em2 );
	}

	/**
	 * Set Encryption Mode
	 *
	 * Valid values include self::ENCRYPTION_OAEP and self::ENCRYPTION_PKCS1.
	 *
	 * @access public
	 * @param int $mode .
	 */
	public function setEncryptionMode( $mode ) { // @codingStandardsIgnoreLine.
		$this->encryptionMode = $mode; // @codingStandardsIgnoreLine.
	}

	/**
	 * Set Signature Mode
	 *
	 * Valid values include self::SIGNATURE_PSS and self::SIGNATURE_PKCS1
	 *
	 * @access public
	 * @param int $mode .
	 */
	public function setSignatureMode( $mode ) { // @codingStandardsIgnoreLine.
		$this->signatureMode = $mode; // @codingStandardsIgnoreLine.
	}

	/**
	 * Set public key comment.
	 *
	 * @access public
	 * @param string $comment .
	 */
	public function setComment( $comment ) { // @codingStandardsIgnoreLine.
		$this->comment = $comment;
	}

	/**
	 * Get public key comment.
	 *
	 * @access public
	 * @return string
	 */
	public function getComment() { // @codingStandardsIgnoreLine.
		return $this->comment;
	}

	/**
	 * Encryption
	 *
	 * Both self::ENCRYPTION_OAEP and self::ENCRYPTION_PKCS1 both place limits on how long $plaintext can be.
	 * If $plaintext exceeds those limits it will be broken up so that it does and the resultant ciphertext's will
	 * be concatenated together.
	 *
	 * @see self::decrypt()
	 * @access public
	 * @param string $plaintext .
	 * @return string
	 */
	public function encrypt( $plaintext ) {
		switch ( $this->encryptionMode ) { // @codingStandardsIgnoreLine.
			case self::ENCRYPTION_NONE:
				$plaintext  = str_split( $plaintext, $this->k );
				$ciphertext = '';
				foreach ( $plaintext as $m ) {
					$ciphertext .= $this->_raw_encrypt( $m );
				}
				return $ciphertext;
			case self::ENCRYPTION_PKCS1:
				$length = $this->k - 11;
				if ( $length <= 0 ) {
					return false;
				}

				$plaintext  = str_split( $plaintext, $length );
				$ciphertext = '';
				foreach ( $plaintext as $m ) {
					$ciphertext .= $this->_rsaes_pkcs1_v1_5_encrypt( $m );
				}
				return $ciphertext;
			default:
				$length = $this->k - 2 * $this->hLen - 2; // @codingStandardsIgnoreLine.
				if ( $length <= 0 ) {
					return false;
				}

				$plaintext  = str_split( $plaintext, $length );
				$ciphertext = '';
				foreach ( $plaintext as $m ) {
					$ciphertext .= $this->_rsaes_oaep_encrypt( $m );
				}
				return $ciphertext;
		}
	}

	/**
	 * Decryption
	 *
	 * @see self::encrypt()
	 * @access public
	 * @param string $ciphertext .
	 * @return string
	 */
	public function decrypt( $ciphertext ) {
		if ( $this->k <= 0 ) {
			return false;
		}

		$ciphertext                             = str_split( $ciphertext, $this->k );
		$ciphertext[ count( $ciphertext ) - 1 ] = str_pad( $ciphertext[ count( $ciphertext ) - 1 ], $this->k, chr( 0 ), STR_PAD_LEFT );

		$plaintext = '';

		switch ( $this->encryptionMode ) { // @codingStandardsIgnoreLine.
			case self::ENCRYPTION_NONE:
				$decrypt = '_raw_encrypt';
				break;
			case self::ENCRYPTION_PKCS1:
				$decrypt = '_rsaes_pkcs1_v1_5_decrypt';
				break;
			default:
				$decrypt = '_rsaes_oaep_decrypt';
		}

		foreach ( $ciphertext as $c ) {
			$temp = $this->$decrypt( $c );
			if ( false === $temp ) {
				return false;
			}
			$plaintext .= $temp;
		}

		return $plaintext;
	}

	/**
	 * Create a signature
	 *
	 * @see self::verify()
	 * @access public
	 * @param string $message .
	 * @return string
	 */
	public function sign( $message ) {
		if ( empty( $this->modulus ) || empty( $this->exponent ) ) {
			return false;
		}

		switch ( $this->signatureMode ) { // @codingStandardsIgnoreLine.
			case self::SIGNATURE_PKCS1:
				return $this->_rsassa_pkcs1_v1_5_sign( $message );
			default:
				return $this->_rsassa_pss_sign( $message );
		}
	}

	/**
	 * Verifies a signature
	 *
	 * @see self::sign()
	 * @access public
	 * @param string $message .
	 * @param string $signature .
	 * @return bool
	 */
	public function verify( $message, $signature ) {
		if ( empty( $this->modulus ) || empty( $this->exponent ) ) {
			return false;
		}

		switch ( $this->signatureMode ) { // @codingStandardsIgnoreLine.
			case self::SIGNATURE_PKCS1:
				return $this->_rsassa_pkcs1_v1_5_verify( $message, $signature );
			default:
				return $this->_rsassa_pss_verify( $message, $signature );
		}
	}

	/**
	 * Extract raw BER from Base64 encoding
	 *
	 * @access private
	 * @param string $str .
	 * @return string
	 */
	private function _extractBER( $str ) { // @codingStandardsIgnoreLine.
		$temp = preg_replace( '#.*?^-+[^-]+-+[\r\n ]*$#ms', '', $str, 1 );
		$temp = preg_replace( '#-+[^-]+-+#', '', $temp );
		$temp = str_replace( array( "\r", "\n", ' ' ), '', $temp );
		$temp = preg_match( '#^[a-zA-Z\d/+]*={0,2}$#', $temp ) ? base64_decode( $temp ) : false;
		return false != $temp ? $temp : $str; // WPCS:Loose comparison ok.
	}
}
