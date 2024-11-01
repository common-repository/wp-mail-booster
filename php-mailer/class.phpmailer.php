<?php  // @codingStandardsIgnoreLine.
/**
 * This file used to send emails.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/php-mailer
 * @version 2.0.0
 */

/**
 * This class is used to send email using phpmiler .
 */
class PHPMailer {

	/**
	 * The PHPMailer Version number.
	 *
	 * @var string
	 */
	public $version = '5.2.26';

	/**
	 * Email priority.
	 * Options: null (default), 1 = High, 3 = Normal, 5 = low.
	 * When null, the header is not set at all.
	 *
	 * @var integer
	 */
	public $priority = null;

	/**
	 * The character set of the message.
	 *
	 * @var string
	 */
	public $charset = 'iso-8859-1';

	/**
	 * The MIME Content-type of the message.
	 *
	 * @var string
	 */
	public $contenttype = 'text/plain';

	/**
	 * The message encoding.
	 * Options: "8bit", "7bit", "binary", "base64", and "quoted-printable".
	 *
	 * @var string
	 */
	public $mb_encoding = '8bit';

	/**
	 * Holds the most recent mailer error message.
	 *
	 * @var string
	 */
	public $errorinfo = '';

	/**
	 * The From email address for the message.
	 *
	 * @var string
	 */
	public $from = 'root@localhost';

	/**
	 * The From name of the message.
	 *
	 * @var string
	 */
	public $fromname = 'Root User';

	/**
	 * The Sender email (Return-Path) of the message.
	 * If not empty, will be sent via -f to sendmail or as 'MAIL FROM' in smtp mode.
	 *
	 * @var string
	 */
	public $sender = '';

	/**
	 * The Return-Path of the message.
	 * If empty, it will be set to either From or Sender.
	 *
	 * @var string
	 * @deprecated Email senders should never set a return-path header;
	 * it's the receiver's job (RFC5321 section 4.4), so this no longer does anything.
	 */
	public $returnpath = '';

	/**
	 * The Subject of the message.
	 *
	 * @var string
	 */
	public $subject = '';

	/**
	 * An HTML or plain text message body.
	 * If HTML then call isHTML(true).
	 *
	 * @var string
	 */
	public $body = '';

	/**
	 * The plain-text message body.
	 * This body can be read by mail clients that do not have HTML email
	 * capability such as mutt & Eudora.
	 * Clients that can read HTML will view the normal Body.
	 *
	 * @var string
	 */
	public $altbody = '';

	/**
	 * An iCal message part body.
	 * Only supported in simple alt or alt_inline message types
	 * To generate iCal events, use the bundled extras/EasyPeasyICS.php class or iCalcreator
	 *
	 * @var string
	 */
	public $ical = '';

	/**
	 * The complete compiled MIME message body.
	 *
	 * @access protected
	 * @var string
	 */
	protected $mimebody = '';

	/**
	 * The complete compiled MIME message headers.
	 *
	 * @var string
	 * @access protected
	 */
	protected $mimeheader = '';

	/**
	 * Extra headers that createHeader() doesn't fold in.
	 *
	 * @var string
	 * @access protected
	 */
	protected $mailheader = '';

	/**
	 * Word-wrap the message body to this number of chars.
	 * Set to 0 to not wrap. A useful value here is 78, for RFC2822 section 2.1.1 compliance.
	 *
	 * @var integer
	 */
	public $wordwrap = 0;

	/**
	 * Which method to use to send mail.
	 * Options: "mail", "sendmail", or "smtp".
	 *
	 * @var string
	 */
	public $mailer = 'mail';

	/**
	 * The path to the sendmail program.
	 *
	 * @var string
	 */
	public $Sendmail = '/usr/sbin/sendmail'; // @codingStandardsIgnoreLine.

	/**
	 * Whether mail() uses a fully sendmail-compatible MTA.
	 * One which supports sendmail's "-oi -f" options.
	 *
	 * @var boolean
	 */
	public $UseSendmailOptions = true; // @codingStandardsIgnoreLine.

	/**
	 * Path to PHPMailer plugins.
	 * Useful if the SMTP class is not in the PHP include path.
	 *
	 * @var string
	 * @deprecated Should not be needed now there is an autoloader.
	 */
	public $PluginDir = ''; // @codingStandardsIgnoreLine.

	/**
	 * The email address that a reading confirmation should be sent to, also known as read receipt.
	 *
	 * @var string
	 */
	public $ConfirmReadingTo = ''; // @codingStandardsIgnoreLine.

	/**
	 * The hostname to use in the Message-ID header and as default HELO string.
	 * If empty, PHPMailer attempts to find one with, in order,
	 * $_SERVER['SERVER_NAME'], gethostname(), php_uname('n'), or the value
	 * 'localhost.localdomain'.
	 *
	 * @var string
	 */
	public $Hostname = ''; // @codingStandardsIgnoreLine.

	/**
	 * An ID to be used in the Message-ID header.
	 * If empty, a unique id will be generated.
	 * as defined in RFC5322 section 3.6.4 or it will be ignored.
	 *
	 * @var string
	 */
	public $MessageID = ''; // @codingStandardsIgnoreLine.

	/**
	 * The message Date to be used in the Date header.
	 * If empty, the current date will be added.
	 *
	 * @var string
	 */
	public $MessageDate = ''; // @codingStandardsIgnoreLine.

	/**
	 * SMTP hosts.
	 * Either a single hostname or multiple semicolon-delimited hostnames.
	 * You can also specify a different port
	 * for each host by using this format: [hostname:port]
	 * You can also specify encryption type, for example:
	 * Hosts will be tried in order.
	 *
	 * @var string
	 */
	public $Host = 'localhost'; // @codingStandardsIgnoreLine.

	/**
	 * The default SMTP server port.
	 *
	 * @var integer
	 * @TODO Why is this needed when the SMTP class takes care of it?
	 */
	public $Port = 25; // @codingStandardsIgnoreLine.

	/**
	 * The SMTP HELO of the message.
	 * Default is $Hostname. If $Hostname is empty, PHPMailer attempts to find
	 * one with the same method described above for $Hostname.
	 *
	 * @var string
	 * @see PHPMailer::$Hostname
	 */
	public $Helo = ''; // @codingStandardsIgnoreLine.

	/**
	 * What kind of encryption to use on the SMTP connection.
	 * Options: '', 'ssl' or 'tls'
	 *
	 * @var string
	 */
	public $SMTPSecure = ''; // @codingStandardsIgnoreLine.

	/**
	 * Whether to enable TLS encryption automatically if a server supports it,
	 * even if `SMTPSecure` is not set to 'tls'.
	 * Be aware that in PHP >= 5.6 this requires that the server's certificates are valid.
	 *
	 * @var boolean
	 */
	public $SMTPAutoTLS = true; // @codingStandardsIgnoreLine.

	/**
	 * Whether to use SMTP authentication.
	 * Uses the Username and Password properties.
	 *
	 * @var boolean
	 * @see PHPMailer::$Username
	 * @see PHPMailer::$Password
	 */
	public $SMTPAuth = false; // @codingStandardsIgnoreLine.

	/**
	 * Options array passed to stream_context_create when connecting via SMTP.
	 *
	 * @var array
	 */
	public $SMTPOptions = array(); // @codingStandardsIgnoreLine.

	/**
	 * SMTP username.
	 *
	 * @var string
	 */
	public $Username = ''; // @codingStandardsIgnoreLine.

	/**
	 * SMTP password.
	 *
	 * @var string
	 */
	public $Password = ''; // @codingStandardsIgnoreLine.

	/**
	 * SMTP auth type.
	 * Options are CRAM-MD5, LOGIN, PLAIN, NTLM, XOAUTH2, attempted in that order if not specified
	 *
	 * @var string
	 */
	public $AuthType = ''; // @codingStandardsIgnoreLine.

	/**
	 * SMTP realm.
	 * Used for NTLM auth
	 *
	 * @var string
	 */
	public $Realm = ''; // @codingStandardsIgnoreLine.

	/**
	 * SMTP workstation.
	 * Used for NTLM auth
	 *
	 * @var string
	 */
	public $Workstation = ''; // @codingStandardsIgnoreLine.

	/**
	 * The SMTP server timeout in seconds.
	 * Default of 5 minutes (300sec) is from RFC2821 section 4.5.3.2
	 *
	 * @var integer
	 */
	public $Timeout = 300; // @codingStandardsIgnoreLine.

	/**
	 * SMTP class debug output mode.
	 * Debug output level.
	 * Options:
	 *
	 * @var integer
	 * @see SMTP::$do_debug
	 */
	public $SMTPDebug = 0; // @codingStandardsIgnoreLine.

	/**
	 * How to handle debug output.
	 *
	 * @var string|callable
	 * @see SMTP::$Debugoutput
	 */
	public $Debugoutput = 'echo'; // @codingStandardsIgnoreLine.

	/**
	 * Whether to keep SMTP connection open after each message.
	 * If this is set to true then to close the connection
	 * requires an explicit call to smtpClose().
	 *
	 * @var boolean
	 */
	public $SMTPKeepAlive = false; // @codingStandardsIgnoreLine.

	/**
	 * Whether to split multiple to addresses into multiple messages
	 * or send them all in one message.
	 * Only supported in `mail` and `sendmail` transports, not in SMTP.
	 *
	 * @var boolean
	 */
	public $SingleTo = false; // @codingStandardsIgnoreLine.

	/**
	 * Storage for addresses when SingleTo is enabled.
	 *
	 * @var array
	 * @TODO This should really not be public
	 */
	public $SingleToArray = array(); // @codingStandardsIgnoreLine.

	/**
	 * Whether to generate VERP addresses on send.
	 * Only applicable when sending via SMTP.
	 *
	 * @var boolean
	 */
	public $do_verp = false;

	/**
	 * Whether to allow sending messages with an empty body.
	 *
	 * @var boolean
	 */
	public $AllowEmpty = false; // @codingStandardsIgnoreLine.

	/**
	 * The default line ending.
	 *
	 * @note The default remains "\n". We force CRLF where we know
	 *        it must be used via self::CRLF.
	 * @var string
	 */
	public $LE = "\n"; // @codingStandardsIgnoreLine.

	/**
	 * DKIM selector.
	 *
	 * @var string
	 */
	public $DKIM_selector = ''; // @codingStandardsIgnoreLine.

	/**
	 * DKIM Identity.
	 * Usually the email address used as the source of the email.
	 *
	 * @var string
	 */
	public $DKIM_identity = ''; // @codingStandardsIgnoreLine.

	/**
	 * DKIM passphrase.
	 * Used if your key is encrypted.
	 *
	 * @var string
	 */
	public $DKIM_passphrase = ''; // @codingStandardsIgnoreLine.

	/**
	 * DKIM signing domain name.
	 *
	 * @example 'example.com'
	 * @var string
	 */
	public $DKIM_domain = ''; // @codingStandardsIgnoreLine.

	/**
	 * DKIM private key file path.
	 *
	 * @var string
	 */
	public $DKIM_private = ''; // @codingStandardsIgnoreLine.

	/**
	 * DKIM private key string.
	 * If set, takes precedence over `$DKIM_private`.
	 *
	 * @var string
	 */
	public $DKIM_private_string = ''; // @codingStandardsIgnoreLine.

	/**
	 * Callback Action function name.
	 *
	 * The function that handles the result of the send email action.
	 * It is called out by send() for each email sent.
	 *
	 *
	 * Parameters:
	 *   boolean $result        result of the send action
	 *   array   $to            email addresses of the recipients
	 *   array   $cc            cc email addresses
	 *   array   $bcc           bcc email addresses
	 *   string  $subject       the subject
	 *   string  $body          the email body
	 *   string  $from          email address of sender
	 *
	 * @var string
	 */
	public $action_function = '';

	/**
	 * What to put in the X-Mailer header.
	 * Options: An empty string for PHPMailer default, whitespace for none, or a string to use
	 *
	 * @var string
	 */
	public $XMailer = ''; // @codingStandardsIgnoreLine.

	/**
	 * Which validator to use by default when validating email addresses.
	 * May be a callable to inject your own validator, but there are several built-in validators.
	 *
	 * @see PHPMailer::validateAddress()
	 * @var string|callable
	 * @static
	 */
	public static $validator = 'auto';

	/**
	 * An instance of the SMTP sender class.
	 *
	 * @var SMTP
	 * @access protected
	 */
	protected $smtp = null;

	/**
	 * The array of 'to' names and addresses.
	 *
	 * @var array
	 * @access protected
	 */
	protected $to = array();

	/**
	 * The array of 'cc' names and addresses.
	 *
	 * @var array
	 * @access protected
	 */
	protected $cc = array();

	/**
	 * The array of 'bcc' names and addresses.
	 *
	 * @var array
	 * @access protected
	 */
	protected $bcc = array();

	/**
	 * The array of reply-to names and addresses.
	 *
	 * @var array
	 * @access protected
	 */
	protected $ReplyTo = array(); // @codingStandardsIgnoreLine.

	/**
	 * An array of all kinds of addresses.
	 * Includes all of $to, $cc, $bcc
	 *
	 * @var array
	 * @access protected
	 * @see PHPMailer::$to @see PHPMailer::$cc @see PHPMailer::$bcc
	 */
	protected $all_recipients = array();

	/**
	 * An array of names and addresses queued for validation.
	 * In send(), valid and non duplicate entries are moved to $all_recipients
	 * and one of $to, $cc, or $bcc.
	 * This array is used only for addresses with IDN.
	 *
	 * @var array
	 * @access protected
	 * @see PHPMailer::$to @see PHPMailer::$cc @see PHPMailer::$bcc
	 * @see PHPMailer::$all_recipients
	 */
	protected $RecipientsQueue = array(); // @codingStandardsIgnoreLine.

	/**
	 * An array of reply-to names and addresses queued for validation.
	 * In send(), valid and non duplicate entries are moved to $ReplyTo.
	 * This array is used only for addresses with IDN.
	 *
	 * @var array
	 * @access protected
	 * @see PHPMailer::$ReplyTo
	 */
	protected $ReplyToQueue = array(); // @codingStandardsIgnoreLine.

	/**
	 * The array of attachments.
	 *
	 * @var array
	 * @access protected
	 */
	protected $attachment = array();

	/**
	 * The array of custom headers.
	 *
	 * @var array
	 * @access protected
	 */
	protected $CustomHeader = array(); // @codingStandardsIgnoreLine.

	/**
	 * The most recent Message-ID (including angular brackets).
	 *
	 * @var string
	 * @access protected
	 */
	protected $lastMessageID = ''; // @codingStandardsIgnoreLine.

	/**
	 * The message's MIME type.
	 *
	 * @var string
	 * @access protected
	 */
	protected $message_type = '';

	/**
	 * The array of MIME boundary strings.
	 *
	 * @var array
	 * @access protected
	 */
	protected $boundary = array();

	/**
	 * The array of available languages.
	 *
	 * @var array
	 * @access protected
	 */
	protected $language = array();

	/**
	 * The number of errors encountered.
	 *
	 * @var integer
	 * @access protected
	 */
	protected $error_count = 0;

	/**
	 * The S/MIME certificate file path.
	 *
	 * @var string
	 * @access protected
	 */
	protected $sign_cert_file = '';

	/**
	 * The S/MIME key file path.
	 *
	 * @var string
	 * @access protected
	 */
	protected $sign_key_file = '';

	/**
	 * The optional S/MIME extra certificates ("CA Chain") file path.
	 *
	 * @var string
	 * @access protected
	 */
	protected $sign_extracerts_file = '';

	/**
	 * The S/MIME password for the key.
	 * Used only if the key is encrypted.
	 *
	 * @var string
	 * @access protected
	 */
	protected $sign_key_pass = '';

	/**
	 * Whether to throw exceptions for errors.
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $exceptions = false;

	/**
	 * Unique ID used for message ID and boundaries.
	 *
	 * @var string
	 * @access protected
	 */
	protected $uniqueid = '';

	/**
	 * Error severity: message only, continue processing.
	 */
	const STOP_MESSAGE = 0;

	/**
	 * Error severity: message, likely ok to continue processing.
	 */
	const STOP_CONTINUE = 1;

	/**
	 * Error severity: message, plus full stop, critical error reached.
	 */
	const STOP_CRITICAL = 2;

	/**
	 * SMTP RFC standard line ending.
	 */
	const CRLF = "\r\n";

	/**
	 * The maximum line length allowed by RFC 2822 section 2.1.1
	 *
	 * @var integer
	 */
	const MAX_LINE_LENGTH = 998;

	/**
	 * Constructor.
	 *
	 * @param boolean $exceptions Should we throw external exceptions .
	 */
	public function __construct( $exceptions = null ) {
		if ( null !== $exceptions ) {
			$this->exceptions = (boolean) $exceptions;
		}
		// Pick an appropriate debug output format automatically.
		$this->Debugoutput = ( strpos( PHP_SAPI, 'cli' ) !== false ? 'echo' : 'html' ); // @codingStandardsIgnoreLine.
	}

	/**
	 * Destructor.
	 */
	public function __destruct() {
		// Close any open SMTP connection nicely.
		$this->smtpClose();
	}

	/**
	 * Call mail() in a safe_mode-aware fashion.
	 * Also, unless sendmail_path points to sendmail (or something that
	 * claims to be sendmail), don't pass params (not a perfect fix,
	 * but it will do)
	 *
	 * @param string $to To .
	 * @param string $subject Subject .
	 * @param string $body Message Body .
	 * @param string $header Additional Header(s) .
	 * @param string $params Params .
	 * @access private
	 * @return boolean
	 */
	private function mailPassthru( $to, $subject, $body, $header, $params ) { // @codingStandardsIgnoreLine.
		// Check overloading of mail function to avoid double-encoding.
		if ( ini_get( 'mbstring.func_overload' ) & 1 ) {
			$subject = $this->secureHeader( $subject );
		} else {
			$subject = $this->encodeHeader( $this->secureHeader( $subject ) );
		}

		// Can't use additional_parameters in safe_mode, calling mail() with null params breaks.
		if ( ini_get( 'safe_mode' ) || ! $this->UseSendmailOptions || is_null( $params ) ) { // @codingStandardsIgnoreLine.
			$result = @mail( $to, $subject, $body, $header ); // @codingStandardsIgnoreLine.
		} else {
			$result = @mail( $to, $subject, $body, $header, $params ); // @codingStandardsIgnoreLine.
		}
		return $result;
	}
	/**
	 * Output debugging info via user-defined method.
	 * Only generates output if SMTP debug output is enabled (@see SMTP::$do_debug).
	 *
	 * @see PHPMailer::$Debugoutput
	 * @see PHPMailer::$SMTPDebug
	 * @param string $str .
	 */
	protected function edebug( $str ) {
		if ( $this->SMTPDebug <= 0 ) { // @codingStandardsIgnoreLine.
			return;
		}
		// Avoid clash with built-in function names.
		if ( ! in_array( $this->Debugoutput, array( 'error_log', 'html', 'echo' ) ) && is_callable( $this->Debugoutput ) ) {  // @codingStandardsIgnoreLine.
			call_user_func( $this->Debugoutput, $str, $this->SMTPDebug ); // @codingStandardsIgnoreLine.
			return;
		}
		switch ( $this->Debugoutput ) { // @codingStandardsIgnoreLine.
			case 'error_log':
				// Don't output, just log.
				error_log( $str ); // @codingStandardsIgnoreLine.
				break;
			case 'html':
				// Cleans up output a bit for a better looking, HTML-safe output.
				echo htmlentities( // WPCS:XSS ok .
					preg_replace( '/[\r\n]+/', '', $str ),
					ENT_QUOTES,
					'UTF-8'
				)
				. "<br>\n";
				break;
			case 'echo':
			default:
				// Normalize line breaks.
				$str = preg_replace( '/\r\n?/ms', "\n", $str );
				echo gmdate( 'Y-m-d H:i:s' ) . "\t" . str_replace( // @codingStandardsIgnoreLine.
					"\n",
					"\n                   \t                  ",
					trim( $str )
				) . "\n";
		}
	}

