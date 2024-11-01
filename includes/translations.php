<?php
/**
 * This file is used for translation strings.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/includes
 * @version 2.1.0
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

		// Pro.
		$mail_booster_pro_label           = __( '( Pro )' );
		$mail_booster_message_pro_edition = __( 'This feature is available only in Pro Edition! Kindly Purchase to unlock it!', 'wp-mail-booster' );
		// Wizard.
		$mail_booster_wizard_welcome_message         = __( 'Hi!', 'wp-mail-booster' );
		$mail_booster_wizard_opportunity             = __( 'Don\'t ever miss an opportunity to opt-in for Email Notifications / Announcements and Special Offers', 'wp-mail-booster' );
		$mail_booster_wizard_diagnostic_info         = __( 'Contribute to making our plugin compatible with most plugins and themes by allowing to share non-sensitive diagnostic information about your website', 'wp-mail-booster' );
		$mail_booster_wizard_email_address           = __( 'Name & Email to receive Notifications / Announcements / Special Offers', 'wp-mail-booster' );
		$mail_booster_wizard_ready                   = __( 'If you\'re not ready to Opt-In, that\'s ok too', 'wp-mail-booster' );
		$mail_booster_work_fine                      = __( 'Mail Booster will still work fine.', 'wp-mail-booster' );
		$mail_booster_hate_spam                      = __( 'We hate Spam too. Your Information will not be shared with anyone.', 'wp-mail-booster' );
		$mail_booster_gurantee                       = __( 'Privacy Guaranteed!', 'wp-mail-booster' );
		$mail_booster_wizard_permission_granted      = __( 'What permissions are being granted', 'wp-mail-booster' );
		$mail_booster_wizard_user_details            = __( 'User Details', 'wp-mail-booster' );
		$mail_booster_wizard_name_email              = __( 'Name and Email Address', 'wp-mail-booster' );
		$mail_booster_wizard_current_plugin          = __( 'Current Plugin Events', 'wp-mail-booster' );
		$mail_booster_wizard_activation_deactivation = __( 'Activation, Deactivation and Uninstall', 'wp-mail-booster' );
		$mail_booster_wizard_website_overview        = __( 'Website Overview', 'wp-mail-booster' );
		$mail_booster_wizard_updates_announcements   = __( 'Updates, Announcements, Marketing, No Spam', 'wp-mail-booster' );
		$mail_booster_wizard_site_info               = __( 'Site URL, WP Version, PHP Info, Plugins &amp; Themes Info', 'wp-mail-booster' );
		$mail_booster_wizard_newsletter              = __( 'Newsletter', 'wp-mail-booster' );
		$mail_booster_wizard_skip                    = __( 'Skip', 'wp-mail-booster' );
		$mail_booster_wizard_opt_in                  = __( 'Opt-In', 'wp-mail-booster' );
		$mail_booster_wizard_continue                = __( 'Continue', 'wp-mail-booster' );
		$mail_booster_wizard_private_policy          = __( 'Privacy Policy', 'wp-mail-booster' );
		$mail_booster_wizard_terms                   = __( 'Terms', 'wp-mail-booster' );
		$mail_booster_wizard_conditions              = __( 'Conditions', 'wp-mail-booster' );
		$mail_booster_wizard_first_name              = __( 'First Name', 'wp-mail-booster' );
		$mail_booster_wizard_last_name               = __( 'Last Name', 'wp-mail-booster' );

		// fetch settings.
		$mail_booster_fetch_settings         = __( 'Fetch Settings', 'wp-mail-booster' );
		$mail_booster_fetch_settings_tooltip = __( 'Choose options for Fetch Settings', 'wp-mail-booster' );
		$mail_booster_indivisual_site        = __( 'Individual Site', 'wp-mail-booster' );
		$mail_booster_multiple_site          = __( 'Network Site', 'wp-mail-booster' );

		// Notifications .
		$mail_booster_more_options                         = __( 'More Options Available with Pro!', 'wp-mail-booster' );
		$mail_booster_notifications_service                = __( 'Notification Services', 'wp-mail-booster' );
		$mail_booster_notifications_service_tooltip        = __( 'Select the notification service you want to recieve alerts about failed emails', 'wp-mail-booster' );
		$mail_booster_notifications_service_email          = __( 'Email', 'wp-mail-booster' );
		$mail_booster_notifications_service_pushover       = __( 'Push Over', 'wp-mail-booster' );
		$mail_booster_notifications_service_slack          = __( 'Slack', 'wp-mail-booster' );
		$mail_booster_notifications_email_address          = __( 'Email Address', 'wp-mail-booster' );
		$mail_booster_notifications_service_pushover_key   = __( 'Pushover User Key', 'wp-mail-booster' );
		$mail_booster_notifications_service_pushover_token = __( 'Pushover App Token', 'wp-mail-booster' );
		$mail_booster_notifications_service_slack_web_book = __( 'Slack WebHook', 'wp-mail-booster' );

		// Dashboard.
		$mail_booster_set_up_guide               = __( 'Setup Guide', 'wp-mail-booster' );
		$mail_booster_join_community             = __( 'Join Our Community', 'wp-mail-booster' );
		$mail_booster_set_up_auth_configuration  = __( 'How to setup Google OAuth API?', 'wp-mail-booster' );
		$mail_booster_yahoo_smtp_details         = __( 'How to setup Yahoo SMTP ( Login )?', 'wp-mail-booster' );
		$mail_booster_outlook_smtp_details       = __( 'How to setup Office 365 SMTP ( Login )?', 'wp-mail-booster' );
		$mail_booster_send_grid_configuration    = __( 'How to setup SendGrid SMTP?', 'wp-mail-booster' );
		$mail_booster_mailgun_configuration      = __( 'How to setup Mailgun SMTP?', 'wp-mail-booster' );
		$mail_booster_follow_us_on_facebook      = __( 'Join Our Facebook VIP Group', 'wp-mail-booster' );
		$mail_booster_follow_us_on_facebook_page = __( 'Follow Us on Our Facebook Page', 'wp-mail-booster' );
		$mail_booster_follow_us_on_twitter       = __( 'Follows Us on Twitter', 'wp-mail-booster' );
		$mail_booster_support                    = __( 'Contact 24/7 Support', 'wp-mail-booster' );
		$mail_booster_leave_a_five_star_rating   = __( 'Leave Us a 5 Star Review', 'wp-mail-booster' );
		$mail_booster_copied_successfully        = __( 'Copied', 'wp-mail-booster' );

		// Sidebar.
		$mail_booster_learn_more             = __( 'Learn More »', 'wp-mail-booster' );
		$mail_booster_advance_email_fields   = __( 'Advanced Email Fields', 'wp-mail-booster' );
		$mail_booster_detailed_email_reports = __( 'Detailed Email Logs', 'wp-mail-booster' );
		$mail_booster_reports_filtering      = __( 'Email Log Filtering', 'wp-mail-booster' );
		$mail_booster_technical_support      = __( '24/7 Technical Support', 'wp-mail-booster' );
		$mail_booster_upgrade                = __( 'Upgrade To Premium »', 'wp-mail-booster' );
		$mail_booster_join_group             = __( 'Join the Group', 'wp-mail-booster' );
		$mail_booster_review_title           = __( 'Leave a 5 Star Review', 'wp-mail-booster' );
		$mail_booster_leave_review           = __( 'Leave my Review »', 'wp-mail-booster' );
		$mail_booster_vip_community          = __( 'Become part of the Tech Banker VIP Community on Facebook. You will get access to the latest beta releases, get help with issues or simply meet like-minded people', 'wp-mail-booster' );
		$mail_booster_greatful_message       = __( 'We are grateful that you’ve decided to join the Tech Banker Family and we are putting maximum efforts to provide you with the Best Product.', 'wp-mail-booster' );
		$mail_booster_star_review            = __( 'Your 5 Star Review will Boost our Morale by 10x!', 'wp-mail-booster' );

		// wizard.
		$mail_booster_wizard_basic_info    = __( 'Account Info', 'wp-mail-booster' );
		$mail_booster_wizard_account_setup = __( 'Login Credentials', 'wp-mail-booster' );
		$mail_booster_wizard_confirm       = __( 'Verify', 'wp-mail-booster' );

		// Menus.
		$wp_mail_booster                     = 'WP Mail Booster';
		$mail_booster_email_configuration    = __( 'Wizard Setup', 'wp-mail-booster' );
		$mail_booster_email_logs             = __( 'Email Logs', 'wp-mail-booster' );
		$mail_booster_email_notification     = __( 'Notifications', 'wp-mail-booster' );
		$mail_booster_test_email             = __( 'Test Email', 'wp-mail-booster' );
		$mail_booster_settings               = __( 'Plugin Settings', 'wp-mail-booster' );
		$mail_booster_roles_and_capabilities = __( 'Roles & Capabilities', 'wp-mail-booster' );
		$mail_booster_system_information     = __( 'System Information', 'wp-mail-booster' );
		$mail_booster_upgrade_now            = __( 'Upgrade Now', 'wp-mail-booster' );

		// Footer.
		$mail_booster_success             = __( 'Success!', 'wp-mail-booster' );
		$mail_booster_successfully_saved  = __( 'Saved Successfully!', 'wp-mail-booster' );
		$mail_booster_confirm_message     = __( 'Are you sure ?', 'wp-mail-booster' );
		$mail_booster_test_email_sent     = __( 'Test Email was sent Successfully!', 'wp-mail-booster' );
		$mail_booster_test_email_not_send = __( 'Test Email was not sent!', 'wp-mail-booster' );
		$mail_booster_delete_log          = __( 'Data Deleted Successfully!', 'wp-mail-booster' );
		$oauth_not_supported              = __( 'The OAuth is not supported by providing SMTP Host, kindly provide username and password', 'wp-mail-booster' );

		// Common Variables.
		$mail_booster_status              = __( 'Status', 'wp-mail-booster' );
		$mail_booster_sent_status         = __( 'Sent', 'wp-mail-booster' );
		$mail_booster_not_sent_status     = __( 'Not Sent', 'wp-mail-booster' );
		$mail_booster_status_tooltip      = __( 'Status of Logs to view', 'wp-mail-booster' );
		$mail_booster_user_access_message = __( 'You don\'t have Sufficient Access to this Page. Kindly contact the Administrator for more Privileges', 'wp-mail-booster' );
		$mail_booster_enable              = __( 'Enable', 'wp-mail-booster' );
		$mail_booster_disable             = __( 'Disable', 'wp-mail-booster' );
		$mail_booster_override            = __( 'Override', 'wp-mail-booster' );
		$mail_booster_dont_override       = __( 'Don\'t Override', 'wp-mail-booster' );
		$mail_booster_save_changes        = __( 'Save Settings', 'wp-mail-booster' );
		$mail_booster_subject             = __( 'Subject', 'wp-mail-booster' );
		$mail_booster_action              = __( 'Action', 'wp-mail-booster' );
		$mail_booster_next_step           = __( 'Next Step', 'wp-mail-booster' );
		$mail_booster_previous_step       = __( 'Previous Step', 'wp-mail-booster' );

		// Email Setup.
		$mail_booster_mailgun_api_details                            = __( 'Get Mailgun API Key', 'wp-mail-booster' );
		$mail_booster_mailgun_api_details_tooltip                    = __( 'In this field, you would need to provide Mailgun API Key', 'wp-mail-booster' );
		$mail_booster_mailgun_domain_name                            = __( 'Domain Name', 'wp-mail-booster' );
		$mail_booster_mailgun_domain_name_tooltip                    = __( 'In this field, you would need to provide Mailgun domain name', 'wp-mail-booster' );
		$mail_booster_send_grid_api_details                          = __( 'SendGrid API', 'wp-mail-booster' );
		$mail_booster_send_grid_api_details_tooltip                  = __( 'In this field, you need to provide SendGrid API Key', 'wp-mail-booster' );
		$mail_booster_email_configuration_from_name                  = __( 'From Name', 'wp-mail-booster' );
		$mail_booster_email_configuration_from_email                 = __( 'From Email', 'wp-mail-booster' );
		$mail_booster_email_configuration_mailer_type                = __( 'Mailer Type', 'wp-mail-booster' );
		$mail_booster_email_configuration_mailer_type_tooltip        = __( 'Choose among the variety of options for routing emails', 'wp-mail-booster' );
		$mail_booster_email_configuration_send_email_via_smtp        = __( 'Send Email via SMTP', 'wp-mail-booster' );
		$mail_booster_email_configuration_send_email_via_mailgun_api = __( 'Mailgun API', 'wp-mail-booster' );
		$mail_booster_get_sendgrid_api_key                           = __( 'Get SendGrid API Key', 'wp-mail-booster' );
		$mail_booster_email_configuration_use_php_mail_function      = __( 'Use The PHP mail() Function', 'wp-mail-booster' );
		$mail_booster_email_configuration_smtp_host                  = __( 'SMTP Host', 'wp-mail-booster' );
		$mail_booster_email_configuration_smtp_host_tooltip          = __( 'Server that will send the email', 'wp-mail-booster' );
		$mail_booster_email_configuration_smtp_port                  = __( 'SMTP Port', 'wp-mail-booster' );
		$mail_booster_email_configuration_smtp_port_tooltip          = __( 'Port to connect to the email server', 'wp-mail-booster' );
		$mail_booster_email_configuration_encryption                 = __( 'Encryption', 'wp-mail-booster' );
		$mail_booster_email_configuration_encryption_tooltip         = __( 'Encrypt the email when sent to the email server using the different methods available', 'wp-mail-booster' );
		$mail_booster_email_configuration_no_encryption              = 'No Encryption';
		$mail_booster_email_configuration_use_ssl_encryption         = 'SSL Encryption';
		$mail_booster_email_configuration_use_tls_encryption         = 'TLS Encryption';
		$mail_booster_email_configuration_authentication             = __( 'Authentication', 'wp-mail-booster' );
		$mail_booster_email_configuration_authentication_tooltip     = __( 'Method for authentication (almost always Login)', 'wp-mail-booster' );
		$mail_booster_email_configuration_test_email_address_tooltip = __( 'A valid Email Address on which you would like to send a Test Email', 'wp-mail-booster' );
		$mail_booster_email_configuration_subject_test_tooltip       = __( 'Subject Line for your Test Email', 'wp-mail-booster' );
		$mail_booster_email_configuration_content_tooltip            = __( 'Email Content for your Test Email', 'wp-mail-booster' );
		$mail_booster_email_configuration_send_test_email            = __( 'Send Test Email', 'wp-mail-booster' );
		$mail_booster_email_configuration_smtp_debugging_output      = __( 'SMTP Debugging Output', 'wp-mail-booster' );
		$mail_booster_email_configuration_send_test_email_textarea   = __( 'Checking your settings', 'wp-mail-booster' );
		$mail_booster_email_configuration_result                     = __( 'Result', 'wp-mail-booster' );
		$mail_booster_email_configuration_send_another_test_email    = __( 'Send Another Test Email', 'wp-mail-booster' );
		$mail_booster_email_configuration_enable_from_name           = __( 'From Name Configuration', 'wp-mail-booster' );
		$mail_booster_email_configuration_enable_from_name_tooltip   = __( 'Do you want to override the Default Name that tells email recipient about who sent the email?', 'wp-mail-booster' );
		$mail_booster_email_configuration_enable_from_email          = __( 'From Email Configuration', 'wp-mail-booster' );
		$mail_booster_email_configuration_enable_from_email_tooltip  = __( 'Do you want to override the Default Email Address that tells email recipient about the sender?', 'wp-mail-booster' );
		$mail_booster_email_configuration_username                   = __( 'Username', 'wp-mail-booster' );
		$mail_booster_email_configuration_username_tooltip           = __( 'Login is typically the full email address (Example: mailbox@yourdomain.com)', 'wp-mail-booster' );
		$mail_booster_email_configuration_password                   = __( 'Password', 'wp-mail-booster' );
		$mail_booster_email_configuration_password_tooltip           = __( 'Password is typically the same as the password to retrieve the email', 'wp-mail-booster' );
		$mail_booster_email_configuration_redirect_uri               = __( 'Redirect URI', 'wp-mail-booster' );
		$mail_booster_email_configuration_redirect_uri_tooltip       = __( 'Please copy this Redirect URI and Paste into Redirect URI field when creating your app', 'wp-mail-booster' );
		$mail_booster_email_configuration_use_oauth                  = 'OAuth (Client Id and Secret Key required)';
		$mail_booster_email_configuration_none                       = 'None';
		$mail_booster_email_configuration_use_plain_authentication   = 'Plain Authentication';
		$mail_booster_email_configuration_cram_md5                   = 'Cram-MD5';
		$mail_booster_email_configuration_login                      = 'Login';
		$mail_booster_email_configuration_client_id                  = __( 'Client Id', 'wp-mail-booster' );
		$mail_booster_email_configuration_client_secret              = __( 'Secret Key', 'wp-mail-booster' );
		$mail_booster_email_configuration_client_id_tooltip          = __( 'Client Id issued by your SMTP Host', 'wp-mail-booster' );
		$mail_booster_email_configuration_client_secret_tooltip      = __( 'Secret Key issued by your SMTP Host', 'wp-mail-booster' );
		$mail_booster_email_configuration_tick_for_sent_mail         = __( 'Yes, automatically send a Test Email upon clicking on the Next Step Button to verify settings', 'wp-mail-booster' );
		$mail_booster_email_configuration_email_address_tooltip      = __( 'A valid Email Address account from which you would like to send Emails', 'wp-mail-booster' );
		$mail_booster_email_configuration_reply_to                   = __( 'Reply To', 'wp-mail-booster' );
		$mail_booster_email_configuration_reply_to_tooltip           = __( 'A valid Email Address that will be used in the \'Reply-To\' field of the email', 'wp-mail-booster' );
		$mail_booster_email_configuration_get_google_credentials     = __( 'Get API Key', 'wp-mail-booster' );
		$mail_booster_email_configuration_how_to_set_up              = __( 'How to Setup?', 'wp-mail-booster' );

		// Email Logs.
		$mail_booster_start_date_title        = __( 'Start Date', 'wp-mail-booster' );
		$mail_booster_resend                  = __( 'Resend Email', 'wp-mail-booster' );
		$mail_booster_start_date_tooltip      = __( 'Start Date for Email Logs', 'wp-mail-booster' );
		$mail_booster_end_date_title          = __( 'End Date', 'wp-mail-booster' );
		$mail_booster_limit_records_title     = __( 'Limit Records', 'wp-mail-booster' );
		$mail_booster_limit_records_tooltip   = __( 'Number of Logs to view', 'wp-mail-booster' );
		$mail_booster_all_records             = 'All';
		$mail_booster_end_date_tooltip        = __( 'End Date for Email Logs', 'wp-mail-booster' );
		$mail_booster_submit                  = __( 'Submit', 'wp-mail-booster' );
		$mail_booster_email_logs_bulk_action  = __( 'Bulk Action', 'wp-mail-booster' );
		$mail_booster_email_logs_delete       = __( 'Delete', 'wp-mail-booster' );
		$mail_booster_email_logs_apply        = __( 'Apply', 'wp-mail-booster' );
		$mail_booster_email_logs_email_to     = __( 'Email To', 'wp-mail-booster' );
		$mail_booster_email_logs_show_details = __( 'Email Content', 'wp-mail-booster' );
		$mail_booster_email_logs_show_outputs = __( 'Debug Output', 'wp-mail-booster' );
		$mail_booster_date_time               = __( 'Date/Time', 'wp-mail-booster' );

		// Email Notification.
		$mail_booster_notification_update_plugin              = __( 'Email when Plugin Updates are Available', 'wp-mail-booster' );
		$mail_booster_notification_update_plugin_tooltip      = __( 'Choose Enable to send an email when Plugin Updates are available', 'wp-mail-booster' );
		$mail_booster_notification_mail_update_plugin         = __( 'Email when Plugin Updated', 'wp-mail-booster' );
		$mail_booster_notification_mail_update_plugin_tooltip = __( 'Choose Enable to send an email when Plugin is Updated', 'wp-mail-booster' );
		$mail_booster_notification_update_theme               = __( 'Email when Theme Updates are Available', 'wp-mail-booster' );
		$mail_booster_notification_update_theme_tooltip       = __( 'Choose Enable to send an email when Theme Updates are available', 'wp-mail-booster' );
		$mail_booster_notification_wordpress                  = __( 'Email when WordPress Automatically Updated', 'wp-mail-booster' );
		$mail_booster_notification_wordpress_tooltip          = __( 'Choose Disable to stop sending an email when WordPress Updated Automatically', 'wp-mail-booster' );

		// Settings.
		$mail_booster_settings_auto_clear_logs                    = __( 'Auto Clear Email Logs', 'wp-mail-booster' );
		$mail_booster_settings_auto_clear_logs_tooltips           = __( 'Do you want to Clear Email Logs Automatically?', 'wp-mail-booster' );
		$mail_booster_settings_delete_logs_after                  = __( 'Delete Email Logs of', 'wp-mail-booster' );
		$mail_booster_settings_delete_logs_after_tooltips         = __( 'Email Logs would be removed automatically after the above specified days', 'wp-mail-booster' );
		$mail_booster_settings_delete_logs_after_one_day          = '1 Day';
		$mail_booster_settings_delete_logs_after_seven_days       = '7 Days';
		$mail_booster_settings_delete_logs_after_forteen_days     = '14 Days';
		$mail_booster_settings_delete_logs_after_twentyone_days   = '21 Days';
		$mail_booster_settings_delete_logs_after_twentyeight_days = '28 Days';
		$mail_booster_settings_automatic_plugin_update            = __( 'Automatic Plugin Updates', 'wp-mail-booster' );
		$mail_booster_settings_debug_mode                         = __( 'Debug Mode', 'wp-mail-booster' );
		$mail_booster_settings_debug_mode_tooltip                 = __( 'Do you want to see Debugging Output for your emails?', 'wp-mail-booster' );
		$mail_booster_remove_tables_title                         = __( 'Remove Database at Uninstall', 'wp-mail-booster' );
		$mail_booster_remove_tables_tooltip                       = __( 'Do you want to remove database at Uninstall of the Plugin?', 'wp-mail-booster' );
		$mail_booster_monitoring_email_log_title                  = __( 'Monitoring Email Logs', 'wp-mail-booster' );
		$mail_booster_monitoring_email_log_tooltip                = __( 'Do you want to monitor your all Outgoing Emails?', 'wp-mail-booster' );


		// Roles and Capabilities.
		$mail_booster_roles_capabilities_show_menu                        = __( 'Show Mail Booster Menu', 'wp-mail-booster' );
		$mail_booster_roles_capabilities_show_menu_tooltip                = __( 'Choose among the following roles who would be able to see the Mail Booster Menu?', 'wp-mail-booster' );
		$mail_booster_roles_capabilities_administrator                    = __( 'Administrator', 'wp-mail-booster' );
		$mail_booster_roles_capabilities_author                           = __( 'Author', 'wp-mail-booster' );
		$mail_booster_roles_capabilities_editor                           = __( 'Editor', 'wp-mail-booster' );
		$mail_booster_roles_capabilities_contributor                      = __( 'Contributor', 'wp-mail-booster' );
		$mail_booster_roles_capabilities_subscriber                       = __( 'Subscriber', 'wp-mail-booster' );
		$mail_booster_roles_capabilities_others                           = __( 'Others', 'wp-mail-booster' );
		$mail_booster_roles_capabilities_topbar_menu                      = __( 'Show Mail Booster Top Bar Menu', 'wp-mail-booster' );
		$mail_booster_roles_capabilities_topbar_menu_tooltip              = __( 'Do you want to show Mail Booster menu in Top Bar?', 'wp-mail-booster' );
		$mail_booster_roles_capabilities_administrator_role               = __( 'An Administrator Role can do the following', 'wp-mail-booster' );
		$mail_booster_roles_capabilities_administrator_role_tooltip       = __( 'Choose what pages would be visible to the users having Administrator Access', 'wp-mail-booster' );
		$mail_booster_roles_capabilities_full_control                     = __( 'Full Control', 'wp-mail-booster' );
		$mail_booster_roles_capabilities_author_role                      = __( 'An Author Role can do the following', 'wp-mail-booster' );
		$mail_booster_roles_capabilities_author_role_tooltip              = __( 'Choose what pages would be visible to the users having Author Access', 'wp-mail-booster' );
		$mail_booster_roles_capabilities_editor_role                      = __( 'An Editor Role can do the following', 'wp-mail-booster' );
		$mail_booster_roles_capabilities_editor_role_tooltip              = __( 'Choose what pages would be visible to the users having Editor Access', 'wp-mail-booster' );
		$mail_booster_roles_capabilities_contributor_role                 = __( 'A Contributor Role can do the following', 'wp-mail-booster' );
		$mail_booster_roles_capabilities_contributor_role_tooltip         = __( 'Choose what pages would be visible to the users having Contributor Access', 'wp-mail-booster' );
		$mail_booster_roles_capabilities_other_role                       = __( 'Other Roles can do the following', 'wp-mail-booster' );
		$mail_booster_roles_capabilities_other_role_tooltip               = __( 'Choose what pages would be visible to the users having Others Role Access', 'wp-mail-booster' );
		$mail_booster_roles_capabilities_other_roles_capabilities         = __( 'Please tick the appropriate capabilities for security purposes', 'wp-mail-booster' );
		$mail_booster_roles_capabilities_other_roles_capabilities_tooltip = __( 'Only users with these capabilities can access Mail Booster', 'wp-mail-booster' );
		$mail_booster_roles_capabilities_subscriber_role                  = __( 'A Subscriber Role can do the following', 'wp-mail-booster' );
		$mail_booster_roles_capabilities_subscriber_role_tooltip          = __( 'Choose what pages would be visible to the users having Subscriber Access', 'wp-mail-booster' );

		// Test Email.
		$mail_booster_test_email_sending_test_email = __( 'Sending Test Email to', 'wp-mail-booster' );
		$mail_booster_licensing_api_key_label       = __( 'API KEY', 'wp-mail-booster' );

		// Email Setup.
		$mail_booster_additional_header         = __( 'Additional Headers', 'wp-mail-booster' );
		$mail_booster_additional_header_tooltip = __( 'You also can insert additional headers in this optional field in order to include in your email', 'wp-mail-booster' );
	}
}
