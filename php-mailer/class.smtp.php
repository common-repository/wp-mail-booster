<?php // @codingStandardsIgnoreLine.
/**
 * This file used to send email.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/php-mailer
 * @version 2.0.0
 */

/**
 * PHPMailer SMTP email transport class.
 */
class SMTP {

	/**
	 * The PHPMailer SMTP version number.
	 *
	 * @var string
	 */
	const VERSION = '5.2.26';

	/**
	 * SMTP line break constant.
	 *
	 * @var string
	 */
	const CRLF = "\r\n";

	/**
	 * The SMTP port to use if one is not specified.
	 *
	 * @var integer
	 */
	const DEFAULT_SMTP_PORT = 25;

	/**
	 * The maximum line length allowed by RFC 2822 section 2.1.1
	 *
	 * @var integer
	 */
	const MAX_LINE_LENGTH = 998;

	/**
	 * Debug level for no output
	 */
	const DEBUG_OFF = 0;

	/**
	 * Debug level to show client -> server messages
	 */
	const DEBUG_CLIENT = 1;

	/**
	 * Debug level to show client -> server and server -> client messages
	 */
	const DEBUG_SERVER = 2;

	/**
	 * Debug level to show connection status, client -> server and server -> client messages
	 */
	const DEBUG_CONNECTION = 3;

	/**
	 * Debug level to show all messages
	 */
	const DEBUG_LOWLEVEL = 4;

	/**
	 * The PHPMailer SMTP Version number.
	 *
	 * @var string
	 * @deprecated Use the `VERSION` constant instead
	 * @see SMTP::VERSION
	 */
	public $version = '5.2.26';

	/**
	 * SMTP server port number.
	 *
	 * @var integer
	 * @deprecated This is only ever used as a default value, so use the `DEFAULT_SMTP_PORT` constant instead
	 * @see SMTP::DEFAULT_SMTP_PORT
	 */
	public $SMTP_PORT = 25; // @codingStandardsIgnoreLine .

	/**
	 * SMTP reply line ending.
	 *
	 * @var string
	 * @deprecated Use the `CRLF` constant instead
	 * @see SMTP::CRLF
	 */
	public $CRLF = "\r\n"; // @codingStandardsIgnoreLine .

	/**
	 * Debug output level.
	 * Options:
	 * * self::DEBUG_OFF (`0`) No debug output, default
	 * * self::DEBUG_CLIENT (`1`) Client commands
	 * * self::DEBUG_SERVER (`2`) Client commands and server responses
	 * * self::DEBUG_CONNECTION (`3`) As DEBUG_SERVER plus connection status
	 * * self::DEBUG_LOWLEVEL (`4`) Low-level data output, all messages
	 *
	 * @var integer
	 */
	public $do_debug = self::DEBUG_OFF;

	/**
	 * How to handle debug output.
	 * Options:
	 * * `echo` Output plain-text as-is, appropriate for CLI
	 * * `html` Output escaped, line breaks converted to `<br>`, appropriate for browser output
	 * * `error_log` Output to error log as configured in php.ini
	 *
	 * @var string|callable
	 */
	public $Debugoutput = 'echo'; // @codingStandardsIgnoreLine .

	/**
	 * Whether to use VERP.
	 *
	 * @var boolean
	 */
	public $do_verp = false;

	/**
	 * The timeout value for connection, in seconds.
	 * Default of 5 minutes (300sec) is from RFC2821 section 4.5.3.2
	 * This needs to be quite high to function correctly with hosts using greetdelay as an anti-spam measure.
	 *
	 * @var integer
	 */
	public $Timeout = 300; // @codingStandardsIgnoreLine .

	/**
	 * How long to wait for commands to complete, in seconds.
	 * Default of 5 minutes (300sec) is from RFC2821 section 4.5.3.2
	 *
	 * @var integer
	 */
	public $Timelimit = 300; // @codingStandardsIgnoreLine .

	/**
	 * Stores smtp transaction patterns.
	 *
	 * @var array Patterns to extract an SMTP transaction id from reply to a DATA command.
	 * The first capture group in each regex will be used as the ID.
	 */
	protected $smtp_transaction_id_patterns = array(
		'exim'     => '/[0-9]{3} OK id=(.*)/',
		'sendmail' => '/[0-9]{3} 2.0.0 (.*) Message/',
		'postfix'  => '/[0-9]{3} 2.0.0 Ok: queued as (.*)/',
	);

	/**
	 * Stores last smtp transaction id.
	 *
	 * @var string The last transaction ID issued in response to a DATA command,
	 * if one was detected
	 */
	protected $last_smtp_transaction_id;

	/**
	 * The socket for the server connection.
	 *
	 * @var resource
	 */
	protected $smtp_conn;

	/**
	 * Error information, if any, for the last SMTP command.
	 *
	 * @var array
	 */
	protected $error = array(
		'error'        => '',
		'detail'       => '',
		'smtp_code'    => '',
		'smtp_code_ex' => '',
	);

	/**
	 * The reply the server sent to us for HELO.
	 * If null, no HELO string has yet been received.
	 *
	 * @var string|null
	 */
	protected $helo_rply = null;

