<?php
/**
 * This file is used for widget.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/lib
 * @version 2.0.0
 */

	/**
	 * This file is used for displaying dashboard widget.
	 *
	 * @param string $type .
	 */
function get_mail_configuration_data_mail_booster( $type ) {
	global $wpdb;
	$meta_value = $wpdb->get_var(
		$wpdb->prepare(
			'SELECT meta_value FROM ' . $wpdb->prefix . 'mail_booster_meta WHERE meta_key=%s', $type
		)
	);// WPCS: db call ok; no-cache ok.
	return maybe_unserialize( $meta_value );
}
$unserialized_mail_configuration_data = get_mail_configuration_data_mail_booster( 'email_configuration' );

/**
 * This is used for displaying today's data.
 *
 * @param string $current_date .
 * @param string $status .
 */
function get_mail_booster_today_logs_data( $current_date, $status ) {
	global $wpdb;
	// Get current week data.
	$current_date          = strtotime( date( 'y-m-d' ) );
	$email_logs_today_data = $wpdb->get_var(
		$wpdb->prepare(
			'SELECT count( status ) FROM ' . $wpdb->prefix . 'mail_booster_logs WHERE timestamp >= %d AND status = %s', $current_date, $status
		)
	);// WPCS: db call ok; no-cache ok.
	return $email_logs_today_data;
}
$email_logs_today_sent_data     = get_mail_booster_today_logs_data( strtotime( date( 'y-m-d' ) ), 'Sent' );
$email_logs_today_not_sent_data = get_mail_booster_today_logs_data( strtotime( date( 'y-m-d' ) ), 'Not Sent' );

/**
 * This is used for displaying current week data.
 *
 * @param string $start_date .
 * @param string $end_date .
 * @param string $status .
 */
function get_mail_booster_logs_data( $start_date, $end_date, $status ) {
	global $wpdb;
	// Get current week data.
	$end_date        = MAIL_BOOSTER_LOCAL_TIME;
	$start_date      = strtotime( 'monday this week', $end_date );
	$email_logs_data = $wpdb->get_var(
		$wpdb->prepare(
			'SELECT count( status ) FROM ' . $wpdb->prefix . 'mail_booster_logs WHERE timestamp BETWEEN %d AND %d AND status = %s', $start_date, $end_date, $status
		)
	);// WPCS: db call ok; no-cache ok.
	return $email_logs_data;
}
$email_logs_sent_data     = get_mail_booster_logs_data( strtotime( 'last monday', MAIL_BOOSTER_LOCAL_TIME ), MAIL_BOOSTER_LOCAL_TIME, 'Sent' );
$email_logs_not_sent_data = get_mail_booster_logs_data( strtotime( 'last monday', MAIL_BOOSTER_LOCAL_TIME ), MAIL_BOOSTER_LOCAL_TIME, 'Not Sent' );

/**
 * This is used for displaying last week data.
 *
 * @param string $start_week .
 * @param string $end_week .
 * @param string $status .
 */
function get_mail_booster_last_week_logs_data( $start_week, $end_week, $status ) {
	global $wpdb;
	// Get last week data.
	$previous_week = strtotime( '-1 week +1 day' );
	$start_week    = strtotime( 'last monday', $previous_week );
	$end_week      = strtotime( 'next sunday', $start_week );

	$email_logs_last_week_data = $wpdb->get_var(
		$wpdb->prepare(
			'SELECT count( status ) FROM ' . $wpdb->prefix . 'mail_booster_logs WHERE timestamp BETWEEN %d AND %d AND status = %s', $start_week, $end_week, $status
		)
	);// WPCS: db call ok; no-cache ok.
	return $email_logs_last_week_data;
}
$email_logs_last_week_sent_data     = get_mail_booster_last_week_logs_data( strtotime( 'last sunday midnight', strtotime( '-1 week +1 day' ) ), strtotime( 'next saturday', strtotime( 'last sunday midnight', strtotime( '-1 week +1 day' ) ) ), 'Sent' );
$email_logs_last_week_not_sent_data = get_mail_booster_last_week_logs_data( strtotime( 'last sunday midnight', strtotime( '-1 week +1 day' ) ), strtotime( 'next saturday', strtotime( 'last sunday midnight', strtotime( '-1 week +1 day' ) ) ), 'Not Sent' );

