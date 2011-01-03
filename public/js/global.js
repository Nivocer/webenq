$(function() {
	$('.dateformat').mask('99-99-9999');
	
	$('ul.sortable').sortable({
		placeholder: 'ui-state-highlight',
		update: function(event, ui) {
			saveState();
		}
	});
	$('ul.sortable').disableSelection();
});

function saveState()
{
	return false;
}