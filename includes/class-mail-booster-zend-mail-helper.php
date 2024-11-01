<?php
/**
 * This file is used for zend mail helper.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/includes
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
if ( ! class_exists( 'Mail_Booster_Zend_Mail_Helper' ) ) {
	/**
	 * This class used for zend mail helper.
	 *
	 * @package    wp-mail-booster
	 * @subpackage includes
	 *
	 * @author  Tech Banker
	 */
	class Mail_Booster_Zend_Mail_Helper {
		/**
		 * Mail Booster zend mail helper validate email.
		 *
		 * @access   public
		 * @var      string    $validate_email zend mail helper validate emailt.
		 */
		public static $validate_email;
		/**
		 * This function is used for email domains.
		 *
		 * @param string $hostname zend mail helper host name.
		 * @param string $needle zend mail helper needle.
		 */
		public static function email_domains_mail_booster( $hostname, $needle ) {
			$length = strlen( $needle );
			return( substr( $hostname, - $length ) === $needle );
		}
		/**
		 * This function is used to retrieve body from response.
		 *
		 * @param string $url retrieve url.
		 * @param string $parameters retrieve parameters.
		 * @param array  $headers retrieve headers.
		 */
		public static function retrieve_body_from_response_mail_booster( $url, $parameters, array $headers = array() ) {
			$response = Mail_Booster_Zend_Mail_Helper::post_request_mail_booster( $url, $parameters, $headers );
			if ( isset( $response['error'] ) ) {
				return wp_json_encode( $response );
			}
			$body = wp_remote_retrieve_body( $response );
			return $body;
		}
		/**
		 * This function is used to make outgoing Http requests.
		 *
		 * @param string $url request for url.
		 * @param array  $parameters request for parameters.
		 * @param array  $headers request for headers.
		 */
		public static function post_request_mail_booster( $url, $parameters = array(), array $headers = array() ) {
			$args     = array(
				'timeout' => '10000',
				'headers' => $headers,
				'body'    => $parameters,
			);
			$response = wp_remote_post( $url, $args );

			if ( is_wp_error( $response ) ) {
				return array(
					'error'             => 'An error occured',
					'error_description' => $response->get_error_message(),
				);
			} else {
				return $response;
			}
		}
		/**
		 * This function is used for basic field validation.
		 *
		 * @param string $text text of the field.
		 */
		public static function check_field_mail_booster( $text ) {
			return( ! isset( $text ) || trim( $text ) === '' );
		}
	}
}