	/**
	 * Sets message type to HTML or plain.
	 *
	 * @param boolean $isHtml True for HTML mode.
	 * @return void
	 */
	public function isHTML( $isHtml = true ) { // @codingStandardsIgnoreLine.
		if ( $isHtml ) { // @codingStandardsIgnoreLine.
			$this->contenttype = 'text/html';
		} else {
			$this->contenttype = 'text/plain';
		}
	}

	/**
	 * Send messages using SMTP.
	 *
	 * @return void
	 */
	public function isSMTP() { // @codingStandardsIgnoreLine.
		$this->mailer = 'smtp';
	}

	/**
	 * Send messages using PHP's mail() function.
	 *
	 * @return void
	 */
	public function isMail() { // @codingStandardsIgnoreLine.
		$this->mailer = 'mail';
	}

	/**
	 * Send messages using $Sendmail.
	 *
	 * @return void
	 */
	public function isSendmail() { // @codingStandardsIgnoreLine.
		$ini_sendmail_path = ini_get( 'sendmail_path' );

		if ( ! stristr( $ini_sendmail_path, 'sendmail' ) ) {
			$this->Sendmail = '/usr/sbin/sendmail'; // @codingStandardsIgnoreLine.
		} else {
			$this->Sendmail = $ini_sendmail_path; // @codingStandardsIgnoreLine.
		}
		$this->mailer = 'sendmail';
	}

	/**
	 * Send messages using qmail.
	 *
	 * @return void
	 */
	public function isQmail() { // @codingStandardsIgnoreLine.
		$ini_sendmail_path = ini_get( 'sendmail_path' );

		if ( ! stristr( $ini_sendmail_path, 'qmail' ) ) {
			$this->Sendmail = '/var/qmail/bin/qmail-inject'; // @codingStandardsIgnoreLine.
		} else {
			$this->Sendmail = $ini_sendmail_path; // @codingStandardsIgnoreLine.
		}
		$this->mailer = 'qmail';
	}

	/**
	 * Add a "To" address.
	 *
	 * @param string $address The email address to send to .
	 * @param string $name .
	 * @return boolean true on success, false if address already used or invalid in some way
	 */
	public function addAddress( $address, $name = '' ) { // @codingStandardsIgnoreLine.
		return $this->addOrEnqueueAnAddress( 'to', $address, $name );
	}

	/**
	 * Add a "CC" address.
	 *
	 * @param string $address The email address to send to .
	 * @param string $name .
	 * @return boolean true on success, false if address already used or invalid in some way
	 */
	public function addCC( $address, $name = '' ) { // @codingStandardsIgnoreLine.
		return $this->addOrEnqueueAnAddress( 'cc', $address, $name );
	}

	/**
	 * Add a "BCC" address.
	 *
	 * @param string $address The email address to send to .
	 * @param string $name .
	 * @return boolean true on success, false if address already used or invalid in some way
	 */
	public function addBCC( $address, $name = '' ) { // @codingStandardsIgnoreLine.
		return $this->addOrEnqueueAnAddress( 'bcc', $address, $name );
	}

	/**
	 * Add a "Reply-To" address.
	 *
	 * @param string $address The email address to reply to .
	 * @param string $name .
	 * @return boolean true on success, false if address already used or invalid in some way
	 */
	public function addReplyTo( $address, $name = '' ) { // @codingStandardsIgnoreLine.
		return $this->addOrEnqueueAnAddress( 'Reply-To', $address, $name );
	}

	/**
	 * Add an address to one of the recipient arrays or to the ReplyTo array. Because PHPMailer
	 * can't validate addresses with an IDN without knowing the PHPMailer::$charset (that can still
	 * be modified after calling this function), addition of such addresses is delayed until send().
	 * Addresses that have been added already return false, but do not throw exceptions.
	 *
	 * @param string $kind One of 'to', 'cc', 'bcc', or 'ReplyTo' .
	 * @param string $address The email address to send, resp. to reply to .
	 * @param string $name .
	 * @throws phpmailerException .
	 * @return boolean true on success, false if address already used or invalid in some way
	 * @access protected
	 */
	protected function addOrEnqueueAnAddress( $kind, $address, $name ) { // @codingStandardsIgnoreLine.
		$address = trim( $address );
		$name    = trim( preg_replace( '/[\r\n]+/', '', $name ) );
		if ( ( $pos = strrpos( $address, '@' ) ) === false ) { // @codingStandardsIgnoreLine.
			// At-sign is misssing.
			$error_message = $this->lang( 'invalid_address' ) . " (addAnAddress $kind): $address";
			$this->setError( $error_message );
			$this->edebug( $error_message );
			if ( $this->exceptions ) {
				throw new phpmailerException( $error_message );
			}
			return false;
		}
		$params = array( $kind, $address, $name );
		// Enqueue addresses with IDN until we know the PHPMailer::$charset.
		if ( $this->has8bitChars( substr( $address, ++$pos ) ) && $this->idnSupported() ) {
			if ( 'Reply-To' !== $kind ) {
				if ( ! array_key_exists( $address, $this->RecipientsQueue ) ) { // @codingStandardsIgnoreLine.
					$this->RecipientsQueue[ $address ] = $params; // @codingStandardsIgnoreLine.
					return true;
				}
			} else {
				if ( ! array_key_exists( $address, $this->ReplyToQueue ) ) { // @codingStandardsIgnoreLine.
					$this->ReplyToQueue[ $address ] = $params; // @codingStandardsIgnoreLine.
					return true;
				}
			}
			return false;
		}
		// Immediately add standard addresses without IDN.
		return call_user_func_array( array( $this, 'addAnAddress' ), $params );
	}

	/**
	 * Add an address to one of the recipient arrays or to the ReplyTo array.
	 * Addresses that have been added already return false, but do not throw exceptions.
	 *
	 * @param string $kind One of 'to', 'cc', 'bcc', or 'ReplyTo' .
	 * @param string $address The email address to send, resp. to reply to .
	 * @param string $name .
	 * @throws phpmailerException .
	 * @return boolean true on success, false if address already used or invalid in some way
	 * @access protected
	 */
	protected function addAnAddress( $kind, $address, $name = '' ) { // @codingStandardsIgnoreLine.
		if ( ! in_array( $kind, array( 'to', 'cc', 'bcc', 'Reply-To' ) ) ) {  // @codingStandardsIgnoreLine.
			$error_message = $this->lang( 'Invalid recipient kind: ' ) . $kind;
			$this->setError( $error_message );
			$this->edebug( $error_message );
			if ( $this->exceptions ) {
				throw new phpmailerException( $error_message );
			}
			return false;
		}
		if ( ! $this->validateAddress( $address ) ) {
			$error_message = $this->lang( 'invalid_address' ) . " (addAnAddress $kind): $address";
			$this->setError( $error_message );
			$this->edebug( $error_message );
			if ( $this->exceptions ) {
				throw new phpmailerException( $error_message );
			}
			return false;
		}
		if ( 'Reply-To' !== $kind ) {
			if ( ! array_key_exists( strtolower( $address ), $this->all_recipients ) ) {
				array_push( $this->$kind, array( $address, $name ) );
				$this->all_recipients[ strtolower( $address ) ] = true;
				return true;
			}
		} else {
			if ( ! array_key_exists( strtolower( $address ), $this->ReplyTo ) ) { // @codingStandardsIgnoreLine.
				$this->ReplyTo[ strtolower( $address ) ] = array( $address, $name ); // @codingStandardsIgnoreLine.
				return true;
			}
		}
		return false;
	}

	/**
	 * Parse and validate a string containing one or more RFC822-style comma-separated email addresses
	 * of the form "display name <address>" into an array of name/address pairs.
	 * Uses the imap_rfc822_parse_adrlist function if the IMAP extension is available.
	 * Note that quotes in the name part are removed.
	 *
	 * @param string $addrstr The address list string .
	 * @param bool   $useimap Whether to use the IMAP extension to parse the list .
	 * @return array
	 */
	public function parseAddresses( $addrstr, $useimap = true ) { // @codingStandardsIgnoreLine.
		$addresses = array();
		if ( $useimap && function_exists( 'imap_rfc822_parse_adrlist' ) ) {
			// Use this built-in parser if it's available .
			$list = imap_rfc822_parse_adrlist( $addrstr, '' );
			foreach ( $list as $address ) {
				if ( '.SYNTAX-ERROR.' !== $address->host ) {
					if ( $this->validateAddress( $address->mailbox . '@' . $address->host ) ) {
						$addresses[] = array(
							'name'    => ( property_exists( $address, 'personal' ) ? $address->personal : '' ),
							'address' => $address->mailbox . '@' . $address->host,
						);
					}
				}
			}
		} else {
			// Use this simpler parser .
			$list = explode( ',', $addrstr );
			foreach ( $list as $address ) {
				$address = trim( $address );
				// Is there a separate name part?
				if ( strpos( $address, '<' ) === false ) {
					// No separate name, just use the whole thing.
					if ( $this->validateAddress( $address ) ) {
						$addresses[] = array(
							'name'    => '',
							'address' => $address,
						);
					}
				} else {
					list($name, $email) = explode( '<', $address );
					$email              = trim( str_replace( '>', '', $email ) );
					if ( $this->validateAddress( $email ) ) {
						$addresses[] = array(
							'name'    => trim( str_replace( array( '"', "'" ), '', $name ) ),
							'address' => $email,
						);
					}
				}
			}
		}
		return $addresses;
	}

	/**
	 * Set the From and fromname properties.
	 *
	 * @param string  $address .
	 * @param string  $name .
	 * @param boolean $auto Whether to also set the Sender address, defaults to true .
	 * @throws phpmailerException .
	 * @return boolean
	 */
	public function setFrom( $address, $name = '', $auto = true ) { // @codingStandardsIgnoreLine.
		$address = trim( $address );
		$name    = trim( preg_replace( '/[\r\n]+/', '', $name ) ); // Strip breaks and trim
		// Don't validate now addresses with IDN. Will be done in send().
		if ( ( $pos = strrpos( $address, '@' ) ) === false || // @codingStandardsIgnoreLine.
			( ! $this->has8bitChars( substr( $address, ++$pos ) ) || ! $this->idnSupported() ) &&
			! $this->validateAddress( $address ) ) {
			$error_message = $this->lang( 'invalid_address' ) . " (setFrom) $address";
			$this->setError( $error_message );
			$this->edebug( $error_message );
			if ( $this->exceptions ) {
				throw new phpmailerException( $error_message );
			}
			return false;
		}
		$this->From     = $address; // @codingStandardsIgnoreLine.
		$this->fromname = $name;
		if ( $auto ) {
			if ( empty( $this->sender ) ) {
				$this->sender = $address;
			}
		}
		return true;
	}

	/**
	 * Return the Message-ID header of the last email.
	 * Technically this is the value from the last time the headers were created,
	 * but it's also the message ID of the last sent message except in
	 * pathological cases.
	 *
	 * @return string
	 */
	public function getLastMessageID() { // @codingStandardsIgnoreLine.
		return $this->lastMessageID; // @codingStandardsIgnoreLine.
	}

	/**
	 * Check that a string looks like an email address.
	 *
	 * @param string          $address The email address to check .
	 * @param string|callable $patternselect .
	 * @return boolean
	 * @static
	 * @access public
	 */
	public static function validateAddress( $address, $patternselect = null ) { // @codingStandardsIgnoreLine.
		if ( is_null( $patternselect ) ) {
			$patternselect = self::$validator;
		}
		if ( is_callable( $patternselect ) ) {
			return call_user_func( $patternselect, $address );
		}
		// Reject line breaks in addresses; it's valid RFC5322, but not RFC5321 .
		if ( strpos( $address, "\n" ) !== false || strpos( $address, "\r" ) !== false ) {
			return false;
		}
		if ( ! $patternselect || 'auto' === $patternselect ) {
			// Check this constant first so it works when extension_loaded() is disabled by safe mode
			// Constant was added in PHP 5.2.4 .
			if ( defined( 'PCRE_VERSION' ) ) {
				// This pattern can get stuck in a recursive loop in PCRE <= 8.0.2.
				if ( version_compare( PCRE_VERSION, '8.0.3' ) >= 0 ) {
					$patternselect = 'pcre8';
				} else {
					$patternselect = 'pcre';
				}
			} elseif ( function_exists( 'extension_loaded' ) && extension_loaded( 'pcre' ) ) {
				// Fall back to older PCRE.
				$patternselect = 'pcre';
			} else {
				// Filter_var appeared in PHP 5.2.0 and does not require the PCRE extension .
				if ( version_compare( PHP_VERSION, '5.2.0' ) >= 0 ) {
					$patternselect = 'php';
				} else {
					$patternselect = 'noregex';
				}
			}
		}
		switch ( $patternselect ) {
			case 'pcre8':
				/**
				 * Uses the same RFC5322 regex on which FILTER_VALIDATE_EMAIL is based, but allows dotless domains.
				 *
				 * Feel free to use and redistribute this code. But please keep this copyright notice.
				 */
				return (boolean) preg_match(
					'/^(?!(?>(?1)"?(?>\\\[ -~]|[^"])"?(?1)){255,})(?!(?>(?1)"?(?>\\\[ -~]|[^"])"?(?1)){65,}@)' .
					'((?>(?>(?>((?>(?>(?>\x0D\x0A)?[\t ])+|(?>[\t ]*\x0D\x0A)?[\t ]+)?)(\((?>(?2)' .
					'(?>[\x01-\x08\x0B\x0C\x0E-\'*-\[\]-\x7F]|\\\[\x00-\x7F]|(?3)))*(?2)\)))+(?2))|(?2))?)' .
					'([!#-\'*+\/-9=?^-~-]+|"(?>(?2)(?>[\x01-\x08\x0B\x0C\x0E-!#-\[\]-\x7F]|\\\[\x00-\x7F]))*' .
					'(?2)")(?>(?1)\.(?1)(?4))*(?1)@(?!(?1)[a-z0-9-]{64,})(?1)(?>([a-z0-9](?>[a-z0-9-]*[a-z0-9])?)' .
					'(?>(?1)\.(?!(?1)[a-z0-9-]{64,})(?1)(?5)){0,126}|\[(?:(?>IPv6:(?>([a-f0-9]{1,4})(?>:(?6)){7}' .
					'|(?!(?:.*[a-f0-9][:\]]){8,})((?6)(?>:(?6)){0,6})?::(?7)?))|(?>(?>IPv6:(?>(?6)(?>:(?6)){5}:' .
					'|(?!(?:.*[a-f0-9]:){6,})(?8)?::(?>((?6)(?>:(?6)){0,4}):)?))?(25[0-5]|2[0-4][0-9]|1[0-9]{2}' .
					'|[1-9]?[0-9])(?>\.(?9)){3}))\])(?1)$/isD',
					$address
				);
			case 'pcre':
				// An older regex that doesn't need a recent PCRE.
				return (boolean) preg_match(
					'/^(?!(?>"?(?>\\\[ -~]|[^"])"?){255,})(?!(?>"?(?>\\\[ -~]|[^"])"?){65,}@)(?>' .
					'[!#-\'*+\/-9=?^-~-]+|"(?>(?>[\x01-\x08\x0B\x0C\x0E-!#-\[\]-\x7F]|\\\[\x00-\xFF]))*")' .
					'(?>\.(?>[!#-\'*+\/-9=?^-~-]+|"(?>(?>[\x01-\x08\x0B\x0C\x0E-!#-\[\]-\x7F]|\\\[\x00-\xFF]))*"))*' .
					'@(?>(?![a-z0-9-]{64,})(?>[a-z0-9](?>[a-z0-9-]*[a-z0-9])?)(?>\.(?![a-z0-9-]{64,})' .
					'(?>[a-z0-9](?>[a-z0-9-]*[a-z0-9])?)){0,126}|\[(?:(?>IPv6:(?>(?>[a-f0-9]{1,4})(?>:' .
					'[a-f0-9]{1,4}){7}|(?!(?:.*[a-f0-9][:\]]){8,})(?>[a-f0-9]{1,4}(?>:[a-f0-9]{1,4}){0,6})?' .
					'::(?>[a-f0-9]{1,4}(?>:[a-f0-9]{1,4}){0,6})?))|(?>(?>IPv6:(?>[a-f0-9]{1,4}(?>:' .
					'[a-f0-9]{1,4}){5}:|(?!(?:.*[a-f0-9]:){6,})(?>[a-f0-9]{1,4}(?>:[a-f0-9]{1,4}){0,4})?' .
					'::(?>(?:[a-f0-9]{1,4}(?>:[a-f0-9]{1,4}){0,4}):)?))?(?>25[0-5]|2[0-4][0-9]|1[0-9]{2}' .
					'|[1-9]?[0-9])(?>\.(?>25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])){3}))\])$/isD',
					$address
				);
			case 'html5':
				/**
				 * This is the pattern used in the HTML5 spec for validation of 'email' type form input elements.
				 */
				return (boolean) preg_match(
					'/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}' .
					'[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/sD',
					$address
				);
			case 'noregex':
				// No PCRE! Do something _very_ approximate!
				// Check the address is 3 chars or longer and contains an @ that's not the first or last char.
				return ( strlen( $address ) >= 3
					&& strpos( $address, '@' ) >= 1
					&& strpos( $address, '@' ) != strlen( $address ) - 1 ); // @codingStandardsIgnoreLine.
			case 'php':
			default:
				return (boolean) filter_var( $address, FILTER_VALIDATE_EMAIL );
		}
	}

