<?php
/**
 * This file is used for dashboard.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/includes
 * @version 2.0.0
 */

?>
<div class="portlet box vivid-blue">
	<div class="portlet-body form">
		<div class="form-body custom-dashboard">
			<div class="row">
				<div class="col-md-12">
					<div id="ux_div_mail_booster_logo">
						<img width="300px" src="<?php echo esc_url( plugins_url( 'assets/global/img/mail-booster-logo.png', dirname( __FILE__ ) ) ); ?>" alt="WP Mail Booster">
					</div>
					<div class="col-md-6 tech-banker-column">
						<h4 class="tech-banker-dashboard-heading"><?php echo esc_attr( $mail_booster_set_up_guide ); ?></h4>
						<ul class="tech-banker-dashboard-listing">
							<li><a target="_blank" href="<?php echo esc_url( TECH_BANKER_URL ); ?>/blog/how-to-setup-google-oauth-api-with-wp-mail-booster/" class="tech-banker-dashboard-links"><i class="dashicons dashicons-cloud tech-banker-dashboard-icons"></i><?php echo ( $mail_booster_set_up_auth_configuration ); ?></a></li>
							<li><a target="_blank" href="<?php echo esc_url( TECH_BANKER_URL ); ?>/blog/how-to-setup-Yahoo-smtp-with-wp-mail-booster/" class="tech-banker-dashboard-links"><i class="dashicons dashicons-sos tech-banker-dashboard-icons"></i><?php echo ( $mail_booster_yahoo_smtp_details ); ?></a></li>
							<li><a target="_blank" href="<?php echo esc_url( TECH_BANKER_URL ); ?>/blog/how-to-setup-office-365-smtp-with-wp-mail-booster/" class="tech-banker-dashboard-links"><i class="dashicons dashicons-screenoptions tech-banker-dashboard-icons"></i><?php echo ( $mail_booster_outlook_smtp_details ); ?></a></li>
							<li><a target="_blank" href="<?php echo esc_url( TECH_BANKER_URL ); ?>/blog/how-to-setup-sendgrid-smtp-with-wp-mail-booster/" class="tech-banker-dashboard-links"><i class="dashicons dashicons-external tech-banker-dashboard-icons"></i><?php echo ( $mail_booster_send_grid_configuration . ' ' ); ?></a><strong><span style="color:#E65454;"><?php echo esc_attr( $mail_booster_pro_label ); ?></strong></span></li>
							<li><a target="_blank" href="<?php echo esc_url( TECH_BANKER_URL ); ?>/blog/how-to-setup-mailgun-smtp-with-wp-mail-booster/" class="tech-banker-dashboard-links"><i class="dashicons dashicons-share-alt2 tech-banker-dashboard-icons"></i><?php echo ( $mail_booster_mailgun_configuration . ' ' ); ?></a><strong><span style="color:#E65454;"><?php echo esc_attr( $mail_booster_pro_label ); ?></strong></span></li>
						</ul>
						</ul>
					</div>
					<div class="col-md-6 tech-banker-column">
						<h4 class="tech-banker-dashboard-heading"><?php echo esc_attr( $mail_booster_join_community ); ?></h4>
						<ul class="tech-banker-dashboard-listing">
							<li><a target="_blank" href="https://www.facebook.com/techbanker" class="tech-banker-dashboard-links"><i class="dashicons dashicons-facebook  tech-banker-dashboard-icons"></i><?php echo ( $mail_booster_follow_us_on_facebook_page ); ?></a></li>
							<li><a target="_blank" href="https://www.facebook.com/groups/152567505440114/" class="tech-banker-dashboard-links"><i class="dashicons dashicons-universal-access-alt  tech-banker-dashboard-icons" ></i><?php echo ( $mail_booster_follow_us_on_facebook ); ?></a></li>
							<li><a target="_blank" href="https://twitter.com/techno_banker" class="tech-banker-dashboard-links"><i class="dashicons dashicons-twitter tech-banker-dashboard-icons"></i><?php echo ( $mail_booster_follow_us_on_twitter ); ?></a></li>
							<li><a target="_blank" href="<?php echo esc_url( TECH_BANKER_URL ); ?>/contact-us/" class="tech-banker-dashboard-links"><i class="dashicons dashicons-admin-users tech-banker-dashboard-icons"></i><?php echo ( $mail_booster_support ); ?></a></li>
							<li><a target="_blank" href="https://wordpress.org/support/plugin/wp-mail-booster/reviews/?filter=5" class="tech-banker-dashboard-links"><i class="dashicons dashicons-star-filled tech-banker-dashboard-icons"></i><?php echo ( $mail_booster_leave_a_five_star_rating ); ?></a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
