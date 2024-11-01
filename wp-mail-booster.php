<?php // @codingStandardsIgnoreLine.
/**
 * Plugin Name: Email SMTP Plugin by Mail Booster
 * Plugin URI: https://tech-banker.com/wp-mail-booster/
 * Description: Mail Booster allows you to send emails from your WordPress site with advanced SMTP Settings or PHPMailer.
 * Author: Tech Banker
 * Author URI: https://tech-banker.com/wp-mail-booster/
 * Version: 4.0.4
 * License: GPLv3
 * Text Domain: wp-mail-booster
 * Domain Path: /languages
 *
 * @package wp-mail-booster
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
/* Constant Declaration */
if ( ! defined( 'MAIL_BOOSTER_FILE' ) ) {
	define( 'MAIL_BOOSTER_FILE', plugin_basename( __FILE__ ) );
}
if ( ! defined( 'MAIL_BOOSTER_DIR_PATH' ) ) {
	define( 'MAIL_BOOSTER_DIR_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'MAIL_BOOSTER_PLUGIN_DIRNAME' ) ) {
	define( 'MAIL_BOOSTER_PLUGIN_DIRNAME', plugin_basename( dirname( __FILE__ ) ) );
}
if ( ! defined( 'MAIL_BOOSTER_LOCAL_TIME' ) ) {
	define( 'MAIL_BOOSTER_LOCAL_TIME', strtotime( date_i18n( 'Y-m-d H:i:s' ) ) );
}
if ( ! defined( 'TECH_BANKER_URL' ) ) {
	define( 'TECH_BANKER_URL', 'https://tech-banker.com' );
}
if ( ! defined( 'TECH_BANKER_BETA_URL' ) ) {
	define( 'TECH_BANKER_BETA_URL', 'https://tech-banker.com/wp-mail-booster' );
}
if ( ! defined( 'TECH_BANKER_STATS_URL' ) ) {
	define( 'TECH_BANKER_STATS_URL', 'http://stats.tech-banker-services.org' );
}
if ( ! defined( 'MAIL_BOOSTER_VERSION_NUMBER' ) ) {
	define( 'MAIL_BOOSTER_VERSION_NUMBER', '4.0.4' );
}

$memory_limit_mail_booster = intval( ini_get( 'memory_limit' ) );
if ( ! extension_loaded( 'suhosin' ) && $memory_limit_mail_booster < 512 ) {
	ini_set( 'memory_limit', '512M' );// @codingStandardsIgnoreLine.
}
ini_set( 'max_execution_time', 6000 );// @codingStandardsIgnoreLine.
ini_set( 'max_input_vars', 10000 );// @codingStandardsIgnoreLine.

if ( ! function_exists( 'install_script_for_mail_booster' ) ) {
	/**
	 * Function Name: install_script_for_mail_booster
	 * Parameters: No
	 * Description: This function is used to create Tables in Database.
	 * Created On: 15-06-2016 09:52
	 * Created By: Tech Banker Team
	 */
	function install_script_for_mail_booster() {
		global $wpdb;
		if ( is_multisite() ) {
			$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );// WPCS: db call ok; no-cache ok.
			foreach ( $blog_ids as $blog_id ) {
				switch_to_blog( $blog_id );// @codingStandardsIgnoreLine.
				$version = get_option( 'mail-booster-version-number' );
				if ( $version < '3.0.3' ) {
					if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'lib/class-dbhelper-install-script-mail-booster.php' ) ) {
						include MAIL_BOOSTER_DIR_PATH . 'lib/class-dbhelper-install-script-mail-booster.php';
					}
				}
				restore_current_blog();
			}
		} else {
			$version = get_option( 'mail-booster-version-number' );
			if ( $version < '3.0.3' ) {
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'lib/class-dbhelper-install-script-mail-booster.php' ) ) {
					include_once MAIL_BOOSTER_DIR_PATH . 'lib/class-dbhelper-install-script-mail-booster.php';
				}
			}
		}
	}
}

if ( ! function_exists( 'check_user_roles_mail_booster' ) ) {
	/**
	 * Function Name: check_user_roles_mail_booster
	 * Parameters: Yes($user)
	 * Description: This function is used for checking roles of different users.
	 * Created On: 19-10-2016 03:40
	 * Created By: Tech Banker Team
	 */
	function check_user_roles_mail_booster() {
		global $current_user;
		$user = $current_user ? new WP_User( $current_user ) : wp_get_current_user();
		return $user->roles ? $user->roles[0] : false;
	}
}

if ( ! function_exists( 'mail_booster' ) ) {
	/**
	 * Function Name: mail_booster
	 * Parameters: No
	 * Description: This function is used to return Parent Table name with prefix.
	 * Created On: 15-06-2016 10:44
	 * Created By: Tech Banker Team
	 */
	function mail_booster() {
		global $wpdb;
		return $wpdb->prefix . 'mail_booster';
	}
}
if ( ! function_exists( 'mail_booster_logs' ) ) {
	/**
	 * Function Name: mail_booster_logs
	 * Parameters: No
	 * Description: This function is used to return Email Logs Table name with prefix.
	 * Created On: 18-07-2018 11:48
	 * Created By: Tech Banker Team
	 */
	function mail_booster_logs() {
		global $wpdb;
		return $wpdb->prefix . 'mail_booster_logs';
	}
}
if ( ! function_exists( 'mail_booster_meta' ) ) {
	/**
	 * Function Name: mail_booster_meta
	 * Parameters: No
	 * Description: This function is used to return Meta Table name with prefix.
	 * Created On: 15-06-2016 10:44
	 * Created By: Tech Banker Team
	 */
	function mail_booster_meta() {
		global $wpdb;
		return $wpdb->prefix . 'mail_booster_meta';
	}
}

if ( ! function_exists( 'get_others_capabilities_mail_booster' ) ) {
	/**
	 * Function Name: get_others_capabilities_mail_booster
	 * Parameters: No
	 * Description: This function is used to get all the roles available in WordPress
	 * Created On: 21-10-2016 12:06
	 * Created By: Tech Banker Team
	 */
	function get_others_capabilities_mail_booster() {
		$user_capabilities = array();
		if ( function_exists( 'get_editable_roles' ) ) {
			foreach ( get_editable_roles() as $role_name => $role_info ) {
				foreach ( $role_info['capabilities'] as $capability => $_ ) {
					if ( ! in_array( $capability, $user_capabilities, true ) ) {
						array_push( $user_capabilities, $capability );
					}
				}
			}
		} else {
			$user_capabilities = array(
				'manage_options',
				'edit_plugins',
				'edit_posts',
				'publish_posts',
				'publish_pages',
				'edit_pages',
				'read',
			);
		}
		return $user_capabilities;
	}
}
/**
 * Function Name: mail_booster_action_links
 * Parameters: Yes
 * Description: This function is used to create link for Pro Editions.
 * Created On: 24-04-2017 12:20
 * Created By: Tech Banker Team
 *
 * @param string $plugin_link .
 */
function mail_booster_action_links( $plugin_link ) {
	$plugin_link[] = '<a href="https://tech-banker.com/wp-mail-booster/pricing/" style="color: red; font-weight: bold;" target="_blank">Go Pro!</a>';
	return $plugin_link;
}

if ( ! function_exists( 'mail_booster_settings_link' ) ) {
	/**
	 * This function is used to add settings link.
	 *
	 * @param string $action .
	 */
	function mail_booster_settings_link( $action ) {
		global $wpdb, $user_role_permission;
		$settings_link = '<a href = "' . admin_url( 'admin.php?page=mail_booster_email_configuration' ) . '">Settings</a>';
		array_unshift( $action, $settings_link );
		return $action;
	}
}

