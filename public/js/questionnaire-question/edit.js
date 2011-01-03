function saveState() {
	$('body').addClass('loading');
	
	var $qqId = window.location.href.match(/\/id\/(\d{1,})/)[1].toString();
	var $list = $('ul.sortable');
	
	var $data = $list.sortable('serialize') + '&cols=' + $('#cols').val() + '&parent=' + $qqId;
	$.post(baseUrl + '/questionnaire-question/order', $data, function() {
		$('body').removeClass('loading');
	});
}

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

function initColWidth() {
	$containerWidth = parseInt($('ul.sortable').css('width'));
	$cols = $('#cols').val();
	$newWidth = $containerWidth / $cols - (5 * $cols);
	$.each($('ul.sortable li'), function($i, $elm) {
		$($elm).css('width', $newWidth);
	});
}

$(function() {
	
	initColWidth();
	
	$('form input[type=checkbox]').live('click', function() {
		submitForm($(this).closest('form'));
		return false;
	});
	
	$('#less').click(function() {
		$containerWidth = parseInt($('ul.sortable').css('width'));
		$itemWidth = parseInt($('ul.sortable li').css('width'));
		$cols = parseInt($containerWidth / $itemWidth);
		$newCols = $cols - 1;
		$('#cols').val($newCols);
		$newWidth = $containerWidth / ($newCols) - (5 * $newCols);
		$.each($('ul.sortable li'), function($i, $elm) {
			$($elm).css('width', $newWidth);
		});
		saveState();
		return false;
	});
	
	$('#more').click(function() {
		$containerWidth = parseInt($('ul.sortable').css('width'));
		$itemWidth = parseInt($('ul.sortable li').css('width'));
		$cols = parseInt($containerWidth / $itemWidth);
		$newCols = $cols + 1;
		$('#cols').val($newCols);
		$newWidth = $containerWidth / ($newCols) - (5 * $newCols);
		$.each($('ul.sortable li'), function($i, $elm) {
			$($elm).css('width', $newWidth);
		});
		saveState();
		return false;
	});
});
