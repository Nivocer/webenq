/**
 * Saves the state of the given element. Must implement an action for
 * any element that can be saved.
 * 
 * @param event
 * @param reload Boolean that indicates reloading of the whole page
 */
function saveState(event, reload)
{
	if ($(event.target).hasClass('questions-list') ||
		$(event.target).hasClass('delete-page') ||
		$(event.target).attr('name') == 'to-page')
	{
		$('body').addClass('loading');
		$('.tabs ul:first li').addClass('ui-state-default');
		
		var $pages = $('.ui-tabs .ui-tabs-panel');
		var $data = new Array();
		
		$pages.each(function($key, $page) {
			var $questionsList = $($page).find('.questions-list');
			$data[$key] = $questionsList.sortable('toArray');
		});
		$.post(baseUrl + '/questionnaire/order', {data: $.toJSON($data)}, function() {
			if (reload === true) {
				window.location.reload();
			} else {
				$('body').removeClass('loading');
			}
		});
	}
	
	else if ($(event.target).hasClass('sub-questions')) {
		$('body').addClass('loading');
		
		var $subQuestions = $(event.target);
		var $question = $subQuestions.closest('.question');
		var $data = $subQuestions.sortable('toArray');
		$.post(baseUrl + '/questionnaire/order', {question: $.toJSON($data)}, function() {
			$('body').removeClass('loading');
		});
	} 
	
	else if (event.target.id === 'repository-questions' ||
			event.target.id === 'less' ||
			event.target.id === 'more' ||
			event.target.id === 'subquestions')
	{
		console.log(event);
		return;
		
		$('body').addClass('loading');
		
		var $action = $elm.closest('form').attr('action');
		var $qqId = $action.match(/\/id\/(\d{1,})/)[1].toString();
		
		var $list = $('#subquestions');
		var $data = $list.sortable('serialize') + '&cols=' + $('#cols').val() + '&parent=' + $qqId;
		$.post(baseUrl + '/questionnaire-question/save-state', $data, function() {
			$('body').removeClass('loading');
		});
	}
}

function makeTabsSortable()
{
//	return $('ul.sortable').sortable({
//		handle: 'div.handle',
//		revert: 'invalid',
//		accept: 'li.question',
//		start: function(event, ui) {
//			$(ui.helper).css({
//				width: '16px',
//				height: '16px',
//				overflow: 'hidden'
//			});
//		},
//		update: function(event, ui) {
//			saveState(event, ui);
//		}
//	}).disableSelection();
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
			var $list = $item.find('ul');
			var $draggable = ui.draggable.clone();
			ui.draggable.remove();
			$list.append('<li id="' + $draggable.attr('id') + '" class="question">' + $draggable.html() + '</li>');
		}
	});
}

function makeQuestionsDroppable($tabs)
{
	return $('li.question').droppable({
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
		//$('#answers-collectionPresentationType').removeAttr('disabled');
	} else {
		$('#answers-answerPossibilityGroup_id').attr('disabled', 'disabled');
		//$('#answers-collectionPresentationType').attr('disabled', 'disabled');
	}
}

function updateValidationTab()
{
	if ($('#answers-useAnswerPossibilityGroup').is(':checked')) {
		$('#dialog #validation #filters-element input[type="checkbox"]').attr('disabled', 'disabled');
		$('#dialog #validation #validators-element input[type="checkbox"]').attr('disabled', 'disabled');
	} else {
		$('#dialog #validation #filters-element input[type="checkbox"]').removeAttr('disabled');
		$('#dialog #validation #validators-elementinput[type="checkbox"]').removeAttr('disabled');
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

function addPage()
{
	// get page tabs
	var $tabs = $('div.tabs');
	// calculate new page id
	var $newPageId = $tabs.tabs('length') + 1;
	// create new page by cloning
	var $newPage = $('#page-1').clone();
	
	// assign id to new page
	$newPage.attr('id', 'page-' + $newPageId);
	// remove cloned questions list
	$('ul.sortable li', $newPage).remove();
	// show delete-page-link
	$('a.delete-page', $newPage).show();
	// append page
	$newPage.appendTo($tabs);
	
	// add tabs functionality to new page
	var $elm = $tabs.tabs('add', '#page-' + $newPageId, 'pagina ' + $newPageId);
	// select the newly added page
	$tabs.tabs('select', $tabs.tabs('length') - 1);
	// append the new page to all questions' change-page-select-elements
	$('select[name="to-page"]').append('<option label="' + $newPageId + '" value="' + $newPageId + '">' + $newPageId + '</option');
	
	// make new page's question list sortable
	$('.questions-list', $newPage).sortable({
		placeholder: 'ui-state-highlight',
		update: function(event, ui) {
			saveState(event, ui);
		}
	}).disableSelection();
	
	// reset sortable and droppable
//	makeTabsSortable();
//	makeTabsDroppable($tabs);
	
	return false;
}

$(function() {
	
//	var $tabs = $('.tabs');
//	makeTabsSortable();
//	var $tabItems = makeTabsDroppable($tabs);

	// add event to add-page button
	$('.add_page').click(function() {
		addPage();
	});
	
	// event for moving question to other page
	$('select[name="to-page"]').change(function(event) {
		var question = $(this).closest('li.question');
		var originalPage = $(event.target).closest('.ui-tabs-panel');
		var newPage = $('div#page-' + $(this).val());
		// move question to selected page
		$('ul.questions-list', newPage).append(question);
		question.removeClass('hover').removeClass('ui-state-highlight');
		// check if original page is empty now
		if (originalPage.find('ul.questions-list li').length === 0) {
			originalPage.find('a.delete-page').show();
		}	
		// make sure new page cannot be deleted
		newPage.find('a.delete-page').hide();
		// save current state
		saveState(event, false);
		return false;
	});
	
	// only show delete-page-link when no questions
	$.each($('a.delete-page'), function() {
		var page = $(this).closest('.ui-tabs-panel');
		var questions = page.find('ul.questions-list li');
		if (questions.length === 0) {
			$(this).show();
		}
	});
	
	// delete page when clicked on delete-page-link
	$('a.delete-page').live('click', function(event) {
		var page = $(this).closest('.ui-tabs-panel');
		var questions = page.find('ul.questions-list li');
		if (questions.length === 0) {
			var pageId = page.attr('id');
			var pageIndex = parseInt(pageId.replace('page-', '')) - 1;
			$('div.tabs').tabs('remove', pageIndex);
			saveState(event, true);
		}
	});
});