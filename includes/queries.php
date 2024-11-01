<?php
/**
 * This file is used for fetching data from database.
 *
 * @author  Tech-Banker
 * @package wp-mail-booster/includes
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}// Exit if accessed directly
if ( ! is_user_logged_in() ) {
	return;
} else {
	$access_granted = false;
	foreach ( $user_role_permission as $permission ) {
		if ( current_user_can( $permission ) ) {
			$access_granted = true;
			break;
		}
	}
	if ( ! $access_granted ) {
		return;
	} else {
		$upgrade_database_mail_booster = wp_create_nonce( 'upgrade_database_mail_booster' );
		/**
		 * This function is used to sort the date.
		 *
		 * @param string $a passes parameter as a.
		 * @param string $b passes parameter as b.
		 */
		function date_sort_mail_booster( $a, $b ) {
			return strtotime( $a ) - strtotime( $b );
		}
		if ( ! function_exists( 'get_mail_booster_meta_value' ) ) {
			/**
			 * This function is used to return unserialized data.
			 *
			 * @param string $meta_key .
			 */
			function get_mail_booster_meta_value( $meta_key ) {
				global $wpdb;
				$meta_value = $wpdb->get_var(
					$wpdb->prepare(
						'SELECT meta_value FROM ' . $wpdb->prefix . 'mail_booster_meta WHERE meta_key=%s', $meta_key
					)
				);// WPCS: db call ok; no-cache ok.
				return maybe_unserialize( $meta_value );
			}
		}
		if ( isset( $_GET['page'] ) ) { // WPCS: CSRF ok, WPCS: input var ok.
			switch ( sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) { // WPCS: CSRF ok,WPCS: input var ok.
				case 'mail_booster_roles_and_capabilities':
					$details_roles_capabilities = get_mail_booster_meta_value( 'roles_and_capabilities' );
					$other_roles_access_array   = array(
						'manage_options',
						'edit_plugins',
						'edit_posts',
						'publish_posts',
						'publish_pages',
						'edit_pages',
						'read',
					);
					$other_roles_array          = isset( $details_roles_capabilities['capabilities'] ) && '' !== $details_roles_capabilities['capabilities'] ? $details_roles_capabilities['capabilities'] : $other_roles_access_array;
					break;

				case 'mail_booster_settings':
					$settings_data_array = get_mail_booster_meta_value( 'settings' );
					break;

				case 'mail_booster_email_logs':
					$end_date   = MAIL_BOOSTER_LOCAL_TIME;
					$start_date = strtotime( '-7 days', $end_date );

					$email_logs_sent_data     = $wpdb->get_results(
						"SELECT id, subject, timestamp, email_to, status, debug_mode, DATE_FORMAT(FROM_UNIXTIME(timestamp), '%m/%d/%Y') AS 'date_formatted' FROM " . $wpdb->prefix . "mail_booster_logs WHERE timestamp BETWEEN " . $start_date . " AND " . $end_date . " AND status = 'Sent' ORDER BY timestamp ASC LIMIT 3000", ARRAY_A // @codingStandardsIgnoreLine
					);// WPCS: db call ok; no-cache ok.
					$email_logs_not_sent_data = $wpdb->get_results(
						"SELECT id, subject, timestamp, email_to, status, debug_mode, DATE_FORMAT(FROM_UNIXTIME(timestamp), '%m/%d/%Y') AS 'date_formatted' FROM " . $wpdb->prefix . "mail_booster_logs WHERE timestamp BETWEEN " . $start_date . " AND " . $end_date . " AND status = 'Not Sent' ORDER BY timestamp ASC LIMIT 3000", ARRAY_A// @codingStandardsIgnoreLine
					);// WPCS: db call ok; no-cache ok.
					$sent_array_dates         = array_column( $email_logs_sent_data, 'date_formatted' );
					$email_logs_data          = array_merge( $email_logs_sent_data, $email_logs_not_sent_data );
					$email_logs_array_dates   = array_column( $email_logs_data, 'date_formatted' );
					$email_logs_array_dates   = array_values( array_unique( $email_logs_array_dates ) );
					usort( $email_logs_array_dates, 'date_sort_mail_booster' );
					$not_sent_array_dates = array_column( $email_logs_not_sent_data, 'date_formatted' );
					$email_reports_array  = $email_logs_data;
					$sort_ids             = array_column( $email_reports_array, 'id' );
					array_multisort( $sort_ids, SORT_DESC, $email_reports_array );
					break;
				case 'mail_booster_email_notification':
					$email_notification_data_array = get_mail_booster_meta_value( 'email_notification' );
					break;
				case 'mail_booster_email_configuration':
					$email_configuration_array = get_mail_booster_meta_value( 'email_configuration' );
					if ( ! empty( $_REQUEST['access_token'] ) && isset( $_REQUEST['access_token'] ) ) {// WPCS: CSRF ok,WPCS: input var ok.
						$code                            = esc_attr( $_REQUEST['access_token'] ); // @codingStandardsIgnoreLine.
						$update_email_configuration_data = get_option( 'update_email_configuration' );
						$mail_booster_auth_host          = new Mail_Booster_Auth_Host( $update_email_configuration_data );
						if ( 'smtp.gmail.com' === $update_email_configuration_data['hostname'] ) {
							$test_secret_key_error = $mail_booster_auth_host->google_authentication_token( $code );
							if ( isset( $test_secret_key_error->error ) ) {
								$test_secret_key_error = $test_secret_key_error->error_description;
								break;
							}
						}
						$obj_dbhelper_mail_booster        = new Dbhelper_Mail_Booster();
						$update_email_configuration_array = array();
						$where                            = array();
						$where['meta_key']                = 'email_configuration';// WPCS: slow query ok.
						$update_email_configuration_array['meta_value'] = maybe_serialize( $update_email_configuration_data );// WPCS: slow query ok.
						$obj_dbhelper_mail_booster->update_command( mail_booster_meta(), $update_email_configuration_array, $where );
						if ( '1' === $update_email_configuration_data['automatic_mail'] ) {
							$automatically_send_mail = 'true';
						} else {
							$automatically_not_send_mail = 'true';
						}
					}
					break;
			}
		}
	}
}