$version = get_option( 'mail-booster-version-number' );
if ( $version >= '3.0.3' ) {

	if ( ! function_exists( 'get_users_capabilities_mail_booster' ) ) {
		/**
		 * Function Name: get_users_capabilities_mail_booster
		 * Parameters: No
		 * Description: This function is used to get users capabilities.
		 * Created On: 21-10-2016 15:21
		 * Created By: Tech Banker Team
		 */
		function get_users_capabilities_mail_booster() {
			global $wpdb, $user_role_permission;
			$user_role_permission      = array();
			$capabilities              = $wpdb->get_var(
				$wpdb->prepare( 'SELECT meta_value FROM ' . $wpdb->prefix . 'mail_booster_meta WHERE meta_key = %s', 'roles_and_capabilities' )
			);// WPCS: db call ok; no-cache ok.
			$core_roles                = array(
				'manage_options',
				'edit_plugins',
				'edit_posts',
				'publish_posts',
				'publish_pages',
				'edit_pages',
				'read',
			);
			$unserialized_capabilities = maybe_unserialize( $capabilities );
			$user_role_permission      = isset( $unserialized_capabilities['capabilities'] ) ? $unserialized_capabilities['capabilities'] : $core_roles;
			return $user_role_permission;
		}
	}
	if ( ! function_exists( 'get_notifications_data_mail_booster' ) ) {
		/**
		 * This function is used to get notification data.
		 */
		function get_notifications_data_mail_booster() {
			global $wpdb, $email_notification_data_array;
			$meta_value                    = $wpdb->get_var(
				$wpdb->prepare(
					'SELECT meta_value FROM ' . $wpdb->prefix . 'mail_booster_meta WHERE meta_key=%s', 'email_notification'
				)
			);// WPCS: db call ok; no-cache ok.
			$email_notification_data_array = maybe_unserialize( $meta_value );
			return $email_notification_data_array;
		}
	}

	/**
	 * Function Name: add_dashboard_widgets_mail_booster
	 * Parameters: No
	 * Description: This function is used to add a widget to the dashboard.
	 * Created On: 24-08-2017 15:20
	 * Created By: Tech Banker Team
	 */
	function add_dashboard_widgets_mail_booster() {
		wp_add_dashboard_widget(
			'mail_booster_dashboard_widget', // Widget slug.
			'Mail Booster Statistics', // Title.
			'dashboard_widget_function_mail_booster'// Display function.
		);
	}
	/**
	 * Function Name: dashboard_widget_function_mail_booster
	 * Parameters: No
	 * Description: This function is used to to output the contents of our Dashboard Widget.
	 * Created On: 29-08-2017 15:20
	 * Created By: Tech Banker Team
	 */
	function dashboard_widget_function_mail_booster() {

		global $wpdb;
		if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'lib/dashboard-widget.php' ) ) {
			include_once MAIL_BOOSTER_DIR_PATH . 'lib/dashboard-widget.php';
		}
	}

	if ( is_admin() ) {
		if ( ! function_exists( 'backend_js_css_for_mail_booster' ) ) {
			/**
			 * This function is used for calling css and js files for backend
			 */
			function backend_js_css_for_mail_booster() {
				$pages_mail_booster = array(
					'wp_mail_booster_wizard',
					'mail_booster_email_configuration',
					'mail_booster_test_email',
					'mail_booster_email_logs',
					'mail_booster_email_notification',
					'mail_booster_settings',
					'mail_booster_roles_and_capabilities',
					'mail_booster_upgrade_now',
					'mail_booster_system_information',
				);
				if ( in_array( isset( $_REQUEST['page'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ) : '', $pages_mail_booster, true ) ) { // WPCS: CSRF ok, WPCS: input var ok.
					wp_enqueue_script( 'jquery' );
					wp_enqueue_script( 'jquery-ui-datepicker' );
					wp_enqueue_script( 'mail-booster-bootstrap.js', plugins_url( 'assets/global/plugins/custom/js/custom.js', __FILE__ ) );
					wp_enqueue_script( 'mail-booster-jquery.validate.js', plugins_url( 'assets/global/plugins/validation/jquery.validate.js', __FILE__ ) );
					wp_enqueue_script( 'mail-booster-jquery.datatables.js', plugins_url( 'assets/global/plugins/datatables/media/js/jquery.datatables.js', __FILE__ ) );
					wp_enqueue_script( 'mail-booster-jquery.fngetfilterednodes.js', plugins_url( 'assets/global/plugins/datatables/media/js/fngetfilterednodes.js', __FILE__ ) );
					wp_enqueue_script( 'mail-booster-toastr.js', plugins_url( 'assets/global/plugins/toastr/toastr.js', __FILE__ ) );
					wp_enqueue_script( 'jquery.clipboard.js', plugins_url( 'assets/global/plugins/clipboard/clipboard.js', __FILE__ ) );
					wp_enqueue_script( 'jquery.chart.js', plugins_url( 'assets/global/plugins/chart/chart.js', __FILE__ ) );
					wp_enqueue_style( 'mail-booster-components.css', plugins_url( 'assets/global/css/components.css', __FILE__ ) );
					wp_enqueue_style( 'mail-booster-custom.css', plugins_url( 'assets/admin/layout/css/mail-booster-custom.css', __FILE__ ) );
					if ( is_rtl() ) {
						wp_enqueue_style( 'mail-booster-bootstrap.css', plugins_url( 'assets/global/plugins/custom/css/custom-rtl.css', __FILE__ ) );
						wp_enqueue_style( 'mail-booster-layout.css', plugins_url( 'assets/admin/layout/css/layout-rtl.css', __FILE__ ) );
						wp_enqueue_style( 'mail-booster-tech-banker-custom.css', plugins_url( 'assets/admin/layout/css/tech-banker-custom-rtl.css', __FILE__ ) );
					} else {
						wp_enqueue_style( 'mail-booster-bootstrap.css', plugins_url( 'assets/global/plugins/custom/css/custom.css', __FILE__ ) );
						wp_enqueue_style( 'mail-booster-layout.css', plugins_url( 'assets/admin/layout/css/layout.css', __FILE__ ) );
						wp_enqueue_style( 'mail-booster-tech-banker-custom.css', plugins_url( 'assets/admin/layout/css/tech-banker-custom.css', __FILE__ ) );
					}
					wp_enqueue_style( 'mail-booster-toastr.min.css', plugins_url( 'assets/global/plugins/toastr/toastr.css', __FILE__ ) );
					wp_enqueue_style( 'mail-booster-jquery-ui.css', plugins_url( 'assets/global/plugins/datepicker/jquery-ui.css', __FILE__ ), false, '2.0', false );
					wp_enqueue_style( 'mail-booster-datatables.foundation.css', plugins_url( 'assets/global/plugins/datatables/media/css/datatables.foundation.css', __FILE__ ) );
				}
				$database_update_option = get_option( 'mail_booster_update_database' );
				if ( false == $database_update_option ) { // WPCS: Loose comparison ok.
					wp_enqueue_script( 'jquery' );
					wp_enqueue_script( 'mail-booster-toastr.js', plugins_url( 'assets/global/plugins/toastr/toastr.js', __FILE__ ) );
					wp_enqueue_style( 'mail-booster-toastr.min.css', plugins_url( 'assets/global/plugins/toastr/toastr.css', __FILE__ ) );
					wp_enqueue_script( 'mail-booster-database-upgrade.js', plugins_url( 'assets/global/plugins/database-upgrade/database-upgrade.js', __FILE__ ) );
				}
			}
		}
		add_action( 'admin_enqueue_scripts', 'backend_js_css_for_mail_booster' );
	}

	if ( ! function_exists( 'helper_file_for_mail_booster' ) ) {
		/**
		 * Function Name: helper_file_for_mail_booster
		 * Parameters: No
		 * Description: This function is used to create Class and Function to perform operations.
		 * Created On: 15-06-2016 09:52
		 * Created By: Tech Banker Team
		 */
		function helper_file_for_mail_booster() {
			global $wpdb, $user_role_permission;
			if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'lib/class-dbhelper-mail-booster.php' ) ) {
				include_once MAIL_BOOSTER_DIR_PATH . 'lib/class-dbhelper-mail-booster.php';
			}
		}
	}

	if ( ! function_exists( 'sidebar_menu_for_mail_booster' ) ) {
		/**
		 * This function is used to create Admin sidebar menus.
		 */
		function sidebar_menu_for_mail_booster() {
			global $wpdb, $current_user, $user_role_permission;
			if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/translations.php' ) ) {
				include MAIL_BOOSTER_DIR_PATH . 'includes/translations.php';
			}
			if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'lib/sidebar-menu.php' ) ) {
				include_once MAIL_BOOSTER_DIR_PATH . 'lib/sidebar-menu.php';
			}
		}
	}

	if ( ! function_exists( 'topbar_menu_for_mail_booster' ) ) {
		/**
		 * Function Name: topbar_menu_for_mail_booster
		 * Parameters: No
		 * Description: This function is used for creating Top bar menu.
		 * Created On: 15-06-2016 10:44
		 * Created By: Tech Banker Team
		 */
		function topbar_menu_for_mail_booster() {
			global $wpdb, $current_user, $wp_admin_bar, $user_role_permission;
			$role_capabilities                        = $wpdb->get_var(
				$wpdb->prepare(
					'SELECT meta_value FROM ' . $wpdb->prefix . 'mail_booster_meta WHERE meta_key = %s', 'roles_and_capabilities'
				)
			);// WPCS: db call ok; no-cache ok.
			$roles_and_capabilities_unserialized_data = maybe_unserialize( $role_capabilities );
			$top_bar_menu                             = $roles_and_capabilities_unserialized_data['show_mail_booster_top_bar_menu'];

			if ( 'enable' === $top_bar_menu ) {
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/translations.php' ) ) {
					include MAIL_BOOSTER_DIR_PATH . 'includes/translations.php';
				}
				if ( get_option( 'wp-mail-booster-wizard-set-up' ) ) {
					if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'lib/admin-bar-menu.php' ) ) {
						include_once MAIL_BOOSTER_DIR_PATH . 'lib/admin-bar-menu.php';
					}
				}
			}
		}
	}

	if ( ! function_exists( 'ajax_register_for_mail_booster' ) ) {
		/**
		 * Function Name: ajax_register_for_mail_booster
		 * Parameters: No
		 * Description: This function is used for register ajax.
		 * Created On: 15-06-2016 10:44
		 * Created By: Tech Banker Team
		 */
		function ajax_register_for_mail_booster() {
			global $wpdb, $user_role_permission;
			if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/translations.php' ) ) {
				include MAIL_BOOSTER_DIR_PATH . 'includes/translations.php';
			}
			if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'lib/action-library.php' ) ) {
				include_once MAIL_BOOSTER_DIR_PATH . 'lib/action-library.php';
			}
		}
	}

	if ( ! function_exists( 'plugin_update_mail_booster' ) ) {
		/**
		 * This function is used to send email when plugin update is available .
		 */
		function plugin_update_mail_booster() {
			global $email_notification_data_array;
			if ( 'enable' === $email_notification_data_array['plugin_update_available'] ) {
				require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
				require_once ABSPATH . '/wp-admin/includes/update.php';
				$plugins_update = get_plugin_updates();
				if ( ! empty( $plugins_update ) ) {
					foreach ( $plugins_update as $key ) {
						$update_plugin = $key->update;
						$to            = get_option( 'admin_email' );
						$subject       = $key->Name . ' Plugin Update Available'; //@codingStandardsIgnoreLine.
						$message       = '<p>There is a Plugin Update available on your WordPress site ' . site_url() . '</p><p>Login to your WordPress site dashboard and manually update the Plugin <strong>' . $key->Name . '</strong> to version <strong>' . $update_plugin->new_version . '</strong>.</p>'; //@codingStandardsIgnoreLine.
						$headers       = 'Content-Type: text/html; charset= utf-8' . "\r\n";
						$result        = wp_mail( $to, $subject, $message, $headers );
					}
				}
			} else {
				wp_clear_scheduled_hook( 'mail_booster_plugin_update_scheduler' );
			}
		}
	}
	if ( ! function_exists( 'mail_booster_plugin_update_scheduler' ) ) {
		/**
		 * This function is used to scheduled plugin update.
		 */
		function mail_booster_plugin_update_scheduler() {
			if ( ! wp_next_scheduled( 'mail_booster_plugin_update_scheduler' ) ) {
				wp_schedule_event( MAIL_BOOSTER_LOCAL_TIME, 'daily', 'mail_booster_plugin_update_scheduler' );
			}
		}
	}

	if ( ! function_exists( 'theme_update_mail_booster' ) ) {
		/**
		 * This function is used to send email when theme update is available .
		 */
		function theme_update_mail_booster() {
			global $email_notification_data_array;
			if ( 'enable' === $email_notification_data_array['email_theme_update'] ) {
				require_once ABSPATH . '/wp-admin/includes/update.php';
				$themes_update = get_theme_updates();
				if ( ! empty( $themes_update ) ) {
					foreach ( $themes_update as $key ) {
						$theme_update_data = $key->update;
						$to                = get_option( 'admin_email' );
						$subject           = $theme_update_data['theme'] . ' Theme Update Available ';
						$message           =  '<p>There is a Theme Update available on your WordPress site ' . site_url() . '</p><p>Login to your WordPress site dashboard and manually update the Theme <strong>' . $theme_update_data['theme'] . '</strong> to version <strong>' . $theme_update_data['new_version'] . '</strong>.</p>'; //@codingStandardsIgnoreLine.
						$headers           = 'Content-Type: text/html; charset= utf-8' . "\r\n";
						$result            = wp_mail( $to, $subject, $message, $headers );
					}
				}
			} else {
				wp_clear_scheduled_hook( 'mail_booster_theme_update_scheduler' );
			}
		}
	}

	if ( ! function_exists( 'mail_booster_theme_update_scheduler' ) ) {
		/**
		 * This function is used to scheduled theme update.
		 */
		function mail_booster_theme_update_scheduler() {
			if ( ! wp_next_scheduled( 'mail_booster_theme_update_scheduler' ) ) {
				wp_schedule_event( MAIL_BOOSTER_LOCAL_TIME, 'daily', 'mail_booster_theme_update_scheduler' );
			}
		}
	}

	if ( ! function_exists( 'plugin_updated_mail_booster' ) ) {
		/**
		 * This function is used to send email after plugin update .
		 */
		function plugin_updated_mail_booster() {
			global $email_notification_data_array;
			if ( 'enable' === $email_notification_data_array['email_plugin_updated'] ) {
				require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
				require_once ABSPATH . '/wp-admin/includes/update.php';
				$plugin_names         = array();
				$plugins_updated_date = array();
				$plugin_updated       = array();
				$plugin_dir           = plugin_dir_path( __DIR__ );
				if ( function_exists( 'get_plugins' ) ) {
					$plugin_updated = get_plugins();
				}
				foreach ( $plugin_updated as $key => $value ) {
					$plugin_full_path  = $plugin_dir . '/' . $key;
					$plugin_info       = get_plugin_data( $plugin_full_path );
					$last_day          = date( 'YmdHi', strtotime( '-1 day' ) );
					$file_updated_date = date( 'YmdHi', filemtime( $plugin_full_path ) );
					if ( $file_updated_date >= $last_day ) {
						foreach ( $plugin_info as $data_key => $data_value ) {
							if ( 'Name' === $data_key ) {
								array_push( $plugin_names, $data_value );
							}
						}
						array_push( $plugins_updated_date, strtotime( $file_updated_date ) );
					}
				}
				$total_num    = 0;
				$updated_list = '<ul>';
				foreach ( $plugins_updated_date as $key => $value ) {
					$updated_list .= '<li>';
					$updated_list .= $plugin_names[ $key ] . ' - ' . date_i18n( 'd M Y h:i A', $value );
					$updated_list .= '</li>';
					$total_num++;
				}
				$updated_list .= '</ul>';
				if ( $total_num > 0 ) {
					$to      = get_option( 'admin_email' );
					$subject = 'One or more plugins have been updated on your WordPress Website';
					$message = '<p>You have updated Plugins on your WordPress site ' . site_url() . '. Login to your WordPress site dashboard and be sure to check if everything still works properly.</p><p>The Following are the list of updated Plugins : </p>' . $updated_list; //@codingStandardsIgnoreLine.
					$headers = 'Content-Type: text/html; charset= utf-8' . "\r\n";
					$result  = wp_mail( $to, $subject, $message, $headers );
				}
			} else {
				wp_clear_scheduled_hook( 'mail_booster_plugin_updated_scheduler' );
			}
		}
	}

	if ( ! function_exists( 'mail_booster_plugin_updated_scheduler' ) ) {
		/**
		 * This function is used to scheduled when plugin updated.
		 */
		function mail_booster_plugin_updated_scheduler() {
			if ( ! wp_next_scheduled( 'mail_booster_plugin_updated_scheduler' ) ) {
				wp_schedule_event( MAIL_BOOSTER_LOCAL_TIME, 'daily', 'mail_booster_plugin_updated_scheduler' );
			}
		}
	}

	if ( ! function_exists( 'plugin_load_textdomain_mail_booster' ) ) {
		/**
		 * Function Name: plugin_load_textdomain_mail_booster
		 * Parameters: No
		 * Description: This function is used to load the plugin's translated strings.
		 * Created On: 16-06-2016 09:47
		 * Created By: Tech Banker Team
		 */
		function plugin_load_textdomain_mail_booster() {
			if ( function_exists( 'load_plugin_textdomain' ) ) {
				load_plugin_textdomain( 'wp-mail-booster', false, MAIL_BOOSTER_PLUGIN_DIRNAME . '/languages' );
			}
		}
	}

	if ( ! function_exists( 'oauth_handling_mail_booster' ) ) {
		/**
		 * Function Name: oauth_handling_mail_booster
		 * Parameters: No
		 * Description: This function is used to Manage Redirect.
		 * Created On: 11-08-2016 11:53
		 * Created By: Tech Banker Team
		 */
		function oauth_handling_mail_booster() {
			if ( is_admin() ) {
				if ( ( count( $_REQUEST ) <= 3 ) && isset( $_REQUEST['code'] ) && isset( $_REQUEST['state'] ) && 'wp-mail-booster' == $_REQUEST['state'] ) { // WPCS: CSRF ok, WPCS: input var ok,loose comparison ok.
					if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'lib/callback.php' ) ) {
						include_once MAIL_BOOSTER_DIR_PATH . 'lib/callback.php';
					}
				} elseif ( ( count( $_REQUEST ) <= 3 ) && isset( $_REQUEST['error'] ) ) { // WPCS: CSRF ok, WPCS: input var ok.
					$url = admin_url( 'admin.php?page=mail_booster_email_configuration' );
					header( "location: $url" );
				}
			}
		}
	}

	if ( ! function_exists( 'email_configuration_mail_booster' ) ) {
		/**
		 * This function is used for checking test email.
		 *
		 * @param string $phpmailer .
		 */
		function email_configuration_mail_booster( $phpmailer ) {
			global $wpdb;
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
			$email_configuration_data       = $wpdb->get_var(
				$wpdb->prepare(
					'SELECT meta_value FROM ' . $mb_table_prefix . 'mail_booster_meta WHERE meta_key = %s', 'email_configuration'
				)
			);// WPCS: db call ok; no-cache ok, unprepared SQL ok.
			$email_configuration_data_array = maybe_unserialize( $email_configuration_data );

			$phpmailer->Mailer = 'php_mail_function' === $email_configuration_data_array['mailer_type'] ? 'mail' : 'smtp';// @codingStandardsIgnoreLine
			if ( 'override' === $email_configuration_data_array['sender_name_configuration'] ) {
				$phpmailer->FromName = stripcslashes( htmlspecialchars_decode( $email_configuration_data_array['sender_name'], ENT_QUOTES ) );// @codingStandardsIgnoreLine
			}
			if ( 'override' === $email_configuration_data_array['from_email_configuration'] ) {
				$phpmailer->From = $email_configuration_data_array['sender_email'];// @codingStandardsIgnoreLine
			}
			if ( '' !== $email_configuration_data_array['reply_to'] ) {
				$phpmailer->clearReplyTos();
				$phpmailer->AddReplyTo( $email_configuration_data_array['reply_to'] );
			}
			if ( isset( $email_configuration_data_array['headers'] ) && '' !== $email_configuration_data_array['headers'] ) {
				$phpmailer->addCustomHeader( $email_configuration_data_array['headers'] );
			}
			$phpmailer->Sender = $email_configuration_data_array['email_address'];// @codingStandardsIgnoreLine
			$phpmailer->ContentType = "text/html";// @codingStandardsIgnoreLine
			if ( 'smtp' === $email_configuration_data_array['mailer_type'] ) {
				$phpmailer->SMTPOptions = array(// @codingStandardsIgnoreLine
					'ssl' => array(
						'verify_peer'       => false,
						'verify_peer_name'  => false,
						'allow_self_signed' => true,
					) );// @codingStandardsIgnoreLine
				switch ( $email_configuration_data_array['enc_type'] ) {
					case 'none':
						$phpmailer->SMTPSecure = '';// @codingStandardsIgnoreLine
						break;
					case 'ssl':
						$phpmailer->SMTPSecure = 'ssl';// @codingStandardsIgnoreLine
						break;
					case 'tls':
						$phpmailer->SMTPSecure = 'tls';// @codingStandardsIgnoreLine
						break;
				}
				$phpmailer->Host          = $email_configuration_data_array['hostname']; // @codingStandardsIgnoreLine
				$phpmailer->Port          = $email_configuration_data_array['port']; // @codingStandardsIgnoreLine
				$phpmailer->SMTPKeepAlive = true; // @codingStandardsIgnoreLine

				if ( 'login' === $email_configuration_data_array['auth_type'] || 'plain' === $email_configuration_data_array['auth_type'] || 'crammd5' === $email_configuration_data_array['auth_type'] ) {
					$phpmailer->SMTPAuth = true; // @codingStandardsIgnoreLine
					if ( 'plain' === $email_configuration_data_array['auth_type'] ) {
						$phpmailer->AuthType = 'PLAIN';// @codingStandardsIgnoreLine
					} elseif ( 'login' === $email_configuration_data_array['auth_type'] ) {
						$phpmailer->AuthType = 'LOGIN';// @codingStandardsIgnoreLine
					} else {
						$phpmailer->AuthType = 'CRAM-MD5';// @codingStandardsIgnoreLine
					}
					$phpmailer->Username = $email_configuration_data_array['username']; // @codingStandardsIgnoreLine
					$phpmailer->Password = base64_decode( $email_configuration_data_array['password'] ); // @codingStandardsIgnoreLine
				} else {
					$phpmailer->SMTPAuth = true; // @codingStandardsIgnoreLine
					$phpmailer->AuthType = 'NTLM';// @codingStandardsIgnoreLine
				}
			}
		}
	}
	if ( ! function_exists( 'mail_booster_compatibility_warning' ) ) {
			/**
			 * This Function is used to include CSS File.
			 */
		function mail_booster_compatibility_warning() {
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'mail-booster-jquery.validate.js', plugins_url( 'assets/global/plugins/validation/jquery.validate.js', __FILE__ ) );
			if ( is_rtl() ) {
				wp_enqueue_style( 'tech-banker-compatibility-rtl.css', plugins_url( 'assets/admin/layout/css/tech-banker-compatibility-rtl.css', __FILE__ ) );
			}
			wp_enqueue_style( 'tech-banker-compatibility.css', plugins_url( 'assets/admin/layout/css/tech-banker-compatibility.css', __FILE__ ) );
		}
	}
	if ( ! function_exists( 'admin_functions_for_mail_booster' ) ) {
		/**
		 * Function Name: admin_functions_for_mail_booster
		 * Parameters: No
		 * Description: This function is used for calling admin_init functions.
		 * Created On: 15-06-2016 10:44
		 * Created By: Tech Banker Team
		 */
		function admin_functions_for_mail_booster() {
			global $user_role_permission;
			install_script_for_mail_booster();
			helper_file_for_mail_booster();
			mail_booster_plugin_update_scheduler();
			mail_booster_theme_update_scheduler();
			mail_booster_plugin_updated_scheduler();
			mail_booster_compatibility_warning();
		}
	}

	if ( ! function_exists( 'mailer_file_for_mail_booster' ) ) {
		/**
		 * Function Name: mailer_file_for_mail_booster
		 * Parameters: No
		 * Description: This function is used for including Mailer File.
		 * Created On: 30-06-2016 02:13
		 * Created By: Tech Banker Team
		 */
		function mailer_file_for_mail_booster() {
			if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/class-mail-booster-auth-host.php' ) ) {
				include_once MAIL_BOOSTER_DIR_PATH . 'includes/class-mail-booster-auth-host.php';
			}
		}
	}

	if ( ! function_exists( 'wp_mail_booster_plugin_update_message' ) ) {
		/**
		 * This function is used to Display Plugin's update message.
		 *
		 * @param string $args .
		 */
		function wp_mail_booster_plugin_update_message( $args ) {
			$response = wp_remote_get( TECH_BANKER_URL . '/plugin-updates/change-logs/wp-mail-booster-readme.txt' );// @codingStandardsIgnoreLine.
			if ( ! is_wp_error( $response ) && ! empty( $response['body'] ) ) {
				$matches        = null;
				$regexp         = '~==\s*Changelog\s*==\s*=\s*[0-9.]+\s*=(.*)(=\s*' . preg_quote( $args['Version'] ) . '\s*=|$)~Uis';
				$upgrade_notice = '';
				if ( preg_match( $regexp, $response['body'], $matches ) ) {
					$changelog       = (array) preg_split( "~[\r\n]+~", trim( $matches[1] ) );
					$upgrade_notice .= "<div class='plugin_update_message'>";
					foreach ( $changelog as $index => $line ) {
						$upgrade_notice .= '<p>' . $line . '</p>';
					}
					$upgrade_notice .= '</div> ';
					echo $upgrade_notice;// @codingStandardsIgnoreLine.
				}
			}
		}
	}
	if ( ! function_exists( 'mail_booster_plugin_autoupdate' ) ) {
		/**
		 * Function Name: mail_booster_plugin_autoupdate
		 * Parameters: No
		 * Description: This function is used to Update the plugin automatically.
		 * Created On: 16-06-2016 11:18
		 * Created By: Tech Banker Team
		 */
		function mail_booster_plugin_autoupdate() {
			try {
				require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
				require_once ABSPATH . 'wp-admin/includes/misc.php';
				define( 'FS_METHOD', 'direct' );
				require_once ABSPATH . 'wp-includes/update.php';
				require_once ABSPATH . 'wp-admin/includes/file.php';
				wp_update_plugins();
				ob_start();
				$plugin_upgrader = new Plugin_Upgrader();
				$plugin_upgrader->upgrade( MAIL_BOOSTER_FILE );
				$output = @ob_get_contents();// @codingStandardsIgnoreLine.
				@ob_end_clean();// @codingStandardsIgnoreLine.
			} catch ( Exception $e ) {// @codingStandardsIgnoreLine.
			}
		}
	}
	/**
	 * This function is used for scheduling days for clearing the logs.
	 *
	 * @param string $schedules passes parameter as schedules.
	 */
	function cron_scheduler_for_intervals_mail_booster( $schedules ) {
		$schedules['1day']   = array(
			'interval' => 60 * 60 * 24,
			'display'  => 'After 1 Day',
		);
		$schedules['7days']  = array(
			'interval' => 60 * 60 * 24 * 7,
			'display'  => 'After 7 Days',
		);
		$schedules['14days'] = array(
			'interval' => 60 * 60 * 24 * 14,
			'display'  => 'After 14 Days',
		);
		$schedules['21days'] = array(
			'interval' => 60 * 60 * 24 * 21,
			'display'  => 'After 21 Days',
		);
		$schedules['28days'] = array(
			'interval' => 60 * 60 * 24 * 28,
			'display'  => 'After 28 Days',
		);
		return $schedules;
	}
	if ( ! function_exists( 'user_functions_for_mail_booster' ) ) {
		/**
		 * Function Name: user_functions_for_mail_booster
		 * Parameters: No
		 * Description: This function is used to call on init hook.
		 * Created On: 16-06-2016 11:08
		 * Created By: Tech Banker Team
		 */
		function user_functions_for_mail_booster() {
			global $wpdb;
			$meta_values = $wpdb->get_results(
				$wpdb->prepare(
					'SELECT meta_value FROM ' . $wpdb->prefix . 'mail_booster_meta WHERE meta_key IN(%s,%s)', 'settings', 'email_configuration'
				)
			);// WPCS: db call ok; no-cache ok.

			$meta_data_array = array();
			foreach ( $meta_values as $value ) {
				$unserialize_data = maybe_unserialize( $value->meta_value );
				array_push( $meta_data_array, $unserialize_data );
			}
			mailer_file_for_mail_booster();
			oauth_handling_mail_booster();
		}
	}
	mailer_file_for_mail_booster();
	if ( ! function_exists( 'deactivation_function_for_wp_mail_booster' ) ) {
		/**
		 * Function Name: deactivation_function_for_wp_mail_booster
		 * Parameters: No
		 * Description: This function is used for executing the code on deactivation.
		 * Created On: 21-04-2017 09:22
		 * Created by: Tech Banker Team
		 */
		function deactivation_function_for_wp_mail_booster() {
			delete_option( 'wp-mail-booster-wizard-set-up' );
		}
	}

	/**
	 * This function is used to check for oauth configuration.
	 */
	function mail_booster_oauth_configure() {
		global $wpdb;
		$email_configuration_data  = $wpdb->get_var(
			$wpdb->prepare(
				'SELECT meta_value FROM ' . $wpdb->prefix . 'mail_booster_meta WHERE meta_key=%s',
				'email_configuration'
			)
		);// db call ok; no-cache ok.
		$email_configuration_array = maybe_unserialize( $email_configuration_data );
		if ( 'php_mail_function' === $email_configuration_array['mailer_type'] || ( 'smtp' === $email_configuration_array['mailer_type'] && 'oauth2' !== $email_configuration_array['auth_type'] ) ) {
			return '4';
		} elseif ( 'smtp' === $email_configuration_array['mailer_type'] && 'oauth2' === $email_configuration_array['auth_type'] ) {
			return '1';
		}
		return false;
	}

	if ( ! function_exists( 'wp_mail' ) && '4' !== mail_booster_oauth_configure() ) {
		if ( '1' === mail_booster_oauth_configure() ) {
			/**
			 * This function is used to send email in case of oauth.
			 *
			 * @param string $to .
			 * @param string $subject .
			 * @param string $message .
			 * @param string $headers .
			 * @param array  $attachments .
			 */
			function wp_mail( $to, $subject, $message, $headers = '', $attachments = array() ) {
				/**
				 * Filters the wp_mail() arguments.
				 *
				 * @since 2.2.0
				 *
				 * @param array $args A compacted array of wp_mail() arguments, including the "to" email,
				 *                    subject, message, headers, and attachments values.
				 */
				$atts = apply_filters( 'wp_mail', compact( 'to', 'subject', 'message', 'headers', 'attachments' ) );
				update_option( 'mail_booster_mail_status', '' );

				if ( isset( $atts['to'] ) ) {
								$to = $atts['to'];
				}

				if ( ! is_array( $to ) ) {
					$to = explode( ',', $to );
				}

				if ( isset( $atts['subject'] ) ) {
					$subject = $atts['subject'];
				}

				if ( isset( $atts['message'] ) ) {
					$message = $atts['message'];
				}

				if ( isset( $atts['headers'] ) ) {
					$headers = $atts['headers'];
				}

				if ( isset( $atts['attachments'] ) ) {
					$attachments = $atts['attachments'];
				}

				if ( ! is_array( $attachments ) ) {
					$attachments = explode( "\n", str_replace( "\r\n", "\n", $attachments ) );
				}

				include_once 'google-api-php-client/vendor/autoload.php';
				include_once 'php-mailer/phpmailer-autoload.php';
				include_once 'includes/class-google-oauth-mail-booster.php';
				include_once 'includes/class-oauth-mail-booster.php';

				$phpmailer = new Oauth_Mail_Booster();

				global $wpdb;
				$email_configuration_data  = $wpdb->get_var(
					$wpdb->prepare(
						'SELECT meta_value FROM ' . $wpdb->prefix . 'mail_booster_meta WHERE meta_key=%s',
						'email_configuration'
					)
				);// db call ok; no-cache ok.
				$email_configuration_array = maybe_unserialize( $email_configuration_data );

				$phpmailer->isSMTP();
				$phpmailer->AuthType = 'XOAUTH2'; // @codingStandardsIgnoreLine

				$phpmailer->SMTPAuth = true; // @codingStandardsIgnoreLine

				$phpmailer->SMTPSecure = $email_configuration_array['enc_type']; // @codingStandardsIgnoreLine

				$phpmailer->Host = $email_configuration_array['hostname']; // @codingStandardsIgnoreLine

				$phpmailer->Port = $email_configuration_array['port']; // @codingStandardsIgnoreLine

				$phpmailer->SMTPAutoTLS = false; // @codingStandardsIgnoreLine

				$phpmailer->SMTPDebug = 4; // @codingStandardsIgnoreLine
				$phpmailer->Debugoutput = 'html'; // @codingStandardsIgnoreLine

				$phpmailer->SMTPOptions = array( // @codingStandardsIgnoreLine
					'ssl' => array(
						'verify_peer'       => false,
						'verify_peer_name'  => false,
						'allow_self_signed' => true,
					),
				);
				$phpmailer->oauthUserEmail    = $email_configuration_array['email_address']; // @codingStandardsIgnoreLine
				$phpmailer->oauthClientId     = $email_configuration_array['client_id']; // @codingStandardsIgnoreLine
				$phpmailer->oauthClientSecret = $email_configuration_array['client_secret']; // @codingStandardsIgnoreLine

				$get_token_data               = get_option( 'mail_booster_auth' );
				$phpmailer->oauthRefreshToken = $get_token_data['refresh_token']; // @codingStandardsIgnoreLine

				$cc       = array();
				$bcc      = array();
				$reply_to = array();
				if ( empty( $headers ) ) {
								$headers = array();
				} else {
					if ( ! is_array( $headers ) ) {
						$tempheaders = explode( "\n", str_replace( "\r\n", "\n", $headers ) );
					} else {
									$tempheaders = $headers;
					}
					$headers = array();
					if ( ! empty( $tempheaders ) ) {
						foreach ( (array) $tempheaders as $header ) {
							if ( strpos( $header, ':' ) === false ) {
								if ( false !== stripos( $header, 'boundary=' ) ) {
									$parts    = preg_split( '/boundary=/i', trim( $header ) );
									$boundary = trim( str_replace( array( "'", '"' ), '', $parts[1] ) );
								}
								continue;
							}
							list( $name, $content ) = explode( ':', trim( $header ), 2 );

							$name    = trim( $name );
							$content = trim( $content );
							switch ( strtolower( $name ) ) {
								case 'from':
									$bracket_pos = strpos( $content, '<' );
									if ( false !== $bracket_pos ) {
										if ( $bracket_pos > 0 ) {
											$from_name = substr( $content, 0, $bracket_pos - 1 );
											$from_name = str_replace( '"', '', $from_name );
											$from_name = trim( $from_name );
										}

										$from_email = substr( $content, $bracket_pos + 1 );
										$from_email = str_replace( '>', '', $from_email );
										$from_email = trim( $from_email );

									} elseif ( '' !== trim( $content ) ) {
										$from_email = trim( $content );
									}
									break;
								case 'content-type':
									if ( strpos( $content, ';' ) !== false ) {
										list( $type, $charset_content ) = explode( ';', $content );
										$content_type                   = trim( $type );
										if ( false !== stripos( $charset_content, 'charset=' ) ) {
											$charset = trim( str_replace( array( 'charset=', '"' ), '', $charset_content ) );
										} elseif ( false !== stripos( $charset_content, 'boundary=' ) ) {
											$boundary = trim( str_replace( array( 'BOUNDARY=', 'boundary=', '"' ), '', $charset_content ) );
											$charset  = '';
										}
									} elseif ( '' !== trim( $content ) ) {
										$content_type = trim( $content );
									}
									break;
								case 'cc':
									$cc = array_merge( (array) $cc, explode( ',', $content ) );
									break;
								case 'bcc':
									$bcc = array_merge( (array) $bcc, explode( ',', $content ) );
									break;
								case 'reply-to':
									$reply_to = array_merge( (array) $reply_to, explode( ',', $content ) );
									break;
								default:
									$headers[ trim( $name ) ] = trim( $content );
									break;
							}
						}
					}
				}

				$phpmailer->clearAllRecipients();
				$phpmailer->clearAttachments();
				$phpmailer->clearCustomHeaders();
				$phpmailer->clearReplyTos();

				if ( 'override' === $email_configuration_array['sender_name_configuration'] ) {
					$from_name = $email_configuration_array['sender_name'];
				} else {
					if ( ! isset( $from_name ) ) {
						$from_name = get_option( 'blogname' );
					}
				}
				if ( '' !== $email_configuration_array['headers'] && isset( $email_configuration_array['headers'] ) ) {
						$phpmailer->addCustomHeader( $email_configuration_array['headers'] );
				}
				if ( '' !== $email_configuration_array['reply_to'] ) {
					$phpmailer->addReplyTo( $email_configuration_array['reply_to'] );
				}
				if ( 'override' === $email_configuration_array['from_email_configuration'] ) {
					$from_email = $email_configuration_array['sender_email'];
				} else {
					if ( ! isset( $from_email ) ) {
						$sitename = strtolower( isset( $_SERVER['SERVER_NAME'] ) ? wp_unslash( $_SERVER['SERVER_NAME'] ) : 'localhost' ); // WPCS: Sanitization ok, input var ok.
						if ( substr( $sitename, 0, 4 ) === 'www.' ) {
							$sitename = substr( $sitename, 4 );
						}
						$from_email = get_option( 'admin_email' ) . $sitename;
					}
				}

				/**
				 * Filters the email address to send from.
				 *
				 * @since 2.2.0
				 *
				 * @param string $from_email Email address to send from.
				 */
					$from_email = apply_filters( 'wp_mail_from', $from_email );

					/**
					 * Filters the name to associate with the "from" email address.
					 *
					 * @since 2.3.0
					 *
					 * @param string $from_name Name associated with the "from" email address.
					 */
					$from_name = apply_filters( 'wp_mail_from_name', $from_name );

				try {
					$phpmailer->setFrom( $from_email, $from_name, false );
				} catch ( phpmailerException $e ) {
					$mail_error_data                             = compact( 'to', 'subject', 'message', 'headers', 'attachments' );
					$mail_error_data['phpmailer_exception_code'] = $e->getCode();

					/** This filter is documented in wp-includes/pluggable.php */
					do_action( 'wp_mail_failed', new WP_Error( 'wp_mail_failed', $e->getMessage(), $mail_error_data ) );
					return false;
				}
				$phpmailer->Subject = $subject; // @codingStandardsIgnoreLine
				$phpmailer->body    = $message;
				$address_headers    = compact( 'to', 'cc', 'bcc', 'reply_to' );
				foreach ( $address_headers as $address_header => $addresses ) {
					if ( empty( $addresses ) ) {
						continue;
					}
					foreach ( (array) $addresses as $address ) {
						try {
							$recipient_name = '';
							if ( preg_match( '/(.*)<(.+)>/', $address, $matches ) ) {
								if ( count( $matches ) === 3 ) {
									$recipient_name = $matches[1];
									$address        = $matches[2];
								}
							}
							switch ( $address_header ) {
								case 'to':
									$phpmailer->addAddress( $address, $recipient_name );
									break;
								case 'cc':
									$phpmailer->addCc( $address, $recipient_name );
									break;
								case 'bcc':
									$phpmailer->addBcc( $address, $recipient_name );
									break;
								case 'reply_to':
									$phpmailer->addReplyTo( $address, $recipient_name );
									break;
							}
						} catch ( phpmailerException $e ) {
							continue;
						}
					}
				}

				if ( ! isset( $content_type ) ) {
					$content_type = 'text/plain';
				}

				/**
				* Filters the wp_mail() content type.
				*
				* @since 2.3.0
				*
				* @param string $content_type Default wp_mail() content type.
				*/
				$content_type           = apply_filters( 'wp_mail_content_type', $content_type );
				$phpmailer->ContentType = $content_type; // @codingStandardsIgnoreLine
				if ( 'text/html' === $content_type ) {
					$phpmailer->isHTML( true );
				}

				if ( ! isset( $charset ) ) {
					$charset = get_bloginfo( 'charset' );
				}
				/**
				* Filters the default wp_mail() charset.
				*
				* @since 2.3.0
				*
				* @param string $charset Default email charset.
				*/
				$phpmailer->CharSet = apply_filters( 'wp_mail_charset', $charset ); // @codingStandardsIgnoreLine
				if ( ! empty( $headers ) ) {
					foreach ( (array) $headers as $name => $content ) {
						$phpmailer->addCustomHeader( sprintf( '%1$s: %2$s', $name, $content ) );
					}
					if ( false !== stripos( $content_type, 'multipart' ) && ! empty( $boundary ) ) {
						$phpmailer->addCustomHeader( sprintf( "Content-Type: %s;\n\t boundary=\"%s\"", $content_type, $boundary ) );
					}
				}
				if ( ! empty( $attachments ) ) {
					foreach ( $attachments as $attachment ) {
						try {
							$phpmailer->addAttachment( $attachment );
						} catch ( phpmailerException $e ) {
										continue;
						}
					}
				}
				/**
				* Fires after PHPMailer is initialized.
				*
				* @since 2.2.0
				*
				* @param PHPMailer $phpmailer The PHPMailer instance (passed by reference).
				*/
				do_action_ref_array( 'phpmailer_init', array( &$phpmailer ) );
				try {
					$mailer = $phpmailer->send();
					global $wpdb, $email_notification_data_array;
					$settings_array_serialized   = $wpdb->get_var(
						$wpdb->prepare(
							'SELECT meta_value FROM ' . $wpdb->prefix . 'mail_booster_meta WHERE meta_key=%s',
							'settings'
						)
					);// db call ok; no-cache ok.
					$settings_array_unserialized = maybe_unserialize( $settings_array_serialized );

					$sender_email = 'override' === $email_configuration_array['from_email_configuration'] ? $email_configuration_array['sender_email'] : $from_email;
					$sender_name  = 'override' === $email_configuration_array['sender_name_configuration'] ? $email_configuration_array['sender_name'] : $from_name;
					if ( 'enable' === $settings_array_unserialized['monitor_email_logs'] ) {
						$email_logs_data_array                 = array();
						$email_logs_data_array['email_to']     = is_array( $to ) ? implode( ',', $to ) : $to;
						$email_logs_data_array['cc']           = implode( ',', $cc );
						$email_logs_data_array['bcc']          = implode( ',', $bcc );
						$email_logs_data_array['subject']      = $subject;
						$email_logs_data_array['content']      = $message;
						$email_logs_data_array['sender_name']  = $sender_name;
						$email_logs_data_array['sender_email'] = $sender_email;
						$email_logs_data_array['timestamp']    = MAIL_BOOSTER_LOCAL_TIME;
						if ( 'enable' === $settings_array_unserialized['debug_mode'] ) {
							$email_logs_data_array['debug_mode']       = $settings_array_unserialized['debug_mode'];
							$email_logs_data_array['debugging_output'] = get_option( 'mail_booster_mail_status' );
							$email_logs_data_array['status']           = get_option( 'mail_booster_is_mail_sent' );
						}
						$wpdb->insert( mail_booster_logs(), $email_logs_data_array );// db call ok; no-cache ok.
						$mb_insert_id    = $wpdb->insert_id;
						$mb_table_prefix = $wpdb->prefix;
						if ( is_multisite() ) {
							$get_other_settings_meta_value    = $wpdb->get_var(
								$wpdb->prepare(
									'SELECT meta_value FROM ' . $wpdb->base_prefix . 'mail_booster_meta WHERE meta_key=%s',
									'settings'
								)
							);// WPCS: db call ok; no-cache ok.
							$other_settings_unserialized_data = maybe_unserialize( $get_other_settings_meta_value );
							if ( isset( $other_settings_unserialized_data['fetch_settings'] ) && 'network_site' === $other_settings_unserialized_data['fetch_settings'] ) {
								$mb_table_prefix = $wpdb->base_prefix;
							}
						}
					}
					return $mailer;
				} catch ( phpmailerException $e ) {
					$mail_error_data = compact( 'to', 'subject', 'message', 'headers', 'attachments' );

					$mail_error_data['phpmailer_exception_code'] = $e->getCode();
					/**
					 * Fires after a phpmailerException is caught.
					 *
					 * @since 4.4.0
					 *
					 * @param WP_Error $error A WP_Error object with the phpmailerException message, and an array
					 *                        containing the mail recipient, subject, message, headers, and attachments.
					 */
					do_action( 'wp_mail_failed', new WP_Error( 'wp_mail_failed', $e->getMessage(), $mail_error_data ) );
					return false;
				}
			}
		}
	} else {
		add_action( 'phpmailer_init', 'email_configuration_mail_booster' );
	}

	/* hooks */

	/**
	 * Description: This hook is used for calling the function of get_users_capabilities_mail_booster.
	 * Created On: 15-06-2016 09:46
	 * Created By: Tech Banker Team
	 */
	add_action( 'plugins_loaded', 'get_users_capabilities_mail_booster' );

	/**
	 * This hook is used for notifications.
	 */
	add_action( 'plugins_loaded', 'get_notifications_data_mail_booster' );

	/**
	 * This hook is used for calling the function of install script.
	 */

	register_activation_hook( __FILE__, 'install_script_for_mail_booster' );

	/**
	 * This hook contains all admin_init functions.
	 */

	add_action( 'admin_init', 'admin_functions_for_mail_booster' );

	/**
	 * This hook is used for calling the function of user functions.
	 */

	add_action( 'init', 'user_functions_for_mail_booster' );

	/**
	 * This hook is used for creating crons.
	 */
	add_filter( 'cron_schedules', 'cron_scheduler_for_intervals_mail_booster' );

	/**
	 * This hook is used for calling the function of sidebar menu.
	 */

	add_action( 'admin_menu', 'sidebar_menu_for_mail_booster' );

	/**
	* This hook is used for calling the function of sidebar menu in multisite case.
	*/

	add_action( 'network_admin_menu', 'sidebar_menu_for_mail_booster' );

	/**
	 * This hook is used for calling the function of topbar menu.
	 */

	add_action( 'admin_bar_menu', 'topbar_menu_for_mail_booster', 100 );

	/**
	 * This hook is used for calling the function of languages.
	 */

	add_action( 'init', 'plugin_load_textdomain_mail_booster' );

	/*
	 * This Hook is used for calling the function of plugin update.
	 */

	add_action( 'mail_booster_plugin_update_scheduler', 'plugin_update_mail_booster' );

	/*
	 * This Hook is used for calling the function of theme update.
	 */

	add_action( 'mail_booster_theme_update_scheduler', 'theme_update_mail_booster' );

	/*
	 * This Hook is used for calling the function of plugin updated.
	 */

	add_action( 'mail_booster_plugin_updated_scheduler', 'plugin_updated_mail_booster' );

	/*
	 * This hook is used for calling the function of plugin update message.
	 */

	add_action( 'in_plugin_update_message-' . MAIL_BOOSTER_FILE, 'wp_mail_booster_plugin_update_message' );

	/*
	 * This hook is used to register ajax.
	 */
	add_action( 'wp_ajax_mail_booster_action', 'ajax_register_for_mail_booster' );

	/*
	 * This hook is used to add widget on dashboard.
	 */
	add_action( 'wp_dashboard_setup', 'add_dashboard_widgets_mail_booster' );

	/*
	 * This hook is used for calling the function of settings link.
	*/
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'mail_booster_action_links', 10, 2 );

	/*
	 * This hook is used for calling the function of settings link.
	 */

	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'mail_booster_settings_link', 10, 2 );

	/**
	 * This hook is used to sets the deactivation hook for a plugin.
	 */

	register_deactivation_hook( __FILE__, 'deactivation_function_for_wp_mail_booster' );

	/**
	 * This hook is used to generate logs.
	 */
	add_action( 'plugins_loaded', 'generate_logs_mail_booster', 101 );

}

