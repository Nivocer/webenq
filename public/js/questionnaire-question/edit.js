$(function() {
	$('form input[type=checkbox]').live('click', function() {
		submitForm($(this).closest('form'));
		return false;
	});
});

function submitForm(form) {
	$.post(
		window.location.href,
		form.serialize(),
		function(response) {
			form.replaceWith(response);
		}
	);
	return false;
};	