	/**
	 * The set of SMTP extensions sent in reply to EHLO command.
	 * Indexes of the array are extension names.
	 * Value at index 'HELO' or 'EHLO' (according to command that was sent)
	 * represents the server name. In case of HELO it is the only element of the array.
	 * Other values can be boolean TRUE or an array containing extension options.
	 * If null, no HELO/EHLO string has yet been received.
	 *
	 * @var array|null
	 */
	protected $server_caps = null;

	/**
	 * The most recent reply received from the server.
	 *
	 * @var string
	 */
	protected $last_reply = '';

	/**
	 * Output debugging info via a user-selected method.
	 *
	 * @see SMTP::$Debugoutput
	 * @see SMTP::$do_debug
	 * @param string  $str Debug string to output .
	 * @param integer $level The debug level of this message; see DEBUG_* constants .
	 * @return void
	 */
	protected function edebug( $str, $level = 0 ) {
		if ( $level > $this->do_debug ) {
			return;
		}
		// Avoid clash with built-in function names.
		if ( ! in_array( $this->Debugoutput, array( 'error_log', 'html', 'echo' ) ) && is_callable( $this->Debugoutput ) ) {  // @codingStandardsIgnoreLine.
			call_user_func( $this->Debugoutput, $str, $level ); // @codingStandardsIgnoreLine .
			return;
		}
		switch ( $this->Debugoutput ) { // @codingStandardsIgnoreLine .
			case 'error_log':
				// Don't output, just log.
				error_log( $str ); // @codingStandardsIgnoreLine
				$mail_booster_debug_output  = get_option( 'mail_booster_mail_status' );
				$mail_booster_debug_output .= $str;
				update_option( 'mail_booster_mail_status', $mail_booster_debug_output );
				update_option( 'mail_booster_is_mail_sent', 'Not Sent' );
				break;
			case 'html':
				// Cleans up output a bit for a better looking, HTML-safe output.
				$mail_booster_debug_output  = get_option( 'mail_booster_mail_status' );
				$mail_booster_debug_output .= gmdate( 'Y-m-d H:i:s' ) . ' ' . htmlentities(
					preg_replace( '/[\r\n]+/', '', $str ),
					ENT_QUOTES,
					'UTF-8'
				) . "<br>\n";
				update_option( 'mail_booster_mail_status', $mail_booster_debug_output );
				update_option( 'mail_booster_is_mail_sent', 'Sent' );
				break;
			case 'echo':
			default:
				// Normalize line breaks.
				$str                        = preg_replace( '/(\r\n|\r|\n)/ms', "\n", $str );
				$mail_booster_debug_output  = get_option( 'mail_booster_mail_status' );
				$mail_booster_debug_output .= gmdate( 'Y-m-d H:i:s' ) . "\t" . str_replace(
					"\n",
					"\n                   \t                  ",
					trim( $str )
				) . "\n";
				update_option( 'mail_booster_mail_status', $mail_booster_debug_output );
				update_option( 'mail_booster_is_mail_sent', 'Sent' );
		}
	}

	/**
	 * Connect to an SMTP server.
	 *
	 * @param string  $host SMTP server IP or host name .
	 * @param integer $port The port number to connect to .
	 * @param integer $timeout How long to wait for the connection to open .
	 * @param array   $options An array of options for stream_context_create() .
	 * @access public
	 * @return boolean
	 */
	public function connect( $host, $port = null, $timeout = 30, $options = array() ) {
		static $streamok;
		// This is enabled by default since 5.0.0 but some providers disable it
		// Check this once and cache the result.
		if ( is_null( $streamok ) ) {
			$streamok = function_exists( 'stream_socket_client' );
		}
		// Clear errors to avoid confusion.
		$this->setError( '' );
		// Make sure we are __not__ connected.
		if ( $this->connected() ) {
			// Already connected, generate error.
			$this->setError( 'Already connected to a server' );
			return false;
		}
		if ( empty( $port ) ) {
			$port = self::DEFAULT_SMTP_PORT;
		}
		// Connect to the SMTP server.
		$this->edebug(
			"Connection: opening to $host:$port, timeout=$timeout, options=" .
			var_export( $options, true ),// @codingStandardsIgnoreLine
			self::DEBUG_CONNECTION
		);
		$errno  = 0;
		$errstr = '';
		if ( $streamok ) {
			$socket_context = stream_context_create( $options );
			set_error_handler( array( $this, 'errorHandler' ) );// @codingStandardsIgnoreLine
			$this->smtp_conn = stream_socket_client(
				$host . ':' . $port,
				$errno,
				$errstr,
				$timeout,
				STREAM_CLIENT_CONNECT,
				$socket_context
			);
			restore_error_handler();
		} else {
			// Fall back to fsockopen which should work in more places, but is missing some features.
			$this->edebug(
				'Connection: stream_socket_client not available, falling back to fsockopen',
				self::DEBUG_CONNECTION
			);
			set_error_handler( array( $this, 'errorHandler' ) );// @codingStandardsIgnoreLine
			$this->smtp_conn = fsockopen(// @codingStandardsIgnoreLine
				$host,
				$port,
				$errno,
				$errstr,
				$timeout
			);
			restore_error_handler();
		}
		// Verify we connected properly.
		if ( ! is_resource( $this->smtp_conn ) ) {
			$this->setError(
				'Failed to connect to server',
				$errno,
				$errstr
			);
			$this->edebug(
				'SMTP ERROR: ' . $this->error['error']
				. ": $errstr ($errno)",
				self::DEBUG_CLIENT
			);
			return false;
		}
		$this->edebug( 'Connection: opened', self::DEBUG_CONNECTION );
		// SMTP server can take longer to respond, give longer timeout for first read
		// Windows does not have support for this timeout function.
		if ( substr( PHP_OS, 0, 3 ) != 'WIN' ) { // WPCS: Loose comparison ok .
			$max = ini_get( 'max_execution_time' );
			// Don't bother if unlimited .
			if ( 0 != $max && $timeout > $max ) { // WPCS: Loose comparison ok .
				@set_time_limit( $timeout ); // @codingStandardsIgnoreLine.
			}
			stream_set_timeout( $this->smtp_conn, $timeout, 0 );
		}
		// Get any announcement.
		$announce = $this->get_lines();
		$this->edebug( 'SERVER -> CLIENT: ' . $announce, self::DEBUG_SERVER );
		return true;
	}

