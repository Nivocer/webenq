$(document).ready(function() {
	$('.sortable').sortable({
		update: function(event, ui) {
			$('body').addClass('loading');
			var url = window.location.href.replace(/\/view\//, '/save-state/');
			$.post(url, $(this).sortable('serialize'));
			$('body').removeClass('loading');
		}
	});
});