/**
* This hook is used for calling the function of install script.
*/

register_activation_hook( __FILE__, 'install_script_for_mail_booster' );

/**
 * This hook used for calling the function of install script.
 */

add_action( 'admin_init', 'install_script_for_mail_booster' );

if ( ! function_exists( 'plugin_activate_wp_mail_booster' ) ) {
	/**
	 * This function is used to add option on plugin activation.
	 */
	function plugin_activate_wp_mail_booster() {
		add_option( 'wp_mail_booster_do_activation_redirect', true );
	}
}

if ( ! function_exists( 'wp_mail_booster_redirect' ) ) {
	/**
	 * This function is used to redirect to email setup.
	 */
	function wp_mail_booster_redirect() {
		if ( get_option( 'wp_mail_booster_do_activation_redirect', false ) ) {
			delete_option( 'wp_mail_booster_do_activation_redirect' );
			wp_safe_redirect( admin_url( 'admin.php?page=mail_booster_email_configuration' ) );
			exit;
		}
	}
}
register_activation_hook( __FILE__, 'plugin_activate_wp_mail_booster' );
add_action( 'admin_init', 'wp_mail_booster_redirect' );

/**
 * Function Name:mail_booster_admin_notice_class
 * Parameter: No
 * Description: This function is used to create the object of admin notices.
 * Created On: 08-29-2017 15:06
 * Created By: Tech Banker Team
 */