	/**
	 * Initiate a TLS (encrypted) session.
	 *
	 * @access public
	 * @return boolean
	 */
	public function startTLS() { // @codingStandardsIgnoreLine .
		if ( ! $this->sendCommand( 'STARTTLS', 'STARTTLS', 220 ) ) {
			return false;
		}

		// Allow the best TLS version(s) we can.
		$crypto_method = STREAM_CRYPTO_METHOD_TLS_CLIENT;

		// PHP 5.6.7 dropped inclusion of TLS 1.1 and 1.2 in STREAM_CRYPTO_METHOD_TLS_CLIENT
		// so add them back in manually if we can.
		if ( defined( 'STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT' ) ) {
			$crypto_method |= STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;
			$crypto_method |= STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT;
		}

		// Begin encrypted connection.
		set_error_handler( array( $this, 'errorHandler' ) );// @codingStandardsIgnoreLine
		$crypto_ok = stream_socket_enable_crypto(
			$this->smtp_conn,
			true,
			$crypto_method
		);
		restore_error_handler();
		return $crypto_ok;
	}

	/**
	 * Perform SMTP authentication.
	 * Must be run after hello().
	 *
	 * @see hello()
	 * @param string     $username The user name .
	 * @param string     $password The password .
	 * @param string     $authtype The auth type (PLAIN, LOGIN, NTLM, CRAM-MD5, XOAUTH2) .
	 * @param string     $realm The auth realm for NTLM .
	 * @param string     $workstation The auth workstation for NTLM .
	 * @param null|OAuth $OAuth An optional OAuth instance (@see PHPMailerOAuth) .
	 * @return bool True if successfully authenticated.* @access public
	 */
	public function authenticate(
		$username,
		$password,
		$authtype = null,
		$realm = '',
		$workstation = '',
		$OAuth = null // @codingStandardsIgnoreLine .
	) {
		if ( ! $this->server_caps ) {
			$this->setError( 'Authentication is not allowed before HELO/EHLO' );
			return false;
		}

		if ( array_key_exists( 'EHLO', $this->server_caps ) ) {
			// SMTP extensions are available; try to find a proper authentication method.
			if ( ! array_key_exists( 'AUTH', $this->server_caps ) ) {
				$this->setError( 'Authentication is not allowed at this stage' );
				// 'at this stage' means that auth may be allowed after the stage changes
				// e.g. after STARTTLS
				return false;
			}

			self::edebug( 'Auth method requested: ' . ( $authtype ? $authtype : 'UNKNOWN' ), self::DEBUG_LOWLEVEL );
			self::edebug(
				'Auth methods available on the server: ' . implode( ',', $this->server_caps['AUTH'] ),
				self::DEBUG_LOWLEVEL
			);

			if ( empty( $authtype ) ) {
				foreach ( array( 'CRAM-MD5', 'LOGIN', 'PLAIN', 'NTLM', 'XOAUTH2' ) as $method ) {
					if ( in_array( $method, $this->server_caps['AUTH'] ) ) { // @codingStandardsIgnoreLine.
						$authtype = $method;
						break;
					}
				}
				if ( empty( $authtype ) ) {
					$this->setError( 'No supported authentication methods found' );
					return false;
				}
				self::edebug( 'Auth method selected: ' . $authtype, self::DEBUG_LOWLEVEL );
			}

			if ( ! in_array( $authtype, $this->server_caps['AUTH'] ) ) { // @codingStandardsIgnoreLine.
				$this->setError( "The requested authentication method \"$authtype\" is not supported by the server" );
				return false;
			}
		} elseif ( empty( $authtype ) ) {
			$authtype = 'LOGIN';
		}
		switch ( $authtype ) {
			case 'PLAIN':
				// Start authentication.
				if ( ! $this->sendCommand( 'AUTH', 'AUTH PLAIN', 334 ) ) {
					return false;
				}
				// Send encoded username and password.
				if ( ! $this->sendCommand(
					'User & Password',
					base64_encode( "\0" . $username . "\0" . $password ),
					235
				)
				) {
					return false;
				}
				break;
			case 'LOGIN':
				// Start authentication.
				if ( ! $this->sendCommand( 'AUTH', 'AUTH LOGIN', 334 ) ) {
					return false;
				}
				if ( ! $this->sendCommand( 'Username', base64_encode( $username ), 334 ) ) {
					return false;
				}
				if ( ! $this->sendCommand( 'Password', base64_encode( $password ), 235 ) ) {
					return false;
				}
				break;
			case 'XOAUTH2':
				// If the OAuth Instance is not set. Can be a case when PHPMailer is used
				// instead of PHPMailerOAuth.
				if ( is_null( $OAuth ) ) { // @codingStandardsIgnoreLine .
					return false;
				}
				$oauth = $OAuth->getOauth64(); // @codingStandardsIgnoreLine .

				// Start authentication .
				if ( ! $this->sendCommand( 'AUTH', 'AUTH XOAUTH2 ' . $oauth, 235 ) ) {
					return false;
				}
				break;
			case 'NTLM':
				/*
				 * ntlm_sasl_client.php
				 * Bundled with Permission
				 *
				 * How to telnet in windows:
				 */
				require_once 'extras/ntlm_sasl_client.php';
				$temp        = new stdClass();
				$ntlm_client = new ntlm_sasl_client_class();
				// Check that functions are available.
				if ( ! $ntlm_client->initialize( $temp ) ) {
					$this->setError( $temp->error );
					$this->edebug(
						'You need to enable some modules in your php.ini file: '
						. $this->error['error'],
						self::DEBUG_CLIENT
					);
					return false;
				}
				// msg1.
				$msg1 = $ntlm_client->typeMsg1( $realm, $workstation ); // msg1.

				if ( ! $this->sendCommand(
					'AUTH NTLM',
					'AUTH NTLM ' . base64_encode( $msg1 ),
					334
				)
				) {
					return false;
				}
				// Though 0 based, there is a white space after the 3 digit number
				// msg2.
				$challenge = substr( $this->last_reply, 3 );
				$challenge = base64_decode( $challenge );
				$ntlm_res  = $ntlm_client->NTLMResponse(
					substr( $challenge, 24, 8 ),
					$password
				);
				// msg3.
				$msg3 = $ntlm_client->typeMsg3(
					$ntlm_res,
					$username,
					$realm,
					$workstation
				);
				// send encoded username.
				return $this->sendCommand( 'Username', base64_encode( $msg3 ), 235 );
			case 'CRAM-MD5':
				// Start authentication.
				if ( ! $this->sendCommand( 'AUTH CRAM-MD5', 'AUTH CRAM-MD5', 334 ) ) {
					return false;
				}
				// Get the challenge.
				$challenge = base64_decode( substr( $this->last_reply, 4 ) );

				// Build the response.
				$response = $username . ' ' . $this->hmac( $challenge, $password );

				// send encoded credentials.
				return $this->sendCommand( 'Username', base64_encode( $response ), 235 );
			default:
				$this->setError( "Authentication method \"$authtype\" is not supported" );
				return false;
		}
		return true;
	}

