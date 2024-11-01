<?php
/**
 * This file provides configuration.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/includes
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
if ( ! class_exists( 'Mail_Booster_Configuration_Provider' ) ) {
	/**
	 * This class used to manage configuration.
	 *
	 * @package    wp-mail-booster
	 * @subpackage includes
	 *
	 * @author  Tech Banker
	 */
	class Mail_Booster_Configuration_Provider {
		/**
		 * This function used to get configuration settings.
		 */
		public function get_configuration_settings() {
			global $wpdb;
			$mb_table_prefix = $wpdb->prefix;
			if ( is_multisite() ) {
				$settings_data       = $wpdb->get_var(
					$wpdb->prepare(
						'SELECT meta_value FROM ' . $wpdb->base_prefix . 'mail_booster_meta WHERE meta_key=%s', 'settings'
					)
				);// WPCS: db call ok; no-cache ok.
				$settings_data_array = maybe_unserialize( $settings_data );
				if ( isset( $settings_data_array['fetch_settings'] ) && 'network_site' === $settings_data_array['fetch_settings'] ) {
					$mb_table_prefix = $wpdb->base_prefix;
				}
			}
			$email_configuration_data  = $wpdb->get_var(
				$wpdb->prepare(
					'SELECT meta_value FROM ' . $mb_table_prefix . 'mail_booster_meta WHERE meta_key=%s', 'email_configuration'
				)
			);// WPCS: db call ok; no-cache ok, unprepared SQL ok.
			$email_configuration_array = maybe_unserialize( $email_configuration_data );
			return $email_configuration_array;
		}
	}
}
