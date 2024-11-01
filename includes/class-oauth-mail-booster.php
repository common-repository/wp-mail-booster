<?php
/**
 * This file is used to Initiate a connection to an SMTP server.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/includes
 * @version 2.0.0
 */

/**
 * This class is used to Initiate a connection to an SMTP server.
 */
class Oauth_Mail_Booster extends \PHPMailer {
	/**
	 * The OAuth user's email address
	 *
	 * @var string $oauthUserEmail .
	 */
	public $oauthUserEmail = ''; // @codingStandardsIgnoreLine

	/**
	 * The OAuth refresh token
	 *
	 * @var string $oauthRefreshToken
	 */
	public $oauthRefreshToken = ''; // @codingStandardsIgnoreLine

	/**
	 * The OAuth client ID
	 *
	 * @var string $oauthClientId
	 */
	public $oauthClientId = ''; // @codingStandardsIgnoreLine

	/**
	 * The OAuth client secret
	 *
	 * @var string $oauthClientSecret
	 */
	public $oauthClientSecret = ''; // @codingStandardsIgnoreLine

	/**
	 * An instance of the OAuth class.
	 *
	 * @var string $oauth
	 * @access protected
	 */
	protected $oauth = null;

	/**
	 * Get an OAuth instance to use.
	 *
	 * @return OAuth
	 */
	public function getOAUTHInstance() {
		if ( ! is_object( $this->oauth ) ) {
			/*
			 * this is the only part that differs,
			 * we create an object of our class Google_Oauth_Mail_Booster instead of the original OAuth class
			 */
			$this->oauth = new Google_Oauth_Mail_Booster(
				$this->oauthUserEmail, // @codingStandardsIgnoreLine
				$this->oauthClientSecret, // @codingStandardsIgnoreLine
				$this->oauthClientId, // @codingStandardsIgnoreLine
				$this->oauthRefreshToken // @codingStandardsIgnoreLine
			);
		}
		return $this->oauth;
	}

	/**
	 * Initiate a connection to an SMTP server.
	 * Overrides the original smtpConnect method to add support for OAuth.
	 *
	 * @param array $options An array of options compatible with stream_context_create() .
	 * @uses SMTP
	 * @access public
	 * @throws \phpmailerException .
	 * @return boolean
	 */
	public function smtpConnect( $options = array() ) {
		if ( is_null( $this->smtp ) ) {
			$this->smtp = $this->getSMTPInstance();
		}
		if ( is_null( $this->oauth ) ) {
			$this->oauth = $this->getOAUTHInstance();
		}
		// Already connected?
		if ( $this->smtp->connected() ) {
			return true;
		}

		$this->smtp->setTimeout( $this->Timeout ); // @codingStandardsIgnoreLine
		$this->smtp->setDebugLevel( $this->SMTPDebug ); // @codingStandardsIgnoreLine
		$this->smtp->setDebugOutput( $this->Debugoutput ); // @codingStandardsIgnoreLine
		$this->smtp->setVerp( $this->do_verp );
		$hosts         = explode( ';', $this->Host ); // @codingStandardsIgnoreLine
		$lastexception = null;
		foreach ( $hosts as $hostentry ) {
			$hostinfo = array();
			if ( ! preg_match( '/^((ssl|tls):\/\/)*([a-zA-Z0-9\.-]*):?([0-9]*)$/', trim( $hostentry ), $hostinfo ) ) {
				continue;
			}
			// $hostinfo[2]: optional ssl or tls prefix
			// $hostinfo[3]: the hostname
			// $hostinfo[4]: optional port number
			// The host string prefix can temporarily override the current setting for SMTPSecure
			// If it's not specified, the default value is used
			$prefix = '';
			$secure = $this->SMTPSecure; // @codingStandardsIgnoreLine
			$tls    = ( 'tls' == $this->SMTPSecure ); // @codingStandardsIgnoreLine

			if ( 'ssl' == $hostinfo[2] or ( '' == $hostinfo[2] and 'ssl' == $this->SMTPSecure ) ) { // @codingStandardsIgnoreLine
				$prefix = 'ssl://';
				$tls    = false; // Can't have SSL and TLS at the same time.
				$secure = 'ssl';
			} elseif ( 'tls' == $hostinfo[2] ) { // WPCS: loose comparison ok.
				$tls = true;
				// tls doesn't use a prefix.
				$secure = 'tls';
			}
			// Do we need the OpenSSL extension?
			$sslext = defined( 'OPENSSL_ALGO_SHA1' );
			if ( 'tls' === $secure or 'ssl' === $secure ) { // @codingStandardsIgnoreLine
				// Check for an OpenSSL constant rather than using extension_loaded, which is sometimes disabled.
				if ( ! $sslext ) {
					throw new \phpmailerException( $this->lang( 'extension_missing' ) . 'openssl', self::STOP_CRITICAL );
				}
			}
			$host  = $hostinfo[3];
			$port  = $this->Port; // @codingStandardsIgnoreLine
			$tport = (integer) $hostinfo[4];
			if ( $tport > 0 and $tport < 65536 ) { // @codingStandardsIgnoreLine
				$port = $tport;
			}
			if ( $this->smtp->connect( $prefix . $host, $port, $this->Timeout, $options ) ) { // @codingStandardsIgnoreLine
				try {
					if ( $this->Helo ) { // @codingStandardsIgnoreLine
							$hello = $this->Helo; // @codingStandardsIgnoreLine
					} else {
						$hello = $this->serverHostname();
					}
					$this->smtp->hello( $hello );
					// Automatically enable TLS encryption if:
					// * it's not disabled
					// * we have openssl extension
					// * we are not already using SSL
					// * the server offers STARTTLS.
					if ( $this->SMTPAutoTLS and $sslext and 'ssl' != $secure and $this->smtp->getServerExt( 'STARTTLS' ) ) { // @codingStandardsIgnoreLine
							$tls = true;
					}
					if ( $tls ) {
						if ( ! $this->smtp->startTLS() ) {
							throw new \phpmailerException( $this->lang( 'connect_host' ) );
						}
						$this->smtp->hello( $hello );
					}
					if ( $this->SMTPAuth ) { // @codingStandardsIgnoreLine
						if ( ! $this->smtp->authenticate(
							$this->Username, // @codingStandardsIgnoreLine
							$this->Password, // @codingStandardsIgnoreLine
							$this->AuthType, // @codingStandardsIgnoreLine
							$this->Realm, // @codingStandardsIgnoreLine
							$this->Workstation, // @codingStandardsIgnoreLine
							$this->oauth
						)
						) {
							throw new \phpmailerException( $this->lang( 'authenticate' ) );
						}
					}
					return true;
				} catch ( \phpmailerException $exc ) {
					$lastexception = $exc;
					$this->edebug( $exc->getMessage() );
					$this->smtp->quit();
				}
			}
		}
		$this->smtp->close();
		if ( $this->exceptions and ! is_null( $lastexception ) ) { // @codingStandardsIgnoreLine
			throw $lastexception;
		}
		return false;
	}
}
