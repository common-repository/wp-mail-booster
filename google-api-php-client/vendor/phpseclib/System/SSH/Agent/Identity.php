<?php // @codingStandardsIgnoreLine
/**
 * This file Pure-PHP ssh-agent client identity object .
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

/**
 * Pure-PHP ssh-agent client.
 *
 * PHP version 5
 *
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link      http://phpseclib.sourceforge.net
 * @internal  See http://api.libssh.org/rfc/PROTOCOL.agent
 */

namespace phpseclib\System\SSH\Agent;

use phpseclib\System\SSH\Agent;

/**
 * Pure-PHP ssh-agent client identity object
 *
 * Instantiation should only be performed by \phpseclib\System\SSH\Agent class.
 * This could be thought of as implementing an interface that phpseclib\Crypt\RSA
 * implements. ie. maybe a Net_SSH_Auth_PublicKey interface or something.
 * The methods in this interface would be getPublicKey and sign since those are the
 * methods phpseclib looks for to perform public key authentication.
 *
 * @access  internal
 */
class Identity {

	/**
	 * Key Object
	 *
	 * @var \phpseclib\Crypt\RSA
	 * @access private
	 * @see self::getPublicKey()
	 */
	var $key; // @codingStandardsIgnoreLine

	/**
	 * Key Blob
	 *
	 * @var string
	 * @access private
	 * @see self::sign()
	 */
	var $key_blob; // @codingStandardsIgnoreLine

	/**
	 * Socket Resource
	 *
	 * @var resource
	 * @access private
	 * @see self::sign()
	 */
	var $fsock; // @codingStandardsIgnoreLine

	/**
	 * Default Constructor.
	 *
	 * @param resource $fsock .
	 * @return \phpseclib\System\SSH\Agent\Identity
	 * @access private
	 */
	private function __construct( $fsock ) {
		$this->fsock = $fsock;
	}

	/**
	 * Set Public Key
	 *
	 * Called by \phpseclib\System\SSH\Agent::requestIdentities()
	 *
	 * @param \phpseclib\Crypt\RSA $key .
	 * @access private
	 */
	private function setPublicKey( $key ) { // @codingStandardsIgnoreLine
		$this->key = $key;
		$this->key->setPublicKey();
	}

	/**
	 * Set Public Key
	 *
	 * Called by \phpseclib\System\SSH\Agent::requestIdentities(). The key blob could be extracted from $this->key
	 * but this saves a small amount of computation.
	 *
	 * @param string $key_blob .
	 * @access private
	 */
	private function setPublicKeyBlob( $key_blob ) { // @codingStandardsIgnoreLine
		$this->key_blob = $key_blob;
	}

	/**
	 * Get Public Key
	 *
	 * Wrapper for $this->key->getPublicKey()
	 *
	 * @param int $format optional .
	 * @return mixed
	 * @access public
	 */
	public function getPublicKey( $format = null ) { // @codingStandardsIgnoreLine
		return ! isset( $format ) ? $this->key->getPublicKey() : $this->key->getPublicKey( $format );
	}

	/**
	 * Set Signature Mode
	 *
	 * Doesn't do anything as ssh-agent doesn't let you pick and choose the signature mode. ie.
	 * ssh-agent's only supported mode is \phpseclib\Crypt\RSA::SIGNATURE_PKCS1
	 *
	 * @param int $mode .
	 * @access public
	 */
	public function setSignatureMode( $mode ) { // @codingStandardsIgnoreLine
	}

	/**
	 * Create a signature
	 *
	 * See "2.6.2 Protocol 2 private key signature request"
	 *
	 * @param string $message .
	 * @return string
	 * @access public
	 */
	public function sign( $message ) {
		// the last parameter (currently 0) is for flags and ssh-agent only defines one flag (for ssh-dss): SSH_AGENT_OLD_SIGNATURE .
		$packet = pack( 'CNa*Na*N', Agent::SSH_AGENTC_SIGN_REQUEST, strlen( $this->key_blob ), $this->key_blob, strlen( $message ), $message, 0 );
		$packet = pack( 'Na*', strlen( $packet ), $packet );
		if ( strlen( $packet ) != fputs( $this->fsock, $packet ) ) { // @codingStandardsIgnoreLine
			user_error( 'Connection closed during signing' );
		}

		$length = current( unpack( 'N', fread( $this->fsock, 4 ) ) ); // @codingStandardsIgnoreLine
		$type   = ord( fread( $this->fsock, 1 ) ); // @codingStandardsIgnoreLine
		if ( $type != Agent::SSH_AGENT_SIGN_RESPONSE ) { // @codingStandardsIgnoreLine
			user_error( 'Unable to retrieve signature' );
		}

		$signature_blob = fread( $this->fsock, $length - 1 ); // @codingStandardsIgnoreLine
		// the only other signature format defined - ssh-dss - is the same length as ssh-rsa
		// the + 12 is for the other various SSH added length fields .
		return substr( $signature_blob, strlen( 'ssh-rsa' ) + 12 );
	}
}
