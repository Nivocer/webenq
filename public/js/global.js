$(function() {
	
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
				width: '50%'
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
					window.location.reload();
				} else {
					$('body').removeClass('loading');
				}
			} else {
				preOpenDialog();
				$dialog.html(response);
				$('body').removeClass('loading');
				postOpenDialog();
			}
		});
		return false;
	});
	
	
	$('.dateformat').mask('99-99-9999');
	
	$('ul.sortable').sortable({
		placeholder: 'ui-state-highlight',
		update: function(event, ui) {
			saveState();
		}
	});
	$('ul.sortable').disableSelection();
});

/**
 * Is used for saving the state of a sortable/draggable overview
 * May be overridden for custom logic.
 */
function saveState() {
	return false;
}

function resetDialog() {
	/* create dialog */
	if ($('#dialog').length == 0) {
		$('<div id="dialog"></div>').appendTo('body');
	}
	/* or empty dialog */
	else {
		$('#dialog').html('');
	}
	
	return $('#dialog');
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