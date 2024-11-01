<?php
/**
 * This file contains code for remove tables and options at uninstall.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/
 * @version 2.0.0
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}
if ( ! current_user_can( 'manage_options' ) ) {
	return;
} else {
	global $wpdb;
	if ( is_multisite() ) {
		$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );// WPCS: db call ok; no-cache ok.
		foreach ( $blog_ids as $blog_id ) {
			switch_to_blog( $blog_id );// @codingStandardsIgnoreLine.
			$version = get_option( 'mail-booster-version-number' );
			if ( false !== $version ) {
				$settings_remove_tables             = $wpdb->get_var(
					$wpdb->prepare(
						'SELECT meta_value FROM ' . $wpdb->prefix . 'mail_booster_meta WHERE meta_key = %s', 'settings'
					)
				);// WPCS: db call ok; no-cache ok.
				$settings_remove_tables_unserialize = maybe_unserialize( $settings_remove_tables );
				if ( 'enable' === esc_attr( $settings_remove_tables_unserialize['remove_tables_at_uninstall'] ) ) {
					$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'mail_booster' );// @codingStandardsIgnoreLine.
					$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'mail_booster_meta' );// @codingStandardsIgnoreLine.
					$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'mail_booster_logs' );// @codingStandardsIgnoreLine.
					// Delete options.
					delete_option( 'mail-booster-version-number' );
					delete_option( 'mail_booster_admin_notice' );
					delete_option( 'wp-mail-booster-wizard-set-up' );
					delete_option( 'mail_booster_update_database' );
				}

				// Unschedule schedulers.
				if ( wp_next_scheduled( 'mail_booster_plugin_update_scheduler' ) ) {
					wp_clear_scheduled_hook( 'mail_booster_plugin_update_scheduler' );
				}
				if ( wp_next_scheduled( 'mail_booster_theme_update_scheduler' ) ) {
					wp_clear_scheduled_hook( 'mail_booster_theme_update_scheduler' );
				}
				if ( wp_next_scheduled( 'mail_booster_plugin_updated_scheduler' ) ) {
					wp_clear_scheduled_hook( 'mail_booster_plugin_updated_scheduler' );
				}
			}
			restore_current_blog();
		}
	} else {
		$version = get_option( 'mail-booster-version-number' );
		if ( false !== $version ) {
			// Drop Tables.
			$settings_remove_tables             = $wpdb->get_var(
				$wpdb->prepare(
					'SELECT meta_value FROM ' . $wpdb->prefix . 'mail_booster_meta WHERE meta_key = %s', 'settings'
				)
			);// WPCS: db call ok; no-cache ok.
			$settings_remove_tables_unserialize = maybe_unserialize( $settings_remove_tables );

			if ( 'enable' === esc_attr( $settings_remove_tables_unserialize['remove_tables_at_uninstall'] ) ) {
				$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'mail_booster' );// @codingStandardsIgnoreLine.
				$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'mail_booster_meta' );// @codingStandardsIgnoreLine.
				$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'mail_booster_logs' );// @codingStandardsIgnoreLine.
				// Delete options.
				delete_option( 'mail-booster-version-number' );
				delete_option( 'mail_booster_admin_notice' );
				delete_option( 'wp-mail-booster-wizard-set-up' );
				delete_option( 'mail_booster_update_database' );
			}

			// Unschedule schedulers.
			if ( wp_next_scheduled( 'mail_booster_plugin_update_scheduler' ) ) {
				wp_clear_scheduled_hook( 'mail_booster_plugin_update_scheduler' );
			}
			if ( wp_next_scheduled( 'mail_booster_theme_update_scheduler' ) ) {
				wp_clear_scheduled_hook( 'mail_booster_theme_update_scheduler' );
			}
			if ( wp_next_scheduled( 'mail_booster_plugin_updated_scheduler' ) ) {
				wp_clear_scheduled_hook( 'mail_booster_plugin_updated_scheduler' );
			}
		}
	}
}
