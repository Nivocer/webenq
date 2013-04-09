$(document).ajaxComplete(function() {
	$('.tabs').tabs();
});

$(function() {
	/**
	 * Forms with error lists are always shown
	 */
	$('form ul.errors').show();

	/**
	 * Every <a class="toggleform"> will toggle the form right after it 
	 */
	$('a.toggleform').toggle(function() {
		$(this).next('form').show('slow');
		return false;
	}, function() {
		$(this).next('form').hide('slow');
		return false;
	});

	/**
	 * Every <a class="ajax"/> will open in a dialog box
	 */
	$('a.ajax').live('click', function() {
		$('body').addClass('loading');
		$dialog = resetDialog();
		$href = $(this).attr('href');
		$title = $(this).attr('title');
		$.get($href, function(response) {
			$('body').removeClass('loading');
			preOpenDialog();
			$dialog.html(response);
			$dialog.dialog({
				title: $title,
				modal: true,
				width: .67 * $(window).width(),
				height: .67 * $(window).height()
			});
			postOpenDialog();			
		});
		return false;
	});
	
	/**
	 * When a form is submitted with ajax, the button pressed in not posted.
	 * Therefore the pressed button's value is stored in a hidden field.
	 */
	$('div#dialog form input[type="submit"]').live('click', function() {
		$name = $(this).attr('name');
		$value = $(this).val();
		$form = $(this).closest('form');
		$hiddenElm = $('<input type="hidden" name="' + $name + '" value="' + $value + '" />');
		$hiddenElm.appendTo($form);
		$form.submit();
		return false;
	});
	
	/**
	 * Every dialog-form will be posted with ajax. The expected return
	 * value is either a json-object, or html (for re-displaying the form).
	 */
	$('div#dialog form').live('submit', function() {
		$('body').addClass('loading');
		$dialog = $('div#dialog');
		$action = $(this).attr('action');
		$.post($action, $(this).serialize(), function(response) {
			if (typeof(response) == 'object') {
				$dialog.dialog('close');
				if (response.reload == true) {
					if (response.href) {
						window.location.href = response.href;
					}
					window.location.reload();
				} else {
					$('body').removeClass('loading');
				}
			} else {
				preOpenDialog(response);
				$dialog.html(response);
				$('body').removeClass('loading');
				postOpenDialog(response);
			}
		});
		return false;
	});
	
	
	$('.dateformat').mask('99-99-9999');
	
	$('.sortable').sortable({
		placeholder: 'ui-state-highlight',
		update: function(event, ui) {
			saveState(event, ui);
		}
	})
	
	$('.draggable').draggable({
		placeholder: 'ui-state-highlight',
		update: function(event, ui) {
			saveState(event, ui);
		}
	}).disableSelection();
	
	$('.droppable').droppable({
		placeholder: 'ui-state-highlight',
		update: function(event, ui) {
			saveState(event, ui);
		}
	}).disableSelection();
	
	$('.tabs').tabs();
	
	$('.hoverable').hover(function() {
		$(this).addClass('hover').addClass('ui-state-highlight');
	}, function() {
		$(this).removeClass('hover').removeClass('ui-state-highlight');
	});
});

/**
 * Is used for saving the state of a sortable/draggable overview
 * May be overridden for custom logic.
 */
function saveState(event, ui) {
	return false;
}

function resetDialog()
{
	if ($('#dialog').length === 0) {
		return $('<div id="dialog"></div>').appendTo('body');
	} else {
		return $('#dialog').dialog('destroy');
	}
}

/**
 * Is executed before opening a jQuery-dialog.
 * May be overridden for custom logic.
 */
function preOpenDialog() {
}

/**
 * Is executed after opening a jQuery-dialog.
 * May be overridden for custom logic.
 */
function postOpenDialog() {
}

/**
 * Initializes the filter function for a list
 */
function initFilter()
{
	$('#filter').keyup(function() {
		clearTimeout(window.timeoutId);
		window.timeoutId = setTimeout(applyFilter, 200);
	});
	
	$('.selectable.filterable').selectable({
		selected: function(event, ui) {
			$('#dialog form #id').val($(ui.selected).attr('id'));
			$('#dialog form').submit();
			return false;
		}
	});
}

/**
 * Applies the current filter to the list
 */
function applyFilter()
{
	var $filter = $('#filter');
	var $search = $filter.val();
	var $list = $('ul.filterable').first();
	var $items = $list.find('li');
	$.each($items, function($key, $val) {
		$elm = $($val);
		$text = $elm.text();
		if ($text.match(new RegExp($search, 'i'))) {
			$elm.show();
		} else {
			$elm.hide();
		}
	});
}