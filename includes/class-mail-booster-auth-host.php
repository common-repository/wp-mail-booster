<?php
/**
 * This file is used for authenticating and sending Emails.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/includes
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
if ( ! class_exists( 'Mail_Booster_Auth_Host' ) ) {
	/**
	 * This class is used to host authentication.
	 *
	 * @package wp-mail-booster
	 * @subpackage includes
	 * @author Tech Banker
	 */
	class Mail_Booster_Auth_Host {
		/**
		 * Manage from name
		 *
		 * @access   public
		 * @var      string    $from_name  holds from name.
		 */
		public $from_name;
		/**
		 * Manage smtp host
		 *
		 * @access   public
		 * @var      string    $smtp_host  holds smtp host.
		 */
		public $smtp_host;
		/**
		 * Manage smtp port
		 *
		 * @access   public
		 * @var      string    $smtp_port  holds smtp port.
		 */
		public $smtp_port;
		/**
		 * Manage client id
		 *
		 * @access   public
		 * @var      string    $client_id  holds client id.
		 */
		public $client_id;
		/**
		 * Manage client secret
		 *
		 * @access   public
		 * @var      string    $client_secret  holds client secret.
		 */
		public $client_secret;
		/**
		 * Manage redirect uri
		 *
		 * @access   public
		 * @var      string    $redirect_uri  holds redirect uri.
		 */
		public $redirect_uri;
		/**
		 * Manage api key
		 *
		 * @access   public
		 * @var      string    $api_key  holds api key.
		 */
		public $api_key;
		/**
		 * Manage authorization token
		 *
		 * @access   public
		 * @var      string    $authorization_token  holds from authorization token.
		 */
		public $authorization_token;
		/**
		 * Manage oauth domains
		 *
		 * @access   public
		 * @var      array    $oauth_domains array holds from authorization domains.
		 */
		public $oauth_domains = array(
			'hotmail.com'  => 'smtp.live.com',
			'outlook.com'  => 'smtp.live.com',
			'yahoo.ca'     => 'smtp.mail.yahoo.ca',
			'yahoo.co.id'  => 'smtp.mail.yahoo.co.id',
			'yahoo.co.in'  => 'smtp.mail.yahoo.co.in',
			'yahoo.co.kr'  => 'smtp.mail.yahoo.com',
			'yahoo.com'    => 'smtp.mail.yahoo.com',
			'ymail.com'    => 'smtp.mail.yahoo.com',
			'yahoo.com.ar' => 'smtp.mail.yahoo.com.ar',
			'yahoo.com.au' => 'smtp.mail.yahoo.com.au',
			'yahoo.com.br' => 'smtp.mail.yahoo.com.br',
			'yahoo.com.cn' => 'smtp.mail.yahoo.com.cn',
			'yahoo.com.hk' => 'smtp.mail.yahoo.com.hk',
			'yahoo.com.mx' => 'smtp.mail.yahoo.com',
			'yahoo.com.my' => 'smtp.mail.yahoo.com.my',
			'yahoo.com.ph' => 'smtp.mail.yahoo.com.ph',
			'yahoo.com.sg' => 'smtp.mail.yahoo.com.sg',
			'yahoo.com.tw' => 'smtp.mail.yahoo.com.tw',
			'yahoo.com.vn' => 'smtp.mail.yahoo.com.vn',
			'yahoo.co.nz'  => 'smtp.mail.yahoo.com.au',
			'yahoo.co.th'  => 'smtp.mail.yahoo.co.th',
			'yahoo.co.uk'  => 'smtp.mail.yahoo.co.uk',
			'yahoo.de'     => 'smtp.mail.yahoo.de',
			'yahoo.es'     => 'smtp.correo.yahoo.es',
			'yahoo.fr'     => 'smtp.mail.yahoo.fr',
			'yahoo.ie'     => 'smtp.mail.yahoo.co.uk',
			'yahoo.it'     => 'smtp.mail.yahoo.it',
			'gmail.com'    => 'smtp.gmail.com',
		);
		/**
		 * Manage yahoo domains
		 *
		 * @access   public
		 * @var      array    $yahoo_domains array holds from yahoo domains.
		 */
		public $yahoo_domains = array(
			'smtp.mail.yahoo.ca',
			'smtp.mail.yahoo.co.id',
			'smtp.mail.yahoo.co.in',
			'smtp.mail.yahoo.com',
			'smtp.mail.yahoo.com',
			'smtp.mail.yahoo.com.ar',
			'smtp.mail.yahoo.com.au',
			'smtp.mail.yahoo.com.br',
			'smtp.mail.yahoo.com.cn',
			'smtp.mail.yahoo.com.hk',
			'smtp.mail.yahoo.com',
			'smtp.mail.yahoo.com.my',
			'smtp.mail.yahoo.com.ph',
			'smtp.mail.yahoo.com.sg',
			'smtp.mail.yahoo.com.tw',
			'smtp.mail.yahoo.com.vn',
			'smtp.mail.yahoo.com.au',
			'smtp.mail.yahoo.co.th',
			'smtp.mail.yahoo.co.uk',
			'smtp.mail.yahoo.de',
			'smtp.correo.yahoo.es',
			'smtp.mail.yahoo.fr',
			'smtp.mail.yahoo.co.uk',
			'smtp.mail.yahoo.it',
		);
		/**
		 * This function is used to.
		 *
		 * @param array $settings_array passes as a settings array .
		 */
		public function __construct( $settings_array ) {
			if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/class-mail-booster-manage-token.php' ) ) {
				include_once MAIL_BOOSTER_DIR_PATH . 'includes/class-mail-booster-manage-token.php';
			}
			$this->authorization_token = mail_booster_manage_token::get_instance();
			$this->from_name           = $settings_array['sender_name'];
			$this->from_email          = $settings_array['sender_email'];
			$this->smtp_host           = $settings_array['hostname'];
			$this->smtp_port           = $settings_array['port'];
			$this->client_id           = $settings_array['client_id'];
			$this->client_secret       = $settings_array['client_secret'];
			$this->redirect_uri        = $settings_array['redirect_uri'];
			$this->sender_email        = $settings_array['email_address'];
		}

		/**
		 * This function is used google authentication.
		 */
		public function google_authentication() {
			if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/class-google-authentication-mail-booster.php' ) ) {
				include_once MAIL_BOOSTER_DIR_PATH . 'includes/class-google-authentication-mail-booster.php';
			}
			if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/class-authentication-manager-mail-booster.php' ) ) {
				include_once MAIL_BOOSTER_DIR_PATH . 'includes/class-authentication-manager-mail-booster.php';
			}
			$obj_google_authentication_mail_booster = new Google_Authentication_Mail_Booster( $this->client_id, $this->client_secret, $this->authorization_token, $this->redirect_uri, $this->sender_email );

			$obj_google_authentication_mail_booster->get_token_code( 'wp-mail-booster' );
		}
		/**
		 * This function is used for google authentication.
		 *
		 * @param string $code .
		 */
		public function google_authentication_token( $code ) {
			if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/class-mail-booster-zend-mail-helper.php' ) ) {
				include_once MAIL_BOOSTER_DIR_PATH . 'includes/class-mail-booster-zend-mail-helper.php';
			}
			if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/class-google-authentication-mail-booster.php' ) ) {
				include_once MAIL_BOOSTER_DIR_PATH . 'includes/class-google-authentication-mail-booster.php';
			}
			if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/class-authentication-manager-mail-booster.php' ) ) {
				include_once MAIL_BOOSTER_DIR_PATH . 'includes/class-authentication-manager-mail-booster.php';
			}
			$obj_google_authentication_mail_booster = new Google_Authentication_Mail_Booster( $this->client_id, $this->client_secret, $this->authorization_token, $this->redirect_uri, $this->sender_email );

			$test_error1 = $obj_google_authentication_mail_booster->process_token_Code( md5( rand() ) );
			if ( isset( $test_error1->error ) ) {
				return $test_error1;
			}

			$this->authorization_token->save_token_mail_booster();
		}
	}
}