	/**
	 * Calculate an MD5 HMAC hash.
	 * Works like hash_hmac('md5', $data, $key)
	 * in case that function is not available
	 *
	 * @param string $data The data to hash .
	 * @param string $key The key to hash with .
	 * @access protected
	 * @return string
	 */
	protected function hmac( $data, $key ) {
		if ( function_exists( 'hash_hmac' ) ) {
			return hash_hmac( 'md5', $data, $key );
		}

		$bytelen = 64; // byte length for md5.
		if ( strlen( $key ) > $bytelen ) {
			$key = pack( 'H*', md5( $key ) );
		}
		$key    = str_pad( $key, $bytelen, chr( 0x00 ) );
		$ipad   = str_pad( '', $bytelen, chr( 0x36 ) );
		$opad   = str_pad( '', $bytelen, chr( 0x5c ) );
		$k_ipad = $key ^ $ipad;
		$k_opad = $key ^ $opad;

		return md5( $k_opad . pack( 'H*', md5( $k_ipad . $data ) ) );
	}

	/**
	 * Check connection state.
	 *
	 * @access public
	 * @return boolean True if connected.
	 */
	public function connected() {
		if ( is_resource( $this->smtp_conn ) ) {
			$sock_status = stream_get_meta_data( $this->smtp_conn );
			if ( $sock_status['eof'] ) {
				// The socket is valid but we are not connected.
				$this->edebug(
					'SMTP NOTICE: EOF caught while checking if connected',
					self::DEBUG_CLIENT
				);
				$this->close();
				return false;
			}
			return true; // everything looks good.
		}
		return false;
	}

	/**
	 * Close the socket and clean up the state of the class.
	 * Don't use this function without first trying to use QUIT.
	 *
	 * @see quit()
	 * @access public
	 * @return void
	 */
	public function close() {
		$this->setError( '' );
		$this->server_caps = null;
		$this->helo_rply   = null;
		if ( is_resource( $this->smtp_conn ) ) {
			// close the connection and cleanup.
			fclose( $this->smtp_conn );// @codingStandardsIgnoreLine
			$this->smtp_conn = null; // Makes for cleaner serialization.
			$this->edebug( 'Connection: closed', self::DEBUG_CONNECTION );
		}
	}

