<?php
/**
 * This Template is used for sending Test Email.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/views/test-email
 * @version 2.0.0
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
	} elseif ( TEST_EMAIL_MAIL_BOOSTER === '1' ) {
		$mail_booster_test_email_configuration = wp_create_nonce( 'mail_booster_test_email_configuration' );
		?>
		<div class="row">
			<div class="col-md-9">
				<?php
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/dashboard.php' ) ) {
					include MAIL_BOOSTER_DIR_PATH . 'includes/dashboard.php';
				}
				?>
				<div class="portlet box vivid-blue">
					<div class="portlet-title">
						<div class="caption">
							<i class="dashicons dashicons-email-alt"></i>
							<?php echo esc_attr( $mail_booster_test_email ); ?>
						</div>
					</div>
					<div class="portlet-body form">
						<div class="form-body">
							<form id="ux_frm_test_email_configuration">
								<div id="ux_div_test_mail">
									<div class="form-group">
										<label class="control-label">
											<?php echo esc_attr( $mail_booster_notifications_email_address ); ?> :
											<span class="required" aria-required="true">*</span>
										</label>
										<input type="text" class="form-control" name='ux_txt_email' id='ux_txt_email' value="<?php echo esc_attr( get_option( 'admin_email' ) ); ?>" >
										<i class="controls-description"><?php echo esc_attr( $mail_booster_email_configuration_test_email_address_tooltip ); ?></i>
									</div>
									<div class="form-group">
										<label class="control-label">
											<?php echo esc_attr( $mail_booster_subject ); ?> :
											<span class="required" aria-required="true">*</span>
										</label>
										<input type="text" class="form-control" name="ux_txt_subject" id="ux_txt_subject" value="Test Email - Mail Booster" >
										<i class="controls-description"><?php echo esc_attr( $mail_booster_email_configuration_subject_test_tooltip ); ?></i>
									</div>
									<div class="form-group">
										<label class="control-label">
											<?php echo esc_attr( $mail_booster_email_logs_show_details ); ?> :
											<span class="required" aria-required="true">*</span>
										</label>
										<?php
										$email_configuration = 'This is a demo Email for Email Setup - Mail Booster';
										wp_editor(
											$email_configuration, 'ux_content', array(
												'teeny' => true,
												'textarea_name' => 'description',
												'media_buttons' => false,
												'textarea_rows' => 5,
											)
										);
										?>
										<textarea id="ux_email_configuration_text_area" name="ux_email_configuration_text_area" style="display: none;"></textarea>
										<i class="controls-description"><?php echo esc_attr( $mail_booster_email_configuration_content_tooltip ); ?></i>
									</div>
									<div class="line-separator"></div>
									<div class="form-actions">
										<div class="pull-right">
											<button class="btn vivid-blue" name="ux_btn_save_test_email"  id="ux_btn_save_test_email" onclick="mail_booster_send_test_mail()"><?php echo esc_attr( $mail_booster_email_configuration_send_test_email ); ?></button>
										</div>
									</div>
								</div>
								<div id="console_log_div" style="display: none;">
									<div class="form-group">
										<label class="control-label"><?php echo esc_attr( $mail_booster_email_configuration_smtp_debugging_output ); ?> :</label>
										<textarea name="ux_txtarea_console_log" class="form-control" id="ux_txtarea_console_log" rows="15" readonly="readonly"><?php echo esc_attr( $mail_booster_email_configuration_send_test_email_textarea ); ?></textarea>
									</div>
								</div>
								<div id="ux_div_mail_console" style="display: none;">
									<div id="result_div">
										<div class="form-group">
											<label class="control-label"><?php echo esc_attr( $mail_booster_email_configuration_result ); ?>:</label>
											<textarea name="ux_txtarea_result_log" id="ux_txtarea_result_log" class="mail-booster-email-setup-wizard form-control" rows="16"  readonly="readonly" ></textarea>
										</div>
									</div>
									<div class="line-separator"></div>
									<div class="form-actions">
										<div class="pull-right">
											<input type="button" class="btn vivid-blue" name="ux_btn_another_test_email" <?php echo ! extension_loaded( 'openssl' ) ? 'disabled=disabled' : ''; ?> onclick="another_test_email_mail_booster();" id="ux_btn_another_test_email" value="<?php echo esc_attr( $mail_booster_email_configuration_send_another_test_email ); ?>">
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3 sidebar-menu-tech-banker">
				<?php
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/sidebar.php' ) ) {
					include MAIL_BOOSTER_DIR_PATH . 'includes/sidebar.php';
				}
				?>
			</div>
		</div>
		<?php
	} else {
		?>
		<div class="row">
			<div class="col-md-9">
				<?php
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/dashboard.php' ) ) {
					include MAIL_BOOSTER_DIR_PATH . 'includes/dashboard.php';
				}
				?>
				<div class="portlet box vivid-blue">
					<div class="portlet-title">
						<div class="caption">
							<i class="dashicons dashicons-email-alt"></i>
							<?php echo esc_attr( $mail_booster_test_email ); ?>
						</div>
					</div>
					<div class="portlet-body form">
						<div class="form-body">
							<strong><?php echo esc_attr( $mail_booster_user_access_message ); ?></strong>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3 sidebar-menu-tech-banker">
				<?php
				if ( file_exists( MAIL_BOOSTER_DIR_PATH . 'includes/sidebar.php' ) ) {
					include MAIL_BOOSTER_DIR_PATH . 'includes/sidebar.php';
				}
				?>
			</div>
		</div>
		<?php
	}
}
