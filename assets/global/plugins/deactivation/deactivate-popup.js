jQuery(document).ready(function($) {
	$( '#the-list #mail-booster-plugin-disable-link' ).click(function(e) {
		e.preventDefault();

		var reason = $( '#mail-booster-feedback-content .mail-booster-reason' ),
			deactivateLink = $( this ).attr( 'href' );

	    $( "#mail-booster-feedback-content" ).dialog({
	    	title: 'Feedback Form',
	    	dialogClass: 'mail-booster-feedback-form',
	      	resizable: false,
	      	minWidth: 430,
	      	minHeight: 300,
	      	modal: true,
	      	buttons: {
	      		'go' : {
		        	text: 'Submit',
        			icons: { primary: "dashicons dashicons-update" },
		        	id: 'mail-booster-feedback-dialog-continue',
					class: 'button deactivation-skip-popup-button',
		        	click: function() {
		        		var dialog = $(this),
		        			go = $('#mail-booster-feedback-dialog-continue'),
		          			form = dialog.find('form').serializeArray(),
							result = {};
							$.each( form, function() {
								if ( '' !== this.value ){
									result[ this.name ] = this.value;
									jQuery('input[name='+ this.name+ ']').css('border-color','#ddd');
									jQuery('textarea[name='+ this.name+ ']').css('border-color','#ddd');
								} else {
										if( jQuery('#ux_rdl_reason_mail_booster').attr('checked') == 'checked' ){
											jQuery('input[name='+ this.name+ ']').css('border-color','#D43F3F');
											jQuery('textarea[name='+ this.name+ ']').css('border-color','#D43F3F');
											result = {};
										}
									}
							});
							jQuery("#ux_frm_deactivation_popup").validate({
								rules:{
									ux_txt_email_address_mail_booster:
									{
										email: true
									}
								}
							});
							if( jQuery('#ux_rdl_reason_mail_booster').attr('checked') == 'checked' ){
								if ( jQuery("#ux_txt_email_address_mail_booster").hasClass('error') ) {
									jQuery('input[name=ux_txt_email_address_mail_booster]').css('border-color','#D43F3F');
									result = {};
								}
							}
							if ( ! jQuery.isEmptyObject( result ) ) {
								result.action = 'post_user_feedback_mail_booster';
									$.ajax({
											url: post_feedback.admin_ajax,
											type: 'POST',
											data: result,
											error: function(){},
											success: function(msg){},
											beforeSend: function() {
												go.addClass('mail-booster-ajax-progress');
											},
											complete: function() {
												go.removeClass('mail-booster-ajax-progress');
													dialog.dialog( "close" );
													location.href = deactivateLink;
											}
									});
							}
		        	},
	      		},
	      		'cancel' : {
		        	text: 'Cancel',
		        	id: 'mail-booster-feedback-cancel',
		        	class: 'button deactivation-cancel-popup-button',
		        	click: function() {
		          		$( this ).dialog( "close" );
		        	}
	      		},
	      		'skip' : {
		        	text: 'Skip',
		        	id: 'mail-booster-feedback-dialog-skip',
							class: 'button deactivation-skip-popup-button',
		        	click: function() {
		          		$( this ).dialog( "close" );
		          		location.href = deactivateLink;
		        	}
	      		},
	      	}
	    });
			reason.change(function() {
				$( '.mail-booster-submit-feedback' ).hide();
				if ( $( this ).hasClass( 'mail-booster-support' ) ) {
					$( this ).find( '.mail-booster-submit-feedback' ).show();
				}
			});
	});
	$('.ui-dialog-titlebar').append('<span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>');
});