/**
 * This is used for displaying current month data.
 *
 * @param string $first_day_this_month .
 * @param string $end_date .
 * @param string $status .
 */
function get_mail_booster_this_month_logs_data( $first_day_this_month, $end_date, $status ) {
	global $wpdb;
	// Get this month data.
	$end_date                   = MAIL_BOOSTER_LOCAL_TIME;
	$first_day_this_month       = strtotime( date( '01-m-Y' ) );
	$email_logs_this_month_data = $wpdb->get_var(
		$wpdb->prepare(
			'SELECT count( status ) FROM ' . $wpdb->prefix . 'mail_booster_logs WHERE timestamp BETWEEN %d AND %d AND status = %s', $first_day_this_month, $end_date, $status
		)
	);// WPCS: db call ok; no-cache ok.
	return $email_logs_this_month_data;
}
$email_logs_this_month_sent_data     = get_mail_booster_this_month_logs_data( strtotime( date( 'm-01-Y' ) ), MAIL_BOOSTER_LOCAL_TIME, 'Sent' );
$email_logs_this_month_not_sent_data = get_mail_booster_this_month_logs_data( strtotime( date( 'm-01-Y' ) ), MAIL_BOOSTER_LOCAL_TIME, 'Not Sent' );

/**
 * This is used for displaying last month data.
 *
 * @param string $last_month_start_date .
 * @param string $last_month_end_date .
 * @param string $status .
 */
function get_mail_booster_last_month_logs_data( $last_month_start_date, $last_month_end_date, $status ) {
	global $wpdb;
	// Get last month data.
	$last_month_start_date      = strtotime( 'first day of previous month' );
	$end_date                   = strtotime( 'first day of this month' );
	$last_month_end_date        = strtotime( '-1 day', $end_date );
	$email_logs_last_month_data = $wpdb->get_var(
		$wpdb->prepare(
			'SELECT count( status ) FROM ' . $wpdb->prefix . 'mail_booster_logs WHERE timestamp BETWEEN %d AND %d AND status = %s', $last_month_start_date, $last_month_end_date, $status
		)
	);// WPCS: db call ok; no-cache ok.
	return $email_logs_last_month_data;
}
$email_logs_last_month_sent_data     = get_mail_booster_last_month_logs_data( strtotime( 'first day of previous month' ), strtotime( 'last day of previous month' ), 'Sent' );
$email_logs_last_month_not_sent_data = get_mail_booster_last_month_logs_data( strtotime( 'first day of previous month' ), strtotime( 'last day of previous month' ), 'Not Sent' );

/**
 * This is used for displaying last month data.
 *
 * @param string $start_date_year .
 * @param string $end_date_year .
 * @param string $status .
 */
function get_mail_booster_this_year_logs_data( $start_date_year, $end_date_year, $status ) {
	global $wpdb;
	// Get this month data.
	$start_date_year           = strtotime( 'first day of january ' . date( 'Y' ) );
	$end_date_year             = strtotime( 'last day of december ' . date( 'Y' ) );
	$email_logs_this_year_data = $wpdb->get_var(
		$wpdb->prepare(
			'SELECT count( status ) FROM ' . $wpdb->prefix . 'mail_booster_logs WHERE timestamp BETWEEN %d AND %d AND status = %s', $start_date_year, $end_date_year, $status
		)
	);// WPCS: db call ok; no-cache ok.
	return $email_logs_this_year_data;
}
$email_logs_this_year_sent_data     = get_mail_booster_this_year_logs_data( strtotime( 'first day of january ' . date( 'Y' ) ), strtotime( 'last day of december ' . date( 'Y' ) ), 'Sent' );
$email_logs_this_year_not_sent_data = get_mail_booster_this_year_logs_data( strtotime( 'first day of january ' . date( 'Y' ) ), strtotime( 'last day of december ' . date( 'Y' ) ), 'Not Sent' );

