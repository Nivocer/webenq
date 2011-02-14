$(function() {
	
	$('.answers').hide();

	$('.toggle_answers').toggle(function() {
		$(this).parent().find('.answers').show();
		return false;
	}, function() {
		$(this).parent().find('.answers').hide();
		return false;
	});
});