function mail_booster_admin_notice_class() {
	global $wpdb;
	/**
	 * This Class is used to add admin notice.
	 */
	class Mail_Booster_Admin_Notices {
		/**
		 * The version of this plugin.
		 *
		 * @access   public
		 * @var      string    $config  .
		 */
		public $config;
		/**
		 * The version of this plugin.
		 *
		 * @access   public
		 * @var      integer    $notice_spam .
		 */
		public $notice_spam = 0;
		/**
		 * The version of this plugin.
		 *
		 * @access   public
		 * @var      integer    $notice_spam_max .
		 */
		public $notice_spam_max = 2;
		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    1.0.0
		 * @param array $config .
		 */
		public function __construct( $config = array() ) {
			// Runs the admin notice ignore function incase a dismiss button has been clicked.
			add_action( 'admin_init', array( $this, 'mail_booster_admin_notice_ignore' ) );
			// Runs the admin notice temp ignore function incase a temp dismiss link has been clicked.
			add_action( 'admin_init', array( $this, 'mail_booster_admin_notice_temp_ignore' ) );
			add_action( 'admin_notices', array( $this, 'mail_booster_display_admin_notices' ) );
		}
		/**
		 * Checks to ensure notices aren't disabled and the user has the correct permissions.
		 */
		public function mail_booster_admin_notices() {
			$settings = get_option( 'mail_booster_admin_notice' );
			if ( ! isset( $settings['disable_admin_notices'] ) || ( isset( $settings['disable_admin_notices'] ) && 0 === $settings['disable_admin_notices'] ) ) {
				if ( current_user_can( 'manage_options' ) ) {
					return true;
				}
			}
			return false;
		}
		/**
		 * Primary notice function that can be called from an outside function sending necessary variables.
		 *
		 * @param string $admin_notices .
		 */
		public function change_admin_notice_mail_booster( $admin_notices ) {
			// Check options.
			if ( ! $this->mail_booster_admin_notices() ) {
				return false;
			}
			foreach ( $admin_notices as $slug => $admin_notice ) {
				// Call for spam protection.
				if ( $this->mail_booster_anti_notice_spam() ) {
					return false;
				}
				// Check for proper page to display on.
				if ( isset( $admin_notices[ $slug ]['pages'] ) && is_array( $admin_notices[ $slug ]['pages'] ) ) {
					if ( ! $this->mail_booster_admin_notice_pages( $admin_notices[ $slug ]['pages'] ) ) {
						return false;
					}
				}
				// Check for required fields.
				if ( ! $this->mail_booster_required_fields( $admin_notices[ $slug ] ) ) {

					// Get the current date then set start date to either passed value or current date value and add interval.
					$current_date = current_time( 'm/d/Y' );
					$start        = ( isset( $admin_notices[ $slug ]['start'] ) ? $admin_notices[ $slug ]['start'] : $current_date );
					$start        = date( 'm/d/Y' );
					$interval     = ( isset( $admin_notices[ $slug ]['int'] ) ? $admin_notices[ $slug ]['int'] : 0 );
					$date         = strtotime( '+' . $interval . ' days', strtotime( $start ) );
					$start        = date( 'm/d/Y', $date );

					// This is the main notices storage option.
					$admin_notices_option = get_option( 'mail_booster_admin_notice', array() );
					// Check if the message is already stored and if so just grab the key otherwise store the message and its associated date information.
					if ( ! array_key_exists( $slug, $admin_notices_option ) ) {
						$admin_notices_option[ $slug ]['start'] = date( 'm/d/Y' );
						$admin_notices_option[ $slug ]['int']   = $interval;
						update_option( 'mail_booster_admin_notice', $admin_notices_option );
					}
					// Sanity check to ensure we have accurate information.
					// New date information will not overwrite old date information.
					$admin_display_check    = ( isset( $admin_notices_option[ $slug ]['dismissed'] ) ? $admin_notices_option[ $slug ]['dismissed'] : 0 );
					$admin_display_start    = ( isset( $admin_notices_option[ $slug ]['start'] ) ? $admin_notices_option[ $slug ]['start'] : $start );
					$admin_display_interval = ( isset( $admin_notices_option[ $slug ]['int'] ) ? $admin_notices_option[ $slug ]['int'] : $interval );
					$admin_display_msg      = ( isset( $admin_notices[ $slug ]['msg'] ) ? $admin_notices[ $slug ]['msg'] : '' );
					$admin_display_title    = ( isset( $admin_notices[ $slug ]['title'] ) ? $admin_notices[ $slug ]['title'] : '' );
					$admin_display_link     = ( isset( $admin_notices[ $slug ]['link'] ) ? $admin_notices[ $slug ]['link'] : '' );
					$output_css             = false;

					// Ensure the notice hasn't been hidden and that the current date is after the start date.
					if ( 0 === $admin_display_check && strtotime( $admin_display_start ) <= strtotime( $current_date ) ) {
						// Get remaining query string.
						$query_str = ( isset( $admin_notices[ $slug ]['later_link'] ) ? $admin_notices[ $slug ]['later_link'] : esc_url( add_query_arg( 'mail_booster_admin_notice_ignore', $slug ) ) );
						if ( strpos( $slug, 'promo' ) === false ) {
							// Admin notice display output.
							echo '<div class="update-nag tech-banker-admin-notice">
									<div></div>
									<strong><p>' . $admin_display_title . '</p></strong>
									<p class="tech-banker-display-notice">' . $admin_display_msg . '</p>
									<strong><ul>' . $admin_display_link . '</ul></strong>
							</div>';// WPCS: XSS ok.
						} else {
							echo '<div class="admin-notice-promo">';
							echo $admin_display_msg;// WPCS: XSS ok.
							echo '<ul class="notice-body-promo blue">
									' . $admin_display_link . '
								</ul>';// WPCS: XSS ok.
							echo '</div>';
						}
						$this->notice_spam += 1;
						$output_css         = true;
					}
				}
			}
		}
		/**
		 * Spam protection check.
		 */
		public function mail_booster_anti_notice_spam() {
			if ( $this->notice_spam >= $this->notice_spam_max ) {
				return true;
			}
			return false;
		}
		/**
		 * Ignore function that gets ran at admin init to ensure any messages that were dismissed get marked.
		 */
		public function mail_booster_admin_notice_ignore() {
			// If user clicks to ignore the notice, update the option to not show it again.
			if ( isset( $_GET['mail_booster_admin_notice_ignore'] ) ) { // WPCS: CSRF ok, WPCS: input var ok.
				$admin_notices_option = get_option( 'mail_booster_admin_notice', array() );
				$admin_notices_option[ wp_unslash( $_GET['mail_booster_admin_notice_ignore'] ) ]['dismissed'] = 1; // @codingStandardsIgnoreLine.
				update_option( 'mail_booster_admin_notice', $admin_notices_option );
				$query_str = remove_query_arg( 'mail_booster_admin_notice_ignore' );
				wp_safe_redirect( $query_str );
				exit;
			}
		}
		/**
		 * Temp Ignore function that gets ran at admin init to ensure any messages that were temp dismissed get their start date changed.
		 */
		public function mail_booster_admin_notice_temp_ignore() {
			// If user clicks to temp ignore the notice, update the option to change the start date - default interval of 7 days.
			if ( isset( $_GET['mail_booster_admin_notice_temp_ignore'] ) ) { // WPCS: CSRF ok, WPCS: input var ok.
				$admin_notices_option = get_option( 'mail_booster_admin_notice', array() );
				$current_date         = current_time( 'm/d/Y' );
				$interval             = ( isset( $_GET['int'] ) ? wp_unslash( $_GET['int'] ) : 7 ); // @codingStandardsIgnoreLine.
				$date                 = strtotime( '+' . $interval . ' days', strtotime( $current_date ) );
				$new_start            = date( 'm/d/Y', $date );

				$admin_notices_option[ wp_unslash( $_GET['mail_booster_admin_notice_temp_ignore'] ) ]['start']     = $new_start; // @codingStandardsIgnoreLine.
				$admin_notices_option[ wp_unslash( $_GET['mail_booster_admin_notice_temp_ignore'] ) ]['dismissed'] = 0; // @codingStandardsIgnoreLine.
				update_option( 'mail_booster_admin_notice', $admin_notices_option );
				$query_str = remove_query_arg( array( 'mail_booster_admin_notice_temp_ignore', 'int' ) );
				wp_safe_redirect( $query_str );
				exit;
			}
		}
		/**
		 * This function is used to add admin notices on pages of backend.
		 *
		 * @param string $pages .
		 */
		public function mail_booster_admin_notice_pages( $pages ) {
			foreach ( $pages as $key => $page ) {
				if ( is_array( $page ) ) {
					if ( isset( $_GET['page'] ) && $page[0] === $_GET['page'] && isset( $_GET['tab'] ) && $page[1] === $_GET['tab'] ) { // WPCS: CSRF ok, WPCS: input var ok.
						return true;
					}
				} else {
					if ( 'all' === $page ) {
						return true;
					}
					if ( get_current_screen()->id === $page ) {
						return true;
					}
					if ( isset( $_GET['page'] ) && $page === $_GET['page'] ) { // WPCS: CSRF ok, WPCS: input var ok.
						return true;
					}
				}
				return false;
			}
		}
		/**
		 * Required fields check.
		 *
		 * @param string $fields .
		 */
		public function mail_booster_required_fields( $fields ) {
			if ( ! isset( $fields['msg'] ) || ( isset( $fields['msg'] ) && empty( $fields['msg'] ) ) ) {
				return true;
			}
			if ( ! isset( $fields['title'] ) || ( isset( $fields['title'] ) && empty( $fields['title'] ) ) ) {
				return true;
			}
			return false;
		}
		/**
		 * This function is used to display message on admin notice.
		 */
		public function mail_booster_display_admin_notices() {
			$two_week_review_ignore     = add_query_arg( array( 'mail_booster_admin_notice_ignore' => 'two_week_review' ) );
			$two_week_review_temp       = add_query_arg(
				array(
					'mail_booster_admin_notice_temp_ignore' => 'two_week_review',
					'int' => 7,
				)
			);
			$mail_booster_sure_love_to  = __( "Sure! I'd love to!", 'wp-mail-booster' );
			$mail_booster_leave_review  = __( "I've already left a review", 'wp-mail-booster' );
			$mail_booster_may_be_later  = __( 'Maybe Later', 'wp-mail-booster' );
			$notices['two_week_review'] = array(
				'title'      => __( 'Leave a 5 Star Review', 'wp-mail-booster' ),
				'msg'        => __( 'We are grateful that you’ve decided to join the Tech Banker Family and we are putting maximum efforts to provide you with the Best Product.<br> Your 5 Star Review will Boost our Morale by 10x!', 'wp-mail-booster' ),
				'link'       => '<span class="dashicons dashicons-external"></span><span><a href="https://wordpress.org/support/plugin/wp-mail-booster/reviews/?filter=5" target="_blank" class="tech-banker-admin-notice-link" > ' . $mail_booster_sure_love_to . ' </a></span>
												<span class="dashicons dashicons-smiley tech-banker-admin-notice"></span><span class="tech-banker-admin-notice"><a href="' . $two_week_review_ignore . '" class="tech-banker-admin-notice-link">' . $mail_booster_leave_review . '</a></span>
												<span class="dashicons dashicons-calendar-alt tech-banker-admin-notice"></span><span class="tech-banker-admin-notice"><a href="' . $two_week_review_temp . '" class="tech-banker-admin-notice-link"> ' . $mail_booster_may_be_later . ' </a></span>',
				'later_link' => $two_week_review_temp,
				'int'        => 7,
			);
			$this->change_admin_notice_mail_booster( $notices );
		}
	}
	$plugin_info_mail_booster = new Mail_Booster_Admin_Notices();
}
add_action( 'init', 'mail_booster_admin_notice_class' );
/**
 * This function is used to add popup on deactivation.
 */
