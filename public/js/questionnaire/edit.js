/**
 * Saves the state of the given element. Must implement an action for
 * any element that can be saved.
 * 
 * @param $elm The element for which to save the current state
 */
function saveState($elm)
{
	var $elmId = $elm[0].id;
	
	switch ($elmId) {
	
		case 'questionnaire-questions-list':
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
			break;
			
		case 'repository-questions':
		case 'less':
		case 'more':
		case 'subquestions':
			$('body').addClass('loading');
			
			var $action = $elm.closest('form').attr('action');
			var $qqId = $action.match(/\/id\/(\d{1,})/)[1].toString();
			
			var $list = $('#subquestions');
			var $data = $list.sortable('serialize') + '&cols=' + $('#cols').val() + '&parent=' + $qqId;
			$.post(baseUrl + '/questionnaire-question/save-state', $data, function() {
				$('body').removeClass('loading');
			});
			break;
			
		default:
			var $message = 'No action implemented for element with id #' + $elmId;
			console.log($message);
			break;
	}
}

function makeTabsSortable()
{
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
			saveState($(this));
		}
	}).disableSelection();
}

function makeTabsDroppable($tabs)
{
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

function updateAnswersTab()
{
	if ($('#answers-useAnswerPossibilityGroup').is(':checked')) {
		$('#answers-answerPossibilityGroup_id').removeAttr('disabled');
		$('#answers-collectionPresentationType').removeAttr('disabled');
	} else {
		$('#answers-answerPossibilityGroup_id').attr('disabled', 'disabled');
		$('#answers-collectionPresentationType').attr('disabled', 'disabled');
	}
}

function updateValidationTab()
{
	if ($('#answers-useAnswerPossibilityGroup').is(':checked')) {
		$('#dialog #validation input[type="checkbox"]').attr('disabled', 'disabled');
	} else {
		$('#dialog #validation input[type="checkbox"]').removeAttr('disabled');
	}
}

function updateColWidth(action)
{
	/* get and update current number of columns */
	$cols = $('#cols').val();
	if ($cols == 'NaN') $cols = 1;
	if (action == 'less' && $cols > 1) {
		$cols--;
	} else if (action == 'more' && $cols < $('#dialog #group ul#subquestions li').length) {
		$cols++;
	}
	$('#cols').val($cols);
	
	/* calculate and set new column width */
	$containerWidth = parseInt($('#dialog').width()) - 65;
	$newWidth = parseInt($containerWidth / $cols) - (5 * $cols);
	$.each($('#dialog #group ul#subquestions li'), function($i, $elm) {
		$($elm).width($newWidth);
	});
}

/**
 * Initialises the tab for editing sub-questions
 */
function initTabSubquestions()
{
	var $list = $('#dialog #group ul#subquestions');

	updateColWidth();
	
	$('#dialog #group #less').click(function() {
		updateColWidth('less');
		saveState($list);
		return false;
	});
	
	$('#dialog #group #more').click(function() {
		updateColWidth('more');
		saveState($list);
		return false;
	});
	
	$('#dialog #group ul#subquestions').sortable({
		update: function(event, ui) {
			saveState($list);
		}
	});
	
	$('#dialog #group ul#subquestions li a.delete').click(function() {
		$(this).closest('li').remove();
		saveState($list);
		return false;
	});
}

/**
 * Initialises the tab for editing answer-possibilities
 */
function initTabAnswerPossibilities()
{
	updateAnswersTab();
	$('#answers-useAnswerPossibilityGroup').change(function() {
		updateAnswersTab();
	});
}

/**
 * Initialises the tab for validation settings
 */
function initTabValidation()
{
	updateValidationTab();
	$('#answers-useAnswerPossibilityGroup').change(function() {
		updateValidationTab();		
	});
}


function postOpenDialog(response) {
	
	/* tabs */
	$('.tabs').tabs();
	
	/* init tab sub-questions */
	if ($('#dialog #group').length > 0) {
		initTabSubquestions();
	}
	
	/* init tab answer-possibilities */
	if ($('#dialog #answer').length > 0) {
		initTabAnswerPossibilities();
		initTabValidation();
	}
	
	/* add questionnaire id to the form */
	var $questionnaireId = window.location.href.match(/id\/(\d{1,})/)[1];
	$('<input type="hidden" id="questionnaire_id" name="questionnaire_id" value="' + $questionnaireId + '" />').appendTo('#dialog form');
	
	/* initialize filter */
	initFilter();	
	
	
//	$.each($('#dialog input[type="text"]'), function($key, $val) {
//		var $elm = $($val);
//		var $name = $elm.attr('name');
//		$elm.autocomplete({
//			source: baseUrl + '/question/autocomplete/element/' + $name,
//			select: function(event, ui) {
//			
//				/* replace the value by the label */
//				$(this).val(ui.item.label);
//				
//				/* add question id to the form */
//				if ($('#dialog form input#question_id').length == 0) {
//					$('<input type="hidden" id="question_id" name="question_id" value="' + ui.item.value + '" />').appendTo('#dialog form');
//				} else {
//					$('#dialog form input#question_id').val(ui.item.value);
//				}
//				
//				return false;
//			}
//		});
//	});
	
	/* hide repository questions list */
	$('#repository-questions').hide();
	
	/* add action to 'add subquestion' link */
	$('#group a.add').toggle(function() {
		$('#repository-questions').show();
		return false;
	}, function() {
		$('#repository-questions').hide();
		return false;
	});
	
	/* make repository questions list selectable */
	$('#repository-questions').selectable({		
		/* add event to selection of a subquestion */
		selected: function(event, ui) {		
			var $list = $('#subquestions');			
			/* make sortable */
			$list.sortable();
			/* append selected subquestion to list */
			$(ui.selected)
				.removeClass('ui-widget-content')
				.removeClass('ui-selectee')
				.removeClass('ui-selected')
				.addClass('ui-state-default')
				.prepend('<a class="icon delete" href="#"></a>')
				.appendTo($list);
			if ($list.find('li').length == 1) {
				$(ui.selected).css({width: '100%'});
			} else {
				$(ui.selected).css({width: $list.find('li:first').width() + 'px'});
			}
			/* save current state of the list */
			saveState($(this));
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
	
	/* add one page if no pages are present */
	if ($tabItems.length == 0) {
		$('.add_page').click();
	}
	
	/* hide form for editing title if no errors */
	if ($('form#HVA_Form_Questionnaire_Edit ul.errors').length == 0) {
		$('form#HVA_Form_Questionnaire_Edit').hide();
	}
	
	/* show form for editing title when edit-buttons is clicked */
	$('a#edit_title').toggle(
		function() {
			$('form#HVA_Form_Questionnaire_Edit').show('slow');
			return false;
		}, function() {
			$('form#HVA_Form_Questionnaire_Edit').hide('slow');
			return false;
		}
	);
	
	/* hide admin options by default; show on hover */
	$('#questionnaire-questions-list li .admin .options').hide();
	$('#questionnaire-questions-list li').hover(function() {
		$(this).addClass('ui-state-highlight')
			.find('.admin .options').show();
	}, function() {
		$(this).removeClass('ui-state-highlight')
			.find('.admin .options').hide();
	});
});
