/**
 * Saves the state of the given element. Must implement an action for
 * any element that can be saved.
 * 
 * @param event
 * @param reload Boolean that indicates reloading of the whole page
 */
function saveState(event, reload)
{
	//if ($(event.target).hasClass('questions-list')  {
		$('body').addClass('loading');
		$('.tabs ul:first li').addClass('ui-state-default');
		
		var $data = new Array();
		var $forms = $('ul').find('.questions-list');// ul with questions
		//determin sort order of pages and travers through them
		var $pageOrder= $('body').find('.page-list').sortable('toArray');
		$.each($pageOrder, function($index, $tabId){
			//create pageId form tabId
			var $page=$tabId.replace("tabId-","");
			var $pageId="pageId-"+$page;
			var $questionsList=$("#"+$pageId).find('.questions-list');
			$data[$index] = [$pageId, $questionsList.sortable('toArray')];
		});
		
		

		var $id=getUrlParam("id");
		$.post(baseUrl + '/questionnaire/order/id/'+$id, {data: $.toJSON($data)}, function() {
			if (reload === true) {
				window.location.reload();
			} else {
				$('body').removeClass('loading');
			}
		});
	//}
}

function makeTabsSortable()
{
	$( ".questions > .connectedSortable").sortable({
		tolerance: "pointer",	
		opacity: 0.7,
		placeholder: 'ui-state-highlight',
		update: function(event, ui) {
			saveState(event, ui);
		}
	})
}
function makeQuestionsSortable()
{
	$('ul.page-list').sortable({
		start: function(event, ui) {
			$(ui.helper).css({
				overflow: 'hidden'
			});
		},
		update: function(event, ui) {
			saveState(event, ui);
		}
	}).disableSelection();
}

function makeTabsDroppable()
{
	var $tabs = $("#tabs").tabs();
	var $tab_items = $( "ul.page-list li", $tabs ).droppable({
		tolerance: "pointer",
		accept: ".connectedSortable li",
		hoverClass: "ui-state-hover",
		drop: function( event, ui ) {
			var $item = $( this ); //li tab
			var $list = $( $item.find( "a" ).attr( "href" ) )
				.find( ".connectedSortable" );
			var $draggable = ui.draggable.clone();
			ui.draggable.remove();
			$tabs.tabs( "option", "active", $tab_items.index( $item ) );
			$list.prepend($('<li id="' + $draggable.attr('id') + '">' + $draggable.html() + '</li>'));
			}
		});
}
function showDeletePage() {
	// only show delete-page-link when no questions
	$.each($('a.delete-page'), function() {
		var page = $(this).closest('.ui-tabs-panel');
		var questions = page.find('ul.questions-list li');
		if (questions.length === 0) {
			$(this).show();
		}
	});
}
$(document).ready(function() {
	
	makeTabsDroppable();
	makeTabsSortable();
	makeQuestionsSortable();
	showDeletePage();
});