function add_popup_on_deactivation_mail_booster() {
	global $wpdb;
	/**
	 * This class is used to add deactivation form.
	 */
	class Mail_Booster_Deactivation_Form {// @codingStandardsIgnoreLine
		/**
		 * Initialize the class and set its properties.
		 */
		function __construct() {
			add_action( 'wp_ajax_post_user_feedback_mail_booster', array( $this, 'post_user_feedback_mail_booster' ) );
			global $pagenow;
			if ( 'plugins.php' === $pagenow ) {
					add_action( 'admin_enqueue_scripts', array( $this, 'feedback_form_js_mail_booster' ) );
					add_action( 'admin_head', array( $this, 'add_form_layout_mail_booster' ) );
					add_action( 'admin_footer', array( $this, 'add_deactivation_dialog_form_mail_booster' ) );
			}
		}
		/**
		 * Enqueue js files.
		 */
		function feedback_form_js_mail_booster() {
			wp_enqueue_style( 'wp-jquery-ui-dialog' );
			wp_register_script( 'mail-booster-feedback', plugins_url( 'assets/global/plugins/deactivation/deactivate-popup.js', __FILE__ ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-dialog' ), false, true );
			wp_localize_script( 'mail-booster-feedback', 'post_feedback', array( 'admin_ajax' => admin_url( 'admin-ajax.php' ) ) );
			wp_enqueue_script( 'mail-booster-feedback' );
		}
		/**
		 * This function is used to post user feedback.
		 */
		function post_user_feedback_mail_booster() {
			$mail_booster_deactivation_reason = isset( $_POST['reason'] ) ? wp_unslash( $_POST['reason'] ) : ''; // // @codingStandardsIgnoreLine.
			$plugin_info_wp_mail_booster      = new Plugin_Info_Wp_Mail_Booster();
			global $wp_version, $wpdb;
			$url              = TECH_BANKER_STATS_URL . '/wp-admin/admin-ajax.php';
			$type             = get_option( 'wp-mail-booster-wizard-set-up' );
			$user_admin_email = get_option( 'mail-booster-admin-email' );
			$theme_details    = array();
			if ( $wp_version >= 3.4 ) {
				$active_theme                   = wp_get_theme();
				$theme_details['theme_name']    = strip_tags( $active_theme->Name );// @codingStandardsIgnoreLine
				$theme_details['theme_version'] = strip_tags( $active_theme->Version );// @codingStandardsIgnoreLine
				$theme_details['author_url']    = strip_tags( $active_theme->{'Author URI'} );
			}
			$plugin_stat_data                   = array();
			$plugin_stat_data['plugin_slug']    = 'wp-mail-booster';
			$plugin_stat_data['reason']         = $mail_booster_deactivation_reason;
			$plugin_stat_data['type']           = 'standard_edition';
			$plugin_stat_data['version_number'] = MAIL_BOOSTER_VERSION_NUMBER;
			$plugin_stat_data['status']         = $type;
			if ( '3' === $mail_booster_deactivation_reason ) {
				$feedback_array               = array();
				$feedback_array['name']       = isset( $_POST['ux_txt_your_name_mail_booster'] ) ? wp_unslash( $_POST['ux_txt_your_name_mail_booster'] ) : ''; //@codingStandardsIgnoreLine.
				$feedback_array['email']      = isset( $_POST['ux_txt_email_address_mail_booster'] ) ? wp_unslash( $_POST['ux_txt_email_address_mail_booster'] ) : '';//@codingStandardsIgnoreLine.
				$feedback_array['request']    = isset( $_POST['ux_txtarea_feedbacks_mail_booster'] ) ? wp_unslash( $_POST['ux_txtarea_feedbacks_mail_booster'] ) : '';//@codingStandardsIgnoreLine.
				$plugin_stat_data['feedback'] = maybe_serialize( $feedback_array );
			}
			$plugin_stat_data['event']            = 'de-activate';
			$plugin_stat_data['domain_url']       = site_url();
			$plugin_stat_data['wp_language']      = defined( 'WPLANG' ) && WPLANG ? WPLANG : get_locale();
			$plugin_stat_data['email']            = false !== $user_admin_email ? $user_admin_email : get_option( 'admin_email' );
			$plugin_stat_data['wp_version']       = $wp_version;
			$plugin_stat_data['php_version']      = esc_html( phpversion() );
			$plugin_stat_data['mysql_version']    = $wpdb->db_version();
			$plugin_stat_data['max_input_vars']   = ini_get( 'max_input_vars' );
			$plugin_stat_data['operating_system'] = PHP_OS . '  (' . PHP_INT_SIZE * 8 . ') BIT';
			$plugin_stat_data['php_memory_limit'] = ini_get( 'memory_limit' ) ? ini_get( 'memory_limit' ) : 'N/A';
			$plugin_stat_data['extensions']       = get_loaded_extensions();
			$plugin_stat_data['plugins']          = $plugin_info_wp_mail_booster->get_plugin_info_wp_mail_booster();
			$plugin_stat_data['themes']           = $theme_details;
			$response                             = wp_safe_remote_post(
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
				die( 'success' );
		}
		/**
		 * Add layout for deactivation form.
		 */
		function add_form_layout_mail_booster() {
			?>
			<style type="text/css">
				.mail-booster-feedback-form {
					height: auto;
					width: 40% !important;
					top: 406px;
					left: 30% !important;
					display: block;
				}
				.feedback-form-submit {
					padding-left: 0px !important;
					padding-right: 0px !important;
					position: relative;
					min-height: 1px;
					float: left;
					width: 100%;
				}
				.feedback-form-submit-col-md-6{
					padding-left: 0px !important;
					padding-right: 0px !important;
					position: relative;
					min-height: 1px;
					width: 50%;
					float: left;
				}
				.mail-booster-feedback-form .ui-dialog-title {
					color : red !important;
				}
				.mail-booster-feedback-form .ui-dialog-titlebar {
					background : #f7f7f7 !important;
				}
				.mail-booster-feedback-form .ui-dialog-buttonpane {
					background : #f7f7f7 !important;
				}
				.mail-booster-feedback-form .ui-dialog-buttonset {
					float: none !important;
				}
				#mail-booster-feedback-dialog-continue,#mail-booster-feedback-dialog-skip {
					float: right;
				}
				#mail-booster-feedback-cancel{
					float: left;
				}
				#mail-booster-feedback-content p {
					font-size: 1.1em;
				}
				.mail-booster-feedback-form .ui-icon {
					display: none;
				}
				#mail-booster-feedback-dialog-continue.mail-booster-ajax-progress .ui-icon {
					text-indent: inherit;
					display: inline-block !important;
					vertical-align: middle;
					animation: rotate 2s infinite linear;
				}
				#mail-booster-feedback-dialog-continue.mail-booster-ajax-progress .ui-button-text {
					vertical-align: middle;
				}
				@keyframes rotate {
				0%    { transform: rotate(0deg); }
				100%  { transform: rotate(360deg); }
				}
			</style>
			<?php
		}
		/**
		 * Add deactivation form Layout.
		 */
		function add_deactivation_dialog_form_mail_booster() {
			?>
			<div id="mail-booster-feedback-content" style="display: none;">
			<p style="margin-top:-5px"><?php echo esc_attr( __( 'Were you expecting something else or Did it fail to work for you?', 'wp-mail-booster' ) ); ?></p>
			<p><?php echo esc_attr( __( 'If you write about your expectations or experience, we can guarantee a Solution for it would be provided 100% free of cost.', 'wp-mail-booster' ) ); ?></p>
			<form id="ux_frm_deactivation_popup">
				<?php wp_nonce_field(); ?>
				<ul id="mail-booster-deactivate-reasons">
					<li class="mail-booster-reason mail-booster-custom-input">
						<label>
							<span><input value="0" type="radio" name="reason"/></span>
							<span><?php echo esc_attr( __( 'The Plugin didn\'t work', 'wp-mail-booster' ) ); ?></span>
						</label>
					</li>
					<li class="mail-booster-reason mail-booster-custom-input">
						<label>
							<span><input value="1" type="radio" name="reason" /></span>
							<span><?php echo esc_attr( __( 'I found a better Plugin', 'wp-mail-booster' ) ); ?></span>
						</label>
					</li>
					<li class="mail-booster-reason mail-booster-custom-input">
						<label>
							<span><input value="2" type="radio" name="reason"/></span>
							<span><?php echo esc_attr( __( 'It\'s a temporary deactivation. I\'m just debugging an issue.', 'wp-mail-booster' ) ); ?></span>
						</label>
					</li>
					<li class="mail-booster-reason mail-booster-support">
						<label>
							<span><input value="3" type="radio" id="ux_rdl_reason_mail_booster" name="reason" checked/></span>
							<span><?php echo esc_attr( __( 'Submit a Ticket', 'wp-mail-booster' ) ); ?></span>
						</label>
						<div class="mail-booster-submit-feedback" style="padding: 10px 10px 0px 10px;">
							<div class="feedback-form-submit">
								<div class="feedback-form-submit-col-md-6">
									<strong><?php echo esc_attr( __( 'Name', 'wp-mail-booster' ) ); ?> : </strong>
									<div class="form-group">
										<input type="text" class="form-control" name="ux_txt_your_name_mail_booster" id="ux_txt_your_name_mail_booster" value="">
									</div>
								</div>
								<div class="feedback-form-submit-col-md-6">
									<strong><?php echo esc_attr( __( 'Email', 'wp-mail-booster' ) ); ?> : </strong>
									<div class="form-group">
										<input type="email" class="form-control" id="ux_txt_email_address_mail_booster" name="ux_txt_email_address_mail_booster"/>
									</div>
								</div>
							</div>
							<strong><?php echo esc_attr( __( 'Feedback', 'wp-mail-booster' ) ); ?> : </strong>
							<div class="form-group">
								<textarea class="form-control" style="width: 100%;" name="ux_txtarea_feedbacks_mail_booster" id="ux_txtarea_feedbacks_mail_booster" rows="2" ></textarea>
							</div>
						</div>
					</li>
				</ul>
			</form>
		</div>
			<?php
		}
	}
	$plugin_deactivation_details = new Mail_Booster_Deactivation_Form();
}
add_action( 'plugins_loaded', 'add_popup_on_deactivation_mail_booster' );
/**
 * This function is used to insert decativate link on deactivate link.
 *
 * @param string $links .
 */
