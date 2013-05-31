//no ajax, override global savestate
function saveState(event, reload)
{}

//save sort order in hidden field
function updateSortField(){
	var $data=$('.answeritems').sortable('toArray');
	$('#answer-items-sortable').val($.toJSON($data));
	var $data=$('.questionrows').sortable('toArray');
	$('#questions-rows-sortable').val($.toJSON($data));
}

//specific code for options tab
function initOptionsTab(){
	//don't display boxwidth/boxheight if  element is input or textComplete
	var boxwidth = ['input', 'textComplete'];
	if (($.inArray($('#options-presentation').val(), boxwidth))==-1){
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
	if (($.inArray($('#options-presentation').val(), numberAnswer))==-1){
		$('#numberOfAnswers-label').hide();
		$('#numberOfAnswers-element').hide();
	}else{
		$('#numberOfAnswers-label').show();
		$('#numberOfAnswers-element').show();
	}
}

// add empty row to add an new answerchoice item
function addItemRow(){
	var tid = new Date().getTime();
	$("table#answeritems tr#newitem").clone().find('input').each(function() {
	    $(this).attr({
	      'id': function(_, id) { 
	    	  	if (id){
	    	  		return id.replace(/^answer-items-new-/,'answer-items-'+tid+'-'); 
	    	  	}
	    	  	},
	      'name': function(_, name) { return name.replace(/^answer\[items\]\[new\]/, 'answer[items]['+tid+']'); }
	    });
	  }).end().insertBefore($("table#answeritems tr#newitem")).attr('id','items-'+tid).removeClass('hidden').show('slow');
}

//add empty row to add an new question
function addQuestionRow(){
	//no subform for questions, so different replace and attribute function as answeritems.	
	var tid = new Date().getTime();
	$("table#questionrows tr#newitem").clone().find('input').each(function() {
	    $(this).attr({
	      'id': function(_, id) { 
	    	  	if (id){
	    	  		return id.replace(/^questions-rows-new-/,'questions-rows-'+tid+'-'); 
	    	  	}
	    	  	},
	      'name': function(_, name) { return name.replace(/^questions\[rows\]\[new\]/, 'questions[rows]['+tid+']'); }
	    });
	  }).end().insertBefore($("table#questionrows tr#newitem")).attr('id',tid).removeClass('hidden').show('slow');
}


//only one of 'reuse' (answer_domain_id) or 'new' should be set
function resetAnswerDomain($element) {
	$('#question-'+$element).val('');
}

$(function() {
	$('.tabs').tabs();
	initOptionsTab();
	//before submit is send to server, update hidden sort field with order of answer items
	
	// only one of 'reuse' (answer_domain_id) or 'new' should be set
	$('#question-new').change(function() {
		resetAnswerDomain('answer_domain_id')
	});
	$('#question-answer_domain_id').change(function() {
		resetAnswerDomain('new');
	});

	//make answer items sortable
	$('.answeritems.sortable2').sortable({
		placeholder: 'ui-state-highlight',
		items: "tr:not(#headerRow, hidden, #footerRow)",
		update: function(event, ui) {
			updateSortField();
		}
	});
	//make answer items sortable
	$('.questionrows.sortable2').sortable({
		placeholder: 'ui-state-highlight',
		items: "tr:not(#headerRow, hidden, #footerRow)",
		update: function(event, ui) {
			updateSortField();
			
		}
	});

	// add empty row to add an new answerchoice item
	$('#addItemRow').click(function() {
		addItemRow();
	});
	// add empty row to add an new question to a group
	$('#addQuestionRow').click(function() {
		addQuestionRow();
	});

	
	/* hide/show presentation options on change of presentation type */
	$('#options-presentation').change(function() {
		initOptionsTab();
	});
	
	//obsolete at this moment
	$('ul.sortable li a.icon.delete').click(function() {
		$(this).closest('li').remove();
		saveState();
		return false;
	});
	
	$("form").submit(function (){
		updateSortField();
	})	
});
