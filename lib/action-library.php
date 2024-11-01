<?php
/**
 * This file is used for managing data in database.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/lib
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}// Exit if accessed directly.
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
		/**
		 * This function is used to sort the date.
		 *
		 * @param string $a passes parameter as a.
		 * @param string $b passes parameter as b.
		 */
		function date_sort_mail_booster( $a, $b ) {
			return strtotime( $a ) - strtotime( $b );
		}
		if ( isset( $_REQUEST['param'] ) ) { // WPCS: input var ok.
			$obj_dbhelper_mail_booster = new Dbhelper_Mail_Booster();
			switch ( sanitize_text_field( wp_unslash( $_REQUEST['param'] ) ) ) { // WPCS: CSRF ok, WPCS: input var ok.
				case 'wizard_wp_mail_booster':
					if ( wp_verify_nonce( isset( $_REQUEST['_wp_nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wp_nonce'] ) ) : '', 'wp_mail_booster_check_status' ) ) { // WPCS: input var ok.
						$plugin_info_wp_mail_booster = new Plugin_Info_Wp_Mail_Booster();

						global $wp_version;

						$url              = TECH_BANKER_STATS_URL . '/wp-admin/admin-ajax.php';
						$type             = isset( $_REQUEST['type'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['type'] ) ) : ''; // WPCS: input var ok.
						$user_admin_email = isset( $_REQUEST['id'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['id'] ) ) : ''; // WPCS: input var ok.
						$user_first_name  = isset( $_REQUEST['first_name'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['first_name'] ) ) : ''; // WPCS: input var ok.
						$user_last_name   = isset( $_REQUEST['last_name'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['last_name'] ) ) : ''; // WPCS: input var ok.
						if ( '' === $user_admin_email ) {
							$user_admin_email = get_option( 'admin_email' );
						}
						update_option( 'wp-mail-booster-wizard-set-up', $type );
						update_option( 'mail-booster-admin-email', $user_admin_email );

						if ( 'opt_in' === $type ) {
							$theme_details = array();
							if ( $wp_version >= 3.4 ) {
								$active_theme                   = wp_get_theme();
								$theme_details['theme_name']    = strip_tags( $active_theme->Name ); // @codingStandardsIgnoreLine.
								$theme_details['theme_version'] = strip_tags( $active_theme->Version );// @codingStandardsIgnoreLine.
								$theme_details['author_url']    = strip_tags( $active_theme->{'Author URI'} );
							}

							$plugin_stat_data                     = array();
							$plugin_stat_data['plugin_slug']      = 'wp-mail-booster';
							$plugin_stat_data['type']             = 'standard_edition';
							$plugin_stat_data['version_number']   = MAIL_BOOSTER_VERSION_NUMBER;
							$plugin_stat_data['status']           = $type;
							$plugin_stat_data['event']            = 'activate';
							$plugin_stat_data['domain_url']       = site_url();
							$plugin_stat_data['wp_language']      = defined( 'WPLANG' ) && WPLANG ? WPLANG : get_locale();
							$plugin_stat_data['email']            = $user_admin_email;
							$plugin_stat_data['first_name']       = $user_first_name;
							$plugin_stat_data['last_name']        = $user_last_name;
							$plugin_stat_data['wp_version']       = $wp_version;
							$plugin_stat_data['php_version']      = sanitize_text_field( phpversion() );
							$plugin_stat_data['mysql_version']    = $wpdb->db_version();
							$plugin_stat_data['max_input_vars']   = ini_get( 'max_input_vars' );
							$plugin_stat_data['operating_system'] = PHP_OS . '  (' . PHP_INT_SIZE * 8 . ') BIT';
							$plugin_stat_data['php_memory_limit'] = ini_get( 'memory_limit' ) ? ini_get( 'memory_limit' ) : 'N/A';
							$plugin_stat_data['extensions']       = get_loaded_extensions();
							$plugin_stat_data['plugins']          = $plugin_info_wp_mail_booster->get_plugin_info_wp_mail_booster();
							$plugin_stat_data['themes']           = $theme_details;

							$response = wp_safe_remote_post(
								$url, array(
									'method'      => 'POST',
									'timeout'     => 45,
									'redirection' => 5,
									'httpversion' => '1.0',
									'blocking'    => true,
									'headers'     => array(),
									'body'        => array(
										'data'    => maybe_serialize( $plugin_stat_data ),
										'site_id' => false !== get_option( 'mail_booster_tech_banker_site_id' ) ? get_option( 'mail_booster_tech_banker_site_id' ) : '',
										'action'  => 'plugin_analysis_data',
									),
								)
							);

							if ( ! is_wp_error( $response ) ) {
								false !== $response['body'] ? update_option( 'mail_booster_tech_banker_site_id', $response['body'] ) : '';
							}
						}
					}
					break;

				case 'update_database_entries_mail_booster':
					if ( wp_verify_nonce( isset( $_REQUEST['_wp_nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wp_nonce'] ) ) : '', 'upgrade_database_mail_booster' ) ) { // WPCS: input var ok.
						$offset                       = isset( $_REQUEST ['offset'] ) ? intval( $_REQUEST ['offset'] ) * 3000 : 0; // WPCS: input var ok.
						$mail_booster_email_logs_data = $wpdb->get_results(
							$wpdb->prepare(
								'SELECT email_data FROM ' . $wpdb->prefix . 'mail_booster_email_logs LIMIT %d , 3000', $offset
							)
						);// WPCS: db call ok; no-cache ok.
						foreach ( $mail_booster_email_logs_data as $data ) {
							$unserialized_data = maybe_unserialize( $data->email_data );
							$obj_dbhelper_mail_booster->insert_command( mail_booster_logs(), $unserialized_data );
							update_option( 'mail_booster_update_database', 'mail_booster_update_database' );
						}
					}
					break;

				case 'mail_booster_set_hostname_port_module':
					if ( wp_verify_nonce( isset( $_REQUEST['_wp_nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wp_nonce'] ) ) : '', 'mail_booster_set_hostname_port' ) ) { // WPCS: input var ok.
						$smtp_user                      = isset( $_REQUEST['smtp_user'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['smtp_user'] ) ) : ''; // WPCS: input var ok.
						$hostname                       = substr( strrchr( $smtp_user, '@' ), 1 );
						$obj_mail_booster_discover_host = new Mail_Booster_Discover_Host();
						$hostname_to_set                = $obj_mail_booster_discover_host->get_smtp_from_email( $hostname );
						echo esc_attr( $hostname_to_set );
					}
					break;

				case 'mail_booster_test_email_configuration_module':
					if ( wp_verify_nonce( isset( $_REQUEST['_wp_nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wp_nonce'] ) ) : '', 'mail_booster_test_email_configuration' ) ) { // WPCS: input var ok.
						parse_str( isset( $_REQUEST['data'] ) ? base64_decode( wp_unslash( filter_input( INPUT_POST, 'data' ) ) ) : '', $form_data ); // WPCS: input var ok.
						global $phpmailer;
						$mb_table_prefix = $wpdb->prefix;
						if ( is_multisite() ) {
							$get_other_settings_meta_value    = $wpdb->get_var(
								$wpdb->prepare(
									'SELECT meta_value FROM ' . $wpdb->base_prefix . 'mail_booster_meta WHERE meta_key=%s', 'settings'
								)
							);// WPCS: db call ok; no-cache ok.
							$other_settings_unserialized_data = maybe_unserialize( $get_other_settings_meta_value );
							if ( isset( $other_settings_unserialized_data['fetch_settings'] ) && 'network_site' === $other_settings_unserialized_data['fetch_settings'] ) {
								$mb_table_prefix = $wpdb->base_prefix;
							}
						}
						$mail_booster_email_configuration_data = $wpdb->get_row(
							$wpdb->prepare(
								'SELECT meta_value FROM ' . $mb_table_prefix . 'mail_booster_meta WHERE meta_key = %s', 'email_configuration'
							)
						);// WPCS: db call ok; no-cache ok, unprepared SQL ok.
						$unserialized_email_configuration_data = maybe_unserialize( $mail_booster_email_configuration_data->meta_value );
						$settings_array_serialized             = $wpdb->get_var(
							$wpdb->prepare(
								'SELECT meta_value FROM ' . $wpdb->prefix . 'mail_booster_meta WHERE meta_key=%s', 'settings'
							)
						);// db call ok; no-cache ok.
						$settings_array_unserialized           = maybe_unserialize( $settings_array_serialized );
						if ( ( 'smtp' === $unserialized_email_configuration_data['mailer_type'] && 'oauth2' !== $unserialized_email_configuration_data['auth_type'] ) || 'php_mail_function' === $unserialized_email_configuration_data['mailer_type'] ) {
							if ( ! is_object( $phpmailer ) || ! is_a( $phpmailer, 'PHPMailer' ) ) {
								if ( file_exists( ABSPATH . WPINC . '/class-phpmailer.php' ) ) {
									require_once ABSPATH . WPINC . '/class-phpmailer.php';
								}
								if ( file_exists( ABSPATH . WPINC . '/class-smtp.php' ) ) {
									require_once ABSPATH . WPINC . '/class-smtp.php';
								}
								$phpmailer = new PHPMailer( true );// @codingStandardsIgnoreLine.
							}
							if ( 'enable' === $settings_array_unserialized['debug_mode'] ) {
								$phpmailer->SMTPDebug = true;// @codingStandardsIgnoreLine.
							}
							ob_start();
						}
						$to      = isset( $form_data['ux_txt_email'] ) && '' != $form_data['ux_txt_email'] ? sanitize_text_field( $form_data['ux_txt_email'] ) : $unserialized_email_configuration_data['email_address']; // WPCS: loose comparison ok.
						$subject = stripcslashes( htmlspecialchars_decode( $form_data['ux_txt_subject'], ENT_QUOTES ) );
						$message = htmlspecialchars_decode( ! empty( $form_data['ux_email_configuration_text_area'] ) ? htmlspecialchars_decode( $form_data['ux_email_configuration_text_area'] ) : 'This is a demo Test Email for Email Setup - Mail Booster' );
						$headers = 'Content-Type: text/html; charset= utf-8' . "\r\n";
						$result  = wp_mail( $to, $subject, $message, $headers );

						if ( 'enable' === $settings_array_unserialized['debug_mode'] ) {
							if ( 'smtp' === $unserialized_email_configuration_data['mailer_type'] ) {
								$smtp_debug = ob_get_contents();
								if ( 'oauth2' === $unserialized_email_configuration_data['auth_type'] ) {
									$mail_booster_mail_status = get_option( 'mail_booster_mail_status' );
									echo $mail_booster_mail_status;// WPCS: XSS ok.
								} else {
									echo $smtp_debug;// WPCS: XSS ok.
								}
							} else {
								echo $result;// WPCS: XSS ok.
							}
						} else {
							echo $result;// WPCS: XSS ok.
						}
					}
					break;

				case 'mail_booster_email_notification_module':
					if ( wp_verify_nonce( isset( $_REQUEST['_wp_nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wp_nonce'] ) ) : '', 'mail_booster_email_notification' ) ) { // WPCS: input var ok.
						parse_str( isset( $_REQUEST['data'] ) ? base64_decode( wp_unslash( filter_input( INPUT_POST, 'data' ) ) ) : '', $email_notification_array ); // WPCS: input var ok.

						$email_notification_data                               = array();
						$email_notification_data['plugin_update_available']    = sanitize_text_field( $email_notification_array['ux_ddl_plugin_update_available'] );
						$email_notification_data['email_plugin_updated']       = sanitize_text_field( $email_notification_array['ux_ddl_email_plugin_updated'] );
						$email_notification_data['email_theme_update']         = sanitize_text_field( $email_notification_array['ux_ddl_email_theme_update'] );
						$email_notification_data['email_wordpress_update']     = sanitize_text_field( $email_notification_array['ux_ddl_email_wordpress_update'] );
						$email_notification_data['notification_service']       = 'email';
						$email_notification_data['notification']               = 'disable';
						$email_notification_data['pushover_user_key']          = '';
						$email_notification_data['pushover_app_token']         = '';
						$email_notification_data['slack_web_hook']             = '';
						$email_notification_data['notification_email_address'] = get_option( 'admin_email' );

						$where                                       = array();
						$email_notification_data_array               = array();
						$where['meta_key']                           = 'email_notification';// WPCS: slow query ok.
						$email_notification_data_array['meta_value'] = maybe_serialize( $email_notification_data );// WPCS: slow query ok.
						$obj_dbhelper_mail_booster->update_command( mail_booster_meta(), $email_notification_data_array, $where );
					}
					break;

				case 'mail_booster_settings_module':
					if ( wp_verify_nonce( isset( $_REQUEST['_wp_nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wp_nonce'] ) ) : '', 'mail_booster_settings' ) ) { // WPCS: input var ok.
						parse_str( isset( $_REQUEST['data'] ) ? base64_decode( wp_unslash( filter_input( INPUT_POST, 'data' ) ) ) : '', $settings_array ); // WPCS: input var ok.
						$settings_data                               = array();
						$settings_data['debug_mode']                 = sanitize_text_field( $settings_array['ux_ddl_debug_mode'] );
						$settings_data['remove_tables_at_uninstall'] = sanitize_text_field( $settings_array['ux_ddl_remove_tables'] );
						$settings_data['monitor_email_logs']         = sanitize_text_field( $settings_array['ux_ddl_monitor_email_logs'] );
						$settings_data['fetch_settings']             = isset( $settings_array['ux_ddl_fetch_settings'] ) ? sanitize_text_field( $settings_array['ux_ddl_fetch_settings'] ) : '';
						$settings_data['auto_clear_logs']            = 'disable';
						$settings_data['delete_logs_after']          = '1day';
						$where                                       = array();
						$settings_data_array                         = array();
						$where['meta_key']                           = 'settings';// WPCS: slow query ok.
						$settings_data_array['meta_value']           = maybe_serialize( $settings_data );// WPCS: slow query ok.
						$obj_dbhelper_mail_booster->update_command( mail_booster_meta(), $settings_data_array, $where );
					}
					break;

				case 'mail_booster_email_configuration_settings_module':
					if ( wp_verify_nonce( isset( $_REQUEST['_wp_nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wp_nonce'] ) ) : '', 'mail_booster_email_configuration_settings' ) ) { // WPCS: input var ok.
						parse_str( isset( $_REQUEST['data'] ) ? base64_decode( wp_unslash( filter_input( INPUT_POST, 'data' ) ) ) : '', $form_data ); // WPCS: input var ok.
						$update_email_configuration_array                              = array();
						$update_email_configuration_array['email_address']             = sanitize_text_field( $form_data['ux_txt_email_address'] );
						$update_email_configuration_array['reply_to']                  = '';
						$update_email_configuration_array['headers']                   = '';
						$update_email_configuration_array['mailer_type']               = sanitize_text_field( $form_data['ux_ddl_type'] );
						$update_email_configuration_array['sender_name_configuration'] = sanitize_text_field( $form_data['ux_rdl_from_name'] );
						$update_email_configuration_array['sender_name']               = isset( $form_data['ux_txt_mail_booster_from_name'] ) ? sanitize_text_field( $form_data['ux_txt_mail_booster_from_name'] ) : '';
						$update_email_configuration_array['from_email_configuration']  = sanitize_text_field( $form_data['ux_rdl_from_email'] );
						$update_email_configuration_array['sender_email']              = isset( $form_data['ux_txt_mail_booster_from_email_configuration'] ) ? sanitize_text_field( $form_data['ux_txt_mail_booster_from_email_configuration'] ) : '';
						$update_email_configuration_array['hostname']                  = sanitize_text_field( $form_data['ux_txt_host'] );
						$update_email_configuration_array['port']                      = intval( $form_data['ux_txt_port'] );
						$update_email_configuration_array['enc_type']                  = sanitize_text_field( $form_data['ux_ddl_encryption'] );
						$update_email_configuration_array['auth_type']                 = sanitize_text_field( $form_data['ux_ddl_mail_booster_authentication'] );
						$update_email_configuration_array['client_id']                 = sanitize_text_field( trim( $form_data['ux_txt_client_id'] ) );
						$update_email_configuration_array['client_secret']             = sanitize_text_field( trim( $form_data['ux_txt_client_secret'] ) );
						$update_email_configuration_array['sendgrid_api_key']          = sanitize_text_field( trim( $form_data['ux_txt_sendgrid_api_key'] ) );
						$update_email_configuration_array['mailgun_api_key']           = sanitize_text_field( trim( $form_data['ux_txt_mailgun_api_key'] ) );
						$update_email_configuration_array['mailgun_domain_name']       = sanitize_text_field( trim( $form_data['ux_txt_mailgun_domain_name'] ) );
						$update_email_configuration_array['username']                  = sanitize_text_field( $form_data['ux_txt_username'] );
						$update_email_configuration_array['automatic_mail']            = isset( $form_data['ux_chk_automatic_sent_mail'] ) ? sanitize_text_field( $form_data['ux_chk_automatic_sent_mail'] ) : '';

						if ( preg_match( '/^\**$/', $form_data['ux_txt_password'] ) ) {
							$email_configuration_data                     = $wpdb->get_var(
								$wpdb->prepare(
									'SELECT meta_value FROM ' . $wpdb->prefix . 'mail_booster_meta WHERE meta_key=%s', 'email_configuration'
								)
							);// WPCS: db call ok; no-cache ok.
							$email_configuration_array                    = maybe_unserialize( $email_configuration_data );
							$update_email_configuration_array['password'] = $email_configuration_array['password'];
						} else {
							$update_email_configuration_array['password'] = base64_encode( esc_attr( $form_data['ux_txt_password'] ) );
						}

						$update_email_configuration_array['redirect_uri'] = sanitize_text_field( $form_data['ux_txt_redirect_uri'] );

						update_option( 'update_email_configuration', $update_email_configuration_array );

						$mail_booster_auth_host = new Mail_Booster_Auth_Host( $update_email_configuration_array );
						if ( ! in_array( $form_data['ux_txt_host'], $mail_booster_auth_host->oauth_domains, true ) && 'oauth2' === $form_data['ux_ddl_mail_booster_authentication'] ) {
							echo '100';
							die();
						}

						if ( 'oauth2' === $update_email_configuration_array['auth_type'] && 'smtp' === $update_email_configuration_array['mailer_type'] ) {
							if ( 'smtp.gmail.com' === $update_email_configuration_array['hostname'] ) {
								$mail_booster_auth_host->google_authentication();
							}
						} else {
							$update_email_configuration_data_array = array();
							$where                                 = array();
							$where['meta_key']                     = 'email_configuration';// WPCS: slow query ok.
							$update_email_configuration_data_array['meta_value'] = maybe_serialize( $update_email_configuration_array );// WPCS: slow query ok.
							$obj_dbhelper_mail_booster->update_command( mail_booster_meta(), $update_email_configuration_data_array, $where );
						}
					}
					break;

				case 'mail_booster_roles_and_capabilities_module':
					if ( wp_verify_nonce( isset( $_REQUEST['_wp_nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wp_nonce'] ) ) : '', 'mail_booster_roles_capabilities' ) ) {// Input var okay.
						parse_str( isset( $_REQUEST['data'] ) ? base64_decode( wp_unslash( filter_input( INPUT_POST, 'data' ) ) ) : '', $roles_array );// Input var okay.
						$update_roles = array();
						$where        = array();

						global $wpdb;
						$roles_and_capabilities_meta_value       = $wpdb->get_var(
							$wpdb->prepare(
								'SELECT meta_value FROM ' . $wpdb->prefix . 'mail_booster_meta WHERE meta_key=%s', 'roles_and_capabilities'
							)
						); // db call ok; no-cache ok.
							$unserialized_roles_and_capabilities = maybe_unserialize( $roles_and_capabilities_meta_value );
							$unserialized_roles_and_capabilities['show_mail_booster_top_bar_menu'] = sanitize_text_field( $roles_array['ux_ddl_mail_booster_menu'] );

							$update_data               = array();
							$where['meta_key']         = 'roles_and_capabilities'; // WPCS: slow query ok.
							$update_data['meta_value'] = maybe_serialize( $unserialized_roles_and_capabilities ); // WPCS: slow query ok.
							$obj_dbhelper_mail_booster->update_command( mail_booster_meta(), $update_data, $where );
					}
					break;
				case 'mail_booster_email_logs_delete_module':
					if ( wp_verify_nonce( isset( $_REQUEST['_wp_nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wp_nonce'] ) ) : '', 'mail_booster_email_logs_delete' ) ) { // WPCS: input var ok.
						$where_meta       = array();
						$where_meta['id'] = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) : ''; // WPCS: input var ok.
						$obj_dbhelper_mail_booster->delete_command( mail_booster_logs(), $where_meta );
					}
					break;
			}
			die();
		}
	}
}