	/**
	 * Tells whether IDNs (Internationalized Domain Names) are supported or not. This requires the
	 * "intl" and "mbstring" PHP extensions.
	 *
	 * @return bool "true" if required functions for IDN support are present
	 */
	public function idnSupported() { // @codingStandardsIgnoreLine.
		// @TODO: Write our own "idn_to_ascii" function for PHP <= 5.2.
		return function_exists( 'idn_to_ascii' ) && function_exists( 'mb_convert_encoding' );
	}

	/**
	 * Converts IDN in given email address to its ASCII form, also known as punycode, if possible.
	 * Important: Address must be passed in same encoding as currently set in PHPMailer::$charset.
	 *
	 * @see PHPMailer::$charset
	 * @param string $address The email address to convert.
	 * @return string The encoded address in ASCII form
	 */
	public function punyencodeAddress( $address ) { // @codingStandardsIgnoreLine.
		// Verify we have required functions, charset, and at-sign.
		if ( $this->idnSupported() &&
			! empty( $this->charset ) &&
			( $pos = strrpos( $address, '@' ) ) !== false ) { // @codingStandardsIgnoreLine.
			$domain = substr( $address, ++$pos );
			// Verify charset string is a valid one, and domain properly encoded in this charset.
			if ( $this->has8bitChars( $domain ) && @mb_check_encoding( $domain, $this->charset ) ) { // @codingStandardsIgnoreLine.
				$domain = mb_convert_encoding( $domain, 'UTF-8', $this->charset );
				if ( ( $punycode = defined( 'INTL_IDNA_VARIANT_UTS46' ) ? // @codingStandardsIgnoreLine.
					idn_to_ascii( $domain, 0, INTL_IDNA_VARIANT_UTS46 ) :
					idn_to_ascii( $domain ) ) !== false ) {
					return substr( $address, 0, $pos ) . $punycode;
				}
			}
		}
		return $address;
	}

	/**
	 * Create a message and send it.
	 * Uses the sending method specified by $Mailer.
	 *
	 * @throws phpmailerException .
	 * @return boolean false on error - See the errorinfo property for details of the error.
	 */
	public function send() {
		try {
			if ( ! $this->preSend() ) {
				return false;
			}
			return $this->postSend();
		} catch ( phpmailerException $exc ) {
			$this->mailheader = '';
			$this->setError( $exc->getMessage() );
			if ( $this->exceptions ) {
				throw $exc;
			}
			return false;
		}
	}

	/**
	 * Prepare a message for sending.
	 *
	 * @throws phpmailerException .
	 * @return boolean
	 */
	public function preSend() { // @codingStandardsIgnoreLine.
		try {
			$this->error_count = 0; // Reset errors.
			$this->mailheader  = '';

			// Dequeue recipient and Reply-To addresses with IDN.
			foreach ( array_merge( $this->RecipientsQueue, $this->ReplyToQueue ) as $params ) { // @codingStandardsIgnoreLine.
				$params[1] = $this->punyencodeAddress( $params[1] );
				call_user_func_array( array( $this, 'addAnAddress' ), $params );
			}
			if ( ( count( $this->to ) + count( $this->cc ) + count( $this->bcc ) ) < 1 ) {
				throw new phpmailerException( $this->lang( 'provide_address' ), self::STOP_CRITICAL );
			}

			// Validate From, Sender, and ConfirmReadingTo addresses.
			foreach ( array( 'from', 'sender', 'ConfirmReadingTo' ) as $address_kind ) {
				$this->$address_kind = trim( $this->$address_kind );
				if ( empty( $this->$address_kind ) ) {
					continue;
				}
				$this->$address_kind = $this->punyencodeAddress( $this->$address_kind );
				if ( ! $this->validateAddress( $this->$address_kind ) ) {
					$error_message = $this->lang( 'invalid_address' ) . ' (punyEncode) ' . $this->$address_kind;
					$this->setError( $error_message );
					$this->edebug( $error_message );
					if ( $this->exceptions ) {
						throw new phpmailerException( $error_message );
					}
					return false;
				}
			}

			// Set whether the message is multipart/alternative.
			if ( $this->alternativeExists() ) {
				$this->contenttype = 'multipart/alternative';
			}

			$this->setMessageType();
			// Refuse to send an empty message unless we are specifically allowing it.
			if ( ! $this->AllowEmpty && empty( $this->body ) ) { // @codingStandardsIgnoreLine.
				throw new phpmailerException( $this->lang( 'empty_message' ), self::STOP_CRITICAL );
			}

			// Create body before headers in case body makes changes to headers (e.g. altering transfer encoding).
			$this->mimeheader = '';
			$this->mimebody   = $this->createBody();
			// createBody may have added some headers, so retain them.
			$tempheaders       = $this->mimeheader;
			$this->mimeheader  = $this->createHeader();
			$this->mimeheader .= $tempheaders;

			// To capture the complete message when using mail(), create
			// an extra header list which createHeader() doesn't fold in.
			if ( 'mail' == $this->mailer ) { // WPCS: Loose comparison ok.
				if ( count( $this->to ) > 0 ) {
					$this->mailheader .= $this->addrAppend( 'To', $this->to );
				} else {
					$this->mailheader .= $this->headerLine( 'To', 'undisclosed-recipients:;' );
				}
				$this->mailheader .= $this->headerLine(
					'Subject',
					$this->encodeHeader( $this->secureHeader( trim( $this->Subject ) ) ) // @codingStandardsIgnoreLine.
				);
			}

			// Sign with DKIM if enabled.
			if ( ! empty( $this->DKIM_domain ) // @codingStandardsIgnoreLine.
				&& ! empty( $this->DKIM_selector ) // @codingStandardsIgnoreLine.
				&& ( ! empty( $this->DKIM_private_string ) // @codingStandardsIgnoreLine.
				|| ( ! empty( $this->DKIM_private ) && file_exists( $this->DKIM_private ) ) // @codingStandardsIgnoreLine.
				)
			) {
				$header_dkim      = $this->DKIM_Add(
					$this->mimeheader . $this->mailheader,
					$this->encodeHeader( $this->secureHeader( $this->Subject ) ), // @codingStandardsIgnoreLine.
					$this->mimebody
				);
				$this->mimeheader = rtrim( $this->mimeheader, "\r\n " ) . self::CRLF .
					str_replace( "\r\n", "\n", $header_dkim ) . self::CRLF;
			}
			return true;
		} catch ( phpmailerException $exc ) {
			$this->setError( $exc->getMessage() );
			if ( $this->exceptions ) {
				throw $exc;
			}
			return false;
		}
	}

	/**
	 * Actually send a message.
	 * Send the email via the selected mechanism .
	 *
	 * @throws phpmailerException .
	 * @return boolean
	 */
	public function postSend() { // @codingStandardsIgnoreLine.
		try {
			// Choose the mailer and send through it.
			switch ( $this->mailer ) {
				case 'sendmail':
				case 'qmail':
					return $this->sendmailSend( $this->mimeheader, $this->mimebody );
				case 'smtp':
					return $this->smtpSend( $this->mimeheader, $this->mimebody );
				case 'mail':
					return $this->mailSend( $this->mimeheader, $this->mimebody );
				default:
					$sendMethod = $this->mailer . 'Send'; // @codingStandardsIgnoreLine.
					if ( method_exists( $this, $sendMethod ) ) { // @codingStandardsIgnoreLine.
						return $this->$sendMethod( $this->mimeheader, $this->mimebody ); // @codingStandardsIgnoreLine.
					}

					return $this->mailSend( $this->mimeheader, $this->mimebody );
			}
		} catch ( phpmailerException $exc ) {
			$this->setError( $exc->getMessage() );
			$this->edebug( $exc->getMessage() );
			if ( $this->exceptions ) {
				throw $exc;
			}
		}
		return false;
	}

	/**
	 * Send mail using the $Sendmail program.
	 *
	 * @param string $header The message headers .
	 * @param string $body The message body .
	 * @see PHPMailer::$Sendmail
	 * @throws phpmailerException .
	 * @access protected
	 * @return boolean
	 */
	protected function sendmailSend( $header, $body ) { // @codingStandardsIgnoreLine.
		// CVE-2016-10033, CVE-2016-10045: Don't pass -f if characters will be escaped.
		if ( ! empty( $this->sender ) && self::isShellSafe( $this->sender ) ) {
			if ( 'qmail' == $this->mailer ) { // WPCS: Loose comparison ok .
				$sendmailFmt = '%s -f%s'; // @codingStandardsIgnoreLine.
			} else {
				$sendmailFmt = '%s -oi -f%s -t'; // @codingStandardsIgnoreLine.
			}
		} else {
			if ( 'qmail' == $this->mailer ) { // WPCS:Loose comparison ok.
				$sendmailFmt = '%s'; // @codingStandardsIgnoreLine.
			} else {
				$sendmailFmt = '%s -oi -t'; // @codingStandardsIgnoreLine.
			}
		}

		$sendmail = sprintf( $sendmailFmt, escapeshellcmd( $this->Sendmail ), $this->sender ); // @codingStandardsIgnoreLine.

		if ( $this->SingleTo ) { // @codingStandardsIgnoreLine.
			foreach ( $this->SingleToArray as $toAddr ) { // @codingStandardsIgnoreLine.
				if ( ! @$mail = popen( $sendmail, 'w' ) ) { // @codingStandardsIgnoreLine.
					throw new phpmailerException( $this->lang( 'execute' ) . $this->Sendmail, self::STOP_CRITICAL ); // @codingStandardsIgnoreLine.
				}
				fputs( $mail, 'To: ' . $toAddr . "\n" ); // @codingStandardsIgnoreLine.
				fputs( $mail, $header ); // @codingStandardsIgnoreLine.
				fputs( $mail, $body ); // @codingStandardsIgnoreLine.
				$result = pclose( $mail );
				$this->doCallback(
					( 0 == $result ), // WPCS:Loose comparison ok.
					array( $toAddr ), // @codingStandardsIgnoreLine.
					$this->cc,
					$this->bcc,
					$this->Subject, // @codingStandardsIgnoreLine.
					$body,
					$this->From // @codingStandardsIgnoreLine.
				);
				if ( 0 != $result ) { // WPCS:Loose comparison ok.
					throw new phpmailerException( $this->lang( 'execute' ) . $this->Sendmail, self::STOP_CRITICAL ); // @codingStandardsIgnoreLine.
				}
			}
		} else {
			if ( ! @$mail = popen( $sendmail, 'w' ) ) { // @codingStandardsIgnoreLine.
				throw new phpmailerException( $this->lang( 'execute' ) . $this->Sendmail, self::STOP_CRITICAL ); // @codingStandardsIgnoreLine.
			}
			fputs( $mail, $header ); // @codingStandardsIgnoreLine.
			fputs( $mail, $body ); // @codingStandardsIgnoreLine.
			$result = pclose( $mail );
			$this->doCallback(
				( 0 == $result ), // WPCS:Loose comparison ok.
				$this->to,
				$this->cc,
				$this->bcc,
				$this->Subject, // @codingStandardsIgnoreLine.
				$body,
				$this->From // @codingStandardsIgnoreLine.
			);
			if ( 0 != $result ) { // WPCS: Loose comparison ok.
				throw new phpmailerException( $this->lang( 'execute' ) . $this->Sendmail, self::STOP_CRITICAL ); // @codingStandardsIgnoreLine.
			}
		}
		return true;
	}

