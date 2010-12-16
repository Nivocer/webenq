function saveState() {
	$('body').addClass('loading');
	$('.tabs ul:first li').addClass('ui-state-default');
	
	var $questionnaireId = window.location.href.match(/\/id\/(\d{1,})/)[1].toString();
	var $pages = $('ul.sortable');
	
	$pages.each(function($key, $val) {
		var $page = $($val);
		var $data = $page.sortable('serialize') + '&page=' + (parseInt($key) + 1);
		$.post(baseUrl + '/questionnaire/order', $data, function() {
			if ($key == ($pages.length - 1)) {
				$('body').removeClass('loading');
			}
		});
	});
}

function makeTabsSortable() {
	$('ul.sortable').sortable({
		handle: 'div.handle',
		revert: 'invalid',
		start: function(event, ui) {
			$(ui.helper).css({
				width: '16px',
				height: '16px',
				overflow: 'hidden'
			});
		},
		update: function(event, ui) {
			saveState();
		}
	}).disableSelection();
}

function makeTabsDroppable($tabs) {
	var $tabItems = $('ul:first li', $tabs);
	return $tabItems.droppable({
		activeClass: 'ui-state-default',
		hoverClass: 'ui-state-hover',
		accept: 'ul.sortable li',
		drop: function(event, ui) {
			var $item = $(this);
			var $list = $($item.find('a').attr('href')).find('ul.sortable');
			var $draggable = ui.draggable.clone();
			ui.draggable.remove();
			$list.prepend($('<li id="' + $draggable.attr('id') + '">' + $draggable.html() + '</li>'));
			$tabs.tabs('select', $tabItems.index($item));
		}
	});
}

$(function() {

	var $tabs = $('.tabs').tabs();
	makeTabsSortable();
	var $tabItems = makeTabsDroppable($tabs);

	$('.add_page').click(function() {
		var $tabs = $('div.tabs');
		var $newPageId = $tabs.tabs('length') + 1;
		var $newPage = $('#page-1').clone();
		
		$newPage.attr('id', 'page-' + $newPageId);
		$('ul.sortable li', $newPage).remove();
		$newPage.appendTo($tabs);
		
		$tabs.tabs('add', '#' + 'page-' + $newPageId, 'pagina ' + $newPageId);
		$tabs.tabs('select', $tabs.tabs('length') - 1);
		
		/* reset */
		makeTabsSortable();
		makeTabsDroppable($tabs);
		
		return false;
	});
});