function insert_deactivate_link_id_mail_booster( $links ) {
	if ( ! is_multisite() ) {
		$links['deactivate'] = str_replace( '<a', '<a id="mail-booster-plugin-disable-link"', $links['deactivate'] );
	}
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'insert_deactivate_link_id_mail_booster', 10, 2 );

/**
 * This function is used to log email in case of phpmailer.
 */
function generate_logs_mail_booster() {
	global $email_notification_data_array;
	if ( 'enable' === $email_notification_data_array['email_wordpress_update'] ) {
		add_filter( 'auto_core_update_send_email', '__return_true' );
	} else {
		add_filter( 'auto_core_update_send_email', '__return_false' );
	}
	global $wpdb;
	$email_configuration_data  = $wpdb->get_var(
		$wpdb->prepare(
			'SELECT meta_value FROM ' . $wpdb->prefix . 'mail_booster_meta WHERE meta_key=%s',
			'email_configuration'
		)
	);// db call ok; no-cache ok.
	$email_configuration_array = maybe_unserialize( $email_configuration_data );
	if ( 'php_mail_function' === $email_configuration_array['mailer_type'] || ( 'smtp' === $email_configuration_array['mailer_type'] && 'oauth2' !== $email_configuration_array['auth_type'] ) ) {
		if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/class-mail-booster-email-logger.php' ) ) {
			include_once MAIL_BOOSTER_DIR_PATH . 'includes/class-mail-booster-email-logger.php';
		}
		$email_logger = new Mail_Booster_Email_Logger();
		$email_logger->load_emails_mail_booster();
	}
}