	/**
	 * Send an SMTP DATA command.
	 * Issues a data command and sends the msg_data to the server,
	 * finializing the mail transaction. $msg_data is the message
	 * that is to be send with the headers. Each header needs to be
	 * on a single line followed by a <CRLF> with the message headers
	 * and the message body being separated by and additional <CRLF>.
	 * Implements rfc 821: DATA <CRLF>
	 *
	 * @param string $msg_data Message data to send .
	 * @access public
	 * @return boolean
	 */
	public function data( $msg_data ) {
		// This will use the standard timelimit.
		if ( ! $this->sendCommand( 'DATA', 'DATA', 354 ) ) {
			return false;
		}

		/**
		 * The server is ready to accept data!
		 * According to rfc821 we should not send more than 1000 characters on a single line (including the CRLF)
		 * so we will break the data up into lines by \r and/or \n then if needed we will break each of those into
		 * smaller lines to fit within the limit.
		 * We will also look for lines that start with a '.' and prepend an additional '.'.
		 * NOTE: this does not count towards line-length limit.
		 */

		// Normalize line breaks before exploding.
		$lines = explode( "\n", str_replace( array( "\r\n", "\r" ), "\n", $msg_data ) );

		/**
		 *To distinguish between a complete RFC822 message and a plain message body, we check if the first field
		 * of the first line (':' separated) does not contain a space then it _should_ be a header and we will
		 * process all lines before a blank line as headers.
		 */

		$field      = substr( $lines[0], 0, strpos( $lines[0], ':' ) );
		$in_headers = false;
		if ( ! empty( $field ) && strpos( $field, ' ' ) === false ) {
			$in_headers = true;
		}

		foreach ( $lines as $line ) {
			$lines_out = array();
			if ( $in_headers && '' == $line ) { // WPCS: Loose comparison ok .
				$in_headers = false;
			}
			// Break this line up into several smaller lines if it's too long
			// Micro-optimisation: isset($str[$len]) is faster than (strlen($str) > $len),.
			while ( isset( $line[ self::MAX_LINE_LENGTH ] ) ) {
				// Working backwards, try to find a space within the last MAX_LINE_LENGTH chars of the line to break on
				// so as to avoid breaking in the middle of a word.
				$pos = strrpos( substr( $line, 0, self::MAX_LINE_LENGTH ), ' ' );
				// Deliberately matches both false and 0.
				if ( ! $pos ) {
					// No nice break found, add a hard break .
					$pos         = self::MAX_LINE_LENGTH - 1;
					$lines_out[] = substr( $line, 0, $pos );
					$line        = substr( $line, $pos );
				} else {
					// Break at the found point.
					$lines_out[] = substr( $line, 0, $pos );
					// Move along by the amount we dealt with.
					$line = substr( $line, $pos + 1 );
				}
				// If processing headers add a LWSP-char to the front of new line RFC822 section 3.1.1.
				if ( $in_headers ) {
					$line = "\t" . $line;
				}
			}
			$lines_out[] = $line;

			// Send the lines to the server.
			foreach ( $lines_out as $line_out ) {
				// RFC2821 section 4.5.2.
				if ( ! empty( $line_out ) && '.' == $line_out[0] ) { // WPCS: Loose comparison ok .
					$line_out = '.' . $line_out;
				}
				$this->client_send( $line_out . self::CRLF );
			}
		}

		// Message data has been sent, complete the command
		// Increase timelimit for end of DATA command.
		$savetimelimit   = $this->Timelimit; // @codingStandardsIgnoreLine .
		$this->Timelimit = $this->Timelimit * 2; // @codingStandardsIgnoreLine .
		$result          = $this->sendCommand( 'DATA END', '.', 250 );
		$this->recordLastTransactionID();
		// Restore timelimit.
		$this->Timelimit = $savetimelimit; // @codingStandardsIgnoreLine .
		return $result;
	}

	/**
	 * Send an SMTP HELO or EHLO command.
	 * Used to identify the sending server to the receiving server.
	 * This makes sure that client and server are in a known state.
	 * Implements RFC 821: HELO <SP> <domain> <CRLF>
	 * and RFC 2821 EHLO.
	 *
	 * @param string $host The host name or IP to connect to .
	 * @access public
	 * @return boolean
	 */
	public function hello( $host = '' ) {
		// Try extended hello first (RFC 2821).
		return (boolean) ( $this->sendHello( 'EHLO', $host ) || $this->sendHello( 'HELO', $host ) );
	}

	/**
	 * Send an SMTP HELO or EHLO command.
	 * Low-level implementation used by hello()
	 *
	 * @see hello()
	 * @param string $hello The HELO string .
	 * @param string $host The hostname to say we are .
	 * @access protected
	 * @return boolean
	 */
	protected function sendHello( $hello, $host ) { // @codingStandardsIgnoreLine .
		$noerror         = $this->sendCommand( $hello, $hello . ' ' . $host, 250 );
		$this->helo_rply = $this->last_reply;
		if ( $noerror ) {
			$this->parseHelloFields( $hello );
		} else {
			$this->server_caps = null;
		}
		return $noerror;
	}

