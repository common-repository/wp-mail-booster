<?php
/**
 * This Template is used for Wizard
 *
 * @author  Tech Banker
 * @package  wp-mail-booster/views/wizard
 * @version 2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.
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
		$wp_mail_booster_check_status  = wp_create_nonce( 'wp_mail_booster_check_status' );
		?>
		<html>
			<body>
				<div>
					<div class="page-container header-wizard">
						<div class="page-content">
							<div class="row row-custom">
								<div class="col-md-12 textalign">
									<p><?php echo esc_attr( $mail_booster_wizard_welcome_message ); ?></p>
									<p><?php echo esc_attr( $mail_booster_wizard_opportunity ); ?></p>
									<p><?php echo esc_attr( $mail_booster_wizard_diagnostic_info ); ?></p>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<div class="col-md-6">
										<div style="padding-left: 20px;">
											<label class="control-label">
												<strong><?php echo esc_attr( $mail_booster_wizard_first_name ); ?> :</strong>
											</label>
											<span id="ux_txt_first_name_wizard_firstname" style="display:none;vertical-align:middle;">*</span>
											<input type="text" id="ux_txt_first_name" name="ux_txt_first_name" class="form-control">
										</div>
									</div>
									<div class="col-md-6">
										<div style="padding-left: 20px;">
											<label class="control-label">
												<strong><?php echo esc_attr( $mail_booster_wizard_last_name ); ?> :</strong>
											</label>
											<input type="text" id="ux_txt_last_name" name="ux_txt_last_name" class="form-control">
										</div>
									</div>
								</div>
							</div>
							<div class="row row-custom">
								<div class="col-md-12">
									<div style="padding: 10px 0px 0px 20px;">
										<label class="control-label">
											<?php echo esc_attr( $mail_booster_wizard_email_address ); ?> :
										</label>
										<span id="ux_txt_email_address_notifications_validate" style="display:none;vertical-align:middle;">*</span>
										<input type="text" class="form-control" name="ux_txt_email_address_notifications" id="ux_txt_email_address_notifications" value="">
									</div>
									<div class="textalign">
										<p><?php echo esc_attr( $mail_booster_wizard_ready ); ?>. <strong><?php echo esc_attr( $mail_booster_work_fine ); ?></strong></p>
										<p><?php echo esc_attr( $mail_booster_hate_spam ); ?><strong> <?php echo esc_attr( $mail_booster_gurantee ); ?></strong></p>
									</div>
								</div>
								<div class="col-md-12">
									<a class="permissions" onclick="show_hide_details_wp_mail_booster();" style="color:#D43F3F;" ><?php echo esc_attr( $mail_booster_wizard_permission_granted ); ?></a>
								</div>
								<div class="col-md-12" style="display:none;" id="ux_div_wizard_set_up">
									<div class="col-md-6">
										<ul>
											<li>
												<i class="dashicons dashicons-admin-users tb-dashicons-admin-users"></i>
												<div class="admin">
													<span><strong><?php echo esc_attr( $mail_booster_wizard_user_details ); ?></strong></span>
													<p><?php echo esc_attr( $mail_booster_wizard_name_email ); ?></p>
												</div>
											</li>
										</ul>
									</div>
									<div class="col-md-6 align align2">
										<ul>
											<li>
												<i class="dashicons dashicons-admin-plugins tb-dashicons-admin-plugins"></i>
												<div class="admin-plugins">
													<span><strong><?php echo esc_attr( $mail_booster_wizard_current_plugin ); ?></strong></span>
													<p><?php echo esc_attr( $mail_booster_wizard_activation_deactivation ); ?></p>
												</div>
											</li>
										</ul>
									</div>
									<div class="col-md-6">
										<ul>
											<li>
												<i class="dashicons dashicons-testimonial tb-dashicons-testimonial"></i>
												<div class="testimonial">
													<span><strong><?php echo esc_attr( $mail_booster_email_notification ); ?></strong></span>
													<p><?php echo esc_attr( $mail_booster_wizard_updates_announcements ); ?></p>
												</div>
											</li>
										</ul>
									</div>
									<div class="col-md-6 align2">
										<ul>
											<li>
												<i class="dashicons dashicons-welcome-view-site tb-dashicons-welcome-view-site"></i>
												<div class="settings">
													<span><strong><?php echo esc_attr( $mail_booster_wizard_website_overview ); ?></strong></span>
													<p><?php echo esc_attr( $mail_booster_wizard_site_info ); ?></p>
												</div>
											</li>
										</ul>
									</div>
									<div class="col-md-6">
										<ul>
											<li>
												<i class="dashicons dashicons-email-alt tb-dashicons-newsletter"></i>
												<div class="settings">
													<span><strong><?php echo esc_attr( $mail_booster_wizard_newsletter ); ?></strong></span>
													<p><?php echo esc_attr( $mail_booster_wizard_updates_announcements ); ?></p>
												</div>
											</li>
										</ul>
									</div>
								</div>
								<div class="col-md-12 allow">
									<div class="tech-banker-actions">
										<a onclick="plugin_stats_wp_mail_booster('opt_in');" class="button button-primary-wizard">
											<strong><?php echo esc_attr( $mail_booster_wizard_opt_in . ' &amp; ' . $mail_booster_wizard_continue ); ?></strong>
											<i class="dashicons dashicons-arrow-right-alt tb-dashicons-arrow-right-alt"></i>
										</a>
										<a onclick="plugin_stats_wp_mail_booster('skip');" class="button button-secondary-wizard" tabindex="2">
											<strong><?php echo esc_attr( $mail_booster_wizard_skip ); ?></strong>
											<i class="dashicons dashicons-arrow-right-alt tb-dashicons-arrow-right-alt"></i>
										</a>
										<div class="clearfix"></div>
									</div>
								</div>
								<div class="col-md-12 terms">
									<a href="https://tech-banker.com/privacy-policy/" target="_blank"><?php echo esc_attr( $mail_booster_wizard_private_policy ); ?></a>
									<span> - </span>
									<a href="https://tech-banker.com/terms-and-conditions/" target="_blank"><?php echo esc_attr( $mail_booster_wizard_terms . ' &amp; ' . $mail_booster_wizard_conditions ); ?></a>
								</div>
							</div>
						</div>
					</div>
				</body>
			</html>
		<?php
	}
}
