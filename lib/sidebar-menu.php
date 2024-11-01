<?php
/**
 * This file is used for creating sidebar menu.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/lib
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
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
		$flag = 0;

		$role_capabilities = $wpdb->get_var(
			$wpdb->prepare(
				'SELECT meta_value from ' . $wpdb->prefix . 'mail_booster_meta WHERE meta_key = %s', 'roles_and_capabilities'
			)
		); // WPCS: db call ok; no-cache ok.

		$roles_and_capabilities_unserialized_data = maybe_unserialize( $role_capabilities );
		$capabilities                             = explode( ',', $roles_and_capabilities_unserialized_data['roles_and_capabilities'] );

		if ( is_super_admin() ) {
			$mb_role = 'administrator';
		} else {
			$mb_role = check_user_roles_mail_booster();
		}
		switch ( $mb_role ) {
			case 'administrator':
				$privileges = 'administrator_privileges';
				$flag       = $capabilities[0];
				break;

			case 'author':
				$privileges = 'author_privileges';
				$flag       = $capabilities[1];
				break;

			case 'editor':
				$privileges = 'editor_privileges';
				$flag       = $capabilities[2];
				break;

			case 'contributor':
				$privileges = 'contributor_privileges';
				$flag       = $capabilities[3];
				break;

			case 'subscriber':
				$privileges = 'subscriber_privileges';
				$flag       = $capabilities[4];
				break;

			default:
				$privileges = 'other_roles_privileges';
				$flag       = $capabilities[5];
				break;
		}

		foreach ( $roles_and_capabilities_unserialized_data as $key => $value ) {
			if ( $privileges === $key ) {
				$privileges_value = $value;
				break;
			}
		}

		$full_control = explode( ',', $privileges_value );
		if ( ! defined( 'FULL_CONTROL' ) ) {
			define( 'FULL_CONTROL', "$full_control[0]" );
		}
		if ( ! defined( 'EMAIL_CONFIGURATION_MAIL_BOOSTER' ) ) {
			define( 'EMAIL_CONFIGURATION_MAIL_BOOSTER', "$full_control[1]" );
		}
		if ( ! defined( 'TEST_EMAIL_MAIL_BOOSTER' ) ) {
			define( 'TEST_EMAIL_MAIL_BOOSTER', "$full_control[2]" );
		}
		if ( ! defined( 'EMAIL_LOGS_MAIL_BOOSTER' ) ) {
			define( 'EMAIL_LOGS_MAIL_BOOSTER', "$full_control[3]" );
		}
		if ( ! defined( 'EMAIL_NOTIFICATION_MAIL_BOOSTER' ) ) {
			define( 'EMAIL_NOTIFICATION_MAIL_BOOSTER', "$full_control[4]" );
		}
		if ( ! defined( 'SETTINGS_MAIL_BOOSTER' ) ) {
			define( 'SETTINGS_MAIL_BOOSTER', "$full_control[5]" );
		}
		if ( ! defined( 'ROLES_AND_CAPABILITIES_MAIL_BOOSTER' ) ) {
			define( 'ROLES_AND_CAPABILITIES_MAIL_BOOSTER', "$full_control[6]" );
		}
		if ( ! defined( 'SYSTEM_INFORMATION_MAIL_BOOSTER' ) ) {
			define( 'SYSTEM_INFORMATION_MAIL_BOOSTER', "$full_control[7]" );
		}
		$check_wp_mail_booster_wizard = get_option( 'wp-mail-booster-wizard-set-up' );
		$mail_booster_pro             = '<strong style="color:#f25454;"> ( Pro )</strong>';
		if ( '1' === $flag ) {
			global $wp_version;

			$icon = plugins_url( 'assets/global/img/icon.png', dirname( __FILE__ ) );
			if ( $check_wp_mail_booster_wizard ) {
				add_menu_page( $wp_mail_booster, $wp_mail_booster, 'read', 'mail_booster_email_configuration', '', $icon );
			} else {
				add_menu_page( $wp_mail_booster, $wp_mail_booster, 'read', 'wp_mail_booster_wizard', '', plugins_url( 'assets/global/img/icon.png', dirname( __FILE__ ) ) );
				add_submenu_page( $wp_mail_booster, $wp_mail_booster, '', 'read', 'wp_mail_booster_wizard', 'wp_mail_booster_wizard' );
			}

			add_submenu_page( 'mail_booster_email_configuration', $mail_booster_email_configuration, $mail_booster_email_configuration, 'read', 'mail_booster_email_configuration', false === $check_wp_mail_booster_wizard ? 'wp_mail_booster_wizard' : 'mail_booster_email_configuration' );
			add_submenu_page( 'mail_booster_email_configuration', $mail_booster_test_email, $mail_booster_test_email, 'read', 'mail_booster_test_email', false === $check_wp_mail_booster_wizard ? 'wp_mail_booster_wizard' : 'mail_booster_test_email' );
			add_submenu_page( 'mail_booster_email_configuration', $mail_booster_email_logs, $mail_booster_email_logs . $mail_booster_pro, 'read', 'mail_booster_email_logs', false === $check_wp_mail_booster_wizard ? 'wp_mail_booster_wizard' : 'mail_booster_email_logs' );
			add_submenu_page( 'mail_booster_email_configuration', $mail_booster_email_notification, $mail_booster_email_notification, 'read', 'mail_booster_email_notification', false === $check_wp_mail_booster_wizard ? 'wp_mail_booster_wizard' : 'mail_booster_email_notification' );
			add_submenu_page( 'mail_booster_email_configuration', $mail_booster_settings, $mail_booster_settings, 'read', 'mail_booster_settings', false === $check_wp_mail_booster_wizard ? 'wp_mail_booster_wizard' : 'mail_booster_settings' );
			add_submenu_page( 'mail_booster_email_configuration', $mail_booster_roles_and_capabilities, $mail_booster_roles_and_capabilities, 'read', 'mail_booster_roles_and_capabilities', false === $check_wp_mail_booster_wizard ? 'wp_mail_booster_wizard' : 'mail_booster_roles_and_capabilities' );
			add_submenu_page( 'mail_booster_email_configuration', $mail_booster_upgrade_now, $mail_booster_upgrade_now, 'read', 'mail_booster_upgrade_now', false === $check_wp_mail_booster_wizard ? 'wp_mail_booster_wizard' : 'mail_booster_upgrade_now' );
			add_submenu_page( 'mail_booster_email_configuration', $mail_booster_system_information, $mail_booster_system_information, 'read', 'mail_booster_system_information', false === $check_wp_mail_booster_wizard ? 'wp_mail_booster_wizard' : 'mail_booster_system_information' );

		}

		if ( ! function_exists( 'wp_mail_booster_wizard' ) ) {
			/**
			 * This function is used for creating wp_mail_booster_wizard.
			 */
			function wp_mail_booster_wizard() {
				global $wpdb, $user_role_permission;
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/translations.php' ) ) {
					include MAIL_BOOSTER_DIR_PATH . 'includes/translations.php';
				}
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'views/wizard/wizard.php' ) ) {
					include_once MAIL_BOOSTER_DIR_PATH . 'views/wizard/wizard.php';
				}
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/footer.php' ) ) {
					include_once MAIL_BOOSTER_DIR_PATH . 'includes/footer.php';
				}
			}
		}

		if ( ! function_exists( 'mail_booster_email_configuration' ) ) {
			/**
			 * This function is used for email configuration.
			 */
			function mail_booster_email_configuration() {
				global $wpdb, $user_role_permission;
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/translations.php' ) ) {
					include MAIL_BOOSTER_DIR_PATH . 'includes/translations.php';
				}
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/header.php' ) ) {
					include_once MAIL_BOOSTER_DIR_PATH . 'includes/header.php';
				}
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/queries.php' ) ) {
					include_once MAIL_BOOSTER_DIR_PATH . 'includes/queries.php';
				}
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'views/email-configuration/email-configuration.php' ) ) {
					include_once MAIL_BOOSTER_DIR_PATH . 'views/email-configuration/email-configuration.php';
				}
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/footer.php' ) ) {
					include_once MAIL_BOOSTER_DIR_PATH . 'includes/footer.php';
				}
			}
		}
		if ( ! function_exists( 'mail_booster_test_email' ) ) {
			/**
			 * This function is used to create mail_booster_test_email menu.
			 */
			function mail_booster_test_email() {
				global $wpdb, $user_role_permission;
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/translations.php' ) ) {
					include MAIL_BOOSTER_DIR_PATH . 'includes/translations.php';
				}
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/header.php' ) ) {
					include_once MAIL_BOOSTER_DIR_PATH . 'includes/header.php';
				}
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/queries.php' ) ) {
					include_once MAIL_BOOSTER_DIR_PATH . 'includes/queries.php';
				}
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'views/test-email/test-email.php' ) ) {
					include_once MAIL_BOOSTER_DIR_PATH . 'views/test-email/test-email.php';
				}
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/footer.php' ) ) {
					include_once MAIL_BOOSTER_DIR_PATH . 'includes/footer.php';
				}
			}
		}
		if ( ! function_exists( 'mail_booster_email_logs' ) ) {
			/**
			 * This function is used to create mail_booster_email_logs menu.
			 */
			function mail_booster_email_logs() {
				global $wpdb, $user_role_permission;
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/translations.php' ) ) {
					include MAIL_BOOSTER_DIR_PATH . 'includes/translations.php';
				}
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/header.php' ) ) {
					include_once MAIL_BOOSTER_DIR_PATH . 'includes/header.php';
				}
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/queries.php' ) ) {
					include_once MAIL_BOOSTER_DIR_PATH . 'includes/queries.php';
				}
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'views/email-logs/email-logs.php' ) ) {
					include_once MAIL_BOOSTER_DIR_PATH . 'views/email-logs/email-logs.php';
				}
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/footer.php' ) ) {
					include_once MAIL_BOOSTER_DIR_PATH . 'includes/footer.php';
				}
			}
		}
		if ( ! function_exists( 'mail_booster_email_notification' ) ) {
			/**
			 * This function is used to show email notification.
			 */
			function mail_booster_email_notification() {
				global $wpdb, $user_role_permission;
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/translations.php' ) ) {
					include MAIL_BOOSTER_DIR_PATH . 'includes/translations.php';
				}
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/header.php' ) ) {
					include_once MAIL_BOOSTER_DIR_PATH . 'includes/header.php';
				}
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/queries.php' ) ) {
					include_once MAIL_BOOSTER_DIR_PATH . 'includes/queries.php';
				}
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'views/email-notification/email-notification.php' ) ) {
					include_once MAIL_BOOSTER_DIR_PATH . 'views/email-notification/email-notification.php';
				}
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/footer.php' ) ) {
					include_once MAIL_BOOSTER_DIR_PATH . 'includes/footer.php';
				}
			}
		}
		if ( ! function_exists( 'mail_booster_settings' ) ) {
			/**
			 * This function is used for plugin settings.
			 */
			function mail_booster_settings() {
				global $wpdb, $user_role_permission;
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/translations.php' ) ) {
					include MAIL_BOOSTER_DIR_PATH . 'includes/translations.php';
				}
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/header.php' ) ) {
					include_once MAIL_BOOSTER_DIR_PATH . 'includes/header.php';
				}
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/queries.php' ) ) {
					include_once MAIL_BOOSTER_DIR_PATH . 'includes/queries.php';
				}
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'views/settings/settings.php' ) ) {
					include_once MAIL_BOOSTER_DIR_PATH . 'views/settings/settings.php';
				}
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/footer.php' ) ) {
					include_once MAIL_BOOSTER_DIR_PATH . 'includes/footer.php';
				}
			}
		}
		if ( ! function_exists( 'mail_booster_roles_and_capabilities' ) ) {
			/**
			 * This function is used to create mail_booster_roles_and_capabilities menu.
			 */
			function mail_booster_roles_and_capabilities() {
				global $wpdb, $user_role_permission;
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/translations.php' ) ) {
					include MAIL_BOOSTER_DIR_PATH . 'includes/translations.php';
				}
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/header.php' ) ) {
					include_once MAIL_BOOSTER_DIR_PATH . 'includes/header.php';
				}
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/queries.php' ) ) {
					include_once MAIL_BOOSTER_DIR_PATH . 'includes/queries.php';
				}
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'views/roles-and-capabilities/roles-and-capabilities.php' ) ) {
					include_once MAIL_BOOSTER_DIR_PATH . 'views/roles-and-capabilities/roles-and-capabilities.php';
				}
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/footer.php' ) ) {
					include_once MAIL_BOOSTER_DIR_PATH . 'includes/footer.php';
				}
			}
		}
		if ( ! function_exists( 'mail_booster_upgrade_now' ) ) {
			/**
			 * This function is used for dashboard.
			 */
			function mail_booster_upgrade_now() {
				global $wpdb, $user_role_permission;
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/translations.php' ) ) {
					include MAIL_BOOSTER_DIR_PATH . 'includes/translations.php';
				}
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/header.php' ) ) {
					include_once MAIL_BOOSTER_DIR_PATH . 'includes/header.php';
				}
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/queries.php' ) ) {
					include_once MAIL_BOOSTER_DIR_PATH . 'includes/queries.php';
				}
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'views/pricing-plans/pricing-plans.php' ) ) {
					include_once MAIL_BOOSTER_DIR_PATH . 'views/pricing-plans/pricing-plans.php';
				}
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/footer.php' ) ) {
					include_once MAIL_BOOSTER_DIR_PATH . 'includes/footer.php';
				}
			}
		}
		if ( ! function_exists( 'mail_booster_system_information' ) ) {
			/**
			 * This function is used to create mail_booster_system_information menu.
			 */
			function mail_booster_system_information() {
				global $wpdb, $user_role_permission;
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/translations.php' ) ) {
					include MAIL_BOOSTER_DIR_PATH . 'includes/translations.php';
				}
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/header.php' ) ) {
					include_once MAIL_BOOSTER_DIR_PATH . 'includes/header.php';
				}
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/queries.php' ) ) {
					include_once MAIL_BOOSTER_DIR_PATH . 'includes/queries.php';
				}
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'views/system-information/system-information.php' ) ) {
					include_once MAIL_BOOSTER_DIR_PATH . 'views/system-information/system-information.php';
				}
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/footer.php' ) ) {
					include_once MAIL_BOOSTER_DIR_PATH . 'includes/footer.php';
				}
			}
		}
	}
}