	/**
	 * Parse a reply to HELO/EHLO command to discover server extensions.
	 * In case of HELO, the only parameter that can be discovered is a server name.
	 *
	 * @access protected
	 * @param string $type - 'HELO' or 'EHLO' .
	 */
	protected function parseHelloFields( $type ) { // @codingStandardsIgnoreLine .
		$this->server_caps = array();
		$lines             = explode( "\n", $this->helo_rply );

		foreach ( $lines as $n => $s ) {
			// First 4 chars contain response code followed by - or space .
			$s = trim( substr( $s, 4 ) );
			if ( empty( $s ) ) {
				continue;
			}
			$fields = explode( ' ', $s );
			if ( ! empty( $fields ) ) {
				if ( ! $n ) {
					$name   = $type;
					$fields = $fields[0];
				} else {
					$name = array_shift( $fields );
					switch ( $name ) {
						case 'SIZE':
							$fields = ( $fields ? $fields[0] : 0 );
							break;
						case 'AUTH':
							if ( ! is_array( $fields ) ) {
								$fields = array();
							}
							break;
						default:
							$fields = true;
					}
				}
				$this->server_caps[ $name ] = $fields;
			}
		}
	}

	/**
	 * Send an SMTP MAIL command.
	 * Starts a mail transaction from the email address specified in
	 * $from. Returns true if successful or false otherwise. If True
	 * the mail transaction is started and then one or more recipient
	 * commands may be called followed by a data command.
	 * Implements rfc 821: MAIL <SP> FROM:<reverse-path> <CRLF>
	 *
	 * @param string $from Source address of this message .
	 * @access public
	 * @return boolean
	 */
	public function mail( $from ) {
		$useVerp = ( $this->do_verp ? ' XVERP' : '' ); // @codingStandardsIgnoreLine .
		return $this->sendCommand(
			'MAIL FROM',
			'MAIL FROM:<' . $from . '>' . $useVerp, // @codingStandardsIgnoreLine .
			250
		);
	}

	/**
	 * Send an SMTP QUIT command.
	 * Closes the socket if there is no error or the $close_on_error argument is true.
	 * Implements from rfc 821: QUIT <CRLF>
	 *
	 * @param boolean $close_on_error Should the connection close if an error occurs?.
	 * @access public
	 * @return boolean
	 */
	public function quit( $close_on_error = true ) {
		$noerror = $this->sendCommand( 'QUIT', 'QUIT', 221 );
		$err     = $this->error; // Save any error.
		if ( $noerror || $close_on_error ) {
			$this->close();
			$this->error = $err; // Restore any error from the quit command.
		}
		return $noerror;
	}

	/**
	 * Send an SMTP RCPT command.
	 * Sets the TO argument to $toaddr.
	 * Returns true if the recipient was accepted false if it was rejected.
	 * Implements from rfc 821: RCPT <SP> TO:<forward-path> <CRLF>
	 *
	 * @param string $address The address the message is being sent to .
	 * @access public
	 * @return boolean
	 */
	public function recipient( $address ) {
		return $this->sendCommand(
			'RCPT TO',
			'RCPT TO:<' . $address . '>',
			array( 250, 251 )
		);
	}

	/**
	 * Send an SMTP RSET command.
	 * Abort any transaction that is currently in progress.
	 * Implements rfc 821: RSET <CRLF>
	 *
	 * @access public
	 * @return boolean True on success.
	 */
	public function reset() {
		return $this->sendCommand( 'RSET', 'RSET', 250 );
	}

	/**
	 * Send a command to an SMTP server and check its return code.
	 *
	 * @param string        $command The command name - not sent to the server .
	 * @param string        $commandstring The actual command to send .
	 * @param integer|array $expect One or more expected integer success codes .
	 * @access protected
	 * @return boolean True on success.
	 */
	protected function sendCommand( $command, $commandstring, $expect ) { // @codingStandardsIgnoreLine .
		if ( ! $this->connected() ) {
			$this->setError( "Called $command without being connected" );
			return false;
		}
		// Reject line breaks in all commands.
		if ( strpos( $commandstring, "\n" ) !== false || strpos( $commandstring, "\r" ) !== false ) {
			$this->setError( "Command '$command' contained line breaks" );
			return false;
		}
		$this->client_send( $commandstring . self::CRLF );

		$this->last_reply = $this->get_lines();
		// Fetch SMTP code and possible error code explanation.
		$matches = array();
		if ( preg_match( '/^([0-9]{3})[ -](?:([0-9]\\.[0-9]\\.[0-9]) )?/', $this->last_reply, $matches ) ) {
			$code    = $matches[1];
			$code_ex = ( count( $matches ) > 2 ? $matches[2] : null );
			// Cut off error code from each response line.
			$detail = preg_replace(
				"/{$code}[ -]" .
				( $code_ex ? str_replace( '.', '\\.', $code_ex ) . ' ' : '' ) . '/m',
				'',
				$this->last_reply
			);
		} else {
			// Fall back to simple parsing if regex fails.
			$code    = substr( $this->last_reply, 0, 3 );
			$code_ex = null;
			$detail  = substr( $this->last_reply, 4 );
		}

		$this->edebug( 'SERVER -> CLIENT: ' . $this->last_reply, self::DEBUG_SERVER );

		if ( ! in_array( $code, (array) $expect ) ) { // @codingStandardsIgnoreLine.
			$this->setError(
				"$command command failed",
				$detail,
				$code,
				$code_ex
			);
			$this->edebug(
				'SMTP ERROR: ' . $this->error['error'] . ': ' . $this->last_reply,
				self::DEBUG_CLIENT
			);
			return false;
		}

		$this->setError( '' );
		return true;
	}