$mail_booster_encryption = '';
switch ( $unserialized_mail_configuration_data['enc_type'] ) {
	case 'tls':
		$mail_booster_encryption = 'TLS Encryption';
		break;
	case 'ssl':
		$mail_booster_encryption = 'SSL Encryption';
		break;
	default:
		$mail_booster_encryption = 'No Encryption';
		break;
}
$mail_booster_authentication = '';
switch ( esc_attr( $unserialized_mail_configuration_data['auth_type'] ) ) {
	case 'crammd5':
		$mail_booster_authentication = 'Crammd5';
		break;
	case 'oauth2':
		$mail_booster_authentication = 'Oauth2';
		break;
	case 'login':
		$mail_booster_authentication = 'Login';
		break;
	case 'plain':
		$mail_booster_authentication = 'Plain';
		break;
	default:
		$mail_booster_authentication = 'No';
		break;
}
switch ( esc_attr( $unserialized_mail_configuration_data['mailer_type'] ) ) {
	case 'smtp':
		$mail_booster_mailer_type = 'SMTP';
		break;

	case 'sendgrid_api':
		$mail_booster_mailer_type = 'SendGrid API';
		break;

	case 'mailgun_api':
		$mail_booster_mailer_type = 'Mailgun API';
		break;

	default:
		$mail_booster_mailer_type = 'PHP Mailer';
		break;
}
$mail_booster_encryption_type = esc_attr( $unserialized_mail_configuration_data['mailer_type'] ) === 'smtp' ? ' - ' . $mail_booster_encryption : '';
$mail_booster_host_name       = esc_attr( $unserialized_mail_configuration_data['hostname'] );
$mail_booster_port_number     = esc_attr( $unserialized_mail_configuration_data['port'] );
$mail_booster_hostname_port   = esc_attr( $unserialized_mail_configuration_data['mailer_type'] ) === 'smtp' ? ' ' . $mail_booster_host_name . ':' . $mail_booster_port_number : '';
$password_authentication      = esc_attr( $unserialized_mail_configuration_data['mailer_type'] ) === 'smtp' ? ' Password ( ' . $mail_booster_authentication . ' ) ' : '';
$mail_booster_authentication  = esc_attr( $unserialized_mail_configuration_data['mailer_type'] ) === 'smtp' ? ' authentication' : '';
$mail_booster_smtp_to         = esc_attr( $unserialized_mail_configuration_data['mailer_type'] ) === 'smtp' ? ' ' . __( 'to', 'wp-mail-booster' ) : '';
$mail_booster_smtp_using      = esc_attr( $unserialized_mail_configuration_data['mailer_type'] ) === 'smtp' ? ' ' . __( 'using', 'wp-mail-booster' ) : '';
?>
<style>
	.mail-booster-stats-table{
		border: 1px solid #ececec;
		width: 100%;
		font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
		border-collapse: collapse;
	}
	.mail-booster-stats-table th {
		padding: 12px 0px 0px 10px;
		padding-bottom: 12px;
		text-align: left;
		background-color: #4CAF50;
		color: white;
	}
	.mail-booster-stats-table td, .mail-booster-stats-table th {
		border: 1px solid #ddd;
		padding: 8px;
	}
	.mail-booster-stats-table tr:nth-child(even) {
		background-color: #f2f2f2;
	}
	.mail-booster-stats-table tr:hover {
		background-color: #ddd;
	}
