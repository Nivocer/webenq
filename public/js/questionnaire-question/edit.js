function saveState() {
	$('body').addClass('loading');
	
	var $qqId = window.location.href.match(/\/id\/(\d{1,})/)[1].toString();
	var $list = $('ul.sortable');
	
	var $data = $list.sortable('serialize') + '&cols=' + $('#cols').val() + '&parent=' + $qqId;
	
	$.post(baseUrl + '/questionnaire-question/save-state', $data, function() {
		$('body').removeClass('loading');
	});
}

function initColWidth() {
	$containerWidth = parseInt($('ul.sortable').css('width'));
	$cols = $('#cols').val();
	if ($cols == 'NaN') {
		$cols = 1;
		$('#cols').val($cols);
	} 
	$newWidth = $containerWidth / $cols - (5 * $cols);
	$.each($('ul.sortable li'), function($i, $elm) {
		$($elm).css('width', $newWidth);
	});
}

function initOptionsTab(){
	//display boxwidth/boxheight if it element is input or textComplete
	var boxwidth = ['input', 'textComplete'];
	if (($.inArray($('#options-options-presentation').val(), boxwidth))==-1){
		$('#presentationWidth-label').hide();
		$('#presentationWidth-element').hide();
		$('#presentationHeight-label').hide();
		$('#presentationHeight-element').hide();
	}else{
		$('#presentationWidth-label').show();
		$('#presentationWidth-element').show();
		$('#presentationHeight-label').show();
		$('#presentationHeight-element').show();
	}
	//display number of answer input 
	var numberAnswer = ['checkbox', 'radio','pulldown', 'slider'];
	if (($.inArray($('#options-options-presentation').val(), numberAnswer))==-1){
		$('#numberOfAnswers-label').hide();
		$('#numberOfAnswers-element').hide();
	}else{
		$('#numberOfAnswers-label').show();
		$('#numberOfAnswers-element').show();
	}
}
// add empty row to ad an new answerchoice item
function addItemRow(){
	
	var tid = Math.floor( Math.random()*999999 );
	var regName= /^items\[(\w)\]\.*/;
	var regId= /^items-(\w)-\.*/;
	  $("table#answerItems tr:last").clone().find("input").each(function() {
	    $(this).attr({
	      'id': function(_, id) { 
	    	  	if (id){
	    	  		return id.replace(regId,'items-'+tid+'-'); 
	    	  	}
	    	  	},
	      'name': function(_, name) { return name.replace(regName, 'items['+tid+']'); },
	      'value': '',
          'checked': false
	    });
	  }).end().appendTo("table");


}

$(function() {
	initColWidth();
	initOptionsTab();
	addItemRow();
	addItemRow();

	
	/* hide answerBox width if not applicable */
	$('#options-options-presentation').change(function() {
		initOptionsTab();
	});

	// add empty row to ad an new answerchoice item
	$('#addItemRow').click(function() {
		addItemRow();
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
	
	$('ul.sortable li a.icon.delete').click(function() {
		$(this).closest('li').remove();
		saveState();
		return false;
	});
	
	$('.tabs').tabs();
});

function postOpenDialog() {
	$('.selectable').selectable({
		stop: function() {
			$('.ui-selected', this).appendTo('.sortable')
				.removeClass('ui-widget-content')
				.removeClass('ui-selectee')
				.removeClass('ui-selected')
				.addClass('ui-state-default')
				.css('width', $('.sortable li').css('width'));
			$('#dialog').dialog('close');
			saveState();
		}
	});
	
}