	/**
	 * Send an SMTP SAML command.
	 * Starts a mail transaction from the email address specified in $from.
	 * Returns true if successful or false otherwise. If True
	 * the mail transaction is started and then one or more recipient
	 * commands may be called followed by a data command. This command
	 * will send the message to the users terminal if they are logged
	 * in and send them an email.
	 * Implements rfc 821: SAML <SP> FROM:<reverse-path> <CRLF>
	 *
	 * @param string $from The address the message is from .
	 * @access public
	 * @return boolean
	 */
	public function sendAndMail( $from ) { // @codingStandardsIgnoreLine .
		return $this->sendCommand( 'SAML', "SAML FROM:$from", 250 );
	}

	/**
	 * Send an SMTP VRFY command.
	 *
	 * @param string $name The name to verify .
	 * @access public
	 * @return boolean
	 */
	public function verify( $name ) {
		return $this->sendCommand( 'VRFY', "VRFY $name", array( 250, 251 ) );
	}

	/**
	 * Send an SMTP NOOP command.
	 * Used to keep keep-alives alive, doesn't actually do anything
	 *
	 * @access public
	 * @return boolean
	 */
	public function noop() {
		return $this->sendCommand( 'NOOP', 'NOOP', 250 );
	}

	/**
	 * Send an SMTP TURN command.
	 * This is an optional command for SMTP that this class does not support.
	 * This method is here to make the RFC821 Definition complete for this class
	 * and _may_ be implemented in future
	 * Implements from rfc 821: TURN <CRLF>
	 *
	 * @access public
	 * @return boolean
	 */
	public function turn() {
		$this->setError( 'The SMTP TURN command is not implemented' );
		$this->edebug( 'SMTP NOTICE: ' . $this->error['error'], self::DEBUG_CLIENT );
		return false;
	}

	/**
	 * Send raw data to the server.
	 *
	 * @param string $data The data to send .
	 * @access public
	 * @return integer|boolean The number of bytes sent to the server or false on error
	 */
	public function client_send( $data ) {
		$this->edebug( "CLIENT -> SERVER: $data", self::DEBUG_CLIENT );
		set_error_handler( array( $this, 'errorHandler' ) );// @codingStandardsIgnoreLine
		$result = fwrite( $this->smtp_conn, $data );// @codingStandardsIgnoreLine
		restore_error_handler();
		return $result;
	}

	/**
	 * Get the latest error.
	 *
	 * @access public
	 * @return array
	 */
	public function getError() { // @codingStandardsIgnoreLine .
		return $this->error;
	}

	/**
	 * Get SMTP extensions available on the server
	 *
	 * @access public
	 * @return array|null
	 */
	public function getServerExtList() { // @codingStandardsIgnoreLine .
		return $this->server_caps;
	}

	/**
	 * A multipurpose method
	 *
	 * @param string $name Name of SMTP extension or 'HELO'|'EHLO' .
	 * @return mixed
	 */
	public function getServerExt( $name ) { // @codingStandardsIgnoreLine .
		if ( ! $this->server_caps ) {
			$this->setError( 'No HELO/EHLO was sent' );
			return null;
		}

		// the tight logic knot ;).
		if ( ! array_key_exists( $name, $this->server_caps ) ) {
			if ( 'HELO' == $name ) { // WPCS: Loose comparison ok .
				return $this->server_caps['EHLO'];
			}
			if ( 'EHLO' == $name || array_key_exists( 'EHLO', $this->server_caps ) ) { // WPCS: Loose comparison ok .
				return false;
			}
			$this->setError( 'HELO handshake was used. Client knows nothing about server extensions' );
			return null;
		}

		return $this->server_caps[ $name ];
	}

	/**
	 * Get the last reply from the server.
	 *
	 * @access public
	 * @return string
	 */
	public function getLastReply() { // @codingStandardsIgnoreLine .
		return $this->last_reply;
	}

	/**
	 * Read the SMTP server's response.
	 * Either before eof or socket timeout occurs on the operation.
	 * With SMTP we can tell if we have more lines to read if the
	 * 4th character is '-' symbol. If it is a space then we don't
	 * need to read anything else.
	 *
	 * @access protected
	 * @return string
	 */
	protected function get_lines() {
		// If the connection is bad, give up straight away.
		if ( ! is_resource( $this->smtp_conn ) ) {
			return '';
		}
		$data    = '';
		$endtime = 0;
		stream_set_timeout( $this->smtp_conn, $this->Timeout ); // @codingStandardsIgnoreLine .
		if ( $this->Timelimit > 0 ) { // @codingStandardsIgnoreLine .
			$endtime = time() + $this->Timelimit; // @codingStandardsIgnoreLine .
		}
		while ( is_resource( $this->smtp_conn ) && ! feof( $this->smtp_conn ) ) {
			$str = @fgets( $this->smtp_conn, 515 );// @codingStandardsIgnoreLine
			$this->edebug( "SMTP -> get_lines(): \$data is \"$data\"", self::DEBUG_LOWLEVEL );
			$this->edebug( "SMTP -> get_lines(): \$str is  \"$str\"", self::DEBUG_LOWLEVEL );
			$data .= $str;
			// If response is only 3 chars (not valid, but RFC5321 S4.2 says it must be handled),
			// or 4th character is a space, we are done reading, break the loop,
			// string array access is a micro-optimisation over strlen.
			if ( ! isset( $str[3] ) || ( isset( $str[3] ) && ' ' == $str[3] ) ) { // WPCS: Loose comparison ok .
				break;
			}
			// Timed-out? Log and break.
			$info = stream_get_meta_data( $this->smtp_conn );
			if ( $info['timed_out'] ) {
				$this->edebug(
					'SMTP -> get_lines(): timed-out (' . $this->Timeout . ' sec)', // @codingStandardsIgnoreLine .
					self::DEBUG_LOWLEVEL
				);
				break;
			}
			// Now check if reads took too long.
			if ( $endtime && time() > $endtime ) {
				$this->edebug(
					'SMTP -> get_lines(): timelimit reached (' .
					$this->Timelimit . ' sec)', // @codingStandardsIgnoreLine .
					self::DEBUG_LOWLEVEL
				);
				break;
			}
		}
		return $data;
	}