/**
 * This function is used to deactivate plugins.
 */
function deactivate_plugin_mail_booster() {
	if ( wp_verify_nonce( isset( $_GET['_wpnonce'] ) ? $_GET['_wpnonce'] : '', 'mb_deactivate_plugin_nonce' ) ) {
		deactivate_plugins( isset( $_GET['plugin'] ) ? wp_unslash( $_GET['plugin'] ) : '' );// WPCS: Input var ok, sanitization ok.
		wp_safe_redirect( wp_get_referer() );
		die();
	}
}
add_action( 'admin_post_mail_booster_deactivate_plugin', 'deactivate_plugin_mail_booster' );
/**
 * This function is used to display admin notice.
 */
function display_admin_notice_mail_booster() {
	$conflict_plugins_list = array(
		'WP Mail SMTP by WPForms'    => 'wp-mail-smtp/wp_mail_smtp.php',
		'Post SMTP Mailer/Email Log' => 'post-smtp/postman-smtp.php',
		'Easy WP SMTP'               => 'easy-wp-smtp/easy-wp-smtp.php',
		'Gmail SMTP'                 => 'gmail-smtp/main.php',
		'SMTP Mailer'                => 'smtp-mailer/main.php',
		'WP Email SMTP'              => 'wp-email-smtp/wp_email_smtp.php',
		'SMTP by BestWebSoft'        => 'bws-smtp/bws-smtp.php',
		'WP SendGrid SMTP'           => 'wp-sendgrid-smtp/wp-sendgrid-smtp.php',
		'Cimy Swift SMTP'            => 'cimy-swift-smtp/cimy_swift_smtp.php',
		'SAR Friendly SMTP'          => 'sar-friendly-smtp/sar-friendly-smtp.php',
		'WP Easy SMTP'               => 'wp-easy-smtp/wp-easy-smtp.php',
		'WP Gmail SMTP'              => 'wp-gmail-smtp/wp-gmail-smtp.php',
		'Email Log'                  => 'email-log/email-log.php',
		'SendGrid'                   => 'sendgrid-email-delivery-simplified/wpsendgrid.php',
		'Mailgun for WordPress'      => 'mailgun/mailgun.php',
	);
	$found                 = array();
	foreach ( $conflict_plugins_list as $name => $path ) {
		if ( is_plugin_active( $path ) ) {
				$found[] = array(
					'name' => $name,
					'path' => $path,
				);
		}
	}
	if ( count( $found ) ) {
		?>
		<div class="notice notice-error notice-warning tech-banker-compatiblity-warning">
			<p><?php echo esc_attr( _e( 'WP Mail Booster has detected the following plugins are activated. Please deactivate them to prevent conflicts.', 'wp-mail-booster' ) ); ?></p>
			<ul>
			<?php
			foreach ( $found as $plugin ) {
				?>
					<li class="tech-banker-deactivation"><strong><?php echo $plugin['name']; // WPCS: XSS ok. ?></strong>
						<a href='<?php echo wp_nonce_url( admin_url( 'admin-post.php?action=mail_booster_deactivate_plugin&plugin=' . urlencode( $plugin['path'] ) ), 'mb_deactivate_plugin_nonce' ); // WPCS: XSS ok, @codingStandardsIgnoreLine. ?>'class='button button-primary tech-banker-deactivation-button'><?php echo esc_attr( _e( 'Deactivate', 'wp-mail-booster' ) ); ?></a>
					</li>
					<?php
			}
			?>
			</ul>
		</div>
		<?php
	}
}
/**
 * This hook is used to display admin notice.
 */
