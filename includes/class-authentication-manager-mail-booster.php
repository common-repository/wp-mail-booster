<?php
/**
 * This file manages authentication.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/includes
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.
if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/class-google-authentication-mail-booster.php' ) ) {
	include_once MAIL_BOOSTER_DIR_PATH . 'includes/class-google-authentication-mail-booster.php';
}
if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/class-mail-booster-register-transport.php' ) ) {
		include_once MAIL_BOOSTER_DIR_PATH . 'includes/class-mail-booster-register-transport.php';
}

if ( ! class_exists( 'Authentication_Manager_Mail_Booster' ) ) {
	/**
	 * This class is used to manage authentication.
	 *
	 * @package    wp-mail-booster
	 * @subpackage includes
	 *
	 * @author  Tech Banker
	 */
	class Authentication_Manager_Mail_Booster {
		/**
		 * This Function is used to create authentication manager.
		 */
		public function create_authentication_manager() {
				$obj_mail_booster_register_transport = new Mail_Booster_Register_Transport();
				$transport                           = $obj_mail_booster_register_transport->retrieve_mailertype_mail_booster();
				$mail_booster_config_provider_obj    = new Mail_Booster_Configuration_Provider();
				$mail_booster_configuration_settings = $mail_booster_config_provider_obj->get_configuration_settings();
				return $this->create_manager( $transport );
		}
		/**
		 * This function checks for the service providers.
		 *
		 * @param Mail_Booster_Smtp_Transport $transport type of transport.
		 */
		public function create_manager( Mail_Booster_Smtp_Transport $transport ) {
				$obj_mail_booster_config_provider = new Mail_Booster_Configuration_Provider();
				$configuration_settings           = $obj_mail_booster_config_provider->get_configuration_settings();
				$authorization_token              = Mail_Booster_Manage_Token::get_instance();
				$hostname                         = $configuration_settings['hostname'];
				$client_id                        = $configuration_settings['client_id'];
				$client_secret                    = $configuration_settings['client_secret'];
				$sender_email                     = $configuration_settings['sender_email'];
				$redirect_uri                     = admin_url( 'admin-ajax.php' );
			if ( $this->check_google_service_provider_mail_booster( $hostname ) ) {
					$obj_service_provider = new Google_Authentication_Mail_Booster( $client_id, $client_secret, $authorization_token, $redirect_uri, $sender_email );
			}
			return $obj_service_provider;
		}
		/**
		 * This function checks for the google service providers.
		 *
		 * @param  string $hostname type of transport.
		 */
		public function check_google_service_provider_mail_booster( $hostname ) {
				return Mail_Booster_Zend_Mail_Helper::email_domains_mail_booster( $hostname, 'gmail.com' ) || Mail_Booster_Zend_Mail_Helper::email_domains_mail_booster( $hostname, 'googleapis.com' );
		}
	}
}