	/**
	 * Enable or disable VERP address generation.
	 *
	 * @param boolean $enabled .
	 */
	public function setVerp( $enabled = false ) { // @codingStandardsIgnoreLine .
		$this->do_verp = $enabled;
	}

	/**
	 * Get VERP address generation mode.
	 *
	 * @return boolean
	 */
	public function getVerp() { // @codingStandardsIgnoreLine .
		return $this->do_verp;
	}

	/**
	 * Set error messages and codes.
	 *
	 * @param string $message The error message .
	 * @param string $detail Further detail on the error .
	 * @param string $smtp_code An associated SMTP error code .
	 * @param string $smtp_code_ex Extended SMTP code .
	 */
	protected function setError( $message, $detail = '', $smtp_code = '', $smtp_code_ex = '' ) { // @codingStandardsIgnoreLine .
		$this->error = array(
			'error'        => $message,
			'detail'       => $detail,
			'smtp_code'    => $smtp_code,
			'smtp_code_ex' => $smtp_code_ex,
		);
	}

	/**
	 * Set debug output method.
	 *
	 * @param string|callable $method The name of the mechanism to use for debugging output, or a callable to handle it.
	 */
	public function setDebugOutput( $method = 'echo' ) { // @codingStandardsIgnoreLine .
		$this->Debugoutput = $method; // @codingStandardsIgnoreLine .
	}

	/**
	 * Get debug output method.
	 *
	 * @return string
	 */
	public function getDebugOutput() { // @codingStandardsIgnoreLine .
		return $this->Debugoutput; // @codingStandardsIgnoreLine .
	}

	/**
	 * Set debug output level.
	 *
	 * @param integer $level .
	 */
	public function setDebugLevel( $level = 0 ) { // @codingStandardsIgnoreLine .
		$this->do_debug = $level;
	}

	/**
	 * Get debug output level.
	 *
	 * @return integer
	 */
	public function getDebugLevel() { // @codingStandardsIgnoreLine .
		return $this->do_debug;
	}

	/**
	 * Set SMTP timeout.
	 *
	 * @param integer $timeout .
	 */
	public function setTimeout( $timeout = 0 ) { // @codingStandardsIgnoreLine .
		$this->Timeout = $timeout; // @codingStandardsIgnoreLine .
	}

	/**
	 * Get SMTP timeout.
	 *
	 * @return integer
	 */
	public function getTimeout() { // @codingStandardsIgnoreLine .
		return $this->Timeout; // @codingStandardsIgnoreLine .
	}

	/**
	 * Reports an error number and string.
	 *
	 * @param integer $errno The error number returned by PHP.
	 * @param string  $errmsg The error message returned by PHP.
	 * @param string  $errfile The file the error occurred in .
	 * @param integer $errline The line number the error occurred on .
	 */
	protected function errorHandler( $errno, $errmsg, $errfile = '', $errline = 0 ) { // @codingStandardsIgnoreLine .
		$notice = 'Connection failed.';
		$this->setError(
			$notice,
			$errno,
			$errmsg
		);
		$this->edebug(
			$notice . ' Error #' . $errno . ': ' . $errmsg . " [$errfile line $errline]",
			self::DEBUG_CONNECTION
		);
	}

	/**
	 * Extract and return the ID of the last SMTP transaction based on
	 * a list of patterns provided in SMTP::$smtp_transaction_id_patterns.
	 * Relies on the host providing the ID in response to a DATA command.
	 * If no reply has been received yet, it will return null.
	 * If no pattern was matched, it will return false.
	 *
	 * @return bool|null|string
	 */
	protected function recordLastTransactionID() { // @codingStandardsIgnoreLine .
		$reply = $this->getLastReply();

		if ( empty( $reply ) ) {
			$this->last_smtp_transaction_id = null;
		} else {
			$this->last_smtp_transaction_id = false;
			foreach ( $this->smtp_transaction_id_patterns as $smtp_transaction_id_pattern ) {
				if ( preg_match( $smtp_transaction_id_pattern, $reply, $matches ) ) {
					$this->last_smtp_transaction_id = $matches[1];
				}
			}
		}

		return $this->last_smtp_transaction_id;
	}

	/**
	 * Get the queue/transaction ID of the last SMTP transaction
	 * If no reply has been received yet, it will return null.
	 * If no pattern was matched, it will return false.
	 *
	 * @return bool|null|string
	 * @see recordLastTransactionID()
	 */
	public function getLastTransactionID() { // @codingStandardsIgnoreLine .
		return $this->last_smtp_transaction_id;
	}
}