</style>
<p class="dashicons-before mail-booster-dashicons-email"> <span style="color:green">Mail Booster <?php echo esc_attr( __( 'is configured', 'wp-mail-booster' ) ); ?></span></p>
<p>Mail Booster <?php echo esc_attr( __( 'will send mail through ', 'wp-mail-booster' ) ); ?><b><?php echo esc_attr( $mail_booster_mailer_type ) . esc_attr( $mail_booster_encryption_type ); ?></b><?php echo esc_attr( $mail_booster_smtp_to ); ?><b><?php echo esc_attr( $mail_booster_hostname_port ); ?></b><?php echo esc_attr( $mail_booster_smtp_using ); ?><b><?php echo esc_attr( $password_authentication ); ?></b><?php echo esc_attr( $mail_booster_authentication ); ?>.</p>
<p><a href="admin.php?page=mail_booster_email_logs"><?php echo esc_attr( __( 'Email Logs', 'wp-mail-booster' ) ); ?></a> | <a href="admin.php?page=mail_booster_email_configuration"><?php echo esc_attr( __( 'Email Configuration', 'wp-mail-booster' ) ); ?></a></p>
<table class="mail-booster-stats-table">
	<tr>
		<th></th>
		<th><?php echo esc_attr( __( 'Sent', 'wp-mail-booster' ) ); ?></th>
		<th><?php echo esc_attr( __( 'Not Sent', 'wp-mail-booster' ) ); ?></th>
	</tr>
	<tr>
		<td><?php echo esc_attr( __( 'Today', 'wp-mail-booster' ) ); ?></td>
		<td>
			<a href="admin.php?page=mail_booster_email_logs">
				<strong><?php echo esc_attr( $email_logs_today_sent_data ); ?></strong>
			</a>
		</td>
		<td>
			<a href="admin.php?page=mail_booster_email_logs">
				<strong><?php echo esc_attr( $email_logs_today_not_sent_data ); ?></strong>
			</a>
		</td>
	</tr>
	<tr>
		<td><?php echo esc_attr( __( 'This Week', 'wp-mail-booster' ) ); ?></td>
		<td>
			<a href="admin.php?page=mail_booster_email_logs">
				<strong><?php echo esc_attr( $email_logs_sent_data ); ?></strong>
			</a>
		</td>
		<td>
			<a href="admin.php?page=mail_booster_email_logs">
				<strong><?php echo esc_attr( $email_logs_not_sent_data ); ?></strong>
			</a>
		</td>
	</tr>
	<tr>
		<td><?php echo esc_attr( __( 'Last Week', 'wp-mail-booster' ) ); ?></td>
		<td>
			<a href="admin.php?page=mail_booster_email_logs">
				<strong><?php echo esc_attr( $email_logs_last_week_sent_data ); ?></strong>
			</a>
		</td>
		<td>
			<a href="admin.php?page=mail_booster_email_logs">
				<strong><?php echo esc_attr( $email_logs_last_week_not_sent_data ); ?></strong>
			</a>
		</td>
	</tr>
	<tr>
		<td><?php echo esc_attr( __( 'This Month', 'wp-mail-booster' ) ); ?></td>
		<td>
			<a href="admin.php?page=mail_booster_email_logs">
				<strong><?php echo esc_attr( $email_logs_this_month_sent_data ); ?></strong>
			</a>
		</td>
		<td>
			<a href="admin.php?page=mail_booster_email_logs">
				<strong><?php echo esc_attr( $email_logs_this_month_not_sent_data ); ?></strong>
			</a>
		</td>
	</tr>
	<tr>
		<td><?php echo esc_attr( __( 'Last Month', 'wp-mail-booster' ) ); ?></td>
		<td>
			<a href="admin.php?page=mail_booster_email_logs">
				<strong><?php echo esc_attr( $email_logs_last_month_sent_data ); ?></strong>
			</a>
		</td>
		<td>
			<a href="admin.php?page=mail_booster_email_logs">
				<strong><?php echo esc_attr( $email_logs_last_month_not_sent_data ); ?></strong>
			</a>
		</td>
	</tr>
	<tr>
		<td><?php echo esc_attr( __( 'This Year', 'wp-mail-booster' ) ); ?></td>
		<td>
			<a href="admin.php?page=mail_booster_email_logs">
				<strong><?php echo esc_attr( $email_logs_this_year_sent_data ); ?></strong>
			</a></td>
		<td>
			<a href="admin.php?page=mail_booster_email_logs">
				<strong><?php echo esc_attr( $email_logs_this_year_not_sent_data ); ?></strong>
			</a>
		</td>
	</tr>
	<tr>
		<td colspan="3" style="text-align: center;">
			<a href="https://tech-banker.com/wp-mail-booster/">
				<strong><?php echo esc_attr( __( 'Upgrade Now to Premium Editions', 'wp-mail-booster' ) ); ?></strong>
			</a>
		</td>
	</tr>
</table>
