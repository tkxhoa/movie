function ProcessFormAjax() {

	var errorNotice = jQuery('#error'),
		successNotice = jQuery('#success')
		refresher = jQuery('#refresher')
		submit = jQuery('#submit');
	
	var theLanguage = jQuery('html').attr('lang');
	if (theLanguage == 'de-DE') {
		var btnMsg = 'Aktualisieren';
	} else {
		var btnMsg = 'Update';
	}
	

	jQuery("label#quiz_error").hide();
	if (jQuery("input#djd_quiz").val() !== jQuery("input#djd_quiz_hidden").val()) {
		jQuery("label#quiz_error").show();
		jQuery("input#djd_quiz").focus();
		return false;
	}

	var ed = tinyMCE.get('djdsitepostcontent');

	ed.setProgressState(1);
	tinyMCE.get('djdsitepostcontent').save();

	var newPostForm = jQuery(this).serialize();

	
	jQuery('#loading').show;
	jQuery.ajax({
		type:"POST",
		url: jQuery(this).attr('action'),
		data: newPostForm,
		success:function(response){
			ed.setProgressState(0);
			jQuery('#loading').hide;
            if(response == "success") {
				successNotice.show();
				refresher.show();
				submit.html(btnMsg);
			} else {
				errorNotice.show();
			}
		}
	});

	return false;
}
