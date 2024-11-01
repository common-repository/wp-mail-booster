<?php
/**
 * This file contains javascript code.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/includes
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
		?>
	</div>
	<script type="text/javascript">
	function show_hide_notifications_service(id, email_div, div, div_id) {
		var email_service = jQuery(id).val();
		switch (email_service) {
			case "email":
				jQuery(email_div).css("display", "block");
				jQuery(div_id).css("display", "none");
				jQuery(div).css("display", "none");
				break;
			case "pushover":
				jQuery(div).css("display", "block");
				jQuery(div_id).css("display", "none");
				jQuery(email_div).css("display", "none");
				break;
			case "slack":
				jQuery(div_id).css("display", "block");
				jQuery(div).css("display", "none");
				jQuery(email_div).css("display", "none");
				break;
			default:
				jQuery(div).css("display", "none");
				jQuery(div_id).css("display", "none");
				jQuery(email_div).css("display", "none");
				break;
		}
	}
	jQuery("li > a").parents("li").each(function ()
	{
		if (jQuery(this).parent("ul.page-sidebar-menu-tech-banker").size() === 1)
		{
			jQuery(this).find("> a").append("<span class=\"selected\"></span>");
		}
	});
	function premium_edition_notification_mail_booster()
	{
		var premium_edition = <?php echo wp_json_encode( $mail_booster_message_pro_edition ); ?>;
		var shortCutFunction = jQuery("#toastTypeGroup_error input:checked").val();
		toastr[shortCutFunction](premium_edition);
	}
	function show_hide_delete_after_logs(id, div_id) {
		var type = jQuery(id).val();
		switch (type) {
			case "enable":
				jQuery(div_id).css("display", "block");
				break;
			case "disable":
				jQuery(div_id).css("display", "none");
				break;
			default:
				jQuery(div_id).css("display", "none");
				break;
		}
	}
	if (typeof (load_sidebar_content_mail_booster) !== "function")
	{
		function load_sidebar_content_mail_booster()
		{
			var menus_height = jQuery(".page-sidebar-menu-tech-banker").height();
			var content_height = jQuery(".page-content").height() + 30;
			if (parseInt(menus_height) > parseInt(content_height))
			{
				jQuery(".page-content").attr("style", "min-height:" + menus_height + "px")
			} else
			{
				jQuery(".page-sidebar-menu-tech-banker").attr("style", "min-height:" + content_height + "px")
			}
		}
	}
	jQuery(".page-sidebar-tech-banker").on("click", "li > a", function (e)
	{
		var hasSubMenu = jQuery(this).next().hasClass("sub-menu");
		var parent = jQuery(this).parent().parent();
		var sidebar_menu = jQuery(".page-sidebar-menu-tech-banker");
		var sub = jQuery(this).next();
		var slideSpeed = parseInt(sidebar_menu.data("slide-speed"));
		parent.children("li.open").children(".sub-menu:not(.always-open)").slideUp(slideSpeed);
		parent.children("li.open").removeClass("open");
		var sidebar_close = parent.children("li.open").removeClass("open");
		if (sidebar_close)
		{
			setInterval(load_sidebar_content_mail_booster, 100);
		}
		if (sub.is(":visible"))
		{
			jQuery(this).parent().removeClass("open");
			sub.slideUp(slideSpeed);
		} else if (hasSubMenu)
		{
			jQuery(this).parent().addClass("open");
			sub.slideDown(slideSpeed);
		}
	});
	if (typeof (paste_only_digits_mail_booster) !== "function")
	{
		function paste_only_digits_mail_booster(control_id)
		{
			jQuery("#" + control_id).on("paste keypress", function (e)
			{
				var $this = jQuery("#" + control_id);
				setTimeout(function ()
				{
					$this.val($this.val().replace(/[^0-9]/g, ""));
				}, 5);
			});
		}
	}
	if (typeof (remove_unwanted_spaces_mail_booster) !== "function")
	{
		function remove_unwanted_spaces_mail_booster(id)
		{
			var api_key = jQuery("#" + id).val();
			api_key = api_key.replace(/[ ]/g, "");
			jQuery("#" + id).val("");
			jQuery("#" + id).val(jQuery.trim(api_key));
		}
	}
	var clipboard = new Clipboard(".dashicons-book");
	clipboard.on("success", function (e)
	{
		var shortCutFunction = jQuery("#manage_messages input:checked").val();
		toastr[shortCutFunction](<?php echo wp_json_encode( $mail_booster_copied_successfully ); ?>);
	});
	var sidebar_load_interval = setInterval(load_sidebar_content_mail_booster, 1000);
	setTimeout(function ()
	{
			clearInterval(sidebar_load_interval);
	}, 5000);
	if (typeof (overlay_loading_mail_booster) !== "function")
	{
		function overlay_loading_mail_booster(control_id)
		{
			var overlay_opacity = jQuery("<div class=\"opacity_overlay\"></div>");
			jQuery("body").append(overlay_opacity);
			var overlay = jQuery("<div class=\"loader_opacity\"><div class=\"processing_overlay\"></div></div>");
			jQuery("body").append(overlay);
			if (control_id !== undefined)
			{
				var message = control_id;
				var success = <?php echo wp_json_encode( $mail_booster_success ); ?>;
				var issuccessmessage = jQuery("#toast-container").exists();
				if (issuccessmessage !== true)
				{
					var shortCutFunction = jQuery("#manage_messages input:checked").val();
					toastr[shortCutFunction](message, success);
				}
			}
		}
	}
	if (typeof (remove_overlay_mail_booster) !== "function"){
		function remove_overlay_mail_booster()
		{
			jQuery(".loader_opacity").remove();
			jQuery(".opacity_overlay").remove();
		}
	}
	if (typeof (base64_encode) !== "function"){
		function base64_encode(data)
		{
			var b64 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';
			var o1, o2, o3, h1, h2, h3, h4, bits, i = 0,
			ac = 0,
			enc = '',
			tmp_arr = [];
			if (!data){
				return data;
			}
			do
			{
				o1 = data.charCodeAt(i++);
				o2 = data.charCodeAt(i++);
				o3 = data.charCodeAt(i++);
				bits = o1 << 16 | o2 << 8 | o3;
				h1 = bits >> 18 & 0x3f;
				h2 = bits >> 12 & 0x3f;
				h3 = bits >> 6 & 0x3f;
				h4 = bits & 0x3f;
				tmp_arr[ac++] = b64.charAt(h1) + b64.charAt(h2) + b64.charAt(h3) + b64.charAt(h4);
			} while (i < data.length);
			enc = tmp_arr.join('');
			var r = data.length % 3;
			return (r ? enc.slice(0, r - 3) : enc) + '==='.slice(r || 3);
		}
	}
	if (typeof (another_test_email_mail_booster) !== "function"){
		function another_test_email_mail_booster()
		{
			jQuery("#ux_div_mail_console").css("display", "none");
			jQuery("#console_log_div").css("display", "none");
			jQuery("#ux_div_test_mail").css("display", "block");
		}
	}
	if (typeof (check_links_oauth_mail_booster) !== "function")
	{
		function check_links_oauth_mail_booster()
		{
			var smtp_host = jQuery("#ux_txt_host").val();
			if (smtp_host === "smtp.gmail.com")
			{
				jQuery("#ux_link_content_google").text("( " +<?php echo wp_json_encode( $mail_booster_email_configuration_get_google_credentials ); ?>);
				jQuery("#ux_link_content").text(<?php echo wp_json_encode( $mail_booster_email_configuration_how_to_set_up ); ?>+ ' )' );
				jQuery("#ux_link_reference_google").attr("href", "https://console.developers.google.com");
				jQuery("#ux_link_reference").attr("href", "https://tech-banker.com/blog/how-to-setup-google-oauth-api-with-wp-mail-booster/");
			} else
			{
				jQuery("#ux_link_content_google").text("");
				jQuery("#ux_link_content").text("");
			}
		}
	}
	if (typeof (mail_booster_mail_sender) !== "function")
	{
		function mail_booster_mail_sender(to_email_address)
		{
			jQuery.post(ajaxurl,
			{
				data: base64_encode(jQuery("#ux_frm_test_email_configuration").serialize()),
				param: "mail_booster_test_email_configuration_module",
				action: "mail_booster_action",
				_wp_nonce: "<?php echo isset( $mail_booster_test_email_configuration ) ? esc_attr( $mail_booster_test_email_configuration ) : ''; ?>"
			},
			function (data)
			{
				jQuery("#ux_txtarea_result_log").html("<?php echo esc_attr( $mail_booster_email_configuration_send_test_email_textarea ); ?>\n");
				jQuery("#ux_txtarea_result_log").append(<?php echo wp_json_encode( $mail_booster_test_email_sending_test_email ); ?> + "&nbsp;" + to_email_address + "\n");
				if (jQuery.trim(data) === "true" || jQuery.trim(data) === "1")
				{
					jQuery("#ux_div_mail_console").css("display", "block");
					jQuery("#console_log_div").css("display", "none");
					jQuery("#ux_txtarea_result_log").append(<?php echo wp_json_encode( $mail_booster_test_email_sent ); ?>);
				} else
				{
					jQuery("#console_log_div").css("display", "none");
					jQuery("#ux_div_mail_console").css("display", "block");
					if (jQuery.trim(data) !== "")
					{
						jQuery("#ux_txtarea_result_log").append('----------------------------------------------------\n'+ data);
					} else
					{
						jQuery("#ux_txtarea_result_log").append(<?php echo wp_json_encode( $mail_booster_test_email_not_send ); ?>);
					}
				}
				load_sidebar_content_mail_booster();
			});
		}
	}
	if (typeof (mail_booster_send_test_mail) !== "function")
	{
		function mail_booster_send_test_mail()
		{
			jQuery("#ux_frm_test_email_configuration").validate
			({
				rules:
				{
					ux_txt_email:
					{
						required: true,
						email: true
					},
					ux_txt_subject:
					{
						required: true
					},
					ux_content:
					{
						required: true
					}
				},
				errorPlacement: function ()
				{
				},
				highlight: function (element)
				{
					jQuery(element).closest(".form-group").removeClass("has-success").addClass("has-error");
				},
				success: function (label, element)
				{
					var icon = jQuery(element).parent(".input-icon").children("i");
					jQuery(element).closest(".form-group").removeClass("has-error").addClass("has-success");
					icon.removeClass("fa-warning").addClass("fa-check");
				},
				submitHandler: function ()
				{
					var to_email_address = jQuery("#ux_txt_email").val();
					if (window.CKEDITOR)
					{
						jQuery("#ux_email_configuration_text_area").val(CKEDITOR.instances["ux_content"].getData());
					} else if (jQuery("#wp-ux_content-wrap").hasClass("tmce-active"))
					{
						jQuery("#ux_email_configuration_text_area").val(tinyMCE.get("ux_content").getContent());
					} else
					{
						jQuery("#ux_email_configuration_text_area").val(jQuery("#ux_content").val());
					}
					mail_booster_mail_sender(to_email_address);
					jQuery("#console_log_div").css("display", "block");
					jQuery("#ux_div_test_mail").css("display", "none");
				}
			});
		}
	}
	function color_picker_mail_booster(id, color_value) {
	jQuery(id).colpick({
		layout: "hex",
		colorScheme: "dark",
		color: color_value,
		onChange: function (hsb, hex, rgb, el, bySetColor) {
			if (!bySetColor)
			jQuery(el).val("#" + hex);
		}
	}).keyup(function () {
		jQuery(this).colpickSetColor("#" + this.value);
		}).focus(function () {
			jQuery(id).colpickSetColor(color_value);
		});
	}
		<?php
		$check_wp_mail_booster_wizard = get_option( 'wp-mail-booster-wizard-set-up' );
		if ( isset( $_GET['page'] ) ) {
			$mb_page = sanitize_text_field( wp_unslash( $_GET['page'] ) );// WPCS: CSRF ok,WPCS: input var ok.
		}
		$page_url = false === $check_wp_mail_booster_wizard ? 'wp_mail_booster_wizard' : $mb_page;
		if ( isset( $_GET['page'] ) ) { // WPCS: CSRF ok,WPCS: input var ok.
			switch ( $page_url ) {
				case 'wp_mail_booster_wizard':
					?>
					if (typeof (show_hide_details_wp_mail_booster) !== "function")
					{
						function show_hide_details_wp_mail_booster()
						{
							if (jQuery("#ux_div_wizard_set_up").hasClass("wizard-set-up"))
							{
								jQuery("#ux_div_wizard_set_up").css("display", "none");
								jQuery("#ux_div_wizard_set_up").removeClass("wizard-set-up");
							} else
							{
								jQuery("#ux_div_wizard_set_up").css("display", "block");
								jQuery("#ux_div_wizard_set_up").addClass("wizard-set-up");
							}
						}
					}
					if (typeof (plugin_stats_wp_mail_booster) !== "function")
					{
						function plugin_stats_wp_mail_booster(type)
						{
							var validate_form = '';
							var email_pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
							var wizard_notification_array = [ 'ux_txt_email_address_notifications', 'ux_txt_first_name' ];
							wizard_notification_array.forEach( function( element ) {
								if( (jQuery('#'+ element).val() === '' || false == email_pattern.test(jQuery("#ux_txt_email_address_notifications").val())) && type !== 'skip' )
									{
										validate_form = 1;
										jQuery('#'+ element).css("border-color","red");
										jQuery('#'+ element+'_validate').css({"display":'','color':'red'});
										jQuery('#'+ element+'_wizard_firstname').css({"display":'','color':'red'});
									} else {
										jQuery('#'+ element).css("border-color","#ddd");
										jQuery('#'+ element+'_validate').css( 'display','none' );
										jQuery('#'+ element+'_wizard_firstname').css( 'display','none' );
									}
							});
							if( validate_form == "" ) {
								overlay_loading_mail_booster();
								jQuery.post(ajaxurl,
								{
									first_name: jQuery("#ux_txt_first_name").val(),
									last_name: jQuery("#ux_txt_last_name").val(),
									id: jQuery("#ux_txt_email_address_notifications").val(),
									type: type,
									param: "wizard_wp_mail_booster",
									action: "mail_booster_action",
									_wp_nonce: "<?php echo esc_attr( $wp_mail_booster_check_status ); ?>"
								},
								function ()
								{
									remove_overlay_mail_booster();
									window.location.href = "admin.php?page=mail_booster_email_configuration";
								});
							}
						}
					}
					<?php
					break;
				case 'mail_booster_email_configuration':
					?>
					jQuery("#ux_mail_booster_li_email_configuration").addClass("active");
					<?php
					if ( '1' === EMAIL_CONFIGURATION_MAIL_BOOSTER ) {
						?>
					if (typeof (select_credentials_mail_booster) !== "function")
					{
						function select_credentials_mail_booster()
						{
							var selected_credential = jQuery("#ux_ddl_mail_booster_authentication").val();
							var type = jQuery("#ux_ddl_type").val();
							if (selected_credential === "oauth2" && type === "smtp")
							{
								jQuery("#ux_div_username_password_authentication").css("display", "none");
								jQuery("#ux_div_oauth_authentication").css("display", "block");
								check_links_oauth_mail_booster();
							} else
							{
								if (selected_credential === "none")
								{
									jQuery("#ux_div_username_password_authentication").css("display", "none");
									jQuery("#ux_div_oauth_authentication").css("display", "none");
								} else
								{
									jQuery("#ux_div_username_password_authentication").css("display", "block");
									jQuery("#ux_div_oauth_authentication").css("display", "none");
								}
							}
						}
					}
					if (typeof (mail_booster_second_step_settings) !== "function")
					{
						function mail_booster_second_step_settings()
						{
							jQuery("#ux_div_first_step").css("display", "none");
							jQuery("#test_email").css("display", "none");
							jQuery("#ux_div_second_step").css("display", "block");
							jQuery("#ux_div_step_progres_bar_width").css("width", "66%");
							jQuery("#ux_div_frm_wizard li:eq(1)").addClass("active");
							jQuery("#ux_div_frm_wizard li:eq(2)").removeClass("active");
						}
					}
					if (typeof (mail_booster_third_step_settings) !== "function")
					{
						function mail_booster_third_step_settings()
						{
							jQuery("#ux_div_first_step").removeClass("first-step-helper");
							jQuery("#test_email").css("display", "block");
							jQuery("#ux_div_first_step").css("display", "none");
							jQuery("#ux_div_second_step").css("display", "none");
							jQuery("#ux_div_step_progres_bar_width").css("width", "100%");
							jQuery("#ux_div_frm_wizard li:eq(1)").addClass("active");
							jQuery("#ux_div_frm_wizard li:eq(2)").addClass("active");
						}
					}
					if (typeof (mail_booster_from_name_override) !== "function")
					{
						function mail_booster_from_name_override(from_name)
						{
							if (jQuery.trim(from_name) === "dont_override")
							{
								jQuery("#ux_txt_mail_booster_from_name").attr("disabled", true);
							} else
							{
								jQuery("#ux_txt_mail_booster_from_name").attr("disabled", false);
							}
						}
					}
					if (typeof (mail_booster_from_email_override) !== "function")
					{
						function mail_booster_from_email_override(from_email)
						{
							if (jQuery.trim(from_email) === "dont_override")
							{
								jQuery("#ux_txt_mail_booster_from_email_configuration").attr("disabled", true);
							} else
							{
								jQuery("#ux_txt_mail_booster_from_email_configuration").attr("disabled", false);
							}
						}
					}
					if (typeof (mail_booster_validate_settings) !== "function")
					{
						function mail_booster_validate_settings()
						{
							jQuery("#ux_frm_email_configuration").validate
							({
								rules:
								{
									ux_txt_mail_booster_from_name:
									{
										required: true
									},
									ux_txt_mail_booster_from_email_configuration:
									{
										required: true,
										email: true
									},
									ux_txt_email_address:
									{
										required: true,
										email: true
									},
									ux_txt_host:
									{
										required: true
									},
									ux_txt_port:
									{
										required: true
									},
									ux_txt_client_id:
									{
										required: true
									},
									ux_txt_client_secret:
									{
										required: true
									},
									ux_txt_username:
									{
										required: true
									},
									ux_txt_password:
									{
										required: true
									},
									ux_txt_sendgrid_api_key:
									{
										required: true
									},
									ux_txt_mailgun_api_key:
									{
										required: true
									},
									ux_txt_mailgun_domain_name:
									{
										required: true
									}
								},
								errorPlacement: function ()
								{
								},
								highlight: function (element)
								{
									jQuery(element).closest(".form-group").removeClass("has-success").addClass("has-error");
								},
								success: function (label, element)
								{
									var icon = jQuery(element).parent(".input-icon").children("i");
									jQuery(element).closest(".form-group").removeClass("has-error").addClass("has-success");
									icon.removeClass("fa-warning").addClass("fa-check");
								},
								submitHandler: function ()
								{
									if (jQuery("#ux_div_first_step").hasClass("first-step-helper"))
									{
										mail_booster_second_step_settings();
									} else if (jQuery("#test_email").hasClass("second-step-helper"))
									{
										jQuery.post(ajaxurl,
										{
											data: base64_encode(jQuery("#ux_frm_email_configuration").serialize()),
											action: "mail_booster_action",
											param: "mail_booster_email_configuration_settings_module",
											_wp_nonce: "<?php echo esc_attr( $mail_booster_email_configuration_settings ); ?>"
										},
										function (data)
										{
											var automatic_mail = jQuery("#ux_chk_automatic_sent_mail").is(":checked");
											var mailer_type = jQuery("#ux_ddl_type").val();
											if (jQuery.trim(data) === "100" && mailer_type === "smtp")
											{
												var shortCutFunction = jQuery("#toastTypeGroup_error input:checked").val();
												toastr[shortCutFunction](<?php echo wp_json_encode( $oauth_not_supported ); ?>);
											} else if (jQuery.trim(data) !== "" && mailer_type === "smtp")
											{
												window.location.href = data;
											} else
											{
												var send_mail = false;
												if (jQuery.trim(automatic_mail) === "true")
												{
													var send_mail = true;
												}
												window.location.href = "admin.php?page=mail_booster_email_configuration&auto_mail=" + send_mail;
											}
										});
									}
								}
							});
						}
					}
					if (typeof (change_settings_mail_booster) !== "function")
					{
						function change_settings_mail_booster()
						{
							var type = jQuery("#ux_ddl_type").val();
							switch (type)
							{
								case "php_mail_function":
									jQuery("#ux_div_smtp_mail_function").css("display", "none");
									jQuery("#ux_div_sendgrid_api").css("display", "none");
									jQuery("#ux_div_mailgun_api").css("display", "none");
								break;
								case "smtp":
									jQuery("#ux_div_smtp_mail_function").css("display", "block");
									jQuery("#ux_div_sendgrid_api").css("display", "none");
									jQuery("#ux_div_mailgun_api").css("display", "none");
								break;
								case "sendgrid_api":
									jQuery("#ux_div_smtp_mail_function").css("display", "none");
									jQuery("#ux_div_sendgrid_api").css("display", "block");
									jQuery("#ux_div_mailgun_api").css("display", "none");
								break;
								case "mailgun_api":
									jQuery("#ux_div_smtp_mail_function").css("display", "none");
									jQuery("#ux_div_mailgun_api").css("display", "block");
									jQuery("#ux_div_sendgrid_api").css("display", "none");
								break;
							}
							select_credentials_mail_booster();
						}
					}
					if (typeof (mail_booster_get_host_port) !== "function")
					{
						function mail_booster_get_host_port()
						{
							change_settings_mail_booster();
							var smtp_user = jQuery("#ux_txt_email_address").val();
							jQuery.post(ajaxurl,
							{
								smtp_user: smtp_user,
								param: "mail_booster_set_hostname_port_module",
								action: "mail_booster_action",
								_wp_nonce: "<?php echo esc_attr( $mail_booster_set_hostname_port ); ?>"
							},
							function (data)
							{
								if (jQuery.trim(data) !== "")
								{
									jQuery("#ux_txt_host").val(data);
									check_links_oauth_mail_booster();
								} else
								{
									jQuery("#ux_txt_host").val("");
									jQuery("#ux_link_content").text("");
								}
								change_settings_mail_booster();
							});
						}
					}
					if (typeof (change_link_content_mail_booster) !== "function")
					{
						function change_link_content_mail_booster()
						{
							var host_type = jQuery("#ux_txt_host").val();
							var indexof = host_type.indexOf("yahoo");
							var hostname = host_type.substr(indexof, 5);
							if (host_type === "smtp.gmail.com")
							{
								check_links_oauth_mail_booster();
								jQuery("#ux_ddl_mail_booster_authentication").val("oauth2");
								select_credentials_mail_booster();
							} else
							{
								check_links_oauth_mail_booster();
								jQuery("#ux_ddl_mail_booster_authentication").val("login");
								select_credentials_mail_booster();
							}
						}
					}
					jQuery(document).ready(function ()
					{
						if (window.CKEDITOR)
						{
							CKEDITOR.replace("ux_content");
						}
						jQuery("#ux_ddl_type").val("<?php echo isset( $email_configuration_array['mailer_type'] ) ? esc_attr( $email_configuration_array['mailer_type'] ) : ''; ?>");
						jQuery("#ux_ddl_mail_booster_authentication").val("<?php echo isset( $email_configuration_array['auth_type'] ) ? esc_attr( $email_configuration_array['auth_type'] ) : 'login'; ?>");
						jQuery("#ux_ddl_encryption").val("<?php echo isset( $email_configuration_array['enc_type'] ) ? esc_attr( $email_configuration_array['enc_type'] ) : ''; ?>");
						<?php
						if ( isset( $test_secret_key_error ) ) {
							?>
							var shortCutFunction = jQuery("#toastTypeGroup_error input:checked").val();
							toastr[shortCutFunction](<?php echo wp_json_encode( $test_secret_key_error ); ?>);
							mail_booster_second_step_settings();
							<?php
						}
						if ( isset( $automatically_send_mail ) ) {
							?>
							window.location.href = "admin.php?page=mail_booster_email_configuration&auto_mail=true";
							<?php
						} elseif ( isset( $automatically_not_send_mail ) ) {
							?>
							window.location.href = "admin.php?page=mail_booster_email_configuration&auto_mail=false";
							<?php
						}
						?>
						load_sidebar_content_mail_booster();
						//change_link_content_mail_booster();
						select_credentials_mail_booster();
						change_settings_mail_booster();
						mail_booster_from_name_override("<?php echo isset( $email_configuration_array['sender_name_configuration'] ) ? esc_attr( $email_configuration_array['sender_name_configuration'] ) : ''; ?>");
						mail_booster_from_email_override("<?php echo isset( $email_configuration_array['from_email_configuration'] ) ? esc_attr( $email_configuration_array['from_email_configuration'] ) : ''; ?>");
						<?php
						if ( isset( $_REQUEST['auto_mail'] ) && sanitize_text_field( wp_unslash( $_REQUEST['auto_mail'] ) ) === 'true' ) { // WPCS: CSRF ok, WPCS: input var ok.
							?>
							mail_booster_mail_sender("<?php echo esc_attr( get_option( 'admin_email' ) ); ?>");
							jQuery("#console_log_div").css("display", "block");
							jQuery("#ux_div_mail_console").css("display", "none");
							jQuery("#ux_div_test_mail").css("display", "none");
							mail_booster_third_step_settings();
							<?php
						} elseif ( isset( $_REQUEST['auto_mail'] ) && 'false' === sanitize_text_field( wp_unslash( $_REQUEST['auto_mail'] ) ) ) { // WPCS: CSRF ok, WPCS: input var ok.
							?>
							jQuery("#ux_div_mail_console").css("display", "none");
							jQuery("#ux_div_test_mail").css("display", "block");
							mail_booster_third_step_settings();
							<?php
						}
						if ( '' !== $email_configuration_array['hostname'] ) {
							?>
							jQuery("#ux_txt_host").val("<?php echo esc_attr( $email_configuration_array['hostname'] ); ?>");
							<?php
						} else {
							?>
							mail_booster_get_host_port();
							<?php
						}
						?>
					});
					if (typeof (mail_booster_move_to_second_step) !== "function")
					{
						function mail_booster_move_to_second_step()
						{
							jQuery("#ux_div_first_step").addClass("first-step-helper");
							mail_booster_validate_settings();
						}
					}
					if (typeof (mail_booster_move_to_first_step) !== "function")
					{
						function mail_booster_move_to_first_step()
						{
							jQuery("#ux_div_first_step").removeClass("first-step-helper");
							jQuery("#test_email").removeClass("second-step-helper");
							jQuery("#ux_div_first_step").css("display", "block");
							jQuery("#test_email").css("display", "none");
							jQuery("#ux_div_second_step").css("display", "none");
							jQuery("#ux_div_step_progres_bar_width").css("width", "33%");
							jQuery("#ux_div_frm_wizard li:eq(1)").removeClass("active");
						}
					}
					if (typeof (mail_booster_save_changes) !== "function")
					{
						function mail_booster_save_changes()
						{
							overlay_loading_mail_booster(<?php echo wp_json_encode( $mail_booster_successfully_saved ); ?>);
							setTimeout(function ()
							{
								remove_overlay_mail_booster();
								window.location.href = "admin.php?page=mail_booster_email_configuration";
							}, 3000);
						}
					}
					if (typeof (mail_booster_move_to_third_step) !== "function")
					{
						function mail_booster_move_to_third_step()
						{
							mail_booster_validate_settings();
							jQuery("#ux_div_first_step").removeClass("first-step-helper");
							jQuery("#test_email").addClass("second-step-helper");
						}
					}
					if (typeof (mail_booster_select_port) !== "function")
					{
						function mail_booster_select_port()
						{
							var encryption = jQuery("#ux_ddl_encryption").val();
							switch (encryption)
							{
								case "none":
								case "tls":
								jQuery("#ux_txt_port").val(587);
								break;
								case "ssl":
								jQuery("#ux_txt_port").val(465);
								break;
							}
						}
					}
					var sidebar_load_interval = setInterval(load_sidebar_content_mail_booster, 1000);
					setTimeout(function ()
					{
						clearInterval(sidebar_load_interval);
					}, 5000);
					load_sidebar_content_mail_booster();
						<?php
					}
					break;
				case 'mail_booster_test_email':
					?>
					jQuery("#ux_mail_booster_li_test_email").addClass("active");
					jQuery(document).ready(function ()
					{
						if (window.CKEDITOR)
						{
							CKEDITOR.replace("ux_content");
						}
					});
					<?php
					break;
				case 'mail_booster_email_logs':
					?>
					jQuery("#ux_mail_booster_li_email_logs").addClass("active");
					<?php
					if ( '1' === EMAIL_LOGS_MAIL_BOOSTER ) {
						?>
						var jQuery_date_array = <?php echo isset( $array3 ) ? wp_json_encode( $array3 ) : 0; ?>;
						var jQuery_sent_array = <?php echo isset( $final_sent_data_array ) ? wp_json_encode( $final_sent_data_array ) : 0; ?>;
						var jQuery_not_sent_array = <?php echo isset( $final_not_sent_data_array ) ? wp_json_encode( $final_not_sent_data_array ) : 0; ?>;
						var mail_booster_charts = document.getElementById("ux_mail_booster_charts").getContext('2d');
						var mail_booster_chart = new Chart(mail_booster_charts, {
								type: 'line',
								data: {
										labels: jQuery_date_array,
										datasets: [{
												label: 'Sent',
												data: jQuery_sent_array,
												backgroundColor: [
														'rgba(12,169,74,0.2)'
												],
												borderColor: [
														'rgba(12,169,74,1)'
												],
												borderWidth: 2,
												fill: false,
										},{
											label: 'Not Sent',
											data: jQuery_not_sent_array,
											backgroundColor: [
													'rgb(227,15,28, 0.2)',
											],
											borderColor: [
													'rgb(227,15,28)',
											],
											borderWidth: 2,
											fill: false,
										}]
								},
								options: {
									responsive: true,
									title: {
										display: true,
										text: 'Legend'
									},
									tooltips: {
										displayColors: false,
										backgroundColor: [
												'rgb(227,15,28, 0.2)',
										],
										mode: 'index',
										intersect: false,
									},
									hover: {
										mode: 'nearest',
										intersect: true,
									},
									scales: {
											yAxes: [{
													ticks: {
															beginAtZero:true
													}
											}]
									}
								}
						});
						jQuery(document).ready(function ()
						{
							jQuery("#ux_txt_mail_booster_start_date").datepicker
							({
								dateFormat: 'mm/dd/yy',
								numberOfMonths: 1,
								changeMonth: true,
								changeYear: true,
								yearRange: "1970:2039",
								onSelect: function (selected)
								{
									jQuery("#ux_txt_mail_booster_end_date").datepicker("option", "minDate", selected)
								}
							});
							jQuery("#ux_txt_mail_booster_end_date").datepicker
							({
								dateFormat: 'mm/dd/yy',
								numberOfMonths: 1,
								changeMonth: true,
								changeYear: true,
								yearRange: "1970:2039",
								onSelect: function (selected)
								{
									jQuery("#ux_txt_mail_booster_start_date").datepicker("option", "maxDate", selected)
								}
							});
						});
						if (typeof (prevent_datepicker_mail_booster) !== "function")
						{
							function prevent_datepicker_mail_booster(id)
							{
								jQuery("#" + id).on("keypress", function (e)
								{
									e.preventDefault();
								});
							}
						}
						var oTable = jQuery("#ux_tbl_email_logs").dataTable
						({
							"pagingType": "full_numbers",
							"language":
							{
								"emptyTable": "No data available in table",
								"info": "Showing _START_ to _END_ of _TOTAL_ entries",
								"infoEmpty": "No entries found",
								"infoFiltered": "(filtered1 from _MAX_ total entries)",
								"lengthMenu": "Show _MENU_ entries",
								"search": "Search:",
								"zeroRecords": "No matching records found"
							},
							"bSort": true,
							"pageLength": 10,
							"aoColumnDefs": [{"bSortable": false, "aTargets": [0]}]
						});
					var sidebar_load_interval = setInterval(load_sidebar_content_mail_booster, 1000);
					setTimeout(function ()
					{
						clearInterval(sidebar_load_interval);
					}, 5000);
					jQuery("#ux_chk_all_email_logs").click(function ()
					{
						jQuery("input[type=checkbox]", oTable.fnGetFilteredNodes()).attr("checked", this.checked);
					});
					if (typeof (delete_email_logs) !== "function")
					{
						function delete_email_logs(id)
						{
							var confirm_delete = confirm(<?php echo wp_json_encode( $mail_booster_confirm_message ); ?>);
							if (confirm_delete === true)
							{
								overlay_loading_mail_booster(<?php echo wp_json_encode( $mail_booster_delete_log ); ?>);
								jQuery.post(ajaxurl,
								{
									id: id,
									param: "mail_booster_email_logs_delete_module",
									action: "mail_booster_action",
									_wp_nonce: "<?php echo esc_attr( $mail_booster_email_logs_delete_log ); ?>"
								},
								function ()
								{
									setTimeout(function ()
									{
										remove_overlay_mail_booster();
										window.location.href = "admin.php?page=mail_booster_email_logs";
									}, 3000);
								});
							}
						}
					}
					if (typeof (check_email_logs) !== "function")
					{
						function check_email_logs(id)
						{
							if (jQuery("input:checked", oTable.fnGetFilteredNodes()).length === jQuery("input[type=checkbox]", oTable.fnGetFilteredNodes()).length)
							{
								jQuery("#ux_chk_all_email_logs").attr("checked", "checked");
							} else
							{
								jQuery("#ux_chk_all_email_logs").removeAttr("checked");
							}
						}
					}
					var ux_frm_email_logs = jQuery("#ux_frm_email_logs").validate
					({
						submitHandler: function ()
						{
							premium_edition_notification_mail_booster();
						}
					});
					load_sidebar_content_mail_booster();
						<?php
					}
					break;
				case 'mail_booster_email_notification':
					?>
					jQuery("#ux_mail_booster_li_email_notification").addClass("active");
						<?php
						if ( '1' === EMAIL_NOTIFICATION_MAIL_BOOSTER ) {
							?>
							jQuery(document).ready(function ()
							{
								jQuery("#ux_ddl_plugin_update_available").val("<?php echo isset( $email_notification_data_array['plugin_update_available'] ) ? esc_attr( $email_notification_data_array['plugin_update_available'] ) : 'disable'; ?>");
								jQuery("#ux_ddl_email_plugin_updated").val("<?php echo isset( $email_notification_data_array['email_plugin_updated'] ) ? esc_attr( $email_notification_data_array['email_plugin_updated'] ) : 'disable'; ?>");
								jQuery("#ux_ddl_email_theme_update").val("<?php echo isset( $email_notification_data_array['email_theme_update'] ) ? esc_attr( $email_notification_data_array['email_theme_update'] ) : 'disable'; ?>");
								jQuery("#ux_ddl_email_wordpress_update").val("<?php echo isset( $email_notification_data_array['email_wordpress_update'] ) ? esc_attr( $email_notification_data_array['email_wordpress_update'] ) : 'disable'; ?>");
								jQuery("#ux_ddl_notifications_service").val('<?php echo isset( $email_notification_data_array['notification_service'] ) ? esc_attr( $email_notification_data_array['notification_service'] ) : 'email'; ?>');
								jQuery("#ux_ddl_notifications").val('<?php echo isset( $email_notification_data_array['notification'] ) ? esc_attr( $email_notification_data_array['notification'] ) : 'disable'; ?>');
								show_hide_delete_after_logs('#ux_ddl_notifications','#ux_div_notification_services');
								show_hide_notifications_service('#ux_ddl_notifications_service', '#ux_div_notification_email_address' ,'#ux_div_notifications_pushover_key', '#ux_div_slack_web_hook');
							});
							jQuery("#ux_frm_email_notification").validate
							({
								submitHandler: function ()
								{
									overlay_loading_mail_booster(<?php echo wp_json_encode( $mail_booster_successfully_saved ); ?>);
									jQuery.post(ajaxurl,
									{
										data: base64_encode(jQuery("#ux_frm_email_notification").serialize()),
										action: "mail_booster_action",
										param: "mail_booster_email_notification_module",
										_wp_nonce: "<?php echo esc_attr( $mail_booster_email_notification_nonce ); ?>"
									},
									function ()
									{
										setTimeout(function ()
										{
											remove_overlay_mail_booster();
											window.location.href = "admin.php?page=mail_booster_email_notification";
										}, 3000);
									});
								}
							});
						load_sidebar_content_mail_booster();
							<?php
						}
					break;
				case 'mail_booster_settings':
					?>
					jQuery("#ux_mail_booster_li_settings").addClass("active");
					<?php
					if ( '1' === SETTINGS_MAIL_BOOSTER ) {
						?>
						jQuery(document).ready(function ()
						{
							jQuery("#ux_ddl_automatic_plugin_updates").val("<?php echo isset( $settings_data_array['automatic_plugin_update'] ) ? esc_attr( $settings_data_array['automatic_plugin_update'] ) : 'disable'; ?>");
							jQuery("#ux_ddl_debug_mode").val("<?php echo isset( $settings_data_array['debug_mode'] ) ? esc_attr( $settings_data_array['debug_mode'] ) : 'enable'; ?>");
							jQuery("#ux_ddl_remove_tables").val("<?php echo isset( $settings_data_array['remove_tables_at_uninstall'] ) ? esc_attr( $settings_data_array['remove_tables_at_uninstall'] ) : 'disable'; ?>");
							jQuery("#ux_ddl_monitor_email_logs").val("<?php echo isset( $settings_data_array['monitor_email_logs'] ) ? esc_attr( $settings_data_array['monitor_email_logs'] ) : 'enable'; ?>");
							jQuery("#ux_ddl_fetch_settings").val("<?php echo isset( $settings_data_array['fetch_settings'] ) ? esc_attr( $settings_data_array['fetch_settings'] ) : 'individual_site'; ?>");
							jQuery("#ux_ddl_delete_logs_after").val("<?php echo isset( $settings_data_array['delete_logs_after'] ) ? esc_attr( $settings_data_array['delete_logs_after'] ) : '1day'; ?>");
							jQuery("#ux_ddl_auto_clear_logs").val("<?php echo isset( $settings_data_array['auto_clear_logs'] ) ? esc_attr( $settings_data_array['auto_clear_logs'] ) : 'disable'; ?>");
							show_hide_delete_after_logs('#ux_ddl_auto_clear_logs','#ux_div_delete_logs_after');
						});
						jQuery("#ux_frm_settings").validate
						({
							submitHandler: function ()
							{
								overlay_loading_mail_booster(<?php echo wp_json_encode( $mail_booster_successfully_saved ); ?>);
								jQuery.post(ajaxurl,
								{
									data: base64_encode(jQuery("#ux_frm_settings").serialize()),
									action: "mail_booster_action",
									param: "mail_booster_settings_module",
									_wp_nonce: "<?php echo esc_attr( $mail_booster_settings_nonce ); ?>"
								},
								function ()
								{
									setTimeout(function ()
									{
										remove_overlay_mail_booster();
										window.location.href = "admin.php?page=mail_booster_settings";
									}, 3000);
								});
							}
						});
						load_sidebar_content_mail_booster();
						<?php
					}
					break;
				case 'mail_booster_roles_and_capabilities':
					?>
					jQuery("#ux_mail_booster_li_roles_and_capabilities").addClass("active");
					var sidebar_load_interval = setInterval(load_sidebar_content_mail_booster, 1000);
					setTimeout(function ()
					{
						clearInterval(sidebar_load_interval);
					}, 5000);
					<?php
					if ( '1' === ROLES_AND_CAPABILITIES_MAIL_BOOSTER ) {
						?>
						if (typeof (full_control_function_mail_booster) !== "function")
						{
							function full_control_function_mail_booster(id, div_id)
							{
								var checkbox_id = jQuery(id).prop("checked");
								jQuery("#" + div_id + " input[type=checkbox]").each(function ()
								{
									if (checkbox_id)
									{
										jQuery(this).attr("checked", "checked");
										if (jQuery(id).attr("id") !== jQuery(this).attr("id"))
										{
											jQuery(this).attr("disabled", "disabled");
										}
									} else
									{
										if (jQuery(id).attr("id") !== jQuery(this).attr("id"))
										{
											jQuery(this).removeAttr("disabled");
											jQuery("#ux_chk_other_capabilities_manage_options").attr("disabled", "disabled");
											jQuery("#ux_chk_other_capabilities_read").attr("checked", "checked").attr("disabled", "disabled");
										}
									}
								});
							}
						}
						if (typeof (show_roles_capabilities_mail_booster) !== "function")
						{
							function show_roles_capabilities_mail_booster(id, div_id)
							{
								if (jQuery(id).prop("checked"))
								{
									jQuery("#" + div_id).css("display", "block");
								} else
								{
									jQuery("#" + div_id).css("display", "none");
								}
							}
						}
						jQuery(document).ready(function ()
						{
							jQuery("#ux_ddl_mail_booster_menu").val("<?php echo isset( $details_roles_capabilities['show_mail_booster_top_bar_menu'] ) ? esc_attr( $details_roles_capabilities['show_mail_booster_top_bar_menu'] ) : 'enable'; ?>");
							show_roles_capabilities_mail_booster("#ux_chk_author", "ux_div_author_roles");
							full_control_function_mail_booster("#ux_chk_full_control_author", "ux_div_author_roles");
							show_roles_capabilities_mail_booster("#ux_chk_editor", "ux_div_editor_roles");
							full_control_function_mail_booster("#ux_chk_full_control_editor", "ux_div_editor_roles");
							show_roles_capabilities_mail_booster("#ux_chk_contributor", "ux_div_contributor_roles");
							full_control_function_mail_booster("#ux_chk_full_control_contributor", "ux_div_contributor_roles");
							show_roles_capabilities_mail_booster("#ux_chk_subscriber", "ux_div_subscriber_roles");
							full_control_function_mail_booster("#ux_chk_full_control_subscriber", "ux_div_subscriber_roles");
							show_roles_capabilities_mail_booster("#ux_chk_others_privileges", "ux_div_other_privileges_roles");
							full_control_function_mail_booster("#ux_chk_full_control_other_privileges_roles", "ux_div_other_privileges_roles");
							full_control_function_mail_booster("#ux_chk_full_control_other_roles", "ux_div_other_roles");
						});
						jQuery("#ux_frm_roles_and_capabilities").validate
						({
							submitHandler: function ()
							{
								var roles_names = [];
								jQuery("#ux_tbl_other_roles input[type=checkbox][id*=ux_chk_other_capabilities_]").each(function ()
								{
									if (jQuery(this).attr("checked"))
									{
										roles_names.push(jQuery(this).val());
									}
								});
								overlay_loading_mail_booster(<?php echo wp_json_encode( $mail_booster_successfully_saved ); ?>);
								jQuery.post(ajaxurl,
								{
									roles_names: JSON.stringify(roles_names),
									data: base64_encode(jQuery("#ux_frm_roles_and_capabilities").serialize()),
									param: "mail_booster_roles_and_capabilities_module",
									action: "mail_booster_action",
									_wp_nonce: "<?php echo esc_attr( $mail_booster_roles_capabilities ); ?>"
								},
								function ()
								{
									setTimeout(function ()
									{
										remove_overlay_mail_booster();
										window.location.href = "admin.php?page=mail_booster_roles_and_capabilities";
									}, 3000);
								});
							}
						});
						load_sidebar_content_mail_booster();
						<?php
					}
					break;
				case 'mail_booster_system_information':
					?>
					jQuery("#ux_mail_booster_li_system_information").addClass("active");
					var sidebar_load_interval = setInterval(load_sidebar_content_mail_booster, 1000);
					setTimeout(function ()
					{
						clearInterval(sidebar_load_interval);
					}, 5000);
					<?php
					if ( '1' === SYSTEM_INFORMATION_MAIL_BOOSTER ) {
						?>
						jQuery.getSystemReport = function (strDefault, stringCount, string, location)
						{
							var o = strDefault.toString();
							if (!string)
							{
								string = "0";
							}
							while (o.length < stringCount)
							{
								if (location === "undefined")
								{
									o = string + o;
								} else
								{
									o = o + string;
								}
							}
							return o;
						};
						jQuery(".system-report").click(function ()
						{
							var report = "";
							jQuery(".custom-form-body").each(function ()
							{
								jQuery("h3.form-section", jQuery(this)).each(function ()
								{
									report = report + "\n### " + jQuery.trim(jQuery(this).text()) + " ###\n\n";
								});
								jQuery("tbody > tr", jQuery(this)).each(function ()
								{
									var the_name = jQuery.getSystemReport(jQuery.trim(jQuery(this).find("strong").text()), 25, " ");
									var the_value = jQuery.trim(jQuery(this).find("span").text());
									var value_array = the_value.split(", ");
									if (value_array.length > 1)
									{
										var temp_line = "";
										jQuery.each(value_array, function (key, line)
										{
											var tab = (key === 0) ? 0 : 25;
											temp_line = temp_line + jQuery.getSystemReport("", tab, " ", "f") + line + "\n";
										});
										the_value = temp_line;
									}
									report = report + "" + the_name + the_value + "\n";
								});
							});
							try
							{
								jQuery("#ux_system_information").slideDown();
								jQuery("#ux_system_information textarea").val(report).focus().select();
								return false;
							} catch (e)
							{
							}
							return false;
						});
						jQuery("#ux_btn_system_information").click(function ()
						{
							if (jQuery("#ux_btn_system_information").text() === "Close System Information!")
							{
								jQuery("#ux_system_information").slideUp();
								jQuery("#ux_btn_system_information").html("Get System Information!");
							} else
							{
								jQuery("#ux_btn_system_information").html("Close System Information!");
								jQuery("#ux_btn_system_information").removeClass("system-information");
								jQuery("#ux_btn_system_information").addClass("close-information");
							}
						});
						load_sidebar_content_mail_booster();
						<?php
					}
					break;
			}
		}
		?>
		</script>
		<?php
	}
}
