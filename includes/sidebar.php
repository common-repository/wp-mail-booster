<?php
/**
 * This contains sidebar widgets.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/includes
 * @version 2.0.0
 */

?>
<div class="portlet box vivid-blue mb-pro-options-head">
	<div class="portlet-title pro-options">
		<div class="caption">
			<i class="dashicons dashicons-admin-customizer"></i>
			<?php echo esc_attr( $mail_booster_more_options ); ?>
		</div>
	</div>
	<div class="addon-list-section">
		<ul class="addon-list">
			<li>
				<a class="addon-title" href="<?php echo esc_url( TECH_BANKER_URL . '/wp-mail-booster/' ); ?>" target="_blank" rel="noopener"><?php echo esc_attr( $mail_booster_advance_email_fields ); ?></a>
				<div class="addon-link-wrapper">
					<a class="learn-more" href="<?php echo esc_url( TECH_BANKER_URL . '/wp-mail-booster/' ); ?>" target="_blank" rel="noopener"> <?php echo esc_attr( $mail_booster_learn_more ); ?> </a>
				</div>
			</li>
			<li>
				<a class="addon-title" href="<?php echo esc_url( TECH_BANKER_URL . '/wp-mail-booster/' ); ?>" target="_blank" rel="noopener"><?php echo esc_attr( $mail_booster_send_grid_api_details ); ?></a>
				<div class="addon-link-wrapper"><a class="learn-more" href="<?php echo esc_url( TECH_BANKER_URL . '/wp-mail-booster/' ); ?>" target="_blank" rel="noopener"> <?php echo esc_attr( $mail_booster_learn_more ); ?> </a>
				</div>
			</li>
			<li>
				<a class="addon-title" href="<?php echo esc_url( TECH_BANKER_URL . '/wp-mail-booster/' ); ?>" target="_blank" rel="noopener"><?php echo esc_attr( $mail_booster_email_configuration_send_email_via_mailgun_api ); ?></a>
				<div class="addon-link-wrapper"><a class="learn-more" href="<?php echo esc_url( TECH_BANKER_URL . '/wp-mail-booster/' ); ?>" target="_blank" rel="noopener"> <?php echo esc_attr( $mail_booster_learn_more ); ?> </a>
				</div>
			</li>
			<li>
				<a class="addon-title" href="<?php echo esc_url( TECH_BANKER_URL . '/wp-mail-booster/' ); ?>" target="_blank" rel="noopener"><?php echo esc_attr( $mail_booster_detailed_email_reports ); ?></a>
				<div class="addon-link-wrapper"><a class="learn-more" href="<?php echo esc_url( TECH_BANKER_URL . '/wp-mail-booster/' ); ?>" target="_blank" rel="noopener"> <?php echo esc_attr( $mail_booster_learn_more ); ?> </a>
				</div>
			</li>
			<li>
				<a class="addon-title" href="<?php echo esc_url( TECH_BANKER_URL . '/wp-mail-booster/' ); ?>" target="_blank" rel="noopener"><?php echo esc_attr( $mail_booster_reports_filtering ); ?></a>
				<div class="addon-link-wrapper"><a class="learn-more" href="<?php echo esc_url( TECH_BANKER_URL . '/wp-mail-booster/' ); ?>" target="_blank" rel="noopener"> <?php echo esc_attr( $mail_booster_learn_more ); ?> </a>
				</div>
			</li>
			<li>
				<a class="addon-title" href="<?php echo esc_url( TECH_BANKER_URL . '/wp-mail-booster/' ); ?>" target="_blank" rel="noopener"><?php echo esc_attr( $mail_booster_settings_automatic_plugin_update ); ?></a>
				<div class="addon-link-wrapper"><a class="learn-more" href="<?php echo esc_url( TECH_BANKER_URL . '/wp-mail-booster/' ); ?>" target="_blank" rel="noopener"> <?php echo esc_attr( $mail_booster_learn_more ); ?> </a>
				</div>
			</li>
			<li>
				<a class="addon-title" href="<?php echo esc_url( TECH_BANKER_URL . '/wp-mail-booster/' ); ?>" target="_blank" rel="noopener"><?php echo esc_attr( $mail_booster_roles_and_capabilities ); ?></a>
				<div class="addon-link-wrapper"><a class="learn-more" href="<?php echo esc_url( TECH_BANKER_URL . '/wp-mail-booster/' ); ?>" target="_blank" rel="noopener"> <?php echo esc_attr( $mail_booster_learn_more ); ?> </a>
				</div>
			</li>
			<li>
				<a class="addon-title" href="<?php echo esc_url( TECH_BANKER_URL . '/wp-mail-booster/' ); ?>" target="_blank" rel="noopener"><?php echo esc_attr( $mail_booster_technical_support ); ?></a>
				<div class="addon-link-wrapper"><a class="learn-more" href="<?php echo esc_url( TECH_BANKER_URL . '/wp-mail-booster/' ); ?>" target="_blank" rel="noopener"> <?php echo esc_attr( $mail_booster_learn_more ); ?> </a>
				</div>
			</li>
		</ul>
	</div>
	<div class="portlet-body form">
		<div class="form-body">
			<a class="btn pro-options" target="_blank" href="<?php echo esc_attr( TECH_BANKER_URL . '/wp-mail-booster/' ); ?>"><?php echo esc_attr( $mail_booster_upgrade ); ?></a>
		</div>
	</div>
</div>
<div class="tech-banker-bloc tech-banker-facebook tech-banker-community">
	<div class="tech-banker-ribbon"><div>VIP</div></div>
	<p class="tech-banker-img">
		<a href="https://www.facebook.com/groups/152567505440114/" target="_blank">
			<img src="<?php echo esc_url( plugins_url( 'assets/global/img/facebook.svg', dirname( __FILE__ ) ) ); ?>" alt="Facebook Group">
		</a>
	</p>
	<div class="tech-banker-content-wrap">
		<p class="content"><?php echo esc_attr( $mail_booster_vip_community ); ?></p>
		<a href="https://www.facebook.com/groups/152567505440114/" class="button tech-banker-button" target="_blank"><?php echo esc_attr( $mail_booster_join_group ); ?></a>
	</div>
	<i class="dashicons dashicons-facebook-alt"></i>
</div>
<div class="portlet box tech-banker-bloc">
	<div class="portlet-title tech-banker-review">
		<div class="caption">
			<i class="tech-banker-icon-review dashicons dashicons dashicons-heart"></i>
			<?php echo esc_attr( $mail_booster_review_title ); ?>
		</div>
	</div>
	<div>
		<p class="content"><?php echo esc_attr( $mail_booster_greatful_message ); ?></p>
		<p class="content"><?php echo esc_attr( $mail_booster_star_review ); ?></p>
		<div class="portlet-body form">
			<div class="form-body">
				<a class="btn tech-banker-leave-review" target="_blank" href="https://wordpress.org/support/plugin/wp-mail-booster/reviews/?filter=5"><?php echo esc_attr( $mail_booster_leave_review ); ?> </a>
			</div>
		</div>
	</div>
	<i class="dashicons dashicons-wordpress"></i>
</div>
