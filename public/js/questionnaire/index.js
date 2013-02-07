/**
 * Saves the state of the given element. Must implement an action for
 * any element that can be saved.
 * 
 * @param event
 * @param reload Boolean that indicates reloading of the whole page
 */
function saveState(event, reload)
{
	if ($(event.target).hasClass('questionnaire')) {
		//alert(event.target.className);
		$('body').addClass('loading');
		 var $data=$(event.target).sortable('toArray');
		 $.post(baseUrl + '/questionnaire/order', {questionnaire: $.toJSON($data)}, function() {
				$('body').removeClass('loading');
		 }); 
	}
};

$(function() {
	// hide form for editing questionnaire title if no errors
	if ($('form#Webenq_Form_Questionnaire_Properties ul.errors').length != 0) {
		$('form#Webenq_Form_Questionnaire_Properties').show();
	}

	// show form for editing title when edit-buttons is clicked
	$('a#add_questionnaire').toggle(function() {
		$('form#Webenq_Form_Questionnaire_Properties').show('slow');
		return false;
	}, function() {
		$('form#Webenq_Form_Questionnaire_Properties').hide('slow');
		return false;
	});
	
});