	/**
	 * Fix CVE-2016-10033 and CVE-2016-10045 by disallowing potentially unsafe shell characters.
	 *
	 * Note that escapeshellarg and escapeshellcmd are inadequate for our purposes, especially on Windows.
	 *
	 * @param string $string The string to be validated .
	 * @access protected
	 * @return boolean
	 */
	protected static function isShellSafe( $string ) { // @codingStandardsIgnoreLine.
		// Future-proof .
		if ( escapeshellcmd( $string ) !== $string
			|| ! in_array( escapeshellarg( $string ), array( "'$string'", "\"$string\"" ) ) // @codingStandardsIgnoreLine.
		) {
			return false;
		}

		$length = strlen( $string );

		for ( $i = 0; $i < $length; $i++ ) {
			$c = $string[ $i ];
			if ( ! ctype_alnum( $c ) && strpos( '@_-.', $c ) === false ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Send mail using the PHP mail() function.
	 *
	 * @param string $header The message headers .
	 * @param string $body The message body .
	 * @throws phpmailerException .
	 * @access protected
	 * @return boolean
	 */
	protected function mailSend( $header, $body ) { // @codingStandardsIgnoreLine.
		$toArr = array(); // @codingStandardsIgnoreLine.
		foreach ( $this->to as $toaddr ) {
			$toArr[] = $this->addrFormat( $toaddr ); // @codingStandardsIgnoreLine.
		}
		$to = implode( ', ', $toArr ); // @codingStandardsIgnoreLine.

		$params = null;
		// This sets the SMTP envelope sender which gets turned into a return-path header by the receiver .
		if ( ! empty( $this->sender ) && $this->validateAddress( $this->sender ) ) {
			// CVE-2016-10033, CVE-2016-10045: Don't pass -f if characters will be escaped.
			if ( self::isShellSafe( $this->sender ) ) {
				$params = sprintf( '-f%s', $this->sender );
			}
		}
		if ( ! empty( $this->sender ) && ! ini_get( 'safe_mode' ) && $this->validateAddress( $this->sender ) ) {
			$old_from = ini_get( 'sendmail_from' );
			ini_set( 'sendmail_from', $this->sender ); // @codingStandardsIgnoreLine.
		}
		$result = false;
		if ( $this->SingleTo && count( $toArr ) > 1 ) { // @codingStandardsIgnoreLine.
			foreach ( $toArr as $toAddr ) { // @codingStandardsIgnoreLine.
				$result = $this->mailPassthru( $toAddr, $this->Subject, $body, $header, $params ); // @codingStandardsIgnoreLine.
				$this->doCallback( $result, array( $toAddr ), $this->cc, $this->bcc, $this->Subject, $body, $this->From ); // @codingStandardsIgnoreLine.
			}
		} else {
			$result = $this->mailPassthru( $to, $this->Subject, $body, $header, $params ); // @codingStandardsIgnoreLine.
			$this->doCallback( $result, $this->to, $this->cc, $this->bcc, $this->Subject, $body, $this->From ); // @codingStandardsIgnoreLine.
		}
		if ( isset( $old_from ) ) {
			ini_set( 'sendmail_from', $old_from ); // @codingStandardsIgnoreLine.
		}
		if ( ! $result ) {
			throw new phpmailerException( $this->lang( 'instantiate' ), self::STOP_CRITICAL );
		}
		return true;
	}

	/**
	 * Get an instance to use for SMTP operations.
	 * Override this function to load your own SMTP implementation
	 *
	 * @return SMTP
	 */
	public function getSMTPInstance() { // @codingStandardsIgnoreLine.
		if ( ! is_object( $this->smtp ) ) {
			$this->smtp = new SMTP();
		}
		return $this->smtp;
	}

	/**
	 * Send mail via SMTP.
	 * Returns false if there is a bad MAIL FROM, RCPT, or DATA input.
	 * Uses the PHPMailerSMTP class by default.
	 *
	 * @see PHPMailer::getSMTPInstance() to use a different class.
	 * @param string $header The message headers .
	 * @param string $body The message body .
	 * @throws phpmailerException .
	 * @uses SMTP
	 * @access protected
	 * @return boolean
	 */
	protected function smtpSend( $header, $body ) { // @codingStandardsIgnoreLine.
		$bad_rcpt = array();
		if ( ! $this->smtpConnect( $this->SMTPOptions ) ) { // @codingStandardsIgnoreLine.
			throw new phpmailerException( $this->lang( 'smtp_connect_failed' ), self::STOP_CRITICAL );
		}
		if ( ! empty( $this->sender ) && $this->validateAddress( $this->sender ) ) {
			$smtp_from = $this->sender;
		} else {
			$smtp_from = $this->From; // @codingStandardsIgnoreLine.
		}
		if ( ! $this->smtp->mail( $smtp_from ) ) {
			$this->setError( $this->lang( 'from_failed' ) . $smtp_from . ' : ' . implode( ',', $this->smtp->getError() ) );
			throw new phpmailerException( $this->errorinfo, self::STOP_CRITICAL );
		}

		// Attempt to send to all recipients.
		foreach ( array( $this->to, $this->cc, $this->bcc ) as $togroup ) {
			foreach ( $togroup as $to ) {
				if ( ! $this->smtp->recipient( $to[0] ) ) {
					$error      = $this->smtp->getError();
					$bad_rcpt[] = array(
						'to'    => $to[0],
						'error' => $error['detail'],
					);
					$isSent     = false; // @codingStandardsIgnoreLine.
				} else {
					$isSent = true; // @codingStandardsIgnoreLine.
				}
				$this->doCallback( $isSent, array( $to[0] ), array(), array(), $this->Subject, $body, $this->From ); // @codingStandardsIgnoreLine.
			}
		}

		// Only send the DATA command if we have viable recipients .
		if ( ( count( $this->all_recipients ) > count( $bad_rcpt ) ) && ! $this->smtp->data( $header . $body ) ) {
			throw new phpmailerException( $this->lang( 'data_not_accepted' ), self::STOP_CRITICAL );
		}
		if ( $this->SMTPKeepAlive ) { // @codingStandardsIgnoreLine.
			$this->smtp->reset();
		} else {
			$this->smtp->quit();
			$this->smtp->close();
		}
		// Create error message for any bad addresses .
		if ( count( $bad_rcpt ) > 0 ) {
			$errstr = '';
			foreach ( $bad_rcpt as $bad ) {
				$errstr .= $bad['to'] . ': ' . $bad['error'];
			}
			throw new phpmailerException(
				$this->lang( 'recipients_failed' ) . $errstr,
				self::STOP_CONTINUE
			);
		}
		return true;
	}

	/**
	 * Initiate a connection to an SMTP server.
	 * Returns false if the operation failed.
	 *
	 * @param array $options An array of options compatible with stream_context_create() .
	 * @uses SMTP
	 * @access public
	 * @throws phpmailerException .
	 * @return boolean
	 */
	public function smtpConnect( $options = null ) { // @codingStandardsIgnoreLine.
		if ( is_null( $this->smtp ) ) {
			$this->smtp = $this->getSMTPInstance();
		}

		// If no options are provided, use whatever is set in the instance.
		if ( is_null( $options ) ) {
			$options = $this->SMTPOptions; // @codingStandardsIgnoreLine.
		}

		// Already connected?
		if ( $this->smtp->connected() ) {
			return true;
		}

		$this->smtp->setTimeout( $this->Timeout ); // @codingStandardsIgnoreLine.
		$this->smtp->setDebugLevel( $this->SMTPDebug ); // @codingStandardsIgnoreLine.
		$this->smtp->setDebugOutput( $this->Debugoutput ); // @codingStandardsIgnoreLine.
		$this->smtp->setVerp( $this->do_verp );
		$hosts         = explode( ';', $this->Host ); // @codingStandardsIgnoreLine.
		$lastexception = null;

		foreach ( $hosts as $hostentry ) {
			$hostinfo = array();
			if ( ! preg_match(
				'/^((ssl|tls):\/\/)*([a-zA-Z0-9\.-]*|\[[a-fA-F0-9:]+\]):?([0-9]*)$/',
				trim( $hostentry ),
				$hostinfo
			) ) {
				// Not a valid host entry.
				$this->edebug( 'Ignoring invalid host: ' . $hostentry );
				continue;
			}
			// $hostinfo[2]: optional ssl or tls prefix
			// $hostinfo[3]: the hostname
			// $hostinfo[4]: optional port number
			// The host string prefix can temporarily override the current setting for SMTPSecure
			// If it's not specified, the default value is used
			$prefix = '';
			$secure = $this->SMTPSecure; // @codingStandardsIgnoreLine.
			$tls    = ( 'tls' == $this->SMTPSecure ); // @codingStandardsIgnoreLine.
			if ( 'ssl' == $hostinfo[2] || ( '' == $hostinfo[2] && 'ssl' == $this->SMTPSecure ) ) { // @codingStandardsIgnoreLine.
				$prefix = 'ssl://';
				$tls    = false; // Can't have SSL and TLS at the same time .
				$secure = 'ssl';
			} elseif ( 'tls' == $hostinfo[2] ) { // @codingStandardsIgnoreLine.
				$tls = true;
				// tls doesn't use a prefix .
				$secure = 'tls';
			}
			// Do we need the OpenSSL extension?
			$sslext = defined( 'OPENSSL_ALGO_SHA1' );
			if ( 'tls' === $secure || 'ssl' === $secure ) {
				// Check for an OpenSSL constant rather than using extension_loaded, which is sometimes disabled.
				if ( ! $sslext ) {
					throw new phpmailerException( $this->lang( 'extension_missing' ) . 'openssl', self::STOP_CRITICAL );
				}
			}
			$host  = $hostinfo[3];
			$port  = $this->Port; // @codingStandardsIgnoreLine.
			$tport = (integer) $hostinfo[4];
			if ( $tport > 0 && $tport < 65536 ) {
				$port = $tport;
			}
			if ( $this->smtp->connect( $prefix . $host, $port, $this->Timeout, $options ) ) { // @codingStandardsIgnoreLine.
				try {
					if ( $this->Helo ) { // @codingStandardsIgnoreLine.
						$hello = $this->Helo; // @codingStandardsIgnoreLine.
					} else {
						$hello = $this->serverHostname();
					}
					$this->smtp->hello( $hello );
					// Automatically enable TLS encryption if:
					// * it's not disabled
					// * we have openssl extension
					// * we are not already using SSL
					// * the server offers STARTTLS.
					if ( $this->SMTPAutoTLS && $sslext && 'ssl' != $secure && $this->smtp->getServerExt( 'STARTTLS' ) ) { // @codingStandardsIgnoreLine.
						$tls = true;
					}
					if ( $tls ) {
						if ( ! $this->smtp->startTLS() ) {
							throw new phpmailerException( $this->lang( 'connect_host' ) );
						}
						// We must resend EHLO after TLS negotiation.
						$this->smtp->hello( $hello );
					}
					if ( $this->SMTPAuth ) { // @codingStandardsIgnoreLine.
						if ( ! $this->smtp->authenticate(
							$this->Username, // @codingStandardsIgnoreLine.
							$this->Password, // @codingStandardsIgnoreLine.
							$this->AuthType, // @codingStandardsIgnoreLine.
							$this->Realm, // @codingStandardsIgnoreLine.
							$this->Workstation // @codingStandardsIgnoreLine.
						)
						) {
							throw new phpmailerException( $this->lang( 'authenticate' ) );
						}
					}
					return true;
				} catch ( phpmailerException $exc ) {
					$lastexception = $exc;
					$this->edebug( $exc->getMessage() );
					// We must have connected, but then failed TLS or Auth, so close connection nicely.
					$this->smtp->quit();
				}
			}
		}
		// If we get here, all connection attempts have failed, so close connection hard.
		$this->smtp->close();
		// As we've caught all exceptions, just report whatever the last one was.
		if ( $this->exceptions && ! is_null( $lastexception ) ) {
			throw $lastexception;
		}
		return false;
	}

	/**
	 * Close the active SMTP session if one exists.
	 *
	 * @return void
	 */
	public function smtpClose() { // @codingStandardsIgnoreLine.
		if ( is_a( $this->smtp, 'SMTP' ) ) {
			if ( $this->smtp->connected() ) {
				$this->smtp->quit();
				$this->smtp->close();
			}
		}
	}

	/**
	 * Set the language for error messages.
	 *
	 * @param string $langcode ISO 639-1 2-character language code .
	 * @param string $lang_path Path to the language file directory, with trailing separator (slash) .
	 * @return boolean
	 * @access public
	 */
	public function setLanguage( $langcode = 'en', $lang_path = '' ) { // @codingStandardsIgnoreLine.
		// Backwards compatibility for renamed language codes.
		$renamed_langcodes = array(
			'br' => 'pt_br',
			'cz' => 'cs',
			'dk' => 'da',
			'no' => 'nb',
			'se' => 'sv',
			'sr' => 'rs',
		);

		if ( isset( $renamed_langcodes[ $langcode ] ) ) {
			$langcode = $renamed_langcodes[ $langcode ];
		}

		// Define full set of translatable strings in English.
		$PHPMAILER_LANG = array( // @codingStandardsIgnoreLine.
			'authenticate'         => 'SMTP Error: Could not authenticate.',
			'connect_host'         => 'SMTP Error: Could not connect to SMTP host.',
			'data_not_accepted'    => 'SMTP Error: data not accepted.',
			'empty_message'        => 'Message body empty',
			'encoding'             => 'Unknown encoding: ',
			'execute'              => 'Could not execute: ',
			'file_access'          => 'Could not access file: ',
			'file_open'            => 'File Error: Could not open file: ',
			'from_failed'          => 'The following From address failed: ',
			'instantiate'          => 'Could not instantiate mail function.',
			'invalid_address'      => 'Invalid address: ',
			'mailer_not_supported' => ' mailer is not supported.',
			'provide_address'      => 'You must provide at least one recipient email address.',
			'recipients_failed'    => 'SMTP Error: The following recipients failed: ',
			'signing'              => 'Signing Error: ',
			'smtp_connect_failed'  => 'SMTP connect() failed.',
			'smtp_error'           => 'SMTP server error: ',
			'variable_set'         => 'Cannot set or reset variable: ',
			'extension_missing'    => 'Extension missing: ',
		);
		if ( empty( $lang_path ) ) {
			// Calculate an absolute path so it can work if CWD is not here.
			$lang_path = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR;
		}
		// Validate $langcode .
		if ( ! preg_match( '/^[a-z]{2}(?:_[a-zA-Z]{2})?$/', $langcode ) ) {
			$langcode = 'en';
		}
		$foundlang = true;
		$lang_file = $lang_path . 'phpmailer.lang-' . $langcode . '.php';
		// There is no English translation file.
		if ( 'en' != $langcode ) { // WPCS:Loose comparison ok.
			// Make sure language file path is readable.
			if ( ! is_readable( $lang_file ) ) {
				$foundlang = false;
			} else {
				// Overwrite language-specific strings.
				// This way we'll never have missing translation keys.
				$foundlang = include $lang_file;
			}
		}
		$this->language = $PHPMAILER_LANG; // @codingStandardsIgnoreLine.
		return (boolean) $foundlang; // Returns false if language not found.
	}

	/**
	 * Get the array of strings for the current language.
	 *
	 * @return array
	 */
	public function getTranslations() { // @codingStandardsIgnoreLine.
		return $this->language;
	}

	/**
	 * Create recipient headers.
	 *
	 * @access public
	 * @param string $type .
	 * @param array  $addr An array of recipient .
	 * @return string
	 */
	public function addrAppend( $type, $addr ) { // @codingStandardsIgnoreLine.
		$addresses = array();
		foreach ( $addr as $address ) {
			$addresses[] = $this->addrFormat( $address );
		}
		return $type . ': ' . implode( ', ', $addresses ) . $this->LE; // @codingStandardsIgnoreLine.
	}

	/**
	 * Format an address for use in a message header.
	 *
	 * @access public
	 * @param array $addr A 2-element indexed array, element 0 containing an address, element 1 containing a name.
	 * @return string
	 */
	public function addrFormat( $addr ) { // @codingStandardsIgnoreLine.
		if ( empty( $addr[1] ) ) { // No name provided.
			return $this->secureHeader( $addr[0] );
		} else {
			return $this->encodeHeader( $this->secureHeader( $addr[1] ), 'phrase' ) . ' <' . $this->secureHeader(
				$addr[0]
			) . '>';
		}
	}

	/**
	 * This function is used to wrap the message .
	 *
	 * @param string  $message The message to wrap .
	 * @param integer $length The line length to wrap to .
	 * @param boolean $qp_mode Whether to run in Quoted-Printable mode .
	 * @access public
	 * @return string
	 */
	public function wrapText( $message, $length, $qp_mode = false ) { // @codingStandardsIgnoreLine.
		if ( $qp_mode ) {
			$soft_break = sprintf( ' =%s', $this->LE ); // @codingStandardsIgnoreLine.
		} else {
			$soft_break = $this->LE; // @codingStandardsIgnoreLine.
		}
		// If utf-8 encoding is used, we will need to make sure we don't
		// split multibyte characters when we wrap.
		$is_utf8 = ( strtolower( $this->charset ) == 'utf-8' ); // WPCS: Loose comparison ok.
		$lelen   = strlen( $this->LE ); // @codingStandardsIgnoreLine.
		$crlflen = strlen( self::CRLF );

		$message = $this->fixEOL( $message );
		// Remove a trailing line break.
		if ( substr( $message, -$lelen ) == $this->LE ) { // @codingStandardsIgnoreLine.
			$message = substr( $message, 0, -$lelen );
		}

		// Split message into lines.
		$lines = explode( $this->LE, $message ); // @codingStandardsIgnoreLine.
		// Message will be rebuilt in here.
		$message = '';
		foreach ( $lines as $line ) {
			$words     = explode( ' ', $line );
			$buf       = '';
			$firstword = true;
			foreach ( $words as $word ) {
				if ( $qp_mode and ( strlen( $word ) > $length ) ) { // @codingStandardsIgnoreLine.
					$space_left = $length - strlen( $buf ) - $crlflen;
					if ( ! $firstword ) {
						if ( $space_left > 20 ) {
							$len = $space_left;
							if ( $is_utf8 ) {
								$len = $this->utf8CharBoundary( $word, $len );
							} elseif ( substr( $word, $len - 1, 1 ) == '=' ) { // WPCS: Loose comparison ok .
								$len--;
							} elseif ( substr( $word, $len - 2, 1 ) == '=' ) { // WPCS: Loose comparison ok .
								$len -= 2;
							}
							$part     = substr( $word, 0, $len );
							$word     = substr( $word, $len );
							$buf     .= ' ' . $part;
							$message .= $buf . sprintf( '=%s', self::CRLF );
						} else {
							$message .= $buf . $soft_break;
						}
						$buf = '';
					}
					while ( strlen( $word ) > 0 ) { // @codingStandardsIgnoreLine.
						if ( $length <= 0 ) {
							break;
						}
						$len = $length;
						if ( $is_utf8 ) {
							$len = $this->utf8CharBoundary( $word, $len );
						} elseif ( substr( $word, $len - 1, 1 ) == '=' ) { // WPCS: Loose comparison ok .
							$len--;
						} elseif ( substr( $word, $len - 2, 1 ) == '=' ) { // WPCS: Loose comparison ok .
							$len -= 2;
						}
						$part = substr( $word, 0, $len );
						$word = substr( $word, $len );

						if ( strlen( $word ) > 0 ) {
							$message .= $part . sprintf( '=%s', self::CRLF );
						} else {
							$buf = $part;
						}
					}
				} else {
					$buf_o = $buf;
					if ( ! $firstword ) {
						$buf .= ' ';
					}
					$buf .= $word;

					if ( strlen( $buf ) > $length && '' != $buf_o ) { // WPCS: Loose comparison ok .
						$message .= $buf_o . $soft_break;
						$buf      = $word;
					}
				}
				$firstword = false;
			}
			$message .= $buf . self::CRLF;
		}

		return $message;
	}

	/**
	 * Find the last character boundary prior to $maxLength in a utf-8
	 * quoted-printable encoded string.
	 * Original written by Colin Brown.
	 *
	 * @access public
	 * @param string  $encodedText utf-8 QP text .
	 * @param integer $maxLength Find the last character boundary prior to this length .
	 * @return integer
	 */
	public function utf8CharBoundary( $encodedText, $maxLength ) { // @codingStandardsIgnoreLine.
		$foundSplitPos = false; // @codingStandardsIgnoreLine.
		$lookBack      = 3; // @codingStandardsIgnoreLine.
		while ( ! $foundSplitPos ) { // @codingStandardsIgnoreLine.
			$lastChunk      = substr( $encodedText, $maxLength - $lookBack, $lookBack ); // @codingStandardsIgnoreLine.
			$encodedCharPos = strpos( $lastChunk, '=' ); // @codingStandardsIgnoreLine.
			if ( false !== $encodedCharPos ) { // @codingStandardsIgnoreLine.
				$hex = substr( $encodedText, $maxLength - $lookBack + $encodedCharPos + 1, 2 ); // @codingStandardsIgnoreLine.
				$dec = hexdec( $hex );
				if ( $dec < 128 ) {
					// Single byte character.
					// If the encoded char was found at pos 0, it will fit
					// otherwise reduce maxLength to start of the encoded char .
					if ( $encodedCharPos > 0 ) { // @codingStandardsIgnoreLine.
						$maxLength = $maxLength - ( $lookBack - $encodedCharPos ); // @codingStandardsIgnoreLine.
					}
					$foundSplitPos = true; // @codingStandardsIgnoreLine.
				} elseif ( $dec >= 192 ) {
					// First byte of a multi byte character
					// Reduce maxLength to split at start of character .
					$maxLength     = $maxLength - ( $lookBack - $encodedCharPos ); // @codingStandardsIgnoreLine.
					$foundSplitPos = true; // @codingStandardsIgnoreLine.
				} elseif ( $dec < 192 ) {
					// Middle byte of a multi byte character, look further back .
					$lookBack += 3; // @codingStandardsIgnoreLine.
				}
			} else {
				// No encoded character found .
				$foundSplitPos = true; // @codingStandardsIgnoreLine.
			}
		}
		return $maxLength; // @codingStandardsIgnoreLine.
	}

	/**
	 * Apply word wrapping to the message body.
	 *
	 * @access public
	 * @return void
	 */
	public function setWordWrap() { // @codingStandardsIgnoreLine.
		if ( $this->wordwrap < 1 ) {
			return;
		}

		switch ( $this->message_type ) {
			case 'alt':
			case 'alt_inline':
			case 'alt_attach':
			case 'alt_inline_attach':
				$this->altbody = $this->wrapText( $this->altbody, $this->wordwrap );
				break;
			default:
				$this->body = $this->wrapText( $this->body, $this->wordwrap );
				break;
		}
	}

	/**
	 * Assemble message headers.
	 *
	 * @access public
	 * @return string The assembled headers
	 */
	public function createHeader() { // @codingStandardsIgnoreLine.
		$result = '';

		$result .= $this->headerLine( 'Date', '' == $this->MessageDate ? self::rfcDate() : $this->MessageDate ); // @codingStandardsIgnoreLine.

		// To be created automatically by mail().
		if ( $this->SingleTo ) { // @codingStandardsIgnoreLine.
			if ( 'mail' != $this->mailer ) { // WPCS: Loose comparison ok .
				foreach ( $this->to as $toaddr ) {
					$this->SingleToArray[] = $this->addrFormat( $toaddr ); // @codingStandardsIgnoreLine.
				}
			}
		} else {
			if ( count( $this->to ) > 0 ) {
				if ( 'mail' != $this->mailer ) { // WPCS: Loose comparison ok .
					$result .= $this->addrAppend( 'To', $this->to );
				}
			} elseif ( count( $this->cc ) == 0 ) { // WPCS: Loose comparison ok .
				$result .= $this->headerLine( 'To', 'undisclosed-recipients:;' );
			}
		}

		$result .= $this->addrAppend( 'From', array( array( trim( $this->From ), $this->fromname ) ) ); // @codingStandardsIgnoreLine.

		// sendmail and mail() extract Cc from the header before sending .
		if ( count( $this->cc ) > 0 ) {
			$result .= $this->addrAppend( 'Cc', $this->cc );
		}

		// sendmail and mail() extract Bcc from the header before sending.
		if ( (
				'sendmail' == $this->mailer || 'qmail' == $this->mailer || 'mail' == $this->mailer // WPCS: Loose comparison ok .
			)
			&& count( $this->bcc ) > 0
		) {
			$result .= $this->addrAppend( 'Bcc', $this->bcc );
		}

		if ( count( $this->ReplyTo ) > 0 ) { // @codingStandardsIgnoreLine.
			$result .= $this->addrAppend( 'Reply-To', $this->ReplyTo ); // @codingStandardsIgnoreLine.
		}

		// mail() sets the subject itself.
		if ( 'mail' != $this->mailer ) { // WPCS: Loose comparison ok .
			$result .= $this->headerLine( 'Subject', $this->encodeHeader( $this->secureHeader( $this->Subject ) ) ); // @codingStandardsIgnoreLine.
		}

		// Only allow a custom message ID if it conforms to RFC 5322 section 3.6.4 .
		if ( '' != $this->MessageID && preg_match( '/^<.*@.*>$/', $this->MessageID ) ) { // @codingStandardsIgnoreLine.
			$this->lastMessageID = $this->MessageID; // @codingStandardsIgnoreLine.
		} else {
			$this->lastMessageID = sprintf( '<%s@%s>', $this->uniqueid, $this->serverHostname() ); // @codingStandardsIgnoreLine.
		}
		$result .= $this->headerLine( 'Message-ID', $this->lastMessageID ); // @codingStandardsIgnoreLine.
		if ( ! is_null( $this->priority ) ) {
			$result .= $this->headerLine( 'X-Priority', $this->priority );
		}
		if ( '' == $this->XMailer ) { // @codingStandardsIgnoreLine.
			$result .= $this->headerLine(
				'X-Mailer',
				'PHPMailer ' . $this->version . ' (https://wordpress.org/plugins/wp-mail-booster/)'
			);
		} else {
			$myXmailer = trim( $this->XMailer ); // @codingStandardsIgnoreLine.
			if ( $myXmailer ) { // @codingStandardsIgnoreLine.
				$result .= $this->headerLine( 'X-Mailer', $myXmailer ); // @codingStandardsIgnoreLine.
			}
		}

		if ( '' != $this->ConfirmReadingTo ) { // @codingStandardsIgnoreLine.
			$result .= $this->headerLine( 'Disposition-Notification-To', '<' . $this->ConfirmReadingTo . '>' ); // @codingStandardsIgnoreLine.
		}

		// Add custom headers.
		foreach ( $this->CustomHeader as $header ) { // @codingStandardsIgnoreLine.
			$result .= $this->headerLine(
				trim( $header[0] ),
				$this->encodeHeader( trim( $header[1] ) )
			);
		}
		if ( ! $this->sign_key_file ) {
			$result .= $this->headerLine( 'MIME-Version', '1.0' );
			$result .= $this->getMailMIME();
		}

		return $result;
	}

	/**
	 * Get the message MIME type headers.
	 *
	 * @access public
	 * @return string
	 */
	public function getMailMIME() { // @codingStandardsIgnoreLine.
		$result      = '';
		$ismultipart = true;
		switch ( $this->message_type ) {
			case 'inline':
				$result .= $this->headerLine( 'Content-Type', 'multipart/related;' );
				$result .= $this->textLine( "\tboundary=\"" . $this->boundary[1] . '"' );
				break;
			case 'attach':
			case 'inline_attach':
			case 'alt_attach':
			case 'alt_inline_attach':
				$result .= $this->headerLine( 'Content-Type', 'multipart/mixed;' );
				$result .= $this->textLine( "\tboundary=\"" . $this->boundary[1] . '"' );
				break;
			case 'alt':
			case 'alt_inline':
				$result .= $this->headerLine( 'Content-Type', 'multipart/alternative;' );
				$result .= $this->textLine( "\tboundary=\"" . $this->boundary[1] . '"' );
				break;
			default:
				$result     .= $this->textLine( 'Content-Type: ' . $this->contenttype . '; charset=' . $this->charset );
				$ismultipart = false;
				break;
		}
		if ( '7bit' != $this->mb_encoding ) { // WPCS: Loose comparison ok .
			if ( $ismultipart ) {
				if ( '8bit' == $this->mb_encoding ) { // WPCS: Loose comparison ok .
					$result .= $this->headerLine( 'Content-Transfer-Encoding', '8bit' );
				}
			} else {
				$result .= $this->headerLine( 'Content-Transfer-Encoding', $this->mb_encoding );
			}
		}

		if ( 'mail' != $this->mailer ) { // WPCS: Loose comparison ok .
			$result .= $this->LE; // @codingStandardsIgnoreLine.
		}

		return $result;
	}

	/**
	 * Returns the whole MIME message.
	 * Includes complete headers and body.
	 * Only valid post preSend().
	 *
	 * @see PHPMailer::preSend()
	 * @access public
	 * @return string
	 */
	public function getSentMIMEMessage() { // @codingStandardsIgnoreLine.
		return rtrim( $this->mimeheader . $this->mailheader, "\n\r" ) . self::CRLF . self::CRLF . $this->mimebody;
	}

	/**
	 * Create unique ID
	 *
	 * @return string
	 */
	protected function generateId() { // @codingStandardsIgnoreLine.
		return md5( uniqid( time() ) );
	}

	/**
	 * Assemble the message body.
	 * Returns an empty string on failure.
	 *
	 * @access public
	 * @throws phpmailerException .
	 * @return string The assembled message body
	 */
	public function createBody() { // @codingStandardsIgnoreLine.
		$body = '';
		// Create unique IDs and preset boundaries .
		$this->uniqueid    = $this->generateId();
		$this->boundary[1] = 'b1_' . $this->uniqueid;
		$this->boundary[2] = 'b2_' . $this->uniqueid;
		$this->boundary[3] = 'b3_' . $this->uniqueid;

		if ( $this->sign_key_file ) {
			$body .= $this->getMailMIME() . $this->LE; // @codingStandardsIgnoreLine.
		}

		$this->setWordWrap();

		$bodyEncoding = $this->mb_encoding; // @codingStandardsIgnoreLine.
		$bodyCharSet  = $this->charset; // @codingStandardsIgnoreLine.
		// Can we do a 7-bit downgrade?
		if ( '8bit' == $bodyEncoding && ! $this->has8bitChars( $this->body ) ) { // @codingStandardsIgnoreLine.
			$bodyEncoding = '7bit'; // @codingStandardsIgnoreLine.
			// All ISO 8859, Windows codepage and UTF-8 charsets are ascii compatible up to 7-bit.
			$bodyCharSet = 'us-ascii'; // @codingStandardsIgnoreLine.
		}
		// If lines are too long, and we're not already using an encoding that will shorten them,
		// change to quoted-printable transfer encoding for the body part only.
		if ( 'base64' != $this->mb_encoding && self::hasLineLongerThanMax( $this->body ) ) { // WPCS: Loose comparison ok .
			$bodyEncoding = 'quoted-printable'; // @codingStandardsIgnoreLine.
		}

		$altBodyEncoding = $this->mb_encoding; // @codingStandardsIgnoreLine.
		$altBodyCharSet  = $this->charset; // @codingStandardsIgnoreLine.
		// Can we do a 7-bit downgrade?
		if ( '8bit' == $altBodyEncoding && ! $this->has8bitChars( $this->altbody ) ) { // @codingStandardsIgnoreLine.
			$altBodyEncoding = '7bit'; // @codingStandardsIgnoreLine.
			// All ISO 8859, Windows codepage and UTF-8 charsets are ascii compatible up to 7-bit.
			$altBodyCharSet = 'us-ascii'; // @codingStandardsIgnoreLine.
		}
		// If lines are too long, and we're not already using an encoding that will shorten them,
		// change to quoted-printable transfer encoding for the alt body part only.
		if ( 'base64' != $altBodyEncoding && self::hasLineLongerThanMax( $this->altbody ) ) { // @codingStandardsIgnoreLine.
			$altBodyEncoding = 'quoted-printable'; // @codingStandardsIgnoreLine.
		}
		// Use this as a preamble in all multipart message types.
		$mimepre = 'This is a multi-part message in MIME format.' . $this->LE . $this->LE; // @codingStandardsIgnoreLine.
		switch ( $this->message_type ) {
			case 'inline':
				$body .= $mimepre;
				$body .= $this->getBoundary( $this->boundary[1], $bodyCharSet, '', $bodyEncoding ); // @codingStandardsIgnoreLine.
				$body .= $this->encodeString( $this->body, $bodyEncoding ); // @codingStandardsIgnoreLine.
				$body .= $this->LE . $this->LE; // @codingStandardsIgnoreLine.
				$body .= $this->attachAll( 'inline', $this->boundary[1] );
				break;
			case 'attach':
				$body .= $mimepre;
				$body .= $this->getBoundary( $this->boundary[1], $bodyCharSet, '', $bodyEncoding ); // @codingStandardsIgnoreLine.
				$body .= $this->encodeString( $this->body, $bodyEncoding ); // @codingStandardsIgnoreLine.
				$body .= $this->LE . $this->LE; // @codingStandardsIgnoreLine.
				$body .= $this->attachAll( 'attachment', $this->boundary[1] );
				break;
			case 'inline_attach':
				$body .= $mimepre;
				$body .= $this->textLine( '--' . $this->boundary[1] );
				$body .= $this->headerLine( 'Content-Type', 'multipart/related;' );
				$body .= $this->textLine( "\tboundary=\"" . $this->boundary[2] . '"' );
				$body .= $this->LE; // @codingStandardsIgnoreLine.
				$body .= $this->getBoundary( $this->boundary[2], $bodyCharSet, '', $bodyEncoding ); // @codingStandardsIgnoreLine.
				$body .= $this->encodeString( $this->body, $bodyEncoding ); // @codingStandardsIgnoreLine.
				$body .= $this->LE . $this->LE; // @codingStandardsIgnoreLine.
				$body .= $this->attachAll( 'inline', $this->boundary[2] );
				$body .= $this->LE; // @codingStandardsIgnoreLine.
				$body .= $this->attachAll( 'attachment', $this->boundary[1] );
				break;
			case 'alt':
				$body .= $mimepre;
				$body .= $this->getBoundary( $this->boundary[1], $altBodyCharSet, 'text/plain', $altBodyEncoding ); // @codingStandardsIgnoreLine.
				$body .= $this->encodeString( $this->altbody, $altBodyEncoding ); // @codingStandardsIgnoreLine.
				$body .= $this->LE . $this->LE; // @codingStandardsIgnoreLine.
				$body .= $this->getBoundary( $this->boundary[1], $bodyCharSet, 'text/html', $bodyEncoding ); // @codingStandardsIgnoreLine.
				$body .= $this->encodeString( $this->body, $bodyEncoding ); // @codingStandardsIgnoreLine.
				$body .= $this->LE . $this->LE; // @codingStandardsIgnoreLine.
				if ( ! empty( $this->ical ) ) {
					$body .= $this->getBoundary( $this->boundary[1], '', 'text/calendar; method=REQUEST', '' );
					$body .= $this->encodeString( $this->ical, $this->mb_encoding );
					$body .= $this->LE . $this->LE; // @codingStandardsIgnoreLine.
				}
				$body .= $this->endBoundary( $this->boundary[1] );
				break;
			case 'alt_inline':
				$body .= $mimepre;
				$body .= $this->getBoundary( $this->boundary[1], $altBodyCharSet, 'text/plain', $altBodyEncoding ); // @codingStandardsIgnoreLine.
				$body .= $this->encodeString( $this->altbody, $altBodyEncoding ); // @codingStandardsIgnoreLine.
				$body .= $this->LE . $this->LE; // @codingStandardsIgnoreLine.
				$body .= $this->textLine( '--' . $this->boundary[1] );
				$body .= $this->headerLine( 'Content-Type', 'multipart/related;' );
				$body .= $this->textLine( "\tboundary=\"" . $this->boundary[2] . '"' );
				$body .= $this->LE; // @codingStandardsIgnoreLine.
				$body .= $this->getBoundary( $this->boundary[2], $bodyCharSet, 'text/html', $bodyEncoding ); // @codingStandardsIgnoreLine.
				$body .= $this->encodeString( $this->body, $bodyEncoding ); // @codingStandardsIgnoreLine.
				$body .= $this->LE . $this->LE; // @codingStandardsIgnoreLine.
				$body .= $this->attachAll( 'inline', $this->boundary[2] );
				$body .= $this->LE; // @codingStandardsIgnoreLine.
				$body .= $this->endBoundary( $this->boundary[1] );
				break;
			case 'alt_attach':
				$body .= $mimepre;
				$body .= $this->textLine( '--' . $this->boundary[1] );
				$body .= $this->headerLine( 'Content-Type', 'multipart/alternative;' );
				$body .= $this->textLine( "\tboundary=\"" . $this->boundary[2] . '"' );
				$body .= $this->LE; // @codingStandardsIgnoreLine.
				$body .= $this->getBoundary( $this->boundary[2], $altBodyCharSet, 'text/plain', $altBodyEncoding ); // @codingStandardsIgnoreLine.
				$body .= $this->encodeString( $this->altbody, $altBodyEncoding ); // @codingStandardsIgnoreLine.
				$body .= $this->LE . $this->LE; // @codingStandardsIgnoreLine.
				$body .= $this->getBoundary( $this->boundary[2], $bodyCharSet, 'text/html', $bodyEncoding ); // @codingStandardsIgnoreLine.
				$body .= $this->encodeString( $this->body, $bodyEncoding ); // @codingStandardsIgnoreLine.
				$body .= $this->LE . $this->LE; // @codingStandardsIgnoreLine.
				$body .= $this->endBoundary( $this->boundary[2] );
				$body .= $this->LE; // @codingStandardsIgnoreLine.
				$body .= $this->attachAll( 'attachment', $this->boundary[1] );
				break;
			case 'alt_inline_attach':
				$body .= $mimepre;
				$body .= $this->textLine( '--' . $this->boundary[1] );
				$body .= $this->headerLine( 'Content-Type', 'multipart/alternative;' );
				$body .= $this->textLine( "\tboundary=\"" . $this->boundary[2] . '"' );
				$body .= $this->LE; // @codingStandardsIgnoreLine.
				$body .= $this->getBoundary( $this->boundary[2], $altBodyCharSet, 'text/plain', $altBodyEncoding ); // @codingStandardsIgnoreLine.
				$body .= $this->encodeString( $this->altbody, $altBodyEncoding ); // @codingStandardsIgnoreLine.
				$body .= $this->LE . $this->LE; // @codingStandardsIgnoreLine.
				$body .= $this->textLine( '--' . $this->boundary[2] );
				$body .= $this->headerLine( 'Content-Type', 'multipart/related;' );
				$body .= $this->textLine( "\tboundary=\"" . $this->boundary[3] . '"' );
				$body .= $this->LE; // @codingStandardsIgnoreLine.
				$body .= $this->getBoundary( $this->boundary[3], $bodyCharSet, 'text/html', $bodyEncoding ); // @codingStandardsIgnoreLine.
				$body .= $this->encodeString( $this->body, $bodyEncoding ); // @codingStandardsIgnoreLine.
				$body .= $this->LE . $this->LE; // @codingStandardsIgnoreLine.
				$body .= $this->attachAll( 'inline', $this->boundary[3] );
				$body .= $this->LE; // @codingStandardsIgnoreLine.
				$body .= $this->endBoundary( $this->boundary[2] );
				$body .= $this->LE; // @codingStandardsIgnoreLine.
				$body .= $this->attachAll( 'attachment', $this->boundary[1] );
				break;
			default:
				// Catch case 'plain' and case '', applies to simple `text/plain` and `text/html` body content types
				// Reset the `mb_encoding` property in case we changed it for line length reasons.
				$this->mb_encoding = $bodyEncoding; // @codingStandardsIgnoreLine.
				$body             .= $this->encodeString( $this->body, $this->mb_encoding );
				break;
		}

		if ( $this->isError() ) {
			$body = '';
		} elseif ( $this->sign_key_file ) {
			try {
				if ( ! defined( 'PKCS7_TEXT' ) ) {
					throw new phpmailerException( $this->lang( 'extension_missing' ) . 'openssl' );
				}
				// @TODO would be nice to use php://temp streams here, but need to wrap for PHP < 5.1
				$file = tempnam( sys_get_temp_dir(), 'mail' ); // @codingStandardsIgnoreLine.
				if ( false === file_put_contents( $file, $body ) ) { // @codingStandardsIgnoreLine.
					throw new phpmailerException( $this->lang( 'signing' ) . ' Could not write temp file' );
				}
				$signed = tempnam( sys_get_temp_dir(), 'signed' ); // @codingStandardsIgnoreLine.
				if ( empty( $this->sign_extracerts_file ) ) {
					$sign = @openssl_pkcs7_sign( // @codingStandardsIgnoreLine.
						$file,
						$signed,
						'file://' . realpath( $this->sign_cert_file ),
						array( 'file://' . realpath( $this->sign_key_file ), $this->sign_key_pass ),
						null
					);
				} else {
					$sign = @openssl_pkcs7_sign( // @codingStandardsIgnoreLine.
						$file,
						$signed,
						'file://' . realpath( $this->sign_cert_file ),
						array( 'file://' . realpath( $this->sign_key_file ), $this->sign_key_pass ),
						null,
						PKCS7_DETACHED,
						$this->sign_extracerts_file
					);
				}
				if ( $sign ) {
					@unlink( $file ); // @codingStandardsIgnoreLine.
					$body = file_get_contents( $signed ); // @codingStandardsIgnoreLine.
					@unlink( $signed ); // @codingStandardsIgnoreLine.
					// The message returned by openssl contains both headers and body, so need to split them up.
					$parts             = explode( "\n\n", $body, 2 );
					$this->mimeheader .= $parts[0] . $this->LE . $this->LE; // @codingStandardsIgnoreLine.
					$body              = $parts[1];
				} else {
					@unlink( $file ); // @codingStandardsIgnoreLine.
					@unlink( $signed ); // @codingStandardsIgnoreLine.
					throw new phpmailerException( $this->lang( 'signing' ) . openssl_error_string() );
				}
			} catch ( phpmailerException $exc ) {
				$body = '';
				if ( $this->exceptions ) {
					throw $exc;
				}
			}
		}
		return $body;
	}

	/**
	 * Return the start of a message boundary.
	 *
	 * @access protected
	 * @param string $boundary .
	 * @param string $charSet .
	 * @param string $contentType .
	 * @param string $encoding .
	 * @return string
	 */
	protected function getBoundary( $boundary, $charSet, $contentType, $encoding ) { // @codingStandardsIgnoreLine.
		$result = '';
		if ( '' == $charSet ) { // @codingStandardsIgnoreLine.
			$charSet = $this->charset; // @codingStandardsIgnoreLine.
		}
		if ( '' == $contentType ) { // @codingStandardsIgnoreLine.
			$contentType = $this->contenttype; // @codingStandardsIgnoreLine.
		}
		if ( '' == $encoding ) { // WPCS: Loose comparison ok .
			$encoding = $this->mb_encoding;
		}
		$result .= $this->textLine( '--' . $boundary );
		$result .= sprintf( 'Content-Type: %s; charset=%s', $contentType, $charSet ); // @codingStandardsIgnoreLine.
		$result .= $this->LE; // @codingStandardsIgnoreLine.
		// RFC1341 part 5 says 7bit is assumed if not specified.
		if ( '7bit' != $encoding ) { // WPCS: Loose comparison ok .
			$result .= $this->headerLine( 'Content-Transfer-Encoding', $encoding );
		}
		$result .= $this->LE; // @codingStandardsIgnoreLine.

		return $result;
	}

	/**
	 * Return the end of a message boundary.
	 *
	 * @access protected
	 * @param string $boundary .
	 * @return string
	 */
	protected function endBoundary( $boundary ) { // @codingStandardsIgnoreLine.
		return $this->LE . '--' . $boundary . '--' . $this->LE; // @codingStandardsIgnoreLine.
	}

	/**
	 * Set the message type.
	 * PHPMailer only supports some preset message types, not arbitrary MIME structures.
	 *
	 * @access protected
	 * @return void
	 */
	protected function setMessageType() { // @codingStandardsIgnoreLine.
		$type = array();
		if ( $this->alternativeExists() ) {
			$type[] = 'alt';
		}
		if ( $this->inlineImageExists() ) {
			$type[] = 'inline';
		}
		if ( $this->attachmentExists() ) {
			$type[] = 'attach';
		}
		$this->message_type = implode( '_', $type );
		if ( '' == $this->message_type ) { // WPCS: Loose comparison ok .
			// The 'plain' message_type refers to the message having a single body element, not that it is plain-text.
			$this->message_type = 'plain';
		}
	}

	/**
	 * Format a header line.
	 *
	 * @access public
	 * @param string $name .
	 * @param string $value .
	 * @return string
	 */
	public function headerLine( $name, $value ) { // @codingStandardsIgnoreLine.
		return $name . ': ' . $value . $this->LE; // @codingStandardsIgnoreLine.
	}

	/**
	 * Return a formatted mail line.
	 *
	 * @access public
	 * @param string $value .
	 * @return string
	 */
	public function textLine( $value ) { // @codingStandardsIgnoreLine.
		return $value . $this->LE; // @codingStandardsIgnoreLine.
	}

	/**
	 * Add an attachment from a path on the filesystem.
	 * Never use a user-supplied path to a file!
	 * Returns false if the file could not be found or read.
	 *
	 * @param string $path Path to the attachment.
	 * @param string $name Overrides the attachment name.
	 * @param string $encoding File encoding .
	 * @param string $type File extension (MIME) type.
	 * @param string $disposition Disposition to use .
	 * @throws phpmailerException .
	 * @return boolean
	 */
	public function addAttachment( $path, $name = '', $encoding = 'base64', $type = '', $disposition = 'attachment' ) { // @codingStandardsIgnoreLine.
		try {
			if ( ! @is_file( $path ) ) { //@codingStandardsIgnoreLine.
				throw new phpmailerException( $this->lang( 'file_access' ) . $path, self::STOP_CONTINUE );
			}

			// If a MIME type is not specified, try to work it out from the file name.
			if ( '' == $type ) { // WPCS: Loose comparison ok .
				$type = self::filenameToType( $path );
			}

			$filename = basename( $path );
			if ( '' == $name ) { // WPCS: Loose comparison ok .
				$name = $filename;
			}

			$this->attachment[] = array(
				0 => $path,
				1 => $filename,
				2 => $name,
				3 => $encoding,
				4 => $type,
				5 => false, // isStringAttachment.
				6 => $disposition,
				7 => 0,
			);

		} catch ( phpmailerException $exc ) {
			$this->setError( $exc->getMessage() );
			$this->edebug( $exc->getMessage() );
			if ( $this->exceptions ) {
				throw $exc;
			}
			return false;
		}
		return true;
	}

	/**
	 * Return the array of attachments.
	 *
	 * @return array
	 */
	public function getAttachments() { // @codingStandardsIgnoreLine.
		return $this->attachment;
	}

	/**
	 * Attach all file, string, and binary attachments to the message.
	 * Returns an empty string on failure.
	 *
	 * @access protected
	 * @param string $disposition_type .
	 * @param string $boundary .
	 * @return string
	 */
	protected function attachAll( $disposition_type, $boundary ) { // @codingStandardsIgnoreLine.
		// Return text of body .
		$mime    = array();
		$cidUniq = array(); // @codingStandardsIgnoreLine.
		$incl    = array();

		// Add all attachments .
		foreach ( $this->attachment as $attachment ) {
			// Check if it is a valid disposition_filter .
			if ( $attachment[6] == $disposition_type ) { // WPCS: Loose comparison ok .
				// Check for string attachment .
				$string  = '';
				$path    = '';
				$bString = $attachment[5]; // @codingStandardsIgnoreLine.
				if ( $bString ) { // @codingStandardsIgnoreLine.
					$string = $attachment[0];
				} else {
					$path = $attachment[0];
				}

				$inclhash = md5( serialize( $attachment ) ); // @codingStandardsIgnoreLine.
				if ( in_array( $inclhash, $incl ) ) { // @codingStandardsIgnoreLine.
					continue;
				}
				$incl[]      = $inclhash;
				$name        = $attachment[2];
				$encoding    = $attachment[3];
				$type        = $attachment[4];
				$disposition = $attachment[6];
				$cid         = $attachment[7];
				if ( 'inline' == $disposition  && array_key_exists( $cid, $cidUniq ) ) { // @codingStandardsIgnoreLine.
					continue;
				}
				$cidUniq[ $cid ] = true; // @codingStandardsIgnoreLine.

				$mime[] = sprintf( '--%s%s', $boundary, $this->LE ); // @codingStandardsIgnoreLine.
				// Only include a filename property if we have one .
				if ( ! empty( $name ) ) {
					$mime[] = sprintf(
						'Content-Type: %s; name="%s"%s',
						$type,
						$this->encodeHeader( $this->secureHeader( $name ) ),
						$this->LE // @codingStandardsIgnoreLine.
					);
				} else {
					$mime[] = sprintf(
						'Content-Type: %s%s',
						$type,
						$this->LE // @codingStandardsIgnoreLine.
					);
				}
				// RFC1341 part 5 says 7bit is assumed if not specified .
				if ( '7bit' != $encoding ) { // WPCS: Loose comparison ok .
					$mime[] = sprintf( 'Content-Transfer-Encoding: %s%s', $encoding, $this->LE ); // @codingStandardsIgnoreLine.
				}

				if ( 'inline' == $disposition ) { // WPCS: Loose comparison ok .
					$mime[] = sprintf( 'Content-ID: <%s>%s', $cid, $this->LE ); // @codingStandardsIgnoreLine.
				}

				// If a filename contains any of these chars, it should be quoted,
				// but not otherwise: RFC2183 & RFC2045 5.1
				// Fixes a warning in IETF's msglint MIME checker
				// Allow for bypassing the Content-Disposition header totally.
				if ( ! ( empty( $disposition ) ) ) {
					$encoded_name = $this->encodeHeader( $this->secureHeader( $name ) );
					if ( preg_match( '/[ \(\)<>@,;:\\"\/\[\]\?=]/', $encoded_name ) ) {
						$mime[] = sprintf(
							'Content-Disposition: %s; filename="%s"%s',
							$disposition,
							$encoded_name,
							$this->LE . $this->LE // @codingStandardsIgnoreLine.
						);
					} else {
						if ( ! empty( $encoded_name ) ) {
							$mime[] = sprintf(
								'Content-Disposition: %s; filename=%s%s',
								$disposition,
								$encoded_name,
								$this->LE . $this->LE // @codingStandardsIgnoreLine.
							);
						} else {
							$mime[] = sprintf(
								'Content-Disposition: %s%s',
								$disposition,
								$this->LE . $this->LE // @codingStandardsIgnoreLine.
							);
						}
					}
				} else {
					$mime[] = $this->LE; // @codingStandardsIgnoreLine.
				}

				// Encode as string attachment .
				if ( $bString ) { // @codingStandardsIgnoreLine.
					$mime[] = $this->encodeString( $string, $encoding );
					if ( $this->isError() ) {
						return '';
					}
					$mime[] = $this->LE . $this->LE; // @codingStandardsIgnoreLine.
				} else {
					$mime[] = $this->encodeFile( $path, $encoding );
					if ( $this->isError() ) {
						return '';
					}
					$mime[] = $this->LE . $this->LE; // @codingStandardsIgnoreLine.
				}
			}
		}

		$mime[] = sprintf( '--%s--%s', $boundary, $this->LE ); // @codingStandardsIgnoreLine.

		return implode( '', $mime );
	}

	/**
	 * Encode a file attachment in requested format.
	 * Returns an empty string on failure.
	 *
	 * @param string $path The full path to the file .
	 * @param string $encoding The encoding to use; one of 'base64', '7bit', '8bit', 'binary', 'quoted-printable' .
	 * @throws phpmailerException .
	 * @access protected
	 * @return string
	 */
	protected function encodeFile( $path, $encoding = 'base64' ) { // @codingStandardsIgnoreLine.
		try {
			if ( ! is_readable( $path ) ) {
				throw new phpmailerException( $this->lang( 'file_open' ) . $path, self::STOP_CONTINUE );
			}
			$magic_quotes = get_magic_quotes_runtime();
			if ( $magic_quotes ) {
				if ( version_compare( PHP_VERSION, '5.3.0', '<' ) ) {
					set_magic_quotes_runtime( false ); // @codingStandardsIgnoreLine.
				} else {
					// Doesn't exist in PHP 5.4, but we don't need to check because
					// get_magic_quotes_runtime always returns false in 5.4+
					// so it will never get here .
					ini_set( 'magic_quotes_runtime', false ); // @codingStandardsIgnoreLine.
				}
			}
			$file_buffer = file_get_contents( $path ); // @codingStandardsIgnoreLine.
			$file_buffer = $this->encodeString( $file_buffer, $encoding );
			if ( $magic_quotes ) {
				if ( version_compare( PHP_VERSION, '5.3.0', '<' ) ) {
					set_magic_quotes_runtime( $magic_quotes ); // @codingStandardsIgnoreLine.
				} else {
					ini_set( 'magic_quotes_runtime', $magic_quotes ); // @codingStandardsIgnoreLine.
				}
			}
			return $file_buffer;
		} catch ( Exception $exc ) {
			$this->setError( $exc->getMessage() );
			return '';
		}
	}

	/**
	 * Encode a string in requested format.
	 * Returns an empty string on failure.
	 *
	 * @param string $str The text to encode .
	 * @param string $encoding The encoding to use; one of 'base64', '7bit', '8bit', 'binary', 'quoted-printable' .
	 * @access public
	 * @return string
	 */
	public function encodeString( $str, $encoding = 'base64' ) { // @codingStandardsIgnoreLine.
		$encoded = '';
		switch ( strtolower( $encoding ) ) {
			case 'base64':
				$encoded = chunk_split( base64_encode( $str ), 76, $this->LE ); // @codingStandardsIgnoreLine.
				break;
			case '7bit':
			case '8bit':
				$encoded = $this->fixEOL( $str );
				// Make sure it ends with a line break .
				if ( substr( $encoded, -( strlen( $this->LE ) ) ) != $this->LE ) { // @codingStandardsIgnoreLine.
					$encoded .= $this->LE; // @codingStandardsIgnoreLine.
				}
				break;
			case 'binary':
				$encoded = $str;
				break;
			case 'quoted-printable':
				$encoded = $this->encodeQP( $str );
				break;
			default:
				$this->setError( $this->lang( 'encoding' ) . $encoding );
				break;
		}
		return $encoded;
	}

	/**
	 * Encode a header string optimally.
	 * Picks shortest of Q, B, quoted-printable or none.
	 *
	 * @access public
	 * @param string $str .
	 * @param string $position .
	 * @return string
	 */
	public function encodeHeader( $str, $position = 'text' ) { // @codingStandardsIgnoreLine.
		$matchcount = 0;
		switch ( strtolower( $position ) ) {
			case 'phrase':
				if ( ! preg_match( '/[\200-\377]/', $str ) ) {
					// Can't use addslashes as we don't know the value of magic_quotes_sybase .
					$encoded = addcslashes( $str, "\0..\37\177\\\"" );
					if ( ( $str == $encoded ) && ! preg_match( '/[^A-Za-z0-9!#$%&\'*+\/=?^_`{|}~ -]/', $str ) ) { // WPCS: Loose comparison ok .
						return ( $encoded );
					} else {
						return ( "\"$encoded\"" );
					}
				}
				$matchcount = preg_match_all( '/[^\040\041\043-\133\135-\176]/', $str, $matches );
				break;
			// @noinspection PhpMissingBreakStatementInspection .
			case 'comment':
				$matchcount = preg_match_all( '/[()"]/', $str, $matches );
				// Intentional fall-through .
			case 'text':
			default:
				$matchcount += preg_match_all( '/[\000-\010\013\014\016-\037\177-\377]/', $str, $matches );
				break;
		}

		// There are no chars that need encoding .
		if ( 0 == $matchcount ) { // WPCS: Loose comparison ok .
			return ( $str );
		}

		$maxlen = 75 - 7 - strlen( $this->charset );
		// Try to select the encoding which should produce the shortest output .
		if ( $matchcount > strlen( $str ) / 3 ) {
			// More than a third of the content will need encoding, so B encoding will be most efficient .
			$encoding = 'B';
			if ( function_exists( 'mb_strlen' ) && $this->hasMultiBytes( $str ) ) {
				// Use a custom function which correctly encodes and wraps long
				// multibyte strings without breaking lines within a character .
				$encoded = $this->base64EncodeWrapMB( $str, "\n" );
			} else {
				$encoded = base64_encode( $str );
				$maxlen -= $maxlen % 4;
				$encoded = trim( chunk_split( $encoded, $maxlen, "\n" ) );
			}
		} else {
			$encoding = 'Q';
			$encoded  = $this->encodeQ( $str, $position );
			$encoded  = $this->wrapText( $encoded, $maxlen, true );
			$encoded  = str_replace( '=' . self::CRLF, "\n", trim( $encoded ) );
		}

		$encoded = preg_replace( '/^(.*)$/m', ' =?' . $this->charset . "?$encoding?\\1?=", $encoded );
		$encoded = trim( str_replace( "\n", $this->LE, $encoded ) ); // @codingStandardsIgnoreLine.

		return $encoded;
	}

	/**
	 * Check if a string contains multi-byte characters.
	 *
	 * @access public
	 * @param string $str multi-byte text to wrap encode .
	 * @return boolean
	 */
	public function hasMultiBytes( $str ) { // @codingStandardsIgnoreLine.
		if ( function_exists( 'mb_strlen' ) ) {
			return ( strlen( $str ) > mb_strlen( $str, $this->charset ) );
		} else {
			return false;
		}
	}

	/**
	 * Does a string contain any 8-bit chars (in any charset)?
	 *
	 * @param string $text .
	 * @return boolean
	 */
	public function has8bitChars( $text ) { // @codingStandardsIgnoreLine.
		return (boolean) preg_match( '/[\x80-\xFF]/', $text );
	}

	/**
	 * Encode and wrap long multibyte strings for mail headers
	 * without breaking lines within a character.
	 * Adapted from a function by paravoid
	 *
	 * @access public
	 * @param string $str multi-byte text to wrap encode .
	 * @param string $linebreak string to use as linefeed/end-of-line .
	 * @return string
	 */
	public function base64EncodeWrapMB( $str, $linebreak = null ) { // @codingStandardsIgnoreLine.
		$start   = '=?' . $this->charset . '?B?';
		$end     = '?=';
		$encoded = '';
		if ( null === $linebreak ) {
			$linebreak = $this->LE; // @codingStandardsIgnoreLine.
		}

		$mb_length = mb_strlen( $str, $this->charset );
		// Each line must have length <= 75, including $start and $end .
		$length = 75 - strlen( $start ) - strlen( $end );
		// Average multi-byte ratio .
		$ratio = $mb_length / strlen( $str );
		// Base64 has a 4:3 ratio .
		$avgLength = floor( $length * $ratio * .75 ); // @codingStandardsIgnoreLine.

		for ( $i = 0; $i < $mb_length; $i += $offset ) {
			$lookBack = 0; // @codingStandardsIgnoreLine.
			do {
				$offset = $avgLength - $lookBack; // @codingStandardsIgnoreLine.
				$chunk  = mb_substr( $str, $i, $offset, $this->charset );
				$chunk  = base64_encode( $chunk );
				$lookBack++; // @codingStandardsIgnoreLine.
			} while ( strlen( $chunk ) > $length ); // @codingStandardsIgnoreLine.
			$encoded .= $chunk . $linebreak;
		}

		// Chomp the last linefeed .
		$encoded = substr( $encoded, 0, -strlen( $linebreak ) );
		return $encoded;
	}

	/**
	 * Encode a string in quoted-printable format.
	 * According to RFC2045 section 6.7.
	 *
	 * @access public
	 * @param string  $string The text to encode .
	 * @param integer $line_max Number of chars allowed on a line before wrapping .
	 * @return string
	 */
	public function encodeQP( $string, $line_max = 76 ) { // @codingStandardsIgnoreLine.
		// Use native function if it's available (>= PHP5.3) .
		if ( function_exists( 'quoted_printable_encode' ) ) {
			return quoted_printable_encode( $string );
		}
		// Fall back to a pure PHP implementation .
		$string = str_replace(
			array( '%20', '%0D%0A.', '%0D%0A', '%' ),
			array( ' ', "\r\n=2E", "\r\n", '=' ),
			rawurlencode( $string )
		);
		return preg_replace( '/[^\r\n]{' . ( $line_max - 3 ) . '}[^=\r\n]{2}/', "$0=\r\n", $string );
	}

	/**
	 * Backward compatibility wrapper for an old QP encoding function that was removed.
	 *
	 * @see PHPMailer::encodeQP()
	 * @access public
	 * @param string  $string .
	 * @param integer $line_max .
	 * @param boolean $space_conv .
	 * @return string
	 * @deprecated Use encodeQP instead.
	 */
	public function encodeQPphp( // @codingStandardsIgnoreLine.
		$string,
		$line_max = 76,
		// @noinspection PhpUnusedParameterInspection .
		$space_conv = false
		) {
		return $this->encodeQP( $string, $line_max );
	}

	/**
	 * Encode a string using Q encoding.
	 *
	 * @param string $str the text to encode .
	 * @param string $position Where the text is going to be used, see the RFC for what that means .
	 * @access public
	 * @return string
	 */
	public function encodeQ( $str, $position = 'text' ) { // @codingStandardsIgnoreLine.
		// There should not be any EOL in the string .
		$pattern = '';
		$encoded = str_replace( array( "\r", "\n" ), '', $str );
		switch ( strtolower( $position ) ) {
			case 'phrase':
				// RFC 2047 section 5.3 .
				$pattern = '^A-Za-z0-9!*+\/ -';
				break;
			case 'comment':
				$pattern = '\(\)"';
				// intentional fall-through
				// for this reason we build the $pattern without including delimiters and [] .
			case 'text':
			default:
				// RFC 2047 section 5.1
				// Replace every high ascii, control, =, ? and _ characters .
				$pattern = '\000-\011\013\014\016-\037\075\077\137\177-\377' . $pattern;
				break;
		}
		$matches = array();
		if ( preg_match_all( "/[{$pattern}]/", $encoded, $matches ) ) {
			// If the string contains an '=', make sure it's the first thing we replace
			// so as to avoid double-encoding .
			$eqkey = array_search( '=', $matches[0] ); // @codingStandardsIgnoreLine.
			if ( false !== $eqkey ) {
				unset( $matches[0][ $eqkey ] );
				array_unshift( $matches[0], '=' );
			}
			foreach ( array_unique( $matches[0] ) as $char ) {
				$encoded = str_replace( $char, '=' . sprintf( '%02X', ord( $char ) ), $encoded );
			}
		}
		// Replace every spaces to _ (more readable than =20) .
		return str_replace( ' ', '_', $encoded );
	}

	/**
	 * Add a string or binary attachment (non-filesystem).
	 * This method can be used to attach ascii or binary data,
	 * such as a BLOB record from a database.
	 *
	 * @param string $string String attachment data.
	 * @param string $filename Name of the attachment.
	 * @param string $encoding File encoding .
	 * @param string $type File extension (MIME) type.
	 * @param string $disposition Disposition to use .
	 * @return void
	 */
	public function addStringAttachment( // @codingStandardsIgnoreLine.
		$string,
		$filename,
		$encoding = 'base64',
		$type = '',
		$disposition = 'attachment'
	) {
		// If a MIME type is not specified, try to work it out from the file name .
		if ( '' == $type ) { // WPCS: Loose comparison ok .
			$type = self::filenameToType( $filename );
		}
		// Append to $attachment array .
		$this->attachment[] = array(
			0 => $string,
			1 => $filename,
			2 => basename( $filename ),
			3 => $encoding,
			4 => $type,
			5 => true, // isStringAttachment .
			6 => $disposition,
			7 => 0,
		);
	}

	/**
	 * Add an embedded (inline) attachment from a file.
	 *
	 * @param string $path Path to the attachment.
	 * @param string $cid Content ID of the attachment; Use this to reference
	 *        the content when using an embedded image in HTML.
	 * @param string $name Overrides the attachment name.
	 * @param string $encoding File encoding .
	 * @param string $type File MIME type.
	 * @param string $disposition Disposition to use .
	 * @return boolean True on successfully adding an attachment
	 */
	public function addEmbeddedImage( $path, $cid, $name = '', $encoding = 'base64', $type = '', $disposition = 'inline' ) { // @codingStandardsIgnoreLine.
		if ( ! @is_file( $path ) ) { // @codingStandardsIgnoreLine.
			$this->setError( $this->lang( 'file_access' ) . $path );
			return false;
		}

		// If a MIME type is not specified, try to work it out from the file name .
		if ( '' == $type ) { // WPCS: Loose comparison ok .
			$type = self::filenameToType( $path );
		}

		$filename = basename( $path );
		if ( '' == $name ) { // WPCS: Loose comparison ok .
			$name = $filename;
		}

		// Append to $attachment array .
		$this->attachment[] = array(
			0 => $path,
			1 => $filename,
			2 => $name,
			3 => $encoding,
			4 => $type,
			5 => false, // isStringAttachment .
			6 => $disposition,
			7 => $cid,
		);
		return true;
	}

	/**
	 * Add an embedded stringified attachment.
	 *
	 * @param string $string The attachment binary data.
	 * @param string $cid Content ID of the attachment; Use this to reference
	 *        the content when using an embedded image in HTML.
	 * @param string $name .
	 * @param string $encoding File encoding .
	 * @param string $type MIME type.
	 * @param string $disposition Disposition to use .
	 * @return boolean True on successfully adding an attachment
	 */
	public function addStringEmbeddedImage( // @codingStandardsIgnoreLine.
		$string,
		$cid,
		$name = '',
		$encoding = 'base64',
		$type = '',
		$disposition = 'inline'
	) {
		// If a MIME type is not specified, try to work it out from the name .
		if ( '' == $type && ! empty( $name ) ) { // WPCS: Loose comparison ok .
			$type = self::filenameToType( $name );
		}

		// Append to $attachment array .
		$this->attachment[] = array(
			0 => $string,
			1 => $name,
			2 => $name,
			3 => $encoding,
			4 => $type,
			5 => true, // isStringAttachment .
			6 => $disposition,
			7 => $cid,
		);
		return true;
	}

	/**
	 * Check if an inline attachment is present.
	 *
	 * @access public
	 * @return boolean
	 */
	public function inlineImageExists() { // @codingStandardsIgnoreLine.
		foreach ( $this->attachment as $attachment ) {
			if ( 'inline' == $attachment[6] ) { // WPCS: Loose comparison ok .
				return true;
			}
		}
		return false;
	}

	/**
	 * Check if an attachment (non-inline) is present.
	 *
	 * @return boolean
	 */
	public function attachmentExists() { // @codingStandardsIgnoreLine.
		foreach ( $this->attachment as $attachment ) {
			if ( 'attachment' == $attachment[6] ) { // WPCS: Loose comparison ok .
				return true;
			}
		}
		return false;
	}

	/**
	 * Check if this message has an alternative body set.
	 *
	 * @return boolean
	 */
	public function alternativeExists() { // @codingStandardsIgnoreLine.
		return ! empty( $this->altbody );
	}

	/**
	 * Clear queued addresses of given kind.
	 *
	 * @access protected
	 * @param string $kind 'to', 'cc', or 'bcc' .
	 * @return void
	 */
	public function clearQueuedAddresses( $kind ) { // @codingStandardsIgnoreLine.
		$RecipientsQueue = $this->RecipientsQueue; // @codingStandardsIgnoreLine.
		foreach ( $RecipientsQueue as $address => $params ) { // @codingStandardsIgnoreLine.
			if ( $params[0] == $kind ) { // WPCS: Loose comparison ok .
				unset( $this->RecipientsQueue[ $address ] ); // @codingStandardsIgnoreLine.
			}
		}
	}

	/**
	 * Clear all To recipients.
	 *
	 * @return void
	 */
	public function clearAddresses() { // @codingStandardsIgnoreLine.
		foreach ( $this->to as $to ) {
			unset( $this->all_recipients[ strtolower( $to[0] ) ] );
		}
		$this->to = array();
		$this->clearQueuedAddresses( 'to' );
	}

	/**
	 * Clear all CC recipients.
	 *
	 * @return void
	 */
	public function clearCCs() { // @codingStandardsIgnoreLine.
		foreach ( $this->cc as $cc ) {
			unset( $this->all_recipients[ strtolower( $cc[0] ) ] );
		}
		$this->cc = array();
		$this->clearQueuedAddresses( 'cc' );
	}

	/**
	 * Clear all BCC recipients.
	 *
	 * @return void
	 */
	public function clearBCCs() { // @codingStandardsIgnoreLine.
		foreach ( $this->bcc as $bcc ) {
			unset( $this->all_recipients[ strtolower( $bcc[0] ) ] );
		}
		$this->bcc = array();
		$this->clearQueuedAddresses( 'bcc' );
	}

	/**
	 * Clear all ReplyTo recipients.
	 *
	 * @return void
	 */
	public function clearReplyTos() { // @codingStandardsIgnoreLine.
		$this->ReplyTo      = array(); // @codingStandardsIgnoreLine.
		$this->ReplyToQueue = array(); // @codingStandardsIgnoreLine.
	}

	/**
	 * Clear all recipient types.
	 *
	 * @return void
	 */
	public function clearAllRecipients() { // @codingStandardsIgnoreLine.
		$this->to              = array();
		$this->cc              = array();
		$this->bcc             = array();
		$this->all_recipients  = array();
		$this->RecipientsQueue = array(); // @codingStandardsIgnoreLine.
	}

	/**
	 * Clear all filesystem, string, and binary attachments.
	 *
	 * @return void
	 */
	public function clearAttachments() { // @codingStandardsIgnoreLine.
		$this->attachment = array();
	}

	/**
	 * Clear all custom headers.
	 *
	 * @return void
	 */
	public function clearCustomHeaders() { // @codingStandardsIgnoreLine.
		$this->CustomHeader = array(); // @codingStandardsIgnoreLine.
	}

	/**
	 * Add an error message to the error container.
	 *
	 * @access protected
	 * @param string $msg .
	 * @return void
	 */
	protected function setError( $msg ) { // @codingStandardsIgnoreLine.
		$this->error_count++;
		if ( 'smtp' == $this->mailer && ! is_null( $this->smtp ) ) { // WPCS: Loose comparison ok .
			$lasterror = $this->smtp->getError();
			if ( ! empty( $lasterror['error'] ) ) {
				$msg .= $this->lang( 'smtp_error' ) . $lasterror['error'];
				if ( ! empty( $lasterror['detail'] ) ) {
					$msg .= ' Detail: ' . $lasterror['detail'];
				}
				if ( ! empty( $lasterror['smtp_code'] ) ) {
					$msg .= ' SMTP code: ' . $lasterror['smtp_code'];
				}
				if ( ! empty( $lasterror['smtp_code_ex'] ) ) {
					$msg .= ' Additional SMTP info: ' . $lasterror['smtp_code_ex'];
				}
			}
		}
		$this->errorinfo = $msg;
	}

	/**
	 * Return an RFC 822 formatted date.
	 *
	 * @access public
	 * @return string
	 * @static
	 */
	public static function rfcDate() { // @codingStandardsIgnoreLine.
		// Set the time zone to whatever the default is to avoid 500 errors
		// Will default to UTC if it's not set properly in php.ini .
		date_default_timezone_set( @date_default_timezone_get() ); //@codingStandardsIgnoreLine.
		return date( 'D, j M Y H:i:s O' );
	}

	/**
	 * Get the server hostname.
	 * Returns 'localhost.localdomain' if unknown.
	 *
	 * @access protected
	 * @return string
	 */
	protected function serverHostname() { // @codingStandardsIgnoreLine.
		$result = 'localhost.localdomain';
		if ( ! empty( $this->Hostname ) ) { // @codingStandardsIgnoreLine.
			$result = $this->Hostname; // @codingStandardsIgnoreLine.
		} elseif ( isset( $_SERVER ) && array_key_exists( 'SERVER_NAME', $_SERVER ) && ! empty( $_SERVER['SERVER_NAME'] ) ) { // WPCS: input var ok .
			$result = wp_unslash( $_SERVER['SERVER_NAME'] ); // WPCS: input var ok ,sanitization ok .
		} elseif ( function_exists( 'gethostname' ) && gethostname() !== false ) {
			$result = gethostname();
		} elseif ( php_uname( 'n' ) !== false ) {
			$result = php_uname( 'n' );
		}
		return $result;
	}

	/**
	 * Get an error message in the current language.
	 *
	 * @access protected
	 * @param string $key .
	 * @return string
	 */
	protected function lang( $key ) {
		if ( count( $this->language ) < 1 ) {
			$this->setLanguage( 'en' ); // set the default language .
		}

		if ( array_key_exists( $key, $this->language ) ) {
			if ( 'smtp_connect_failed' == $key ) { // WPCS: Loose comparison ok .
				// Include a link to troubleshooting docs on SMTP connection failure
				// this is by far the biggest cause of support questions
				// but it's usually not PHPMailer's fault.
				return $this->language[ $key ] . ' https://github.com/PHPMailer/PHPMailer/wiki/Troubleshooting';
			}
			return $this->language[ $key ];
		} else {
			// Return the key as a fallback .
			return $key;
		}
	}

	/**
	 * Check if an error occurred.
	 *
	 * @access public
	 * @return boolean True if an error did occur.
	 */
	public function isError() { // @codingStandardsIgnoreLine.
		return ( $this->error_count > 0 );
	}

	/**
	 * Ensure consistent line endings in a string.
	 * Changes every end of line from CRLF, CR or LF to $this->LE.
	 *
	 * @access public
	 * @param string $str String to fixEOL .
	 * @return string
	 */
	public function fixEOL( $str ) { // @codingStandardsIgnoreLine.
		// Normalise to \n .
		$nstr = str_replace( array( "\r\n", "\r" ), "\n", $str );
		// Now convert LE as needed .
		if ( "\n" !== $this->LE ) { // @codingStandardsIgnoreLine.
			$nstr = str_replace( "\n", $this->LE, $nstr ); // @codingStandardsIgnoreLine.
		}
		return $nstr;
	}

	/**
	 * Add a custom header.
	 * $name value can be overloaded to contain
	 * both header name and value (name:value)
	 *
	 * @access public
	 * @param string $name Custom header name .
	 * @param string $value Header value .
	 * @return void
	 */
	public function addCustomHeader( $name, $value = null ) { // @codingStandardsIgnoreLine.
		if ( null === $value ) {
			// Value passed in as name:value .
			$this->CustomHeader[] = explode( ':', $name, 2 ); // @codingStandardsIgnoreLine.
		} else {
			$this->CustomHeader[] = array( $name, $value ); // @codingStandardsIgnoreLine.
		}
	}

	/**
	 * Returns all custom headers.
	 *
	 * @return array
	 */
	public function getCustomHeaders() { // @codingStandardsIgnoreLine.
		return $this->CustomHeader; // @codingStandardsIgnoreLine.
	}

	/**
	 * Create a message body from an HTML string.
	 * Automatically inlines images and creates a plain-text version by converting the HTML,
	 * overwriting any existing values in Body and altbody.
	 *
	 * @access public
	 * @param string           $message HTML message string .
	 * @param string           $basedir Absolute path to a base directory to prepend to relative paths to images .
	 * @param boolean|callable $advanced Whether to use the internal HTML to text converter .
	 *    or your own custom converter @see PHPMailer::html2text() .
	 * @return string $message The transformed message Body .
	 */
	public function msgHTML( $message, $basedir = '', $advanced = false ) { // @codingStandardsIgnoreLine.
		preg_match_all( '/(src|background)=["\'](.*)["\']/Ui', $message, $images );
		if ( array_key_exists( 2, $images ) ) {
			if ( strlen( $basedir ) > 1 && substr( $basedir, -1 ) != '/' ) { // @codingStandardsIgnoreLine.
				// Ensure $basedir has a trailing .
				$basedir .= '/';
			}
			foreach ( $images[2] as $imgindex => $url ) {
				// Convert data URIs into embedded images .
				if ( preg_match( '#^data:(image[^;,]*)(;base64)?,#', $url, $match ) ) {
					$data = substr( $url, strpos( $url, ',' ) );
					if ( $match[2] ) {
						$data = base64_decode( $data );
					} else {
						$data = rawurldecode( $data );
					}
					$cid = md5( $url ) . '@phpmailer.0';
					if ( $this->addStringEmbeddedImage( $data, $cid, 'embed' . $imgindex, 'base64', $match[1] ) ) {
						$message = str_replace(
							$images[0][ $imgindex ],
							$images[1][ $imgindex ] . '="cid:' . $cid . '"',
							$message
						);
					}
					continue;
				}
				if (
					// Only process relative URLs if a basedir is provided (i.e. no absolute local paths) .
					! empty( $basedir )
					// Ignore URLs containing parent dir traversal (..) .
					&& ( strpos( $url, '..' ) === false )
					// Do not change urls that are already inline images .
					&& substr( $url, 0, 4 ) !== 'cid:'
					// Do not change absolute URLs, including anonymous protocol .
					&& ! preg_match( '#^[a-z][a-z0-9+.-]*:?//#i', $url )
				) {
					$filename  = basename( $url );
					$directory = dirname( $url );
					if ( '.' == $directory ) { // WPCS: Loose comparison ok .
						$directory = '';
					}
					$cid = md5( $url ) . '@phpmailer.0';
					if ( strlen( $directory ) > 1 && substr( $directory, -1 ) != '/' ) { // @codingStandardsIgnoreLine.
						$directory .= '/';
					}
					if ( $this->addEmbeddedImage(
						$basedir . $directory . $filename,
						$cid,
						$filename,
						'base64',
						self::_mime_types( (string) self::mb_pathinfo( $filename, PATHINFO_EXTENSION ) )
					)
					) {
						$message = preg_replace(
							'/' . $images[1][ $imgindex ] . '=["\']' . preg_quote( $url, '/' ) . '["\']/Ui',
							$images[1][ $imgindex ] . '="cid:' . $cid . '"',
							$message
						);
					}
				}
			}
		}
		$this->isHTML( true );
		// Convert all message body line breaks to CRLF, makes quoted-printable encoding work much better .
		$this->body    = $this->normalizeBreaks( $message );
		$this->altbody = $this->normalizeBreaks( $this->html2text( $message, $advanced ) );
		if ( ! $this->alternativeExists() ) {
			$this->altbody = 'To view this email message, open it in a program that understands HTML!' .
				self::CRLF . self::CRLF;
		}
		return $this->body;
	}

	/**
	 * Convert an HTML string into plain text.
	 * This is used by msgHTML().
	 *
	 * @param string           $html The HTML text to convert .
	 * @param boolean|callable $advanced Any boolean value to use the internal converter,
	 *   or provide your own callable for custom conversion.
	 * @return string
	 */
	public function html2text( $html, $advanced = false ) {
		if ( is_callable( $advanced ) ) {
			return call_user_func( $advanced, $html );
		}
		return html_entity_decode(
			trim( strip_tags( preg_replace( '/<(head|title|style|script)[^>]*>.*?<\/\\1>/si', '', $html ) ) ),
			ENT_QUOTES,
			$this->charset
		);
	}

	/**
	 * Get the MIME type for a file extension.
	 *
	 * @param string $ext File extension .
	 * @access public
	 * @return string MIME type of file.
	 * @static
	 */
	public static function _mime_types( $ext = '' ) { // @codingStandardsIgnoreLine .
		$mimes = array(
			'xl'    => 'application/excel',
			'js'    => 'application/javascript',
			'hqx'   => 'application/mac-binhex40',
			'cpt'   => 'application/mac-compactpro',
			'bin'   => 'application/macbinary',
			'doc'   => 'application/msword',
			'word'  => 'application/msword',
			'xlsx'  => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'xltx'  => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
			'potx'  => 'application/vnd.openxmlformats-officedocument.presentationml.template',
			'ppsx'  => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
			'pptx'  => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
			'sldx'  => 'application/vnd.openxmlformats-officedocument.presentationml.slide',
			'docx'  => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'dotx'  => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
			'xlam'  => 'application/vnd.ms-excel.addin.macroEnabled.12',
			'xlsb'  => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
			'class' => 'application/octet-stream',
			'dll'   => 'application/octet-stream',
			'dms'   => 'application/octet-stream',
			'exe'   => 'application/octet-stream',
			'lha'   => 'application/octet-stream',
			'lzh'   => 'application/octet-stream',
			'psd'   => 'application/octet-stream',
			'sea'   => 'application/octet-stream',
			'so'    => 'application/octet-stream',
			'oda'   => 'application/oda',
			'pdf'   => 'application/pdf',
			'ai'    => 'application/postscript',
			'eps'   => 'application/postscript',
			'ps'    => 'application/postscript',
			'smi'   => 'application/smil',
			'smil'  => 'application/smil',
			'mif'   => 'application/vnd.mif',
			'xls'   => 'application/vnd.ms-excel',
			'ppt'   => 'application/vnd.ms-powerpoint',
			'wbxml' => 'application/vnd.wap.wbxml',
			'wmlc'  => 'application/vnd.wap.wmlc',
			'dcr'   => 'application/x-director',
			'dir'   => 'application/x-director',
			'dxr'   => 'application/x-director',
			'dvi'   => 'application/x-dvi',
			'gtar'  => 'application/x-gtar',
			'php3'  => 'application/x-httpd-php',
			'php4'  => 'application/x-httpd-php',
			'php'   => 'application/x-httpd-php',
			'phtml' => 'application/x-httpd-php',
			'phps'  => 'application/x-httpd-php-source',
			'swf'   => 'application/x-shockwave-flash',
			'sit'   => 'application/x-stuffit',
			'tar'   => 'application/x-tar',
			'tgz'   => 'application/x-tar',
			'xht'   => 'application/xhtml+xml',
			'xhtml' => 'application/xhtml+xml',
			'zip'   => 'application/zip',
			'mid'   => 'audio/midi',
			'midi'  => 'audio/midi',
			'mp2'   => 'audio/mpeg',
			'mp3'   => 'audio/mpeg',
			'mpga'  => 'audio/mpeg',
			'aif'   => 'audio/x-aiff',
			'aifc'  => 'audio/x-aiff',
			'aiff'  => 'audio/x-aiff',
			'ram'   => 'audio/x-pn-realaudio',
			'rm'    => 'audio/x-pn-realaudio',
			'rpm'   => 'audio/x-pn-realaudio-plugin',
			'ra'    => 'audio/x-realaudio',
			'wav'   => 'audio/x-wav',
			'bmp'   => 'image/bmp',
			'gif'   => 'image/gif',
			'jpeg'  => 'image/jpeg',
			'jpe'   => 'image/jpeg',
			'jpg'   => 'image/jpeg',
			'png'   => 'image/png',
			'tiff'  => 'image/tiff',
			'tif'   => 'image/tiff',
			'eml'   => 'message/rfc822',
			'css'   => 'text/css',
			'html'  => 'text/html',
			'htm'   => 'text/html',
			'shtml' => 'text/html',
			'log'   => 'text/plain',
			'text'  => 'text/plain',
			'txt'   => 'text/plain',
			'rtx'   => 'text/richtext',
			'rtf'   => 'text/rtf',
			'vcf'   => 'text/vcard',
			'vcard' => 'text/vcard',
			'xml'   => 'text/xml',
			'xsl'   => 'text/xml',
			'mpeg'  => 'video/mpeg',
			'mpe'   => 'video/mpeg',
			'mpg'   => 'video/mpeg',
			'mov'   => 'video/quicktime',
			'qt'    => 'video/quicktime',
			'rv'    => 'video/vnd.rn-realvideo',
			'avi'   => 'video/x-msvideo',
			'movie' => 'video/x-sgi-movie',
		);
		if ( array_key_exists( strtolower( $ext ), $mimes ) ) {
			return $mimes[ strtolower( $ext ) ];
		}
		return 'application/octet-stream';
	}

	/**
	 * Map a file name to a MIME type.
	 * Defaults to 'application/octet-stream', i.e.. arbitrary binary data.
	 *
	 * @param string $filename A file name or full path, does not need to exist as a file .
	 * @return string
	 * @static
	 */
	public static function filenameToType( $filename ) { // @codingStandardsIgnoreLine.
		// In case the path is a URL, strip any query string before getting extension .
		$qpos = strpos( $filename, '?' );
		if ( false !== $qpos ) {
			$filename = substr( $filename, 0, $qpos );
		}
		$pathinfo = self::mb_pathinfo( $filename );
		return self::_mime_types( $pathinfo['extension'] );
	}

	/**
	 * Multi-byte-safe pathinfo replacement.
	 * Drop-in replacement for pathinfo(), but multibyte-safe, cross-platform-safe, old-version-safe.
	 * Works similarly to the one in PHP >= 5.2.0
	 *
	 * @param string         $path A filename or path, does not need to exist as a file .
	 * @param integer|string $options Either a PATHINFO_* constant,
	 *      or a string name to return only the specified piece, allows 'filename' to work on PHP < 5.2 .
	 * @return string|array
	 * @static
	 */
	public static function mb_pathinfo( $path, $options = null ) {
		$ret      = array(
			'dirname'   => '',
			'basename'  => '',
			'extension' => '',
			'filename'  => '',
		);
		$pathinfo = array();
		if ( preg_match( '%^(.*?)[\\\\/]*(([^/\\\\]*?)(\.([^\.\\\\/]+?)|))[\\\\/\.]*$%im', $path, $pathinfo ) ) {
			if ( array_key_exists( 1, $pathinfo ) ) {
				$ret['dirname'] = $pathinfo[1];
			}
			if ( array_key_exists( 2, $pathinfo ) ) {
				$ret['basename'] = $pathinfo[2];
			}
			if ( array_key_exists( 5, $pathinfo ) ) {
				$ret['extension'] = $pathinfo[5];
			}
			if ( array_key_exists( 3, $pathinfo ) ) {
				$ret['filename'] = $pathinfo[3];
			}
		}
		switch ( $options ) {
			case PATHINFO_DIRNAME:
			case 'dirname':
				return $ret['dirname'];
			case PATHINFO_BASENAME:
			case 'basename':
				return $ret['basename'];
			case PATHINFO_EXTENSION:
			case 'extension':
				return $ret['extension'];
			case PATHINFO_FILENAME:
			case 'filename':
				return $ret['filename'];
			default:
				return $ret;
		}
	}

	/**
	 * Set or reset instance properties.
	 *
	 * @access public
	 * @param string $name The property name to set .
	 * @param mixed  $value The value to set the property to .
	 * @return boolean
	 * @TODO Should this not be using the __set() magic function?
	 */
	public function set( $name, $value = '' ) {
		if ( property_exists( $this, $name ) ) {
			$this->$name = $value;
			return true;
		} else {
			$this->setError( $this->lang( 'variable_set' ) . $name );
			return false;
		}
	}

	/**
	 * Strip newlines to prevent header injection.
	 *
	 * @access public
	 * @param string $str .
	 * @return string
	 */
	public function secureHeader( $str ) { // @codingStandardsIgnoreLine.
		return trim( str_replace( array( "\r", "\n" ), '', $str ) );
	}

	/**
	 * Normalize line breaks in a string.
	 * Converts UNIX LF, Mac CR and Windows CRLF line breaks into a single line break format.
	 * Defaults to CRLF (for message bodies) and preserves consecutive breaks.
	 *
	 * @param string $text .
	 * @param string $breaktype What kind of line break to use, defaults to CRLF .
	 * @return string
	 * @access public
	 * @static
	 */
	public static function normalizeBreaks( $text, $breaktype = "\r\n" ) { // @codingStandardsIgnoreLine.
		return preg_replace( '/(\r\n|\r|\n)/ms', $breaktype, $text );
	}

	/**
	 * Set the public and private key files and password for S/MIME signing.
	 *
	 * @access public
	 * @param string $cert_filename .
	 * @param string $key_filename .
	 * @param string $key_pass Password for private key .
	 * @param string $extracerts_filename Optional path to chain certificate .
	 */
	public function sign( $cert_filename, $key_filename, $key_pass, $extracerts_filename = '' ) {
		$this->sign_cert_file       = $cert_filename;
		$this->sign_key_file        = $key_filename;
		$this->sign_key_pass        = $key_pass;
		$this->sign_extracerts_file = $extracerts_filename;
	}

	/**
	 * Quoted-Printable-encode a DKIM header.
	 *
	 * @access public
	 * @param string $txt .
	 * @return string
	 */
	public function DKIM_QP( $txt ) {  // @codingStandardsIgnoreLine.
		$line = '';
		for ( $i = 0; $i < strlen( $txt ); $i++ ) {  // @codingStandardsIgnoreLine.
			$ord = ord( $txt[ $i ] );
			if ( ( ( 0x21 <= $ord ) && ( $ord <= 0x3A ) ) || 0x3C == $ord || ( ( 0x3E <= $ord ) && ( $ord <= 0x7E ) ) ) { // WPCS: Loose comparison ok .
				$line .= $txt[ $i ];
			} else {
				$line .= '=' . sprintf( '%02X', $ord );
			}
		}
		return $line;
	}

	/**
	 * Generate a DKIM signature.
	 *
	 * @access public
	 * @param string $signHeader .
	 * @throws phpmailerException .
	 * @return string The DKIM signature value
	 */
	public function DKIM_Sign( $signHeader ) { // @codingStandardsIgnoreLine.
		if ( ! defined( 'PKCS7_TEXT' ) ) {
			if ( $this->exceptions ) {
				throw new phpmailerException( $this->lang( 'extension_missing' ) . 'openssl' );
			}
			return '';
		}
		$privKeyStr = ! empty( $this->DKIM_private_string ) ? $this->DKIM_private_string : file_get_contents( $this->DKIM_private ); // @codingStandardsIgnoreLine.
		if ( '' != $this->DKIM_passphrase ) { // @codingStandardsIgnoreLine.
			$privKey = openssl_pkey_get_private( $privKeyStr, $this->DKIM_passphrase ); // @codingStandardsIgnoreLine.
		} else {
			$privKey = openssl_pkey_get_private( $privKeyStr ); // @codingStandardsIgnoreLine.
		}
		// Workaround for missing digest algorithms in old PHP & OpenSSL versions .
		if ( version_compare( PHP_VERSION, '5.3.0' ) >= 0 &&
			in_array( 'sha256WithRSAEncryption', openssl_get_md_methods( true ) ) ) { // @codingStandardsIgnoreLine.
			if ( openssl_sign( $signHeader, $signature, $privKey, 'sha256WithRSAEncryption' ) ) { // @codingStandardsIgnoreLine.
				openssl_pkey_free( $privKey ); // @codingStandardsIgnoreLine.
				return base64_encode( $signature );
			}
		} else {
			$pinfo = openssl_pkey_get_details( $privKey ); // @codingStandardsIgnoreLine.
			$hash  = hash( 'sha256', $signHeader ); // @codingStandardsIgnoreLine.
			$t     = '3031300d060960864801650304020105000420' . $hash;
			$pslen = $pinfo['bits'] / 8 - ( strlen( $t ) / 2 + 3 );
			$eb    = pack( 'H*', '0001' . str_repeat( 'FF', $pslen ) . '00' . $t );

			if ( openssl_private_encrypt( $eb, $signature, $privKey, OPENSSL_NO_PADDING ) ) { // @codingStandardsIgnoreLine.
				openssl_pkey_free( $privKey ); // @codingStandardsIgnoreLine.
				return base64_encode( $signature );
			}
		}
		openssl_pkey_free( $privKey ); // @codingStandardsIgnoreLine.
		return '';
	}

	/**
	 * Generate a DKIM canonicalization header.
	 *
	 * @access public
	 * @param string $signHeader Header .
	 * @return string
	 */
	public function DKIM_HeaderC( $signHeader ) { // @codingStandardsIgnoreLine.
		$signHeader = preg_replace( '/\r\n\s+/', ' ', $signHeader ); // @codingStandardsIgnoreLine.
		$lines      = explode( "\r\n", $signHeader ); // @codingStandardsIgnoreLine.
		foreach ( $lines as $key => $line ) {
			list($heading, $value) = explode( ':', $line, 2 );
			$heading               = strtolower( $heading );
			$value                 = preg_replace( '/\s{2,}/', ' ', $value ); // Compress useless spaces .
			$lines[ $key ]         = $heading . ':' . trim( $value ); // Don't forget to remove WSP around the value .
		}
		$signHeader = implode( "\r\n", $lines ); // @codingStandardsIgnoreLine.
		return $signHeader; // @codingStandardsIgnoreLine.
	}

	/**
	 * Generate a DKIM canonicalization body.
	 *
	 * @access public
	 * @param string $body Message Body .
	 * @return string
	 */
	public function DKIM_BodyC( $body ) { // @codingStandardsIgnoreLine.
		if ( '' == $body ) { // WPCS: Loose comparison ok .
			return "\r\n";
		}
		// stabilize line endings .
		$body = str_replace( "\r\n", "\n", $body );
		$body = str_replace( "\n", "\r\n", $body );
		// END stabilize line endings .
		while ( substr( $body, strlen( $body ) - 4, 4 ) == "\r\n\r\n" ) { // @codingStandardsIgnoreLine.
			$body = substr( $body, 0, strlen( $body ) - 2 );
		}
		return $body;
	}

	/**
	 * Create the DKIM header and body in a new message header.
	 *
	 * @access public
	 * @param string $headers_line Header lines .
	 * @param string $subject Subject .
	 * @param string $body Body .
	 * @return string
	 */
	public function DKIM_Add( $headers_line, $subject, $body ) { // @codingStandardsIgnoreLine.
		$DKIMsignatureType    = 'rsa-sha256'; // @codingStandardsIgnoreLine.
		// Signature & hash algorithms .
		$DKIMcanonicalization = 'relaxed/simple'; // @codingStandardsIgnoreLine.
		// Canonicalization of header/body .
		$DKIMquery            = 'dns/txt'; // @codingStandardsIgnoreLine.
		// Query method .
		$DKIMtime = time(); // @codingStandardsIgnoreLine.
		// Signature Timestamp = seconds since 00:00:00 - Jan 1, 1970 (UTC time zone) .
		$subject_header = "Subject: $subject";
		$headers        = explode( $this->LE, $headers_line ); // @codingStandardsIgnoreLine.
		$from_header    = '';
		$to_header      = '';
		$date_header    = '';
		$current        = '';
		foreach ( $headers as $header ) {
			if ( strpos( $header, 'From:' ) === 0 ) {
				$from_header = $header;
				$current     = 'from_header';
			} elseif ( strpos( $header, 'To:' ) === 0 ) {
				$to_header = $header;
				$current   = 'to_header';
			} elseif ( strpos( $header, 'Date:' ) === 0 ) {
				$date_header = $header;
				$current     = 'date_header';
			} else {
				if ( ! empty( $$current ) && strpos( $header, ' =?' ) === 0 ) {
					$$current .= $header;
				} else {
					$current = '';
				}
			}
		}
		$from    = str_replace( '|', '=7C', $this->DKIM_QP( $from_header ) );
		$to      = str_replace( '|', '=7C', $this->DKIM_QP( $to_header ) );
		$date    = str_replace( '|', '=7C', $this->DKIM_QP( $date_header ) );
		$subject = str_replace(
			'|',
			'=7C',
			$this->DKIM_QP( $subject_header )
		); // Copied header fields .
		$body    = $this->DKIM_BodyC( $body );
		$DKIMlen = strlen( $body ); // @codingStandardsIgnoreLine.
		// Length of body .
		$DKIMb64 = base64_encode( pack( 'H*', hash( 'sha256', $body ) ) ); // @codingStandardsIgnoreLine.
		// Base64 of packed binary SHA-256 hash of body .
		if ( '' == $this->DKIM_identity ) { // @codingStandardsIgnoreLine.
			$ident = '';
		} else {
			$ident = ' i=' . $this->DKIM_identity . ';'; // @codingStandardsIgnoreLine.
		}
		$dkimhdrs = 'DKIM-Signature: v=1; a=' .
			$DKIMsignatureType . '; q=' . // @codingStandardsIgnoreLine.
			$DKIMquery . '; l=' . // @codingStandardsIgnoreLine.
			$DKIMlen . '; s=' . // @codingStandardsIgnoreLine.
			$this->DKIM_selector . // @codingStandardsIgnoreLine.
			";\r\n" .
			"\tt=" . $DKIMtime . '; c=' . $DKIMcanonicalization . ";\r\n" . // @codingStandardsIgnoreLine.
			"\th=From:To:Date:Subject;\r\n" .
			"\td=" . $this->DKIM_domain . ';' . $ident . "\r\n" . // @codingStandardsIgnoreLine.
			"\tz=$from\r\n" .
			"\t|$to\r\n" .
			"\t|$date\r\n" .
			"\t|$subject;\r\n" .
			"\tbh=" . $DKIMb64 . ";\r\n" . // @codingStandardsIgnoreLine.
			"\tb=";
		$toSign   = $this->DKIM_HeaderC( // @codingStandardsIgnoreLine.
			$from_header . "\r\n" .
			$to_header . "\r\n" .
			$date_header . "\r\n" .
			$subject_header . "\r\n" .
			$dkimhdrs
		);
		$signed   = $this->DKIM_Sign( $toSign ); // @codingStandardsIgnoreLine.
		return $dkimhdrs . $signed . "\r\n";
	}

	/**
	 * Detect if a string contains a line longer than the maximum line length allowed.
	 *
	 * @param string $str .
	 * @return boolean
	 * @static
	 */
	public static function hasLineLongerThanMax( $str ) { // @codingStandardsIgnoreLine.
		// +2 to include CRLF line break for a 1000 total
		return (boolean) preg_match( '/^(.{' . ( self::MAX_LINE_LENGTH + 2 ) . ',})/m', $str );
	}

	/**
	 * Allows for public read access to 'to' property.
	 *
	 * @note: Before the send() call, queued addresses (i.e. with IDN) are not yet included.
	 * @access public
	 * @return array
	 */
	public function getToAddresses() { // @codingStandardsIgnoreLine.
		return $this->to;
	}

	/**
	 * Allows for public read access to 'cc' property.
	 *
	 * @note: Before the send() call, queued addresses (i.e. with IDN) are not yet included.
	 * @access public
	 * @return array
	 */
	public function getCcAddresses() { // @codingStandardsIgnoreLine.
		return $this->cc;
	}

	/**
	 * Allows for public read access to 'bcc' property.
	 *
	 * @note: Before the send() call, queued addresses (i.e. with IDN) are not yet included.
	 * @access public
	 * @return array
	 */
	public function getBccAddresses() { // @codingStandardsIgnoreLine.
		return $this->bcc;
	}

	/**
	 * Allows for public read access to 'ReplyTo' property.
	 *
	 * @note: Before the send() call, queued addresses (i.e. with IDN) are not yet included.
	 * @access public
	 * @return array
	 */
	public function getReplyToAddresses() { // @codingStandardsIgnoreLine.
		return $this->ReplyTo; // @codingStandardsIgnoreLine.
	}

	/**
	 * Allows for public read access to 'all_recipients' property.
	 *
	 * @note: Before the send() call, queued addresses (i.e. with IDN) are not yet included.
	 * @access public
	 * @return array
	 */
	public function getAllRecipientAddresses() { // @codingStandardsIgnoreLine.
		return $this->all_recipients;
	}

	/**
	 * Perform a callback.
	 *
	 * @param boolean $isSent .
	 * @param array   $to .
	 * @param array   $cc .
	 * @param array   $bcc .
	 * @param string  $subject .
	 * @param string  $body .
	 * @param string  $from .
	 */
	protected function doCallback( $isSent, $to, $cc, $bcc, $subject, $body, $from ) { // @codingStandardsIgnoreLine.
		if ( ! empty( $this->action_function ) && is_callable( $this->action_function ) ) {
			$params = array( $isSent, $to, $cc, $bcc, $subject, $body, $from ); // @codingStandardsIgnoreLine.
			call_user_func_array( $this->action_function, $params );
		}
	}
}

/**
 * PHPMailer exception handler
 *
 * @package PHPMailer
 */
class phpmailerException extends Exception { // @codingStandardsIgnoreLine.

	/**
	 * Prettify error message output
	 *
	 * @return string
	 */
	public function errorMessage() {
		$errorMsg = '<strong>' . htmlspecialchars( $this->getMessage() ) . "</strong><br />\n"; // @codingStandardsIgnoreLine.
		return $errorMsg; // @codingStandardsIgnoreLine.
	}
}
