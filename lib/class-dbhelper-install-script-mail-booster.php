<?php
/**
 * This file is used for creating tables in database on the activation hook.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/lib
 * @version 2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
if ( ! is_user_logged_in() ) {
	return;
} else {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	} else {
		if ( ! class_exists( 'Dbhelper_Install_Script_Mail_Booster' ) ) {
			/**
			 * This Class is used to Insert, Update operations.
			 *
			 * @package    wp-mail-booster
			 * @subpackage lib
			 *
			 * @author  Tech Banker
			 */
			class Dbhelper_Install_Script_Mail_Booster {
				/**
				 * This Function is used to Insert data in database.
				 *
				 * @param string $table_name .
				 * @param string $data .
				 */
				public function insert_command( $table_name, $data ) {
					global $wpdb;
					$wpdb->insert( $table_name, $data ); // db call ok; no-cache ok.
					return $wpdb->insert_id;
				}
				/**
				 * This Function is used to Insert data in database.
				 *
				 * @param string $table_name .
				 * @param string $data .
				 * @param string $where .
				 */
				public function update_command( $table_name, $data, $where ) {
					global $wpdb;
					$wpdb->update( $table_name, $data, $where ); // db call ok; no-cache ok.
				}
			}
		}

		if ( file_exists( ABSPATH . 'wp-admin/includes/upgrade.php' ) ) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}

		$mail_booster_version_number = get_option( 'mail-booster-version-number' );

		if ( ! function_exists( 'mail_booster_table' ) ) {
			/**
			 * This Function is used to Insert data in database.
			 */
			function mail_booster_table() {
				global $wpdb;
				$collate = $wpdb->get_charset_collate();
				$sql     = 'CREATE TABLE IF NOT EXISTS ' . mail_booster() . '
				(
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`type` varchar(100) NOT NULL,
					`parent_id` int(11) NOT NULL,
					PRIMARY KEY (`id`)
				)ENGINE = MyISAM ' . $collate;
				dbDelta( $sql );

				$data = 'INSERT INTO ' . mail_booster() . " (`type`, `parent_id`) VALUES
				('email_configuration', 0),
				('email_logs', 0),
				('settings', 0),
				('email_notification', 0),
				('collation_type', 0),
				('roles_and_capabilities', 0)";
				dbDelta( $data );
			}
		}
		if ( ! function_exists( 'mail_booster_logs_table' ) ) {
			/**
			 * This Function is used to fetch email logs data from database.
			 */
			function mail_booster_logs_table() {
				global $wpdb;
				$collate                                  = $wpdb->get_charset_collate();
				$obj_dbhelper_install_script_mail_booster = new Dbhelper_Install_Script_Mail_Booster();
				$sql                                      = 'CREATE TABLE IF NOT EXISTS ' . mail_booster_logs() . '
				(
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`email_to` varchar(200),
					`cc` varchar(200),
					`bcc` varchar(200),
					`subject` longtext,
					`content` longtext,
					`sender_name` varchar(200),
					`sender_email` varchar(200),
					`debug_mode` varchar(50),
					`debugging_output` longtext,
					`timestamp` int(20),
					`status` varchar(10),
					PRIMARY KEY (`id`)
				) ENGINE = MyISAM ' . $collate;
				dbDelta( $sql );
			}
		}
		if ( ! function_exists( 'mail_booster_meta_table' ) ) {
			/**
			 * This Function is used to create mail_booster_meta_table table in database.
			 */
			function mail_booster_meta_table() {
				global $wpdb;
				$collate                                  = $wpdb->get_charset_collate();
				$obj_dbhelper_install_script_mail_booster = new Dbhelper_Install_Script_Mail_Booster();
				global $wpdb;
				$sql = 'CREATE TABLE IF NOT EXISTS ' . mail_booster_meta() . '
				(
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`meta_id` int(11) NOT NULL,
					`meta_key` varchar(255) NOT NULL,
					`meta_value` longtext NOT NULL,
					PRIMARY KEY (`id`)
				)ENGINE = MyISAM ' . $collate;
				dbDelta( $sql );

				$admin_email = get_option( 'admin_email' );
				$admin_name  = get_option( 'blogname' );

				$mail_booster_table_data = $wpdb->get_results(
					'SELECT * FROM ' . mail_booster()// @codingStandardsIgnoreLine.
				); // db call ok; no-cache ok.

				foreach ( $mail_booster_table_data as $row ) {
					switch ( $row->type ) {
						case 'email_configuration':
							$option_value              = array();
							$email_configuration_array = array();
							$oauth_token               = get_option( 'postman_auth_token' );
							if ( false !== $oauth_token ) {
								update_option( 'mail_booster_auth', $oauth_token );
							}
							$option_value = get_option( 'postman_options' );
							if ( false !== $option_value ) {
								$email_configuration_array['email_address']       = $option_value['envelope_sender'];
								$email_configuration_array['reply_to']            = $option_value['reply_to'];
								$email_configuration_array['headers']             = $option_value['headers'];
								$email_configuration_array['sender_name']         = $option_value['sender_name'];
								$email_configuration_array['client_id']           = $option_value['oauth_client_id'];
								$email_configuration_array['client_secret']       = $option_value['oauth_client_secret'];
								$email_configuration_array['redirect_uri']        = admin_url( 'admin-ajax.php' );
								$email_configuration_array['sender_email']        = $option_value['sender_email'];
								$email_configuration_array['username']            = $option_value['basic_auth_username'];
								$email_configuration_array['password']            = $option_value['basic_auth_password'];
								$email_configuration_array['hostname']            = isset( $option_value['hostname'] ) ? sanitize_text_field( $option_value['hostname'] ) : '';
								$email_configuration_array['port']                = isset( $option_value['port'] ) ? intval( $option_value['port'] ) : '';
								$email_configuration_array['sendgrid_api_key']    = isset( $option_value['sendgrid_api_key'] ) ? base64_decode( $option_value['sendgrid_api_key'] ) : '';
								$email_configuration_array['mailgun_api_key']     = isset( $option_value['mailgun_api_key'] ) ? base64_decode( $option_value['mailgun_api_key'] ) : '';
								$email_configuration_array['mailgun_domain_name'] = isset( $option_value['mailgun_domain_name'] ) ? $option_value['mailgun_domain_name'] : '';

								switch ( $option_value['enc_type'] ) {
									case 'tls':
										$email_configuration_array['enc_type'] = 'tls';
										break;
									case 'ssl':
										$email_configuration_array['enc_type'] = 'ssl';
										break;
									case 'none':
										$email_configuration_array['enc_type'] = 'none';
										break;
								}
								switch ( $option_value['transport_type'] ) {
									case 'default':
										$email_configuration_array['mailer_type'] = 'smtp';
										break;
									case 'smtp':
										$email_configuration_array['mailer_type'] = 'smtp';
										break;
									case 'gmail_api':
										$email_configuration_array['mailer_type'] = 'smtp';
										break;
									case 'mandrill_api':
										$email_configuration_array['mailer_type'] = 'smtp';
										break;
									case 'sendgrid_api':
										$email_configuration_array['mailer_type'] = 'sendgrid_api';
										break;
									case 'mailgun_api':
											$email_configuration_array['mailer_type'] = 'mailgun_api';
										break;
								}
								switch ( $option_value['auth_type'] ) {
									case 'none':
										$email_configuration_array['auth_type'] = 'none';
										break;
									case 'plain':
										$email_configuration_array['auth_type'] = 'plain';
										break;
									case 'login':
										$email_configuration_array['auth_type'] = 'login';
										break;
									case 'crammd5':
										$email_configuration_array['auth_type'] = 'crammd5';
										break;
									case 'oauth2':
										$email_configuration_array['auth_type'] = 'oauth2';
										break;
								}
							} else {
								$email_configuration_array['email_address']       = $admin_email;
								$email_configuration_array['reply_to']            = '';
								$email_configuration_array['headers']             = '';
								$email_configuration_array['mailer_type']         = 'smtp';
								$email_configuration_array['sender_name']         = $admin_name;
								$email_configuration_array['hostname']            = '';
								$email_configuration_array['port']                = '587';
								$email_configuration_array['client_id']           = '';
								$email_configuration_array['client_secret']       = '';
								$email_configuration_array['redirect_uri']        = '';
								$email_configuration_array['sender_email']        = $admin_email;
								$email_configuration_array['auth_type']           = 'login';
								$email_configuration_array['username']            = $admin_email;
								$email_configuration_array['password']            = '';
								$email_configuration_array['enc_type']            = 'tls';
								$email_configuration_array['sendgrid_api_key']    = '';
								$email_configuration_array['mailgun_api_key']     = '';
								$email_configuration_array['mailgun_domain_name'] = '';
							}

							$email_configuration_array['from_email_configuration']  = 'override';
							$email_configuration_array['sender_name_configuration'] = 'override';

							$email_configuration_array_data               = array();
							$email_configuration_array_data['meta_id']    = $row->id;
							$email_configuration_array_data['meta_key']   = 'email_configuration';// WPCS: slow query ok.
							$email_configuration_array_data['meta_value'] = maybe_serialize( $email_configuration_array );// WPCS: slow query ok.
							$obj_dbhelper_install_script_mail_booster->insert_command( mail_booster_meta(), $email_configuration_array_data );
							break;

						case 'settings':
							$settings_data_array                               = array();
							$settings_data_array['fetch_settings']             = 'individual_site';
							$settings_data_array['debug_mode']                 = 'enable';
							$settings_data_array['remove_tables_at_uninstall'] = 'disable';
							$settings_data_array['monitor_email_logs']         = 'enable';
							$settings_data_array['auto_clear_logs']            = 'disable';
							$settings_data_array['delete_logs_after']          = '1day';

							$settings_array               = array();
							$settings_array['meta_id']    = $row->id;
							$settings_array['meta_key']   = 'settings';// WPCS: slow query ok.
							$settings_array['meta_value'] = maybe_serialize( $settings_data_array );// WPCS: slow query ok.
							$obj_dbhelper_install_script_mail_booster->insert_command( mail_booster_meta(), $settings_array );
							break;

						case 'email_notification':
								$email_notification_data_array                               = array();
								$email_notification_data_array['plugin_update_available']    = 'enable';
								$email_notification_data_array['email_plugin_updated']       = 'enable';
								$email_notification_data_array['email_theme_update']         = 'enable';
								$email_notification_data_array['email_wordpress_update']     = 'enable';
								$email_notification_data_array['notification']               = 'disable';
								$email_notification_data_array['notification_service']       = 'email';
								$email_notification_data_array['notification_email_address'] = $admin_email;
								$email_notification_data_array['pushover_user_key']          = '';
								$email_notification_data_array['pushover_app_token']         = '';
								$email_notification_data_array['slack_web_hook']             = '';

								$email_notification_array               = array();
								$email_notification_array['meta_id']    = $row->id;
								$email_notification_array['meta_key']   = 'email_notification';// WPCS: slow query ok.
								$email_notification_array['meta_value'] = maybe_serialize( $email_notification_data_array );// WPCS: slow query ok.
								$obj_dbhelper_install_script_mail_booster->insert_command( mail_booster_meta(), $email_notification_array );
							break;

						case 'roles_and_capabilities':
							$roles_capabilities_data_array                                   = array();
							$roles_capabilities_data_array['roles_and_capabilities']         = '1,1,1,0,0,0';
							$roles_capabilities_data_array['show_mail_booster_top_bar_menu'] = 'enable';
							$roles_capabilities_data_array['others_full_control_capability'] = '0';
							$roles_capabilities_data_array['administrator_privileges']       = '1,1,1,1,1,1,1,1,1';
							$roles_capabilities_data_array['author_privileges']              = '0,0,1,0,0,0,0,1,0';
							$roles_capabilities_data_array['editor_privileges']              = '0,0,1,0,0,0,1,0,0';
							$roles_capabilities_data_array['contributor_privileges']         = '0,0,0,0,0,0,1,0,0';
							$roles_capabilities_data_array['subscriber_privileges']          = '0,0,0,0,0,0,0,0,0';
							$roles_capabilities_data_array['other_roles_privileges']         = '0,0,0,0,0,0,0,0,0';
							$user_capabilities        = get_others_capabilities_mail_booster();
							$other_roles_array        = array();
							$other_roles_access_array = array(
								'manage_options',
								'edit_plugins',
								'edit_posts',
								'publish_posts',
								'publish_pages',
								'edit_pages',
								'read',
							);
							foreach ( $other_roles_access_array as $role ) {
								if ( in_array( $role, $user_capabilities, true ) ) {
									array_push( $other_roles_array, $role );
								}
							}
							$roles_capabilities_data_array['capabilities'] = $other_roles_array;

							$roles_data_array               = array();
							$roles_data_array['meta_id']    = $row->id;
							$roles_data_array['meta_key']   = 'roles_and_capabilities';// WPCS: slow query ok.
							$roles_data_array['meta_value'] = maybe_serialize( $roles_capabilities_data_array );// WPCS: slow query ok.
							$obj_dbhelper_install_script_mail_booster->insert_command( mail_booster_meta(), $roles_data_array );
							break;
					}
				}
			}
		}

		$obj_dbhelper_install_script_mail_booster = new Dbhelper_Install_Script_Mail_Booster();
		switch ( $mail_booster_version_number ) {
			case '':
				mail_booster_table();
				mail_booster_meta_table();
				mail_booster_logs_table();
				$mail_booster_admin_notices_array                    = array();
				$mail_booster_start_date                             = date( 'm/d/Y' );
				$mail_booster_start_date                             = strtotime( $mail_booster_start_date );
				$mail_booster_start_date                             = strtotime( '+7 day', $mail_booster_start_date );
				$mail_booster_start_date                             = date( 'm/d/Y', $mail_booster_start_date );
				$mail_booster_admin_notices_array['two_week_review'] = array( 'start' => $mail_booster_start_date, 'int' => 7, 'dismissed' => 0 ); // @codingStandardsIgnoreLine.
				update_option( 'mail_booster_admin_notice', $mail_booster_admin_notices_array );
				update_option( 'mail_booster_update_database', 'mail_booster_update_database' );
				break;

			default:
				if ( wp_next_scheduled( 'check_plugin_updates-wp-mail-booster-webmaster-edition' ) ) {
					wp_clear_scheduled_hook( 'check_plugin_updates-wp-mail-booster-webmaster-edition' );
				}
				if ( $wpdb->query( "SHOW TABLES LIKE '" . $wpdb->prefix . 'mail_booster' . "'" ) !== 0 && $wpdb->query( "SHOW TABLES LIKE '" . $wpdb->prefix . 'mail_booster_meta' . "'" ) !== 0 ) {// db call ok; no-cache ok.
					$settings_data       = $wpdb->get_var(
						$wpdb->prepare(
							'SELECT meta_value FROM ' . $wpdb->prefix . 'mail_booster_meta WHERE meta_key=%s',
							'settings'
						)
					);// db call ok; no-cache ok.
					$settings_data_array = maybe_unserialize( $settings_data );
					if ( ! array_key_exists( 'remove_tables_at_uninstall', $settings_data_array ) ) {
							$settings_data_array['remove_tables_at_uninstall'] = 'disable';
					}
					if ( ! array_key_exists( 'monitor_email_logs', $settings_data_array ) ) {
							$settings_data_array['monitor_email_logs'] = 'enable';
					}
					if ( ! array_key_exists( 'fetch_settings', $settings_data_array ) ) {
						$settings_data_array['fetch_settings'] = 'individual_site';
					}
					if ( ! array_key_exists( 'auto_clear_logs', $settings_data_array ) ) {
						$settings_data_array['auto_clear_logs'] = 'disable';
					}
					$where                        = array();
					$settings_array               = array();
					$where['meta_key']            = 'settings';// WPCS: slow query ok.
					$settings_array['meta_value'] = maybe_serialize( $settings_data_array );// WPCS: slow query ok.
					$obj_dbhelper_install_script_mail_booster->update_command( mail_booster_meta(), $settings_array, $where );

					$notification_settings_data        = $wpdb->get_var(
						$wpdb->prepare(
							'SELECT meta_value FROM ' . $wpdb->prefix . 'mail_booster_meta WHERE meta_key=%s',
							'email_notification'
						)
					); // WPCS: db call ok; no-cache ok.
					$notifications_settings_data_array = maybe_unserialize( $notification_settings_data );
					if ( is_array( $notifications_settings_data_array ) && ( ! array_key_exists( 'notification', $notifications_settings_data_array ) ) ) {
						$notifications_settings_data_array['notification'] = 'disable';
					}
					$where                                      = array();
					$notifications_settings_array               = array();
					$where['meta_key']                          = 'email_notification';// WPCS: slow query ok.
					$notifications_settings_array['meta_value'] = maybe_serialize( $notifications_settings_data_array );// WPCS: slow query ok.
					$obj_dbhelper_install_script_mail_booster->update_command( mail_booster_meta(), $notifications_settings_array, $where );

						// Change Database Type.
						$change_type_main_table = $wpdb->query(
							'ALTER TABLE ' . $wpdb->prefix . 'mail_booster ENGINE = MyISAM' //@codingStandardsIgnoreLine.
						);// WPCS: db call ok, no-cache ok.
						$change_type_meta_table = $wpdb->query(
							'ALTER TABLE ' . $wpdb->prefix . 'mail_booster_meta ENGINE = MyISAM' //@codingStandardsIgnoreLine.
						);// WPCS: db call ok, no-cache ok.

					$get_roles_settings_data = $wpdb->get_var(
						$wpdb->prepare(
							'SELECT meta_value FROM ' . $wpdb->prefix . 'mail_booster_meta WHERE meta_key=%s',
							'roles_and_capabilities'
						)
					);// db call ok; no-cache ok.

					$get_roles_settings_data_array = maybe_unserialize( $get_roles_settings_data );
					$get_collate_status_data       = $wpdb->query(
						$wpdb->prepare(
							'SELECT type FROM ' . $wpdb->prefix . 'mail_booster WHERE type=%s',
							'collation_type'
						)
					);// db call ok; no-cache ok.
					if ( 0 === $get_collate_status_data ) {
						if ( array_key_exists( 'roles_and_capabilities', $get_roles_settings_data_array ) ) {
							$roles_and_capabilities_data   = isset( $get_roles_settings_data_array['roles_and_capabilities'] ) ? explode( ',', $get_roles_settings_data_array['roles_and_capabilities'] ) : '1,1,1,0,0,0';
							$administrator_privileges_data = isset( $get_roles_settings_data_array['administrator_privileges'] ) ? explode( ',', $get_roles_settings_data_array['administrator_privileges'] ) : '1,1,1,1,1,1,1,1,1';
							$author_privileges_data        = isset( $get_roles_settings_data_array['author_privileges'] ) ? explode( ',', $get_roles_settings_data_array['author_privileges'] ) : '0,0,0,0,1,0,0,0,0';
							$editor_privileges_data        = isset( $get_roles_settings_data_array['editor_privileges'] ) ? explode( ',', $get_roles_settings_data_array['editor_privileges'] ) : '0,0,0,0,1,0,0,1,0';
							$contributor_privileges_data   = isset( $get_roles_settings_data_array['contributor_privileges'] ) ? explode( ',', $get_roles_settings_data_array['contributor_privileges'] ) : '0,0,0,0,0,0,0,1,0';
							$subscriber_privileges_data    = isset( $get_roles_settings_data_array['subscriber_privileges'] ) ? explode( ',', $get_roles_settings_data_array['subscriber_privileges'] ) : '0,0,0,0,0,0,0,0,0';
							$other_privileges_data         = isset( $get_roles_settings_data_array['other_roles_privileges'] ) ? explode( ',', $get_roles_settings_data_array['other_roles_privileges'] ) : '0,0,0,0,0,0,0,0,0';

							if ( count( $roles_and_capabilities_data ) === 5 ) {
									array_push( $roles_and_capabilities_data, 0 );
									$get_roles_settings_data_array['roles_and_capabilities'] = implode( ',', $roles_and_capabilities_data );
							}

							if ( count( $administrator_privileges_data ) === 7 ) {
									array_splice( $administrator_privileges_data, 2, 0, 1 );
									array_splice( $administrator_privileges_data, 3, 0, 1 );
									array_splice( $administrator_privileges_data, 6, 0, 1 );
							} elseif ( count( $administrator_privileges_data ) === 8 ) {
									array_splice( $administrator_privileges_data, 2, 0, 1 );
									array_splice( $administrator_privileges_data, 3, 0, 1 );
							} elseif ( count( $administrator_privileges_data ) === 9 ) {
									unset( $administrator_privileges_data[3] );
									array_splice( $administrator_privileges_data, 4, 0, 1 );
							}

							if ( count( $author_privileges_data ) === 7 ) {
										array_splice( $author_privileges_data, 2, 0, 1 );
										array_splice( $author_privileges_data, 3, 0, 1 );
										array_splice( $author_privileges_data, 6, 0, 0 );
							} elseif ( count( $author_privileges_data ) === 8 ) {
										array_splice( $author_privileges_data, 2, 0, 0 );
										array_splice( $author_privileges_data, 3, 0, 0 );
							} elseif ( count( $author_privileges_data ) === 9 ) {
									unset( $author_privileges_data[3] );
									array_splice( $author_privileges_data, 4, 0, 0 );
							}

							if ( count( $editor_privileges_data ) === 7 ) {
									array_splice( $editor_privileges_data, 2, 0, 0 );
									array_splice( $editor_privileges_data, 3, 0, 0 );
									array_splice( $editor_privileges_data, 6, 0, 0 );
							} elseif ( count( $editor_privileges_data ) === 8 ) {
									array_splice( $editor_privileges_data, 2, 0, 0 );
									array_splice( $editor_privileges_data, 3, 0, 0 );
							} elseif ( count( $editor_privileges_data ) === 9 ) {
									unset( $editor_privileges_data[3] );
									array_splice( $editor_privileges_data, 4, 0, 0 );
							}

							if ( count( $contributor_privileges_data ) === 7 ) {
									array_splice( $contributor_privileges_data, 2, 0, 0 );
									array_splice( $contributor_privileges_data, 3, 0, 0 );
									array_splice( $contributor_privileges_data, 6, 0, 0 );
							} elseif ( count( $contributor_privileges_data ) === 8 ) {
									array_splice( $contributor_privileges_data, 2, 0, 0 );
									array_splice( $contributor_privileges_data, 3, 0, 0 );
							} elseif ( count( $contributor_privileges_data ) === 9 ) {
									unset( $contributor_privileges_data[3] );
									array_splice( $contributor_privileges_data, 4, 0, 0 );
							}

							if ( count( $subscriber_privileges_data ) === 7 ) {
									array_splice( $subscriber_privileges_data, 2, 0, 0 );
									array_splice( $subscriber_privileges_data, 3, 0, 0 );
									array_splice( $subscriber_privileges_data, 6, 0, 0 );
							} elseif ( count( $subscriber_privileges_data ) === 8 ) {
									array_splice( $subscriber_privileges_data, 2, 0, 0 );
									array_splice( $subscriber_privileges_data, 3, 0, 0 );
							} elseif ( count( $subscriber_privileges_data ) === 9 ) {
									unset( $subscriber_privileges_data[3] );
									array_splice( $subscriber_privileges_data, 4, 0, 0 );
							}

							if ( count( $other_privileges_data ) === 7 ) {
									array_splice( $other_privileges_data, 2, 0, 0 );
									array_splice( $other_privileges_data, 3, 0, 0 );
									array_splice( $other_privileges_data, 6, 0, 0 );
							} elseif ( count( $other_privileges_data ) === 8 ) {
									array_splice( $other_privileges_data, 2, 0, 0 );
									array_splice( $other_privileges_data, 3, 0, 0 );
							} elseif ( count( $other_privileges_data ) === 9 ) {
									unset( $other_privileges_data[3] );
									array_splice( $other_privileges_data, 4, 0, 0 );
							}

							if ( ! array_key_exists( 'others_full_control_capability', $get_roles_settings_data_array ) ) {
									$get_roles_settings_data_array['others_full_control_capability'] = '0';
							}

							if ( ! array_key_exists( 'capabilities', $get_roles_settings_data_array ) ) {
								$user_capabilities        = get_others_capabilities_mail_booster();
								$other_roles_array        = array();
								$other_roles_access_array = array(
									'manage_options',
									'edit_plugins',
									'edit_posts',
									'publish_posts',
									'publish_pages',
									'edit_pages',
									'read',
								);
								foreach ( $other_roles_access_array as $role ) {
									if ( in_array( $role, $user_capabilities, true ) ) {
											array_push( $other_roles_array, $role );
									}
								}
									$get_roles_settings_data_array['capabilities'] = $other_roles_array;
							}

							$get_roles_settings_data_array['administrator_privileges'] = implode( ',', $administrator_privileges_data );
							$get_roles_settings_data_array['author_privileges']        = implode( ',', $author_privileges_data );
							$get_roles_settings_data_array['editor_privileges']        = implode( ',', $editor_privileges_data );
							$get_roles_settings_data_array['contributor_privileges']   = implode( ',', $contributor_privileges_data );
							$get_roles_settings_data_array['subscriber_privileges']    = implode( ',', $subscriber_privileges_data );
							$get_roles_settings_data_array['other_roles_privileges']   = implode( ',', $other_privileges_data );
							$where                                  = array();
							$roles_capabilities_array               = array();
							$where['meta_key']                      = 'roles_and_capabilities';// WPCS: slow query ok.
							$roles_capabilities_array['meta_value'] = maybe_serialize( $get_roles_settings_data_array );// WPCS: slow query ok.
							$obj_dbhelper_install_script_mail_booster->update_command( mail_booster_meta(), $roles_capabilities_array, $where );
						}
					}
				} else {
					if ( $wpdb->query( "SHOW TABLES LIKE '" . $wpdb->prefix . 'mail_booster' . "'" ) === 0 ) {
						mail_booster_table();// db call ok; no-cache ok.
					}
					if ( $wpdb->query( "SHOW TABLES LIKE '" . $wpdb->prefix . 'mail_booster_meta' . "'" ) === 0 ) {
						mail_booster_meta_table();// db call ok; no-cache ok.
					}
				}
				if ( $wpdb->query( "SHOW TABLES LIKE '" . $wpdb->prefix . 'mail_booster_logs' . "'" ) === 0 ) {
					mail_booster_logs_table();// db call ok; no-cache ok.
					$email_logs_array      = $wpdb->get_results(
						$wpdb->prepare(
							'SELECT meta_value FROM ' . $wpdb->prefix . 'mail_booster_meta WHERE meta_key=%s',
							'email_logs'
						)
					);// WPCS: db call ok; no-cache ok.
					$email_logs_data_array = array();
					foreach ( $email_logs_array as $value ) {
						$unserialize_data                          = maybe_unserialize( $value->meta_value );
						$email_logs_data_array['email_to']         = $unserialize_data['email_to'];
						$email_logs_data_array['cc']               = $unserialize_data['cc'];
						$email_logs_data_array['bcc']              = $unserialize_data['bcc'];
						$email_logs_data_array['subject']          = $unserialize_data['subject'];
						$email_logs_data_array['content']          = $unserialize_data['content'];
						$email_logs_data_array['sender_name']      = $unserialize_data['sender_name'];
						$email_logs_data_array['sender_email']     = $unserialize_data['sender_email'];
						$email_logs_data_array['debug_mode']       = isset( $unserialize_data['debug_mode'] ) ? $unserialize_data['debug_mode'] : '';
						$email_logs_data_array['debugging_output'] = isset( $unserialize_data['debugging_output'] ) ? $unserialize_data['debugging_output'] : '';
						$email_logs_data_array['timestamp']        = $unserialize_data['timestamp'];
						$email_logs_data_array['status']           = $unserialize_data['status'];
						$obj_dbhelper_install_script_mail_booster->insert_command( mail_booster_logs(), $email_logs_data_array );// db call ok; no-cache ok.
					}
					$wpdb->query(
						$wpdb->prepare(
							'DELETE FROM ' . $wpdb->prefix . 'mail_booster_meta WHERE meta_key = %s',
							'email_logs'
						)
					);// db call ok; no-cache ok.
				}
				$get_email_notification_data = $wpdb->get_var(
					$wpdb->prepare(
						'SELECT type FROM ' . $wpdb->prefix . 'mail_booster WHERE type=%s',
						'email_notification'
					)
				); // db call ok; no-cache ok.

				if ( '' == $get_email_notification_data ) {// WPCS: Loose comparison ok.
					$email_notification_data              = array();
					$email_notification_data['type']      = 'email_notification';
					$email_notification_data['parent_id'] = '0';
					$insert_id                            = $obj_dbhelper_install_script_mail_booster->insert_command( mail_booster(), $email_notification_data );

					$email_notification_data_array                               = array();
					$email_notification_data_array['plugin_update_available']    = 'enable';
					$email_notification_data_array['email_plugin_updated']       = 'enable';
					$email_notification_data_array['email_theme_update']         = 'enable';
					$email_notification_data_array['email_wordpress_update']     = 'enable';
					$email_notification_data_array['notification']               = 'disable';
					$email_notification_data_array['notification_service']       = 'email';
					$email_notification_data_array['notification_email_address'] = get_option( 'admin_email' );
					$email_notification_data_array['pushover_user_key']          = '';
					$email_notification_data_array['pushover_app_token']         = '';
					$email_notification_data_array['slack_web_hook']             = '';

					$email_notification_array               = array();
					$email_notification_array['meta_id']    = $insert_id;
					$email_notification_array['meta_key']   = 'email_notification';// WPCS: slow query ok.
					$email_notification_array['meta_value'] = maybe_serialize( $email_notification_data_array );// WPCS: slow query ok.
					$obj_dbhelper_install_script_mail_booster->insert_command( mail_booster_meta(), $email_notification_array );
				}

				$get_collate_status_data = $wpdb->query(
					$wpdb->prepare(
						'SELECT type FROM ' . $wpdb->prefix . 'mail_booster WHERE type=%s',
						'collation_type'
					)
				);// db call ok; no-cache ok.
				$mb_charset_collate      = '';
				if ( ! empty( $wpdb->charset ) ) {
					$mb_charset_collate .= 'CONVERT TO CHARACTER SET ' . $wpdb->charset;
				}
				if ( ! empty( $wpdb->collate ) ) {
					$mb_charset_collate .= ' COLLATE ' . $wpdb->collate;
				}
				if ( 0 === $get_collate_status_data ) {
					if ( ! empty( $mb_charset_collate ) ) {
						$change_collate_main_table         = $wpdb->query(
							'ALTER TABLE ' . $wpdb->prefix . 'mail_booster ' . $mb_charset_collate // @codingStandardsIgnoreLine.
						);// WPCS: db call ok, no-cache ok.
						$change_collate_meta_table         = $wpdb->query(
							'ALTER TABLE ' . $wpdb->prefix . 'mail_booster_meta ' . $mb_charset_collate // @codingStandardsIgnoreLine.
						);// WPCS: db call ok, no-cache ok.
						$change_collate_email_logs_table   = $wpdb->query(
							'ALTER TABLE ' . $wpdb->prefix . 'mail_booster_logs ' . $mb_charset_collate // @codingStandardsIgnoreLine.
						);// WPCS: db call ok, no-cache ok.
						$collation_data_array              = array();
						$collation_data_array['type']      = 'collation_type';
						$collation_data_array['parent_id'] = '0';
						$obj_dbhelper_install_script_mail_booster->insert_command( mail_booster(), $collation_data_array );
					}
				}
		}
		update_option( 'mail-booster-version-number', '3.0.3' );
	}
}
