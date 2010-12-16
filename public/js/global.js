$(function() {
	$('.dateformat').mask('99-99-9999');
	
	$('ul.sortable').sortable({
		placeholder: 'ui-state-highlight'
	});
	$('ul.sortable').disableSelection();
});