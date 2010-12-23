$(function() {
	
	$('.sortable').sortable({
		accept: '.draggable',
		update: function(event, ui) {
//			ui.draggable.appendTo($(this));
		}
	}).disableSelection();
	
	$('.draggable').draggable({
		connectToSortable: '.sortable',
		handle: '.handle',
		revert: 'invalid',
		start: function(event, ui) {
//			$(ui.helper).css({
//				width: '100px',
//				height: '100px',
//				overflow: 'hidden'
//			});
//		},
//		stop: function(event, ui) {
//			$(ui.helper).css({
//				width: '33%',
//				height: 'auto',
//				float: 'left',
//				left: 'auto',
//				top: 'auto'
//			});
		}
	}).disableSelection();
});