add_action( 'admin_notices', 'display_admin_notice_mail_booster' );

/**
 * This hook is used to display admin notice.
 */
function upgrade_database_admin_notice_mail_booster() {
	global $wpdb;
	if ( $wpdb->query( "SHOW TABLES LIKE '" . $wpdb->prefix . 'mail_booster_email_logs' . "'" ) != 0 ) { // @codingStandardsIgnoreLine.
		$mail_booster_email_logs_count = $wpdb->get_var(
			'SELECT COUNT(id) FROM ' . $wpdb->prefix . 'mail_booster_email_logs'
		);// WPCS: db call ok; no-cache ok.
		if( $mail_booster_email_logs_count != 0 ) {// @codingStandardsIgnoreLine.
			$batches                       = ceil( $mail_booster_email_logs_count / 3000 );
			$upgrade_database_mail_booster = wp_create_nonce( 'upgrade_database_mail_booster' );
			?>
			<div class="update-nag">
				<strong><?php echo esc_attr( __( 'Important Announcement - Mail Booster?', 'wp-mail-booster' ) ); ?></strong>
				<p><?php echo esc_attr( __( 'We have made imminent changes to our Database to improve the Performance. You would need to update the Database to view prior Email Reports.', 'wp-mail-booster' ) ); ?></p>
				<p><?php echo esc_attr( __( 'All of your Past Email Reports are safely backed up. Contact Us', 'wp-mail-booster' ) ); ?><a href="https://tech-banker.com/contact-us/" target="_blank"> <?php echo esc_attr( __( 'here', 'wp-mail-booster' ) ); ?></a> <?php echo esc_attr( __( 'if you face any issues updating your database.', 'wp-mail-booster' ) ); ?></p>
				<a class="btn tech-banker-pro-options" onclick="update_database_interval(<?php echo intval( $batches ); ?>, '<?php echo esc_attr( $upgrade_database_mail_booster ); ?>' );"><?php echo esc_attr( __( 'Update Database!', 'wp-mail-booster' ) ); ?></a>
			</div>
			<?php
		}
	}
}
$database_update_option = get_option( 'mail_booster_update_database' );
if ( false == $database_update_option ) {// WP: loose comparison ok.
	add_action( 'admin_notices', 'upgrade_database_admin_notice_mail_booster' );
}
