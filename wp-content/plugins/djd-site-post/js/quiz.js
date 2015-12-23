jQuery(document).ready(function($) {
	$('.error').hide();
	$('#djd_post_form').on('submit', function() {

		if ($("input#djd_quiz").val() === $("input#djd_quiz_hidden").val()) {
			return true;
		} else {
			$("label#quiz_error").show();  
			$("input#djd_quiz").focus();
			return false;
		}
	});
});
